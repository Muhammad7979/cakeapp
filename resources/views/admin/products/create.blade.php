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
                            <i class="fa fa-birthday-cake"></i>
                            Create Cake
                        </h3>
                    </div>

                    <div class="panel-body">

                        {{--<div class="row">--}}
                        {!! Form::open (['method'=>'POST','action'=>'AdminProductsController@store','files'=>true]) !!}
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
                                {!! Form::label('category_id','Product Category')!!}
                                {!! Form::select('category_id',array(' '=>'Select Cake Category',
                                'Child Categories'=>[]+$categories),null,['class'=>'form-control'])!!}
                            </div>


                            <div class="form-group">
                                {!! Form::label('weight','Weight')!!}
                                {!! Form::number('weight',null,['class'=>'form-control'])!!}
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
                                {!! Form::label('file','Image')!!}
                                {!! Form::file('photo_id',null,['class'=>'form-control form-control-file'])!!}
                            </div>


                        </div>
                        <div class="col-md-6 col-xs-12">

                            <h4>
                                <i class="fa fa-book"></i>
                                Flavours
                            </h4>
                            <div class="form-group">
                                {!! Form::label('flavour_id','Flavours')!!}
                                {!! Form::select('flavour_id[]', []+$flavours,null, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'flavour_select','tabindex'=>'-1','aria-hidden'=>'true')) !!}
                            </div>

                            <h4>
                                <i class="fa fa-book"></i>
                                Materials
                            </h4>
                            <div class="form-group">
                                {!! Form::label('material_id','Materials')!!}
                                {!! Form::select('material_id[]', []+$materials,null, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'material_select','tabindex'=>'-1','aria-hidden'=>'true')) !!}
                            </div>




                        </div>
                        <div class="col-md-offset-5 col-md-6 col-xs-12">
                            <div class="form-group">
                                {!! Form:: submit('Create Product',['class'=>'btn btn-primary'])!!}
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
@section('scripts')

    <script src="{{asset('js/select2.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#flavour_select').select2();
    });
    $(document).ready(function(){
        $('#material_select').select2();
    });
</script>

    @stop