@extends('layouts.admin')

@section('content')
    @if(Session::has('Error'))
        <p class="bg-danger">{{session('Error')}}</p>
    @endif
    <h1>Dashboard</h1>
    <hr>


    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-bag fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$totalOrders}}</div>
                            <div>Total Orders !</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        {{--<span class="pull-left">View Details</span>--}}
                        {{--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>--}}
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-check-square fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$processedOrderCount}}</div>
                            <div>Completed Orders !</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        {{--<span class="pull-left">View Details</span>--}}
                        {{--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>--}}
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$unProcessedOrderCount}}</div>
                            <div>Pending Orders!</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        {{--<span class="pull-left">View Details</span>--}}
                        {{--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>--}}
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-support fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{$cancelledOrderCount}}</div>
                            <div>Cancelled Orders!</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        {{--<span class="pull-left">View Details</span>--}}
                        {{--<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>--}}
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-8">
            @can('view-sales')
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Sales Chart
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                Actions
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu pull-right ranges" role="menu">
                                <li><a href="#" data-range='7'>7 Days</a></li>
                                <li><a href="#" data-range='30'>30 Days</a></li>
                                <li><a href="#" data-range='60'>60 Days</a></li>
                                <li><a href="#" data-range='90'>90 Days</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div id="morris-area-chart">
                        {{--{!! $lineChart->html() !!}--}}

                    </div>
                </div>
                <div class="pane-footer">
                    <hr>
                    @if(\Illuminate\Support\Facades\Session::get('is_server')==1)
                    <div class="text-center" style="       padding-top: 5px;
    padding-bottom: 23px;">
                        <h3 style="display: inline; font-weight: bold">Life-Time Sales (Rs) :</h3><p style="display: inline;
    font-size: x-large;
    font-family: sans-serif;     padding-left: 10px;">{{$totalSale}}</p>
                    </div>
                        @endif
                </div>

            </div>

        @endcan
            <div class="row">
                <div class="col-lg-12">
                    @can('view-sales')
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-bar-chart-o fa-fw"></i> Order History Chart
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                            Actions
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right ranges" role="menu">
                                            <li><a href="#" data-range='7'>7 Days</a></li>
                                            <li><a href="#" data-range='30'>30 Days</a></li>
                                            <li><a href="#" data-range='60'>60 Days</a></li>
                                            <li><a href="#" data-range='90'>90 Days</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div id="morris-area-order-chart">
                                    {{--{!! $lineChart->html() !!}--}}

                                </div>
                            </div>
                            <div class="pane-footer">

                            </div>

                        </div>

                    @endcan
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Pending Orders With Short Deadline
                </div>

                <div class="panel-body">
                    <div class="list-group">
                        @foreach($ordersDeadline as $order)
                        <a class="list-group-item">
                            <i class="fa fa-shopping-bag fa-fw"></i> Order Id : {{$order->order_number}}
                            <span class="pull-right text-muted small"><em>{{\Carbon\Carbon::parse($order->delivery_date)->diffForHumans()}}</em>
                                    </span>
                        </a>

                    @endforeach

                    </div>

                    <a href="{{route('orders.index')}}" class="btn btn-default btn-block">View All Orders</a>
                </div>

            </div>

            @can('view-sales')
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Type of Orders
                </div>
                <div class="panel-body" id="morris-donut-chart-panel">
                    <div id="morris-donut-chart">

                    </div>

                </div>

            </div>
            @endcan
        {{--{!! Charts::scripts() !!}--}}

        </div>

        <!-- /.col-lg-4 -->
    </div>

    <div class="row">




            {!! Charts::scripts() !!}


        <!-- /.col-lg-4 -->
    </div>












@stop
@section('scripts')
    <script src="{{asset('vendor/raphael/raphael.min.js')}}"></script>
    <script src="{{asset('vendor/morrisjs/morris.min.js')}}"></script>
<script>

    function loadDonutChart()
    {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "get",

            url: "/donutChart/date", // path to function
            cache: false,
            success: function (val) {
                var script = '{!! ":id->script()" !!}}'
                script = script.replace(":id",val);

                // console.log(val);
                // $('#morris-donut-chart').empty();
                $('#morris-donut-chart').append(val);
                // $('#morris-donut-chart-panel').append(script);


            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }

        });
    }
    function loadLineChart(range)
    {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            data:{dateRange:range},
            url: "/lineChart/date", // path to function
            cache: false,
            success: function (val) {
                //
                // console.log(val);
                $('#morris-area-chart').empty();
                $('#morris-area-chart').append(val);
                // $('#morris-donut-chart-panel').append(script);


            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }

        });
    }
    function loadOrderLineChart(range)
    {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: "post",
            data:{dateRange:range},
            url: "/orderLineChart/date", // path to function
            cache: false,
            success: function (val) {
                //
                console.log(val);
                $('#morris-area-order-chart').empty();
                $('#morris-area-order-chart').append(val);
                // $('#morris-donut-chart-panel').append(script);


            },
            error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }

        });
    }



    $(document).ready(function () {



        loadDonutChart();
        loadLineChart(7);
        loadOrderLineChart(7);

        $('ul.ranges a').click(function(e){
            e.preventDefault();
            // Get the number of days from the data attribute
            var el = $(this);
            days = el.attr('data-range');
            console.log('data range',days);
            // Request the data and render the chart using our handy function
            loadLineChart(days);
            loadOrderLineChart(days);
            // Make things pretty to show which button/tab the user clicked
            el.parent().addClass('active');
            el.parent().siblings().removeClass('active');
        });




    });




</script>


@stop
