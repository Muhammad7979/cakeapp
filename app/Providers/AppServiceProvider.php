<?php

namespace App\Providers;

use App\Order;
use App\OrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        View::composer('layouts.admin',function($view)
        {

            $fromDate= Carbon::now()->startOfDay()->format('Y-m-d');
            $toDate=Carbon::now()->addDay()->format('Y-m-d');

            $processedOrder= OrderStatus::where('name','=','Processed')->first();
            $unProcessedOrder= OrderStatus::where('name','=','Un-Processed')->first();
            $cancelledOrder= OrderStatus::where('name','=','Cancelled')->first();

            $totalOrders            = Order::whereBetween('order_date',array($fromDate,$toDate))->count();

            Log::info("TOTAL ORDERS".$totalOrders.' FROM DATE '.$fromDate. ' TO DATE '. $toDate);

            $processedOrderCount    = Order::where('order_status','=',$processedOrder->id)->whereBetween('order_date',array($fromDate,$toDate))->count();
//            $processedOrderCount = number_format(round($processedOrderCount/$totalOrders*100),0);


            $unProcessedOrderCount  = Order::where('order_status','=',$unProcessedOrder->id)->whereBetween('order_date',array($fromDate,$toDate))->count();
//            $unProcessedOrderCountPer= number_format(round($unProcessedOrderCount/$totalOrders*100),0);

            $cancelledOrderCount     = Order::where('order_status','=',$cancelledOrder->id)->whereBetween('order_date',array($fromDate,$toDate))->count();
//            $cancelledOrderCount= number_format(round($cancelledOrderCount/$totalOrders*100),0);

            $view->with('processedCount',$processedOrderCount)->with('unProcessedCount',$unProcessedOrderCount)->with('cancelledOrderCount',$cancelledOrderCount)->with('totalOrders',$totalOrders);


        });


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
