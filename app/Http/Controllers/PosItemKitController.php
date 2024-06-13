<?php

namespace App\Http\Controllers;

use App\Branch;
use App\CakePosItemTemp;
use App\Configuration;
use App\Flavour;
use App\FlavourCategory;
use App\Item;
use App\ItemKitItems;
use App\ItemKitItemsOnline;
use App\ItemKits;
use App\ItemKitsOnline;
use App\ItemTax;
use App\Material;
use App\Order;
use App\OrderProduct;
use App\OrderStatus;
use App\OrderType;
use App\PaymentType;
use App\Photo;
use App\PosSaleItemKitItems;
use App\PosSaleItemsTemp;
use App\PosSalePaymentsTemp;
use App\PosSaleTemp;
use App\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;



class PosItemKitController extends Controller
{
    //

    public function index()
    {
        $itemkits = ItemKits::with('items')->paginate(15);


        return view('admin.itemkits.index', compact('itemkits'));
    }

    public function sync(){
            // $online_kits = ItemKitsOnline::where('branch_code','th002');
            $online_db_connection = DB::connection('online')->getPdo();

         if($online_db_connection){

            $online_kits = ItemKitsOnline::with('items')->where('branch_code','th002')->get();
            $online_kits_items = ItemKitItemsOnline::all();

        $item_kit_items  = [];

        foreach($online_kits as $item){
            $items = $item->items;
            $item_kit_items = array_merge($item_kit_items, $items->toArray());
        }

            $local_kits = ItemKits::all();
            $local_kits_name = ItemKits::all()->pluck('name')->toArray();
            $localJson = $local_kits->toJson();
            $onlineJson = json_encode($online_kits);
            $diff = strcmp($localJson, $onlineJson);
            if($diff){


                
                try {
                    // Disable foreign key checks
                    DB::beginTransaction();
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    // Truncate all tables
                    // ItemKits::truncate();
                    // ItemKitItems::truncate();

                    /**
                     * 
                     * Adding data in item Kits table
                     *
                     */


                     // Process online kits
            foreach($online_kits->toArray() as $kit){
                 if (in_array($kit['name'], $local_kits_name)) {
                 
                  $local_kit = ItemKits::where('name', $kit['name'])->value('deleted');
          
                  if($local_kit){
          
                      ItemKits::where('name', $kit['name'])->update(['deleted' => 0 ]);
          
                  }
                 
                  // Get local kit items
                  $local_kit_items = ItemKits::with('items')->where('name', $kit['name'])->first()->items->toArray();
          
                  // Get online kit items
                  $online_kit_items = $kit['items'];
                  
                  // Compare local and online kit items
                  foreach ($online_kit_items as $online_item) {
                      $online_item_found = false; // Flag to track if the online item is found locally
                      foreach ($local_kit_items as &$local_item) {
                          if ($local_item['item_id'] === $online_item['item_id']) {
                            ItemKitItems::where('item_id', $local_item['item_id'])
                                      ->where('item_kit_id', $local_item['item_kit_id'])
                                      ->update(['deleted' => 0]);
                              // Update quantity if it differs
                              if ($local_item['quantity'] != $online_item['quantity']) {
                                  $local_item['quantity'] = $online_item['quantity'];
                                  // Update the local item with the new quantity
                                  ItemKitItems::where('item_id', $local_item['item_id'])
                                      ->where('item_kit_id', $local_item['item_kit_id'])
                                      ->update(['quantity' => $online_item['quantity']]);
                              }
                              $online_item_found = true;
                              break; // Move to the next online item
                          }
                      }
                      // If online item not found, insert the local item into ItemKitItems with local item kit ID
                      if (!$online_item_found) {
                          ItemKitItems::create([
                              'item_kit_id' => $kit['id'], // Assuming $kit contains the local kit data
                              'item_id' => $online_item['item_id'],
                              'quantity' => $online_item['quantity']
                          ]);
                      }
                  }
                  // Mark local items with no corresponding online item as deleted
                     foreach ($local_kit_items as $local_item) {
                          if (!in_array($local_item['item_id'], array_column($online_kit_items, 'item_id'))) {
                              ItemKitItems::where('item_id', $local_item['item_id'])
                              ->where('item_kit_id', $local_item['item_kit_id'])
                              ->update(['deleted' => 1]);
                      }
                  }
                  continue; // Move to the next kit
              }
          
              // If the kit is not found locally, create it along with its items
              $createdKit = ItemKits::create($kit);
              if ($createdKit) {
                  foreach ($kit['items'] as $item) {
                      ItemKitItems::create($item);
                  }
              }
          }
          
          // Process kits that exist only in the local database
          foreach ($local_kits_name as $kit_name) {
              if (!in_array($kit_name, array_column($online_kits->toArray(), 'name'))) {
                  // Mark items kit deleted which are not in online kit
                  ItemKits::where('name', $kit_name)->update(['deleted' => 1]);
                  // Mark items associated with this local kit as deleted
                  $local_kit_items = ItemKits::with('items')->where('name', $kit_name)->first()->items->toArray();
                  foreach ($local_kit_items as $local_item) {
                      ItemKitItems::where('item_id', $local_item['item_id'])
                          ->where('item_kit_id', $local_item['item_kit_id'])
                          ->update(['deleted' => 1]);
                  }
              }
          }


                    // Commit transaction
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');

                    DB::commit();

                    return response()->json(['success' => true, 'message' => 'Update data successfully']);

                } catch (Exception $e) {
                    // Rollback transaction if any error occurs
                    DB::rollback();
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    Log::error('Error inserting data: ' . $e->getMessage()); // Log the error
                    return response()->json(['success' => false, 'error' => 'Failed to update data.']);
                }

            } else {

                return response()->json(['success' => true, 'error' => 'Already up to date.']);

            }
        }else{

            return response()->json(['success' => true, 'error' => 'Online db not connected.']);

        }
    }


