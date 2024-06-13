@extends('layouts.admin')

@section('content')



    @if(Session::has('deleted_role'))
        <p class="bg-danger">{{session('deleted_role')}}</p>

    @elseif(Session::has('updated_role'))
        <p class="bg-primary">{{session('updated_role')}}</p>
    @elseif(Session::has('created_role'))
        <p class="bg-success">{{session('created_role')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-tasks"></i>
                            All Roles
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">

                                <table class="table">
                                    <thead class=".thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Role Name</th>
                                        <th scope="col">Slug</th>
                                        <th scope="col">Created</th>
                                        <th scope="col">Updated</th>

                                        {{--<th scope="col">Handle</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if($roles)

                                        @foreach($roles as $role)
                                            <tr>

                                                <th scope="row">{{$role->id}}</th>
                                                <td><a href="{{route('roles.edit',$role->id)}}">{{$role->name}}</a></td>
                                                <td>{{$role->slug}}</td>
                                                <td>{{$role->created_at ? $role->created_at->diffForHumans():'No date'}}</td>
                                                <td>{{$role->updated_at ? $role->updated_at->diffForHumans(): 'No date'}}</td>

                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>


                            </div>

                        </div>
                    </div>



                </div>



            </div>



        </div>



    </div>
    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$roles->render()}}
        </div>
    </div>
@stop