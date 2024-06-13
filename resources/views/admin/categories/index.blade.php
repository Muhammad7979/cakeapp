@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_category'))
        <p class="bg-danger">{{session('deleted_category')}}</p>

    @elseif(Session::has('updated_category'))
        <p class="bg-primary">{{session('updated_category')}}</p>
    @elseif(Session::has('created_category'))
        <p class="bg-success">{{session('created_category')}}</p>
    @endif
    <div class="loader"></div>
    <div class="row">


        <div class="col-md-5">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
                {!! Form::open (['method'=>'POST','action'=>'AdminCategoriesController@search','id'=>'form_csv']) !!}
            <div class="form-group">
                <label class="d-inline">Product Category Search</label>
                <div class='input-group ' >

                    <input name="search_term" type='text' class="form-control"  placeholder="Search by Category Id, Name , Parent Category" id="search"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">

            <div class="form-group">
                <label class="d-inline">Product Category Status</label>
                <div class='input-group d-inline  ' style="display: inline" >
                    {!! Form::select('is_active',['-1'=> 'Select Product Category Status','1'=>'Active','0'=>'In-Active'],-1,['class'=>'form-control','id'=>'product_status'])!!}
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
                    <a href="{{route('categories.index')}}" class=" btn btn-default" style="margin-top: 25px" > Reset</a>
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
                            <i class="fa fa-list-alt "></i>
                            All Product Categories
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">

                                <table class="table">
                                    <thead class=".thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Parent Category</th>
                                        <th scope="col">Active</th>
                                        <th scope="col">Image</th>


                                        {{--<th scope="col">Handle</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody id="table_body">

                                    @if($categories)

                                        @foreach($categories as $category)
                                            <tr>
                                                <th scope="row">{{$category->id}}</th>

                                                @can('update-category')
                                                    <td><a href="{{route('categories.edit',$category->id)}}">{{$category->name}}</a></td>

                                                @elsecannot('update-category')
                                                    <td>{{$category->name}}</td>
                                                @endcan
                                                <td>{{$category->description}}</td>
                                                <td>@if($category->parent_id==0)

                                                        {{'No Parent Category'}}

                                                    @else
                                                    {{$category->parent->name}}

                                                        @endif
                                                </td>
                                                <td>
                                                    {{--{{$user->is_active ==1 ? 'Active': 'Inactive'}}--}}

                                                    {{--this is another way of displaying the user status--}}
                                                    @if($category->is_active ==1)
                                                        <i class="fa fa-circle " style="color: green"></i>
                                                    @else
                                                        <i class="fa fa-circle red" style="color: red"></i>

                                                    @endif



                                                </td>
                                                {{--diffForHumans is used to get time as 20 hours ago etc--}}
                                                <td><img height="60" src="{{$category->photo ?URL::asset( 'images/Product_Categories/'.$category->photo_path ): '/images/Event.png' }}" alt="" > </td>



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
            {{$categories->render()}}
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