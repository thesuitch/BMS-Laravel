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

            $categories = Category::with(['products' => function ($query) {
                $query->select('id', 'category_id', 'product_name', 'default');
            }])
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
                $category['custom_labels'] =  $custom_label;
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

            $data = [

                'patterns' => $this->getColorPartanModel($product_id),
                'attributes' => $this->getProductToAttribute($product_id)
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


        $responseData = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Data retrieved successfully',
            'data' => $resInfo
        ];

        return response()->json($responseData);
    }







    // public function calculateUpCondition(
    //     $upConditionHeight = '0',
    //     $upConditionHeightFraction = '0',
    //     $upConditionWidth = '0',
    //     $upConditionWidthFraction = '0',
    //     $upAttributeId,
    //     $upLevel = '0',
    //     $productId = 0,
    //     $patternId = 0
    // ) {
    //     $upAttributeIdArray = explode("_", $upAttributeId);
    //     $upchargeAttributeId = end($upAttributeIdArray);

    //     // $upData = DB::table('upcharges_price_condition as upc')
    //     //     ->join('upcharges_price_condition_attributes as upca', 'upc.upcharges_price_condition_id', '=', 'upca.upcharges_price_condition_id', 'FULL')
    //     //     ->where('upca.upcharge_attribute_id', $upchargeAttributeId)
    //     //     ->where('upca.attribute_level', $upLevel)
    //     //     ->where('upc.created_by', $this->level_id)
    //     //     ->where('upc.is_active', 1)
    //     //     ->get();

    //     $upData = DB::table('upcharges_price_condition as upc')
    //         ->leftJoin('upcharges_price_condition_attributes as upca', 'upc.upcharges_price_condition_id', '=', 'upca.upcharges_price_condition_id')
    //         ->where('upca.upcharge_attribute_id', $upchargeAttributeId)
    //         ->where('upca.attribute_level', $upLevel)
    //         ->where('upc.created_by', $this->level_id)
    //         ->where('upc.is_active', 1)
    //         ->get();


    //     $finalUpConditionPrice = 0;

    //     if ($upData->count() > 0) {
    //         $isCheckUpCondition = true;

    //         if (request()->has('phase_2_attr')) {
    //             $phase2Attr = explode(",", request()->input('phase_2_attr'));

    //             if (in_array($upchargeAttributeId, $phase2Attr)) {
    //                 $isCheckUpCondition = false;
    //             }
    //         }

    //         if ($isCheckUpCondition) {
    //             foreach ($upData as $key => $rec) {
    //                 if ($rec->condition_type == '1') {
    //                     // Height
    //                     $frHeight = 0;
    //                     if ($upConditionHeightFraction != 0) {
    //                         $frData = DB::table('width_height_fractions')->where('id', $upConditionHeightFraction)->first();
    //                         $frHeight = $frData->decimal_value;
    //                     }
    //                     $finalHeight = $upConditionHeight + $frHeight;

    //                     if ($finalHeight > 0) {
    //                         if ($rec->condition_operation == '1') {
    //                             // Inch/CM
    //                             // Formula : Height * inches and operator value
    //                             $upInchesDetailsData = DB::table('upcharges_price_inches_condition_details')
    //                                 ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
    //                                 ->get();

    //                             $finalExPrice = 0;
    //                             foreach ($upInchesDetailsData as $kkk => $val1) {
    //                                 $perExPrice = $finalHeight * $val1->per_inches_value;
    //                                 $perInchesDetailsArr = unserialize($val1->per_inches_details);

    //                                 if (is_array($perInchesDetailsArr)) {
    //                                     foreach ($perInchesDetailsArr as $key => $value) {
    //                                         $oper = $value['per_inches_operator'];
    //                                         $perExPrice = $this->calculateTotalAmt($perExPrice, $oper, $value['per_inches_amt']);
    //                                     }
    //                                 }
    //                                 $finalExPrice += $perExPrice;
    //                             }
    //                             $finalUpConditionPrice += round($finalExPrice, 2);
    //                         } else {
    //                             // Manual
    //                             // Formula : Base price and operator
    //                             $upDetailsData = DB::table('upcharges_price_condition_details')
    //                                 ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
    //                                 ->where('min_w_h', '<=', $finalHeight)
    //                                 ->where('max_w_h', '>=', $finalHeight)
    //                                 ->orderBy('upcharges_price_condition_details_id', 'asc')
    //                                 ->limit(1)
    //                                 ->first();

    //                             $manualPrice = 0;
    //                             if ($upDetailsData) {
    //                                 $basePrice = $upDetailsData->base_price;
    //                                 $priceDetails = $upDetailsData->price_details;

    //                                 $manualPrice = $basePrice;
    //                                 if ($priceDetails != '') {
    //                                     $priceDetailsArr = unserialize($priceDetails);
    //                                     if (is_array($priceDetailsArr)) {
    //                                         foreach ($priceDetailsArr as $key => $value) {
    //                                             $oper = $value['price_details_operator'];
    //                                             $manualPrice = $this->calculateTotalAmt($manualPrice, $oper, $value['price_details_value']);
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                             $finalUpConditionPrice += round($manualPrice, 2);
    //                         }
    //                     }
    //                 } elseif ($rec->condition_type == '2') {
    //                     // Width
    //                     $frWidth = 0;
    //                     if ($upConditionWidthFraction != 0) {
    //                         $frData = DB::table('width_height_fractions')->where('id', $upConditionWidthFraction)->first();
    //                         $frWidth = $frData->decimal_value;
    //                     }
    //                     $finalWidth = $upConditionWidth + $frWidth;

    //                     if ($finalWidth > 0) {
    //                         if ($rec->condition_operation == '1') {
    //                             // Inch/CM
    //                             // Formula : Width * inches and operator value
    //                             $upInchesDetailsData = DB::table('upcharges_price_inches_condition_details')
    //                                 ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
    //                                 ->get();

    //                             $finalExPrice = 0;
    //                             foreach ($upInchesDetailsData as $kkk => $val1) {
    //                                 $perExPrice = $finalWidth * $val1->per_inches_value;
    //                                 $perInchesDetailsArr = unserialize($val1->per_inches_details);

    //                                 if (is_array($perInchesDetailsArr)) {
    //                                     foreach ($perInchesDetailsArr as $key => $value) {
    //                                         $oper = $value['per_inches_operator'];
    //                                         $perExPrice = $this->calculateTotalAmt($perExPrice, $oper, $value['per_inches_amt']);
    //                                     }
    //                                 }
    //                                 $finalExPrice += $perExPrice;
    //                             }
    //                             $finalUpConditionPrice += round($finalExPrice, 2);
    //                         } else {
    //                             // Manual
    //                             // Formula : Base price and operator
    //                             $upDetailsData = DB::table('upcharges_price_condition_details')
    //                                 ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
    //                                 ->where('min_w_h', '<=', $finalWidth)
    //                                 ->where('max_w_h', '>=', $finalWidth)
    //                                 ->orderBy('upcharges_price_condition_details_id', 'asc')
    //                                 ->limit(1)
    //                                 ->first();

    //                             $manualPrice = 0;
    //                             if ($upDetailsData) {
    //                                 $basePrice = $upDetailsData->base_price;
    //                                 $priceDetails = $upDetailsData->price_details;

    //                                 $manualPrice = $basePrice;
    //                                 if ($priceDetails != '') {
    //                                     $priceDetailsArr = unserialize($priceDetails);
    //                                     if (is_array($priceDetailsArr)) {
    //                                         foreach ($priceDetailsArr as $key => $value) {
    //                                             $oper = $value['price_details_operator'];
    //                                             $manualPrice = $this->calculateTotalAmt($manualPrice, $oper, $value['price_details_value']);
    //                                         }
    //                                     }
    //                                 }
    //                             }
    //                             $finalUpConditionPrice += round($manualPrice, 2);
    //                         }
    //                     }
    //                 } elseif ($rec->condition_type == '3') {
    //                     $price = 0;
    //                     $upchargesConditionFormula = DB::table('upcharges_price_condition_formula')
    //                         ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
    //                         ->first();

    //                     if ($upchargesConditionFormula) {
    //                         $frWidth = 0;
    //                         if ($upConditionWidthFraction != 0) {
    //                             $frData = DB::table('width_height_fractions')->where('id', $upConditionWidthFraction)->first();
    //                             $frWidth = $frData->decimal_value;
    //                         }
    //                         $finalWidth = $upConditionWidth + $frWidth;

    //                         $frHeight = 0;
    //                         if ($upConditionHeightFraction != 0) {
    //                             $frData = DB::table('width_height_fractions')->where('id', $upConditionHeightFraction)->first();
    //                             $frHeight = $frData->decimal_value;
    //                         }
    //                         $finalHeight = $upConditionHeight + $frHeight;

    //                         $orderAttrArr = request()->input('order_attr_arr', $this->formatAttributeArray());

    //                         $actualProductFormula = unserialize($upchargesConditionFormula->upcharges_formula);

    //                         if (count($actualProductFormula) > 0) {
    //                             $extraArr = [
    //                                 'attribute' => 'attribute',
    //                                 'attr_id' => 'attr_id',
    //                                 'attribute_level' => 'attribute_level',
    //                                 'custom_text' => 'custom_text',
    //                             ];

    //                             $finalFormula = $this->makeAttributeFormula($actualProductFormula, $orderAttrArr, $finalWidth, $finalHeight, $extraArr);
    //                             $finalVal = $this->convertFormulaToValue($finalFormula);

    //                             $price = $finalVal;

    //                             // For Cuts if even then we need to consider the roundup always
    //                             if (strpos($finalFormula, 'custom_round_even') !== false) {
    //                                 $newFinalFormula = str_replace('custom_round_even', 'ceil', $finalFormula);
    //                                 $price = $this->convertFormulaToValue($newFinalFormula);
    //                             }
    //                         }
    //                     }

    //                     $finalUpConditionPrice += $price;
    //                 } elseif ($rec->condition_type == '4') {
    //                     $frWidth = 0;
    //                     if ($upConditionWidthFraction != 0) {
    //                         $frData = DB::table('width_height_fractions')->where('id', $upConditionWidthFraction)->first();
    //                         $frWidth = $frData->decimal_value;
    //                     }
    //                     $finalWidth = $upConditionWidth + $frWidth;

    //                     $frHeight = 0;
    //                     if ($upConditionHeightFraction != 0) {
    //                         $frData = DB::table('width_height_fractions')->where('id', $upConditionHeightFraction)->first();
    //                         $frHeight = $frData->decimal_value;
    //                     }
    //                     $finalHeight = $upConditionHeight + $frHeight;

    //                     $tablePrice = DB::table('price_style')
    //                         ->where('style_id', $rec->table_condition_id)
    //                         ->where('row', $finalWidth)
    //                         ->where('col', $finalHeight)
    //                         ->first();

    //                     if (!$tablePrice) {
    //                         $tablePrice = DB::table('price_style')
    //                             ->where('style_id', $rec->table_condition_id)
    //                             ->where('row', '>=', $finalWidth)
    //                             ->where('col', '>=', $finalHeight)
    //                             ->orderBy('row_id', 'asc')
    //                             ->limit(1)
    //                             ->first();
    //                     }

    //                     $finalUpConditionPrice = $tablePrice ? $tablePrice->price : 0;
    //                 }
    //             }
    //         }

    //         return $finalUpConditionPrice;
    //     } else {
    //         return false;
    //     }
    // }



    public function calculateUpCondition(
        $upConditionHeight = '0',
        $upConditionHeightFraction = '0',
        $upConditionWidth = '0',
        $upConditionWidthFraction = '0',
        $upAttributeId,
        $upLevel = '0',
        $productId = 0,
        $patternId = 0
    ) {
        $upAttributeIdArray = explode("_", $upAttributeId);
        $upchargeAttributeId = end($upAttributeIdArray);
        $finalFormula = "";
        $mainArr = [];
        // $commonModel = new Common();

        if (auth()->user()->user_type == 'c') {
            $userType = 'retailer';
            $userInfo = $this->checkRetailerConnectToWholesaler(auth()->user()->user_id);

            if (isset($userInfo['id']) && $userInfo['id'] != '') {
                $createdBy = auth()->user()->user_id;
            } else {
                $createdBy = auth()->user()->main_b_id;
            }
        } else {
            $userType = 'wholesaler';
            $isAdmin = auth()->user()->isAdmin;

            if ($isAdmin == 1) {
                $createdBy = auth()->user()->user_id;
            } else {
                $createdBy = auth()->user()->admin_created_by;

                if (empty($createdBy)) {
                    $createdBy = auth()->user()->user_id;
                }
            }
        }

        $upData = DB::table('upcharges_price_condition')
            ->join('upcharges_price_condition_attributes as upca', 'upcharges_price_condition.upcharges_price_condition_id', '=', 'upca.upcharges_price_condition_id')
            ->where('upca.upcharge_attribute_id', $upchargeAttributeId)
            ->where('upca.attribute_level', $upLevel)
            ->where('upcharges_price_condition.created_by', $createdBy)
            ->whereRaw('FIND_IN_SET(' . $productId . ', upcharges_price_condition.product_ids) <> 0')
            ->where('upcharges_price_condition.is_active', 1)
            ->get()
            ->toArray();

        // dd($upData->toSql());

        $response = [];
        $response['upcharges'] = [];
        $img = "";
        $getImg = [];

        if ($createdBy == auth()->user()->level_id) {
            $userDetail = $this->getCompanyProfileOrderConditionSettings(auth()->user()->level_id);
        } else {
            $userDetail = $this->getCompanyProfileOrderConditionSettingsPart2($createdBy);
        }

        if ($userDetail->enable_attribute_image == 1) {
            if ($upLevel == 1) {
                $getImg = DB::table("attr_options")->select("attributes_images")
                    ->where("att_op_id", $upchargeAttributeId)
                    ->first();
            } elseif ($upLevel == 2) {
                $getImg = DB::table("attr_options_option_tbl")->select("att_op_op_images as attributes_images")
                    ->where("op_op_id", $upchargeAttributeId)
                    ->first();
            } elseif ($upLevel == 3) {
                $getImg = DB::table("attr_options_option_option_tbl")->select("att_op_op_op_images as attributes_images")
                    ->where("att_op_op_op_id", $upchargeAttributeId)
                    ->first();
            } elseif ($upLevel == 4) {
                $getImg = DB::table("attr_op_op_op_op_tbl")->select("att_op_op_op_op_images as attributes_images")
                    ->where("att_op_op_op_op_id", $upchargeAttributeId)
                    ->first();
            }

            if ($getImg) {
                $img = $getImg->attributes_images;
            }
        }


        $response['attribute_img'] = $img;

        if (count($upData) > 0) {
            $costFactorData = $this->commonWholesalerToRetailerCommission($productId);
            $costFactorRate = $costFactorData['dealer_price'];

            foreach ($upData as $key => $rec) {
                $isCheckUpCondition = true;

                if (request()->input('phase_2_up_id')) {
                    $phase2UpId = explode(",", request()->input('phase_2_up_id'));

                    if (in_array($rec->upcharges_price_condition_id, $phase2UpId)) {
                        $isCheckUpCondition = false;
                    }
                }

                // if ($isCheckUpCondition) {
                //     // ... (remaining code for calculations)
                // }

                if ($isCheckUpCondition) {
                    $display_name = "";
                    $purpose = 0;
                    $related_attr_class = [];

                    $final_up_condition_price = 0;

                    if ($rec->condition_type == '1') {
                        // Height
                        $fr_height = 0;

                        if ($upConditionHeightFraction != 0) {
                            $fr_data = DB::table('width_height_fractions')->where('id', $upConditionHeightFraction)->first();
                            $fr_height = $fr_data->decimal_value;
                        }

                        $final_height = $upConditionHeight + $fr_height;

                        if ($final_height > 0) {
                            if ($rec->condition_operation == '1') {
                                // Inch/CM
                                // Formula : Height * inches and oprator value

                                $up_inches_details_data = DB::table('upcharges_price_inches_condition_details')
                                    ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
                                    ->get()
                                    ->toArray();

                                $final_ex_price = 0;

                                foreach ($up_inches_details_data as $kkk => $val1) {
                                    $per_ex_price = $final_height * $val1->per_inches_value;
                                    $per_inches_details_arr = unserialize($val1->per_inches_details);

                                    if (is_array($per_inches_details_arr)) {
                                        foreach ($per_inches_details_arr as $key => $value) {
                                            $oper = $value['per_inches_operator'];
                                            $per_ex_price = $this->calculateTotalAmt($per_ex_price, $oper, $value['per_inches_amt']);
                                        }
                                    }

                                    $final_ex_price += $per_ex_price;
                                }

                                $final_up_condition_price = round($final_ex_price, 2);
                            } else {
                                // Manual
                                // Formula : Base price and operator
                                $up_details_data = DB::table('upcharges_price_condition_details')
                                    ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
                                    ->where('min_w_h', '<=', $final_height)
                                    ->where('max_w_h', '>=', $final_height)
                                    ->orderBy('upcharges_price_condition_details_id', 'asc')
                                    ->limit(1)
                                    ->first();

                                $manual_price = 0;

                                if ($up_details_data) {
                                    $base_price = $up_details_data->base_price;
                                    $price_details = $up_details_data->price_details;

                                    $manual_price = $base_price;

                                    if ($price_details != '') {
                                        $price_details_arr = unserialize($price_details);

                                        if (is_array($price_details_arr)) {
                                            foreach ($price_details_arr as $key => $value) {
                                                $oper = $value['price_details_operator'];
                                                $manual_price = $this->calculateTotalAmt($manual_price, $oper, $value['price_details_value']);
                                            }
                                        }
                                    }
                                }

                                $final_up_condition_price = round($manual_price, 2);
                            }
                        }
                    } elseif ($rec->condition_type == '2') {
                        // Width
                        $fr_width = 0;

                        if ($upConditionWidthFraction != 0) {
                            $fr_data = DB::table('width_height_fractions')->where('id', $upConditionWidthFraction)->first();
                            $fr_width = $fr_data->decimal_value;
                        }

                        $final_width = $upConditionWidth + $fr_width;

                        if ($final_width > 0) {
                            if ($rec->condition_operation == '1') {
                                // Inch/CM
                                // Formula : Width * inches and oprator value
                                $up_inches_details_data = DB::table('upcharges_price_inches_condition_details')
                                    ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
                                    ->get()
                                    ->toArray();

                                $final_ex_price = 0;

                                foreach ($up_inches_details_data as $kkk => $val1) {
                                    $per_ex_price = $final_width * $val1->per_inches_value;
                                    $per_inches_details_arr = unserialize($val1->per_inches_details);

                                    if (is_array($per_inches_details_arr)) {
                                        foreach ($per_inches_details_arr as $key => $value) {
                                            $oper = $value['per_inches_operator'];
                                            $per_ex_price = $this->calculateTotalAmt($per_ex_price, $oper, $value['per_inches_amt']);
                                        }
                                    }

                                    $final_ex_price += $per_ex_price;
                                }

                                $final_up_condition_price = round($final_ex_price, 2);
                            } else {
                                // Manual
                                // Formula : Base price and operator
                                $up_details_data = DB::table('upcharges_price_condition_details')
                                    ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
                                    ->where('min_w_h', '<=', $final_width)
                                    ->where('max_w_h', '>=', $final_width)
                                    ->orderBy('upcharges_price_condition_details_id', 'asc')
                                    ->limit(1)
                                    ->first();

                                $manual_price = 0;

                                if ($up_details_data) {
                                    $base_price = $up_details_data->base_price;
                                    $price_details = $up_details_data->price_details;

                                    $manual_price = $base_price;

                                    if ($price_details != '') {
                                        $price_details_arr = unserialize($price_details);

                                        if (is_array($price_details_arr)) {
                                            foreach ($price_details_arr as $key => $value) {
                                                $oper = $value['price_details_operator'];
                                                $manual_price = $this->calculateTotalAmt($manual_price, $oper, $value['price_details_value']);
                                            }
                                        }
                                    }
                                }

                                $final_up_condition_price = round($manual_price, 2);
                            }
                        }
                    } elseif ($rec->condition_type == '3') {
                        // Custom Formula
                        $price = 0;
                        $upcharges_condition_formula = DB::table('upcharges_price_condition_formula')
                            ->where('upcharges_price_condition_id', $rec->upcharges_price_condition_id)
                            ->first();

                        if ($upcharges_condition_formula) {
                            $fr_width = 0;

                            if ($upConditionWidthFraction != 0) {
                                $fr_data = DB::table('width_height_fractions')->where('id', $upConditionWidthFraction)->first();
                                $fr_width = $fr_data->decimal_value;
                            }

                            $final_width = $upConditionWidth + $fr_width;

                            $fr_height = 0;

                            if ($upConditionHeightFraction != 0) {
                                $fr_data = DB::table('width_height_fractions')->where('id', $upConditionHeightFraction)->first();
                                $fr_height = $fr_data->decimal_value;
                            }

                            $final_height = $upConditionHeight + $fr_height;

                            $display_name = @$upcharges_condition_formula->display_name;

                            $order_attr_arr = $this->format_attribute_array();
                            $actual_product_formula = unserialize($upcharges_condition_formula->upcharges_formula);

                            if (count($actual_product_formula) > 0) {
                                $extra_arr = array(
                                    'attribute'         => 'attribute',
                                    'attr_id'           => 'attr_id',
                                    'attribute_level'   => 'attribute_level',
                                    'custom_text'       => 'custom_text'
                                );
                            }


                            $finalFormula = $this->make_attribute_formula($actual_product_formula, $order_attr_arr, $final_width, $final_height, $extra_arr, $productId, $patternId, @$_POST['fabric_price'], $upcharges_condition_formula);

                            $final_val = $this->convertFormulaToValue($finalFormula);
                            $price = $final_val;

                            if (strpos($finalFormula, 'custom_round_even') !== false) {
                                $new_final_formula = str_replace('custom_round_even', 'ceil', $finalFormula);
                                $price = $this->convertFormulaToValue($new_final_formula);
                            }
                        }

                        $final_up_condition_price = $price;
                    } elseif ($rec->condition_type == '4') {
                        // Table Price
                        $fr_width = 0;

                        if ($upConditionWidthFraction != 0) {
                            $fr_data = DB::table('width_height_fractions')->where('id', $upConditionWidthFraction)->first();
                            $fr_width = $fr_data->decimal_value;
                        }

                        $final_width = $upConditionWidth + $fr_width;

                        $fr_height = 0;

                        if ($upConditionHeightFraction != 0) {
                            $fr_data = DB::table('width_height_fractions')->where('id', $upConditionHeightFraction)->first();
                            $fr_height = $fr_data->decimal_value;
                        }

                        $final_height = $upConditionHeight + $fr_height;

                        $table_price = DB::table('price_style')
                            ->where('style_id', @$rec->table_condition_id)
                            ->where('row', $final_width)
                            ->where('col', $final_height)
                            ->first();

                        if (empty($table_price)) {
                            $table_price = DB::table('price_style')
                                ->where('style_id', @$rec->table_condition_id)
                                ->where('row >=', $final_width)
                                ->where('col >=', $final_height)
                                ->orderBy('row_id', 'asc')
                                ->limit(1)
                                ->first();
                        }

                        $final_up_condition_price = ($table_price != NULL ? $table_price->price : 0);
                    }

                    $purpose = @$rec->purpose;
                    $cost_factor = @$rec->cost_factor;
                    $cost_factor_price = 0;

                    if ($final_up_condition_price > 0) {
                        if ($cost_factor == 0) {
                            $cost_factor_price = $final_up_condition_price;
                        } else {
                            $cost_factor_price = (($final_up_condition_price * $costFactorRate));
                        }
                    }

                    $arr = [
                        "upcharge_condition_id" => $rec->upcharges_price_condition_id,
                        "price" => $final_up_condition_price,
                        "cost_factor_price" => $cost_factor_price,
                        "cost_factor_rate" => $costFactorRate,
                        "display_name" => $display_name,
                        "final_formula" => $finalFormula,
                        "related_attr_class" => $related_attr_class,
                        "display_purpose" => intval($purpose),
                    ];

                    $mainArr[] = $arr;
                }
            }

            $response['upcharges'] = $mainArr;
            return response()->json($response);
        } else {
            return response()->json($response);
        }
    }

    
}
