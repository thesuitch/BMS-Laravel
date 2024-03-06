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
            if (auth()->user()->is_admin == 1) {
                $this->level_id = auth()->user()->user_id;
            } else {
                $this->level_id = auth()->user()->userinfo->created_by;
            }
            $this->user_id = auth()->user()->user_id;
            return $next($request);
        });
    }


    function getCategoryProducts()
    {
        try {

            $userId = auth()->user()->id;

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

            if (auth()->user()->user_type == 'c') {
                $userInfo = $this->checkRetailerConnectToWholesaler(auth()->user()->id);
                $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? auth()->user()->main_b_id : auth()->user()->level_id;
            } else {
                $createdBy = auth()->user()->level_id;
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
            $discount = $this->getProductCommission($product_id,$customer_id);
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
        $customerDetails = DB::table('customers')->where('id', $customer_id)->first();
        $shippingAddress = DB::table('shipping_address_info')->where('customer_id', $customer_id)->first();
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
                'billingAddressLabel' => $billingAddressLabel,
                'customerDetails' => $customerDetails,
                'customerAddress' => $customerAddress,
            ];
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

            $resInfo[] = [
                'ShippingAddressLabel' => $addressLabel,
                'shippingAddress' => $shippingAddress,
                'ShippingAddress' => $resAddress,
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



    function store(Request $request)
    {
        foreach ($request->att_options as $key => $attr) {
            // echo $key;
            print_r($attr);
        }
    }
}