    public  function showCategories()
    {


        // $categories = DB::table('categories')
        //             ->join('photos','categories.photo_id','=','photos.id')
        //             ->where([['categories.parent_id','!=','0'],['categories.is_active','=','1']])
        //             ->select('categories.id','categories.name','photos.path')
        //             ->get();
            /*
             * Above Query fetches the all the categories that are active and returns its id , name and image.name
             * which are stored in /images/{image name here}
             */
            $categories = ItemKits::where('deleted',0)->get();


        return  response()->json($categories);

    }

    public function getProductData($id)
    {

        $items  = ItemKits::with('items')->where('item_kit_id',$id)->first();

        $all_kits = ItemKits::with('items')->get();

        foreach ($items->items as $key => $itemKitItem) {
            $itemKitItemId = $itemKitItem->item_id;
            $positem = Item::where('item_id', $itemKitItemId)->first();
        
            $items->items[$key] = $itemKitItem->toArray(); // Convert $itemKitItem to array
            $items->items[$key] = array_merge($items->items[$key], $positem->toArray());
        }

        foreach ($all_kits as $kit) {
            foreach ($kit->items as $key => $itemKitItem) {
                $itemKitItemId = $itemKitItem->item_id;
                $positem = Item::where('item_id', $itemKitItemId)->first();
        
                $kit->items[$key] = $itemKitItem->toArray(); // Convert $itemKitItem to array
                $kit->items[$key] = array_merge($kit->items[$key], $positem->toArray());
            }
        }


        $branches = Branch::where('is_active', '=', '1')->get();

            if($branches->count()<0)
            {
                $data = ["status" => "Error","statusMessage"=>"No Branches In Database "];
                return response()->json($data);
            }
            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if($branchCode->count()<0)
            {
                $data = ["status" => "Error","statusMessage"=>"Branch Code Not Set In Configuration"];
                return response()->json($data);
            }

            if($branches->count()>0) {
                foreach ($branches as $branch) {
                    if ($branch->code == $branchCode->value) {
                        $branch['is_current'] = true;
                    } else {
                        $branch['is_current'] = false;
                    }
                }
            }

        $flavourCategories = FlavourCategory::all();
        $flavours = Flavour::all();
        $materials= Material::all();

        if($flavours->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Flavours In database"];
            return response()->json($data) ;
        }
        if($materials->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Materials In database"];
            return response()->json($data) ;
        }
        if($flavourCategories->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Materials In database"];
            return response()->json($data) ;
        }

        $priorities = Configuration::where('key','=','Priority_key')->get();
        if($priorities->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"Priorities Not Set In Configuration"];
            return response()->json($data);
        }

