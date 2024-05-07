<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Traits\OrderTrait;
use BarcodeGeneratorHTML;
use App\Http\Requests\OrderStoreRequest;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;


class OrderController extends Controller
{
    use OrderTrait;

    protected $level_id;
    protected $user_id;
    // public $barcode_img_path;
    // global $data;


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


    // function index(Request $request){

    //     // return DB::table('b_level_quatation_tbl')->where('level_id',2)->get()->count();
    //     return DB::table('b_level_quatation_tbl')->where('level_id',2)->get();

    // }


    public function index()
    {
        // $numRows = DB::table('b_level_quatation_tbl as b_q')
        //     ->leftJoin('customers as ci', 'ci.id', '=', 'b_q.customer_id')
        //     ->leftJoin('user_info as cf', 'cf.id', '=', 'b_q.created_by')
        //     ->leftJoin('order_stage_status as oss', 'oss.order_stage_no', '=', 'b_q.order_stage')
        //     ->where(function ($query) {
        //         $query->where('b_q.created_by', '=', $this->level_id)
        //             ->orWhere(function ($query) {
        //                 $query->where('b_q.created_by', '!=', $this->level_id)
        //                     ->where('b_q.order_stage', '>', DB::raw('CASE WHEN cf.user_type = "c" THEN 1 ELSE 0 END'));
        //             });
        //     })
        //     ->where('b_q.level_id', '=', $this->level_id)
        //     ->count();

        // echo $numRows;

        $results = DB::table('b_level_quatation_tbl as b_q')
            ->leftJoin('customers as ci', 'ci.id', '=', 'b_q.customer_id')
            ->leftJoin('user_info as cf', 'cf.id', '=', 'b_q.created_by')
            ->leftJoin('order_stage_status as oss', 'oss.order_stage_no', '=', 'b_q.order_stage')
            ->where('b_q.order_stage', '!=', 1)
            ->where(function ($query) {
                $query->where('b_q.created_by', '=', '2')
                    ->orWhere('b_q.created_by', '!=', '2');
            })
            ->where('b_q.level_id', '=', '2')
            ->where('b_q.order_stage', '!=', 1)
            ->select(
                'b_q.*',
                DB::raw("CONCAT(ci.first_name, ' ', ci.last_name) AS customer_name"),
                'ci.responsible_employee',
                'ci.company',
                DB::raw("IF(b_q.paid_amount = '0', 'Unpaid', IF(b_q.grand_total > b_q.paid_amount, 'Partially Paid', IF(b_q.grand_total = b_q.paid_amount, 'Paid', IF(b_q.paid_amount > b_q.grand_total, 'Credit', 'Partially Paid')))) AS new_status"),
                'oss.status_name',
                'oss.position',
                'oss.status_color',
                DB::raw("COALESCE(NULLIF(cf.company, ''), CONCAT(cf.first_name, ' ', cf.last_name)) AS submitted_by"),
                'oss.status AS order_stage_status',
                'oss.parent_id AS order_stage_parent_id'
            )
            ->orderBy('b_q.order_id', 'ASC')
            // ->get();
            ->paginate(10);
        return $results;
    }


