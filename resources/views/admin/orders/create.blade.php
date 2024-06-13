@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">



                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-shopping-cart"></i>
                            Create Order
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-4">
                        <div class="row">
                        <div class="col-md-12 " style="display: none" id="productImageDiv">
                            <img height="200" src="" alt=""  class="img-rounded img-responsive" id="product_image">
                        </div>
                        </div>
                        </div>
                        <div class="col-md-8">

                        {{--<div class="row">--}}
                        {!! Form::open (['method'=>'POST','action'=>'AdminOrderController@store',]) !!}
                        <div class="col-md-6 col-xs-12">




                            <div class="form-group">
                                {!! Form::label('product_id','Cake')!!}
                                <select name="product_id" id="lists_products" class="form-control">
                                    <option value="" selected> Select Cake</option>
                                   @foreach($products as $product)
                                    <option value="{{$product->id}}" data-thumb="{{$product->photo->path}}" data-price="{{$product->price}}" data-weight="{{$product->weight}}" data-pid="{{$product->photo_id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>


                            </div>
                            <h4>
                                <i class="fa fa-book"></i>
                                Flavours
                            </h4>
                            <div class="form-group">
                                {!! Form::label('flavourCategory_id','Flavour Category')!!}
                                {!! Form::select('flavourCategory_id', [''=>'Select Category']+$flavourCategories,null, array( 'class' => 'form-control margin  ','id'=>'flavourCategory_select')) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('flavour_id','Flavours')!!}
                                {!! Form::select('flavour_id[]', [],null, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'flavour_select','tabindex'=>'-1','aria-hidden'=>'true')) !!}
                            </div>


                            <h4>
                                <i class="fa fa-book"></i>
                                Materials
                            </h4>
                            <div class="form-group">
                                {!! Form::label('material_id','Materials')!!}
                                 {!! Form::select('material_id[]', [],null, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'material_select','tabindex'=>'-1','aria-hidden'=>'true')) !!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('weight','Weight')!!}
                                {!! Form::number('weight',null,['class'=>'form-control','id'=>'weight_product'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('quantity','Quantity')!!}
                                {!! Form::number('quantity',1,['class'=>'form-control','id'=>'quantity'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form::label('remarks','Remarks')!!}
                                {!! Form::text('remarks',null,['class'=>'form-control'])!!}
                            </div>





                        </div>
                        <div class="col-md-6 col-xs-12">



                                    <div class="form-group">
                                        {!! Form::label('customer_name','Customer Name')!!}
                                        {!! Form::text('customer_name',null,['class'=>'form-control'])!!}
                                    </div>



                                    <div class="form-group">
                                        {!! Form::label('salesman','Salesman')!!}
                                        {!! Form::text('salesman',null,['class'=>'form-control'])!!}
                                    </div>


                                    <div class="form-group">
                                        {!! Form::label('customer_email','Customer Email')!!}
                                        {!! Form::email('customer_email',null,['class'=>'form-control'])!!}
                                    </div>


                                    <div class="form-group">
                                        {!! Form::label('customer_phone','Customer Phone')!!}
                                        {!! Form::text('customer_phone',null,['class'=>'form-control'])!!}
                                    </div>



                                    <div class="form-group">

                                                {!! Form::label('delivery_date','Delivery Date')!!}

                                        <div class='input-group date' id='datetimepicker1'>
                                                <input type='text' class="form-control" name="delivery_date" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                        </div>

                                    </div>



                                    <div class="form-group">

                                                     {!! Form::label('delivery_time','Delivery Time')!!}

                                        <div class='input-group time' id='datetimepicker2'>
                                                    <input type='text' class="form-control" name="delivery_time" />
                                            <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('priority','Order Priority')!!}
                                        {!! Form::select('priority',[''=>'Select Priority']+$priority,null,['class'=>'form-control'])!!}
                                    </div>









                        </div>

                            <div class="row">

                                <div class="row">
                                    <div class="col-md-offset-5 col-md-5 pull-right">
                                        <div class="form-group">
                                            {!! Form::label('payment_type','Payment Type')!!}
                                            {!! Form::select('payment_type',[''=> 'Select Payment Type']+$paymentTypes,null,['class'=>'form-control'])!!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('order_type','Order Type')!!}
                                            {!! Form::select('order_type',[''=> 'Select Order Type']+$orderTypes,null,['class'=>'form-control'])!!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('assigned_to','Assigned To')!!}
                                            {!! Form::select('assigned_to',[''=> 'Select Branch'],null,['class'=>'form-control','id'=>'assigned_to'])!!}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-offset-5 col-md-5 pull-right" style="padding-top:10%">
                                    <div class="form-group">
                                        {!! Form::label('total_price','Total (Rs)')!!}
                                        {!! Form::number('total_price',0,['class'=>'form-control' , 'id'=>'total_price', 'readonly'])!!}
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('advance_price','Advance (Rs)')!!}
                                        {!! Form::number('advance_price',0,['class'=>'form-control','id'=>'advance_price' ])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('discount','Discount (Rs)')!!}
                                        {!! Form::number('discount',0,['class'=>'form-control','id'=>'discount_price' ])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('balance','Balance (Rs)')!!}
                                        {!! Form::number('balance',0,['class'=>'form-control','readonly' => 'true','id'=>'balance'])!!}
                                    </div>
                                </div>

                                {{--{!! Form::hidden('total_price',0,['class'=>'form-control' , 'id'=>'total_price_hidden'])!!}--}}
                                {!! Form::hidden('payment_status',0,['class'=>'form-control','readonly' => 'true','id'=>'payment_status'])!!}
                                {!! Form::hidden('order_status','Un-Processed',['class'=>'form-control','readonly' => 'true','id'=>'order_status'])!!}
                                {!! Form::hidden('branch_id',$branchId->value,['class'=>'form-control','readonly' => 'true','id'=>'branch_id'])!!}
                                {!! Form::hidden('branch_code',env("BRANCH_CODE"),['class'=>'form-control','readonly' => 'true','id'=>'branch_code'])!!}
                                {!! Form::hidden('user_id',auth()->user()->id,['class'=>'form-control','readonly' => 'true','id'=>'user_id'])!!}
                                {!! Form::hidden('is_active',1,['class'=>'form-control','readonly' => 'true','id'=>'is_active'])!!}
                                {!! Form::hidden('is_custom',0,['class'=>'form-control','readonly' => 'true','id'=>'is_custom'])!!}
                                {!! Form::hidden('live_synced',0,['class'=>'form-control','readonly' => 'true','id'=>'is_active'])!!}
                                {!! Form::hidden('order_number',"".env('BRANCH_CODE')."-".((int)\Illuminate\Support\Facades\Session::get('lastId')+1),['class'=>'form-control','readonly' => 'true','id'=>'order_number'])!!}
                                {!! Form::hidden('photo_id',null,['class'=>'form-control','readonly' => 'true','id'=>'photo_id'])!!}



                            </div>




                        <div class="col-md-offset-5 col-md-6 col-xs-12">
                            <div class="form-group">
                                {!! Form:: submit('Place Order',['class'=>'btn btn-primary'])!!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                        {{--</div>--}}
                    </div>



                </div>



            </div>



        </div>



    </div>
    @include('includes.errorReporting')

@stop
@section('scripts')

    <script src="{{asset('js/select2.js')}}"></script>
    <script src="{{asset('js/select2.min.js')}}"></script>
    <script src="{{asset('js/moment.js')}}"></script>
    <script src="{{asset('js/bootstrap.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/bootstrap-datetimepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $('#datetimepicker1').datetimepicker({
                format: 'L'
            });
            $('#datetimepicker2').datetimepicker({
                format: 'LT'
            });


        });

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

                        if(value.code == '{{env('BRANCH_CODE')}}')
                        {

                            $('#assigned_to')
                                .append($('<option>', { value : value.code })
                                    .text("Current Branch"));
                        }
                        else {
                            $('#assigned_to')
                                .append($('<option>', {value: value.code})
                                    .text(value.name));
                        }

                        $('#assigned_to').val('{{env('BRANCH_CODE')}}');
                    });


                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }
        $(document).ready(function(){

            populateAssignedTo();
            $('#flavour_select').select2();

            $('#material_select').select2();

            function format(state) {
                var thumb = $(state.element).data('thumb');
               if (!thumb) return state.text; // optgroup

                return '<img height="60"  sytle="display: inline-block;" src="/images/Product_Images/' + thumb+ '" /> ' + state.text ;
            }
            function calculateTotal()
            {

                var productPrice = $( "#lists_products :selected" ).data('price');

                var quantity =parseInt($('#quantity').val());
                var totalPrice =0;
                var weight = parseInt($('#weight_product').val());
                $('#flavour_select :selected').each(function () {
                   var flavour = $(this).text().split(' - ');
                   console.log('cost :', flavour[1]);
                   totalPrice = totalPrice + (parseInt(flavour[1])*weight);
                });
                $('#material_select :selected').each(function () {
                    var material = $(this).text().split(' - ');
                    console.log('cost :', material[1]);
                    totalPrice = totalPrice + (parseInt(material[1])*weight);
                });

                 totalPrice = quantity *(totalPrice + productPrice);



            //     console.log('Total cost :', totalPrice);
                $("#total_price").attr('value',totalPrice);
                $("#total_price_hidden").attr('value',totalPrice);
              //  alert('Total Price' + parseInt(totalPrice));

            }


            $("#lists_products").select2({
                templateResult: format,

                escapeMarkup: function(m) { return m; }
            });





        $(function() {
            $("#lists_products").on("change",function() {
                var image = $(this).find(':selected').data('thumb');
                var photoId = $(this).find(':selected').data('pid');
                var price = $(this).find(':selected').data('price');
                var weight = $(this).find(':selected').data('weight');
                if (image=="")
                {
                    return;
                } // please select - possibly you want something else here


                $('#productImageDiv').attr('style','display:block');

                $("#product_image").attr('src','/images/Product_Images/'+image);
                $("#total_price").attr('value',price);
                $("#weight_product").attr('value',weight);

                // console.log('Photo_id',photoId);
                $("#photo_id").attr('value',photoId);

        });
        });



        $(function() {
            $("#flavourCategory_select").on("change",function() {

                var productId = $( "#lists_products" ).val();
                 var catId=   $(this).val();

                // alert('product id '+productId,);
                if(catId)
                {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({

                        url:'/flavours/material/get',
                        type:'POST',
                        data: { product_id: productId, cat_id:catId },
                        datatype:'json',

                        success:function (data) {

                            $('#flavour_select').empty();
                            $('#material_select').empty();


                            $.each(data, function (key,val) {
                                console.log(key)
                                if(key ==0) {
                                    $.each(val, function (index, dat) {
                                        $('#flavour_select').append('<option value="'+ dat.id +'">' + dat.name + ' - '+ dat.price + '</option>');
                                        // console.log('index flavours', dat.name)
                                    })
                                }
                                else
                                {
                                    $.each(val, function (index, dat) {
                                        $('#material_select').append('<option value="'+ dat.id +'">' + dat.name + ' - '+ dat.price + '</option>');
                                        // console.log('index flavours', dat.name)
                                    })
                                }
                            })


                        },

                        complete: function(){
                            $('#loader').css("visibility", "hidden");
                            $('#loader').css("visibility", "hidden");
                        }


                    });




                }
                else {
                    $('#flavour_select').empty();
                }

            });

        });

            $(function() {
                $("#flavour_select").on("change",function() {

                    calculateTotal()

                });
            });
            $(function() {
                $("#material_select").on("change",function() {

                    calculateTotal()

                });
            });
            $(function() {
                $("#quantity").on("input",function() {

                    calculateTotal()

                });
            });
            $(function() {
                $("#weight_product").on("input",function() {

                    calculateTotal()

                });
            });
            $(function() {
                $("#discount_price").on("input",function() {
                    var discount = parseInt($('#discount_price').val());
                    var totalPrice = parseInt($('#total_price').val());
                    var advancePrice = parseInt($('#advance_price').val());

                    var balance = totalPrice - advancePrice - discount;
                    $('#balance').attr('value',balance);

                });
            });
            $(function() {
                $("#advance_price").on("input",function(e) {

                    var totalPrice = parseInt($('#total_price').val());
                    var advancePrice = parseInt($('#advance_price').val());

                    var balance = totalPrice - advancePrice;

                    if(balance<=0)
                    {
                        $('#payment_status').attr('value',1);
                    }
                    else
                    {
                        $('#payment_status').attr('value',0);
                    }

                    $('#balance').attr('value',balance);
                    console.log("payment Status",parseInt($('#payment_status').val()));


                });
            });

        });







    </script>

@stop