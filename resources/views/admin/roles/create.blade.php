@extends('layouts.admin')

@section('content')

    <div class="row">

        <div class="col-md-12">



            <div class="panel panel-info">

                <div class="panel-heading">


                    <h3 class="panel-title bariol-thin">
                        <i class="fa fa-tasks"></i>
                        Create Roles
                    </h3>
                </div>

                <div class="panel-body">

                    {{--<div class="row">--}}
                    {!! Form::open (['method'=>'POST','action'=>'AdminRolesController@store','files'=>true]) !!}
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

                         <div class="col-md-9">
                             <div class="panel panel-info">
                                 <div class="panel-heading">Users Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-user'>View User
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-user'>Create User
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-user'>Update User
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-user'>Delete User
                                     </label>
                                 </div>
                             </div>



                             <div class="panel panel-info">
                                 <div class="panel-heading">Group Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-group'>View Groups
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-group'>Create Groups
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-group'>Update Groups
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-group'>Delete Groups
                                     </label>
                                 </div>
                             </div>


                             <div class="panel panel-info">
                                 <div class="panel-heading">Branch Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-branch'>View Branches
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-branch'>Create Branches
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-branch'>Update Branches
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-branch'>Delete Branches
                                     </label>
                                 </div>
                             </div>



                             <div class="panel panel-info">
                                 <div class="panel-heading">Roles Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-role'>View Roles
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-role'>Create Roles
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-role'>Update Roles
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-role'>Delete Roles
                                     </label>
                                 </div>
                             </div>


                             <div class="panel panel-info">
                                 <div class="panel-heading">Material Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-material'>View Material
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-material'>Create Material
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-material'>Update Material
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-material'>Delete Material
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Flavour Category Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-flavourCategory'>View Flavour Category
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-flavourCategory'>Create Flavour Category
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-flavourCategory'>Update Flavour Category
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-flavourCategory'>Delete Flavour Category
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Flavours Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-flavour'>View Flavours
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-flavour'>Create Flavours
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-flavour'>Update Flavours
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-flavour'>Delete Flavours
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Product Category Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-category'>View Category
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-category'>Create Category
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-category'>Update Category
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-category'>Delete Category
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Product  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-product'>View Product
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-product'>Create Product
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-product'>Update Product
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-product'>Delete Product
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Order Type  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-orderType'>View Order Type
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-orderType'>Create Order Type
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-orderType'>Update Order Type
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-orderType'>Delete Order Type
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Order Status  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-orderStatus'>View Order Status
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-orderStatus'>Create Order Status
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-orderStatus'>Update Order Status
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-orderStatus'>Delete Order Status
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Payment Type  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-paymentType'>View Payment Type
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-paymentType'>Create Payment Type
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-paymentType'>Update Payment Type
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-paymentType'>Delete Payment Type
                                     </label>
                                 </div>
                             </div>
                             <div class="panel panel-info">
                                 <div class="panel-heading">Configuration  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-configuration'>View Configuration
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-configuration'>Create Configuration
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-configuration'>Update Configuration
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-configuration'>Delete Configuration
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-local-configuration'>Create Local Configuration
                                     </label>
                                 </div>
                             </div>

                             <div class="panel panel-info">
                                 <div class="panel-heading">Order  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-order'>View Orders
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='create-order'>Create Orders
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-order'>Update Orders
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='delete-order'>Delete Orders
                                     </label>
                                 </div>
                             </div>

                             <div class="panel panel-info">
                                 <div class="panel-heading">Bakeman  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='bakeman-view'>View Orders
                                     </label>
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='update-order'>Update Orders
                                     </label>

                                 </div>
                             </div>


                             <div class="panel panel-info">
                                 <div class="panel-heading">Sales  Permissions</div>
                                 <div class="panel-body">
                                     <label class="checkbox-inline">
                                         <input name="permissions[]" type="checkbox" value='view-sales'>View Sales
                                     </label>
                                 </div>
                             </div>



                         </div>
                     </div>
                    <div class="form-group">
                        {!! Form:: submit('Create Role',['class'=>'btn btn-primary'])!!}
                    </div>
                    </div>





                    {!! Form::close() !!}
                    {{--</div>--}}
                </div>



            </div>







        </div>



    </div>
    @include('includes.errorReporting')

@stop