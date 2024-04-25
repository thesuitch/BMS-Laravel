<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Traits\OrderTrait;
use BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    use OrderTrait;

    protected $level_id;
    protected $user_id;


    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth('api')->user()->is_admin == 1) {
                $this->level_id = auth('api')->user()->user_id;
            } else {
                $this->level_id = auth('api')->user()->userinfo->created_by;
            }
            $this->user_id = auth('api')->user()->user_id;
            return $next($request);
        });
    }


    function getCategoryProducts()
    {
        try {

            $userId = auth('api')->user()->id;

            $categories = Category::with([
                'products' => function ($query) {
                    $query->select('id', 'category_id', 'product_name', 'default', 'hide_height_width', 'hide_pattern', 'hide_room');
                }
            ])
                ->select('id', 'category_name')
                ->where('created_by', $userId)
                ->where('status', 1)
                ->where('parent_category', 0)
                ->orderBy('position')
                ->get();

            if (auth('api')->user()->user_type == 'c') {
                $userInfo = $this->checkRetailerConnectToWholesaler(auth('api')->user()->id);
                $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? auth('api')->user()->main_b_id : auth('api')->user()->level_id;
            } else {
                $createdBy = auth('api')->user()->level_id;
            }

            $categories->each(function ($category, $createdBy) {

                // fractions
                $category_get = Category::findOrFail($category->id);
                $selectedFractions = $category_get->getSelectedFractions();
                $category['fractions'] = $selectedFractions->toArray();

                // custom labels
                $custom_label = $this->getCustomLabelUserwise($createdBy, $category->id);
                $category['custom_labels'] = $custom_label;
            });

            $responseData = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'categories' => $categories,
                ]
            ];

            if ($categories->isEmpty()) {
                throw new \Exception();
            }


            return response()->json($responseData);
        } catch (\Exception $e) {
            $responseData = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found' . $e->getMessage(),
                'data' => [
                    'categories' => [],
                ]
            ];

            return response()->json($responseData, 404);
        }
    }

    function getAttributes($product_id)
    {
        try {

            $height = request()->get('height');
            $width = request()->get('width');
            $height_fraction = request()->get('height_fraction');
            $width_fraction = request()->get('width_fraction');
            $pattern_id = request()->get('pattern_id');
            $customer_id = request()->get('customer_id');

            $main_price =   $this->getProductRowColPrice($height, $width, $product_id, $pattern_id, $width_fraction, $height_fraction);
            $patterns = $this->getColorPartanModel($product_id);
            $discount = $this->getProductCommission($product_id, $customer_id);
            $MinMaxHeightWidth = $this->getMinMaxHeightWidth($product_id, $pattern_id);
            $attributes =  $this->getProductToAttribute($product_id);
            // dd($main_price);
            $data = [
                'patterns' => $patterns,
                'main_price' =>  $main_price,
                'discount' => $discount,
                'MinMaxHeightWidth' => $MinMaxHeightWidth,
                'attributes' => $attributes
            ];

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => []
            ], 404);
        }
    }


    public function generate_order_id_based_on_format(Request $request)
    {

        try {

            $userInfo = DB::table('user_info')->find($this->user_id);
            $companyName = $userInfo->company ?? DB::table('company_profile')->where('user_id', $userInfo->created_by)->value('company_name');
            $compName = $companyName ? str_replace(' ', '', $companyName) : 'XXXX';
            $compProfileData = getCompanyProfileOrderConditionSettings();

            if (optional($compProfileData)->order_id_format == 1) {
                $wholesalerOrderFormat = DB::table('wholesaler_order_id_format')->where('level_id', $this->level_id)->first();
                if ($wholesalerOrderFormat) {
                    [$orderFormat, $lastOrder, $orderNumber] = [explode("-", $wholesalerOrderFormat->order_number_format), DB::table('wholesaler_order_id_numbers')->orderByDesc('id')->where('level_id', $this->level_id)->first(), 0];
                    if ($lastOrder) {
                        $orderNumber = str_pad($lastOrder->current_order_number + 1, 3, '0', STR_PAD_LEFT);
                    } else {
                        $orderNumber = str_pad($wholesalerOrderFormat->order_starting_number, 3, '0', STR_PAD_LEFT);
                    }
                    $customerWiseSidemark = '';
                    foreach ($orderFormat as $value) {
                        $dataArr = ['[CUST]', '[COMPANY]', '[SM]', '[NUMBER]'];

                        $customerWiseSidemark .= match ($value) {
                            '[ORD]' => match ($wholesalerOrderFormat->order_prefix) {
                                '[CUST]' => $request->cust_name . "-",
                                '[COMPANY]' => $compName . "-",
                                '[SM]' => $request->side_mark . "-",
                                default => $wholesalerOrderFormat->order_prefix . "-"
                            },
                            '[CUST]' => $request->cust_name ? $request->cust_name . "-" : "[CUST]-",
                            '[COMPANY]' => $compName . "-",
                            '[SM]' => $request->side_mark ? $request->side_mark . "-" : "XXXX-",
                            '[NUMBER]' => $orderNumber . "-",
                            default => ''
                        };
                    }
                    $customerWiseSidemark = rtrim($customerWiseSidemark, '-');
                }
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    "customer_wise_sidemark" => $customerWiseSidemark ?? '',
                    "current_order_number" => $orderNumber ?? ''
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found' . $e->getMessage(),
                'data' => [
                    'customers' => [],
                ]
            ], 404);
        }
    }


    public function getEmployees()
    {
        try {
            $employeList = DB::table('user_info')
                ->select(DB::raw("*, CONCAT_WS(' ', first_name, last_name) AS fullname"))
                ->where('user_type', 'b')
                ->where('created_by', '!=', '')
                ->where('created_by', $this->level_id)
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'employees' => $employeList,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'employees' => [],
                ]
            ], 404);
        }
    }


    public function existingShippingAddress($customer_id)
    {
        $customerDetails = DB::table('customers')->select('first_name', 'last_name', 'city', 'state', 'zip_code', 'country_code', 'phone', 'email', 'address', 'billing_address_label', 'different_shipping_address')->where('id', $customer_id)->first();
        $shippingAddress = DB::table('shipping_address_info')->select('first_name', 'last_name', 'city', 'state', 'zip as zip_code', 'country_code', 'phone', 'email', 'address', 'is_residential', 'commercial', 'storage_facility', 'freight_terminal')->where('customer_id', $customer_id)->first();
        //   dd($shippingAddress);

        $resInfo = [];

        if (!empty($customerDetails)) {
            $customerAddress = explode(",", $customerDetails->address)[0];
            $billingAddressLabel = "";

            switch ($customerDetails->billing_address_label) {
                case 'is_residential':
                    $billingAddressLabel = "Residential";
                    break;
                case 'commercial':
                    $billingAddressLabel = "Commercial";
                    break;
                case 'storage_facility':
                    $billingAddressLabel = "Storage Facility";
                    break;
                case 'freight_terminal':
                    $billingAddressLabel = "Freight Terminal";
                    break;
            }

            $resInfo[] = [
                'addressLabel' => $billingAddressLabel,
                'customerDetails' => $customerDetails,
                // 'address' => $customerAddress,
            ];
            // $resInfo[] = [
            //     'billingAddressLabel' => $billingAddressLabel,
            //     'customerDetails' => $customerDetails,
            //     'customerAddress' => $customerAddress,
            // ];
        }

        if (!empty($shippingAddress->address) && !empty($shippingAddress->city) && !empty($customerDetails->different_shipping_address)) {
            $resAddress = explode(",", $shippingAddress->address)[0];
            $addressLabel = "";

            if (isset($shippingAddress->is_residential) && $shippingAddress->is_residential == 1) {
                $addressLabel = "Residential";
            } else if (isset($shippingAddress->commercial) && $shippingAddress->commercial == 1) {
                $addressLabel = "Commercial";
            } else if (isset($shippingAddress->storage_facility) && $shippingAddress->storage_facility == 1) {
                $addressLabel = "Storage Facility";
            } else if (isset($shippingAddress->freight_terminal) && $shippingAddress->freight_terminal == 1) {
                $addressLabel = "Freight Terminal";
            }

            // $resInfo[] = [
            //     'ShippingAddressLabel' => $addressLabel,
            //     'shippingAddress' => $shippingAddress,
            //     'ShippingAddress' => $resAddress,
            // ];
            $resInfo[] = [
                'addressLabel' => $addressLabel,
                'customerDetails' => $shippingAddress,
                // 'address' => $resAddress,
            ];
        }


        $responseData = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $resInfo
        ];

        return response()->json($responseData);
    }



    // function store(Request $request)
    // {

    //     foreach ($request->order_details['products'] as $p_key => $product) {

    //         foreach ($product['attributes'] as $a_key => $attribute) {

    //             $outputArray[] = [
    //                 "attribute_id" => explode('_', $a_key)[2],
    //                 "attribute_value" => "",
    //                 "attributes_type" =>  $attribute['attributes_type'],
    //                 "options" => [
    //                     [
    //                         "option_type" =>  @$attribute['option_type'] ?? 0,
    //                         "option_id" => explode('_', $attribute['value'])[1],
    //                         "option_value" => "",
    //                         "option_key_value" => $attribute['value']
    //                     ]
    //                 ],
    //                 "opop" => [],
    //                 "opopop" => [],
    //                 "opopopop" => []
    //             ];

    //             foreach ($attribute as $nestedKey => $nestedValue) {
    //                 if (strpos($nestedKey, 'op_op_id_') !== false) {


    //                     foreach ($nestedValue as $op_op_op_id => $op_op_op_value) {
    //                         if (strpos($op_op_op_id, 'op_op_op_id_') !== false) {


    //                             if($op_op_op_value['type'] == 'select'){

    //                                 $outputArray[count($outputArray) - 1]["opopop"][] = [
    //                                     "op_op_id" => explode('_',$op_op_op_id)[4], // Assuming the ID is at index 3
    //                                     "op_op_value" => null, // Assuming you want the label here
    //                                     "option_key_value" => @$op_op_op_value['value']
    //                                 ];
    //                             }else{

    //                                 $outputArray[count($outputArray) - 1]["opopop"][] = [
    //                                     "op_op_id" => explode('_',$op_op_op_id)[4], // Assuming the ID is at index 3
    //                                     "op_op_value" => null, // Assuming you want the label here
    //                                     "option_key_value" => @$op_op_op_value['value']
    //                                 ];

    //                             }






    //                         }
    //                     }


    //                     // if (@$nestedValue['type'] == "input_with_select") {
    //                     //     $outputArray[count($outputArray) - 1]["opop"][] = [
    //                     //         "op_op_id" => explode('_', $nestedKey)[3], // Assuming the ID is at index 3
    //                     //         "op_op_value" => @$nestedValue['label'], // Assuming you want the label here
    //                     //         "option_key_value" => @$nestedValue['option_key_value']
    //                     //     ];
    //                     // }




    //                     if (@$nestedValue['label']) {
    //                         $outputArray[count($outputArray) - 1]["opop"][] = [
    //                             "op_op_id" => explode('_', $nestedKey)[3], // Assuming the ID is at index 3
    //                             "op_op_value" => @$nestedValue['label'], // Assuming you want the label here
    //                             "option_key_value" => @$nestedValue['option_key_value']
    //                         ];
    //                     }

    //                     if (!isset($nestedValue['value'])) {
    //                         // dd(3);

    //                         if (isset($nestedValue['input']) && isset($nestedValue['select'])) {

    //                             $outputArray[count($outputArray) - 1]["opop"][] = [
    //                                 "op_op_id" => explode('_', $nestedKey)[3], // Assuming the ID is at index 3
    //                                 "op_op_value" =>  @$nestedValue['input']['value'] . ' ' . @$nestedValue['select']['value'], // Assuming you want the label here
    //                                 "option_key_value" => @$nestedValue['input']['option_key_value']
    //                             ];

    //                         } else {


    //                             if (is_array($nestedValue)) {

    //                                 if (isset($nestedValue[0]['value'])) {
    //                                     $outputArray[count($outputArray) - 1]["opop"][] = [
    //                                         "op_op_id" => explode('_', $nestedKey)[3], // Assuming the ID is at index 3
    //                                         "op_op_value" =>  $nestedValue['value'] ?? '', // Assuming you want the label here
    //                                         "option_key_value" => @$nestedValue[0]['option_key_value'],
    //                                     ];
    //                                 }

    //                                 foreach ($nestedValue as $subnestedkey => $subnestedValue) {

    //                                     if (strpos($subnestedkey, 'op_op_op_id_') !== false) {
    //                                         $outputArray[count($outputArray) - 1]["opop"][] = [
    //                                             "op_op_id" => explode('_', $nestedValue['value'])[0], // Assuming the ID is at index 3
    //                                             "op_op_value" =>  @$nestedValue['label'], // Assuming you want the label here
    //                                             "option_key_value" => @$nestedValue['value']
    //                                         ];
    //                                     } else {

    //                                         $outputArray[count($outputArray) - 1]["opopop"][] = [
    //                                             "op_op_op_id" => explode('_', $subnestedValue['value'])[0],
    //                                             "op_op_op_value" => @$subnestedValue['label'],
    //                                             "option_key_value" => @$subnestedValue['value']
    //                                         ];
    //                                     }
    //                                 }
    //                             }




    //                         }
    //                     }

    //                     // if (is_array($nestedValue)) {
    //                     //     foreach ($nestedValue as $key => $value) {
    //                     //         if (strpos($key, 'op_op_op_id_') !== false) {
    //                     //             // print_r($key) . '</br>';
    //                     //         }
    //                     //     }
    //                     // }
    //                 }






    //                 // elseif (strpos($nestedKey, 'op_op_op_id_') !== false) {


    //                 // if (@$nestedValue['label']) {
    //                 //     $outputArray[count($outputArray) - 1]["opop"][] = [
    //                 //         "op_op_id" => explode('_', $nestedKey)[3], // Assuming the ID is at index 3
    //                 //         "op_op_value" => @$nestedValue['label'], // Assuming you want the label here
    //                 //         "option_key_value" => @$nestedValue['option_key_value']
    //                 //     ];
    //                 // }

    //                 // if (!isset($nestedValue['value'])) {

    //                 //     if (isset($nestedValue['input']) && isset($nestedValue['select'])) {

    //                 //         $outputArray[count($outputArray) - 1]["opop"][] = [
    //                 //             "op_op_id" => explode('_', $nestedKey)[3], // Assuming the ID is at index 3
    //                 //             "op_op_value" =>  @$nestedValue['input']['value'] . ' ' . @$nestedValue['select']['value'], // Assuming you want the label here
    //                 //             "option_key_value" => @$nestedValue['option_key_value']
    //                 //         ];
    //                 //     } else {

    //                 //         if (is_array($nestedValue)) {

    //                 //             foreach ($nestedValue as $subnestedkey => $subnestedValue) {

    //                 //                 $outputArray[count($outputArray) - 1]["opopop"][] = [
    //                 //                     "op_op_op_id" => explode('_', $subnestedValue['value'])[0],
    //                 //                     "op_op_op_value" => @$subnestedValue['label'],
    //                 //                     "option_key_value" => @$subnestedValue['value']
    //                 //                 ];
    //                 //             }
    //                 //         }
    //                 //     }
    //                 // }
    //                 // }
    //             }
    //         }
    //     }

    //     return $outputArray;
    // }
    function store(Request $request)
    {


        //    echo 1;

        $order_id = $request->order_details['order_id'];
        $customer_id = $request->order_details['customer_id'];
        $side_mark = $request->order_details['side_mark'];

        // return $this->generateBarcodeAndSave($customer_id, $order_id, $side_mark);

        // exit;

        $opopop_input_value = '';
        $opopopop_input_value = '';
        $attrib = [];


        function filterKeys($key, $findkey)
        {
            return preg_match('/^' . $findkey . '\d+$/', $key);
        }

        // Iterate through the nested arrays and find keys
        function findKeys($array, $findkey, &$result)
        {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    if (filterKeys($key, $findkey)) {
                        $result[] = $value;
                    }
                    findKeys($value, $findkey, $result);
                }
            }
        }


        foreach ($request->order_details['products'] as $p_key => $product) {

            foreach ($product['attributes'] as $main_attr_key => $att) {

                $attr_id = explode('_', $main_attr_key)[2];

                $attribute = DB::table('attribute_tbl')
                    ->select('attribute_tbl.attribute_type')
                    ->where('attribute_tbl.attribute_id', $attr_id)
                    ->first();

                // return $attribute;

                $attributes_type = $attribute->attribute_type;

                $options = [];
                $op_op_s = [];
                $op_op_op_s = [];
                $op_op_op_op_s = [];

                // Call the function to find keys
                // findKeys($att, 'op_op_id_');
                // exit;

                if ($att['type'] != 'input') {

                    $option_type = DB::table('attr_options')
                        ->select('attr_options.option_type')
                        ->where('attr_options.att_op_id', explode('_', $att['value'])[1])
                        ->first()->option_type;


                    $options[] = [
                        'option_type' => $option_type,
                        'option_id' => explode('_', $att['value'])[1],
                        'option_value' => $att['label'],
                        'option_key_value' => $att['value'], //added by itsea, previously there was no value saving of attributes drop down, so added this
                    ];
                }



                $op_op_matchedKeys = [];
                findKeys($att, 'op_op_id_', $op_op_matchedKeys);
                $op_op_op_matchedKeys = [];
                findKeys($att, 'op_op_op_id_', $op_op_op_matchedKeys);
                $op_op_op_op_matchedKeys = [];
                findKeys($att, 'op_op_op_op_id_', $op_op_op_op_matchedKeys);

                // print_r($matchedKeys);
                // exit;

                foreach ($op_op_matchedKeys as $key => $value) {
                    if (!isset($value['type'])) {
                        $op_op_s[] = [
                            'op_op_id' => explode('_', $value[0]['op_op_key_value'])[0],
                            'op_op_value' => implode(', ', array_column($value, 'label')),
                            'option_key_value' => $value[0]['op_op_key_value'],
                        ];


                        foreach ($value as $v) {

                            $op_op_op_s[] = [
                                'op_op_op_id' => explode('_', $v['value'])[0],
                                'op_op_op_value' => $v['label'],
                                'option_key_value' => $v['value'],
                            ];
                        }
                    } else if (@$value['type'] == 'input_with_select') {
                        $op_op_s[] = [
                            'op_op_id' => @explode('_', $value['input']['op_op_key_value'])[0],
                            'op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                            'option_key_value' => @$value['input']['op_op_key_value']
                        ];
                        // $op_op_s[] = [
                        //     'op_op_id' => @explode('_', $value['op_op_key_value'])[0],
                        //     'op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                        //     'option_key_value' => @$value['op_op_key_value']
                        // ];
                    } else if (@$value['type'] == 'input') {

                        $op_op_s[] = [
                            'op_op_id' => explode('_', $value['op_op_key_value'])[0],
                            'op_op_value' => $value['value'],
                            'option_key_value' => $value['op_op_key_value'],
                        ];
                    } else {
                        $op_op_s[] = [
                            'op_op_id' => @explode('_', $value['op_op_key_value'])[0],
                            'op_op_value' => @$value['label'],
                            'option_key_value' => @$value['op_op_key_value']
                        ];
                    }
                }


                foreach ($op_op_op_matchedKeys as $key => $value) {


                    if (isset($value['op_op_op_op_id'])) {

                        if ($value['type'] == 'input_with_select') {
                            $op_op_op_op_s[] = [
                                'op_op_op_op_id' => @explode('_', $value['value'])[0],
                                'op_op_op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                                'option_key_value' => @$value['op_op_op_key_value']
                            ];
                        } else {
                            $op_op_op_op_s[] = [
                                'op_op_op_op_id' => @explode('_', $value['value'])[0],
                                'op_op_op_op_value' => @$value['label'],
                                'option_key_value' => @$value['value']
                            ];
                        }
                    }


                    if (isset($value['input']['op_op_id']) || isset($value['input']['op_op_id'])) {

                        if ($value['type'] == 'input_with_select') {
                            $op_op_s[] = [
                                'op_op_id' => @explode('_', $value['input']['op_op_id'])[0],
                                'op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                                'option_key_value' => @$value['input']['op_op_id']
                            ];
                            // $op_op_s[] = [
                            //     'op_op_id' => @explode('_', $value['op_op_id'])[0],
                            //     'op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                            //     'option_key_value' => @$value['op_op_id']
                            // ];
                        } else {
                            $op_op_op_op_s[] = [
                                'op_op_id' => @explode('_', $value['op_op_id'])[0],
                                'op_op_value' => @$value['label'],
                                'option_key_value' => @$value['op_op_id']
                            ];
                        }
                    }

                    if ($value['type'] == 'input_with_select') {
                        $op_op_op_s[] = [
                            'op_op_op_id' => @explode('_', $value['input']['op_op_op_key_value'])[0],
                            'op_op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                            'option_key_value' => @$value['input']['op_op_op_key_value']
                        ];
                    } else {
                        $op_op_op_s[] = [
                            'op_op_op_id' => @explode('_', $value['value'])[2],
                            'op_op_op_value' => @$value['label'],
                            'option_key_value' => @$value['op_op_op_key_value']
                        ];
                    }
                }



                foreach ($op_op_op_op_matchedKeys as $key => $value) {

                    $op_op_op_op_s[] = [
                        'op_op_op_op_id' => @explode('_', $value['value'])[0],
                        'op_op_op_op_value' => @$value['label'],
                        'option_key_value' => @$value['value']
                    ];
                }


                $attrib[] = [
                    'attribute_id' => $attr_id,
                    'attribute_value' => $att['value'],
                    'attributes_type' => $attributes_type,
                    'options' => @$options,
                    'opop' => @$op_op_s,
                    'opopop' => @$op_op_op_s,
                    'opopopop' => @$op_op_op_op_s
                ];
            }
        }


        return $attrib;
    }



    public function generateBarcodeAndSave($customer_id, $order_id, $side_mark)
    {
        $barcode_img_path = '';

        $show_b_customer_record = Customer::selectRaw("*, CONCAT_WS('-', first_name, last_name) as full_name")
            ->where('id', $customer_id)
            ->first();

        $shipping_address_b_customer = DB::table('shipping_address_info')->where('customer_id', $customer_id)->get();
        $company_profile = DB::table('company_profile')->where('user_id', $this->level_id)->get();


        $order_array = [
            "order_id" => $order_id,
            "side_mark" => $side_mark,
            "customer_name" => $show_b_customer_record->full_name
        ];

        $order_explode = explode('-', $order_array['order_id']);
        $customer_explode = explode('-', $order_array['customer_name']);

        $side_mark_string = $this->manageOrderBarcodeString($order_array['side_mark'], 4);
        $customer_name = $this->manageOrderBarcodeString($customer_explode[0], 4);

        $use_order_id = $order_explode[0];
        $use_smark_nm = $side_mark_string;
        $use_custo_nm = $customer_name;

        $use_in_barcode_ord_id = $use_custo_nm . "-" . $use_smark_nm . "-" . $use_order_id;

        if (!empty($use_in_barcode_ord_id)) {

            $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
            $image = $generator->getBarcode($use_in_barcode_ord_id, $generator::TYPE_CODE_128);
            $barcode_img_path = 'assets/barcode/b/' . $order_id . '.jpg';
            Storage::put($barcode_img_path, $image);
            // return response($image)->header('Content-type', 'image/png');
        }

        // return $barcode_img_path;
    }

    public function manageOrderBarcodeString($sidemark_strings, $string_length)
    {
        $side_mark_explode = explode('-', $sidemark_strings);

        if ($side_mark_explode) {
            $barcode_array = array();
            foreach ($side_mark_explode as $key => $smrk) {
                $sring_len = strlen($smrk);

                $side_mark = $smrk;

                if ($sring_len != $string_length) {
                    if ($sring_len < $string_length) {

                        $loop_count = $string_length - $sring_len;

                        for ($x = 1; $x <= $loop_count; $x++) {
                            $side_mark .= "#";
                        }
                    } else {
                        $side_mark = substr($smrk, 0, $string_length);
                    }
                } else {
                    $side_mark = substr($smrk, 0, $string_length);
                }

                if ($key <= 1) {
                    array_push($barcode_array, $side_mark);
                }
            }
            return implode('-', $barcode_array);
        }

        return false;
    }
}
