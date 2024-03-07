<style>
    .force-red * {
        color: red !important;
    }

    .display-price-details {
        color: white !important;
    }

    .display-price-details,
    .price-details {
        width: 100%;
        margin: auto;
    }

    .display-price-details tr td:last-child,
    .price-details tr td:last-child {
        white-space: nowrap;
    }

    .display-price-details tr td,
    .price-details tr td {
        background-color: #145388 !important;
    }

    .fixed_item {
        background: #145388;
        padding: 15px !important;
    }

    .drapery_price_section {
        margin-bottom: 20px;
        background: #145388;
        padding: 15px !important;
    }

    .drapery_price_section tr td {
        background-color: #145388 !important;
    }

    #tprice,
    .drape_price_div {
        color: #fff;
    }

    .custom-select-css {
        margin-left: 15px;
        max-width: calc(50% - 30px) !important;
    }

    #content div.box,
    #content #right,
    #content {
        overflow: inherit
    }

    #content div.box #save_order h5 {
        display: inline-block;
        width: 100%;
        box-sizing: border-box;
        margin: 0;
        padding: 15px;
    }

    .sticky {
        position: -webkit-sticky;
        position: sticky;
        top: 40px;
    }

    .col-sm-9 .col-sm-6+.col-sm-4 {
        max-width: 100%;
        flex: 0 0 100%;
        margin: 0 !important;
    }

    .overlay {
        position: fixed;
        height: 100%;
        width: 100%;
        top: 0px;
        left: 0px;
        background-color: rgba(255, 255, 255, 0.95);
        z-index: 99;
        color: #3498db;
        text-align: center;
    }

    .overlay .loader {
        display: block;
        border: 16px solid #f3f3f3;
        /* Light grey */
        border-top: 16px solid #3498db;
        /* Blue */
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
        margin-left: auto;
        margin-right: auto;
        margin-top: 20%;
    }

    .overlay h2 {
        border-bottom: none !important;
    }

    .hidden {
        display: none;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .swal2-radio {
        display: block !important;
        text-align: left;
    }

    .swal2-checkbox label,
    .swal2-radio label {
        margin: 0 1.5%;
        font-size: 1em;
        max-width: 47%;
        width: 47%;
        position: relative;
        padding-left: 28px;
    }

    .swal2-checkbox label,
    .swal2-radio label input[type="radio"] {
        position: absolute;
        top: 5px;
        left: 0px;
    }

    .form-child-label {
        text-align: right;
        padding-left: 20px;
    }

    #subcategory_id label {
        text-align: right;
        padding-left: 20px;
    }

    .disable-select {
        pointer-events: none;
        background-color: #e9ecef;
        opacity: 1;
    }

    select option:disabled {
        pointer-events: none;
        background-color: #d3d3d3;
    }

    /* Component section : START */
    .component-group-section .component-group-name {
        font-weight: bold;
        text-decoration: underline;
    }

    .component-group-section .component-part-name {
        padding-left: 10px;
        display: block;
    }

    .component_price_section {
        margin-top: 20px;
        background: #145388;
        padding: 15px !important;
    }

    .component_price_section tr td {
        background-color: #145388 !important;
        color: #fff;
    }

    .component-group-section .component-input-div {
        max-width: 16.5% !important;
        padding-right: 10px !important;
        padding-left: 10px !important;
    }

    .component-group-section .component-part-name-div {
        max-width: 33.33% !important;
    }

    /* Component section : END  */

    /* 
        Order Details choose image : START
    */
    #upload_checkbox_div .custom-control-label::before,
    #upload_checkbox_div .custom-control-label::after {
        top: 13px !important;
    }

    #upload_checkbox_div label.custom-control-label {
        padding: 10px 0;
    }

    #upload_checkbox_div,
    #upload_checkbox_div label {
        cursor: pointer;
    }

    #special_note_checkbox_div .custom-control-label::before,
    #special_note_checkbox_div .custom-control-label::after {
        top: 13px !important;
    }

    #special_note_checkbox_div label.custom-control-label {
        padding: 10px 0;
    }

    #special_note_checkbox_div,
    #special_note_checkbox_div label {
        cursor: pointer;
    }


    .btn_upload {
        font-weight: 700;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        position: relative;
        color: #3870a8;
        /* background-color: #376da4; */
        border: 2px solid #34689c;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 13px;
        box-shadow: 2px 2px 0px 0px #eee;
    }

    .btn_upload:hover,
    .btn_upload:focus {
        background-color: #4579af;
        color: white;
        border: 2px solid white;
        box-shadow: 3px 2px 4px 0px #999;
    }

    #upload-image-div {
        display: flex;
        align-items: center;
        margin: 10px 0 20px 0 !important;
    }

    .btn_upload input {
        cursor: pointer;
        height: 100%;
        position: absolute;
        filter: alpha(opacity=1);
        -moz-opacity: 0;
        opacity: 0;
        left: 0;
        top: 0;
    }

    .preview {
        height: 100px;
    }

    .btn-rmv1 {
        display: none;
    }

    .rmv-icon {
        cursor: pointer;
        color: #fff;
        border-radius: 30px;
        border: 1px solid #fff;
        display: inline-block;
        background: rgba(255, 0, 0, 1);
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: -10px;
        right: -10px;
    }

    .rmv-con:hover {
        background: rgba(255, 0, 0, 0.5);
    }

    #preview-div {
        position: relative;
    }

    img#ImgPreview.show-border {
        border: 2px solid #356a9f;
        padding: 5px;
        border-radius: 3px;
        margin-left: 50px;
    }

    /* 
        Order Details choose image : END
    */

    button.btn.dropdown-toggle.btn-light {
        border: 1px solid #ccc;
        border-radius: 0;
        background-color: transparent !important;
        font-size: 12px;
        padding: 8px 10px;
    }

    .bootstrap-select .bs-ok-default:after {
        color: green;
    }

    .dropdown-menu li {
        border-bottom: 1px solid #eeeeee;
    }

    #content div.box li {
        padding: 0px 0 2px 0;
    }

    #upload_checkbox_div .custom-control-label::before,
    #upload_checkbox_div .custom-control-label::after {
        top: 0.2rem !important;
    }

    .the-count {
        float: right;
        padding: 0.1rem 0 0 0;
        font-size: 0.875rem;
    }

    .inc-dec-btn {
        cursor: pointer;
        width: 25px;
        height: 25px;
    }

    .inc-dec-qty {
        height: 24px;
        width: 50px;
        text-align: center;
    }

    .prod-div button {
        border-radius: 3px;
        cursor: pointer;
        width: 25px;
        height: 25px;
        border: 1px solid rgb(0 0 0 / 50%);
    }

    .prod-div input {
        height: 24px;
    }

    #attr-img-div {
        position: relative;
        top: 0;
        left: 0;
        min-height: 400px;
    }

    .attr-img {
        position: absolute;
        top: 0;
        left: 0;
        width: auto;
        max-height: 100%;
        max-width: 100%;
    }

    .attr-img-parent-div {
        position: relative;
        height: auto;
        max-height: 100%;
    }

    .attr-img img {
        height: 100%;
        width: 100%;
    }

    .zoom-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        font-size: 15px;
        background: #f5f5f5;
        padding: 5px;
        border-radius: 50%;
        box-shadow: 1px 3px 4px 1px #ccc;
        cursor: pointer;
        border: 1px solid #d1d1d1;
    }

    .zoom-icon:hover {
        background: #f4b30a !important;
    }

    .remove-room-div {
        margin: 20px 20px 0 20px;
    }

    .remove-room-div h4 {
        width: 50%;
        display: inline-block;
        margin-bottom: 10px;
        text-align: start;
    }

    .swal2-checkbox label,
    .swal2-radio label {
        display: inline-block !important;
        width: 50% !important;
        max-width: 47% !important;
        margin: 5px 0 !important;
    }

    /* CSS styles for the loader and progress bar */
    /* #loader-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      display: none;
    }

    #loader-progress {
      width: 80%;
      max-width: 400px;
      height: 20px;
      background: #ccc;
      border-radius: 10px;
      position: relative;
    }

    #progress-bar {
      width: 0;
      height: 100%;
      background: #4caf50;
      border-radius: 10px;
    }

    #progress-text {
      margin-top: 10px;
      color: white;
    } */

    #loader-container {
        display: none;
        /* Initially hide the loader container */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        /* Add a semi-transparent background */
        z-index: 9999;
        /* Ensure the loader is on top of other content */
        text-align: center;
    }

    #loader-wrapper {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        /* text-align: center; */
    }

    #loader {
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
        margin-bottom: 10px;
        /* Add margin to separate loader and text */
    }

    #loading-text {
        font-size: 13px;
        color: #333;
        position: relative;
        text-align: center;
    }

    /* Keyframes animation for the loader */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<div class="col-sm-9">

    <!-- For loader : START -->
    <div class="overlay" style="display: none">
        <div class="loader"></div>
        <br>
        <br>
        <h2>Please Wait while form is loading</h2>
    </div>
    <!-- For loader : END -->

    <!-- Loader container -->
    <!-- <div id="loader-container">
    <div id="loader-progress">
      <div id="progress-bar"></div>
    </div>
    <div id="progress-text">Processing...</div>
  </div> -->

    <div id="loader-container">
        <div id="loader-wrapper">
            <div id="loader"></div>
            <div id="loading-text">Processing...</div>
            <!-- <div id="loading-progress">Processing 0 of 0 requests...</div> -->
        </div>
    </div>


    <?= form_open('b_level/order_controller/add_to_cart', array('id' => 'AddToCart', 'autocomplete' => 'off', 'class' => 'frm_product_order_form', 'enctype' => 'multipart/form-data')) ?>

    <!-- For Housing attribute alert : START -->
    <input type="hidden" name="housing_style_op" id="housing_style_op">
    <input type="hidden" name="housing_style_name" id="housing_style_name">
    <input type="hidden" name="housing_style_attr_op" id="housing_style_attr_op">
    <input type="hidden" name="housing_style_attr_op_name" id="housing_style_attr_op_name">
    <!-- For Housing attribute alert : END -->

    <input type="hidden" name="is_drapery_cat" id="is_drapery_cat" value="0">
    <input type="hidden" name="final_drapery_price" id="final_drapery_price" value="0">

    <!-- For Drapery right side blue box price : START -->
    <input type="hidden" name="hid_drapery_of_cuts" id="hid_drapery_of_cuts" value="0">
    <input type="hidden" name="hid_drapery_of_cuts_only_panel" id="hid_drapery_of_cuts_only_panel" value="0">
    <input type="hidden" name="hid_drapery_cut_length" id="hid_drapery_cut_length" value="0">
    <input type="hidden" name="hid_drapery_total_fabric" id="hid_drapery_total_fabric" value="0">
    <input type="hidden" name="hid_drapery_total_yards" id="hid_drapery_total_yards" value="0">
    <input type="hidden" name="hid_drapery_trim_yards" id="hid_drapery_trim_yards" value="0">
    <input type="hidden" name="hid_drapery_banding_yards" id="hid_drapery_banding_yards" value="0">
    <input type="hidden" name="hid_drapery_flange_yards" id="hid_drapery_flange_yards" value="0">
    <input type="hidden" name="hid_drapery_finished_width" id="hid_drapery_finished_width" value="0">
    <!-- For Drapery right side blue box price : END -->

    <div class="form-row">
        <div class="form-group col-md-12">
            <div class="row">
                <?php $category_label = $custom_label['order_category_label']; ?>
                <label for="" class="col-sm-2"><span class="custom_cat_label"><?= $category_label ?></span></label>
                <div class="col-sm-6">
                    <select class="form-control select2-single" name="category_id" id="category_id" required="" data-placeholder="-- Select one --">
                        <option value="">-- Select one --</option>
                        <?php foreach ($get_category as $category) { ?>
                            <option value='<?php echo $category->category_id; ?>' <?php echo ($category->default == '1') ? 'selected' : '' ?>><?php echo $category->category_name; ?></option>;
                        <?php  } ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12" id="order_qty_div" style="display:none">
            <div class="row mb-2">
                <label for="" class="col-sm-2"><span>Qty</span></label>
                <div class="col-sm-6 product-qty-div">

                    <?php
                    $_SESSION['form_room_datacounter'] = @$_SESSION['room_datacounter'];

                    $cat_data = $this->db->select('category_tbl.hide_room,product_tbl.hide_room as product_hide_room')
                        ->where('product_id', @$get_product_order_info->product_id)
                        ->join('category_tbl', 'category_tbl.category_id=product_tbl.category_id')
                        ->get("product_tbl")->row();
                    ?>

                    <input type="hidden" id="form_room_arr" value=''>
                    <input type="hidden" id="form_missing_key" value=''>

                    <input type="hidden" id="form_item_id" value='<?= @$get_product_order_info->id ?? @$get_product_order_info->row_id; ?>'>
                    <input type="hidden" id="form_old_room" value='<?= htmlentities(@$get_product_order_info->room, ENT_QUOTES) ?? ""; ?>'>

                    <input type="hidden" id="form_product_hide_room" value='<?php echo @$cat_data->product_hide_room; ?>'>
                    <input type="hidden" id="form_hide_room" value='<?php echo @$cat_data->hide_room; ?>'>

                    <input type="button" value="-" id="dec_qty" class="decrease inc-dec-btn" />
                    <input type="text" id="product-qty" class="inc-dec-qty" step="1" min="1" name="qty" value="<?= @$get_product_order_info->qty ? @$get_product_order_info->qty : (@$get_product_order_info->product_qty ?? 1); ?>" oninput="this.value = this.value.replace(new RegExp(/[^\d]/,'ig'), '')" />
                    <input type="button" value="+" id="form_increase_qty" class="increase inc-dec-btn" />

                </div>
                <script>

                </script>
                <div class="col-sm-2"></div>
            </div>
        </div>

        <div class="form-group col-md-12" id="phase_2_ordering_div" style="display:none">
            <div class="row mb-2">
                <label for="" class="col-sm-2"><span>Phase 2</span></label>
                <div class="col-sm-6 d-flex align-items-center">
                    <div class="custom-control custom-checkbox" id="">
                        <input type="checkbox" class="custom-control-input" name="phase_2" value="1" id="phase_2">
                        <input type="hidden" name="phase_2_ordering" value="0" id="phase_2_ordering_condition">
                        <label class="custom-control-label" for="phase_2"></label>
                    </div>
                    <div class="phase_2_condition_div w-100" style="display:none" id="phase_2_condition_div">
                        <!-- <select name="phase_2_condition[]" id="phase_2_condition" class="form-control selectpicker select-all" data-live-search="true" placeholder="" multiple></select> -->
                        <select name="phase_2_condition[]" id="phase_2_condition" class="form-control selectpicker " data-live-search="true" placeholder=""></select>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            <div class="row mb-2 force-red" id="phase_2_ordering_instruction"></div>
        </div>


        <div class="component-section col-md-12"></div>
        <div class="form-group col-md-12" id="subcategory_id">
        </div>
        <div class="form-group col-md-12 product-section">
            <div class="row">
                <?php $product_label = $custom_label['order_product_label']; ?>
                <label for="" class="col-sm-2"><span class="custom_product_label"><?= $product_label ?></span></label>
                <div class="col-sm-6">
                    <select class="form-control select2-single" name="product_id" id="product_id" onchange="getAttribute(this.value)" required data-placeholder="-- Select one --">
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12" id="color_model">
            <div class="row">
                <?php $pattern_label = $custom_label['order_pattern_label']; ?>
                <label for="" class="col-sm-2"><span class="custom_pattern_label"><?= $pattern_label ?></span></label>
                <div class="col-sm-4">
                    <select class="form-control select2" name="pattern_model_id" id="pattern_id" data-placeholder="-- Select one --" required="">
                        <option value="">-- Select One --</option>
                        <!-- <option value="1">Smooth</option> -->
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12" id="pattern_color_model">
            <div class="row">
                <?php $color_label = $custom_label['order_color_label']; ?>
                <label for="" class="col-sm-2"><span class="custom_color_label"><?= $color_label ?></span></label>
                <div class="col-sm-4">
                    <select class="form-control select2" name="color_id" id="color_id" onchange="getColorCode(this.value)" required="" data-placeholder="-- Select one --">
                        <option value="">-- Select one --</option>
                        <!-- <option value="11">Alabaster</option>
                        <option value="10">Antique White</option>
                        <option value="9">White White</option> -->
                    </select>
                </div>
                <!-- <div class="col-sm-2">Color code :</div> -->
                <div class="col-sm-2">
                    <input type="text" id="colorcode" onkeyup="getColorCode_select(this.value)" class="form-control" placeholder='<?= $color_label ?> Code'>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="row">
                <?php $width_label = $custom_label['order_width_label']; ?>
                <label for="" class="col-sm-2"><span class="custom_width_label"><?= $width_label ?></span> (<?= $company_profile[0]->unit ?>)</label>
                <div class="col-sm-4">
                    <!-- <input type="text" name="width" class="form-control valid_height_width" id="width" onChange="loadPStyle();changeWidth();"
                           onKeyup="masked_two_decimal(this);loadPStyle()" min="1" required> -->

                    <input type="text" name="width" class="form-control valid_height_width" id="width" onChange="loadPStyle();changeWidth();" onKeyup="masked_two_decimal(this);" min="1" required>

                    <input type="hidden" id="cord_len_val" value="" class="cord_len_val">
                    <input type="hidden" id="unit_type" value="<?= $company_profile[0]->unit ?>" min="1">
                </div>
                <div class="col-sm-2">
                    <?php if ($company_profile[0]->unit == 'inches') { ?>
                        <select class="form-control" name="width_fraction_id" id="width_fraction_id" onKeyup="loadPStyle(this)" onChange="loadPStyle()" data-placeholder='-- Select one --'>
                            <option value="">--Select one--</option>
                            <?php
                            foreach ($fractions as $f) {
                                echo "<option value='$f->id'>$f->fraction_value</option>";
                            }
                            ?>
                        </select>
                    <?php  } ?>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12">
            <div class="row">
                <?php $height_label = $custom_label['order_height_label']; ?>
                <label class="col-sm-2"><span class="custom_height_label"><?= $height_label ?></span> (<?= $company_profile[0]->unit ?>)</label>
                <div class="col-sm-4">
                    <!-- <input type="text" name="height" class="form-control valid_height_width" id="height" onChange="loadPStyle();changeHeight();"
                           onKeyup="masked_two_decimal(this);loadPStyle()" min="1" required> -->

                    <input type="text" name="height" class="form-control valid_height_width" id="height" onChange="loadPStyle();changeHeight();" onKeyup="masked_two_decimal(this);" min="1" required>
                </div>
                <div class="col-sm-2">
                    <?php if ($company_profile[0]->unit == 'inches') { ?>
                        <select class="form-control " name="height_fraction_id" id="height_fraction_id" onKeyup="loadPStyle()" onChange="loadPStyle()" data-placeholder='-- Select one --'>
                            <option value="">--Select one--</option>
                            <?php
                            foreach ($fractions as $f) {
                                echo "<option value='$f->id'>$f->fraction_value</option>";
                            }
                            ?>
                        </select>
                    <?php  } ?>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12" id="ssssttt14" style="margin: 0px;"></div>
        <div class="form-group col-md-12" style="margin: 0px;">
        </div>
        <!-- atributs area -->
        <div class="form-group col-md-12" id="attr">
        </div>
        <!-- End atributs area -->
        <?php if ($user_detail->is_hide_room != 1) { ?>
            <div class="form-group col-md-12">
                <div class="row">
                    <?php $room_label = $custom_label['order_room_label']; ?>
                    <label for="" class="col-sm-2"><span class="custom_room_label"><?= $room_label ?></span></label>
                    <div class="col-sm-6">
                        <input type="hidden" name="room_index" id="room_index">
                        <select class="form-control select2-single" name="room" id="room" <?php echo (!empty($user_detail->room_require) ? 'required' : '') ?> data-placeholder="--Select one --">
                            <option value="">--- Select one ---</option>
                            <?php
                            foreach ($rooms as $r) {
                                echo '<option value="' . $r->room_name . '">' . $r->room_name . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <a href="javascript:void(0)" id="new_room_anchor" onclick="new_room_modal()" style="white-space:nowrap;" class="btn btn-success"><i class="fa fa-plus"></i></a>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (@$user_detail->display_upload_window_image) { ?>

            <div class="form-group col-md-12 upload-image-section">
                <div class="row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-6">
                        <div class="custom-control custom-checkbox" id="upload_checkbox_div">
                            <input type="checkbox" class="custom-control-input" name="upload_cart_image_check" value="1" id="upload_cart_image_check">
                            <label class="custom-control-label" for="upload_cart_image_check">Upload Window Image</label>
                        </div>
                        <div id="upload-image-div" class="d-none">
                            <span class="btn_upload">
                                <input type="file" id="select_cart_image" name="cart_qutation_image" title="" class="input-img" accept="image/x-png,image/gif,image/jpeg" />
                                Choose Image
                            </span>
                            <div id="preview-div">
                                <img id="ImgPreview" src="" class="preview show-border" alt="No Image Found!" onerror="this.src='<?php echo base_url('assets/no-image.png'); ?>'" />
                                <input type="hidden" id="selected_cart_quatation_image" name="selected_cart_quatation_image" value="" />
                                <input type="hidden" id="hid_selected_cart_quatation_image" name="hid_selected_cart_quatation_image" value="" />
                                <i id="removeImage1" class="fa fa-close btn-rmv1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="form-group col-md-12 special-instruction-section">
            <div class="row">
                <label class="col-sm-2">Special Instruction</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="notes" rows="6" id="notes" maxlength="150"></textarea>
                    <div class="the-count">
                        <span class="current">0</span>
                        <span class="maximum">/ 0</span>
                    </div>
                </div>
            </div>
        </div>
        <?php if (@$user_detail->special_instructions) { ?>
            <div class="form-group col-md-12 mb-2">
                <div class="row">
                    <label class="col-sm-2"></label>
                    <div class="col-sm-6">
                        <div class="custom-control custom-checkbox" id="special_note_checkbox_div">
                            <input type="checkbox" class="custom-control-input" name="special_notes_for_installer_check" value="1" id="special_notes_for_installer_check">
                            <label class="custom-control-label" for="special_notes_for_installer_check">Special Notes for Installer</label>
                        </div>
                        <div id="special_notes_for_installer_div" class="d-none">

                            <div class="col-sm-12">
                                <textarea class="form-control" name="special_notes_for_installer" rows="6" id="special_notes_for_installer" maxlength="150"></textarea>
                                <div class="the-count">
                                    <span class="current">0</span>
                                    <span class="maximum">/ 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="form-group col-md-11 mb-0 btns-box">
            <?php
            if ($view_action == 0) {
                //view, user can also add same product to list
            ?>
                <input type="hidden" name="hidden_crdleft_value" id="hidden_crdleft_value">
                <input type="hidden" name="hidden_routless_value" id="hidden_routless_value">
                <button type="button" class="btn btn-warning ml-2 float-right" id="cpyPrevOrder">Copy Previous Product</button>&nbsp;&nbsp;
                <button type="submit" class="btn btn-primary mb-2 float-right submit_cart_btn" id="cartbtn">Add Product to Order</button>
                <!-- <button type="button" class="btn btn-primary mb-2 float-right"  onclick="Cal_LoadPStyle()" >Calculate Price</button> -->

            <?php
            } else {
                //edit , user can update 
            ?>
                <input type="hidden" name="oldpro_id" id="old_product_id" value="<?php echo !empty($oldpro_id) ? $oldpro_id : ''; ?>">
                <input type="hidden" name="old_room" value="<?php echo !empty($old_room) ? $old_room : ''; ?>">
                <input type="hidden" name="update_product" value="<?php echo !empty($cart_rowid) ? $cart_rowid : ''; ?>">
                <button type="submit" class="btn btn-primary mb-0 float-right submit_cart_btn">Update</button>
            <?php } ?>
            <?php if (isset($order_edit_popup) && $order_edit_popup == 1) { ?>
                <button type="button" class="btn btn-info mr-2 mb-0 float-right" data-dismiss="modal">Cancel</button>
            <?php } else { ?>
                <button type="button" class="btn btn-info mb-0 mr-2 float-right" id="clrbtn" onclick="load_add_order_form('0', '0')">Cancel</button>
            <?php } ?>
        </div>
    </div>
    <input type="hidden" name="total_price" id="total_price">
    <input type="hidden" name="h_w_price" id="h_w_price">
    <input type="hidden" name="upcharge_price" id="upcharge_price">
    <input type="hidden" name="upcharge_label" id="upcharge_label">
    <input type="hidden" name="upcharge_details" id="upcharge_details">
    <input type="hidden" name="display_upcharge_details" id="display_upcharge_details">
    <input type="hidden" name="separate_display_upcharge_details" id="separate_display_upcharge_details">
    <input type="hidden" name="price_style_type" id="price_style_type">
    <input type="hidden" name="product_combo_or_not" id="product_combo_or_not">
    <input type="hidden" name="fix_price_value" id="fix_price_value">
    <input type="hidden" name="cart_rowid" id="cart_rowid" value="<?= $cart_rowid ?>">
    <input type="hidden" name="view_action" id="view_action" value="<?= $view_action ?>">
    <input type="hidden" name="selected_multioption_type" id="selected_multioption_type">
    <input type="hidden" name="selected_option_type" id="selected_option_type">
    <input type="hidden" name="selected_option_type_op_op" id="selected_option_type_op_op">
    <input type="hidden" name="selected_option_fifth" id="selected_option_fifth">
    <input type="hidden" name="edit_type" id="edit_type" value="<?= $edit_type ?>">
    <input type="hidden" name="phase2_housing_style" id="phase2_housing_style" value="">
    <input type="hidden" name="housingStyleAttributeName" id="housingStyleAttributeName" value="">
    <input type="hidden" name="discount" id="discount" value="<?php echo !empty($get_product_order_info->discount) ? $get_product_order_info->discount : '' ?>">
    <!-- <input type="hidden" name="qty" id="qty" value="<?php echo !empty($get_product_order_info->product_qty) ? $get_product_order_info->product_qty : '' ?>"> -->
    <input type="hidden" name="is_wholesaler_form" value="1"> <!-- NOTE :: only Use For ad/pro retailer and basic retialer invoice page edit order  -->
    <?= form_close(); ?>
</div>

<div class="col-sm-3 mt-2">

    <div class="sticky">
        <!-- For Drapery Price : START -->
        <div class="drapery_price_section drapery_price" style="display: none;">
            <div class="drape_price_div">
                <table>
                    <tr class="drape_width">
                        <td># of Cuts</td>
                        <td class="drape_width_price drapery_prices">0</td>
                        <input type="hidden" id="drape_width_price_round_val" />
                    </tr>
                    <tr class="drape_height">
                        <td>Cut Length</td>
                        <td class="drape_height_price drapery_prices">0</td>
                    </tr>
                    <tr class="drape_cuts">
                        <td>Total Fabric (Inches)</td>
                        <td class="drape_cuts_price drapery_prices">0</td>
                    </tr>
                    <tr class="drape_yard">
                        <td>Total Yards</td>
                        <td class="drape_yard_price drapery_prices">0</td>
                    </tr>
                    <tr class="drape_trim_yard">
                        <td>Trim Yards (Per Trim)</td>
                        <td class="drape_trim_yard_price drapery_prices">0</td>
                    </tr>
                    <tr class="drape_banding_yard">
                        <td>Banding Yards</td>
                        <td class="drape_banding_yard_price drapery_prices">0</td>
                    </tr>
                    <tr class="drape_flange_yard">
                        <td>Flange Yards</td>
                        <td class="drape_flange_yard_price drapery_prices">0</td>
                    </tr>
                    <tr class="drape_product">
                        <td>Product Price</td>
                        <td class="drape_product_price drapery_prices">0</td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- For Drapery Price : END -->

        <?php if ($user_detail->display_upcharges == 0) { ?>
            <div class="fixed_item display_fixed_item_section mb-2" style="display: none;">
                <div id="displayPrice"></div>
            </div>
            <div class="fixed_item fixed_item_section fixed_item" style="display: none;">
                <div id="tprice">
                    </p>
                </div>
            </div>
        <?php } ?>

        <!-- For Component Price : START -->
        <div class="component_price_section component_price" style="display: none;"></div>
        <!-- For Component Price : END -->

        <div class="fixed_item_section mt-3" id="attr-img-div">
        </div>


    </div>
</div>

<script>
    phase_2_attr = "";
    phase_2_up_id = "";
    get_attr_img = true;
    is_start_load_text_upcharge = false;

    function readURL(input, imgControlName) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(imgControlName).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // $(document).on("change", "#upload_cart_image_check", function(){
    $("#upload_cart_image_check").change(function() {

        if ($(this).prop("checked") == true) {
            $("#upload-image-div").removeClass("d-none");
            // $("#preview-div").removeClass("d-none");
            $("#select_cart_image").attr("required", true);
        } else {

            // $("#preview-div").addClass("d-none");
            $("#upload-image-div").addClass("d-none");
            $("#select_cart_image").removeAttr("required");
        }
    })

    $("#special_notes_for_installer_check").change(function() {
        if ($(this).prop("checked") == true) {
            $("#special_notes_for_installer_div").removeClass("d-none");
            // $("#preview-div").removeClass("d-none");
            $("#special_notes_for_installer").attr("required", true);
        } else {

            // $("#preview-div").addClass("d-none");
            $("#special_notes_for_installer_div").addClass("d-none");
            $("#special_notes_for_installer").removeAttr("required");
        }
    });

    $("#select_cart_image").change(function() {

        var fileInput = document.getElementById('select_cart_image');
        var filePath = fileInput.value;
        // Allowing file type
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.jfif|\.tif|\.tiff|\.bmp)$/i;

        if (!allowedExtensions.exec(filePath)) {
            alert('Invalid file type. Please select only image file.');
            fileInput.value = '';
            return false;
        } else {
            // $("#preview-div").removeClass("d-none");
            // add your logic to decide which image control you'll use
            var imgControlName = "#ImgPreview";
            readURL(this, imgControlName);
            $('#selected_cart_quatation_image').val('');
            $('.btn-rmv1').addClass('rmv-icon');
        }
    });

    $("#removeImage1").click(function(e) {
        e.preventDefault();
        $("#select_cart_image").val("");
        $("#selected_cart_quatation_image").val("");
        $("#select_cart_image").attr("required", true);

        $("#ImgPreview").attr("src", "");
        $('.btn-rmv1').removeClass('rmv-icon');
        // $("#preview-div").addClass("d-none");
    });

    <?php
    if (@$get_product_order_info->qutation_image != null && @$get_product_order_info->qutation_image != "") {

        if (!isset($get_product_order_info->upload_cart_image_check) && $get_product_order_info->qutation_image) {
    ?>
            $("#hid_selected_cart_quatation_image").val("<?php echo $get_product_order_info->qutation_image; ?>");
        <?php
            $get_product_order_info->qutation_image = base_url($get_product_order_info->qutation_image);
        }
        ?>
        $("#upload_cart_image_check").prop("checked", true);
        $("#upload-image-div").removeClass("d-none");
        // $("#preview-div").removeClass("d-none");
        $("#select_cart_image").attr("required", true);
        // $("#ImgPreview").attr("src", "<?php if (!preg_match('/^(?:[data]{4}:(text|image|application)\/[a-z]*)/', $get_product_order_info->qutation_image)) {
                                                echo base_url($get_product_order_info->qutation_image);
                                            } else {
                                                echo $get_product_order_info->qutation_image;
                                            }  ?>");
        $("#ImgPreview").attr("src", "<?php echo $get_product_order_info->qutation_image;  ?>");
        $("#selected_cart_quatation_image").val("<?php echo $get_product_order_info->qutation_image; ?>");

        $('.btn-rmv1').addClass('rmv-icon');
        $("#select_cart_image").removeAttr("required");
    <?php
    }
    ?>
</script>

<script>
    $(document).ready(function() {
        var product_id = $('#product_id').val();
        if (product_id == null) {
            var category_id = $('#category_id').val();
            onCategoryChange(category_id);
        }
    });
    // function Cal_LoadPStyle()
    // {
    //     $("body .fixed_item_section").hide();
    // $("body .display_fixed_item_section").hide();
    // loadPStyle1();
    // cal1();

    // }

    $(".submit_cart_btn").on('click', function(e) {
        console.log("here submit_cart_btn");
        // loadPStyle1();
        // cal1();
        // Cal_LoadPStyle();
        $("body .fixed_item_section").hide();
        $("body .display_fixed_item_section").hide();


        var depth = $('.convert_text_fraction').val();
        var fraction_val = $('select.select_text_fraction').val();
        if ((depth == 1) && (fraction_val == '')) {
            swal.fire("If IM <= 1 3/4inch, can't do IM");
            $('.convert_text_fraction').val('');
            $('select.select_text_fraction').prop('selectedIndex', 0);
            return false;
        }

        var customer_id = $("#customer_id").val();
        var side_mark_f_name = $("#side_mark_f_name").val();
        if (customer_id == '') {
            $(".customer-error").removeClass('d-none');
            $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            return false;
        } else if (side_mark_f_name.length < 4) {
            $(".side_mark_f_name-error").removeClass('d-none');
            $('html, body').animate({
                scrollTop: "0px"
            }, 800);
            return false;
        } else {
            return true;
        }

    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////-------category product pattern change events starts | Height and width keyup / change events starts----------->
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $("#category_id").change(function() {
        //$('body').on('change', '#category_id', function (event) {

        var category_id = $(this).val();
        $("#cartbtn").attr('disabled', 'true');
        $(".drapery_price_section").hide();
        $("#is_drapery_cat").val(0)
        onCategoryChange(category_id);
        <?php if (@$user_detail->phase_2_ordering) { ?>
            get_phase_2_conditions();
        <?php } ?>
    });
    //$("#sub_category_id").change(function(){
    //$('body').on('change', '#sub_category_id', function (event) {
    $(document).on('change', '#sub_category_id', function() {

        var sub_category_id = $(this).val();
        if ((sub_category_id != 'undefined') && (sub_category_id != null) && (sub_category_id != '')) {
            // $("#cartbtn").attr('disabled', 'true');
            onSubCategoryChange(sub_category_id);
        }
    });
    //$('body').on('change', '#product_id', function (event) {
    $("#product_id").change(function() {
        console.log("Inside1");
        $("#old_product_id").val("");
        var product_id = $(this).val();
        $("#cartbtn").attr('disabled', 'true');
        onProductChange(product_id);
    });
    $(document).on('change', '#pattern_id', function() {
        //$('body').on('change', '#pattern_id', function (event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        var pattern_id = $(this).val();
        $("#cartbtn").attr('disabled', 'true');
        OnPatternChange(pattern_id);
        loadPStyle();
    });
    //$(document).on('change', '#width', function() {
    $('body').on('keyup', '#width', function() {

        $("#cartbtn").attr('disabled', 'true');

        var hif = $(this).val().split(".")[1];
        var category_id = $('#category_id').val();
        if (hif) {
            $.ajaxQueue({
                url: "<?php echo base_url(); ?>b_level/order_controller/get_height_width_fraction/" + hif +
                    "/" + category_id,
                type: 'get',
                success: function(r) {
                    $("#width_fraction_id ").val(r);
                }
            });
        } else {
            //$("#width_fraction_id ").val('');
        }
        get_fabric_price($("#product_id").val(), $("#pattern_id").val());
    });
    $('body').on('keyup', '#height', function() {

        $("#cartbtn").attr('disabled', 'true');
        var hif = $(this).val().split(".")[1];
        if (hif) {
            $.ajaxQueue({
                url: "<?php echo base_url(); ?>b_level/order_controller/get_height_width_fraction/" + hif,
                type: 'get',
                success: function(r) {
                    $("#height_fraction_id ").val(r);
                }
            });
        } else {
            // $("#height_fraction_id ").val('');
        }
        get_fabric_price($("#product_id").val(), $("#pattern_id").val());
    });
    $('body').on('change', function() {


        var wwidth = $('#width').val();
        var hheight = $('#height').val();
        var unit_type = $('#unit_type').val();
        if (typeof wwidth !== "undefined") {
            //  $('#width').val($('#width').val().split(".")[0]);
            if (unit_type == 'inches') {
                $('#width').val($('#width').val().split(".")[0]);
            } else {
                $('#width').val($('#width').val());
            }
        }
        if (typeof hheight !== "undefined") {
            if (unit_type == 'inches') {
                $('#height').val($('#height').val().split(".")[0]);
            } else {
                $('#height').val($('#height').val());
            }
        }
    });


    // Get fractiob value based on enter value in text box for text+fraction type : START
    // $('body').on('keyup', '.convert_text_fraction', function () {
    $('body').on('change', '.convert_text_fraction', function() { // As confirm with khushbu change "keyup" to "change" Event because of fraction issue

        $("#cartbtn").attr('disabled', 'true');
        masked_two_decimal(this);
        var text_val = (isNaN($(this).val()) ? '' : $(this).val());
        var attr_option_key = $(this).attr('data-op_op_key');
        var _this = $(this);
        if (text_val) {
            var hif = text_val.split(".")[1];
            var category_id = $('#category_id').val();
            if (hif) {
                $.ajaxQueue({
                    url: "<?php echo base_url(); ?>b_level/order_controller/get_height_width_fraction/" + hif +
                        "/" + category_id,
                    type: 'get',
                    success: function(r) {
                        // $(".key_text_fraction_"+attr_option_key).val(r);
                        _this.parent().parent('.row').find('.select_text_fraction').val(r);
                    }
                });
            } else {
                // $(".key_text_fraction_"+attr_option_key).val('');
                _this.parent().parent('.row').find('.select_text_fraction').val('');
            }
        } else {
            $(this).val(text_val);
            // $(".key_text_fraction_"+attr_option_key).val('');
            _this.parent().parent('.row').find('.select_text_fraction').val('');
        }
    });

    $('body').on('blur', '.convert_text_fraction', function() {

        $(this).val($(this).val().split(".")[0]);
    });
    // Get fractiob value based on enter value in text box for text+fraction type : END

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////-------category product pattern change events starts | Height and width keyup / change events End----------->
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    function getColorCode(id, comboCount = 0) {
        $("#manual_color_div").remove();
        if (id != '') {
            if (id > 0) {
                var fromWholesaler = "<?php echo !empty($wholesaler_data) ? $wholesaler_data : ''  ?>";
                if (fromWholesaler == 1)
                    var submit_url = "<?= base_url(); ?>b_level/order_controller/get_color_code_wholesaler/" + id;
                else
                    var submit_url = "<?= base_url(); ?>b_level/order_controller/get_color_code/" + id;
                $.ajaxQueue({
                    type: 'GET',
                    url: submit_url,
                    success: function(res) {
                        if (comboCount > 0)
                            $('#colorcode_' + comboCount).val(res).show();
                        else
                            $('#colorcode').val(res).show();
                    }
                });
            } else {

                if ($("#pattern_id").val() > 0 && $('body #pattern_color_model').children().length > 0) {
                    html = '<div class="row" id="manual_color_div">\
                    <label for="" class="col-sm-2"><span class="">Color Name</span></label>\
                    <div class="col-sm-6">\
                    <input type="text" class="form-control" placeholder="Manual Color Entry" name="manual_color_entry" required="true" id="manual_color_entry">\
                    </div>\
                    </div>';
                    $("#pattern_color_model").append(html);

                    if (comboCount > 0) {
                        $('#colorcode_' + comboCount).val('').hide();
                    } else {
                        $('#colorcode').val('').hide();
                    }
                }
            }
        } else {
            // Added by insys to avoid unwanted ajax call rest is same just add if condition if id is empty or not.
            if (comboCount > 0) {
                $('#colorcode_' + comboCount).val('').show();
            } else {
                $('#colorcode').val('').show();
            }
        }
    }

    function getColorCode_select(keyword, comboCount = 0) {
        var pattern_id = $("#pattern_id").val();
        var fromWholesaler = "<?php echo !empty($wholesaler_data) ? $wholesaler_data : ''  ?>";
        if (keyword !== '') {
            if (fromWholesaler == 1)
                var submit_url = "<?= base_url(); ?>b_level/order_controller/get_color_code_select_wholesaler/" + keyword + '/' + pattern_id;
            else
                var submit_url = "<?= base_url(); ?>b_level/order_controller/get_color_code_select/" + keyword + '/' + pattern_id;
            $.ajaxQueue({
                type: 'GET',
                url: submit_url,
                success: function(res) {
                    if (comboCount > 0)
                        $('.combo_color_id_' + comboCount).val(res);
                    else
                        $('#color_id').val(res);
                }
            });
        }
    }

    $("#width").blur(function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        var att_id = $('.op_op_load').data('id');
        var val = $('.options_' + att_id).val();
        // OptionOptions(val,att_id);
        //  $("#cartbtn").attr('disabled', 'true');
        // To preserve option type values on width blur event : START

        // for option type : 4 Multioption :START
        var mul_op_op_op_selected = [];
        $('.mul_op_op_op').each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            mul_op_op_op_selected.push($(this).val());
            // }

        });

        $("#selected_multioption_type").val(mul_op_op_op_selected.join('@'));
        // for option type : 4 Multioption :END

        // for option type : 2 Option :START
        var option_type = [];
        $("select[class*='cls_op_']").each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            option_type.push($(this).val());
            // }

        });

        $("#selected_option_type").val(option_type.join('@'));

        // for option type : 2 Option :END

        // for option type : 2 Option op op :START
        var option_type_op_op = [];
        $("select[class*='cls_op_op_']").each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            option_type_op_op.push($(this).val());
            // }

        });

        $("#selected_option_type_op_op").val(option_type_op_op.join('@'));

        // for option type : 2 Option op op :END


        // for option type : fifth level option :START
        var option_op_five = [];
        $("select[class*='cls_op_five_']").each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            option_op_five.push($(this).val());
            // }

        });

        $("#selected_option_fifth").val(option_op_five.join('@'));


        // for option type : fifth level option :END

        // To preserve option type values on width blur event : END
        //  $('.overlay').show();
        //setTimeout(() => {
        $(".op_op_load").each(function() {
            var att_id = jQuery(this).data('id');
            var val = $('.options_' + att_id).val();
            var data_attr_name = jQuery(this).attr('data-attr-name');
            if (val != '' && jQuery("#width").val() != '' && jQuery("#width").val() != 0) {
                // OptionOptions(val,att_id);
                if (data_attr_name != 'Control Type') {
                    setTimeout(() => {
                        OptionOptions(val, att_id);
                    }, 1000);
                } else {
                    // For Motorized Accessories : START
                    if ($(".custom_multi_select.selectpicker").length > 0) {
                        var mot_acc_attr_id = '';
                        $(".custom_multi_select.selectpicker").each(function() {
                            if ($(this).data("attr-name") == 'Motorized Accessories') {
                                mot_acc_attr_id = $(this).attr('id');
                            }
                        });
                        if (mot_acc_attr_id != '') {
                            var mot_arr = new Array();
                            if ($('#' + mot_acc_attr_id).val().length > 0) {
                                $('#' + mot_acc_attr_id + ' > option').each(function() {

                                    mot_arr.push($(this).val());

                                });
                            }
                        }

                    }
                    // For Motorized Accessories : END 
                    setTimeout(() => {
                        MultiOptionOptionsOptionOptions('#' + mot_acc_attr_id, att_id);
                    }, 1000);
                }
            }
        });
    });

    // For housing style : START 
    /*  $( "#width_fraction_id , #height_fraction_id" ).change(function() {
          $("#cartbtn").attr('disabled', 'true');
          var att_id = $('.op_op_load').data('id');
          var val = $('.options_'+att_id).val();
          OptionOptions(val,att_id);
      });*/
    // For housing style : END
    
    $("#width_fraction_id").change(function(event) {
        $("#cartbtn").attr('disabled', 'true');
        var att_id = $('.op_op_load').data('id');
        var val = $('.options_' + att_id).val();
        // OptionOptions(val,att_id);
        $('#width').trigger('blur');
    });

    $("#height_fraction_id").change(function(event) {

        $("#cartbtn").attr('disabled', 'true');
        var att_id = $('.op_op_load').data('id');
        var val = $('.options_' + att_id).val();
        //  OptionOptions(val,att_id);
        $('#height').trigger('blur');
    });


    $("#height").blur(function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        // $('#loader-container').fadeIn();
        // var att_id = $('.op_op_load').data('id');
        // var val = $('.options_'+att_id).val();
        // OptionOptions(val,att_id);
        // $("#cartbtn").attr('disabled', 'true');
        //  $('.overlay').show();
        //setTimeout(() => {
        // To preserve option type values on height blur event : START

        // for option type : 4 Multioption :START
        var mul_op_op_op_selected = [];
        $('.mul_op_op_op').each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            mul_op_op_op_selected.push($(this).val());
            // }

        });

        $("#selected_multioption_type").val(mul_op_op_op_selected.join('@'));
        // for option type : 4 Multioption :END

        // for option type : 2 Option :START
        var option_type = [];
        $("select[class*='cls_op_']").each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            option_type.push($(this).val());
            // }

        });

        $("#selected_option_type").val(option_type.join('@'));

        // for option type : 2 Option :END

        // for option type : 2 Option op op :START
        var option_type_op_op = [];
        $("select[class*='cls_op_op_']").each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            option_type_op_op.push($(this).val());
            // }

        });

        $("#selected_option_type_op_op").val(option_type_op_op.join('@'));

        // for option type : 2 Option op op :END


        // for option type : fifth level option :START
        var option_op_five = [];
        $("select[class*='cls_op_five_']").each(function() {
            // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
            // {
            option_op_five.push($(this).val());
            // }

        });

        $("#selected_option_fifth").val(option_op_five.join('@'));


        // for option type : fifth level option :END

        // To preserve option type values on height blur event : END

        $(".op_op_load").each(function() {
            var att_id = jQuery(this).data('id');
            var val = $('.options_' + att_id).val();
            var data_attr_name = jQuery(this).attr('data-attr-name');
            if (val != '' && jQuery("#height").val() != '' && jQuery("#height").val() != 0) {
                // OptionOptions(val,att_id);
                if (data_attr_name != 'Control Type') {
                    setTimeout(() => {
                        OptionOptions(val, att_id);
                    }, 1000);
                } else {

                    // For Motorized Accessories : START
                    if ($(".custom_multi_select.selectpicker").length > 0) {
                        var mot_acc_attr_id = '';
                        $(".custom_multi_select.selectpicker").each(function() {
                            if ($(this).data("attr-name") == 'Motorized Accessories') {
                                mot_acc_attr_id = $(this).attr('id');
                            }
                        });
                        if (mot_acc_attr_id != '') {
                            var mot_arr = new Array();
                            if ($('#' + mot_acc_attr_id).val().length > 0) {
                                $('#' + mot_acc_attr_id + ' > option').each(function() {

                                    mot_arr.push($(this).val());

                                });
                            }
                        }

                    }
                    // For Motorized Accessories : END 
                    setTimeout(() => {
                        MultiOptionOptionsOptionOptions('#' + mot_acc_attr_id, att_id);
                    }, 1000);
                }
            }
        });

        setTimeout(() => {
            $('.overlay').hide();
        }, 1000);

        var remote_value = '';
        $(".mul_op_op_op").each(function() {
            var remote_value = $(this).val();
            if ((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')) {
                multiOptionPriceValue(remote_value);
            }
        });
    });
    $("#customer_id").on('change', function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        $("#cartbtn").attr('disabled', 'true');
        var att_id = $('.op_op_load').data('id');
        var val = $('.options_' + att_id).val();
        if (att_id != "undefined" && val != "undefined") {
            OptionOptions(val, att_id);
        }
    });
    $("#cartbtn").click(function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var cordLiftValue = $("#custom-value-6").html();
        var routlessValue = $("#custom-value-8").html();

        $("#hidden_crdleft_value").val(cordLiftValue);
        $("#hidden_routless_value").val(routlessValue);
    });


    $("body").on('keyup', "#width", function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        // If width >=72 and tube attribute available then select 38mm default : START
        $("#cartbtn").attr('disabled', 'true');
        var width = $("#width").val();
        if (width >= 72 && $("select[data-attr-name='Tube']").length > 0) {
            $("select[data-attr-name='Tube'] option").each(function() {
                var tube_text = $(this).text();
                var match_text = tube_text.substring(0, 4);
                if (match_text == '38mm') {
                    var tube_val = $(this).val();
                    $("select[data-attr-name='Tube']").val(tube_val);
                    var att_id = $('.op_op_load').data('id');
                    // OptionOptions(tube_val,att_id);
                }
            });
        } else if ($("select[data-attr-name='Control Type'] option:selected").text() != 'Motorized') {
            $("select[data-attr-name='Tube'] option").each(function() {
                var tube_text = $(this).text();
                var match_text = tube_text.substring(0, 4);
                if (match_text == '32mm') {
                    var tube_val = $(this).val();
                    $("select[data-attr-name='Tube']").val(tube_val);
                    var att_id = $('.op_op_load').data('id');
                    // OptionOptions(tube_val,att_id);
                }
            });
        }
        // If width >=72 and tube attribute available then select 38mm default otherwise select 32mm : END
    });

    $("body").on('change', "#height", function() {

        $("#cartbtn").attr('disabled', 'true');
        select_contorl_length_option();
    });

    $('body').on('change', '#height_fraction_id', function() {

        $("#cartbtn").attr('disabled', 'true');
        select_contorl_length_option();
    });

    // For control length option select default value based on the height value : START
    function select_contorl_length_option() {
        var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text().split("/")[1]);
        var height = parseFloat($('#height').val()) + (isNaN(hif) ? 0 : hif);
        var height_w = (isNaN(height) ? '' : height);

        if ($("select[data-attr-name='Control Length']").length > 0) {
            var company_unit = '<?= isset($company_profile[0]->unit) ? $company_profile[0]->unit : ""; ?>';
            var control_length_val = 'Cord Length';

            if (company_unit == 'inches') {
                // If company_unit is inches then consider this option.
                if (height_w >= 55 && height_w <= 65) {
                    control_length_val = '36"';
                } else if (height_w > 65 && height_w <= 75) {
                    control_length_val = '48"';
                } else if (height_w > 75 && height_w <= 85) {
                    control_length_val = '54"';
                } else if (height_w > 85 && height_w <= 95) {
                    control_length_val = '64"';
                }
            } else {
                // If company_unit is cm then consider this option.
                if (height_w >= 139.7 && height_w <= 165.1) {
                    control_length_val = '91';
                } else if (height_w > 165.1 && height_w <= 190.5) {
                    control_length_val = '122';
                } else if (height_w > 190.5 && height_w <= 215.9) {
                    control_length_val = '137';
                } else if (height_w > 215.9 && height_w <= 241.3) {
                    control_length_val = '163';
                }
            }
            $("select[data-attr-name='Control Length'] option").each(function() {
                var c_length_text = $(this).text();
                if (c_length_text == control_length_val) {
                    var c_length_val = $(this).val();
                    $("select[data-attr-name='Control Length']").val(c_length_val);
                }
            });

        }
    }
    // For control length option select default value based on the height value : END

    // function get_product_row_col_price(callback = false) {

    //     var product_id   = $('#product_id').val();
    //     var pattern_id   = $('#pattern_id').val();
    //     var pricestyle   = $('#pricestyle').val();
    //     var productType  = $('#product_combo_or_not').val();
    //     var price = $("#height").parent().parent().parent().next();
    //     var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text()
    //             .split("/")[1]);
    //     var wif = ($("#width_fraction_id :selected").text().split("/")[0] / $("#width_fraction_id :selected").text().split(
    //             "/")[1]);
    //     var width = parseInt($('#width').val()) + (isNaN(wif) ? 0 : wif);
    //     var height = parseInt($('#height').val()) + (isNaN(hif) ? 0 : hif);
    //     var width_w = (isNaN(width) ? '' : width);
    //     var height_w = (isNaN(height) ? '' : height);
    //     if (pricestyle !== '2') {

    //         var comboProductArray  = [];
    //         var comboProductPatternArray  = [];
    //         if(productType == 1){
    //             for (var i = 1; i <= comboLength; i++) {
    //                 comboProductArray.push($('.combo_product_id_'+i).val());                
    //                 comboProductPatternArray.push($('.combo_fabric_id_'+i).val());                
    //             }
    //         }
    //         var comboProductIds = comboProductArray;
    //         var comboPatternIds = comboProductPatternArray;

    //         if(height_w !== '' && width_w !== '') {
    //             $.ajaxQueue({
    //                 url: "<?php echo base_url('b_level/order_controller/get_product_row_col_price/') ?>" + height_w +
    //                         "/" + width_w + "/" + product_id + "/" + pattern_id + "/" + productType,
    //                 type: 'POST',
    //                 async: false,
    //                 data : { comboProductIds : comboProductIds,comboPatternIds : comboPatternIds},
    //                 success: function (r) {
    //                     var obj = jQuery.parseJSON(r);
    //                     $(price).html(obj.ht);
    //                     if (isNaN(parseFloat(obj.prince))) {
    //                         $("#tprice").text("");
    //                     } else {
    //                         $("#tprice").text("");
    //                     }

    //                     $("body .fixed_item_section").hide();

    //                     if (obj.st === 1) {
    //                         $('#cartbtn').removeAttr('disabled');
    //                     } else if (obj.st === 2) {
    //                         //$('#cartbtn').prop('disabled', true);
    //                     }
    //                     if (callback != false) {
    //                         callback();
    //                     }
    //                     setTimeout(function(){ cal(); }, 2000);
    //                 }
    //             });
    //         }
    //     }
    // }

    // function loadPStyle1(callback = false) {
    //     //alert('hi');
    //     var product_id   = $('#product_id').val();
    //     var pattern_id   = $('#pattern_id').val();
    //     var pricestyle   = $('#pricestyle').val();
    //     var productType  = $('#product_combo_or_not').val();
    //     var comboLength  = $(".combo-product-section").length;
    //     var comboProductArray  = [];
    //     var comboProductPatternArray  = [];
    //     if(productType == 1){
    //         for (var i = 1; i <= comboLength; i++) {
    //             comboProductArray.push($('.combo_product_id_'+i).val());                
    //             comboProductPatternArray.push($('.combo_fabric_id_'+i).val());                
    //         }
    //     }
    //     var comboProductIds = comboProductArray;
    //     var comboPatternIds = comboProductPatternArray;
    //     var price = $("#height").parent().parent().parent().next();
    //     var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text()
    //             .split("/")[1]);
    //     var wif = ($("#width_fraction_id :selected").text().split("/")[0] / $("#width_fraction_id :selected").text().split(
    //             "/")[1]);
    //     var width = parseInt($('#width').val()) + (isNaN(wif) ? 0 : wif);
    //     var height = parseInt($('#height').val()) + (isNaN(hif) ? 0 : hif);
    //     var width_w = (isNaN(width) ? '' : width);
    //     var height_w = (isNaN(height) ? '' : height);
    //     var is_drapery_cat = $("#is_drapery_cat").val();
    //     var is_component_category = $("#is_component_category").val();

    //     if($("#manual_fabric_price").length > 0)
    //     {
    //         var fabric_price = $("#manual_fabric_price").val() ? $("#manual_fabric_price").val() : null;
    //     } else {

    //         var fabric_price = $("#fabric_price").val() ? $("#fabric_price").val() : null;
    //     }

    //     if(is_drapery_cat == 1) {
    //         // If Drapery then assign the Drapery product price
    //         if (callback != false) {
    //            // callback();
    //         }
    //         calculate_drapery_price(); // Call for Calculate Drapery price
    //        // cal();
    //        //setTimeout(function(){ cal(); }, 2000);
    //     } else if(is_component_category == 1) {
    //         // If Component category then no need to for calculation
    //         if (callback != false) {
    //             //callback();
    //         }
    //     } else if (pricestyle !== '2') {
    //         // if(height_w != '' && width_w != '') {
    //             var catName = $("#category_id :selected").text();
    //             if(catName == "Arch" || catName == "Shades")
    //             {
    //                 $.ajaxQueue({
    //                     url: "<?php echo base_url('b_level/order_controller/get_product_row_col_price/') ?>" + height_w +
    //                             "/" + width_w + "/" + product_id + "/" + pattern_id + "/" + productType,
    //                     type: 'POST',
    //                     async: false,
    //                     //data : { comboProductIds : comboProductIds,comboPatternIds : comboPatternIds, fabric_price : fabric_price},         // fabric_price used for Fabric formula price, price style = 10
    //                     data : $("form.frm_product_order_form").serialize() + "&comboProductIds=" + comboProductIds + "&comboPatternIds=" + comboPatternIds + "&fabric_price = " + fabric_price,
    //                     // data : "&comboProductIds=" + comboProductIds + "&comboPatternIds=" + comboPatternIds + "&fabric_price = " + fabric_price,

    //                     success: function (r) {
    //                         var obj = jQuery.parseJSON(r);
    //                         $(price).html(obj.ht);
    //                         if (isNaN(parseFloat(obj.prince))) {
    //                             // $("#tprice").text("Total Price = $0");
    //                             $("#tprice").text("");
    //                         } else {
    //                             // $("#tprice").text("Total Price = $" + parseFloat(obj.prince));
    //                             $("#tprice").text("");
    //                         }

    //                         $("body .fixed_item_section").hide();
    //                         $("body .display_fixed_item_section").hide();

    //                         $("#sqr_val").val(obj.area)
    //                         // $("#height").val(obj.col);
    //                         if (obj.st === 1) {
    //                             $('#cartbtn').removeAttr('disabled');
    //                         } else if (obj.st === 2) {
    //                             //$('#cartbtn').prop('disabled', true);
    //                         }
    //                         // if (callback != false) {
    //                         //     callback();
    //                         // }
    //                         if (callback != false && typeof callback === 'function') {
    //                             callback();
    //                         }
    //                     }  // I added this because of bracket missing while taking pull from master and because of this new order page not working.
    //                 }); 
    //             }
    //             else
    //             {
    //                 if(height_w >= 0 && width_w >= 0 && height_w != '' && width_w != '') {
    //                     $.ajaxQueue({
    //                         url: "<?php echo base_url('b_level/order_controller/get_product_row_col_price/') ?>" + height_w +
    //                                 "/" + width_w + "/" + product_id + "/" + pattern_id + "/" + productType,
    //                         type: 'POST',
    //                         async: false,
    //                         // data : { comboProductIds : comboProductIds,comboPatternIds : comboPatternIds, fabric_price : fabric_price},         // fabric_price used for Fabric formula price, price style = 10
    //                         data : $("form.frm_product_order_form").serialize() + "&comboProductIds=" + comboProductIds + "&comboPatternIds=" + comboPatternIds + "&fabric_price = " + fabric_price,

    //                         success: function (r) {
    //                             var obj = jQuery.parseJSON(r);
    //                             $(price).html(obj.ht);
    //                             if (isNaN(parseFloat(obj.prince))) {
    //                                 // $("#tprice").text("Total Price = $0");
    //                                 $("#tprice").text("");
    //                             } else {
    //                                 // $("#tprice").text("Total Price = $" + parseFloat(obj.prince));
    //                                 $("#tprice").text("");
    //                             }

    //                             $("body .fixed_item_section").hide();
    //                             $("body .display_fixed_item_section").hide();

    //                             $("#sqr_val").val(obj.area)
    //                             // $("#height").val(obj.col);
    //                             if (obj.st === 1) {
    //                                 $('#cartbtn').removeAttr('disabled');
    //                             } else if (obj.st === 2) {
    //                                 //$('#cartbtn').prop('disabled', true);
    //                             }
    //                             if (callback != false) {
    //                                 //callback();
    //                             }
    //                         // cal();
    //                         //setTimeout(function(){ cal(); }, 2000);
    //                         }
    //                     });   
    //                 }
    //             }

    //         // }

    //         // if (callback != false) {
    //         //     callback();
    //         // }
    //     } else if (pricestyle === '2') {
    //         var main_p = parseFloat($('#sqr_price').val());
    //         if (isNaN(main_p)) {
    //             var main_price = 0;
    //         } else {
    //             var main_price = main_p;
    //         }

    //         var new_width = parseFloat(width + 3);
    //         var new_height = parseFloat(height + 1.5);

    //         var sum = (new_width * new_height) / 144;

    //         // var sum = (width * height) / 144;
    //         var price = main_price * sum;
    //         $('#main_price').val(price.toFixed(2));
    //         if (callback != false) {
    //             //callback();
    //         }
    //        // cal();
    //        //setTimeout(function(){ cal(); }, 2000);
    //     } else {
    //         if (callback != false) {
    //             //callback();
    //         }
    //         // cal();
    //         //setTimeout(function(){ cal(); }, 2000);
    //     }
    //     setTimeout(function(){ 
    //         if (callback != false) {
    //             callback();
    //         }

    //      }, 2000);
    //     setTimeout(function(){ cal(); }, 2000);

    // }
    function loadPStyle(callback = false) {
        // debugger;
        // if ($('#old_product_id').val() != "") {
        var product_id = $('#product_id').val();
        // } else {
        //     var product_id = $('#old_product_id').val();
        // }
        // console.log("poductID:" + product_id);
        var pattern_id = $('#pattern_id').val();
        var pricestyle = $('#pricestyle').val();
        // console.log("pricestyle:" + pricestyle);
        var productType = $('#product_combo_or_not').val();
        var comboLength = $(".combo-product-section").length;
        var comboProductArray = [];
        var comboProductPatternArray = [];
        if (productType == 1) {
            for (var i = 1; i <= comboLength; i++) {
                comboProductArray.push($('.combo_product_id_' + i).val());
                comboProductPatternArray.push($('.combo_fabric_id_' + i).val());
            }
        }
        var comboProductIds = comboProductArray;
        var comboPatternIds = comboProductPatternArray;
        var price = $("#height").parent().parent().parent().next();
        var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text()
            .split("/")[1]);
        var wif = ($("#width_fraction_id :selected").text().split("/")[0] / $("#width_fraction_id :selected").text().split(
            "/")[1]);
        var width = parseInt($('#width').val()) + (isNaN(wif) ? 0 : wif);
        var height = parseInt($('#height').val()) + (isNaN(hif) ? 0 : hif);
        var width_w = (isNaN(width) ? '' : width);
        var height_w = (isNaN(height) ? '' : height);
        if (width_w == "") {
            width_w = 0;
        }
        if (height_w == "") {
            height_w = 0;
        }
        var is_drapery_cat = $("#is_drapery_cat").val();
        var is_component_category = $("#is_component_category").val();

        if ($("#manual_fabric_price").length > 0) {
            var fabric_price = $("#manual_fabric_price").val() ? $("#manual_fabric_price").val() : null;
        } else {

            var fabric_price = $("#fabric_price").val() ? $("#fabric_price").val() : null;
        }

        if (is_drapery_cat == 1) {
            // If Drapery then assign the Drapery product price
            if (callback != false) {
                // callback();
            }
            calculate_drapery_price(); // Call for Calculate Drapery price
            // cal();
            //setTimeout(function(){ cal(); }, 2000);
        } else if (is_component_category == 1) {
            // If Component category then no need to for calculation
            if (callback != false) {
                //callback();
            }
        } else if (pricestyle !== '2') {
            // if(height_w != '' && width_w != '') {
            var catName = $("#category_id :selected").text();
            if ((catName == "Arch" || catName == "Shades") && (height_w != 0 && width_w != 0)) {
                URL = "<?php echo base_url('b_level/order_controller/get_product_row_col_price/') ?>" + height_w +
                    "/" + width_w + "/" + product_id + "/" + pattern_id + "/" +  ;
                // console.log("URL:" + URL);
                $.ajaxQueue({
                    url: URL,
                    type: 'POST',
                    async: false,
                    //data : { comboProductIds : comboProductIds,comboPatternIds : comboPatternIds, fabric_price : fabric_price},         // fabric_price used for Fabric formula price, price style = 10
                    data: $("form.frm_product_order_form").serialize() + "&comboProductIds=" + comboProductIds + "&comboPatternIds=" + comboPatternIds + "&fabric_price = " + fabric_price,
                    // data : "&comboProductIds=" + comboProductIds + "&comboPatternIds=" + comboPatternIds + "&fabric_price = " + fabric_price,

                    success: function(r) {
                        var obj = jQuery.parseJSON(r);
                        // console.log("HT Object1:" + obj.ht);

                        $(price).html(obj.ht);
                        if (isNaN(parseFloat(obj.prince))) {
                            // $("#tprice").text("Total Price = $0");
                            $("#tprice").text("");
                        } else {
                            // $("#tprice").text("Total Price = $" + parseFloat(obj.prince));
                            $("#tprice").text("");
                        }

                        $("body .fixed_item_section").hide();
                        $("body .display_fixed_item_section").hide();

                        $("#sqr_val").val(obj.area)
                        // $("#height").val(obj.col);
                        if (obj.st === 1) {
                            $('#cartbtn').removeAttr('disabled');
                        } else if (obj.st === 2) {
                            $('#cartbtn').prop('disabled', true);
                        }
                        // if (callback != false) {
                        //     callback();
                        // }
                        if (callback != false && typeof callback === 'function') {
                            callback();
                        }
                    } // I added this because of bracket missing while taking pull from master and because of this new order page not working.
                });
            } else {
                if (height_w >= 0 && width_w >= 0 && height_w != '' && width_w != '') {
                    $.ajaxQueue({
                        url: "<?php echo base_url('b_level/order_controller/get_product_row_col_price/') ?>" + height_w +
                            "/" + width_w + "/" + product_id + "/" + pattern_id + "/" + productType,
                        type: 'POST',
                        async: false,
                        // data : { comboProductIds : comboProductIds,comboPatternIds : comboPatternIds, fabric_price : fabric_price},         // fabric_price used for Fabric formula price, price style = 10
                        data: $("form.frm_product_order_form").serialize() + "&comboProductIds=" + comboProductIds + "&comboPatternIds=" + comboPatternIds + "&fabric_price = " + fabric_price,

                        success: function(r) {
                            var obj = jQuery.parseJSON(r);
                            // console.log("HT Object2:" + obj.ht);
                            $(price).html(obj.ht);
                            if (isNaN(parseFloat(obj.prince))) {
                                // $("#tprice").text("Total Price = $0");
                                $("#tprice").text("");
                            } else {
                                // $("#tprice").text("Total Price = $" + parseFloat(obj.prince));
                                $("#tprice").text("");
                            }

                            $("body .fixed_item_section").hide();
                            $("body .display_fixed_item_section").hide();

                            $("#sqr_val").val(obj.area)
                            // $("#height").val(obj.col);
                            if (obj.st === 1) {
                                $('#cartbtn').removeAttr('disabled');
                            } else if (obj.st === 2) {
                                $('#cartbtn').prop('disabled', true);
                            }
                            if (callback != false) {
                                //callback();
                            }
                            // cal();
                            //setTimeout(function(){ cal(); }, 2000);
                        }
                    });
                }
            }

            // }

            // if (callback != false) {
            //     callback();
            // }
        } else if (pricestyle === '2') {
            var main_p = parseFloat($('#sqr_price').val());
            // console.log("main_p" + main_p);
            if (isNaN(main_p)) {
                var main_price = 0;
            } else {
                var main_price = main_p;
            }

            var new_width = parseFloat(width + 3);
            var new_height = parseFloat(height + 1.5);

            var sum = (new_width * new_height) / 144;

            // var sum = (width * height) / 144;
            var price = main_price * sum;
            $('#main_price').val(price.toFixed(2));
            if (callback != false) {
                //callback();
            }
            // cal();
            //setTimeout(function(){ cal(); }, 2000);
        } else {
            if (callback != false) {
                //callback();
            }
            // cal();
            //setTimeout(function(){ cal(); }, 2000);
        }
        setTimeout(function() {
            if (callback != false) {
                callback();
            }

        }, 2000);
        setTimeout(function() {
            cal();
        }, 2000);
    }

    function multiOptionPriceValue(att_op_op_op_op_id, callback = false) {
        //alert(att_op_op_op_op_id);
        var op_op_op_id = att_op_op_op_op_id.split("_")[0];
        var att = att_op_op_op_op_id.split("_")[1];
        var op_op_id = att_op_op_op_op_id.split("_")[2];
        if ($("#manual_fabric_price").length > 0) {
            var main_p = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_p = parseFloat($('#main_price').val());
        }
        if (isNaN(main_p)) {
            var main_price = 0;
        } else {
            var main_price = main_p;
        }

        // var mul_op_val = $("#mul_op_op_id" + op_op_id).val();
        // if (op_op_op_id && mul_op_val) {
        var selected_option_fifth = $("#selected_option_fifth").val();
        if (op_op_op_id) {

            var wrapper = $("#mul_op_op_id" + op_op_id).parent().next();
            //alert('multioption_price_value');
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/multioption_price_value/') ?>" + op_op_op_id +
                    "/" + att + "/" + main_price + "/" + selected_option_fifth,
                type: 'get',
                async: false,
                success: function(r) {

                    // For shades product fifth attributes : START
                    $(wrapper).show();
                    // For shades product fifth attributes : END


                    $(wrapper).html(r);
                    if (op_op_op_id == '4' && att == '18' && op_op_id == '13') {
                        $('body #mul_op_op_id14').val('12_18_14');

                    } else if (op_op_op_id == '5' && att == '18' && op_op_id == '13') {
                        $('body #mul_op_op_id14').val('22_18_14');

                    } else {
                        $('body #mul_op_op_id14').val('');
                    }

                    if (callback != false) {
                        callback();
                    }
                    //   cal();
                    setTimeout(function() {
                        cal();
                    }, 2000);
                }
            });


            var op_class = ".cls_op_five_" + att;
            var op_f_val = $(op_class).val();
            // alert(att);
            // alert(op_f_val);  
            if ((op_f_val != '') && (op_f_val != '0') && (op_f_val != 'undefined')) {
                OptionFive(op_f_val, att);
            }

            // Call apply_upcharges_condition function : START 
            var up_attribute_id = op_op_op_id;
            var up_level = 3;
            var up_class = 'mul_op_op_' + op_op_id;
            apply_upcharges_condition(up_attribute_id, up_level, up_class);
            // Call apply_upcharges_condition function : END
        }
    }

    function multiSelctOptionPriceValue(att_op_op_op_op_id, callback = false) {
        //alert(att_op_op_op_op_id);
        var op_op_op_id = att_op_op_op_op_id.split("_")[0];
        var att = att_op_op_op_op_id.split("_")[1];
        var op_op_id = att_op_op_op_op_id.split("_")[2];
        if ($("#manual_fabric_price").length > 0) {
            var main_p = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_p = parseFloat($('#main_price').val());
        }
        if (isNaN(main_p)) {
            var main_price = 0;
        } else {
            var main_price = main_p;
        }

        // var mul_op_val = $("#mul_op_op_id" + op_op_id).val();
        // if (op_op_op_id && mul_op_val) {
        var selected_option_fifth = $("#selected_option_fifth").val();
        if (op_op_op_id) {

            var wrapper = $("#mulselect_op_op_op_id_" + op_op_id).parent().next();
            //alert('multioption_price_value');
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/multioption_price_value/') ?>" + op_op_op_id +
                    "/" + att + "/" + main_price + "/" + selected_option_fifth,
                type: 'get',
                async: false,
                success: function(r) {

                    // For shades product fifth attributes : START
                    $(wrapper).show();
                    // For shades product fifth attributes : END
                    $(wrapper).html(r);

                    if (callback != false) {
                        callback();
                    }
                    //   cal();
                    setTimeout(function() {
                        cal();
                    }, 2000);
                }
            });


            var op_class = ".cls_op_five_" + att;
            var op_f_val = $(op_class).val();
            // alert(att);
            // alert(op_f_val);  
            if ((op_f_val != '') && (op_f_val != '0') && (op_f_val != 'undefined')) {
                OptionFive(op_f_val, att);
            }

            // Call apply_upcharges_condition function : START 
            var up_attribute_id = op_op_op_id;
            var up_level = 3;
            var up_class = 'mulselect_op_op_op_id_' + op_op_id;
            apply_upcharges_condition(up_attribute_id, up_level, up_class);
            // Call apply_upcharges_condition function : END
        }
    }

    function OptionFive(att_op_op_op_op_id, attribute_id, callback = false) {
        // alert('OptionFive');
        if (att_op_op_op_op_id) {
            var op_op_op_op_id = att_op_op_op_op_id.split("_")[0];
            var op_op_op_id = att_op_op_op_op_id.split("_")[1];
            if ($("#manual_fabric_price").length > 0) {
                var main_p = parseFloat($("#manual_fabric_price").val());

            } else {
                var main_p = parseFloat($('#main_price').val());
            }
            if (isNaN(main_p)) {
                var main_price = 0;
            } else {
                var main_price = main_p;
            }
            if (op_op_op_op_id) {
                var wrapper = $("#op_op_op_" + op_op_op_id).next().next();
                $.ajaxQueue({
                    url: "<?php echo base_url('b_level/order_controller/get_product_attr_op_five/') ?>" +
                        op_op_op_op_id + "/" + attribute_id + "/" + main_price,
                    type: 'get',
                    async: false,
                    success: function(r) {
                        $(wrapper).html(r);
                        loadPStyle();

                        if (callback != false) {
                            callback();
                        }
                    }
                });

                // Call apply_upcharges_condition function : START 
                var up_attribute_id = op_op_op_op_id;
                var up_level = 4;
                var up_class = 'cls_op_five_' + attribute_id;
                var not_parent = 1; // add upcharges after the current element div otherwise add after parent element div
                apply_upcharges_condition(up_attribute_id, up_level, up_class, not_parent);
                // Call apply_upcharges_condition function : END

            } else {
                loadPStyle();
            }
        } else {
            loadPStyle();
        }
    }

    function OptionOptionsOptionOption(pro_att_op_id, attribute_id, callback = false) {
        var op_op_op_id = pro_att_op_id.split("_")[0];
        var op_op_id = pro_att_op_id.split("_")[1];
        if ($("#manual_fabric_price").length > 0) {
            var main_p = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_p = parseFloat($('#main_price').val());
        }
        if (isNaN(main_p)) {
            var main_price = 0;
        } else {
            var main_price = main_p;
        }
        if (op_op_id) {
            var wrapper = $("#op_op_" + op_op_id).next().next();
            var selected_option_fifth = $("#selected_option_fifth").val();
            // alert("get_product_attr_op_op_op_op");
            // alert(selected_option_fifth);
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/get_product_attr_op_op_op_op/') ?>" +
                    op_op_op_id + "/" + attribute_id + "/" + main_price + "/" + selected_option_fifth,
                type: 'get',
                async: false,
                success: function(r) {
                    $(wrapper).html(r);
                    loadPStyle();

                    if (callback != false) {
                        callback();
                    }
                }
            });

            // Call apply_upcharges_condition function : START 
            var up_attribute_id = op_op_op_id;
            var up_level = 3;
            var up_class = 'cls_op_op_' + op_op_id;
            apply_upcharges_condition(up_attribute_id, up_level, up_class);
            // Call apply_upcharges_condition function : END
            var remote_value = '';
            $(".mul_op_op_op").each(function() {
                var remote_value = $(this).val();
                if ((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')) {
                    multiOptionPriceValue(remote_value);
                }
            });

        } else {
            loadPStyle();
        }
    }

    function OptionOptionsOption(pro_att_op_id, attribute_id, callback = false) {
        // alert('hi');
        var op_op_id = pro_att_op_id.split("_")[0];
        var id = pro_att_op_id.split("_")[1];
        var op_id = pro_att_op_id.split("_")[2];
        if ($("#manual_fabric_price").length > 0) {
            var main_p = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_p = parseFloat($('#main_price').val());
        }
        if (isNaN(main_p)) {
            var main_price = 0;
        } else {
            var main_price = main_p;
        }
        if (op_op_id) {
            var wrapper = $("#op_" + op_id).parent().next().next();
            var selected_option_type_op_op = $("#selected_option_type_op_op").val();

            var selected_option_fifth = $("#selected_option_fifth").val();
            // alert('get_product_attr_op_op_op');

            if (selected_option_type_op_op == '') {
                selected_option_type_op_op = 0;
            }
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/get_product_attr_op_op_op/') ?>" + op_op_id +
                    "/" + id + "/" + attribute_id + "/" + main_price + "/" + selected_option_type_op_op + "/" + selected_option_fifth,
                type: 'get',
                async: false,
                success: function(r) {
                    $(wrapper).html(r);

                    loadPStyle();
                    if (callback != false) {
                        callback();
                    }
                }
            });


            // Call apply_upcharges_condition function : START 
            var up_attribute_id = op_op_id;
            var up_level = 2;
            var up_class = 'cls_op_' + op_id;
            apply_upcharges_condition(up_attribute_id, up_level, up_class);
            // Call apply_upcharges_condition function : END
            var remote_value = '';
            $(".mul_op_op_op").each(function() {
                var remote_value = $(this).val();
                if ((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')) {
                    multiOptionPriceValue(remote_value);
                }
            });

            if (selected_option_fifth != '') {
                setTimeout(function() {
                    var class_op_five = 'cls_op_five_' + attribute_id;
                    var class_op_five_selected_val = $('.' + class_op_five).val();
                    // alert(class_op_five_selected_val);
                    // alert(attribute_id);
                    OptionFive(class_op_five_selected_val, attribute_id);
                }, 500);
            }
        } else {
            if (callback != false) {
                callback();
            }
            loadPStyle();
        }
    }


    function MultiOptionOptionsOption(pro_att_op_id, attribute_id, callback = false) {
        var multi_values = $(pro_att_op_id).val();
        var get_cur_id = $(pro_att_op_id).attr('id');

        if ($("#manual_fabric_price").length > 0) {
            var main_p = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_p = parseFloat($('#main_price').val());
        }
        if (isNaN(main_p)) {
            var main_price = 0;
        } else {
            var main_price = main_p;
        }
        if (multi_values) {
            var wrapper = $("#" + get_cur_id).parent().parent().next().next();
            var multi_val = JSON.stringify(multi_values);
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/get_product_attr_op_op_op_multiselect/') ?>" + attribute_id + "/" + main_price,
                type: 'post',
                data: {
                    multi_val: multi_val
                },
                success: function(r) {
                    $(wrapper).html(r);
                    loadPStyle();
                    if (callback != false) {
                        callback();
                    }
                }
            });
            var remote_value = '';
            $(".mul_op_op_op").each(function() {
                var remote_value = $(this).val();
                if ((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')) {
                    multiOptionPriceValue(remote_value);
                }
            });
        } else {
            if (callback != false) {
                callback();
            }
            loadPStyle();
        }
    }

    function MultiOptionOptionsOptionOptions(pro_att_op_id, attribute_id, callback = false) {
        // alert(pro_att_op_id);
        var multi_values = $(pro_att_op_id).val();
        var get_cur_id = $(pro_att_op_id).attr('id');

        if ($("#manual_fabric_price").length > 0) {
            var main_p = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_p = parseFloat($('#main_price').val());
        }
        if (isNaN(main_p)) {
            var main_price = 0;
        } else {
            var main_price = main_p;
        }
        if (multi_values) {
            var wrapper = $("#" + get_cur_id).parent().parent().next().next();
            var multi_val = JSON.stringify(multi_values);
            var selected_option_fifth = $("#selected_option_fifth").val();
            $.ajax({
                url: "<?php echo base_url('b_level/order_controller/get_product_attr_op_op_op_op_multiselect/') ?>" + attribute_id + "/" + main_price,
                type: 'post',
                data: {
                    multi_val: multi_val
                },
                success: function(r) {
                    $(wrapper).html(r);
                    loadPStyle();
                    if (callback != false) {
                        callback();
                    }
                }
            });

            // It's create issue in multi-Select select - upcharges  
            // var remote_value = '';
            // $(".mul_op_op_op").each(function() {
            //     var remote_value = $(this).val();
            //     if((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')){
            //         multiOptionPriceValue(remote_value);
            //     }
            // });
            if (multi_values.length > 0) {
                var op_op_id = multi_values[0].split("_")[2];
                var up_class = 'mulselect_op_op_op_id_' + op_op_id;
                $('body .' + up_class).parent().parent().find('.up_condition_upcharge').remove();
            }

            $.each(multi_values, function(key, value) {
                multiSelctOptionPriceValue(value, '', "is_multiselect");
            })

        } else {
            if (callback != false) {
                callback();
            }
            loadPStyle();
        }
    }

    function OptionOptions(pro_att_op_id, attribute_id, callback = false) {

        if (pro_att_op_id) {

            //id
            var id = pro_att_op_id.split("_")[0];
            //option_id
            var optin_id = pro_att_op_id.split("_")[1];
            var product_name = $("#product_id :selected").text().toUpperCase();
            //mainprice
            if ($("#manual_fabric_price").length > 0) {
                var main_p = parseFloat($("#manual_fabric_price").val());

            } else {
                var main_p = parseFloat($('#main_price').val());
            }
            if (isNaN(main_p)) {
                var main_price = 0;
            } else {
                var main_price = main_p;
            }

            var attr_name = $('.options_' + attribute_id).attr('data-attr-name');

            // Call apply_upcharges_condition function : START 
            var up_attribute_id = pro_att_op_id;
            var up_level = 1;
            var up_class = 'options_' + attribute_id;
            // console.log("up_attribute_id:"+up_attribute_id);
            // console.log("up_class:"+up_class);
            // console.log(""+);

            apply_upcharges_condition(up_attribute_id, up_level, up_class);
            // Call apply_upcharges_condition function : END

            // If Model is speciallity shutter then display Specialty Type section otherwise hide : START

            if (attr_name == 'Model') {
                var get_selected_value = $(".options_" + attribute_id + " option:selected").text();
                var sepcial_type_section = $('select[data-attr-name="Speciality Type"]');
                var attr_data_id = $(sepcial_type_section).attr('data-id');
                var option_val = '';
                if (get_selected_value.toLowerCase() == 'speciality shutters') {
                    // Display
                    option_val = $(sepcial_type_section).children('option:eq(1)').val();
                    $(sepcial_type_section).parent().parent('.row').show();
                    $(sepcial_type_section).prop('required', true);
                    $('input[name="op_op_value_' + attr_data_id + '[]"]').prop('required', true);
                } else {
                    //hide
                    option_val = $(sepcial_type_section).children('option:eq(0)').val();
                    $(sepcial_type_section).parent().parent('.row').hide();
                    $(sepcial_type_section).prop('required', false);
                    $('input[name="op_op_value_' + attr_data_id + '[]"]').prop('required', false);
                }
                $(sepcial_type_section).val(option_val);
                $(sepcial_type_section).change();
            }
            // If Model is speciallity shutter then display Specialty Type section otherwise hide : END

            // If Contorl type is motorized and tube attribute available then select 38mm for tube : START
            if (attr_name == 'Control Type') {
                //var width = $("#width").val();
                var viewaction = "<?php echo  $view_action; ?>";
                if (viewaction == '0' && jQuery("#inputchnge").val() == "nochange") {
                    var width = $("#hiddenwidth").val();
                } else {
                    var width = $("#width").val();
                }
                var get_selected_value = $(".options_" + attribute_id + " option:selected").text();
                if (get_selected_value == 'Motorized' && $("select[data-attr-name='Tube']").length > 0) {
                    $("select[data-attr-name='Tube'] option").each(function() {
                        var tube_text = $(this).text();
                        var match_text = tube_text.substring(0, 4);
                        if (match_text == '38mm') {
                            var tube_val = $(this).val();
                            $("select[data-attr-name='Tube']").val(tube_val);
                        }
                    });
                } else if (width < 72 && $("select[data-attr-name='Tube']").length > 0) {
                    $("select[data-attr-name='Tube'] option").each(function() {
                        var tube_text = $(this).text();
                        var match_text = tube_text.substring(0, 4);
                        if (match_text == '32mm') {
                            var tube_val = $(this).val();
                            $("select[data-attr-name='Tube']").val(tube_val);
                        }
                    });
                }
            }
            // If Contorl type is motorized and tube attribute available then select 38mm for tube : END
            //alert('op op op val');

            var wrapper = $(".options_" + attribute_id).parent().next().next();
            var select_multi_option_type = $("#selected_multioption_type").val();
            var selected_option_type = $("#selected_option_type").val();

            if (select_multi_option_type == '') {
                select_multi_option_type = 0;
            }
            if (selected_option_type == '') {
                selected_option_type = 0
            }
            var cart_rowids = $('#cart_rowid').val();
            if (cart_rowids == '') {
                cart_rowid = '';
            } else {
                cart_rowid = cart_rowids;
            }
            console.log("Hitting get_product_attr_option_option");
            $.ajax({
                url: "<?php echo base_url('b_level/order_controller/get_product_attr_option_option/') ?>" + id +
                    "/" + attribute_id + "/" + main_price + "/" + select_multi_option_type + "/" + selected_option_type + "/" + cart_rowid,
                type: 'get',
                async: false,
                success: function(r) {
                    $(wrapper).html(r);

                    $(".custom_multi_select.selectpicker").selectpicker();

                    // If Contorl type is motorized and Motorized Accessories & Remote Controller attribute available then assign default value is Not selected : START 
                    if (attr_name == 'Control Type') {
                        var get_selected_value = $(".options_" + attribute_id + " option:selected").text();
                        if (get_selected_value == 'Motorized') {

                            // For Motorized Accessories : START
                            if ($(".custom_multi_select.selectpicker").length > 0) {
                                var mot_acc_attr_id = '';
                                $(".custom_multi_select.selectpicker").each(function() {
                                    if ($(this).data("attr-name") == 'Motorized Accessories') {
                                        mot_acc_attr_id = $(this).attr('id');
                                    }
                                });

                                if (mot_acc_attr_id != '') {
                                    var mot_arr = new Array();
                                    if ($('#' + mot_acc_attr_id).val().length == 0) {
                                        $('#' + mot_acc_attr_id + ' > option').each(function() {
                                            if ($(this).text() == 'Not Selected') {
                                                mot_arr.push($(this).val());
                                            }
                                        });
                                    }

                                    if (mot_arr.length > 0) {
                                        $('#' + mot_acc_attr_id).selectpicker('val', mot_arr);
                                    }
                                    MultiOptionOptionsOptionOptions('#' + mot_acc_attr_id, attribute_id);
                                }
                            }
                            // For Motorized Accessories : END 

                            // For Remote Controller : START
                            var remote_attr_id = '';
                            $(".mul_op_op_op").each(function() {
                                if ($(this).data("attr-name") == 'Remote Controller') {
                                    remote_attr_id = $(this).attr('id');
                                }
                            });

                            if (remote_attr_id != '') {
                                if ($('#' + remote_attr_id).val() == '') {
                                    $('#' + remote_attr_id + ' > option').each(function() {
                                        if ($(this).text() == 'Not Selected') {
                                            $('#' + remote_attr_id).val($(this).val());
                                        }
                                    });
                                }
                            }
                            // For Remote Controller : END
                        }
                    }
                    // If Contorl type is motorized and Motorized Accessories & Remote Controller attribute available then assign default value is Not selected : START
                    // var remote_value = '';
                    // $(".mul_op_op_op").each(function() {
                    //     var remote_value = $(this).val();
                    //     if((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')){
                    //         multiOptionPriceValue(remote_value);
                    //     }
                    // }); 

                    // If Housing Styles then select the sub attributes based on width and height (task # 739): START
                    var hood_5_5 = '<?= HOOD_5_5 ?>';
                    var hood_7 = '<?= HOOD_7 ?>';
                    var hood_7_12 = '<?= HOOD_7_12 ?>';

                    var hood_7_4_0 = '<?= HOOD_7_4_0 ?>';
                    var no_hood_8_4_0 = '<?= NO_HOOD_8_4_0 ?>';
                    var no_hood_8_5_5 = '<?= NO_HOOD_8_5_5 ?>';

                    var no_hood_6_6 = '<?= NO_HOOD_6_6 ?>';
                    var no_hood_7_7 = '<?= NO_HOOD_7_7 ?>';
                    var no_hood_7_5 = '<?= NO_HOOD_7_5 ?>';

                    if (attr_name == 'Housing Styles') {

                        var get_selected_value = $(".options_" + attribute_id + " option:selected").text();

                        // Store attr value into hidden for checke the same attr select or not and display alert : START
                        var housing_style_op = $(".options_" + attribute_id).val();
                        $("#housing_style_op").val(housing_style_op);
                        $("#housing_style_name").val(get_selected_value);
                        // Store attr value into hidden for checke the same attr select or not and display alert : END

                        var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text()
                            .split("/")[1]);
                        var wif = ($("#width_fraction_id :selected").text().split("/")[0] / $("#width_fraction_id :selected").text().split(
                            "/")[1]);
                        var width = parseInt($('#width').val()) + (isNaN(wif) ? 0 : wif);
                        var height = parseInt($('#height').val()) + (isNaN(hif) ? 0 : hif);
                        var width_w = (isNaN(width) ? '' : width);
                        var height_w = (isNaN(height) ? '' : height);

                        var op_name = '';
                        // if(product_name == 'INSECT SCREEN') {
                        //     if(get_selected_value == 'Hoods') {
                        //         if(width_w >= 36 && width_w <= 216 && height_w <= 150){
                        //             op_name = hood_5_5;
                        //         } else if(width_w >= 36 && width_w <= 216 && height_w > 150 && height_w <=264){
                        //             op_name = hood_7;
                        //         } else if(width_w > 216 && width_w <= 312 && height_w <=264){
                        //             op_name = hood_7;
                        //         }
                        //     } else if(get_selected_value == 'No Hoods') {
                        //         if(width_w >= 36 && width_w <= 216 && height_w <= 150){
                        //             op_name = no_hood_6_6;
                        //         } else if(width_w >= 36 && width_w <= 216 && height_w > 150 && height_w <=264){
                        //             op_name = no_hood_7_7;
                        //         } else if(width_w > 216 && width_w <= 312 && height_w <=264){
                        //             op_name = no_hood_7_7;
                        //         }
                        //     } 
                        // } else if(product_name == 'SHADE SCREEN') {
                        //     if(get_selected_value == 'Hoods') {
                        //         if(width_w >= 36 && width_w <= 216 && height_w <= 150){
                        //             op_name = hood_5_5;
                        //         } else if(width_w >= 36 && width_w <= 216 && height_w > 150 && height_w <=240){
                        //             op_name = hood_7;
                        //         } else if(width_w > 216 && width_w <= 312 && height_w <=240){
                        //             op_name = hood_7;
                        //         }
                        //     } else if(get_selected_value == 'No Hoods') {
                        //         if(width_w >= 36 && width_w <= 216 && height_w <= 150){
                        //             op_name = no_hood_6_6;
                        //         } else if(width_w >= 36 && width_w <= 216 && height_w > 150 && height_w <=240){
                        //             op_name = no_hood_7_7;
                        //         } else if(width_w > 216 && width_w <= 312 && height_w <=240){
                        //             op_name = no_hood_7_7;
                        //         }
                        //     }
                        // } else if(product_name == 'CLEAR SCREEN') {
                        //     if(get_selected_value == 'Hoods') {
                        //         if(width_w >= 36 && width_w <= 192 && height_w <= 150){
                        //             op_name = hood_5_5;
                        //         } else if(width_w >= 36 && width_w <= 192 && height_w > 150 && height_w <=192){
                        //             op_name = hood_7;
                        //         } else if(width_w > 192 && width_w <= 264 && height_w <=192){
                        //             op_name = hood_7;
                        //         }
                        //     } else if(get_selected_value == 'No Hoods') {
                        //         if(width_w >= 36 && width_w <= 192 && height_w <= 150){
                        //             op_name = no_hood_6_6;
                        //         } else if(width_w >= 36 && width_w <= 192 && height_w > 150 && height_w <=192){
                        //             op_name = no_hood_7_7;
                        //         } else if(width_w > 192 && width_w <= 264 && height_w <=192){
                        //             op_name = no_hood_7_7;
                        //         }
                        //     }
                        // } else if(product_name == 'HURRICANE SCREEN') {
                        //     if(get_selected_value == 'Hoods') {
                        //         if(width_w >= 36 && width_w <= 192 && height_w >= 24 && height_w <= 113){
                        //             op_name = hood_5_5;
                        //         } else if(width_w >= 36 && width_w <= 192 && height_w >= 113.1 && height_w <= 150){
                        //             op_name = hood_7_4_0;
                        //         } else if(width_w >= 192.1 && width_w <= 240 && height_w >= 24 && height_w <= 125){
                        //             op_name = hood_7;
                        //         }
                        //     } else if(get_selected_value == 'No Hoods') {
                        //       /* if(width_w >= 36 && width_w <= 192 && height_w <= 115){
                        //             op_name = no_hood_6_6;
                        //         } else if(width_w >= 36 && width_w <= 192 && height_w > 115 && height_w <=240){
                        //             op_name = no_hood_7_7;
                        //         } else if(width_w > 192 && width_w <= 312 && height_w <=240){
                        //             op_name = no_hood_7_7;
                        //         }*/
                        //         if(width_w >= 36 && width_w <= 192 && height_w >= 113.1 && height_w <= 150){
                        //             op_name = no_hood_8_4_0;
                        //         } else if(width_w >= 192.1 && width_w <= 240 && height_w >= 24 && height_w <= 150){
                        //             op_name = no_hood_8_5_5;
                        //         }
                        //     }
                        if (product_name == 'INSECT SCREEN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 240 && height_w >= 24 && height_w <= 150) {
                                    op_name = hood_5_5;
                                } else if (width_w >= 240.1 && width_w <= 300 && height_w >= 24 && height_w <= 207) {
                                    op_name = hood_7;
                                } else if (width_w >= 36 && width_w <= 240 && height_w >= 150 && height_w <= 207) {
                                    op_name = hood_7_4_0;
                                }
                                //else if(width_w > 216 && width_w <= 312 && height_w <=264){
                                //     op_name = hood_7;
                                // }
                            } else if (get_selected_value == 'No Hoods') {
                                if (width_w >= 36 && width_w <= 240 && height_w >= 24 && height_w <= 150) {
                                    op_name = no_hood_6_6;
                                } else if (width_w >= 240 && width_w <= 300 && height_w >= 24 && height_w <= 207) {
                                    op_name = no_hood_7_7;
                                } else if (width_w >= 36 && width_w <= 240 && height_w >= 150.1 && height_w <= 207) {
                                    op_name = no_hood_7_5;
                                }
                                //else if(width_w > 216 && width_w <= 312 && height_w <=264){
                                // op_name = no_hood_7_7;
                                //}
                            }
                        } else if (product_name == 'SHADE SCREEN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 240 && height_w >= 24 && height_w <= 150) {
                                    op_name = hood_5_5;
                                } else if (width_w >= 240.1 && width_w <= 300 && height_w >= 24 && height_w <= 207) {
                                    op_name = hood_7;
                                } else if (width_w >= 36 && width_w <= 240 && height_w >= 150.1 && height_w <= 207) {
                                    op_name = hood_7_4_0;
                                }
                            } else if (get_selected_value == 'No Hoods') {
                                if (width_w >= 36 && width_w <= 240 && height_w >= 24 && height_w <= 150) {
                                    op_name = no_hood_6_6;
                                }
                                /*else if(width_w >= 36 && width_w <= 216 && height_w > 150 && height_w <=240){
                                                                  op_name = no_hood_7_7;
                                                              }*/
                                else if (width_w >= 240.1 && width_w <= 300 && height_w >= 24 && height_w <= 207) {
                                    op_name = no_hood_7_7;
                                } else if (width_w >= 36 && width_w <= 240 && height_w >= 150.1 && height_w <= 207) {
                                    op_name = no_hood_7_5;
                                }
                            }
                        } else if (product_name == 'CLEAR SCREEN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w >= 24 && height_w <= 150) {
                                    op_name = hood_5_5;
                                } else if (width_w >= 192.1 && width_w <= 240 && height_w > 24 && height_w <= 192) {
                                    op_name = hood_7;
                                } else if (width_w >= 36 && width_w <= 192 && height_w >= 150.1 && height_w <= 192) {
                                    op_name = hood_7_4_0;
                                }
                            } else if (get_selected_value == 'No Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w >= 24 && height_w <= 150) {
                                    op_name = no_hood_6_6;
                                } else if (width_w >= 192.1 && width_w <= 240 && height_w >= 24 && height_w <= 180) {
                                    op_name = no_hood_7_7;
                                } else if (width_w >= 36 && width_w <= 192 && height_w >= 150.1 && height_w <= 180) {
                                    op_name = no_hood_7_5;
                                } //else if(width_w > 192 && width_w <= 264 && height_w <=192){
                                // op_name = no_hood_7_7;
                                //}
                            }
                        } else if (product_name == 'HURRICANE SCREEN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w >= 24 && height_w <= 120) {
                                    op_name = hood_5_5;
                                } else if (width_w >= 36 && width_w <= 192 && height_w >= 120.1 && height_w <= 168) {
                                    op_name = hood_7_4_0;
                                } else if (width_w >= 192.1 && width_w <= 300 && height_w >= 24 && height_w <= 126) {
                                    op_name = hood_7;
                                }
                            } else if (get_selected_value == 'No Hoods') {
                                /* if(width_w >= 36 && width_w <= 192 && height_w <= 115){
                                     op_name = no_hood_6_6;
                                 } else if(width_w >= 36 && width_w <= 192 && height_w > 115 && height_w <=240){
                                     op_name = no_hood_7_7;
                                 } else if(width_w > 192 && width_w <= 312 && height_w <=240){
                                     op_name = no_hood_7_7;
                                 }*/
                                if (width_w >= 36 && width_w <= 192 && height_w >= 126.1 && height_w <= 240) {
                                    op_name = no_hood_8_4_0;
                                } else if (width_w >= 192.1 && width_w <= 300 && height_w >= 24 && height_w <= 216) {
                                    op_name = no_hood_8_5_5;
                                }
                            }
                        } else if (product_name == 'COMBO SCREEN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w <= 115) {
                                    op_name = hood_7_12;
                                }
                            } else if (get_selected_value == 'No Hoods') {
                                // No condition
                            }
                        } else if (product_name == 'FERRARI/CLEAR CURTAIN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w <= 150) {
                                    op_name = hood_5_5;
                                } else if (width_w >= 36 && width_w <= 192 && height_w > 150 && height_w <= 192) {
                                    op_name = hood_7;
                                } else if (width_w > 192 && width_w <= 264 && height_w <= 192) {
                                    op_name = hood_7;
                                }
                            } else if (get_selected_value == 'No Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w <= 150) {
                                    op_name = no_hood_6_6;
                                } else if (width_w >= 36 && width_w <= 192 && height_w > 150 && height_w <= 192) {
                                    op_name = no_hood_7_7;
                                } else if (width_w > 192 && width_w <= 264 && height_w <= 192) {
                                    op_name = no_hood_7_7;
                                }
                            }
                        } else if (product_name == 'FERRARI/SHADE CURTAIN') {
                            if (get_selected_value == 'Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w <= 150) {
                                    op_name = hood_5_5;
                                } else if (width_w >= 36 && width_w <= 192 && height_w > 150 && height_w <= 192) {
                                    op_name = hood_7;
                                } else if (width_w > 192 && width_w <= 264 && height_w <= 192) {
                                    op_name = hood_7;
                                }
                            } else if (get_selected_value == 'No Hoods') {
                                if (width_w >= 36 && width_w <= 192 && height_w <= 150) {
                                    op_name = no_hood_6_6;
                                } else if (width_w >= 36 && width_w <= 192 && height_w > 150 && height_w <= 192) {
                                    op_name = no_hood_7_7;
                                } else if (width_w > 192 && width_w <= 264 && height_w <= 192) {
                                    op_name = no_hood_7_7;
                                }
                            }
                        }
                        for_select_hood_style_based_on_option(optin_id, op_name);
                    }
                    // If Housing Styles then select the sub attributes based on width and height (task # 739) : END


                    // If Hand sewn rings for Drapery category then calculate the No. Of rings : START
                    if (attr_name == '<?= DRAPERY_HAND_SEWN_RINGS ?>' && $("#is_drapery_cat").val() == 1) {
                        manage_no_of_rings_val();
                    }
                    // If Hand sewn rings for Drapery category then calculate the No. Of rings : END

                    // If Fabric (Included) for Drapery category then calculate the Fabric (Yard) : START
                    if (attr_name == '<?= DRAPERY_FABRIC_INCLUDED ?>' && $("#is_drapery_cat").val() == 1) {
                        var yard_price = $(".drape_yard .drape_yard_price").html();
                        var final_yard_price = 0;
                        if (yard_price > 0) {
                            final_yard_price = parseFloat(yard_price);
                        }
                        $("input[data-attr-name='<?= DRAPERY_FABRIC_YARD ?>']").val(final_yard_price);
                    }
                    // If Fabric (Included) for Drapery category then calculate the Fabric (Yard) : END

                    loadPStyle();
                    if (callback != false) {
                        callback();

                    }
                }
            });
        } else {
            // If not select any option (-select one-) then need to remove dependent attributes : START
            var wrapper = $(".options_" + attribute_id).parent().next().next();
            $(wrapper).html('');
            loadPStyle();
            // If not select any option (-select one-) then need to remove dependent attributes : END
        }
    }

    function manage_no_of_rings_val(check_input = false) {
        $is_there = true;

        if (check_input) {
            $is_there = false;
            if ($("input[data-attr-name='<?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS ?>']").length > 0 && $("#is_drapery_cat").val() == 1) {
                $is_there = true;
            }
        }

        if ($is_there) {
            var cut_width_price = $(".drape_width .drape_width_price").html();
            var final_cut_width_price = 0;
            if (cut_width_price > 0) {
                final_cut_width_price = parseFloat(cut_width_price);
            }

            var default_no_of_rings = final_cut_width_price * <?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS_VAL ?>;
            $("input[data-attr-name='<?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS ?>']").val(default_no_of_rings);
        }
    }

    // For hood style option common function for select option : START
    function for_select_hood_style_based_on_option(optin_id, op_name) {
        var op_val = '';
        var opp_name = '';
        $("select.cls_op_" + optin_id + " option").each(function() {
            var op_text = $(this).text();
            if (op_name != '') {
                if (op_text == op_name) {
                    op_val = $(this).val();
                    opp_name = op_name;
                    $("select.cls_op_" + optin_id).val(op_val).trigger("change");
                    <?php if ($view_action == 0) { ?>
                        // If add order then disable the option other wise user can change.
                        $("select.cls_op_" + optin_id).addClass('disable-select');
                    <?php } ?>
                }
            } else {
                $("select.cls_op_" + optin_id).val('').trigger("change");
                $("select.cls_op_" + optin_id).removeClass('disable-select');
            }
        });

        // Store attr value into hidden for checke the same attr select or not and display alert : START
        $("#housing_style_attr_op").val(op_val);
        $("#housing_style_attr_op_name").val(opp_name);
        // Store attr value into hidden for checke the same attr select or not and display alert : END
    }
    // For hood style option common function for select option : END

    var attr_related_attr_class_list = [];
    // For apply upcharges condition : START
    function apply_upcharges_condition(up_attribute_id, up_level, up_class, not_parent = 0) {
        console.log("testNow");
        // $('.submit_cart_btn').attr('disabled','true');
        var up_condition_height = $("#height").val();
        var up_condition_height_fraction = parseInt($("#height_fraction_id").val()) || 0;
        var up_condition_width = $("#width").val();
        var up_condition_width_fraction = parseInt($("#width_fraction_id").val()) || 0;
        var product_id = $("#product_id").val();
        var pattern_id = $("#pattern_id").val() ? $("#pattern_id").val() : 0;
        if (up_condition_height != '' || up_condition_width != '') {
            // if (up_condition_height != '' || up_condition_width != '') {
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/Upcharges_condition_controller/calculate_up_condition/') ?>" + up_condition_height + "/" + up_condition_height_fraction + "/" + up_condition_width + "/" + up_condition_width_fraction + "/" + up_attribute_id + "/" + up_level + "/" + product_id + "/" + pattern_id,
                type: 'POST',
                data: $("form.frm_product_order_form").serialize() + "&phase_2_up_id=" + phase_2_up_id,
                async: false,
                success: function(response) {

                    response = JSON.parse(response)

                    $remove_upcharge = true;
                    if ($('body .' + up_class + " select")) {
                        if ($('body .' + up_class + " select option:selected").length > 1) {
                            $remove_upcharge = false; // becasuse it's Multiselect select box 
                        }
                    }
                    if ($remove_upcharge) {
                        if (not_parent == 0) {
                            $('body .' + up_class).parent().parent().find('.up_condition_upcharge').remove();
                        } else {
                            $('body .' + up_class).parent().find('.up_condition_upcharge').remove();
                        }
                    }
                    var attrImg = $('body .' + up_class).parent().find('.attr-imgs');
                    if (attrImg.length > 0) {
                        attrImg.remove();
                    }
                    if (get_attr_img && response.attribute_img) {
                        $('body .' + up_class).parent().append("<input class='d-none attr-imgs' value='" + response.attribute_img + "' data-upclass='" + up_class + "'> ")
                    } else {
                        get_attr_img = true;
                    }

                    $.each(response.upcharges, function(res_key, res_value) {

                        $.each(res_value.related_attr_class, function(key, value) {
                            new_obj = {};
                            new_obj['up_attribute_id'] = up_attribute_id;
                            new_obj['up_attribute_position'] = $('body .' + up_class);
                            new_obj['up_level'] = up_level;
                            new_obj['up_class'] = up_class;
                            new_obj['not_parent'] = not_parent;

                            if (!attr_related_attr_class_list[value]) {
                                attr_related_attr_class_list[value] = {};
                            }
                            attr_related_attr_class_list[value][up_attribute_id] = new_obj;
                        });

                        if (res_value.price > 0) {
                            input_html = '<input type="hidden" value="' + res_value.price + '" class="form-control up_condition_upcharge contri_price" data-display_name="' + res_value.display_name + '" data-upcharge_condition_id="' + res_value.upcharge_condition_id + '" data-display-purpose="' + res_value.display_purpose + '"  data-cost_factor_price="' + res_value.cost_factor_price + '">';

                            if (not_parent == 0) {
                                $('body .' + up_class).parent().next().append(input_html);
                            } else {
                                $('body .' + up_class).next().append(input_html);
                            }
                        }
                    })
                    var cordsLenth = $('.cord_len_val').val();
                    if (cordsLenth == '') {
                        var heightVals = $('#height').val();
                        var cordLenVal = heightVals * <?= CORD_LENGTH_VALUES_MANUAL_YES ?>;
                        var contribute_price_nearest = parseFloat(Math.ceil(cordLenVal * 2) / 2);
                        $('.cord_len_val').val(Number(contribute_price_nearest));
                        var cordsLenth = $('.cord_len_val').val();
                    }
                }
            });
        } else {
            // remove
            if (not_parent == 0) {
                $('body .' + up_class).parent().parent().find('.up_condition_upcharge').remove();
            } else {
                $('body .' + up_class).parent().find('.up_condition_upcharge').remove();
            }
        }
        // cal();
        setTimeout(function() {
            cal();
        }, 2000);
    }
    // For apply upcharges condition : END

    function callTrigger() {
        $('.op_op_load').trigger('change');
        $('.op_op_op_load').trigger('change');
        $('.op_op_op_op_load').trigger('change');
        $('.op_op_op_op_op_load').trigger('change');
    }

    // function cal1() {
    //     var currentRequest = null;
    //     // For checking product price fix or not Start by VPN Team
    //     var productId = $('#product_id').val();
    //     var product_qty = parseInt($("#product-qty").val())
    //   //  console.log(productId);
    //     // if(productId != '')
    //     // {
    //     // currentRequest = $.ajaxQueue({
    //     //     url: "<?php echo base_url('b_level/Order_controller/get_single_product_data/') ?>" + productId,
    //     //     type: 'get',
    //     //     async : false,
    //     //     beforeSend : function()    {          
    //     //         if(currentRequest != null) {
    //     //             currentRequest.abort();
    //     //         }
    //     //     },
    //     //     success: function (r) {
    //     //         var data = jQuery.parseJSON(r);
    //     //         if(data){
    //     //             $('#price_style_type').val(data.price_style_type);
    //     //             $('#fix_price_value').val(data.fixed_price);
    //     //             $('#product_combo_or_not').val(data.enable_combo_product);
    //     //         }
    //     //     }
    //     // });
    //     // }
    //     // For checking product price fix or not End by VPN Team

    //     var contribut_prices_array = {};
    //     var display_contribut_prices_array = {};
    //     var separate_display_upcharge_details_arr = [];
    //     var w = $('body #width').val();
    //     var h = $('body #height').val();
    //     if (w !== '' && h !== '') {
    //         var contribut_price = 0;
    //         $("body .contri_price").each(function () {

    //             var contri_price_for = $(this).parent().parent().children('label').text();

    //             // For Drapery price static condition : START
    //             if($("#is_drapery_cat").val() == 1) {
    //                 if($(this).hasClass('drapery_attribute_price_value')) {
    //                     var attr_option_price = $(this).siblings('.drapery_attr_price_value').val();
    //                     var choose_type_option = $("select[data-attr-name='<?= DRAPERY_CHOOSE_TYPE ?>'] option:selected").text(); // Select Box
    //                     var finished_length = $("input[data-attr-name='<?= DRAPERY_FINISHED_LENGTH ?>']").val(); // Text box
    //                     var yard_price = $(".drape_yard .drape_yard_price").html();

    //                     // For get Product Qty : START
    //                     var product_qty = $("#product-qty").val();
    //                     if(!isNaN(product_qty)) {
    //                         product_qty = parseInt(product_qty);
    //                     } else {
    //                         product_qty = 0;
    //                     }
    //                     // For get Product Qty : END

    //                     // For Get Single Qty Yard price : START
    //                     if(product_qty > 1) {
    //                         yard_price = (parseFloat(yard_price) / product_qty).toFixed(2);
    //                     } 
    //                     // For Get Single Qty Yard price : END

    //                     // For final finished length : START
    //                     var final_finished_length = 0;
    //                     if($.isNumeric(finished_length) && finished_length > 0) {
    //                         final_finished_length = parseFloat(finished_length);
    //                     }
    //                     // For final finished length : END

    //                     if(contri_price_for == '<?= DRAPERY_LINING_OPTION ?>') {
    //                         // For Lining option attribute
    //                         this.value = parseFloat(yard_price) * attr_option_price;
    //                         // if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
    //                         //     // If Panel
    //                         //     this.value = parseFloat(yard_price) * attr_option_price;
    //                         // } else {
    //                         //     // If Pair
    //                         //     this.value = parseFloat(yard_price) * attr_option_price * 2;
    //                         // }

    //                         // If lining option have special have special option selected then callcualte the price : START
    //                         var lining_option_text = $("select[data-attr-name='<?= DRAPERY_LINING_OPTION ?>'] option:selected").text();
    //                         if(lining_option_text == '<?= DRAPERY_LINING_OPTION_SPECIAL ?>') {
    //                             var special_price_yard_val = $("select[data-attr-name='<?= DRAPERY_LINING_OPTION ?>']").parent().parent('.row').find("input[data-attr-name='<?= DRAPERY_LINING_OPTION_SPECIAL_PRICE_PER_YARD ?>']").val(); 
    //                             if(special_price_yard_val != '') {
    //                                 special_price_yard_val = parseFloat(special_price_yard_val);
    //                             } else {
    //                                 special_price_yard_val = 0;
    //                             }
    //                             var special_op_val = parseFloat(yard_price) * special_price_yard_val;
    //                             this.value = this.value + special_op_val;
    //                         }
    //                         // If lining option have special have special option selected then callcualte the price : END
    //                     } else if(contri_price_for == '<?= DRAPERY_TRIM ?>') {
    //                         var trim_yard_price = 0;
    //                         // For Trim attribute : START

    //                         var trim_option_text = $("select[data-attr-name='<?= DRAPERY_TRIM ?>'] option:selected").text(); // Select Box
    //                         var fabric_width = $("select[data-attr-name='<?= DRAPERY_FABRIC_WIDTH ?>'] option:selected").text(); // Select Box

    //                         // For get cut width price : START
    //                         var cut_width_price =  $("#drape_width_price_round_val").val();
    //                         var final_cut_width_price = 0;
    //                         if(cut_width_price > 0) {
    //                             final_cut_width_price = parseFloat(cut_width_price);

    //                             // For Get Single Qty Width price : START
    //                             if(product_qty > 1) {
    //                                 final_cut_width_price = (parseFloat(final_cut_width_price) / product_qty).toFixed(2);
    //                             } 
    //                             // For Get Single Qty Width price : END
    //                         }
    //                         // For get cut width price : END

    //                         // For get fabric width : START
    //                         var final_fabric_width = 0;
    //                         if($.isNumeric(fabric_width) && fabric_width > 0) {
    //                             final_fabric_width = parseFloat(fabric_width);
    //                         }
    //                         // For get fabric width : END

    //                         //For Get Layered Trim value : START
    //                         var layered_trim_val = $("select[data-attr-name='<?= DRAPERY_TRIM_LAYERED_TRIM ?>'] option:selected").val();
    //                         var final_layered_trim_val = 1;
    //                         if(layered_trim_val != '') {
    //                             var layered_trim_text = $("select[data-attr-name='<?= DRAPERY_TRIM_LAYERED_TRIM ?>'] option:selected").text();
    //                             if($.isNumeric(layered_trim_text)) {
    //                                 final_layered_trim_val = layered_trim_text;
    //                             } else if(layered_trim_text == '<?= DRAPERY_TRIM_LAYERED_TRIM_ENTRY ?>') {
    //                                 // If entry seleted then consider the below textbox value
    //                                 var custom_entry_val = $("select[data-attr-name='<?= DRAPERY_TRIM_LAYERED_TRIM ?>']").parent('.row').next('div').children('.row').children('div').children('.form-control').val();
    //                                 if($.isNumeric(custom_entry_val)) {
    //                                     final_layered_trim_val = custom_entry_val;
    //                                 }
    //                             } 
    //                         } 
    //                         //For Get Layered Trim value : END

    //                         // Apply Formula based on selected option : START
    //                         if(trim_option_text == '<?= DRAPERY_TRIM_DOWN_LEAD_EDGE ?>') {
    //                             if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
    //                                 // If Panel
    //                                 this.value = parseFloat(Math.ceil((final_finished_length + 10) / 36) * attr_option_price);
    //                                 trim_yard_price = parseFloat(Math.ceil((final_finished_length + 10) / 36));
    //                             } else {
    //                                 // If Pair
    //                                 this.value = parseFloat((Math.ceil((final_finished_length + 10) / 36) * attr_option_price) * 2);
    //                                 trim_yard_price = parseFloat((Math.ceil((final_finished_length + 10) / 36)) * 2);
    //                             }
    //                         } else if(trim_option_text == '<?= DRAPERY_TRIM_DOWN_BOTH_SIDES ?>') {
    //                             if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
    //                                 // If Panel
    //                                 this.value = parseFloat((Math.ceil((final_finished_length + 10) / 36) * attr_option_price) * 2);
    //                                 trim_yard_price = parseFloat((Math.ceil((final_finished_length + 10) / 36)) * 2);
    //                             } else {
    //                                 // If Pair
    //                                 this.value = parseFloat((Math.ceil((final_finished_length + 10) / 36) * attr_option_price) * 4);
    //                                 trim_yard_price = parseFloat((Math.ceil((final_finished_length + 10) / 36)) * 4);
    //                             }
    //                         } else if(trim_option_text == '<?= DRAPERY_TRIM_DOWN_LEAD_EDGE_ACROSS_BOTTOM_AT_EDGE ?>') {
    //                             if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
    //                                 // If Panel
    //                                 this.value = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36) * attr_option_price));
    //                                 trim_yard_price = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36)));
    //                             } else {
    //                                 // If Pair
    //                                 this.value = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36) * attr_option_price) * 2);
    //                                 trim_yard_price = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36)) * 2);
    //                             }
    //                         } else {
    //                             this.value = 0;
    //                         }
    //                         // Apply Formula based on selected option : END

    //                         // Apply trim entry Layered Trim : START
    //                         this.value = this.value * parseFloat(final_layered_trim_val);
    //                         // Apply trim entry Layered Trim : END

    //                         // For Assign the trim yard value 
    //                         drape_trim_yard_price = trim_yard_price*product_qty;
    //                         $(".drapery_price_section .drape_trim_yard_price").html(drape_trim_yard_price);
    //                         $("#hid_drapery_trim_yards").val(trim_yard_price);

    //                         // For Trim attribute : END
    //                     } else if(contri_price_for == '<?= DRAPERY_CONTRAST_BANDING ?>') {

    //                         var contrast_banding_option = $("select[data-attr-name='<?= DRAPERY_CONTRAST_BANDING ?>'] option:selected").text();
    //                         // For Banding option attribute : START
    //                         var banding_yard_price = round_up_val(parseFloat((final_finished_length + 15) / 36));
    //                         if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
    //                             // If Panel
    //                             this.value = parseFloat(banding_yard_price) * attr_option_price;
    //                         } else {
    //                             // If Pair
    //                             this.value = parseFloat(banding_yard_price) * attr_option_price * 2;
    //                         }
    //                         // For Banding option attribute : END

    //                         // IF banding option is not "Yes" then consider price 0 : START
    //                         if(contrast_banding_option != '<?= DRAPERY_CONTRAST_BANDING_YES ?>') {
    //                             banding_yard_price = 0;
    //                         }
    //                         // IF banding option is not "Yes" then consider price 0 : END

    //                         // For Assign the Banding Yard value 
    //                         drape_banding_yard_price = banding_yard_price*product_qty;
    //                         $(".drapery_price_section .drape_banding_yard_price").html(drape_banding_yard_price);
    //                         $("#hid_drapery_banding_yards").val(banding_yard_price);
    //                     } else if(contri_price_for == '<?= DRAPERY_CONTRAST_FLANGE ?>') {
    //                         var contrast_flange_option = $("select[data-attr-name='<?= DRAPERY_CONTRAST_FLANGE ?>'] option:selected").text();

    //                         // For Flange option attribute : START
    //                         var flange_yard_price = round_up_val(parseFloat((final_finished_length + 15) / 36));
    //                         if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
    //                             // If Panel
    //                             this.value = parseFloat(flange_yard_price) * attr_option_price;
    //                         } else {
    //                             // If Pair
    //                             this.value = parseFloat(flange_yard_price) * attr_option_price * 2;
    //                         }
    //                         // For Flange option attribute : END

    //                          // IF banding option is not "Yes" then consider price 0 : START
    //                         if(contrast_flange_option != '<?= DRAPERY_CONTRAST_FLANGE_YES ?>') {
    //                             flange_yard_price = 0;
    //                         }
    //                         // IF banding option is not "Yes" then consider price 0 : END

    //                         // For Assign the Flange Yard value 
    //                         drape_flange_yard_price = flange_yard_price*product_qty;
    //                         $(".drapery_price_section .drape_flange_yard_price").html(drape_flange_yard_price);
    //                         $("#hid_drapery_flange_yards").val(flange_yard_price);
    //                     } else if(contri_price_for == '<?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS ?>') {
    //                         // For Hand sewn rings option with no of rings attribute : START
    //                         var no_of_rings = $("input[data-attr-name='<?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS ?>']").val(); // Text box

    //                         // For Get Single Qty no_of_rings : START
    //                         if(product_qty > 1) {
    //                             no_of_rings = (parseFloat(no_of_rings) / product_qty).toFixed(2);
    //                         } 
    //                         // For Get Single Qty no_of_rings : END

    //                         // For final no_of_rings : START
    //                         var final_no_of_rings = 0;
    //                         if($.isNumeric(no_of_rings) && no_of_rings > 0) {
    //                             final_no_of_rings = parseFloat(no_of_rings);
    //                         }
    //                         // For final no_of_rings : END

    //                         this.value = final_no_of_rings * attr_option_price ;
    //                         // For Hand sewn rings option with no of rings attribute : END

    //                     } else if(contri_price_for == '<?= DRAPERY_FABRIC_YARD ?>') {
    //                         // For Fabric Yard attribute : START
    //                         var fabric_yard = $("input[data-attr-name='<?= DRAPERY_FABRIC_YARD ?>']").val(); // Text box
    //                         var fabric_price_per_yard = $("input[data-attr-name='<?= DRAPERY_FABRIC_YARD ?>']").parent().parent('.row').parent().parent('.row').find("input[data-attr-name='<?= DRAPERY_FABRIC_PRICE_PER_YARD ?>']").val(); // Text box

    //                         // For final fabric_yard : START
    //                         var final_fabric_yard = 0;
    //                         if($.isNumeric(fabric_yard) && fabric_yard > 0) {
    //                             final_fabric_yard = parseFloat(fabric_yard);
    //                         }
    //                         // For final fabric_yard : END

    //                         // For final fabric_price_per_yard : START
    //                         var final_fabric_price_per_yard = 0;
    //                         if($.isNumeric(fabric_price_per_yard) && fabric_price_per_yard > 0) {
    //                             final_fabric_price_per_yard = parseFloat(fabric_price_per_yard);
    //                         }
    //                         // For final fabric_price_per_yard : END

    //                         this.value = final_fabric_yard * final_fabric_price_per_yard ;
    //                         // For Fabric Yard attribute : END

    //                     } else if(contri_price_for == '<?= DRAPERY_HAND_SEWN_HEM_PRICE ?>') {
    //                         var hand_sewn_hem_price = $("select[data-attr-name='<?= DRAPERY_HAND_SEWN_HEM ?>']").parent().parent().find("input[data-attr-name='<?= DRAPERY_HAND_SEWN_HEM_PRICE ?>']").val(); // Text box
    //                         if($.isNumeric(hand_sewn_hem_price)) {
    //                             this.value = hand_sewn_hem_price;
    //                         }
    //                     }else {
    //                         this.value = 0;
    //                     }
    //                 }
    //             } 
    //             // For Drapery price static condition : END

    //             if($(this).hasClass('cls_multi_select_option')){
    //                 // If multi select then consider this block 
    //                 contri_price_for = $(this).attr('data-select-mul-option');
    //             }else{
    //                 // If not multi select then consider this block 

    //                 var select_2_length = $(this).parent().parent().children('div').children('.select2').length;
    //                 var mul_option_length = $(this).parent().prev('.cls_mul_option').length;

    //                 // For multi option multiple select data get : START
    //                 // if(select_2_length > 1){
    //                 if(select_2_length >= 1 && mul_option_length >= 1){
    //                     var select_op_name = '';
    //                     $(this).parent().parent().children('div').children('.select2').each(function( index ) {
    //                       if($( this ).val() != ''){
    //                         select_op_name += $( this ).find('option:selected').text() + ", ";
    //                       }
    //                     });
    //                     select_op_name = select_op_name.replace(/,\s*$/, "");

    //                     // For display the cls_mul_option option title : START
    //                     if(mul_option_length >= 1){
    //                         contri_price_for = $(this).parent().prev('.cls_mul_option').children('label').text();
    //                     }
    //                     // For display the cls_mul_option option title : END

    //                 }else{

    //                     if($(this).parent().parent().hasClass('fifth_attr_row')){
    //                         // For fiifth attr name : START
    //                         var select_op_name = $(this).parent().parent().children('.select2').children("option:selected").text();
    //                          // For fiifth attr name : END
    //                     } else if($(this).parent().parent().children().children().hasClass('cls_text_op_op_value')) {
    //                         // For get textbox value 
    //                         if(contri_price_for != '<?= DRAPERY_HAND_SEWN_HEM_PRICE ?>') {
    //                             // If Hand sewn price then not consider the textbox value like (10) don't want for that.
    //                             var select_op_name = $(this).parent().parent().children().children('.cls_text_op_op_value').val();
    //                         } 
    //                     } else {
    //                         var select_op_name = $(this).parent().parent().children('div').children('.select2').children("option:selected").text();
    //                     }                        
    //                 }
    //                 // For multi option multiple select data get : END

    //                 if (select_op_name != '' && typeof select_op_name !== 'undefined') {
    //                     contri_price_for = contri_price_for+" ("+ select_op_name +")"; 
    //                 }    

    //                 // For blank key issue for Control Type type option : START
    //                 if(contri_price_for == ''){
    //                     contri_price_for = $(this).parent('div').prev('div').children('select').children("option:selected").text();
    //                     if (typeof contri_price_for === 'undefined') {
    //                         contri_price_for = '';
    //                     } 
    //                 }
    //                 // For blank key issue for Control Type type option : END
    //             }

    //             // For cart item order issue : START
    //             contri_price_for = " "+contri_price_for;
    //             // For cart item order issue : END
    //             display_name = $(this).attr("data-display_name");
    //             display_purpose = $(this).attr("data-display-purpose");
    //             upcharge_condition_id = $(this).attr("data-upcharge_condition_id") ? parseInt($(this).attr("data-upcharge_condition_id")) : "";
    //             if(display_name != undefined && display_name != "" && display_name != null)
    //             {
    //                 contri_price_for = $(this).attr("data-display_name");
    //             }

    //             if(display_purpose == 0 || display_purpose == 2  || display_purpose == undefined)
    //             {
    //                 if(display_purpose != undefined)        // Show Cost factor Price only for upcharge value
    //                 {   
    //                     cost_factor_price = parseFloat($(this).attr("data-cost_factor_price"));

    //                     isNaN(cost_factor_price) || 0 == cost_factor_price.length || (contribut_price += cost_factor_price);

    //                     if (contribut_prices_array[contri_price_for] == undefined) {
    //                         contribut_prices_array[contri_price_for] = cost_factor_price;
    //                     } else {
    //                         contribut_prices_array[contri_price_for] += cost_factor_price;
    //                     }

    //                 } else {

    //                     isNaN(this.value) || 0 == this.value.length || (contribut_price += parseFloat(this.value));

    //                     if (contribut_prices_array[contri_price_for] == undefined) {
    //                         contribut_prices_array[contri_price_for] = parseFloat(this.value);
    //                     } else {
    //                         contribut_prices_array[contri_price_for] += parseFloat(this.value);
    //                     }
    //                 }
    //             } 

    //             if(display_purpose == 1 || display_purpose == 2) {
    //                 if (display_contribut_prices_array[contri_price_for] == undefined) {
    //                     display_contribut_prices_array[contri_price_for] = parseFloat(this.value);
    //                 } else {
    //                     display_contribut_prices_array[contri_price_for] += parseFloat(this.value);
    //                 }
    //                 separate_display_upcharge_details_arr.push({'upcharge_label' : contri_price_for , 'upcharge_val' : parseFloat(this.value), "upcharge_condition_id" :  upcharge_condition_id});
    //             }
    //         });

    //         // If Drapery then consider Drapery Product price other wise consider the W*H regular price : START
    //         var is_drapery_cat = $("#is_drapery_cat").val();
    //         if(is_drapery_cat == 1) {
    //             var main_price = parseFloat($("#final_drapery_price").val());
    //         } else {
    //             if("<?= @$user_detail->display_fabric_price ?>" == 1)
    //             {
    //                 if($('#price_style_type').val() == 5 || $('#price_style_type').val() == 6)
    //                 {
    //                     fabric_price = $("#fabric_price").val();
    //                     sqr_val = $("#sqr_val").val();
    //                     total_val = sqr_val*fabric_price;
    //                     $("#main_price").val(total_val)
    //                 }
    //             }
    //             if($("#manual_fabric_price").length > 0)
    //             {
    //                 fabric_price = $("#manual_fabric_price").val() ?? 0;
    //                 total_val = fabric_price;
    //                 $("#main_price").val(total_val)
    //             }
    //             var main_price = parseFloat($('#main_price').val());
    //         }
    //         // If Drapery then consider Drapery Product price other wise consider the W*H regular price : END


    //         // console.log("main_price: ",main_price);
    //         show_main_price = main_price* product_qty;
    //         var priceListHtml  = '<table class="price-details">';
    //         if($('#price_style_type').val() == 3) {
    //             if($('#fix_price_value').val() != 0) {
    //                 priceListHtml += "<tr><td>Price:</td><td id='total_price'>" + var_currency + show_main_price.toFixed(2); + "</td></tr>";
    //             }
    //         } else if($('#price_style_type').val() == 8) {
    //             // For Drapery formula then don't assign the W*H price
    //         } else {
    //             priceListHtml += "<tr><td>Product Price:</td><td id='total_price'>" + var_currency + show_main_price.toFixed(2); + "</td></tr>";
    //         }

    //         // alert(var_currency);
    //         var i = 0;
    //         var label_arr = [];
    //         var upcharge_details_arr = [];
    //         for (var key in contribut_prices_array) {
    //             i = i + 1;
    //             if (contribut_prices_array.hasOwnProperty(key)) {
    //                 if (contribut_prices_array[key] > 0) {
    //                     label_arr.push(key);
    //                     upcharge_details_arr.push({'upcharge_label' : key , 'upcharge_val' : (contribut_prices_array[key]).toFixed(2) });
    //                     <?php if ($user_detail->display_partial_upcharges == 0) { ?>
    //                         if(key == ' Cord Length (Yes)'){
    //                             var contribute_price_nearest = (parseFloat(Math.ceil(contribut_prices_array[key] * 2) / 2) * product_qty);
    //                             var currencies = var_currency + contribute_price_nearest;
    //                             // var currencies = '';
    //                             // var cordLengths = currencies.replaceAll('$', '');
    //                             $('.cords_length').val(contribute_price_nearest);
    //                             var class_show = "value_show";
    //                             var cord_shw = "class='manual_shw' style='display:none;'";

    //                         }else{
    //                             var currencies = var_currency + ((contribut_prices_array[key]).toFixed(2) * product_qty).toFixed(2); 
    //                             var class_show = "";
    //                             var cord_shw = "";
    //                         }
    //                         priceListHtml += "<tr '"+cord_shw+"'><td>" + key + ":</td><td id='custom-value-"+i+"' class='"+class_show+"'>" + currencies +
    //                             "</td></tr>";
    //                     <?php } ?>
    //                 }
    //             }
    //         } 

    //         // START :: For Display Upcharges 
    //         DisplaypriceListHtml = '<table class="display-price-details">';
    //         var index = 0;
    //         var is_show_DisplaypriceListHtml = false;
    //         var display_upcharge_details_arr = [];

    //         for (var key in display_contribut_prices_array) {

    //             index++;

    //             if (display_contribut_prices_array.hasOwnProperty(key)) {

    //                 if (display_contribut_prices_array[key] > 0) {

    //                     display_upcharge_details_arr.push({'upcharge_label' : key , 'upcharge_val' : (display_contribut_prices_array[key]) });

    //                     <?php if ($user_detail->display_partial_upcharges == 0) { ?>

    //                         if(key == ' Cord Length (Yes)'){
    //                             var contribute_price_nearest = (parseInt(Math.ceil(display_contribut_prices_array[key] * 2) / 2) * product_qty);
    //                             var currencies = contribute_price_nearest;
    //                             var class_show = "value_show";
    //                             var cord_shw = "class='manual_shw' style='display:none;'";

    //                         }else{
    //                             var currencies = ((display_contribut_prices_array[key]) * product_qty).toFixed(2);
    //                             var class_show = "";
    //                             var cord_shw = "";
    //                             is_show_DisplaypriceListHtml = true;
    //                         }


    //                         DisplaypriceListHtml += "<tr '"+cord_shw+"'><td>" + key + ":</td><td id='custom-value-"+index+"' class='"+class_show+"'>" + currencies + "</td></tr>";

    //                     <?php } ?>
    //                 }
    //             }
    //         } 
    //         DisplaypriceListHtml += "</table>";

    //         $("body .display_fixed_item_section").hide();

    //         if(is_show_DisplaypriceListHtml)
    //             $("body .display_fixed_item_section").show();

    //         $("body #displayPrice").html(DisplaypriceListHtml);
    //         $("body #display_upcharge_details").val(JSON.stringify(display_upcharge_details_arr));
    //         $("body #separate_display_upcharge_details").val(JSON.stringify(separate_display_upcharge_details_arr));

    //         // END :: For Display Upcharges 

    //         if (isNaN(main_price)) {
    //             var prc = 0;
    //         } else {
    //             var prc = main_price;
    //         }
    //         var total_price = (contribut_price + prc);
    //         var t = (isNaN(total_price) ? 0 : total_price);

    //         var upcharge_lab = label_arr.toString();
    //         $("body #total_price").val(t.toFixed(2));
    //         $("body #upcharge_price").val(contribut_price.toFixed(2));
    //         $("body #upcharge_label").val(upcharge_lab);
    //         $("body #upcharge_details").val(JSON.stringify(upcharge_details_arr));
    //         $("body #h_w_price").val(prc.toFixed(2));
    //         //$("body #tprice").text("Total Price = " + var_currency + t);
    //         // priceListHtml += "<tr><td>Total Price = </td><td>" + var_currency + (t.toFixed(2)) + "</td>";
    //         priceListHtml += "</table>";
    //         if(priceListHtml)
    //             $("body .fixed_item_section").show();

    //         $("body #tprice").html(priceListHtml);

    //         // Showing the cord length value and arch top manual entry particulars

    //         var cordLengths = $('.cords_length_val').val();
    //         var cords = $('.cord_len_val').val();
    //         if(cordLengths == '' || cordLengths == undefined){
    //             if(cords ==''){
    //             }else{
    //                 var cords = $('.cord_len_val').val();
    //                 var contribute_price_nearest = parseFloat(Math.ceil(cords * 2) / 2);
    //                 $('.cords_length').val(contribute_price_nearest);
    //                 $('.cords_length_val').val(contribute_price_nearest);
    //             }
    //         }else{
    //             var up_condition_width1 = $("#change_width").val();
    //             var up_condition_width2 = $("#width").val();
    //             var up_condition_height1 = $("#change_height").val();
    //             var up_condition_height2 = $("#height").val();
    //             var cordLengths = $('.cords_length_val').val();
    //             var cordLengthsVal = Number(cordLengths);
    //             var contribute_price_nearest = parseFloat(Math.ceil(cordLengthsVal * 2) / 2);
    //             // var cordVal = $('.cords_length').val();
    //             var cordVal = $('.cord_len_val').val();
    //             var cordRounds = parseFloat(Math.ceil(cordVal * 2) / 2);
    //             $('.cords_length').val(cordRounds);
    //             $('.value_show').html('$'+cordRounds);
    //             if(Number(up_condition_width1) == Number(up_condition_width2) &&  Number(up_condition_height1) == Number(up_condition_height2)){
    //                 $('.cords_length').val(contribute_price_nearest);
    //                 $('.value_show').html('$'+contribute_price_nearest);
    //             }
    //         }

    //         // Showing the cord length value and arch top manual entry particulars end

    //         var contri_price = $('.contri_price').val();
    //         // Display total of main price and upcharges if display_partial_upcharges is ON start
    //         <?php if ($user_detail->display_partial_upcharges == 1) { ?>
    //             var totalPrice = var_currency + t.toFixed(2);
    //             $('.price-details tr').find("td#total_price").html(totalPrice);
    //         <?php } ?>
    //         // Display total of main price and upcharges if display_partial_upcharges is ON end

    //         // calculate_drapery_price(); // Call for Calculate Drapery price

    //             $("#attr-img-div").html("");
    //             $(".attr-imgs").each(function( index ) {
    //                 val = $(this).val();
    //                 if(val)
    //                 {
    //                     val = $(this).val();
    //                     if(val)
    //                     {
    //                         if(val != "null" && val != "" && val != undefined && val != "undefined")
    //                         {
    //                             attr_img_src = "<?= base_url(); ?>" + val;
    //                             checkIfImageExists(attr_img_src, (exists) => {
    //                                 if (exists) {

    //                                     html = "<div class='attr-img'>\
    //                                                 <div class='attr-img-parent-div'>\
    //                                                     <img src='"+attr_img_src+"'>\
    //                                                     <i class='fa fa-search-plus zoom-icon' onclick='initAttrImagePopup($(this))' data_src='"+attr_img_src+"' ></i>\
    //                                                 </div>\
    //                                             </div>";
    //                                     // html = "<div style='position:relative'><img src='"+attr_img_src+"' class='attr-img'><i class='fa fa-search img-container'></i></div>"
    //                                     $("#attr-img-div").append(html)
    //                                 }
    //                             });
    //                         }
    //                     }
    //                 }
    //             });


    //     }     
    // }
    function cal() {

        var currentRequest = null;
        // For checking product price fix or not Start by VPN Team
        var productId = $('#product_id').val();
        var product_qty = parseInt($("#product-qty").val())
        //  console.log(productId);
        // if(productId != '')
        // {
        // currentRequest = $.ajaxQueue({
        //     url: "<?php echo base_url('b_level/Order_controller/get_single_product_data/') ?>" + productId,
        //     type: 'get',
        //     async : false,
        //     beforeSend : function()    {          
        //         if(currentRequest != null) {
        //             currentRequest.abort();
        //         }
        //     },
        //     success: function (r) {
        //         var data = jQuery.parseJSON(r);
        //         if(data){
        //             $('#price_style_type').val(data.price_style_type);
        //             $('#fix_price_value').val(data.fixed_price);
        //             $('#product_combo_or_not').val(data.enable_combo_product);
        //         }
        //     }
        // });
        // }
        // For checking product price fix or not End by VPN Team

        // apply_upcharges_condition(attribute_id, up_level, up_class);


        var contribut_prices_array = {};
        var display_contribut_prices_array = {};
        var separate_display_upcharge_details_arr = [];
        var w = $('body #width').val();
        var h = $('body #height').val();

        if (w !== '' && h !== '') {
            var contribut_price = 0;
            $("body .contri_price").each(function() {
                // debugger;

                var contri_price_for = $(this).parent().parent().children('label').text();

                // For Drapery price static condition : START
                if ($("#is_drapery_cat").val() == 1) {
                    if ($(this).hasClass('drapery_attribute_price_value')) {
                        var attr_option_price = $(this).siblings('.drapery_attr_price_value').val();
                        var choose_type_option = $("select[data-attr-name='<?= DRAPERY_CHOOSE_TYPE ?>'] option:selected").text(); // Select Box
                        var finished_length = $("input[data-attr-name='<?= DRAPERY_FINISHED_LENGTH ?>']").val(); // Text box
                        var yard_price = $(".drape_yard .drape_yard_price").html();

                        // For get Product Qty : START
                        var product_qty = $("#product-qty").val();
                        if (!isNaN(product_qty)) {
                            product_qty = parseInt(product_qty);
                        } else {
                            product_qty = 0;
                        }
                        // For get Product Qty : END

                        // For Get Single Qty Yard price : START
                        if (product_qty > 1) {
                            yard_price = (parseFloat(yard_price) / product_qty).toFixed(2);
                        }
                        // For Get Single Qty Yard price : END

                        // For final finished length : START
                        var final_finished_length = 0;
                        if ($.isNumeric(finished_length) && finished_length > 0) {
                            final_finished_length = parseFloat(finished_length);
                        }
                        // For final finished length : END

                        if (contri_price_for == '<?= DRAPERY_LINING_OPTION ?>') {
                            // For Lining option attribute
                            this.value = parseFloat(yard_price) * attr_option_price;
                            // if(choose_type_option == '<?= DRAPERY_PANEL ?>') {
                            //     // If Panel
                            //     this.value = parseFloat(yard_price) * attr_option_price;
                            // } else {
                            //     // If Pair
                            //     this.value = parseFloat(yard_price) * attr_option_price * 2;
                            // }

                            // If lining option have special have special option selected then callcualte the price : START
                            var lining_option_text = $("select[data-attr-name='<?= DRAPERY_LINING_OPTION ?>'] option:selected").text();
                            if (lining_option_text == '<?= DRAPERY_LINING_OPTION_SPECIAL ?>') {
                                var special_price_yard_val = $("select[data-attr-name='<?= DRAPERY_LINING_OPTION ?>']").parent().parent('.row').find("input[data-attr-name='<?= DRAPERY_LINING_OPTION_SPECIAL_PRICE_PER_YARD ?>']").val();
                                if (special_price_yard_val != '') {
                                    special_price_yard_val = parseFloat(special_price_yard_val);
                                } else {
                                    special_price_yard_val = 0;
                                }
                                var special_op_val = parseFloat(yard_price) * special_price_yard_val;
                                this.value = this.value + special_op_val;
                            }
                            // If lining option have special have special option selected then callcualte the price : END
                        } else if (contri_price_for == '<?= DRAPERY_TRIM ?>') {
                            var trim_yard_price = 0;
                            // For Trim attribute : START

                            var trim_option_text = $("select[data-attr-name='<?= DRAPERY_TRIM ?>'] option:selected").text(); // Select Box
                            var fabric_width = $("select[data-attr-name='<?= DRAPERY_FABRIC_WIDTH ?>'] option:selected").text(); // Select Box

                            // For get cut width price : START
                            var cut_width_price = $("#drape_width_price_round_val").val();
                            var final_cut_width_price = 0;
                            if (cut_width_price > 0) {
                                final_cut_width_price = parseFloat(cut_width_price);

                                // For Get Single Qty Width price : START
                                if (product_qty > 1) {
                                    final_cut_width_price = (parseFloat(final_cut_width_price) / product_qty).toFixed(2);
                                }
                                // For Get Single Qty Width price : END
                            }
                            // For get cut width price : END

                            // For get fabric width : START
                            var final_fabric_width = 0;
                            if ($.isNumeric(fabric_width) && fabric_width > 0) {
                                final_fabric_width = parseFloat(fabric_width);
                            }
                            // For get fabric width : END

                            //For Get Layered Trim value : START
                            var layered_trim_val = $("select[data-attr-name='<?= DRAPERY_TRIM_LAYERED_TRIM ?>'] option:selected").val();
                            var final_layered_trim_val = 1;
                            if (layered_trim_val != '') {
                                var layered_trim_text = $("select[data-attr-name='<?= DRAPERY_TRIM_LAYERED_TRIM ?>'] option:selected").text();
                                if ($.isNumeric(layered_trim_text)) {
                                    final_layered_trim_val = layered_trim_text;
                                } else if (layered_trim_text == '<?= DRAPERY_TRIM_LAYERED_TRIM_ENTRY ?>') {
                                    // If entry seleted then consider the below textbox value
                                    var custom_entry_val = $("select[data-attr-name='<?= DRAPERY_TRIM_LAYERED_TRIM ?>']").parent('.row').next('div').children('.row').children('div').children('.form-control').val();
                                    if ($.isNumeric(custom_entry_val)) {
                                        final_layered_trim_val = custom_entry_val;
                                    }
                                }
                            }
                            //For Get Layered Trim value : END

                            // Apply Formula based on selected option : START
                            if (trim_option_text == '<?= DRAPERY_TRIM_DOWN_LEAD_EDGE ?>') {
                                if (choose_type_option == '<?= DRAPERY_PANEL ?>') {
                                    // If Panel
                                    this.value = parseFloat(Math.ceil((final_finished_length + 10) / 36) * attr_option_price);
                                    trim_yard_price = parseFloat(Math.ceil((final_finished_length + 10) / 36));
                                } else {
                                    // If Pair
                                    this.value = parseFloat((Math.ceil((final_finished_length + 10) / 36) * attr_option_price) * 2);
                                    trim_yard_price = parseFloat((Math.ceil((final_finished_length + 10) / 36)) * 2);
                                }
                            } else if (trim_option_text == '<?= DRAPERY_TRIM_DOWN_BOTH_SIDES ?>') {
                                if (choose_type_option == '<?= DRAPERY_PANEL ?>') {
                                    // If Panel
                                    this.value = parseFloat((Math.ceil((final_finished_length + 10) / 36) * attr_option_price) * 2);
                                    trim_yard_price = parseFloat((Math.ceil((final_finished_length + 10) / 36)) * 2);
                                } else {
                                    // If Pair
                                    this.value = parseFloat((Math.ceil((final_finished_length + 10) / 36) * attr_option_price) * 4);
                                    trim_yard_price = parseFloat((Math.ceil((final_finished_length + 10) / 36)) * 4);
                                }
                            } else if (trim_option_text == '<?= DRAPERY_TRIM_DOWN_LEAD_EDGE_ACROSS_BOTTOM_AT_EDGE ?>') {
                                if (choose_type_option == '<?= DRAPERY_PANEL ?>') {
                                    // If Panel
                                    this.value = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36) * attr_option_price));
                                    trim_yard_price = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36)));
                                } else {
                                    // If Pair
                                    this.value = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36) * attr_option_price) * 2);
                                    trim_yard_price = parseFloat((Math.ceil(((final_finished_length + 10) + (final_cut_width_price * final_fabric_width)) / 36)) * 2);
                                }
                            } else {
                                this.value = 0;
                            }
                            // Apply Formula based on selected option : END

                            // Apply trim entry Layered Trim : START
                            this.value = this.value * parseFloat(final_layered_trim_val);
                            // Apply trim entry Layered Trim : END

                            // For Assign the trim yard value 
                            drape_trim_yard_price = trim_yard_price * product_qty;
                            $(".drapery_price_section .drape_trim_yard_price").html(drape_trim_yard_price);
                            $("#hid_drapery_trim_yards").val(trim_yard_price);

                            // For Trim attribute : END
                        } else if (contri_price_for == '<?= DRAPERY_CONTRAST_BANDING ?>') {

                            var contrast_banding_option = $("select[data-attr-name='<?= DRAPERY_CONTRAST_BANDING ?>'] option:selected").text();
                            // For Banding option attribute : START
                            var banding_yard_price = round_up_val(parseFloat((final_finished_length + 15) / 36));
                            if (choose_type_option == '<?= DRAPERY_PANEL ?>') {
                                // If Panel
                                this.value = parseFloat(banding_yard_price) * attr_option_price;
                            } else {
                                // If Pair
                                this.value = parseFloat(banding_yard_price) * attr_option_price * 2;
                            }
                            // For Banding option attribute : END

                            // IF banding option is not "Yes" then consider price 0 : START
                            if (contrast_banding_option != '<?= DRAPERY_CONTRAST_BANDING_YES ?>') {
                                banding_yard_price = 0;
                            }
                            // IF banding option is not "Yes" then consider price 0 : END

                            // For Assign the Banding Yard value 
                            drape_banding_yard_price = banding_yard_price * product_qty;
                            $(".drapery_price_section .drape_banding_yard_price").html(drape_banding_yard_price);
                            $("#hid_drapery_banding_yards").val(banding_yard_price);
                        } else if (contri_price_for == '<?= DRAPERY_CONTRAST_FLANGE ?>') {
                            var contrast_flange_option = $("select[data-attr-name='<?= DRAPERY_CONTRAST_FLANGE ?>'] option:selected").text();

                            // For Flange option attribute : START
                            var flange_yard_price = round_up_val(parseFloat((final_finished_length + 15) / 36));
                            if (choose_type_option == '<?= DRAPERY_PANEL ?>') {
                                // If Panel
                                this.value = parseFloat(flange_yard_price) * attr_option_price;
                            } else {
                                // If Pair
                                this.value = parseFloat(flange_yard_price) * attr_option_price * 2;
                            }
                            // For Flange option attribute : END

                            // IF banding option is not "Yes" then consider price 0 : START
                            if (contrast_flange_option != '<?= DRAPERY_CONTRAST_FLANGE_YES ?>') {
                                flange_yard_price = 0;
                            }
                            // IF banding option is not "Yes" then consider price 0 : END

                            // For Assign the Flange Yard value 
                            drape_flange_yard_price = flange_yard_price * product_qty;
                            $(".drapery_price_section .drape_flange_yard_price").html(drape_flange_yard_price);
                            $("#hid_drapery_flange_yards").val(flange_yard_price);
                        } else if (contri_price_for == '<?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS ?>') {
                            // For Hand sewn rings option with no of rings attribute : START
                            var no_of_rings = $("input[data-attr-name='<?= DRAPERY_HAND_SEWN_RINGS_NO_OF_RINGS ?>']").val(); // Text box

                            // For Get Single Qty no_of_rings : START
                            if (product_qty > 1) {
                                no_of_rings = (parseFloat(no_of_rings) / product_qty).toFixed(2);
                            }
                            // For Get Single Qty no_of_rings : END

                            // For final no_of_rings : START
                            var final_no_of_rings = 0;
                            if ($.isNumeric(no_of_rings) && no_of_rings > 0) {
                                final_no_of_rings = parseFloat(no_of_rings);
                            }
                            // For final no_of_rings : END

                            this.value = final_no_of_rings * attr_option_price;
                            // For Hand sewn rings option with no of rings attribute : END

                        } else if (contri_price_for == '<?= DRAPERY_FABRIC_YARD ?>') {
                            // For Fabric Yard attribute : START
                            var fabric_yard = $("input[data-attr-name='<?= DRAPERY_FABRIC_YARD ?>']").val(); // Text box
                            var fabric_price_per_yard = $("input[data-attr-name='<?= DRAPERY_FABRIC_YARD ?>']").parent().parent('.row').parent().parent('.row').find("input[data-attr-name='<?= DRAPERY_FABRIC_PRICE_PER_YARD ?>']").val(); // Text box

                            // For final fabric_yard : START
                            var final_fabric_yard = 0;
                            if ($.isNumeric(fabric_yard) && fabric_yard > 0) {
                                final_fabric_yard = parseFloat(fabric_yard);
                            }
                            // For final fabric_yard : END

                            // For final fabric_price_per_yard : START
                            var final_fabric_price_per_yard = 0;
                            if ($.isNumeric(fabric_price_per_yard) && fabric_price_per_yard > 0) {
                                final_fabric_price_per_yard = parseFloat(fabric_price_per_yard);
                            }
                            // For final fabric_price_per_yard : END

                            this.value = final_fabric_yard * final_fabric_price_per_yard;
                            // For Fabric Yard attribute : END

                        } else if (contri_price_for == '<?= DRAPERY_HAND_SEWN_HEM_PRICE ?>') {
                            var hand_sewn_hem_price = $("select[data-attr-name='<?= DRAPERY_HAND_SEWN_HEM ?>']").parent().parent().find("input[data-attr-name='<?= DRAPERY_HAND_SEWN_HEM_PRICE ?>']").val(); // Text box
                            if ($.isNumeric(hand_sewn_hem_price)) {
                                this.value = hand_sewn_hem_price;
                            }
                        } else {
                            this.value = 0;
                        }
                    }
                }
                // For Drapery price static condition : END

                if ($(this).hasClass('cls_multi_select_option')) {
                    // If multi select then consider this block 
                    contri_price_for = $(this).attr('data-select-mul-option');
                } else {
                    // If not multi select then consider this block 

                    var select_2_length = $(this).parent().parent().children('div').children('.select2').length;
                    var mul_option_length = $(this).parent().prev('.cls_mul_option').length;

                    // For multi option multiple select data get : START
                    // if(select_2_length > 1){
                    if (select_2_length >= 1 && mul_option_length >= 1) {
                        var select_op_name = '';
                        $(this).parent().parent().children('div').children('.select2').each(function(index) {
                            if ($(this).val() != '') {
                                select_op_name += $(this).find('option:selected').text() + ", ";
                            }
                        });
                        select_op_name = select_op_name.replace(/,\s*$/, "");

                        // For display the cls_mul_option option title : START
                        if (mul_option_length >= 1) {
                            contri_price_for = $(this).parent().prev('.cls_mul_option').children('label').text();
                        }
                        // For display the cls_mul_option option title : END

                    } else {

                        if ($(this).parent().parent().hasClass('fifth_attr_row')) {
                            // For fiifth attr name : START
                            var select_op_name = $(this).parent().parent().children('.select2').children("option:selected").text();
                            // For fiifth attr name : END
                        } else if ($(this).parent().parent().children().children().hasClass('cls_text_op_op_value')) {
                            // For get textbox value 
                            if (contri_price_for != '<?= DRAPERY_HAND_SEWN_HEM_PRICE ?>') {
                                // If Hand sewn price then not consider the textbox value like (10) don't want for that.
                                var select_op_name = $(this).parent().parent().children().children('.cls_text_op_op_value').val();
                            }
                        } else {
                            var select_op_name = $(this).parent().parent().children('div').children('.select2').children("option:selected").text();
                        }
                    }
                    // For multi option multiple select data get : END

                    if (select_op_name != '' && typeof select_op_name !== 'undefined') {
                        contri_price_for = contri_price_for + " (" + select_op_name + ")";
                    }

                    // For blank key issue for Control Type type option : START
                    if (contri_price_for == '') {
                        contri_price_for = $(this).parent('div').prev('div').children('select').children("option:selected").text();
                        if (typeof contri_price_for === 'undefined') {
                            contri_price_for = '';
                        }
                    }
                    // For blank key issue for Control Type type option : END
                }

                // For cart item order issue : START
                contri_price_for = " " + contri_price_for;
                // For cart item order issue : END
                display_name = $(this).attr("data-display_name");
                display_purpose = $(this).attr("data-display-purpose");
                upcharge_condition_id = $(this).attr("data-upcharge_condition_id") ? parseInt($(this).attr("data-upcharge_condition_id")) : "";
                if (display_name != undefined && display_name != "" && display_name != null) {
                    contri_price_for = $(this).attr("data-display_name");
                }

                if (display_purpose == 0 || display_purpose == 2 || display_purpose == undefined) {
                    if (display_purpose != undefined) // Show Cost factor Price only for upcharge value
                    {
                        cost_factor_price = parseFloat($(this).attr("data-cost_factor_price"));

                        isNaN(cost_factor_price) || 0 == cost_factor_price.length || (contribut_price += cost_factor_price);

                        if (contribut_prices_array[contri_price_for] == undefined) {
                            contribut_prices_array[contri_price_for] = cost_factor_price;
                        } else {
                            contribut_prices_array[contri_price_for] += cost_factor_price;
                        }

                    } else {

                        isNaN(this.value) || 0 == this.value.length || (contribut_price += parseFloat(this.value));

                        if (contribut_prices_array[contri_price_for] == undefined) {
                            contribut_prices_array[contri_price_for] = parseFloat(this.value);
                        } else {
                            contribut_prices_array[contri_price_for] += parseFloat(this.value);
                        }
                    }
                }

                if (display_purpose == 1 || display_purpose == 2) {
                    if (display_contribut_prices_array[contri_price_for] == undefined) {
                        display_contribut_prices_array[contri_price_for] = parseFloat(this.value);
                    } else {
                        display_contribut_prices_array[contri_price_for] += parseFloat(this.value);
                    }
                    separate_display_upcharge_details_arr.push({
                        'upcharge_label': contri_price_for,
                        'upcharge_val': parseFloat(this.value),
                        "upcharge_condition_id": upcharge_condition_id
                    });
                }
            });

            // If Drapery then consider Drapery Product price other wise consider the W*H regular price : START
            var is_drapery_cat = $("#is_drapery_cat").val();
            if (is_drapery_cat == 1) {
                var main_price = parseFloat($("#final_drapery_price").val());
            } else {
                if ("<?= @$user_detail->display_fabric_price ?>" == 1) {
                    if ($('#price_style_type').val() == 5 || $('#price_style_type').val() == 6) {
                        fabric_price = $("#fabric_price").val();
                        sqr_val = $("#sqr_val").val();
                        total_val = sqr_val * fabric_price;
                        $("#main_price").val(total_val)
                    }
                }
                if ($("#manual_fabric_price").length > 0) {
                    fabric_price = $("#manual_fabric_price").val() ?? 0;
                    total_val = fabric_price;
                    $("#main_price").val(total_val)
                }
                var main_price = parseFloat($('#main_price').val());
            }
            // If Drapery then consider Drapery Product price other wise consider the W*H regular price : END


            // console.log("main_price: ", main_price);
            show_main_price = main_price * product_qty;
            // console.log("show_main_price: ", show_main_price);

            var priceListHtml = '<table class="price-details">';
            if ($('#price_style_type').val() == 3) {
                if ($('#fix_price_value').val() != 0) {
                    priceListHtml += "<tr><td>Price:</td><td id='total_price'>" + var_currency + show_main_price.toFixed(2); + "</td></tr>";
                }
            } else if ($('#price_style_type').val() == 8) {
                // For Drapery formula then don't assign the W*H price
            } else {
                priceListHtml += "<tr><td>Product Price:</td><td id='total_price'>" + var_currency + show_main_price.toFixed(2); + "</td></tr>";
            }
            // alert(var_currency);
            // console.log("Contri_price_array:"+contribut_prices_array);
            // console.log("Contri_price_array:"+JSON.stringify(contribut_prices_array))
            var i = 0;
            var label_arr = [];
            var upcharge_details_arr = [];
            for (var key in contribut_prices_array) {
                i = i + 1;
                if (contribut_prices_array.hasOwnProperty(key)) {
                    if (contribut_prices_array[key] > 0) {
                        label_arr.push(key);
                        upcharge_details_arr.push({
                            'upcharge_label': key,
                            'upcharge_val': (contribut_prices_array[key]).toFixed(2)
                        });
                        <?php if ($user_detail->display_partial_upcharges == 0) { ?>
                            if (key == ' Cord Length (Yes)') {
                                var contribute_price_nearest = (parseFloat(Math.ceil(contribut_prices_array[key] * 2) / 2) * product_qty);
                                var currencies = var_currency + contribute_price_nearest;
                                // var currencies = '';
                                // var cordLengths = currencies.replaceAll('$', '');
                                $('.cords_length').val(contribute_price_nearest);
                                var class_show = "value_show";
                                var cord_shw = "class='manual_shw' style='display:none;'";

                            } else {
                                var currencies = var_currency + ((contribut_prices_array[key]).toFixed(2) * product_qty).toFixed(2);
                                var class_show = "";
                                var cord_shw = "";
                            }
                            priceListHtml += "<tr '" + cord_shw + "'><td>" + key + ":</td><td id='custom-value-" + i + "' class='" + class_show + "'>" + currencies +
                                "</td></tr>";
                        <?php } ?>
                    }
                }
            }

            // START :: For Display Upcharges 
            DisplaypriceListHtml = '<table class="display-price-details">';
            var index = 0;
            var is_show_DisplaypriceListHtml = false;
            var display_upcharge_details_arr = [];

            for (var key in display_contribut_prices_array) {

                index++;

                if (display_contribut_prices_array.hasOwnProperty(key)) {

                    if (display_contribut_prices_array[key] > 0) {

                        display_upcharge_details_arr.push({
                            'upcharge_label': key,
                            'upcharge_val': (display_contribut_prices_array[key])
                        });

                        <?php if ($user_detail->display_partial_upcharges == 0) { ?>

                            if (key == ' Cord Length (Yes)') {
                                var contribute_price_nearest = (parseInt(Math.ceil(display_contribut_prices_array[key] * 2) / 2) * product_qty);
                                var currencies = contribute_price_nearest;
                                var class_show = "value_show";
                                var cord_shw = "class='manual_shw' style='display:none;'";

                            } else {
                                var currencies = ((display_contribut_prices_array[key]) * product_qty).toFixed(2);
                                var class_show = "";
                                var cord_shw = "";
                                is_show_DisplaypriceListHtml = true;
                            }


                            DisplaypriceListHtml += "<tr '" + cord_shw + "'><td>" + key + ":</td><td id='custom-value-" + index + "' class='" + class_show + "'>" + currencies + "</td></tr>";

                        <?php } ?>
                    }
                }
            }
            DisplaypriceListHtml += "</table>";
            console.log("CAL Upcharge");
            $("body .display_fixed_item_section").hide();

            if (is_show_DisplaypriceListHtml)
                $("body .display_fixed_item_section").show();

            $("body #displayPrice").html(DisplaypriceListHtml);
            $("body #display_upcharge_details").val(JSON.stringify(display_upcharge_details_arr));
            $("body #separate_display_upcharge_details").val(JSON.stringify(separate_display_upcharge_details_arr));

            // END :: For Display Upcharges 

            if (isNaN(main_price)) {
                var prc = 0;
            } else {
                var prc = main_price;
            }
            var total_price = (contribut_price + prc);
            var t = (isNaN(total_price) ? 0 : total_price);

            var upcharge_lab = label_arr.toString();
            $("body #total_price").val(t.toFixed(2));
            $("body #upcharge_price").val(contribut_price.toFixed(2));
            $("body #upcharge_label").val(upcharge_lab);
            $("body #upcharge_details").val(JSON.stringify(upcharge_details_arr));
            $("body #h_w_price").val(prc.toFixed(2));
            //$("body #tprice").text("Total Price = " + var_currency + t);
            // priceListHtml += "<tr><td>Total Price = </td><td>" + var_currency + (t.toFixed(2)) + "</td>";
            priceListHtml += "</table>";
            if (priceListHtml)
                $("body .fixed_item_section").show();

            $("body #tprice").html(priceListHtml);

            // Showing the cord length value and arch top manual entry particulars

            var cordLengths = $('.cords_length_val').val();
            var cords = $('.cord_len_val').val();
            if (cordLengths == '' || cordLengths == undefined) {
                if (cords == '') {} else {
                    var cords = $('.cord_len_val').val();
                    var contribute_price_nearest = parseFloat(Math.ceil(cords * 2) / 2);
                    $('.cords_length').val(contribute_price_nearest);
                    $('.cords_length_val').val(contribute_price_nearest);
                }
            } else {
                var up_condition_width1 = $("#change_width").val();
                var up_condition_width2 = $("#width").val();
                var up_condition_height1 = $("#change_height").val();
                var up_condition_height2 = $("#height").val();
                var cordLengths = $('.cords_length_val').val();
                var cordLengthsVal = Number(cordLengths);
                var contribute_price_nearest = parseFloat(Math.ceil(cordLengthsVal * 2) / 2);
                // var cordVal = $('.cords_length').val();
                var cordVal = $('.cord_len_val').val();
                var cordRounds = parseFloat(Math.ceil(cordVal * 2) / 2);
                $('.cords_length').val(cordRounds);
                $('.value_show').html('$' + cordRounds);
                if (Number(up_condition_width1) == Number(up_condition_width2) && Number(up_condition_height1) == Number(up_condition_height2)) {
                    $('.cords_length').val(contribute_price_nearest);
                    $('.value_show').html('$' + contribute_price_nearest);
                }
            }

            // Showing the cord length value and arch top manual entry particulars end

            var contri_price = $('.contri_price').val();
            // Display total of main price and upcharges if display_partial_upcharges is ON start
            <?php if ($user_detail->display_partial_upcharges == 1) { ?>
                var totalPrice = var_currency + t.toFixed(2);
                $('.price-details tr').find("td#total_price").html(totalPrice);
            <?php } ?>
            // Display total of main price and upcharges if display_partial_upcharges is ON end

            // calculate_drapery_price(); // Call for Calculate Drapery price

            $("#attr-img-div").html("");
            $(".attr-imgs").each(function(index) {
                val = $(this).val();
                if (val) {
                    val = $(this).val();
                    if (val) {
                        if (val != "null" && val != "" && val != undefined && val != "undefined") {
                            attr_img_src = "<?= base_url(); ?>" + val;
                            checkIfImageExists(attr_img_src, (exists) => {
                                if (exists) {

                                    html = "<div class='attr-img'>\
                                                    <div class='attr-img-parent-div'>\
                                                        <img src='" + attr_img_src + "'>\
                                                        <i class='fa fa-search-plus zoom-icon' onclick='initAttrImagePopup($(this))' data_src='" + attr_img_src + "' ></i>\
                                                    </div>\
                                                </div>";
                                    // html = "<div style='position:relative'><img src='"+attr_img_src+"' class='attr-img'><i class='fa fa-search img-container'></i></div>"
                                    $("#attr-img-div").append(html)
                                }
                            });
                        }
                    }
                }
            });


        }
    }

    function checkIfImageExists(url, callback) {
        const img = new Image();

        img.src = url;

        if (img.complete) {
            callback(true);
        } else {
            img.onload = () => {
                callback(true);
            };

            img.onerror = () => {
                callback(false);
            };
        }
    }

    // Without select the customer and side mark not able to select category : START
    $('body').on('change', '#category_id', function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        if ($("#category_id").val() == 49) {
            swal.fire('Width is overall system width, Including Tracks.Height is overall system Height, Including Hood and Bottom Track.');
        }
        var customer_id = $("#customer_id").val();
        var side_mark_f_name = $("#side_mark_f_name").val();
        var side_mark_house_no = $("#side_mark_house_no").val();

        var valid_category_select = '';
        if (customer_id == '') {
            valid_category_select = 1;
            swal.fire('Please select customer first');
        } else if (side_mark_f_name == '') {
            valid_category_select = 1;
            swal.fire('Please enter side mark first');
        }

        if (valid_category_select == 1) {
            $("#category_id").val('');
        }
    });
    // Without select the customer and side mark not able to select category : END


    $('body').on('blur', '.valid_height_width', function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        // Without selecting Category,Product, Pattern and color, it should not allow us to enter the width and height value : START
        var category_id = $("#category_id").val();
        var product_id = $("#product_id").val();
        var pattern_id = $("#pattern_id").val();
        var color_id = $("#color_id").val();
        if (category_id == '') {
            $(this).val('');
            var custom_cat_label = $("span.custom_cat_label").html();
            swal.fire('Please select ' + custom_cat_label + ' first');
        } else if (product_id == '') {
            $(this).val('');
            var custom_product_label = $("span.custom_product_label").html();
            swal.fire('Please select ' + custom_product_label + ' first');
        } else if (pattern_id == '' && $(".combo-product-section").length == 0) {
            $(this).val('');
            var custom_pattern_label = $("span.custom_pattern_label").html();
            swal.fire('Please select ' + custom_pattern_label + ' first');
        } else if (color_id == '') {
            $(this).val('');
            var custom_color_label = $("span.custom_color_label").html();
            swal.fire('Please select ' + custom_color_label + ' first');
        } else {
            check_valid_height_width();
            // get_product_row_col_price(); // New function for calculate row col price instead of all the time call in loadpstye function    
        }
        // Without selecting Category,Product, Pattern and color, it should not allow us to enter the width and height value : END

        // check_valid_height_width();
    })

    $('body').on('change', '#product_id, #pattern_id, #height_fraction_id, #width_fraction_id', function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        $("#cartbtn").attr('disabled', 'true');
        check_valid_height_width();
    })

    // If main price 0 then get max height and width and display on alert : START
    function check_valid_height_width() {

        var product_id = $('#product_id').val();
        var pattern_id = $('#pattern_id').val();
        if ($("#manual_fabric_price").length > 0) {
            var main_price = parseFloat($("#manual_fabric_price").val());

        } else {
            var main_price = parseFloat($('#main_price').val());
        }

        var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text()
            .split("/")[1]);
        var wif = ($("#width_fraction_id :selected").text().split("/")[0] / $("#width_fraction_id :selected").text().split(
            "/")[1]);
        var width_t = parseInt($('#width').val()) + (isNaN(wif) ? 0 : wif);
        var height_t = parseInt($('#height').val()) + (isNaN(hif) ? 0 : hif);
        var width = (isNaN(width_t) ? '' : width_t);
        var height = (isNaN(height_t) ? '' : height_t);

        // if(height != '' && width != '' && product_id != '' && pattern_id != '' && main_price == 0) {
        if (height !== '' && width !== '' && product_id != '' && pattern_id != '') {

            //var submit_url = "<? //= base_url(); 
                                ?>//b_level/order_controller/get_max_height_width/" + product_id + "/"+pattern_id;
            var submit_url = "<?= base_url(); ?>b_level/order_controller/get_min_max_height_width/" + product_id + "/" + pattern_id;
            $.ajaxQueue({
                type: 'GET',
                url: submit_url,
                async: false,
                dataType: 'json',
                success: function(res) {
                    if (res.success == 1) {
                        // var max_height = parseFloat(res.col);
                        // var max_width = parseFloat(res.row);
                        var min_height = parseFloat(res.minh);
                        var min_width = parseFloat(res.minw);
                        var max_height = parseFloat(res.maxh);
                        var max_width = parseFloat(res.maxw);
                        height = parseFloat(height);
                        width = parseFloat(width);

                        var custom_width_label = $("span.custom_width_label").html();
                        var custom_height_label = $("span.custom_height_label").html();

                        // $("#cartbtn").attr('disabled', 'true');
                        // $(".submit_cart_btn").attr('disabled', 'true');
                        /* alert("max_height"+max_height);//126
                          alert("max_width"+max_width);//108
                          alert("height"+height);
                          alert("width"+width);*/

                        if ((max_height > 0 && max_width > 0) && (height > max_height && width > max_width)) {
                            swal.fire(custom_width_label + ' cannot be more than ' + max_width + ' and ' + custom_height_label + ' cannot be more than ' + max_height);
                            localStorage.setItem("width_height_error", 'true');
                            $('#width').val('');
                            $('#height').val('');
                        } else if (max_width > 0 && width > max_width) {
                            swal.fire(custom_width_label + ' cannot be more than ' + max_width);
                            localStorage.setItem("width_height_error", 'true');
                            $('#width').val('');
                        } else if (max_height > 0 && height > max_height) {
                            swal.fire(custom_height_label + ' cannot be more than ' + max_height);
                            localStorage.setItem("width_height_error", 'true');
                            $('#height').val('');
                        } else if ((min_height > 0 && min_width) > 0 && (height < min_height && width < min_width)) {
                            swal.fire(custom_width_label + ' cannot be less than ' + min_width + ' and ' + custom_height_label + ' cannot be less than ' + min_height);
                            localStorage.setItem("width_height_error", 'true');
                            $('#width').val('');
                            $('#height').val('');
                        } else if (min_width > 0 && width < min_width) {
                            swal.fire(custom_width_label + ' cannot be less than ' + min_width);
                            localStorage.setItem("width_height_error", 'true');
                            $('#width').val('');
                        } else if (min_height > 0 && height < min_height) {
                            swal.fire(custom_height_label + ' cannot be less than ' + min_height);
                            localStorage.setItem("width_height_error", 'true');
                            $('#height').val('');
                        } //else if(max_height == 0 || max_width == 0){
                        //swal.fire('Please enter proper '+custom_width_label+' and '+custom_height_label);
                        // }
                        else {
                            // swal.fire('Please enter proper width and height');
                            $("#cartbtn").removeAttr('disabled');
                            $(".submit_cart_btn").removeAttr('disabled');
                            localStorage.setItem("width_height_error", 'false');
                        }
                    } else {
                        // swal.fire('Please enter proper width and height');
                        $("#cartbtn").removeAttr('disabled');
                        $(".submit_cart_btn").removeAttr('disabled');
                        localStorage.setItem("width_height_error", 'false');
                    }
                }
            });
        } else {
            $("#cartbtn").removeAttr('disabled');
            $(".submit_cart_btn").removeAttr('disabled');
            localStorage.setItem("width_height_error", 'false');

        }
    }
    // If main price 0 then get max height and width and display on alert : END



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////-------function for change trigger with callback functionality starts here--------------------->
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function onCategoryChange(selectedCat, callback = false) {
        if ($(".combo-product-section").length > 0) {
            $(".combo-product-section").remove();
            $("#color_model").show();
        }

        if (selectedCat) {
            is_retiler = '<?= @$is_basic_retiler; ?>';
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/category_wise_subcategory/') ?>" + selectedCat,
                type: 'get',
                data: {
                    is_retiler
                },
                async: false,
                success: function(r) {

                    var list = jQuery.parseJSON(r);


                    //$("#subcategory_id").html(r);
                    $("#subcategory_id").html(list['sub_category_list_html']);
                    $("#product_id").html(list['product_list_by_category_html']);
                    CheckOrderCondition(list);


                    var product_id = $('#product_id').val();
                    if ((product_id != 'undefined') && (product_id != null) && (product_id != '')) {
                        onProductChange(product_id);
                    }

                    var resultLength = (list['product_list_by_category_html'].match(/option/g)).length;
                    if (resultLength == 4)
                        $('#product_id').trigger('change');

                    var fraction_list = list['fraction_array'];
                    $("#width_fraction_id").html('');
                    $("#width_fraction_id").html(fraction_list['html']);
                    $("#height_fraction_id").html('');
                    $("#height_fraction_id").html(fraction_list['html']);
                    if (callback != false) {
                        callback();
                    }
                    $(".component-section").html(list['component_category']);
                    //check_component_category(selectedCat);

                    check_drapery_category(selectedCat, list['drapery_category']);
                    //get_custom_label_category_wise(selectedCat); // Custom.js file function for change custom label
                    get_custom_label_category_wise_in_orderpage(list['custom_label_data']);
                    check_component_product_hide(list['display_hide_product']);



                }
            });
        }


        var sub_category_id = $('#sub_category_id').val();
        if ((sub_category_id != 'undefined') && (sub_category_id != null) && (sub_category_id != '') && sub_category_id != 0) {
            onSubCategoryChange(sub_category_id);
        }
    }


    function get_custom_label_category_wise_in_orderpage(label_data) {
        if (typeof(label_data.order_category_label) != "undefined" && label_data.order_category_label != '') {
            $("span.custom_cat_label").html(label_data.order_category_label);
        }
        if (typeof(label_data.order_product_label) != "undefined" && label_data.order_product_label != '') {
            $("span.custom_product_label").html(label_data.order_product_label);
        }
        if (typeof(label_data.order_pattern_label) != "undefined" && label_data.order_pattern_label != '') {
            $("span.custom_pattern_label").html(label_data.order_pattern_label);
        }
        if (typeof(label_data.order_color_label) != "undefined" && label_data.order_color_label != '') {
            $("span.custom_color_label").html(label_data.order_color_label);
        }
        if (typeof(label_data.order_room_label) != "undefined" && label_data.order_room_label != '') {
            $("span.custom_room_label").html(label_data.order_room_label);
        }
        if (typeof(label_data.order_width_label) != "undefined" && label_data.order_width_label != '') {
            $("span.custom_width_label").html(label_data.order_width_label);
        }
        if (typeof(label_data.order_height_label) != "undefined" && label_data.order_height_label != '') {
            $("span.custom_height_label").html(label_data.order_height_label);
        }
    }



    function CheckOrderCondition(list) {
        if (list) {
            is_retiler = '<?= @$is_basic_retiler; ?>';

            $("#phase_2").prop("checked", false).change();
            if (list['phase_2_ordering'] == 1) {
                $("#phase_2_ordering_condition").val(1);
                $("#phase_2_ordering_div").show();
                phase_2_ordering_instruction = '<div class="col-sm-2"></div><div class="col-sm-6" id="phase_2_condition_instruction_html" style="display:none">' + list['phase_2_ordering_instruction'] + "</div>";
                $("#phase_2_ordering_instruction").html(phase_2_ordering_instruction);
                get_phase_2_conditions(list['phase_2_select']);
            } else {
                $("#phase_2_ordering_condition").val(0);
                $("#phase_2_ordering_div").hide();
                $("#phase_2_condition_instruction_html").hide();
                $("#phase_2_ordering_instruction").html("")
                $("#phase_2_condition_div").html("")
            }

            /* 
                 END :: Check Phase 2 Order Condition 
            */
            if (list['is_enable_order_form_qty'] == 1) {
                $("#order_qty_div").show();
                $("#product-qty").prop("required", true);
            } else {
                $("#order_qty_div").hide();
                $("#product-qty").prop("required", false);
            }
        } else {

            /* 
                START :: Check Phase 2 Order Condition 
            */
            $("#phase_2_ordering_condition").val(0);
            $("#phase_2_ordering_div").hide();
            $("#phase_2_condition_instruction_html").hide();
            $("#phase_2_ordering_instruction").html("")
            $("#phase_2_condition_div").html("")

            /* 
                END :: Check Phase 2 Order Condition 
            */

            $("#order_qty_div").hide();
            $("#product-qty").prop("required", false);
        }
    }

    function check_component_product_hide(data) {
        // For hide/show Product : START
        if (data) {
            if (data.hide_product == 1) {
                // Hide Product
                $("#product_id").val('');
                $(".product-section").hide();
                $('#product_id').prop('required', false);

                // Hide Height
                $("#height").val('0');
                $("#height").parent().parent('.row').parent('.form-group').hide();
                $("#height").prop('min', 0);

                // Hide Width
                $("#width").val('0');
                $("#width").parent().parent('.row').parent('.form-group').hide();
                $("#width").prop('min', 0);

                // Hide Room
                $("#room").parent().parent('.row').parent('.form-group').hide();
                $("#room").val('');
                $('#room').prop('required', false);

                // Hide Pattern 
                $("#pattern_id").val('');
                $("#pattern_id").prop("required", false);
                $("#color_model").hide();

                // Hide Color
                $('body #pattern_color_model').html('');

                // Make Attribute section empty
                $("#attr").html('');

                // Hide special instruction 
                $(".special-instruction-section #notes").val('');
                $(".special-instruction-section").hide();

                // loadPStyle(); // call this because height and width is already hide so need to call when product change.
                $("body .fixed_item_section").hide();
                $("body .display_fixed_item_section").hide();
                $("#tprice").html('');
                $("#displayPrice").html('');
            } else {
                // Show Product
                $(".product-section").show();
                $('#product_id').prop('required', true);

                // Show special instruction 
                $(".special-instruction-section").show();

                // Hide Component Section 
                $(".component_price_section").html('');
                $(".component_price_section").hide();
            }
            // For hide/show Room : END 
        }
    }

    // For check drapery category then hide the product dropdown : START
    function check_drapery_category(selectedCat, r) {
        if (r != '') {
            $("#is_drapery_cat").val(r); // For is drapery category or not

            // Hide Right side Darpery price blue box : START
            if ($("#is_drapery_cat").val() != 1) {
                $(".drapery_price_section").hide();

                // Make Drapery right side blue box value 0 if not Drapery : START

                $(".drapery_price_section .drape_width_price").html(0);
                $(".drapery_price_section #drape_width_price_round_val").val(0);
                $(".drapery_price_section .drape_height_price").html(0);
                $(".drapery_price_section .drape_cuts_price").html(0);
                $(".drapery_price_section .drape_yard_price").html(0);
                $(".drapery_price_section .drape_trim_yard_price").html(0);
                $(".drapery_price_section .drape_banding_yard_price").html(0);
                $(".drapery_price_section .drape_flange_yard_price").html(0);
                $(".drapery_price_section .drape_product_price").html(0);

                $("#hid_drapery_of_cuts").val(0);
                $("#hid_drapery_of_cuts_only_panel").val(0);
                $("#hid_drapery_cut_length").val(0);
                $("#hid_drapery_total_fabric").val(0);
                $("#hid_drapery_total_yards").val(0);
                $("#hid_drapery_trim_yards").val(0);
                $("#hid_drapery_banding_yards").val(0);
                $("#hid_drapery_flange_yards").val(0);
                $("#hid_drapery_finished_width").val(0);

                // Make Drapery right side blue box value 0 if not Drapery : END

            }
            // Hide Right side Darpery price blue box : END

            if ($("#component_id").length == 0) {
                // if component then no need to check 
                if (r > 0 && $("#product_id option").length <= 2) {
                    $("#product_id").parent().parent('.row').parent('div').hide();
                } else {
                    $("#product_id").parent().parent('.row').parent('div').show();
                }
            }
        } else {
            $("#product_id").parent().parent('.row').parent('div').show();
        }
    }
    // For check drapery category then hide the product dropdown : END

    // For check category have component or not : START
    function check_component_category(selectedCat, r) {
        if (selectedCat != '') {
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/check_component_category/') ?>" + selectedCat,
                type: 'get',
                async: false,
                success: function(data) {
                    $(".component-section").html(data);
                }
            })
        } else {
            $(".component-section").html('');
        }
    }
    // For check category have component or not : END

    function onSubCategoryChange(selectedSubCat, callback = false) {
        var cat_id = $("#category_id").val();
        if (selectedSubCat != '0' && selectedSubCat != '') {
            // If sub cateogry select then get product and fraction based on subcategory : START
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/get_product_by_subcategory/') ?>" +
                    selectedSubCat,
                type: 'get',
                success: function(r) {
                    $("#product_id").html(r);
                    var product_id = $('#product_id').val();
                    if ((product_id != 'undefined') && (product_id != null) && (product_id != '')) {
                        onProductChange(product_id);
                    }
                    if (callback != false) {
                        callback();
                    }
                }
            });
            // If sub cateogry select then get product and fraction based on subcategory : END
        } else {
            // If sub cateogry not select then get product and fraction based on category : START
            var selectedCat = cat_id;
            is_retiler = '<?= @$is_basic_retiler; ?>';
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/category_wise_subcategory/') ?>" + selectedCat,
                type: 'get',
                data: {
                    is_retiler
                },
                async: false,
                success: function(r) {
                    //$("#subcategory_id").html(r);
                    var list = jQuery.parseJSON(r);
                    //$("#subcategory_id").html(r);
                    $("#subcategory_id").html(list['sub_category_list_html']);
                    $("#product_id").html(list['product_list_by_category_html']);
                    var product_id = $('#product_id').val();
                    if ((product_id != 'undefined') && (product_id != null) && (product_id != '')) {
                        onProductChange(product_id);
                    }
                    var fraction_list = list['fraction_array'];
                    $("#width_fraction_id").html('');
                    $("#width_fraction_id").html(fraction_list['html']);
                    $("#height_fraction_id").html('');
                    $("#height_fraction_id").html(fraction_list['html']);
                    if (callback != false) {
                        callback();
                    }
                }
            });
            // If sub cateogry not select then get product and fraction based on category : END
        }
    }

    function onProductChange(selectedProduct, callback = false) {
        console.log("Inside2");

        $('.overlay').show();
        var productId = selectedProduct;
        is_basic_retiler = '<?= @$is_basic_retiler; ?>';
        get_color_partan_model_url = '';
        /*Update by ak*/
        var cart_rowid = $("#cart_rowid").val();
        var edit_type = $("#edit_type").val();

        if (cart_rowid != '') {
            cart_rowid = "/" + cart_rowid + "/" + edit_type;
        }
        /*Update by ak end*/

        if (productId) {
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/Order_controller/get_data_on_product_change/') ?>" + productId + cart_rowid + "/" + is_basic_retiler,
                type: 'get',
                async: false,
                success: function(r) {
                    var list = jQuery.parseJSON(r);
                    // console.log("Returned Object of get_data_on_product_change:" + list);

                    if (list) {
                        data = list['getProductData'];
                        //attributes_html = list['attributes_html'];
                        //color_pattern_html = list['color_pattern_html'];
                        display_h_w = list['display_h_w'];
                        combo_product_html = list['combo_product_html'];
                        combo_products = list['combo_products'];

                        if (data) {
                            $('#price_style_type').val(data.price_style_type);
                            $('#fix_price_value').val(data.fixed_price);
                            $('#product_combo_or_not').val(data.enable_combo_product);
                        }

                        get_color_partan_model_url = ''
                        is_basic_retiler = '<?= @$is_basic_retiler; ?>';
                        if (is_basic_retiler == 1) {
                            get_color_partan_model_url = "<?php echo base_url('c_level/customer_order_controller/get_color_partan_model/') ?>" + selectedProduct;

                        } else {

                            get_color_partan_model_url = "<?php echo base_url('b_level/order_controller/get_color_partan_model/') ?>" + selectedProduct;
                        }

                        $.ajaxQueue({
                            url: "<?php echo base_url('b_level/order_controller/get_product_to_attribute/') ?>" + selectedProduct,
                            type: 'get',
                            // async : false,
                            success: function(r) {
                                console.log("Inside3");

                                $("#attr").html(r);
                                $('#cartbtn').removeAttr('disabled');
                                $('#pattern_color_model').html('');
                                var att_id = $('.op_op_load').data('id');
                                var val = $('.options_' + att_id).val();
                                if ((att_id != 'undefined') && (att_id != null) && (att_id != '')) {
                                    //OptionOptions(val,att_id);
                                }
                                $.ajaxQueue({
                                    url: get_color_partan_model_url,
                                    type: 'get',
                                    data: {
                                        is_basic_retiler: is_basic_retiler
                                    },
                                    //    async : false,
                                    success: function(r) {
                                        $('#color_model').html(r);
                                        var pattern_id = $('#pattern_id').val();
                                        if ((pattern_id != 'undefined') && (pattern_id != null) && (pattern_id != '')) {
                                            //OnPatternChange(pattern_id);
                                        }
                                        if ($(".combo-product-section").length > 0) {
                                            $("#pattern_id").prop("required", false);
                                            $("#color_model").hide();
                                        } else {
                                            $("#pattern_id").prop("required", true);
                                            $("#color_model").show();
                                        }
                                        if (callback != false) {
                                            callback();
                                        }
                                        //     $("#pattern_id").select2({
                                        //      closeOnSelect: false,
                                        //    });
                                        //    $("#color_id").select2({
                                        //      closeOnSelect: false,
                                        //    });
                                        //$("#pattern_id").select2();
                                        //$("#color_id").select2();
                                        //$("#color_id").select2('close');

                                        callTrigger();
                                    }
                                });
                            }
                        });

                        /*if(attributes_html)
                        {
                            $("#attr").html(attributes_html);
                            $('#cartbtn').removeAttr('disabled');
                            $('#pattern_color_model').html('');
                            var att_id = $('.op_op_load').data('id');
                            var val = $('.options_'+att_id).val();
                           /* if((att_id != 'undefined') && (att_id != null) && (att_id != ''))
                            {
                             OptionOptions(val,att_id);
                            }*/

                        /* }

                         if(color_pattern_html)
                         {
                             $('#color_model').html(color_pattern_html);
                             var pattern_id = $('#pattern_id').val();
                             if((pattern_id != 'undefined') && (pattern_id != null) && (pattern_id != ''))
                             {
                              //OnPatternChange(pattern_id);
                             }
                             if($(".combo-product-section").length > 0){
                                 $("#pattern_id").prop("required",false);
                                 $("#color_model").hide();
                             }else{
                                 $("#pattern_id").prop("required",true);
                                 $("#color_model").show();
                             }
                             if (callback != false) {
                                 callback();
                             }
                             callTrigger();
                         }
                         else
                         {
                             $('#color_model').html("");

                         }*/

                        if (display_h_w) {
                            // For hide/show Room : START
                            if (display_h_w.hide_room == 1) {
                                $("#room").parent().parent('.row').parent('.form-group').hide();
                                $("#room").val('');
                                $('#room').prop('required', false);
                            } else {
                                $("#room").parent().parent('.row').parent('.form-group').show();
                                $('#room').prop('required', <?php echo (!empty($user_detail->room_require) ? 'true' : 'false') ?>);
                            }
                            // For hide/show Room : END

                            // For hide Height and Width : START
                            if (display_h_w.hide_height_width == 1) {
                                // Hide Width
                                $("#width").val('0');
                                $("#width").parent().parent('.row').parent('.form-group').hide();
                                $("#width").prop('min', 0);

                                // Show Height
                                // $("#height").val('');
                                $("#height").parent().parent('.row').parent('.form-group').show();
                                $("#height").prop('min', 1);
                            } else if (display_h_w.hide_height_width == 2) {
                                // Hide Height
                                $("#height").val('0');
                                $("#height").parent().parent('.row').parent('.form-group').hide();
                                $("#height").prop('min', 0);

                                // Show Width
                                // $("#width").val('');
                                $("#width").parent().parent('.row').parent('.form-group').show();
                                $("#width").prop('min', 1);
                            } else if (display_h_w.hide_height_width == 3) {
                                // Hide Both
                                // Hide Height
                                $("#height").val('0');
                                $("#height").parent().parent('.row').parent('.form-group').hide();
                                $("#height").prop('min', 0);

                                // Hide Width
                                $("#width").val('0');
                                $("#width").parent().parent('.row').parent('.form-group').hide();
                                $("#width").prop('min', 0);

                                //loadPStyle(); // call this because height and width is already hide so need to call when product change.
                            } else {
                                // Show Both
                                // Show Height
                                // $("#height").val('');
                                $("#height").parent().parent('.row').parent('.form-group').show();
                                $("#height").prop('min', 1);

                                // Show Width
                                // $("#width").val('');
                                $("#width").parent().parent('.row').parent('.form-group').show();
                                $("#width").prop('min', 1);
                            }

                            // For hide Height and Width : END
                        }


                    }

                    if (combo_product_html != '' && combo_products != '') {

                        if ($(".combo-product-section").length > 0) {
                            $("#pattern_id").prop("required", false);
                            $("#color_model").hide();
                        } else {
                            $("#pattern_id").prop("required", true);
                            $("#color_model").show();
                        }
                        $(".combo-product-section").remove();
                        $(".product-section").append(combo_product_html);
                        /*Update by ak*/
                        var i = 1;
                        $.each(JSON.parse(combo_products), function(index, value) {
                            $('#combo_product_id_' + i).val(value).trigger('change');
                            i++;
                        });

                    }


                }
            });
        }

        // console.log('here');
        //$('[data-attr-name~="Housing Styles"]').text('No Hoods');
        setTimeout(function() {
            // alert('hi')
            // $('select.options_225').find('option:selected').remove().end();
            $("select.op_op_load option").filter(function() {
                if ($(this).text() == $("#phase2_housing_style").val()) {
                    var housingStyleAttrName = $(this).parent().attr('name');
                    $("#housingStyleAttributeName").val(housingStyleAttrName);
                    $(this).parent().addClass('disable-select')
                }
                return $(this).text() == $("#phase2_housing_style").val();
            }).prop("selected", true).trigger('change');

        }, 2000);

        // set value from data-housing style from phase 2 here in above once (it will b select by text statically)


        // get_product_row_col_price(); // New function for calculate row col price instead of all the time call in loadpstye function
        $('.overlay').hide();

    }

    function OnPatternChange(selectedPattern, callback = false, fabric_price = 0) {
        var product_id = $('#product_id').val();
        var fromWholesaler = "<?php echo !empty($wholesaler_data) ? $wholesaler_data : ''  ?>";

        if ("<?= @$user_detail->display_fabric_price ?>" == 1) {
            if (!fabric_price) {
                get_fabric_price(product_id, selectedPattern)
            } else {
                $("#fabric_price").val(fabric_price);
            }
        }

        $("#manual_pattern_color_div").remove();
        $("#manual_pattern_color_div2").remove();
        $("#display_fabric_price_div").show();

        if (selectedPattern > 0) {
            if (fromWholesaler == 1) {
                $.ajaxQueue({
                    url: "<?php echo base_url('b_level/order_controller/get_color_model_wholesaler/') ?>" + product_id + "/" +
                        selectedPattern,
                    type: 'get',
                    success: function(r) {
                        $('body #pattern_color_model').html('');
                        $('body  #pattern_color_model').html(r);
                        $('body #color_id').parent().removeClass('col-sm-3').addClass('col-sm-6');
                        // $('body #color_id').parent().next().removeClass('col-sm-3').addClass('col-sm-1');
                        $('body #color_id').change();
                        if (callback != false) {
                            callback();
                        }
                        if ($('#price_style_type').val() == 10) {
                            loadPStyle();
                        } else if ($('#price_style_type').val() == 5 || $('#price_style_type').val() == 6) {
                            cal()
                        }
                    }
                });
            } else {
                $.ajaxQueue({
                    url: "<?php echo base_url('b_level/order_controller/get_color_model/') ?>" + product_id + "/" +
                        selectedPattern,
                    type: 'get',
                    success: function(r) {
                        $('body #pattern_color_model').html('');
                        $('body  #pattern_color_model').html(r);
                        $('body #color_id').parent().removeClass('col-sm-3').addClass('col-sm-6');
                        $('body #color_id').change();
                        if (callback != false) {
                            callback();
                        }
                        if ($('#price_style_type').val() == 10) {
                            loadPStyle();
                        } else if ($('#price_style_type').val() == 5 || $('#price_style_type').val() == 6) {
                            cal()
                        }
                        // $("#pattern_id").select2({
                        //     closeOnSelect: false,
                        // });
                        // $("#color_id").select2({
                        //     closeOnSelect: false,
                        // });
                        //$("#pattern_id").select2();
                        //$("#color_id").select2();
                        //$("#color_id").select2('close');
                    }
                });
            }
        } else {
            $('body #pattern_color_model').html('');

            if (selectedPattern !== "" && $('body #color_model').children().length > 0) {
                $("#display_fabric_price_div").hide();

                html = '<div class="row" id="manual_pattern_color_div">\
                            <label for="" class="col-sm-2"><span class="">Fabric Name</span></label>\
                            <div class="col-sm-6">\
                                <input type="text" class="form-control" placeholder="Manual Pattern Entry" name="manual_pattern_entry" required="true" id="manual_pattern_entry">\
                            </div>\
                            <div class="col-sm-2">\
                                <input class="form-control" id="manual_fabric_price" name="fabric_price" type="text" value="0">\
                            </div>\
                        </div>\
                        <div class="row" id="manual_pattern_color_div2">\
                            <label for="" class="col-sm-2"><span class="">Color Name</span></label>\
                            <div class="col-sm-6">\
                                <input type="text" class="form-control" placeholder="Manual Color Entry" name="manual_color_entry" required="true" id="manual_color_entry">\
                            </div>\
                        </div>';
                $("#color_model").append(html);
            }
            if (callback != false) {
                callback();
            }
        }

        // get_product_row_col_price(); // New function for calculate row col price instead of all the time call in loadpstye function
    }
    var fabric_original_price = "<?php echo !empty($get_product_order_info->fabric_price) ? $get_product_order_info->fabric_price : 0; ?>";

    function get_fabric_price(product_id, pattern_id) {
        $.ajax({
            url: "<?php echo base_url('b_level/order_controller/get_fabric_price/') ?>" + product_id + "/" + pattern_id,
            type: 'get',
            success: function(data) {
                data = (data ? data : 0) * 1; // For remove 0 from start 
                $("#fabric_price").val(data);
                fabric_original_price = data;
            }
        });
    }

    $(document).ready(function() {
        $(document).on("keyup", "#fabric_price", function(event) {
            if (event) {
                // event.stopImmediatePropagation();
            }
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')

            if ($("#pattern_id").val() != "") {
                if ($('#price_style_type').val() == 10) {
                    loadPStyle();
                } else {
                    cal()
                }
            } else {
                $(this).val(0);
                Swal.fire("Please select first fabric.");
            }
        })
        $(document).on("blur", "#fabric_price", function(event) {
            if (event) {
                // event.stopImmediatePropagation();
            }
            if ($("#fabric_price").val() == 0 && fabric_original_price > 0 && $("#pattern_id").val() != "") {
                $("#fabric_price").val(fabric_original_price)
                if ($('#price_style_type').val() == 10) {
                    loadPStyle();
                } else {
                    cal()
                }
                swal.fire("Please select fabric price more than 0.");
            }
        })
        $(document).on("keyup", "#manual_fabric_price", function(event) {
            this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1')
            loadPStyle();
        })
        $(document).on("blur", "#manual_fabric_price", function(event) {
            ChangeAttributes();
        })

        function ChangeAttributes() {
            // for option type : 4 Multioption :START
            var mul_op_op_op_selected = [];
            $('.mul_op_op_op').each(function() {
                // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
                // {
                mul_op_op_op_selected.push($(this).val());
                // }

            });

            $("#selected_multioption_type").val(mul_op_op_op_selected.join('@'));
            // for option type : 4 Multioption :END

            // for option type : 2 Option :START
            var option_type = [];
            $("select[class*='cls_op_']").each(function() {
                // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
                // {
                option_type.push($(this).val());
                // }

            });

            $("#selected_option_type").val(option_type.join('@'));

            // for option type : 2 Option :END

            // for option type : 2 Option op op :START
            var option_type_op_op = [];
            $("select[class*='cls_op_op_']").each(function() {
                // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
                // {
                option_type_op_op.push($(this).val());
                // }

            });

            $("#selected_option_type_op_op").val(option_type_op_op.join('@'));

            // for option type : 2 Option op op :END


            // for option type : fifth level option :START
            var option_op_five = [];
            $("select[class*='cls_op_five_']").each(function() {
                // if ((mul_op_op_op_selected.indexOf($(this).val()) === -1) && ($(this).val() !== undefined) && ($(this).val() !== ''))
                // {
                option_op_five.push($(this).val());
                // }

            });

            $("#selected_option_fifth").val(option_op_five.join('@'));


            // for option type : fifth level option :END

            // To preserve option type values on height blur event : END

            $(".op_op_load").each(function() {
                var att_id = jQuery(this).data('id');
                var val = $('.options_' + att_id).val();
                var data_attr_name = jQuery(this).attr('data-attr-name');
                if (val != '' && jQuery("#height").val() != '' && jQuery("#height").val() != 0) {
                    // OptionOptions(val,att_id);
                    if (data_attr_name != 'Control Type') {
                        setTimeout(() => {
                            OptionOptions(val, att_id);
                        }, 1000);
                    } else {

                        // For Motorized Accessories : START
                        if ($(".custom_multi_select.selectpicker").length > 0) {
                            var mot_acc_attr_id = '';
                            $(".custom_multi_select.selectpicker").each(function() {
                                if ($(this).data("attr-name") == 'Motorized Accessories') {
                                    mot_acc_attr_id = $(this).attr('id');
                                }
                            });
                            if (mot_acc_attr_id != '') {
                                var mot_arr = new Array();
                                if ($('#' + mot_acc_attr_id).val().length > 0) {
                                    $('#' + mot_acc_attr_id + ' > option').each(function() {

                                        mot_arr.push($(this).val());

                                    });
                                }
                            }

                        }
                        // For Motorized Accessories : END 
                        setTimeout(() => {
                            MultiOptionOptionsOptionOptions('#' + mot_acc_attr_id, att_id);
                        }, 1000);
                    }
                }
            });

            setTimeout(() => {
                $('.overlay').hide();
            }, 1000);

            var remote_value = '';
            $(".mul_op_op_op").each(function() {
                var remote_value = $(this).val();
                if ((remote_value != '') && (remote_value != '0') && (remote_value != 'undefined')) {
                    multiOptionPriceValue(remote_value);
                }
            });
        }
    })

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////-------function for change trigger with callback functionality ends here--------------------->
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).ready(function() {
        if ($('.sticky_container .sticky_item').length > 0) {
            $('.sticky_container .sticky_item').theiaStickySidebar({
                additionalMarginTop: 110
            });
        }
    });

    $(document).ajaxStop(function() {
        // console.log('ajaxStop');
        if (localStorage.getItem("width_height_error") == 'true') {
            $("#cartbtn").attr('disabled', 'true');
            $(".submit_cart_btn").attr('disabled', 'true');
        } else {
            setTimeout(function() {
                $('#cartbtn').removeAttr('disabled');
                $(".submit_cart_btn").removeAttr('disabled');
            }, 800);
            localStorage.setItem("width_height_error", 'false');
        }


        //$('.overlay').hide();
    });
    $(document).ajaxStart(function() {
        // console.log('ajaxStart');
        setTimeout(function() {
            $("#cartbtn").attr('disabled', 'true');
            $(".submit_cart_btn").attr('disabled', 'true');
        }, 800);

        //  $('.overlay').show();
    });
