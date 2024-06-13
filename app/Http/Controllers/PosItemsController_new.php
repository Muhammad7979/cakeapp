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
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests\StoreProductRequest;

class PosItemsController extends Controller
{


    public function index()
    {

        //
        if (Gate::allows('view-product')) {


            $items = Item::paginate(10);
            return view('admin.positems.index', compact('items'));
        } else {
            return redirect('admin');
        }




    }

  
 
    public function getPosItemsLive(Request $request)
    {
        if (!Gate::allows('view-product')) {
            return response()->json(['success' => false, 'error' => 'Unauthorized access.']);
        }
    
        $branchcode = strtolower(str_replace('-', '', env("BRANCH_CODE")));
        $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all/' . $branchcode;
    
        $username = 'pos';
        $password = 'z1Whz&61';
    
        $client = new Client([
            'base_uri' => $apiUrl,
            'auth' => [$username, $password],
        ]);
    
        try {
            $response = $client->get('');
            $data = json_decode($response->getBody()->getContents(), true); // Convert to associative array
    
            if (empty($data)) {
                return response()->json(['success' => false, 'error' => 'Failed to decode JSON response']);
            }
    
            try {
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::beginTransaction();
                try {
                    // Truncate tables
                    Item::truncate();
                    ItemQuantity::truncate();
                    ItemTax::truncate();
                    Inventory::truncate();
    
                    // Insert data into tables
                    Item::insert(array_map(function($item) {
                        return array_only($item, (new Item())->getFillable());
                    }, $data['items']));
    
                    ItemQuantity::insert(array_map(function($itemQuantity) {
                        return array_only($itemQuantity, (new ItemQuantity())->getFillable());
                    }, $data['item_quantities']));
    
                    ItemTax::insert(array_map(function($itemTax) {
                        return array_only($itemTax, (new ItemTax())->getFillable());
                    }, $data['item_taxes']));
    
                    Inventory::insert(array_map(function($inventoryItem) {
                        return array_only($inventoryItem, (new Inventory())->getFillable());
                    }, $data['inventory']));
    
                    DB::commit();
    
                    // Enable foreign key checks again after truncation
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    
                    return response()->json(['success' => true, 'message' => 'Data successfully updated from API.']);
                } catch (Exception $e) {
                    DB::rollback();
                    Log::error('Error inserting data: ' . $e->getMessage());
                    return response()->json(['success' => false, 'error' => 'Failed to update data.']);
                }
            } finally {
                // Make sure to re-enable foreign key checks even if an exception occurs
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $error = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error], $statusCode);
        }
    }
  


    // public function getPosItemsLive(Request $request)
    // {

    //     $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all';
    //     $username = 'pos';
    //     $password = 'z1Whz&61';

    //     // Initialize Guzzle client
    //     $client = new Client([
    //         'base_uri' => $apiUrl,
    //         // 'timeout' => 10, // Increase timeout if needed
    //         'auth' => [$username, $password],
    //     ]);

    //     try {
    //         // Make a GET request
    //         $response = $client->get('');

    //         // Get response body
    //         $body = $response->getBody()->getContents();

    //         // Decode JSON response
    //         $data = json_decode($body, true);

    //         // Check if data is valid
    //         if ($data !== null) {
    //             // Start transaction
    //             DB::beginTransaction();

    //             try {
    //                 // Truncate all tables
    //                 DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    //                 DB::table('items')->truncate();
    //                 DB::table('item_quantities')->truncate();
    //                 DB::table('items_taxes')->truncate();
    //                 DB::table('inventorys')->truncate();

    //                 // Insert data into tables
    //                 if (isset($data['items'])) {
    //                     DB::table('items')->insert($data['items']);
    //                 }

    //                 if (isset($data['item_quantities'])) {
    //                     DB::table('item_quantities')->insert($data['item_quantities']);
    //                 }

    //                 if (isset($data['items_taxes'])) {
    //                     DB::table('items_taxes')->insert($data['items_taxes']);
    //                 }

    //                 if (isset($data['inventorys'])) {
    //                     DB::table('inventorys')->insert($data['inventorys']);
    //                 }

    //                 // Commit transaction
    //                 DB::commit();

    //                 return response()->json(['success' => true, 'message' => 'Data successfully updated from API.']);
    //             } catch (\Exception $e) {
    //                 // Rollback transaction if any error occurs
    //                 DB::rollback();
    //                 return response()->json(['success' => false, 'error' => 'Failed to update data.']);
    //             }
    //         } else {
    //             return response()->json(['success' => false, 'error' => 'Failed to decode JSON response']);
    //         }
    //     } catch (RequestException $e) {
    //         // Handle request exception
    //         $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
    //         $error = $e->getMessage();
    //         return response()->json(['success' => false, 'error' => $error], $statusCode);
    //     }
    // }


    //     public function search(Request $request)
