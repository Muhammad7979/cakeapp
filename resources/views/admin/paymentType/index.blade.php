@extends('layouts.admin')

@section('content')

    @if(Session::has('deleted_orderType'))
        <p class="bg-danger">{{session('deleted_paymentType')}}</p>

    @elseif(Session::has('updated_orderType'))
        <p class="bg-primary">{{session('updated_paymentType')}}</p>
    @elseif(Session::has('created_orderType'))
        <p class="bg-success">{{session('created_paymentType')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-list-alt"></i>
                            Create Payment Type
                        </h3>
                    </div>

                    <div class="panel-body">
                        @can('create-paymentType')
                            <div class="col-md-4 col-xs-12">

                                {!! Form::open (['method' => 'POST', 'action'=> 'AdminPaymentTypeController@store']) !!}

                                <div class="form-group">
                                    {!! Form::label('name',' Payment Type')!!}
                                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                                </div>


                                <div class="form-group">
                                    {!! Form:: submit('Create Type',['class'=>'btn btn-success'])!!}
                                </div>

                                {!! Form::close() !!}
                            </div>
                        @endcan
                        @can('view-orderType')
                            <div class="col-md-8 col-xs-12">

                                <h4>
                                    <i class="fa fa-list-alt"></i>
                                    Payment Types
                                </h4>

                                <table class="table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Created</th>
                                        <th scope="col">Updated</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($paymentTypes)
                                        @foreach($paymentTypes as $type)
                                            <tr>
                                                <th scope="row">{{$type->id}}</th>
                                                @can('update-orderType')
                                                    <td><a href="{{route('paymentTypes.edit',$type->id)}}">{{$type->name}}</a></td>
                                                @elsecannot('update-orderType')
                                                    <td>{{$type->name}}</td>
                                                @endcan
                                                <td>{{$type->created_at ? $type->created_at->diffForHumans(): 'no Date'}}</td>
                                                <td>{{$type->updated_at ? $type->updated_at->diffForHumans(): 'no Date'}}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>

                            </div>
                        @endcan

                    </div>



                </div>



            </div>



        </div>



    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$paymentTypes->render()}}
        </div>
    </div>
    @include('includes.errorReporting')

@stop