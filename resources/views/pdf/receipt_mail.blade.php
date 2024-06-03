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
</style>


<div>
    <div class="card">
        <div style="display: flex; justify-content: center; width:100%; text-align: center;">
       
            <img alt="Barcode" loading="lazy" width="120" height="30" decoding="async" data-nimg="1" src="{{$data['barcode']}}" style="color: transparent;">
        </div>
      

        <table style="width: 100%;" claas>
            <tbody>

                <tr>
                    <td>
                        <img alt="Logo" loading="lazy" width="150" height="120" decoding="async" data-nimg="1" src="{{$data['logo']}}" style="color: transparent;">
                    </td>

                    <td>
                        <table class="borderd ">
                            
                            <tr>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Order Id">{{$data['order_stage']}} Date</td>
                                <td data-label="Order Id Value">{{$data['order_date']}}</td>
                            </tr>
                            <tr>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Order Id">{{$data['order_stage']}} Id</td>
                                <td data-label="Order Id Value">{{$data['order_id']}}</td>
                            </tr>
                            <tr>
                                 <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Sidemark">Sidemark</td>
                                <td data-label="Sidemark Value">{{$data['side_mark']}}</td>
                            </tr>
                            <tr>
                                 <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <th class="border-hide"></th>
                                <td data-label="Shipping Method">Shipping Method/Tracking #</td>
                                <td data-label="Shipping Method Value">{{$data['shipping_method']}}</td>
                            </tr>
                        </table>
                    </td>
                    <!-- <td data-label="Order Date">Quote Date</td> -->
                    <!-- <td data-label="Order Date Value">05/28/2024 05:14 AM</td> -->
                </tr>


            </tbody>
        </table>


        <table>
            <tr>
                <td>
                    <div class="inline-group description">
                        {{$data['wholesaler_info']['company_name']}} <br>
                        {{$data['wholesaler_info']['address']}} <br>
                        {{$data['wholesaler_info']['city']}}, {{$data['wholesaler_info']['zip_code']}}, {{$data['wholesaler_info']['country_code']}} <br>
                        {{$data['wholesaler_info']['phone']}} <br>
                        {{$data['wholesaler_info']['email']}} <br>
                    </div>
                </td>
                <td>
                    <div class="inline-group description">
                        <strong>{{$data['sold_to']['label']}}</strong> {{$data['sold_to']['name']}} <br>
                        {{$data['sold_to']['name']}} <br>
                        {{$data['sold_to']['address']}} <br>
                        {{$data['sold_to']['city']}}, {{$data['sold_to']['state']}}, {{$data['sold_to']['zip_code']}}, {{$data['sold_to']['country_code']}} <br>
                        {{$data['sold_to']['phone']}} <br>
                        {{$data['sold_to']['email']}} <br>
                    </div>
                </td>
                <td>
                    <div class="inline-group description">
                        <strong>{{$data['ship_to']['label']}}</strong> {{$data['ship_to']['label']}} <br>
                        {{$data['ship_to']['label']}} <br>
                        {{$data['ship_to']['shipping_address']}} <br>
                        {{$data['ship_to']['city']}}, {{$data['ship_to']['state']}}, {{$data['ship_to']['zip_code']}}, {{$data['ship_to']['country_code']}} <br>
                        {{$data['ship_to']['phone']}} <br>
                        {{$data['ship_to']['email']}} <br>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="card">
        <h1>Order Details</h1>
        <div class="tableResponsive" style="margin-top: 20px;">
            <table class="table borderd">
                <thead>
                    <tr>
                        <th>SL No.</th>
                        <th>Name of Product</th>
                        <th>Qty</th>
                        <th>Product Price</th>
                        <th>Discount %</th>
                        <th>List Price ($)</th>
                        <th>Upcharge</th>
                        <th>Price ($)</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                @foreach($data['products'] as $key =>  $product)
                <tbody>
                    <tr>
                        <td data-label="SL No.">{{$key+1}}</td>
                        <td data-label="Name of Product">
                            <div class="productDetail">
                                <p>{{$product['name_of_product']['category']}} , {{$product['name_of_product']['product_name']}}</p>
                                <hr>
                                <div class="attributesDetail">
                                    <span>{{$product['name_of_product']['width']}} </span><br>
                                    <span>{{$product['name_of_product']['height']}}</span><br>
                                    <span>{{$product['name_of_product']['pattern']}}</span>
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
                        <td data-label="Comments" style="text-align: left; font-size: 90%;">
                            {{$product['comments']['notes']}}
                            <br>
                            {{$product['comments']['special_installer_notes']}}
                        </td>
                    </tr>
                </tbody>
                @endforeach
           
            </table>
        </div>
        <div class="tableResponsive " style="margin-top: 20px;">
            <table class="table borderd ">
                <tbody>
                    <tr>
                        <th rowspan="2">TOTAL</th>
                        <th>QTY</th>
                        <th>WIDTH</th>
                        <th>HEIGHT</th>
                        <th>SQFT</th>
                        <th>PRICE</th>
                    </tr>
                    <tr>
                        <td data-label="Qty">{{$data['total']['qty']}}</td>
                        <td data-label="width" style="min-width: 100px;">{{$data['total']['width']}}</td>
                        <td data-label="height">{{$data['total']['height']}}</td>
                        <td data-label="sqft_or_sqm">{{$data['total']['sqft_or_sqm']}}</td>
                        <td data-label="price">{{$data['total']['price']}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tableResponsive" style="margin-top: 20px;">
            <table class="table borderd ">
                <thead>
                    <tr>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th class="border-hide"></th>
                        <th> Sales Tax (%)</th>
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