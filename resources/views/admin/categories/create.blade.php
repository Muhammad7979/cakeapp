@extends('layouts.admin')

@section('content')

    <div class="row">

        @can('create-category')
            <div class="col-md-12 main">
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif


                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-list-alt"></i>
                        <h3 class="panel-title bariol-thin">Create Product Category</h3>

                    </div>

                    <div class="panel-body">
                         <div class="col-md-6">

                             {!! Form::open (['method'=>'POST','action'=>'AdminCategoriesController@store','files'=>true]) !!}

                                    <div class="form-group">
                                        {!! Form::label('name','Name')!!}
                                        {!! Form::text('name',null,['class'=>'form-control'])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('description','Description')!!}
                                        {!! Form::textarea('description',null,['class'=>'form-control', 'rows'=>3])!!}
                                    </div>
                                     <div class="form-group">
                                         {!! Form::label('parent_id','Parent Category')!!}
                                         {!! Form::select('parent_id',array(' '=>'Select Category',
                                         'Parent Categories'=>array('0'=> 'None')+$parentCategories,
                                         'Child Categories'=>[]+$childCategories),null,['class'=>'form-control'])!!}

                                     </div>

                                    <div class="form-group">
                                        {!! Form::label('is_active','Status')!!}
                                        {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),0,['class'=>'form-control'])!!}
                                    </div>

                                     <div class="form-group">
                                         {!! Form::label('file','Image')!!}
                                         {!! Form::file('photo_id',null,['class'=>'form-control form-control-file'])!!}
                                     </div>
                                <div class="col-md-offset-9">
                                    <div class="form-group">
                                        {!! Form:: submit('Create Category',['class'=>'btn btn-info'])!!}
                                    </div>

                                    {!! Form::close() !!}
                                </div>
                         </div>
                    </div>
                </div>

            </div>
        @endcan

    </div>
    @include('includes.errorReporting')
@stop