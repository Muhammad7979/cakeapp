@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_fcategory'))
        <p class="bg-danger">{{session('deleted_fcategory')}}</p>

    @elseif(Session::has('updated_fcategory'))
        <p class="bg-primary">{{session('updated_fcategory')}}</p>
    @elseif(Session::has('created_fcategory'))
        <p class="bg-success">{{session('created_fcategory')}}</p>
    @endif
    <div class="loader"></div>
    <div class="row">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="col-md-5">
            {!! Form::open (['method'=>'POST','action'=>'AdminFlavourCategoryController@search','id'=>'form_csv']) !!}
            <div class="form-group">
                <label class="d-inline">Flavour Category Search</label>
                <div class='input-group ' >

                    <input name="search_term" type='text' class="form-control"  placeholder="Search by Cateogry  Id  or Name " id="search"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">

            <div class="form-group">
                <label class="d-inline">Category Status</label>
                <div class='input-group d-inline  ' style="display: inline" >
                    {!! Form::select('is_active',['-1'=> 'Select Category Status','1'=>'Active','0'=>'In-Active'],-1,['class'=>'form-control','id'=>'product_status'])!!}
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
                    <a href="{{route('flavourCategory.index')}}" class=" btn btn-default" style="margin-top: 25px" > Reset</a>
                </div>
            </div>
        </div>

    </div>
    <hr>
    <div class="row">

        @can('create-flavourCategory')
            <div class="col-md-4 main">


                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-list-alt"></i>
                        <h3 class="panel-title bariol-thin">Create Flavour Category</h3>

                    </div>

                    <div class="panel-body">
                        {!! Form::open (['method' => 'POST', 'action'=> 'AdminFlavourCategoryController@store']) !!}

                        <div class="form-group">
                            {!! Form::label('name','Name')!!}
                            {!! Form::text('name',null,['class'=>'form-control'])!!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('description','Description')!!}
                            {!! Form::textarea('description',null,['class'=>'form-control', 'rows'=>3])!!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('is_active','Status')!!}
                            {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),0,['class'=>'form-control'])!!}
                        </div>

                        <div class="form-group">
                            {!! Form:: submit('Create Material',['class'=>'btn btn-info'])!!}
                        </div>

                        {!! Form::close() !!}

                    </div>

                </div>

            </div>
        @endcan

        @can('view-flavourCategory')
            <div class="col-md-8">

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-list-alt"></i>
                        <h3 class="panel-title bariol-thin">Flavour Category</h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-12">

                                <table class="table table-bordered table-inverse">
                                    <thead class="thead-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                    </thead>

                                    <tbody id="table_body">
                                    @if($flavourCategories)
                                        @foreach($flavourCategories as $flavourCategory)
                                            <tr>
                                                <th scope="row">{{$flavourCategory->id}}</th>
                                                @can('update-flavourCategory')
                                                    <td><a href="{{route('flavourCategory.edit',$flavourCategory->id)}}">{{$flavourCategory->name}}</a></td>
                                                @elsecannot('update-flavourCategory')
                                                    <td>{{$flavourCategory->name}}</td>
                                                @endcan
                                                <td>{{$flavourCategory->description}}</td>
                                                <td>
                                                    {{--{{$user->is_active ==1 ? 'Active': 'Inactive'}}--}}

                                                    {{--this is another way of displaying the material status--}}
                                                    @if($flavourCategory->is_active ==1)
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
            <div class="row">
                <div class="col-sm-6 col-sm-offset-5">
                    {{$flavourCategories->render()}}
                </div>
            </div>
        @endcan



    </div>
    @include('includes.errorReporting')
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