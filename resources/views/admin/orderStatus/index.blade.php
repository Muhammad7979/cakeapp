@extends('layouts.admin')

@section('content')

    @if(Session::has('deleted_orderStatus'))
        <p class="bg-danger">{{session('deleted_orderStatus')}}</p>

    @elseif(Session::has('updated_orderStatus'))
        <p class="bg-primary">{{session('updated_orderStatus')}}</p>
    @elseif(Session::has('created_orderStatus'))
        <p class="bg-success">{{session('created_orderStatus')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-list-alt"></i>
                            Create Status Type
                        </h3>
                    </div>

                    <div class="panel-body">
                        @can('create-orderStatus')
                            <div class="col-md-4 col-xs-12">

                                {!! Form::open (['method' => 'POST', 'action'=> 'AdminOrderStatusController@store']) !!}

                                <div class="form-group">
                                    {!! Form::label('name','Status')!!}
                                    {!! Form::text('name',null,['class'=>'form-control'])!!}
                                </div>


                                <div class="form-group">
                                    {!! Form:: submit('Create Status',['class'=>'btn btn-success'])!!}
                                </div>

                                {!! Form::close() !!}
                            </div>
                        @endcan
                        @can('view-orderStatus')
                            <div class="col-md-8 col-xs-12">

                                <h4>
                                    <i class="fa fa-list-alt"></i>
                                    Order Status
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
                                    @if($orderStatus)
                                        @foreach($orderStatus as $status)
                                            <tr>
                                                <th scope="row">{{$status->id}}</th>
                                                @can('update-orderStatus')
                                                    <td><a href="{{route('orderStatuses.edit',$status->id)}}">{{$status->name}}</a></td>
                                                @elsecannot('update-orderStatus')
                                                    <td>{{$status->name}}</td>
                                                @endcan
                                                <td>{{$status->created_at ? $status->created_at->diffForHumans(): 'no Date'}}</td>
                                                <td>{{$status->updated_at ? $status->updated_at->diffForHumans(): 'no Date'}}</td>
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
            {{$orderStatus->render()}}
        </div>
    </div>
    @include('includes.errorReporting')

@stop