        $paymentTypes = PaymentType::all();
        $orderTypes = OrderType::all();

        $minAdvancePayment = Configuration::where('key','=','min_advance')->first();
        if(empty($minAdvancePayment))
        {
            $data = ["status" => "Error","statusMessage"=>"Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }else {
            $minAdvance = ['min_Advance' => $minAdvancePayment->value];
        }

        $product['flavours'] = $flavours;
        $product['materials'] = $materials;
        $data = [ "branches" => $branches,"flavour_categories"=>$flavourCategories,"priorities"=>$priorities,"payment_type"=>$paymentTypes,"order_types"=>$orderTypes,"min_advance"=>$minAdvance,'items'=>$items,'all_kits'=>$all_kits ];

        return response()->json($data);

    }

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

            // 'salesman'=>'required',
            'customer_name'=>'required',
            'customer_phone'=>'required',
            // 'weight'=>array('required','min:0'),
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
            // 'photo_id'=>'required',
            // 'flavour_id'=>'required',
            // 'material_id'=>'required',
            'product_id'=>'required',
            'server_sync'=>'required',
            'live_synced'=>'required',
            'is_custom'=>'required',
            'product_price'=>'required',
            // 'custom_image'=>'mimes:jpeg,bmp,png,jpg',

        ] ;

        $messages=  [
            'salesman.required' => 'Salesman Name is required',
            'customer_name.required' => 'Custom Name is required',
            'customer_phone.required' => 'Customer Number is required',
            'quantity.required' => 'Quantity is required',
            // 'weight.required' => 'Product  Weight is required',
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
                        $itemKit =ItemKits::where('item_kit_id',$productId)->first();
                        $itemKit_name = $itemKit->name;

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
                // $photo_path = Photo::findOrFail($input['photo_id']);
//                $input['photo_path'] = Str::substr($photo_path->path, 8);
                // $input['photo_path'] = $photo_path->path;
            }

            $input['is_cake'] = '0';
            $input['weight'] = '0';
            $order = Order::Create($input);
//            $productId = $request->input('product_id');
//            $flavourId = explode(',', $request->input('flavour_id'));
//            $materialId = explode(',', $request->input('material_id'));

