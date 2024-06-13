@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">



            <div class="panel panel-info">

                <div class="panel-heading">


                    <h3 class="panel-title bariol-thin">
                        <i class="fa fa-tasks"></i>
                        Edit Roles
                    </h3>
                </div>

                <div class="panel-body">

                    {{--<div class="row">--}}
                    {!! Form::model ($role,['method'=>'PATCH','action'=>['AdminRolesController@update',$role->id]]) !!}
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('name','Role Name')!!}
                            {!! Form::text('name',null,['class'=>'form-control'])!!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('slug','Role Slug')!!}
                            {!! Form::text('slug',null,['class'=>'form-control'])!!}
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-10">
                            <div class="panel panel-info">
                                <div class="panel-heading">Users Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-user' @if($role->hasAccess(['view-user'])) checked @endIf>View User
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-user' @if($role->hasAccess(['create-user'])) checked @endIf>Create User
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-user' @if($role->hasAccess(['update-user'])) checked @endIf>Update User
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-user' @if($role->hasAccess(['delete-user'])) checked @endIf>Delete User
                                    </label>
                                </div>
                            </div>



                            <div class="panel panel-info">
                                <div class="panel-heading">Group Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-group' @if($role->hasAccess(['view-group'])) checked @endIf>View Groups
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-group' @if($role->hasAccess(['create-group'])) checked @endIf>Create Groups
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-group' @if($role->hasAccess(['update-group'])) checked @endIf>Update Groups
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-group' @if($role->hasAccess(['delete-group'])) checked @endIf>Delete Groups
                                    </label>
                                </div>
                            </div>


                            <div class="panel panel-info">
                                <div class="panel-heading">Branch Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-branch' @if($role->hasAccess(['view-branch'])) checked @endIf>View Branches
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-branch' @if($role->hasAccess(['create-branch'])) checked @endIf>Create Branches
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-branch' @if($role->hasAccess(['update-branch'])) checked @endIf>Update Branches
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-branch' @if($role->hasAccess(['delete-branch'])) checked @endIf>Delete Branches
                                    </label>
                                </div>
                            </div>



                            <div class="panel panel-info">
                                <div class="panel-heading">Roles Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-role' @if($role->hasAccess(['view-role'])) checked @endIf>View Roles
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-role' @if($role->hasAccess(['create-role'])) checked @endIf>Create Roles
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-role' @if($role->hasAccess(['update-role'])) checked @endIf>Update Roles
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-role' @if($role->hasAccess(['delete-role'])) checked @endIf>Delete Roles
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Material Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-material' @if($role->hasAccess(['view-material'])) checked @endIf>View Material
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-material' @if($role->hasAccess(['view-material'])) checked @endIf>Create Material
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-material' @if($role->hasAccess(['view-material'])) checked @endIf>Update Material
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-material' @if($role->hasAccess(['view-material'])) checked @endIf>Delete Material
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Flavour Category Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-flavourCategory' @if($role->hasAccess(['view-flavourCategory'])) checked @endIf>View Flavour Category
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-flavourCategory' @if($role->hasAccess(['create-flavourCategory'])) checked @endIf>Create Flavour Category
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-flavourCategory' @if($role->hasAccess(['update-flavourCategory'])) checked @endIf>Update Flavour Category
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-flavourCategory' @if($role->hasAccess(['delete-flavourCategory'])) checked @endIf>Delete Flavour Category
                                    </label>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading">Flavours Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-flavour' @if($role->hasAccess(['view-flavour'])) checked @endIf>View Flavours
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-flavour' @if($role->hasAccess(['create-flavour'])) checked @endIf>Create Flavours
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-flavour' @if($role->hasAccess(['update-flavour'])) checked @endIf>Update Flavours
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-flavour' @if($role->hasAccess(['delete-flavour'])) checked @endIf>Delete Flavours
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Product Category Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-category' @if($role->hasAccess(['view-category'])) checked @endIf>View Category
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-category' @if($role->hasAccess(['create-category'])) checked @endIf>Create Category
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-category' @if($role->hasAccess(['update-category'])) checked @endIf>Update Category
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-category' @if($role->hasAccess(['delete-category'])) checked @endIf>Delete Category
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Product Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-product' @if($role->hasAccess(['view-product'])) checked @endIf>View Product
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-product' @if($role->hasAccess(['create-product'])) checked @endIf>Create Product
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-product' @if($role->hasAccess(['update-product'])) checked @endIf>Update Product
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-product' @if($role->hasAccess(['delete-product'])) checked @endIf>Delete Product
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Order Type  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-orderType' @if($role->hasAccess(['view-orderType'])) checked @endIf>View Order Type
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-orderType' @if($role->hasAccess(['create-orderType'])) checked @endIf>Create Order Type
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-orderType' @if($role->hasAccess(['update-orderType'])) checked @endIf>Update Order Type
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-orderType' @if($role->hasAccess(['delete-orderType'])) checked @endIf>Delete Order Type
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Order Status  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-orderStatus' @if($role->hasAccess(['view-orderStatus'])) checked @endIf>View Order Status
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-orderStatus' @if($role->hasAccess(['create-orderStatus'])) checked @endIf>Create Order Status
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-orderStatus' @if($role->hasAccess(['update-orderStatus'])) checked @endIf>Update Order Status
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-orderStatus' @if($role->hasAccess(['delete-orderStatus'])) checked @endIf>Delete Order Status
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Payment Type  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-paymentType' @if($role->hasAccess(['view-paymentType'])) checked @endIf>View Payment Type
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-paymentType' @if($role->hasAccess(['create-paymentType'])) checked @endIf>Create Payment Type
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-paymentType' @if($role->hasAccess(['update-paymentType'])) checked @endIf>Update Payment Type
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-paymentType' @if($role->hasAccess(['delete-paymentType'])) checked @endIf>Delete Payment Type
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Configuration  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-configuration' @if($role->hasAccess(['view-configuration'])) checked @endIf>View configuration
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-configuration' @if($role->hasAccess(['create-configuration'])) checked @endIf>Create configuration
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-configuration' @if($role->hasAccess(['update-configuration'])) checked @endIf>Update configuration
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-configuration' @if($role->hasAccess(['delete-configuration'])) checked @endIf>Delete configuration
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-local-configuration' @if($role->hasAccess(['create-local-configuration'])) checked @endIf>Create Local Configuration
                                    </label>
                                </div>
                            </div>
                            <div class="panel panel-info">
                                <div class="panel-heading">Order  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-order' @if($role->hasAccess(['view-order'])) checked @endIf>View order
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='create-order' @if($role->hasAccess(['create-order'])) checked @endIf>Create order
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='update-order' @if($role->hasAccess(['update-order'])) checked @endIf>Update order
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='delete-order' @if($role->hasAccess(['delete-order'])) checked @endIf>Delete order
                                    </label>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading">Bakeman  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='bakeman-view' @if($role->hasAccess(['bakeman-view'])) checked @endIf>View order
                                    </label>
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='bakeman-update' @if($role->hasAccess(['bakeman-update'])) checked @endIf>update order
                                    </label>

                                </div>
                            </div>


                            <div class="panel panel-info">
                                <div class="panel-heading">Sales  Permissions</div>
                                <div class="panel-body">
                                    <label class="checkbox-inline">
                                        <input name="permissions[]" type="checkbox" value='view-sales' @if($role->hasAccess(['view-sales'])) checked @endIf>View Sales
                                    </label>
                                </div>
                            </div>



                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-offset-3 col-md-8 col-xs-12">
                            <a class=" btn btn-default" href="{{route('roles.index')}}" style="margin-right: 60px;margin-left: 210px;"> Cancel</a>
                        {!! Form:: submit('Update Role',['class'=>'btn btn-primary'])!!}

                        {!! Form::close() !!}

                        @can('delete-role')
                            {!! Form::open (['method' => 'DELETE', 'action'=> ['AdminRolesController@destroy',$role->id], 'class'=>'pull-right' ,'style'=>'    margin-right: 115px;']) !!}

                            <div class="form-group">
                                {!! Form:: submit('Delete Role
                                ',['class'=>'btn btn-danger '])!!}
                            </div>

                            {!! Form::close() !!}
                        @endcan
                        </div>
                    </div>


                </div>








                {{--</div>--}}
            </div>



        </div>







    </div>



    </div>
    @include('includes.errorReporting')

@stop