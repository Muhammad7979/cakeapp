@extends('layouts.admin')

@section('content')
    <p class="bg-danger">Fields in red are required</p>
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-user"></i>
                            SYSTEM CONFIGURATION
                        </h3>
                    </div>

                    <div class="panel-body">

                        {{--<div class="row">--}}
                        {!! Form::open (['method'=>'POST','action'=>'SystemConfigurationsController@saveSystemConfiguration']) !!}
                        <div class="col-md-6 col-xs-12">

                            @if(isset($branchCode))

                            <div class="form-group">
                                {!! Form::label('branch_Code','Branch Code',array('style'=>'color:red'))!!}
                                {!! Form::select('branch_Code',array(' '=>'Select Branch Code',env('BRANCH_CODE')=>env('BRANCH_CODE')),$branchCode? $branchCode->label:null,['class'=>'form-control','id'=>'branchCode'])!!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('branch_address','Address',array('style'=>'color:red'))!!}
                                {!! Form::textarea('branch_address',$branchAddress? $branchAddress->label:null,['class'=>'form-control','row'=>4,'readonly' => 'true','id'=>'branchAddress'])!!}
                            </div>

                            <div class="form-group">
                                {!! Form::label('branch_name','Branch Name',array('style'=>'color:red'))!!}
                                {!! Form::text('branch_name',$branchName? $branchName->label:null,['class'=>'form-control','readonly' => 'true','id'=>'branchName'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('system_id','System ID',array('style'=>'color:red'))!!}
                                {!! Form::number('system_id',$systemId? $systemId->label:null,['class'=>'form-control'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('website','Website')!!}
                                {!! Form::text('website',$website? $website->label:null,['class'=>'form-control'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('branch_number','Branch Phone',array('style'=>'color:red'))!!}
                                {!! Form::text('branch_number',$branchNumber? $branchNumber->label:null,['class'=>'form-control','readonly' => 'true','id'=>'branchPhone'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('branch_fax','Branch Fax')!!}
                                {!! Form::text('branch_fax', $branchFax? $branchFax->label:null ,['class'=>'form-control'])!!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('return_policy','Return Policy',array('style'=>'color:red'))!!}
                                {!! Form::textarea('return_policy',$returnPolicy? $returnPolicy->label:null,['class'=>'form-control'])!!}
                            </div>
                                {!! Form::hidden('branch_id', $branchId? $branchId->label : null ,['class'=>'form-control','id'=>'branchId'])!!}
                            @else

                                <div class="form-group">
                                    {!! Form::label('branch_Code','Branch Code',array('style'=>'color:red'))!!}
                                    {!! Form::select('branch_Code',array(''=>'Select Branch Code',env('BRANCH_CODE')=>env('BRANCH_CODE')),null,['class'=>'form-control','id'=>'branchCode'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('branch_address','Address',array('style'=>'color:red'))!!}
                                    {!! Form::textarea('branch_address',null,['class'=>'form-control','row'=>4,'readonly' => 'true','id'=>'branchAddress'])!!}
                                </div>

                                <div class="form-group">
                                    {!! Form::label('branch_name','Branch Name',array('style'=>'color:red'))!!}
                                    {!! Form::text('branch_name',null,['class'=>'form-control','readonly' => 'true','id'=>'branchName'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('system_id','System ID',array('style'=>'color:red'))!!}
                                    {!! Form::number('system_id',null,['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('website','Website')!!}
                                    {!! Form::text('website',null,['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('branch_number','Branch Phone',array('style'=>'color:red'))!!}
                                    {!! Form::text('branch_number',null,['class'=>'form-control','readonly' => 'true','id'=>'branchPhone'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('branch_fax','Branch Fax')!!}
                                    {!! Form::text('branch_fax', null ,['class'=>'form-control'])!!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('return_policy','Return Policy',array('style'=>'color:red'))!!}
                                    {!! Form::textarea('return_policy',null,['class'=>'form-control'])!!}
                                </div>
                                {!! Form::hidden('branch_id', null ,['class'=>'form-control','id'=>'branchId'])!!}

                        @endif

                        </div>

                        <div class="col-md-offset-5 col-md-6 col-xs-12">
                            <div class="form-group">
                                {!! Form:: submit('Submit',['class'=>'btn btn-primary'])!!}
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
    <script src="/resources/assets/js/libs/select2.min.js"></script>
    <script>
        $(document).ready(function(){


            $('#branchCode').change(function(){

                var branch_code=$('#branchCode').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "post",
                    url: "/system/config", // path to function
                    dataType: 'json',
                    cache: false,
                    data: { branchCode: branch_code},
                    success: function(val){


                        try{

                        document.getElementById('branchAddress').value=val[0]['address'];
                        document.getElementById('branchName').value=val[0]['name'];
                        document.getElementById('branchPhone').value=val[0]['phone'];
                        document.getElementById('branchId').value=val[0]['id'];

                        }catch(e) {
                            alert('Exception while request..'+e);
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }

                });
            });
        });


    </script>

@stop