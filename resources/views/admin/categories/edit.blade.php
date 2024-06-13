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
                        <i class="fa fa-list-alt"></i>
                        Edit Product Category
                    </h3>
                </div>

                <div class="panel-body">
                    <div class="col-md-2">
                        <img height="60" src="{{$category->photo ? URL::asset('images/Product_Categories/'.$category->photo->path) : '/images/Event.png' }}" alt=""  class="img-rounded img-responsive">
                    </div>

                    <div class="col-md-9">
                        {{--<div class="row">--}}
                        {!! Form::model ($category,['method' => 'PATCH', 'action'=> ['AdminCategoriesController@update',$category->id],'files'=>true]) !!}
                        <div class="col-md-6 col-xs-12">

                            <div class="form-group">
                                {!! Form::label('name','Name:')!!}
                                {!! Form::text('name',null,['class'=>'form-control'])!!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('description','Description')!!}
                                {!! Form::textarea('description',null,['class'=>'form-control', 'rows'=>3])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('parent_id','Parent Category')!!}
                                {!! Form::select('parent_id',array('0'=>'Select Category',
                                'Parent Categories'=>array('0'=> 'None')+$parentCategories,
                                'Child Categories'=>[]+$childCategories),$category->parent_id,['class'=>'form-control'])!!}

                            </div>

                            <div class="form-group">
                                {!! Form::label('is_active','Status:')!!}
                                {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),$category->is_active,['class'=>'form-control'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form::label('photo_id','Image:')!!}
                                {!! Form::file('photo_id',null,['class'=>'form-control form-control-file'])!!}
                            </div>


                        </div>

                        <div class="col-md-offset-4 col-md-8 col-xs-12">
                            <a class=" btn btn-default" href="{{route('categories.index')}}" style="margin-right: 58px;margin-left: 53px;"> Cancel</a>

                            @can('update-user')
                                {!! Form:: submit('Update Category',['class'=>'btn btn-primary'])!!}
                                {!! Form::close() !!}
                            @endcan

                            @can('delete-user')
                                {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminCategoriesController@destroy',$category->id], 'class'=>'pull-right']) !!}

                                <div class="form-group">
                                    {!! Form:: submit('Delete Category',['class'=>'btn btn-danger col-sm-12'])!!}
                                </div>

                                {!! Form::close() !!}
                            @endcan
                        </div>





                    </div>
                    {{--</div>--}}
                </div>



            </div>







        </div>



    </div>
    @include('includes.errorReporting')

@stop