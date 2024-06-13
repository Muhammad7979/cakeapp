@extends('layouts.admin')

@section('content')

    <div class="loader"></div>

    <div class="row">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        @if ($display_date_past == 1 || $display_date_future == 1)
            <div class="col-md-2">

                <div class="form-group d-inline">
                    <label class="d-inline">From date</label>
                    <div class='input-group date d-inline' id='datetimepicker6'>

                        <input type='text' class="form-control" placeholder="From Date" id="date_from" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>

                </div>
            </div>
            <div class="col-md-2">

                <div class="form-group">
                    <label class="d-inline">To date</label>
                    <div class='input-group date' id='datetimepicker7'>

                        <input type='text' class="form-control" placeholder="To Date" id="date_to" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>


                </div>
            </div>
        @endif

        <div class="col-md-4">
            <div class="form-group">
                <label class="d-inline">Search by Order Id</label>
                <div class='input-group '>

                    <input name="search_id" type='text' class="form-control search"
                        placeholder="Search by order id (Complete Order ID) " id="search_id" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-search search "></span>
                    </span>
                </div>
            </div>

        </div>
        <div class="col-md-4">
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
        </div>

        <div class="col-md-2">

        <input name="cake_item" type='hidden' id="search_type" value="{{$order_type}}" />

            <div class="form-group">

                <a class="btn btn-primary" id="filter_button" style="margin-top: 24px;width: 70%;margin-left: 55px;">
                    Filter</a>

            </div>
        </div>
        <div class="col-md-2">

            <div class="form-group ">

                <button id="search_reset" class=" btn btn-default" style="margin-top: 25px"> Reset</button>



            </div>
        </div>
        <div class="col-md-2">

            <div class="form-group">

                <button class="btn btn-default" id="sale_upload_button" style="margin-top: 25px"> <i
                        class="fa fa-cloud-download "></i> Sale
                    upload</button>

            </div>
        </div>




    </div>

    <hr>

    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-shopping-cart"></i>
                            Orders
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">

                                <div class="row">
                                    <div class="col-sm-6 col-sm-offset-5">
                                        {{ $orders->render() }}
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered inverse-table table-responsive">
                                        <thead class=".thead-dark">
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col">Order No</th>
                                                <th scope="col">Product Name</th>
                                                @if($order_type!=='pos')
                                                <th scope="col">Weight</th>
                                                @endif

                                                <th scope="col">Order Status</th>
                                                <th scope="col">Priority</th>
                                                <th scope="col">Phone no.</th>
                                                <th scope="col">Delivery Date</th>
                                                @if($order_type!=='pos')
                                                <th scope="col">Image</th>
                                                @endif
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">

                                            @if ($orders)

                                                @foreach ($orders as $order)
                                                    <tr>

                                                        @can('bakeman-view')
                                                            <th>


                                                                <a id="{{ $order->order_number }}" class="link table-row"> <i
                                                                        class="fa fa-eye fa-2x"></i> </a> <br>


                                                                @if ($order->withcake)
                                                                    <span class="btn btn-primary">{{ $order->is_cake == 0 ? 'Only POS items' : $order->withcake }}</span>
                                                                @endif

                                                            </th>
                                                        @elsecannot('bakeman-view')
                                                            <th scope="row"></th>
                                                        @endcan

                                                        <th scope="row"> {{ $order->order_number }}</th>
                                                        <td>{{ $order->product_name }}</td>
                                                        @if($order_type!=='pos')
                                                        <td>{{ $order->weight . '-pounds' }} </td>
                                                        @endif
                                                        <td>{{ $order->orderStatus->name }}</td>
                                                        <td>{{ \App\Configuration::where('key', '=', 'Priority_key')->where('value', '=', $order->priority)->first()->label }}
                                                        </td>
                                                        <td>{{ $order->customer_phone }}
                                                        </td>
                                                        <td>{{ Carbon\Carbon::parse($order->delivery_date)->format('d-m-Y') . ' ' . $order->delivery_time }}
                                                        </td>
                                                        @if($order->is_cake == 1)
                                                        @if ($order->is_custom == 1)
                                                            <td><img height="60"
                                                                    src="{{ $order->photo ? URL::asset('/images/Custom_Orders/' . $order->photo_path) : '/images/Placeholder.png' }}"
                                                                    alt=""> </td>
                                                        @else
                                                            <td><img height="60"
                                                                    src="{{ $order->photo ? URL::asset('images/Product_Images/' . $order->photo_path) : '/images/Placeholder.png' }}"
                                                                    alt=""> </td>
                                                        @endif
                                                        @endif
                                                        <td>
                                                        @if ($order->withcake)
                                                            <a class="btn btn-info" href="{{route('bakeman.reports',$order->order_number)}}">Details</a></td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>


                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>



    </div>


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        data-backdrop="static">

        <div class="modal-dialog" id="modal_dialog" style="position: fixed; width: fit-content">
            <div class="modal-dialog-inner">
                <div class="modal-content" id="modal_content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel" style="text-align: center">Order No</h4>
                    </div>
                    <div class="modal-body" style="">

                        <div class="row">
                            <div class="col-md-2 col-example"></div>
                            <div class="col-md-8 ml-auto col-example">
                                <div mag-thumb="inner">
                                    <a onclick="productImageZoomer($(this))"> <img height="200" src=""
                                            alt="" class="img-responsive zoom_image" id="modal_image"></a>
                                </div>
                            </div>
                            <div class="col-md-2 col-example"></div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 ml-auto col-example">
                                <h3 style="display: inline">Product Name : </h3>
                                <p id="modal_productName"
                                    style="  display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br>
                                <div id="table_data">
                                <table class="table" id='modal_table' >
                                <thead>
                                   <tr>
                                   <th colspan="5" style="text-align: center;"><h3><b>Items</b></h3></th>
                                    </tr>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <!-- Add more columns as needed -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Data 1</td>
                                        <td>Data 2</td>
                                        <td>Data 3</td>
                                        <td>Data 4</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                                <h3 id="modal_weight_heading" style="display: inline">Weight : </h3>
                                <p id="modal_weight"
                                    style="display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br>
                                <h3 style="display: inline">Quantity : </h3>
                                <p id="modal_quantity"
                                    style="display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br /><br />
                                <h3 style="display: inline">Message : </h3>
                                <p id="modal_cakeMessage"
                                    style="  display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br>
                                <br /><br />
                                <h3 style="display: inline">Order Instructions : </h3>
                                <p id="modal_orderInstructions"
                                    style="  display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br>
                                <br /><br />
                            </div>
                            <div class="col-md-2 ml-auto col-example"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ml-5 col-example">

                                <h3 id="modal_flavours_heading">Flavour</h3>
                                <div id="modal_flavours"></div>
                                <h3 id="modal_materials_heading">Materials </h3>
                                <div id="modal_materials"></div>
                                <br /><br />
                                <h3 id="order_status_title">Order Status</h3>
                                @can('bakeman-update')
                                    <select id="modal_orderStatus" data-branch="{{ env('BRANCH_CODE') }}"
                                        class="form-control">
                                        <option value=""></option>
                                    </select>
                                @endcan
                                <br /><br />
                            </div>
                            <div class="col-md-5  ml-5">
                                <h3>Delivery Date</h3>
                                <p id="modal_deliveryDate"
                                    style="display: inline;  font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br>
                                <h3>Delivery Time </h3>
                                <p id="modal_deliveryTime"
                                    style="display: inline  ;font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-9 col-example">
                                <input type="hidden" id="order_number" value="">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect waves-light"
                                data-dismiss="modal">Close</button>
                            <a id="save_button" class="  btn btn-primary waves-effect waves-light"
                                onclick="saveOrderStatus($(this))">Save changes</a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        style="top: 50%;left: 40%" data-backdrop="static">

        <div class="modal-dialog-image .ui-resizable" id="modal_dialog" style="position: fixed;">

            <div class="modal-content" id="modal_content" style="height:200px; width: 300px">
                <div class="modal-header">
                    <button class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <img height="250" src="" alt="" class="zoom" id="image_sep_modal"
                    style="    padding-left: 4%;
    min-width: 95%;
    min-height: 90%;
    max-width: 100%;
    max-height: 100%;">


            </div>


        </div>
    </div>







    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{ $orders->render() }}
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





        var currentImgModal;
        var newImgmodal;


        $(function() {
            var todayDate = new Date().getDate();

            var past_range = parseInt("{{ $date_range }}");
            var future_range = past_range;

            var can_view_past = parseInt("{{ $display_date_past }}");
            var can_view_future = parseInt("{{ $display_date_future }}");

            if (can_view_past != 1) {
                past_range = 0;
            }
            if (can_view_future != 1) {
                future_range = 0;
            }

            $('#datetimepicker6').datetimepicker({
                useCurrent: false,
                format: 'L',
                // minDate: new Date(new Date().setDate(todayDate - past_range)).setHours(0,0,0,0),
                minDate: moment().subtract(past_range, 'days'),
                maxDate: moment().add(future_range, 'days'),
                //maxDate: new Date(new Date().setDate(todayDate + future_range)).setHours(0,0,0,0),

            });
            $('#datetimepicker7').datetimepicker({
                useCurrent: false, //Important! See issue #1075
                format: 'L',
                minDate: moment().subtract(past_range, 'days'),
                maxDate: moment().add(future_range, 'days'),
            });
            $("#datetimepicker6").on("dp.change", function(e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker7").on("dp.change", function(e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });

        });

        function saveOrderStatus(element) {
            $(".loader").css("display", "block");

            var modal = element.closest('.modal');

            var orderid = element.closest('.modal').find("#order_number").val();
            var orderStatus = element.closest('.modal').find("#modal_orderStatus").val();


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "post",
                url: "/bakeman/orderStatus/set", // path to function
                cache: false,
                data: {
                    order_id: orderid,
                    order_status: orderStatus
                },
                success: function(val) {


                    try {

                        alert('Order Stauts Updated');

                        $('#search_reset').click(); //Trigger search button click event

                        $(".loader").fadeOut("slow");
                    } catch (e) {
                        alert('Exception while request..' + e);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });




        }



        /*
        FOR MANUAL UPLOAD
         */


        function productImageZoomer(element) {

            // var currentImgModal ;
            // var newImgmodal;


            var imageSrc = element.closest('.modal').find("#modal_image").attr('src');

            if ($("#imageModal").is(':visible')) {


                newImgmodal = $('#imageModal').clone();
                newImgmodal.addClass(['.modal-open', '.modal']);
                counter++;
                newImgmodal.attr('id', 'imageModal' + counter);


                newImgmodal.find("#image_sep_modal").attr("src", imageSrc);




                modal_content = newImgmodal.find('.modal-content');
                modal_dialog = newImgmodal.find('.modal-dialog-image');
                modal_content.resizable({
                    alsoResize: modal_dialog,

                    //minHeight: 150
                });




                newImgmodal.modal({
                    backdrop: false,
                    show: true
                });






                currentImgModal = newImgmodal;

            } else {

                currentImgModal = $('#imageModal').clone();
                currentImgModal.find("#image_sep_modal").attr("src", imageSrc);



                var modal_content = currentImgModal.find('.modal-content');

                var modal_dialog = currentImgModal.find('.modal-dialog-image');
                modal_content.resizable({
                    alsoResize: modal_dialog,

                    // height:50,
                    // width:50
                    // minHeight: 150
                });
                modal_dialog.draggable({});

                currentImgModal.on('show.bs.modal', function() {
                    $(this).find('.modal-body').css({
                        'height': '50%',
                        'width': '30%',
                        'max-height': '100%'
                    });
                });

                currentImgModal.modal({
                    backdrop: false,
                    show: true
                });




            }
        }

        function saleUpload() {
            $(".loader").css("display", "block");

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: "/positems/saleUpload", // path to function
                dataType: 'json',
                cache: false,

                success: function(val) {
                    alert('' + val['message']);
                    location.reload();
                    $(".loader").fadeOut("slow");
                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }


        $(document).ready(function() {
            $('#sale_upload_button').click(function() {

                saleUpload();

            });


            var currentModal;
            var newmodal;
            var counter = 0;

            $('#filter_button').click(function() {
                $(".loader").css("display", "block");

                var search_type = $('#search_type').val();
                var fromDate = $('#date_from').val();
                var toDate = $('#date_to').val();
                var searchId = $('#search_id').val();
                var phone = $('#phone_id').val();


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "post",
                    url: "/bakeman/search", // path to function
                    dataType: 'json',
                    cache: false,
                    data: {
                        search_type,
                        from_date: fromDate,
                        to_date: toDate,
                        phone: phone,
                        search_id: searchId,
                    },
                    success: function(val) {

                        if (val.data.length != 0) {
                            $('#table_body').empty();
                            var trHTML = '';
                            var priority = '';
                            var image = '';
                            var imageTd = '';
                            var tag = '';
                            
                            $.each(val.data, function(i, item) {
                           console.log(item.sale !== null);
                                let detailUrl = "";
                                    detailUrl = "{{ route('bakeman.reports', ':orderNumber') }}".replace(':orderNumber', item.order_number);
                                if (item.is_custom == 1) {
                                    image =
                                        '<img height="60" src= /images/Custom_Orders/' +
                                        item.photo_path + ' alt="" >';
                                } else if (item.is_custom == 0) {
                                    image =
                                        '<img height="60" src= /images/Product_Images/' +
                                        item.photo_path + ' alt="" >';
                                }
                                var final_image =
                                    '<img class="backup_image" height="60" src= /images/Created_Order_Images/' +
                                    item.final_image + ' >';

                                if (item.priority == 0) {
                                    priority = 'High';
                                } else if (item.priority == 1) {
                                    priority = ' Medium';
                                } else {
                                    priority = ' Low';
                                }
                                var imageRoute = '{{ asset('' . ':id') }}';
                                imageRoute = imageRoute.replace(':id', item.photo_path);
                                var editRoute = ' {{ route('orders.edit', ':id') }}';
                                editRoute = editRoute.replace(':id', item.order_number);
                                if (search_type !== 'pos') {
                                 imageTd = '<td>' + image + '</td>';
                                }
                                if(item.sale == null){
                                      tag="";
                                }else{
                                    tag = '<span class="btn btn-primary"> '+ val.withcake +'</span>';
                                }
                                
                                trHTML += '<tr>' +
                                    ' <th><a id=' + item.order_number +
                                    ' class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a><br>'+ tag +'</th>' +
                                    '<td>' + item.order_number +

                                    // ' ' + val.withcake +


                                    '</td><td>' + item.product_name +
                                    '</td><td>' + item.weight + '-pounds' +
                                    '</td><td>' + item.order_status.name +
                                    '</td><td>' + priority +
                                    '</td><td>' + item.customer_phone +
                                    '</td><td>' + moment(item.delivery_date).format(
                                        'DD-MM-YYYY') + ' ' + item.delivery_time +
                                    '</td>'+imageTd +'<td><a class="btn btn-info" href="'+detailUrl+'">Details</a></td>' + 
                                    '</tr>';

                            });

                            $('#table_body').append(trHTML);
                            $(".loader").fadeOut("slow");

                        } else {
                            $('#table_body').empty();
                            $(".loader").fadeOut("slow");
                        }

                        $(".backup_image").on("error", function() {
                            $(this).attr('src', '/images/Placeholder.png');
                        });

                    },
                    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }

                });


            });





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
                // reset modal if it isn't visible
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "post",
                    url: "/order/get", // path to function
                    dataType: 'json',
                    cache: false,
                    data: {
                        orderId: order_id
                    },
                    success: function(val) {

                        try {
                            //    var    response = val[name];

                            console.log('Returned', val[0]['product_name'][0]['name']);
                            console.log("Flavours", val[1]);
                            // $.each(val[1],function(i,itemData){
                            //     console.log('Flavours',itemData.name);
                            // });


                            if ($("#myModal").is(':visible')) {
                                if (val[0]['is_custom'] == 1) {
                                    var image = '/images/Custom_Orders/' + val[0]['image'];
                                } else {
                                    var image = '/images/Product_Categories/' + val[0]['image'];
                                }
                                var orderStatus = val[0]['order_status'];

                                newmodal = $('#myModal').clone();
                                newmodal.addClass(['.modal-open', '.modal']);
                                counter++;
                                newmodal.attr('id', 'myModal' + counter);


                                newmodal.find("#myModalLabel").text("Order Number " + val[0][
                                    'order_number'
                                ]);
                                newmodal.find("#order_number").attr('value', val[0]['number']);
                                newmodal.find("#modal_imageAnchor").attr("href", image);
                                newmodal.find("#modal_imageAnchor").attr("href", val[0][
                                    'image'
                                ]);
                                newmodal.find("#modal_image").attr("src", image);
                                newmodal.find("#modal_productName").text("" + val[0][
                                    'product_name'
                                ]);
                                newmodal.find("#modal_weight").text("" + val[0]['weight'] +
                                    '-pounds');
                                if (val[0]['remarks'] != null) {
                                    newmodal.find("#modal_cakeMessage").text("" + val[0][
                                        'remarks'
                                    ]);
                                }
                                if (val[0]['instructions'] != null) {
                                    currentModal.find("#modal_orderInstructions").text("" + val[
                                        0]['instructions']);

                                }
                                newmodal.find("#modal_quantity").text("" + val[0]['quantity']);

                                newmodal.find('#modal_flavours').empty();
                                newmodal.find('#modal_materials').empty();

                                $.each(val[1], function(i, itemData) {
                                    newmodal.find("#modal_flavours").append($(
                                        '<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px">' +
                                        itemData.flavour_name + '(' + itemData
                                        .category_name + ' )</P><br>'));
                                });

                                $.each(val[2], function(i, itemData) {
                                    newmodal.find("#modal_materials").append($(
                                        '<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px">' +
                                        itemData.material_name + '</P><br>'));
                                });



                                newmodal.find('#modal_orderStatus').empty();



                                if (val[0].assigned_to == $('#modal_orderStatus').data(
                                        'branch')) {
                                    // alert('my order');
                                    $.each(val[3], function(index, dat) {
                                        newmodal.find('#modal_orderStatus').append(
                                            '<option value="' + dat.id + '">' + dat
                                            .name + '</option>');
                                        //   console.log('Order status', dat.name)
                                    });
                                    newmodal.find("#modal_orderStatus option[value=" +
                                        orderStatus + "]").prop('selected', true);
                                } else {
                                    // alert('not my order');
                                    newmodal.find('#modal_orderStatus').hide();
                                    newmodal.find('#save_button').hide();
                                    newmodal.find('#order_status_title').hide();
                                }




                                newmodal.find("#modal_deliveryDate").text("" + val[0][
                                    'delivery_date'
                                ].toLocaleDateString());
                                newmodal.find("#modal_deliveryTime").text("" + val[0][
                                    'delivery_time'
                                ]);
      


                                modal_content = newmodal.find('.modal-content');
                                modal_dialog = newmodal.find('.modal-dialog');
                                modal_content.resizable({
                                    alsoResize: modal_dialog,

                                    //minHeight: 150
                                });




                                newmodal.modal({
                                    backdrop: false,
                                    show: true
                                });





                                currentModal = newmodal;

                            } else {

                                currentModal = $('#myModal').clone();
                                counter++;
                                currentModal.attr('id', 'myModal' + counter);
                                if (val[0]['is_custom'] == 1) {
                                    var image = '/images/Custom_Orders/' + val[0]['image'];
                                } else {
                                    var image = '/images/Product_Images/' + val[0]['image'];
                                }
                                var orderStatus = val[0]['order_status'];


                                currentModal.find("#myModalLabel").text("Order Number " + val[0]
                                    ['order_number']);
                                currentModal.find("#modal_image").attr("src", image);
                                currentModal.find("#order_number").attr('value', val[0][
                                    'order_number'
                                ]);
                                currentModal.find("#modal_imageAnchor").attr("href", val[0][
                                    'image'
                                ]);
                                currentModal.find("#modal_productName").text("" + val[0][
                                    'product_name'
                                ]);

                                 (val[0]['weight'])?currentModal.find("#modal_weight").text("" + val[0]['weight'] +'-pounds'):
                                    currentModal.find("#modal_weight_heading").empty();

                                if (val[0]['remarks'] != null) {
                                    currentModal.find("#modal_cakeMessage").text("" + val[0][
                                        'remarks'
                                    ]);

                                }
                                if (val[0]['instructions'] != null) {
                                    currentModal.find("#modal_orderInstructions").text("" + val[
                                        0]['instructions']);

                                }

                                currentModal.find("#modal_quantity").text("" + val[0][
                                    'quantity'
                                ]);

                                currentModal.find('#modal_flavours').empty();
                                currentModal.find('#modal_materials').empty();
                                (val[1].length > 0) ?
                                $.each(val[1], function(i, itemData) {
                                    currentModal.find("#modal_flavours").append($(
                                        '<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px"> > ' +
                                        itemData.flavour_name + '(' + itemData
                                        .category_name + ' )</P><br>'))
                                }):
                                        currentModal.find("#modal_flavours_heading").empty();
                                
                                (val[2].length > 0) ?        
                                $.each(val[2], function(i, itemData) {
                                    currentModal.find("#modal_materials").append($(
                                        '<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px"> > ' +
                                        itemData.material_name + '</P><br>'));
                                }):
                                        currentModal.find("#modal_materials_heading").empty();


                            
                                currentModal.find('#modal_orderStatus').empty();



                                if (val[0].assigned_to == $('#modal_orderStatus').data(
                                        'branch')) {
                                    // alert('my order '+currentModal.find('#modal_orderStatus').data('branchCode'));
                                    $.each(val[3], function(index, dat) {
                                        currentModal.find('#modal_orderStatus').append(
                                            '<option value="' + dat.id + '">' + dat
                                            .name + '</option>');
                                        //   console.log('Order status', dat.name)
                                    });
                                    currentModal.find("#modal_orderStatus option[value=" +
                                        orderStatus + "]").prop('selected', true);
                                } else {
                                    // alert(' not my order'+ $('#modal_orderStatus').data('branch'));
                                    currentModal.find('#modal_orderStatus').hide();
                                    currentModal.find('#save_button').hide();
                                    currentModal.find('#order_status_title').hide();
                                }
                                if(val[6]!= null){
                                  currentModal.find("#table_data").empty(); 

                                  Object.keys(val[6]).forEach(key => {
                                    var tableData = '';
                                     tableData = `
                                     <table class="table" >
                                <thead>
                                   <tr>
                                   <th colspan="3" style="text-align: center;"><h3><b>${key}</b></h3></th>
                                   <th colspan="3" style="text-align: center;"><h3><b>${val[6][key][0].kit_quantity}</b></h3></th>
                                    </tr>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                                      val[6][key].forEach((item,index) => {
                                    //    console.log('Item:', item);
                                     // Access item properties here
                                     tableData += `
                                    <tr>
                                        <td>${index+1}</td>
                                        <td>${item.name}</td>
                                        <td>${item.quantity}</td>
                                        <td>${item.price}</td>
                                        <td>${item.quantity*item.price}</td>
                                    </tr>
                            
                                     `;
                                        });

                                        tableData +=`</tbody> </table>`
                                     currentModal.find("#table_data").append(tableData);

                                 });

                                }else{

                                if(val[5]!==null){
                                currentModal.find("#modal_table tbody").empty(); 
                                val[5]['items'].forEach((item,index) => {                      // Create and append the tbody HTML to the modal body
                                 var rowHtml = `
                                 <tr>
                                 <td>${index+1}</td>
                                  <td>${item.name}</td>
                                  <td>${item.quantity_purchased/val[0]['quantity']}</td>
                                  <td>${item.item_unit_price}</td>
                                  <td>${item.quantity_purchased * item.item_unit_price}</td>
                                 </tr>
                                 `;
                                 currentModal.find("#modal_table tbody").append(rowHtml);
                                });
                               }else{
                                currentModal.find("#modal_table").empty();
                               }
                             }

                                currentModal.find("#modal_deliveryDate").text("" + new Date(val[
                                    0]['delivery_date']).toLocaleDateString());
                                currentModal.find("#modal_deliveryTime").text("" + val[0][
                                    'delivery_time'
                                ]);
                                //      currentModal.find("#save_button").attr('onclick','saveOrderStatus('+val[0]['id']+','+6+')');

                                if (!($('.modal.in').length)) {
                                    $('.modal-dialog').css({
                                        top: 0,
                                        left: 0
                                    });
                                }
                                var modal_content = currentModal.find('.modal-content');
                                var modal_dialog = currentModal.find('.modal-dialog');
                                modal_content.resizable({
                                    alsoResize: modal_dialog,

                                    //minHeight: 150
                                });
                                modal_dialog.draggable({});

                                currentModal.on('show.bs.modal', function() {
                                    $(this).find('.modal-body').css({
                                        'max-height': '60%'
                                    });
                                });

                                currentModal.modal({
                                    backdrop: false,
                                    show: true
                                });




                            }


                        } catch (e) {
                            alert('Exception while request..' + e);
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }

                });




                /*/
                ALL OF THIS GOES IN THE SUCCESS function
                 */

            });
            $('.search').keypress(function(e) {
                if (e.which == 13) { //Enter key pressed
                    $('#filter_button').click(); //Trigger search button click event
                }
            });



        });
    </script>
@stop
