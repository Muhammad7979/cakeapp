<?php

namespace App\Http\Controllers;

use App\Item;
use Exception;
use App\ItemTax;
use App\Inventory;
use App\ItemQuantity;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class PosItemsController extends Controller
{


    public function index()
    {
        if (Gate::allows('view-product')) {

            $items = Item::where('deleted','0')->paginate(10);
            return view('admin.positems.index', compact('items'));
        } else {
            return redirect('admin');
        }

    }
    public function getPosItemsLive(Request $request)
    {
        if (Gate::allows('view-product')) {
            $branchcode = strtolower(str_replace('-', '', env("BRANCH_CODE")));
            $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all/' . $branchcode;
            $username = 'pos';
            $password = 'z1Whz&61';

            // Initialize Guzzle client
            $client = new Client([
                'base_uri' => $apiUrl,
                'auth' => [$username, $password],
            ]);

            // Make a GET request
            $response = $client->get('');

            // Get response body
            $body = $response->getBody()->getContents();

            // Decode JSON response
            $data = json_decode($body, true);

            $online_items = $data['items'];
            $local_items = Item::all();
            $localJson = $local_items->toJson();
            $onlineJson = json_encode($online_items);
            $diff = strcmp($localJson, $onlineJson);
            // Check if data is valid
            if ($diff) {

                $online_item_quantities = $data['item_quantities'];
                $online_item_taxes = $data['item_taxes'];
                $online_inventory = $data['inventory'];

                $local_item_quantities = ItemQuantity::all();
                $local_item_taxes = ItemTax::all();


                // try {
                // Start transaction


                try {
                    // Disable foreign key checks
                    DB::beginTransaction();
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    // Truncate all tables
                    Item::truncate();
                    ItemQuantity::truncate();
                    ItemTax::truncate();
                    Inventory::truncate();

                    /**
                     * 
                     * adding data in item taxes table
                     *
                     */
                    foreach ($data['items'] as $item) {
                        if (($item['custom_id'] && $item['custom_cost_price']) != null && $item['custom_unit_price'] != null) {
                            // Your code here
                            $item['cost_price'] = $item['custom_cost_price'];
                            $item['unit_price'] = $item['custom_unit_price'];
                        }
                        unset($item['custom_id'], $item['custom_cost_price'], $item['custom_unit_price']);

                        Item::create($item);
                    }

                    /**
                     * 
                     * adding data in item taxes table
                     *
                     */
                    foreach ($data['item_taxes'] as $itemTax) {


                        if (($itemTax['custom_id'] && $itemTax['custom_tax_percent']) != null) {

                            $itemTax['percent'] = $itemTax['custom_tax_percent'];

                        }
                        unset($itemTax['custom_id'], $itemTax['custom_tax_percent']);

                        ItemTax::create($itemTax);

                    }

                    /**
                     * 
                     * adding data in item Quantity table
                     *
                     */

                    foreach ($data['item_quantities'] as $itemQuantity) {

                        ItemQuantity::create($itemQuantity);

                    }

                    /**
                     * 
                     * adding data in item inventry table
                     *
                     */

                    foreach ($data['inventory'] as $inventoryItem) {

                        Inventory::create($inventoryItem);


                    }

                    /**
                     * restoring old item taxes
                     */


                    foreach ($local_item_taxes as $taxes) {
                        $itemTaxes = [
                            'item_id' => $taxes->item_id,
                            'name' => $taxes->name,
                            'percent' => $taxes->percent,
                        ];

                        ItemTax::where($itemTaxes)->update($taxes->toArray());
                    }




                    /**
                     * restoring old item quantities
                     */
                    foreach ($local_item_quantities as $quantity) {
                        $data = $quantity->toArray();
                        $itemQuantity = [
                            'item_id' => $quantity->item_id,
                            'location_id' => $quantity->location_id,
                        ];

                        ItemQuantity::where($itemQuantity)->update($data);

                    }


                    /**
                     * Restore OLD local non protected Items
                     *
                     * only custom 10 should update from online
                     *
                     */

                    foreach ($online_items as $item) {
                        if ($item['custom10'] == "no") {
                            //find old local item
                            //set custom 10 to no
                            //update the item
                            foreach ($local_items as $oldItem) {
                                if ($item['item_id'] == $oldItem->item_id) {

                                    $item_array = [

                                        'item_id' => $oldItem->item_id
                                    ];

                                    $oldItem->custom10 = "no";

                                    Item::where($item_array)->update($oldItem->toArray());

                                    break;
                                }
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

                // } catch (RequestException $e) {
                //     // Handle request exception
                //     $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
                //     $error = $e->getMessage();
                //     return response()->json(['success' => false, 'error' => $error], $statusCode);
                // }
            } else {

                return response()->json(['success' => true, 'error' => 'Already up to date.']);

            }

        }
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search_term');

        // Perform the search query based on the item name
        $items = Item::where('name', 'like', '%' . $searchTerm . '%')->where('deleted',0)->paginate(10);

        // You can adjust the response as needed, for example, returning a view with the search results
        return view('admin.positems.index', ['items' => $items]);
    }




    public function positems()
    {
        try {
            // Fetch items from the 'items' table
            $items = Item::where('deleted','0')->get();

            // Return items in the response
            return response()->json(['success' => true, 'items' => $items]);
        } catch (\Exception $e) {
            // Handle any exceptions
            $error = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error]);
        }
    }


    public function save(Request $request)
    {



        $data = ["status" => "Success", "statusMessage" => "added"];

        return response()->json($data);
    }


}
