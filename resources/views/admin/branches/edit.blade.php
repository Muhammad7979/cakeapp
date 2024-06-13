@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="panel panel-info">

                <div class="panel-heading">


                    <h3 class="panel-title bariol-thin">
                        <i class="fa fa-building-o"></i>
                        Edit Branch
                    </h3>
                </div>

                <div class="panel-body">

                    {!! Form::model ($branch,['method' => 'PATCH', 'action'=> ['AdminBranchesController@update',$branch->id]]) !!}
                    <div class="col-md-6 col-xs-12">

                        <div class="form-group">
                            {!! Form::label('name','Title')!!}
                            {!! Form::text('name',null,['class'=>'form-control'])!!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('code','Branch Code')!!}
                            {!! Form::text('code',null,['class'=>'form-control'])!!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('is_active','Status')!!}
                            {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),$branch->is_active,['class'=>'form-control'])!!}
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            {!! Form::label('address','Address')!!}
                            {!! Form::text('address',null,['class'=>'form-control'])!!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('phone','Contact Number')!!}
                            {!! Form::text('phone',null,['class'=>'form-control'])!!}
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-offset-4 col-md-8 col-xs-12">
                            <a class=" btn btn-default" href="{{route('branches.index')}}" style="margin-right: 60px;margin-left: 285px;"> Cancel</a>

                            {!! Form:: submit('Update Branch',['class'=>'btn btn-primary'])!!}


                    {!! Form::close() !!}
                            @can('delete-branch')

                            {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminBranchesController@destroy',$branch->id], 'class'=>'pull-right']) !!}

                            {!! Form:: submit('Delete Branch',['class'=>'btn btn-danger col-sm-12'])!!}

                            {!! Form::close() !!}

                            @endcan
                        </div>


                    </div>






                </div>





            </div>



        </div>


    </div>
    @include('includes.errorReporting')


@stop