//
//        $flavourId=$request->input('flavour_id');
//       $materialId=$request->input('material_id');

            if ($order->wasRecentlyCreated) {

                    $order->products()->sync(['Item kit' => ['product_name' => $itemKit_name, 'product_price' => $product_price]]);

                $cake_invoice_number = Order::latest('id')->value('order_number');

                $cake_order_info = Order::latest('id')->first();
                 
                $cake_item = [
                    "item_id" => 4,
                    "name" => "MIX",
                    "category" => "MIX",
                    "supplier_id" => null,
                    "item_number" => "1",
                    "description" => "",
                    "cost_price" => $cake_order_info->total_price,
                    "unit_price" => $cake_order_info->total_price,
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

                $itemKitItems = json_decode($request->input('items'), true);
                foreach($itemKitItems as $key=>$item){
                    $itemKitItems[$key]['quantity']= bcmul($item['quantity'],$request->quantity);
                }
                $kit_quantity = $request->quantity;
                $pos = json_decode($request->cartItems, true);
                $pos_items = $itemKitItems;
                $data['cart'] = $pos_items;
                $all_price_data = $this->get_all_price_data($pos_items);
                $data['subtotal'] = $all_price_data['subtotal'];
                $data['tax_exclusive_subtotal'] = $all_price_data['totalWithoutTax'];
                $data['total_tax'] = $all_price_data['totalTax'];
                $data['discount'] = 0;
                $data['total'] = $all_price_data['total'];
                $data['transaction_time'] = now()->format('Y-m-d H:i');
                $dateTime = now()->format('Y-m-d H:i:s');
                $payment_type = "Cash";
                $employee_id = auth()->user()->id;
                $branchcode = strtolower(str_replace('-', '', env("BRANCH_CODE")));
                $comment = '';
                $data['fbr_fee'] = Configuration::where('key', 'fbr_fee')->value('value');
                $invoice_number = $this->generate_invoice_number();
                $data['invoice_number'] = $invoice_number;
                $data['payment_type'] = $payment_type;
                $data['InvoiceType'] = 1;
                //if payment type cash payment mode = 1
                $data['Payment_mode'] = 1;
                // $fbr_invoice_number = $this->postDataApiFBR($data);
                $total = bcadd($data['total'],$cake_order_info->total_price,2);

                $payments = [
                    "payment_type" => $payment_type,
                    "payment_amount" => $data['total']
                ];
                $sales_data = [
                    'order_id' => $cake_order_info->id,
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
                    'cake_invoice' => $cake_invoice_number

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
                    
                     foreach($pos_items as $line =>$item){
                    
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
                              'description' =>Str::limit($item['description'], 30),
                              'serialnumber' => Str::limit($serialnumber, 30),
                              'quantity_purchased' => $item['quantity'],
                              'discount_percent' => $discount,
                              'item_cost_price' => $item['cost_price'],
                              // 'item_unit_price' => $item['unit_price'],
                              'item_unit_price' => $item['unit_price'],
                              'item_location' => '1'
                          ];
                    
                          PosSaleItemsTemp::create($sales_items_data);
                    
                             }
                    
                    
                     $order_number = Order::latest('id')->value('order_number');
                    
                         $dt = [
                          'order_number' => $order_number,
                          'sale_id' => $sale_id,
                        //   'fbr_invoice_number' => $fbr_invoice_number,
                          'branch_code' => $branchcode
                         ];
                    
                    
                     CakePosItemTemp::create($dt);
    

                    DB::commit();


                } catch (\Exception $e) {

                    DB::rollback();

                    // Log the exception
                    Log::error($e->getMessage());

                    // Return a generic error message
                    return response()->json(['error' => 'An unexpected error occurred.'], 500);
                }

                $data = ["status" => "Success", "statusMessage" => "Message", "payLoad" => $order_number];

                return response()->json($data);
            }

        }
    }

    public function storeCustomOrder(Request $request)
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

           // 'salesman'=>'required',
           'customer_name'=>'required',
           'customer_phone'=>'required',
           // 'weight'=>array('required','min:0'),
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
           // 'photo_id'=>'required',
           // 'flavour_id'=>'required',
           // 'material_id'=>'required',
           'product_id'=>'required',
           'server_sync'=>'required',
           'live_synced'=>'required',
           'is_custom'=>'required',
        //    'product_price'=>'required',
           // 'custom_image'=>'mimes:jpeg,bmp,png,jpg',

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


                $itemKitItems = json_decode($request->input('items'), true);
                $itemKits=[];
                foreach($itemKitItems as $key=>$item){
                    $itemKit =ItemKits::where('item_kit_id',$item['item_kit_id'])->first();

                    $itemKits[$key]= $itemKit;

                }



                $productId = $request->input('product_id');
           
                     Log::info("In Else of custom order");


                     Log::info("In Else of custom order after fetching product");

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

            Log::info("Parsed Delivery Date and Time".$convertedDeliveryDate);
            $input['delivery_date'] = $convertedDeliveryDate;
            $input['branch_code']=$branchCode->value;


            Log::info('Converted Time'.$converted_time);



            $input['is_cake'] = '0';
            $input['weight'] = '0';
            $order = Order::Create($input);


            if ($order->wasRecentlyCreated) {

                // foreach($itemKits as $itemKit){
                    $order->products()->sync(['Custom item kit' => ['product_name' => 'Custom kit', 'product_price' => '0']]);
                // }
                $cake_invoice_number = Order::latest('id')->value('order_number');

                // foreach($itemKits as $itemKit){
                //     $data=['order_number'=> $cake_invoice_number, 'product_sku'=> 'Custom item kit', 'product_name'=> $itemKit->name, 'product_price' => '0'];
                //     OrderProduct::insert($data);
                // }


                $cake_order_info = Order::latest('id')->first();
                 
                $cake_item = [
                    "item_id" => 4,
                    "name" => "MIX",
                    "category" => "MIX",
                    "supplier_id" => null,
                    "item_number" => "1",
                    "description" => "",
                    "cost_price" => $cake_order_info->total_price,
                    "unit_price" => $cake_order_info->total_price,
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

                $itemKitItems = json_decode($request->input('items'), true);
                $kit_items = $itemKitItems;

                foreach($itemKitItems as $key=>$item){
                    (!isset($item['kit_quantity']) &&  $item['kit_quantity'] = '1');
                    $quantity = bcmul($item['quantity'],$request->quantity);
                    $itemKitItems[$key]['quantity']= bcmul($item['kit_quantity'],$quantity);
                }

                 $collection = collect($itemKitItems);
                 // Group the collection by the combination of 'item_id' and 'item_kit_id'
                 $grouped = $collection->groupBy(['item_id']);
                 // Map through the grouped collection and sum up the quantities
                 $items_with_add_up_quan = $grouped->map(function ($group) {
                 $sum = $group->sum('quantity');
                 $firstItem = $group->first(); // You can use any item in the group since they all have the same 'item_id' and 'item_kit_id'
                 $firstItem['quantity'] = $sum;
                 unset($firstItem['kit_quantity']);
                 return $firstItem;
                 })->values()->all();
                $kit_quantity = $request->quantity;
                $pos = json_decode($request->cartItems, true);
                $pos_items = $items_with_add_up_quan;
                $data['cart'] = $pos_items;
                $all_price_data = $this->get_all_price_data($pos_items);
                $data['subtotal'] = $all_price_data['subtotal'];
                $data['tax_exclusive_subtotal'] = $all_price_data['totalWithoutTax'];
                $data['total_tax'] = $all_price_data['totalTax'];
                $data['discount'] = 0;
                $data['total'] = $all_price_data['total'];
                $data['transaction_time'] = now()->format('Y-m-d H:i');
                $dateTime = now()->format('Y-m-d H:i:s');
                $payment_type = "Cash";
                $employee_id = auth()->user()->id;
                $branchcode = strtolower(str_replace('-', '', env("BRANCH_CODE")));
                $comment = '';
                $data['fbr_fee'] = Configuration::where('key', 'fbr_fee')->value('value');
                $invoice_number = $this->generate_invoice_number();
                $data['invoice_number'] = $invoice_number;
                $data['payment_type'] = $payment_type;
                $data['InvoiceType'] = 1;
                //if payment type cash payment mode = 1
                $data['Payment_mode'] = 1;
                // $fbr_invoice_number = $this->postDataApiFBR($data);
                $total = bcadd($data['total'],$cake_order_info->total_price,2);

                $payments = [
                    "payment_type" => $payment_type,
                    "payment_amount" => $data['total']
                ];

                $sales_data = [
                    'order_id' => $cake_order_info->id,
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
                    'cake_invoice' => $cake_invoice_number

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
                    
                     foreach($pos_items as $line =>$item){
                    
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
                              'description' =>Str::limit($item['description'], 30),
                              'serialnumber' => Str::limit($serialnumber, 30),
                              'quantity_purchased' => $item['quantity'],
                              'discount_percent' => $discount,
                              'item_cost_price' => $item['cost_price'],
                              // 'item_unit_price' => $item['unit_price'],
                              'item_unit_price' => $item['unit_price'],
                              'item_location' => '1',
                          ];
                    
                          PosSaleItemsTemp::create($sales_items_data);
                    
                             }
                    
                             foreach($kit_items as $line =>$item){
                                (!isset($item['kit_quantity']) &&  $item['kit_quantity'] = '1');
                                $kit_items_data = [
                                    'item_kit_id' => $item['item_kit_id'],
                                    'item_id' =>$item['item_id'],
                                    'quantity' => $item['quantity'],
                                    'kit_quantity' => $item['kit_quantity'],
                                    'cake_invoice' => $cake_invoice_number,
                                ];
                          
                                PosSaleItemKitItems::create($kit_items_data);
                          
                                   }
                    
                     $order_number = Order::latest('id')->value('order_number');
                    
                         $dt = [
                          'order_number' => $order_number,
                          'sale_id' => $sale_id,
                        //   'fbr_invoice_number' => $fbr_invoice_number,
                          'branch_code' => $branchcode
                         ];
                    
                    
                     CakePosItemTemp::create($dt);


                    DB::commit();


                } catch (Exception $e) {

                    DB::rollback();

                    // Log the exception
                    Log::error($e->getMessage());

                    // Return a generic error message
                    return response()->json(['error' => 'An unexpected error occurred.'], 500);
                }

                $data = ["status" => "Success", "statusMessage" => "Message", "payLoad" => $order_number];

                return response()->json($data);
            }

        }
    }

    public function getCustomProductData(){


        $all_kits = ItemKits::with('items')->where('deleted',0)->get();

        foreach ($all_kits as $kit) {
            foreach ($kit->items as $key => $itemKitItem) {
                $itemKitItemId = $itemKitItem->item_id;
                $positem = Item::where('item_id', $itemKitItemId)->first();
        
                $kit->items[$key] = $itemKitItem->toArray(); // Convert $itemKitItem to array
                $kit->items[$key] = array_merge($kit->items[$key], $positem->toArray());
            }
        }


        $branches = Branch::where('is_active', '=', '1')->get();

            if($branches->count()<0)
            {
                $data = ["status" => "Error","statusMessage"=>"No Branches In Database "];
                return response()->json($data);
            }
            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if($branchCode->count()<0)
            {
                $data = ["status" => "Error","statusMessage"=>"Branch Code Not Set In Configuration"];
                return response()->json($data);
            }

            if($branches->count()>0) {
                foreach ($branches as $branch) {
                    if ($branch->code == $branchCode->value) {
                        $branch['is_current'] = true;
                    } else {
                        $branch['is_current'] = false;
                    }
                }
            }

        $flavourCategories = FlavourCategory::all();
        $flavours = Flavour::all();
        $materials= Material::all();

        if($flavours->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Flavours In database"];
            return response()->json($data) ;
        }
        if($materials->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Materials In database"];
            return response()->json($data) ;
        }
        if($flavourCategories->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Materials In database"];
            return response()->json($data) ;
        }

        $priorities = Configuration::where('key','=','Priority_key')->get();
        if($priorities->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"Priorities Not Set In Configuration"];
            return response()->json($data);
        }

        $paymentTypes = PaymentType::all();
        $orderTypes = OrderType::all();

        $minAdvancePayment = Configuration::where('key','=','min_advance')->first();
        if(empty($minAdvancePayment))
        {
            $data = ["status" => "Error","statusMessage"=>"Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }else {
            $minAdvance = ['min_Advance' => $minAdvancePayment->value];
        }

        $data = [ "branches" => $branches,"flavour_categories"=>$flavourCategories,"priorities"=>$priorities,"payment_type"=>$paymentTypes,"order_types"=>$orderTypes,"min_advance"=>$minAdvance,'all_kits'=>$all_kits ];

        return response()->json($data);


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

            $sub = bcmul($item['cost_price'], $item['quantity'], 2);
            $subtotal += $sub;

            $totalP = bcmul($item['unit_price'], $item['quantity']);
            $total += $totalP;

            $taxAmount = bcmul($totalP, $tax_percent / 100);
            $totalWithoutTax += ($totalP - $taxAmount);

            // Add tax amount to the total tax variable
            $totalTax += $taxAmount;

        }
        return [
            'subtotal' => $subtotal,
            'total' => $total,
            'totalWithoutTax' => $totalWithoutTax,
            'totalTax' => $totalTax
        ];
    }


    public function generate_invoice_number($code = null)
    {

        if (empty($code)) {
            $code = now()->format('YmdHis');
        }

        return env('BRANCH_CODE') . "-" . env('SYSTEM_CODE') . $code . mt_rand(100, 999);

    }

    public function getItemKitInfo(Request $request)
    {

        $kitId = $request->kit_id;

        try {
            $itemkit = ItemKits::with('items')->where('item_kit_id', $kitId)->firstOrFail();
            $items = $itemkit->items->toArray();

            $itemIds = []; // Initialize an empty array to store item IDs
            foreach ($items as $item) {
                $itemIds[] = $item['item_id']; // Add each item ID to the array
            }
            $results = Item::whereIn('item_id', $itemIds)->get();

            return $results;

        } catch (\Exception $exception) {
            Log::error("Order Details Not Found " . $exception->getMessage());
            return response()->json(['error' => 'Failed to retrieve items'], 500);
        }

    }

    public function uploadImage(Request $request){

          // Validate the uploaded file
          $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store the uploaded file
        $file = $request->file('image');
        $image_name = time(). '.' . $file->getClientOriginalExtension();
        $file->move('images/item_kits/', $image_name);
        ItemKits::where('item_kit_id',$request->kit_id)->update(['image'=>$image_name]);

        // Display a success message
        return back()->with('success', 'Image uploaded successfully!');

    }


}
