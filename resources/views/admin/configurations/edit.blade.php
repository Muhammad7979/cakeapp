@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-list-alt"></i>
                            Edit Configuration Variable
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="col-sm-6">

                            {!! Form::model ($systemVariable,['method' => 'PATCH', 'action'=> ['SystemConfigurationsController@update',$systemVariable->id]]) !!}

                            <div class="form-group">
                                {!! Form::label('key','Key')!!}
                                {!! Form::text('key',null,['class'=>'form-control'])!!}

                            </div>
                            <div class="form-group">
                                {!! Form::label('value','Value')!!}
                                {!! Form::text('value',null,['class'=>'form-control'])!!}

                            </div>
                            <div class="form-group">
                                {!! Form::label('label','Label')!!}
                                {!! Form::text('label',null,['class'=>'form-control'])!!}

                            </div>


                            <div class="form-group">
                                {!! Form:: submit('Update Variable',['class'=>'btn btn-primary col-sm-4'])!!}
                            </div>

                            {!! Form::close() !!}


                            @can('delete-configuration')
                                {!! Form::open (['method' => 'DELETE', 'action'=> ['SystemConfigurationsController@destroy',$systemVariable->id], 'class'=>'pull-right']) !!}


                                <div class="form-group">
                                    {!! Form:: submit('Delete Variable',['class'=>'btn btn-danger col-sm-12'])!!}
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