    public function receipt($order_id)
    {

        $orderd = DB::table('b_level_quatation_tbl')
            ->select(
                'b_level_quatation_tbl.*',
                DB::raw('CONCAT(customers.first_name, " ", customers.last_name) as customer_name'),
                'customers.id',
                'customers.phone',
                'customers.address',
                'customers.city',
                'customers.state',
                'customers.zip_code',
                'customers.country_code',
                'customers.customer_no',
                'customers.email',
                'customers.id'
            )
            ->leftJoin('customers', 'customers.id', '=', 'b_level_quatation_tbl.customer_id')
            ->where('b_level_quatation_tbl.order_id', $order_id)
            ->first();

        $company_profile = DB::table('company_profile')->select('*')
            ->where('user_id', $this->level_id)
            ->first();
        $customer = DB::table('customers')->select('*')
            ->where('id', $orderd->customer_id)
            ->first();

        // return $customer;



        $shipping_method = "";
        if ($orderd->ship_method && $orderd->ship_method != '') {
            $ship_method = $orderd->ship_method;

            switch ($ship_method) {
                case 1:
                case 8:
                    $shipping_method = "Pick Up at {$company_profile->company_name}";
                    break;
                case 2:
                    $shipping_method = "LTL -(Zone)";
                    break;
                case 3:
                    $shipping_method = "Installation";
                    break;
                case 4:
                    $shipping_detail = DB::table('order_shipping_carrier_details')
                        ->where('order_id', $orderd->order_id)
                        ->first();
                    $service_data = DB::table('wholesaler_configured_easypost_carrieracc')
                        ->where('level_id', $this->level_id)
                        ->first();
                    $account = explode(",", $service_data['account']);
                    foreach ($account as $value) {
                        if (strpos($value, $shipping_detail['carrier']) !== false) {
                            $shipping_method = rtrim(explode("|", $value)[1], '"');
                            break;
                        }
                    }
                    break;
                case 5:
                    $shipping_method = "{$company_profile->company_name} Delivery";
                    break;
                case 7:
                    $shipping_method = "Other";
                    break;
            }
        }




        if (!empty($company_profile->time_zone)) {
            $date = new DateTime($orderd->order_date);
            $date->setTimezone(new DateTimeZone(trim($company_profile->time_zone)));
            $order_date_time_zone = $date->format('Y-m-d H:i:s');
        } else {
            $order_date_time_zone = $orderd->order_date;
        }
        $date_time_format = $this->date_time_format_by_profile($company_profile->date_format, $company_profile->time_format);

        $order_date =  date_format(date_create($order_date_time_zone), $date_time_format);

        $address_label = "";
        $binfo = DB::table('company_profile')->where('user_id', $customer->customer_user_id)->first();
        $b_c_info = DB::table('customers')->where('customer_user_id', $customer->customer_user_id)->first();

        if (isset($b_c_info->billing_address_label)) {
            switch ($b_c_info->billing_address_label) {
                case 'is_residential':
                    $address_label = "Residential";
                    break;
                case 'commercial':
                    $address_label = "Commercial";
                    break;
                case 'storage_facility':
                    $address_label = "Storage Facility";
                    break;
                case 'freight_terminal':
                    $address_label = "Freight Terminal";
                    break;
                default:
                    // handle unexpected cases if needed
                    break;
            }
        }


        if (!empty($orderd->customer_id)) {
            $shipping_address_info = DB::table('shipping_address_info')->where('customer_id', $orderd->customer_id)->first();

            if (isset($shipping_address_info->is_residential) && $shipping_address_info->is_residential == 1) {
                $address_label = "Residential";
            } else if (isset($shipping_address_info->commercial) && $shipping_address_info->commercial == 1) {
                $address_label = "Commercial";
            } else if (isset($shipping_address_info->storage_facility) && $shipping_address_info->storage_facility == 1) {
                $address_label = "Storage Facility";
            } else if (isset($shipping_address_info->freight_terminal) && $shipping_address_info->freight_terminal == 1) {
                $address_label = "Freight Terminal";
            }
            $shipping_address_label = $address_label;
        }

        $data['barcode'] = asset($orderd->barcode);
        $data['order_stage'] = ($orderd->order_stage == 1) ? "Quote" : "Order";
        $data['order_date'] = $order_date;
        $data['order_id'] = $orderd->order_id;
        $data['side_mark'] = ($orderd->side_mark != '') ? $orderd->side_mark : $customer->side_mark;
        $data['shipping_method'] = $shipping_method;
        $data['wholesaler_info']['company_name'] = $company_profile->company_name;
        $data['wholesaler_info']['address'] = $company_profile->address;
        $data['wholesaler_info']['city'] = $company_profile->city;
        $data['wholesaler_info']['zip_code'] = $company_profile->zip_code;
        $data['wholesaler_info']['country_code'] = $company_profile->country_code;
        $data['wholesaler_info']['phone'] = $company_profile->phone;
        $data['wholesaler_info']['email'] = $company_profile->email;
        if ($binfo) {
            $data['sold_to']['label'] = 'Sold To:';

            $data['sold_to']['name'] = (($b_c_info->customer_type == 'business') ? ($binfo->company_name ?? '') : $b_c_info->first_name . ' ' . $b_c_info->last_name);
            $data['sold_to']['address_label'] = $address_label;
            $data['sold_to']['address'] = $binfo->address;
            $data['sold_to']['city'] = $binfo->city;
            $data['sold_to']['state'] = $binfo->state;
            $data['sold_to']['zip_code'] = $binfo->zip_code;
            $data['sold_to']['country_code'] = $binfo->country_code;
            $data['sold_to']['phone'] = $binfo->phone;
            $data['sold_to']['email'] = $binfo->email;
        }

        $data['ship_to']['label'] = (($orderd->is_different_shipping == 1 && $orderd->is_different_shipping_type == 3) ? 'Pickup From:' : 'Ship To:');

        if ($orderd->is_different_shipping == 1) {
            $shipping_address_explode = explode(",", $orderd->different_shipping_address);
            $shipping_address = $shipping_address_explode[0];

            $data['ship_to']['name'] = $orderd->receiver_name;
            $data['ship_to']['shipping_address_label'] = $shipping_address;
            $data['ship_to']['shipping_address'] = $shipping_address;
            $data['ship_to']['receiver_city'] = $orderd->receiver_city ?? '';
            $data['ship_to']['receiver_state'] = $orderd->receiver_state ?? '';
            $data['ship_to']['receiver_zip_code'] = $orderd->receiver_zip_code ?? '';
            $data['ship_to']['receiver_country_code'] = $orderd->receiver_country_code ?? '';
            $data['ship_to']['receiver_phone_no'] = $orderd->receiver_phone_no ?? '';
            $data['ship_to']['receiver_email'] = ($b_c_info->customer_type == 'business') ? $orderd->receiver_email : '';
        } else {
            $data['ship_to']['name'] = (($b_c_info->customer_type == 'business') ? $binfo->company_name ?? '' : $b_c_info->first_name . ' ' . $b_c_info->last_name);
            $data['ship_to']['shipping_address_label'] = $shipping_address_label;
            if ($binfo) {

                $data['ship_to']['shipping_address'] = $binfo->address;
                $data['ship_to']['city'] = $binfo->city;
                $data['ship_to']['state'] = $binfo->state;
                $data['ship_to']['zip_code'] = $binfo->zip_code;
                $data['ship_to']['country_code'] = $binfo->country_code;
                $data['ship_to']['phone'] = $binfo->phone;
                $data['ship_to']['email'] = $binfo->email;
            }
        }


        return $data;
    }


