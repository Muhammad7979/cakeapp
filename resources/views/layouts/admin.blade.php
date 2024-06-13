<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html charset=utf-8" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin</title>


    <!-- Bootstrap Core CSS -->
    {{-- <link href="{{asset('css/libs.css')}}" rel="stylesheet"> --}}

    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    {{-- metis menu css --}}
    <link href="{{ asset('vendor/metisMenu/metisMenu.css') }}" rel="stylesheet">

    {{-- custom css --}}
    <link href="{{ asset('vendor/sbAdmin2css/sb-admin-2.css') }}" rel="stylesheet">

    {{-- Moris chart css --}}
    <link href="{{ asset('vendor/morrisjs/morris.css') }}" rel="stylesheet">

    {{-- Custom fonts --}}
    <link href="{{ asset('vendor/font-awesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css">

    {{-- Date time picker css --}}
    <link href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet">
    {{-- <link href="{{asset('css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"> --}}

    {{-- Multi Select Css --}}
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" /> --}}

    {{-- Lightbox Css --}}
    <link href="{{ asset('css/lightbox.css') }}" rel="stylesheet">

    {{-- Jquery-ui css --}}
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">

    {{-- custom css --}}
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    {{-- <link href="{{asset('image-zoom/dist/zoomify.css')}}" rel="stylesheet"> --}}








    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" /> --}}
    {{-- <link href="{{asset('css/app.css')}}" rel="stylesheet"> --}}
    {{-- <link href="{{asset('css/app.css')}}" rel="stylesheet"> --}}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->





</head>

