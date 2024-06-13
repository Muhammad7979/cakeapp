@extends('layouts.admin')

@section('content')
    @if(Session::has('error'))
        <p class="bg-danger">{{session('error')}}</p>

    @endif
    <div class="row">
        {!! Form::open (['method'=>'POST','action'=>'AdminSalesController@generateCsv','files'=>true,'id'=>'form_csv']) !!}
        <div class="col-md-2">

            <div class="form-group d-inline">
                <label class="d-inline">From date</label>
                <div class='input-group date d-inline' id='datetimepicker6'>

                    <input name="from_date" type='text' class="form-control" placeholder="From Date" id="date_from" />
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

                    <input name="to_date" type='text' class="form-control" placeholder="To Date" id="date_to" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>


            </div>
        </div>


        <div class="col-md-2">
            <div class="form-group">
                <label class="d-inline">Search by Order Id</label>
                <div class='input-group ' >

                    <input name="search_id" type='text' class="form-control"  placeholder="Search by order id " id="search_id"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="d-inline">Search by Product name</label>
                <div class='input-group ' >

                    <input name="search_productName" type='text' class="form-control"  placeholder="Search by product name" id="search_productName"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="d-inline">Search by Branch Code</label>
                <div class='input-group ' >

                    {{--<input name="search_branchCode" type='text' class="form-control search"  placeholder="Search by branch code " id="search_branchCode"/>--}}
                    {!! Form::select('branch_code',[' '=> 'Select Branch']+$branches,null,['class'=>'form-control','id'=>'search_branchCode'])!!}
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search search  "></span>
                </span>
                </div>
            </div>
        </div>
    </div>
        <div class="row" style="margin-top: 15px">

        <div class="col-md-2">

            <div class="form-group">
                <label class="d-inline">Payment Type</label>

                    {!! Form::select('payment_type',['0'=> 'Select Payment Type']+$paymentTypes,null,['class'=>'form-control','id'=>'paymentType'])!!}


            </div>
        </div>
        <div class="col-md-2">

            <div class="form-group">
                <label class="d-inline">Order Status</label>

                    {!! Form::select('order_status',['0'=> 'Select Order Status']+$orderStatus,null,['class'=>'form-control','id'=>'orderStatus'])!!}

            </div>
        </div>

            <div class="col-md-2">

                <div class="form-group">
                    <label class="d-inline">Payment Status</label>

                    {!! Form::select('payment_status',['-1'=> 'Select Payment Status']+$paymentStatus,null,['class'=>'form-control','id'=>'paymentStatus'])!!}

                </div>
            </div>

        <div class="col-md-2">

            <div class="form-group">

                   <a class="btn btn-info" id="filter_button" style="margin-top: 24px;width: 70%;margin-left: 55px;"> Filter</a>

            </div>
        </div>

        <div class="col-md-2">

            <div class="form-group">

                <div class='input-group ' >
                    {!! Form:: button('Generate CSV',['class'=>'btn btn-primary','style'=>'margin-top:23px','id'=>'generate_csv'])!!}
                </div>
            </div>
        </div>


        {!! Form::close() !!}

            <div class="col-md-1" style="    margin-left: 10px;" >

                <div class="form-group">

                    <div class='input-group ' >
                        <button id="reset_search" class=" btn btn-default" style="margin-top: 25px" > Reset</button>
                    </div>
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
                            <i class="fa fa-bar-chart fa-lg"></i>
                            Sales
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-6 col-sm-offset-5">
                                        {{$sales->render()}}
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered inverse-table">
                                        <thead class=".thead-dark">
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Order No</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Branch Name</th>
                                            <th scope="col">Total Amount</th>
                                            <th scope="col">Order Status</th>
                                            <th scope="col">Payment Type</th>
                                            <th scope="col">Order Date</th>
                                            <th scope="col">Salesman</th>
                                            {{--<th scope="col">Advance Amount</th>--}}
                                            {{--<th scope="col">Order Status</th>--}}
                                            {{--<th scope="col">Priority</th>--}}
                                            {{--<th scope="col">Created Date</th>--}}
                                            {{--<th scope="col">Delivery Date</th>--}}
                                            {{--<th scope="col">Delivery Time</th>--}}
                                            <th scope="col">Image</th>
                                            {{--<th scope="col">Operations</th>--}}


                                            {{--<th scope="col">Handle</th>--}}
                                        </tr>
                                        </thead>
                                        <tbody id="table_body">

                                        @if($sales)



                                            @foreach($sales as $order)
                                                <tr>

                                                    @can('update-order')
                                                        <th><a id="{{$order->order_number}}" class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a></th>

                                                    @elsecannot('update-order')

                                                        <th  scope="row"> {{$order->order_number}}</th>
                                                    @endcan
                                                        <th  scope="row"> {{$order->order_number}}</th>

                                                    <td>{{$order->product_name}}</td>
                                                    <td>{{$order->branch_code}}</td>
                                                    <td>{{$order->total_price}}</td>
                                                    <td>{{$order->orderStatus?$order->orderStatus->name : 'Order Status Not Available'}}</td>
                                                    <td>{{$order->paymentType? $order->paymentType->name: 'Payment Type Not Available'}}</td>
                                                    <td>{{Carbon\Carbon::parse($order->order_date)->format('d-m-Y ').' '.Carbon\Carbon::parse($order->created_at)->format('h:i a')}}</td>
                                                    <td>{{$order->salesman}}</td>



                                                    {{--diffForHumans is used to get time as 20 hours ago etc--}}

                                                        @if($order->is_custom==1)
                                                            <td><img height="60" src="{{$order->photo ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/avatar-male.jpg' }}" alt="" > </td>
                                                        @else
                                                            <td><img height="60" src="{{$order->photo ?  URL::asset('images/Product_Images/'.$order->photo->path ): '/images/avatar-male.jpg' }}" alt="" > </td>
                                                          @endif


                                                    @endforeach
                                                @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-right">
                                    <h4 class="d-inline" style="font-weight: bold; font-family: 'Open Sans', sans-serif; display: inline">Total Sales (Rs) </h4>
                                    <p id="totalSale" style="display: inline;font-size: 20px;font-family: 'Open Sans', sans-serif">{{$totalSale}}</p>
                                </div>
                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>



    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" >

        <div class="modal-dialog" id="modal_dialog" style="position: fixed; width: fit-content">
            <div class="modal-dialog-inner">
                <div class="modal-content" id="modal_content">
                    <div class="modal-header">
                        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"  style="text-align: center">Order No</h4>
                    </div>
                    <div class="modal-body" style="">

                        <div class="row">
                            <div class="col-md-2 col-example"></div>
                            <div class="col-md-8 ml-auto col-example">
                                <div mag-thumb="inner">
                                    <img height="200" src="" alt=""  class="img-responsive zoom_image" id="modal_image" >
                                </div>
                            </div>
                            <div class="col-md-2 col-example"></div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 ml-auto col-example">
                                <h3 style="display: inline">Product Name :     </h3><p id="modal_productName" style="  display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p><br>
                                <h3  style="display: inline" >Weight : </h3><p id="modal_weight" style="display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p><br>
                                <h3   style="display: inline">Quantity : </h3><p id="modal_quantity" style="display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                                <br/><br/>
                                <h3 style="display: inline">Cake Message :     </h3><p id="modal_cakeMessage" style="  display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p><br>
                                <br/><br/>
                                <h3 style="display: inline">Order Instructions :    </h3><p id="modal_orderInstructions" style="  display: inline; font-weight: bold; font-size: 30px; margin-left: 10px "></p><br>
                                <br/><br/>
                            </div>
                            <div class="col-md-2 ml-auto col-example"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ml-5 col-example">

                                <h3 >Flavour</h3><div id="modal_flavours"  ></div><br>
                                <h3 >Materials </h3><div id="modal_materials"></div>
                                <br/><br/>
                                <h3 id="order_status_title" >Order Status</h3>

                                    <select id="modal_orderStatus" data-branch="{{env('BRANCH_CODE')}}" class="form-control" disabled >
                                        <option value=""></option>
                                    </select>

                            </div>
                            <br/><br/>
                            <div class="col-md-5  ml-5">
                                <h3>Delivery Date</h3><p id="modal_deliveryDate" style="display: inline;  font-weight: bold; font-size: 30px; margin-left: 10px " ></p><br>
                                <h3 >Delivery Time </h3><p id="modal_deliveryTime" style="display: inline  ;font-weight: bold; font-size: 30px; margin-left: 10px "></p>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-9 col-example">
                                <input type="hidden" id="order_number" value="">
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal">Close</button>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>







    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$sales->render()}}
        </div>
    </div>