//     {

    //         $search_term = Input::get('search_term');



    //         if ((empty($search_term) || is_null($search_term)) && $is_active != -1) {
//             $products = Product::where('is_active', '=', $is_active)
//                 ->with('photo')
//                 ->with('category')
//                 ->paginate(10)->setPath('');
//             $products = $products->appends(
//                 array(
//                     'search_term' => Input::get('search_term'),
//                     'is_active' => Input::get('is_active'),
//                 )
//             );
//             return view('admin.products.index', compact('products'));

    //         } else if (!empty($search_term) && $is_active == -1) {
//             $products = Product::
//                 with('photo')
//                 ->with('category')
//                 ->where('sku', 'LIKE', '%' . $search_term . '%')
//                 ->orWhere('name', 'LIKE', '%' . $search_term . '%')
//                 ->orWhereHas('category', function ($q) use ($search_term) {
//                     $q->where('name', 'LIKE', '%' . $search_term . '%');
//                 })
//                 ->paginate(10)->setPath('');
//             //                ->get();  // for testing purposes
//             $products = $products->appends(
//                 array(
//                     'search_term' => Input::get('search_term'),
//                     'is_active' => Input::get('is_active'),
//                 )
//             );



    //             return view('admin.products.index', compact('products'));
//             //
//         } else {

    //             $products = Product::with('photo')
//                 ->with('category')
//                 ->where('is_active', '=', $is_active)
//                 ->where(function ($query) use ($search_term) {
//                     $query->where('sku', 'LIKE', '%' . $search_term . '%')
//                         ->orWhere('name', 'LIKE', '%' . $search_term . '%')
//                         ->orWhereHas('category', function ($q) use ($search_term) {
//                             $q->where('name', 'LIKE', '%' . $search_term . '%');
//                         });
//                 })->paginate(10)->setPath('');
//             //                ->get();
//             $products = $products->appends(
//                 array(
//                     'search_term' => Input::get('search_term'),
//                     'is_active' => Input::get('is_active'),
//                 )
//             );
//             //
//             return view('admin.products.index', compact('products'));




    //             //            return view('admin.products.index',compact('products'));
// //


    //         }
