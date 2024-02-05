<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Customer;
use App\Models\QutationDetail;
use Illuminate\Support\Facades\DB;
use App\Traits\OrderTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RoomCreateRequest;
use hash;
use Illuminate\Support\Facades\Hash as FacadesHash;

class OrderControllerCopy extends Controller
{
    use OrderTrait;

    protected $level_id;
    protected $user_id;


    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->is_admin == 1) {
                $this->level_id = auth()->user()->user_id;
            } else {
                $this->level_id = auth()->user()->userinfo->created_by;
            }
            $this->user_id = auth()->user()->user_id;
            return $next($request);
        });
    }


    function getCategory()
    {
        try {
            $userId = auth()->user()->id;
            $categories = DB::table('categories')
                ->select('categories.*') // Add the columns you need
                ->where('created_by', $userId)
                ->where('status', 1)
                ->where('parent_category', 0)
                ->orderBy('position', 'asc')
                ->get();

            if ($categories->isEmpty()) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'categories' => $categories,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found' . $e->getMessage(),
                'data' => [
                    'categories' => [],
                ]
            ], 404);
        }
    }

    function getCustomer()
    {

        try {
            $customers = DB::table('customers')
                ->select('customers.*', 'customers.is_taxable as customer_is_taxable', 'customers.enable_shipping_zone as customer_enable_shipping_zone', 'users.*', 'user_info.*')
                ->join('users', 'customers.customer_user_id', '=', 'users.user_id')
                ->join('user_info', 'customers.customer_user_id', '=', 'user_info.id')
                ->where('customers.level_id', $this->level_id)
                ->where('users.status', 1)
                ->where('user_info.wholesaler_connection', 1)
                ->orderBy('customer_id', 'desc')
                ->get();

            if ($customers->isEmpty()) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'customers' => $customers,
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


    public function generate_order_id_based_on_format(Request $request)
    {

        try {

            $userInfo = DB::table('user_info')->find($this->user_id);
            $companyName = $userInfo->company ?? DB::table('company_profile')->where('user_id', $userInfo->created_by)->value('company_name');
            $compName = $companyName ? str_replace(' ', '', $companyName) : 'XXXX';
            $compProfileData = $this->getCompanyProfileOrderConditionSettings();

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


    public function getProducts($category_id)
    {
        try {
            $products = DB::table('product_tbl')
                ->select('product_id', 'category_id', 'product_name', 'default')
                ->where('category_id', $category_id)
                ->where('active_status', 1)
                ->orderBy('position', 'asc')
                ->orderBy('product_name', 'asc')
                ->get();

            if ($products->isEmpty()) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    "products" => $products,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'products' => [],
                ]
            ], 404);
        }
    }



    // public function getProductToAttribute($product_id = '')
    // {
    //     $onKeyup = "checkTextboxUpcharge()";
    //     $level = 1;
    //     $attributeData = [];

    //     $attributes = DB::table('product_attribute')
    //         ->select('product_attribute.*', 'attribute_tbl.attribute_name', 'attribute_tbl.attribute_type')
    //         ->join('attribute_tbl', 'attribute_tbl.attribute_id', '=', 'product_attribute.attribute_id')
    //         ->where('product_attribute.product_id', $product_id)
    //         ->orderBy('attribute_tbl.position', 'ASC')
    //         ->get()
    //         ->toArray();

    //     $p = DB::table('product_tbl')->where('product_id', $product_id)->first();
    //     $category_id = (!empty($p->category_id) ? $p->category_id : '');

    //     $fraction_option = '';
    //     if ($category_id != '') {
    //         $hw1 = DB::table('categories')->select('fractions')->where('category_id', $category_id)->first();
    //         $fracs1 = $hw1->fractions;
    //         $fracs = explode(",", $fracs1);
    //         $hw2 = DB::table('width_height_fractions')->select('id', 'fraction_value')->orderBy('decimal_value', 'asc')->get()->toArray();

    //         foreach ($hw2 as $row) {
    //             if (in_array($row->fraction_value, $fracs)) {
    //                 $fraction_option .= '<option value="' . $row->id . '">' . $row->fraction_value . '</option>';
    //             }
    //         }
    //     }

    //     $q = '';
    //     $main_price = 0;
    //     if (isset($p->price_style_type)) {
    //         if ($p->price_style_type == 3) {
    //             $main_price = $p->fixed_price;
    //         } elseif ($p->price_style_type == 2) {
    //             $main_price = $p->sqft_price;
    //         }
    //     }

    //     foreach ($attributes as $attribute_key => $attribute) {
    //         if ($attribute->attribute_type == 3) {
    //             $options = DB::table('attr_options')
    //                 ->select('attr_options.*', 'product_attr_option.id', 'product_attr_option.product_id')
    //                 ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
    //                 ->where('product_attr_option.pro_attr_id', $attribute->id)
    //                 ->orderBy('attr_options.att_op_id', 'ASC')
    //                 ->orderBy('attr_options.position', 'ASC')
    //                 ->get()
    //                 ->toArray();

    //             foreach ($options as $op) {
    //                 $ctm_class = "op_text_box_" . $op->att_op_id;

    //                 $attributeData[] = [
    //                     'attribute_name' => $attribute->attribute_name,
    //                     'attribute_id' => $attribute->attribute_id,
    //                     'op_id' => $op->id . '_' . $op->att_op_id,
    //                     'level' => $level,
    //                     'price_type' => $op->price_type,
    //                     'price' => $op->price,
    //                     'main_price' => $main_price,
    //                     'product_id' => $op->product_id,
    //                 ];
    //             }
    //         } elseif ($attribute->attribute_type == 2) {
    //             $options = DB::table('attr_options')
    //                 ->select('attr_options.*', 'product_attr_option.id')
    //                 ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
    //                 ->where('product_attr_option.pro_attr_id', $attribute->id)
    //                 ->orderBy('attr_options.position', 'ASC')
    //                 ->orderBy('attr_options.att_op_id', 'ASC')
    //                 ->get()
    //                 ->toArray();

    //             foreach ($options as $op) {
    //                 $attributeData[] = [
    //                     'attribute_name' => $attribute->attribute_name,
    //                     'attribute_id' => $attribute->attribute_id,
    //                     'op_id' => $op->id . '_' . $op->att_op_id,
    //                     'level' => $level,
    //                     'default' => $op->default,
    //                 ];
    //             }
    //         } elseif ($attribute->attribute_type == 5) {
    //             if ($attribute->attribute_name == "Tilt Bar Split Location") {
    //                 $attributeData[] = [
    //                     'attribute_name' => $attribute->attribute_name,
    //                     'op_op_id' => $attribute->attribute_id,
    //                     'default' => 0,
    //                 ];
    //             } else {
    //                 $attributeData[] = [
    //                     'attribute_name' => $attribute->attribute_name,
    //                     'attribute_id' => $attribute->attribute_id,
    //                     'default' => 0,
    //                 ];
    //             }
    //         } elseif ($attribute->attribute_type == 1) {
    //             $ctm_class = "text_box_" . $attribute->attribute_id;
    //             $level = 0;

    //             $attributeData[] = [
    //                 'attribute_name' => $attribute->attribute_name,
    //                 'attribute_id' => $attribute->attribute_id,
    //                 'level' => $level,
    //             ];
    //         }
    //     }
    //     unset($attributes);

    //     return $attributeData;
    // }






    function getRooms()
    {

        try {

            $rooms = DB::table('rooms')
                ->where(function ($query) {
                    $query->where('created_by', 0)
                        ->orWhere('created_by', $this->level_id)
                        ->orWhere('wholesaler_by', $this->level_id);
                })
                ->orderBy('room_name')
                ->get();

            if ($rooms->isEmpty()) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'rooms' => $rooms,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'rooms' => [],
                ]
            ], 404);
        }
    }

    function saveRoom(RoomCreateRequest $request)
    {
        try {
            $room_name = $request->input('room_name');
            $user_id = auth()->user()->id;

            $last_room_id = DB::table('rooms')->insertGetId([
                'room_name' => $room_name,
                'created_by' => $user_id,
            ]);

            if ($last_room_id > 0) {
                $room = DB::table('rooms')->where('id', $last_room_id)->first();

                return response()->json([
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'Data inserted successfully',
                    'data' => [
                        "room" => $room
                    ]
                ]);
            }
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data is not inserted',
                'data' => [
                    "room" => [],
                ]
            ], 404);
        }
    }


    public function getFractions($category_id)
    {
        try {
            // Fetch data from the categories table
            $hw1 = DB::table('categories')
                ->select('fractions')
                ->where('category_id', $category_id)
                ->first();

            $fracs1 = $hw1->fractions;
            $fracs = explode(",", $fracs1);
            $selectedFractions = [];

            // Fetch data from the width_height_fractions table
            $hw2 = DB::table('width_height_fractions')
                ->select('id', 'fraction_value', 'decimal_value')
                ->orderBy('decimal_value', 'asc')
                ->get();

            foreach ($hw2 as $row) {
                if (in_array($row->fraction_value, $fracs)) {
                    $selectedFractions[] = [
                        'id' => $row->id,
                        'fraction_value' => $row->fraction_value,
                        'decimal_value' => $row->decimal_value,
                    ];
                }
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => [
                    'fractions' => $selectedFractions,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'fractions' => [],
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


    public function getColorPartanModel($product_id = '')
    {

        try {
            $result = [];

            $pp = DB::table('product_tbl')
                ->select('colors', 'pattern_models_ids', 'hide_pattern', 'category_id', 'price_style_type')
                ->where('product_id', $product_id)
                ->first();

            if (!isset($pp)) {
                throw new \Exception();
            }

            if ($pp->hide_pattern == 1) {
                return $result;
            }
            $pattern_model = [];

            $pattern_model = DB::table('pattern_model_tbl')
                ->whereIn('pattern_model_id', explode(',', $pp->pattern_models_ids))
                ->orderBy("position", "asc")
                ->orderBy('pattern_name', 'asc')
                ->get();

            $user_detail = $this->getCompanyProfileOrderConditionSettings();
            $pattern_label = 'pattern';

            $result[] = [
                'label' => $pattern_label,
                'options' => [
                    ['value' => '', 'label' => '-- Select one --'],
                    ['value' => '0', 'label' => 'Manual Entry', 'selected' => $user_detail->enable_fabric_manual_entry == 1],
                ],
            ];

            foreach ($pattern_model as $pattern) {
                $result[0]['options'][] = [
                    'value' => $pattern->pattern_model_id,
                    'label' => $pattern->pattern_name,
                    'selected' => $pattern->default == '1',
                ];
            }

            $price_style_type = $pp->price_style_type;
            $display_fabric_price = $user_detail->display_fabric_price;

            if (in_array($price_style_type, [5, 6, 10]) && $display_fabric_price) {
                $result[] = [
                    'input_type' => 'text',
                    'input_id' => 'fabric_price',
                    'input_name' => 'fabric_price',
                    'input_value' => '0',
                ];
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => $result,

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


    public function getColorModel($product_id, $pattern_id)
    {

        try {
            $result = [];

            $colorIds = DB::table('colors')
                ->select('id')
                ->where('pattern_id', $pattern_id)
                ->where('created_by', $this->level_id)
                ->where('status', 1)
                ->orderBy('position', 'asc')
                ->orderBy('color_name', 'asc')
                ->get()
                ->pluck('id')
                ->toArray();

            $where = "FIND_IN_SET('" . $pattern_id . "', pattern_models_ids)";
            $product_color_data = DB::table('product_tbl')
                ->where('product_id', $product_id)
                ->whereRaw($where)
                ->first();

            if (!isset($product_color_data->hide_color) || $product_color_data->hide_color == 1) {
                // Hide color option
                return $result;
            }
            // Display color option
            $product_color_ids = $product_color_data->colors != '' ? array_intersect($colorIds, explode(',', $product_color_data->colors)) : [];

            $colors = [];
            if (count($product_color_ids) > 0) {
                $colors = DB::table('colors')
                    ->whereIn('id', $product_color_ids)
                    ->where('status', 1)
                    ->where('created_by', $this->level_id)
                    ->orderBy('position', 'asc')
                    ->orderBy('color_name', 'asc')
                    ->get();
            }

            $created_by = $this->level_id;
            $user_detail = $this->getCompanyProfileOrderConditionSettings();
            $color_label = 'color';

            $result[] = [
                'label' => $color_label,
                'onChange' => 'getColorCode(this.value)',

                'options' => [
                    ['value' => '', 'text' => '-- Select one --'],
                    ['value' => '0', 'text' => 'Manual Entry', 'selected' => @$user_detail->enable_color_manual_entry == 1],
                ],
            ];

            foreach ($colors as $color) {
                $result[0]['options'][] = [
                    'value' => $color->id,
                    'text' => $color->color_name,
                    'selected' => $color->default == '1',
                ];
            }

            $result[] = [
                'input_type' => 'text',
                'input_id' => 'colorcode',
                'onKeyup' => 'getColorCode_select(this.value)',
                'placeholder' => $color_label . ' Code',
                'class' => 'form-control',
            ];

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' => $result,

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

    public function getColorCode($id = '0')
    {

        try {

            $color = DB::table('colors')
                ->select('color_number')
                ->where('id', $id)
                ->where('created_by', $this->level_id)
                ->where('status', 1)
                ->first();

            if (empty($color)) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' =>  $color,

            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'rooms' => [],
                ]
            ], 404);
        }
    }


    public function getColorCodeSelect($keyword, $patternId = '')
    {

        try {

            $color = DB::table('colors')->where('color_number', $keyword)
                ->where('pattern_id', $patternId)
                ->where('created_by', $this->level_id)
                ->where('status', 1)
                ->first();

            if (empty($color)) {
                throw new \Exception();
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data retrieved successfully',
                'data' =>  $color,

            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => [
                    'rooms' => [],
                ]
            ], 404);
        }
    }



    public function existingShippingAddress($customer_id)
    {
        $customerDetails = DB::table('customers')->where('customer_id', $customer_id)->first();
        $shippingAddress = DB::table('shipping_address_info')->where('customer_id', $customer_id)->first();


        return   $shippingAddress;

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

            $resInfo =  [
                'billingAddressLabel' => $billingAddressLabel,
                'customerDetails' => $customerDetails,
                'customerAddress' => $customerAddress,
            ];
        }

        if (!empty($shippingAddress->address) && !empty($shippingAddress->city) && !empty($customerDetails->different_shipping_address)) {
            $resAddress = explode(",", $shippingAddress->address)[0];
            $addressLabel = "";

            switch ($shippingAddress->billing_address_label) {
                case 'is_residential':
                    $addressLabel = "Residential";
                    break;
                case 'commercial':
                    $addressLabel = "Commercial";
                    break;
                case 'storage_facility':
                    $addressLabel = "Storage Facility";
                    break;
                case 'freight_terminal':
                    $addressLabel = "Freight Terminal";
                    break;
            }

            $resInfo =  [
                'addressLabel' => $addressLabel,
                'shippingAddress' => $shippingAddress,
                'resAddress' => $resAddress,
            ];
        }

        return $resInfo;
    }







    public function getProductToAttribute($product_id = '')
    {
        $result = [];

        if ($product_id == '') {
            return $result;
        }


        $result['pattern'] = $this->getColorPartanModel($product_id);

        $onKeyup = "checkTextboxUpcharge()";
        $level = 1;

        $attributes = DB::table('product_attribute')
            ->select('product_attribute.*', 'attribute_tbl.attribute_name', 'attribute_tbl.attribute_type')
            ->join('attribute_tbl', 'attribute_tbl.attribute_id', '=', 'product_attribute.attribute_id')
            ->where('product_attribute.product_id', $product_id)
            ->orderBy('attribute_tbl.position', 'ASC')
            ->get();

        // dd($attributes->toSql());

        $p = DB::table('product_tbl')->where('product_id', $product_id)->first();

        $category_id = (!empty($p->category_id) ? $p->category_id : '');

        // Get fraction category wise: START
        $fraction_option = [];

        if ($category_id != '') {
            $hw1 = DB::table('categories')->select('fractions')->where('category_id', $category_id)->first();
            $fracs1 = $hw1->fractions;
            $fracs = explode(",", $fracs1);

            $hw2 = DB::table('width_height_fractions')->select('id', 'fraction_value')->orderBy('decimal_value', 'asc')->get();

            foreach ($hw2 as $row) {
                if (in_array($row->fraction_value, $fracs)) {
                    $fraction_option[] = ['id' => $row->id, 'value' => $row->fraction_value];
                }
            }
        }
        // Get fraction category wise: END

        $main_price = 0;

        $discountData = DB::table('c_cost_factor_tbl')
            ->select("individual_cost_factor", "costfactor_discount")
            ->where('product_id', $product_id)
            ->where('level_id', $level)
            ->first();

        if (!empty($discountData->individual_cost_factor)) {
            $result['individual_cost_factor'] = $discountData->individual_cost_factor;
        }

        if (!empty($p->price_style_type) && $p->price_style_type == 3) {
            $result['pricestyle'] = $p->price_style_type;
            $result['main_price'] = $p->fixed_price;
            $main_price = $p->fixed_price;
        } elseif (!empty($p->price_style_type) && $p->price_style_type == 2) {
            $result['pricestyle'] = $p->price_style_type;
            $result['sqr_price'] = $p->sqft_price;
            $result['main_price'] = $p->sqft_price;
            $main_price = $p->sqft_price;
        }

        // dd($attributes);

        foreach ($attributes as $attribute_key => $attribute) {
            if ($attribute->attribute_type == 3) {
                $options = DB::table('attr_options')
                    ->select('attr_options.*', 'product_attr_option.id', 'product_attr_option.product_id')
                    ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
                    ->where('product_attr_option.pro_attr_id', $attribute->id)
                    ->orderBy('attr_options.position', 'ASC')
                    ->orderBy('attr_options.att_op_id', 'ASC')
                    ->get();

                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'attribute_id' => $attribute->attribute_id,
                    'options' => [],
                ];

                foreach ($options as $op) {

                    $ret_attr_query = DB::table('ret_attr_options')
                        ->where('att_op_id', $op->att_op_id)
                        ->where('created_by', $this->user_id)
                        ->first();

                    if (!empty($ret_attr_query->retailer_att_op_id) && $ret_attr_query->retailer_att_op_id != '' && $ret_attr_query->retailer_price != '') {
                        $op->price_type = $ret_attr_query->retailer_price_type;
                        $op->price = $ret_attr_query->retailer_price;
                    }

                    $optionData = [
                        'option_label' => $op->op_op_name,
                        'op_id' => $op->id . '_' . $op->att_op_id,
                        'attr_id' => $op->att_op_id,
                        'onkeyup' => $onKeyup,
                    ];

                    $attributeData['options'][] = $optionData;
                }

                $result['attributes'][] = $attributeData;
            } elseif ($attribute->attribute_type == 2) {

                $options = DB::table('attr_options')
                    ->select('attr_options.*', 'product_attr_option.id')
                    ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
                    ->where('product_attr_option.pro_attr_id', $attribute->id)
                    ->orderBy('attr_options.position', 'ASC')
                    ->orderBy('attr_options.att_op_id', 'ASC')
                    ->get();


                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'attribute_id' => $attribute->attribute_id,
                    'type' => 'select',
                    'options' => [],
                ];

                // $attributeData['op_value'] = '';

                foreach ($options as $op) {
                    $sl1 = ($op->default == 1 ? 1 : 0);

                    $optionData = [
                        'value' => $op->id . '_' . $op->att_op_id,
                        'label' => $op->option_name,
                        'selected' => $sl1,
                        'onChange' =>  $this->getProductAttrOptionOption($op->id, $attribute->attribute_id, 0)
                    ];

                    $attributeData['options'][] = $optionData;
                }

                $result['attributes'][] = $attributeData;
            } elseif ($attribute->attribute_type == 5) {

                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'attribute_id' => $attribute->attribute_id,
                ];
                $attributeData['input'] = [
                    'type' => 'text',
                    'name' => 'attribute_value[]',
                    'onkeyup' => 'checkTextboxUpcharge()',
                ];

                $attributeData['select'] = [
                    'name ' => "fraction_" . $attribute->attribute_id . "[]",
                    'options' => $fraction_option,
                ];

                $result['attributes'][] = $attributeData;
            } elseif ($attribute->attribute_type == 1) {
                $ctm_class = "text_box_" . $attribute->attribute_id;
                $level = 0;
                $height = $attribute->attribute_name;

                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'attribute_id' => $attribute->attribute_id,
                    'type' => 'input',
                    'onkeyup' => $onKeyup,
                ];

                $result['attributes'][] = $attributeData;
            }
        }
        unset($attributes);

        return $result;
    }









   



    public function getProductAttrOptionOption($proAttOpId, $attributeId, $mainPrice, $individualCostFactor = 0, $selectedMultiOption = '')
    {

        $onKeyup = "checkTextboxUpcharge()";
        $level = 2;

        $options = DB::table('product_attr_option')
            ->select('attr_options.*', 'product_attr_option.product_id', 'product_attr_option.id as adddd')
            ->join('attr_options', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
            ->where('product_attr_option.id', $proAttOpId)
            ->orderBy('attr_options.position', 'ASC')
            ->orderBy('attr_options.att_op_id', 'ASC')
            ->first();
        // dd($options->toSql());
        // dd( $options);
        $categoryId = '';

        // Get product category based on product id: START
        if (isset($options->product_id) && $options->product_id != '') {
            $productData = DB::table('product_tbl')
                ->where('product_id', $options->product_id)
                ->first();

            if (isset($productData->category_id) && $productData->category_id != '') {
                $categoryId = $productData->category_id;
            }
        }
        // Get product category based on product id: END

        $optionsArray = [];

        // price value 
        // if (isset($options->price_type)) {
        //     if ($options->price_type == 1) {

        //         $price_total = $mainPrice + optional($options)->price;

        //         $contribution_price = !empty($options->price) ? $options->price : 0;
        //         $q .= '<input placeholder="hidden" type="text" value="' . $contribution_price . '" class="form-control contri_price">';

        //         // For Drapery price static condition : START
        //         $drapery_price = !empty($options->attribute_value) ? $options->attribute_value : 0;
        //         $q .= '<input placeholder="hidden" type="text" value="' . $drapery_price . '" class="drapery_attr_price_value">';
        //         $q .= '<input placeholder="hidden" type="text" value="' . $drapery_price . '" class="form-control drapery_attribute_price_value contri_price">';
        //         // For Drapery price static condition : END
        //     } else {
        //         $cost_factor_data = $this->commonWholesalerToRetailerCommission($options->product_id, 5);

        //         // return $cost_factor_data;
        //         $cost_factor_rate = $cost_factor_data['dealer_price'];
        //         //  dd($cost_factor_rate);
        //         $price_total = round((($mainPrice * $cost_factor_rate * optional($options)->price) / 100), 2);
        //         $contribution_price = !empty($price_total) ? $price_total : 0;
        //         $q .= '<input placeholder="hidden" type="text" value="' . $contribution_price . '" class="form-control contri_price">';

        //         // For Drapery price static condition : START
        //         $drapery_price = !empty($options->attribute_value) ? $options->attribute_value : 0;
        //         if ($drapery_price > 0) {
        //             $drapery_price = ($drapery_price / 100);
        //         }
        //         $q .= '<input placeholder="hidden" type="text" value="' . $drapery_price . '" class="drapery_attr_price_value">';
        //         $q .= '<input placeholder="hidden" type="text" value="' . $drapery_price . '" class="form-control drapery_attribute_price_value contri_price">';
        //         // For Drapery price static condition : END
        //     }
        // }
        if ($options->option_type == 5) {
            // Text + Fraction

            $opops = DB::table('attr_options_option_tbl')
                ->select('attr_options_option_tbl.*', 'product_attr_option_option.id', 'product_attr_option_option.product_id')
                ->join('product_attr_option_option', 'attr_options_option_tbl.op_op_id', '=', 'product_attr_option_option.op_op_id')
                ->where('product_attr_option_option.pro_att_op_id', $proAttOpId)
                ->orderBy('attr_options_option_tbl.att_op_op_position', 'ASC')
                ->orderBy('attr_options_option_tbl.op_op_id', 'ASC')
                ->get()
                ->toArray();

            // Get fraction category wise : START
            $fractionOptions = [];
            if ($categoryId != '') {
                $hw1 = DB::table('categories')->select('fractions')->where('category_id', $categoryId)->first();
                $fracs1 = $hw1->fractions;
                $fracs = explode(",", $fracs1);
                $hw2 = DB::table('width_height_fractions')->select('id', 'fraction_value')->orderBy('decimal_value', 'asc')->get();
                foreach ($hw2 as $row) {
                    if (in_array($row->fraction_value, $fracs)) {
                        $fractionOptions[] = [
                            'id' => $row->id,
                            'value' => $row->fraction_value,
                        ];
                    }
                }
            }
            // Get fraction category wise : END

            foreach ($opops as $kk => $op_op) {
                $optionArray = [
                    'label' => $op_op->op_op_name,
                    'op_op_id' => $op_op->op_op_id,
                    'id' => $op_op->id,
                    'att_op_id' => $options->att_op_id,
                ];

                if ($op_op->op_op_name == "Divider Rail  #1" || $op_op->op_op_name == "Divider Rail  #2") {
                    $optionArray['input'] = [
                        // 'label' => $op_op->op_op_name,
                        // 'input' => [
                        'name' => 'op_op_value_' . $attributeId . '[]',
                        // 'class' => 'form-control convert_text_fraction op_op_text_box_' . $op_op->op_op_id,
                        'id' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                        // 'required',
                        'onkeyup' => 'checkTextboxUpcharge()',
                        // 'data-level' => $level,
                        // 'data-attr-id' => $op_op->op_op_id,
                        // 'value' => '0',
                        // ],
                    ];
                } else {
                    $optionArray['input'] = [
                        // 'label' => $op_op->op_op_name,
                        'input' => [
                            'name' => 'op_op_value_' . $attributeId . '[]',
                            // 'class' => 'form-control convert_text_fraction op_op_text_box_' . $op_op->op_op_id,
                            'id' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                            // 'required',
                            'onkeyup' => 'checkTextboxUpcharge()',
                            // 'data-level' => $level,
                            // 'data-attr-id' => $op_op->op_op_id,
                        ],
                    ];
                }

                $optionArray['select'] = [
                    'label' => 'Select Fraction',
                    // 'select' => [
                    // 'class' => 'form-control select_text_fraction key_text_fraction_' . $kk,
                    'name' => 'fraction_' . $attributeId . '[]',
                    // 'data-placeholder' => '-- Select one --',
                    'onchange' => 'checkTextboxUpcharge()',
                    // 'data-level' => $level,
                    // 'data-attr-id' => $op_op->op_op_id,
                    'options' => [
                        [
                            'value' => '',
                            'label' => '-- Select one --',
                        ],
                        ...array_map(function ($fraction) {
                            return [
                                'value' => $fraction['id'],
                                'label' => $fraction['value'],
                            ];
                        }, $fractionOptions),
                    ],
                    // ],
                ];

                $optionsArray[] = $optionArray;

                $optionsArray[count($optionsArray) - 1]['contri_price'] = $this->contriPrice($op_op->att_op_op_price_type, $op_op->att_op_op_price, $mainPrice, $op_op->product_id);
            }

            unset($opops);
        }
        if ($options->option_type == 4) {

            // dd(4);
            // Multi option
            $opops = DB::table('product_attr_option_option')
                ->select('attr_options_option_tbl.*', 'product_attr_option_option.id', 'product_attr_option_option.product_id')
                ->join('attr_options_option_tbl', 'attr_options_option_tbl.op_op_id', '=', 'product_attr_option_option.op_op_id')
                ->where('product_attr_option_option.pro_att_op_id', $proAttOpId)
                ->orderBy('attr_options_option_tbl.att_op_op_position', 'ASC')
                ->orderBy('attr_options_option_tbl.op_op_id', 'ASC')
                ->get();

            // dd($opops->toSql());

            foreach ($opops as $op_op) {
                $opopops = DB::table('attr_options_option_option_tbl')
                    ->where('attribute_id', $attributeId)
                    ->where('att_op_op_id', $op_op->op_op_id)
                    ->get();

                $optionsArray[] = [
                    'label' => $op_op->op_op_name,
                    'op_op_id' => $op_op->op_op_id,
                    'id' => $op_op->id,
                    'att_op_id' => $options->att_op_id,
                ];

                if ($op_op->type == 2) {
                    $selectOptions = [];
                    $selectedValues = [];

                    foreach ($opopops as $keyss => $opopop) {
                        $selected = ($opopop->att_op_op_op_default == '1') ? true : false;
                        $optionsArray[count($optionsArray) - 1]['type'] = 'select';

                        // if ($keyss === 0) {
                        //     $optionsArray[count($optionsArray) - 1]['option_vals'] = $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id;
                        // }

                        // $val = $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id;

                        // if (isset($selectedValues)) {
                        //     $selected = (in_array($val, $selectedValues)) ? true : false;
                        // }

                        // if ($op_op->op_op_name == 'Control Position' && $opopop->att_op_op_op_name == 'Right') {
                        //     $optionsArray[count($optionsArray) - 1]['option_vals'] = $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id;

                        //     if (!isset($selectedValues)) {
                        //         $selected = ($opopop->att_op_op_op_default == '1') ? true : false;
                        //     }
                        // }

                        // if ($selected) {
                        //     $optionsArray[count($optionsArray) - 1]['final_mul_op_value'] = $val;
                        // }

                        $selectOptions[] = [
                            'value' => $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id,
                            // 'selected' => $selected,
                            'label' => $opopop->att_op_op_op_name,
                            'onchange' => $this->multioption_price_value($opopop->att_op_op_op_id, $attributeId, 0)

                        ];
                    }

                    $optionsArray[count($optionsArray) - 1]['options'] = $selectOptions;
                } elseif ($op_op->type == 1) {
                    // Handle type 1 (Text input) options
                    $cordlength = [];
                    $cordlength1 = [];
                    $upcharge = [];
                    $upcharge1 = [];
                    $cordVal = '';
                    $widthVal = '';
                    $heightVal = '';

                    if ($op_op->op_op_name == 'Value') {
                        $segments = request()->segment(8);

                        if (isset($segments) && !empty($segments)) {
                            $segments = request()->segment(8);

                            if (isset($segments) && !empty($segments)) {
                                $particulars = DB::table('b_level_quatation_attributes')
                                    ->select('b_level_quatation_attributes.product_attribute', 'b_level_qutation_details.width', 'b_level_qutation_details.height')
                                    ->join('b_level_qutation_details', 'b_level_qutation_details.row_id', '=', 'b_level_quatation_attributes.fk_od_id')
                                    ->where('fk_od_id', $segments)
                                    ->first();

                                $selectedAttributes = json_decode($particulars->product_attribute);

                                if (isset($selectedAttributes) && !empty($selectedAttributes)) {
                                    foreach ($selectedAttributes as $attributes) {
                                        if (isset($attributes->opop[0]->op_op_value)) {
                                            $values = $attributes->opop[0]->op_op_value;
                                            array_push($cordlength1, $values);
                                        }
                                    }

                                    $widthVal = $particulars->width;
                                    $heightVal = $particulars->height;
                                }
                            }

                            if (!empty($cordlength[0])) {
                                $cordVal = $cordlength[0];
                            } else {
                                if (!empty($cordlength1[0])) {
                                    $cordVal = $cordlength1[0];
                                }
                            }
                        }

                        // $class = 'cords_length';
                        // $change = "onBlur='changeLength();' data-text-type='code-legth-val'";

                        $optionsArray[count($optionsArray) - 1]['cords_length_val'] = $cordVal;
                        $optionsArray[count($optionsArray) - 1]['change_height'] = $heightVal;
                        $optionsArray[count($optionsArray) - 1]['change_width'] = $widthVal;
                    } else {
                        // $class = '';
                        // $change = '';
                        // $tag = [];
                    }

                    $optionsArray[count($optionsArray) - 1]['text_input'] = [
                        'label' => 'Text Input',
                        // 'class' => 'cls_text_op_op_value ' . $class,
                        // 'onkeyup' => $onKeyup,
                        'name' => 'op_op_value_' . $attributeId . '[]',

                        // 'attributes' => [
                        //     'data-level' => $level,
                        //     'data-attr-id' => $op_op->op_op_id,
                        //     $change,
                        //     'required',
                        //     'data-attr-name' => $op_op->op_op_name,
                        // ],
                        // 'tag' => $tag,
                    ];

                    $optionsArray[count($optionsArray) - 1]['contri_price'] = $this->contriPrice($op_op->att_op_op_price_type, $op_op->att_op_op_price, $mainPrice, $op_op->product_id);

                    // For Drapery price : START
                    $drapery_price = (!empty($op_op->att_op_op_attr_value) ? $op_op->att_op_op_attr_value : 0);

                    if ($op_op->att_op_op_price_type != 1) {
                        // If not 1 then it's percentage price
                        if ($drapery_price > 0) {
                            $drapery_price = ($drapery_price / 100);
                        }
                    }

                    $optionsArray[count($optionsArray) - 1]['drapery_attr_price_value'] = $drapery_price;
                    // For Drapery price : END
                } elseif ($op_op->type == 6) {


                    $optionsArray[count($optionsArray) - 1]['type'] = 'multi_select';
                    // $optionsArray[count($optionsArray) - 1]['onChange'] = 'MultiOptionOptionsOptionOptions()';


                    $multiselectOptions = [];

                    foreach ($opopops as $keyss => $opopop) {
                        $selected = ($opopop->att_op_op_op_default == '1') ? true : false;

                        $multiselectOptions[] = [
                            'value' => $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id,
                            // 'selected' => $selected,
                            'label' => $opopop->att_op_op_op_name,
                        ];
                    }

                    $optionsArray[count($optionsArray) - 1]['options'] = $multiselectOptions;
                } else {
                    // Handle other types of options
                    $optionsArray[count($optionsArray) - 1]['other_type'] = [
                        'contri_price' => $this->contriPrice($op_op->att_op_op_price_type, $op_op->att_op_op_price, $mainPrice, $op_op->product_id),
                    ];
                }
            }

            unset($opops);
        } elseif ($options->option_type == 2) {
            $opops = DB::table('product_attr_option_option')
                ->select('attr_options_option_tbl.*', 'product_attr_option_option.id')
                ->join('attr_options_option_tbl', 'attr_options_option_tbl.op_op_id', '=', 'product_attr_option_option.op_op_id')
                ->where('product_attr_option_option.pro_att_op_id', $proAttOpId)
                ->orderBy('attr_options_option_tbl.att_op_op_position', 'ASC')
                ->orderBy('attr_options_option_tbl.op_op_id', 'ASC')
                ->get()
                ->toArray();

            $optionTypeArray = [
                'input_hidden' => [
                    'type' => 'hidden',
                    'name' => 'op_op_value_' . $attributeId . '[]',
                ],

                'select' => [
                    'id' => 'op_' . $options->att_op_id,
                    'name' => 'op_op_id_' . $attributeId . '[]',
                    'onChange' => 'OptionOptionsOption(this.value,' . $attributeId . ')',
                    'options' => [
                        [
                            'value' => '',
                            'label' => '--Select one--',
                        ],
                    ],
                ],



            ];

            $selected_values = !empty($selected_option_type) ? explode('@', $selected_option_type) : [];

            foreach ($opops as $op_op) {
                $val = $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id;
                $selected = in_array($val, $selected_values) ? 'selected' : '';
                if (!isset($selected_values)) {
                    $selected = ($op_op->att_op_op_default == '1') ? 'selected' : '';
                }

                $optionTypeArray['select']['options'][] = [
                    'value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    'label' => $op_op->op_op_name,
                    // 'selected' => $selected,
                ];
            }

            unset($opops);

            $optionsArray[] = $optionTypeArray;
        } elseif ($options->option_type == 3) {
            // dd(4);
            $opops = DB::table('product_attr_option_option')
                ->select('attr_options_option_tbl.*', 'product_attr_option_option.id', 'product_attr_option_option.product_id')
                ->join('attr_options_option_tbl', 'attr_options_option_tbl.op_op_id', '=', 'product_attr_option_option.op_op_id')
                ->where('product_attr_option_option.pro_att_op_id', $proAttOpId)
                ->orderBy('attr_options_option_tbl.att_op_op_position', 'ASC')
                ->orderBy('attr_options_option_tbl.op_op_id', 'ASC')
                ->get();

            foreach ($opops as $op_op) {
                $ctm_class = "op_op_text_box_" . $op_op->op_op_id;

                $optionArray = [

                    'label' =>  $op_op->op_op_name,

                    'input_text' => [
                        'type' => 'text',
                        'data-level' => $level,
                        'data-attr-id' => $op_op->op_op_id,
                        'onkeyup' => $onKeyup,
                        'name' => 'op_op_value_' . $attributeId . '[]',
                        // 'class' => 'form-control cls_text_op_op_value ' . $ctm_class,
                        // 'required' => true,
                        // 'data-attr-name' => $op_op->op_op_name,
                    ],

                    'input_hidden' => [
                        'type' => 'hidden',
                        'name' => 'op_op_id_' . $attributeId . '[]',
                        'value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    ],

                    'contri_price' => $this->contriPrice($op_op->att_op_op_price_type, $op_op->att_op_op_price, $mainPrice, $op_op->product_id),

                    'drapery_price' => [
                        'input_hidden' => [
                            'type' => 'hidden',
                            'value' => (!empty($op_op->att_op_op_attr_value) ? $op_op->att_op_op_attr_value : 0),
                            // 'class' => 'drapery_attr_price_value',
                        ],
                        'input_hidden_form_control' => [
                            'type' => 'hidden',
                            'value' => (!empty($op_op->att_op_op_attr_value) ? $op_op->att_op_op_attr_value : 0),
                            // 'class' => 'form-control drapery_attribute_price_value contri_price',
                        ],
                    ],


                ];

                $optionsArray[] = $optionArray;
            }

            unset($opops);
        } elseif ($options->option_type == 6) {
            $opops = DB::table('product_attr_option_option')
                ->select('attr_options_option_tbl.*', 'product_attr_option_option.id')
                ->join('attr_options_option_tbl', 'attr_options_option_tbl.op_op_id', '=', 'product_attr_option_option.op_op_id')
                ->where('product_attr_option_option.pro_att_op_id', $proAttOpId)
                ->orderBy('attr_options_option_tbl.att_op_op_position', 'ASC')
                ->orderBy('attr_options_option_tbl.op_op_id', 'ASC')
                ->get();

            $optionArray = [
                'input_hidden' => [
                    'type' => 'hidden',
                    'name' => 'op_op_value_' . $attributeId . '[]',
                ],

                'select' => [
                    'id' => 'op_' . $options->att_op_id,
                    'name' => 'op_op_id_' . $attributeId . '[]',
                    'onChange' => 'MultiOptionOptionsOption(this,' . $attributeId . ')',
                    'data-placeholder' => '-- Select pattern/model --',
                    'multiple' => true,
                    'elements' => [],
                ],


            ];

            foreach ($opops as $op_op) {
                $selected = ($op_op->att_op_op_default == '1') ? 'selected' : '';
                $optionArray['options'][] = [
                    'value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    'selected' => $selected,
                    'text' => $op_op->op_op_name,
                ];
            }

            $optionsArray[] = $optionArray;
        } elseif ($options->option_type == 1) {
            $ctm_class = "op_text_box_" . $options->att_op_id;
            $level = 1;

            $optionArray = [
                'input_hidden' => [
                    'type' => 'hidden',
                    'value' => $options->att_op_id,
                    'name' => 'op_id_' . $attributeId . '[]',
                ],

                'input_text' => [
                    'type' => 'text',
                    'data-level' => $level,
                    'data-attr-id' => @$options->att_op_id,
                    'onkeyup' => $onKeyup,
                    'name' => 'op_value_' . $attributeId . '[]',
                ]
            ];


            $optionsArray[] = $optionArray;
        }

        return $optionsArray;
    }





    

    


    public function multioption_price_value($op_op_op_id, $attribute_id, $main_price, $selected_option_fifth = '')
    {
        $opopopop = DB::table('attr_options_option_option_tbl')
            ->select('*')
            ->where('attribute_id', $attribute_id)
            ->where('att_op_op_op_id', $op_op_op_id)
            ->orderBy('att_op_op_op_position', 'ASC')
            ->first();
        $output = [];


        if ($opopopop->att_op_op_op_price_type == 1) {
            $price_total = $main_price + optional($opopopop)->att_op_op_op_price;
            $contribution_price = !empty($opopopop->att_op_op_op_price) ? $opopopop->att_op_op_op_price : 0;
            $output['data'][] =
                [
                    'type' => 'hidden',
                    'value' => $contribution_price,
                    'name' => 'contri_price'
                ];
        } else {
            $product_attr_data = DB::table('product_attr_option_option_option')
                ->where('op_op_op_id', $op_op_op_id)
                ->first();

            if (isset($product_attr_data->product_id)) {
                $cost_factor_data = $this->Common_wholesaler_to_retailer_commission($product_attr_data->product_id);
                $cost_factor_rate = $cost_factor_data['dealer_price'];
            } else {
                $cost_factor_rate = 1;
            }

            $price_total = ($main_price * $cost_factor_rate * optional($opopopop)->att_op_op_op_price) / 100;
            $contribution_price = !empty($price_total) ? $price_total : 0;

            $output['data'][] = [
                'type' => 'hidden',
                'value' => $contribution_price,
                'name' => 'contri_price'
            ];
        }


        if ($opopopop->att_op_op_op_type == 2) {

            $output['data'][] = [
                'type' => 'hidden',
                'value' => $opopopop->att_op_op_op_name,
                'name' => "op_op_value_' . $attribute_id . '[]"
            ];


            // $output[] = '<br><input type="hidden" name="op_op_value_' . $attribute_id . '[]" value=\'' . $opopopop->att_op_op_op_name . '\' class="form-control">';
            $opopopop = DB::table('attr_op_op_op_op_tbl')
                ->select('*')
                ->where('attribute_id', $attribute_id)
                ->where('op_op_op_id', $op_op_op_id)
                ->orderBy('att_op_op_op_op_position', 'ASC')
                ->orderBy('att_op_op_op_op_name', 'ASC')
                ->orderBy('op_op_op_id', 'ASC')
                ->get();

            // $output['name'] = "op_op_op_op_value_' . $attribute_id . '[]";

            // $output[] = '<div class="row fifth_attr_row"><label class="col-sm-2"></label><select class="form-control custom-select-css roller-screen-color-css select2 col-sm-4 cls_op_five_' . $attribute_id . '" id="op_op_op_' . $op_op_op_id . '"  name="op_op_op_op_id_' . $attribute_id . '[]" onChange="OptionFive(this.value,' . $attribute_id . ')" data-placeholder="-- Select pattern/model --" required>
            //             <option value="">--Select one--</option>';
            // $output['select'];

            $output['data'][] = [
                'type' => 'select',
                'name' => "op_op_op_op_id_" . $attribute_id . "[]",
                'onChange' => "OptionFive(this.value," . $attribute_id . ")",
                'options' => [
                    [
                        'value' => '',
                        'label' => '--Select one--',
                    ],
                ],
            ];

            $selected = '';
            $selected_values = !empty($selected_option_fifth) ? explode('@', $selected_option_fifth) : [];
            foreach ($opopopop as $op_op_op_op) {
                $val = $op_op_op_op->att_op_op_op_op_id . '_' . $op_op_op_id;
                $selected = in_array($val, $selected_values) ? 'selected' : '';
                $selected = !isset($selected_values) ? ($op_op_op_op->att_op_op_op_op_default == '1' ? 'selected' : '') : $selected;
                $output['data'][2]['options'][] = [
                    'value' => $op_op_op_op->att_op_op_op_op_id . '_' . $op_op_op_id . '" ' . $selected,
                    'label' => $op_op_op_op->att_op_op_op_op_name,
                    // 'selected' => $selected,
                ];
            }
        } elseif ($opopopop->att_op_op_op_type == 1) {
            $onKeyup = "checkTextboxUpcharge()";
            $level = 3;
            $output['data'][] =
                [
                    'type' => 'hidden',
                    'value' => $op_op_op_id,
                    'name' => "op_op_id_' . $attribute_id . '[]"
                ];

            $output['data'][] =
                [
                    'type' => 'text',
                    'value' => $op_op_op_id,
                    'name' => "op_op_value_' . $attribute_id . '[]",
                    'data-level' =>  $level,
                    'onkeyup' => $onKeyup
                ];
        } else {

            $output['data'][] =
                [
                    'type' => 'hidden',
                    'value' => $opopopop->att_op_op_op_name,
                    'name' => "op_op_value_" . $attribute_id . "[]",

                ];
        }

        return $output;
    }









    
}
