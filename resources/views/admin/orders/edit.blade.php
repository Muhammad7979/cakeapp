@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif



            <div class="panel panel-info">

                <div class="panel-heading">


                    <h3 class="panel-title bariol-thin">
                        <i class="fa fa-shopping-cart"></i>
                        Edit Order
                    </h3>
                </div>

                <div class="panel-body">

                    <div class="col-md-4">

                            @if($order->is_custom==1)
                                <img height="200" src="{{$order->photo ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/Placeholder.png' }}" alt="" >
                            @else
                                <img height="200" src="{{$order->photo ?  URL::asset('images/Product_Images/'.$order->photo->path ): '/images/Placeholder.png' }}" alt="" class="img-rounded img-responsive">
                            @endif

                                {{--<img height="200" src="{{$order->photo ? $order->photo->path : '/images/avatar-male.jpg' }}" alt=""  class="img-rounded img-responsive" >--}}
                                {!! Form::model($order,['method' => 'PATCH', 'action'=> ['AdminOrderController@update',$order->order_number],'files'=>true]) !!}

                        <div class="row" style="padding-top: 5%">
                                  <div class="col-md-12">
                                      <h3 class="text-center">Final Image </h3>
                                     <img class="img-responsive img-rounded"  height="400" width="" id="final-image-display" src="{{$order->final_image?  URL::asset('/images/Created_Order_Images/'.$order->final_image) : '' }}"  />
                                    @if(env('IS_SERVER')!=1 && env('BRANCH_CODE')==$order->assigned_to)
                                         <input style="padding-top: 4%" name="final_image" type='file' onchange="readURL(this);" />
                                        @endif
                                 </div>


                        </div>

                        </div>

                    <div class="col-md-8">
                        <div class="row">

                        {{--<div class="row">--}}


                        <div class="col-md-6 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('id','Order ID')!!}

                                {!! Form::text('order_number',null,['class'=>'form-control','readonly'=>'true','id'=>'orderNumber'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form::label('product_name','Cake')!!}
                                {!! Form::text('name',$order->product_name,['class'=>'form-control','readonly'=>'true'])!!}


                            </div>
                            <h4>
                                <i class="fa fa-book"></i>
                                Flavours
                            </h4>

                            <div class="form-group">
                                {!! Form::label('flavour_id','Flavours')!!}
                                {!! Form::select('flavour_id[]', []+$flavours,$flavour_ids, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'flavour_select','tabindex'=>'-1','aria-hidden'=>'true')) !!}
                            </div>


                            <h4>
                                <i class="fa fa-book"></i>
                                Materials
                            </h4>
                            <div class="form-group">
                                {!! Form::label('material_id','Materials')!!}
                                {!! Form::select('material_id[]', []+$materials,$material_ids, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'material_select','tabindex'=>'-1','aria-hidden'=>'true','readonly'=>'true')) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('weight','Weight')!!}
                                {!! Form::text('weight' ,$order->weight .'- pounds',['class'=>'form-control','id'=>'weight_product','disabled'=>'true'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('quantity','Quantity')!!}
                                {!! Form::number('quantity',null,['class'=>'form-control','id'=>'quantity','readonly'=>'true'])!!}
                            </div>




                                <div class="form-group">
                                    {!! Form::label('remarks','Cake Message')!!}
                                    {!! Form::textarea('remarks',null,['class'=>'form-control','readonly'=>'readonly','rows'=>3,'cols'=>5])!!}
                                </div>
                            <div class="form-group">
                                {!! Form::label('instructions','Instructions')!!}
                                {!! Form::textarea('instructions',null,['class'=>'form-control','readonly'=>'readonly','rows'=>3,'cols'=>5])!!}
                            </div>





                        </div>
                        <div class="col-md-6 col-xs-12">



                            <div class="form-group">
                                {!! Form::label('customer_name','Customer Name')!!}
                                {!! Form::text('customer_name',null,['class'=>'form-control','readonly'=>'true'])!!}
                            </div>



                            <div class="form-group">
                                {!! Form::label('salesman','Salesman')!!}
                                {!! Form::text('salesman',null,['class'=>'form-control','readonly'=>'true'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form::label('customer_email','Customer Email')!!}
                                {!! Form::email('customer_email',null,['class'=>'form-control','readonly'=>'true'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form::label('customer_phone','Customer Phone')!!}
                                {!! Form::text('customer_phone',null,['class'=>'form-control','readonly'=>'true'])!!}
                            </div>
                            <br/>
                            <br/>
                            <div class="form-group">
                                {!! Form::label('order_date','Order Date')!!}
                                {!! Form::text('order_date',Carbon\Carbon::parse($order->order_date)->format('d-m-Y ').'-'.Carbon\Carbon::parse($order->created_at)->format('h:i a '),['class'=>'form-control','disabled'=>true])!!}
                            </div>



                            <div class="form-group">

                                {!! Form::label('delivery_date','Delivery Date')!!}

                                <div class='input-group date' id='datetimepicker5'>
                                    <input type='text' class="form-control" name="delivery_date" readonly value="{{ Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d')}}" />
                                    <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                </div>

                            </div>



                            <div class="form-group">

                                {!! Form::label('delivery_time','Delivery Time')!!}

                                <div class='input-group time' id='datetimepicker2'>
                                    <input type='text' class="form-control" name="delivery_time" readonly value="{{$order->delivery_time}}" />
                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                </div>

                            </div>



                                <div class="form-group">
                                    {!! Form::label('priority','Order Priority')!!}
                                    {!! Form::select('priority',[''=>'Select Priority']+$priority,$order->priority,['class'=>'form-control','disabled'=>'true'])!!}
                                </div>









                        </div>
                            <div class="row">

                            </div>

                        <div class="row">


                                <div class= " col-md-5 " style="margin-left: 1.5%; margin-top: 5%">
                                    <div class="form-group">
                                        {!! Form::label('payment_type','Payment Type')!!}
                                        {!! Form::select('payment_type',[''=> 'Select Payment Type']+$paymentTypes,$order->payment_type,['class'=>'form-control','disabled'=>'true'])!!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('order_type','Order Type')!!}
                                        {!! Form::select('order_type',[''=> 'Select Order Type']+$orderTypes,$order->order_type,['class'=>'form-control','disabled'=>'true'])!!}
                                    </div>
                                    @if($order->assigned_to == env('BRANCH_CODE'))

                                        @if($lock==1)
                                        <div class="form-group">
                                            {!! Form::label('order_status','Order Status')!!}
                                            {!! Form::select('order_status',[''=> 'Select Order Status']+$orderStatus,$order->order_status,['class'=>'form-control','disabled'=>'true'])!!}
                                        </div>
                                            @else
                                            <div class="form-group">
                                                {!! Form::label('order_status','Order Status')!!}
                                                {!! Form::select('order_status',[''=> 'Select Order Status']+$orderStatus,$order->order_status,['class'=>'form-control'])!!}
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            {!! Form::label('payment_status','Payment Status')!!}
                                            {!! Form::select('payment_status',[''=> 'Select Payment Status']+$paymentStatus,$order->payment_status,['class'=>'form-control','disabled'=>true])!!}
                                        </div>
                                            <div class="form-group">
                                                {!! Form::label('order_branch','Assigned From')!!}
                                                {!! Form::select('order_branch',[''=> 'Select Branch'],null,['class'=>'form-control branches','id'=>'order_branch','disabled'=>true])!!}
                                            </div>

                                        @if($lock==1)
                                                <div class="form-group">
                                                    {!! Form::label('assigned_to','Assigned To')!!}
                                                    {!! Form::select('assigned_to',[''=> 'Select Branch'],null,['class'=>'form-control assigned_to','id'=>'assigned_to','disabled'=>'true'])!!}
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    {!! Form::label('assigned_to','Assigned To')!!}
                                                    {!! Form::select('assigned_to',[''=> 'Select Branch'],null,['class'=>'form-control assigned_to','id'=>'assigned_to'])!!}
                                                </div>
                                            @endif



                                    @else


                                        <div class="form-group">
                                            {!! Form::label('order_status','Order Status')!!}
                                            {!! Form::select('order_status',[''=> 'Select Order Status']+$orderStatus,$order->order_status,['class'=>'form-control','disabled'=>'true'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('payment_status','Payment Status')!!}
                                            {!! Form::select('payment_status',[''=> 'Select Payment Status']+$paymentStatus,$order->payment_status,['class'=>'form-control','id'=>'payment_status','disabled'=>'true'])!!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('order_branch','Assigned From')!!}
                                            {!! Form::select('order_branch',[''=> 'Select Branch'],null,['class'=>'form-control branches','id'=>'order_branch','disabled'=>true])!!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('assigned_to','Assigned To')!!}
                                            {!! Form::select('assigned_to',[''=> 'Select Branch'],null,['class'=>'form-control branches','id'=>'assigned_to','disabled'=>true])!!}
                                        </div>

                                    @endif
                                    <div class="form-group">
                                        {!! Form::label('delivery_sms','Delivery Sms Status')!!}
                                        {!! Form::text('delivery_sms_label',$order->delivery_sms==1? 'Sent':'Not Sent',['class'=>'form-control','readonly'=>'true'])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('delivery_sms_response','Delivery Sms Response')!!}
                                        {!! Form::textarea('delivery_sms_response_label',$order->delivery_sms_response? $order->delivery_sms_response:'',['class'=>'form-control','readonly'=>'true','rows'=>3,'cols'=>4])!!}
                                    </div>




                                </div>



                            <div class="col-md-5 " style="    margin-left: 7%; margin-top: 5%">
                                <div class="form-group">
                                    {!! Form::label('total_price','Total (Rs)')!!}
                                    {!! Form::number('total_price',null,['class'=>'form-control' , 'id'=>'total_price', 'readonly'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('advance_price','Advance (Rs)')!!}
                                    {!! Form::number('advance_price',null,['class'=>'form-control','id'=>'advance_price' ,'readonly'=>'true'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('discount','Discount(Rs)')!!}
                                {!! Form::number('discount',null,['class'=>'form-control','id'=>'discount_price' ,'readonly'=>'true'])!!}
                                </div>
                                 <div class="form-group">
                                    {!! Form::label('discounted_total','After Discount (Rs)')!!}
                                    {!! Form::number('discounted_total',($order->total_price-$order->discount),['class'=>'form-control','id'=>'discount_price' ,'readonly'=>'true'])!!}
                                </div>
                                <br/>
                                <br/>

                                <div class="form-group">
                                    {!! Form::label('pending_amount','Pending Amount (Rs)')!!}
                                    {!! Form::number('pending_amount',$order->pending_amount,['class'=>'form-control','id'=>'pending_amount' ,'readonly'=>'true'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('pending_amount_paid_date','Paid Date')!!}
                                    {!! Form::text('pending_amount_paid_date',$order->pending_amount_paid_date? \Carbon\Carbon::parse($order->pending_amount_paid_date)->format('d-m-Y').' '.\Carbon\Carbon::parse($order->pending_amount_paid_time)->format('h:i a'):'',['class'=>'form-control','id'=>'pending_amount_paid_date' ,'readonly'=>'true'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('pending_amount_paid_branch','Paid Branch')!!}
                                    {!! Form::text('pending_amount_paid_branch',$order->pending_amount_paid_branch,['class'=>'form-control','id'=>'pending_amount_paid_branch' ,'readonly'=>'true'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('balance','Balance (Rs)')!!}
                                    {!! Form::number('balance',($order->total_price-$order->advance_price-$order->discount-$order->pending_amount),['class'=>'form-control','readonly' => 'true','id'=>'balance'])!!}
                                </div>
                            </div>
                        </div>

                            {!! Form::hidden('live_synced',0,['class'=>'form-control' , 'id'=>'live_synced_hidden'])!!}
                            {{--{!! Form::hidden('payment_status',0,['class'=>'form-control','readonly' => 'true','id'=>'payment_status'])!!}--}}
                            {{--{!! Form::hidden('order_status',$order->order_status,['class'=>'form-control','readonly' => 'true','id'=>'order_status_hidden'])!!}--}}
                            {{--{!! Form::hidden('branch_id',$branchId->value,['class'=>'form-control','readonly' => 'true','id'=>'branch_id'])!!}--}}
                            {{--{!! Form::hidden('branch_code',env("BRANCH_CODE"),['class'=>'form-control','readonly' => 'true','id'=>'branch_code'])!!}--}}
                            {{--{!! Form::hidden('user_id',auth()->user()->id,['class'=>'form-control','readonly' => 'true','id'=>'user_id'])!!}--}}
                            {{--{!! Form::hidden('is_active',1,['class'=>'form-control','readonly' => 'true','id'=>'is_active'])!!}--}}
                            {{--{!! Form::hidden('photo_id',null,['class'=>'form-control','readonly' => 'true','id'=>'photo_id'])!!}--}}



                        </div>




                        <div class=" col-md-12 col-xs-12 pull-right" style="padding-top: 3%">


                            <div class="form-group">
                                <a class=" btn btn-default" href="{{route('orders.index')}}" style="margin-right: 5%;margin-left: 25%;"> Cancel</a>
                                @if(env('IS_SERVER')!=1 && env('BRANCH_CODE')==$order->assigned_to )
                                    @if(($order->total_price-$order->advance_price-$order->discount-$order->pending_amount)!=0)
                                <a class="btn btn-default" style="margin-right: 5%" onclick="payInFull()"  id="pay_in_full"> Pay in Full <span class="fa fa-credit-card fa-xl" aria-hidden="true"></span></a>
                                @endif
                                        @can('update-order')
                                {!! Form:: submit('Update Order',['class'=>'btn btn-primary'])!!}
                                @endcan
                                @endif
                               <a class="btn btn-default"  href="/generateInvoice/{{$order->order_number}}" target="_blank">Print Recepit  <span class="fa fa-print fa-xl" aria-hidden="true"></span>
                                </a>

                            </div>
                        </div>
                        {!! Form::close() !!}
                        {{--</div>--}}
                    </div>



                </div>
                </div>


            </div>



        </div>



    </div>
    @include('includes.errorReporting')

@stop
@section('scripts')

    <script src="{{asset('js/select2.js')}}"></script>
    <script src="{{asset('js/moment.js')}}"></script>
    <script src="{{asset('js/bootstrap.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script type="text/javascript">



        function payInFull() {

            var order_number = $('#orderNumber').val();

            // alert("In pay in full method");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: "/order/payment", // path to function
                cache: false,
                data: {
                    orderNumber: order_number,
                },
                success: function (data) {

                    $('#payment_status').val(1);
                    $('#payment_status').prop('disabled',true);
                    $('.assigned_to').prop('disabled',true);


                        alert(" "+data['Status'] );

                    $('#pending_amount').val(data['Pending_Amount']);
                    $('#pending_amount_paid_date').val(''+data['Pending_Amount_Date']);
                    $('#pending_amount_paid_branch').val(''+data['Pending_Branch']);
                    $('#balance').val(0);
                    $('#pay_in_full').hide()

                        // window.location.reload();


                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });


        }


        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();


                reader.onload = function (e) {

                    $('#final-image-display').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }


        function populateAssignedTo()
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",

                url: "/branches/get", // path to function
                cache: false,
                success: function (val) {

                    $.each(val, function(key, value) {


                            $('#assigned_to')
                                .append($('<option>', {value: value.code})
                                    .text(value.name));

                        $('#order_branch')
                            .append($('<option>', {value: value.code})
                                .text(value.name));

                    });

                    $('#assigned_to').val('{{$order->assigned_to}}');
                    $('#order_branch').val('{{$order->branch_code}}');
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }
        function generateInvoice(id) {


            var orderid =id;
alert(id)


            $.ajax({
                type: "get",
                url: "/generateInvoice/"+id, // path to function
                cache: false,


                success: function(val){


                    try{






                    }catch(e) {
                        alert('Exception while request..'+e);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });




            // modal.hide();
            //alert('order id '+orderid+" order Status "+orderStatus);
        }
        $(document).ready(function(){
            populateAssignedTo();
            $('#flavour_select').select2({disabled:false})

            $('#material_select').select2({disabled:false});

            $('#datetimepicker1').datetimepicker({
                format: 'L'
            });
            $('#datetimepicker2').datetimepicker({
                format: 'LT'
            });

            $("#final_image").change(function(){
                readURL(this);
            });




        });







    </script>

@stop
