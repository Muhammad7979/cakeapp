@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_flavour'))
        <p class="bg-danger">{{session('deleted_flavour')}}</p>

    @elseif(Session::has('updated_flavour'))
        <p class="bg-primary">{{session('updated_flavour')}}</p>
    @elseif(Session::has('created_flavour'))
        <p class="bg-success">{{session('created_flavour')}}</p>
    @endif
    <div class="loader"></div>
    <div class="row">


        <div class="col-md-5">
            {!! Form::open (['method'=>'POST','action'=>'AdminFlavoursController@search','id'=>'form_csv']) !!}
            <div class="form-group">
                <label class="d-inline">Flavour Search</label>
                <div class='input-group ' >

                    <input name="search_term" type='text' class="form-control"  placeholder="Search by Flavour Id, Name , Category" id="search"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">

            <div class="form-group">
                <label class="d-inline">Flavour Status</label>
                <div class='input-group d-inline  ' style="display: inline" >
                    {!! Form::select('is_active',['-1'=> 'Select Flavour Status','1'=>'Active','0'=>'In-Active'],-1,['class'=>'form-control','id'=>'product_status'])!!}
                </div>

            </div>
        </div>

        <div class="col-md-2">

            <div class="form-group">


                <button class="btn btn-primary" id="filter_button" style="width: 70%; margin-top:25px "> Filter</button>

            </div>
            {!! Form::close() !!}
        </div>

        <div class="col-md-1">

            <div class="form-group">

                <div class='input-group ' >
                    <a href="{{route('flavours.index')}}" class=" btn btn-default" style="margin-top: 25px" > Reset</a>
                </div>
            </div>
        </div>

    </div>
    <hr>
    <div class="row">

        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$flavours->render()}}
                </div>
            </div>

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-book"></i>
                            FLAVOURS
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">

                                <table class="table table-bordered table-inverse">
                                    <thead class=".thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Sku</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Active</th>

                                    </tr>
                                    </thead>
                                    <tbody id="table_body">

                                    @if($flavours)

                                        @foreach($flavours as $flavour)
                                            <tr>
                                                <th scope="row">{{$flavour->id}}</th>
                                                <td>{{$flavour->sku}}</td>

                                                @can('update-flavour')
                                                    <td><a href="{{route('flavours.edit',$flavour->id)}}">{{$flavour->name}}</a></td>

                                                @elsecannot('update-user')
                                                    <td>{{$flavour->name}}</td>
                                                @endcan
                                                <td>{{$flavour->price}}</td>
                                                <td>{{ $flavour->flavourCategory ? $flavour->flavourCategory->name : "Category Name Not Available"}}</td>
                                                <td>

                                                    @if($flavour->is_active ==1)
                                                        <i class="fa fa-circle " style="color: green"></i>
                                                    @else
                                                        <i class="fa fa-circle red" style="color: red"></i>

                                                    @endif

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
    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$flavours->render()}}
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


        $(document).ready(function(){
            $('#search').keypress(function(e){
                if(e.which == 13){//Enter key pressed
                    $('#filter_button').click();//Trigger search button click event
                }
            });


        });



    </script>
@stop