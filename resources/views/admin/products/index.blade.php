@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_product'))
        <p class="bg-danger">{{session('deleted_product')}}</p>

    @elseif(Session::has('updated_product'))
        <p class="bg-primary">{{session('updated_product')}}</p>
    @elseif(Session::has('created_product'))
        <p class="bg-success">{{session('created_product')}}</p>
    @endif
    <div class="row">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

            <div class="loader"></div>
            <form action="/products/search" method="POST" role="search">
                {{ csrf_field() }}
        <div class="col-md-4">

            <div class="form-group">
                <label class="d-inline">Product Search</label>
                <div class='input-group ' >

                    <input name="search_term" type='text' class="form-control"  placeholder="Search by Product Id, Name , Category" id="search"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">

            <div class="form-group">
                <label class="d-inline">Product Status</label>
                <div class='input-group d-inline  ' style="display: inline" >
                    {!! Form::select('is_active',['-1'=> 'Select Product Status','1'=>'Active','0'=>'In-Active'],-1,['class'=>'form-control','id'=>'product_status'])!!}
                </div>

            </div>
        </div>

        <div class="col-md-2">

            <div class="form-group">


                    <button type="submit" class="btn btn-primary" id="filter_button" style="width: 70%; margin-top:25px "> Filter</button>

            </div>
        </div>
          </form>

        <div class="col-md-1">

            <div class="form-group">

                <div class='input-group ' >
                    <a href="{{route('products.index')}}" class=" btn btn-default" style="margin-top: 25px" > Reset</a>
                </div>
            </div>
        </div>

        @if(\Illuminate\Support\Facades\Session::get('is_server')==0)
        <div class="col-md-1">

            <div class="form-group">

                <div class='input-group ' >
                    <button class="btn btn-default" id="sync_button" style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i> Sync Products</button>
                </div>
            </div>
        </div>
@endif

    </div>
    <hr>
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-birthday-cake"></i>
                            Cakes
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-6 col-sm-offset-5">
                                        {{$products->render()}}
                                    </div>
                                </div>

                                <table class="table table-bordered inverse-table">
                                    <thead class=".thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Sku</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Weight</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Active</th>
                                        <th scope="col">Image</th>


                                        {{--<th scope="col">Handle</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody id="table_body">

                                    @if($products)

                                        @foreach($products as $product)
                                            <tr>
                                                <th scope="row">{{$product->id}}</th>
                                                <td>{{$product->sku}}</td>
                                                <td>{{$product->category ?$product->category->name: "Product Category Not Available"}}</td>

                                                @can('update-product')
                                                    <td><a href="{{route('products.edit',$product->id)}}">{{$product->name}}</a></td>

                                                @elsecannot('update-product')
                                                    <td>{{$product->name}}</td>
                                                @endcan
                                                <td>{{$product->weight}}</td>
                                                <td>{{$product->price}}</td>
                                                <td>
                                                    {{--{{$user->is_active ==1 ? 'Active': 'Inactive'}}--}}

                                                    {{--this is another way of displaying the user status--}}
                                                    @if($product->is_active ==1)
                                                        <i class="fa fa-circle " style="color: green"></i>
                                                    @else
                                                        <i class="fa fa-circle red" style="color: red"></i>

                                                    @endif



                                                </td>
                                                {{--diffForHumans is used to get time as 20 hours ago etc--}}

                                                <td><img height="60" src="{{$product->photo ? URL::asset('/images/Product_Images/'.$product->photo->path ): '/images/Placeholder.png' }}" alt="" > </td>

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
    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$products->render()}}
        </div>
    </div>
@stop
@section('scripts')
    <script src="{{asset('js/jquery-ui.js')}}"></script>
    <script src="{{asset('js/moment.js')}}"></script>


    <script>

        $(window).load(function(){
            // PAGE IS FULLY LOADED
            // FADE OUT YOUR OVERLAYING DIV
            $(".loader").css("display", "block");
            $('.loader').fadeOut('slow');
        });

        function syncProducts()
        {
            $(".loader").css("display", "block");

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: "/products/liveSync", // path to function
                dataType: 'json',
                cache: false,

                success: function (val) {
                    alert(''+val['status']);
                    {{--}--}}
                    location.reload();
                    $(".loader").fadeOut("slow");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }


        $(document).ready(function(){

                $('#sync_button').click(function () {

                    syncProducts();


                });


            $('#search').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('#filter_button').click();//Trigger search button click event
                }
            });

        });



    </script>
@stop
