<?php

namespace App\Http\Controllers;

use App\Branch;
use App\CakePosItem;
use App\CakePosItemTemp;
use App\Configuration;
use App\Flavour;
use App\FlavourCategory;
use App\Http\Requests\StoreOrderRequest;
use App\Item;
use App\ItemTax;
use App\Material;
use App\Order;
use App\OrderStatus;
use App\OrderType;
use App\PaymentType;
use App\Photo;
use App\PosSale;
use App\PosSaleItemKitItems;
use App\PosSaleItems;
use App\PosSaleItemsTemp;
use App\PosSalePayments;
use App\PosSalePaymentsTemp;
use App\PosSaleTemp;
use App\Product;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use http\Env\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AdminOrderController extends PosSaleController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Gate::allows('view-order')) {

//            $orders = Order::with('products')->get();
            // just for testing purposes

//            $orders = Order::where('branch_code','=',env('BRANCH_CODE'))->
//                            orWhere('assigned_to','=',env('BRANCH_CODE'))->paginate(15);
            $branch_code = Configuration::where('key', '=', 'branch_Code')->first();
            //for local
           // $orders = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')->where('assigned_to', '=', $branch_code->value)->select('orders.*', 'order_product.product_name')->paginate(15);


            $orders = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')->select('orders.*', 'order_product.product_name')->orderBy('orders.id', 'desc')->paginate(15);
//            $orders= Order::join('order_product','orders.id','=','order_product.order_id')->leftJoin('flavour_order','orders.id','=','flavour_order.order_id')->select('orders.*','order_product.product_name','flavour_order.*')->groupBy('flavour_order.flavour_name')->get();

            try {
                $priority = Configuration::where('key', '=', 'Priority_key')->pluck('label', 'value')->toArray();
                $branches = Branch::pluck('name', 'code')->all();
                $paymentTypes = PaymentType::pluck('name', 'id')->all();
                $orderStatus = OrderStatus::pluck('name', 'id')->all();

            } catch (\Exception $exception) {
                Log::error('Priority , Branches, PaymentType or Order Statys missing order index Page' . $exception->getMessage());
            }
//            dd($orders);

//            return $orders;
            return view('admin.orders.index', compact('orders','paymentTypes','orderStatus','branches','priority'));
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
//        if(Gate::allows('create-order')) {
        $products = Product::where('is_active','=','1')->get();
        $priority = Configuration::where('key','=','Priority_key')->pluck('label','value')->toArray();
        $branchId=Configuration::where('key','=','branch_id')->first();
        $paymentTypes = PaymentType::pluck('name','id')->all();
        $orderTypes = OrderType::pluck('name','id')->all();
        $flavourCategories= FlavourCategory::pluck('name','id')->all();

        return view('admin.orders.create',compact('products','flavourCategories','paymentTypes','orderTypes','priority','branchId'));
        //}
//        else
//        {
//            return redirect('admin');
//        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        //
        if(Gate::allows('create-order')) {
            $branchCode=Configuration::where('key','=','branch_Code')->first();
            $latestId = Order::latest('id')->first();
            if(is_null($latestId) || empty($latestId))
            {
                $lastId =0;
            }else
            {
                $lastId=(int)$latestId->id;
            }
            $input = $request->except(['flavour_id','material_id','product_id']);
            $productId= array($request->input('product_id'));
            $orderStatus = OrderStatus::where('name','=',$request->input('order_status'))->first();
            $input['order_status']=$orderStatus->id;
            $input['server_sync']=0;
            $input['order_number']=$branchCode->value."-".((int)$lastId+1);
            $deliverDate = $request->input('delivery_date');
            $deliverDate = Carbon::parse($deliverDate);
            $input['delivery_date'] = $deliverDate;

            $photo_path = Photo::findOrFail($input['photo_id']);


            $input['photo_path']=$photo_path->path;
            $order = Order::Create($input);

            $flavourId= $request->input('flavour_id');
            $materialId=$request->input('material_id');
            if($order->wasRecentlyCreated)
            {
                foreach ($productId as $id) {
                    $product = Product::findOrFail($id);
                    $name=$product->name;
                    $price=$product->price;
                    $order->products()->sync([$product->sku =>['product_name' => $name,'product_price'=>$price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }



                foreach ($flavourId as $fid) {
                    $flavour = Flavour::findOrFail($fid);
                    $name=$flavour->name;
                    $price=$flavour->price;
                    $order->flavours()->attach([$flavour->sku =>['flavour_name' => $name,'flavour_price'=>$price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }
                foreach ($materialId as $mid) {
                    $material = Material::findOrFail($mid);
                    $name=$material->name;
                    $price=$material->price;
                    $order->materials()->attach([$material->sku =>['material_name' => $name,'material_price'=>$price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }

//

                Session::flash('created_order', 'Order Created');
                return redirect('admin/orders');
            }
            else{
                Session::flash('created_order', 'Product Already Exits');
                return redirect('admin/orders');

            }



//
        }
        else
        {
            return redirect('admin');
        }
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
        if (Gate::allows('view-order')) {

//            $order = Order::with('products')->findOrFail($id);
            try {
                $order = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')->select('orders.*', 'order_product.product_name')->findOrFail($id);
                }
                catch (\Exception $exception)
                {
                    Log::error("Order Not Found While Edit".$exception->getMessage());
                    return back()->withError("Fail To find Order ".$id);
                }
            //            $order = Order::join('order_product','orders.order_number','=','order_product.order_number')->select('orders.*','order_product.product_name')->where('id','=',$id)->get();

            try {
                $flavours = Order::join('flavour_order', 'orders.order_number', '=', 'flavour_order.order_number')->where('orders.order_number', '=', $id)->pluck('flavour_name', 'flavour_sku')->toArray();
                $flavour_ids = Order::join('flavour_order', 'orders.order_number', '=', 'flavour_order.order_number')->where('orders.order_number', '=', $id)->pluck('flavour_sku')->toArray();
                }catch (\Exception $exception)
                {
                    Log::error("Order Flavours Not Found While Edit".$exception->getMessage());
                    return back()->withError("Fail To find Order Flavours".$id);
                }

                try {

/*
 *I MAY HAVE TO CHANGE THIS LOGIC
 */
                    $products = Product::where('is_active', '=', '1')->get();


                    }catch (\Exception $exception)
                    {
                        Log::error("Order Product Not Found While Edit".$exception->getMessage());
                        return back()->withError("Fail To find Order Product ORDER ID".$id);
                    }


                    try {
                        $priority = Configuration::where('key', '=', 'Priority_key')->pluck('label', 'value')->toArray();
                        $paymentStatus = Configuration::where('key', '=', 'Payment_Status')->pluck('label', 'value')->toArray();
                        }catch (\Exception $exception)
                    {
                        Log::error("Payment_Status and Priority Error in Edit".$exception->getMessage());
                        return back()->withError("Fail To Find priority and Payment Status in Configuration");
                    }


        try {
            $branchId = Configuration::where('key', '=', 'branch_id')->first();
            }catch (\Exception $exception)
                {
                    Log::error("Cannot Find Branch Id in Configuration".$exception->getMessage());
                    return back()->withError("Cannot Find Branch Id in Configuration");
                }

            try {
            $paymentTypes = PaymentType::pluck('name','id')->all();
            $orderTypes = OrderType::pluck('name','id')->all();
            $orderStatus = OrderStatus::pluck('name','id')->all();
            }catch (\Exception $exception)
            {
                Log::error("Payment_Type, Order_Type and Order Status Error in Edit".$exception->getMessage());
                return back()->withError("Fail To Find Payment_Type, Order_Type Or Order Status  in Configuration");
            }

            try {
                $materials = Order::join('material_order', 'orders.order_number', '=', 'material_order.order_number')->where('orders.order_number', '=', $id)->pluck('material_name', 'material_sku')->toArray();
                $material_ids = Order::join('material_order', 'orders.order_number', '=', 'material_order.order_number')->where('orders.order_number', '=', $id)->pluck('material_sku')->toArray();
                }
                catch (\Exception $exception)
                {
                    Log::error("Order Materials Not Found While Edit".$exception->getMessage());
                    return back()->withError("Fail To find Order Materials".$id);
                }

                try{
                        $order_completion_status = OrderStatus::where('id','=',$order->order_status)->first();
                        /*
                         * To allow admin to change status even after status is changed to Cancelled  remove cancelled from if statement
                         */
                        if ($order_completion_status->name == "Delivered")
                        {
                            $lock=1;
                        }else{
                            $lock=0;
                        }
                }catch (\Exception $exception)
                {
                    Log::info('Order Status Not Set in Database'.$exception->getMessage());
                }
//

            return view('admin.orders.edit', compact('order', 'products', 'priority', 'paymentStatus','branchId','paymentTypes','orderTypes','orderStatus','flavours','materials','flavour_ids','material_ids','lock'));
        }
        else
        {
            return redirect('admin');
        }
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
        if (Gate::allows('update-order')) {

            try{
            $order = Order::findOrFail($id);
                }catch (ModelNotFoundException $exception)
            {
                return back()->withError(" Order Not Found By ID".$id);
            }
            // use the below code if you have allowed the user to skip the password field for updation  by creating a new edit request
            //this will assign all the fields except password if it is not set in the edit page
            // REMOVE THE bcrypt password statment from end ...if using this method
//        if(trim($request->password) =='')
//        {
//            $input=$request->except('password');
//        }else
//        {
//            $input = $request->all();
            //  $input['password']=bcrypt($request->password);
//        }











                $input=$request->all();

//                $order_status = OrderStatus::where('id','=',$input['order_status'])->first();
//                if($order_status->name=='Delivered')
//                {
//                    $input['assigned_to']=$order->branch_code;
//                }




            if ($file = $request->file('final_image')) {


//                $name = time() .  str_replace(' ', '',$file->getClientOriginalName());
                $name = $request->input('order_number') .time(). '.' . $file->getClientOriginalExtension();
                Log::info('image added'.$name);
                $file->move('images/Created_Order_Images/', $name);

                Log::info('image added');
                if ($order->final_image == "" || $order->final_image == null) {
                    $photo = Photo::create(['path' => $name]);
//                    $input['photo_id'] = $photo->id;
                    $input['final_image'] = $name;

//                    $unlink = Configuration::where('key', '=', 'Unlink_product_image')->first();
//
//                    if($unlink->count()>0) {
//
//                        if ($unlink->value == 1) {
//                            unlink(public_path() . 'images/Created_Order_Images/' . $order->final_image);
//                        }
//                    }

                } else {

                    Log::info(''.$order->final_image);

                    $photo = Photo::where('path','=',$order->final_image)->first();
                    $photo->path = $name;
                    Log::info(''.$order->final_image);
                    $photo->save();
//                $elseif = $user->photo_id;
                    $input['final_image'] = $photo->path;

                }

            }


            $input['delivery_date']=Carbon::parse($request->input('delivery_date'));
            $input['pending_amount_paid_date']=$order->pending_amount_paid_date;

            $order->update($input);


            Session::flash('updated_order', 'The order has been updated');
            return redirect('admin/orders');
        }
        else
        {

            return redirect('admin');
        }
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



    public function getflavourMaterial()
    {

        $productId=     Input::post('product_id');
        $catId=     Input::post('cat_id');
        $product = Product::findOrFail($productId);

        $flavours = $product->flavours->where('flavourCategory_id','=',$catId);
        $materials = $product->materials;

        return(array($flavours,$materials));

    }
    public function getOrderDetails()
    {
        $item_kits_info = null;
        $orderId = Input::post('orderId');
        $pos_sale_info = null;
        try {
//        $order_id = Order::where('order_number','=',$orderId);
            $order = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')->select('orders.*', 'order_product.product_name')->findOrFail($orderId);
//        $order = Order::join('order_product','orders.order_number','=','order_product.order_number')->select('orders.*','order_product.product_name')->where('orders.order_number','=',$orderId)->get();
            $flavours = Order::select('flavour_order.*')->join('flavour_order', 'orders.order_number', '=', 'flavour_order.order_number')->where('orders.order_number', '=', $orderId)->get();
            $materials = Order::select('material_order.*')->join('material_order', 'orders.order_number', '=', 'material_order.order_number')->where('orders.order_number', '=', $orderId)->get();

            $flavour_categories=array();

            foreach ($flavours as $flavour)
            {
                $o_flavour=Flavour::where('sku',$flavour->flavour_sku)->first();
                $category=$o_flavour->flavourCategory->name;

                $flavour['category_name']=$category;
            }


        //   $pos_sale = PosSaleTemp::with('items')->where('cake_invoice',$orderId)->first();

        //   if($pos_sale){

        //     $pos_sale_info = $pos_sale;
        //     foreach($pos_sale_info->items as $key=>$item){

        //         $item_name = Item::where('item_id',$item->item_id)->value('name');
    
        //         $pos_sale_info->items[$key]['name'] = $item_name;
    
        //       }

        //   }else{

        //   $pos_sale_info = PosSale::with('items')->where('cake_invoice',$orderId)->first();
        //   if($pos_sale_info){

        //   foreach($pos_sale_info->items as $key=>$item){

        //     $item_name = Item::where('item_id',$item->item_id)->value('name');

        //     $pos_sale_info->items[$key]['name'] = $item_name;

        //   }
        // }

        //   }


        $pos_sale = PosSale::with('items')->where('cake_invoice',$orderId)->first();

        if($pos_sale){

          $pos_sale_info = $pos_sale;
          foreach($pos_sale_info->items as $key=>$item){

              $item_name = Item::where('item_id',$item->item_id)->value('name');
  
              $pos_sale_info->items[$key]['name'] = $item_name;
  
            }

        }else{

        $pos_sale_info = PosSaleTemp::with('items')->where('cake_invoice',$orderId)->first();
        if($pos_sale_info){

        foreach($pos_sale_info->items as $key=>$item){

          $item_name = Item::where('item_id',$item->item_id)->value('name');

          $pos_sale_info->items[$key]['name'] = $item_name;

        }
      }

        }

            $order['image'] = $order->photo_path;
            $order['product_name'] = $order->product_name;

            $orderStatus = OrderStatus::all();

            if($order->product_name == 'Custom kit'){

                $item_kit_info = PosSaleItemKitItems::with('items')->with('kit')->where('cake_invoice',$orderId)->get();
                  foreach($item_kit_info as $key => $item){
    
                    $kit_name = $item['kit']['name']; 
                    $item_detail = [
                        'name' =>  $item['items']['name'],
                        'quantity' =>  $item['quantity'],
                        'price' =>$item['items']['unit_price'],
                        'kit_quantity' =>$item['kit_quantity']
                    ];
                    $item_kits_info[$kit_name][] = $item_detail;
                  }
                }

            return (array($order, $flavours, $materials, $orderStatus,$flavour_categories,$pos_sale_info,$item_kits_info));
        }catch (\Exception $exception)
        {
            Log::error("Order Details Not Found ".$exception->getMessage());
//            return back()->withError("Fail To find Order Materials".$id);
        }
    }
    public function setOrderStatus()
    {
        $orderId=     Input::post('order_id');
        $orderStatus=     Input::post('order_status');


            try {
                $order = Order::findOrFail($orderId);
                 }
                 catch (ModelNotFoundException $exception)
                    {
                        return "Order Not Found ID".$orderId;
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
    public function generatePdf($id)
    {
//        $orderId=     Input::post('order_id');
//        $order = Order::join('order_product','orders.order_number','=','order_product.order_number')->select('orders.*','order_product.product_name')->findOrFail($id);
     try {
              $item_kits = [];
              $pos_sale_items = null;
              $pos_sales_payments = null;
              $all_price_info = [];
              $positem = PosSaleTemp::where('cake_invoice', $id)->first();
           if ($positem) {
                 // The record exists, you can access the sale_id value
              $positem_sale_id = $positem->sale_id;

              $pos_sale_items = PosSaleItemsTemp::join('items','pos_sales_items_temp.item_id','=','items.item_id')
                                          ->select('items.name','items.item_number','pos_sales_items_temp.*')->where('pos_sales_items_temp.sale_id',$positem_sale_id)
                                          ->get();
              $pos_sales_payments = PosSalePaymentsTemp::where('sale_id', $positem_sale_id)->first();

              $all_price_info = $this->get_all_price_data($pos_sale_items->toArray());


               }

         $order = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')
                         ->select('orders.*', 'order_product.product_name')->findOrFail($id);
         if($order->is_cake == '0'){
            $item_kit_order = Order::with('orderProduct')->where('order_number',$id)->first();
            $item_kits =  $item_kit_order->orderProduct;
         }
         $flavours = Order::select('flavour_order.*')->join('flavour_order', 'orders.order_number', '=', 'flavour_order.order_number')->where('orders.order_number', '=', $id)->get();
//          $flavours= Order::where('order_number',$id)->with('flavours')->get();
         $flavour_categories=array();

         foreach ($flavours as $flavour)
         {
             $o_flavour=Flavour::where('sku',$flavour->flavour_sku)->first();
             $category=$o_flavour->flavourCategory->name;

             array_push($flavour_categories,$category);
         }
         $materials = Order::join('material_order', 'orders.order_number', '=', 'material_order.order_number')->where('orders.order_number', '=', $id)->get();
         $branchId = Configuration::where('key', '=', 'branch_id')->first();
         $branchName = Configuration::where('key', '=', 'branch_name')->first();
         $branchNumber = Configuration::where('key', '=', 'branch_number')->first();

         $assignedBranch = Branch::where('code','=',$order->assigned_to)->first();
         $payment_status = Configuration::where('key','=','Payment_Status')->where('value','=',$order->payment_status)->first();


       $data= array($order, $branchName,$branchId,$branchNumber,$flavours,$materials,$assignedBranch,$payment_status,$flavour_categories);
     

       $barcode = new DNS1D();
       $barcodeData = $barcode->getBarcodePNG('cake'.$order->id, 'C39');
       
       // Build the HTML to display the barcode image
       $barcodeImage = '<img src="data:image/png;base64,'.$barcodeData.'" alt="barcode" />';
    
           if ($positem) {
            $positem_sale_id = $positem->sale_id; 
            $this->uploadRecent($positem_sale_id);
         
           }

          $pay_again = false;


       //    dd($data);
    // return $data;
//        $pdf = PDF:: loadview('InvoiceTemplate',$data);

//         return view('invoice');
//         return view('InvoiceTemplateWPF', compact('order','flavours','materials','branchId','branchName','branchNumber'));
// return PDF::loadHTML('Hello World!')->stream('download.pdf');
     return view('invoice',compact('order', 'branchId', 'branchName', 'branchNumber', 'flavours', 'materials','assignedBranch','payment_status','flavour_categories','pos_sale_items','pos_sales_payments','all_price_info','item_kits','barcodeImage','pay_again'));
        // return PDF::loadview('invoice', compact('data'))->stream('invoice.pdf');
        // $pdf = PDF::loadview('invoice', compact('order', 'branchId', 'branchName', 'branchNumber', 'flavours', 'materials','assignedBranch','payment_status','flavour_categories'));
        // $pdf = PDF::loadview('InvoiceTemplate', compact('order', 'branchId', 'branchName', 'branchNumber','flavours', 'materials'));
//

//return $flavour_categories;



//         $pdf =PDF::loadview('invoice');
//        return $pdf->stream('Invoice.pdf', array("Attachment" => false));
//         exit(0);
        //  return $pdf->download('invoice.pdf');
//    dd($contents);

        // return $pdf->stream();

     }catch (\Exception $exception)
     {
         Log::error("Error Generating PDF".$exception->getMessage());

         $data = ["status" => "Error","statusMessage"=>"Error Generating PDF (Or Flavour Doesn't Exits)"];
         return response()->json($data);
     }


//        return view('InvoiceTemplate',compact('order','branchId','branchName','branchNumber','flavours','materials'));


    }



    public function reGeneratePdf($id)
    {

     try {
              $item_kits = [];
              $pos_sale_items = null;
              $pos_sales_payments = null;
              $all_price_info = [];
              $positem = PosSale::where('cake_invoice', $id)->first();

           if ($positem) {
                 // The record exists, you can access the sale_id value
              $positem_sale_id = $positem->sale_id;

              $pos_sale_items = PosSaleItems::join('items','pos_sales_items.item_id','=','items.item_id')
                                          ->select('items.name','items.item_number','pos_sales_items.*')->where('pos_sales_items.sale_id',$positem_sale_id)
                                          ->get();
              $pos_sales_payments = PosSalePayments::where('sale_id', $positem_sale_id)->first();

              $all_price_info = $this->get_all_price_data($pos_sale_items->toArray());


               }

         $order = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')
                         ->select('orders.*', 'order_product.product_name')->findOrFail($id);
         if($order->is_cake == '0'){
            $item_kit_order = Order::with('orderProduct')->where('order_number',$id)->first();
            $item_kits =  $item_kit_order->orderProduct;
         }
         $flavours = Order::select('flavour_order.*')->join('flavour_order', 'orders.order_number', '=', 'flavour_order.order_number')->where('orders.order_number', '=', $id)->get();
//          $flavours= Order::where('order_number',$id)->with('flavours')->get();
         $flavour_categories=array();

         foreach ($flavours as $flavour)
         {
             $o_flavour=Flavour::where('sku',$flavour->flavour_sku)->first();
             $category=$o_flavour->flavourCategory->name;

             array_push($flavour_categories,$category);
         }
         $materials = Order::join('material_order', 'orders.order_number', '=', 'material_order.order_number')->where('orders.order_number', '=', $id)->get();
         $branchId = Configuration::where('key', '=', 'branch_id')->first();
         $branchName = Configuration::where('key', '=', 'branch_name')->first();
         $branchNumber = Configuration::where('key', '=', 'branch_number')->first();

         $assignedBranch = Branch::where('code','=',$order->assigned_to)->first();
         $payment_status = Configuration::where('key','=','Payment_Status')->where('value','=',$order->payment_status)->first();


       $data= array($order, $branchName,$branchId,$branchNumber,$flavours,$materials,$assignedBranch,$payment_status,$flavour_categories);
     

       $barcode = new DNS1D();
       $barcodeData = $barcode->getBarcodePNG($order->id, 'C39');
       
       // Build the HTML to display the barcode image
       $barcodeImage = '<img src="data:image/png;base64,'.$barcodeData.'" alt="barcode" />';
    
           if ($positem) {
            $positem_sale_id = $positem->sale_id; 
            $this->uploadRecent($positem_sale_id);
         
           }
        
        $pay_again = true;

     return view('invoice',compact('order', 'branchId', 'branchName', 'branchNumber', 'flavours', 'materials','assignedBranch','payment_status','flavour_categories','pos_sale_items','pos_sales_payments','all_price_info','item_kits','barcodeImage', 'pay_again'));

     }catch (\Exception $exception)
     {
         Log::error("Error Generating PDF".$exception->getMessage());

         $data = ["status" => "Error","statusMessage"=>"Error Generating PDF (Or Flavour Doesn't Exits)"];
         return response()->json($data);
     }


    }



    public function get_all_price_data($pos_items)
    {
        $subtotal = 0;
        $total = 0;
        $totalWithoutTax = 0;
        $totalTax = 0;

        foreach ($pos_items as $item) {

            $itemTax = ItemTax::where('item_id', $item['item_id'])->first();

            if ($itemTax) {
                $tax_percent = $itemTax->value('percent');
            } else {
                $tax_percent = 0; // Set tax_percent to 0 if no item tax found
            }

            $sub = bcmul($item['item_cost_price'], $item['quantity_purchased'], 2);
            $subtotal = bcadd($subtotal,$sub,2);

            $totalP = bcmul($item['item_unit_price'], $item['quantity_purchased']);
            $total += $totalP;

            $taxAmount = bcmul($totalP, $tax_percent / 100,2);
            $totalWithoutTax += ($totalP - $taxAmount);

            $totalTax += $taxAmount;


        }
        return [
            'subtotal' => $subtotal,
            'total' => $total,
            'totalWithoutTax' => $totalWithoutTax,
            'totalTax' => $totalTax,
        ];
    }



    public function getOrderInformation()
    {
//        $fromDate=     Input::post('from_date');
//        $toDate=     Input::post('to_date');
//        $deliveryDate=     Input::post('delivery_date');
//        $searchId=     Input::post('search_id');
//        $searchCustomerName=     Input::post('search_customerName');
//        $searchBranchCode=     Input::post('search_branchCode');
//        $paymentStatus=     Input::post('payment_status');
//        $orderStatus=     Input::post('order_status');
//        $orderPriority=     Input::post('order_priority');
//        $finalImage=     Input::post('final_image');

        $from_date=    Input::get('date_from');
        $to_date=      Input::get('to_date');
        $delivery_date=     Input::get('delivery_date');
        $search_id=     Input::get('search_id');
        $search_customerName=     Input::get('search_customerName');
        $search_branchCode=     Input::get('branch_code');
        $payment_status=      Input::get('payment_status');
        $order_status=       Input::get('order_status');
        $order_priority=    Input::get('order_priority');
        $final_image=     Input::get('final_image');


        Log::info('From Data'. $payment_status);
        /*
         * With Compulsary Date Check
         */

//        $query = Order::whereBetween('orders.created_at', [$convertedFromDate, $convertedToDate])
//            -> join('order_product','orders.order_number','=','order_product.order_number')
//            ->with('orderStatus')
//            ->with('paymentType')
//            ->with('photo');
        $branchCode = Configuration::where('key', '=', 'branch_code')->first();
//                where('assigned_to','=',$branchCode->value)
        $orders = Order::

               join('order_product','orders.order_number','=','order_product.order_number')
            ->with('orderStatus')
            ->with('paymentType')
            ->with('photo')->where(function ($query)use ($from_date, $to_date, $delivery_date, $search_id,$search_customerName,$search_branchCode,$payment_status,
                $order_status,$order_priority,$final_image){
                if($final_image != -1)
                {
                    if($final_image==1 ) {
                        Log::info('Final Image');
                        $query->where('final_image', '=', '')
                            ->orWhereNull('final_image');
                    }
                    if($final_image==0)
                    {
                        $query->where('final_image', '!=', '')
                            ->orWhereNotNull('final_image');
                    }

                }
                if(!empty($from_date))
                {
                    Log::info('from date');
                    $convertedFromDate = Carbon::parse($from_date);
                    $query->whereDate('orders.order_date','>=',$convertedFromDate);
                }
                if(!empty($to_date))
                {
                    Log::info('to date');
                    $convertedToDate = Carbon::parse($to_date);
//            $convertedToDate = $convertedToDate->addDays(1);
                    $query->whereDate('orders.order_date','<=',$convertedToDate);
                }
//
//
                if(!empty($delivery_date))
                {
                    Log::info('delivery date');
                    $convertedDeliveryDate = Carbon::parse($delivery_date);
//            $convertedToDate = $convertedToDate->addDays(1);
                    $query->whereDate('orders.delivery_date','=',$convertedDeliveryDate);
                }
                if(!empty($search_id))
                {
                    Log::info('order id');
                    $query->where('orders.order_number','LIKE','%'.$search_id);

                }
                if(!empty($search_branchCode) )
                {
                    Log::info('branch code '.$search_branchCode);
                    $query->where('branch_code','=',$search_branchCode);

                }
                if(!empty($search_customerName)) {
                    Log::info('customer name');
                    $query->where('customer_name','LIKE','%'.$search_customerName.'%');

                }
                if($payment_status!=-1)
                {
                    Log::info('payment status');
                    $query->where('payment_status','=',$payment_status);
                }
//
                if($order_status!=0)
                {
                    Log::info('order status');
                    $query->where('order_status','=',$order_status);
                }


                if($order_priority != -1)
                {
                    Log::info('priority');
                    $query->where('priority','=',$order_priority);
                }



            })->orderBy('orders.id', 'desc')->paginate(10)->setPath ( '' );
        $orders = $orders->appends ( array (
            'from_date'=>    Input::get('from_date'),
            'to_date'=>    Input::get('from_date'),
            'delivery_date'=>    Input::get('delivery_date'),
            'search_id'=>    Input::get('search_id'),
            'search_customerName'=>    Input::get('search_customerName'),
            'search_branchCode'=>    Input::get('search_branchCode'),
            'payment_status'=>    Input::get('payment_status'),
            'order_status'=>    Input::get('order_status'),
            'order_priority'=>    Input::get('order_priority'),
            'final_image'=>    Input::get('final_image'),

        ) );



////       $query =DB::table('orders')->select(['id','branch_code','total_price','order_status','payment_type','created_at','salesman']);
//
//        if($finalImage != -1)
//        {
//            if($finalImage==1 ) {
//                Log::info('Final Image');
//                $query->where('final_image', '=', '')
//                    ->orWhereNull('final_image');
//            }
//            if($finalImage==0)
//            {
//                $query->where('final_image', '!=', '')
//                    ->orWhereNotNull('final_image');
//            }
//
//        }
//        if(!empty($fromDate))
//        {
//            Log::info('from date');
//            $convertedFromDate = Carbon::parse($fromDate);
//            $query->whereDate('orders.order_date','>=',$convertedFromDate);
//        }
//        if(!empty($toDate))
//        {
//            Log::info('to date');
//            $convertedToDate = Carbon::parse($toDate);
////            $convertedToDate = $convertedToDate->addDays(1);
//            $query->whereDate('orders.order_date','<=',$convertedToDate);
//        }
////        if(!empty($deliveryDate))
////        {
////            Log::info('to date');
////            $convertedDeliveryDate = Carbon::parse($deliveryDate);
//////            $convertedToDate = $convertedToDate->addDays(1);
////            $query->whereDate('orders.delivery_date','=',$convertedDeliveryDate);
////        }
//
////
//        if(!empty($deliveryDate))
//        {
//            Log::info('delivery date');
//            $convertedDeliveryDate = Carbon::parse($deliveryDate);
////            $convertedToDate = $convertedToDate->addDays(1);
//            $query->whereDate('orders.delivery_date','=',$convertedDeliveryDate);
//        }
//        if(!empty($searchId))
//        {
//            Log::info('order id');
//            $query->where('orders.order_number','LIKE','%'.$searchId);
//
//        }
//        if(!empty($searchBranchCode) )
//        {
//            Log::info('branch code '.$searchBranchCode);
//            $query->where('branch_code','=',$searchBranchCode);
//
//        }
//        if(!empty($searchCustomerName)) {
//            Log::info('customer name');
//            $query->where('customer_name','LIKE','%'.$searchCustomerName.'%');
//
//        }
//        if($paymentStatus!=-1)
//        {
//            Log::info('payment status');
//            $query->where('payment_status','=',$paymentStatus);
//        }
////
//        if($orderStatus!=0)
//        {
//            Log::info('order status');
//            $query->where('order_status','=',$orderStatus);
//        }
//
//
//        if($orderPriority != -1)
//        {
//            Log::info('priority');
//            $query->where('priority','=',$orderPriority);
//        }
//
//
//        if(!empty($deliveryDate))
//        {
//            Log::info('delivery date');
//            $convertedDeliveryDate = Carbon::parse($deliveryDate);
////            $convertedToDate = $convertedToDate->addDays(1);
//            $query->whereDate('orders.delivery_date','=',$convertedDeliveryDate);
//        }


//
//        $orders = $query->paginate(2);

        try {
            $priority = Configuration::where('key', '=', 'Priority_key')->pluck('label', 'value')->toArray();
            $branches = Branch::pluck('name', 'code')->all();
            $paymentTypes = PaymentType::pluck('name', 'id')->all();
            $orderStatus = OrderStatus::pluck('name', 'id')->all();

        } catch (\Exception $exception) {
            Log::error('Priority , Branches, PaymentType or Order Status missing order index Page' . $exception->getMessage());
        }
//
//
//        return $data;
        return view('admin.orders.index',compact('orders','priority','branches','paymentTypes','orderStatus'));

    }



    public function getBranchOrders()
    {
        $branchCode=     Input::post('branch_code');
        $dateRange= Configuration::where('key','=','Past_Order_Range')->first();
        $DateRange= $dateRange->label;




        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'),'headers'=>['Accept'=>'application/json']]);
        try {
            $response = $client->request('POST', 'api/orderBranch/details', [
                'form_params' => [
                    'branchCode' => $branchCode,
                    'dateRange'=>$DateRange
                ]
            ]);
        } catch (GuzzleException $e) {
            Log::error('Error Fetching Branch Orders From Live'.$e->getMessage());

        }


        $data =$response->getBody()->getContents();

        $decodedData= json_decode($data);
//        return array($fromDate,$toDate,$searchId,$searchProductName,$searchBranchCode,$paymentType,$orderStatus);


//        dd($decodedData);

        if(!empty($decodedData)){
            try {

                foreach ($decodedData as $record) {


                    if (Order::where('order_number', '=', $record->order_number)->count() <= 0) {
                        Log::info('Order Doesnt Exists Server');
                        $syncFlavoursAndMaterials = 1;
                    } else {
                        Log::info('Order  Exists Server');
                        $syncFlavoursAndMaterials = 0;
                    }
//            $record->order_number = "BH-002-01";
                    $order_created = Order::updateOrCreate(['order_number' => $record->order_number],
                        ['salesman' => $record->salesman,
                            'customer_name' => $record->customer_name,
                            'customer_email' => $record->customer_email,
                            'customer_address' => $record->customer_address,
                            'customer_phone' => $record->customer_phone,
                            'weight' => $record->weight,
                            'quantity' => $record->quantity,
                            'total_price' => $record->total_price,
                            'advance_price' => $record->advance_price,
                            'payment_type' => $record->payment_type->id,
                            'payment_status' => $record->payment_status,
                            'order_type' => $record->order_type,
                            'order_status' => $record->order_status->id,
                            'delivery_date' => $record->delivery_date,
                            'delivery_time' => $record->delivery_time,
                            'remarks' => $record->remarks,
                            'branch_id' => $record->branch_id,
                            'branch_code' => $record->branch_code,
                            'assigned_to' => $record->assigned_to,
                            'user_id' => $record->user_id,
                            'is_active' => $record->is_active,
                            'priority' => $record->priority,
                            'photo_id' => $record->photo_id,
                            'photo_path' => $record->photo_path,
                            'live_synced' => $record->live_synced,
                            'server_sync' => $record->server_sync,
                            'is_custom' => $record->is_custom,

                            'discount'=>$record->discount,
                            'instructions'=>$record->instructions,
                            'final_image'=>$record->final_image,
                            'pending_amount'=>$record->pending_amount,
                            'pending_amount_paid_date'=>$record->pending_amount_paid_date,
                            'pending_amount_paid_time'=>$record->pending_amount_paid_time,
                            'pending_amount_paid_branch'=>$record->pending_amount_paid_branch,
                            'order_date'=>$record->order_date,
                            'created_at' => $record->created_at,
                            'updated_at' => $record->updated_at]);

                    if ($syncFlavoursAndMaterials == 1) {

                        foreach ($record->flavour as $flavour) {
//                $order->flavours()->sync($flavour->id);
                            DB::insert('insert into flavour_order (order_number,flavour_sku,flavour_name,flavour_price) values (?,?,?,?)', [$flavour->order_number, $flavour->flavour_sku, $flavour->flavour_name, $flavour->flavour_price]);

                        }
                        foreach ($record->material as $material) {
//                $order->materials()->sync($material->id);
                            DB::insert('insert into material_order (order_number,material_sku,material_name,material_price) values (?,?,?,?)', [$material->order_number, $material->material_sku, $material->material_name, $material->material_price]);
                        }
                        foreach ($record->product as $product) {
                            DB::insert('insert into order_product (order_number,product_sku,product_name,product_price) values (?,?,?,?)', [$product->order_number, $product->product_sku, $product->product_name, $product->product_price]);
                        }
                    }
                    $id = $order_created->order_number;
                    $updatedOrder = Order::findOrFail($id);




                    if($record->final_image!= null || !empty($record->final_image))
                    {
                        if (!(Photo::where('path', '=', $updatedOrder->final_image)->count() > 0)) {


                            $temp_path = public_path() . '/images/Sync_Created_Order_Images/' . $updatedOrder->final_image;
                            $fixed_path = public_path() . '/images/Created_Order_Images/' . $updatedOrder->final_image;
                            $file_path_handle = fopen($temp_path, 'w');
//                $pictureArray[]=$updatedProduct->sku;
                            $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK')]);
                            try {
                                $client->request('get', 'images/Created_Order_Images/' . $updatedOrder->final_image, ['sink' => $file_path_handle]);

                                $photo = Photo::create(['path' => $updatedOrder->final_image]);


                                if (is_resource($file_path_handle)) {
                                    fclose($file_path_handle);
                                }
                            } catch (GuzzleException $e) {
                                return $e;
                            }
                            File::move($temp_path, $fixed_path);
//                            $updatedOrder->photo_id = $photo->id;
//                            $updatedOrder->save();


                        }




                    }








                    if ($record->is_custom == 1) {


                        if (!(Photo::where('path', '=', $updatedOrder->photo_path)->count() > 0)) {
                            // id doesnt exists
                            $temp_path = public_path() . '/images/Sync_Custom_Orders/' . $updatedOrder->photo_path;
                            $fixed_path = public_path() . '/images/Custom_Orders/' . $updatedOrder->photo_path;
                            $file_path_handle = fopen($temp_path, 'w');
//                $pictureArray[]=$updatedProduct->sku;
                            $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK')]);
                            try {
                                $client->request('get', 'images/Custom_Orders/' . $updatedOrder->photo_path, ['sink' => $file_path_handle]);

                                $photo = Photo::create(['path' => $updatedOrder->photo_path]);


                                if (is_resource($file_path_handle)) {
                                    fclose($file_path_handle);
                                }
                            } catch (GuzzleException $e) {
                                return $e;
                            }
                            File::move($temp_path, $fixed_path);
                            $updatedOrder->photo_id = $photo->id;
                            $updatedOrder->save();


                        } else if ((Photo::where('path', '=', $updatedOrder->photo_path)->count() > 0)) {
                            $existing_photo = Photo::where('path', '=', $updatedOrder->photo_path)->first();
                            $updatedOrder->photo_id = $existing_photo->id;
                            $updatedOrder->save();
                        }


//
                    }else if ((Photo::where('path', '=', $updatedOrder->photo_path)->count() > 0)) {
                        $existing_photo = Photo::where('path', '=', $updatedOrder->photo_path)->first();
                        $updatedOrder->photo_id = $existing_photo->id;
                        $updatedOrder->save();
                    }


                }
            }catch (\Exception $exception)
            {
                Log::error("Error Updating Orders synced from Live".$exception->getMessage());
            }
        }


try{
    $branch_code = Configuration::where('key', '=', 'branch_Code')->first();
    Log::info("Branch Code" .$branch_code->value) ;
}catch (\Exception $exception)
{
    Log::info("Branch Code Not Found Database While Syncing Order From live and passing on to Ajax request".$exception->getMessage());
}

        $query = Order::
                join('order_product','orders.order_number','=','order_product.order_number')
//            ->where('assigned_to','=',$branch_code->value)
            ->with('orderStatus')
            ->with('paymentType')
            ->with('photo')->get();

//
//
//
//
        return $query;
//
    }
    public function getBranchOrdersLive(Request $request)
    {

                    $branchCode=     $request->input('branchCode');
                    $dateRange=     $request->input('dateRange');

                   $fromDate=    Carbon::now()->subDays($dateRange);
                   $toDate=     Carbon::now();

        $query = Order::whereBetween('created_at', [$fromDate, $toDate])
            ->where('assigned_to','=',$branchCode)
            ->where('server_sync','=',0)
            ->with('orderStatus')
            ->with('paymentType')
            ->with('photo')->get();
//


        foreach ($query as $order)
        {
            $product = DB::select('select order_number,product_sku,product_name,product_price from order_product where order_number = :id', ['id' => $order->order_number]);
            $flavours = DB::select('select order_number,flavour_sku,flavour_name,flavour_price from flavour_order where order_number = :id', ['id' => $order->order_number]);
            $materials = DB::select('select order_number,material_sku,material_name,material_price from material_order where order_number = :id', ['id' => $order->order_number]);
            $order['product']=$product;
            $order['flavour']=$flavours;
            $order['material']=$materials;

        }

        try {
        $updated_orders = Order::whereBetween('created_at', [$fromDate, $toDate])
            ->where('assigned_to','=',$branchCode)
            ->where('server_sync','=',0)->update(['server_sync' => '1']);
        }catch (\Exception $exception)
        {
            Log::error('Error while updating server orders server sync to 1'.$exception->getMessage());
        }

        return response()->json($query);
//return $query;


    }
    public function searchReset()
    {
        try{
            $branch_code = Configuration::where('key', '=', 'branch_Code')->first();
        }catch (\Exception $exception)
        {
            Log::info("Branch Code Not Found Database While reseting".$exception->getMessage());
        }

        $sales = Order::
        join('order_product','orders.order_number','=','order_product.order_number')
//            ->where('assigned_to','=',$branch_code->value)  //for local server
            ->with('orderStatus')
            ->with('paymentType')
            ->with('photo')->orderBy('orders.id', 'desc')->get();

        return $sales;
    }


    public function syncLocalOrders(Request $request)
    {
        $orders = json_decode($request->input('body'));
//        $orders=$request->input('body');
        $decodedData = $orders;
        $orderLog = new Logger("order");

//
        if (!empty($decodedData)) {


            try {
                foreach ($decodedData as $order) {
////            $productSku[]=$product->sku;
//

                    $order = (array)$order;


                    if (Order::where('order_number', '=', $order['order_number'])->count() <= 0) {
                        Log::info('Order Doesnt Exists Server');
                        $syncFlavoursAndMaterials = 1;
                    } else {
                        Log::info('Order  Exists Server');
                        $syncFlavoursAndMaterials = 0;
                    }


///             Flavour::create($record);
//                         $order['order_number'] = "BH-002-01";
                    $order_created = Order::updateOrCreate(['order_number' => $order['order_number']],
                        ['salesman' => $order['salesman'],
                            'customer_name' => $order['customer_name'],
                            'customer_email' => $order['customer_email'],
                            'customer_address' => $order['customer_address'],
                            'customer_phone' => $order['customer_phone'],
                            'weight' => $order['weight'],
                            'quantity' => $order['quantity'],
                            'total_price' => $order['total_price'],
                            'advance_price' => $order['advance_price'],
                            'payment_type' => $order['payment_type'],
                            'payment_status' => $order['payment_status'],
                            'order_type' => $order['order_type'],
                            'order_status' => $order['order_status'],
                            'delivery_date' => $order['delivery_date'],
                            'delivery_time' => $order['delivery_time'],
                            'remarks' => $order['remarks'],
                            'branch_id' => $order['branch_id'],
                            'branch_code' => $order['branch_code'],
                            'assigned_to' => $order['assigned_to'],
                            'user_id' => $order['user_id'],
                            'is_active' => $order['is_active'],
                            'priority' => $order['priority'],
                            'photo_id' => $order['photo_id'],
                            'photo_path' => $order['photo_path'],
                            'live_synced' => $order['live_synced'],
                            'server_sync' => $order['server_sync'],
                            'is_custom'=>$order['is_custom'],
                            'discount'=>$order['discount'],
                            'instructions'=>$order['instructions'],
                            'final_image'=>$order['final_image'],
                            'pending_amount'=>$order['pending_amount'],
                            'pending_amount_paid_date'=>$order['pending_amount_paid_date'],
                            'pending_amount_paid_time'=>$order['pending_amount_paid_time'],
                            'pending_amount_paid_branch'=>$order['pending_amount_paid_branch'],
                            'order_date'=>$order['order_date'],
                            'created_at' => $order['created_at'],
                            'updated_at' => $order['updated_at']]);
//


                    $id = $order_created->order_number;
                    $updatedOrder = Order::findOrFail($id);
                    $product = Product::where('sku', '=', $order['product_sku'])->first();

                    try {
                        $existing_photo = Photo::where('path', '=', $order['photo_path'])->first();
                        $updatedOrder->photo_id = $existing_photo->id;
                        $updatedOrder->save();
                    }catch (\Exception $exception)
                    {
                        Log::info('Image Doesnt Exist On Live , Exception : '.$exception->getMessage());
                    }

                    $name = $product->name;
                    $price = $product->price;
                    $updatedOrder->products()->sync([$product->sku => ['product_name' => $name, 'product_price' => $price]]);


                    if ($syncFlavoursAndMaterials == 1) {

                        foreach ($order['flavours_sku'] as $flavour) {
                            $flavour_db = Flavour::where('sku', '=', $flavour->flavour_sku)->first();
                            $name = $flavour_db->name;
                            $price = $flavour_db->price;
                            $updatedOrder->flavours()->attach([$flavour_db->sku => ['flavour_name' => $name, 'flavour_price' => $price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                        }
                        foreach ($order['materials_sku'] as $material) {
                            $material_db = Material::where('sku', '=', $material->material_sku)->first();
                            $name = $material_db->name;
                            $price = $material_db->price;
                            $updatedOrder->materials()->attach([$material_db->sku => ['material_name' => $name, 'material_price' => $price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                        }
                    }


                }

            } catch (\Exception $exception)
            {
            Log::error('Error Syncing Orders From Local '.$exception->getMessage());
            }

        }
        return json_encode($orders);
    }

    public  function syncLocalOrdersImages(Request $request)
    {

//        if(isset($request))
//        {
//            Log::info('In Server '.print_r($request->file('order_image'),true));
//        }

        try {

            if ($file = $request->file('order_image')) {

                $name = $file->getClientOriginalName();
//            $name = $request->input('sku').'.'.$file->getClientOriginalExtension();

//            Log::info('In Server '.print_r($file,true));


                $file->move('images/Custom_Orders/', $name);
//            $file->move('images/Custom_Orders/', "TESTING.jpg");

                $photo = Photo::create(['path' => $name]);


                return response()->json(['Status' => 'Image Saved', 'photo_id' => $photo->id]);
            }

        }catch (\Exception $exception)
        {
            Log::error("Error Syncing Image from Local".$exception->getMessage());
            return response()->json(['Status' => 'Error']);
        }



    }
    public  function syncLocalOrdersFinalImages(Request $request)
    {

//        if(isset($request))
//        {
//            Log::info('In Server '.print_r($request->file('order_image'),true));
//        }

        try {

            if ($file = $request->file('order_final_image')) {

                $name = $file->getClientOriginalName();
//            $name = $request->input('sku').'.'.$file->getClientOriginalExtension();

//            Log::info('In Server '.print_r($file,true));


                $file->move('images/Created_Order_Images/', $name);
//            $file->move('images/Custom_Orders/', "TESTING.jpg");

                $photo = Photo::create(['path' => $name]);


                return response()->json(['Status' => 'Image Saved', 'photo_path' => $photo->path]);
            }

        }catch (\Exception $exception)
        {
            Log::error("Error Syncing Image from Local".$exception->getMessage());
            return response()->json(['Status' => 'Error']);
        }



    }
    public  function checkLiveCustomOrder(Request $request)
    {
        $orderNumber = $request->input('order_number');


        $order = Order::where('order_number','=',$orderNumber)->first();
        /*
         * FOR TESING PURPOSES ONLY
         */
//        $order = Order::where('order_number','=',"BH-003-44")->first();

        if($order)
        {
        return response()->json(['Status'=>'Found']);
        }
        else
        {
            return response()->json(['Status'=>'Not-Found']);
        }



    }

    public function setPaymentStatus()
    {
        $orderId=     Input::post('orderNumber');



        try {
            $order = Order::findOrFail($orderId);
        }
        catch (ModelNotFoundException $exception)
        {
            return "Order Not Found ID".$orderId;
        }

        try {
            $branch = Configuration::where('key','=','branch_name')->first() ;
        }
        catch (ModelNotFoundException $exception)
        {
            return "Order Not Found ID".$orderId;
        }

        $response=['Status'=>"Order Payment Status Updated",'Payment_Status'=>'1','Pending_Amount'=>($order->total_price - $order->advance_price - $order->discount),'Pending_Amount_Date'=>Carbon::parse(now())->toDayDateTimeString(),'Pending_Branch'=>$branch->value];
        $order->pending_amount = ($order->total_price - $order->advance_price - $order->discount);
        $order->pending_amount_paid_date = Carbon::now()->format('Y-m-d');
        $order->pending_amount_paid_time = Carbon::now()->format('h:i a');
        $order->payment_status = 1;
        $order->pending_amount_paid_branch =$branch->value;
        $order->live_synced=0;
        $order->save();

        return $response;

    }


    public function manualUploadOrders()
        {
            $orderLog = new Logger("order");
            $ordersToSync = '';

            try {

                $range = Configuration::where('key', '=', 'Past_Order_Range')->first();
                $currentBranch = Configuration::where('key', '=', 'branch_Code')->first();
            }catch (\Exception $exception)
            {
                Log::info('Range Or Current Branch Not Set In Configuration');
            }

            $fromDate = Carbon::now()->subDays($range->value);
            $toDate = Carbon::now();


            $query = Order::join('order_product', 'orders.order_number', '=', 'order_product.order_number')
//
                ->select('orders.*', 'order_product.product_sku')->whereBetween('orders.created_at', [$fromDate, $toDate])
                ->get();

//
//        Log::info('Test');
        if ($query->count()) {


//        $query = DB::table('orders')->whereBetween('created_at',[$fromDate,$toDate])->where('live_synced','=','0')->distinct()->get();

//        $query = DB::table('orders')->get();

            try {


                foreach ($query as $order) {


                    if (($order->branch_code == $order->assigned_to) && ($order->branch_code != $currentBranch->value)) {
                        $order['server_sync'] = 0;
                    } else if ($order->branch_code == $order->assigned_to) {
                        $order['server_sync'] = 1;
                    } else {
                        $order['server_sync'] = 0;
                    }
                    $flavour_sku = Order::join('flavour_order', 'orders.order_number', '=', 'flavour_order.order_number')->select('flavour_order.flavour_sku')->where('orders.order_number', '=', $order->order_number)->get();
                    $order['flavours_sku'] = $flavour_sku;

                    $material_sku = Order::join('material_order', 'orders.order_number', '=', 'material_order.order_number')->select('material_order.material_sku')->where('orders.order_number', '=', $order->order_number)->get();
                    $order['materials_sku'] = $material_sku;

                    $log = $order->toArray();
                    Log::info(print_r($log, true) . Carbon::now());





                    if($order->final_image!=null || !empty($order->final_image))
                    {






                        $name = $order->final_image;
                        $base_path = public_path() . '/images/Created_Order_Images/' . $name;
                        $destination_path = public_path() . '/images/Sync_Created_Order_Images/' . $name;
                        File::copy($base_path, $destination_path);


                        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'), 'headers' => ['Accept' => 'application/json']]);
                        try {
                            $response = $client->request('POST', 'api/localOrders/sync/finalImages', [
                                'multipart' => [
                                    [
                                        'name' => 'order_final_image',
                                        'contents' => $file_path_handle = fopen($destination_path, 'r'),

                                    ]]
                            ]);
                            unlink($destination_path);
                        } catch (GuzzleException $e) {
                            return $e;
                        }
                        $photo_path = json_decode($response->getBody()->getContents());
                        //uncomment after testing

                        Log::info('Message From Serve ' . $photo_path->Status);
//                        $order['final_'] = $photoId->photo_id;






                    }



//
                    if ($order->is_custom == 1) {
                        Log::info('do_sync');
                        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'), 'headers' => ['Accept' => 'application/json']]);
                        try {
                            $response = $client->request('POST', 'api/customOrder/check', [
                                'form_params' => [
                                    'order_number' => $order->order_number,
                                ]
                            ]);
                        } catch (GuzzleException $e) {
                            return $e;
                        }
                        $do_sync = json_decode($response->getBody()->getContents());

                        Log::info('Message From Serve' . $do_sync->Status);

                        if ($do_sync->Status == 'Not-Found') {


                            $name = $order->photo_path;
                            $base_path = public_path() . '/images/Custom_Orders/' . $name;
                            $destination_path = public_path() . '/images/Sync_Custom_Orders/' . $name;
                            File::copy($base_path, $destination_path);


                            $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'), 'headers' => ['Accept' => 'application/json']]);
                            try {
                                $response = $client->request('POST', 'api/localOrders/sync/images', [
                                    'multipart' => [
                                        [
                                            'name' => 'order_image',
                                            'contents' => $file_path_handle = fopen($destination_path, 'r'),

                                        ]]
                                ]);
                                unlink($destination_path);
                            } catch (GuzzleException $e) {
                                return $e;
                            }
                            $photoId = json_decode($response->getBody()->getContents());
                            //uncomment after testing

                            Log::info('Message From Serve ' . $photoId->Status);
                            $order['photo_id'] = $photoId->photo_id;


                        }


                    }

                    Log::info('End Of is_custom');
                    $log = $order->toArray();
                    Log::info(print_r($log, true) . Carbon::now());


                }
            }catch (\Exception $exception)
            {
                Log::info("Error in Foreach  ".$exception->getMessage());
            }




            $data = json_encode($query);



            $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'), 'headers' => ['Accept' => 'application/json']]);
            try {
                $response = $client->request('POST', 'api/localOrders/sync', [
                    'form_params' => [
                        'body' => $data
                    ]
                ]);
            } catch (GuzzleException $e) {
                Log::error('Error while pushing Orders to live'.$exception->getMessage());
            }
            try {
                Order::whereBetween('created_at', [$fromDate, $toDate])
                    ->where('live_synced', '=', 0)->update(['live_synced' => '1']);
            }catch (\Exception $exception)
            {
                Log::error('Error while updation local orders live sync to 1');
            }

//

            $log =['Order Synced at '.Carbon::now()];
            try {
                $orderLog->pushHandler(new StreamHandler(storage_path('logs/Order_Sync.log')), Logger::INFO);
            } catch (\Exception $e) {
                Log::error("Error while login ".$e->getMessage());
            }
            $orderLog->info('Order Sync Log', $log);

            $response=['Status'=>'Orders Uploaded'];
             return $response;
        }
        $response=['Status'=>'Nothing To Upload'];
        return $response;



        }
}
