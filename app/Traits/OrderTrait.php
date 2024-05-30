<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash as FacadesHash;
use App\Models\Product;
use App\Models\UserInfo;
use App\Models\WidthHeightFraction;
use App\Models\PatternModel;
use ErrorException;
use Exception;
use ParseError;

trait OrderTrait
{


    // get contribute Price start
    public function contriPrice($priceType, $optionPrice, $mainPrice, $productId = '0')
    {
        $contributionPrice = 0;

        if ($priceType == 1) {
            // Calculation for priceType 1
            $contributionPrice = !empty($optionPrice) ? $optionPrice : 0;
        } else {
            // Calculation for other priceType
            $costFactorData = commonWholesalerToRetailerCommission($productId);
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
        if (auth('api')->user()->user_type == 'c') {
            $userInfo = checkRetailerConnectToWholesaler($createdBy);
            $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? $createdBy : auth('api')->user()->main_b_id;
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

        // dd($defaultLabelData);

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
                // $data->$label = $defaultLabelData->$label ?? config('constants.DEFAULT_' . strtoupper($label) . '_LABEL');
                $data->$label  = $defaultLabelData->$label ?? 'ss';
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


            if (auth('api')->user()->user_type == 'c') {
                $userInfo = checkRetailerConnectToWholesaler(auth('api')->user()->id);
                $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? auth('api')->user()->main_b_id : auth('api')->user()->level_id;
            } else {
                $createdBy = auth('api')->user()->level_id;
            }

            $user_detail = getCompanyProfileOrderConditionSettings();

            $category_idd = $product->category_id;
            $custom_label = $this->getCustomLabelUserwise($createdBy, $category_idd);
            $pattern_label =  $custom_label->order_pattern_label;
            // $pattern_label = 'pattern';

            $result[] = [
                'label' => $pattern_label,
                "type" => "select",
                "name" => "pattern",
                'options' => [
                    ['value' => '', 'label' => '-- Select one --'],
                    // ['value' => '0', 'label' => 'Manual Entry', 'selected' => $user_detail->enable_fabric_manual_entry == 1],
                ],
            ];

            $pattern_models->each(function ($pattern) use (&$result, $product_id) {
                $result[0]['options'][] = [
                    'value' => $pattern->pattern_model_id,
                    'label' => $pattern->pattern_name,
                    'selected' => $pattern->default == '1',
                    'subAttributes' => $this->getColorModel($product_id, $pattern->pattern_model_id)
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
            return [
                'status' => 'error',
                'code' => 404,
                'message' => 'Data not found ',
                'data' => []
            ];
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



            if (auth('api')->user()->user_type == 'c') {
                $userInfo = checkRetailerConnectToWholesaler(auth('api')->user()->id);
                $createdBy = isset($userInfo['id']) && $userInfo['id'] != '' ? auth('api')->user()->main_b_id : auth('api')->user()->level_id;
            } else {
                $createdBy = auth('api')->user()->level_id;
            }


            $user_detail = getCompanyProfileOrderConditionSettings();
            $category_idd = $product_color_data->category_id;
            $custom_label = $this->getCustomLabelUserwise($createdBy, $category_idd);
            $color_label =  $custom_label->order_color_label;
            $result[0] = [
                'label' => $color_label,
                'type'  => 'select_with_input',
                'name' => "color"
            ];
            $result[0]['select'] = [
                'onChange' => 'getColorCode',
                'options' => [
                    ['value' => '', 'label' => '-- Select one --'],
                    ['value' => '0', 'label' => 'Manual Entry', 'selected' => @$user_detail->enable_color_manual_entry == 1],
                ],
            ];

            foreach ($colors as $color) {
                $result[0]['select']['options'][] = [
                    'value' => $color->id,
                    'label' => $color->color_name,
                    'selected' => $color->default == '1',
                    'color_code' => $color->color_number
                ];
            }

            $result[0]['input'] = [
                'onKeyup' => 'getColorCode_select',
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

        $height = request()->get('height');
        $width = request()->get('width');
        $height_fraction = request()->get('height_fraction');
        $width_fraction = request()->get('width_fraction');
        $pattern_id = request()->get('pattern_id');

        // dd($width_fraction);
        $main_price =   $this->getProductRowColPrice($height, $width, $product_id, $pattern_id, $width_fraction, $height_fraction);

        // print_r($main_price);
        //  exit;
        $result = [];

        if ($product_id == '') {
            return $result;
        }

        // $result[]['main_pricea'] = $main_price;


        // $onKeyup = "checkTextboxUpcha   rge()";
        $level = 1;

        $attributes = DB::table('product_attribute')
            ->select('product_attribute.*', 'attribute_tbl.attribute_name', 'attribute_tbl.attribute_type')
            ->join('attribute_tbl', 'attribute_tbl.attribute_id', '=', 'product_attribute.attribute_id')
            ->where('product_attribute.product_id', $product_id)
            ->orderBy('attribute_tbl.position', 'ASC')
            ->get();
        //  dd($attributes);
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

        // $main_price = 0;

        $discountData = DB::table('c_cost_factor_tbl')
            ->select("individual_cost_factor", "costfactor_discount")
            ->where('product_id', $product_id)
            ->where('level_id', $level)
            ->first();

        if (!empty($discountData->individual_cost_factor)) {
            $result['individual_cost_factor'] = $discountData->individual_cost_factor;
        }

        if (!empty($p->price_style_type) && $p->price_style_type == 3) {
            // $result['pricestyle'] = $p->price_style_type;
            // $result['main_price'] = $p->fixed_price;
            $main_price = $p->fixed_price;
        } elseif (!empty($p->price_style_type) && $p->price_style_type == 2) {
            // $result['pricestyle'] = $p->price_style_type;
            // $result['sqr_price'] = $p->sqft_price;
            // $result['main_price'] = $p->sqft_price;
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
                    'name' => $attribute->attribute_id,
                    "attributes_type" => $attribute->attribute_type,
                    'type' => 'select',
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
                        'label' => $op->op_op_name,
                        'value' => $op->id . '_' . $op->att_op_id,
                        // 'op_key_value' =>  $op->id . '_' . $op->att_op_id,
                        'option_type' => $op->option_type,
                        'option_id' => $op->att_op_id,
                        'attr_id' => $height . '' . $height_fraction . '' . $width . '' . $width_fraction . '' . $op->id . '_' . $op->att_op_id . '1' . $product_id . '' . $pattern_id,
                        'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op->id . '_' . $op->att_op_id, 1, $product_id, $pattern_id),
                    ];

                    $attributeData['options'][] = $optionData;
                }

                $result[] = $attributeData;
            } elseif ($attribute->attribute_type == 2) {

                $options = DB::table('attr_options')
                    ->select('attr_options.*', 'product_attr_option.id', 'product_attr_option.product_id as product_id')
                    ->join('product_attr_option', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
                    ->where('product_attr_option.pro_attr_id', $attribute->id)
                    ->orderBy('attr_options.position', 'ASC')
                    ->orderBy('attr_options.att_op_id', 'ASC')
                    ->get();

                // dd($attribute->id);
                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'name' => 'op_id_' . $attribute->attribute_id,
                    'type' => 'select',
                    "attributes_type" => $attribute->attribute_type,
                    'options' => [],
                ];

                foreach ($options as $op) {
                    $sl1 = ($op->default == 1 ? 1 : 0);

                    $optionData = [
                        'value' => $op->id . '_' . $op->att_op_id,
                        'label' => $op->option_name,
                        'selected' => $sl1,
                        // 'op_key_value' =>  $op->id . '_' . $op->att_op_id,
                        'option_type' => $op->option_type,
                        'option_id' => $op->att_op_id,
                        'contribute_price' => $this->contributePrice($op->id,  $main_price),
                        'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op->id . '_' . $op->att_op_id, 1, $product_id, $pattern_id),
                        'subAttributes' =>  $this->getProductAttrOptionOption($op->id, $attribute->attribute_id, $main_price, $height, $width, $height_fraction, $width_fraction, $pattern_id)
                    ];

                    $attributeData['options'][] = $optionData;
                }

                $result[] = $attributeData;
            } elseif ($attribute->attribute_type == 5) {

                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'name' => 'op_id_' . $attribute->attribute_id,
                    "attributes_type" => $attribute->attribute_type,
                    'type' => 'input_with_select'
                ];
                $attributeData['input'] = [
                    'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $attribute->id . '_' . $attribute->attribute_id, 1, $product_id, $pattern_id),
                    // 'upcharge' => 'sdf',
                ];

                $attributeData['select'] = [
                    'options' => $fraction_option,
                ];
                $result[] = $attributeData;
            } elseif ($attribute->attribute_type == 1) {
                // $ctm_class = "text_box_" . $attribute->attribute_id;
                // $level = 0;
                // $height = $attribute->attribute_name;

                $attributeData = [
                    'label' => $attribute->attribute_name,
                    'name' => 'op_id_' . $attribute->attribute_id,
                    "attributes_type" => $attribute->attribute_type,
                    'type' => 'input',
                    'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $attribute->id . '_' . $attribute->attribute_id, 1, $product_id, $pattern_id),
                ];

                $result[] = $attributeData;
            }
        }
        unset($attributes);

        return $result;
    }

    public function contributePrice($proAttOpId,  $mainPrice)
    {



        $options = DB::table('product_attr_option')
            ->select('attr_options.*', 'product_attr_option.product_id', 'product_attr_option.id as adddd', 'attribute_tbl.attribute_name as parent_attribute')
            ->join('attr_options', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
            ->join('attribute_tbl', 'attribute_tbl.attribute_id', '=', 'product_attr_option.attribute_id')
            ->where('product_attr_option.id', $proAttOpId)
            ->orderBy('attr_options.position', 'ASC')
            ->orderBy('attr_options.att_op_id', 'ASC')
            ->first();
        $output = [];
        // dd($proAttOpId);

        // price value 
        if (isset($options->price_type)) {
            if ($options->price_type == 1) {

                // dd($mainPrice);

                $price_total = $mainPrice + optional($options)->price;
                $contribution_price = !empty($options->price) ? $options->price : 0;
                $drapery_price = !empty($options->attribute_value) ? $options->attribute_value : 0;
            } else {
                $cost_factor_data = commonWholesalerToRetailerCommission($options->product_id, 5);

                // return $cost_factor_data;
                $cost_factor_rate = $cost_factor_data['dealer_price'];
                $price_total = (($mainPrice * $cost_factor_rate * optional($options)->price) / 100);
                $contribution_price = round(!empty($price_total) ? $price_total : 0, 2);
                $drapery_price = !empty($options->attribute_value) ? $options->attribute_value : 0;

                if ($drapery_price > 0) {
                    $drapery_price = ($drapery_price / 100);
                }
            }
        }

        // return $contribution_price.'--'.$options->option_name;
        // return $data[] = [
        //     'value' => $contribution_price,
        //     'name' => $options->parrent_attribute.' ('.$options->option_name.')'
        // ];

        $output = [
            'value' => $contribution_price,
            'name' => $options->parent_attribute . ' (' . $options->option_name . ')'
        ];

        return $output;
    }
    public function getProductAttrOptionOption($proAttOpId, $attributeId, $mainPrice, $height = 0, $width = 0, $height_fraction = 0, $width_fraction = 0,  $pattern_id, $individualCostFactor = 0, $selectedMultiOption = '')
    {

        $options = DB::table('product_attr_option')
            ->select('attr_options.*', 'product_attr_option.product_id', 'product_attr_option.id as adddd')
            ->join('attr_options', 'attr_options.att_op_id', '=', 'product_attr_option.option_id')
            ->where('product_attr_option.id', $proAttOpId)
            ->orderBy('attr_options.position', 'ASC')
            ->orderBy('attr_options.att_op_id', 'ASC')
            ->first();
        // dd($options->toSql());
        // dd( $options);
        // print_r($options);
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
        if (isset($options->price_type)) {
            if ($options->price_type == 1) {

                $price_total = $mainPrice + optional($options)->price;
                $contribution_price = !empty($options->price) ? $options->price : 0;
                $drapery_price = !empty($options->attribute_value) ? $options->attribute_value : 0;
            } else {
                $cost_factor_data = commonWholesalerToRetailerCommission($options->product_id, 5);

                // return $cost_factor_data;
                $cost_factor_rate = $cost_factor_data['dealer_price'];
                $price_total = round((($mainPrice * $cost_factor_rate * optional($options)->price) / 100), 2);
                $contribution_price = !empty($price_total) ? $price_total : 0;
                $drapery_price = !empty($options->attribute_value) ? $options->attribute_value : 0;

                if ($drapery_price > 0) {
                    $drapery_price = ($drapery_price / 100);
                }
            }
        }

        // $optionsArray[] = ['contiprice' => $contribution_price];
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
                    'name' => 'op_op_id_' . $op_op->op_op_id,
                    // 'id' => $op_op->id,
                    // 'att_op_id' => $options->att_op_id,
                    // 'contiprice' => $contribution_price,
                    'type' => 'input_with_select',
                    'op_op_key_value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $op_op->option_id

                ];

                if ($op_op->op_op_name == "Divider Rail  #1" || $op_op->op_op_name == "Divider Rail  #2") {
                    $optionArray['input'] = [
                        // 'name' => 'op_op_value_' . $attributeId . '[]',
                        // 'id' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                        'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op_op->op_op_id, auth('api')->user()->user_id, $options->product_id, $pattern_id),
                    ];
                } else {
                    $optionArray['input'] = [

                        // 'name' => 'op_op_value_' . $attributeId . '[]',
                        'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op_op->op_op_id, auth('api')->user()->user_id, $options->product_id, $pattern_id),

                    ];
                }

                $optionArray['select'] = [
                    // 'name' => 'fraction_' . $attributeId . '[]',
                    // 'upcharge' => 'checkTextboxUpcharge()',
                    'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op_op->op_op_id, auth('api')->user()->user_id, $options->product_id, $pattern_id),
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
                ];
                $optionsArray[] = $optionArray;
                // $optionsArray[count($optionsArray) - 1]['contri_price'] = $this->contriPrice($op_op->att_op_op_price_type, $op_op->att_op_op_price, $mainPrice, $op_op->product_id);
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


            foreach ($opops as $op_op) {
                $opopops = DB::table('attr_options_option_option_tbl')
                    ->where('attribute_id', $attributeId)
                    ->where('att_op_op_id', $op_op->op_op_id)
                    ->get();

                $optionsArray[] = [
                    'label' => $op_op->op_op_name,
                    'name' => 'op_op_id_' . $op_op->op_op_id,
                    // 'op_op_id_' . $attributeId => $op_op->op_op_id . '_' . $op_op->id . '_' . $op_op->option_id,
                    'op_op_op_id'  => $op_op->op_op_id . '_' . $op_op->id . '_' . $op_op->option_id,
                    // 'att_op_id' => $options->att_op_id,
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

                        $multioptionpricevalues = $this->multioption_price_value($opopop->att_op_op_op_id, $attributeId, $mainPrice);

                        $selectOptions[] = [
                            'value' => $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id,
                            'label' => $opopop->att_op_op_op_name,
                            'op_op_key_value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $op_op->option_id,
                            'subAttributes' => $this->AttrOptionOptionOption($opopop->att_op_op_op_id, $attributeId, $mainPrice),
                            'price_value' => $this->multioption_price_value($opopop->att_op_op_op_id, $attributeId, $mainPrice),

                        ];

                        // foreach ($multioptionpricevalues[0] as $keyss => $multioptionpricevalue) {

                        //     if(isset($multioptionpricevalue) ){
                        //     $selectOptions[count($selectOptions) - 1]['subAttributes'] = $multioptionpricevalue;
                        //     }

                        // }
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

                        $optionsArray[count($optionsArray) - 1]['cords_length_val'] = $cordVal;
                        $optionsArray[count($optionsArray) - 1]['change_height'] = $heightVal;
                        $optionsArray[count($optionsArray) - 1]['change_width'] = $widthVal;
                    }

                    $optionsArray[count($optionsArray) - 1]['text_input'] = [
                        'label' => 'Text Input',
                        'name' => 'op_op_value_' . $attributeId . '[]'
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
                    $multiselectOptions = [];
                    foreach ($opopops as $keyss => $opopop) {
                        $selected = ($opopop->att_op_op_op_default == '1') ? true : false;
                        $multiselectOptions[] = [
                            'value' => $opopop->att_op_op_op_id . '_' . $attributeId . '_' . $op_op->op_op_id,
                            'label' => $opopop->att_op_op_op_name,
                            'op_op_key_value' => $opopop->att_op_op_id . '_' . $op_op->id . '_' . $opopop->op_id,
                            'subAttributes' => $this->AttrOptionOptionOption($opopop->att_op_op_op_id, $attributeId, $mainPrice),
                            'price_value' => $this->multioption_price_value($opopop->att_op_op_op_id, $attributeId, $mainPrice),

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
                // 'id' => 'op_' . $options->att_op_id,
                'name' => 'op_op_id_' . $attributeId,
                'type' => 'select',


                // 'label' => $options->op_op_name,
                // 'value' => $options->id . '_' . $options->att_op_id,
                // 'option_key_value' =>  $options->id . '_' . $options->att_op_id,
                // 'option_type' => $options->option_type,
                // 'option_id' => $options->att_op_id,

                'options' => [
                    [
                        'value' => '',
                        'label' => '--Select one--',
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

                $optionTypeArray['options'][] = [
                    'value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    'label' => $op_op->op_op_name,
                    'op_op_key_value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    'subAttributes' => $this->get_product_attr_op_op_op($op_op->op_op_id, $op_op->id, $attributeId, $mainPrice, $height, $width, $height_fraction, $width_fraction, $pattern_id),
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

                $optionArray = [

                    'label' =>  $op_op->op_op_name,
                    'type' => 'input',
                    'name' => 'op_op_id_' . $op_op->op_op_id,
                    'op_op_key_value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $op_op->option_id,
                    'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op_op->op_op_id, auth('api')->user()->user_id, $options->product_id, $pattern_id),

                    // 'class' => 'form-control cls_text_op_op_value ' . $ctm_class,
                    // 'required' => true,
                    // 'data-attr-name' => $op_op->op_op_name,
                    // ],

                    // 'input_hidden' => [
                    //     'type' => 'hidden',
                    //     'name' => 'op_op_id_' . $attributeId . '[]',
                    //     'value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    // ],

                    // 'contri_price' => $this->contriPrice($op_op->att_op_op_price_type, $op_op->att_op_op_price, $mainPrice, $op_op->product_id),

                    // 'drapery_price' => [
                    //     'input_hidden' => [
                    //         'type' => 'hidden',
                    //         'value' => (!empty($op_op->att_op_op_attr_value) ? $op_op->att_op_op_attr_value : 0),
                    //         // 'class' => 'drapery_attr_price_value',
                    //     ],
                    //     'input_hidden_form_control' => [
                    //         'type' => 'hidden',
                    //         'value' => (!empty($op_op->att_op_op_attr_value) ? $op_op->att_op_op_attr_value : 0),
                    //         // 'class' => 'form-control drapery_attribute_price_value contri_price',
                    //     ],
                    // ],


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


                // 'select' => [
                'id' => 'op_' . $options->att_op_id,
                'name' => 'op_op_id_' . $attributeId,
                // 'onChange' => 'MultiOptionOptionsOption(this,' . $attributeId . ')',
                // 'data-placeholder' => '-- Select pattern/model --',
                // 'multiple' => true,
                // 'elements' => [],
                // ],


            ];

            foreach ($opops as $op_op) {
                $selected = ($op_op->att_op_op_default == '1') ? 'selected' : '';
                $optionArray['options'][] = [
                    'value' => $op_op->op_op_id . '_' . $op_op->id . '_' . $options->att_op_id,
                    'selected' => $selected,
                    'label' => $op_op->op_op_name,
                ];
            }

            $optionsArray[] = $optionArray;
        } elseif ($options->option_type == 1) {

            $optionArray = [
                'type' => 'input',
                'name' => 'op_op_id_' . $options->att_op_id,
                'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $options->att_op_id, auth('api')->user()->user_id, $options->product_id, $pattern_id),
            ];


            $optionsArray[] = $optionArray;
        }

        return $optionsArray;
    }

    public function multioption_price_value($op_op_op_id, $attribute_id, $main_price, $selected_option_fifth = '')
    {
        $opopopop = DB::table('attr_options_option_option_tbl')
            ->select('attr_options_option_option_tbl.*', 'attr_options_option_tbl.op_op_name as parent_attribute')
            ->join('attr_options_option_tbl', 'attr_options_option_option_tbl.att_op_op_id', 'attr_options_option_tbl.op_op_id')
            ->where('attr_options_option_option_tbl.attribute_id', $attribute_id)
            ->where('attr_options_option_option_tbl.att_op_op_op_id', $op_op_op_id)
            ->orderBy('attr_options_option_option_tbl.att_op_op_op_position', 'ASC')
            ->first();
        $output = [];

        // dd($opopopop);

        if ($opopopop->att_op_op_op_price_type == 1) {
            $price_total = $main_price + optional($opopopop)->att_op_op_op_price;
            $contribution_price = !empty($opopopop->att_op_op_op_price) ? $opopopop->att_op_op_op_price : 0;
            $output =
                [
                    'value' => $contribution_price,
                    'name' => $opopopop->parent_attribute . ' (' . $opopopop->att_op_op_op_name . ')',
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

            $output = [
                'type' => 'hidden',
                'value' => $contribution_price,
                'name' => 'contri_price'
            ];
        }

        return $output;
    }
    public function AttrOptionOptionOption($op_op_op_id, $attribute_id, $main_price, $selected_option_fifth = '')
    {
        $opopopop = DB::table('attr_options_option_option_tbl')
            ->select('*')
            ->where('attribute_id', $attribute_id)
            ->where('att_op_op_op_id', $op_op_op_id)
            ->orderBy('att_op_op_op_position', 'ASC')
            ->first();
        $output = [];

        if ($opopopop->att_op_op_op_type == 2) {

            $opopopop = DB::table('attr_op_op_op_op_tbl')
                ->select('*')
                ->where('attribute_id', $attribute_id)
                ->where('op_op_op_id', $op_op_op_id)
                ->orderBy('att_op_op_op_op_position', 'ASC')
                ->orderBy('att_op_op_op_op_name', 'ASC')
                ->orderBy('op_op_op_id', 'ASC')
                ->get();

            $output[] = [
                'type' => 'select',
                'name' => "op_op_op_op_id_" . $attribute_id,
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

                $output[0]['options'][] = [
                    'value' => $op_op_op_op->att_op_op_op_op_id . '_' . $op_op_op_id . '" ' . $selected,
                    'label' => $op_op_op_op->att_op_op_op_op_name,
                ];
            }
        } elseif ($opopopop->att_op_op_op_type == 1) {
            // $onKeyup = "checkTextboxUpcharge()";
            $level = 3;

            $output[] =
                [
                    'type' => 'input',
                    'value' => $op_op_op_id,
                    'name' => "op_op_value_' . $attribute_id . '[]",
                    // 'onkeyup' => $onKeyup
                ];
        } else {

            // $output[] =
            //     [
            //         'type' => 'hidden',
            //         'value' => $opopopop->att_op_op_op_name,
            //         'name' => "op_op_value_" . $attribute_id . "[]",

            //     ];
        }
        return $output;
    }



    // public function getProductAttrOpFive($attOpOpOpOpId, $attributeId, $mainPrice)
    // {
    //     $opopopop = DB::table('attr_op_op_op_op_tbl')
    //         ->select('*')
    //         ->where('attribute_id', $attributeId)
    //         ->where('att_op_op_op_op_id', $attOpOpOpOpId)
    //         ->orderBy('att_op_op_op_op_position', 'ASC')
    //         ->first();

    //     $q = '';

    //     if ($opopopop->att_op_op_op_op_price_type == 1) {

    //         $priceTotal = $mainPrice + $opopopop->att_op_op_op_op_price;
    //         $contributionPrice = !empty($opopopop->att_op_op_op_op_price) ? $opopopop->att_op_op_op_op_price : 0;
    //         $q .= '<input type="hidden" value="' . $contributionPrice . '" class="form-control contri_price">';

    //         // For Drapery price static condition : START
    //         $draperyPrice = !empty($opopopop->att_op_op_op_op_attr_value) ? $opopopop->att_op_op_op_op_attr_value : 0;
    //         $q .= '<input type="hidden" value="' . $draperyPrice . '" class="drapery_attr_price_value">';
    //         $q .= '<input type="hidden" value="' . $draperyPrice . '" class="form-control drapery_attribute_price_value contri_price">';
    //         // For Drapery price static condition : END
    //     } else {

    //         // For Cost Factor : START
    //         $productAttrData =  DB::table('product_attr_option_option_option')
    //             ->where('op_op_op_id', $opopopop->op_op_op_id)
    //             ->first();

    //         if ($productAttrData && isset($productAttrData->product_id)) {
    //             $costFactorData = commonWholesalerToRetailerCommission($productAttrData->product_id);
    //             $costFactorRate = $costFactorData['dealer_price'];
    //         } else {
    //             $costFactorRate = 1;
    //         }
    //         $priceTotal = ($mainPrice * $costFactorRate * $opopopop->att_op_op_op_op_price) / 100;
    //         // For Cost Factor : END 

    //         $contributionPrice = !empty($priceTotal) ? $priceTotal : 0;
    //         $q .= '<input type="hidden" value="' . $contributionPrice . '" class="form-control contri_price">';

    //         // For Drapery price static condition : START
    //         $draperyPrice = !empty($opopopop->att_op_op_op_op_attr_value) ? $opopopop->att_op_op_op_op_attr_value : 0;
    //         if ($draperyPrice > 0) {
    //             $draperyPrice = ($draperyPrice / 100);
    //         }
    //         $q .= '<input type="hidden" value="' . $draperyPrice . '" class="drapery_attr_price_value">';
    //         $q .= '<input type="hidden" value="' . $draperyPrice . '" class="form-control drapery_attribute_price_value contri_price">';
    //         // For Drapery price static condition : END
    //     }

    //     if ($opopopop->att_op_op_op_op_type == 1) {
    //         $onKeyup = "checkTextboxUpcharge($(this))";
    //         $level = 4;
    //         $ctmClass = "op_op_op_op_text_box_" . $opopopop->op_op_op_id;
    //         $q .= '<br><input type="text" name="op_op_op_op_value_' . $attributeId . '" data-level="' . $level . '" data-attr-id="' . $opopopop->op_op_op_id . '" onkeyup="' . $onKeyup . '" class="form-control ' . $ctmClass . '">';
    //     } else {
    //         $q .= '';
    //     }

    //     return $q;
    // }




    public function get_product_attr_op_op_op($opOpId, $proAttOpOpId, $attributeId, $mainPrice, $height, $width, $height_fraction, $width_fraction, $selectedOptionTypeOpOp = '', $selectedOptionFifth = '')
    {
        $result = [];

        // $onKeyup = "checkTextboxUpcharge($(this))";
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
            foreach ($opopop as $op_op_op_key =>  $op_op_op) {

                if ($op_op_op->att_op_op_op_type == 2) {
                    $opopopops = DB::table('attr_op_op_op_op_tbl')
                        ->where('attribute_id', $attributeId)
                        ->where('op_op_op_id', $op_op_op->att_op_op_op_id)
                        ->orderBy('att_op_op_op_op_position', 'ASC')
                        ->get();
                    //    dd($opopopops->toSql());

                    $result[] = [
                        'name' => 'op_op_op_id_' . $op_op_op->att_op_op_op_id,
                        'op_op_op_op_id' =>  true,
                        'op_op_op_key_value' => $op_op_op->att_op_op_op_id . '_' . $op_op_op->att_op_op_id,
                        // 'op_op_op_op_id_' . $op_op_op->attribute_id => $op_op_op->att_op_op_op_id . '_' . $op_op_op->att_op_op_id,
                        // 'op_op_id_' . $op_op_op->attribute_id => $op_op_op->att_op_op_op_id . '_' . $op_op_op->attribute_id . '_' . $op_op_op->att_op_op_id,
                        'type' => 'select',
                        'label' => $op_op_op->att_op_op_op_name,

                    ];

                    foreach ($opopopops as $key => $opopopopsvalue) {
                        $result[$op_op_op_key]['options'][] = ['value' => $opopopopsvalue->att_op_op_op_op_id . '_' . $opopopopsvalue->attribute_id . '_' . $op_op_op->att_op_op_op_id, 'label' => $opopopopsvalue->att_op_op_op_op_name];
                    }
                } elseif ($op_op_op->att_op_op_op_type == 5) {

                    $result[]  = [
                        'label' => $op_op_op->att_op_op_op_name,
                        'name' => 'op_op_op_id_' . $op_op_op->att_op_op_op_id,
                        // 'op_op_op_id_' . $op_op_op->attribute_id => $op_op_op->att_op_op_op_id . '_' . $op_op_op->att_op_op_id,
                        'op_op_id' => $op_op_op->att_op_op_op_id . '_' . $op_op_op->attribute_id . '_' . $op_op_op->att_op_op_id,
                        'op_op_op_key_value' => $op_op_op->att_op_op_op_id . '_' . $op_op_op->att_op_op_id,
                        'type' => 'input_with_select',
                        'input' => [
                            // 'upcharge' => $this->calculateUpCondition($height, $height_fraction, $width, $width_fraction, $op_op_op->id . '_' . $op_op_op->att_op_id, 1, $productAttrData->product_id, $pattern_id),

                            'upcharge' => 'checkTextboxUpcharge',
                        ],
                        'select' => [
                            'upcharge' => 'checkTextboxUpcharge()',
                            'options' =>  $fractionOption,
                        ]

                    ];
                } elseif ($op_op_op->att_op_op_op_type == 1) {
                    $result[] = [
                        'att_op_op_op_id' => $op_op_op->att_op_op_op_id,
                        'type' => 'input',
                        'label' => $op_op_op->att_op_op_op_name
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
                $result[] = [
                    'att_op_op_op_id' => $op_op_op->att_op_op_op_id,
                    'type' => 'input',
                    'label' => $op_op_op->att_op_op_op_name
                ];
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
                $result[] = [
                    'att_op_op_op_id' => $op_op_op->att_op_op_op_id,
                    'type' => 'input',
                    'label' => $op_op_op->att_op_op_op_name
                ];
            }

            unset($opopop);
        } elseif ($opop->type == 1) {

            $result[] = [
                'opOpId' => $opOpId,
                'type' => 'input',
                // 'level' => $level
            ];
        }

        return $result;
    }

    // get Attributes end




    public function getProductRowColPrice($height = null, $width = null, $product_id = null, $pattern_id = null, $width_fraction = null, $height_fraction = 0, $product_type = 0)
    {

        // dd($width_fraction);

        // return 1;
        if ($height == 0) {
            $height = "-1";
        }
        if ($width == 0) {
            $width = "-1";
        }

        $fr_width = 0;
        $fr_height = 0;

        if ($width_fraction != 0) {
            $fr_data_width = DB::table('width_height_fractions')->where('id', $width_fraction)->first();
            $fr_width = $fr_data_width->decimal_value;
        }

        if ($height_fraction != 0) {
            $fr_data_height = DB::table('width_height_fractions')->where('id', $height_fraction)->first();
            $fr_height = $fr_data_height->decimal_value;
        }


        $height = $height + $fr_height;
        $width = $width + $fr_width;

        // dd($height);

        $q = "";
        $st = "";
        $row = "";
        $col = "";
        $price = "";
        $s_area = 0;

        if ($height >= 0 && $width >= 0) {
            if (!empty(request()->post('comboProductIds'))) {
                $comboPrice = [];

                foreach (request()->post('comboProductIds') as $combo_key => $combo_product_id) {
                    $p = DB::table('products')->where('id', $combo_product_id)->first();

                    if (!empty($p->price_style_type) && $p->price_style_type == 1) {
                        $price = DB::table('price_style')
                            ->where('style_id', $p->price_rowcol_style_id)
                            ->where('row', $width)
                            ->where('col', $height)
                            ->first();

                        $pc = ($price != NULL ? $price->price : 0);

                        if (!empty($price)) {
                            array_push($comboPrice, str_replace(",", "", $pc));
                            $st = 1;

                            $row = isset($price->row) ? $price->row : 0;
                            $col = isset($price->col) ? $price->col : 0;
                            $price = $pc;
                        } else {
                            $price = DB::table('price_style')
                                ->where('style_id', $p->price_rowcol_style_id)
                                ->where('row', '>=', $width)
                                ->where('col', '>=', $height)
                                ->orderBy('row_id', 'asc')
                                ->limit(1)
                                ->first();

                            $pc = ($price != NULL ? $price->price : 0);
                            array_push($comboPrice, str_replace(",", "", $pc));

                            $row = isset($price->row) ? $price->row : 0;
                            $col = isset($price->col) ? $price->col : 0;
                            $price = $pc;
                            $st = 2;
                        }
                    } elseif (!empty($p->price_style_type) && $p->price_style_type == 2) {
                        $price = $p->sqft_price;
                        array_push($comboPrice, str_replace(",", "", $price));
                    } elseif (!empty($p->price_style_type) && $p->price_style_type == 3) {
                        $price = $p->fixed_price;
                    } elseif (!empty($p->price_style_type) && $p->price_style_type == 4) {
                        $pg = DB::table('price_model_mapping_tbl')
                            ->select('*')
                            ->where('product_id', $combo_product_id)
                            ->where('pattern_id', $this->input->post('comboPatternIds')[$combo_key])
                            ->first();

                        $group_id = optional($pg)->group_id ?? '0';

                        $price = DB::table('price_style')
                            ->where('style_id', $group_id)
                            ->where('row', '>=', $width)
                            ->where('col', '>=', $height)
                            ->orderBy('row_id', 'asc')
                            ->limit(1)
                            ->first();

                        $pc = optional($price)->price ?? 0;

                        array_push($comboPrice, str_replace(",", "", $pc));

                        $row = optional($price)->row ?? '';
                        $col = optional($price)->col ?? '';

                        $price = $pc;
                        $st = 2;
                    } elseif (!empty($p->price_style_type) && $p->price_style_type == 5) {
                        $pg = DB::table('sqm_price_model_mapping_tbl')
                            ->select('*')
                            ->where('product_id', $combo_product_id)
                            ->where('pattern_id', $this->input->post('comboPatternIds')[$combo_key])
                            ->first();

                        $pc = optional($pg)->price ?? 0;

                        $total_area = $height * $width;
                        $sqm_area = $total_area / 10000;

                        if ($sqm_area < 2) {
                            $sqm_area = 2;
                        }

                        $sqm = $sqm_area * $pc;
                        $price = $sqm;

                        array_push($comboPrice, str_replace(",", "", $price));

                        $st = 2;
                    } elseif (!empty($p->price_style_type) && $p->price_style_type == 7) {
                        $widthHeightPrice = $width + $height;
                        array_push($comboPrice, str_replace(",", "", $widthHeightPrice));

                        $st = 2;
                    } elseif (!empty($p->price_style_type) && $p->price_style_type == 6) {
                        $pg = DB::table('sqft_price_model_mapping_tbl')
                            ->select('*')
                            ->where('product_id', $combo_product_id)
                            ->where('pattern_id', $this->input->post('comboPatternIds')[$combo_key])
                            ->first();

                        $pc = isset($pg->price) ? ($pg->price != NULL ? $pg->price : 0) : 0;

                        $total_area = $height * $width;
                        $sqft_area = $total_area / 144;
                        $sqft = ($sqft_area) * ($pc);
                        $price = $sqft;
                        array_push($comboPrice, str_replace(",", "", $price));

                        $st = 2;
                    } else if (!empty($p->price_style_type) && $p->price_style_type == 9) {
                        // For Sqft + Table Price
                        $price = DB::table('price_style')
                            ->where('style_id', $p->price_rowcol_style_id)
                            ->where('row', $width)
                            ->where('col', $height)
                            ->first();

                        $pc = ($price != NULL ? $price->price : 0);

                        if (!empty($price)) {
                            // It means exact height and width match
                            $st = 1;
                        } else {
                            // It means need to consider the next greater value from price style
                            $price = DB::table('price_style')
                                ->where('style_id', $p->price_rowcol_style_id)
                                ->where('row', '>=', $width)
                                ->where('col', '>=', $height)
                                ->orderBy('row_id', 'asc')
                                ->limit(1)
                                ->first();
                            $pc = ($price != NULL ? $price->price : 0);
                            $st = 2;
                        }

                        // Calculate with sqft + table price : START
                        $sqft_price = 1;
                        if ($p->product_id != '' && $pattern_id != '') {
                            $sqft_data = DB::table('sqft_price_model_mapping_tbl')
                                ->where('product_id', $p->product_id)
                                ->where('pattern_id', $pattern_id)
                                ->first();
                            $sqft_price = isset($sqft_data->price) ? $sqft_data->price : 1;
                        }
                        $pc = round(($pc * $sqft_price), 2);
                        // Calculate with sqft + table price : END



                        $row = isset($price->row) ? $price->row : 0;
                        $col = isset($price->col) ? $price->col : 0;
                        $price = $pc;
                    }

                    // ... (continue with the remaining cases)
                }

                $price = array_sum($comboPrice);



                return $price;
                // $arr = ['st' => $st, 'row' => $row, 'col' => $col, 'price' => $price];
                // return response()->json($arr);
            } else {


                $p = DB::table('products')->where('id', $product_id)->first();
                // dd($p->price_style_type);
                // exit;


                if (!empty($p->price_style_type) && $p->price_style_type == 1) {

                    $price = DB::table('price_style')
                        ->where('style_id', $p->price_rowcol_style_id)
                        ->where('row', $width)
                        ->where('col', $height)
                        ->first();

                    $pc = ($price != NULL ? $price->price : 0);

                    if (!empty($price)) {


                        $st = 1;

                        $row = isset($price->row) ? $price->row : 0;
                        $col = isset($price->col) ? $price->col : 0;
                        $price = $pc;
                    } else {
                        $price = DB::table('price_style')
                            ->where('style_id', $p->price_rowcol_style_id)
                            ->where('row', '>=', $width)
                            ->where('col', '>=', $height)
                            ->orderBy('row_id', 'asc')
                            ->limit(1)
                            ->first();
                        // dd($price);
                        $pc = ($price != NULL ? $price->price : 0);

                        $row = isset($price->row) ? $price->row : 0;
                        $col = isset($price->col) ? $price->col : 0;
                        $price = $pc;
                        $st = 2;
                    }
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 2) {
                    $price = $p->sqft_price;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 3) {
                    $price = $p->fixed_price;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 4) {
                    // group price
                    $pg = DB::table('price_model_mapping_tbl')
                        ->where('product_id', $product_id)
                        ->where('pattern_id', $pattern_id)
                        ->first();

                    $group_id = isset($pg->group_id) ? $pg->group_id : '0';
                    $price = DB::table('price_style')
                        ->where('style_id', $group_id)
                        ->where('row', '>=', $width)
                        ->where('col', '>=', $height)
                        ->orderBy('row_id', 'asc')
                        ->limit(1)
                        ->first();

                    $pc = ($price != NULL ? $price->price : 0);

                    $row = !empty($price->row) ? $price->row : '';
                    $col = !empty($price->col) ? $price->col : '';

                    $price = $pc;
                    $st = 2;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 5) {
                    $pg = DB::table('sqm_price_model_mapping_tbl')
                        ->where('product_id', $product_id)
                        ->where('pattern_id', $pattern_id)
                        ->first();
                    $pc = ((isset($pg->price) && $pg->price != NULL) ? $pg->price : 0);

                    $total_area = $height * $width;
                    $sqm_area = $total_area / 10000;
                    if ($sqm_area < 2) {
                        $sqm_area = 2;
                    }
                    $sqm = ($sqm_area) * ($pc);
                    $price = $sqm;

                    $st = 2;
                    $s_area = $sqm_area;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 7) {
                    $widthHeightPrice = $width + $height;
                    $st = 2;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 6) {
                    $pg = DB::table('sqft_price_model_mapping_tbl')
                        ->where('product_id', $product_id)
                        ->where('pattern_id', $pattern_id)
                        ->first();
                    $pc = isset($pg->price) ? ($pg->price != NULL ? $pg->price : 0) : 0;

                    $total_area = $height * $width;
                    $sqft_area = $total_area / 144;
                    // if($sqft_area < 2):
                    //     $sqft_area = 2;
                    // endif;
                    $sqft = ($sqft_area) * ($pc);
                    $price = $sqft;

                    $st = 2;
                    $s_area = $sqft_area;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 9) {
                    // For Sqft + Table Price
                    $price = DB::table('price_style')
                        ->where('style_id', $p->price_rowcol_style_id)
                        ->where('row', $width)
                        ->where('col', $height)
                        ->first();

                    $pc = ($price != NULL ? $price->price : 0);

                    if (!empty($price)) {
                        // It means exact height and width match
                        $st = 1;
                    } else {
                        // It means need to consider next greater value from the price style
                        $price = DB::table('price_style')
                            ->where('style_id', $p->price_rowcol_style_id)
                            ->where('row', '>=', $width)
                            ->where('col', '>=', $height)
                            ->orderBy('row_id', 'asc')
                            ->limit(1)
                            ->first();
                        $pc = ($price != NULL ? $price->price : 0);
                        $st = 2;
                    }

                    // Calculate with sqft + table price : START
                    $sqft_price = 1;
                    if ($p->product_id != '' && $pattern_id != '') {
                        $sqft_data = DB::table('sqft_price_model_mapping_tbl')
                            ->where('product_id', $p->product_id)
                            ->where('pattern_id', $pattern_id)
                            ->first();
                        $sqft_price = isset($sqft_data->price) ? $sqft_data->price : 1;
                    }
                    $pc = round(($pc * $sqft_price), 2);
                    // Calculate with sqft + table price : END




                    $row = isset($price->row) ? $price->row : 0;
                    $col = isset($price->col) ? $price->col : 0;
                    $price = $pc;
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 10) {
                    // For Get Formula price based on product id : START
                    $where = "FIND_IN_SET('" . $product_id . "', product_ids)";
                    $product_formula_data = DB::table('fabric_price_formula')->whereRaw($where)->first();

                    if (isset($product_formula_data['fabric_price_formula_id'])) {
                        // For Call Format attribute post array : START
                        $order_attr_arr = $this->format_attribute_array();
                        // For Call Format attribute post array : END

                        // Get Formula which is set while creating the formula : START
                        $actual_product_formula = unserialize($product_formula_data['formula']);
                        // Get Formula which is set while creating the formula : END

                        $width_formula_arr = [];
                        $height_formula_arr = [];

                        //  Formula : START
                        if (isset($actual_product_formula)) {
                            $width_formula_arr = @$actual_product_formula['width'];
                            $height_formula_arr = @$actual_product_formula['height'];
                        }
                        //  Formula : END

                        // ================= For Calculate the Formula price : START =================
                        // For Calculate Width Price : START
                        $width_price = 0;
                        if (count($width_formula_arr) > 0) {
                            $width_extra_arr = array(
                                'attribute'         => 'attribute',
                                'attr_id'           => 'attr_id',
                                'attribute_level'   => 'attribute_level',
                                'custom_text'       => 'custom_text'
                            );

                            $width_condition_formula = [];
                            $width_condition_formula['is_min_value_formula']   = @$product_formula_data['is_min_width_formula'] ?? 0;
                            $width_condition_formula['min_value']              = @$product_formula_data['width_min_value'] ?? 0;
                            $width_condition_formula['custom_price']           = @$product_formula_data['width_custom_price'] ?? 0;

                            $width_final_formula = $this->make_attribute_formula($width_formula_arr, $order_attr_arr, $width, $height, $width_extra_arr, $product_id, $pattern_id, @$_POST['fabric_price'], $width_condition_formula);
                            $final_width_val = convert_formula_to_value($width_final_formula);
                            $width_price = $final_width_val;

                            // For Cuts if even width then we need to consider the roundup always : START
                            if (strpos($width_final_formula, 'custom_round_even') !== false) {
                                $new_width_final_formula = str_replace('custom_round_even', 'ceil', $width_final_formula);
                                $width_price = convert_formula_to_value($new_width_final_formula);
                            }
                            // For Cuts if even width then we need to consider the roundup always : END
                        }
                        // For Calculate Width Price : END

                        // For Calculate Height Price : START
                        $height_price = 0;
                        if (count($height_formula_arr) > 0) {
                            $height_extra_arr = array(
                                'attribute'         => 'attribute',
                                'attr_id'           => 'attr_id',
                                'attribute_level'   => 'attribute_level',
                                'custom_text'       => 'custom_text'
                            );

                            $height_condition_formula = [];
                            $height_condition_formula['is_min_value_formula']   = @$product_formula_data['is_min_height_formula'] ?? 0;
                            $height_condition_formula['min_value']              = @$product_formula_data['height_min_value'] ?? 0;
                            $height_condition_formula['custom_price']           = @$product_formula_data['height_custom_price'] ?? 0;

                            $height_final_formula = $this->make_attribute_formula($height_formula_arr, $order_attr_arr, $width, $height, $height_extra_arr, $product_id, $pattern_id, @$_POST['fabric_price'], $height_condition_formula);
                            $final_height_val = convert_formula_to_value($height_final_formula);
                            $height_price = $final_height_val;

                            // For Cuts if even width then we need to consider the roundup always : START
                            if (strpos($height_final_formula, 'custom_round_even') !== false) {
                                $new_height_final_formula = str_replace('custom_round_even', 'ceil', $height_final_formula);
                                $height_price = convert_formula_to_value($new_height_final_formula);
                            }
                            // For Cuts if even width then we need to consider the roundup always : END
                        }
                        // For Calculate Height Price : END

                        // ================= For Calculate the Formula price : START =================
                        $s_area = 1;
                        $price = $width_price + $height_price;
                    } else {
                        // No Formula set for this product.
                        $price = 0;
                    }
                } elseif (!empty($p->price_style_type) && $p->price_style_type == 11) {
                    $pg = DB::table('group_fixed_price_model_mapping_tbl')
                        ->where('product_id', $product_id)
                        ->where('pattern_id', $pattern_id)
                        ->first();
                    $price = ((isset($pg->price) && $pg->price != NULL) ? $pg->price : 0);
                }

                $price = round(intval($price), 2);
                $formula = array(
                    "width" => @$width_final_formula,
                    "height" => @$height_final_formula
                );
                $arr = array('st' => $st, 'row' => $row, 'col' => $col, 'price' => $price, "area" => $s_area, "formula" => $formula);
                return $price;

                // ... (continue with the remaining cases)
            }
        } else {
            $p = DB::table('products')
                ->where('id', $product_id)
                ->first();

            if (!empty($p->price_style_type) && $p->price_style_type == 11 && empty(request('comboProductIds'))) {
                $pg = DB::table('group_fixed_price_model_mapping_tbl')
                    ->where('product_id', $product_id)
                    ->where('pattern_id', $pattern_id)
                    ->first();
                $price = ((isset($pg->price) && $pg->price != NULL) ? $pg->price : 0);

                $price = round(intval($price), 2);
            } else {

                $st = 1;
                $price = 0;
            }

            return $price;
            // $arr = ['st' => $st, 'row' => $row, 'col' => $col, 'price' => $price];
            // return $arr;

            // ... (same as the provided PHP code for height and width not greater than or equal to 0)
        }
    }


    public function calculateUpCondition(
        $upConditionHeight = '0',
        $upConditionHeightFraction = '0',
        $upConditionWidth = '0',
        $upConditionWidthFraction = '0',
        $upAttributeId,
        $upLevel = '0',
        $productId = 0,
        $patternId = 0,

    ) {


        $upAttributeIdArray = explode("_", $upAttributeId);
        $upchargeAttributeId = end($upAttributeIdArray);
        $finalFormula = "";
        $mainArr = [];
        // $commonModel = new Common();

        if (auth('api')->user()->user_type == 'c') {
            $userType = 'retailer';
            $userInfo = checkRetailerConnectToWholesaler(auth('api')->user()->user_id);

            if (isset($userInfo['id']) && $userInfo['id'] != '') {
                $createdBy = auth('api')->user()->user_id;
            } else {
                $createdBy = auth('api')->user()->main_b_id;
            }
        } else {
            $userType = 'wholesaler';
            $isAdmin = auth('api')->user()->isAdmin;

            if ($isAdmin == 1) {
                $createdBy = auth('api')->user()->user_id;
            } else {
                $createdBy = auth('api')->user()->admin_created_by;

                if (empty($createdBy)) {
                    $createdBy = auth('api')->user()->user_id;
                }
            }
        }

        // $upData = DB::table('upcharges_price_condition')
        //     ->join('upcharges_price_condition_attributes as upca', 'upcharges_price_condition.upcharges_price_condition_id', '=', 'upca.upcharges_price_condition_id')
        //     ->where('upca.upcharge_attribute_id', $upchargeAttributeId)
        //     ->where('upca.attribute_level', $upLevel)
        //     ->where('upcharges_price_condition.created_by', $createdBy)
        //     ->whereRaw('FIND_IN_SET(' . $productId . ', upcharges_price_condition.product_ids) <> 0')
        //     ->where('upcharges_price_condition.is_active', 1)
        //     ->get()
        //     ->toArray();
        $upData = DB::table('upcharges_price_condition')
            ->join('upcharges_price_condition_attributes as upca', 'upcharges_price_condition.upcharges_price_condition_id', '=', 'upca.upcharges_price_condition_id')
            ->join('attribute_tbl', 'attribute_tbl.attribute_id', 'upca.attr_id')
            ->join('attr_options', 'attr_options.att_op_id', 'upca.upcharge_attribute_id')
            ->where('upca.upcharge_attribute_id', $upchargeAttributeId)
            ->where('upca.attribute_level', $upLevel)
            ->where('upcharges_price_condition.created_by', $createdBy)
            ->whereRaw('FIND_IN_SET(' . $productId . ', upcharges_price_condition.product_ids) <> 0')
            ->where('upcharges_price_condition.is_active', 1)
            ->get()
            ->toArray();

        // dd($upData->toSql());

        // $response = [];
        $response = [];
        $img = "";
        $getImg = [];

        if ($createdBy == auth('api')->user()->level_id) {
            $userDetail = getCompanyProfileOrderConditionSettings(auth('api')->user()->level_id);
        } else {
            $userDetail = getCompanyProfileOrderConditionSettingsPart2($createdBy);
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


        // $response['attribute_img'] = $img;

        if (count($upData) > 0) {
            $costFactorData = commonWholesalerToRetailerCommission($productId);
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
                                            $per_ex_price = calculateTotalAmt($per_ex_price, $oper, $value['per_inches_amt']);
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
                                                $manual_price = calculateTotalAmt($manual_price, $oper, $value['price_details_value']);
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
                                            $per_ex_price = calculateTotalAmt($per_ex_price, $oper, $value['per_inches_amt']);
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
                                                $manual_price = calculateTotalAmt($manual_price, $oper, $value['price_details_value']);
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

                            $final_val = convertFormulaToValue($finalFormula);
                            $price = $final_val;

                            if (strpos($finalFormula, 'custom_round_even') !== false) {
                                $new_final_formula = str_replace('custom_round_even', 'ceil', $finalFormula);
                                $price = convertFormulaToValue($new_final_formula);
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
                                ->where('row', '>=', $final_width)
                                ->where('col', '>=', $final_height)
                                ->orderBy('row_id', 'asc')
                                ->limit(1)
                                ->first();
                        }

                        $final_up_condition_price = ($table_price != NULL ? $table_price->price : 0);
                    }

                    $purpose = $rec->purpose;
                    $cost_factor = $rec->cost_factor;
                    $cost_factor_price = 0;

                    if ($final_up_condition_price > 0) {
                        if ($cost_factor == 0) {
                            $cost_factor_price = $final_up_condition_price;
                        } else {
                            $cost_factor_price = (($final_up_condition_price * $costFactorRate));
                        }
                    }

                    // $arr = [
                    //     "upcharge_condition_id" => $rec->upcharges_price_condition_id,
                    //     "price" => $final_up_condition_price,
                    //     "cost_factor_price" => $cost_factor_price,
                    //     "cost_factor_rate" => $costFactorRate,
                    //     "display_name" => $display_name,
                    //     "final_formula" => $finalFormula,
                    //     "related_attr_class" => $related_attr_class,
                    //     "display_purpose" => intval($purpose),

                    // ];
                    $arr = [
                        "value" => $final_up_condition_price,
                        // "name" => $rec->option_name
                        "name" => $rec->attribute_name . ' (' . $rec->option_name . ')'
                    ];


                    $mainArr = $arr;
                }
            }

            // return 1;
            $response = $mainArr;
            return $response;
        } else {
            return $response;
        }
    }


    public function getProductCommission($product_id, $customer_id)
    {
        $product = DB::table('b_cost_factor_tbl')->where('product_id', $product_id)
            ->where('customer_id', $customer_id)
            ->first(['dealer_cost_factor as dealer_price', 'individual_cost_factor as individual_price']);

        if (!$product) {
            $product = Product::where('id', $product_id)->first(['dealer_price', 'individual_price']);
        }
        if ($product) {
            if ($product->dealer_price > 0) {
                $discount_rate = 100 - ($product->dealer_price * 100);
            } else {
                $discount_rate = 0;
            }
        }
        return $discount_rate ?? 0;
    }


    public function getMinMaxHeightWidth($product_id = null, $pattern_id = null)
    {
        if (!$product_id) {
            return [];
        }

        $product = DB::table('products')->find($product_id);
        if (!$product) {
            return [];
        }

        $response = [];

        switch ($product->price_style_type) {
            case 5:
            case 6:
                $response = [
                    'minw' => $product->min_width ? intval($product->min_width) : null,
                    'minh' => $product->min_height ? intval($product->min_height) : null,
                    'maxh' => $product->max_height ? intval($product->max_height) : null,
                    'maxw' => $product->max_width ? intval($product->max_width) : null
                ];
                break;

            case 4:
                $group_id = DB::table('price_model_mapping_tbl')
                    ->where('product_id', $product_id)
                    ->where('pattern_id', $pattern_id)
                    ->value('group_id') ?? '0';

                $response = DB::table('price_style')
                    ->select('row as maxw', 'col as maxh')
                    ->where('style_id', $group_id)
                    ->latest('row_id')
                    ->first();
                break;

            case 1:
            case 9:
                $response = DB::table('price_style')
                    ->select('row as maxw', 'col as maxh')
                    ->where('style_id', $product->price_rowcol_style_id)
                    ->latest('row_id')
                    ->first();
                break;
        }

        return $response;
    }




    // Stage update : Start
    function createMfgLebelEntry($order_id)
    {
        $is_already_mfg_label = DB::table('b_level_quotation_details_mfg_label')->where('order_id', $order_id)->count();

        if (!$is_already_mfg_label) {
            $productData = DB::table('b_level_qutation_details')->where('order_id', $order_id)->get();

            foreach ($productData as $order_product_data) {
                // Add data in b_level_quotation_details_mfg_label table start
                $roomValue = json_decode($order_product_data->room_index, true);

                foreach ($roomValue as $room_value) {
                    $tempDetailData = [
                        'fk_row_id' => $order_product_data->row_id,
                        'order_id' => $order_id,
                        'room' => $room_value,
                        'product_id' => $order_product_data->product_id,
                        'product_qty' => 1,
                        'list_price' => $order_product_data->list_price,
                        'upcharge_price' => $order_product_data->upcharge_price,
                        'upcharge_label' => $order_product_data->upcharge_label,
                        'discount' => $order_product_data->discount,
                        'unit_total_price' => $order_product_data->unit_total_price,
                        'category_id' => $order_product_data->category_id,
                        'sub_category_id' => $order_product_data->sub_category_id,
                        'pattern_model_id' => $order_product_data->pattern_model_id,
                        'manual_pattern_entry' => $order_product_data->manual_pattern_entry,
                        'manual_color_entry' => $order_product_data->manual_color_entry,
                        'fabric_price' => $order_product_data->fabric_price,
                        'color_id' => $order_product_data->color_id,
                        'width' => $order_product_data->width,
                        'height' => $order_product_data->height,
                        'height_fraction_id' => $order_product_data->height_fraction_id,
                        'width_fraction_id' => $order_product_data->width_fraction_id,
                        'notes' => $order_product_data->notes,
                        'room_index' => $order_product_data->room_index
                    ];

                    DB::table('b_level_quotation_details_mfg_label')->insert($tempDetailData);
                }
                // Add data in b_level_quotation_details_mfg_label table end
            }
        }
    }
    function removeMfgLebelEntry($order_id)
    {
        DB::table('b_level_quotation_details_mfg_label')->whereIn('order_id', $order_id)->delete();

    }
    // stage update : End 



    // Modify Amount for order receipt : Start

    // Update Shipping Installation Chatges : Start
    function updateShippingInstallationCharge($amount,$order_id)
    {

        $quotationData = DB::table('b_level_quatation_tbl')
            ->where('order_id', $order_id)
            ->where('level_id', $this->level_id)
            ->first();

        if ($quotationData) {
            $oldShippingCharges = $quotationData->shipping_charges;
            $oldInstallationCharge = $quotationData->installation_charge;
            $due = $quotationData->due;
            $grandTotal = $quotationData->grand_total;
            $oldShippingPercentage = number_format((($quotationData->subtotal * $quotationData->shipping_percentage) / 100), 2);
            $newGrandTotal = $grandTotal - ($oldShippingCharges + $oldInstallationCharge + $oldShippingPercentage) + $amount;
            $newDue = $due - ($oldShippingCharges + $oldInstallationCharge + $oldShippingPercentage) + $amount;

            DB::table('b_level_quatation_tbl')
                ->where('order_id', $order_id)
                ->update([
                    'shipping_charges' => $amount,
                    'installation_charge' => 0,
                    'shipping_percentage' => 0,
                    'grand_total' => $newGrandTotal,
                    'due' => $newDue,
                ]);

                $message = 'Shipping/Installation Charge applied Successfully.';
                return response()->json(['success' => true, 'message' => $message], 200);

        } else {

                $message = 'Something went wrong. Please try again';
                return response()->json(['success' => false, 'message' => $message], 400);
        }
    }
    // Update Shipping Installation Chatges : End


    // Update Credit Amount : Start
    function updateCredit($credit,$order_id) {
    
        $quotationData = DB::table('b_level_quatation_tbl')
            ->where('order_id', $order_id)
            ->where('level_id', $this->level_id) // Assuming $this->level_id exists in your controller
            ->first();

        if($quotationData){
            $oldCredit = $quotationData->credit;
            $grandTotal = $quotationData->grand_total;
            $due = $quotationData->due;

            $newGrandTotal = $grandTotal + $oldCredit - $credit;
            $newDue = $due + $oldCredit - $credit;

            DB::table('b_level_quatation_tbl')
                ->where('order_id', $order_id)
                ->update([
                    'credit' => $credit,
                    'grand_total' => $newGrandTotal,
                    'due' => $newDue,
                ]);

                $message = 'Credit Applied Successfully.';
                return response()->json(['success' => true, 'message' => $message], 200);

        } else {

                $message = 'Something went wrong. Please try again';
                return response()->json(['success' => false, 'message' => $message], 400);
        }
    }
    // Update Credit Amount : End


    // Update Discount Amount : Start
    function updateDiscount($amount , $order_id) {
    
        $quotation_data = DB::table('b_level_quatation_tbl')
                            ->where('order_id', $order_id)
                            ->where('level_id', $this->level_id)
                            ->first();
                            // return $quotation_data;
    
        if($quotation_data){
    
            $old_invoice_discount = $quotation_data->invoice_discount;
            $grand_total = $quotation_data->grand_total;
            $due = $quotation_data->due;
    
            $new_grand_total = $grand_total + $old_invoice_discount - $amount;
            $new_due = $due + $old_invoice_discount - $amount;
    
            $new_data = [
                'invoice_discount' => $amount, 
                'grand_total' => $new_grand_total, 
                'due' => $new_due, 
            ];
    
            DB::table('b_level_quatation_tbl')
                ->where('order_id', $order_id)
                ->update($new_data);
    
                $message = 'Extra Discount Applied Successfully.';
                return response()->json(['success' => true, 'message' => $message], 200);
        } else {
            $message = 'Something went wrong. Please try again.';
            return response()->json(['success' => false, 'message' => $message], 400);
        }
    }
    // Update Discount Amount : End

    // Modify Amount for order receipt : End


    // Calculate the wholesaler to retailer / retailer to wholesaler total : Start
    public function retailerToWholesalerCalculation($order_id) {
        
        // Calculate sub total
        $quote_total_price = DB::table('b_level_qutation_details')
            ->where('order_id', $order_id)
            ->sum('unit_total_price');

        $controller_total_price = DB::table('order_controller_cart_item')
            ->where('order_id', $order_id)
            ->sum('item_total_price');

        $hardware_total_price = DB::table('order_hardware_cart_item')
            ->where('order_id', $order_id)
            ->sum('item_total_price');

        $component_total_price = DB::table('order_component_cart_item')
            ->where('order_id', $order_id)
            ->sum('component_total_price');

        $sub_total_price = round(($quote_total_price + $controller_total_price + $component_total_price + $hardware_total_price), 2);

        $quote_data = DB::table('b_level_quatation_tbl')
            ->where('order_id', $order_id)
            ->first();

        $shipping_charges = !empty($quote_data->shipping_charges) ? $quote_data->shipping_charges : 0;
        $installation_charge = !empty($quote_data->installation_charge) ? $quote_data->installation_charge : 0;
        $misc = !empty($quote_data->misc) ? $quote_data->misc : 0;
        $credit = !empty($quote_data->credit) ? $quote_data->credit : 0;
        $invoice_discount = !empty($quote_data->invoice_discount) ? $quote_data->invoice_discount : 0;
        $paid_amount = !empty($quote_data->paid_amount) ? $quote_data->paid_amount : 0;

        // Calculate grand total
        $sales_tax_per = isset($quote_data->tax_percentage) ? $quote_data->tax_percentage : 0;
        $sales_product_base_tax = @$quote_data->is_product_base_tax ? 1 : 0;
        $sales_tax_amt = 0;

        if ($sales_tax_per > 0) {
            $sales_tax_amt = (($sub_total_price * $sales_tax_per) / 100);
        }

        if (@$sales_product_base_tax) {
            $quote_tax_total_price = DB::table('b_level_qutation_details')
                ->where('order_id', $order_id)
                ->sum('product_base_tax');

            $controller_tax_total_price = DB::table('order_controller_cart_item')
                ->where('order_id', $order_id)
                ->sum('product_base_tax');

            $hardware_tax_total_price = DB::table('order_hardware_cart_item')
                ->where('order_id', $order_id)
                ->sum('product_base_tax');

            $component_tax_total_price = DB::table('order_component_cart_item')
                ->where('order_id', $order_id)
                ->sum('product_base_tax');
            
            $sales_tax_amt = round(($quote_tax_total_price + $controller_tax_total_price + $hardware_tax_total_price + $component_tax_total_price), 2);
        }

        $shipping_zone_per = isset($quote_data->shipping_percentage) ? $quote_data->shipping_percentage : 0;
        $shipping_zone_amt = 0;

        if ($shipping_zone_per > 0) {
            $shipping_zone_amt = (($sub_total_price * $shipping_zone_per) / 100);
        }

        $grand_total = round(($sub_total_price + $sales_tax_amt + $shipping_zone_amt + $shipping_charges + $installation_charge + $misc - $credit - $invoice_discount), 2); 

        // Calculate due amount
        $due_amount = round(($grand_total - $paid_amount), 2);

        // Update final amount
        $data = [
            'subtotal' => $sub_total_price,
            'grand_total' => $grand_total,
            'due' => $due_amount,
        ];

        DB::table('b_level_quatation_tbl')
            ->where('order_id', $order_id)
            ->update($data);

        return true;
    }
    // Calculate the wholesaler to retailer / retailer to wholesaler total : End



    // Calculate shipping_percentage and update for wholesaler to retailer : Start
    public function retailerToWholesalerShippingCalculation($customer_id, $order_id, $level_id)
    {
        // Get customer Zone
        $customer_data = DB::table('customers')
                            ->where('id', $customer_id)
                            ->first();

        // Calculate sub total
        $quote_total_price = DB::table('b_level_qutation_details')
                                ->where('order_id', $order_id)
                                ->sum('unit_total_price');

        $controller_total_price = DB::table('order_controller_cart_item')
                                    ->where('order_id', $order_id)
                                    ->sum('item_total_price');

        $hardware_total_price = DB::table('order_hardware_cart_item')
                                    ->where('order_id', $order_id)
                                    ->sum('item_total_price');

        $component_total_price = DB::table('order_component_cart_item')
                                    ->where('order_id', $order_id)
                                    ->sum('component_total_price');

        $sub_total_price = round(($quote_total_price + $controller_total_price + $hardware_total_price + $component_total_price), 2);

        // Get the New sales tax once update the order
        $new_sales_tax_percentage = @$customer_data->is_taxable == 1 ? ($customer_data->tax_percentage ?? 0) : 0;

        // Get zone wise percentage
        if ($customer_data->enable_shipping_zone == 1) {
            // If shipping zone is enable then get shipping percentage
            $zone_details = DB::table('shipping_zones')
                                ->where('zone_id', $customer_data->zone)
                                ->where('min_price', '<=', $sub_total_price)
                                ->where('max_price', '>=', $sub_total_price)
                                ->where('level_id', $level_id)
                                ->first();
        }

        $shipping_percentage = $zone_details->percentage ?? 0;

        // Update Shipping percentage
        DB::table('b_level_quatation_tbl')
            ->where('order_id', $order_id)
            ->update([
                'shipping_percentage' => $shipping_percentage,
                'tax_percentage' => $new_sales_tax_percentage,
            ]);

        return true;
    }
    // Calculate shipping_percentage and update for wholesaler to retailer : End




    public function get_height_width_fraction($g_val, $category_id = 0)
    {
        if (isset($g_val) && !empty($g_val)) {
            if ($category_id > 0) {
                $hw = DB::table('width_height_fractions')->select('*')
                    ->where('decimal_value' ,'>=', "0." . $g_val)
                    ->orderBy('decimal_value', 'asc')
                    ->get();
                $hw1 = DB::table('categories')->select('*')
                        ->where('id', $category_id)
                        ->limit(1)
                        ->first();
                if(isset($hw1->fractions) && $hw1->fractions != '') {
                    $category_fractions = explode(',', $hw1->fractions);
                    $hwf = 0;
                    foreach ($hw as $key => $value) {
                        if (in_array($value->fraction_value,$category_fractions)){
                            $hwf = $value->id;
                            break;
                        }
                    }
                    return  $hwf;
                }else{
                    return  0;
                }        
            }else{
                $hw = DB::table('width_height_fractions')->select('*')
                    ->where('decimal_value','>=', "0." . $g_val)
                    ->orderBy('decimal_value', 'asc')
                    ->limit(1)
                    ->first();
                $hwf = isset($hw->id)?$hw->id:0;  
                return  $hwf;
            }  
        }else{
            return 0;
        }    
    }


}
