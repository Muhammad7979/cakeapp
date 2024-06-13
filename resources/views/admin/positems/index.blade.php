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
            <form action="/positems/search" method="POST" role="search">
                {{ csrf_field() }}
        <div class="col-md-4">

            <div class="form-group">
                <label class="d-inline">Item Search</label>
                <div class='input-group ' >

                    <input name="search_term" type='text' class="form-control"  placeholder="Search by item Id, Name , Category" id="search"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-2">

            <div class="form-group">


                    <button type="submit" class="btn btn-primary" id="filter_button" style="width: 70%; margin-top:25px "> Filter</button>

            </div>
        </div>
        <div class="col-md-2">

            <div class="form-group">


                    <a href="{{route('pos.items')}}"  class="btn btn-primary" id="refresh_button" style="width: 70%; margin-top:25px "> Refresh</a>

            </div>
        </div>
            </form>

        <div class="col-md-1">

            <div class="form-group">

                {{-- <div class='input-group ' >
                    <a href="{{route('items.index')}}" class=" btn btn-default" style="margin-top: 25px" > Reset</a>
                </div> --}}
            </div>
        </div>

        @if(\Illuminate\Support\Facades\Session::get('is_server')==0)
        <div class="col-md-1">

            <div class="form-group">
                <div class='input-group ' >
                   <button class="btn btn-default" id="sync_button" style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i> Sync items</button>
                   <!-- <button class="btn btn-default" id="sale_upload_button" style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i> Sale upload</button> -->
                   <!-- <button class="btn btn-default" id="sync_item_kits" style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i> Sync item kits</button> -->
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
                                        {{$items->render()}}
                                    </div>
                                </div>

                                <table class="table table-bordered inverse-table">
                                    <thead class=".thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Item_No</th>
                                        <th scope="col">Retail Price</th>
                                       


                                        {{--<th scope="col">Handle</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody id="table_body">

                                    @if($items)

                                        @foreach($items as $item)
                                            <tr>
                                                <th scope="row">{{$item->item_id}}</th>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->category}}</td>
                                                <td>{{$item->item_number}}</td>
                                                <td>{{$item->unit_price}}</td>
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
            {{$items->render()}}
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
                url: "/positems/liveSync", // path to function
                dataType: 'json',
                cache: false,

                success: function (val) {
                    alert(''+val['message']);
                    location.reload();
                    $(".loader").fadeOut("slow");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }


        function saleUpload()
        {
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

                success: function (val) {
                    alert(''+val['message']);
                    location.reload();
                    $(".loader").fadeOut("slow");
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }


        function syncItemKits()
        {
            $(".loader").css("display", "block");

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: "/positems/syncItemKits", // path to function
                dataType: 'json',
                cache: false,

                success: function (val) {
                    alert(''+val['message']);
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

                 $('#sale_upload_button').click(function () {

                   saleUpload();

                 });

                 $('#sync_item_kits').click(function () {

                  syncItemKits();

                   });


            $('#search').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('#filter_button').click();//Trigger search button click event
                }
            });

        });



    </script> 
@stop
