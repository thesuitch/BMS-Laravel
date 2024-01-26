<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash as FacadesHash;
use App\Models\Product;
use App\Models\UserInfo;
use App\Models\WidthHeightFraction;
use App\Models\PatternModel;

trait OrderTrait
{

    /* get order condition setings from company_profile table and user related data from user_info table*/
    function getCompanyProfileOrderConditionSettings()
    {
        if (auth()->user()->is_admin == 1) {

            $data = DB::table('user_info as ui')
                ->join('company_profile as cp', 'cp.user_id', '=', 'ui.id')
                ->select([
                    'ui.*',
                    'cp.display_color_price',
                    'cp.display_upcharges',
                    'cp.website_status',
                    'cp.display_partial_upcharges',
                    'cp.show_upcharge_breakup',
                    'cp.display_list_price',
                    'cp.display_category',
                    'cp.display_qty',
                    'cp.display_product_qty',
                    'cp.display_product_price',
                    'cp.display_room',
                    'cp.display_discount',
                    'cp.display_total',
                    'cp.display_paid',
                    'cp.display_due',
                    'cp.display_attributes',
                    'cp.display_controller',
                    'cp.room_require',
                    'cp.enable_order_qty',
                    'cp.separate_qty_attribute_price',
                    'cp.show_quote_status',
                    'cp.drapery_template',
                    'cp.drapery_template_category_id',
                    'cp.is_hide_room',
                    'cp.is_taxable',
                    'cp.enable_customer_account_type',
                    'cp.enable_shipping_zone',
                    'cp.is_enable_terms_condition',
                    'cp.terms_condition_text',
                    'cp.is_enable_print_download_footer',
                    'cp.print_download_footer_text',
                    'cp.display_total_values',
                    'cp.hide_extra_discount',
                    'cp.hide_upcharge',
                    'cp.show_misc',
                    'cp.default_receive_payment',
                    'cp.custom_receive_payment',
                    'cp.show_wholesaler_cost_factor',
                    'cp.show_room',
                    'cp.product_base_tax',
                    'cp.product_base_shipping',
                    'cp.default_customer_account_type',
                    'cp.display_hardware',
                    'cp.display_fabric_price',
                    'cp.display_upload_window_image',
                    'cp.special_instructions',
                    'cp.order_id_format',
                    'cp.phase_2_ordering',
                    'cp.phase_2_ordering_instruction',
                    'cp.phase_2_display_category',
                    'cp.enable_order_form_qty',
                    'cp.enable_order_form_qty_category',
                    'cp.enable_attribute_image',
                    'cp.enable_fabric_manual_entry',
                    'cp.enable_color_manual_entry',
                    'cp.display_est_delivery_date',
                    'cp.enable_edit_order_stage',
                    'cp.is_enable_order_days_comments',
                    'cp.is_enable_order_days_comments_text',
                    'cp.terms_condition_order_days',
                    'cp.customer_based_shipping',
                    'cp.disable_different_shipping_address',
                    'cp.other_popup_text',
                    'cp.wholesaler_delivery_option',
                    'cp.other_popup_message'
                ])
                ->where('ui.id', auth()->user()->id)
                ->first();
        } else {

            $data = DB::table('user_info as ui')
                ->join('company_profile as cp', 'cp.user_id', '=', 'ui.created_by')
                ->select([
                    'ui.*',
                    'cp.display_color_price',
                    'cp.display_upcharges',
                    'cp.website_status',
                    'cp.display_partial_upcharges',
                    'cp.show_upcharge_breakup',
                    'cp.display_list_price',
                    'cp.display_category',
                    'cp.display_qty',
                    'cp.display_product_qty',
                    'cp.display_product_price',
                    'cp.display_room',
                    'cp.display_discount',
                    'cp.display_total',
                    'cp.display_paid',
                    'cp.display_due',
                    'cp.display_attributes',
                    'cp.display_controller',
                    'cp.room_require',
                    'cp.enable_order_qty',
                    'cp.separate_qty_attribute_price',
                    'cp.show_quote_status',
                    'cp.drapery_template',
                    'cp.drapery_template_category_id',
                    'cp.is_hide_room',
                    'cp.is_taxable',
                    'cp.enable_customer_account_type',
                    'cp.enable_shipping_zone',
                    'cp.is_enable_terms_condition',
                    'cp.terms_condition_text',
                    'cp.is_enable_print_download_footer',
                    'cp.print_download_footer_text',
                    'cp.display_total_values',
                    'cp.hide_extra_discount',
                    'cp.hide_upcharge',
                    'cp.show_misc',
                    'cp.default_receive_payment',
                    'cp.custom_receive_payment',
                    'cp.show_wholesaler_cost_factor',
                    'cp.show_room',
                    'cp.product_base_tax',
                    'cp.product_base_shipping',
                    'cp.default_customer_account_type',
                    'cp.display_hardware',
                    'cp.display_fabric_price',
                    'cp.display_upload_window_image',
                    'cp.special_instructions',
                    'cp.order_id_format',
                    'cp.phase_2_ordering',
                    'cp.phase_2_ordering_instruction',
                    'cp.phase_2_display_category',
                    'cp.enable_order_form_qty',
                    'cp.enable_order_form_qty_category',
                    'cp.enable_attribute_image',
                    'cp.enable_fabric_manual_entry',
                    'cp.enable_color_manual_entry',
                    'cp.display_est_delivery_date',
                    'cp.enable_edit_order_stage',
                    'cp.is_enable_order_days_comments',
                    'cp.is_enable_order_days_comments_text',
                    'cp.terms_condition_order_days',
                    'cp.customer_based_shipping',
                    'cp.disable_different_shipping_address',
                    'cp.other_popup_text',
                    'cp.wholesaler_delivery_option',
                    'cp.other_popup_message'
                ])
                ->where('ui.id', auth()->user()->id)
                ->first();
        }
        return $data;
    }

