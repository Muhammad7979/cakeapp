<?php

namespace App\Http\Controllers;

use App\Item;
use App\Order;
use App\Photo;
use App\Flavour;
use App\ItemTax;
use App\PosSale;
use App\Product;
use App\Material;
use App\OrderStatus;
use App\PosSaleTemp;
use App\PosSaleItems;
use App\Configuration;
use App\PosSalePayments;
use App\PosSaleItemsTemp;
use App\CakeSuspended;
use App\CakeSuspendedItems;
use Illuminate\Support\Str;
use App\PosSalePaymentsTemp;
use Illuminate\Http\Request;
use App\CakeSuspendedItemsTaxes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\CakeSuspendedPayments;
use App\PosSaleItemsTaxesTemp;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class PosSaleController extends Controller
{

    public function sale(Request $request)
    {
        $minAdvancePayment = Configuration::where('key', '=', 'min_advance')->first();
        if (!$minAdvancePayment) {
            $data = ["status" => "Error", "statusMessage" => "Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }

        if (!Auth::user()) {
            $data = ["status" => "Error", "statusMessage" => "Please Login To Place an Order"];
            return response()->json($data);
        } else {
            $user_id = auth()->user()->id;
        }

        $rules = [

            'salesman' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'weight' => array('required', 'min:0'),
            'quantity' => array('required', 'min:0'),
            'total_price' => 'required',
            'payment_type' => 'required',
            'payment_status' => 'required',
            'order_type' => 'required',
            'order_status' => 'required',
            'delivery_date' => 'required',
            'delivery_time' => 'required',
            'branch_id' => 'required',
            'branch_code' => 'required',
            'assigned_to' => 'required',
            'priority' => 'required',
            'photo_id' => 'required',
            'flavour_id' => 'required',
            'material_id' => 'required',
            'product_id' => 'required',
            'server_sync' => 'required',
            'live_synced' => 'required',
            'is_custom' => 'required',
            'product_price' => 'required',
            'custom_image' => 'mimes:jpeg,bmp,png,jpg',

        ];

        $messages = [
            'salesman.required' => 'Salesman Name is required',
            'customer_name.required' => 'Custom Name is required',
            'customer_phone.required' => 'Customer Number is required',
            'quantity.required' => 'Quantity is required',
            'weight.required' => 'Product  Weight is required',

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
            'custom_image.mimes' => 'Image Type Invalid',

        ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $data = ["status" => "Error", "statusMessage" => $validator->messages()->first()];
            return response()->json($data);

        } else {


            if ($request->input('advance_price') < $minAdvancePayment->value) {
                $data = ["status" => "Error", "statusMessage" => "Minimum advance payment i.e " . $minAdvancePayment->value . "  must be payed"];
                return response()->json($data);
            }





            $customOrder = $request->input('is_custom');

            $productId = $request->input('product_id');
            $flavourId = explode(',', $request->input('flavour_id'));
            $materialId = explode(',', $request->input('material_id'));

            if ($customOrder == 1) {

                $product = Product::where('sku', '=', 'PH-000')->first();
                if (!$product) {
                    $data = ["status" => "Error", "statusMessage" => "Custom Product Doesnt Exists"];
                    return response()->json($data);
                }
                $product_sku = $product->sku;
                $product_name = $product->name;
                $product_price = $request->input('product_price');
            } else {
                Log::info("In Else of custom order");

                $product = Product::where('id', '=', $productId)->first();
                Log::info("In Else of custom order after fetching product");
                if ($product) {
                    $product_sku = $product->sku;
                    $product_name = $product->name;
                    $product_price = $product->price;
                } else {
                    $data = ["status" => "Error", "statusMessage" => "Please Select a Product"];
                    return response()->json($data);
                }
            }

            if (!empty($flavourId)) {
                foreach ($flavourId as $fid) {

                    $flavour = Flavour::where('id', '=', $fid)->first();

                    if (!$flavour) {
                        $data = ["status" => "Error", "statusMessage" => "Invalid Flavour Selected (Or Flavour Doesn't Exits)"];
                        return response()->json($data);
                    }
                    //                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }
            } else {
                $data = ["status" => "Error", "statusMessage" => "Please Select at-least one flavour"];
                return response()->json($data);
            }

            if (!empty($materialId)) {
                foreach ($materialId as $mid) {
                    $material = Material::where('id', '=', $mid)->first();
                    if (!$material) {
                        $data = ["status" => "Error", "statusMessage" => "Invalid Material Selected (Or Material Doesn't Exits)"];
                        return response()->json($data);
                    }
                    //                    $order->products()->attach([$id=>['product_name'=>$name]]);
                }
            } else {
                $data = ["status" => "Error", "statusMessage" => "Please Select at-least one material"];
                return response()->json($data);
            }


            Log::info("After All the Checks");

            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if (!$branchCode) {
                $data = ["status" => "Error", "statusMessage" => "Branch Code Not Set in Configuration"];
                return response()->json($data);
            }

            $latestId = Order::latest('id')->first();
            $lastId = 0;
            if (is_null($latestId) || empty($latestId)) {
                $lastId = 0;
            } else {
                $lastId = (int) $latestId->id;
            }




            $input = $request->except(['flavour_id', 'material_id', 'product_id']);


            $balance = $input['total_price'] - $input['advance_price'] - $input['discount'];
            Log::info("Balacne" . $balance);
            try {
                $branch_name = Configuration::where('key', '=', 'branch_name')->first();
            } catch (ModelNotFoundException $exception) {
                return "Order Not Found ID";
            }




            if ($balance == 0) {
                Log::info("Balacne if");
                $input['pending_amount'] = 0;
                $input['pending_amount_paid_date'] = Carbon::now()->format('Y-m-d');
                $input['pending_amount_paid_time'] = Carbon::now()->format('h:i a');
                $input['payment_status'] = 1;
                $input['pending_amount_paid_branch'] = $branch_name->value;

            }
            $input['pending_amount'] = $balance;

            $input['payment_status'] = 0;
            $input['pending_amount_paid_branch'] = $branch_name->value;

            $orderStatus = OrderStatus::where('name', '=', $request->input('order_status'))->first();

            $input['order_status'] = $orderStatus->id;
            $order_number = $branchCode->value . '-' . (int) ($lastId + 1);
            $input['order_number'] = $order_number;

            $input['user_id'] = $user_id;


            Log::info("Order Number " . $order_number);
            $tftime = Carbon::parse($request->input('delivery_time'));
            $converted_time = $tftime->format('G:i');

            $deliverDate = $request->input('delivery_date');
            $deliverDate = $deliverDate . ' ' . $converted_time;
            Log::info("Converted Date and Time" . $deliverDate);
            $convertedDeliveryDate = Carbon::parse($deliverDate);

            $closingTimeFrom = Carbon::createFromTime(23, 59, 0, 0);
            $closingTimeTo = Carbon::createFromTime(03, 00, 0, 0)->addDay();

            $st_time = strtotime($closingTimeFrom->toDateTimeString());
            $end_time = strtotime($closingTimeTo->toDateTimeString());

            $cur_time = time();
            Log::info("Now : " . time() . " Start Time : " . $st_time . " End Time : " . $end_time);

            if ($cur_time > $st_time && $cur_time < $end_time) {
                $order_date = Carbon::now()->subDay()->format('Y-m-d');
                Log::info("Order Date : " . $order_date);
            } else {
                $order_date = Carbon::now()->format('Y-m-d');
                Log::info("Order Date else : " . $order_date);
            }

            $input['order_date'] = $order_date;


            Log::info("Parsed Delivery Date and Time" . $convertedDeliveryDate);
            $input['delivery_date'] = $convertedDeliveryDate;
            $input['branch_code'] = $branchCode->value;


            Log::info('Converted Time' . $converted_time);


            if ($customOrder == 1) {

                Log::info("In if of custom order photo");
                $file = $request->file('custom_image');

                $name = $order_number . '.' . $file->getClientOriginalExtension();

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
            $order_id = Order::latest('id')->value('id');


            if ($order->wasRecentlyCreated) {

                $order->products()->sync([$product_sku => ['product_name' => $product_name, 'product_price' => $product_price]]);


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
                }

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

                $pos = json_decode($request->cartItems, true);
                $pos_items = array_merge($pos, [$cake_item]);
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
                $orderprice = Order::latest('id')->select('total_price', 'advance_price')->first();
                // $total = bcadd($input['total_price'], 2);
                $advance_price = $cake_order_info->advance_price;

                $payments = [
                    "payment_type" => $payment_type,
                    "payment_amount" => $data['total']
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

                    foreach ($pos_items as $line => $item) {

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

                        PosSaleItemsTemp::create($sales_items_data);

                    }



                    DB::commit();

                } catch (\Exception $e) {

                    DB::rollback();

                    // Log the exception
                    Log::error($e->getMessage());

                    // Return a generic error message
                    return response()->json(['error' => 'An unexpected error occurred.'], 500);
                }


                $data = ["status" => "Success", "statusMessage" => "Order Created", "payLoad" => $order_number];

                return response()->json($data);

            } else {

                $data = ["status" => "Success", "statusMessage" => "Cannot Create Order"];

                return response()->json($data);


            }



        }
    }


    public function itemSale(Request $request)
    {
        $minAdvancePayment = Configuration::where('key', '=', 'min_advance')->first();
        if (!$minAdvancePayment) {
            $data = ["status" => "Error", "statusMessage" => "Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }

        if (!Auth::user()) {
            $data = ["status" => "Error", "statusMessage" => "Please Login To Place an Order"];
            return response()->json($data);
        } else {
            $user_id = auth()->user()->id;
        }

        $rules = [

            'salesman' => 'required',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'payment_type' => 'required',
            'payment_status' => 'required',
            'order_type' => 'required',
            'order_status' => 'required',
            'delivery_date' => 'required',
            'delivery_time' => 'required',
            'branch_id' => 'required',
            'branch_code' => 'required',
            'assigned_to' => 'required',
            'priority' => 'required',
            'server_sync' => 'required',
            'live_synced' => 'required',
            'is_custom' => 'required',
        ];

        $messages = [
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
            'custom_image.mimes' => 'Image Type Invalid',

        ];


        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $data = ["status" => "Error", "statusMessage" => $validator->messages()->first()];
            return response()->json($data);

        } else {


            if ($request->input('advance_price') < $minAdvancePayment->value) {
                $data = ["status" => "Error", "statusMessage" => "Minimum advance payment i.e " . $minAdvancePayment->value . "  must be payed"];
                return response()->json($data);
            }


            $customOrder = $request->input('is_custom');

            $productId = $request->input('product_id');
            $flavourId = explode(',', $request->input('flavour_id'));
            $materialId = explode(',', $request->input('material_id'));

            if ($customOrder == 1) {
                $product = Product::where('sku', '=', 'PH-000')->first();
                if (!$product) {
                    $data = ["status" => "Error", "statusMessage" => "Custom Product Doesnt Exists"];
                    return response()->json($data);
                }
                $product_sku = $product->sku;
                $product_name = $product->name;
                $product_price = $request->input('product_price');
            } else {

            }

           

            Log::info("After All the Checks");

            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if (!$branchCode) {
                $data = ["status" => "Error", "statusMessage" => "Branch Code Not Set in Configuration"];
                return response()->json($data);
            }

            $latestId = Order::latest('id')->first();
            $lastId = 0;
            if (is_null($latestId) || empty($latestId)) {
                $lastId = 0;
            } else {
                $lastId = (int) $latestId->id;
            }




            $input = $request->except(['flavour_id', 'material_id', 'product_id']);


            $balance = $input['total_price'] - $input['advance_price'] - $input['discount'];
            Log::info("Balacne" . $balance);
            try {
                $branch_name = Configuration::where('key', '=', 'branch_name')->first();
            } catch (ModelNotFoundException $exception) {
                return "Order Not Found ID";
            }




            if ($balance == 0) {
                Log::info("Balacne if");
                $input['pending_amount'] = 0;
                $input['pending_amount_paid_date'] = Carbon::now()->format('Y-m-d');
                $input['pending_amount_paid_time'] = Carbon::now()->format('h:i a');
                $input['payment_status'] = 1;
                $input['pending_amount_paid_branch'] = $branch_name->value;

            }
            $input['pending_amount'] = $balance;

            $input['payment_status'] = 0;
            $input['pending_amount_paid_branch'] = $branch_name->value;

            $orderStatus = OrderStatus::where('name', '=', $request->input('order_status'))->first();

            $input['order_status'] = $orderStatus->id;
            $order_number = $branchCode->value . '-' . (int) ($lastId + 1);
            $input['order_number'] = $order_number;

            $input['user_id'] = $user_id;


            Log::info("Order Number " . $order_number);
            $tftime = Carbon::parse($request->input('delivery_time'));
            $converted_time = $tftime->format('G:i');

            $deliverDate = $request->input('delivery_date');
            $deliverDate = $deliverDate . ' ' . $converted_time;
            Log::info("Converted Date and Time" . $deliverDate);
            $convertedDeliveryDate = Carbon::parse($deliverDate);

            $closingTimeFrom = Carbon::createFromTime(23, 59, 0, 0);
            $closingTimeTo = Carbon::createFromTime(03, 00, 0, 0)->addDay();

            $st_time = strtotime($closingTimeFrom->toDateTimeString());
            $end_time = strtotime($closingTimeTo->toDateTimeString());

            $cur_time = time();
            Log::info("Now : " . time() . " Start Time : " . $st_time . " End Time : " . $end_time);

            if ($cur_time > $st_time && $cur_time < $end_time) {
                $order_date = Carbon::now()->subDay()->format('Y-m-d');
                Log::info("Order Date : " . $order_date);
            } else {
                $order_date = Carbon::now()->format('Y-m-d');
                Log::info("Order Date else : " . $order_date);
            }

            $input['order_date'] = $order_date;


            Log::info("Parsed Delivery Date and Time" . $convertedDeliveryDate);
            $input['delivery_date'] = $convertedDeliveryDate;
            $input['branch_code'] = $branchCode->value;


            Log::info('Converted Time' . $converted_time);


            if ($customOrder == 1) {

                Log::info("In if of custom order photo");

            } else {
               
            }

            $input['is_cake'] = 0;
            $order = Order::Create($input);
            $order_id = Order::latest('id')->value('id');


            if ($order->wasRecentlyCreated) {

                $order->products()->sync([$product_sku => ['product_name' => $product_name, 'product_price' => $product_price]]);

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

                $pos_items = json_decode($request->cartItems, true);
                // $pos_items = array_merge($pos, [$cake_item]);
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
                              'quantity_purchased' => $item['receiving_quantity'],
                              'discount_percent' => $discount,
                              'item_cost_price' => $item['cost_price'],
                              // 'item_unit_price' => $item['unit_price'],
                              'item_unit_price' => $item['unit_price'],
                              'item_location' => '1'
                          ];
                    
                          PosSaleItemsTemp::create($sales_items_data);
                    
                             }
                    
                    
                    DB::commit();


                } catch (\Exception $e) {

                    DB::rollback();

                    // Log the exception
                    Log::error($e->getMessage());

                    // Return a generic error message
                    return response()->json(['error' => 'An unexpected error occurred.'], 500);
                }


                $data = ["status" => "Success", "statusMessage" => "Order Created", "payLoad" => $order_number];

                return response()->json($data);

            } else {

                $data = ["status" => "Success", "statusMessage" => "Cannot Create Order"];

                return response()->json($data);


            }



        }
    }


    public function reOrder($cake_order_info){

        $dateTime = now()->format('Y-m-d H:i:s');
        $order_id  = $cake_order_info->order_id;
        $payment_type = "Cash";
        $total = $cake_order_info->total_price;
        $employee_id = auth()->user()->id;
        $branchcode = strtolower(str_replace('-', '', env("BRANCH_CODE")));
        $comment = '';
        $data['fbr_fee'] = Configuration::where('key', 'fbr_fee')->value('value');
        $invoice_number = $this->generate_invoice_number();
        $cake_invoice_number = $cake_order_info->cake_invoice;


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
            
             foreach($cake_item as $line =>$item){
            
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
                      'quantity_purchased' => $item['receiving_quantity'],
                      'discount_percent' => $discount,
                      'item_cost_price' => $item['cost_price'],
                      // 'item_unit_price' => $item['unit_price'],
                      'item_unit_price' => $item['unit_price'],
                      'item_location' => '1'
                  ];
            
                  PosSaleItemsTemp::create($sales_items_data);
            
                     }
            
                  Order::where('id',$order_id)->update(['order_status'=> 4]);
            
            DB::commit();


        } catch (\Exception $e) {

            DB::rollback();

            // Log the exception
            Log::error($e->getMessage());

            // Return a generic error message
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }


    }


    public function saleup(Request $req)
    {

        $pos_items = $req->cartItems;
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
        $fbr_invoice_number = $this->postDataApiFBR($data);

        $payments = [
            "payment_type" => $payment_type,
            "payment_amount" => $data['total'],
        ];

        $sales_data = [
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
            'fbr_invoice_number' => $fbr_invoice_number,
        ];


        try {
            // SALES TABLE save

            DB::beginTransaction();

            $newSale = PosSale::create($sales_data);
            $sale_id = $newSale->sale_id;

            // PAYMENTS TABLE save

            $payments['sale_id'] = $sale_id;
            PosSalePayments::create($payments);

            //SALES ITEMS save

            foreach ($pos_items as $line => $item) {

                // $cur_item_info = Item::where('item_id',$item['item_id'])->first();
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

                PosSaleItems::create($sales_items_data);

            }

            DB::commit();

            $data = ["status" => "Success", "statusMessage" => "Message", "payLoad" => $order_number];
            return response()->json($data);

        } catch (\Exception $e) {

            DB::rollback();

            // Log the exception
            Log::error($e->getMessage());

            // Return a generic error message
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }

    }

    public function upload()
    {

        try {
            $online_db_connection = DB::connection('online')->getPdo();
            // $table = Schema::connection('online')->hasTable('sales');
            // dd($table);

            $sale_count = PosSaleTemp::count();
            $message = '';
            $error = '';

            if ($sale_count > 0) {

                $new_sales = [];
                $old_sales = [];

                $all_sale = PosSaleTemp::all();

                foreach ($all_sale as $sale) {
                    $sale_data = $sale->toArray();
                    $status = $sale_data['status'];
                    $sale_id = $sale_data['sale_id'];

                    $payments = PosSalePaymentsTemp::where('sale_id', $sale_id)->first();
                    $payments_data = $payments->toArray();

                    $items = PosSaleItemsTemp::where('sale_id', $sale_id)->get();
                    $items_data = $items->toArray();

                    $data['sale_data'] = $sale_data;
                    $data['payments'] = $payments_data;
                    $data['items'] = $items_data;

                    $data['id'] = $sale_id;
                    $data['status'] = $status;

                    if ($status == 1) {
                        // if(count($old_sales) < 20){

                        array_push($old_sales, $data);

                        // }
                    } else {

                        array_push($new_sales, $data);

                    }
                    // if(count($new_sales) > 20){
                    //     break;
                    // }
                }

                $old_count = count($old_sales);
                $new_count = count($new_sales);


                if (!$online_db_connection) {

                    // Log::error('Online database is not connected');

                    if (!empty($new_sales)) {
                        foreach ($new_sales as $sale) {
                            //Upload in localdb with status = 1
                            if ($this->local_db_up($sale)) {

                                PosSaleTemp::where('sale_id', $sale['id'])->update(['status' => '1']);
                                $message = 'Internet not working.Successfully upload on local DB.';

                            } else {

                                $error += 'Unable to load in local db.';

                            }
                        }
                    }

                } else {


                    /**
                     * UPLOAD ALL RECORDS WITH STATUS == 1 TO ONLINE DB
                     */
                    if (!empty($old_sales)) {

                        foreach ($old_sales as $sale) {
                            if ($this->online_db_up($sale)) {

                                PosSalePaymentsTemp::where('sale_id', $sale['id'])->delete();
                                PosSaleItemsTemp::where('sale_id', $sale['id'])->delete();
                                PosSaleTemp::where('sale_id', $sale['id'])->delete();
                                $message = "Uploaded and deleted.";

                            } else {

                                $error += 'Old sales did not upload on online DB: dbUp failed';
                            }

                        }
                    }

                    shuffle($new_sales);

                    foreach ($new_sales as $sale) {

                        /**
                         * Save to online db
                         */
                        if ($this->online_db_up($sale)) {
                            //delete record from TEMP table - UPDATE LAST TIMESTAMP
                            PosSalePaymentsTemp::where('sale_id', $sale['id'])->delete();
                            PosSaleItemsTemp::where('sale_id', $sale['id'])->delete();
                            PosSaleTemp::where('sale_id', $sale['id'])->delete();
                            
                            $message = 'Upload sale successfully';

                        } else {

                            // echo " online db up failed ";
                            $error += $sale['id'] . ' process new records: online db up failed.';
                        }

                        // Save to online db and delete from temp table

                    }

                }

                if ($error) {

                    return response()->json(['message' => $error], 200);

                }

                return response()->json(['message' => $message], 200);


            } else {

                return response()->json(['message' => 'No cache data.'], 200);

            }


        } catch (\Exception $e) {


            // Log the exception
            Log::error($e->getMessage());

            // Return a generic error message
            return response()->json(['message' => 'An unexpected error occurred.'], 200);
        }


    }

    public function uploadRecent($id)
    {

        try {

            $online_db_connection = DB::connection('online')->getPdo();
            $sale_count = PosSaleTemp::count();
            $message = '';
            $error = '';

            if ($sale_count > 0) {

                $new_sales = [];

                $all_sale = PosSaleTemp::where('sale_id',$id)->get();

                foreach ($all_sale as $sale) {

                    $sale_data = $sale->toArray();
                    $status = $sale_data['status'];
                    $sale_id = $sale_data['sale_id'];

                    $payments = PosSalePaymentsTemp::where('sale_id', $sale_id)->first();
                    $payments_data = $payments->toArray();

                    $items = PosSaleItemsTemp::where('sale_id', $sale_id)->get();
                    $items_data = $items->toArray();

                    $taxes = PosSaleItemsTaxesTemp::where('sale_id', $sale_id)->get();
                    $taxes_data = $taxes->toArray();

                    $data['sale_data'] = $sale_data;
                    $data['payments'] = $payments_data;
                    $data['items'] = $items_data;
                    $data['taxes'] = $taxes_data;

                    $data['id'] = $sale_id;
                    $data['status'] = $status;

                        array_push($new_sales, $data);

                }


                if (!$online_db_connection) {

                    if (!empty($new_sales)) {
                        foreach ($new_sales as $sale) {

                            //Upload in localdb with status = 1
                            if ($this->local_db_up($sale)) {

                                PosSaleTemp::where('sale_id', $sale['id'])->update(['status' => '1']);
                                $message = 'Internet not working.Successfully upload on local DB.';

                            } else {

                                $error += 'Unable to load in local db.';

                            }
                        }
                    }

                } else {


                    foreach ($new_sales as $sale) {

                        /**
                         * Save to online db
                         */
                        if ($this->online_db_up($sale)) {
                            //delete record from TEMP table - UPDATE LAST TIMESTAMP
                            PosSalePaymentsTemp::where('sale_id', $sale['id'])->delete();
                            PosSaleItemsTemp::where('sale_id', $sale['id'])->delete();
                            PosSaleItemsTaxesTemp::where('sale_id', $sale['id'])->delete();
                            PosSaleTemp::where('sale_id', $sale['id'])->delete();
                            
                            $message = 'Upload sale successfully';

                        } else {

                            // echo " online db up failed ";
                            $error += $sale['id'] . ' process new records: online db up failed.';
                        }

                        // Save to online db and delete from temp table

                    }

                }

                if ($error) {

                    return response()->json(['message' => $error], 200);

                }

                return response()->json(['message' => $message], 200);


            } else {

                return response()->json(['message' => 'No cache data.'], 200);

            }


        } catch (\Exception $e) {


            // Log the exception
            Log::error($e->getMessage());

            // Return a generic error message
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }


    }

    public function local_db_up($sale)
    {

        try {

            $sale_data = $sale['sale_data'];
            unset($sale_data['sale_id'], $sale_data['status']);

            $payments = $sale['payments'];
            unset($payments['sale_id']);

            $items = $sale['items'];
            unset($items['sale_id']);

            DB::beginTransaction();
            // Sale table save
            $new_sale = PosSale::create($sale_data);
            $sale_id = $new_sale->sale_id;

            // Payments table save
            $payments['sale_id'] = $sale_id;
            PosSalePayments::create($payments);

            //Items table save
            foreach ($items as $item) {

                $item['$sale_id'] = $sale_id;
                PosSaleItems::create($item);

            }

            DB::commit();

            return true;

        } catch (\Exception $e) {


            DB::rollback();

            // Log the exception
            Log::error($e->getMessage());

            return false;
        }

    }


    public function online_db_up($sale)
    {

        try {


            $sale_data = $sale['sale_data'];
            unset($sale_data['sale_id']);
            $sale_data['employee_id'] = '52';

            $payments = $sale['payments'];
            $taxes = $sale['taxes'];

            $items = $sale['items'];

             /*
             *
             * Store data to local sales tables
             * 
             */
            DB::beginTransaction();

            $new_sale = PosSale::create($sale_data);
            $sale_id = $new_sale->sale_id;

            // Payments table save
            // $payments['sale_id'] = $sale_id;
            // PosSalePayments::create($payments);

            //Sale Items table save
            foreach ($items as $item) {

                $item['sale_id'] = $sale_id;
                PosSaleItems::create($item);

            }

            // DB::commit();

            // DB::connection('online')->beginTransaction();


            /*
             *
             * Upload data to cake suspended tables
             * 
             */

            //Insert in Cake suspended table
             $online_new_sale =  CakeSuspended::create($sale_data);
             $online_sale_id = $online_new_sale->id; 

              // cake suspended Payments table save
            $payments['sale_id'] = $online_sale_id;
            CakeSuspendedPayments::create($payments);

            //Insert in Cake suspended items table
            foreach ($items as $item) {
                 $sale_id_bc = $item['sale_id']; // bc before change
                $item['sale_id'] = $online_sale_id;
                CakeSuspendedItems::create($item);
                
                 foreach($taxes as $tax){

                    if($sale_id_bc == $tax['sale_id'] && $item['item_id'] == $tax['item_id'] && $item['line'] == $tax['line'] ){
                        $tax_info = [
                            'sale_id' => $online_sale_id,
                            'item_id' => $item['item_id'],
                            'line' => $item['line'],
                            'name' => 'Total Tax',
                            'percent' => $tax['percent'],
                        ];
                    }else{

                                        //Insert in  cake suspended items taxes table
                $itemTax = ItemTax::where('item_id', $item['item_id'])->first();

                if ($itemTax) {
                    $tax_percent = $itemTax->value('percent');
                } else {
                    $tax_percent = 0.000; // Set tax_percent to 0 if no item tax found
                }

                $tax_info = [
                    'sale_id' => $online_sale_id,
                    'item_id' => $item['item_id'],
                    'line' => $item['line'],
                    'name' => 'Total Tax',
                    'percent' => $tax_percent,
                ];


                    }
                CakeSuspendedItemsTaxes::create($tax_info);

                 }


            }


            DB::commit();
            // DB::connection('online')->commit();

            return true;

        } catch (\Exception $e) {

            DB::rollback();

            DB::connection('online')->rollback();

            // Log the exception
            Log::error($e->getMessage());

            return false;
        }

    }





    public function generate_invoice_number($code = null)
    {

        if (empty($code)) {
            $code = now()->format('YmdHis');
        }

        return env('BRANCH_CODE') . "-" . env('SYSTEM_CODE') . $code . mt_rand(100, 999);

    }


    public function postDataApiFBR($data)
    {

        $fbr_pct_code = Configuration::where('key', 'fbr_access_code')->value('value');
        $data['cartObject'] = [];
        $data['totalQuantity'] = 0;
        // $data['total'] = 0;
        $data['TotalTaxCharged'] = 0;

        foreach ($data['cart'] as $key => $value) {

            $value['discount'] = 0;

            $itemTax = ItemTax::where('item_id', $value['item_id'])->first();

            if ($itemTax) {
                $value['item_tax_percent'] = $itemTax->value('percent');
            } else {
                $value['item_tax_percent'] = 0; // Set tax_percent to 0 if no item tax found
            }

            $TaxCharged = 0;
            $TaxPercent = 0;
            if ($value['item_tax_percent']) {

                if ($value['discount']) {
                    $discounttedPrice = $value['cost_price'] - ($value['cost_price'] * $value['discount'] / 100);
                } else {
                    $discounttedPrice = $value['cost_price'];
                }

                $TaxPercent = $value['item_tax_percent'];
                $TaxCharged = $discounttedPrice * $value['receiving_quantity'] * ($value['item_tax_percent'] / 100);
            }

            // $data['TotalTaxCharged'] += $TaxCharged;

            $data['totalQuantity'] += $value['receiving_quantity'];
            // $data['total'] += $value['unit_price'];


            $data['cartObject'][] = [
                'PCTCode' => $fbr_pct_code,
                'ItemCode' => $value['item_id'],
                'ItemName' => $value['name'],
                'Quantity' => $value['receiving_quantity'],
                'TaxRate' => $TaxPercent,
                'TotalAmount' => $value['unit_price'],
                'TaxCharged' => $TaxCharged,
                'InvoiceType' => $data['InvoiceType'],
                'Discount' => $value['discount'],
                'SaleValue' => $value['discount'] - $TaxCharged,
                'RefUSIN' => ''
            ];
        }
        unset($data['cart']);

        $data['cartObject'] = collect($data['cartObject'])->values()->all();
        $data['cartObject'] = collect($data['cartObject'])->toJson();

        return $this->generateFBRInvoice($data);

    }

    public function generateFBRInvoice($data)
    {

        $url = 'http://localhost:8524/api/IMSFiscal/GetInvoiceNumberByModel';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $token = '2bfbffd3-b513-3eb1-a611-51189cfb1000';
        $pos_id = '137064';
        $headers = [
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json",
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data['TotalTaxCharged'] = $data['total'] - $data['fbr_fee'] - $data['tax_exclusive_subtotal'];

        $data = '
        {
          "TotalBillAmount": ' . $data['total'] . ',
          "POSID": ' . $pos_id . ',
          "Discount": ' . $data['discount'] . ',
          "USIN": "THBKRY",
          "TotalQuantity": ' . $data['totalQuantity'] . ',
          "TotalTaxCharged": ' . $data['TotalTaxCharged'] . ',
          "TotalSaleValue": ' . $data['tax_exclusive_subtotal'] . ',
          "FurtherTax":' . $data['fbr_fee'] . ',
          "Items": ' . $data['cartObject'] . ',
          "DateTime": "' . $data['transaction_time'] . '",
          "PaymentMode": ' . $data['Payment_mode'] . ',
          "InvoiceType": ' . $data['InvoiceType'] . ',
          "RefUSIN":"",
          "InvoiceNumber":""
        }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $invoice = json_decode($resp)->InvoiceNumber;
        // $invoice = "";

        return $invoice;

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

            $sub = bcmul($item['cost_price'], $item['receiving_quantity'], 2);
            $subtotal += $sub;

            $totalP = bcmul($item['unit_price'], $item['receiving_quantity']);
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


    public function get_item_total($quantity, $price, $discount_percentage, $include_discount = FALSE)
    {
        $total = bcmul($quantity, $price);
        if ($include_discount) {
            $discount_amount = $this->get_item_discount($quantity, $price, $discount_percentage);

            return bcsub($total, $discount_amount);
        }

        return $total;
    }

    public function get_item_discount($quantity, $price, $discount_percentage)
    {
        $total = bcmul($quantity, $price);
        $discount_fraction = bcdiv($discount_percentage, 100);

        return bcmul($total, $discount_fraction);
    }

    public function getSuspendedOrder(){

        $suspended_order_data = CakeSuspended::all();
        // $suspended_order_data = CakeSuspended::with('suspended_items')->with('suspended_items_taxes')->with('suspended_payments')->get();
        $suspended_items = CakeSuspendedItems::all();
        $suspended_items_taxes = CakeSuspendedItemsTaxes::all();
        $suspended_payments = CakeSuspendedPayments::all();
        return response()->json([
                                 'suspended_sale' => $suspended_order_data,
                                 'suspended_sale_items'=>$suspended_items,
                                 'suspended_items_taxes'=>$suspended_items_taxes,
                                 'suspended_payments'=>$suspended_payments 
                                ]);

    }

    public function deleteSuspendedOrder($sale_id){

        try{


          $suspended_order = CakeSuspended::where('sale_id',$sale_id)->first();

        if ($suspended_order) {
            DB::beginTransaction();

            CakeSuspendedPayments::where('sale_id',$sale_id)->delete();
            CakeSuspendedItemsTaxes::where('sale_id',$sale_id)->delete();
            CakeSuspendedItems::where('sale_id',$sale_id)->delete();
            $suspended_order->where('sale_id',$sale_id)->delete();

            DB::commit();
    
            return response()->json(['message' => 'Suspended order deleted successfully']);
         } else {
            return response()->json(['error' => 'Suspended order not found']);
         }

        } catch (\Exception $e) {

        DB::rollback();

        return response()->json(['error' => 'Suspended order not found']);
     }

    }

    public function updateOrderStatus($cake_invoice){

      $update_status =  Order::where('order_number',$cake_invoice)->update(['order_status'=> 4]);

      if($update_status){

        return response()->json(['message' => 'Status successfully updated']);

      }else{

        return response()->json(['error' => 'Status not updated']);

      }

    }



}