<body id="admin-page" style="padding-top: 0px;">
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Home</a>
            </div>
            <!-- /.navbar-header -->



            <ul class="nav navbar-top-links navbar-right">


                {{-- start of  dropdown for tasks --}}

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        @if ($totalOrders > 0)
                            <li>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Processed Orders</strong>
                                            <?php $processedPer = number_format(round(($processedCount / $totalOrders) * 100), 0); ?>
                                            <span class="pull-right text-muted">{{ $processedPer }}% </span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-success" role="progressbar"
                                                aria-valuenow="{{ $processedPer }}" aria-valuemin="0"
                                                aria-valuemax="100" style="width: {{ $processedPer }}%">
                                                <span class="sr-only">{{ $processedPer }}% Complete (success)</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>

                            <li> <?php $unProcessedPer = number_format(round(($unProcessedCount / $totalOrders) * 100), 0); ?>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Un-Processed Orders </strong>

                                            <span class="pull-right text-muted">{{ $unProcessedPer }}%</span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-warning" role="progressbar"
                                                aria-valuenow="{{ $unProcessedPer }}" aria-valuemin="0"
                                                aria-valuemax="100" style="width: {{ $unProcessedPer }}%">
                                                <span class="sr-only">{{ $unProcessedPer }}% Un (warning)</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <?php $cancelledPer = number_format(round(($cancelledOrderCount / $totalOrders) * 100), 0); ?>
                            <li>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Cancelled Orders</strong>

                                            <span class="pull-right text-muted">{{ $cancelledPer }}% </span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-danger" role="progressbar"
                                                aria-valuenow="{{ $cancelledPer }}" aria-valuemin="0"
                                                aria-valuemax="100" style="width: {{ $cancelledPer }}%">
                                                <span class="sr-only">{{ $cancelledPer }}% Cancelled (danger)</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="text-center" href="{{ route('orders.index') }}">
                                    <strong>See all orders</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        @endif
                    </ul>

                    <!-- /.dropdown-tasks -->
                </li>

                {{-- End of dropdown for tasks --}}


                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>
                        {{ Auth::user()->name }} <span class=" fa fa-caret-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a class="dropdown-item fa fa-sign-out fa-fw" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                    <!-- /.dropdown-users -->
                </li>

                <!-- /.dropdown -->


            </ul>












            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">

                        <li>
                            <a href="/admin"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        @canany(['view-user', 'create-user', 'update-user', 'delete-user'])
                            <li>
                                <a href="#"><i class="fa fa-user fa-fw"></i>Users<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    @can('view-user')
                                        <li>
                                            <a href="{{ route('users.index') }}"><i class="fa fa-users fa-fw"></i>All
                                                Users</a>
                                        </li>
                                    @endcan
                                    {{-- @can('create-user') --}}
                                    <li>
                                        <a href="{{ route('users.create') }}"><i class="fa fa-plus-circle fa-fw"></i>Add
                                            New User</a>
                                    </li>
                                    {{-- @endcan --}}
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                        @endcanany
                        @canany(['view-product'])
                            <li>
                                <a href="#"><i class="fa fa-birthday-cake fa-fw"></i> Cakes<span
                                        class="fa arrow"></span></a>

                                <ul class="nav nav-second-level">

                                    <li>
                                        <a href="#"><span class="fa fa-birthday-cake fa-fw"></span> Products<span
                                                class="fa arrow"></span></a>

                                        <ul class="nav nav-third-level">
                                            @can('view-product')
                                                <li>
                                                    <a href="{{ route('products.index') }}"><i
                                                            class="fa fa-birthday-cake fa-fw"></i>All Products</a>
                                                </li>
                                            @endcan
                                            @can('view-product')
                                                <li>
                                                    <a href="{{ route('pos.items') }}"><i
                                                            class="fa fa-birthday-cake fa-fw"></i>POS Items</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('itemkits') }}"><i
                                                            class="fa fa-birthday-cake fa-fw"></i>Item Kits</a>
                                                </li>
                                            @endcan
                                            @can('create-product')
                                                <li>
                                                    <a href="{{ route('products.create') }}"><i
                                                            class="fa fa-plus-circle fa-fw"></i>Create Products</a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </li>
                                </ul>

                                <ul class="nav nav-second-level">


                                    @canany(['view-category', 'create-category', 'update-category', 'delete-category'])
                                        <li>

                                            <a href="#"><i class="fa fa-list-alt fa-fw"></i> Product Category<span
                                                    class="fa arrow"></span></a>

                                            <ul class="nav nav-third-level">
                                                @can('view-category')
                                                    <li>
                                                        <a href="{{ route('categories.index') }}"><i
                                                                class="fa fa-list-alt fa-fw"></i>All Product Categories</a>
                                                    </li>
                                                @endcan
                                                @can('create-category')
                                                    <li>
                                                        <a href="{{ route('categories.create') }}"><i
                                                                class="fa fa-plus-circle fa-fw"></i>Create Product Category</a>
                                                    </li>
                                                @endcan
                                            </ul>

                                        </li>
                                    @endcanany

                                </ul>

                                <ul class="nav-second-level nav">

                                    @canany(['view-material', 'create-material', 'update-material', 'delete-material'])
                                        <li> <a href="#"><i class="fa fa-book fa-fw"></i> Materials<span
                                                    class="fa arrow"></span></a>
                                            <ul class="nav nav-third-level">
                                                <li>
                                                    <a href="{{ route('materials.index') }}"><i
                                                            class="fa fa-book fa-fw"></i>Material List</a>
                                                </li>

                                            </ul>
                                        </li>
                                    @endcanany
                                </ul>











                                <ul class="nav nav-second-level">

                                    <li> <a href="#"><i class="fa fa-book fa-fw"></i> Flavours<span
                                                class="fa arrow"></span></a>

                                        <ul class="nav nav-third-level">
                                            @can('view-flavour')
                                                <li>
                                                    <a href="{{ route('flavours.index') }}"><i
                                                            class="fa fa-book fa-fw"></i>All Flavours</a>
                                                </li>
                                            @endcan
                                            @can('create-flavour')
                                                <li>
                                                    <a href="{{ route('flavours.create') }}"><i
                                                            class="fa fa-plus-circle fa-fw"></i>Create Flavour</a>
                                                </li>
                                            @endcan
                                        </ul>

                                        @canany(['view-flavourCategory', 'create-flavourCategory', 'update-flavourCategory',
                                            'delete-flavourCategory'])
                                        <li>

                                            <a href="#"><i class="fa fa-list-alt fa-fw"></i> Flavours Category<span
                                                    class="fa arrow"></span></a>

                                            <ul class="nav nav-third-level">
                                                <li>
                                                    <a href="{{ route('flavourCategory.index') }}"><i
                                                            class="fa fa-list-alt fa-fw"></i>Flavour Category</a>
                                                </li>
                                            </ul>

                                        </li>
                                    @endcanany


                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                        @endcanany

                        @canany(['view-group', 'create-group', 'update-group', 'delete-group'])
                            <li>
                                <a href="#"><i class="fa fa-users fa-fw"></i>Groups<span
                                        class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">

                                    <li>
                                        <a href="{{ route('groups.index') }}"><i class="fa fa-users fa-fw"></i>All
                                            Groups</a>
                                    </li>



                                </ul>

                            </li>
                        @endcanany


                        @canany(['view-branch', 'create-branch', 'update-branch', 'delete-branch'])
                            <li>
                                <a href="#"><i class="fa fa-building-o fa-fw"></i>Branches<span
                                        class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">

                                    <li>
                                        <a href="{{ route('branches.index') }}"><i class="fa fa-building-o fa-fw"></i>All
                                            Branches</a>
                                    </li>


                                </ul>

                            </li>
                        @endcanany

                        {{-- @canany(['view-role', 'create-role', 'update-role', 'delete-role']) --}}
                        <li>
                            <a href="#"><i class="fa fa-tasks fa-fw"></i>Roles<span
                                    class="fa arrow"></span></a>

                            <ul class="nav nav-second-level">
                                {{-- @can('view-role') --}}
                                <li>
                                    <a href="{{ route('roles.index') }}"><i class="fa fa-tasks fa-fw"></i>All
                                        Roles</a>
                                </li>
                                {{-- @endcan --}}
                                {{-- @can('create-role') --}}
                                <li>
                                    <a href="{{ route('roles.create') }}"><i
                                            class="fa fa-plus-circle fa-fw"></i>Create Roles</a>
                                </li>
                                {{-- @endcan --}}



                            </ul>

                        </li>
                        {{-- @endcanany --}}

                        @canany(['view-order', 'create-order', 'update-order', 'delete-order'])
                            <li>
                                <a href="#"><i class="fa fa-shopping-cart fa-fw"></i>Orders<span
                                        class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    @can('view-order')
                                        <li>
                                            <a href="{{ route('orders.index') }}"><i
                                                    class="fa fa-shopping-cart fa-fw"></i>All Orders</a>
                                        </li>
                                    @endcan
                                    {{-- <li> --}}
                                    {{-- <a href="{{route('orders.create')}}"><i class="fa fa-shopping-cart fa-fw"></i>All Orders</a> --}}
                                    {{-- </li> --}}

                                </ul>

                            </li>
                        @endcanany
                        @canany(['bakeman-view', 'bakeman-update'])
                            <li>
                                <a href="#"><i class="fa fa-shopping-cart fa-fw"></i>Bakeman<span
                                        class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    @can('bakeman-view')
                                        <li>
                                            <a href="{{ route('bakeman.index') }}"><i
                                                    class="fa fa-shopping-cart fa-fw"></i>All Orders</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('bakeman.reports') }}"><i
                                                    class="fa fa-shopping-cart fa-fw"></i>Cake & Pos Items Report</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('bakeman.pos_items','positem') }}"><i
                                                    class="fa fa-shopping-cart fa-fw"></i>Pos Items orders</a>
                                        </li>
                                    @endcan


                                </ul>

                            </li>
                        @endcanany





                        @can('view-sales')
                            <li>
                                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Sales<span
                                        class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="{{ route('sales.index') }}"><i
                                                class="fa fa-bar-chart fa-lg"></i>Sales</a>
                                    </li>
                                </ul>

                            </li>
                        @endcan
                        @canany(['view-orderType', 'create-orderType', 'update-orderType', 'delete-orderType',
                            'create-local-configuration'])
                            <li>
                                <a href="#"><i class="fa fa-sitemap fa-fw"></i> System Configuration<span
                                        class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    @canany(['view-orderType', 'create-orderType', 'update-orderType', 'delete-orderType'])
                                        <li>
                                            <a href="{{ route('orderTypes.index') }}"><i class="fa fa-tasks fa-fw"></i>Order
                                                Type</a>
                                        </li>
                                    @endcanany

                                    @canany(['view-orderStatus', 'create-orderStatus', 'update-orderStatus',
                                        'delete-orderStatus'])
                                        <li>
                                            <a href="{{ route('orderStatuses.index') }}"><i
                                                    class="fa fa-tasks fa-fw"></i>Order Status</a>
                                        </li>
                                    @endcanany
                                    @canany(['view-paymentType', 'create-paymentType', 'update-paymentType',
                                        'delete-paymentType'])
                                        <li>
                                            <a href="{{ route('paymentTypes.index') }}"><i
                                                    class="fa fa-tasks fa-fw"></i>Payment Type</a>
                                        </li>
                                    @endcanany


                                    @can(['view-configuration'])
                                        <li>
                                            <a href="#">System Settings<span class="fa arrow"></span></a>
                                            <ul class="nav nav-third-level">
                                                @canany(['create-configuration', 'update-configuration',
                                                    'delete-configuration'])
                                                    <li>
                                                        <a href="{{ route('configurations.index') }}"><i
                                                                class="fa fa-tasks fa-fw"></i>System Variables</a>
                                                    </li>
                                                @endcanany
                                                @canany(['create-local-configuration'])
                                                    <li>
                                                        <a href="{{ route('configurations.create') }}"><i
                                                                class="fa fa-tasks fa-fw"></i>System Setting</a>
                                                    </li>
                                                @endcanany

                                            </ul>

                                        </li>
                                    @endcan

                                </ul>

                            </li>
                        @endcanany

                    </ul>


                </div>

            </div>

        </nav>





        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li>
                        <a href="/profile"><i class="fa fa-dashboard fa-fw"></i>Profile</a>
                    </li>






                </ul>

            </div>

        </div>








        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">


                        @yield('content')
                    </div>

                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->



    </div>

    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.12/jquery.mousewheel.js"></script>




    {{-- Bootsratp Core Js --}}
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    {{-- Metis Menu js --}}
    <script src="{{ asset('vendor/metisMenu/metisMenu.min.js') }}"></script>




    <script src="{{ asset('js/sb-admin-2.js') }}"></script>









    @yield('scripts')
    @yield('footer')






</body>

</html>
