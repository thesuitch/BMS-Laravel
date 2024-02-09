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
                    $query->select('id', 'category_id', 'product_name', 'default');
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


    // public function get_product_attr_op_op_op($opOpId, $proAttOpOpId, $attributeId, $mainPrice, $selectedOptionTypeOpOp = '', $selectedOptionFifth = '')
    // {
    //     $onKeyup = "checkTextboxUpcharge($(this))";
    //     $level = 3;

    //     $opop = DB::table('attr_options_option_tbl')->where('op_op_id', $opOpId)->first();

    //     $productAttrData = DB::table('product_attr_option_option')
    //         ->select('product_attr_option_option.*')
    //         ->where('op_op_id', $opOpId)
    //         ->join('products', 'products.id', '=', 'product_attr_option_option.product_id')
    //         ->first();

    //     $productData = DB::table('products')->where('id', $productAttrData->product_id)->first();
    //     $categoryId = $productData->category_id;

    //     $fractionOption = '';
    //     if ($categoryId != '') {
    //         $hw1 = DB::table('categories')->select('fractions')->where('id', $categoryId)->first();
    //         $fracs1 = $hw1->fractions;
    //         $fracs = explode(",", $fracs1);
    //         $hw2 = DB::table('width_height_fractions')->select('id', 'fraction_value')->orderBy('decimal_value', 'asc')->get();
    //         foreach ($hw2 as $row) {
    //             if (in_array($row->fraction_value, $fracs)) {
    //                 $fractionOption .= '<option value="' . $row->id . '">' . $row->fraction_value . '</option>';
    //             }
    //         }
    //         unset($hw2);
    //     }

    //     $q = '';
    //     if ($opop->att_op_op_price_type == 1) {
    //         $priceTotal = $mainPrice + @$opop->att_op_op_price;
    //         $contributionPrice = (!empty($opop->att_op_op_price) ? $opop->att_op_op_price : 0);
    //         $q .= '<input type="hidden" value="' . $contributionPrice . '" class="form-control contri_price">';
    //     } else {
    //         if (isset($productAttrData->product_id)) {
    //             $costFactorData = $this->Common_wholesaler_to_retailer_commission($productAttrData->product_id);
    //             $costFactorRate = $costFactorData['dealer_price'];
    //         } else {
    //             $costFactorRate = 1;
    //         }
    //         $priceTotal = ($mainPrice * $costFactorRate * @$opop->att_op_op_price) / 100;
    //         $contributionPrice = (!empty($priceTotal) ? $priceTotal : 0);
    //         $q .= '<input type="hidden" value="' . $contributionPrice . '" class="form-control contri_price">';
    //     }



    //     if ($opop->type == 4) {
    //         $opopop = DB::table('product_attr_option_option_option')
    //             ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
    //             ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
    //             ->where('product_attr_option_option_option.attribute_id', $attributeId)
    //             ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
    //             ->get();

    //         foreach ($opopop as $op_op_op) {
    //             $ctm_class = "op_op_op_text_box_" . $op_op_op->att_op_op_op_id;

    //             $q .= '<input type="hidden" name="op_op_op_id_' . $attributeId . '[]" value="' . $op_op_op->att_op_op_op_id . '_' . $opOpId . '">';

    //             if ($op_op_op->att_op_op_op_type == 2) {
    //                 $opopopops = DB::table('attr_op_op_op_op_tbl')
    //                     ->where('attribute_id', $attributeId)
    //                     ->where('op_op_op_id', $op_op_op->att_op_op_op_id)
    //                     ->orderBy('att_op_op_op_op_position', 'ASC')
    //                     ->get();

    //                 $q .= '<input type="hidden" name="op_op_op_op_value_' . $attributeId . '[]"  class="form-control">';

    //                 $q .= '<div class="row fifth_attr_row">
    //                             <label class="col-sm-2 form-child-label">' . $op_op_op->att_op_op_op_name . '</label>
    //                             <select class="form-control custom-select-css col-sm-6 select2 cls_op_five_' . $attributeId . '" id="op_op_op_op_id_' . $op_op_op->att_op_op_op_id . '" name="op_op_op_op_id_' . $attributeId . '[]" onChange="OptionFive(this.value,' . $attributeId . ')" required>
    //                                 <option value="">--Select one--</option>';

    //                 $selected = '';
    //                 if (!empty($selectedOptionFifth)) {
    //                     $selectedValues = explode('@', $selectedOptionFifth);
    //                 }

    //                 foreach ($opopopops as $kk => $opopopop) {
    //                     if (isset($selectedValues)) {
    //                         $val = $opopopop->att_op_op_op_op_id . '_' . $attributeId . '_' . $op_op_op->att_op_op_op_id;
    //                         $selected = (in_array($val, $selectedValues)) ? 'selected' : '';
    //                     }
    //                     if (!isset($selectedValues)) {
    //                         $selected = ($opopopop->att_op_op_op_op_default == '1') ? 'selected' : '';
    //                     }

    //                     $q .= '<option value="' . $opopopop->att_op_op_op_op_id . '_' . $attributeId . '_' . $op_op_op->att_op_op_op_id . '" ' . $selected . '>' . $opopopop->att_op_op_op_op_name . '</option>';
    //                 }
    //                 // unset($opopopops);

    //                 $q .= '</select>';
    //                 $q .= '<div class="col-sm-6" style="display:none;"></div>';
    //                 $q .= '<div class="col-sm-12"></div>';
    //                 $q .= '</div>';
    //             } elseif ($op_op_op->att_op_op_op_type == 5) {
    //                 $q .= '<br><div class="row">
    //                             <label class="col-sm-2 form-child-label">' . $op_op_op->att_op_op_op_name . '</label>
    //                             <input type="hidden" name="op_op_id_' . $attributeId . '[]" value="' . $op_op_op->att_op_op_op_id . '_' . $attributeId . '_' . $opOpId . '">
    //                             <div class="col-sm-4">
    //                                 <input type="text" value="0" name="op_op_value_' . $attributeId . '[]"  class="form-control convert_text_fraction  op_op_text_box_' . $attributeId . '" data-op_op_key="' . $kk . '" required onkeyup="checkTextboxUpcharge($(this))"   data-level="' . $level . '" data-attr-id="' . $attributeId . '">
    //                             </div>';

    //                 $q .= '<div class="col-sm-2">
    //                             <select class="form-control select_text_fraction key_text_fraction_' . $kk . '" name="fraction_' . $attributeId . '[]" id=""  data-placeholder="-- Select one --"  onchange="checkTextboxUpcharge($(this))"  data-level="' . $level . '" data-attr-id="' . $attributeId . '">
    //                                 <option value="">-- Select one --</option>';

    //                 $q .= $fractionOption;

    //                 $q .= '</select></div>';
    //                 $q .= '<div class="col-sm-6" style="display:none;"></div>';
    //                 $q .= '<div class="col-sm-12"></div>';
    //                 $q .= '</div>';
    //             } elseif ($op_op_op->att_op_op_op_type == 1) {
    //                 $q .= '<br><div class="row">
    //                             <label class="col-sm-2">' . $op_op_op->att_op_op_op_name . '</label>
    //                             <div class="col-sm-3"><input type="text" data-level="' . $level . '"  data-attr-id="' . $op_op_op->att_op_op_op_id . '"  onkeyup="' . $onKeyup . '" name="op_op_op_value_' . $attributeId . '[]"  class="form-control ' . @$ctm_class . '"></div>
    //                             <div class="col-sm-6" style="display:none;"></div>
    //                         </div>';
    //             }

    //             // $q .= $this->contri_price($op_op_op->att_op_op_op_price_type, $op_op_op->att_op_op_op_price, $mainPrice, $productAttrData->product_id);
    //         }
    //         unset($opopop);
    //     } elseif ($opop->type == 3) {
    //         $opopop = DB::table('product_attr_option_option_option')
    //             ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
    //             ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
    //             ->where('product_attr_option_option_option.attribute_id', $attributeId)
    //             ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
    //             ->get();

    //         $class = "";
    //         foreach ($opopop as $op_op_op) {
    //             $ctm_class = "op_op_op_text_box_" . $op_op_op->att_op_op_op_id;

    //             $q .= '<br><div class="row">
    //                         <label class="col-sm-2">' . $op_op_op->att_op_op_op_name . '</label>
    //                         <input type="hidden" name="op_op_op_id_' . $attributeId . '[]" value="' . $op_op_op->att_op_op_op_id . '_' . $opOpId . '">
    //                         <div class="col-sm-3"><input type="text" name="op_op_op_value_' . $attributeId . '[]"  class="form-control ' . $class . ' ' . @$ctm_class . '" data-level="' . $level . '"  data-attr-id="' . $op_op_op->att_op_op_op_id . '"  onkeyup="' . $onKeyup . '"></div>
    //                         <div class="col-sm-6" style="display:none;"></div>
    //                     </div>';

    //             // $q .= $this->contri_price($op_op_op->att_op_op_op_price_type, $op_op_op->att_op_op_op_price, $mainPrice, $productAttrData->product_id);
    //         }
    //         unset($opopop);
    //     } elseif ($opop->type == 2) {
    //         $opopop = DB::table('product_attr_option_option_option')
    //             ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
    //             ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
    //             ->where('product_attr_option_option_option.attribute_id', $attributeId)
    //             ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
    //             ->get();

    //         foreach ($opopop as $op_op_op) {
    //             $ctm_class = "op_op_op_text_box_" . $op_op_op->att_op_op_op_id;

    //             $q .= '<br><div class="row">
    //                         <label class="col-sm-2">' . $op_op_op->att_op_op_op_name . '</label>
    //                         <div class="col-sm-3"><input type="text" name="op_op_op_value_' . $attributeId . '[]"  class="form-control ' . $class . ' ' . @$ctm_class . '" data-level="' . $level . '"  data-attr-id="' . $op_op_op->att_op_op_op_id . '"  onkeyup="' . $onKeyup . '"></div>
    //                         <div class="col-sm-6" style="display:none;"></div>
    //                     </div>';

    //             // $q .= $this->contri_price($op_op_op->att_op_op_op_price_type, $op_op_op->att_op_op_op_price, $mainPrice, $productAttrData->product_id);
    //         }
    //         unset($opopop);
    //     } elseif ($opop->type == 2) {
    //         $opopop = DB::table('product_attr_option_option_option')
    //             ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
    //             ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
    //             ->where('product_attr_option_option_option.attribute_id', $attributeId)
    //             ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
    //             ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
    //             ->get();

    //         foreach ($opopop as $op_op_op) {
    //             $ctm_class = "op_op_op_text_box_" . $op_op_op->att_op_op_op_id;

    //             $q .= '<br><div class="row">
    //                         <label class="col-sm-2">' . $op_op_op->att_op_op_op_name . '</label>
    //                         <div class="col-sm-3"><input type="text" name="op_op_op_value_' . $attributeId . '[]"  class="form-control ' . $class . ' ' . @$ctm_class . '" data-level="' . $level . '"  data-attr-id="' . $op_op_op->att_op_op_op_id . '"  onkeyup="' . $onKeyup . '"></div>
    //                         <div class="col-sm-6" style="display:none;"></div>
    //                     </div>';

    //             // $q .= $this->contri_price($op_op_op->att_op_op_op_price_type, $op_op_op->att_op_op_op_price, $mainPrice, $productAttrData->product_id);
    //         }
    //         unset($opopop);
    //     } elseif ($opop->type == 1) {
    //         $level = 2;
    //         $ctm_class = "op_op_text_box_" . @$opOpId;
    //         $q .= '<br>
    //                 <div class="row">
    //                     <label class="col-sm-2"></label>
    //                     <div class="col-sm-3">
    //                         <input type="hidden" value="' . @$opOpId . '"  name="op_op_id_' . $attributeId . '[]">
    //                         <input type="text" data-level="' . $level . '" data-attr-id="' . @$opOpId . '" onkeyup="' . $onKeyup . '" name="op_op_value_' . $attributeId . '[]" class="form-control ' . @$ctm_class . '">
    //                     </div>
    //                     <div class="col-sm-6" style="display:none;"></div>
    //                 </div>
    //             <br>';
    //     } else {
    //         $q .= '';
    //     }

    //     echo $q;
    // }

    public function get_product_attr_op_op_op($opOpId, $proAttOpOpId, $attributeId, $mainPrice, $selectedOptionTypeOpOp = '', $selectedOptionFifth = '')
    {
        $result = [];

        $onKeyup = "checkTextboxUpcharge($(this))";
        $level = 3;

        $opop = DB::table('attr_options_option_tbl')->where('op_op_id', $opOpId)->first();

        $productAttrData = DB::table('product_attr_option_option')
            ->select('product_attr_option_option.*')
            ->where('op_op_id', $opOpId)
            ->join('products', 'products.id', '=', 'product_attr_option_option.product_id')
            ->first();

        $productData = DB::table('products')->where('id', $productAttrData->product_id)->first();
        $categoryId = $productData->category_id;

        $fractionOption = [];
        if ($categoryId != '') {
            $hw1 = DB::table('categories')->select('fractions')->where('id', $categoryId)->first();
            $fracs1 = $hw1->fractions;
            $fracs = explode(",", $fracs1);
            $hw2 = DB::table('width_height_fractions')->select('id', 'fraction_value')->orderBy('decimal_value', 'asc')->get();
            foreach ($hw2 as $row) {
                if (in_array($row->fraction_value, $fracs)) {
                    $fractionOption[] = ['value' => $row->id, 'label' => $row->fraction_value];
                }
            }
            unset($hw2);
        }

        $q = '';

        if ($opop->att_op_op_price_type == 1) {
            $priceTotal = $mainPrice + @$opop->att_op_op_price;
            $contributionPrice = (!empty($opop->att_op_op_price) ? $opop->att_op_op_price : 0);
            // $result['contributionPrice'] = $contributionPrice;
        } else {
            if (isset($productAttrData->product_id)) {
                $costFactorData = $this->Common_wholesaler_to_retailer_commission($productAttrData->product_id);
                $costFactorRate = $costFactorData['dealer_price'];
            } else {
                $costFactorRate = 1;
            }
            $priceTotal = ($mainPrice * $costFactorRate * @$opop->att_op_op_price) / 100;
            $contributionPrice = (!empty($priceTotal) ? $priceTotal : 0);
            $result['contributionPrice'] = $contributionPrice;
        }

        if ($opop->type == 4) {
            // $options = [];
            $opopop = DB::table('product_attr_option_option_option')
                ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
                ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
                ->where('product_attr_option_option_option.attribute_id', $attributeId)
                ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
                ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
                ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
                ->get();

            // dd($opopop);
            foreach ($opopop as $op_op_op) {

                if ($op_op_op->att_op_op_op_type == 2) {
                    $opopopops = DB::table('attr_op_op_op_op_tbl')
                        ->where('attribute_id', $attributeId)
                        ->where('op_op_op_id', $op_op_op->att_op_op_op_id)
                        ->orderBy('att_op_op_op_op_position', 'ASC')
                        ->get();

                    $result[] = [
                        'id' => $op_op_op->att_op_op_op_id,
                        'type' => 'select',
                        'name' => $op_op_op->att_op_op_op_name,

                    ];

                    foreach ($opopopops as $key => $opopopopsvalue) {
                        $result[0]['option'][] = ['value' => $opopopopsvalue->att_op_op_op_op_id, 'label' => $opopopopsvalue->att_op_op_op_op_name];
                    }
                } elseif ($op_op_op->att_op_op_op_type == 5) {

                    $result[]  = [
                        'label' => $op_op_op->att_op_op_op_name,
                        'id' => $op_op_op->att_op_op_op_id,
                        'type' => 'input_with_select',
                        'input' => [
                            'name' => 'op_op_value_' . $attributeId . '[]',
                            'upcharge' => 'upcharge',
                        ],
                        'select' => $fractionOption
                    ];
                } elseif ($op_op_op->att_op_op_op_type == 1) {
                    $result[] = [
                        'id' => $op_op_op->att_op_op_op_id,
                        'type' => 'text',
                        'name' => $op_op_op->att_op_op_op_name
                    ];
                }
            }
            unset($opopop);
        } elseif ($opop->type == 3) {
            $opopop = DB::table('product_attr_option_option_option')
                ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
                ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
                ->where('product_attr_option_option_option.attribute_id', $attributeId)
                ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
                ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
                ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
                ->get();

            foreach ($opopop as $op_op_op) {
                $currentOption = [];

                $ctm_class = "op_op_op_text_box_" . $op_op_op->att_op_op_op_id;
                $currentOption['op_op_op_id'] = $op_op_op->att_op_op_op_id . '_' . $opOpId;

                // Your common code for type 3 attributes goes here...

                $result['options'][] = $currentOption;
            }
            unset($opopop);
        } elseif ($opop->type == 2) {
            $opopop = DB::table('product_attr_option_option_option')
                ->select('product_attr_option_option_option.id', 'attr_options_option_option_tbl.*', 'product_attr_option_option_option.product_id')
                ->join('attr_options_option_option_tbl', 'attr_options_option_option_tbl.att_op_op_op_id', '=', 'product_attr_option_option_option.op_op_op_id')
                ->where('product_attr_option_option_option.attribute_id', $attributeId)
                ->where('product_attr_option_option_option.pro_att_op_op_id', $proAttOpOpId)
                ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
                ->orderBy('attr_options_option_option_tbl.att_op_op_op_id', 'ASC')
                ->get();

            foreach ($opopop as $op_op_op) {
                $currentOption = [];

                $ctm_class = "op_op_op_text_box_" . $op_op_op->att_op_op_op_id;
                $currentOption['op_op_op_id'] = $op_op_op->att_op_op_op_id . '_' . $opOpId;

                // Your common code for type 2 attributes goes here...

                $result['options'][] = $currentOption;
            }
            unset($opopop);
        } elseif ($opop->type == 1) {
            $level = 2;
            $ctm_class = "op_op_text_box_" . @$opOpId;
            $currentOption = [];
            $currentOption['op_op_id'] = $opOpId;
            $currentOption['op_op_value'] = [];

            $result['options'][] = $currentOption;
        }


        return $result;
    }
}
