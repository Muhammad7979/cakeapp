<?php

namespace App\Http\Controllers;

use App\Item;
use App\Order;
use App\Flavour;
use App\PosSale;
use Carbon\Carbon;
use App\OrderStatus;
use App\PosSaleTemp;
use App\PosSaleItems;
use App\Configuration;
use App\CakePosItemTemp;
use App\CakeSuspended;
use App\ItemTax;
use App\PosSaleItemsTaxesTemp;
use App\PosSalePayments;
use App\PosSaleItemsTemp;
use Illuminate\Support\Str;
use App\PosSalePaymentsTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BakemanController extends PosSaleController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($param = null)
    {
        //
        ($param)?$order_type = 'pos': $order_type = 'cake';
        
        if (Gate::allows('bakeman-view')) {
            try {
                $past_order_range = Configuration::where('key', '=', 'Past_Order_Range')->first();
                $order_date_access = Configuration::where('key', '=', 'Bakeman_order_date_access')->first();
                $unProcessedOrder = OrderStatus::where('name', '=', 'Un-Processed')->first();
                $branch_code = Configuration::where('key', '=', 'branch_Code')->first();
            } catch (\Exception $exception) {
                Log::error('Error Getting Configurations from database ' . $exception->getMessage());
            }

            $fromDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            if($param == 'positem'){
                $orders = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')->select('orders.*', 'order_product.product_name')->orderBy('orders.id', 'desc')
                ->where(['assigned_to' => $branch_code->value , 'is_cake'=>'0'])
                ->paginate(15);
            }else{
                $orders = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')->select('orders.*', 'order_product.product_name')->orderBy('orders.id', 'desc')
                ->where(['assigned_to' => $branch_code->value , 'is_cake'=>'1'])
                ->paginate(15);
            }
          

            foreach ($orders as $order) {



                if ($order->sale) {
                    $order->withcake = "Cake with POS Items";
                }
            }


            $explode_id = explode(',', $order_date_access->value);

            if (in_array(1, $explode_id)) {
                $display_date_past = 1;
            } else {
                $display_date_past = 0;
            }
            if (in_array(2, $explode_id)) {
                $display_date_future = 1;
            } else {
                $display_date_future = 0;
            }
            $date_range = $past_order_range->value;


            return view('bakeman.index', compact('orders', 'display_date_past', 'display_date_future', 'date_range','order_type'));
        } else {
            return redirect('/');
        }

    }

    public function setOrderStatus()
    {
        $orderId = Input::post('order_id');
        $orderStatus = Input::post('order_status');


        try {
            $order = Order::findOrFail($orderId);
        } catch (ModelNotFoundException $exception) {
            return "Order Not Found ID" . $orderId;
        }

        $order->order_status = $orderStatus;

        //            try {
//                        $order_status = OrderStatus::where('id', '=', $orderStatus)->first();
//
////                        if ($order_status->name == 'Delivered') {
//////                            $order->assigned_to = $order->branch_code;
////
////                        }
//            }catch (\Exception $exception)
//            {
//                return "Order Status not found";
//            }
        $order->live_synced = '0';
        $order->server_sync = '0';
        $order->save();

        return "Order Status Updated";



    }
    public function getOrderInformation()
    {
        $search_type = Input::post('search_type');
        $fromDate = Input::post('from_date');
        $toDate = Input::post('to_date');
        $searchId = Input::post('search_id');
        $phone = Input::post('phone');


        /*
         * With Compulsary Date Check
         */
        //        $query = Order::whereBetween('orders.created_at', [$convertedFromDate, $convertedToDate])
//            -> join('order_product','orders.order_number','=','order_product.order_number')
//            ->with('orderStatus')
//            ->with('paymentType')
//            ->with('photo');
        $branchCode = Configuration::where('key', '=', 'branch_code')->first();
        $unProcessedOrder = OrderStatus::where('name', '=', 'Un-Processed')->first();




        $query = Order::with('sale')
            ->where('assigned_to', '=', $branchCode->value)

            // ->where('orders.order_status', '=', $unProcessedOrder->id)
            ->join('order_product', 'orders.order_number', '=', 'order_product.order_number')
            ->with('photo')
            ->with('orderStatus');

        ////       $query =DB::table('orders')->select(['id','branch_code','total_price','order_status','payment_type','created_at','salesman']);
//
        if (!empty($fromDate)) {
            Log::info('from date');
            $convertedFromDate = Carbon::parse($fromDate);
            $query->whereDate('orders.delivery_date', '>=', $convertedFromDate);
        }
        if (!empty($toDate)) {
            Log::info('to date');
            $convertedToDate = Carbon::parse($toDate);
            //            $convertedToDate = $convertedToDate->addDays(1);
            $query->whereDate('orders.delivery_date', '<=', $convertedToDate);
        }
        if (!empty($searchId)) {
            Log::info('order id');
            $query->where('orders.order_number', 'LIKE', '%' . $searchId);

        }
        if (!empty($phone)) {
            Log::info('phone');
            $query->where('orders.customer_phone', '=', $phone);
        }

        $withcake = "";
        if($search_type == 'pos'){
            $orders = $query->where('is_cake','0')->get();
            $withcake = "Only POS Item";
        }else{
            $orders =  $query->where('is_cake','1')->get();
            $withcake = "Cake with POS Item";

        }

        // dd($orders);
        // foreach ($orders as $order) {



        //     if ($order->sale()) {
        //     } else {
        //         $withcake = "";
        //     }
        // }


        //
        $data = $query->orderBy('orders.delivery_date', 'desc')
            ->orderBy('orders.priority', 'asc')
            ->select('orders.*', 'order_product.product_name')
            ->get();

        //
        // $orders = $data->pluck('orders.*');



        // dd($withcake);
        $data = response()->json(['data' => $data, "withcake" => $withcake]);
        return $data;
        // return response()->json(['data' => $data, "withcake" => $withcake]);


    }

    public function reports($id = null)
    {
        if (Gate::allows('bakeman-view')) {
            try {
                $past_order_range = Configuration::where('key', '=', 'Past_Order_Range')->first();
                $order_date_access = Configuration::where('key', '=', 'Bakeman_order_date_access')->first();
                $unProcessedOrder = OrderStatus::where('name', '=', 'Un-Processed')->first();
                $branch_code = Configuration::where('key', '=', 'branch_Code')->first();
            } catch (\Exception $exception) {
                Log::error('Error Getting Configurations from database ' . $exception->getMessage());
            }



            $orders = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')
                ->where('assigned_to', '=', $branch_code->value)
                ->where('orders.order_status', '=', $unProcessedOrder->id)
                ->orderBy('orders.delivery_date', 'desc')
                ->orderBy('orders.priority', 'desc')
                ->select('orders.*', 'order_product.product_name')
                ->paginate(15);

            $explode_id = explode(',', $order_date_access->value);

            if (in_array(1, $explode_id)) {
                $display_date_past = 1;
            } else {
                $display_date_past = 0;
            }
            if (in_array(2, $explode_id)) {
                $display_date_future = 1;
            } else {
                $display_date_future = 0;
            }
            $date_range = $past_order_range->value;
            $cake_invoice = $id;

            return view('bakeman.reports.orders_report', compact('orders', 'display_date_past', 'display_date_future', 'date_range','cake_invoice'));
        } else {
            return redirect('/');
        }
    }
 
    public function searchOrderNumber()
    {
        $searchId = Input::post('search_id');
        $phoneNumber = Input::post('phone_number');
        if (!empty($searchId) || !empty($phoneNumber)) {

            // Get the Un-Processed order status
            $unProcessedOrder = OrderStatus::where('name', 'Un-Processed')->first();
            $branchCode = Configuration::where('key', '=', 'branch_code')->first();
            // Start building the query
            $query = Order::with('sale')
                ->where('assigned_to', '=', $branchCode->value)
                // ->where('orders.order_status', $unProcessedOrder->id)
                ->join('order_product', 'orders.order_number', '=', 'order_product.order_number')
                ->with('photo')
                ->with('orderStatus');
            
            // Check if a search ID is provided
            if (!empty($searchId)) {
                $query->where('orders.order_number', 'LIKE', '%' . $searchId . '%');

            }


            if (!empty($phoneNumber)) {
                $query->where('orders.customer_phone', 'LIKE', '%' . $phoneNumber . '%');
            }

            // Fetch the data
            $ordersData = $query->select('orders.*', 'order_product.product_name')
                ->get();

            // Separate order and sales data
            $orders = $ordersData->toArray();

            $sales = $ordersData->pluck('sale')->toArray();

            if ((count($orders) > 0) && $sales[0] != null) {



                $items = null;
                $payments = null;
                foreach ($sales as $s) {

                    // $items = PosSaleItems::where('sale_id', $s['sale_id'])->get();
                    $items = PosSaleItems::where('sale_id', $s['sale_id'])
                        ->join('items', 'pos_sales_items.item_id', '=', 'items.item_id')
                        ->select('pos_sales_items.*', 'items.name', 'items.category')
                        ->get();


                    $payments = PosSalePayments::where('sale_id', $s['sale_id'])->
                        get();

                }


                foreach($items as $item){
                    $itemTax = ItemTax::where('item_id', $item['item_id'])->first();

                    if ($itemTax) {
                        $tax_percent = $itemTax->value('percent');
                    } else {
                        $tax_percent = 0.000; // Set tax_percent to 0 if no item tax found
                    }

                    // Store tax percentage for this item in the array
                   $taxPercentages[] = $tax_percent;
                }
                $averageTaxPercent = count($taxPercentages) > 0 ? bcdiv(array_sum($taxPercentages) , count($taxPercentages),2) : 0;

                $payment_data = null;

                $order_number = null;

                // Iterate through orders array
                foreach ($orders as $order) {
                    $order_number = $order['order_number'];
                    // Add order total price to payment_data array
                    $payment_data = ["pending_amount" => $order['pending_amount'], 'total_price' => $order['total_price'], 'advance_price' => $order['advance_price'],'total_tax'=> $averageTaxPercent];
                }


                // Prepare the response data
                $responseData = [
                    'orders' => $orders,
                    'sales' => $sales,
                    'items' => $items,
                    'payments' => $payments,
                    'payment_data' => $payment_data,
                    'order_number' => $order_number,
                    'status' => "Success"

                ];


                return response()->json($responseData);
            } else {

                $data = ["status" => "Error", "statusMessage" => "No Record Found"];

                return response()->json($data);

            }


        } else {

            $data = ["status" => "Error", "statusMessage" => "No Record Found"];

            return response()->json($data);

        }


        // Return the response as JSON
    }



    public function getOrderDetails()
    {

        $orderId = Input::post('orderId');
        try {
            $order = Order::with('sale')->where('order_number', 'like', '%' . $orderId . '%')->first();

            if ($order && $order->sale && $order->sale->order_number !== null) {
                return response()->json([$order->sale]);
            } else {
                return response()->json([]);
            }

        } catch (\Exception $exception) {
            Log::error("Order Details Not Found " . $exception->getMessage());
            //            return back()->withError("Fail To find Order Materials".$id);
        }
    }


    public function generate_invoice_number($code = null)
    {

        if (empty($code)) {
            $code = now()->format('YmdHis');
        }

        return env('BRANCH_CODE') . "-" . env('SYSTEM_CODE') . $code . mt_rand(100, 999);

    }


    public function reOrderSale(Request $request)
    {
        $ordernumber = $request->order_number;
        $total_tax = $request->total_tax;

        $order_exist = CakeSuspended::where(['cake_invoice'=>$ordernumber,'second_payment'=>1])->count();
        if($order_exist > 0){
            return response()->json(['status' => 'Success', 'message' => 'This order already in queue.'], 200);
        }else{

        $cake_order_info = Order::where('order_number', $ordernumber)->first();


        $dateTime = now()->format('Y-m-d H:i:s');
        $order_id = $cake_order_info->id;
        $payment_type = "Cash";
        // $total = $cake_order_info->pending_amount;
        $employee_id = auth()->user()->id;
        $branchcode = strtolower(str_replace('-', '', env("BRANCH_CODE")));
        $comment = '';
        $data['fbr_fee'] = Configuration::where('key', 'fbr_fee')->value('value');
        $total = bcadd($request->total_price,$data['fbr_fee']);
        $invoice_number = $this->generate_invoice_number();
        $cake_invoice_number = $cake_order_info->order_number;


        $cake_item[0] = [
            "item_id" => 4,
            "name" => "MIX",
            "category" => "MIX",
            "supplier_id" => null,
            "item_number" => "1",
            "description" => "",
            "cost_price" => $total,
            "unit_price" => $total,
            "reorder_level" => "0.000",
            "receiving_quantity" => "1.000",
            "pic_id" => null,
            "allow_alt_description" => 0,
            "is_serialized" => 1,
            "deleted" => 0,
            "custom1" => "no",
            "custom2" => "",
            "custom3" => "",
            "custom4" => "",
            "custom5" => "",
            "custom6" => "",
            "custom7" => "",
            "custom8" => "",
            "custom9" => "",
            "custom10" => "yes",
        ];


        $payments = [
            "payment_type" => $payment_type,
            "payment_amount" => $total
        ];

        $sales_data = [
            'order_id' => $order_id,
            'exact_time' => $dateTime,
            'sale_time' => now()->format('Y-m-d'),
            'customer_id' => null,
            'employee_id' => $employee_id,
            'sale_type' => 'normal',
            'sale_payment' => $payment_type,
            'branch_code' => $branchcode,
            'comment' => $comment,
            'fbr_fee' => $data['fbr_fee'],
            'invoice_number' => $invoice_number,
            // 'fbr_invoice_number' => $fbr_invoice_number,
            'cake_invoice' => $cake_invoice_number,
            'second_payment'=> 1,

        ];


        try {
            // SALES TABLE save

            DB::beginTransaction();

            $newSale = PosSaleTemp::create($sales_data);
            $sale_id = $newSale->sale_id;

            // PAYMENTS TABLE save

            $payments['sale_id'] = $sale_id;
            PosSalePaymentsTemp::create($payments);

            //SALES ITEMS save

            foreach ($cake_item as $line => $item) {

                $cur_item_info = Item::join('items_taxes', 'items.item_id', '=', 'items_taxes.item_id')
                    ->join('item_quantities', 'items.item_id', '=', 'item_quantities.item_id')
                    ->where('items.item_id', $item['item_id'])
                    ->select('items.*', 'items_taxes.*', 'item_quantities.*')
                    ->first();

                $line += 1;
                $serialnumber = '';
                $discount = '0';
                $sales_items_data = [
                    'sale_id' => $sale_id,
                    'item_id' => $item['item_id'],
                    'line' => $line,
                    'description' => Str::limit($item['description'], 30),
                    'serialnumber' => Str::limit($serialnumber, 30),
                    'quantity_purchased' => $item['receiving_quantity'],
                    'discount_percent' => $discount,
                    'item_cost_price' => $item['cost_price'],
                    // 'item_unit_price' => $item['unit_price'],
                    'item_unit_price' => $item['unit_price'],
                    'item_location' => '1'
                ];

                $taxes = [
                    'sale_id'=> $sale_id,
                    'item_id'=> $item['item_id'],
                    'line' => $line,
                    'name' => 'Total Tax',
                    'percent' => $total_tax,
                ];

                PosSaleItemsTemp::create($sales_items_data);
                PosSaleItemsTaxesTemp::create($taxes);


            }
              
                // Order::where('id',$order_id)->update(['order_status'=>4]);


               DB::commit();


                $this->uploadRecent($sale_id);

       
            return response()->json(['status' => 'Success', 'message' => 'Re-order Successfully'], 200);

        } catch (\Exception $e) {

            DB::rollback();

            // Log the exception
            Log::error($e->getMessage());

            // Return a generic error message
            return response()->json(['error' => 'Success', 'message' => 'An unexpected error occurred'], 500);

        }
    }

    }







}
