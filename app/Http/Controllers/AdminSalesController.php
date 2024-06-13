<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Configuration;
use App\Order;
use App\OrderStatus;
use App\PaymentType;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Psy\Util\Json;


class AdminSalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Gate::allows('view-sales')) {

            $sales =  $orders= Order::join('order_product','orders.order_number','=','order_product.order_number')->select('orders.*','order_product.product_name')->paginate(15);;
            try {
                $branchName = Configuration::where('key', '=', 'branch_name')->first();
                $cancelledOrder = OrderStatus::where('name', '=', 'Cancelled')->first();
                $branchCode = Configuration::where('key', '=', 'branch_Code')->first();
                }catch (\Exception $exception)
            {
                Log::error("Branch Code Not Set In Configuration");
                Session::flash('error', 'Branch Code Not Set in Configuration (Some features may not Work Properly if left unattended)');
            }
            try {
                $branches= Branch::pluck('name','code')->all();
                $paymentTypes = PaymentType::pluck('name', 'id')->all();
                $orderStatus = OrderStatus::pluck('name', 'id')->all();
                $paymentStatus = Configuration::where('key', '=', 'Payment_Status')->pluck('label', 'value')->toArray();
                }catch (\Exception $exception)
            {
                Log::error("Order Type and Payment Status Not Set In Configuration");
                Session::flash('error', 'Order Type and Payment Status  Not Set in Configuration (Some features may not Work Properly if left unattended)');
            }
            $orders_am=  DB::select('select sum(o.advance_price) as sale,max(pending_amt.pending_amount) as pending_amount,o.order_date from orders as o left join (select sum(pending_amount) as pending_amount,pending_amount_paid_date from orders where pending_amount_paid_branch= :branchName group by pending_amount_paid_date ) as pending_amt on o.order_date = pending_amt.pending_amount_paid_date where  o.branch_code= :branchCode and o.order_status <> :cancelId group by o.order_date', ['branchName'=>$branchName->value,'branchCode'=>$branchCode->value,'cancelId'=>$cancelledOrder->id]);
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

