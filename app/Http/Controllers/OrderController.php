<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Traits\OrderTrait;

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
        $customerDetails = DB::table('customers')->select('first_name','last_name','city','state','zip_code','country_code','phone','email','address','billing_address_label','different_shipping_address')->where('id', $customer_id)->first();
        $shippingAddress = DB::table('shipping_address_info')->select('first_name','last_name','city','state','zip as zip_code','country_code','phone','email','address','is_residential','commercial','storage_facility','freight_terminal')->where('customer_id', $customer_id)->first();
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

        // $opopop_input_value = '';
        // $opopopop_input_value = '';

        // foreach ($request->order_details['products'] as $p_key => $product) {

        //     foreach ($product['attributes'] as $main_attr_key => $att) {

        //         $attr_id = explode('_', $main_attr_key)[2];

        //         $attribute = DB::table('attribute_tbl')
        //             ->select('attribute_tbl.attribute_type')
        //             ->where('attribute_tbl.attribute_id', $attr_id)
        //             ->first();

        //             // return $attribute;

        //         $attributes_type = $attribute->attribute_type;

        //         $options = [];
        //         $op_op_s = [];
        //         $op_op_op_s = [];
        //         $op_op_op_op_s = [];

        //         // $ops = request()->input('op_id_' . $att);
        //         // $option_value = request()->input('op_value_' . $att);

        //         // if ($ops && is_array($ops)) {
        //         //     foreach ($ops as $key => $op) {
        //         //         $option_type = DB::table('attr_options')
        //         //             ->select('attr_options.option_type')
        //         //             ->where('attr_options.att_op_id', explode('_', $op)[1])
        //         //             ->first()->option_type;

        //         //         $opval = ($option_type == 1) ? $option_value[1] : $option_value[$key];

        //         //         $options[] = [
        //         //             'option_type' => $option_type,
        //         //             'option_id' => explode('_', $op)[1],
        //         //             'option_value' => $opval,
        //         //             'option_key_value' => $op, //added by itsea, previously there was no value saving of attributes drop down, so added this
        //         //         ];
        //         //     }
        //         // }

        //         // $opopid = request()->input('op_op_id_' . $att);
        //         // $op_op_value = request()->input('op_op_value_' . $att);
        //         // $fraction = request()->input('fraction_' . $att);
        //         // $fr_key = 0;
        //         // if ($opopid && is_array($opopid)) {
        //         //     foreach ($opopid as $key => $opop) {
        //         //         if (!empty($fraction) && count($fraction) > 0) {
        //         //             // If fraction value
        //         //             $op_op_s[] = [
        //         //                 'op_op_id' => explode('_', $opop)[0],
        //         //                 'op_op_value' => $op_op_value[$key] . ' ' . $fraction[$fr_key],
        //         //                 'option_key_value' => $opop,
        //         //             ];
        //         //             $fr_key++;
        //         //         } else {
        //         //             // If not fraction value
        //         //             $op_op_s[] = [
        //         //                 'op_op_id' => explode('_', $opop)[0],
        //         //                 'op_op_value' => $op_op_value[$key],
        //         //                 'option_key_value' => $opop,
        //         //             ];
        //         //         }
        //         //     }
        //         // }

        //         // $opopopid = request()->input('op_op_op_id_' . $att);
        //         // $op_op_op_value = request()->input('op_op_op_value_' . $att);
        //         // $opopop_input_value = $opopopid;

        //         // if ($opopopid && is_array($opopopid)) {
        //         //     foreach ($opopopid as $key => $opopop) {
        //         //         $op_op_op_s[] = [
        //         //             'op_op_op_id' => explode('_', $opopop)[0],
        //         //             'op_op_op_value' => $op_op_op_value[$key],
        //         //             'option_key_value' => $opopop,
        //         //         ];
        //         //     }
        //         // }
        //         // $opopopopid = request()->input('op_op_op_op_id_' . $att);
        //         // $op_op_op_op_value = request()->input('op_op_op_op_value_' . $att);
        //         // if ($opopopopid && is_array($opopopopid)) {
        //         //     $opopopop_input_value = $opopopopid;

        //         //     foreach ($opopopopid as $key => $opopopop) {

        //         //         $op_op_op_op_s[] = [
        //         //             'op_op_op_op_id' => explode('_', $opopopop)[0],
        //         //             'op_op_op_op_value' => $op_op_op_op_value[$key],
        //         //             'option_key_value' => $opopopop,
        //         //         ];
        //         //     }
        //         // }

        //         // // IF Main attribute type is text + fraction then store the fraction value : START
        //         // $main_attr_val = $attribute_value[@$main_attr_key];
        //         // if ($attributes_type == 5) {
        //         //     // Text + Fraction
        //         //     $fraction = request()->input('fraction_' . $att);
        //         //     if (count($fraction) > 0) {
        //         //         $main_attr_val = $attribute_value[@$main_attr_key] . ' ' . $fraction[0];
        //         //     }
        //         // }
        //         // // IF Main attribute type is text + fraction then store the fraction value : END

        //        return  $attrib[] = [
        //             'attribute_id' => $attr_id,
        //             // 'attribute_value' => $main_attr_val,
        //             'attributes_type' => $attributes_type,
        //             'options' => @$options,
        //             'opop' => @$op_op_s,
        //             'opopop' => @$op_op_op_s,
        //             'opopopop' => @$op_op_op_op_s
        //         ];
        //     }
        // }

        $json_data = '{
            "attributes" : {
                "op_id_35": {
                    "label": "Speciality Shutters",
                    "value": "3718_76",
                    "op_key_value": "3718_76",
                    "option_id": 76,
                    "option_type": 4,
                    "parentLabel": "Model",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_30": {
                        "label": "3 1/2\" Louvers",
                        "value": "64_35_30",
                        "op_op_key_value": "30_1835_76",
                        "parentLabel": "Louvers",
                        "type": "select",
                        "attributes_type": ""
                    },
                    "op_op_id_31": {
                        "label": "Outside Mound",
                        "value": "67_35_31",
                        "op_op_key_value": "31_1836_76",
                        "parentLabel": "Mount",
                        "type": "select",
                        "attributes_type": ""
                    },
                    "op_op_id_32": {
                        "label": "Finished Size",
                        "value": "69_35_32",
                        "op_op_key_value": "32_1837_76",
                        "parentLabel": "Measurements",
                        "type": "select",
                        "attributes_type": ""
                    },
                    "op_op_id_33": {
                        "label": "2",
                        "value": "71_35_33",
                        "op_op_key_value": "33_1838_76",
                        "parentLabel": "Number of Panel",
                        "type": "select",
                        "attributes_type": ""
                    }
                },
                "op_id_36": {
                    "label": "Hidden Tilt",
                    "value": "3721_79",
                    "op_key_value": "3721_79",
                    "option_id": 79,
                    "option_type": 0,
                    "parentLabel": "Tilt Bar",
                    "type": "select",
                    "attributes_type": 2
                },
                "op_id_185": {
                    "label": "Entry",
                    "value": "3723_429",
                    "op_key_value": "3723_429",
                    "option_id": 429,
                    "option_type": 5,
                    "parentLabel": "Tilt Bar Split Location",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_236": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test ",
                            "parentLabel": "Size of Height",
                            "type": "input",
                            "option_key_value": "236_1843_429"
                        },
                        "select": {
                            "label": "1/4",
                            "value": 3,
                            "parentLabel": "Size of Height",
                            "option_key_value": "236_1843_429"
                        }
                    }
                },
                "op_id_38": {
                    "label": "L Frame",
                    "value": "3726_82",
                    "op_key_value": "3726_82",
                    "option_id": 82,
                    "option_type": 0,
                    "parentLabel": "Frame Type",
                    "type": "select",
                    "attributes_type": 2
                },
                "op_id_39": {
                    "label": "3 Sided",
                    "value": "3729_85",
                    "op_key_value": "3729_85",
                    "option_id": 85,
                    "option_type": 0,
                    "parentLabel": "Frame Sides",
                    "type": "select",
                    "attributes_type": 2
                },
                "op_id_186": {
                    "label": "Entry",
                    "value": "3732_431",
                    "op_key_value": "3732_431",
                    "option_id": 431,
                    "option_type": 5,
                    "parentLabel": "Build out Thickness",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_237": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Size",
                            "type": "input",
                            "option_key_value": "237_1844_431"
                        },
                        "select": {
                            "label": "1/4",
                            "value": 3,
                            "parentLabel": "Size",
                            "option_key_value": "237_1844_431"
                        }
                    }
                },
                "op_id_187": {
                    "label": "1 Divider Rail",
                    "value": "3734_433",
                    "op_key_value": "3734_433",
                    "option_id": 433,
                    "option_type": 5,
                    "parentLabel": "Divider Rail",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_238": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Divider Rail  #1",
                            "type": "input",
                            "option_key_value": "238_1845_433"
                        },
                        "select": {
                            "label": "1/8",
                            "value": 2,
                            "parentLabel": "Divider Rail  #1",
                            "option_key_value": "238_1845_433"
                        }
                    },
                    "op_op_id_239": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Divider Rail  #2",
                            "type": "input",
                            "option_key_value": "239_1846_433"
                        },
                        "select": {
                            "label": "1/4",
                            "value": 3,
                            "parentLabel": "Divider Rail  #2",
                            "option_key_value": "239_1846_433"
                        }
                    }
                },
                "op_id_42": {
                    "label": "Yes",
                    "value": "3737_92",
                    "op_key_value": "3737_92",
                    "option_id": 92,
                    "option_type": 2,
                    "parentLabel": "T-Post",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_42": {
                        "label": "2\" T-Post",
                        "value": "45_1850_92",
                        "op_op_key_value": "45_1850_92",
                        "parentLabel": "",
                        "type": "select",
                        "attributes_type": "",
                        "op_op_op_id_91": {
                            "label": "2 Panel-LL",
                            "value": "47_42",
                            "parentLabel": "1st section panel configuration",
                            "type": "select",
                            "attributes_type": "",
                            "op_op_op_key_value" : "47_42"
                        },
                        "op_op_op_id_92": {
                            "type": "input_with_select",
                            "input": {
                                "value": "test",
                                "parentLabel": "T-Post #1 Location",
                                "type": "input",
                                "op_op_op_key_value" : "test"
                            },
                            "select": {
                                "label": "3/8",
                                "value": 4,
                                "parentLabel": "T-Post #1 Location"
                            }
                        },
                        "op_op_op_id_93": {
                            "label": "2 Panel-LL",
                            "value": "55_42",
                            "parentLabel": "2nd section panel configuration",
                            "type": "select",
                            "attributes_type": "",
                            "op_op_op_key_value" :"55_42"
                        },
                        "op_op_op_id_94": {
                            "type": "input_with_select",
                            "input": {
                                "value": "test",
                                "parentLabel": "T-Post #2 Location",
                                "type": "input",
                                "op_op_op_key_value" : "test"
                            },
                            "select": {
                                "label": "1/4",
                                "value": 3,
                                "parentLabel": "T-Post #2 Location"
                            }
                        },
                        "op_op_op_id_95": {
                            "label": "2 Panel-LL",
                            "value": "63_42",
                            "parentLabel": "3rd section panel configuration",
                            "type": "select",
                            "attributes_type": "",
                            "op_op_op_key_value" : "63_42"
                        },
                        "op_op_op_id_96": {
                            "type": "input_with_select",
                            "input": {
                                "value": "test",
                                "parentLabel": "T-Post #3 Location",
                                "type": "input",
                                "op_op_op_key_value" :"test"
                            },
                            "select": {
                                "label": "1/4",
                                "value": 3,
                                "parentLabel": "T-Post #3 Location"
                            }
                        }
                    }
                },
                "op_id_43": {
                    "label": "2 Panel-LR",
                    "value": "3741_96",
                    "op_key_value": "3741_96",
                    "option_id": 96,
                    "option_type": 0,
                    "parentLabel": "Panel Configuration",
                    "type": "select",
                    "attributes_type": 2
                },
                "op_id_44": {
                    "label": "Contoured-Half Arch-Left",
                    "value": "3747_102",
                    "op_key_value": "3747_102",
                    "option_id": 102,
                    "option_type": 5,
                    "parentLabel": "Speciality Type",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_49": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Left Leg Height",
                            "type": "input",
                            "op_op_key_value": "49_1854_102"
                            
                        },
                        "select": {
                            "label": "1/8",
                            "value": 2,
                            "parentLabel": "Left Leg Height",
                            "option_key_value": "49_1854_102"
                        }
                    },
                    "op_op_id_50": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Mid Arch Height",
                            "type": "input",
                            "op_op_key_value": "50_1855_102"
                        },
                        "select": {
                            "label": "1/8",
                            "value": 2,
                            "parentLabel": "Mid Arch Height",
                            "option_key_value": "50_1855_102"
                        }
                    },
                    "op_op_id_51": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Tallest Height",
                            "type": "input",
                            "op_op_key_value": "51_1856_102"
                        },
                        "select": {
                            "label": "1/16",
                            "value": 1,
                            "parentLabel": "Tallest Height",
                            "option_key_value": "51_1856_102"
                        }
                    }
                },
                "op_id_45": {
                    "label": "Specialty Hinge",
                    "value": "3750_105",
                    "op_key_value": "3750_105",
                    "option_id": 105,
                    "option_type": 5,
                    "parentLabel": "Hinge Color",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_55": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Entry",
                            "type": "input",
                            "op_op_key_value": "55_1860_105"
                        },
                        "select": {
                            "label": "1/8",
                            "value": 2,
                            "parentLabel": "Entry",
                            "option_key_value": "55_1860_105"
                        }
                    }
                },
                "op_id_46": {
                    "label": "Yes",
                    "value": "3752_107",
                    "op_key_value": "3752_107",
                    "option_id": 107,
                    "option_type": 5,
                    "parentLabel": "Double Hung Shutters",
                    "type": "select",
                    "attributes_type": 2,
                    "op_op_id_56": {
                        "type": "input_with_select",
                        "input": {
                            "value": "test",
                            "parentLabel": "Double Hung Split Location",
                            "type": "input",
                            "op_op_key_value": "56_1861_107"
                        },
                        "select": {
                            "label": "1/4",
                            "value": 3,
                            "parentLabel": "Double Hung Split Location",
                            "option_key_value": "56_1861_107"
                        }
                    }
                }
            }
        }';





        // Decode JSON data
        $data = json_decode($json_data, true);

        // Function to recursively find keys and values starting with "op_op_op_id_"
        // function findKeysAndValues($array, &$result)
        // {
        //     foreach ($array as $key => $value) {
        //         if (strpos($key, 'op_op_key_value') === 0) {
        //             $result[$key] = $value;
        //         }
        //         if (is_array($value)) {
        //             findKeysAndValues($value, $result);
        //         }
        //     }
        // }
        // function findKeysAndValues($array, &$result)
        // {
        //     foreach ($array as $key => $value) {
        //         if (strpos($key, 'op_op_id_') === 0) {
        //             // Check if the value of the key contains 'op_op_key_value'
        //             if (isset($value['op_key_value'])) {
        //                 $result[$key] = $value['op_key_value'];
        //             }
        //         }
        //         if (is_array($value)) {
        //             findKeysAndValues($value, $result);
        //         }
        //     }
        // }

        // // Array to store keys and values starting with "op_op_op_id_"
        // $op_op_op_id_data = array();

        // // Call the function to find keys and values recursively
        // findKeysAndValues($data, $op_op_op_id_data);

        // // Output the keys and values
        // print_r($op_op_op_id_data);

        // Function to recursively find keys and values starting with "op_key_value"
        function findKeysAndValues($array, &$result)
        {
            foreach ($array as $key => $value) {
                if ($key === 'op_op_op_key_value') {
                    $result[] = $value;
                }
                if (is_array($value)) {
                    findKeysAndValues($value, $result);
                }
            }
        }

        // Array to store op_key_value values
        $op_key_value_data = array();

        // Call the function to find op_key_value values recursively
        findKeysAndValues($data, $op_key_value_data);

        // Output the op_key_value values
        print_r($op_key_value_data);
    }
}
