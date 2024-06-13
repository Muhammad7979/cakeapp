@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-book"></i>
                            Edit Material
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="col-sm-6">

                            {!! Form::model ($material,['method' => 'PATCH', 'action'=> ['AdminMaterialsController@update',$material->id]]) !!}

                            <div class="form-group">
                                {!! Form::label('name','Title')!!}
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
                                {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),$material->is_active,['class'=>'form-control'])!!}
                            </div>

                            <div class="form-group col-md-4">

                                <a class=" btn btn-default" href="{{route('materials.index')}}" > Cancel</a>
                            </div>


                            <div class="form-group col-md-4">
                                {!! Form:: submit('Update Material',['class'=>'btn btn-primary'])!!}
                            </div>


                            {!! Form::close() !!}


                            @can('delete-group')
                                {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminMaterialsController@destroy',$material->id], 'class'=>'pull-right']) !!}


                                <div class="form-group">
                                    {!! Form:: submit('Delete Material',['class'=>'btn btn-danger col-sm-12'])!!}
                                </div>

                                {!! Form::close() !!}
                            @endcan

                        </div>
                    </div>




                </div>



            </div>



        </div>



    </div>



    </div>
    @include('includes.errorReporting')

@stop