</script>

<?php if (isset($win_to_edit) && !empty($win_to_edit)) { ?>

    <?php
    if (isset($get_product_order_info->notes)) {
        $order_notes = str_replace("'", "\'", $get_product_order_info->notes);
        $order_notes = trim(preg_replace('/\s\s+/', ' ', $order_notes));
    }
    $order_installer_notes = "";
    if (isset($get_product_order_info->special_installer_notes) && $get_product_order_info->special_installer_notes != '') {
        $order_installer_notes = str_replace("'", "\'", $get_product_order_info->special_installer_notes);
        $order_installer_notes = trim(preg_replace('/\s\s+/', ' ', $order_installer_notes));
    }
    ?>



    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery("#room_index").val('<?php echo !empty($get_product_order_info->room_index) ? htmlentities($get_product_order_info->room_index, ENT_QUOTES) : ''; ?>');
            $('body #category_id').val("<?php echo !empty($get_product_order_info->category_id) ? $get_product_order_info->category_id : ''; ?>");
            $('body #sub_category_id').val("<?php echo !empty($get_product_order_info->win_sub_category_id) ? $get_product_order_info->win_sub_category_id : ''; ?>");
            $('body #room').val("<?php echo !empty($get_product_order_info->room) ? $get_product_order_info->room : ''; ?>");
            $('body #notes').val('<?php echo $order_notes; ?>');
            <?php if (@$user_detail->special_instructions && @$order_installer_notes) { ?>
                $("#special_notes_for_installer_check").trigger("click");
                $("#special_notes_for_installer").val('<?php echo $order_installer_notes; ?>');

            <?php } ?>
            onCategoryChange("<?php echo !empty($get_product_order_info->category_id) ? $get_product_order_info->category_id : ''; ?>", function() {


                $('body #sub_category_id').val("<?php echo !empty($get_product_order_info->sub_category_id) ? $get_product_order_info->sub_category_id : ''; ?>");
                onSubCategoryChange("<?php echo !empty($get_product_order_info->sub_category_id) ? $get_product_order_info->sub_category_id : ''; ?>", function() {
                    $('body #product_id').val("<?php echo !empty($get_product_order_info->product_id) ? $get_product_order_info->product_id : ''; ?>");
                    <?php if (@$get_product_order_info->phase_2_option) { ?>
                        $("#phase_2").prop("checked", true).change();
                    <?php } ?>
                    $("#phase_2_condition").selectpicker('val', [<?php echo @$get_product_order_info->phase_2_condition; ?>]);

                    onProductChange("<?php echo !empty($get_product_order_info->product_id) ? $get_product_order_info->product_id : ''; ?>", function() {
                        $('#product_id').val("<?php echo !empty($get_product_order_info->product_id) ? $get_product_order_info->product_id : ''; ?>");
                        $('body #pattern_id').val("<?php echo (@$get_product_order_info->pattern_model_id >= 0) ? @$get_product_order_info->pattern_model_id : ''; ?>");
                        OnPatternChange("<?php echo (@$get_product_order_info->pattern_model_id >= 0) ? @$get_product_order_info->pattern_model_id : ''; ?>", function() {
                            $('body #color_id').val("<?php echo (@$get_product_order_info->color_id >= 0) ? @$get_product_order_info->color_id : '';  ?>");
                            getColorCode("<?php echo (@$get_product_order_info->color_id >= 0) ? @$get_product_order_info->color_id : ''; ?>");

                            if ("<?= @$get_product_order_info->pattern_model_id ?>" === "0") {
                                if ("<?php echo @$get_product_order_info->manual_pattern_entry; ?>" !== null && "<?php echo @$get_product_order_info->manual_color_entry; ?>" !== null) {
                                    $('body #manual_pattern_entry').val("<?php echo @$get_product_order_info->manual_pattern_entry; ?>");
                                    $('body #manual_fabric_price').val("<?php echo @$get_product_order_info->fabric_price; ?>");
                                    $('body #manual_color_entry').val("<?php echo @$get_product_order_info->manual_color_entry; ?>");
                                } else {

                                    $('body #pattern_id').val("");
                                }
                            }

                            if ("<?= @$get_product_order_info->color_id ?>" === "0" && "<?= @$get_product_order_info->pattern_model_id ?>" !== "0") {
                                if ("<?php echo @$get_product_order_info->manual_color_entry; ?>" !== null) {
                                    $('body #color_id').val("<?php echo @$get_product_order_info->color_id;  ?>");
                                    getColorCode("<?php echo (@$get_product_order_info->color_id >= 0) ? @$get_product_order_info->color_id : ''; ?>");
                                    $('body #manual_color_entry').val("<?php echo @$get_product_order_info->manual_color_entry; ?>");
                                } else {

                                    $('body #color_id').val("");
                                }
                            }
                            <?php if ($view_action != 0) { ?>
                                // If click on copy order then not select the height and width  : START
                                $('body #width').val("<?php echo $get_product_order_info->width; ?>");
                                $('body #width_fraction_id').val("<?php echo $get_product_order_info->width_fraction_id; ?>");
                                $('body #height').val("<?php echo $get_product_order_info->height; ?>");
                                $('body #height_fraction_id').val("<?php echo $get_product_order_info->height_fraction_id; ?>");
                                // get_product_row_col_price(); // For Calculate W*H Price
                                // If click on copy order then not select the height and width  : END
                            <?php } ?>
                            $('body #hiddenwidth').val("<?php echo $get_product_order_info->width; ?>");
                            $('body #hiddenheight').val("<?php echo $get_product_order_info->height; ?>");

                            // loadPStyle(function () {
                            var i = 0;
                            //alert('hi');
                            <?php foreach ($selected_attributes as $records) { ?>
                                <?php
                                $attribute_id = $records->attribute_id;
                                if ($records->attributes_type == 2) {
                                    $options_val = $records->options[0]->option_key_value; ?>
                                    // if ($('select[name="op_id_<?php echo $attribute_id; ?>[]"]').val() != '<?php echo $options_val; ?>' || '<?= $records->options[0]->option_type ?>' == 6 || '<?= $records->options[0]->option_type ?>' == 4 || '<?= $records->options[0]->option_type ?>' == 3) {
                                    $('select[name="op_id_<?php echo $attribute_id; ?>[]"]').val('<?php echo $options_val; ?>');
                                    OptionOptions("<?php echo $options_val; ?>", "<?php echo $attribute_id; ?>", function() {
                                        <?php if ($records->options[0]->option_type == 5) {
                                            if (isset($records->opop[0]) && $records->opop[0]->op_op_value != '') {
                                                $opop_val1 = explode(" ", $records->opop[0]->op_op_value);
                                            }
                                            if (isset($records->opop[1]) && $records->opop[1]->op_op_value != '') {
                                                $opop_val2 = explode(" ", $records->opop[1]->op_op_value);
                                            }
                                            if (isset($records->opop[2]) && $records->opop[2]->op_op_value != '') {
                                                $opop_val3 = explode(" ", $records->opop[2]->op_op_value);
                                            }
                                        ?>
                                            var i = 0;
                                            $('input[name="op_op_value_<?php echo $attribute_id; ?>[]"]').each(function() {
                                                if (i == 0) {
                                                    $(this).val("<?php echo isset($opop_val1[0]) ? $opop_val1[0] : ''; ?>");
                                                    $('select[name="fraction_<?php echo $attribute_id; ?>[]"]').val("<?php echo isset($opop_val1[0]) ? $opop_val1[0] : ''; ?>");
                                                }
                                                if (i == 1) {
                                                    $(this).val("<?php echo !empty($opop_val2) ? $opop_val2[0] : ''; ?>");
                                                }
                                                if (i == 2) {
                                                    $(this).val("<?php echo !empty($opop_val3) ? $opop_val3[0] : ''; ?>");
                                                }
                                                i++;
                                            });
                                            var i = 0;
                                            $('select[name="fraction_<?php echo $attribute_id; ?>[]"]').each(function() {
                                                if (i == 0) {
                                                    $(this).val("<?php echo isset($opop_val1[1]) ? $opop_val1[1] : ''; ?>");
                                                }
                                                if (i == 1) {
                                                    $(this).val("<?php echo !empty($opop_val2[1]) ? $opop_val2[1] : ''; ?>");
                                                }
                                                if (i == 2) {
                                                    $(this).val("<?php echo !empty($opop_val3[1]) ? $opop_val3[1] : ''; ?>");
                                                }
                                                i++;
                                            });
                                        <?php } else if ($records->options[0]->option_type == 1) { ?>
                                            $('input[name="op_value_<?php echo $attribute_id; ?>[]"]').last().val('<?php echo $records->options[0]->option_value; ?>');
                                        <?php } else if ($records->options[0]->option_type == 3) { ?>
                                            var i = 0;
                                            $('input[name="op_op_value_<?php echo $attribute_id; ?>[]"]').each(function() {
                                                if (i == 0) {
                                                    $(this).val("<?php echo !empty($records->opop[0]->op_op_value) ? $records->opop[0]->op_op_value : 0; ?>");
                                                }
                                                if (i == 1) {
                                                    $(this).val("<?php echo !empty($records->opop[1]->op_op_value) ? $records->opop[1]->op_op_value : 0; ?>");
                                                }
                                                if (i == 2) {
                                                    $(this).val("<?php echo  !empty($records->opop[2]->op_op_value) ? $records->opop[2]->op_op_value : 0;; ?>");
                                                }
                                                i++;
                                            });
                                        <?php } else if ($records->options[0]->option_type == 2) { ?>
                                            // alert(<?php echo $attribute_id; ?>);
                                            $('select[name="op_op_id_<?php echo $attribute_id; ?>[]"]').val('<?php echo $records->opop[0]->option_key_value; ?>');
                                            OptionOptionsOption('<?php echo $records->opop[0]->option_key_value; ?>', "<?php echo $attribute_id; ?>", function() {

                                                <?php for ($i = 0; $i < sizeof($records->opopop); $i++) { ?>
                                                    var updatedNumber = 0;
                                                    $('input[name="op_op_op_value_<?php echo $attribute_id; ?>[]"]').each(function() {
                                                        if (updatedNumber == <?php echo $i; ?>) {
                                                            $(this).val('<?php echo isset($records->opopop[$i]->op_op_op_value) ? $records->opopop[$i]->op_op_op_value : ''; ?>');
                                                        }
                                                        updatedNumber = updatedNumber + 1;
                                                    });
                                                <?php } ?>

                                                // Work on 25-06-2020 By Insys for T_post issue : START
                                                // For Multioption : START
                                                <?php for ($i = 0; $i < sizeof($records->opopopop); $i++) { ?>
                                                    var updatedNumber = 0;
                                                    $('select[name="op_op_op_op_id_<?php echo $attribute_id; ?>[]"]').each(function() {
                                                        if (updatedNumber == <?php echo $i; ?>) {
                                                            $(this).val('<?php echo isset($records->opopopop[$i]->option_key_value) ? $records->opopopop[$i]->option_key_value : ''; ?>');

                                                            // For fifth attribute value select : START
                                                            var op_five_option = $(".cls_op_five_" + <?php echo $attribute_id; ?>).length;
                                                            if (op_five_option > 0) {
                                                                setTimeout(function() {
                                                                    $(".cls_op_five_" + <?= $attribute_id ?>).change();
                                                                }, 1000);
                                                            }
                                                            // For fifth attribute value select : END
                                                        }
                                                        updatedNumber = updatedNumber + 1;
                                                    });
                                                <?php } ?>
                                                // For Multioption : END

                                                // For Fraction text box : START
                                                <?php $frc_key = 0;
                                                for ($i = 0; $i < sizeof($records->opop); $i++) {
                                                    if ($records->opop[$i]->op_op_value != '') {
                                                        $frac_val = explode(" ", $records->opop[$i]->op_op_value); ?>
                                                        $('input[name="op_op_value_<?php echo $attribute_id; ?>[]"]:eq(<?= $i ?>)').val('<?= $frac_val[0] ?>');
                                                        <?php if (isset($frac_val[1])) { ?>
                                                            $('select[name="fraction_<?php echo $attribute_id; ?>[]"]:eq(<?= $frc_key ?>)').val('<?= $frac_val[1] ?>');
                                                    <?php $frc_key++;
                                                        }
                                                    } ?>
                                                <?php } ?>
                                                // For Fraction text box : END
                                                // Work on 25-06-2020 By Insys for T_post issue : END

                                            });
                                        <?php } else if ($records->options[0]->option_type == 4) { ?>

                                            var mul_op_arr = new Array();
                                            <?php for ($i = 0; $i < sizeof($records->opop); $i++) { ?>
                                                var option_text = '<?= $records->opop[$i]->op_op_value ?>';

                                                // For multipleselect arr : START
                                                if (option_text.trim() == '') {
                                                    mul_op_arr.push('<?= $records->opop[$i]->op_op_id ?>');
                                                }
                                                // For multipleselect arr : END

                                                // $("#mul_op_op_id"+<?= $records->opop[$i]->op_op_id ?>).val();

                                                $('#mul_op_op_id' + <?= $records->opop[$i]->op_op_id ?> + ' > option').each(function() {
                                                    if (option_text.trim() == $(this).text().trim()) {
                                                        $("#mul_op_op_id" + <?= $records->opop[$i]->op_op_id ?>).val($(this).val());
                                                        $("#mul_op_op_id" + <?= $records->opop[$i]->op_op_id ?>).change();

                                                        // For fifth attribute value select : START
                                                        setTimeout(function() {
                                                            var op_five_option = $(".cls_op_five_" + <?= $records->attribute_id ?>).length;
                                                            var op_five_val = '<?= isset($records->opopopop[0]->option_key_value) ? $records->opopopop[0]->option_key_value : '' ?>';
                                                            if (op_five_option > 0) {
                                                                $(".cls_op_five_" + <?= $records->attribute_id ?>).val(op_five_val);
                                                            }
                                                        }, 2000);
                                                        // For fifth attribute value select : END
                                                    }
                                                });

                                                if ($('.op_op_text_box_' + <?= $records->opop[$i]->op_op_id ?>).length == 1) {
                                                    $('.op_op_text_box_' + <?= $records->opop[$i]->op_op_id ?>).val("<?= htmlentities($records->opop[$i]->op_op_value, ENT_QUOTES); ?>")
                                                }
                                            <?php } ?>

                                            // For Multi option multi select : START
                                            $(mul_op_arr).each(function(index, val_id) {
                                                var select_mul_arr = new Array();
                                                <?php for ($i = 0; $i < sizeof($records->opopop); $i++) { ?>
                                                    var option_key_val = '<?= !empty($records->opopop[$i]->option_key_value) ? $records->opopop[$i]->option_key_value : '' ?>';
                                                    if (option_key_val != '') {
                                                        strFine = option_key_val.substring(option_key_val.lastIndexOf('_'));
                                                        if (strFine == "_" + val_id) {
                                                            select_mul_arr.push(option_key_val);
                                                        }
                                                    }
                                                <?php } ?>
                                                $('#mulselect_op_op_op_id_' + val_id).selectpicker('val', select_mul_arr);
                                            });
                                            // For Multi option multi select : END
                                        <?php } else if ($records->options[0]->option_type == 6) { ?>

                                            var select_mul_arr = new Array();
                                            <?php for ($i = 0; $i < sizeof($records->opop); $i++) { ?>
                                                select_mul_arr.push('<?= $records->opop[$i]->option_key_value ?>');
                                            <?php } ?>
                                            $('#op_<?= $records->options[0]->option_id ?>').selectpicker('val', select_mul_arr);
                                        <?php } ?>

                                    });
                                    // }
                                <?php } else if ($records->attributes_type == 1) { ?>
                                    $('input.op_input_<?php echo $attribute_id; ?>').val('<?php echo $records->attribute_value; ?>');
                                <?php } else if ($records->attributes_type == 5) { ?>
                                    // Text Fraction
                                    <?php
                                    $attr_val_arr = explode(' ', $records->attribute_value);
                                    $attr_text_val = isset($attr_val_arr[0]) ? $attr_val_arr[0] : '';
                                    $attr_fraction_val = isset($attr_val_arr[1]) ? $attr_val_arr[1] : '';
                                    ?>
                                    $('input.op_input_<?php echo $attribute_id; ?>').val('<?php echo $attr_text_val; ?>');
                                    $('select.fraction_<?php echo $attribute_id; ?>').val('<?php echo $attr_fraction_val; ?>');
                            <?php }
                            } ?>
                            is_start_load_text_upcharge = true;
                            $("#attr input[type=text]").each(function() { // It's for input upcharge calculation
                                $(this).keyup();
                            })
                            is_start_load_text_upcharge = false;
                            // });

                            $(document).ready(function() {

                            })

                        }, "<?php echo !empty($get_product_order_info->fabric_price) ? $get_product_order_info->fabric_price : 0; ?>");
                    });
                });
            });

        });

        function changeheightwidth() {
            jQuery("#inputchnge").val("chnageHW");
        }
    </script>