    function date_time_format_by_profile($date = '', $time = '')
    {
        $convert_date = '';
        if (!empty($date) && !empty($time)) {
            $convert_date = $date . ' ' . $time;
        } elseif (!empty($date)) {
            $convert_date = $date . ' h:i A';
        } elseif (!empty($time)) {
            $convert_date = 'm-d-Y ' . $time;
        } else {
            $convert_date = 'm-d-Y h:i A';
        }
        return $convert_date;
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
                'value' => 1,
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
                'value' => 2,
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




    function store(OrderStoreRequest $request)
    {
        try {
            global $barcode_img_path;

            $orderDetails = $request->order_details;
            $order_id = $orderDetails['order_id'];
            $customer_id = $orderDetails['customer_id'];
            $est_delivery_date = isset($orderDetails['est_delivery_date']) ? date("Y-m-d", strtotime(str_replace("-", "/", $orderDetails['est_delivery_date']))) : null;
            $side_mark = $orderDetails['side_mark'];
            $order_status = $orderDetails['order_status'] ?? "";
            $shippingAddress = $orderDetails['shipping_address'];
            $is_different_shipping = $shippingAddress['different_address'];
            $is_different_address_type = $shippingAddress['different_address_type'] ?? 0;
            $is_address_type = $shippingAddress['address_type'] ?? 0;
            $misc = $orderDetails['misc_total'] ?? '';
            $invoice_discount = $orderDetails['invoice_discount'];
            $grand_total = $orderDetails['grand_total'];
            $subtotal = $orderDetails['subtotal'];
            $tax_percentage = $orderDetails['tax_percentage'];
            $is_product_base_tax = $orderDetails['is_product_base_tax'];

            $isReceiver = $shippingAddress;
            $is_receiver_name = $isReceiver['receiver_name'] ?? '';
            $is_receiver_phone_no = $isReceiver['receiver_phone_no'] ?? '';
            $is_receiver_city = $isReceiver['receiver_city'] ?? '';
            $is_receiver_state = $isReceiver['receiver_state'] ?? '';
            $is_receiver_zip_code = $isReceiver['receiver_zip'] ?? '';
            $is_receiver_country_code = $isReceiver['receiver_country'] ?? '';
            $is_receiver_email = $isReceiver['receiver_email'] ?? '';
            $is_receiver_address = $isReceiver['receiver_address'] ?? '';

            $this->generateBarcodeAndSave($customer_id, $order_id, $side_mark);


            $show_b_customer_record = Customer::selectRaw("*, CONCAT_WS('-', first_name, last_name) as full_name")
                ->where('id', $customer_id)
                ->first();
            $shipping_address_b_customer = DB::table('shipping_address_info')->where('customer_id', $customer_id)->first();
            $company_profile = DB::table('company_profile')->where('user_id', $this->level_id)->first();

            if ($is_different_address_type == 2 && $is_different_shipping == 1) {

                if ($is_address_type == 2) {
                    $shipping_address = $shipping_address_b_customer;

                    $different_shipping_address = $shipping_address->address ?? '';
                    $receiver_name              = ($shipping_address->first_name ?? '') . ' ' . ($shipping_address->last_name ?? '');
                    $receiver_phone_no          = $shipping_address->phone ?? '';
                    $receiver_city              = $shipping_address->city ?? '';
                    $receiver_state             = $shipping_address->state ?? '';
                    $receiver_zip_code          = $shipping_address->zip ?? '';
                    $receiver_country_code      = $shipping_address->country_code ?? '';
                    $receiver_email             = $shipping_address->email ?? '';
                } else {
                    $record = $show_b_customer_record;

                    $different_shipping_address = $record['address'] ?? '';
                    $receiver_name              = ($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '');
                    $receiver_phone_no          = $record['phone'] ?? '';
                    $receiver_city              = $record['city'] ?? '';
                    $receiver_state             = $record['state'] ?? '';
                    $receiver_zip_code          = $record['zip_code'] ?? '';
                    $receiver_country_code      = $record['country_code'] ?? '';
                    $receiver_email             = $record['email'] ?? '';
                }
            } else if ($is_different_address_type == 3 && $is_different_shipping == 1) {
                $profile = $company_profile;

                $different_shipping_address = $profile->address ?? '';
                $receiver_name              = $profile->company_name ?? '';
                $receiver_phone_no          = $profile->phone ?? '';
                $receiver_city              = $profile->city ?? '';
                $receiver_state             = $profile->state ?? '';
                $receiver_zip_code          = $profile->zip_code ?? '';
                $receiver_country_code      = $profile->country_code ?? '';
                $receiver_email             = $profile->email ?? '';
            } else {
                $different_shipping_address = ($is_different_shipping == 1 ? $is_receiver_address : '');
                $receiver_name              = ($is_different_shipping == 1 ? $is_receiver_name : '');
                $receiver_phone_no          = ($is_different_shipping == 1 ? $is_receiver_phone_no : '');
                $receiver_city              = $is_receiver_city ?? '';
                $receiver_state             = $is_receiver_state ?? '';
                $receiver_zip_code          = $is_receiver_zip_code ?? '';
                $receiver_country_code      = $is_receiver_country_code ?? '';
                $receiver_email             = $is_receiver_email ?? '';
            }
            if (!empty($different_shipping_address)) {
                $different_shipping_address = explode(",", $different_shipping_address)[0];
            }

            $user_detail = getCompanyProfileOrderConditionSettingsPart2($this->level_id);

            $wholesaler_taxable = $user_detail->is_taxable;
            $customer_taxable = $show_b_customer_record['is_taxable'] ?? 0;
            $wholesaler_shipping = $user_detail->enable_shipping_zone;
            $customer_shipping = $show_b_customer_record['enable_shipping_zone'] ?? 0;

            //order
            $orderData = array(
                'order_id' => $order_id,
                'order_date' => date('Y-m-d H:i:s'), // call from custom_helper
                'customer_id' => $customer_id,
                'est_delivery_date' => $est_delivery_date,
                'is_different_shipping' => $is_different_shipping,
                'is_different_shipping_type' => $is_different_address_type,
                'different_shipping_address' => $different_shipping_address,
                'address_type' => $is_address_type,
                'receiver_name' => $receiver_name,
                'receiver_phone_no' => $receiver_phone_no,
                'receiver_city' => $receiver_city,
                'receiver_state' => $receiver_state,
                'receiver_zip_code' => $receiver_zip_code,
                'receiver_country_code' => $receiver_country_code,
                'receiver_email' => $receiver_email,
                'level_id' => $this->level_id,
                'side_mark' =>  $side_mark,
                // 'upload_file' => $upload_file,
                'barcode' => @$barcode_img_path,
                // 'state_tax' => $this->input->post('tax'),
                'shipping_charges' => 0.00,
                // 'installation_charge' => $this->input->post('install_charge'),
                // 'other_charge' => $this->input->post('other_charge'),
                'misc' => $misc,
                'invoice_discount' => $invoice_discount,
                'grand_total' => $grand_total,
                'wholesaler_taxable' => !empty($wholesaler_taxable) ? $wholesaler_taxable : 0,
                'customer_taxable' => !empty($customer_taxable) ? $customer_taxable : 0,
                'wholesaler_shipping' => !empty($wholesaler_shipping) ? $wholesaler_shipping : 0,
                'customer_shipping' => !empty($customer_shipping) ? $customer_shipping : 0,
                'tax_percentage' => !empty($tax_percentage) ? $tax_percentage : 0,
                'is_product_base_tax' => $company_profile->product_base_tax ? 1 : 0,
                'shipping_percentage' => !empty($shipping_percentage) ? $shipping_percentage : 0,
                'subtotal' => $subtotal,
                'paid_amount' => 0,
                'due' => $grand_total,
                'order_status' => $order_status,
                'created_by' => auth('api')->user()->id,
                'ship_method' => 1,
                'updated_by' => 0,
                'is_sync_with_quickbook' => 0,
                'created_date' => date('Y-m-d H:i:s'),
                'updated_date' => date('Y-m-d H:i:s')
            );


            // dd($orderData);
            // return $company_profile->product_base_tax;

            // return DB::table('b_level_quatation_tbl')->insert($orderData);

            if (DB::table('b_level_quatation_tbl')->insert($orderData)) {

                // product data
                foreach ($request->order_details['products'] as $key => $product) {

                    // return $product['product_id'];
                    $product_base_tax = 0;
                    if ($company_profile->product_base_tax == 1) {
                        if (@$product->unit_total_price && @$tax_percentage) {

                            $is_taxable_product = DB::table('product_tbl')
                                ->where("is_taxable", 1)
                                ->where('id', @$product['product_id'])
                                ->get();
                            if (@$is_taxable_product) {
                                $product_base_tax = round((@$product->unit_total_price * $tax_percentage / 100), 2);
                            }
                        }
                    }
                    $productData = array(
                        'order_id' => $order_id,
                        // 'room' => $room[$key],
                        'product_id' => $product['product_id'],
                        // 'combo_product_details' => $combo_product_details[$key],
                        'product_qty' => $product['qty'],
                        'list_price' => $product['list_price'],
                        'upcharge_price' => $product['upcharge_price'],
                        'upcharge_label' => $product['upcharge_label'],
                        'upcharge_details' => $product['upcharge_details'],
                        'display_upcharge_details' => null,
                        'separate_display_upcharge_details' => null,
                        'discount' => $product['discount'],
                        'unit_total_price' => $product['unit_total_price'],
                        'category_id' => $product['category_id'],
                        'sub_category_id' => @$product['sub_category_id'],
                        'pattern_model_id' => $product['pattern_id'],
                        // 'manual_pattern_entry' => $manual_pattern_entry[$key],
                        'manual_color_entry' => $product['manual_color_entry'],
                        // 'fabric_price' => $fabric_price[$key],
                        'color_id' => $product['color_id'],
                        'width' => $product['width'],
                        'height' => $product['height'],
                        'height_fraction_id' => $product['height_fraction_id'],
                        'width_fraction_id' => $product['width_fraction_id'],
                        'notes' => $product['notes'],
                        'special_installer_notes' => $product['special_installer_notes'],
                        'room_index' => $product['room_index'],
                        // 'drapery_of_cuts' => $drapery_of_cuts[$key],
                        // 'drapery_of_cuts_only_panel' => $drapery_of_cuts_only_panel[$key],
                        // 'drapery_cut_length' => $drapery_cut_length[$key],
                        // 'drapery_total_fabric' => $drapery_total_fabric[$key],
                        // 'drapery_total_yards' => $drapery_total_yards[$key],
                        // 'drapery_trim_yards' => $drapery_trim_yards[$key],
                        // 'drapery_banding_yards' => $drapery_banding_yards[$key],
                        // 'drapery_flange_yards' => $drapery_flange_yards[$key],
                        // 'drapery_finished_width' => $drapery_finished_width[$key],
                        // 'qutation_image' => $image_file,
                        'product_base_tax' => $product_base_tax,
                        'phase_2_option' =>  0,
                        // 'phase_2_condition' => @$phase_2_condition[$key] ?? null,
                    );

                    DB::table('b_level_qutation_details')->insert($productData);
                    $fk_od_id = DB::table('b_level_qutation_details')->orderBy('row_id', 'desc')->first()->row_id;


                    $attributeData = $this->attributeData($product['attributes']);

                    $attrData = array(
                        'fk_od_id' => $fk_od_id,
                        'order_id' => $order_id,
                        'product_id' => $product['product_id'],
                        'product_attribute' => json_encode($attributeData)
                    );

                    DB::table('b_level_quatation_attributes')->insert($attrData);
                }

                /// misc data isdert
                $miscData =  $request->order_details['misc'];
                DB::table('misc_breakdown_details')->insert($miscData);
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Order inserted successfully',
                'data' => [
                    'order_id' => $order_id
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 501,
                'message' => 'Data is not inserted' . $e,

            ], 501);
        }
    }


    public function attributeData($array)
    {
        $attrib = [];
        foreach ($array as $main_attr_key => $att) {

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
            $this->findKeys($att, 'op_op_id_', $op_op_matchedKeys);
            $op_op_op_matchedKeys = [];
            $this->findKeys($att, 'op_op_op_id_', $op_op_op_matchedKeys);
            $op_op_op_op_matchedKeys = [];
            $this->findKeys($att, 'op_op_op_op_id_', $op_op_op_op_matchedKeys);

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
        return json_encode($attrib);
    }


    public function filterKeys($key, $findkey)
    {
        return preg_match('/^' . $findkey . '\d+$/', $key);
    }
    // Iterate through the nested arrays and find keys
    public function findKeys($array, $findkey, &$result)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if ($this->filterKeys($key, $findkey)) {
                    $result[] = $value;
                }
                $this->findKeys($value, $findkey, $result);
            }
        }
    }



    public function generateBarcodeAndSave($fullname, $order_id, $side_mark)
    {
        global  $barcode_img_path;




        $order_array = [
            "order_id" => $order_id,
            "side_mark" => $side_mark,
            "customer_name" => $fullname
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
