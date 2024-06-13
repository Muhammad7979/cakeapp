@extends('layouts.admin')

@section('content')

    @if(Session::has('deleted_group'))
        <p class="bg-danger">{{session('deleted_group')}}</p>

    @elseif(Session::has('updated_group'))
        <p class="bg-primary">{{session('updated_group')}}</p>
    @elseif(Session::has('created_group'))
        <p class="bg-success">{{session('created_group')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-users"></i>
                            Create Group
                        </h3>
                    </div>

                    <div class="panel-body">
                        @can('create-group')
                        <div class="col-md-6 col-xs-12">

                        {!! Form::open (['method' => 'POST', 'action'=> 'AdminGroupsController@store']) !!}

                        <div class="form-group">
                            {!! Form::label('name','Title')!!}
                            {!! Form::text('name',null,['class'=>'form-control'])!!}
                        </div>


                        <div class="form-group">
                            {!! Form:: submit('Create Group',['class'=>'btn btn-success'])!!}
                        </div>

                         {!! Form::close() !!}
                        </div>
                        @endcan
                        @can('view-group')
                        <div class="col-md-6 col-xs-12">

                            <h4>
                                <i class="fa fa-users"></i>
                                Group
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
                                @if($groups)
                                    @foreach($groups as $group)
                                        <tr>
                                            <th scope="row">{{$group->id}}</th>
                                            @can('update-group')
                                            <td><a href="{{route('groups.edit',$group->id)}}">{{$group->name}}</a></td>
                                            @elsecannot('update-group')
                                                <td>{{$group->name}}</td>
                                            @endcan
                                            <td>{{$group->created_at ? $group->created_at->diffForHumans(): 'no Date'}}</td>
                                            <td>{{$group->updated_at ? $group->updated_at->diffForHumans(): 'no Date'}}</td>
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
            {{$groups->render()}}
        </div>
    </div>
    @include('includes.errorReporting')

@stop