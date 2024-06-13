@extends('layouts.admin')

@section('content')
    @if(Session::has('deleted_branch'))
        <p class="bg-danger">{{session('deleted_branch')}}</p>

    @elseif(Session::has('updated_branch'))
        <p class="bg-primary">{{session('updated_branch')}}</p>
    @elseif(Session::has('created_branch'))
        <p class="bg-success">{{session('created_branch')}}</p>
    @endif
    <div class="row">

        <div class="col-md-12 justify-content-center ">
            <div class="col-md-2 " style="margin-left: 40%;">

                @if(!env('IS_SERVER')==1)
                <div class="form-group">

                    <button class="btn btn-default" id="sync_button" style="margin-top: 24px;width: 70%;margin-left: 55px;"> <i class="fa fa-cloud-download "></i> Sync Branches</button>

                </div>
                    @endif
            </div>
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
    </div>
    <div class="loader"></div>
    <div class="row">

        <div class="col-md-12">

            @can('create-branch')
                <div class="panel panel-info">

                    <div class="panel-heading">


                        <h3 class="panel-title bariol-thin">
                            <i class="fa fa-building-o"></i>
                            Create Branch
                        </h3>
                    </div>

                    <div class="panel-body">

                        {!! Form::open (['method' => 'POST', 'action'=> 'AdminBranchesController@store']) !!}
                        <div class="col-md-6 col-xs-12">

                                    <div class="form-group">
                                        {!! Form::label('name','Title')!!}
                                        {!! Form::text('name',null,['class'=>'form-control'])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('code','Branch Code')!!}
                                        {!! Form::text('code',null,['class'=>'form-control'])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('is_active','Status')!!}
                                        {!! Form::select('is_active',array(1=>'Active', 0=>'Inactive'),0,['class'=>'form-control'])!!}
                                    </div>
                        </div>
                        <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('address','Address')!!}
                                        {!! Form::text('address',null,['class'=>'form-control'])!!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('phone','Contact Number')!!}
                                        {!! Form::text('phone',null,['class'=>'form-control'])!!}
                                    </div>

                        </div>
                        <div class="col-md-offset-5 col-md-6 col-xs-12">
                            <div class="form-group">
                                {!! Form:: submit('Create Branch',['class'=>'btn btn-primary'])!!}
                            </div>
                        </div>

                                    {!! Form::close() !!}

                        </div>





                </div>

            @endcan

        </div>


        </div>
    @include('includes.errorReporting')

    <div class="row">

          <div class="col-md-12">

              <div class="panel panel-info">

                  <div class="panel-heading">
                          <h3 class="panel-title bariol-thin">
                              <i class="fa fa-building-o"></i>
                              Branches
                          </h3>
                  </div>




                  <div class="panel-body">
                      @can('view-branch')
                      <div class="col-md-12 col-xs-12">


                          <table class="table">
                              <thead class="thead-light">
                              <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Name</th>
                                  <th scope="col">Branch Code</th>
                                  <th scope="col">Address</th>
                                  <th scope="col">Contact Number</th>
                                  <th scope="col">Status</th>
                              </tr>
                              </thead>
                              <tbody id="table_body">
                              @if($branches)
                                  @foreach($branches as $branch)
                                      <tr>
                                          <th scope="row">{{$branch->id}}</th>
                                          @can('update-branch')
                                          <td><a href="{{route('branches.edit',$branch->id)}}">{{$branch->name}}</a></td>
                                          @elsecannot('update-branch')
                                          <td>{{$branch->name}}</td>
                                          @endcan
                                          <td>{{$branch->code}}</td>
                                          <td>{{$branch->address}}</td>
                                          <td>{{$branch->phone}}</td>
                                          <td>
                                              {{--{{$user->is_active ==1 ? 'Active': 'Inactive'}}--}}

                                              {{--this is another way of displaying the user status--}}
                                              @if($branch->is_active ==1)
                                                  <i class="fa fa-circle " style="color: green"></i>
                                              @else
                                                  <i class="fa fa-circle red" style="color: red"></i>

                                              @endif



                                          </td>

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

    <div class="row">
        <div class="col-sm-6 col-sm-offset-5">
            {{$branches->render()}}
        </div>
    </div>







@stop

@section('scripts')
<script>
    $(window).load(function(){
        // PAGE IS FULLY LOADED
        // FADE OUT YOUR OVERLAYING DIV
        $(".loader").css("display", "block");
        $('.loader').fadeOut('slow');
    });
    function syncBranches()

    {
        $(".loader").css("display", "block");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "post",
            url: "/branches/sync", // path to function
            cache: false,
            success: function(val){





                if (val.length != 0) {
                    $('#table_body').empty();
                    var trHTML = '';
                    var isActive = '';
                    $.each(val, function (i, item) {

                        if(item.is_active==1)
                        {
                            isActive='  <i class="fa fa-circle " style="color: green"></i>';
                        }else
                        {
                            isActive=  ' <i class="fa fa-circle " style="color: red"></i>';
                        }
                        var editRoute = ' {{route("orders.edit",":id")}}';
                        editRoute = editRoute.replace(':id', item.id);
                        trHTML += '<tr>' +
                            '<td>' + item.id +'</td>'+
                                @can('update-branch')
                                    ' <td><a href="' + editRoute + '"> '+item.name+' </a> </td>' +
                                @elsecannot('update-branch')
                                     '<td>'+item.name+' </td>' +
                                @endcan
                            '</td><td>' + item.code +
                            '</td><td>' + item.address+
                            '</td><td>' + item.phone +
                            '</td><td>' + isActive +
                                '</td>'+

                                    '</tr>';
                    });

                    $('#table_body').append(trHTML);
                    $(".loader").fadeOut("slow");

                }
                else {
                    $('#table_body').empty();
                    $(".loader").fadeOut("slow");
                }






            },
            error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }

        });
    }

    $(document).ready(function() {



        $('#sync_button').click(function () {

            syncBranches();
        });


    });


</script>


@stop