@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_user'))
        <p class="bg-danger">{{session('deleted_user')}}</p>

    @elseif(Session::has('updated_user'))
        <p class="bg-primary">{{session('updated_user')}}</p>
    @elseif(Session::has('created_user'))
        <p class="bg-success">{{session('created_user')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12">

            <div class="col-md-12">

                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-user"></i>
                            All Users
                        </h3>
                    </div>

                    <div class="panel-body">

                        <div class="row">

                            <div class="col-md-12">

                                <table class="table">
                                    <thead class=".thead-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Active</th>
                                        <th scope="col">Branch</th>


                                        {{--<th scope="col">Handle</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if($users)

                                        @foreach($users as $user)
                                            <tr>
                                                <th scope="row">{{$user->id}}</th>
                                                <td><img height="60" src="{{$user->photo ? URL::asset('/images/User_Images/'.$user->photo->path ): '/images/avatar-male.jpg' }}" alt="" > </td>

                                                @can('update-user')
                                                    <td><a href="{{route('users.edit',$user->id)}}">{{$user->name}}</a></td>

                                                @elsecannot('update-user')
                                                    <td>{{$user->name}}</td>
                                                @endcan
                                                <td>{{$user->email}}</td>
                                                <td>{{$user->role? $user->role->name : "User Role Not Available"}}</td>
                                                <td>
                                                    {{--{{$user->is_active ==1 ? 'Active': 'Inactive'}}--}}

                                                    {{--this is another way of displaying the user status--}}
                                                    @if($user->is_active ==1)
                                                        <i class="fa fa-circle " style="color: green"></i>
                                                    @else
                                                        <i class="fa fa-circle red" style="color: red"></i>

                                                    @endif



                                                </td>
                                                {{--diffForHumans is used to get time as 20 hours ago etc--}}
                                                <td>{{$user->branch?$user->branch->name:'User Branch Not Available'}}</td>


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
            {{$users->render()}}
        </div>
    </div>
@stop