<?php } ?>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $("#room").select2();
        //$('#pattern_id').select2();
        //$('#color_id').select2();
        if ($("#UpdateOrderItem").length > 0) {
            $("#new_room_anchor").hide();
        }
    });

    function getComboProductPattern(_this, productCount) {
        /*Update by ak*/
        var cart_rowid = $("#cart_rowid").val();
        var edit_type = $("#edit_type").val();

        if (cart_rowid != '') {
            cart_rowid = "/" + cart_rowid + "/" + edit_type;
        }
        /*Update by ak end*/
        var selectedProduct = $('.combo_product_id_' + productCount).val();
        $.ajaxQueue({
            url: "<?php echo base_url('b_level/order_controller/get_comob_product_pattern/') ?>" + selectedProduct + cart_rowid,
            dataType: 'JSON',
            type: 'get',
            success: function(r) {
                var patternSelected = '';
                if (r.patternCount == 1)
                    patternSelected = "selected";
                else
                    patternSelected = "";

                $('.combo_fabric_id_' + productCount).html('');
                $('.combo_fabric_id_' + productCount).append(`<option value="">---- Select One ----</option>`);
                $.each(r.getPatternData, function(key, value) {
                    $('.combo_fabric_id_' + productCount).append(`<option value="${value.pattern_model_id}" ${patternSelected}> ${value.pattern_name} </option>`);
                    if (r.patternCount == 1)
                        $('#combo_fabric_id_' + productCount).trigger('change');
                });
                /*Update by ak*/
                var i = 1;
                $.each(JSON.parse(r.combo_fabrics), function(index, value) {
                    $('#combo_fabric_id_' + i).val(value).trigger('change');
                    i++;
                });
            }
        });
    }

    function getComboProductColors(_this, patternCount) {
        /*Update by ak*/
        var cart_rowid = $("#cart_rowid").val();
        var edit_type = $("#edit_type").val();

        if (cart_rowid != '') {
            cart_rowid = "/" + cart_rowid + "/" + edit_type;
        }
        /*Update by ak end*/
        var product_id = $('.combo_product_id_' + patternCount).val();
        var selectedPattern = $('.combo_fabric_id_' + patternCount).val();
        $.ajaxQueue({
            url: "<?php echo base_url('b_level/order_controller/get_combo_product_pattern_color/') ?>" + product_id + "/" +
                selectedPattern + "/" + patternCount + cart_rowid,
            dataType: 'JSON',
            type: 'get',
            success: function(r) {
                $('.combo-color-section-' + patternCount).remove();
                $('.combo-pattern-section-' + patternCount).append(r.colorData);
                /*Update by ak*/
                var i = 1;
                $.each(JSON.parse(r.combo_colors), function(index, value) {
                    $('#combo_color_id_' + i).val(value).trigger('change');
                    i++;
                });
                if ($("#view_action").val() > 0) {
                    var cartData = JSON.parse(r.cartData);
                    $("#width").val(cartData.width);
                    $("#height").val(cartData.height);
                    $('#width_fraction_id').val(cartData.width_fraction_id).trigger('change');
                    $('#height_fraction_id').val(cartData.width_fraction_id).trigger('change');
                    if (cartData.att_options != "" && cartData.att_options != null) {
                        $.each(JSON.parse(cartData.att_options), function(index, value) {
                            $.each(value.options, function(index, op) {
                                $('.options_' + value.attribute_id).val(op.option_key_value).trigger('change');
                            });
                            $.each(value.opop, function(index, opopVal) {
                                var opopValData = opopVal.op_op_value.split(" ");
                                $("#" + opopVal.option_key_value).val(opopValData[0]);
                                $(".key_text_fraction_" + index).val(opopValData[1]);
                            });
                        });
                    }
                }
                /*Update by ak end*/
            }
        });
    }

    // For first level textbox and third level value change and text fraction event for Drapery : START
    $('body').on('blur', 'input[name="attribute_value[]"] , .cls_text_op_op_value, .convert_text_fraction', function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var is_drapery_cat = $("#is_drapery_cat").val();

        if (is_drapery_cat == 1) {
            calculate_drapery_price();
            // cal();
            setTimeout(function() {
                cal();
            }, 2000);
        }
    });
    // For first level textbox and third level value change event for Drapery : END

    // For fraction value change event for Drapery : START
    $(document).on('change', '.select_text_fraction', function() {
        //$('body').on('change', '.select_text_fraction', function (event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        var is_drapery_cat = $("#is_drapery_cat").val();
        if (is_drapery_cat == 1) {
            calculate_drapery_price();
            // cal();
            setTimeout(function() {
                cal();
            }, 2000);
        }
    });
    // For fraction value change event for Drapery : END
    $(document).on('change', '.convert_text_fraction', function() {
        //$('body').on('change', '.convert_text_fraction', function (event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var lblText = $(this).parent().parent().find('label').text();
        if (lblText == 'Window Depth') {
            //alert('hi');
            //alert($(this).closest( "select.select_text_fraction" ).find("option:selected").text());
            var obj = $(this);


            $("[data-attr-name=Mount]").filter(
                function(index) {
                    if (($(this).find("option:selected").text() == 'IB') || ($(this).find("option:selected").text() == 'IM')) {

                        if (((obj.val() % 1) != 0) && (obj.val() <= 1.75)) {
                            swal.fire("If IM <= 1 3/4inch, can't do IM");
                            $(this).find("option:selected").removeAttr("selected");
                            obj.val('');
                            $('select.select_text_fraction').prop('selectedIndex', 0);

                            return;
                        }


                    }
                });
        }
    });
    $(document).on('change', '.select_text_fraction', function() {
        // $('body').on('change', '.select_text_fraction', function (event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var fractionval = eval($(this).find("option:selected").text());
        var depth = $('.convert_text_fraction').val();
        var obj1 = $(this);
        if (($("[data-attr-name=Mount]").find("option:selected").text() == 'IB') || ($("[data-attr-name=Mount]").find("option:selected").text() == 'IM')) {
            if ($('.convert_text_fraction').val() == 1) {
                if (fractionval <= 0.75) {
                    swal.fire("If IM <= 1 3/4inch, can't do IM");

                    $("[data-attr-name=Mount]").filter(
                        function(index) {
                            $(this).find("option:selected").removeAttr("selected");

                        });
                    $(".convert_text_fraction").val('');
                    //obj1.prop('selectedIndex','');


                }

            } //
        }

    });

    $('body').on('change', '[data-attr-name=Mount]', function() {


        $(this).parent().find('.convert_text_fraction').val('');
        $(this).parent().find('select.select_text_fraction').prop('selectedIndex', 0);


    });

    // For textbox value change event for Drapery : START
    // Applied on like Layered Trim Entry option text box
    $('body').on('change', 'input[type="text"]', function() {


        var is_drapery_cat = $("#is_drapery_cat").val();
        if (is_drapery_cat == 1) {
            // calculate_drapery_price();
            cal();
        }
    });
    // For textbox value change event for Drapery : END


    function calculate_drapery_price() {
        $(".drapery_price_section").hide();
        var category_id = $("#category_id").val();
        // var width = $("#width").val();
        var product_id = $("#product_id").val();
        var is_drapery_cat = $("#is_drapery_cat").val();

        // if(category_id != '' && width != '' && product_id != '' && is_drapery_cat == 1) {
        if (category_id != '' && product_id != '' && is_drapery_cat == 1) {
            $(".drapery_price_section").show();
            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/calculate_drapery_price/') ?>",
                dataType: 'JSON',
                type: 'post',
                data: $("form.frm_product_order_form").serialize(),
                async: false,
                success: function(res) {
                    // console.log(res);

                    var drpery_width_price = 0;
                    var drpery_width_price_round_val = 0;
                    var drpery_height_price = 0;
                    var drpery_cut_price = 0;
                    var drpery_yard_price = 0;
                    var drpery_product_price = 0;

                    if (res.is_product_formula == 'Yes') {
                        var drpery_width_price = res.drpery_width_price;
                        var drpery_width_price_round_val = res.drpery_width_price_round_val;
                        var drpery_height_price = res.drpery_height_price;
                        var drpery_cut_price = res.drpery_cut_price;
                        var drpery_yard_price = res.drpery_yard_price;
                        var drpery_product_price = res.drpery_product_price;
                    }

                    if (isNaN(drpery_product_price)) {
                        drpery_product_price = 0;
                    }

                    $("#final_drapery_price").val(drpery_product_price);

                    // $(".drapery_price_section .drape_width_price").html(drpery_width_price);
                    // $(".drapery_price_section #drape_width_price_round_val").val(drpery_width_price_round_val);
                    // $(".drapery_price_section .drape_height_price").html(drpery_height_price);
                    // $(".drapery_price_section .drape_cuts_price").html(drpery_cut_price);
                    // $(".drapery_price_section .drape_yard_price").html(drpery_yard_price);
                    // $(".drapery_price_section .drape_product_price").html(var_currency + drpery_product_price);

                    // Show Qty wise price (means qty * price)
                    product_qty = $("#product-qty").val();

                    $(".drapery_price_section .drape_width_price").html(drpery_width_price * product_qty);
                    $(".drapery_price_section #drape_width_price_round_val").val(drpery_width_price_round_val * product_qty);
                    $(".drapery_price_section .drape_height_price").html(drpery_height_price * product_qty);
                    $(".drapery_price_section .drape_cuts_price").html(drpery_cut_price * product_qty);
                    $(".drapery_price_section .drape_yard_price").html(drpery_yard_price * product_qty);
                    $(".drapery_price_section .drape_product_price").html(var_currency + (drpery_product_price * product_qty));

                    // Drapery hidden field value : START
                    $("#hid_drapery_of_cuts").val(drpery_width_price);
                    $("#hid_drapery_of_cuts_only_panel").val(drpery_width_price_round_val);
                    $("#hid_drapery_cut_length").val(drpery_height_price);
                    $("#hid_drapery_total_fabric").val(drpery_cut_price);
                    $("#hid_drapery_total_yards").val(drpery_yard_price);
                    // Drapery hidden field value : END

                    // ====== For Drapery finished width calculation : START ======
                    var choose_type_option = $("select[data-attr-name='<?= DRAPERY_CHOOSE_TYPE ?>'] option:selected").text(); // Select Box
                    var finished_width_mul_val = 1;
                    if (choose_type_option == '<?= DRAPERY_PAIR ?>') {
                        finished_width_mul_val = 2;
                    }

                    // Get Road Width value : START
                    var road_width = $("input[data-attr-name='<?= DRAPERY_ROD_WIDTH ?>']").val();
                    if (road_width === '') {
                        road_width = 0;
                    }
                    road_width = (isNaN(road_width) ? 0 : road_width);
                    var road_width_fraction = 0;
                    if ($("input[data-attr-name='<?= DRAPERY_ROD_WIDTH ?>']").hasClass("convert_text_fraction")) {
                        // If fraction then get value
                        var road_width_fraction_text = $("input[data-attr-name='<?= DRAPERY_ROD_WIDTH ?>']").parent().next('.col-sm-2').children('.select_text_fraction').children('option:selected').text();
                        if (road_width_fraction_text) {
                            road_width_fraction = (road_width_fraction_text.split("/")[0] / road_width_fraction_text.split("/")[1]);
                            road_width_fraction = (isNaN(road_width_fraction) ? 0 : road_width_fraction);
                        }
                    }
                    var final_road_width = parseFloat(road_width) + parseFloat(road_width_fraction);
                    // Get Road Width value : END

                    // Get Return Attribute value : START
                    var return_attr_val = $("select[data-attr-name='<?= DRAPERY_RETURN ?>'] option:selected").text();
                    var final_return_val = 0;
                    if (return_attr_val == '<?= DRAPERY_RETURN_MANUAL ?>') {
                        // Check if manual
                        var return_manual_val = $("select[data-attr-name='<?= DRAPERY_RETURN ?>']").parent().siblings('.col-sm-12').children('.row').find('.convert_text_fraction').val();
                        var return_manual_fraction_text = $("select[data-attr-name='<?= DRAPERY_RETURN ?>']").parent().siblings('.col-sm-12').children('.row').find('.select_text_fraction').children('option:selected').text();
                        var return_manual_fraction_val = 0;
                        if (return_manual_fraction_text) {
                            return_manual_fraction_val = (return_manual_fraction_text.split("/")[0] / return_manual_fraction_text.split("/")[1]);
                            return_manual_fraction_val = (isNaN(return_manual_fraction_val) ? 0 : return_manual_fraction_val);
                        }
                        final_return_val = parseFloat(return_manual_val) + parseFloat(return_manual_fraction_val);
                    } else {
                        // if exist value
                        final_return_val = $("select[data-attr-name='<?= DRAPERY_RETURN ?>']").parent().siblings('.col-sm-12').find('.drapery_attr_price_value').val();
                    }
                    final_return_val = (isNaN(final_return_val) ? 0 : parseFloat(final_return_val));
                    // Get Return Attribute value : END

                    // Get Overlap Attribute value : START
                    var overlap_attr_val = $("select[data-attr-name='<?= DRAPERY_OVERLAP ?>'] option:selected").text();
                    var final_overlap_val = 0;
                    if (overlap_attr_val == '<?= DRAPERY_OVERLAP_MANUAL ?>') {
                        // Check if manual
                        var overlap_manual_val = $("select[data-attr-name='<?= DRAPERY_OVERLAP ?>']").parent().siblings('.col-sm-12').children('.row').find('.convert_text_fraction').val();
                        var overlap_manual_fraction_text = $("select[data-attr-name='<?= DRAPERY_OVERLAP ?>']").parent().siblings('.col-sm-12').children('.row').find('.select_text_fraction').children('option:selected').text();
                        var overlap_manual_fraction_val = 0;
                        if (overlap_manual_fraction_text) {
                            overlap_manual_fraction_val = (overlap_manual_fraction_text.split("/")[0] / overlap_manual_fraction_text.split("/")[1]);
                            overlap_manual_fraction_val = (isNaN(overlap_manual_fraction_val) ? 0 : overlap_manual_fraction_val);
                        }
                        final_overlap_val = parseFloat(overlap_manual_val) + parseFloat(overlap_manual_fraction_val);
                    } else {
                        // if exist value
                        final_overlap_val = $("select[data-attr-name='<?= DRAPERY_OVERLAP ?>']").parent().siblings('.col-sm-12').find('.drapery_attr_price_value').val();
                    }
                    final_overlap_val = (isNaN(final_overlap_val) ? 0 : parseFloat(final_overlap_val));
                    // Get Overlap Attribute value : END

                    var final_finished_width = (parseFloat(final_road_width) + ((parseFloat(final_return_val) + parseFloat(final_overlap_val)) * parseFloat(finished_width_mul_val)));
                    final_finished_width = (isNaN(final_finished_width) ? 0 : final_finished_width.toFixed(2));
                    $("#hid_drapery_finished_width").val(final_finished_width);

                    // ====== For Drapery finished width calculation : END ======

                }
            });
        } else {
            $(".drapery_price_section").hide();
        }
    }


    // Angled Bottom Static Condition for Fenetex : START
    $(document).on('change', "select[name='fraction_221[]'], select[name='fraction_254[]']", function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        $(this).parent().prev().children('.convert_text_fraction').blur();
    });

    $("body").on('blur', "input[name='op_op_value_221[]'], input[name='op_op_value_254[]']", function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var _this = $(this);
        var angled_height = _this.val();
        var angled_height_fraction_length = _this.parent().next().children('.select_text_fraction').length;
        var angled_height_fraction_val = 0;
        if (angled_height_fraction_length > 0) {
            var angled_height_select_text = _this.parent().next().children('.select_text_fraction').find(":selected").text();
            angled_height_fraction_val = angled_height_select_text.split("/")[0] / angled_height_select_text.split("/")[1];
        }

        var angled_height = parseFloat(angled_height) + (isNaN(angled_height_fraction_val) ? 0 : angled_height_fraction_val);
        var no_valid_height = check_valid_height(angled_height);

        if (no_valid_height) {
            // Not valid height
            _this.val('');
            _this.parent().next().children('.select_text_fraction').val('');
            swal.fire('Please enter a value which is equal to or less than Product Height');
        }
    });

    function check_valid_height(angled_height) {
        // var height = $("#height").val();
        var hif = ($("#height_fraction_id :selected").text().split("/")[0] / $("#height_fraction_id :selected").text().split("/")[1]);
        var height = parseFloat($('#height').val()) + (isNaN(hif) ? 0 : hif);

        if (angled_height > height) {
            return true;
        } else {
            return false;
        }
    }
    // Angled Bottom Static Condition for Fenetex : END

    // For Combo Hurricane attribute show some attribute and vice versa for fenetex : START
    $(document).on('change', "#combo_product_id_1", function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var product_1_val = $(this).val();
        // 259 for Hurricane Screen Product id for combo product
        if (product_1_val == '259') {
            // Hurricane Screen Product
            // 10034_1032 : Combo Hi-Load Track/One-Track
            // 10037_1035 : Combo Type B/Type H
            $(".options_421 option").each(function() {
                if ($(this).val() != '' && $(this).text() != 'Combo Hi-Load Track/One-Track' && $(this).text() != 'Combo Type B/Type H') {
                    $(this).attr("disabled", true);
                } else {
                    $(this).attr("disabled", false);
                }
            });
        } else {
            // Other Product
            $(".options_421 option").each(function() {
                if ($(this).text() == 'Combo Hi-Load Track/One-Track' || $(this).text() == 'Combo Type B/Type H') {
                    $(this).attr("disabled", true);
                } else {
                    $(this).attr("disabled", false);
                }
            });
        }
    });

    // For Combo Hurricane attribute show some attribute and vice versa for fenetex : END
    function changeLength(load_style = true) {
        if ($('.cords_length').length) {
            var lengths = $('.cords_length').val();
            var cordLength = lengths.replaceAll('$', '');
            $('.cords_length_val').val(cordLength);
            var cordLengths = $('.cords_length_val').val();
            var cordLengthsVal = Number(cordLengths);
            var contribute_price_nearest = parseFloat(Math.ceil(cordLengthsVal * 2) / 2);
            $('.value_show').html('$' + contribute_price_nearest);
            $('.cords_length').val(contribute_price_nearest);
            $('.cord_len_val').val(contribute_price_nearest);
        }
        if (load_style) {
            loadPStyle();
        }
    }

    function changeWidth() {
        $('.cord_len_val').val('');
        var up_condition_width1 = $("#change_width").val();
        var up_condition_width2 = $("#width").val();
        var cordLengths = $('.cords_length_val').val();
        var cordLengthsVal = Number(cordLengths);
        var contribute_price_nearest = parseFloat(Math.ceil(cordLengthsVal * 2) / 2);
        var cordVal = $('.cords_length').val();
        if (Number(up_condition_width1) == Number(up_condition_width2)) {
            $('.cords_length').val(contribute_price_nearest);
            $('.value_show').html('$' + contribute_price_nearest);
        } else {
            $('.cords_length').val(cordVal);
            $('.value_show').html('$' + cordVal);
            var manual_entry = $('.manual_entry_chk').val();
            var manually = Number(manual_entry);
            $('.manual_entry_archtop').html('$' + manually.toFixed(2));
        }
    }

    function changeHeight() {
        $('.cord_len_val').val('');
        var up_condition_height1 = $("#change_height").val();
        var up_condition_height2 = $("#height").val();
        var cordLengths = $('.cords_length_val').val();
        var cordLengthsVal = Number(cordLengths);
        var contribute_price_nearest = parseFloat(Math.ceil(cordLengthsVal * 2) / 2);
        var cordVal = $('.cords_length').val();
        if (Number(up_condition_height1) == Number(up_condition_height2)) {
            $('.cords_length').val(contribute_price_nearest);
            $('.value_show').html('$' + contribute_price_nearest);
        } else {
            $('.cords_length').val(cordVal);
            $('.value_show').html('$' + cordVal);
            var manual_entry = $('.manual_entry_chk').val();
            var manually = Number(manual_entry);
            $('.manual_entry_archtop').html('$' + manually.toFixed(2));
        }
    }

    // Manage phase_2 Check box :: START

    $("#phase_2").on("change", function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }
        if ($(this).prop("checked") == true) {
            $(".phase_2_condition_div").show()
            $("#phase_2_condition_instruction_html").show()
            $("#phase_2_condition").prop("required", true)
            if ($("#housingStyleAttributeName").val() != '') {
                $('select[name="' + $("#housingStyleAttributeName").val() + '"]').addClass('disable-select');
            }

            get_phase_2_conditions_on_check_click();

        } else {
            $("#phase_2_condition").removeAttr("required")
            $(".phase_2_condition_div").hide().html("")
            $("#phase_2_condition_instruction_html").hide()
            phase_2_condition_on_change();
        }
        if (($("#housingStyleAttributeName").val() != '') && ($(this).prop("checked") == false)) {
            $('select[name="' + $("#housingStyleAttributeName").val() + '"]').removeClass('disable-select');
        }
    })

    // Manage phase_2 Check box  :: END

    $(document).on("change", ".selectpicker.select-all", function(event) {
        if (event) {
            // event.stopImmediatePropagation();
        }

        var selectPicker = $(this);
        var selectAllOption = selectPicker.find('option.select-all');
        var checkedAll = selectAllOption.prop('selected');
        var optionValues = selectPicker.find('option[value!="[all]"][data-divider!="true"]');

        if (checkedAll) {
            // Process 'all/none' checking
            var allChecked = selectAllOption.data("all") || false;

            if (!allChecked) {
                optionValues.prop('selected', true).parent().selectpicker('refresh');
                selectAllOption.data("all", true);
            } else {
                optionValues.prop('selected', false).parent().selectpicker('refresh');
                selectAllOption.data("all", false);
            }

            selectAllOption.prop('selected', false).parent().selectpicker('refresh');
        } else {
            // Clicked another item, determine if all selected
            var allSelected = optionValues.filter(":selected").length == optionValues.length;
            selectAllOption.data("all", allSelected);
        }

        if ($(this).attr("id") == "phase_2_condition") {
            phase_2_condition_on_change();
        }

    }).trigger('change');


    old_phase_2_attr = [];
    new_phase_2_attr = [];

    function phase_2_condition_on_change() {

        phase_2_attr = "";
        phase_2_up_id = "";
        not_selected_phase_2_attr = "";
        new_prev_selected_val = '';

        $("#phase_2_condition option").each(function(a) {
            if ($(this).prop("selected") == true) {
                new_prev_selected_val = $(this).val();
                if ($(this).attr("data-attr") != "") {
                    if (phase_2_attr != "") {
                        phase_2_attr += ','
                    }
                    phase_2_attr += $(this).attr("data-attr");
                }
                if ($(this).attr("data-up-condition-id") != "") {
                    if (phase_2_up_id != "") {
                        phase_2_up_id += ','
                    }
                    phase_2_up_id += $(this).attr("data-up-condition-id");
                }
                if ($(this).attr("data-housing") != "") {
                    $("#phase2_housing_style").val($(this).attr("data-housing"));
                    $("select.op_op_load option").filter(function() {
                        if ($(this).text() == $("#phase2_housing_style").val()) {
                            var housingStyleAttrName = $(this).parent().attr('name');
                            $("#housingStyleAttributeName").val(housingStyleAttrName);
                            // console.log($(this).parent())
                            $(this).parent().addClass('disable-select');

                        }
                        return $(this).text() == $("#phase2_housing_style").val();
                    }).prop("selected", true).trigger('change');

                } else {
                    $("#phase2_housing_style").val($(this).attr("data-housing"));
                    $("select.op_op_load option").filter(function() {
                        if ($(this).text() != $("#phase2_housing_style").val()) {
                            var housingStyleAttrName = $(this).parent().attr('name');
                            $("#housingStyleAttributeName").val(housingStyleAttrName);
                            // console.log($(this).parent())
                            $(this).parent().removeClass('disable-select');

                        }
                        return $(this).text() != $("#phase2_housing_style").val();
                    })

                }
            } else {

                if ($(this).attr("data-attr") != "" && prev_selected_val == $(this).val()) {
                    if (not_selected_phase_2_attr != "") {
                        not_selected_phase_2_attr += ','
                    }
                    not_selected_phase_2_attr += $(this).attr("data-attr");
                }
            }

        });
        prev_selected_val = new_prev_selected_val;
        /* if($("#housingStyleAttributeName").val() != '')
         {
          $('select[name="' + $("#housingStyleAttributeName").val() + '"]').removeClass('disable-select');
         }*/


        new_phase_2_attr = phase_2_attr.split(",");
        if (phase_2_attr != "") {
            old_phase_2_attr = new_phase_2_attr;
        } else {

            new_phase_2_attr = old_phase_2_attr;
        }

        AlreadyAttrPos = [];
        $("#attr select, #attr input").each(function() {
            if ($(this).val() != null && $(this).val() != undefined && $(this).val() != "" && $(this).attr("type") != "hidden") {
                if (!jQuery.isArray($(this).val())) {
                    attribute_arr = $(this).val().split('_');
                    if ($(this).parent("div").hasClass("cls_mul_option") || $(this).find("option").length > 0) {
                        attr_id = attribute_arr[0];
                    } else {
                        attr_id = attribute_arr[attribute_arr.length - 1];
                    }

                    if ((jQuery.inArray(attr_id, new_phase_2_attr) !== -1 && jQuery.inArray(attr_id, AlreadyAttrPos) == -1) || jQuery.inArray(attr_id, not_selected_phase_2_attr.split(",")) !== -1) {
                        AlreadyAttrPos.push(attr_id)
                        $(this).change()
                    }
                }
            }
        })
    }


    function get_phase_2_conditions(data) {
        if (data) {
            $("#phase_2_condition_div").html(data);
            $("#phase_2_condition").selectpicker('val', [<?php echo @$get_product_order_info->phase_2_condition; ?>]);
            if ($("#phase_2").prop("checked") == true) {
                $("#phase_2_condition").attr("required", true)
            } else {

                $("#phase_2_condition").removeAttr("required")
            }
        }
    }

    function get_phase_2_conditions_on_check_click() {
        category_id = $("#category_id").val();
        if (category_id) {
            $.ajaxQueue({
                url: "<?php echo base_url(); ?>b_level/order_controller/get_phase_2_conditions/" + category_id,
                type: 'GET',
                async: false,
                success: function(r) {
                    $("#phase_2_condition_div").html(r);
                    $("#phase_2_condition").selectpicker('val', [<?php echo @$get_product_order_info->phase_2_condition; ?>]);
                    if ($("#phase_2").prop("checked") == true) {
                        $("#phase_2_condition").attr("required", true)
                    } else {

                        $("#phase_2_condition").removeAttr("required")
                    }
                }
            });
        }
    }

    /*
        START :: Daynamic Text area Charactor limit set 
    */
    $(document).ready(function() {
        $("textarea").each(function() {
            teaxtarea_char_limit($(this))
        })
        $("textarea").keyup(function() {
            teaxtarea_char_limit($(this))
        });
    })

    function teaxtarea_char_limit(that) {
        maxLength = that.attr("maxlength")
        if (maxLength) {
            parent = that.parent()
            var leng = that.val().length;
            parent.find(".current").text(leng);
            parent.find(".maximum").html('/ ' + maxLength);
        }
    }

    /*
        END :: Daynamic Text area Charactor limit set 
    */
    var text_timout = null;
    var input_item = null;

    function checkTextboxUpcharge(that) {
        // that.val(that.val().replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'));

        if (text_timout != null && !is_start_load_text_upcharge) {
            clearTimeout(text_timout)
        } else {
            input_item = that;
        }

        changeLength(false)
        text_timout = setTimeout(function() {

            var attribute_id = that.attr("data-attr-id");
            var up_level = that.attr("data-level");
            var text_type = that.attr("data-text-type");
            var up_class = '';

            if (up_level == 0) {

                up_class = 'text_box_' + attribute_id;

            } else if (up_level == 1) {
                up_class = 'op_text_box_' + attribute_id;

            } else if (up_level == 2) {
                up_class = 'op_op_text_box_' + attribute_id;

            } else if (up_level == 3) {
                up_class = 'op_op_op_text_box_' + attribute_id;

            } else if (up_level == 4) {

                up_class = 'op_op_op_op_text_box_' + attribute_id;
            }
            apply_upcharges_condition(attribute_id, up_level, up_class);

            if (attr_related_attr_class_list[up_class]) {
                $.each(attr_related_attr_class_list[up_class], function(key, value) {
                    attr_type = value['up_attribute_position'].attr("type")
                    up_attribute_id1 = value['up_attribute_position'].val()
                    up_attribute_id = up_attribute_id1.split("_")[0]

                    if (attr_type == "text") {
                        if (attribute_id != value['up_attribute_id']) {
                            apply_upcharges_condition(value['up_attribute_id'], value['up_level'], value['up_class'], value['not_parent']);
                        }

                    } else if (value['up_attribute_id'] == up_attribute_id) {
                        apply_upcharges_condition(up_attribute_id, value['up_level'], value['up_class'], value['not_parent']);
                    } else if (value['up_attribute_id'] == up_attribute_id1) {
                        apply_upcharges_condition(up_attribute_id1, value['up_level'], value['up_class'], value['not_parent']);
                    }
                });
            }
            if (text_type != "code-legth-val") {
                loadPStyle();
            }
        }, 1000)
    }

    function nextRoundHalf(num) {
        decimal = num.toFixed(2) - Math.floor(num);

        if (decimal != 0.50 && decimal < 0.50 && decimal != 0) {
            return Math.round(num) + 0.5;

        } else if (decimal > 0.5) {
            return parseFloat(num.toFixed(0));

        } else {

            return parseFloat(num.toFixed(1));
        }
    }

    /*
        Increase & decrease input field value
    */
    // $(function () {

    //     set_($('.inc-dec-qty'), 0);  //return 0 maximum digites, IF 0 then no limit

    //     $("#product-qty").keyup(function(){
    //         val = $(this).val() 
    //         if(val == 0 || val == "" || val == undefined || val == null)
    //         {
    //             $(this).val(1);
    //         }
    //         cal()
    //     })

    //     function set_(_this, max) {
    //         var block = _this.parent();
    //         block.find(".increase").click(function () {
    //             var currentVal = _this.val() ? parseInt(_this.val()) : 0;
    //             if (currentVal != NaN && (((currentVal + 1) <= max) || max == 0)) {
    //                 _this.val(currentVal + 1);
    //             }
    //             cal()
    //         });

    //         block.find(".decrease").click(function () {
    //             var currentVal = _this.val() ? parseInt(_this.val()) : 0;
    //             if (currentVal != NaN && currentVal != 1) {
    //                 _this.val(currentVal - 1);
    //             }
    //             cal()
    //         });
    //     }
    // });

    $(document).ready(function() {
        if ($("#view_action").val() == 1) {
            get_form_room_details();
        }

        function get_form_room_details() {

            qty = $("#product-qty").val();
            form_item_id = $("#form_item_id").val();
            old_room = $("#form_old_room").val();

            $.ajaxQueue({
                url: "<?php echo base_url('b_level/order_controller/get_form_room_details/') ?>",
                type: 'POST',
                data: {
                    room: old_room,
                    item_id: form_item_id
                },
                async: false,
                success: function(response) {
                    response = JSON.parse(response);

                    $("#form_room_arr").val(response.form_room_arr)
                    $("#form_missing_key").val(response.form_missing_key)
                }
            });
        }
    })

    $(function() {

        set_($('.inc-dec-qty'), 0); //return 0 maximum digites, IF 0 then no limit
        var ProductQtyTimeOut = null;
        $("#product-qty").keyup(function() {

            var is_drapery_cat = $("#is_drapery_cat").val();

            val = $(this).val()
            if (val == 0 || val == "" || val == undefined || val == null) {
                $(this).val(1);
            }

            if ($("#view_action").val() == 1) {

                if ($("#product-qty").val() > 1000) {
                    $("#product-qty").val(1000)
                }
                if (ProductQtyTimeOut != null) {
                    clearTimeout(ProductQtyTimeOut)
                }
                ProductQtyTimeOut = setTimeout(function() {
                    var qty = parseInt($("#product-qty").val());
                    var item_id = $("#form_item_id").val();
                    var form_missing_key = $("#form_missing_key").val();
                    var room = $("#room").val();

                    $.ajaxQueue({
                        url: "<?php echo base_url('b_level/order_controller/form_get_all_cart_data') ?>",
                        type: 'POST',
                        async: false,
                        data: {
                            qty: qty,
                            room: room,
                            item_id: item_id
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            $("#form_room_arr").val(response.form_room_arr)
                            $("#form_missing_key").val(response.form_missing_key)
                            if (is_drapery_cat == 1) {
                                calculate_drapery_price();
                                manage_no_of_rings_val(1);
                            }
                            cal()
                        }
                    });
                }, 500)
            } else {
                if (is_drapery_cat == 1) {
                    calculate_drapery_price();
                    manage_no_of_rings_val(1);
                }
            }
            cal()
        })

        function set_(_this, max) {
            var block = _this.parent();

            block.find(".increase").click(function() {

                var is_drapery_cat = $("#is_drapery_cat").val();

                if ($("#view_action").val() == 1) {

                    if ($("#product-qty").val() < 1000) {

                        var qty = parseInt($("#product-qty").val()) + 1;
                        var item_id = $("#form_item_id").val();
                        var form_missing_key = $("#form_missing_key").val();
                        var room = $("#room").val();
                        var form_product_hide_room = $("#form_product_hide_room").val();
                        var form_hide_room = $("#form_hide_room").val();

                        var missingkeyarr = {};
                        if (form_missing_key != '' && form_product_hide_room == 0 && form_hide_room == 0) {
                            jQuery.each(JSON.parse(form_missing_key), function(key, value) {
                                var val = value + 1;
                                missingkeyarr[value] = old_room + " " + val;
                            });
                            var selectedval = "";
                            swal.fire({
                                title: 'Is this order for...',
                                input: 'radio',
                                inputOptions: missingkeyarr,
                                inputPlaceholder: 'required',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'No',
                                inputValidator: function(value) {
                                    return new Promise(function(resolve, reject) {
                                        if (value !== '') {
                                            resolve();
                                        } else {
                                            resolve('You need to select a Tier');
                                        }
                                    });
                                }
                            }).then(function(result) {

                                if (result.value) {
                                    selectedval = result.value;
                                    $.ajaxQueue({
                                        url: "<?php echo base_url('b_level/order_controller/form_get_all_cart_data') ?>",
                                        type: 'POST',
                                        async: false,
                                        data: {
                                            qty: qty,
                                            type: 'addmissingcounter',
                                            room: room,
                                            roomcounter: result.value,
                                            item_id: item_id,
                                        },
                                        success: function(response) {
                                            response = JSON.parse(response);

                                            var qty = parseInt($("#product-qty").val()) + 1;
                                            $("#product-qty").val(qty)

                                            $("#form_room_arr").val(response.form_room_arr)
                                            $("#form_missing_key").val(response.form_missing_key)
                                            if (is_drapery_cat == 1) {
                                                calculate_drapery_price();
                                                manage_no_of_rings_val(1);
                                            }
                                            cal()
                                        }
                                    });
                                } else {
                                    $.ajaxQueue({
                                        url: "<?php echo base_url('b_level/order_controller/form_get_all_cart_data') ?>",
                                        type: 'POST',
                                        async: false,
                                        data: {
                                            qty: qty,
                                            room: room,
                                            item_id: item_id
                                        },
                                        success: function(response) {
                                            response = JSON.parse(response);

                                            var qty = parseInt($("#product-qty").val()) + 1;
                                            $("#product-qty").val(qty)

                                            $("#form_room_arr").val(response.form_room_arr)
                                            $("#form_missing_key").val(response.form_missing_key)
                                            if (is_drapery_cat == 1) {
                                                calculate_drapery_price();
                                                manage_no_of_rings_val(1);
                                            }
                                            cal()
                                        }
                                    });
                                }
                            });
                        } else {
                            if (ProductQtyTimeOut != null) {
                                clearTimeout(ProductQtyTimeOut)
                            }
                            ProductQtyTimeOut = setTimeout(function() {
                                $.ajaxQueue({
                                    url: "<?php echo base_url('b_level/order_controller/form_get_all_cart_data') ?>",
                                    type: 'POST',
                                    async: false,
                                    data: {
                                        qty: qty,
                                        room: room,
                                        item_id: item_id
                                    },
                                    success: function(response) {
                                        response = JSON.parse(response);

                                        var qty = parseInt($("#product-qty").val()) + 1;
                                        $("#product-qty").val(qty)

                                        $("#form_room_arr").val(response.form_room_arr)
                                        $("#form_missing_key").val(response.form_missing_key)
                                        if (is_drapery_cat == 1) {
                                            calculate_drapery_price();
                                            manage_no_of_rings_val(1);
                                        }
                                        cal()

                                    }
                                });
                            }, 500)
                        }
                    }
                } else {

                    var currentVal = _this.val() ? parseInt(_this.val()) : 0;
                    if (currentVal != NaN && (((currentVal + 1) <= max) || max == 0)) {
                        _this.val(currentVal + 1);
                    }
                    if (is_drapery_cat == 1) {
                        calculate_drapery_price();
                        manage_no_of_rings_val(1);
                    }
                    cal()
                }
            });


            block.find(".decrease").click(function() {

                var is_drapery_cat = $("#is_drapery_cat").val();

                if ($("#view_action").val() == 1) {
                    if ($("#product-qty").val() > 1) {

                        var item_id = $("#form_item_id").val();
                        var form_missing_key = $("#form_missing_key").val();
                        var form_room_arr = $("#form_room_arr").val();
                        var room = $("#room").val();

                        var hid_product_hide_room = $("#form_product_hide_room").val();
                        var hid_hide_room = $("#form_hide_room").val();

                        var new_hiddencounter = {};

                        if (hid_product_hide_room == 0 && hid_hide_room == 0) {
                            html = '';
                            hiddencounterParse = JSON.parse(form_room_arr)
                            $.each(hiddencounterParse, function(key, val) {
                                html += '<h4><input type="checkbox" value="' + key + '" class="rooms" data-name="' + val + '" /> ' + val + '</h4>'
                            })

                            swal.fire({
                                title: 'Please select room you want to remove',
                                html: '<div class="remove-room-div">' + html + '</div>',
                                confirmButtonText: 'Ok',
                                cancelButtonText: 'Cancel',
                                preConfirm: () => {
                                    var totalRoom = Swal.getPopup().querySelectorAll('.rooms')
                                    new_arr = [];
                                    $.each(totalRoom, function(i) {
                                        if ($(this).prop("checked") == true) {
                                            new_arr.push($(this).val())
                                        } else {

                                            new_hiddencounter[$(this).val()] = $(this).attr("data-name");
                                        }
                                    });
                                    return new_arr
                                }
                            }).then((result) => {

                                if (result.value) {
                                    if (hiddencounterParse.length != result.value.length) {
                                        if (result.value.length > 0) {

                                            var qty = $("#product-qty").val() - result.value.length;
                                            $("#product-qty").val(qty)

                                            formRemoveQuantity(qty, result.value, item_id, room)

                                        } else {
                                            return false;
                                        }
                                    } else {

                                        swal.fire("Please letf atleast one room.");
                                    }
                                }
                            });
                        } else {
                            if (ProductQtyTimeOut != null) {
                                clearTimeout(ProductQtyTimeOut)
                            }
                            ProductQtyTimeOut = setTimeout(function() {
                                var qty = $("#product-qty").val() - 1;
                                $("#product-qty").val(qty)
                                formRemoveQuantity(qty, "", item_id, room)
                            }, 500)
                            if (is_drapery_cat == 1) {
                                calculate_drapery_price();
                                manage_no_of_rings_val(1);
                            }
                            cal()
                        }
                    }
                } else {

                    var currentVal = _this.val() ? parseInt(_this.val()) : 0;
                    if (currentVal != NaN && currentVal != 1) {
                        _this.val(currentVal - 1);
                    }
                    if (is_drapery_cat == 1) {
                        calculate_drapery_price();
                        manage_no_of_rings_val(1);
                    }
                    cal()
                }
            });
        }

        $("#room").change(function() {

            if ($("#view_action").val() == 1) {
                if (ProductQtyTimeOut != null) {
                    clearTimeout(ProductQtyTimeOut)
                }
                ProductQtyTimeOut = setTimeout(function() {
                    var qty = parseInt($("#product-qty").val());
                    var form_item_id = $("#form_item_id").val();
                    var form_old_room = $("#form_old_room").val();
                    var form_new_room = $("#room").val();

                    $.ajaxQueue({
                        url: "<?php echo base_url('b_level/order_controller/change_form_room') ?>",
                        type: 'POST',
                        data: {
                            qty: qty,
                            item_id: form_item_id,
                            old_room: form_old_room,
                            new_room: form_new_room
                        },
                        async: false,
                        success: function(response) {
                            response = JSON.parse(response);
                            $("#form_old_room").val(form_new_room)
                            $("#form_room_arr").val(response.form_room_arr)
                            $("#form_missing_key").val(response.form_missing_key)
                        }
                    });
                }, 500)
            }
        })
    });

    function formRemoveQuantity(qty, remove_keys, item_id, room) {
        var is_drapery_cat = $("#is_drapery_cat").val();
        $.ajaxQueue({
            url: "<?php echo base_url('b_level/order_controller/form_get_all_cart_data') ?>",
            type: 'POST',
            data: {
                qty: qty,
                type: 'removequnatity',
                roomcounter: remove_keys,
                item_id: item_id,
                room: room
            },
            async: false,
            success: function(response) {
                response = JSON.parse(response);
                $("#form_room_arr").val(response.form_room_arr)
                $("#form_missing_key").val(response.form_missing_key)
                if (is_drapery_cat == 1) {
                    calculate_drapery_price();
                    manage_no_of_rings_val(1);
                }
                cal()
            }
        });
    }

    /* 
        Show Image Zoom preview :: START
    */

    function initAttrImagePopup(e) {

        var image = e; // get current clicked image
        // create new popup image with all attributes for clicked images and offsets of the clicked image
        var popupImage = document.createElement("img");
        popupImage.setAttribute('src', image.attr("data_src"));
        // popupImage.style.width = image.width+"px";
        popupImage.style.height = image.height + "px";
        // popupImage.style.left = image.offsetLeft+"px";
        // popupImage.style.top = image.offsetTop+"px";
        popupImage.classList.add('popImage');

        // creating popup image container
        var popupContainer = document.createElement("div");
        popupContainer.classList.add('popupContainer');

        // creating popup image background
        var popUpBackground = document.createElement("div");
        popUpBackground.classList.add('popUpBackground');

        // creating popup image Div
        var ImgDiv = document.createElement("div");
        ImgDiv.classList.add('ImgDiv');

        // creating popup image container close dic
        var popupCloseContainer = document.createElement("div");
        popupCloseContainer.classList.add('popupCloseContainer');
        popupCloseContainer.innerHTML = "<i class='fa fa-close remove-popup-img'></i>";


        // append all created elements to the popupContainer then on the document.body
        popupContainer.appendChild(popUpBackground);
        popupContainer.appendChild(ImgDiv);
        ImgDiv.appendChild(popupImage);
        ImgDiv.appendChild(popupCloseContainer);
        document.body.appendChild(popupContainer);

        // call function popup image to create new dimensions for popup image and make the effect
        popupImageFunction();


        // resize function, so that popup image have responsive ability
        var wait;
        window.onresize = function() {
            clearTimeout(wait);
            wait = setTimeout(popupImageFunction, 100);
        };

        // close popup image clicking on it
        popupCloseContainer.addEventListener('click', function(e) {
            closePopUpImage();
        });
        // // close popup image on clicking on the background
        // popUpBackground.addEventListener('click', function (e) {
        //     closePopUpImage();
        // });

        function popupImageFunction() {
            // wait few miliseconds (10) and change style of the popup image and make it popup
            // waiting is for animation to work, yulu can disable it and check what is happening when it's not there
            setTimeout(function() {
                // I created this part very simple, but you can do it much better by calculating height and width of the screen,
                // image dimensions.. so that popup image can be placed much better
                popUpBackground.classList.add('active');
                // popupImage.style.left = "15%";
                // popupImage.style.top = "50px";       
                // popupImage.style.width = window.innerWidth * 0.7+"px";
                popupImage.style.height = ((image.height / image.width) * (window.innerWidth * 0.7)) + "px";
            }, 10);
        }

        // function for closing popup image, first it will be return to the place where 
        // it started then it will be removed totaly (deleted) after animation is over, in our case 300ms
        function closePopUpImage() {
            // popupImage.style.width = image.width+"px";
            popupImage.style.height = image.height + "px";
            // popupImage.style.left = image.offsetLeft+"px";
            // popupImage.style.top = image.offsetTop+"px";
            popUpBackground.classList.remove('active');
            setTimeout(function() {
                popupContainer.remove();
            }, 300);
        }

    }                   

    /* 
        Show Image Zoom preview :: END
    */





    // (function($) {

    //     // jQuery on an empty object, we are going to use this as our Queue
    //     var ajaxQueue = $({});
    //     var totalRequests = 0; // Initialize the total number of requests
    //     var completedRequests = 0;
    //     activeRequ = 0;
    //     if ('<?php echo "$copying"; ?>' == 'yes') {
    //         $('#loader-container').fadeIn();
    //     }
    //     $.ajaxQueue = function(ajaxOpts) {
    //         if ('<?php echo "$copying"; ?>' == 'yes') {
    //             $('#loader-container').fadeIn();
    //         }
    //         var jqXHR,
    //             dfd = $.Deferred(),
    //             promise = dfd.promise();
    //         // Add your progress bar logic here
    //         // var $progressBar = $('#progress-bar');
    //         // var $progressText = $('#progress-text');
    //         var $loaderText = $('#loading-text');

    //         copying = '<?php echo "$copying"; ?>';
    //         activeRequ++;
    //         // queue our ajax request
    //         ajaxQueue.queue(doRequest);

    //         // add the abort method
    //         promise.abort = function(statusText) {

    //             // proxy abort to the jqXHR if it is active
    //             if (jqXHR) {
    //                 return jqXHR.abort(statusText);
    //             }

    //             // if there wasn't already a jqXHR we need to remove from queue
    //             var queue = ajaxQueue.queue(),
    //                 index = $.inArray(doRequest, queue);

    //             if (index > -1) {
    //                 queue.splice(index, 1);
    //             }

    //             // and then reject the deferred
    //             dfd.rejectWith(ajaxOpts.context || ajaxOpts,
    //                 [promise, statusText, ""]);
    //             if (copying == "yes") {
    //                 activeRequ--;
    //                 updateProgress(); // Update progress on success
    //             }
    //             return promise;
    //         };

    //         // run the actual query
    //         function doRequest(next) {
    //             jqXHR = $.ajax(ajaxOpts)
    //                 .done(function() {
    //                     dfd.resolve();
    //                     if (copying == "yes") {
    //                         completedRequests++;
    //                         activeRequ--;
    //                         var completedUrl = ajaxOpts.url;
    //                         updateProgress(completedUrl); // Update progress on success
    //                     }
    //                 })
    //                 .fail(function() {
    //                     dfd.reject();
    //                     if (copying == "yes") {
    //                         activeRequ--;
    //                         updateProgress(); // Update progress on failure
    //                     }
    //                 })
    //                 .then(next, next);

    //             // Increment the total number of requests
    //             totalRequests++;

    //             // Update the total number of requests in ajaxOpts for progress calculation
    //             // ajaxOpts.totalRequests = totalRequests;

    //         }


    //         function updateProgress(url) {
    //             // var completedRequests = ajaxQueue.queue().length; // Number of completed requests
    //             // completedRequests++;
    //             // Calculate progress percentage
    //             if (url.indexOf("get_product_attr_option_option") !== -1) {
    //                 // Text is present in the string
    //                 text = "Fectching Product Attribute Option!";
    //             } else if (url.indexOf("calculate_up_condition") !== -1) {
    //                 // Text is present in the string
    //                 text = "Fectching Upcharges!";
    //             } else if (url.indexOf("multioption_price_value") !== -1) {
    //                 // Text is present in the string
    //                 text = "Calculating Product Price Based on Attributes!";
    //             } else if (url.indexOf("get_product_row_col_price") !== -1) {
    //                 // Text is present in the string
    //                 text = "Calculating Product Price Based on Height and Width!";
    //             } else {
    //                 text = ""
    //             }
    //             completedRequests = Math.min(completedRequests, totalRequests);
    //             // var progress = (completedRequests / Math.max(totalRequests, 1)) * 100;

    //             // Update the progress bar and text
    //             // $progressBar.css('width', progress + '%');
    //             // $progressText.text('Processing ' + completedRequests + ' of ' + totalRequests + ' requests...');
    //             console.log('loader text:' + text);
    //             if (text != "") {

    //                 $loaderText.text(text);
    //             }
    //             if (activeRequ === 0) {
    //                 // All requests are completed, you can hide the loader here
    //                 $('#loader-container').fadeOut();
    //             }
    //         }

    //         return promise;
    //     };

    // })(jQuery);




    (function($) {

// jQuery on an empty object, we are going to use this as our Queue
var ajaxQueue = $({});

$.ajaxQueue = function(ajaxOpts) {
    var jqXHR,
        dfd = $.Deferred(),
        promise = dfd.promise();

    // queue our ajax request
    ajaxQueue.queue(doRequest);

    // add the abort method
    promise.abort = function(statusText) {

        // proxy abort to the jqXHR if it is active
        if (jqXHR) {
            return jqXHR.abort(statusText);
        }

        // if there wasn't already a jqXHR we need to remove from queue
        var queue = ajaxQueue.queue(),
            index = $.inArray(doRequest, queue);

        if (index > -1) {
            queue.splice(index, 1);
        }

        // and then reject the deferred
        dfd.rejectWith(ajaxOpts.context || ajaxOpts,
            [promise, statusText, ""]);

        return promise;
    };

    // run the actual query
    function doRequest(next) {
        jqXHR = $.ajax(ajaxOpts)
            .done(dfd.resolve)
            .fail(dfd.reject)
            .then(next, next);
    }

    return promise;
};

})(jQuery);



</script>