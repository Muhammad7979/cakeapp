<?php

namespace App\Http\Controllers;

use App\Branch;
use App\CakePosItem;
use App\CakePosItemTemp;
use App\Configuration;
use App\Flavour;
use App\Http\Requests\StoreOrderRequest;
use App\Material;
use App\Order;
use App\OrderProduct;
use App\OrderStatus;
use App\Photo;
use App\PosSalePayments;
use App\PosSalePaymentsTemp;
use App\PosSaleTemp;
use App\Product;
use Barryvdh\DomPDF\PDF;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientOrderController extends Controller
{
    //



    public function storeOrder(Request $request)
    {
        $minAdvancePayment = Configuration::where('key','=','min_advance')->first();
        if(!$minAdvancePayment)
        {
            $data = ["status" => "Error","statusMessage"=>"Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }

        if(! Auth::user())
        {
            $data = ["status" => "Error","statusMessage"=>"Please Login To Place an Order"];
            return response()->json($data);
        }
        else
        {
            $user_id=   auth()->user()->id;
        }

        $rules = [

            'salesman'=>'required',
            'customer_name'=>'required',
            'customer_phone'=>'required',
            'weight'=>array('required','min:0'),
            'quantity'=>array('required','min:0'),
            'total_price'=>'required',
            'advance_price'=>array('required'),
            'payment_type'=>'required',
            'payment_status'=>'required',
            'order_type'=>'required',
            'order_status'=>'required',
            'delivery_date'=>'required',
            'delivery_time'=>'required',
            'branch_id'=>'required',
            'branch_code'=>'required',
            'assigned_to'=>'required',
            'priority'=>'required',
            'photo_id'=>'required',
            'flavour_id'=>'required',
            'material_id'=>'required',
            'product_id'=>'required',
            'server_sync'=>'required',
            'live_synced'=>'required',
            'is_custom'=>'required',
            'product_price'=>'required',
            'custom_image'=>'mimes:jpeg,bmp,png,jpg',

        ] ;

        $messages=  [
            'salesman.required' => 'Salesman Name is required',
            'customer_name.required' => 'Custom Name is required',
            'customer_phone.required' => 'Customer Number is required',
            'quantity.required' => 'Quantity is required',
            'weight.required' => 'Product  Weight is required',
            'advance_price.required' => 'Advance Price is required',
            'payment_type.required' => 'Please Select Payment Type',
            'payment_status.required' => 'Payment Status is required',
            'order_type.required' => 'Please Select an Order Type',
            'order_status.required' => 'Order Status is required.',
            'delivery_date.required' => 'Please provide a DELIVERY DATE',
            'delivery_time.required' => 'Please provide a DELIVERY TIME',
            'branch_id.required' => 'Branch Id is Required',
            'branch_code.required' => 'Branch Code is Required',
            'assigned_to.required' => 'Please Select Assigned Branch From Assign To list',
            'user_id.required' => 'User Id is required',
            'priority.required' => 'Please Select Order Priority',
            'photo_id.required' => 'Image is required',
            'custom_image.mimes'=>'Image Type Invalid',

        ];


        $validator = Validator::make($request->all(),$rules,$messages);
        if ($validator->fails())
        {
            $data = ["status" => "Error","statusMessage"=>$validator->messages()->first()];
            return response()->json($data);

        }
        else
            {


                if($request->input('advance_price')<$minAdvancePayment->value)
                {
                    $data = ["status" => "Error","statusMessage"=>"Minimum advance payment i.e ".$minAdvancePayment->value."  must be payed"];
                    return response()->json($data);
                }





                $customOrder = $request->input('is_custom');

                $productId = $request->input('product_id');
                $flavourId = explode(',', $request->input('flavour_id'));
                $materialId = explode(',', $request->input('material_id'));

             if( $customOrder==1)
                {

                      $product = Product::where('sku','=','PH-000')->first();
                      if(!$product)
                      {
                          $data = ["status" => "Error","statusMessage"=>"Custom Product Doesnt Exists"];
                          return response()->json($data);
                      }
                      $product_sku = $product->sku;
                      $product_name = $product->name;
                      $product_price=$request->input('product_price');
                 }
             else
                 {
                     Log::info("In Else of custom order");

                        $product = Product::where('id','=',$productId)->first();
                     Log::info("In Else of custom order after fetching product");
                        if($product) {
                            $product_sku = $product->sku;
                            $product_name = $product->name;
                            $product_price = $product->price;
                        }
                        else
                            {
                                $data = ["status" => "Error","statusMessage"=>"Please Select a Product"];
                                return response()->json($data);
                            }
                 }

            if(!empty($flavourId))
            {
                   foreach ($flavourId as $fid) {

                       $flavour = Flavour::where('id','=',$fid)->first();

                        if(!$flavour)
                           {
                               $data = ["status" => "Error","statusMessage"=>"Invalid Flavour Selected (Or Flavour Doesn't Exits)"];
                               return response()->json($data);
                           }
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                                                 }
            }

            else
             {
                        $data = ["status" => "Error","statusMessage"=>"Please Select at-least one flavour"];
                        return response()->json($data);
             }

             if(!empty($materialId))
             {
                    foreach ($materialId as $mid) {
                        $material = Material::where('id','=',$mid)->first();
                        if(!$material)
                        {
                            $data = ["status" => "Error","statusMessage"=>"Invalid Material Selected (Or Material Doesn't Exits)"];
                            return response()->json($data);
                        }
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                    }
             }
             else
             {
                    $data = ["status" => "Error","statusMessage"=>"Please Select at-least one material"];
                    return response()->json($data);
             }


                Log::info("After All the Checks");

            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if(!$branchCode)
            {
                $data = ["status" => "Error","statusMessage"=>"Branch Code Not Set in Configuration"];
                return response()->json($data);
            }

            $latestId = Order::latest('id')->first();
            $lastId = 0;
            if (is_null($latestId) || empty($latestId)) {
                $lastId = 0;
            } else {
                $lastId = (int)$latestId->id;
            }




            $input = $request->except(['flavour_id', 'material_id', 'product_id']);


            $balance= $input['total_price']-$input['advance_price']-$input['discount'];
    Log::info("Balacne".$balance);
                try {
                    $branch_name = Configuration::where('key','=','branch_name')->first() ;
                }
                catch (ModelNotFoundException $exception)
                {
                    return "Order Not Found ID";
                }




            if($balance==0)
            {
                Log::info("Balacne if");
                $input['pending_amount'] = 0;
                $input['pending_amount_paid_date'] = Carbon::now()->format('Y-m-d');
                $input['pending_amount_paid_time'] = Carbon::now()->format('h:i a');
                $input['payment_status']= 1;
                $input['pending_amount_paid_branch'] =$branch_name->value;

            }



            $orderStatus = OrderStatus::where('name', '=', $request->input('order_status'))->first();

            $input['order_status'] = $orderStatus->id;
                $order_number = $branchCode->value . '-' . (int)($lastId + 1);
        $input['order_number']=$order_number;


//
//            $input['order_number'] = $order_number;

            $input['user_id'] = $user_id;


            Log::info("Order Number ".$order_number);
            $tftime= Carbon::parse($request->input('delivery_time'));
            $converted_time=$tftime->format('G:i');

            $deliverDate = $request->input('delivery_date');
            $deliverDate= $deliverDate.' '.$converted_time;
            Log::info("Converted Date and Time".$deliverDate);
            $convertedDeliveryDate = Carbon::parse($deliverDate);





            /*
             * Closing Time Shit Starts Here
             */



            //$closingTimeFrom= '23:59:00';
//            $closingTimeTo= '03:00:00';
            $closingTimeFrom=     Carbon::createFromTime(23, 59, 0, 0);
            $closingTimeTo=     Carbon::createFromTime(03, 00, 0, 0)->addDay();

            $st_time = strtotime($closingTimeFrom->toDateTimeString());
            $end_time= strtotime($closingTimeTo->toDateTimeString());

            $cur_time= time();
            Log::info("Now : ".time() . " Start Time : ".$st_time. " End Time : ".$end_time);

            if($cur_time > $st_time && $cur_time < $end_time)
            {
                $order_date= Carbon::now()->subDay()->format('Y-m-d');
                Log::info("Order Date : ".$order_date);
            }else
            {
                $order_date=Carbon::now()->format('Y-m-d');
                Log::info("Order Date else : ".$order_date);
            }

            $input['order_date'] = $order_date;










//            $deliverDate = Carbon::createFromFormat('m/d/y ', $deliverDate);;
            Log::info("Parsed Delivery Date and Time".$convertedDeliveryDate);
            $input['delivery_date'] = $convertedDeliveryDate;
            $input['branch_code']=$branchCode->value;


            Log::info('Converted Time'.$converted_time);


            if ($customOrder == 1) {
// for custom order

                Log::info("In if of custom order photo");
                $file = $request->file('custom_image');

//                $name = time() . $file->getClientOriginalName();
                $name = $order_number. '.' . $file->getClientOriginalExtension();

                $file->move('images/Custom_Orders/', $name);

                $photo = Photo::create(['path' => $name]);

                $input['photo_id'] = $photo->id;
                $input['photo_path'] = $name;

            } else {
                $photo_path = Photo::findOrFail($input['photo_id']);
//                $input['photo_path'] = Str::substr($photo_path->path, 8);
                $input['photo_path'] = $photo_path->path;
            }


            $order = Order::Create($input);

//            $productId = $request->input('product_id');
//            $flavourId = explode(',', $request->input('flavour_id'));
//            $materialId = explode(',', $request->input('material_id'));

//
//        $flavourId=$request->input('flavour_id');
//       $materialId=$request->input('material_id');

            if ($order->wasRecentlyCreated) {

//                foreach ($productId as $id) {

//                    $product = Product::findOrFail($id);
//                    $name = $product->name;
//
//                    if ($customOrder == 1) {
//                        $price = $input['product_price'];
//                    } else {
//                        $price = $product->price;
//                    }
                    $order->products()->sync([$product_sku => ['product_name' => $product_name, 'product_price' => $product_price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);




                foreach ($flavourId as $fid) {
                    $flavour = Flavour::findOrFail($fid);
                    $name = $flavour->name;
                    $price = $flavour->price;
                    $order->flavours()->attach([$flavour->sku => ['flavour_name' => $name, 'flavour_price' => $price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }
                foreach ($materialId as $mid) {
                    $material = Material::findOrFail($mid);
                    $name = $material->name;
                    $price = $material->price;
                    $order->materials()->attach([$material->sku => ['material_name' => $name, 'material_price' => $price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }

//            Session::flash('created_order', 'Order Created');

//        $this->generatePdf($order->id);

                $data = ["status" => "Success", "statusMessage" => "Message", "payLoad" => $order_number];

                return response()->json($data);
            }

        }
    }

    public function cakeOrder($request)
    {

        $minAdvancePayment = Configuration::where('key','=','min_advance')->first();
        // dd(!$minAdvancePayment);

        if(!$minAdvancePayment)
        {
            $data = ["status" => "Error","statusMessage"=>"Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }

        if(! Auth::user())
        {
            $data = ["status" => "Error","statusMessage"=>"Please Login To Place an Order"];
            return response()->json($data);
        }
        else
        {
            $user_id=   auth()->user()->id;
        }

        $rules = [

            'salesman'=>'required',
            'customer_name'=>'required',
            'customer_phone'=>'required',
            'weight'=>array('required','min:0'),
            'quantity'=>array('required','min:0'),
            'total_price'=>'required',
            'advance_price'=>array('required'),
            'payment_type'=>'required',
            'payment_status'=>'required',
            'order_type'=>'required',
            'order_status'=>'required',
            'delivery_date'=>'required',
            'delivery_time'=>'required',
            'branch_id'=>'required',
            'branch_code'=>'required',
            'assigned_to'=>'required',
            'priority'=>'required',
            'photo_id'=>'required',
            'flavour_id'=>'required',
            'material_id'=>'required',
            'product_id'=>'required',
            'server_sync'=>'required',
            'live_synced'=>'required',
            'is_custom'=>'required',
            'product_price'=>'required',
            'custom_image'=>'mimes:jpeg,bmp,png,jpg',

        ] ;

        $messages=  [
            'salesman.required' => 'Salesman Name is required',
            'customer_name.required' => 'Custom Name is required',
            'customer_phone.required' => 'Customer Number is required',
            'quantity.required' => 'Quantity is required',
            'weight.required' => 'Product  Weight is required',
            'advance_price.required' => 'Advance Price is required',
            'payment_type.required' => 'Please Select Payment Type',
            'payment_status.required' => 'Payment Status is required',
            'order_type.required' => 'Please Select an Order Type',
            'order_status.required' => 'Order Status is required.',
            'delivery_date.required' => 'Please provide a DELIVERY DATE',
            'delivery_time.required' => 'Please provide a DELIVERY TIME',
            'branch_id.required' => 'Branch Id is Required',
            'branch_code.required' => 'Branch Code is Required',
            'assigned_to.required' => 'Please Select Assigned Branch From Assign To list',
            'user_id.required' => 'User Id is required',
            'priority.required' => 'Please Select Order Priority',
            'photo_id.required' => 'Image is required',
            'custom_image.mimes'=>'Image Type Invalid',

        ];

        // $validator = Validator::make($request->all(),$rules,$messages);
        // if ($validator->fails())
        // {
        //     $data = ["status" => "Error","statusMessage"=>$validator->messages()->first()];
        //     return response()->json($data);

        // }
        // else
        //     {


                if($request['productAdvanced'] < $minAdvancePayment->value)
                {
                    $data = ["status" => "Error","statusMessage"=>"Minimum advance payment i.e ".$minAdvancePayment->value."  must be payed"];
                    return response()->json($data);
                }



                $customOrder = $request['customOrder'];

                $productId = $request['productId'];
                $flavourId = explode(',', $request['selectedFlavours'][0]['falvourId']);
                $materialId = explode(',', $request['selectedMaterials'][0]['materialId']);
             if( $customOrder==1)
                {

                      $product = Product::where('sku','=','PH-000')->first();
                      if(!$product)
                      {
                          $data = ["status" => "Error","statusMessage"=>"Custom Product Doesnt Exists"];
                          return response()->json($data);
                      }
                      $product_sku = $product->sku;
                      $product_name = $product->name;
                      $product_price=$request['productPrice'];
                 }
             else
                 {
                     Log::info("In Else of custom order");

                        $product = Product::where('id','=',$productId)->first();
                     Log::info("In Else of custom order after fetching product");
                        if($product) {
                            $product_sku = $product->sku;
                            $product_name = $product->name;
                            $product_price = $product->price;
                        }
                        else
                            {
                                $data = ["status" => "Error","statusMessage"=>"Please Select a Product"];
                                return response()->json($data);
                            }
                 }

            if(!empty($flavourId))
            {
                   foreach ($flavourId as $fid) {

                       $flavour = Flavour::where('id','=',$fid)->first();

                        if(!$flavour)
                           {
                               $data = ["status" => "Error","statusMessage"=>"Invalid Flavour Selected (Or Flavour Doesn't Exits)"];
                               return response()->json($data);
                           }
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                                                 }
            }

            else
             {
                        $data = ["status" => "Error","statusMessage"=>"Please Select at-least one flavour"];
                        return response()->json($data);
             }

             if(!empty($materialId))
             {
                    foreach ($materialId as $mid) {
                        $material = Material::where('id','=',$mid)->first();
                        if(!$material)
                        {
                            $data = ["status" => "Error","statusMessage"=>"Invalid Material Selected (Or Material Doesn't Exits)"];
                            return response()->json($data);
                        }
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                    }
             }
             else
             {
                    $data = ["status" => "Error","statusMessage"=>"Please Select at-least one material"];
                    return response()->json($data);
             }


                Log::info("After All the Checks");

            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if(!$branchCode)
            {
                $data = ["status" => "Error","statusMessage"=>"Branch Code Not Set in Configuration"];
                return response()->json($data);
            }

            $latestId = Order::latest('id')->first();
            $lastId = 0;
            if (is_null($latestId) || empty($latestId)) {
                $lastId = 0;
            } else {
                $lastId = (int)$latestId->id;
            }


            $request['order_status'] = 'Processed';

            // $input = $request->except(['flavour_id', 'material_id', 'product_id']);
            $input = $request;
            // $balance= $input['total_price']-$input['advance_price']-$input['discount'];
            $balance= $input['productPrice']-$input['productAdvanced']-$input['discount'];
   
            Log::info("Balacne".$balance);
                try {
                    $branch_name = Configuration::where('key','=','branch_name')->first() ;
                }
                catch (ModelNotFoundException $exception)
                {
                    return "Order Not Found ID";
                }




            if($balance==0)
            {
                Log::info("Balacne if");
                $input['pending_amount'] = 0;
                $input['pending_amount_paid_date'] = Carbon::now()->format('Y-m-d');
                $input['pending_amount_paid_time'] = Carbon::now()->format('h:i a');
                $input['payment_status']= 1;
                $input['pending_amount_paid_branch'] =$branch_name->value;

            }


            $orderStatus = OrderStatus::where('name', '=', $request['order_status'])->first();

            $input['order_status'] = $orderStatus->id;
                $order_number = $branchCode->value . '-' . (int)($lastId + 1);
            $input['order_number']=$order_number;

//
//            $input['order_number'] = $order_number;

            $input['user_id'] = $user_id;

            Log::info("Order Number ".$order_number);
            $tftime= Carbon::parse($request['deliveryTime']);
            $converted_time=$tftime->format('G:i');

            $deliverDate = $request['deliveryDate'];


            $deliverDate= $deliverDate.' '.$converted_time;

            Log::info("Converted Date and Time".$deliverDate);
            $convertedDeliveryDate = Carbon::parse($deliverDate);

            /*
             * Closing Time Shit Starts Here
             */



            //$closingTimeFrom= '23:59:00';
//            $closingTimeTo= '03:00:00';

            $closingTimeFrom=     Carbon::createFromTime(23, 59, 0, 0);
            $closingTimeTo=     Carbon::createFromTime(03, 00, 0, 0)->addDay();
            $st_time = strtotime($closingTimeFrom->toDateTimeString());
            $end_time= strtotime($closingTimeTo->toDateTimeString());

            $cur_time= time();
            Log::info("Now : ".time() . " Start Time : ".$st_time. " End Time : ".$end_time);

            if($cur_time > $st_time && $cur_time < $end_time)
            {
                $order_date= Carbon::now()->subDay()->format('Y-m-d');
                Log::info("Order Date : ".$order_date);
            }else
            {
                $order_date=Carbon::now()->format('Y-m-d');
                Log::info("Order Date else : ".$order_date);
            }
            $input['order_date'] = $order_date;



//            $deliverDate = Carbon::createFromFormat('m/d/y ', $deliverDate);;
            Log::info("Parsed Delivery Date and Time".$convertedDeliveryDate);
            $input['delivery_date'] = $convertedDeliveryDate;
            $input['branch_code']=$branchCode->value;


            Log::info('Converted Time'.$converted_time);


            if ($customOrder == 1) {
// for custom order

                Log::info("In if of custom order photo");
//                 if(!empty($request['imageFile'])){

//                 $file = $request['imageFile'];

// //                $name = time() . $file->getClientOriginalName();
//                 $name = $order_number. '.' . $file->getClientOriginalExtension();

//                 $file->move('images/Custom_Orders/', $name);

//                 $photo = Photo::create(['path' => $name]);

//                 $input['photo_id'] = $photo->id;
//                 $input['photo_path'] = $name;
//             }


            } else {
                $photo_path = Photo::findOrFail($input['photo_id']);
//                $input['photo_path'] = Str::substr($photo_path->path, 8);
                $input['photo_path'] = $photo_path->path;
            }

            $input['salesman'] = $input['salesmanName'];
            $input['customer_name'] = $input['customerName'];
            $input['weight'] = $input['productWeight'];
            $input['quantity'] = $input['productQuantity'];
            $input['total_price'] = $input['productPrice'];
            $input['customer_email'] = $input['customerEmail'] ;
            $input['customer_phone'] = $input['customerPhone'] ;
            $input['advance_price'] = $input['productAdvanced'] ;
            $input['payment_type'] = $input['paymentType']['label'];
            $input['order_type'] = $input['orderType']['label'];
            $input['delivery_time'] = $input['deliveryTime'] ;


            // $input['remarks'] ;
            // $input['branch_id'] ;



            $input['assigned_to'] = $input['assignedTo']['code'] ;




            // $input['is_active'] ;


            $input['priority'] = $input['orderPriority']['value'] ;


            // $input['photo_id'] ;
            // $input['live_synced'] ;
            // $input['photo_path'] ;
            // $input['server_sync'] ;

            $input['is_custom'] = $input['customOrder'] ;


            // $input['instructions'] ;


            // $input['final_image'] ;

            $input['pending_amount'] ;
            $input['pending_amount_paid_date'] ;
            $input['pending_amount_paid_branch'] ;

            // $input['delivery_sms'] ;
            // $input['delivery_sms_response'] ;


            $order = Order::Create($input);
//            $productId = $request->input('product_id');
//            $flavourId = explode(',', $request->input('flavour_id'));
//            $materialId = explode(',', $request->input('material_id'));

//
//        $flavourId=$request->input('flavour_id');
//       $materialId=$request->input('material_id');


            if ($order->wasRecentlyCreated) {

//                foreach ($productId as $id) {

//                    $product = Product::findOrFail($id);
//                    $name = $product->name;
//
//                    if ($customOrder == 1) {
//                        $price = $input['product_price'];
//                    } else {
//                        $price = $product->price;
//                    }

                    $order->products()->sync([$product_sku => ['product_name' => $product_name, 'product_price' => $product_price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);


                foreach ($flavourId as $fid) {
                    $flavour = Flavour::findOrFail($fid);
                    $name = $flavour->name;
                    $price = $flavour->price;
                    $order->flavours()->attach([$flavour->sku => ['flavour_name' => $name, 'flavour_price' => $price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }
                foreach ($materialId as $mid) {
                    $material = Material::findOrFail($mid);
                    $name = $material->name;
                    $price = $material->price;
                    $order->materials()->attach([$material->sku => ['material_name' => $name, 'material_price' => $price]]);
//                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }

//            Session::flash('created_order', 'Order Created');

//        $this->generatePdf($order->id);

                $data = ["status" => "Success", "statusMessage" => "Message", "payLoad" => $order_number];

                return response()->json($data);
            }

        // } //if
    }


    public function getOrderInformation($id)
    {
        $positem_total_payment = 0;
        $custom_kit_name = null;

        $order = Order::join('order_product','orders.order_number','=','order_product.order_number')->select('orders.*','order_product.product_name')->findOrFail($id);
        $custom_kit = OrderProduct::where('order_number',$id)->get();

        if($custom_kit->count() > 1){
           $custom_kit_name = 'Custom kit';
        }

        // $positem = CakePosItemTemp::where('order_number', $id)->first();

        $positem = PosSaleTemp::where('cake_invoice', $order->order_number)->first();
        if ($positem) {
            // The record exists, you can access the sale_id value
            $positem_sale_id = $positem->sale_id;

            $positem_total_payment = PosSalePaymentsTemp::where('sale_id', $positem_sale_id)->value('payment_amount');

            $total = $positem_total_payment;
        }else{

            $total = bcmul($order->total_price,$order->quantity);
        }
        $data = ["customer_name" => $order->customer_name,"photo_path" => $order->photo_path,"product_name" => $order->product_name, "weight" => $order->weight, "total_price" => $order->total_price,'is_custom'=>$order->is_custom,'pos_item_total'=>$positem_total_payment,'total'=>$total,'custom_kit_name'=>$custom_kit_name];


        $customer_phone= $order->customer_phone;


        $this->sendOrderSms($customer_phone);

        return response()->json($data);
    }

    public function sendOrderSms($customer_phone)
    {
        try{
            $smsTemplate=Configuration::where('key','=','Sms_Message')->first();


        }catch (\Exception $exception)
        {
            Log::info("Error Getting Sms template Database ".$exception->getMessage());
        }

            //for testing
//        $customer_phone="03461231515";
        $client = new \GuzzleHttp\Client(['base_uri' => 'http://119.160.92.2:7700/sendsms_url.html', 'headers' => ['Accept' => 'application/json']]);


        try {
            $response = $client->request('GET', '?Username='.env('SMS_API_URL').'&Password='.env('SMS_API_PASS').'&From='.env('SMS_API_FROM_MASK').'&To='.$customer_phone.'&Message='.$smsTemplate->value);
        } catch (GuzzleException $e) {

            Log::error("Error Syncing Products From Live".$e->getMessage());
            return back()->withError("Error Syncing Products From Live");
        }

    }


}
