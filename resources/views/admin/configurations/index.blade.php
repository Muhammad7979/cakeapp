@extends('layouts.admin')

@section('content')

    @if(Session::has('deleted_configuration'))
        <p class="bg-danger">{{session('deleted_configuration')}}</p>

    @elseif(Session::has('updated_configuration'))
        <p class="bg-primary">{{session('updated_configuration')}}</p>
    @elseif(Session::has('created_configuration'))
        <p class="bg-success">{{session('created_configuration')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-list-alt"></i>
                            Create System Variable
                        </h3>
                    </div>

                    <div class="panel-body">
                        @can('create-configuration')
                            <div class="col-md-4 col-xs-6">

                                {!! Form::open (['method' => 'POST', 'action'=> 'SystemConfigurationsController@store']) !!}

                                <div class="form-group">
                                    {!! Form::label('key','Key')!!}
                                    {!! Form::text('key',null,['class'=>'form-control'])!!}

                                </div>
                                <div class="form-group">
                                    {!! Form::label('value','Value')!!}
                                    {!! Form::text('value',null,['class'=>'form-control'])!!}

                                </div>
                                <div class="form-group">
                                    {!! Form::label('label','Label')!!}
                                    {!! Form::text('label',null,['class'=>'form-control'])!!}

                                </div>


                                <div class="form-group">
                                    {!! Form:: submit('Create Variable',['class'=>'btn btn-success'])!!}
                                </div>

                                {!! Form::close() !!}
                            </div>
                        @endcan
                            <div class="col-8 col-xs-6">
                        @can('view-configuration')


                                <h4>
                                    <i class="fa fa-list-alt"></i>
                                  System Variables
                                </h4>

                                <table class="table table-inverse">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Key</th>
                                        <th scope="col">Value</th>
                                        <th scope="col">Label</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($systemVariables)
                                        @foreach($systemVariables as $variable)
                                            <tr>
                                                <th scope="row">{{$variable->id}}</th>
                                                @can('update-configuration')
                                                    <td><a href="{{route('configurations.edit',$variable->id)}}">{{$variable->key}}</a></td>
                                                @elsecannot('update-configuration')
                                                    <td>{{$variable->key}}</td>
                                                @endcan
                                                <td>{{$variable->value}}</td>
                                                <td>{{$variable->label}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                                @endcan
                            </div>


                    </div>



                </div>



            </div>



        </div>



    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$systemVariables->render()}}
        </div>
    </div>
    @include('includes.errorReporting')

@stop