@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-book"></i>
                            Create Flavour
                        </h3>
                    </div>

                    <div class="panel-body">

                        {{--<div class="row">--}}
                        {!! Form::open (['method'=>'POST','action'=>'AdminFlavoursController@store']) !!}
                        <div class="col-md-6 col-xs-12">

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
                                {!! Form::label('is_active','Status:')!!}
                                {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),0,['class'=>'form-control'])!!}
                            </div>


                            </div>
                        <div class="col-md-6 col-xs-12">

                            <h4>
                                <i class="fa fa-book"></i>
                                Flavour Category
                            </h4>

                            <div class="form-group">
                                {!! Form::label('flavourCategory_id','Category')!!}
                                {!! Form::select('flavourCategory_id',[' '=>'Select Category']+$flavourCategories,null,['class'=>'form-control'])!!}
                            </div>



                        </div>
                            <div class="col-md-offset-5 col-md-6 col-xs-12">
                                <div class="form-group">
                                    {!! Form:: submit('Create Flavour',['class'=>'btn btn-primary'])!!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                            {{--</div>--}}


                        </div>




                </div>



            </div>



        </div>



    </div>
    @include('includes.errorReporting')

@stop