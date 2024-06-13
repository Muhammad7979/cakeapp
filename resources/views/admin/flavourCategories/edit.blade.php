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
                            <i class="fa fa-list-alt"></i>
                            Edit Flavour Category
                        </h3>
                    </div>

                    <div class="panel-body">
                        <div class="col-sm-6">

                            {!! Form::model ($flavourCategory,['method' => 'PATCH', 'action'=> ['AdminFlavourCategoryController@update',$flavourCategory->id]]) !!}

                            <div class="form-group">
                                {!! Form::label('name','Title:')!!}
                                {!! Form::text('name',null,['class'=>'form-control'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('description','Description')!!}
                                {!! Form::textarea('description',$flavourCategory->description,['class'=>'form-control', 'rows'=>3])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('is_active','Status')!!}
                                {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),$flavourCategory->is_active,['class'=>'form-control'])!!}
                            </div>


                            <div class="form-group col-md-2">

                                <a class=" btn btn-default" href="{{route('flavourCategory.index')}}" > Cancel</a>
                            </div>


                            <div class="form-group col-md-4" style="margin-left: 50px">
                                {!! Form:: submit('Update Material',['class'=>'btn btn-primary'])!!}
                            </div>
                            {!! Form::close() !!}


                            @can('delete-flavourCategory')
                                {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminFlavourCategoryController@destroy',$flavourCategory->id], 'class'=>'pull-right']) !!}


                                <div class="form-group">
                                    {!! Form:: submit('Delete Flavour Category',['class'=>'btn btn-danger col-sm-12'])!!}
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