    // get checkRetailerConnectToWholesaler start
    public function checkRetailerConnectToWholesaler($userId)
    {
        // if retailer not connect with wholesaler then return data
        $data = DB::table('user_info')
            ->where('wholesaler_connection', 0)
            ->where('id', $userId)
            ->first();

        return $data;
    }
    // get checkRetailerConnectToWholesaler end

    // get commonWholesalerToRetailerCommission start
    public function commonWholesalerToRetailerCommission($productId, $customerId = 0)
    {
        if ($customerId != 0) {

            if (auth()->user()->user_type == 'c') {
                // It means retailer
                $userInfo = $this->checkRetailerConnectToWholesaler($this->user_id);

                if (isset($userInfo->id) && $userInfo->id != '') {
                    // If no wholesaler connected, get retailer's custom label
                    $createdBy = auth()->user()->id;
                } else {
                    // If wholesaler connected, get wholesaler's custom label
                    // $createdBy = session()->get('main_b_id');
                    $createdBy = auth()->user()->id;
                }
            } else {

                // It means wholesaler
                if (auth()->user()->is_admin == 1) {
                    $createdBy = auth()->user()->id;
                } else {
                    // It means wholesaler employee
                    $createdBy = auth()->user()->userinfo->created_by;

                    if (empty($createdBy)) {
                        $createdBy = auth()->user()->id;
                    }
                }
            }

            $product = DB::table('b_cost_factor_tbl')
                ->select('dealer_cost_factor', 'individual_cost_factor')
                ->where('product_id', $productId)
                ->where('customer_id', $customerId)
                ->where('created_by', $createdBy)
                ->first();

            $commission = [];

            if (!empty($product)) {
                $commission = ['dealer_price' => $product->dealer_cost_factor, 'individual_price' => $product->individual_cost_factor];
            } else {
                $product = DB::table('product_tbl')
                    ->select('dealer_price', 'individual_price')
                    ->where('product_id', $productId)
                    ->first();

                $commission = ['dealer_price' => $product->dealer_price, 'individual_price' => $product->individual_price];
            }
        } else {
            $commission = ['dealer_price' => 1, 'individual_price' => 0];
        }

        return $commission;
    }
    // get commonWholesalerToRetailerCommission end