            return view('admin.reports.sales', compact('sales','branchName','paymentTypes','orderStatus','totalSale','branches','paymentStatus'));
        }else{
            return redirect('admin');
        }
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
    public function search()
    {
//


        $fromDate=     Input::post('from_date');
        $toDate=     Input::post('to_date');
        $searchId=     Input::post('search_id');
        $searchProductName=     Input::post('search_productName');
        $searchBranchCode=     Input::post('search_branchCode');
        $orderStatus=     Input::post('order_status');
        $paymentType=     Input::post('payment_type');
        $paymentStatus=     Input::post('payment_status');


        $query = Order::
             join('order_product','orders.order_number','=','order_product.order_number')
            ->with('orderStatus')
            ->with('paymentType')
            ->with('photo');





        if(!empty($fromDate))
        {
            $convertedFromDate = Carbon::parse($fromDate);
            $query->whereDate('orders.order_date','>=',$convertedFromDate);
        }
        if(!empty($toDate))
        {
            $convertedToDate = Carbon::parse($toDate);

            $query->whereDate('orders.order_date','<=',$convertedToDate);
        }

        if(!empty($searchId))
        {
            $query->where('orders.order_number','=',$searchId);

        }
        if(!empty($searchBranchCode))
        {
            $query->where('branch_code','=',$searchBranchCode);

        }

        if($orderStatus!=0)
        {
            $query->where('order_status','=',$orderStatus);
        }
        if($paymentType!=0)
        {
            $query->where('payment_type','=',$paymentType);
        }
        if($paymentStatus != -1)
        {
            Log::info('priority');
            $query->where('payment_status','=',$paymentStatus);
        }


        $data= $query->get();

        if(!empty($searchProductName)) {
            $data = collect($data)->filter(function ($order) use ($searchProductName) {
                return false !==
                    stristr($order->product_name, $searchProductName);
            });
        }
        return $data;



    }


    public function generateCsv(Request $request)
    {

        $fromDate=         $request->input('from_date');
        $toDate=           $request->input('to_date');
        $searchId=          $request->input('search_id');
        $searchProductName=      $request->input('search_productName');
        $searchBranchCode=      $request->input('search_branchCode');
        $orderStatus=      $request->input('order_status');
        $paymentType=      $request->input('payment_type');
        $paymentStatus=     $request->input('payment_status');

        $query = Order::
        join('order_product','orders.order_number','=','order_product.order_number')
            ->with('orderStatus')
            ->with('paymentType');





       if(!empty($fromDate))
       {
           $convertedFromDate = Carbon::parse($fromDate);
           $query->whereDate('orders.order_date','>=',$convertedFromDate);
       }
       if(!empty($toDate))
       {
           $convertedToDate = Carbon::parse($toDate);

           $query->whereDate('orders.order_date','<=',$convertedToDate);
       }
        if(!empty($searchId))
        {
            $query->where('orders.order_name','=',$searchId);

        }
        if(!empty($searchBranchCode) )
        {
            Log::info('branch code '.$searchBranchCode);
            $query->where('branch_code','=',$searchBranchCode);

        }
       if($orderStatus!=0)
       {
           $query->where('order_status','=',$orderStatus);
       }
       if($paymentType!=0)
       {
           $query->where('payment_type','=',$paymentType);
       }
        if($paymentStatus != -1)
        {
            Log::info('priority');
            $query->where('payment_status','=',$paymentStatus);
        }

        if(!empty($searchProductName)) {

                $query->where('product_name','like','%'.$searchProductName.'%');

        }


            $data= $query->get();




//

        try {
            $csvExporter = new \Laracsv\Export();

            $csvExporter->beforeEach(function ($data) {

                $branchCode = Configuration::where('key', '=', 'branch_Code')->first();
                $branch_name = Configuration::where('key', '=', 'branch_name')->first();

                // Now notes field will have this value
                $data['productName'] = $data->product_name;
                $data['orderStatus'] = $data->orderStatus->name;
                $data['paymentType'] = $data->paymentType->name;
                $data['orderDate'] =Carbon::parse($data->order_date)->format('d-m-Y') ;
                $data['orderTime'] =Carbon::parse($data->created_at)->format('d-m-Y g:ia') ;

                Log::info('GENERATE CSV  : '. 'P.S : '.$data->payment_status. ' B.C : '.$data->branch_code. ' D.B.C '.$branchCode->value  . ' P.B :  '.$data->pending_amount_paid_branch. ' D.B.N' . $branch_name);

                if($data->payment_status==1 && $data->branch_code == $branchCode->value && $data->pending_amount_paid_branch == $branch_name->value)
                {
                    $data['paymentStatus'] = "Paid";
                    $data['sale_amount']= $data->advance_price + $data->pending_amount;
                }
                else  if($data->payment_status==1 && $data->branch_code == $branchCode->value && $data->pending_amount_paid_branch != $branch_name->value)
                {
                    $data['paymentStatus'] = "Paid";
                    $data['sale_amount']= $data->advance_price;
                }
                else  if($data->payment_status==1 && $data->branch_code != $branchCode->value && $data->pending_amount_paid_branch == $branch_name->value)
                {

                    $data['paymentStatus'] = "Paid";
                    $data['sale_amount']= $data->pending_amount;
                }
                else if($data->payment_status!=1)
                {
                    $data['paymentStatus'] = "Pending";
                    $data['sale_amount']= $data->advance_price;
                }
            });



            $csvExporter->build($data, ['order_number' => 'Order Id', 'productName' => 'Product Name', 'branch_code' => 'Branch Code', 'sale_amount' => 'Amount','paymentStatus'=>"Payment Status", 'orderStatus' => 'Order Status', 'paymentType' => 'Payment Type', 'orderDate' => 'Order Date', 'orderTime'=>'Order Time']);
            $csvExporter->download();

        }catch (\Exception $exception)
        {
            Log::error("Error Generating CSV".$exception->getMessage());

        }






        }
        public function searchReset()
        {
            $sales = Order::
            join('order_product','orders.order_number','=','order_product.order_number')
            ->with('orderStatus')
            ->with('paymentType')
            ->with('photo')->get();

            return $sales;
        }



}
