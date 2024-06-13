@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-user"></i>
                            Create User
                        </h3>
                    </div>

                    <div class="panel-body">

                        {{--<div class="row">--}}
                        {!! Form::open (['method'=>'POST','action'=>'AdminUsersController@store','files'=>true]) !!}
                            <div class="col-md-6 col-xs-12">

                                <div class="form-group">
                                    {!! Form::label('name','Name')!!}
                                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('email','Email')!!}
                                    {!! Form::email('email',null,['class'=>'form-control'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('is_active','Status')!!}
                                    {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),0,['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('is_admin','Admin')!!}
                                    {!! Form::select('is_admin',array(1=>'Yes', 0=>'No'),0,['class'=>'form-control'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('file','Image')!!}
                                    {!! Form::file('photo_id',null,['class'=>'form-control form-control-file'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('password','Password')!!}
                                    {!! Form::password('password',['class'=>'form-control .awesome'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('password_confirmation','Confirm Password')!!}
                                    {!! Form::password('password_confirmation',['class'=>'form-control'])!!}
                                </div>

                            </div>
                            <div class="col-md-6 col-xs-12">

                                <h4>
                                    <i class="fa fa-users"></i>
                                    Group
                                </h4>
                                <div class="form-group">
                                    {!! Form::label('group_id','Group')!!}


                                    {!! Form::select('group_id[]', ['' => 'Select Groups']+$groups,-1, array('multiple' => 'multiple', 'class' => 'form-control margin  ','id'=>'group_select','tabindex'=>'-1','aria-hidden'=>'true')) !!}

                                    {{--Bootstarp Multiselect--}}
                                    {{--{!! Form::select('group_id[]', [''=>'Select Group']+$groups, null, array('multiple' => 'multiple', 'class' => 'form-control margin multiselect')) !!}--}}
                                </div>

                                <h4>
                                    <i class="fa fa-lock"></i>
                                    Role
                                </h4>
                                <div class="form-group">
                                    {!! Form::label('role_id','Role')!!}
                                    {!! Form::select('role_id',[''=>'Select Role']+$roles,null,['class'=>'form-control'])!!}
                                </div>
                                <h4>
                                    <i class="fa fa-building-o" aria-hidden="true"></i>
                                   Branch
                                </h4>
                                <div class="form-group">
                                    {!! Form::label('branch_id','Branch')!!}
                                    {!! Form::select('branch_id',[''=>'Select Branch']+$branches,null,['class'=>'form-control'])!!}
                                </div>



                            </div>
                        <div class="col-md-offset-5 col-md-6 col-xs-12">
                                       <div class="form-group">
                                          {!! Form:: submit('Create User',['class'=>'btn btn-primary'])!!}
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
            $('#group_select').select2();
        });

    </script>

@stop