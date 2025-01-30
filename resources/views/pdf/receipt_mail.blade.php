<style>
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 8px;
    text-align: left;
}

.borderd td {
    border: 1px solid black;
}

.borderd th {
    border: 1px solid black;
}

.border-hide {
    border: none !important;
}

.top-table{
    
    width: 57%;
    float: right;
    font-size: 11px;
   
    margin-bottom: 24px;

}

.top-table table {
    /* Optional: Styles for the entire table */
    width: 100%;
    border-collapse: collapse;
    /* Add more table styles as needed */
}

.top-table th,
.top-table td {
    padding: 0 !important;
    /* Other styles for th and td */
}


.customer-details{
    font-size: 11px;
}

.dotted-line {
            border: none;
            border-top: 2px  dotted #999; /* Adjust color and size as needed */
            height: 0;
            margin:0;
        }

.tableResponsive > table{

    font-size:11px;
}

.order-detail-heading{

    margin : 0px;
}

.subtotal{
    width: 44%;
    float: right;
}

body{
    margin:0px;
}




        
</style>


<div>
    <div class="card">
        <div style="display: flex; justify-content: center; width:100%; text-align: center;">

            @if(isset($data['barcode']) && $data['barcode'] != '')
            <img alt="Barcode" loading="lazy" width="120" height="30" decoding="async" data-nimg="1"
                src="{{$data['barcode']}}" style="color: transparent;">
            @endif
        </div>


        <table style="width: 100%;" claas>
            <tbody>

                <tr>
                    <td>
                        <img alt="Logo" loading="lazy" width="150" height="120" decoding="async" data-nimg="1"
                            src="{{$data['logo']}}" style="color: transparent;">
                    </td>

                    <td>
                        <table class=" top-table">

                            <tr>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Order Id"><strong>{{@$data['order_stage']}} Date</strong></td>
                                <td data-label="Order Id Value">{{@$data['order_date']}}</td>
                            </tr>
                            <tr>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Order Id"><strong>{{@$data['order_stage']}} Id</strong></td>
                                <td data-label="Order Id Value">{{@$data['order_id']}}</td>
                            </tr>
                            <tr>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Sidemark"><strong>Sidemark</strong></td>
                                <td data-label="Sidemark Value">{{@$data['side_mark']}}</td>
                            </tr>
                            <tr>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Shipping Method"><strong>Shipping Method/Tracking #</strong></td>
                                <td data-label="Shipping Method Value">{{@$data['shipping_method']}}</td>
                            </tr>
                        </table>
                    </td>
                    <!-- <td data-label="Order Date">Quote Date</td> -->
                    <!-- <td data-label="Order Date Value">05/28/2024 05:14 AM</td> -->
                </tr>


            </tbody>
        </table>


        <table class="customer-details">
            <tr>
                <td>
                    <div class="inline-group description">
                        {{@$data['wholesaler_info']['company_name']}} <br>
                        {{@$data['wholesaler_info']['address']}} <br>
                        {{@$data['wholesaler_info']['city']}}, {{@$data['wholesaler_info']['zip_code']}},
                        {{@$data['wholesaler_info']['country_code']}} <br>
                        {{@$data['wholesaler_info']['phone']}} <br>
                        {{@$data['wholesaler_info']['email']}} <br>
                    </div>
                </td>
                <td>
                    <div class="inline-group description">
                        <strong>{{@$data['sold_to']['label']}}</strong> {{@$data['sold_to']['name']}} <br>
                        {{@$data['sold_to']['name']}} <br>
                        {{@$data['sold_to']['address']}} <br>
                        {{@$data['sold_to']['city']}}, {{@$data['sold_to']['state']}},
                        {{@$data['sold_to']['zip_code']}},
                        {{@$data['sold_to']['country_code']}} <br>
                        {{@$data['sold_to']['phone']}} <br>
                        {{@$data['sold_to']['email']}} <br>
                    </div>
                </td>
                <td>
                    <div class="inline-group description">
                        <strong>{{@$data['ship_to']['label']}}</strong> {{@$data['ship_to']['name']}} <br>
                        <!-- {{@$data['ship_to']['label']}} <br> -->
                        {{@$data['ship_to']['shipping_address']}} <br>
                        {{@$data['ship_to']['city']}}, {{@$data['ship_to']['state']}},
                        {{@$data['ship_to']['zip_code']}},
                        {{@$data['ship_to']['country_code']}} <br>
                        {{@$data['ship_to']['phone']}} <br>
                        {{@$data['ship_to']['email']}} <br>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="card">
        <h4 class="order-detail-heading">Order Details</h4>
        <hr>
        <div class="tableResponsive" style="margin-top: 20px;">
            <table class="table ">
                <thead style="background-color:#c9c8c8">
                    <tr>
                        <th>SL No.</th>
                        <th>Name of Product</th>
                        <th>Qty</th>
                        <th>Product Price</th>
                        <th>Discount %</th>
                        <th>List Price ($)</th>
                        <th>Upcharge</th>
                        <th>Price ($)</th>
                        <!-- <th>Comments</th> -->
                    </tr>
                </thead>
                @foreach($data['products'] as $key => $product)
                <tbody>
                    <tr>
                        <td data-label="SL No.">{{$key+1}}</td>
                        <td data-label="Name of Product">
                            <div class="productDetail">
                                <strong>{{$product['name_of_product']['category']}} ,
                                    {{$product['name_of_product']['product_name']}}
                                </strong>
                                <hr class="dotted-line">

                                <div class="attributesDetail">
                                    <span>{{$product['name_of_product']['pattern']}}</span><br>
                                    <span>W : {{$product['name_of_product']['width']}} </span>&nbsp;&nbsp;
                                    <span>H :{{$product['name_of_product']['height']}}</span><br>
                                    <span>{{$product['name_of_product']['color_number'] ?? ''}}</span><br>
                                    <span> 

                                    @foreach($product['name_of_product']['room'] as $room)
                                         {{$room}}
                                    @endforeach


                                    </span>
                                    <span></span>
                                </div>
                            </div>
                        </td>
                        <td data-label="Qty">
                            <div class="couterWrapper">
                                <span>{{$product['product_qty']}}</span>
                            </div>
                        </td>
                        <td data-label="Product Price">{{$product['product_price']}}</td>
                        <td data-label="Discount %">{{$product['discount']}}</td>
                        <td data-label="List Price ($)">{{$product['list_price']}}</td>
                        <td data-label="upcharge">{{$product['upcharge']['upcharge_price']}}</td>
                        <td data-label="Price ($)">{{$product['total_price']}}</td>
                        <!-- <td data-label="Comments" style="text-align: left; font-size: 90%;">
                            {{$product['comments']['notes']}}
                            <br>
                            {{$product['comments']['special_installer_notes']}}
                        </td> -->
                    </tr>
                </tbody>
                @endforeach

                <!-- controller data start -->
                @if(isset($data['controllers']) && count($data['controllers']) > 0)
                <tbody style="background-color:#c9c8c8">
                    <tr>
                        <th></th>
                        <th>Controllers</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <!-- <th></th> -->
                    </tr>
                </tbody>
                @foreach($data['controllers'] as $key => $controller)
                <tbody>
                    <tr>
                        <td data-label="SL No.">{{$key+1}}</td>
                        <td data-label="Name of Product">
                            <div class="productDetail">
                             {{$controller['name']}}
                            </div>
                        </td>
                        <td data-label="Qty">
                            <div class="couterWrapper">
                                <span>{{$controller['qty']}}</span>
                            </div>
                        </td>
                        <td data-label="Product Price">${{$controller['price']}}</td>
                        <td data-label="Discount %"></td>
                        <td data-label="List Price ($)"></td>
                        <td data-label="upcharge"></td>
                        <td data-label="Price ($)">${{$controller['item_total_price']}}</td>
                        <!-- <td data-label="Comments" style="text-align: left; font-size: 90%;"> -->
                           
                        </td>
                    </tr>
                </tbody>
                @endforeach
                @endif
                <!-- controller data end -->
                
                <!-- component data end -->
                @if(isset($data['components']) && count($data['components']) > 0)
                <thead style="background-color:#c9c8c8">
                    <tr>
                        <th></th>
                        <th>Components</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <!-- <th></th> -->
                    </tr>
                </thead>
                @foreach($data['components'] as $key => $component)
                <tbody>
                    <tr>
                        <td data-label="SL No.">{{$key+1}}</td>
                        <td data-label="Name of Product">
                            <div class="productDetail">
                             {{$component['name']}}
                            </div>
                        </td>
                        <td data-label="Qty">
                            <div class="couterWrapper">
                                <span>{{$component['qty']}}</span>
                            </div>
                        </td>
                        <td data-label="Product Price">${{$component['price']}}</td>
                        <td data-label="Discount %"></td>
                        <td data-label="List Price ($)"></td>
                        <td data-label="upcharge"></td>
                        <td data-label="Price ($)">${{$component['item_total_price']}}</td>
                        <!-- <td data-label="Comments" style="text-align: left; font-size: 90%;"> -->
                           
                        </td>
                    </tr>
                </tbody>
                @endforeach
                @endif
                <!-- component data end -->


                <!-- hardware data end -->
                @if(isset($data['hardware']) && count($data['hardware']) > 0)
                <thead style="background-color:#c9c8c8">
                    <tr>
                        <th></th>
                        <th>Hardware</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <!-- <th></th> -->
                    </tr>
                </thead>
                @foreach($data['hardware'] as $key => $hardware)
                <tbody>
                    <tr>
                        <td data-label="SL No.">{{$key+1}}</td>
                        <td data-label="Name of Product">
                            <div class="productDetail">
                             {{$hardware['name']['vendor_name']}} </br>
                             {{$hardware['name']['group_name']}} </br>
                             {{$hardware['name']['product_name']}} </br>
                             {{$hardware['name']['sub_group_name']}} </br>
                             {{$hardware['name']['item_name']}} </br>
                             {{$hardware['name']['finish_name']}} </br>
                            </div>
                        </td>
                        <td data-label="Qty">
                            <div class="couterWrapper">
                                <span>{{$hardware['qty']}}</span>
                            </div>
                        </td>
                        <td data-label="Product Price">${{$hardware['price']}}</td>
                        <td data-label="Discount %"></td>
                        <td data-label="List Price ($)"></td>
                        <td data-label="upcharge"></td>
                        <td data-label="Price ($)">${{$hardware['item_total_price']}}</td>
                        <!-- <td data-label="Comments" style="text-align: left; font-size: 90%;"> -->
                           
                        </td>
                    </tr>
                </tbody>
                @endforeach
                @endif
                <!-- hardware data end -->

            </table>
        </div>

        <div class="tableResponsive " style="margin-top: 20px;">
            <table class="table borderd ">
                <tbody>
                    <tr style="background-color:#c9c8c8">
                        <th rowspan="2">TOTAL</th>
                        <th>QTY</th>
                        @if(isset($data['total']['width']))
                        <th>WIDTH</th>
                        @endif
                        @if(isset($data['total']['height']))
                        <th>HEIGHT</th>
                        @endif
                        @if(isset($data['total']['sqft_or_sqm']))
                        <th>SQFT</th>
                        @endif
                        <th>PRICE</th>
                    </tr>
                    <tr>
                        <td  data-label="Qty">{{$data['total']['qty']}}</td>
                        @if(isset($data['total']['width']))
                        <td data-label="width" style="min-width: 100px;">{{$data['total']['width']}}</td>
                        @endif
                        @if(isset($data['total']['height']))
                        <td data-label="height">{{$data['total']['height']}}</td>
                        @endif
                        @if(isset($data['total']['sqft_or_sqm']))
                        <td data-label="sqft_or_sqm">{{$data['total']['sqft_or_sqm']}}</td>
                        @endif
                        <td data-label="price">{{$data['total']['price']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        @if(isset($data['misc']) && count($data['misc']) > 0)
        <h3>MISC Breakdown Details</h3>
        <hr>
        <div class="tableResponsive" style="margin-top: 20px;">
            <table class="table borderd">
                <thead>
                    <tr>
                        <th>SL No.</th>
                        <th>Description</th>
                        <th>Unit Cost </th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                @php $subtotal_misc = 0; @endphp
                @foreach($data['misc'] as $key => $misc)
                @php $subtotal_misc += $misc['misc_price'] @endphp
                <tbody>
                    <tr>
                        <td data-label="SL No.">{{$key+1}}</td>
                        <td data-label="misc_description">{{$misc['misc_description']}}</td>
                        <td data-label="misc_unite_cost">{{$misc['misc_unite_cost']}}</td>
                        <td data-label="misc_qty">{{$misc['misc_qty']}}</td>
                        <td data-label="misc_price">${{$misc['misc_price']}}</td>

                    </tr>
                </tbody>
                @endforeach

                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th>Subtotal</th>
                        <td>${{$subtotal_misc}}</td>

                    </tr>
                </tbody>

            </table>
        </div>
        @endif




        <div class="tableResponsive" style="margin-top: 20px;">
            <table class="table borderd  subtotal">
                <thead>
                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th> Sales Tax ({{$data['total']['tax_percentage']}}%)</th>
                        <td data-label="sales_tax">{{$data['total']['sales_tax']}}</td>
                    </tr>
                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th>Sub-Total</th>
                        <td data-label="sub_total" style="min-width: 100px;">{{$data['total']['sub_total']}}</td>
                    </tr>
                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th>Grand Total</th>
                        <td data-label="grand_total">{{$data['total']['grand_total']}}</td>
                    </tr>
                    @if(isset($data['misc']) && count($data['misc']) > 0)
                         @php $subtotal_misc = 0; @endphp
                        @foreach($data['misc'] as $key => $misc)
                        @php $subtotal_misc += $misc['misc_price'] @endphp

                        @endforeach
                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th>MISC.</th>
                        <td data-label="grand_total">{{$subtotal_misc}}</td>
                    </tr>
                    @endif
                    


                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th>Deposit</th>
                        <td data-label="deposit">{{$data['total']['deposit']}}</td>
                    </tr>
                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th>Due</th>
                        <td data-label="due">{{$data['total']['due']}}</td>
                    </tr>
                </thead>

            </table>
        </div>
</div>

    </div>
</div>