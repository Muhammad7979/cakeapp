@extends('layouts.admin')

@section('content')
    @if (Session::has('deleted_product'))
        <p class="bg-danger">{{ session('deleted_product') }}</p>
    @elseif(Session::has('updated_product'))
        <p class="bg-primary">{{ session('updated_product') }}</p>
    @elseif(Session::has('created_product'))
        <p class="bg-success">{{ session('created_product') }}</p>
    @endif
    <div class="row">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="loader"></div>
        <form action="/positems/search" method="POST" role="search">
            {{ csrf_field() }}
            {{-- <div class="col-md-4">

                <div class="form-group">
                    <label class="d-inline">Item Search</label>
                    <div class='input-group '>

                        <input name="search_term" type='text' class="form-control"
                            placeholder="Search by item Id, Name , Category" id="search" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-search"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-2">

                <div class="form-group">


                    <button type="submit" class="btn btn-primary" id="filter_button" style="width: 70%; margin-top:25px ">
                        Filter</button>

                </div>
            </div> --}}
            <div class="col-md-2">

                <div class="form-group">


                    <a href="{{ route('itemkits') }}" class="btn btn-primary" id="refresh_button"
                        style="width: 70%; margin-top:25px "> Refresh</a>

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

        @if (\Illuminate\Support\Facades\Session::get('is_server') == 0)
            <div class="col-md-1 ">

                <div class="form-group ">
                    <div class='input-group '>
                        {{-- <button class="btn btn-default" id="sync_button"
                            style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i>
                            Sync items</button>
                        <button class="btn btn-default" id="sale_upload_button"
                            style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i>
                            Sale upload</button> --}}
                        <button class="btn btn-default" id="sync_item_kits"
                            style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i>
                            Sync item kits</button>
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


                                <table class="table table-bordered inverse-table">
                                    <thead class=".thead-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Kit Name</th>
                                            <th scope="col">Branch Code</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Items</th>
                                            {{-- <th scope="col">Cost Price</th> --}}



                                            {{-- <th scope="col">Handle</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">

                                        @if ($itemkits)

                                            @foreach ($itemkits as $itemkit)
                                                <tr>
                                                    <th scope="row">{{ $loop->index + 1 }}</th>
                                                    <td>{{ $itemkit->name }}</td>
                                                    <td>{{ $itemkit->branch_code }}</td>
                                                    <td>{{ $itemkit->description }}</td>
                                                    <td>
                                                         <!-- <img src="{{ asset('storage/'. $itemkit->image) }}" alt="Uploaded Image"> -->
                                                         <img src="{{ URL::asset('images/item_kits/'.$itemkit->image)}}" width="50" alt="">
                                                        </td>
                                                    {{-- <td> 
                                                        
                                                        <a id="{{ $itemkit->item_kit_id }}" class="link table-row"> <i
                                                                class="fa fa-eye fa-2x"></i> </a>
                                                            
                                                            
                                                    </td> --}}
                                                    <td>
                                                        <a id="{{ $itemkit->item_kit_id }}" class="link table-row"
                                                            data-toggle="modal" data-target="#myModal">
                                                            <i class="fa fa-eye fa-2x"></i>
                                                        </a>
                                                    </td>
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
            {{ $itemkits->render() }}
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <h4 class="modal-title">Items</h4>
                </div>
                <div class="modal-body">
                    <!-- Table to be populated dynamically -->
                    <table class="table table-striped" id="item_table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <!-- Add more table headers if needed -->
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <!-- Table body content will be added dynamically -->
                        </tbody>
                    </table>

                    <form class="" id="form" action="{{ route('image.upload') }}" 
                          method="POST" enctype="multipart/form-data"
                          style="margin-bottom: 20px; display:flex; margin-top:20px">
                       @csrf
                     <input type="file" name="image">
                     <button type="submit">Upload new image</button>
                     </form>
                     
                </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script src="{{ asset('js/jquery-ui.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>



    <script>
        $(window).load(function() {
            // PAGE IS FULLY LOADED
            // FADE OUT YOUR OVERLAYING DIV
            $(".loader").css("display", "block");
            $('.loader').fadeOut('slow');
        });

        // $(document).on('click', '.link', function() {

        //     var kit_id = $(this).attr('id');


        //     $.ajaxSetup({
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         }
        //     });
        //     $.ajax({
        //         type: "post",
        //         url: "itemkits/get", // path to function
        //         dataType: 'json',
        //         cache: false,
        //         data: {
        //             kit_id: kit_id
        //         },
        //         success: function(val) {
        //             var trow = '<tr>';

        //             $.each(val, function(index, item) {
        //                 trow += '<td>' + item.name + '</td></tr>'
        //             })
        //             $('#item_table').append(trow)

        //             trow = "";
        //         }
        //     });


        // });
        $(document).on('click', '.link', function() {
            var kit_id = $(this).attr('id');
             // Define trow variable outside the loop

            console.log(kit_id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "post",
                url: "itemkits/get", // path to function
                dataType: 'json',
                cache: false,
                data: {
                    kit_id: kit_id
                },
                success: function(val) {
                    $('#modalTableBody').empty();
                    var trow = '';
                    $.each(val, function(index, item) {
                        trow += '<tr>' +
                            '<td>' +
                            item.name +
                            '</td>' +
                            '<td>' +
                            item.cost_price +
                            '</td>'

                            +
                            '</tr>'; // Append each column to the table row
                    });
                    $('#modalTableBody').append(trow); // Set the HTML content of the table body
                    $('#form').append('<input type="hidden" name="kit_id" value="'+kit_id+'">'); // Set the HTML content of the table body
                }
            });
        });

        function syncProducts() {
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


        function syncItemKits() {
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

            $('#sync_button').click(function() {

                syncProducts();

            });

            $('#sale_upload_button').click(function() {

                saleUpload();

            });

            $('#sync_item_kits').click(function() {

                syncItemKits();

            });


            $('#search').keypress(function(e) {
                if (e.which == 13) { //Enter key pressed
                    $('#filter_button').click(); //Trigger search button click event
                }
            });

        });
    </script>
@stop
