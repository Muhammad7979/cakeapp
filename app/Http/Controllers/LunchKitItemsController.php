<?php

namespace App\Http\Controllers;

use Exception;
use App\LunchKit;
use App\LunchKitItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LunchKitItemsController extends Controller
{
    public function index(Request $request)
    {

    }

    public function save(Request $request)
    {


        if (!Auth::user()) {
            $data = ["status" => "Error", "statusMessage" => "Please Login To Place an Order"];
            return response()->json($data);
        } else {
            $user_id = auth()->user()->id;
        }

        $rules = [

            'boxname' => 'required',
            'boxdescription' => 'required',
            'cartItems' => 'required',


        ];

        $messages = [
            'boxname.required' => ' Name is required',
            'boxdescription.required' => 'Description is required',
            'cartItems.required' => 'Items required',



        ];


        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $data = ["status" => "Error", "statusMessage" => $validator->messages()->first()];
            return response()->json($data);

        } else {

            $input = $request->all();





            try {

                DB::beginTransaction();
                $lunchkit = LunchKit::firstOrCreate(
                    ["name" => $input['boxname']],
                    [
                        "name" => $input['boxname'],
                        "description" => $input['boxdescription']
                    ]
                );
                if ($lunchkit->wasRecentlyCreated) {
                    $lunchkitid = LunchKit::latest('lunch_kit_id')->value('lunch_kit_id');
                    $kitItems = json_decode($request->cartItems, true);
                    foreach ($kitItems as $kitItem) {

                        $lunchkititems = LunchKitItem::create(
                            [
                                "lunch_kit_id" => $lunchkitid,
                                "item_id" => $kitItem['item_id'],
                                "quantity" => $kitItem['receiving_quantity'],
                                "total_price" => number_format($kitItem['receiving_quantity'] * $kitItem['cost_price'], 2)
                            ]
                        );
                    }

                    DB::commit();
                    $data = ["status" => "Success", "statusMessage" => "Items added to kit"];

                    return response()->json($data);


                } else {
                    DB::rollback();
                    $data = ["status" => "Error", "statusMessage" => "Record Already exists"];

                    return response()->json($data);

                }









            } catch (Exception $e) {

                DB::rollback();

                // Log the exception
                Log::error($e->getMessage());

                // Return a generic error message
                return response()->json(['error' => 'An unexpected error occurred.'], 500);
            }


        }




    }

    public function lunchkitItems()
    {
        try {
            // Fetch items from the 'items' table
            $lunchkitwithitems = LunchKit::with('lunchkit_items')->get();
            // dd($lunchkits);

            // Return items in the response
            return response()->json(['success' => true, 'items' => $lunchkitwithitems]);
        } catch (\Exception $e) {
            // Handle any exceptions
            $error = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error]);
        }
    }
}
