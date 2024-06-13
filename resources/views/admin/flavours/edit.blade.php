@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">



            <div class="panel panel-info">

                <div class="panel-heading">


                    <h3 class="panel-title bariol-thin">
                        <i class="fa fa-book"></i>
                        Edit Flavour
                    </h3>
                </div>

                <div class="panel-body">

                    <div class="col-md-9">
                        {{--<div class="row">--}}
                        {!! Form::model ($flavour,['method' => 'PATCH', 'action'=> ['AdminFlavoursController@update',$flavour->id]]) !!}
                        <div class="col-md-7 col-xs-12">

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
                            {!! Form::label('is_active','Status')!!}
                            {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),$flavour->is_active,['class'=>'form-control'])!!}
                        </div>


                    </div>
                    <div class="col-md-5 col-xs-12">

                        <h4>
                            <i class="fa fa-list-alt"></i>
                            Flavour Category
                        </h4>

                        <div class="form-group">
                            {!! Form::label('flavourCategory_id','Category')!!}
                            {!! Form::select('flavourCategory_id',[' '=>'Select Category']+$flavourCategories,$flavour->flavourCategory_id?? null,['class'=>'form-control'])!!}
                        </div>



                    </div>


                    {{--</div>--}}

                        <div class="col-md-offset-4 col-md-8 col-xs-12">
                            <a class=" btn btn-default" href="{{route('flavours.index')}}" style="margin-right: 46px;margin-left: 90px;"> Cancel</a>

                            @can('update-flavour')
                                {!! Form:: submit('Update Flavour',['class'=>'btn btn-primary'])!!}
                                {!! Form::close() !!}
                            @endcan

                            @can('delete-flavour')
                                {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminFlavoursController@destroy',$flavour->id], 'class'=>'pull-right']) !!}

                                <div class="form-group">
                                    {!! Form:: submit('Delete Flavour',['class'=>'btn btn-danger col-sm-12'])!!}
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