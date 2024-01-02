<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Customer;
use App\Models\QutationDetail;
use Illuminate\Support\Facades\DB;
use App\Traits\OrderTrait;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\RoomCreateRequest;
use hash;
use Illuminate\Support\Facades\Hash as FacadesHash;

class OrderController extends Controller
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



    public function getProductToAttribute($product_id = '')
    {
        $onKeyup = "checkTextboxUpcharge($(this))";
        $level = 1;
        $attributeData = [];

        $attributes = DB::table('product_attribute')
            ->select('product_attribute.*', 'attribute_tbl.attribute_name', 'attribute_tbl.attribute_type')
            ->join('attribute_tbl', 'attribute_tbl.attribute_id', '=', 'product_attribute.attribute_id')
            ->where('product_attribute.product_id', $product_id)
            ->orderBy('attribute_tbl.position', 'ASC')
            ->get()
            ->toArray();

        $p = DB::table('product_tbl')->where('product_id', $product_id)->first();
        $category_id = (!empty($p->category_id) ? $p->category_id : '');

        $fraction_option = '';
        if ($category_id != '') {
            $hw1 = DB::table('categories')->select('fractions')->where('category_id', $category_id)->first();
            $fracs1 = $hw1->fractions;
            $fracs = explode(",", $fracs1);
            $hw2 = DB::table('width_height_fractions')->select('id', 'fraction_value')->orderBy('decimal_value', 'asc')->get()->toArray();

            foreach ($hw2 as $row) {
                if (in_array($row->fraction_value, $fracs)) {
                    $fraction_option .= '<option value="' . $row->id . '">' . $row->fraction_value . '</option>';
                }
            }
        }

        $q = '';
        $main_price = 0;
        if (isset($p->price_style_type)) {
            if ($p->price_style_type == 3) {
                $main_price = $p->fixed_price;
            } elseif ($p->price_style_type == 2) {
                $main_price = $p->sqft_price;
            }
        }

        foreach ($attributes as $attribute_key => $attribute) {
            if ($attribute->attribute_type == 3) {
                $options = DB::table('attr_options')
                    ->select('attr_options.*', 'product_attr_option.id', 'product_attr_option.product_id')
                    ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
                    ->where('product_attr_option.pro_attr_id', $attribute->id)
                    ->orderBy('attr_options.att_op_id', 'ASC')
                    ->orderBy('attr_options.position', 'ASC')
                    ->get()
                    ->toArray();

                foreach ($options as $op) {
                    $ctm_class = "op_text_box_" . $op->att_op_id;

                    $attributeData[] = [
                        'attribute_name' => $attribute->attribute_name,
                        'attribute_id' => $attribute->attribute_id,
                        'op_id' => $op->id . '_' . $op->att_op_id,
                        'level' => $level,
                        'price_type' => $op->price_type,
                        'price' => $op->price,
                        'main_price' => $main_price,
                        'product_id' => $op->product_id,
                    ];
                }
            } elseif ($attribute->attribute_type == 2) {
                $options = DB::table('attr_options')
                    ->select('attr_options.*', 'product_attr_option.id')
                    ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
                    ->where('product_attr_option.pro_attr_id', $attribute->id)
                    ->orderBy('attr_options.position', 'ASC')
                    ->orderBy('attr_options.att_op_id', 'ASC')
                    ->get()
                    ->toArray();

                foreach ($options as $op) {
                    $attributeData[] = [
                        'attribute_name' => $attribute->attribute_name,
                        'attribute_id' => $attribute->attribute_id,
                        'op_id' => $op->id . '_' . $op->att_op_id,
                        'level' => $level,
                        'default' => $op->default,
                    ];
                }
            } elseif ($attribute->attribute_type == 5) {
                if ($attribute->attribute_name == "Tilt Bar Split Location") {
                    $attributeData[] = [
                        'attribute_name' => $attribute->attribute_name,
                        'op_op_id' => $attribute->attribute_id,
                        'default' => 0,
                    ];
                } else {
                    $attributeData[] = [
                        'attribute_name' => $attribute->attribute_name,
                        'attribute_id' => $attribute->attribute_id,
                        'default' => 0,
                    ];
                }
            } elseif ($attribute->attribute_type == 1) {
                $ctm_class = "text_box_" . $attribute->attribute_id;
                $level = 0;

                $attributeData[] = [
                    'attribute_name' => $attribute->attribute_name,
                    'attribute_id' => $attribute->attribute_id,
                    'level' => $level,
                ];
            }
        }
        unset($attributes);

        return $attributeData;
    }


    public function saveCustomer(CustomerCreateRequest $request)
    {
        try {

            $customerData = $this->prepareCustomerData($request);
            $userInsertId = $this->insertCustomerUserInfo($customerData);
            $this->insertCustomerLogInfo($userInsertId, $request->input('username'), $request->input('password'));
            $this->insertCompanyProfile($userInsertId, $customerData);
            $customerInsertedId = $this->insertCustomer($userInsertId, $customerData);
            $this->insertShippingAddress($userInsertId, $customerInsertedId, $customerData);
            $this->insert_b_acc_coa($customerData);
            $this->insertCustomerPhone($userInsertId, $customerInsertedId, $customerData);
            $this->insertCustomerTaxId($userInsertId, $customerInsertedId, $customerData);
            $this->insertAccesslog($customerData);

            $queryCustomerInfo = DB::table('customers')
                ->where('level_id', $this->level_id)
                ->where('customer_id', $customerInsertedId)
                ->first();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data inserted successfully',
                'data' => [
                    "customer" => $queryCustomerInfo,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data is not inserted',
                'data' => [
                    "customer" => [],
                ]
            ], 404);
        }
    }



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
            // Fetch data from the category_tbl table
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

            $colors = [];
            $pattern_model = [];

            if ($pp->colors) {
                $colors = DB::table('colors')
                    ->whereIn('id', explode(',', $pp->colors))
                    ->where('created_by', $this->level_id)
                    ->where('status', 1)
                    ->get();
            }

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
                    ['value' => '', 'text' => '-- Select one --'],
                    ['value' => '0', 'text' => 'Manual Entry', 'selected' => $user_detail->enable_fabric_manual_entry == 1],
                ],
            ];

            foreach ($pattern_model as $pattern) {
                $result[0]['options'][] = [
                    'value' => $pattern->pattern_model_id,
                    'text' => $pattern->pattern_name,
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
}