    // get contribute Price start
    public function contriPrice($priceType, $optionPrice, $mainPrice, $productId = '0')
    {
        $contributionPrice = 0;

        if ($priceType == 1) {
            // Calculation for priceType 1
            $contributionPrice = !empty($optionPrice) ? $optionPrice : 0;
        } else {
            // Calculation for other priceType
            $costFactorData = $this->commonWholesalerToRetailerCommission($productId);
            $costFactorRate = $costFactorData['dealer_price'];
            $contributionPrice = ($mainPrice * $costFactorRate * $optionPrice) / 100;

            // Alternatively, if you want to use the commented line:
            // $contributionPrice = ($mainPrice * $optionPrice) / 100;
        }

        return $contributionPrice;
    }
    // get contribute Price end

    // get custom label start
    public function getCustomLabelUserwise($createdBy, $categoryId = 0)
    {
        if (auth()->user()->user_type == 'c') {
            $userInfo = $this->checkRetailerConnectToWholesaler($createdBy);
            $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? $createdBy : auth()->user()->main_b_id;
        }

        if ($categoryId != 0) {
            $categoryData = DB::table('categories')
                ->where('id', $categoryId)
                ->orderBy('position', 'asc')
                ->first();

            $createdBy = $categoryData->created_by ?? $createdBy;
        }

        $defaultLabelData = $this->getDefaultLabelData($createdBy);

        if ($categoryId == 0) {
            return $defaultLabelData;
        }

        $categoryLabelData = $this->getCategoryLabelData($categoryId, $createdBy, $defaultLabelData);

        return $categoryLabelData;
    }

    private function getDefaultLabelData($createdBy)
    {
        $defaultLabelData = DB::table('custom_labels')
            ->select('order_category_label', 'order_sub_category_label', 'order_width_label', 'order_height_label', 'order_product_label', 'order_pattern_label', 'order_color_label', 'order_room_label')
            ->where('custom_label_category_id', 0)
            ->where('created_by', $createdBy)
            ->first();

        return $this->fillMissingLabels($defaultLabelData);
    }

    private function getCategoryLabelData($categoryId, $createdBy, $defaultLabelData)
    {
        $data = DB::table('custom_labels')
            ->select('order_category_label', 'order_sub_category_label', 'order_width_label', 'order_height_label', 'order_product_label', 'order_pattern_label', 'order_color_label', 'order_room_label')
            ->where('custom_label_category_id', $categoryId)
            ->where('created_by', $createdBy)
            ->first();

        return $this->fillMissingLabels($data, $defaultLabelData);
    }

    private function fillMissingLabels($data, $defaultLabelData = null)
    {
        $labels = ['order_category_label', 'order_sub_category_label', 'order_width_label', 'order_height_label', 'order_product_label', 'order_pattern_label', 'order_color_label', 'order_room_label'];

        foreach ($labels as $label) {
            if (!isset($data->$label) || $data->$label == '') {
                $data->$label = $defaultLabelData->$label ?? config('constants.DEFAULT_' . strtoupper($label) . '_LABEL');
            }
        }

        return $data;
    }
    // get custom label end