@stop
<div class="modal-overlay"></div>

@section('scripts')
    <script src="{{asset('js/jquery-ui.js')}}"></script>
    <script src="{{asset('js/lightbox.js')}}"></script>
    <script src="{{asset('js/moment.js')}}"></script>
    <script src="{{asset('js/bootstrap.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>

        $(function () {
            $('#datetimepicker6').datetimepicker({     format: 'L'});
            $('#datetimepicker7').datetimepicker({
                useCurrent: false, //Important! See issue #1075
                format: 'L'
            });
            $("#datetimepicker6").on("dp.change", function (e) {
                $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
            });
            $("#datetimepicker7").on("dp.change", function (e) {
                $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
            });
        });



        function getsales()
        {

        }


        $(document).ready(function(){


            var currentModal ;
            var newmodal;
            var counter =0;
            var totalSales=0;
            $('#filter_button').click(function() {

                var generateAll = false;
                var fromDate = $('#date_from').val();
                var toDate = $('#date_to').val();
                var searchId = $('#search_id').val();
                var searchProductName = $('#search_productName').val();
                var searchBranchCode = $('#search_branchCode').val();
                var paymentType= $('#paymentType').val();
                var orderStatus = $('#orderStatus').val();
                var paymentStatus = $('#paymentStatus').val();


                if(fromDate == "" && toDate == "")
                {
                    generateAll = confirm("Generate report of complete sales ?")
                }
                else if(fromDate != "" || toDate != "") {
                    generateAll = true;
                }


                if(generateAll) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "post",
                        url: "/sales/search", // path to function
                        dataType: 'json',
                        cache: false,
                        data: {
                            from_date: fromDate,
                            to_date: toDate,
                            search_id: searchId,
                            search_productName:searchProductName,
                            search_branchCode:searchBranchCode,
                            payment_type: paymentType,
                            order_status: orderStatus,
                            payment_status: paymentStatus,
                        },
                        success: function (val) {

                            // if (paymentType == 0 && orderStatus == 0) {
// alert("both null if");
                            totalSales=0;
                                if (val.length != 0) {
                                    $('#table_body').empty();
                                    var trHTML = '';
                                    var image='';
                                    $.each(val, function (i, item) {
                                        if(item.is_custom==1)
                                        {
                                             image='<img height="60" src= /images/Custom_Orders/' + item.photo_path + ' alt="" >' ;
                                        }
                                        else if(item.is_custom==0)
                                        {
                                            image='<img height="60" src= /images/Product_Images/' + item.photo_path+ ' alt="" >' ;
                                        }
                                        totalSales = totalSales + item.total_price;
                                        trHTML += '<tr>' +
                                            ' <th><a id=' + item.order_number + ' class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a></th>' +
                                            '<td>' + item.order_number +
                                            '</td><td>' + item.product_name +
                                            '</td><td>' + item.branch_code +
                                            '</td><td>' + item.total_price +
                                            '</td><td>' + item.order_status.name +
                                            '</td><td>' + item.payment_type.name +
                                            '</td><td>' + moment(item.order_date).format('DD-MM-YYYY ')+' '+moment(item.created_at).format('hh:mm A') +
                                            '</td><td>' + item.salesman +
                                            '</td><td>'+image +
                                            '</td></tr>';
                                    });

                                    $('#table_body').append(trHTML);
                                    $('#totalSale').html(totalSales);

                                }
                                else {
                                    $('#table_body').empty();
                                    $('#totalSale').html('0');
                                }


                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }

                    });
                }

            });



            $('#search_id').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('#filter_button').click();//Trigger search button click event
                }
            });
            $('#search_productName').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('#filter_button').click();//Trigger search button click event
                }
            });
            $('#search_branchCode').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('#filter_button').click();//Trigger search button click event
                }
            });



            $('#reset_search').click(function() {



                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "get",
                        url: "/sales/reset", // path to function
                        dataType: 'json',
                        cache: false,

                        success: function (val) {

                            // if (paymentType == 0 && orderStatus == 0) {
// alert("both null if");
                            totalSales=0;
                            if (val.length != 0) {
                                $('#table_body').empty();
                                var trHTML = '';
                                var       image = '';
                                $.each(val, function (i, item) {
                                    if(item.is_custom==1)
                                    {
                                        image='<img height="60" src= /images/Custom_Orders/' + item.photo_path + ' alt="" >' ;
                                    }
                                    else if(item.is_custom==0)
                                    {
                                        image='<img height="60" src= /images/Product_Images/' + item.photo_path+ ' alt="" >' ;
                                    }
                                    totalSales = totalSales + item.total_price;
                                    trHTML += '<tr>' +
                                        ' <th><a id=' + item.order_number + ' class="link table-row"> <i  class="fa fa-eye fa-2x"></i> </a></th>' +
                                        '<td>' + item.order_number +
                                        '</td><td>' + item.product_name+
                                        '</td><td>' + item.branch_code +
                                        '</td><td>' + item.total_price +
                                        '</td><td>' + item.order_status.name +
                                        '</td><td>' + item.payment_type.name +
                                        '</td><td>' + moment(item.order_date).format('DD-MM-YYYY ')+' '+moment(item.created_at).format('hh:mm A') +
                                        '</td><td>' + item.salesman +
                                        '</td><td>'+image +
                                        '</td></tr>';
                                });

                                $('#table_body').append(trHTML);
                                $('#totalSale').html(totalSales);

                            }
                            else {
                                $('#table_body').empty();
                                $('#totalSale').html('0');
                            }

                             $('#date_from').val(' ');
                            $('#date_to').val(' ');
                            $('#search_id').val(' ');
                            $('#search_productName').val(' ');
                            $('#search_branchCode').val(' ');
                            $('#paymentType').val('0');
                            $('#orderStatus').val('0');

                        },
                        error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }

                    });


            });



            $(document).on('click','.link',function () {


            // $('.link').on("click",function() {

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
                    data: { orderId: order_id},
                    success: function(val){



                        try{
                            //    var    response = val[name];


                            console.log('Returned',val[0]['product_name'][0]['name']);



                            if($("#myModal").is(':visible'))
                            {
                                if(val[0]['is_custom']==1) {
                                    var image ='/images/Custom_Orders/'+val[0]['image'];
                                }else
                                {
                                    var image ='/images/Product_Images/'+val[0]['image'];
                                }
                                var orderStatus = val[0]['order_status'];

                                newmodal = $('#myModal').clone();
                                newmodal.addClass(['.modal-open','.modal']);
                                counter++;
                                newmodal.attr('id','myModal'+counter);


                                newmodal.find("#myModalLabel").text("Order Number "+val[0]['number']);
                                newmodal.find("#order_number").attr('value',val[0]['id']);
                                newmodal.find("#modal_imageAnchor").attr("href",image);
                                newmodal.find("#modal_imageAnchor").attr("href",val[0]['image']);
                                newmodal.find("#modal_image").attr("src",image);
                                newmodal.find("#modal_productName").text(""+val[0]['product_name']);
                                newmodal.find("#modal_weight").text(""+val[0]['weight']+' -pounds');
                                newmodal.find("#modal_quantity").text(""+val[0]['quantity']);
                                if(val[0]['remarks']!=null ) {
                                    currentModal.find("#modal_cakeMessage").text("" + val[0]['remarks']);

                                }
                                if(val[0]['instructions']!=null ) {
                                    currentModal.find("#modal_orderInstructions").text("" + val[0]['instructions']);

                                }

                                newmodal.find('#modal_flavours').empty();
                                newmodal.find('#modal_materials').empty();

                                $.each(val[1],function(i,itemData){
                                    newmodal.find("#modal_flavours").append($('<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px">'+itemData.flavour_name+'</P><br>'));
                                });

                                $.each(val[2],function(i,itemData){
                                    newmodal.find("#modal_materials").append($('<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px">'+itemData.material_name+'</P><br>'));
                                });



                                newmodal.find('#modal_orderStatus').empty();




                                $.each(val[4], function (index, dat) {
                                    newmodal.find('#modal_orderStatus').append('<option value="'+ dat.id +'">' + dat.name +'</option>');
                                    //   console.log('Order status', dat.name)
                                });
                                newmodal.find("#modal_orderStatus option[value="+orderStatus+"]").prop('selected', true);





                                newmodal.find("#modal_deliveryDate").text(""+new Date(val[0]['delivery_date']).toLocaleDateString());
                                newmodal.find("#modal_deliveryTime").text(""+val[0]['delivery_time']);


                                modal_content= newmodal.find('.modal-content');
                                modal_dialog= newmodal.find('.modal-dialog');
                                modal_content.resizable({
                                    alsoResize:modal_dialog,

                                    //minHeight: 150
                                });




                                newmodal.modal({
                                    backdrop: false,
                                    show: true
                                });






                                currentModal = newmodal;

                            }
                            else {

                                currentModal = $('#myModal').clone();
                                counter++;
                                currentModal.attr('id','myModal'+counter);
                                if(val[0]['is_custom']==1) {
                                    var image ='/images/Custom_Orders/'+val[0]['image'];
                                }else
                                {
                                    var image ='/images/Product_Images/'+val[0]['image'];
                                }
                                var orderStatus = val[0]['order_status'];


                                currentModal.find("#myModalLabel").text("Order Number "+val[0]['order_number']);
                                currentModal.find("#modal_image").attr("src",image);
                                currentModal.find("#order_number").attr('value',val[0]['id']);
                                currentModal.find("#modal_imageAnchor").attr("href",val[0]['image']);
                                currentModal.find("#modal_productName").text(""+val[0]['product_name']);
                                currentModal.find("#modal_weight").text(""+val[0]['weight']+' -pounds');
                                currentModal.find("#modal_quantity").text(""+val[0]['quantity']);

                                if(val[0]['remarks']!=null ) {
                                    currentModal.find("#modal_cakeMessage").text("" + val[0]['remarks']);

                                }
                                if(val[0]['instructions']!=null ) {
                                    currentModal.find("#modal_orderInstructions").text("" + val[0]['instructions']);

                                }

                                currentModal.find('#modal_flavours').empty();
                                currentModal.find('#modal_materials').empty();
                                $.each(val[1],function(i,itemData){
                                    currentModal.find("#modal_flavours").append($('<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px"> > '+itemData.flavour_name+'</P><br>'));
                                });

                                $.each(val[2],function(i,itemData){
                                    currentModal.find("#modal_materials").append($('<p style="display: inline  ;font-weight: bold; font-size: 20px; margin-left: 10px"> > '+itemData.material_name+'</P><br>'));
                                });


                                currentModal.find('#modal_orderStatus').empty();




                                $.each(val[3], function (index, dat) {
                                    currentModal.find('#modal_orderStatus').append('<option value="'+ dat.id +'">' + dat.name +'</option>');
                                    //   console.log('Order status', dat.name)
                                });
                                currentModal.find("#modal_orderStatus option[value="+orderStatus+"]").prop('selected', true);





                                currentModal.find("#modal_deliveryDate").text(""+new Date(val[0]['delivery_date']).toLocaleDateString());
                                currentModal.find("#modal_deliveryTime").text(""+val[0]['delivery_time']);
                                //      currentModal.find("#save_button").attr('onclick','saveOrderStatus('+val[0]['id']+','+6+')');

                                if (!($('.modal.in').length)) {
                                    $('.modal-dialog').css({
                                        top: 0,
                                        left: 0
                                    });
                                }
                                var modal_content= currentModal.find('.modal-content');
                                var modal_dialog= currentModal.find('.modal-dialog');
                                modal_content.resizable({
                                    alsoResize:modal_dialog,

                                    //minHeight: 150
                                });
                                modal_dialog.draggable({
                                });

                                currentModal.on('show.bs.modal', function () {
                                    $(this).find('.modal-body').css({
                                        'max-height':'60%'
                                    });
                                });

                                currentModal.modal({
                                    backdrop: false,
                                    show: true
                                });




                            }


                        }catch(e) {
                            alert('Exception while request..'+e);
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



            $('#generate_csv').click(function() {

                var fromDate = $('#date_from').val();
                var toDate = $('#date_to').val();
                var searchId = $('#search_id').val();
                var searchProductName = $('#search_productName').val();
                var searchBranchCode = $('#search_branchCode').val();
                var paymentType= $('#paymentType').val();
                var orderStatus = $('#orderStatus').val();
                var paymentStatus = $('#paymentStatus').val();

                var generateAll = false;


              if(fromDate == "" && toDate == "")
                {
                    generateAll = confirm("Generate report of complete sales ?")
                }
                else if(fromDate != "" || toDate != "") {
                    generateAll = true;
              }
              if(generateAll)
              {
                  $('#form_csv').submit();
              }




            });



        });







    </script>
@stop
