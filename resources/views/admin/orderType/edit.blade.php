@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-list-alt"></i>
                            Edit Order Type
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="col-sm-6">

                            {!! Form::model ($orderType,['method' => 'PATCH', 'action'=> ['AdminOrderTypeController@update',$orderType->id]]) !!}

                            <div class="form-group">
                                {!! Form::label('name','Type')!!}
                                {!! Form::text('name',null,['class'=>'form-control'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form:: submit('Update Type',['class'=>'btn btn-primary col-sm-4'])!!}
                            </div>

                            {!! Form::close() !!}


                            @can('delete-group')
                                {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminOrderTypeController@destroy',$orderType->id], 'class'=>'pull-right']) !!}


                                <div class="form-group">
                                    {!! Form:: submit('Delete Type',['class'=>'btn btn-danger col-sm-12'])!!}
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