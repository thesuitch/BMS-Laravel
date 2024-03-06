<?php


use Illuminate\Support\Facades\DB;

use App\Models\UserInfo;


// get order condition setings from company_profile table and user related data from user_info table
if (!function_exists('getCompanyProfileOrderConditionSettings')) {

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
}

// get checkRetailerConnectToWholesaler start
if (!function_exists('checkRetailerConnectToWholesaler')) {

    function checkRetailerConnectToWholesaler($userId)
    {
        // if retailer not connect with wholesaler then return data
        $data = DB::table('user_info')
            ->where('wholesaler_connection', 0)
            ->where('id', $userId)
            ->first();

        return $data;
    }
}
// get checkRetailerConnectToWholesaler end


// get commonWholesalerToRetailerCommission start
if (!function_exists('commonWholesalerToRetailerCommission')) {

    function commonWholesalerToRetailerCommission($productId, $customerId = 0)
    {
        if ($customerId != 0) {

            if (auth()->user()->user_type == 'c') {
                // It means retailer
                $userInfo = checkRetailerConnectToWholesaler(auth()->user()->id);

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
                $product = DB::table('products')
                    ->select('dealer_price', 'individual_price')
                    ->where('id', $productId)
                    ->first();

                $commission = ['dealer_price' => $product->dealer_price, 'individual_price' => $product->individual_price];
            }
        } else {
            $commission = ['dealer_price' => 1, 'individual_price' => 0];
        }

        return $commission;
    }
}
// get commonWholesalerToRetailerCommission end

if (!function_exists('getCompanyProfileOrderConditionSettingsPart2')) {

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


if (!function_exists('convert_formula_to_value')) {


    function convert_formula_to_value($formula)
    {
        $is_even = strpos($formula, 'custom_round_even') !== false;

        try {
            $result = @eval('return ' . str_replace('custom_round_even', 'ceil', $formula) . ';');
        } catch (Exception | ErrorException | ParseError $e) {
            $result = 0;
        }

        return trim($result) == 'INF' ? 0 : ($is_even ? ceil($result) : $result);
    }
}


if (!function_exists('convertFormulaToValue')) {

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
}



// For strint operator calculate price for upcharges : START
if (!function_exists('calculateTotalAmt')) {

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
}
   // For strint operator calculate price for upcharges : END
