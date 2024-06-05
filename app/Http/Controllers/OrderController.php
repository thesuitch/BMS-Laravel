<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Traits\OrderTrait;
use BarcodeGeneratorHTML;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Requests\OrderUpdateItem;
use Illuminate\Support\Facades\Storage;
use DateTime;
use DateTimeZone;
use Str;
use PDF;
use Mail;


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


    public function index(Request $request)
    {

        try {

            // dd($request);
            //code...
            $customer_id = $request->customer_id ?? '';
            $from_date = (empty($request->from_date)) ? '' : date( 'Y-m-d', strtotime( $request->from_date ) ) ;
            $to_date =  (empty($request->from_date)) ? '' : date( 'Y-m-d', strtotime( $request->to_date ) ) ;
            $order_stage = $request->order_stage ?? '';
            $side_mark =  $request->side_mark ?? '';//'Jessica-1516';
            $payment_status = $request->payment_status ?? ''; // Unpaid ,  Partially Paid , Paid , Credit
            $search_responsible_employee = $request->employee_id ?? '';
            $searchValue = $request->search ?? '';
            $orderColumnIndex = $request->order_column;
            $orderDirection = $request->order_dir;
            $per_page = $request->per_page ?? 10;

    
            $column_order = array(null, 'b_q.order_id',null, 'b_q.side_mark','b_q.order_date');
            $column_search = array('b_q.order_id', 'b_q.side_mark','ci.company', 'b_q.order_date','oss.status_name','cf.company','cf.first_name','cf.last_name');
            $concatenation = "COALESCE(NULLIF(cf.company, ''), CONCAT(cf.first_name, ' ', cf.last_name))";
            $order = array('b_q.order_date' => 'desc');
            $user_detail = getCompanyProfileOrderConditionSettings();

            if(isset($user_detail->display_total) && $user_detail->display_total == 0) {
                array_push($column_order, 'b_q.grand_total');
                array_push($column_search, 'b_q.grand_total');
            }else{
                array_push($column_order, null);
            } 
            if(isset($user_detail->display_paid) && $user_detail->display_paid == 0) {
                array_push($column_order, 'b_q.paid_amount');
                array_push($column_search, 'b_q.paid_amount');  
            }else{
                array_push($column_order, null);
            } 
            if(isset($user_detail->display_due) && $user_detail->display_due == 0) {
                array_push($column_order, 'b_q.due'); 
                array_push($column_search, 'b_q.due');   
            } else{
                array_push($column_order, null);
            }   
            $column_order = array_merge($column_order, ['new_status','oss.status_name', "submitted_by", null]);

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
            ->when($customer_id, function ($query) use ($customer_id) {
                return $query->where('b_q.customer_id', $customer_id);
            })
            ->when($from_date , function ($query) use ($from_date, $to_date) {
                return $query->whereDate('b_q.order_date', '>=', $from_date)
                            ->whereDate('b_q.order_date', '<=', $to_date);
            })
            ->when($order_stage, function ($query) use ($order_stage) {
                return $query->where('b_q.order_stage', $order_stage);
            })
            ->when($side_mark, function ($query) use ($side_mark) {
                return $query->where('b_q.side_mark', 'like', '%' . $side_mark . '%');
            })
            ->when($payment_status, function ($query) use ($payment_status) {
                return $query->whereRaw("IF(b_q.paid_amount = '0', 'Unpaid', IF(b_q.grand_total > b_q.paid_amount, 'Partially Paid', IF(b_q.grand_total = b_q.paid_amount, 'Paid', IF(b_q.paid_amount > b_q.grand_total, 'Credit', 'Partially Paid')))) = ?", [$payment_status]);
            })
            ->when($search_responsible_employee, function ($query) use ($search_responsible_employee) {
                return $query->whereRaw("FIND_IN_SET(?, ci.responsible_employee)", [$search_responsible_employee]);
            })
            ->when($searchValue, function ($query) use ($searchValue, $column_search, $concatenation) {
                return $query->where(function ($query) use ($searchValue, $column_search, $concatenation) {
                    foreach ($column_search as $column) {
                        if ($column === $concatenation) {
                            $query->orWhereRaw("$concatenation LIKE ?", ["%$searchValue%"]);
                        } else {
                            $query->orWhere($column, 'like', '%' . $searchValue . '%');
                        }
                    }
                });
            });

            if (isset($request->order_column) && in_array($orderColumnIndex, [1,3,4,5,6,7,8,9,10])) {
                
                $columnName = $column_order[$orderColumnIndex];
                $results = $results->orderBy($columnName, $orderDirection)->paginate($per_page);
            } else{
                $results = $results->orderBy('b_q.order_date', 'desc')->paginate($per_page);
            }
            
           return $results;

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 400);
        }
    }


    public function quotes(Request $request)
    {

        try {
            //code...
            $customer_id = $request->customer_id ?? '';
            $from_date = (empty($request->from_date)) ? '' : date( 'Y-m-d', strtotime( $request->from_date ) ) ;
            $to_date =  (empty($request->from_date)) ? '' : date( 'Y-m-d', strtotime( $request->to_date ) ) ;
            $order_stage = $request->order_stage ?? '';
            $side_mark =  $request->side_mark ?? '';//'Jessica-1516';
            $payment_status = $request->payment_status ?? ''; // Unpaid ,  Partially Paid , Paid , Credit
            $search_responsible_employee = $request->employee_id ?? '';
            $searchValue = $request->search ?? '';
            $orderColumnIndex = $request->order_column;
            $orderDirection = $request->order_dir;
            $per_page = $request->per_page ?? 10;

    
            $column_order = array(null, 'b_q.order_id',null, 'b_q.side_mark','b_q.order_date');
            $column_search = array('b_q.order_id', 'b_q.side_mark','ci.company', 'b_q.order_date','oss.status_name','cf.company','cf.first_name','cf.last_name');
            $concatenation = "COALESCE(NULLIF(cf.company, ''), CONCAT(cf.first_name, ' ', cf.last_name))";
            $order = array('b_q.order_date' => 'desc');
            $user_detail = getCompanyProfileOrderConditionSettings();

            if(isset($user_detail->display_total) && $user_detail->display_total == 0) {
                array_push($column_order, 'b_q.grand_total');
                array_push($column_search, 'b_q.grand_total');
            }else{
                array_push($column_order, null);
            } 
            if(isset($user_detail->display_paid) && $user_detail->display_paid == 0) {
                array_push($column_order, 'b_q.paid_amount');
                array_push($column_search, 'b_q.paid_amount');  
            }else{
                array_push($column_order, null);
            } 
            if(isset($user_detail->display_due) && $user_detail->display_due == 0) {
                array_push($column_order, 'b_q.due'); 
                array_push($column_search, 'b_q.due');   
            } else{
                array_push($column_order, null);
            }   
            $column_order = array_merge($column_order, ['new_status','oss.status_name', "submitted_by", null]);

            $results = DB::table('b_level_quatation_tbl as b_q')
            ->leftJoin('customers as ci', 'ci.id', '=', 'b_q.customer_id')
            ->leftJoin('user_info as cf', 'cf.id', '=', 'b_q.created_by')
            ->leftJoin('order_stage_status as oss', 'oss.order_stage_no', '=', 'b_q.order_stage')
            ->where('b_q.order_stage', '=', 1)
            ->where(function ($query) {
                $query->where('b_q.created_by', '=', '2')
                    ->orWhere('b_q.created_by', '!=', '2');
            })
            ->where('b_q.level_id', '=', '2')
            ->where('b_q.order_stage', '=', 1)
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
            ->when($searchValue, function ($query) use ($searchValue, $column_search, $concatenation) {
                return $query->where(function ($query) use ($searchValue, $column_search, $concatenation) {
                    foreach ($column_search as $column) {
                        if ($column === $concatenation) {
                            $query->orWhereRaw("$concatenation LIKE ?", ["%$searchValue%"]);
                        } else {
                            $query->orWhere($column, 'like', '%' . $searchValue . '%');
                        }
                    }
                });
            });

            if (isset($request->order_column) && in_array($orderColumnIndex, [1,3,4,5,6,7,8,9,10])) {
                
                $columnName = $column_order[$orderColumnIndex];
                $results = $results->orderBy($columnName, $orderDirection)->paginate($per_page);
            } else{
                $results = $results->orderBy('b_q.order_date', 'DESC')->paginate($per_page);
            }
            
           return $results;

        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()], 400);
        }
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

            if(empty($orderd)){
                $message = "Order not found";
                return response()->json(['success' => false, 'message' => $message], 400);
            }


        $company_profile = DB::table('company_profile')->select('*')
            ->where('user_id', $this->level_id)
            ->first();
        $customer = DB::table('customers')->select('*')
            ->where('id', $orderd->customer_id)
            ->first();


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
                    if($service_data){
                        $account = explode(",", $service_data['account']);
                        foreach ($account as $value) {
                            if (strpos($value, $shipping_detail['carrier']) !== false) {
                                $shipping_method = rtrim(explode("|", $value)[1], '"');
                                break;
                            }
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

        $barcodeUrl = asset($orderd->barcode);

        $logoUrl = asset('assets/b_level/uploads/appsettings/' . $company_profile->logo);


        $address_label = "";
        $binfo = DB::table('company_profile')->where('user_id', @$customer->customer_user_id)->first();
        $b_c_info = DB::table('customers')->where('customer_user_id', @$customer->customer_user_id)->first();

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


        // return 1;
        // return $b_c_info;

        $order_details = DB::table('b_level_qutation_details')
            ->select(
                'b_level_qutation_details.*',
                'products.product_name',
                'categories.category_name',
                'b_level_quatation_attributes.product_attribute',
                'pattern_model_tbl.pattern_name',
                'colors.color_name',
                'colors.color_number'
            )
            ->leftJoin('products', 'products.id', '=', 'b_level_qutation_details.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'b_level_qutation_details.category_id')
            ->leftJoin('b_level_quatation_attributes', 'b_level_quatation_attributes.fk_od_id', '=', 'b_level_qutation_details.row_id')
            ->leftJoin('pattern_model_tbl', 'pattern_model_tbl.pattern_model_id', '=', 'b_level_qutation_details.pattern_model_id')
            ->leftJoin('colors', 'colors.id', '=', 'b_level_qutation_details.color_id')
            ->where('b_level_qutation_details.order_id', $order_id)
            ->get();

        // return $order_details;

        $user_detail = getCompanyProfileOrderConditionSettings();


        // dd($orderd);
        $data['barcode'] = (file_exists(public_path($orderd->barcode))) ? asset($barcodeUrl) : '';
        $data['logo'] = (file_exists(public_path('assets/b_level/uploads/appsettings/' . $company_profile->logo))) ? $logoUrl : '';
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
        $data['products'] = [];

        $i = 1;
        $total_qty = 0;
        $total_final_price = 0;
        $finalTotalPrice = 0;
        $sub_total = array();
        $finalTotal = array();
        $total_tax = 0;


        if ($user_detail->display_total_values == 1) {
            $Totalwidth = array();
            $Totalheight = array();
            if ($company_profile->unit == 'inches') {
                $Total_sqft = array();
            }
            if ($company_profile->unit == 'cm') {
                $Total_sqm = array();
            }
        }

        // return $order_details;
        foreach ($order_details as $key => $item) {

            // dd($item);
            $total_qty += $item->product_qty;
            $table_price = ($item->list_price - $item->upcharge_price);
            $disc_price = ($table_price * $item->discount) / 100;
            $list_price = ($table_price - $disc_price) * $item->product_qty;


            $product_width = $item->width;
            $product_height = $item->height;
            $company_unit = $company_profile->unit;

            array_push($sub_total, $item->unit_total_price);


            $width_fraction = DB::table('width_height_fractions')->where('id', $item->width_fraction_id)->first();
            $height_fraction = DB::table('width_height_fractions')->where('id', $item->height_fraction_id)->first();
            if (!empty($width_fraction->decimal_value)) {
                $decimal_width_value = $width_fraction->decimal_value;
                $product_width = $item->width + $decimal_width_value;
            }
            if (!empty($height_fraction->decimal_value)) {
                $decimal_height_value = $height_fraction->decimal_value;
                $product_height = $item->height + $decimal_height_value;
            }


            if ($item->upcharge_price != '') {
                $up_price = $item->upcharge_price;
            } else {
                $up_price = 0;
            }
            $unit_total_price    = number_format($list_price + $up_price, 2);
            $finalUnitTotalPrice = str_replace(",", "", $unit_total_price);
            array_push($finalTotal, $finalUnitTotalPrice);
            $total_final_price += $list_price + $up_price;

            $categoryData = DB::table('categories')->where('id', $item->category_id)->first();
            $getProductData = DB::table('products')->where('id', $item->product_id)->first();


            if ($user_detail->display_total_values == 1) {
                $product_qty = (int) $item->product_qty;
                $product_width1 = $product_width * $product_qty;
                $product_height1 = $product_height * $product_qty;
                array_push($Totalwidth, $product_width1);
                array_push($Totalheight, $product_height1);

                if ($company_unit == 'inches') {
                    // $sqft = (($product_width*$product_height)/144) * $product_qty;

                    //Get the particular value from table if they select the price style either sqft+table_price or table_price form products
                    if ($getProductData->price_style_type == 1 || $getProductData->price_style_type == 9) {
                        $prince = DB::table('price_style')->where('style_id', $getProductData->price_rowcol_style_id)
                            ->where('row', $product_width)
                            ->where('col', $product_height)
                            ->first();

                        $pc = ($prince != NULL ? $prince->price : 0);

                        if (!empty($prince)) {
                            // It means exact height and width match
                            $st = 1;
                        } else {
                            // It means need to consider next greater value from price style
                            $prince = DB::table('price_style')->where('style_id', $getProductData->price_rowcol_style_id)
                                ->where('row', '>=', $product_width)
                                ->where('col', '>=', $product_height)
                                ->orderBy('row_id', 'asc')
                                ->limit(1)
                                ->first();
                            $pc = ($prince != NULL ? $prince->price : 0);
                            $st = 2;
                        }

                        // Calcualte with sqft + table price : START
                        $sqft_price = 1;
                        if ($getProductData->id != '' && @$item->pattern_model_id != '') {
                            $sqft_data = DB::table('sqft_price_model_mapping_tbl')->where('product_id', $getProductData->id)->where('pattern_id', $item->pattern_model_id)->limit(1)->first();
                            $sqft_price = isset($sqft_data->price) ? $sqft_data->price : 1;
                        }
                        $sqft =  round(($pc * $sqft_price), 2) * $product_qty;
                    } else {
                        $sqft = (($product_width * $product_height) / 144) * $product_qty;
                    }

                    array_push($Total_sqft, $sqft);
                }
                if ($company_unit == 'cm') {
                    $sqm = (($product_width * $product_height) / 10000) * $product_qty;
                    array_push($Total_sqm, $sqm);
                }
            }



             //Upcharege Data convert string to array  
             $input_string = $item->upcharge_details;
             $input_string = trim($input_string, '[]');
             $key_value_pairs = explode('},{', $input_string);
             $upcharge_details_result = [];
             foreach ($key_value_pairs as $pair) {
                 preg_match('/upcharge_label:(.*?),upcharge_val:(.*)/', $pair, $matches);
                 $upcharge_details_result[] = [
                     'upcharge_label' => isset($matches[1]) ? trim($matches[1]) : '',
                     'upcharge_val' => isset($matches[2]) ? trim($matches[2]) : ''
                 ];
             }


            // For Get Sub Category name : START
            $sub_cat_name = '';
            if (isset($item->sub_category_id) && $item->sub_category_id > 0) {
                $sub_category_data = DB::get('categories')->where('id', $item->sub_category_id)->first();
                if (isset($sub_category_data->category_id)) {
                    $sub_cat_name = " (" . $sub_category_data->category_name . ") ";
                }
            }
            // For Get Sub Category name : END


             // add status index
             $mfg_label_data = DB::table('b_level_quotation_details_mfg_label')->where('fk_row_id', $item->row_id)->first();
             if ($mfg_label_data) {
                 $mfg_status_data = '';
                //  foreach ($mfg_label_data as $mfg_key => $mfg_val) {
                     // For mfg status color badge : START
                     $status_name = $mfg_label_data->status;
                     if ($mfg_label_data->status == 'Ready to be Shipped' && $mfg_label_data->is_save_scanned == 2) {
                         $new_order_stage = '8';
                     } else if ($mfg_label_data->status == 'Mfg Completed' && $mfg_label_data->is_save_scanned == 1 || ($mfg_label_data->status == 'Ready to be Shipped')) {
                         $new_order_stage = '15';
                         $status_name = 'Mfg Completed';
                     } else if ($mfg_label_data->status == 'Mfg Canceled') {
                         $new_order_stage = '16';
                     } else if ($mfg_label_data->status == 'Mfg Label Printed') {
                         $new_order_stage = '18';
                     } else {
                         $new_order_stage = '17';
                         $status_name = 'Mfg Pending';
                     }
                     // For mfg status color badge : END
 
                     $mfg_status_data =  $mfg_label_data->room . " is " . $status_name;
                //  }
                //  $product['status'] = $mfg_status_data;
             }
 

             if($item->room_index!= '') {
                $indexarr = json_decode($item->room_index,true);
                if($indexarr != '') {
                    $room_data = implode(",", $indexarr);
                }
            } else {
                $room_data = $item->room;
            }


            $is_cat_hide_room = DB::table('products')
                ->select('categories.hide_room', 'categories.hide_color', 'products.hide_room as product_hide_room', 'products.hide_color as product_hide_color')
                ->where('products.id', @$item->product_id)
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->first();




            $data['products'][] = [
                'row_id' => $item->row_id,
                'product_qty' => $item->product_qty,
                'name_of_product' => [
                    'category' => ($user_detail->display_category == 1) ? $categoryData->category_name . $sub_cat_name : '',
                    'product_name' => $item->product_name,
                    'pattern' => ($item->pattern_name) ? $item->pattern_name : (($item->pattern_model_id == 0 && $item->manual_pattern_entry != null) ? $item->manual_pattern_entry : ''),
                    'manual_color_entry' => ($is_cat_hide_room->product_hide_color == 0 && $is_cat_hide_room->hide_color == 0 && (@$item->pattern_model_id == 0 || @$item->color_id == 0) && @$item->manual_color_entry != null) ? $item->manual_color_entry : '',
                    'width' => $item->width.' '.@$width_fraction->fraction_value . ' ' . strtoupper($company_unit), // Initialize width attribute
                    'height' => $item->height. ' ' . @$height_fraction->fraction_value . ' ' . strtoupper($company_unit), // Initialize height attribute
                    'color_number' => ($item->color_number != '' || $item->color_name != '') ? $item->color_number . ' ' . $item->color_name : '',
                    'room' =>  $room_data ?? ''
                ],
                'product_price' => $company_profile->currency . $table_price,
                'discount' => ($user_detail->display_discount == 0 && $item->discount > 0) ? $item->discount . " %" : "0 %",
                'list_price' => ($user_detail->display_list_price == 0) ? $company_profile->currency .  number_format($list_price, 2) : 0,
                'upcharge' => [
                    'upcharge_price' => $company_profile->currency . number_format($item->upcharge_price, 2) ,
                    // 'upcharge_details' => $upcharge_details_result
                    'upcharge_details' => json_decode($item->upcharge_details) ?? $upcharge_details_result
                ],
                'total_price' => $company_profile->currency . $unit_total_price,
                'comments' => [
                    'notes' => ($item->notes != '') ? 'Special Instruction :' .  $item->notes : '',
                    'special_installer_notes' => ($item->special_installer_notes != '') ? "Note For Installer : " . $item->special_installer_notes : '',
                ],
                'status' => $mfg_status_data ?? ""
            ];

            // foreach ($data['products'] as $k => &$product) {

            //     // add Height and width 
            //     if ($user_detail->drapery_template != 1 || $user_detail->drapery_template_category_id != $item->category_id) {
            //         if ($getProductData->hide_height_width == 0 || $getProductData->hide_height_width == 2) {
            //             // dd();
            //             $product['name_of_product']['width'] = ' W: ' . $order_details[$k]->width . ' ' . @$width_fraction->fraction_value . ' ' . strtoupper($company_unit);
            //         }
            //         if ($getProductData->hide_height_width == 0 || $getProductData->hide_height_width == 1) {
            //             $product['name_of_product']['height'] = ' H: ' . $order_details[$k]->height . ' ' . @$height_fraction->fraction_value . ' ' . strtoupper($company_unit);
            //         }
            //     }

            //     // add room index
            //     if ($user_detail->display_room == 0) {
            //         if ($getProductData->hide_room == 0 && $is_cat_hide_room->product_hide_room == 0) {
                        
            //             $product['name_of_product']['room'] = $order_details[$k]->room_index;

            //             // if ($order_details[$k]->room_index != '') {
            //             //     // $indexarr = json_decode($order_details[$k]->room_index, true);
            //             //     $indexarr = $order_details[$k]->room_index;
            //             //     if ($indexarr != '') {
            //             //         // return $indexarr;
            //             //         $product['name_of_product']['room'] = implode(",", $indexarr);
            //             //     }
            //             // } else {
            //             //     $product['name_of_product']['room'] = $order_details[$k]->room;
            //             // }
            //         }
            //     }

            //     // dd($order_details[$k]->room_index);


            //     // add Upcharge price and Details
            //     if ($user_detail->display_upcharges == 0 && $user_detail->display_partial_upcharges == 0) {
            //         // Display the upcharge price tooltip : START
            //         if ($user_detail->show_upcharge_breakup == 1) {

            //             // convert string to array  
            //             $input_string = $order_details[$k]->upcharge_details;
            //             $input_string = trim($input_string, '[]');
            //             $key_value_pairs = explode('},{', $input_string);
            //             $result = [];
            //             foreach ($key_value_pairs as $pair) {
            //                 preg_match('/upcharge_label:(.*?),upcharge_val:(.*)/', $pair, $matches);
            //                 $result[] = [
            //                     'upcharge_label' => isset($matches[1]) ? trim($matches[1]) : '',
            //                     'upcharge_val' => isset($matches[2]) ? trim($matches[2]) : ''
            //                 ];
            //             }

            //             $product['upcharge']['upcharge_details'] =  $result;
            //         }
            //         // Display the upcharge price tooltip : END

            //         // Display the upcharge price : Start
            //         $product['upcharge']['upcharge_price'] = $company_profile->currency . number_format($order_details[$k]->upcharge_price, 2);
            //         // Display the upcharge price : END

            //     }


            //     // add status index
            //     $mfg_label_data = DB::table('b_level_quotation_details_mfg_label')->where('fk_row_id', $order_details[$k]->row_id)->get();
            //     if (count($mfg_label_data) > 0) {
            //         $mfg_status_data = '';
            //         foreach ($mfg_label_data as $mfg_key => $mfg_val) {
            //             // For mfg status color badge : START
            //             $status_name = $mfg_val->status;
            //             if ($mfg_val->status == 'Ready to be Shipped' && $mfg_val->is_save_scanned == 2) {
            //                 $new_order_stage = '8';
            //             } else if ($mfg_val->status == 'Mfg Completed' && $mfg_val->is_save_scanned == 1 || ($mfg_val->status == 'Ready to be Shipped')) {
            //                 $new_order_stage = '15';
            //                 $status_name = 'Mfg Completed';
            //             } else if ($mfg_val->status == 'Mfg Canceled') {
            //                 $new_order_stage = '16';
            //             } else if ($mfg_val->status == 'Mfg Label Printed') {
            //                 $new_order_stage = '18';
            //             } else {
            //                 $new_order_stage = '17';
            //                 $status_name = 'Mfg Pending';
            //             }
            //             // For mfg status color badge : END

            //             $mfg_status_data =  $mfg_val->room . " is " . $status_name;
            //         }
            //         $product['status'] = $mfg_status_data;
            //     }
            // }


           
          
            // [{"attribute_id":"132","attribute_value":"2649_312","attributes_type":2,"options":[{"option_type":5,"option_id":"312","option_value":"2 on One","option_key_value":"2649_312"}],"opop":[{"op_op_id":"172","op_op_value":"32 4","option_key_value":"172_1195_312"},{"op_op_id":"173","op_op_value":"12 2","option_key_value":"173_1196_312"}],"opopop":[],"opopopop":[]},{"attribute_id":"7","attribute_value":"2651_6","attributes_type":2,"options":[{"option_type":0,"option_id":"6","option_value":"IB","option_key_value":"2651_6"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"8","attribute_value":"2654_9","attributes_type":2,"options":[{"option_type":0,"option_id":"9","option_value":"Yes","option_key_value":"2654_9"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"9","attribute_value":"2656_11","attributes_type":2,"options":[{"option_type":0,"option_id":"11","option_value":"Wand Tilter","option_key_value":"2656_11"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"10","attribute_value":"2658_13","attributes_type":2,"options":[{"option_type":0,"option_id":"13","option_value":"Right","option_key_value":"2658_13"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"11","attribute_value":"2660_15","attributes_type":2,"options":[{"option_type":0,"option_id":"15","option_value":"Cordless Lift","option_key_value":"2660_15"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"12","attribute_value":"2662_17","attributes_type":2,"options":[{"option_type":0,"option_id":"17","option_value":"Left","option_key_value":"2662_17"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"13","attribute_value":"2664_19","attributes_type":2,"options":[{"option_type":0,"option_id":"19","option_value":"Yes","option_key_value":"2664_19"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"14","attribute_value":"2666_21","attributes_type":2,"options":[{"option_type":0,"option_id":"21","option_value":"2 1/2" Standard","option_key_value":"2666_21"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"15","attribute_value":"2669_24","attributes_type":2,"options":[{"option_type":5,"option_id":"24","option_value":"Yes","option_key_value":"2669_24"}],"opop":[{"op_op_id":"6","op_op_value":"12 2","option_key_value":"6_1200_24"}],"opopop":[],"opopopop":[]},{"attribute_id":"16","attribute_value":"2671_26","attributes_type":2,"options":[{"option_type":0,"option_id":"26","option_value":"High Position","option_key_value":"2671_26"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"17","attribute_value":"2674_29","attributes_type":2,"options":[{"option_type":0,"option_id":"29","option_value":"1/2" Returns","option_key_value":"2674_29"}],"opop":[],"opopop":[],"opopopop":[]},{"attribute_id":"18","attribute_value":"2679_34","attributes_type":2,"options":[{"option_type":5,"option_id":"34","option_value":"Both Bottom Cutout","option_key_value":"2679_34"}],"opop":[{"op_op_id":"12","op_op_value":"21 3","option_key_value":"12_1206_34"},{"op_op_id":"13","op_op_value":"21222 2","option_key_value":"13_1207_34"}],"opopop":[],"opopopop":[]},{"attribute_id":"19","attribute_value":"2681_36","attributes_type":2,"options":[{"option_type":5,"option_id":"36","option_value":"Yes","option_key_value":"2681_36"}],"opop":[{"op_op_id":"14","op_op_value":"12 2","option_key_value":"14_1208_36"}],"opopop":[],"opopopop":[]}]

            if (($item->upcharge_label || $item->product_attribute) && $user_detail->display_attributes == 1) {
                $selected_attributes = json_decode($item->product_attribute);

                // return $selected_attributes;
                $attributes_data = [];

                foreach ($selected_attributes as $atributes) {
                    $attribute_entry = []; // Create an entry for each attribute

                    $at_id = $atributes->attribute_id;
                    $att_name = DB::table('attribute_tbl')->where('attribute_id', $at_id)->first();
                    $attribute_entry['name'] = @$att_name->attribute_name; // Save primary attribute name

                    if (isset($atributes->options[0]->option_id) && $atributes->options[0]->option_id != '' && $atributes->attributes_type != 1) {
                        $att_op_name = DB::table('attr_options')->where('att_op_id', $atributes->options[0]->option_id)->first();
                        $attribute_value = @$att_op_name->option_name;
                    } elseif (isset($atributes->attribute_value) && $atributes->attribute_value != '') {
                        $attribute_value = $atributes->attribute_value;
                    }


                    // Check if primary attribute has a value
                    // if (isset($atributes->attribute_value) && $atributes->attribute_value != '') {
                    $attribute_entry['value'] = $attribute_value; // Save primary attribute value
                    // }

                    // Append primary attribute directly to the attributes data array
                    $attributes_data[] = $attribute_entry;

                    // Check for sub-attributes
                    if (isset($atributes->options[0]->option_type)) {
                        if ($atributes->options[0]->option_type == 3 || $atributes->options[0]->option_type == 5 || $atributes->options[0]->option_type == 2 || $atributes->options[0]->option_type == 4 || $atributes->options[0]->option_type == 6) {
                            if (sizeof($atributes->opop) > 0) {
                                foreach ($atributes->opop as $secondLevelOpts) {
                                    $secondLevelOpt = DB::table('attr_options_option_tbl')->where('op_op_id', $secondLevelOpts->op_op_id)->first();
                                    $secondLevelOptName = @$secondLevelOpt->op_op_name;

                                    $secondLevelOptValue = "";
                                    if (@$secondLevelOpt->type == 1 || @$secondLevelOpt->type == 0 || @$secondLevelOpt->type == 2) {
                                        $secondLevelOptValue = $secondLevelOpts->op_op_value;
                                    }

                                    // Handle sub-attributes of type 4 (multioption with multiselect)
                                    if (@$atributes->options[0]->option_type == 4 && @$secondLevelOpt->type == 6) {
                                        // Logic to handle multiselect options
                                        $attributes_data[] = [
                                            'name' => $secondLevelOptName,
                                            'value' => $secondLevelOpts->op_op_value
                                        ];
                                    }else{

                                        // Append sub-attribute directly to the attributes data array
                                    $attributes_data[] = [
                                        'name' => $secondLevelOptName,
                                        'value' => $secondLevelOptValue
                                    ];
                                    }
                                }
                            }
                        }
                    }
                }

                // Append attributes data to the product
                $data['products'][count($data['products']) - 1]['name_of_product']['attributes'] = $attributes_data;
            }

            if (@$orderd->is_product_base_tax == 1) {
                $tax = $item->product_base_tax;
                $total_tax += $tax;
            }
        }


        $order_controller_cart_item = DB::table('order_controller_cart_item')->where('order_id', $order_id)->get();
        $order_hardware_cart_item = DB::table('order_hardware_cart_item')->where('order_id', $order_id)->get();
        $order_component_cart_item = DB::table('order_component_cart_item')->where('order_id', $order_id)->get();
        $misc_breakdown_details = DB::table('misc_breakdown_details')
        ->where('order_id', $order_id)
        ->orderBy('id', 'asc')
        ->get();

        if(count($misc_breakdown_details) > 0) {

            $data['misc'] = [];
            foreach($misc_breakdown_details as $c_item_key => $misc) {                   
                    $data['misc'][] = [
                        'id' => $misc->id,
                        'misc_description' => $misc->misc_description,
                        'misc_unite_cost' => $misc->misc_unite_cost,
                        'misc_qty' => $misc->misc_qty,
                        'misc_price' => $misc->misc_price,
                    ];
            }
        }

        //For Controller Item Cart Item : START 
        if(count($order_controller_cart_item) > 0) {
            $sr_c_item = 0;
            $data['controllers'] = [];
            foreach($order_controller_cart_item as $c_item_key => $c_item) { 
                    $total_qty += $c_item->item_qty;
                    $total_final_price += $c_item->item_total_price;
                    array_push($finalTotal, $c_item->item_total_price);

                    $data['controllers'][] = [
                        'row_id' => $c_item->order_controller_cart_item_id,
                        'qty' => $c_item->item_qty,
                        'name' => $c_item->item_name,
                        'price' => number_format($c_item->item_price,2),
                        'item_total_price' => number_format($c_item->item_total_price,2)
                    ];

            }
        }
        //For Controller Item Cart Item : END 

        // For Component Item Cart Item : START
        if(count($order_component_cart_item) > 0) {
            $data['components'] = [];
            $sr_c_item = 0;
            foreach($order_component_cart_item as $c_item_key => $c_item) { 
                $total_qty += $c_item->component_qty;
                array_push($finalTotal, $c_item->component_total_price);

                $discount_rate = 0;
                if (isset($c_item->dealer_cost_factor) && $c_item->dealer_cost_factor > 0) {
                    $discount_rate = $c_item->discount;
                } else {
                    $discount_rate = 0;
                }

                if(isset($discount_rate) && $discount_rate!=0){
                   
                    $item_final_price = $c_item->list_price;
                }else{
                    $item_final_price = $c_item->component_total_price;
                }
                $total_final_price += $item_final_price;


                $data['components'][] = [
                    'row_id' => $c_item->order_component_cart_item_id,
                    'qty' => $c_item->component_qty,
                    'name' => $c_item->part_name,
                    'price' => number_format($c_item->part_price,2),
                    'discount' => $discount_rate,
                    'item_total_price' => number_format($item_final_price,2)
                ];

            }

        }
        // For Component Item Cart Item : END

        // For Hardware Item Cart Item : START
        if(count($order_hardware_cart_item) > 0) { 
            $data['hardware'] = [];
            $sr_h_item = 0;
            foreach($order_hardware_cart_item as $h_item_key => $h_item) { 
                $total_qty += $h_item->item_qty;
                array_push($finalTotal, $h_item->item_total_price);
                $total_final_price += $h_item->item_total_price;

                $item_details = DB::table('hardware_sub_group_detail as hsgd')
                ->select(
                    'v.vendor_name',
                    'g.group_name',
                    'h.h_product_name as product_name',
                    'hsg.group_name as sub_group_name',
                    'hsgd.hardware_sub_group_detail_name as item_name',
                    'f.finish_name',
                    'is_taxable'
                )
                ->leftJoin('hardware_sub_group as hsg', 'hsg.hardware_sub_group_id', '=', 'hsgd.hardware_sub_group_id')
                ->leftJoin('hardware as h', 'h.hardware_id', '=', 'hsg.hardware_id')
                ->leftJoin('vendor as v', 'v.vendor_id', '=', 'h.h_vendor_id')
                ->leftJoin('group as g', 'g.group_id', '=', 'h.h_group_id')
                ->leftJoin('finish as f', 'f.finish_id', '=', DB::raw($h_item->finish_id))
                ->where('hsgd.hardware_sub_group_detail_id', $h_item->hardware_sub_group_detail_id)
                ->first();

                $data['hardware'][] = [
                    'row_id' => $h_item->order_hardware_cart_item_id,
                    'qty' => $h_item->item_qty,
                    'name' => [
                            'vendor_name' =>   @$item_details->vendor_name,
                            'group_name' =>   @$item_details->group_name,
                            'product_name' =>   @$item_details->product_name,
                            'sub_group_name' =>   @$item_details->sub_group_name,
                            'item_name' =>   @$item_details->item_name,
                            'finish_name' =>   @$item_details->finish_name
                    ],
                    'price' => number_format($h_item->item_price,2),
                    'item_total_price' => number_format($h_item->item_total_price,2)
                ];
            }
        }
        // For Hardware Item Cart Item : END

        

        // $finalTotalPrice = $orderd->subtotal;
        $finalTotalPrice = $total_final_price;
        // For Sales Tax : START
        $customer_based_tax = 0;
        if (@$orderd->is_product_base_tax == 1) {
            $customer_based_tax = round($total_tax, 2);
        } else {
            if ($orderd->tax_percentage != '' && $orderd->tax_percentage > 0) {
                $customer_based_tax = ($finalTotalPrice * $orderd->tax_percentage / 100);
            }
        }
        // For Sales Tax : END

        // Shipping percentage calculation : START
        $shipping_charges = 0;
        if ($orderd->shipping_percentage != '' && $orderd->shipping_percentage > 0) {
            $shipping_charges = (($finalTotalPrice * $orderd->shipping_percentage) / 100);
        }
        // Shipping percentage calculation : END
        if (isset($finalTotalPrice) && !empty($finalTotalPrice)) {
            $finalTotalPrice = $finalTotalPrice;
        } else {
            $finalTotalPrice = 0;
        }
        // Shipping percentage calculation : END
        $order_misc_charges = (isset($orderd->misc) && ($orderd->misc != '')) ? $orderd->misc : 0;
        $order_other_charges = (isset($orderd->other_charge) && ($orderd->other_charge != '')) ? $orderd->other_charge : 0;
        $shipping_installation_chargej = $orderd->installation_charge + $orderd->shipping_charges + $shipping_charges;
        $grandtotals = ($finalTotalPrice + $customer_based_tax + $shipping_installation_chargej + $order_misc_charges + $order_other_charges) - $orderd->invoice_discount;
        $checkdueamt = $grandtotals - $orderd->paid_amount;

        $shipping_installation_charge = $orderd->installation_charge + $orderd->shipping_charges + $shipping_charges;
        $order_misccharges = (isset($orderd->misc) && ($orderd->misc != '')) ? $orderd->misc : 0;
        $allow_max_credit = $orderd->credit + $orderd->due;
        $allow_max_discount = $orderd->invoice_discount + $orderd->due;

        // Total Section Start
        $data['total'] = [
            'qty' => $total_qty,
        ];
        if ($user_detail->display_total_values == 1) {
            $data['total']['width'] = array_sum($Totalwidth) . ' ' . $company_unit;
            $data['total']['height'] = array_sum($Totalheight) . ' ' . $company_unit;
            $data['total']['sqft_or_sqm'] = ($company_unit == 'inches' && array_sum($Total_sqft) != 0) ?
                number_format(array_sum($Total_sqft), 2) : (($company_unit == 'cm' && array_sum($Total_sqm) != 0) ? number_format(array_sum($Total_sqm), 2) : 0);
        }
        $data['total']['price'] = $company_profile->currency . number_format($total_final_price, 2);
        $data['total']['sales_tax'] =  $company_profile->currency . number_format(($customer_based_tax), 2);
        $data['total']['sub_total'] =  $company_profile->currency . number_format(($finalTotalPrice), 2);
        $data['total']['shipping_installation_charge'] =  $shipping_installation_charge;
        $data['total']['misc'] =  $company_profile->currency . $order_misccharges;
        $data['total']['credit'] =  number_format($orderd->credit, 2);
        $data['total']['allow_max_credit'] =  number_format(($allow_max_credit),2);
        $data['total']['discount'] =  number_format($orderd->invoice_discount, 2);
        $data['total']['allow_max_discount'] =  number_format(($allow_max_discount),2);
        $data['total']['grand_total'] =  $company_profile->currency . number_format($grandtotals, 2);
        $data['total']['deposit'] =  $company_profile->currency . number_format($orderd->paid_amount, 2);
        $data['total']['due'] =  $company_profile->currency . number_format($checkdueamt, 2);


        return $data;
    }


    public function receiptMail($order_id)
    {

        try {

            $data = $this->receipt($order_id);
            $email =  env('MAIL_FOR_TESTING') ?? $data['sold_to']['email'];        
            $pdf_name = $order_id.'.pdf';
            $pdf = PDF::loadView('pdf.receipt_mail', compact('data'));
            Mail::send('email.receipt_mail', compact('data'), function($message)use($data, $pdf , $pdf_name , $email) {
                $message->to($email, $email)
                        ->subject($pdf_name)
                        ->attachData($pdf->output(), $pdf_name);
            });

            return response()->json(['success' => true, 'message' => 'Email Sent Successfully!'], 200);  

        } catch (\Throwable $th) {
            
            return response()->json(['success' => false, 'message' => 'Email is not sent','error_message'=> $th->getMessage()], 400);  

        }
      
    }



    public function receiptPDF($order_id)
    {

        try {

            $data = $this->receipt($order_id);
            $pdf_name = $order_id.'.pdf';
            $pdf = PDF::loadView('pdf.receipt_mail', compact('data'));
            return $pdf->download($pdf_name);
            
        } catch (\Throwable $th) {
            
            return response()->json(['success' => false, 'message' => 'PDF generation failed', 'error_message' => $th->getMessage()], 400);

        }
      
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

                $default_f[] = [
                    "id" => '',
                    "fraction_value" => '--Select--',
                    "decimal_value" => ''
                ];
                
                $category['fractions'] = array_merge($default_f, $selectedFractions->toArray());
                

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

            $p = DB::table('products')->where('id', $product_id)->first();

            $f_w = @explode('.',$width)[1];
            $f_h = @explode('.',$height)[1];

            $fraction_w = $this->get_height_width_fraction($f_w, $p->category_id);
            $fraction_h = $this->get_height_width_fraction($f_h);


            $data = [
                'fraction_w' => $fraction_w,
                'fraction_h' => $fraction_h,
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




    // Save Order : Start
    function store(OrderStoreRequest $request)
    {
        try {
            global $barcode_img_path;

            $orderDetails = $request->order_details;
            $order_id = $orderDetails['order_id'];
            $current_order_number = $orderDetails['current_order_number'];
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

            


            $show_b_customer_record = Customer::selectRaw("*, CONCAT_WS('-', first_name, last_name) as full_name")
                ->where('id', $customer_id)
                ->first();

            $this->generateBarcodeAndSave($show_b_customer_record->full_name, $order_id, $side_mark);


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
                // 'state_tax' => $request->tax,
                'shipping_charges' => 0.00,
                // 'installation_charge' => $request->install_charge,
                // 'other_charge' => $request->other_charge,
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
                'order_stage' => 1,
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

                // if (isset($user_detail->order_id_format) && $user_detail->order_id_format == 1) {
                    $order_id_numbers_data = [
                        'order_id' => $order_id,
                        'current_order_number' => $current_order_number,
                        'level_id' => $this->level_id,
                        'created_date' => date('Y-m-d H:i:s') // Use now() instead of date('Y-m-d')
                    ];
                
                    DB::table('wholesaler_order_id_numbers')->insert($order_id_numbers_data);
                // }



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
                        'room' => $product['room'],
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
                    $attributeData = str_replace('\\', '', json_encode($attributeData));
                    $attributeData = Str::replaceFirst('"', "", $attributeData);
                    $attributeData = Str::replaceLast('"', "", $attributeData);
                    $attrData = array(
                        'fk_od_id' => $fk_od_id,
                        'order_id' => $order_id,
                        'product_id' => $product['product_id'],
                        'product_attribute' => $attributeData
                    );

                   $att =  DB::table('b_level_quatation_attributes')->insert($attrData);

                }

                /// misc data isdert
                // $miscData =  ;
                if(isset($request->order_details['misc'])){
                    DB::table('misc_breakdown_details')->insert($request->order_details['misc']);
                }
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
    // Save Order : End






     // Update Order : Start
     function UpdateOrder(OrderUpdateRequest $request)
     {
         try {


             global $barcode_img_path;
 
             $orderDetails = $request->order_details;
             $order_id = $orderDetails['order_id'];
             $current_order_number = $orderDetails['current_order_number'];
             $customer_id = $orderDetails['customer_id'];
             $est_delivery_date = isset($orderDetails['est_delivery_date']) ? date("Y-m-d", strtotime(str_replace("-", "/", $orderDetails['est_delivery_date']))) : null;
             $side_mark = $orderDetails['side_mark'];
             $order_status = $orderDetails['order_status'] ?? "";
             $shippingAddress = $orderDetails['shipping_address'];
             $is_different_shipping = $shippingAddress['different_address'];
             $is_different_address_type = $shippingAddress['different_address_type'] ?? 0;
             $is_address_type = $shippingAddress['address_type'] ?? 0;
             $misc = $orderDetails['misc_total'] ?? '';
             $credit_val = $orderDetails['credit_val'] ?? 0;
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
 


             $orderd = DB::table('b_level_quatation_tbl')
             ->where('b_level_quatation_tbl.order_id', $order_id)
             ->first();
 
             if(empty($orderd)){
                 $message = "Order not found";
                 return response()->json(['success' => false, 'message' => $message], 400);
             }

 
             $show_b_customer_record = Customer::selectRaw("*, CONCAT_WS('-', first_name, last_name) as full_name")
                 ->where('id', $customer_id)
                 ->first();
 
             $this->generateBarcodeAndSave($show_b_customer_record->full_name, $order_id, $side_mark);
 
 
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
                //  'order_id' => $order_id,
                //  'order_date' => date('Y-m-d H:i:s'), // call from custom_helper
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
                 // 'state_tax' => $request->tax,
                 'shipping_charges' => 0.00,
                 // 'installation_charge' => $request->install_charge,
                 // 'other_charge' => $request->other_charge,
                 'misc' => $misc,
                 'credit' => $credit_val,
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
                //  'order_stage' => 1,
                 'created_by' => auth('api')->user()->id,
                 'ship_method' => 1,
                 'updated_by' => 0,
                //  'is_sync_with_quickbook' => 0,
                //  'created_date' => date('Y-m-d H:i:s'),
                 'updated_date' => date('Y-m-d H:i:s')
             );
 
             // dd($orderData);
             // return $company_profile->product_base_tax;
 
             $affectedRows = DB::table('b_level_quatation_tbl')
             ->where('order_id', $order_id)
             ->update($orderData);
         
                // Check the number of affected rows
                if ($affectedRows >= 0) { 
             
                
                    // Fetch qutation_image where order_id matches and qutation_image is not null
                    $qutationDetailsList = DB::table('b_level_qutation_details')
                    ->select('qutation_image')
                    ->where('order_id', $order_id)
                    ->whereNotNull('qutation_image')
                    ->get();

                    // Delete from b_level_qutation_details where order_id matches
                    DB::table('b_level_qutation_details')
                    ->where('order_id', $order_id)
                    ->delete();

                    // Delete from b_level_quatation_attributes where order_id matches
                    DB::table('b_level_quatation_attributes')
                    ->where('order_id', $order_id)
                    ->delete();


                     // Delete from misc_breakdown_details where order_id matches
                     DB::table('misc_breakdown_details')
                     ->where('order_id', $order_id)
                     ->delete();
 
 
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
                         'room' => $product['room'],
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
                     $attributeData = str_replace('\\', '', json_encode($attributeData));
                     $attributeData = Str::replaceFirst('"', "", $attributeData);
                     $attributeData = Str::replaceLast('"', "", $attributeData);
                     $attrData = array(
                         'fk_od_id' => $fk_od_id,
                         'order_id' => $order_id,
                         'product_id' => $product['product_id'],
                         'product_attribute' => $attributeData
                     );
 
                    $att =  DB::table('b_level_quatation_attributes')->insert($attrData);
 
                 }
 
                 /// misc data isdert
                 if(isset($request->order_details['misc'])){
                     DB::table('misc_breakdown_details')->insert($request->order_details['misc']);
                 }
             }
 
             return response()->json([
                 'status' => 'success',
                 'code' => 200,
                 'message' => 'Order Updatd successfully',
                 'data' => [
                     'order_id' => $order_id
                 ]
             ]);
         } catch (\Exception $e) {
 
             return response()->json([
                 'status' => 'error',
                 'code' => 501,
                 'message' => 'Data is not Updated' . $e,
 
             ], 501);
         }
     }
     // Update Order : End




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
                    'option_value' => str_replace('"', '', $att['label']),
                    // 'option_value' => '',
                    'option_key_value' => str_replace('"', '',$att['value']), //added by itsea, previously there was no value saving of attributes drop down, so added this
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
                        'op_op_value' => str_replace('"', '', implode(', ', array_column($value, 'label'))),
                        'option_key_value' => str_replace('"', '',$value[0]['op_op_key_value']),
                    ];


                    foreach ($value as $v) {

                        $op_op_op_s[] = [
                            'op_op_op_id' => explode('_', $v['value'])[0],
                            'op_op_op_value' => str_replace('"', '',$v['label']),
                            'option_key_value' => str_replace('"', '',$v['value']),
                        ];
                    }
                } else if (@$value['type'] == 'input_with_select') {
                    $op_op_s[] = [
                        'op_op_id' => @explode('_', $value['input']['op_op_key_value'])[0],
                        'op_op_value' => str_replace('"', '', @$value['input']['value'] . ' ' . @$value['select']['value']),
                        'option_key_value' => str_replace('"', '', @$value['input']['op_op_key_value'])
                    ];
                    // $op_op_s[] = [
                    //     'op_op_id' => @explode('_', $value['op_op_key_value'])[0],
                    //     'op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                    //     'option_key_value' => @$value['op_op_key_value']
                    // ];
                } else if (@$value['type'] == 'input') {

                    $op_op_s[] = [
                        'op_op_id' => explode('_', $value['op_op_key_value'])[0],
                        'op_op_value' => str_replace('"', '',$value['value']),
                        'option_key_value' => str_replace('"', '',$value['op_op_key_value']),
                    ];
                } else {
                    $op_op_s[] = [
                        'op_op_id' => @explode('_', $value['op_op_key_value'])[0],
                        'op_op_value' => str_replace('"', '',@$value['label']),
                        'option_key_value' => str_replace('"', '',@$value['op_op_key_value'])
                    ];
                }
            }

            foreach ($op_op_op_matchedKeys as $key => $value) {


                if (isset($value['op_op_op_op_id'])) {

                    if ($value['type'] == 'input_with_select') {
                        $op_op_op_op_s[] = [
                            'op_op_op_op_id' => @explode('_', $value['value'])[0],
                            'op_op_op_op_value' => str_replace('"', '',@$value['input']['value'] . ' ' . @$value['select']['value']),
                            'option_key_value' => str_replace('"', '',@$value['op_op_op_key_value'])
                        ];
                    } else {
                        $op_op_op_op_s[] = [
                            'op_op_op_op_id' => @explode('_', $value['value'])[0],
                            'op_op_op_op_value' => str_replace('"', '',@$value['label']),
                            'option_key_value' => str_replace('"', '',@$value['value'])
                        ];
                    }
                }


                if (isset($value['input']['op_op_id']) || isset($value['input']['op_op_id'])) {

                    if ($value['type'] == 'input_with_select') {
                        $op_op_s[] = [
                            'op_op_id' => @explode('_', $value['input']['op_op_id'])[0],
                            'op_op_value' => str_replace('"', '',@$value['input']['value'] . ' ' . @$value['select']['value']),
                            'option_key_value' => str_replace('"', '',@$value['input']['op_op_id'])
                        ];
                        // $op_op_s[] = [
                        //     'op_op_id' => @explode('_', $value['op_op_id'])[0],
                        //     'op_op_value' => @$value['input']['value'] . ' ' . @$value['select']['value'],
                        //     'option_key_value' => @$value['op_op_id']
                        // ];
                    } else {
                        $op_op_op_op_s[] = [
                            'op_op_id' => @explode('_', $value['op_op_id'])[0],
                            'op_op_value' => str_replace('"', '',@$value['label']),
                            'option_key_value' => str_replace('"', '',@$value['op_op_id'])
                        ];
                    }
                }

                if ($value['type'] == 'input_with_select') {
                    $op_op_op_s[] = [
                        'op_op_op_id' => @explode('_', $value['input']['op_op_op_key_value'])[0],
                        'op_op_op_value' => str_replace('"', '',@$value['input']['value'] . ' ' . @$value['select']['value']),
                        'option_key_value' => str_replace('"', '',@$value['input']['op_op_op_key_value'])
                    ];
                } else {
                    $op_op_op_s[] = [
                        'op_op_op_id' => @explode('_', $value['value'])[2],
                        'op_op_op_value' => str_replace('"', '',@$value['label']),
                        'option_key_value' => str_replace('"', '',@$value['op_op_op_key_value'])
                    ];
                }
            }

            foreach ($op_op_op_op_matchedKeys as $key => $value) {

                $op_op_op_op_s[] = [
                    'op_op_op_op_id' => @explode('_', $value['value'])[0],
                    'op_op_op_op_value' => str_replace('"', '',@$value['label']),
                    'option_key_value' => str_replace('"', '',@$value['value'])
                ];
            }

            $attrib[] = [
                'attribute_id' => $attr_id,
                'attribute_value' => str_replace('"', '',$att['value']),
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



    // Generate barcode and save : Start
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

        // dd($use_in_barcode_ord_id);
        if (!empty($use_in_barcode_ord_id)) {

            $generator = new \Picqer\Barcode\BarcodeGeneratorJPG();
            $image = $generator->getBarcode($use_in_barcode_ord_id, $generator::TYPE_CODE_128);
            $barcode_img_name = public_path('assets/barcode/b/' . $order_id . '.jpg');
            $barcode_img_path = 'assets/barcode/b/' . $order_id . '.jpg';
            file_put_contents($barcode_img_name, $image);

            // dd(8);
            // $barcode_img_path = 'assets/barcode/b/' . $order_id . '.jpg';
            // Storage::disk('public')->put($barcode_img_path, $image);
            // Storage::put($barcode_img_path, $image);
            // return response($image)->header('Content-type', 'image/png');
        }

        return $barcode_img_path;
    }
    // Generate barcode and save : End


    // Managae barcode strng : STart
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
    // Managae barcode strng : End



    // single order delete : Start
    public function deleteOrder($orderId)
    {
        // Delete only if Order Stage id 1, 13, 10, 14 
        $orderDetail = DB::table('b_level_quatation_tbl')
                        ->where('order_id', $orderId)
                        ->select('order_stage')
                        ->first();


        if(!empty($orderDetail)){
            if ($orderDetail && in_array($orderDetail->order_stage, [1, 13, 10, 14])) {
                DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->delete();
                DB::table('b_level_qutation_details')->where('order_id', $orderId)->delete();
                DB::table('b_level_quatation_attributes')->where('order_id', $orderId)->delete();
                DB::table('b_level_quotation_details_mfg_label')->where('order_id', $orderId)->delete();
                // DB::table('order_controller_cart_item')->where('order_id', $orderId)->delete();
                // DB::table('order_hardware_cart_item')->where('order_id', $orderId)->delete();
                // DB::table('order_component_cart_item')->where('order_id', $orderId)->delete();

                $message = "Order id '" . $orderId . "' deleted successfully!";

                return response()->json(['success' => true, 'message' => $message], 200);


            } else {

                $message = "The Order is already in progress, so can't deleted. You can Cancel the order if you want.";

                return response()->json(['success' => false, 'message' => $message], 400);

            }
        }else{

            $message = "Your Order Id is not found";

            return response()->json(['success' => false, 'message' => $message], 400);
        }
    }
    // single order delete : End




    // Multi order Delete : Start
    public function deleteMultiOrders(Request $request)
    {
        $orderIds = $request->order_ids;

        if (!empty($orderIds)) {
            $deletableIds = [];
            $undeletableIds = [];

            foreach ($orderIds as $orderId) {
                
                $orderDetail = DB::table('b_level_quatation_tbl')
                    ->select('order_stage')
                    ->where('order_id', $orderId)
                    ->first();

                if ($orderDetail && in_array($orderDetail->order_stage, [1, 13, 10, 14])) {
                    $deletableIds[] = $orderId;
                } else {
                    $undeletableIds[] = $orderId;
                }
            }

            if (!empty($deletableIds)) {
                DB::table('b_level_quatation_tbl')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                DB::table('b_level_qutation_details')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                DB::table('b_level_quatation_attributes')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                DB::table('b_level_quotation_details_mfg_label')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                DB::table('order_controller_cart_item')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                DB::table('order_hardware_cart_item')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                DB::table('order_component_cart_item')
                    ->whereIn('order_id', $deletableIds)
                    ->delete();

                $deletedIdsPrint = implode(',', $deletableIds);
                $message = "Order ids $deletedIdsPrint have been deleted successfully.";
                return response()->json(['success' => true, 'message' => $message], 200);
            }

            if (!empty($undeletableIds)) {
                $notDeletedIds = implode(',', $undeletableIds);
                $message = "Order ids $notDeletedIds cannot be deleted.";
                return response()->json(['success' => false, 'message' => $message], 400);
            }
        }
    }
    // Multi order Delete : End


    // invoice controller delete : Start
    public function OrderControllerDelete($orderControllerCartItemID)
    {
        $result = DB::table('order_controller_cart_item')
            ->where('order_controller_cart_item_id', $orderControllerCartItemID)
            ->first();

        if (!$result) {
            // Handle if the record is not found
            $message = "Item not found!";
            return response()->json(['success' => false, 'message' => $message], 400);
        }

        $orderID = $result->order_id;
        $unitTotalPrice = $result->item_total_price;

        $result2 = DB::table('b_level_quatation_tbl')
            ->select('subtotal', 'grand_total', 'paid_amount', 'customer_id', 'misc', 'credit', 'invoice_discount', 'installation_charge', 'wholesaler_taxable', 'customer_taxable')
            ->where('order_id', $result->order_id)
            ->first();

        $customerInfo = DB::table('customer_info')
            ->where('customer_id', $result2->customer_id)
            ->first();

        $subtotal = $result2->subtotal;

        // Delete controller order item 
        DB::table('order_controller_cart_item')
            ->where('order_controller_cart_item_id', $orderControllerCartItemID)
            ->delete();

        if ($subtotal == $unitTotalPrice) {
            DB::table('b_level_quatation_tbl')
                ->where('order_id', $orderID)
                ->delete();
        } else {
             
            $this->retailerToWholesalerCalculation($orderID);
        }

        $message = "Order controller deleted successfully!";

        return response()->json(['success' => true, 'message' => $message], 200);
    }
    // invoice controller delete : End

    // invoice component delete : Start
    public function OrderComponentDelete($orderComponentCartItemId)
    {
        $result = DB::table('order_component_cart_item')->where('order_component_cart_item_id', $orderComponentCartItemId)->first();
        
        if (!$result) {
            // Handle if the record is not found
            $message = "Item not found!";
            return response()->json(['success' => false, 'message' => $message], 400);
        }
        $orderId = $result->order_id;
        $unitTotalPrice = $result->component_total_price;

        $result2 = DB::table('b_level_quatation_tbl')->select('subtotal', 'grand_total', 'paid_amount', 'customer_id', 'misc', 'credit', 'invoice_discount', 'installation_charge', 'wholesaler_taxable', 'customer_taxable')->where('order_id', $result->order_id)->first();
    
        $customerInfo = DB::table('customer_info')->where('customer_id', $result2->customer_id)->first();
    
        $subtotal = $result2->subtotal;
    
        // Delete Component order item 
        DB::table('order_component_cart_item')->where('order_component_cart_item_id', $orderComponentCartItemId)->delete();
    
        if ($subtotal == $unitTotalPrice) {
            DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->delete();
        } else {
            // Call common function for calculate the grand total, sub total and due amount : START
            if ($orderId != '') {
                $this->retailerToWholesalerCalculation($orderId);
            }
            // Call common function for calculate the grand total, sub total and due amount : END
        }

        $message = "Order component deleted successfully!";

        return response()->json(['success' => true, 'message' => $message], 200);
    }
    // invoice compponent delete : End
    

    // invoice hardware delete : Start
    public function OrderHardwareDelete($orderHardwareCartItemId)
    {
        $result = DB::table('order_hardware_cart_item')->where('order_hardware_cart_item_id', $orderHardwareCartItemId)->first();
        $orderId = $result->order_id;
        $unitTotalPrice = $result->item_total_price;

        if (!$result) {
            // Handle if the record is not found
            $message = "Item not found!";
            return response()->json(['success' => false, 'message' => $message], 400);
        }

        $result2 = DB::table('b_level_quatation_tbl')->select('subtotal', 'grand_total', 'paid_amount', 'customer_id', 'misc', 'credit', 'invoice_discount', 'installation_charge', 'wholesaler_taxable', 'customer_taxable')->where('order_id', $result->order_id)->first();

        $subtotal = $result2->subtotal;

        // Delete hardware order item 
        DB::table('order_hardware_cart_item')->where('order_hardware_cart_item_id', $orderHardwareCartItemId)->delete();

        if ($subtotal == $unitTotalPrice) {
            DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->delete();
        } else {
            // Call common function for calculate the grand total, sub total and due amount : START
            if ($orderId != '') {
                $this->retailerToWholesalerCalculation($orderId);
            }
            // Call common function for calculate the grand total, sub total and due amount : END
        }

        $message = "Order hardware deleted successfully!";

        return response()->json(['success' => true, 'message' => $message], 200);

    }
    // invoice hardware delete : End


    public function setOrderStage($stage, $orderId)
    {
        if (!empty($stage) && !empty($orderId)) {


            $order_stage = DB::table('order_stage_status')->where('id', $stage)->first();
            $order = DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->first();

            if(empty($order_stage)){
                $message = "order stage id is not found";
                return response()->json(['success' => false, 'message' => $message], 400);   
            }
            if(empty($order)){
                $message = "order id is not found";
                return response()->json(['success' => false, 'message' => $message], 400);   
            }

            // On this all status have to check MFG lebel entry and if not then add entry 
            if (in_array($stage, [4, 16, 20, 15, 5, 21, 8, 9, 19, 7, 12])) {
                $this->createMfgLebelEntry($orderId);
            } else if ($stage == 14) {    // If reset then remove b_level_quotation_mfg_level entry
                $this->removeMfgLebelEntry($orderId);
            }

            // If status change with delivered first time then update the delivery_date : START
            if ($stage == 7 && empty($order->delivery_date)) {
                DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->update(['delivery_date' => date('Y-m-d H:i:s')]);
            }
            // If status change with delivered first time then update the delivery_date : END

            // IF Stage is Mfg(4) then consider the inner mfg label change to in progress : START 
            if ($stage == 4) {
                DB::table('b_level_quotation_details_mfg_label')->where('order_id', $orderId)->update(['status' => 'Mfg pending']);
            }
            // IF Stage is Mfg(4) then consider the inner mfg label change to in progress : END
            if (in_array($stage, [4, 11])) {
                $orderDetails = DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->first();
                if (is_null($orderDetails->from_schedule_date) && is_null($orderDetails->to_schedule_date)) {
                    $toFromDate = now()->addDays(15)->format('Y-m-d');
                    DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->update(['from_schedule_date' => $toFromDate, 'to_schedule_date' => $toFromDate]);
                }
            }

            DB::table('b_level_quatation_tbl')->where('order_id', $orderId)->update(['order_stage' => $stage]);
            // DB::table('quatation_tbl')->where('order_id', $orderId)->update(['order_stage' => $stage]);

            // Get status name based on stage no 
            $stageStatus = DB::table('order_stage_status')->where('order_stage_no', $stage)->first();
            $orderStage = $stageStatus ? $stageStatus->status_name : '';

            // Update status for mfg label if stage is mfg completed or canceled: START
            if (in_array($stage, [15, 16])) {
                $mfgStatus = ($stage == 15) ? 'Mfg completed' : 'Mfg canceled';
                $isSaveScanned = ($stage == 15) ? 1 : 3;

                DB::table('b_level_quotation_details_mfg_label')->where('order_id', $orderId)->update(['status' => $mfgStatus, 'is_save_scanned' => $isSaveScanned]);
            }
            // Update status for mfg label if stage is mfg completed or canceled : END


            $message = 'Order Stage has been updated to ' . $orderStage . ' for ' . $orderId;
            return response()->json(['success' => true, 'message' => $message], 200);

        } else {
            $message = "Something is Wrong"; ;
            return response()->json(['success' => false, 'message' => $message], 400);   
        }
    }


    // modify Amount for Order Receipt : Start
    public function modify_amount(Request $request)  
    {
        $amount = $request->amount;
        $order_id = $request->order_id;
        $type = $request->type;
    
        switch ($type) {
            case 'shipping':
                return $this->updateShippingInstallationCharge($amount, $order_id);
                break;
            case 'credit':
                return$this->updateCredit($amount, $order_id);
                break;
            case 'discount':
                return $this->updateDiscount($amount, $order_id);
                break;
            default:
                $message = "Invalid type provided";
                return response()->json(['success' => false, 'message' => $message], 400);
        }
    
    }
    // modify Amount for Order Receipt : End



    // Order Product Delete : Start
    public function order_product_delete($row_id)
    {
        $result = DB::table('b_level_qutation_details')
            ->select('order_id', 'unit_total_price', 'qutation_image')
            ->where('row_id', $row_id)
            ->first();

            if(empty($result)){
                $message = "Order product Id is not found";
                return response()->json(['success' => false, 'message' => $message], 400);
            }

        $order_id = $result->order_id;
        $unit_total_price = $result->unit_total_price;

        $result2 = DB::table('b_level_quatation_tbl')
            ->select('subtotal', 'grand_total', 'paid_amount','customer_id')
            ->where('order_id', $result->order_id)
            ->first();

        $subtotal = $result2->subtotal;
        $grand_total = $result2->grand_total;
        $paid_amount = $result2->paid_amount;

        // if ($result->qutation_image) {
        //     unlink(public_path($result->qutation_image));
        // }


        if ($subtotal == $unit_total_price) {
            DB::table('b_level_qutation_details')->where('row_id', $row_id)->delete();
            DB::table('b_level_quotation_details_mfg_label')->where('fk_row_id', $row_id)->delete();
            DB::table('b_level_quatation_tbl')->where('order_id', $order_id)->delete();
        
        } else {
            DB::table('b_level_qutation_details')->where('row_id', $row_id)->delete();
            DB::table('b_level_quotation_details_mfg_label')->where('fk_row_id', $row_id)->delete();

            // Call common function for calculate the grand total, sub total and due amount : START
            if ($order_id != '') {
                $customer_id = $result2->customer_id ?? null;
                $main_b_id = $this->level_id;
                $this->retailerToWholesalerShippingCalculation($customer_id, $order_id, $main_b_id);
                $this->retailerToWholesalerCalculation($order_id);
            }
            // Call common function for calculate the grand total, sub total and due amount : END

        }

        $message = "Order product deleted successfully!";

        return response()->json(['success' => true, 'message' => $message], 200);

    }
    // Order Product Delete : End
   
    
    // Edit Order Item : Start
    public function editOrderItem($row_id) {

        $order_item = DB::table('b_level_qutation_details')->where('row_id', $row_id)->first();
        $category = DB::table('categories')->where('id', $order_item->category_id)->first();
        $products = DB::table('products')->where('id', $order_item->product_id)->first();
        $pattern = DB::table('pattern_model_tbl')->where('pattern_model_id', $order_item->pattern_model_id)->first();
        $color = DB::table('colors')->where('id', $order_item->color_id)->first();
        $widthFraction = DB::table('width_height_fractions')->where('id', $order_item->height_fraction_id)->first();
        $heightFraction = DB::table('width_height_fractions')->where('id', $order_item->width_fraction_id)->first();
        $room = DB::table('rooms')->where('room_name', $order_item->room)->first();

        $fracs1 = $category->fractions;
        $fracs = explode(",", $fracs1);

        $hw2 = DB::table('width_height_fractions')->orderBy('decimal_value', 'asc')->get();

        //Upcharege Data convert string to array : start
        $input_string = $order_item->upcharge_details;
        $input_string = trim($input_string, '[]');
        $key_value_pairs = explode('},{', $input_string);
        $upcharge_details_result = [];
        foreach ($key_value_pairs as $pair) {
            preg_match('/upcharge_label:(.*?),upcharge_val:(.*)/', $pair, $matches);
            $upcharge_details_result[] = [
                'upcharge_label' => isset($matches[1]) ? trim($matches[1]) : '',
                'upcharge_val' => isset($matches[2]) ? trim($matches[2]) : ''
            ];
        }
        //Upcharege Data convert string to array : end


       
        $data = [
            "row_id" => $order_item->row_id,
            "selectedCategory" => [
                "value" => $category->category_name,
                "label" =>  $category->category_name,
                "id" => $order_item->category_id,
                "fractions" => []
            ],

            "selectedproduct" => [
                "value" => $products->product_name,
                "label" => $products->product_name,
                "id" =>  $products->id
            ],

            "selectedPattern" => [
                "pattern" => [
                    "label" => $pattern->pattern_name,
                    "value" => $pattern->pattern_model_id,
                    "parentLabel" => "Pattern",
                    "type" => "select",
                    "attributes_type" => "",
                    "color" =>  [
                        "select" => [
                            "label" => $color->color_name ?? "Manual Entry",
                            "value" => $color->color_number ?? $order_item->manual_color_entry
                        ],
                        "input" => [
                            "value" => $color->color_number ?? $order_item->manual_color_entry
                        ]
                    ]
                ]
            ],
            "discountprice" => $order_item->discount,
            "upcharge" => $order_item->upcharge_price,
            "upcharge_details" => json_decode($order_item->upcharge_details) ?? $upcharge_details_result  ,
            "list_price" => $order_item->list_price,
            "width" => (string)$order_item->width,
            "height" => (string)$order_item->height,
            "quantity" => $order_item->product_qty,
            "mainPrice" => $order_item->unit_total_price,
            "widthFraction" => [
                "value" => $widthFraction->decimal_value ?? '',
                "label" => $widthFraction->fraction_value ?? '',
                "id" => $widthFraction->id ?? ''
            ],
            "heightFraction" => [
                "value" => $heightFraction->decimal_value ?? '',
                "label" => $heightFraction->fraction_value ?? '',
                "id" => $heightFraction->id ?? ''
            ],
            "selectedRoom" => [
                "value" => $room->room_name ?? $order_item->room,
                "label" => $room->room_name ?? $order_item->room,
                "id" => $room->id ?? $order_item->room
            ],
            "comments" => $order_item->notes,
            "special_installer_notes" => $order_item->special_installer_notes,

            // "row_id" => $order_item->row_id,
            // "product_id" => (int)$order_item->product_id,
            // "category_id" => $order_item->category_id,
            // "pattern_id" => $order_item->pattern_model_id,
            // "room" => $order_item->room,
            // "room_index" => $order_item->room_index,
            // "color_id" => $order_item->color_id,
            // "width" => $order_item->width,
            // "height" => $order_item->height,
            // "height_fraction_id" => $order_item->height_fraction_id,
            // "width_fraction_id" => $order_item->width_fraction_id,
            // "notes" => $order_item->notes,
            // "special_installer_notes" => $order_item->special_installer_notes,
            // "qty" => $order_item->product_qty,
            // "discount" => $order_item->discount,
            // "list_price" => $order_item->list_price,
            // "upcharge_price" => $order_item->upcharge_price,
            // "upcharge_label" => $order_item->upcharge_label,
            // "upcharge_details" => $order_item->upcharge_details  ,
            // "manual_color_entry" => $order_item->manual_color_entry,
        ];

        foreach ($hw2 as $row) {
            if (in_array($row->fraction_value, $fracs)) {
                $data['selectedCategory']['fractions'][] = [
                    'id' => $row->id, 
                    'fraction_value' => $row->fraction_value,
                    "decimal_value" =>  $row->decimal_value
                ];
            }
        }

        $data['selectedAttributeValues'] = $this->editAttributeData($row_id);
        
       return $data;

        
    }
    // Edit Order Item : End


    // Update Order Item : Start
    public function UpdateOrderItem(OrderUpdateItem $request){

        $row_id = $request->row_id;
        $product_id = $request->product_id;
        $category_id = $request->category_id;
        $sub_category_id = $request->sub_category_id ?? 0;
        $pattern_model_id = $request->pattern_id;
        $manual_pattern_entry = @$request->manual_pattern_entry ?? NULL;
        $manual_color_entry = @$request->manual_color_entry ?? NULL;
        $fabric_price = @$request->fabric_price;
        $color_id = $request->color_id ?? 0;
        $width = $request->width;
        $height = $request->height;
        $h_w_price = $request->list_price;
        $upcharge_price = $request->upcharge_price;
        $qty = intval(@$request->qty) ?? 1;
        // $total_price = $request->total_price;
        $total_price = round(($h_w_price + ($upcharge_price * $qty)), 2);
        $upcharge_label = $request->upcharge_label;
        $upcharge_details = $request->upcharge_details;
        $display_upcharge_details = null;
        $separate_display_upcharge_details = null;
        $notes = @$request->notes;
        $width_fraction_id = $request->width_fraction_id ?? 0;
        $height_fraction_id = $request->height_fraction_id ?? 0;
        $discount = $request->discount;
        $room = $request->room;
        // $room_index = $request->room_index;
        $special_notes_for_installer = $request->special_installer_notes;
            
        $getOldQuotationDetailData = DB::table('b_level_qutation_details')->where('row_id', $row_id)->first();
        $getQuotationLabelData  = DB::table('b_level_quatation_tbl')->where('order_id', $getOldQuotationDetailData->order_id)->first();
        $customer_id = isset($getQuotationLabelData->customer_id) ? $getQuotationLabelData->customer_id : '';
        $product = DB::table('b_cost_factor_tbl')->select('dealer_cost_factor','individual_cost_factor')
            ->where('product_id', $product_id)
            ->where('customer_id', $customer_id)
            ->first();
    

        $commission = [];
        if (!empty($product)) {
            $individual_price = 100 - ($product->dealer_cost_factor * 100);
            $commission = array('dealer_price' => $product->dealer_cost_factor, 'individual_price' => $individual_price);
        } else {
            $product = DB::table('products')->select('dealer_price','individual_price')
                ->where('id', $product_id)
                ->first();
            $commission = array('dealer_price' => $product->dealer_price, 'individual_price' => $product->individual_price);
        }
    
        if (@$commission['dealer_price'] > 0) {
            $discount = 100 - (@$commission['dealer_price'] * 100);
        } else {
            $discount = 0;
        }
    
        //start code for cal by DM
        $per = 0;
        if ($discount != '' && $discount > 0) {
            $per = ($h_w_price * $discount) / 100;
        }
        // $per = ($h_w_price * $discount)/100;
        $unitTotalPrice = $h_w_price - $per;
        $unitTotalPrice = $unitTotalPrice + $upcharge_price;
        //end code for cal by DM


        $attributeData = $this->attributeData($request['attributes']);
        $attributeData = str_replace('\\', '', json_encode($attributeData));
        $attributeData = Str::replaceFirst('"', "", $attributeData);
        $attributeData = Str::replaceLast('"', "", $attributeData);
       
        $newQuatAttrInfo = [
            'product_id' => $product_id,
            'product_attribute' => $attributeData
        ];    
        
        // return $attributeData;
       
        DB::table('b_level_quatation_attributes')
            ->where('fk_od_id', $row_id)
            ->update($newQuatAttrInfo);

        $newProOrdInfo = array(
            'room' => $room,
            'product_id' => $product_id,
            // 'combo_product_details' => $jsonEncodeValue,
            'category_id' => $category_id,
            'sub_category_id' => $sub_category_id,
            'list_price' => $total_price,
            // 'product_qty' => $qty,
            'upcharge_price' => $upcharge_price * $qty,
            'upcharge_label' => $upcharge_label,
            'upcharge_details' => $upcharge_details,
            'display_upcharge_details' => $display_upcharge_details,
            'separate_display_upcharge_details' => $separate_display_upcharge_details,
            // 'unit_total_price' => round(($unitTotalPrice * $qty) , 2),
            'unit_total_price' => round(($unitTotalPrice * $qty) , 2),
            'pattern_model_id' => $pattern_model_id,
            'manual_pattern_entry' => $manual_pattern_entry,
            'manual_color_entry' => $manual_color_entry,
            'fabric_price' => @$fabric_price,
            'color_id' => $color_id,
            'width' => $width,
            'height' => $height,
            'width_fraction_id' => $width_fraction_id,
            'height_fraction_id' => $height_fraction_id,
            'notes' => $notes ?? '',
            // 'room_index' => $room_index,
            // 'drapery_of_cuts' => $drapery_of_cuts,
            // 'drapery_of_cuts_only_panel' => $drapery_of_cuts_only_panel,
            // 'drapery_cut_length' => $drapery_cut_length,
            // 'drapery_total_fabric' => $drapery_total_fabric,
            // 'drapery_total_yards' => $drapery_total_yards,
            // 'drapery_trim_yards' => $drapery_trim_yards,
            // 'drapery_banding_yards' => $drapery_banding_yards,
            // 'drapery_flange_yards' => $drapery_flange_yards,
            // 'drapery_finished_width' => $drapery_finished_width,
            'special_installer_notes' => @$special_notes_for_installer ,
            'discount' => $discount,
            'qutation_image' => null,
            'phase_2_option' =>  0,
            'phase_2_condition' =>  null,
        );


        $newProOrdInfo['product_base_tax'] = 0;
        $unitTotalPrice = $unitTotalPrice * $qty;



        $taxDetail = DB::table('b_level_qutation_details as blqd')
            ->select('tax_percentage', 'is_product_base_tax')
            ->join('b_level_quatation_tbl as blqt', 'blqt.order_id', '=', 'blqd.order_id', 'right')
            ->where('blqd.row_id', $row_id)
            ->first();

        if ($taxDetail && $taxDetail->is_product_base_tax) {
            if ($unitTotalPrice && $taxDetail->tax_percentage) {
                $isTaxableProduct = DB::table('products')
                    ->where('is_taxable', 1)
                    ->where('id', $product_id)
                    ->count();

                if ($isTaxableProduct) {
                    $newProOrdInfo['product_base_tax'] = round(($unitTotalPrice * $taxDetail->tax_percentage / 100), 2);
                }
            }
        }

        // return $newProOrdInfo;

        DB::table('b_level_qutation_details')
        ->where('row_id', $row_id)
        ->update($newProOrdInfo);

        if (isset($getOldQuotationDetailData->order_id) && $getOldQuotationDetailData->order_id != '') {
            $customer_id = isset($getQuotationLabelData->customer_id) ? $getQuotationLabelData->customer_id : '';
            $main_b_id = $this->level_id;
            $this->retailerToWholesalerCalculation($getOldQuotationDetailData->order_id);
        }


        $message = "order item Updated successfully";
        return response()->json(['success' => true, 'message' => $message], 200);  
    
        // return $customer_id;
        
    }
    // Update Order Item : End



    
    // Edit Attribute Data : Start
    public function editAttributeData($row_id)
    {


        $attr = DB::table('b_level_quatation_attributes')->where('fk_od_id', $row_id)->first();
        if(empty($attr)){
            return response()->json(['success' => false, 'message' => 'attribute is not found'], 400);
        }
        $attr_data =   json_decode($attr->product_attribute);
        $attributes = [];

        foreach ($attr_data as $attribute) {

            $op_parent = DB::table('attribute_tbl')->where('attribute_id',@$attribute->attribute_id)->first();
            $option = DB::table('attr_options')->where('att_op_id',@$attribute->options[0]->option_id)->first();
            $opId = "op_id_" . $attribute->attribute_id;
            
            if(@$op_parent->attribute_type == 1){

                $attributes[$opId] = [
                    "value" => @$attribute->attribute_value,
                    "parentLabel" => @$op_parent->attribute_name, 
                    "type" => "input",
                    "attributes_type" => @(int)$attribute->attributes_type
                ];

            }else{

                $attributes[$opId] = [
                    "label" => @$option->option_name,
                    "value" => @$attribute->options[0]->option_key_value,
                    "option_id" => @(int)$attribute->options[0]->option_id,
                    "option_type" => @(int)$attribute->options[0]->option_type,
                    "parentLabel" => @$op_parent->attribute_name, 
                    "type" => "select",
                    "attributes_type" => @(int)$attribute->attributes_type
                ];

            }
            
            // Handle opop
            foreach ($attribute->opop as $opOp) {
                $att_op_id = explode('_',$opOp->option_key_value);
                $op_op_parent = DB::table('attr_options_option_tbl')->where('op_op_id',$opOp->op_op_id)->first();
                // $op_op_value = DB::table('attr_options_option_option_tbl')->where('att_op_op_id',$opOp->op_op_id)->where('att_op_op_op_name', 'like', '%' . $opOp->op_op_value . '%')->first();
                $op_op_value = DB::table('attr_options_option_option_tbl')
                ->where('att_op_op_id',$opOp->op_op_id)
                ->whereRaw('REPLACE(att_op_op_op_name, \'"\', \'\') LIKE ?', ['%'. $opOp->op_op_value .'%'])
                // ->where('att_op_op_op_name', 'like', '%' . $opOp->op_op_value . '%')

                ->first();
                $op_op_type = DB::table('attr_options')->where('att_op_id',$att_op_id[2])->first();



                $check_op_op_att = DB::table('attr_op_op_op_op_tbl')->where('op_op_id',$att_op_id[2])
                // ->where('op_id',$att_op_id[0])
                ->where('attribute_id',$att_op_id[1])->first();

                // print_r($op_op_parent);
                
               if(empty($check_op_op_att)){

                $op_op_att_t_post = DB::table('attr_op_op_op_op_tbl')->where('op_op_id',$att_op_id[0])
                ->where('op_id',$att_op_id[2])->first();

                    if(empty($op_op_att_t_post)){

                        $opOpId = "op_op_id_" . $opOp->op_op_id;
                        if($op_op_type->option_type == 5){

                            $op_op_value_f = explode(' ',$opOp->op_op_value);
                            $fraction = DB::table('width_height_fractions')->where('id',$op_op_value_f[1])->first();

                            $attributes[$opId][$opOpId] = [
                                "type" => "input_with_select",
                                "input" => [
                                    "value" => $op_op_value_f[0],
                                    "parentLabel" => $op_op_parent->op_op_name,
                                    "type" => "input",
                                    "op_op_key_value" => $opOp->option_key_value
                                ],
                                "select" => [
                                    "label" => $fraction->fraction_value,
                                    "value" => (int)$op_op_value_f[1],
                                    "parentLabel" => $op_op_parent->op_op_name,
                                    "op_op_key_value" => $opOp->option_key_value
                                ]
                            
                            ];


                        }else{

                            if($op_op_parent->type == 6){


                                $opOpData = [];
                                foreach ($attribute->opopop as $opOpOp) {

                                    $att_id =  explode('_', @$opOpOp->option_key_value);

                                $op_op_att = DB::table('attr_options_option_tbl')->where('op_op_id',@$att_id[2])->first();
                                $op_op_op_att = DB::table('attr_options_option_option_tbl')->where('att_op_op_op_id',@$att_id[0])->first();

                                    if(@$op_op_att->type == 6){
                                        $opOpData[] = [
                                            "label" => @$op_op_op_att->att_op_op_op_name,
                                            "value" => $opOpOp->option_key_value, // Fill this if available
                                            "op_op_key_value" => $opOp->option_key_value,
                                            "parentLabel" => $op_op_att->op_op_name, // Fill this if available
                                            
                                        ];
                                    }
                                    
                                }
                                $attributes[$opId][$opOpId] = $opOpData;
                            
                            } 
                            else if($op_op_parent->type == 1){

                                $attributes[$opId][$opOpId] = [
                                    // "label" => $opOp->op_op_value,
                                    "value" => $opOp->op_op_value,
                                    "parentLabel" => $op_op_parent->op_op_name,  // Fill this if available
                                    "op_op_key_value" => $opOp->option_key_value,
                                    "type" => "input",
                                    "op_op_key_value" => $opOp->option_key_value // Fill this if available
                                ];

                            }
                            else {

                                $attributes[$opId][$opOpId] = [
                                    "label" => $opOp->op_op_value,
                                    "value" => @$op_op_value->att_op_op_op_id.'_'.@$op_op_value->attribute_id.'_'.@$op_op_value->att_op_op_id,
                                    "op_op_key_value" => $opOp->option_key_value,
                                    "parentLabel" => $op_op_parent->op_op_name,  // Fill this if available
                                    "type" => "select",
                                    "attributes_type" => "" // Fill this if available
                                ];
                            
                            }
                        }

                    }else{


                        if($op_parent->attribute_name == "T-Post"){

                            $opOpId = "op_op_id_" . $attribute->attribute_id;
                            $attributes[$opId][$opOpId] = [
                                "label" => $opOp->op_op_value,
                                "value" => $opOp->option_key_value,
                                "op_op_key_value" => $opOp->option_key_value,
                                "parentLabel" => $op_op_parent->op_op_name,  // Fill this if available
                                "type" => "select",
                                "attributes_type" => "", // Fill this if available
                            ];

                            $opOpData = [];
                            foreach ($attribute->opopop as $opOpOp) {
                                $att_op_op_op_id =  explode('_', $opOpOp->option_key_value);
                                $op_op_att = DB::table('attr_options_option_tbl')->where('op_op_id',@$att_op_op_op_id[2])->first();
                                $check_op_op_op_att = DB::table('attr_options_option_option_tbl')
                                ->where('att_op_op_op_id',@$att_op_op_op_id[0])
                                ->where('att_op_op_id',@$att_op_op_op_id[1])->first();

                                if(!empty($check_op_op_op_att)){
                                    if(@$op_op_att->type != 6){

                                        if(@$check_op_op_op_att->att_op_op_op_type == 5){

                                            $op_op_op_value_f = explode(' ',@$opOpOp->op_op_op_value);
                                            $fraction = DB::table('width_height_fractions')->where('id',@$op_op_op_value_f[1])->first();
                
                                            $opOpData[ $opOpOpId = "op_op_op_id_" . $opOpOp->op_op_op_id] = [
                                                "type" => "input_with_select",
                                                "input" => [
                                                    "value" => $op_op_value_f[0],
                                                    "parentLabel" => @$check_op_op_op_att->att_op_op_op_name,
                                                    "type" => "input",
                                                    "op_op_op_key_value" => $opOpOp->option_key_value,
                                                    "op_op_id" => @$check_op_op_op_att->att_op_op_op_id.'_'. @$check_op_op_op_att->attribute_id.'_'.@$check_op_op_op_att->att_op_op_id
                                                ],
                                                "select" => [
                                                    "label" => @$fraction->fraction_value,
                                                    "value" => (int)$op_op_op_value_f[1],
                                                    "parentLabel" => @$check_op_op_op_att->att_op_op_op_name,
                                                    "op_op_op_key_value" => $opOpOp->option_key_value,
                                                    "op_op_id" => @$check_op_op_op_att->att_op_op_op_id.'_'. @$check_op_op_op_att->attribute_id.'_'.@$check_op_op_op_att->att_op_op_id
                                                ]
                                            
                                            ];
                                        }else{

                                            $op_op_att_t_post = DB::table('attr_op_op_op_op_tbl')
                                            ->where('op_op_op_id',$att_op_op_op_id[0])
                                            ->where('op_op_id',$att_op_op_op_id[1])
                                            ->where('att_op_op_op_op_name',$opOpOp->op_op_op_value)->first();
                
                                            $opOpData[ $opOpOpId = "op_op_op_id_" . $opOpOp->op_op_op_id] = [
                                                "label" => $opOpOp->op_op_op_value,
                                                "value" => $op_op_att_t_post->att_op_op_op_op_id.'_'.$op_op_att_t_post->attribute_id.'_'.$op_op_att_t_post->op_op_op_id,
                                                "parentLabel" => @$check_op_op_op_att->att_op_op_op_name,
                                                "type" => "select",
                                                "attributes_type" => "",
                                                "op_op_op_key_value" => $opOpOp->option_key_value,
                                                "op_op_op_op_id" => true
                                            ];
                                        }
                                    }
                                }
                                
                            }
                            $attributes[$opId][$opOpId] = array_merge($attributes[$opId][$opOpId], $opOpData);

                        }
                        else{

                            $op_value = trim($opOp->op_op_value);
                            // Remove quotes and create a regex pattern
                            $search_value = str_replace('"', '', $op_value);

                            $op_op_value = DB::table('attr_options_option_option_tbl')
                            ->where('attribute_id',@$op_op_att_t_post->attribute_id)
                            ->where('att_op_op_id',@$op_op_att_t_post->op_op_id)
                            ->whereRaw("REPLACE(att_op_op_op_name, '\"', '') = ?", [$search_value])
                            ->first();
                          
                            $opOpId = "op_op_id_" . $opOp->op_op_id;
                            $attributes[$opId][$opOpId] = [
                                "label" => $opOp->op_op_value,
                                "value" => @$op_op_value->att_op_op_op_id.'_'.@$op_op_att_t_post->attribute_id.'_'.@$op_op_att_t_post->op_op_id,
                                "op_op_key_value" => $opOp->option_key_value,
                                "parentLabel" => $op_op_parent->op_op_name,  // Fill this if available
                                "type" => "select",
                                "attributes_type" => "" // Fill this if available
                            ];

                         }
                    }
                }

            }
        
            // Handle opopop
            foreach ($attribute->opopop as $opOpOp) {

                $att_id =  explode('_', @$opOpOp->option_key_value);

                $op_op_att = DB::table('attr_options_option_tbl')->where('op_op_id',@$att_id[2])->first();
                $check_op_op_op_att = DB::table('attr_options_option_option_tbl')
                ->where('att_op_op_op_id',@$att_id[0])
                ->where('att_op_op_id',@$att_id[1])->first();

                if(empty($check_op_op_op_att)){
                    if(@$op_op_att->type != 6){

                        $opOpOpId = "op_op_op_id_" . $opOpOp->op_op_op_id;

                        $attributes[$opId][$opOpOpId] = [
                            "label" => $opOpOp->op_op_op_value,
                            "value" => "", // Fill this if available
                            "parentLabel" => "", // Fill this if available
                            "type" => "select",
                            "attributes_type" => "" // Fill this if available
                        ];


                    }
                }
                
            }
        

            $opOpData = [];
            // Handle opopopop
            foreach ($attribute->opopopop as $opOpOpOp) {

                $att_op_op_op_op_id = explode('_',$opOpOpOp->option_key_value);

                $op_op_op_op_att = DB::table('attr_op_op_op_op_tbl')->where('attribute_id',$attribute->attribute_id)->where('att_op_op_op_op_id',$att_op_op_op_op_id[0])->first();
                
                $check_op_op_op_op_att = DB::table('attr_op_op_op_op_tbl')->where('op_op_op_id',@$att_op_op_op_op_id[2])
                ->where('attribute_id',$att_op_op_op_op_id[1])
                ->where('att_op_op_op_op_id',$att_op_op_op_op_id[0])->first();

                if(empty($check_op_op_op_op_att)){
                    // $opOpOpOpId = "op_op_op_op_id_" . $opOpOpOp->op_op_op_op_id;
                    // $opOpOpOpId = "op_op_op_op_id_" . $attribute->attribute_id;
                    // $attributes[$opId][$opOpOpOpId] = [
                    $opOpData[ $opOpOpOpId = "op_op_op_op_id_" . $attribute->attribute_id] = [
                        "label" => $op_op_op_op_att->att_op_op_op_op_name,
                        "value" =>  $opOpOpOp->option_key_value,
                        "parentLabel" => "", // Fill this if available
                        "type" => "select",
                        "attributes_type" => "" // Fill this if available
                    ];
                    $attributes[$opId][$opOpId] = array_merge($attributes[$opId][$opOpId], $opOpData);
                 }
            }
        }
        
        
        return $attributes;

    }
    // Edit Attribute Data : End



    
    public function editOrder($order_id)
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

            if(empty($orderd)){
                $message = "Order not found";
                return response()->json(['success' => false, 'message' => $message], 400);
            }


        $company_profile = DB::table('company_profile')->select('*')
            ->where('user_id', $this->level_id)
            ->first();
        $customer = DB::table('customers')->select('*')
            ->where('id', $orderd->customer_id)
            ->first();


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
        $barcodeUrl = asset($orderd->barcode);
        $logoUrl = asset('assets/b_level/uploads/appsettings/' . $company_profile->logo);


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


        // return 1;
        // return $b_c_info;

        $order_details = DB::table('b_level_qutation_details')
            ->select(
                'b_level_qutation_details.*',
                'products.product_name',
                'categories.category_name',
                'b_level_quatation_attributes.product_attribute',
                'pattern_model_tbl.pattern_name',
                'colors.color_name',
                'colors.color_number'
            )
            ->leftJoin('products', 'products.id', '=', 'b_level_qutation_details.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'b_level_qutation_details.category_id')
            ->leftJoin('b_level_quatation_attributes', 'b_level_quatation_attributes.fk_od_id', '=', 'b_level_qutation_details.row_id')
            ->leftJoin('pattern_model_tbl', 'pattern_model_tbl.pattern_model_id', '=', 'b_level_qutation_details.pattern_model_id')
            ->leftJoin('colors', 'colors.id', '=', 'b_level_qutation_details.color_id')
            ->where('b_level_qutation_details.order_id', $order_id)
            ->get();

        // return $order_details;

        $user_detail = getCompanyProfileOrderConditionSettings();


        // dd($orderd);
        $data['customer_info']['order_id'] = $orderd->order_id;
        $data['customer_info']['order_date'] = $order_date;
        $data['customer_info']['est_delivery_date'] = $orderd->est_delivery_date;
        $data['customer_info']['current_order_number'] = explode('-',$orderd->order_id)[0];
        $data['customer_info']['side_mark'] = ($orderd->side_mark != '') ? $orderd->side_mark : $customer->side_mark;
        $data['customer_info']['customer_id'] = $customer->id;
        $data['customer_info']['addCustomer']['value'] = $customer->first_name.' '.$customer->last_name;
        $data['customer_info']['addCustomer']['label'] = $customer->first_name.' '.$customer->last_name;
        $data['customer_info']['addCustomer']['id'] = $customer->id;
        $data['customer_info']['addCustomer']['tax_percentage'] = $customer->tax_percentage;

        $data['customer_info']["shipping_address"] = [
                    "receiver_name" => $orderd->receiver_name,
                    "receiver_phone_no" =>$orderd->receiver_phone_no,
                    "receiver_email" => $orderd->receiver_email,
                    "receiver_address" => $orderd->different_shipping_address,
                    "receiver_city" => $orderd->receiver_city,
                    "receiver_state" => $orderd->receiver_state,
                    "receiver_zip" => $orderd->receiver_zip_code,
                    "receiver_country" => $orderd->receiver_country_code,
                    "different_address" => $orderd->is_different_shipping,
                    "different_address_type" => $orderd->is_different_shipping_type,
                    "address_type" => $orderd->address_type,
        ];

         
        // if ($orderd->is_different_shipping == 1 && $orderd->is_different_shipping_type == 2) {

        //     $shipping_address_explode = explode(",", $orderd->different_shipping_address);
        //     $shipping_address = $shipping_address_explode[0];
        //     $data['ship_to']['name'] = $orderd->receiver_name;
        //     $data['ship_to']['shipping_address_label'] = $shipping_address;
        //     $data['ship_to']['shipping_address'] = $shipping_address;
        //     $data['ship_to']['receiver_city'] = $orderd->receiver_city ?? '';
        //     $data['ship_to']['receiver_state'] = $orderd->receiver_state ?? '';
        //     $data['ship_to']['receiver_zip_code'] = $orderd->receiver_zip_code ?? '';
        //     $data['ship_to']['receiver_country_code'] = $orderd->receiver_country_code ?? '';
        //     $data['ship_to']['receiver_phone_no'] = $orderd->receiver_phone_no ?? '';
        //     $data['ship_to']['receiver_email'] = ($b_c_info->customer_type == 'business') ? $orderd->receiver_email : '';
        // } 
        
        $data['products'] = [];
        $i = 1;
        $total_qty = 0;
        $total_final_price = 0;
        $finalTotalPrice = 0;
        $sub_total = array();
        $finalTotal = array();
        $total_tax = 0;


        if ($user_detail->display_total_values == 1) {
            $Totalwidth = array();
            $Totalheight = array();
            if ($company_profile->unit == 'inches') {
                $Total_sqft = array();
            }
            if ($company_profile->unit == 'cm') {
                $Total_sqm = array();
            }
        }

        // return $order_details;
        foreach ($order_details as $key => $item) {

            // dd($item);
            $total_qty += $item->product_qty;
            $table_price = ($item->list_price - $item->upcharge_price);
            $disc_price = ($table_price * $item->discount) / 100;
            $list_price = ($table_price - $disc_price) * $item->product_qty;


            $product_width = $item->width;
            $product_height = $item->height;
            $company_unit = $company_profile->unit;

            array_push($sub_total, $item->unit_total_price);


            $width_fraction = DB::table('width_height_fractions')->where('id', $item->width_fraction_id)->first();
            $height_fraction = DB::table('width_height_fractions')->where('id', $item->height_fraction_id)->first();
            if (!empty($width_fraction->decimal_value)) {
                $decimal_width_value = $width_fraction->decimal_value;
                $product_width = $item->width + $decimal_width_value;
            }
            if (!empty($height_fraction->decimal_value)) {
                $decimal_height_value = $height_fraction->decimal_value;
                $product_height = $item->height + $decimal_height_value;
            }


            if ($item->upcharge_price != '') {
                $up_price = $item->upcharge_price;
            } else {
                $up_price = 0;
            }
            $unit_total_price    = number_format($list_price + $up_price, 2);
            $finalUnitTotalPrice = str_replace(",", "", $unit_total_price);
            array_push($finalTotal, $finalUnitTotalPrice);
            $total_final_price += $list_price + $up_price;

            $categoryData = DB::table('categories')->where('id', $item->category_id)->first();
            $getProductData = DB::table('products')->where('id', $item->product_id)->first();


            if ($user_detail->display_total_values == 1) {
                $product_qty = (int) $item->product_qty;
                $product_width1 = $product_width * $product_qty;
                $product_height1 = $product_height * $product_qty;
                array_push($Totalwidth, $product_width1);
                array_push($Totalheight, $product_height1);

                if ($company_unit == 'inches') {
                    // $sqft = (($product_width*$product_height)/144) * $product_qty;

                    //Get the particular value from table if they select the price style either sqft+table_price or table_price form products
                    if ($getProductData->price_style_type == 1 || $getProductData->price_style_type == 9) {
                        $prince = DB::table('price_style')->where('style_id', $getProductData->price_rowcol_style_id)
                            ->where('row', $product_width)
                            ->where('col', $product_height)
                            ->first();

                        $pc = ($prince != NULL ? $prince->price : 0);

                        if (!empty($prince)) {
                            // It means exact height and width match
                            $st = 1;
                        } else {
                            // It means need to consider next greater value from price style
                            $prince = DB::table('price_style')->where('style_id', $getProductData->price_rowcol_style_id)
                                ->where('row', '>=', $product_width)
                                ->where('col', '>=', $product_height)
                                ->orderBy('row_id', 'asc')
                                ->limit(1)
                                ->first();
                            $pc = ($prince != NULL ? $prince->price : 0);
                            $st = 2;
                        }

                        // Calcualte with sqft + table price : START
                        $sqft_price = 1;
                        if ($getProductData->id != '' && @$item->pattern_model_id != '') {
                            $sqft_data = DB::table('sqft_price_model_mapping_tbl')->where('product_id', $getProductData->id)->where('pattern_id', $item->pattern_model_id)->limit(1)->first();
                            $sqft_price = isset($sqft_data->price) ? $sqft_data->price : 1;
                        }
                        $sqft =  round(($pc * $sqft_price), 2) * $product_qty;
                    } else {
                        $sqft = (($product_width * $product_height) / 144) * $product_qty;
                    }

                    array_push($Total_sqft, $sqft);
                }
                if ($company_unit == 'cm') {
                    $sqm = (($product_width * $product_height) / 10000) * $product_qty;
                    array_push($Total_sqm, $sqm);
                }
            }




            // For Get Sub Category name : START
            $sub_cat_name = '';
            if (isset($item->sub_category_id) && $item->sub_category_id > 0) {
                $sub_category_data = DB::get('categories')->where('id', $item->sub_category_id)->first();
                if (isset($sub_category_data->category_id)) {
                    $sub_cat_name = " (" . $sub_category_data->category_name . ") ";
                }
            }
            // For Get Sub Category name : END


            $is_cat_hide_room = DB::table('products')
                ->select('categories.hide_room', 'categories.hide_color', 'products.hide_room as product_hide_room', 'products.hide_color as product_hide_color')
                ->where('products.id', @$item->product_id)
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->first();


            if($item->room_index!= '') {
                $indexarr = json_decode($item->room_index,true);
                if($indexarr != '') {
                    $room_data = implode(",", $indexarr);
                }
            } else {
                $room_data = $item->room;
            }


            $pattern = DB::table('pattern_model_tbl')->where('pattern_model_id', $item->pattern_model_id)->first();
            $color = DB::table('colors')->where('id', $item->color_id)->first();
            $widthFraction = DB::table('width_height_fractions')->where('id', $item->height_fraction_id)->first();
            $heightFraction = DB::table('width_height_fractions')->where('id', $item->width_fraction_id)->first();
            $room = DB::table('rooms')->where('room_name', $item->room)->first();
    
            $fracs1 = $categoryData->fractions;
            $fracs = explode(",", $fracs1);
    
            $hw2 = DB::table('width_height_fractions')->orderBy('decimal_value', 'asc')->get();
    
            //Upcharege Data convert string to array : start
            $input_string = $item->upcharge_details;
            $input_string = trim($input_string, '[]');
            $key_value_pairs = explode('},{', $input_string);
            $upcharge_details_result = [];
            foreach ($key_value_pairs as $pair) {
                preg_match('/upcharge_label:(.*?),upcharge_val:(.*)/', $pair, $matches);
                $upcharge_details_result[] = [
                    'upcharge_label' => isset($matches[1]) ? trim($matches[1]) : '',
                    'upcharge_val' => isset($matches[2]) ? trim($matches[2]) : ''
                ];
            }


            // $total_qty += $item->product_qty;
            $table_price = ($item->list_price - $item->upcharge_price);
            $disc_price = ($table_price * $item->discount) / 100;
            $list_price = ($table_price - $disc_price) * $item->product_qty;


           


             /// Room data : Start
             $room_datacounter = [];                
             $room = $item->room;
             $old_rooms = json_decode(@$item->room_index, true);
        
             // Check if json_decode returned null and assign an empty array if it did
             if (is_null($old_rooms)) {
                 $old_rooms = [];
             }
             
             foreach ($old_rooms as $key_val => $val) {
                 $room_datacounter[$room][$key_val] = @$item->row_id;
             }

             $roomcoun_arr = array();
             if(!empty($room_datacounter))
             {
                 foreach($room_datacounter as $key=>$val)
                 {
                     foreach($val as $k=>$v)
                     {
                             $counter = $k+1;
                             $roomcoun_arr[$v][]=$key." ".$counter."<=>".$k;
                     }
                     if(count($val)<1){
                         // unset($data['room_datacounter'][$key]);
                     }
                 }
             }


             $missingarraykey=array();
             $hiddencounterarr = array();
             if(isset($roomcoun_arr[$item->row_id]) && count($roomcoun_arr[$item->row_id])>0)
             {

                 $cat_data = DB::table('products')
                 ->select(
                     'categories.hide_room',
                     'categories.hide_color',
                     'products.hide_room as product_hide_room',
                     'products.hide_color as product_hide_color',
                     'products.enable_combo_product',
                     'products.is_taxable',
                     'products.product_base_shipping_status'
                 )
                 ->join('categories', 'categories.id', '=', 'products.category_id')
                 ->where('products.id', $item->product_id)
                 ->first();
                 
               
                 $hiddencounterval = json_encode($roomcoun_arr[$item->row_id]);
                 foreach($roomcoun_arr[$item->row_id] as $key=>$val)
                 {
                     $val = explode("<=>",$val);
                     $hiddencounterarr[$val[1]]=$val[0];
                     $sorthiddencounterarr[$val[1]]=$val[0];
                 }
                 ksort($sorthiddencounterarr);
                //  if($cat_data->product_hide_room == 0 && $cat_data->hide_room == 0)
                //  {
                    //  $data['room_data'][] = $sorthiddencounterarr; 
                     $room_data = $sorthiddencounterarr;

                     // echo "<p class='cart-room'><span>".implode(",</span><span>",$sorthiddencounterarr)."</p>";
                //  }
                 // else
                     //echo "N/A";
                 $sessionarray = @$room_datacounter[$item->room];
                 $firstkey = 0; // get first index of array
                 @end($sessionarray);         
                 $lastkey = @max(array_keys($sessionarray));  // get last index of array
                 for($sessionidex = $firstkey;$sessionidex <= $lastkey;$sessionidex++)
                 {
                      if(!@array_key_exists($sessionidex,$sessionarray)) // check key exist or not
                         array_push($missingarraykey,$sessionidex);
                 }
                 //print_r($hiddencounterarr);
                 @end($hiddencounterarr);   
                 $lastkeyofitemarray = @max(array_keys($hiddencounterarr)); 
                 unset($sorthiddencounterarr);

             }

             $roomindex_data = json_encode($hiddencounterarr);
            
         /// Room data : End
        
           
            $data['products'][] = [
                "row_id" => $item->row_id,
                "selectedCategory" => [
                    "value" => $categoryData->category_name,
                    "label" =>  $categoryData->category_name,
                    "id" => $item->category_id,
                    "fractions" => []
                ],
    
                "selectedproduct" => [
                    "value" => $getProductData->product_name,
                    "label" => $getProductData->product_name,
                    "id" =>  $getProductData->id
                ],
    
                "selectedPattern" => [
                    "pattern" => [
                        "label" => $pattern->pattern_name ?? '',
                        "value" => $pattern->pattern_model_id ?? '',
                        "parentLabel" => "Pattern",
                        "type" => "select",
                        "attributes_type" => "",
                        "color" =>  [
                            "select" => [
                                "label" => $color->color_name ?? "Manual Entry",
                                "value" => $color->color_number ?? $item->manual_color_entry
                            ],
                            "input" => [
                                "value" => $color->color_number ?? $item->manual_color_entry
                            ]
                        ]
                    ]
                ],
                "discountprice" => $item->discount,
                "upcharge" => $item->upcharge_price,
                "upcharge_details" => json_decode($item->upcharge_details) ?? $upcharge_details_result  ,
                "listPrice" => (string)$list_price,
                "width" => (string)$item->width,
                "height" => (string)$item->height,
                "quantity" => $item->product_qty,
                "mainPrice" => $table_price,
                "roomIndex" => $roomindex_data,
                "room" => $room_data,
                "widthFraction" => [
                    "value" => $widthFraction->decimal_value ?? '',
                    "label" => $widthFraction->fraction_value ?? '',
                    "id" => $widthFraction->id ?? ''
                ],
                "heightFraction" => [
                    "value" => $heightFraction->decimal_value ?? '',
                    "label" => $heightFraction->fraction_value ?? '',
                    "id" => $heightFraction->id ?? ''
                ],
                "selectedRoom" => [
                    "value" => $room->room_name ?? $item->room,
                    "label" => $room->room_name ?? $item->room,
                    "id" => $room->id ?? $item->room
                ],
                "comments" => $item->notes,
                "special_installer_notes" => $item->special_installer_notes,
                'selectedAttributeValues' => $this->editAttributeData($item->row_id)
                // 'selectedAttributeValues' => $item->row_id
               
            ];

            
            if (@$orderd->is_product_base_tax == 1) {
                $tax = $item->product_base_tax;
                $total_tax += $tax;
            }


           


        }

        $order_controller_cart_item = DB::table('order_controller_cart_item')->where('order_id', $order_id)->get();
        $order_hardware_cart_item = DB::table('order_hardware_cart_item')->where('order_id', $order_id)->get();
        $order_component_cart_item = DB::table('order_component_cart_item')->where('order_id', $order_id)->get();
        $misc_breakdown_details = DB::table('misc_breakdown_details')
        ->where('order_id', $order_id)
        ->orderBy('id', 'asc')
        ->get();

        if(count($misc_breakdown_details) > 0) {

            $data['misc'] = [];
            foreach($misc_breakdown_details as $c_item_key => $misc) {                   
                    $data['misc'][] = [
                        'order_id' => $misc->order_id,
                        'misc_description' => $misc->misc_description,
                        'misc_unite_cost' => $misc->misc_unite_cost,
                        'misc_qty' => $misc->misc_qty,
                        'misc_price' => $misc->misc_price,
                    ];
            }
        }


        //For Controller Item Cart Item : START 
        if(count($order_controller_cart_item) > 0) {
            $sr_c_item = 0;
            $data['controllers'] = [];
            foreach($order_controller_cart_item as $c_item_key => $c_item) { 
                    $total_qty += $c_item->item_qty;
                    $total_final_price += $c_item->item_total_price;
                    array_push($finalTotal, $c_item->item_total_price);

                    $data['controllers'][] = [
                        'row_id' => $c_item->order_controller_cart_item_id,
                        'qty' => $c_item->item_qty,
                        'name' => $c_item->item_name,
                        'price' => number_format($c_item->item_price,2),
                        'item_total_price' => number_format($c_item->item_total_price,2)
                    ];

            }
        }
        //For Controller Item Cart Item : END 

        // For Component Item Cart Item : START
        if(count($order_component_cart_item) > 0) {
            $data['components'] = [];
            $sr_c_item = 0;
            foreach($order_component_cart_item as $c_item_key => $c_item) { 
                $total_qty += $c_item->component_qty;
                array_push($finalTotal, $c_item->component_total_price);

                $discount_rate = 0;
                if (isset($c_item->dealer_cost_factor) && $c_item->dealer_cost_factor > 0) {
                    $discount_rate = $c_item->discount;
                } else {
                    $discount_rate = 0;
                }

                if(isset($discount_rate) && $discount_rate!=0){
                   
                    $item_final_price = $c_item->list_price;
                }else{
                    $item_final_price = $c_item->component_total_price;
                }
                $total_final_price += $item_final_price;


                $data['components'][] = [
                    'row_id' => $c_item->order_component_cart_item_id,
                    'qty' => $c_item->component_qty,
                    'name' => $c_item->part_name,
                    'price' => number_format($c_item->part_price,2),
                    'discount' => $discount_rate,
                    'item_total_price' => number_format($item_final_price,2)
                ];

            }

        }
        // For Component Item Cart Item : END

        // For Hardware Item Cart Item : START
        if(count($order_hardware_cart_item) > 0) { 
            $data['hardware'] = [];
            $sr_h_item = 0;
            foreach($order_hardware_cart_item as $h_item_key => $h_item) { 
                $total_qty += $h_item->item_qty;
                array_push($finalTotal, $h_item->item_total_price);
                $total_final_price += $h_item->item_total_price;

                $item_details = DB::table('hardware_sub_group_detail as hsgd')
                ->select(
                    'v.vendor_name',
                    'g.group_name',
                    'h.h_product_name as product_name',
                    'hsg.group_name as sub_group_name',
                    'hsgd.hardware_sub_group_detail_name as item_name',
                    'f.finish_name',
                    'is_taxable'
                )
                ->leftJoin('hardware_sub_group as hsg', 'hsg.hardware_sub_group_id', '=', 'hsgd.hardware_sub_group_id')
                ->leftJoin('hardware as h', 'h.hardware_id', '=', 'hsg.hardware_id')
                ->leftJoin('vendor as v', 'v.vendor_id', '=', 'h.h_vendor_id')
                ->leftJoin('group as g', 'g.group_id', '=', 'h.h_group_id')
                ->leftJoin('finish as f', 'f.finish_id', '=', DB::raw($h_item->finish_id))
                ->where('hsgd.hardware_sub_group_detail_id', $h_item->hardware_sub_group_detail_id)
                ->first();

                $data['hardware'][] = [
                    'row_id' => $h_item->order_hardware_cart_item_id,
                    'qty' => $h_item->item_qty,
                    'name' => [
                            'vendor_name' =>   @$item_details->vendor_name,
                            'group_name' =>   @$item_details->group_name,
                            'product_name' =>   @$item_details->product_name,
                            'sub_group_name' =>   @$item_details->sub_group_name,
                            'item_name' =>   @$item_details->item_name,
                            'finish_name' =>   @$item_details->finish_name
                    ],
                    'price' => number_format($h_item->item_price,2),
                    'item_total_price' => number_format($h_item->item_total_price,2)
                ];
            }
        }
        // For Hardware Item Cart Item : END




        


        

        // $finalTotalPrice = $orderd->subtotal;
        $finalTotalPrice = $total_final_price;
        // For Sales Tax : START
        $customer_based_tax = 0;
        if (@$orderd->is_product_base_tax == 1) {
            $customer_based_tax = round($total_tax, 2);
        } else {
            if ($orderd->tax_percentage != '' && $orderd->tax_percentage > 0) {
                $customer_based_tax = ($finalTotalPrice * $orderd->tax_percentage / 100);
            }
        }
        // For Sales Tax : END

        // Shipping percentage calculation : START
        $shipping_charges = 0;
        if ($orderd->shipping_percentage != '' && $orderd->shipping_percentage > 0) {
            $shipping_charges = (($finalTotalPrice * $orderd->shipping_percentage) / 100);
        }
        // Shipping percentage calculation : END
        if (isset($finalTotalPrice) && !empty($finalTotalPrice)) {
            $finalTotalPrice = $finalTotalPrice;
        } else {
            $finalTotalPrice = 0;
        }
        // Shipping percentage calculation : END
        $order_misc_charges = (isset($orderd->misc) && ($orderd->misc != '')) ? $orderd->misc : 0;
        $order_other_charges = (isset($orderd->other_charge) && ($orderd->other_charge != '')) ? $orderd->other_charge : 0;
        $shipping_installation_chargej = $orderd->installation_charge + $orderd->shipping_charges + $shipping_charges;
        $grandtotals = ($finalTotalPrice + $customer_based_tax + $shipping_installation_chargej + $order_misc_charges + $order_other_charges) - $orderd->invoice_discount;
        $checkdueamt = $grandtotals - $orderd->paid_amount;

        $shipping_installation_charge = $orderd->installation_charge + $orderd->shipping_charges + $shipping_charges;
        $order_misccharges = (isset($orderd->misc) && ($orderd->misc != '')) ? $orderd->misc : 0;
        $allow_max_credit = $orderd->credit + $orderd->due;
        $allow_max_discount = $orderd->invoice_discount + $orderd->due;

        // Total Section Start
        $data['total'] = [
            'qty' => $total_qty,
        ];
      
        $data['total']['price'] = $company_profile->currency . number_format($total_final_price, 2);
        $data['total']['sales_tax'] =  $company_profile->currency . number_format(($customer_based_tax), 2);
        $data['total']['sub_total'] =  $company_profile->currency . number_format(($finalTotalPrice), 2);
        // $data['total']['shipping_installation_charge'] =  $shipping_installation_charge;
        $data['total']['misc'] =  $company_profile->currency . $order_misccharges;
        // $data['total']['credit'] =  number_format($orderd->credit, 2);
        // $data['total']['allow_max_credit'] =  number_format(($allow_max_credit),2);
        $data['total']['discount'] =  number_format($orderd->invoice_discount, 2);
        // $data['total']['allow_max_discount'] =  number_format(($allow_max_discount),2);
        $data['total']['grand_total'] =  $company_profile->currency . number_format($grandtotals, 2);
        // $data['total']['deposit'] =  $company_profile->currency . number_format($orderd->paid_amount, 2);
        // $data['total']['due'] =  $company_profile->currency . number_format($checkdueamt, 2);

        return $data;
    }



    public function getAllRetailerOrderStage()
    {
        $statusData = DB::table('order_stage_status')
            ->select('*')
            ->where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('position', 'asc')
            ->get();

        foreach ($statusData as $key => $status) {
            $childStatusData = DB::table('order_stage_status')
                ->select('*')
                ->where('status', 1)
                ->where('parent_id', $status->order_stage_no)
                ->orderBy('position', 'asc')
                ->get();

            $status->child_statuses = $childStatusData;
        }

        return $statusData;
    }

    public function filterOptions() {
        $user = auth()->user();
        $userId = $user->id;
        $isAdmin = $user->is_admin;
    
        // Customer Data : start
        $customersQuery = DB::table('customers')
            ->select('customers.id', 'customers.customer_user_id', 'customers.first_name', 'customers.last_name', 'customers.company', 'customers.customer_no')
            ->join('users', 'customers.customer_user_id', '=', 'users.user_id')
            ->join('user_info', 'customers.customer_user_id', '=', 'user_info.id')
            ->where('customers.level_id', $this->level_id)
            ->where('users.status', 1)
            ->where('user_info.wholesaler_connection', 1)
            ->orderBy('customers.id', 'desc');
    
        if (!$isAdmin) {
            $customersQuery->whereRaw("FIND_IN_SET(?, customers.responsible_employee) <> 0", [$userId]);
        }
    
        $customers = [
            ['id' => '', 'name' => '--Select Customer--']
        ];
        
        $customersFromQuery = $customersQuery->get()->map(function ($value) {
            return [
                'id' => $value->id,
                'name' => $value->company ?: trim($value->first_name . " " . $value->last_name)
            ];
        })->toArray();
        $customers = array_merge($customers, $customersFromQuery);
        $data['customers'] = $customers;
        // Customer Data : End
    
        // Sales Incharge Data : Start
        $createdBy = $isAdmin ? $user->user_id : $user->userinfo->created_by;
    
        $salesIncharge = DB::table('user_info')
            ->select('id', DB::raw("CONCAT_WS(' ', first_name, last_name) AS name"))
            ->where(function ($query) use ($createdBy) {
                $query->where('created_by', $createdBy)
                    ->orWhere('id', $createdBy);
            })
            ->where('user_type', 'b')
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        $salesInchargeList = array_merge(
            [['id' => '', 'name' => '--Select Sales Incharge--']],
            $salesIncharge
        );

        $data['sales_incharge'] = $salesInchargeList;
        // Sales Incharge Data : End
    
        // Payment status : Start
        // $data['payment_status'] = ['Unpaid', 'Paid', 'Credit', 'Partially Paid'];
        $data['payment_status'][] = ['id'=> '', 'name'=> '--Select Payment--'];
        $data['payment_status'][] = ['id'=> 'Unpaid', 'name'=> 'Unpaid'];
        $data['payment_status'][] = ['id'=> 'Paid', 'name'=> 'Paid'];
        $data['payment_status'][] = ['id'=> 'Credit', 'name'=> 'Credit'];
        $data['payment_status'][] = ['id'=> 'Partially', 'name'=> 'Partially'];
        // Payment status : End
    
        // Stages : Start

        $stageList = array_merge(
            [['id' => '', 'status_name' => '--Select Order Stage--']],
            $this->getAllRetailerOrderStage()->toArray()
        );
        $data['stages'] = $stageList;
        // Stages : End
    
        return response()->json($data);
    }
    


   



}
