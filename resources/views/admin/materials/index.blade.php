@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_material'))
        <p class="bg-danger">{{session('deleted_material')}}</p>

    @elseif(Session::has('updated_material'))
        <p class="bg-primary">{{session('updated_material')}}</p>
    @elseif(Session::has('created_material'))
        <p class="bg-success">{{session('created_material')}}</p>
    @endif
    <div class="row">
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
            <div class="loader"></div>
        <div class="col-md-5">
            {!! Form::open (['method'=>'POST','action'=>'AdminMaterialsController@search','id'=>'form_csv']) !!}
            <div class="form-group">
                <label class="d-inline">Material Search</label>
                <div class='input-group ' >

                    <input name="search_term" type='text' class="form-control"  placeholder="Search by Material  Id  or Name " id="search"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-search"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">

            <div class="form-group">
                <label class="d-inline">Material Status</label>
                <div class='input-group d-inline  ' style="display: inline" >
                    {!! Form::select('is_active',['-1'=> 'Select Material Status','1'=>'Active','0'=>'In-Active'],-1,['class'=>'form-control','id'=>'product_status'])!!}
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
                    <a href="{{route('materials.index')}}" class=" btn btn-default" style="margin-top: 25px" > Reset</a>
                </div>
            </div>
        </div>

    </div>
    <hr>
    <div class="row">

        @can('create-material')
        <ldiv class="col-md-4 main">


            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-book"></i>
                    <h3 class="panel-title bariol-thin">Create Materials</h3>

                </div>

                <div class="panel-body">
                    {!! Form::open (['method' => 'POST', 'action'=> 'AdminMaterialsController@store']) !!}

                    <div class="form-group">
                        {!! Form::label('name','Name')!!}
                        {!! Form::text('name',null,['class'=>'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('sku','Sku')!!}
                        {!! Form::text('sku',null,['class'=>'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('price','Price')!!}
                        {!! Form::number('price',null,['class'=>'form-control'])!!}
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

        </ldiv>
        @endcan

        @can('view-material')
        <div class="col-md-8">

            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title bariol-thin">Materials</h3>
                </div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">

                            <table class="table table-bordered table-inverse">
                              <thead class="thead-light">
                                <tr>
                                  <th scope="col">#</th>
                                    <th scope="col">Sku</th>
                                  <th scope="col">Name</th>
                                  <th scope="col">Price</th>
                                  <th scope="col">Status</th>
                                </tr>
                              </thead>

                              <tbody id="table_body">
                              @if($materials)
                                  @foreach($materials as $material)
                                      <tr>
                                          <th scope="row">{{$material->id}}</th>
                                          <td>{{$material->sku}}</td>
                                          @can('update-material')
                                              <td><a href="{{route('materials.edit',$material->id)}}">{{$material->name}}</a></td>
                                          @elsecannot('update-material')
                                              <td>{{$material->name}}</td>
                                          @endcan
                                          <td>{{$material->price}}</td>
                                          <td>
                                              {{--{{$user->is_active ==1 ? 'Active': 'Inactive'}}--}}

                                              {{--this is another way of displaying the material status--}}
                                              @if($material->is_active ==1)
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
                {{$materials->render()}}
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