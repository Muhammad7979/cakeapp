@extends('layouts.admin')

@section('content')

    <div class="loader"></div>

    <div class="row">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif



        <div class="col-md-4">
            <div class="form-group">
                <label class="d-inline">Search by Order Number</label>
                <div class='input-group '>

                    <input name="search_id" type='text' class="form-control search"
                        placeholder="Search by Complete Order Number " value="{{$cake_invoice}}" required id="search_id" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-search search "></span>
                    </span>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="form-group">
                <label class="d-inline">Search by Phone Number</label>
                <div class='input-group '>

                    <input name="phone_id" type='text' class="form-control search" placeholder="Search by Phone Number "
                        required id="phone_id" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-search search "></span>
                    </span>
                </div>
            </div>
        </div> --}}

        <div class="col-md-2">

            <div class="form-group">

                <a class="btn btn-primary" id="filter_button" style="margin-top: 24px;width: 70%;margin-left: 55px;">
                    Filter</a>

            </div>
        </div>

        <div class="col-md-1" style="    margin-left: 10px;">

            <div class="form-group">

                <div class='input-group '>
                    <button id="search_reset" class=" btn btn-default" style="margin-top: 25px"> Reset</button>
                </div>
            </div>
        </div>


    </div>
    <div>
        <h2 class="no_record">
            </h2`>
    </div>
    <hr>

    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-shopping-cart"></i>
                            Cake Order
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">



                                <div class="table-responsive">
                                    <table id="table_no_1" class="table table-bordered inverse-table table-responsive">
                                        <thead class=".thead-dark">
                                            <tr>

                                                <th scope="col">Order No</th>
                                                <th scope="col">Product Name</th>
                                                <th scope="col">Weight</th>
                                                <th scope="col">Order Status</th>
                                                <th scope="col">Priority</th>
                                                <th scope="col">Total Price</th>
                                                <th scope="col">Advance Paid</th>
                                                <th scope="col">Pending Amount</th>
                                                <th scope="col">Created Date</th>
                                                <th scope="col">Delivery Date</th>
                                                <th scope="col">Image</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">

                                            {{-- @if ($orders)

                                            @foreach ($orders as $order)
                                                <tr>

                                                    @can('bakeman-view')
                                                        <th><a id="{{$order->order_number}}" class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a></th>

                                                    @elsecannot('bakeman-view')
                                                        <th  scope="row"></th>

                                                    @endcan

                                                    <th  scope="row"> {{$order->order_number}}</th>
                                                    <td>{{$order->product_name}}</td>
                                                    <td>{{$order->weight.'-pounds'}} </td>
                                                    <td>{{$order->orderStatus->name}}</td>
                                                    <td>{{\App\Configuration:: where('key', '=', 'Priority_key')->
                                                                    where('value', '=', $order->priority)->first()->label
                                                                    }}</td>
                                                    <td>{{Carbon\Carbon::parse($order->order_date)->format('d-m-Y').' '.Carbon\Carbon::parse($order->created_at)->format('h:i a') }}</td>
                                                    <td>{{ Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y') . ' '.$order->delivery_time}}</td>

                                                    @if ($order->is_custom == 1)
                                                        <td><img height="60"  src="{{$order->photo ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/Placeholder.png' }}" alt="" > </td>
                                                    @else
                                                        <td><img height="60" src="{{$order->photo ?  URL::asset('images/Product_Images/'.$order->photo_path ): '/images/Placeholder.png' }}" alt="" > </td>
                                                    @endif

                                                </tr>
                                            @endforeach
                                        @endif --}}
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-shopping-cart"></i>
                            POS Items
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">



                                <div class="table-responsive">
                                    <table id="table_no_3" class="table table-bordered inverse-table table-responsive">
                                        <thead class=".thead-dark">
                                            <tr>

                                                <th scope="col">#</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Cost Price</th>
                                                <th scope="col">Unit Price</th>
                                                {{-- <th scope="col">Payment Amount</th> --}}

                                            </tr>
                                        </thead>
                                        <tbody id="table_body">

                                            {{-- @if ($orders)

                                            @foreach ($orders as $order)
                                                <tr>

                                                    @can('bakeman-view')
                                                        <th><a id="{{$order->order_number}}" class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a></th>

                                                    @elsecannot('bakeman-view')
                                                        <th  scope="row"></th>

                                                    @endcan

                                                    <th  scope="row"> {{$order->order_number}}</th>
                                                    <td>{{$order->product_name}}</td>
                                                    <td>{{$order->weight.'-pounds'}} </td>
                                                    <td>{{$order->orderStatus->name}}</td>
                                                    <td>{{\App\Configuration:: where('key', '=', 'Priority_key')->
                                                                    where('value', '=', $order->priority)->first()->label
                                                                    }}</td>
                                                    <td>{{Carbon\Carbon::parse($order->order_date)->format('d-m-Y').' '.Carbon\Carbon::parse($order->created_at)->format('h:i a') }}</td>
                                                    <td>{{ Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y') . ' '.$order->delivery_time}}</td>

                                                    @if ($order->is_custom == 1)
                                                        <td><img height="60"  src="{{$order->photo ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/Placeholder.png' }}" alt="" > </td>
                                                    @else
                                                        <td><img height="60" src="{{$order->photo ?  URL::asset('images/Product_Images/'.$order->photo_path ): '/images/Placeholder.png' }}" alt="" > </td>
                                                    @endif

                                                </tr>
                                            @endforeach
                                        @endif --}}
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>


        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-shopping-cart"></i>
                            Sales
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">



                                <div class="table-responsive">
                                    <table id="table_no_2" class="table table-bordered inverse-table table-responsive">
                                        <thead class=".thead-dark">
                                            <tr>

                                                <th scope="col">#</th>
                                                <th scope="col">BranchCode</th>
                                                <th scope="col">InvoiceNumber</th>
                                                <th scope="col">Sale Type</th>
                                                <th scope="col">Sale Payment</th>
                                                <th scope="col">Exact Time</th>

                                            </tr>
                                        </thead>
                                        <tbody id="table_body">


                                        </tbody>
                                    </table>
                                    <div style="display: flex;justify-content:center;align-items:center;">

                                        <h3 class="total_price "></h3>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-shopping-cart"></i>
                            Payments
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">



                                <div class="table-responsive">
                                    <table id="table_no_4" class="table table-bordered inverse-table table-responsive">
                                        <thead class=".thead-dark">
                                            <tr>


                                                <th scope="col">Total </th>
                                                <th scope="col">Advance Paid </th>
                                                <th scope="col">Pending Amount to Pay</th>

                                            </tr>
                                        </thead>
                                        <tbody id="table_body">


                                        </tbody>
                                    </table>
                                    <div style="display: flex;justify-content:right;align-items:center;">

                                        <button id="reorder" class="btn btn-primary"
                                            style="padding: 10px 30px;">Pay remaining amount</button>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>




    </div>










@stop
<div class="modal-overlay"></div>

@section('scripts')

    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>
    {{-- <script src="{{asset('js/lightbox.js')}}"></script> --}}



    <script>
        $(window).load(function() {
            // PAGE IS FULLY LOADED
            // FADE OUT YOUR OVERLAYING DIV
            $(".loader").css("display", "block");
            $('.loader').fadeOut('slow');
        });








        // $(document).ready(function(){

        //     var currentModal ;
        //     var newmodal;
        //     var counter =0;

        //     $('#filter_button').click(function() {
        //         $(".loader").css("display", "block");



        //         var searchId = $('#search_id').val();

        //         $.ajaxSetup({
        //             headers: {
        //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //             }
        //         });
        //         $.ajax({
        //             type: "post",
        //             url: "/bakeman/searchbynumber", // path to function
        //             dataType: 'json',
        //             cache: false,
        //             data: {
        //                 // from_date: fromDate,
        //                 // to_date: toDate,
        //                 search_id: searchId,
        //             },
        //             success: function (val) {

        //                     if(val.orders)
        //                     {
        //                         console.log(val.orders);
        //                     }

        //                 if (val.length != 0) {
        //                     $('#table_body').empty();
        //                     var trHTML = '';
        //                     var priority = '';
        //                     var image='';
        //                     $.each(val, function (i, item) {
        //                         if(item.is_custom==1)
        //                         {
        //                             image='<img height="60" src= /images/Custom_Orders/' + item.photo_path + ' alt="" >' ;
        //                         }
        //                         else if(item.is_custom==0)
        //                         {
        //                             image='<img height="60" src= /images/Product_Images/' + item.photo_path + ' alt="" >' ;
        //                         }
        //                         var final_image='<img class="backup_image" height="60" src= /images/Created_Order_Images/' + item.final_image+ ' >';

        //                         if (item.priority == 0) {
        //                             priority = 'High';
        //                         } else if (item.priority == 1) {
        //                             priority = ' Medium';
        //                         } else {
        //                             priority = ' Low';
        //                         }
        //                         var imageRoute= '{{ asset('' . ':id') }}';
        //                         imageRoute=imagstatusMessaged=' + item.order_number + ' class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a></th>' +
        //                             '<td>' + item.order_number +


        //                             '</td><td>' + item.product_name +
        //                             '</td><td>' + item.weight + '-pounds' +
        //                             '</td><td>' + item.order_status.name +
        //                             '</td><td>' + priority +
        //                             '</td><td>' + moment(item.order_date).format('DD-MM-YYYY ')+' '+moment(item.created_at).format('hh:mm A') +
        //                             '</td><td>' +moment(item.delivery_date).format('DD-MM-YYYY') + ' '+item.delivery_time +
        //                             '</td><td>'+image +

        //                             '</td> ' +
        //                                     '</tr>';

        //                     });

        //                     $('#table_body').append(trHTML);
        //                     $(".loader").fadeOut("slow");

        //                 }
        //                 else {
        //                     $('#table_body').empty();
        //                     $(".loader").fadeOut("slow");
        //                 }

        //                 $(".backup_image").on("error", function(){
        //                     $(this).attr('src', '/images/Placeholder.png');
        //                 });

        //             },
        //             error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
        //                 console.log(JSON.stringify(jqXHR));
        //                 console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
        //             }

        //         });


        //     });

        $(document).ready(function() {
            $('#reorder').attr('readonly', true);
            var currentModal;
            var newmodal;
            var counter = 0;
            $('.col-md-12').hide();
            $('.no_record').hide();

            var searchId = $('#search_id').val();
            var phoneId = $('#phone_id').val();
            if(searchId!== ''){

                $(".loader").css("display", "block");
                 loadData(searchId);

            }

            $('#filter_button').click(function() {

                $(".loader").css("display", "block");
                loadData(searchId,phoneId);

            });

            function loadData(searchId,phoneId = ""){
                if (searchId != "" || phoneId != "") {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "post",
                        url: "/bakeman/searchbynumber", // path to function
                        dataType: 'json',
                        cache: false,
                        data: {
                            // from_date: fromDate,
                            // to_date: toDate,
                            phone_number: phoneId,
                            search_id: searchId,
                        },
                        success: function(val) {
                            var remaining_amount = 0;
                            var order_status = '';
                            $('#search_id').val('');
                            $('#phone_id').val('');
                            $('.col-md-12').hide();
                            if (val.status == "Error") {
                                // alert(val.statusMessage);
                                $(".loader").fadeOut("slow");
                                $('.col-md-12').hide();
                                $('.no_record').html(val.statusMessage).show();

                            } else {
                                $('.no_record').html('').hide();
                                $('.col-md-12').show();
                                if (val.orders && !$('#table_no_1 tbody').children().length) {


                                    var ordersHTML =
                                        ''; // Variable to store HTML for orders table rows
                                    $.each(val.orders, function(index, order) {
                                        // $('.total_price').html('Total: ' + order
                                        //     .with_positems_price + ' Rs')
                                        order_status = order.order_status;
                                        if (order.is_custom == 1) {
                                            image =
                                                '<img height="60" src= /images/Custom_Orders/' +
                                                order.photo_path + ' alt="" >';
                                        } else if (order.is_custom == 0) {
                                            image =
                                                '<img height="60" src= /images/Product_Images/' +
                                                order.photo_path + ' alt="" >';
                                        }
                                        var final_image =
                                            '<img class="backup_image" height="60" src= /images/Created_Order_Images/' +
                                            order.final_image + ' >';

                                        if (order.priority == 0) {
                                            priority = 'High';
                                        } else if (order.priority == 1) {
                                            priority = ' Medium';
                                        } else {
                                            priority = ' Low';
                                        }
                                        ordersHTML += '<tr>' +
                                            '<td>' + order.order_number + '</td>' +
                                            '<td>' + order.product_name + '</td>' +
                                            '<td>' + order.weight + '-pounds</td>' +
                                            '<td>' + order.order_status.name + '</td>' +
                                            '<td>' + priority + '</td>' +
                                            '<td>' + order.total_price + '</td>' +
                                            '<td>' + order.advance_price + '</td>' +
                                            '<td>' + order.pending_amount + '</td>' +
                                            '<td>' + moment(order.order_date).format(
                                                'DD-MM-YYYY ') + ' ' + moment(order
                                                .created_at)
                                            .format('hh:mm A') + '</td>' +
                                            '<td>' + moment(order.delivery_date).format(
                                                'DD-MM-YYYY') + ' ' + order
                                            .delivery_time +
                                            '</td>' +
                                            '<td>' + image + '</td>' +
                                            '</tr>';
                                    });
                                    // Append all orders HTML to table_no_1
                                    $('#table_no_1').append(ordersHTML);
                                    $(".loader").fadeOut("slow");
                                    if (val.sales && !$('#table_no_2 tbody').children()
                                        .length) {
                                        var salesHTML =
                                            ''; // Variable to store HTML for sales table rows
                                        $.each(val.sales, function(index, sale) {
                                            salesHTML += '<tr>' +
                                                '<td>' + sale.sale_id + '</td>' +
                                                '<td>' + sale.branch_code + '</td>' +
                                                '<td>' + sale.invoice_number + '</td>' +

                                                '<td>' + sale.sale_type + '</td>' +
                                                '<td>' + sale.sale_payment + '</td>' +
                                                '<td>' + sale.exact_time + '</td>' +

                                                '</tr>';
                                        });
                                        // Append all sales HTML to table_no_2
                                        $('#table_no_2').append(salesHTML);
                                        $(".loader").fadeOut("slow");
                                    }
                                    if (val.items && !$('#table_no_3 tbody').children()
                                        .length) {
                                        var total = 0 ;
                                        var salesHTML =
                                            ''; // Variable to store HTML for sales table rows
                                        $.each(val.items, function(index, item) {
                                            let unitPrice = 0;
                                            unitPrice = parseFloat(item.item_unit_price)*parseFloat(item.quantity_purchased);
                                            total += unitPrice;
                                            salesHTML += '<tr>' +
                                                '<td>' + item.item_id + '</td>' +
                                                '<td>' + item.name + '</td>' +
                                                '<td>' + item.quantity_purchased +
                                                '</td>' +
                                                '<td>' + item.item_cost_price +
                                                '</td>' +
                                                '<td>' + item.item_unit_price +
                                                '</td>' +
                                                '</tr>';
                                        });

                                        // Append all sales HTML to table_no_3
                                        $('#table_no_3').append(salesHTML);

                                        // Create the row for total payment
                                        // var totalPaymentRow = '<tr><td colspan="5"></td>';
                                        // $.each(val.payments, function(index, payment) {
                                        //     totalPaymentRow += '<td>Total: ' +
                                        //         payment
                                        //         .payment_amount + '</td>';
                                        // });
                                        // totalPaymentRow += '</tr>';

                                        // Append the total payment row to the table
                                        // $('#table_no_3').append(totalPaymentRow);

                                        $(".loader").fadeOut("slow");
                                    }


                                    if (val.payment_data && !$('#table_no_4 tbody').children()
                                        .length) {
                                        const pdata = val.payment_data;
                                        var payHTML =
                                            ''; // Variable to store HTML for sales table rows
                                        remaining_amount = total - parseFloat(pdata.advance_price); 
                                        payHTML += '<tr>' +
                                            '<td>' + total + '</td>' +
                                            // '<td>' + pdata.total_price + '</td>' +
                                            '<td>' + pdata.advance_price + '</td>' +
                                            // '<td>' + pdata.pending_amount + '</td>' +
                                            '<td>' + `${total - parseFloat(pdata.advance_price)}` + '</td>' +

                                            '</tr>';

                                        // Append all sales HTML to table_no_2
                                        $('#table_no_4').append(payHTML);
                                        $(".loader").fadeOut("slow");
                                    }

                                }

                            }

                            $('#reorder').on('click', function() {
                                $('#reorder').prop('disabled', true);
                                 var cake_invoice = $('#search_id').val();
                                if(order_status.name == 'Delivered'){
                                    alert("Order already delieverd can't be reorder!")
                                }else{

                                if (val.order_number) {
                                    let cake_invoice = val.order_number;
                                    let permition =  confirm("Are you sure to continue this?")
                                    if(permition){
                                    $(".loader").css("display", "block");
                                    $.ajax({
                                        type: "post",
                                        url: "/bakeman/reorder", // path to function
                                        dataType: 'json',
                                        cache: false,
                                        data: {
                                            // from_date: fromDate,
                                            // to_date: toDate,
                                            total_price : remaining_amount,
                                            order_number: val.order_number,
                                            total_tax:val.payment_data.total_tax

                                        },
                                        success: function(val) {
                                            $('#reorder').prop('disabled', false);

                                            if (val.status == "Success") {
                                                alert(val.message)
                                            } else {
                                                alert(val.message)
                                            }
                                         $(".loader").fadeOut("slow");

                                         window.open(`/reGenerateInvoice/${cake_invoice}`, "_blank");
                                        },
                                        error: function(jqXHR, textStatus,
                                            errorThrown
                                        ) { // What to do if we fail
                                            console.log(JSON.stringify(
                                                jqXHR));
                                            console.log("AJAX error: " +
                                                textStatus + ' : ' +
                                                errorThrown);
                                        }

                                    })
                                 }
                                } else {
                                    alert("Order Number required")
                                }
                        }
                            })

                            // Hide loader after inserting data
                            $(".loader").fadeOut("slow");
                        },

                        error: function(jqXHR, textStatus,
                            errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' +
                                errorThrown);
                        }

                    });

                } else {
                    alert("Please Enter Order Number or Phone Number");
                    location.reload();
                    $(".loader").fadeOut("slow");
                }
            }




            $('#search_id').keypress(function(e) {
                // $(".loader").css("display", "none");
                if (e.which == 13) { //Enter key pressed
                    $('#filter_button').click(); //Trigger search button click event
                }
            });
            $('#search_productName').keypress(function(e) {
                // $(".loader").css("display", "none");
                if (e.which == 13) { //Enter key pressed
                    $('#filter_button').click(); //Trigger search button click event
                }
            });
            $('#search_branchCode').keypress(function(e) {
                // $(".loader").css("display", "none");
                if (e.which == 13) { //Enter key pressed
                    $('#filter_button').click(); //Trigger search button click event
                }
            });



            $('#search_reset').click(function() {

                window.location.reload(true);

            });



            $(document).on('click', '.link', function() {
                var order_id = $(this).attr('id');

                // Check if the link has already been clicked
                if (!$(this).hasClass('clicked')) {
                    // Set the flag to indicate that the link has been clicked
                    $(this).addClass('clicked');

                    // reset modal if it isn't visible
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content')
                        }
                    });

                    $.ajax({
                        type: "post",
                        url: "/bakeman/order/get", // path to function
                        dataType: 'json',
                        cache: false,
                        data: {
                            orderId: order_id
                        },
                        success: function(val) {
                            var trHTML = '<tr>' +
                                '<th></th>' +
                                '<th>Sale ID</th>' +
                                // Add more headings if needed
                                '</tr>';
                            if (val.length != 0) {
                                $.each(val, function(i, item) {
                                    trHTML += '<tr>' +
                                        '<td></td>' +
                                        '<td>' + item.sale_id +
                                        '</td>' +
                                        '</tr>';
                                });
                                $('#table_body').append(trHTML);
                                $(".loader").fadeOut("slow");
                            } else {
                                $('#table_body').empty();
                                $(".loader").fadeOut("slow");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        },
                        complete: function() {
                            // Enable the link after the AJAX call is complete
                            $('.link').removeClass('clicked');
                        }
                    });
                }
            });




            /*/
            ALL OF THIS GOES IN THE SUCCESS function
             */

            // });
            $('.search').keypress(function(e) {
                if (e.which == 13) { //Enter key pressed
                    $('#filter_button').click(); //Trigger search button click event
                }
            });



        });
    </script>
@stop