    // get patterns and colors start
    public function getColorPartanModel($product_id = '')
    {
        try {
            $result = [];

            $product = Product::select('colors', 'pattern_models_ids', 'hide_pattern', 'category_id', 'price_style_type')
                ->findOrFail($product_id);

            if (!$product) {
                throw new \Exception();
            }

            if ($product->hide_pattern == 1) {
                return $result;
            }

            $pattern_models = PatternModel::whereIn('pattern_model_id', explode(',', $product->pattern_models_ids))
                ->orderBy("position", "asc")
                ->orderBy('pattern_name', 'asc')
                ->get();


            if (auth()->user()->user_type == 'c') {
                $userInfo = $this->checkRetailerConnectToWholesaler(auth()->user()->id);
                $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? auth()->user()->main_b_id : auth()->user()->level_id;
            } else {
                $createdBy = auth()->user()->level_id;
            }

            $user_detail = $this->getCompanyProfileOrderConditionSettings();

            $category_idd = $product->category_id;
            $custom_label = $this->getCustomLabelUserwise($createdBy, $category_idd);
            $pattern_label =  $custom_label->order_pattern_label;
            // $pattern_label = 'pattern';

            $result = [
                'label' => $pattern_label,
                'options' => [
                    ['value' => '', 'label' => '-- Select one --'],
                    ['value' => '0', 'label' => 'Manual Entry', 'selected' => $user_detail->enable_fabric_manual_entry == 1],
                ],
            ];

            $pattern_models->each(function ($pattern) use (&$result, $product_id) {
                $result['options'][] = [
                    'value' => $pattern->pattern_model_id,
                    'label' => $pattern->pattern_name,
                    'selected' => $pattern->default == '1',
                    'colors' => $this->getColorModel($product_id, $pattern->pattern_model_id)
                ];
            });

            $price_style_type = $product->price_style_type;
            $display_fabric_price = $user_detail->display_fabric_price;

            if (in_array($price_style_type, [5, 6, 10]) && $display_fabric_price) {
                $result[] = [
                    'input_type' => 'text',
                    'input_id' => 'fabric_price',
                    'input_name' => 'fabric_price',
                    'input_value' => '0',
                ];
            }

            return $result;
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
            $product_color_data = DB::table('products')
                ->where('id', $product_id)
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



            if (auth()->user()->user_type == 'c') {
                $userInfo = $this->checkRetailerConnectToWholesaler(auth()->user()->id);
                $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? auth()->user()->main_b_id : auth()->user()->level_id;
            } else {
                $createdBy = auth()->user()->level_id;
            }


            $user_detail = $this->getCompanyProfileOrderConditionSettings();
            $category_idd = $product_color_data->category_id;
            $custom_label = $this->getCustomLabelUserwise($createdBy, $category_idd);
            $color_label =  $custom_label->order_color_label;

            $result[] = [
                'label' => $color_label,
                'onChange' => 'getColorCode(this.value)',

                'options' => [
                    ['value' => '', 'label' => '-- Select one --'],
                    ['value' => '0', 'label' => 'Manual Entry', 'selected' => @$user_detail->enable_color_manual_entry == 1],
                ],
            ];

            foreach ($colors as $color) {
                $result[0]['options'][] = [
                    'value' => $color->id,
                    'label' => $color->color_name,
                    'selected' => $color->default == '1',
                    'color_code' => $color->color_number
                ];
            }

            $result[] = [
                'type' => 'text',
                'onKeyup' => 'getColorCode_select(this.value)',
                'placeholder' => $color_label . ' Code',
            ];



            return $result;
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ' . $e->getMessage(),
                'data' => []
            ], 404);
        }
    }
    // get patterns and colors end


    // get Attributes start
    public function getProductToAttribute($product_id = '')
    {
        $result = [];

        if ($product_id == '') {
            return $result;
        }



        $onKeyup = "checkTextboxUpcharge()";
        $level = 1;

        $attributes = DB::table('product_attribute')
            ->select('product_attribute.*', 'attribute_tbl.attribute_name', 'attribute_tbl.attribute_type')
            ->join('attribute_tbl', 'attribute_tbl.attribute_id', '=', 'product_attribute.attribute_id')
            ->where('product_attribute.product_id', $product_id)
            ->orderBy('attribute_tbl.position', 'ASC')
            ->get();

        // dd($attributes->toSql());

        $p = DB::table('products')->where('id', $product_id)->first();

        $category_id = (!empty($p->category_id) ? $p->category_id : '');

        // Get fraction category wise: START
        $fraction_option = [];

        if ($category_id != '') {
            $hw1 = DB::table('categories')->select('fractions')->where('id', $category_id)->first();
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

                $result[] = $attributeData;
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

                $result[] = $attributeData;
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

                $result[] = $attributeData;
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

                $result[] = $attributeData;
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
            $productData = DB::table('products')
                ->where('id', $options->product_id)
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
                $hw1 = DB::table('categories')->select('fractions')->where('id', $categoryId)->first();
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
    // get Attributes end




    // For strint operator calculate price for upcharges : START
    function calculateTotalAmt($main_price, $operator, $amt)
    {
        // $final_amt = 0;
        $final_amt = $main_price;
        if (!is_numeric($amt)) {
            $amt = 0;
        }

        if ($amt > 0) {
            switch ($operator) {
                case "+":
                    $final_amt = $main_price + $amt;
                    break;

                case "/":
                    $final_amt = $main_price / $amt;
                    break;

                case "%":
                    $final_amt = (($main_price * $amt) / 100);
                    break;

                case "-":
                    $final_amt = $main_price - $amt;
                    break;

                case "*":
                    $final_amt = $main_price * $amt;
                    break;
            }
        }
        return $final_amt;
    }
    // For strint operator calculate price for upcharges : END

    function convertFormulaToValue($formula)
    {
        // For Even Formula condition : START
        $is_even = '';

        if (strpos($formula, 'custom_round_even') !== false) {
            $formula = str_replace('custom_round_even', 'ceil', $formula);
            $is_even = 1;
        }
        // For Even Formula condition : END

        try {
            $result = @eval('return ' . $formula . ';');
        } catch (\Exception $e) {
            $result = 0;
        } catch (\ErrorException $e) {
            $result = 0;
        } catch (\ParseError $e) {
            // Invalid Formula
            $result = 0;
        }

        if (trim($result) == 'INF') {
            // Divide by Zero
            $result = 0;
        }

        // For Even Formula then convert into even number : START
        if ($result && $is_even == 1) {
            if ($result % 2 == 0) {
                // If even no then no changes.
                $result = $result;
            } else {
                // If odd then add 1 to make even
                ++$result;
                $result = $result;
            }
        }
        // For Even Formula then convert into even number : END

        return round($result, 2);
    }





    function getCompanyProfileOrderConditionSettingsPart2($userId = '', $getDataFor = '')
    {
        if (auth()->user()->isAdmin == 1 || $getDataFor == 'Retailer_Employee') {
            $data = UserInfo::select('user_info.*', 'company_profile.display_color_price', 'company_profile.display_upcharges', /* Add all other columns you need */)
                ->join('company_profile', 'company_profile.user_id', '=', 'user_info.id')
                ->where('user_info.id', $userId)
                ->first();

            return $data;
        } else {
            $data = UserInfo::select('user_info.created_by')
                ->where('user_info.id', $userId)
                ->first();

            if ($data->created_by != '') {
                $data = UserInfo::select('user_info.*', 'company_profile.display_color_price', 'company_profile.display_upcharges', /* Add all other columns you need */)
                    ->join('company_profile', 'company_profile.user_id', '=', 'user_info.created_by')
                    ->where('user_info.id', $userId)
                    ->first();
            } else {
                $data = UserInfo::select('user_info.*', 'company_profile.display_color_price', 'company_profile.display_upcharges', /* Add all other columns you need */)
                    ->join('company_profile', 'company_profile.user_id', '=', 'user_info.id')
                    ->where('user_info.id', $userId)
                    ->first();
            }

            return $data;
        }
    }
}