//     }
    public function search(Request $request)
    {
        $searchTerm = $request->input('search_term');

        // Perform the search query based on the item name
        $items = Item::where('name', 'like', '%' . $searchTerm . '%')->paginate(10);

        // You can adjust the response as needed, for example, returning a view with the search results
        return view('admin.positems.index', ['items' => $items]);
    }





    // public function positems()
    // {


    //     // $branccode = env("BRANCH_CODE");
    //     // $branccode = strtolower(str_replace('-', '', $branccode));
    //     // $client = new Client();
    //     // $response = $client->request('GET', 'http://localhost:8080/api/getitems');
    //     // // $response = $client->request('GET', 'http://192.168.110.19/index.php/api/all/' . $branccode);


    //     // // Decode the response body
    //     // $items = json_decode($response->getBody(), true);

    //     // $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all';
    //     // $username = 'your_username';
    //     // $password = 'your_password';
    //     // // Make the HTTP request with Laravel's HTTP client
    //     // $response = HTTP::withBasicAuth($username, $password)->get($apiUrl);

    //     // if ($response->successful()) {
    //     //     // Access the API response body
    //     //     $apiData = $response->json();

    //     //     // Process the data as needed
    //     //     // ...

    //     //     return response()->json(['success' => true, 'data' => $apiData]);
    //     // } else {
    //     //     // Handle error responses
    //     //     $error = $response->json();
    //     //     return response()->json(['success' => false, 'error' => $error], $response->status());
    //     // }

    //     // Make the HTTP request with Laravel's HTTP client
    //     // $response = Http::withBasicAuth($username, $password)->get($apiUrl);

    //     // // Return the items in JSON format
    //     // return response()->json($items);



    //     $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all';
    //     $username = 'your_username';
    //     $password = '123124';

    //     $client = new Client([
    //         // Base URI is used with relative requests
    //         'base_uri' => $apiUrl,
    //         // You can set any number of default request options.
    //         'timeout' => 2.0, // Adjust timeout as needed
    //     ]);

    //     try {
    //         // Make the HTTP request with Guzzle
    //         $response = $client->request('GET', '', [
    //             'auth' => [$username, $password],
    //         ]);

    //         $statusCode = $response->getStatusCode();
    //         if ($statusCode === 200) {
    //             // Access the API response body
    //             $apiData = json_decode($response->getBody(), true);

    //             // Process the data as needed
    //             // ...

    //             return response()->json(['success' => true, 'data' => $apiData]);
    //         } else {
    //             // Handle error responses
    //             $error = json_decode($response->getBody(), true);
    //             return response()->json(['success' => false, 'error' => $error], $statusCode);
    //         }
    //     } catch (\GuzzleHttp\Exception\RequestException $e) {
    //         // Handle Guzzle request exceptions

    //         $statusCode = $e->getResponse()->getStatusCode();
    //         $error = json_decode($e->getResponse()->getBody(), true);
    //         return response()->json(['success' => false, 'error' => $error], $statusCode);
    //     }
    // }








    // public function positems()
    // {


    //     $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all';
    //     $username = 'pos';
    //     $password = 'z1Whz&61';

    //     $client = new Client([
    //         // Base URI is used with relative requests
    //         'base_uri' => $apiUrl,
    //         // You can set any number of default request options.
    //         'timeout' => 2.0, // Adjust timeout as needed
    //     ]);

    //     try {
    //         // Make the HTTP request with Guzzle
    //         $response = $client->request('GET', '', [
    //             'auth' => [$username, $password],
    //         ]);

    //         $statusCode = $response->getStatusCode();
    //         if ($statusCode === 200) {
    //             // Access the API response body
    //             $apiData = json_decode($response->getBody(), true);

    //             // Process the data as needed
    //             // ...

    //             return response()->json(['success' => true, 'data' => $apiData]);
    //         } else {
    //             // Handle error responses
    //             $error = json_decode($response->getBody(), true);
    //             return response()->json(['success' => false, 'error' => $error], $statusCode);
    //         }
    //     } catch (\GuzzleHttp\Exception\RequestException $e) {
    //         // Handle Guzzle request exceptions

    //         $statusCode = $e->getResponse()->getStatusCode();
    //         $error = json_decode($e->getResponse()->getBody(), true);
    //         return response()->json(['success' => false, 'error' => $error], $statusCode);
    //     }
    // }



    // public function positems()
    // {
    //     $apiUrl = 'http://pos.tehzeeb.com/index.php/api/all';
    //     $username = 'pos';
    //     $password = 'z1Whz&61';

    //     $client = new Client([
    //         'base_uri' => $apiUrl,
    //         'timeout' => 2.0,
    //     ]);

    //     try {
    //         $response = $client->request('GET', '', [
    //             'auth' => [$username, $password],
    //         ]);

    //         $statusCode = $response->getStatusCode();
    //         if ($statusCode === 200) {
    //             $apiData = json_decode($response->getBody(), true);
    //             return response()->json(['success' => true, 'data' => $apiData]);
    //         } else {
    //             $error = json_decode($response->getBody(), true);
    //             return response()->json(['success' => false, 'error' => $error], $statusCode);
    //         }
    //     } catch (RequestException $e) {
    //         $response = $e->getResponse();
    //         if ($response) {
    //             $statusCode = $response->getStatusCode();
    //             $error = json_decode($response->getBody(), true);
    //         } else {
    //             // If there is no response, set default status code and error message
    //             $statusCode = 500;
    //             $error = ['message' => 'Internal Server Error'];
    //         }
    //         return response()->json(['success' => false, 'error' => $error], $statusCode);
    //     }
    // }
    public function positems()
    {
        try {
            // Fetch items from the 'items' table
            $items = Item::all();
    
            // Return items in the response
            return response()->json(['success' => true, 'items' => $items]);
        } catch (\Exception $e) {
            // Handle any exceptions
            $error = $e->getMessage();
            return response()->json(['success' => false, 'error' => $error]);
        }
    }



   


}
