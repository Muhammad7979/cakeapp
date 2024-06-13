<?php

namespace App\Http\Controllers;

use App\Configuration;
use App\Order;
use App\OrderStatus;
use App\OrderType;
use Carbon\Carbon;


//use ConsoleTVs\Charts\Facades\Charts;
use ConsoleTVs\Charts\Facades\Charts;
//use ConsoleTVs\Charts\Facades\Charts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $lastId = Order::latest('id')->first();
        if(!is_null($lastId)){
            $lastId = $lastId->id;
        }
        else{
            $lastId=0;
        }

        session(['lastId'=>$lastId]);
        session(['branch_code'=>env('BRANCH_CODE')]);
        session(['is_server'=>env('IS_SERVER')]);

        try {
            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();
            $branch_name = Configuration::where('key', '=', 'branch_name')->first();

            $processedOrder = OrderStatus::where('name', '=', 'Processed')->first();
            $deliveredOrder = OrderStatus::where('name', '=', 'Delivered')->first();
            $unProcessedOrder = OrderStatus::where('name', '=', 'Un-Processed')->first();
            $cancelledOrder = OrderStatus::where('name', '=', 'Cancelled')->first();
        }catch (\Exception $exception)
        {
            Log::error('Error Getting Order Status from Database '.$exception->getMessage());
            Session::flash('Error', 'Order Status Not Set in Database');
        }

        $totalOrders            = Order::all()->count();

        $processedOrderCount    = Order::whereIn('order_status',array($processedOrder->id,$deliveredOrder->id))->count() ;

        $unProcessedOrderCount  = Order::where('order_status','=',$unProcessedOrder->id)->count();

        $cancelledOrderCount     = Order::where('order_status','=',$cancelledOrder->id)->count();


        $ordersDeadline = Order::whereBetween( 'delivery_date',[Carbon::now(),Carbon::today()->addDays(2)])->where('order_status','=',$unProcessedOrder->id)->get();


        try {

            $orders_am = DB::select('select sum(o.advance_price) as sale,max(pending_amt.pending_amount) as pending_amount,o.order_date from orders as o left join (select sum(pending_amount) as pending_amount,pending_amount_paid_date from orders where pending_amount_paid_branch= :branchName group by pending_amount_paid_date ) as pending_amt on o.order_date = pending_amt.pending_amount_paid_date where  o.branch_code= :branchCode and o.order_status <> :cancelId group by o.order_date', ['branchName' => $branch_name->value, 'branchCode' => $branchCode->value, 'cancelId' => $cancelledOrder->id]);
        }catch (\Exception $exception)
        {
            $orders_am=[];
            Log::info('Error '.$exception->getMessage());
        }
        $totalSale=0;
        $totalSales=0;
        if (!empty($orders_am)) {
            $sales_am = [];
            $dates = [];
            $totalSale=0;

            foreach ($orders_am as $order) {
                Log::info('Date' . $order->order_date);
                if (isset($sales_am[$order->order_date])) {
                    $sales_am[$order->order_date] = $sales_am[$order->order_date] + $order->sale + (int)$order->pending_amount;
                } else {
                    $dates[$order->order_date] = $order->order_date;
                    $sales_am[$order->order_date] = $order->sale + (int)$order->pending_amount;
                }
            }
            foreach ($sales_am as $sale)
            {
                $totalSale= $totalSale +$sale;
            }
            Log::info('SALES'.print_r($sales_am,true));
        }
        // $lineChart = $this->getLineChartData();


        return view('admin.index', compact('totalOrders','processedOrderCount','unProcessedOrderCount','cancelledOrderCount','ordersDeadline',/* 'lineChart', */'totalSale'));



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function getDonutChartData()
    {


            $orders = Order::all();
            if(count($orders)) {

                $orderTypes = OrderType::all();
                foreach ($orderTypes as $type) {
                    $count = Order::where('order_type', '=', $type->id)->count();
                    $headers[] = $type->name;
                    $values[] = $count;

                }

                $donutChart = Charts::create('donut', 'highcharts')
                    ->title('Order Type Details')
                    ->labels($headers)
                    ->values($values)
                    ->dimensions(500, 1000)
                    ->responsive(true);


                return $donutChart->render();
            }
            else
            {
                return "No Records";
            }
    }
    public function getLineChartData()
{

    $branchCode = Configuration::where('key', '=', 'branch_Code')->first();
    $branch_name = Configuration::where('key', '=', 'branch_name')->first();

    $dateRange = Input::post('dateRange');

    $fromDate = Carbon::now()->subDays($dateRange +1 );
    $toDate = Carbon::now()->addDay();


    $cancelledOrder = OrderStatus::where('name', '=', 'Cancelled')->first();


//        $orders = Order::whereBetween('created_at', [$fromDate, $toDate])
//            ->whereIn('order_status', array($processedOrder->id, $deliveredOrder->id))
//            ->groupBy('date')->orderBy('date', 'ASC')
//            ->get([
//            DB::raw('Date(order_date) as date'),
//            DB::raw('SUM(advance_price) as sale'),
//        ]);
//        $orders = Order::whereBetween('created_at', [$fromDate, $toDate])->whereIn('order_status', array($processedOrder->id, $deliveredOrder->id))->groupBy('date')->orderBy('date', 'ASC')->get([
//            DB::raw('Date(created_at) as date'),
//            DB::raw('SUM(total_price) as sale')
//        ]);

    $orders=  DB::select('select sum(o.advance_price) as sale,max(pending_amt.pending_amount) as pending_amount,o.order_date from orders as o left join (select sum(pending_amount) as pending_amount,pending_amount_paid_date from orders where pending_amount_paid_branch= :branchName group by pending_amount_paid_date ) as pending_amt on o.order_date = pending_amt.pending_amount_paid_date where o.order_date < :todate and o.order_date >= :fromdate and o.branch_code= :branchCode and o.order_status <> :cancelId group by o.order_date', ['todate' => $toDate,'fromdate'=>$fromDate,'branchName'=>$branch_name->value,'branchCode'=>$branchCode->value,'cancelId'=>$cancelledOrder->id]);

    Log::info('Date' .print_r($orders,true));

//        $orders = Order::
//        selectSub(function ($query) use ($branch_name){
//            return $query->selectRaw('SUM(advance_price)');
//
//        },'advance_sum')
//            ->selectSub(function ($query) use ($branch_name){
//                return $query->selectRaw('SUM(pending_amount)')
//                                ->where('pending_amount_paid_branch','=',$branch_name->value);
//
//            },'pending_sum')
//            -> whereBetween('order_date', [$fromDate, $toDate])
//            ->whereIn('order_status', array($processedOrder->id, $deliveredOrder->id))
//            ->groupBy('date')->orderBy('date', 'ASC')
//            ->get([
//            DB::raw('Date(order_date) as date'),
//            DB::raw('*'),
//
//        ]);



    if (!empty($orders)) {
        $sales = []; $dates=[];

        foreach ($orders as $order) {
            Log::info('Date'.$order->order_date);
            if(isset($sales[$order->order_date])){
                $sales[$order->order_date] = $sales[$order->order_date]+$order->sale+(int)$order->pending_amount;
            }else {
                $dates[$order->order_date] = $order->order_date;
                $sales[$order->order_date] = $order->sale+(int)$order->pending_amount;
            }
        }
//            Log::info('Dates'.print_r($dates,true) );
//            Log::info('Sales'.print_r($sales,true) );
        $lineChart = Charts::create('line', 'highcharts')
            ->title('Sales Chart')
            ->elementLabel('Sales')
            ->labels($dates)
            ->values($sales)
            ->dimensions(800, 500)
            ->responsive(false);

        return $lineChart->render();

    }
    else{

        return "No Records To Display";
    }





}
    public function getOrderLineChartData()
    {

        $branchCode = Configuration::where('key', '=', 'branch_Code')->first();
        $branch_name = Configuration::where('key', '=', 'branch_name')->first();

        $dateRange = Input::post('dateRange');

        $fromDate = Carbon::now()->subDays($dateRange +1 );
        $toDate = Carbon::now()->addDay();


        $cancelledOrder = OrderStatus::where('name', '=', 'Cancelled')->first();


        $orders = Order::whereBetween('created_at', [$fromDate, $toDate])
            ->where('order_status','!=' ,$cancelledOrder->id)
            ->groupBy('date')->orderBy('date', 'ASC')
            ->get([
            DB::raw('Date(order_date) as date'),
            DB::raw('COUNT(*) as "orders"'),
        ]);
//
        Log::info('Orders order Chart' .print_r($orders,true));

//


        if (!$orders->isEmpty()) {
            $sales = []; $dates=[];

            foreach ($orders as $order) {
                array_push($dates,$order->date);
                array_push($sales,$order->orders);
            Log::info('Orders Date : '.$order->date. ' Orders : '.$order->orders);
            }
            Log::info('Dates'.print_r($dates,true) );
            Log::info('Sales'.print_r($sales,true) );

            if($sales)
            $lineChart = Charts::create('line', 'highcharts')
                ->title('Order Chart')
                ->elementLabel('Orders')
                ->labels($dates)
                ->values($sales)
                ->dimensions(800, 500)
                ->responsive(false);

            return $lineChart->render();

        }
        else{
            Log::info('No orders in range');
            return "No Records To Display";
        }





    }



}
