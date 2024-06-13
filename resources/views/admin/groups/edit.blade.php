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
                            <i class="fa fa-user"></i>
                            Edit Group
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="col-sm-6">

                            {!! Form::model ($group,['method' => 'PATCH', 'action'=> ['AdminGroupsController@update',$group->id]]) !!}

                            <div class="form-group">
                                {!! Form::label('name','Title:')!!}
                                {!! Form::text('name',null,['class'=>'form-control'])!!}
                            </div>

                              <div class="form-group col-md-2">
                                <a class=" btn btn-default" href="{{route('groups.index')}}" > Cancel</a>
                              </div>





                            <div class="form-group col-md-4" style="margin-left: 130px">
                                {!! Form:: submit('Update Group',['class'=>'btn btn-primary '])!!}
                            </div>

                            {!! Form::close() !!}


                            @can('delete-group')
                            {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminGroupsController@destroy',$group->id], 'class'=>'pull-right']) !!}


                            <div class="form-group">
                                {!! Form:: submit('Delete Type',['class'=>'btn btn-danger'])!!}
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