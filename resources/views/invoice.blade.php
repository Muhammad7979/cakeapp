<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>INVOICE</title>
    {{--<link href="{{asset('css/print.css')}}" rel="stylesheet" media="print" type="text/css">--}}
    <style type="text/css">
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        a {
            color: #fff;
            text-decoration: none;
        }

        table {
            font-size: large;

        }

        td.spaceRight {
            padding-right: 2em;
        }

        td.spaceTop {
            padding-bottom: 18em;
        }

        tfoot tr td {
            margin-top: 10px;
            font-weight: bold;
            font-size: large;
        }

        tr.spaceUnder>td {
            padding-bottom: 1em;
        }

        .invoice table {
            margin: 15px;
        }

        .invoice h3 {
            margin-left: 15px;
        }

        .information .logo {
            margin: 5px;
        }

        .information table {
            padding: 10px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>

    <div class="information" style="min-height: 250px; max-height: 400px; padding-top:200px;">

        <table width="100%">

            <tr>
                <td align="left" style="width: 40%; vertical-align: middle;">
                    <pre style="font-size: 20px;">
<b>Salesman</b> : {{$order->salesman}}
<b>Customer Name</b> : {{$order->customer_name}}
<b>Phone Number</b> : {{$order->customer_phone}}
<b>Order Number</b> : {{$order->order_number}}
<b>Payment Status</b> : {{$payment_status->label}}
</pre>


                </td>

                <td align="right" style="width: 40%; vertical-align: baseline;">

                    <pre style="font-size: 20px;">
                    <b>Order Branch</b> : {{$branchName->value}}
                    <b>Date</b> : {{ Carbon\Carbon::parse($order->order_date)->format('d-m-Y')}} {{ Carbon\Carbon::parse($order->created_at)->format('h:i a')}}
                    <b>Branch Phone</b>  : {{$branchNumber->value}}

                    <b>Delivery Branch</b> : {{$assignedBranch->name}}
                    <b>Branch Phone</b> :{{$assignedBranch->phone}}
                    <b>Delivery Date</b> : {{ Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y')}}
                    <b>Delivery Time</b>: {{$order->delivery_time}}
                </pre>
                </td>
            </tr>

        </table>
    </div>


    <br />

    <div class="invoice">
        @if($pay_again)
        <h3 style="text-align: center; margin-bottom: 20px;">Customer Copy <small>(pre)</small> </h3>
        @else
        <h3 style="text-align: center; margin-bottom: 20px;">Customer Copy</h3>
        @endif
        <h3 style="text-align: center;">Invoice Number # {{$order->order_number}}</h3>
        <div style="text-align: center;">{!!$barcodeImage!!}</div>
        @if($order->is_custom==1)
        <img style="margin-left: 20px" height="200"
            src="{{$order->photo_path ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/Placeholder.png' }}"
            alt="">
        @else
        <img style="margin-left: 20px" height="200"
            src="{{$order->photo_path ?  URL::asset('images/Product_Images/'.$order->photo_path ): '/images/Placeholder.png' }}"
            alt="">
        @endif
        <div style="margin: auto;">
            <table width="100%"
                style="{{-- margin-left: 5%; margin-top: 5% ; margin-right: 3%; table-layout: fixed --}}">
                <thead>
                    <tr>

                        <th style="width: 50%;" align="left">Description</th>
                        <th align="left">Weight</th>
                    @if(!empty($pos_sale_items))

                    <th align="left">Cost price</th>

                    @endif
                        <th align="left">Quantity</th>
                        <th align="left">Total</th>
                    </tr>
                </thead>
                <tbody>
                     @php
                       $items_total = 0; 
                     @endphp

                    @if(!empty($pos_sale_items))
                <tr>
                        <td>
                            <b>Other Items</b> 
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    

                    @foreach($pos_sale_items as $item)

                    @php
                       $items_total += $item->quantity_purchased*$item->item_unit_price; 
                       if($item->name == "MIX"){
                       continue;
                       }
                     @endphp

                    <tr>
                        <td>
                            {{ $item->name }}  
                        </td>
                        <td></td>
                        <td> {{ $item->item_cost_price }} </td>
                        <td>  {{ $item->quantity_purchased }} </td>
                        <td>{{ $item->quantity_purchased*$item->item_unit_price }}</td>
                    </tr>
                    @endforeach
                    <!-- <tr>
                    <td></td>
                        <td></td>
                        <td></td>
                        <td>Sub total</td>
                        <td>
                         <b>{{ $all_price_info['subtotal'] }}</b><small> .Rs</small>
                        </td>
                    </tr> -->
                    <!-- <tr>
                    <td></td>
                        <td></td>
                        <td></td>
                        <td>Total tax</td>
                        <td>
                         <b>{{ $all_price_info['totalTax']}}</b><small> .Rs</small>
                        </td>
                    </tr> -->
                   
                    <!-- <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Items total</td>
                        <td>
                         <b>{{ $items_total }}.00</b><small> .Rs</small>
                        </td>
                    </tr> -->
               
                    @endif
                    @if($order->is_cake == '1')
                         <tr>
                           <td>
                            <b>Product</b> : {{$order->product_name }}
                           </td>
                         </tr>
                            @else
                            <!-- @foreach($item_kits as $kit)
                         <tr>
                             <td>
                               <b>Item Kit {{$loop->iteration}}</b> :

                               {{$kit->product_name}}
                               </td>
                               <td></td>
                               <td></td>
                               <td></td>
                         </tr>
                            @endforeach -->
                        @endif
                        
                    <tr>
                        <td>
                            <b>Flavours</b> :
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            @foreach ($flavours as $index => $flavour)
                            {{"".$flavour->flavour_name." - (".$flavour_categories[$index].")"}}
                            @endforeach
                        </td>
                        <td>
                        @if($order->weight>0)
                        {{$order->weight}} - pounds
                         @endif
                         </td>
                         @if(!empty($pos_sale_items))
                        <td></td>
                        @endif
                        @if($order->is_cake == 1)
                        <td>{{$order->quantity}}</td>
                        <td>{{$order->total_price * $order->quantity}}(Rs)</td>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            <b>Material</b> :
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            @foreach ($materials as $material)
                            {{"".$material->material_name." "}}
                            @endforeach
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>Message</b> : {{$order->remarks}}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>
                  @if(!empty($pos_sale_items))
                     <tr>
                        <td style="width: 63%;"></td>
                        <td align="left">Sub Total</td>
                        <td align="left" class="gray">{{ $all_price_info['subtotal'] }} (Rs)</td>
                     </tr>
                    @endif
                    <tr>
                        <td style="width: 63%;"><b>Instructions</b> : {{$order->instructions}}</td>
                        <td align="left">Total</td>
                        @if($items_total > 0)
                        <td align="left" class="gray">{{$items_total}} (Rs)</td>
                       @else
                        <td align="left" class="gray">{{$order->total_price * $order->quantity}} (Rs)</td>
                       @endif
                    </tr>

                    @if($order->discount>0)
                    <tr>
                        <td colspan="1"></td>
                        <td align="left">Discount</td>
                        <td align="left" class="gray">{{$order->discount}}(Rs)</td>
                    </tr>
                    @endif
                    <tr>
                        <td align="left"></td>
                        <td align="left">{{ $pay_again ? 'Last paid amount' : 'Advance' }}</td>
                        <td align="left" class="gray">{{$order->advance_price}} (Rs)</td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td align="left">{{ $pay_again ? 'Amount to be paid' : 'Balance' }} </td>
                       @if($items_total > 0)
                       <td align="left" class="gray">{{($items_total - $order->advance_price-$order->discount) }} (Rs)</td>
                       @else
                       <td align="left" class="gray">{{($order->total_price * $order->quantity - $order->advance_price-$order->discount) }} (Rs)</td>
                       @endif
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    <tr class="spaceUnder">
                        <td colspan="1"><u>Salesman</u></td>
                        <td align="center"><u>Cashier Name </u> </td>
                        <td align="right" class="gray"><u>Cashier Signature</u></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>



    <div class="page-break">
    </div>

    <div class="information" style="min-height: 250px; max-height: 400px; padding-top:200px;">

            <table width="100%">

                <tr>
                    <td align="left" style="width: 40%; vertical-align: middle;">
                        <pre style="font-size: 20px;">
    <b>Salesman</b> : {{$order->salesman}}
    <b>Customer Name</b> : {{$order->customer_name}}
    <b>Phone Number</b> : {{$order->customer_phone}}
    <b>Order Number</b> : {{$order->order_number}}
    <b>Payment Status</b> : {{$payment_status->label}}
    </pre>


                    </td>

                    <td align="right" style="width: 40%; vertical-align: baseline;">

                        <pre style="font-size: 20px;">
                        <b>Order Branch</b> : {{$branchName->value}}
                        <b>Date</b> : {{ Carbon\Carbon::parse($order->order_date)->format('d-m-Y')}} {{ Carbon\Carbon::parse($order->created_at)->format('h:i a')}}
                        <b>Branch Phone</b>  : {{$branchNumber->value}}

                        <b>Delivery Branch</b> : {{$assignedBranch->name}}
                        <b>Branch Phone</b> :{{$assignedBranch->phone}}
                        <b>Delivery Date</b> : {{ Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y')}}
                        <b>Delivery Time</b>: {{$order->delivery_time}}
                    </pre>
                    </td>
                </tr>

            </table>
        </div>


        <br />

        <div class="invoice">
        @if($pay_again)
        <h3 style="text-align: center; margin-bottom: 20px;">Branch Copy <small>(pre)</small></h3>
        @else
        <h3 style="text-align: center; margin-bottom: 20px;">Branch Copy</h3>
        @endif
        <h3 style="text-align: center;">Invoice Number # {{$order->order_number}}</h3>
        <div style="text-align: center;">{!!$barcodeImage!!}</div>
        @if($order->is_custom==1)
        <img style="margin-left: 20px" height="200"
            src="{{$order->photo_path ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/Placeholder.png' }}"
            alt="">
        @else
        <img style="margin-left: 20px" height="200"
            src="{{$order->photo_path ?  URL::asset('images/Product_Images/'.$order->photo_path ): '/images/Placeholder.png' }}"
            alt="">
        @endif
        <div style="margin: auto;">
            <table width="100%"
                style="{{-- margin-left: 5%; margin-top: 5% ; margin-right: 3%; table-layout: fixed --}}">
                <thead>
                    <tr>

                        <th style="width: 50%;" align="left">Description</th>
                        <th align="left">Weight</th>
                    @if(!empty($pos_sale_items))

                    <th align="left">Cost price</th>

                    @endif
                        <th align="left">Quantity</th>
                        <th align="left">Total</th>
                    </tr>
                </thead>
                <tbody>
                     @php
                       $items_total = 0; 
                     @endphp

                    @if(!empty($pos_sale_items))
                <tr>
                        <td>
                            <b>Other Items</b> 
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    

                    @foreach($pos_sale_items as $item)

                    @php
                       $items_total += $item->quantity_purchased*$item->item_unit_price; 
                     @endphp

                    <tr>
                        <td>
                            {{ $item->name }}  
                        </td>
                        <td></td>
                        <td> {{ $item->item_cost_price }} </td>
                        <td>  {{ $item->quantity_purchased }} </td>
                        <td>{{ $item->quantity_purchased*$item->item_unit_price }}</td>
                    </tr>
                    @endforeach
                    <!-- <tr>
                    <td></td>
                        <td></td>
                        <td></td>
                        <td>Sub total</td>
                        <td>
                         <b>{{ $all_price_info['subtotal'] }}</b><small> .Rs</small>
                        </td>
                    </tr> -->
                    <!-- <tr>
                    <td></td>
                        <td></td>
                        <td></td>
                        <td>Total tax</td>
                        <td>
                         <b>{{ $all_price_info['totalTax']}}</b><small> .Rs</small>
                        </td>
                    </tr> -->
                   
                    <!-- <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Items total</td>
                        <td>
                         <b>{{ $items_total }}.00</b><small> .Rs</small>
                        </td>
                    </tr> -->
               
                    @endif
                    @if($order->is_cake == '1')
                         <tr>
                           <td>
                            <b>Product</b> : {{$order->product_name }}
                           </td>
                         </tr>
                            @else
                            <!-- @foreach($item_kits as $kit)
                         <tr>
                             <td>
                               <b>Item Kit {{$loop->iteration}}</b> :

                               {{$kit->product_name}}
                               </td>
                               <td></td>
                               <td></td>
                               <td></td>
                         </tr>
                            @endforeach -->
                        @endif
                        
                    <tr>
                        <td>
                            <b>Flavours</b> :
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            @foreach ($flavours as $index => $flavour)
                            {{"".$flavour->flavour_name." - (".$flavour_categories[$index].")"}}
                            @endforeach
                        </td>
                        <td>
                        @if($order->weight>0)
                        {{$order->weight}} - pounds
                         @endif
                         </td>
                         @if(!empty($pos_sale_items))
                        <td></td>
                        @endif
                        @if($order->is_cake == 1)
                        <td>{{$order->quantity}}</td>
                        <td>{{$order->total_price * $order->quantity}}(Rs)</td>
                        @endif
                    </tr>
                    <tr>
                        <td>
                            <b>Material</b> :
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            @foreach ($materials as $material)
                            {{"".$material->material_name." "}}
                            @endforeach
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            <b>Message</b> : {{$order->remarks}}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>
                @if(!empty($pos_sale_items))
                     <tr>
                        <td style="width: 63%;"></td>
                        <td align="left">Sub Total</td>
                        <td align="left" class="gray">{{ $all_price_info['subtotal'] }} (Rs)</td>
                     </tr>
                    @endif
                    <tr>
                        <td style="width: 63%;"><b>Instructions</b> : {{$order->instructions}}</td>
                        <td align="left">Total</td>
                        @if($items_total > 0)
                        <td align="left" class="gray">{{$items_total}} (Rs)</td>
                       @else
                        <td align="left" class="gray">{{$order->total_price * $order->quantity}} (Rs)</td>
                       @endif
                    </tr>
                    @if($order->discount>0)
                    <tr>
                        <td colspan="1"></td>
                        <td align="left">Discount</td>
                        <td align="left" class="gray">{{$order->discount}}(Rs)</td>
                    </tr>
                    @endif
                    <tr>
                        <td align="left"></td>
                        <td align="left">{{ $pay_again ? 'Last paid amount' : 'Advance' }}</td>
                        <td align="left" class="gray">{{$order->advance_price}} (Rs)</td>
                    </tr>
                    <tr>
                        <td colspan="1"></td>
                        <td align="left">{{ $pay_again ? 'Amount to be paid' : 'Balance' }}</td>
                       @if($items_total > 0)
                       <td align="left" class="gray">{{($items_total - $order->advance_price-$order->discount) }} (Rs)</td>
                       @else
                       <td align="left" class="gray">{{($order->total_price * $order->quantity - $order->advance_price-$order->discount) }} (Rs)</td>
                       @endif
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    </tr>
                    <tr class="spaceUnder">
                        <td colspan="1"></td>
                        <td align="center"></td>
                        <td align="right" class="gray"></td>
                    <tr class="spaceUnder">
                        <td colspan="1"><u>Salesman</u></td>
                        <td align="center"><u>Cashier Name </u> </td>
                        <td align="right" class="gray"><u>Cashier Signature</u></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>





    <script type="text/javascript">

    try {  window.onload = function() { window.print();
//         {{-- window.open('', '_self', ''); //bug fix
// window.close(); --}}
}   } catch (e) { window.onload = window.print();
    // {{-- window.open('', '_self', ''); //bug fix
    // window.close(); --}}
}

   </script>
</body>

</html>
