<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/users', function (Request $request) {
    return $request->user();
});
Route::post('/branch/data',['uses'=>'AdminBranchesController@getBranchData']);
Route::post('/orderBranch/details',['uses'=>'AdminOrderController@getBranchOrdersLive']);
Route::post('/branchesLive/details',['uses'=>'AdminBranchesController@getBranchesLive']);
Route::post('/products/live',['uses'=>'AdminProductsController@getProductsLiveSync']);
Route::post('/products/images',['uses'=>'AdminProductsController@getProductsLiveImage']);
Route::post('/branchesLive/sync',['uses'=>'AdminBranchesController@syncBranchesLive']);

Route::post('/localOrders/sync',['uses'=>'AdminOrderController@syncLocalOrders']);
Route::post('/localOrders/sync/images',['uses'=>'AdminOrderController@syncLocalOrdersImages']);
Route::post('/localOrders/sync/finalImages',['uses'=>'AdminOrderController@syncLocalOrdersFinalImages']);
Route::post('/customOrder/check',['uses'=>'AdminOrderController@checkLiveCustomOrder']);

Route::get('/suspendedOrder',['uses'=>'PosSaleController@getSuspendedOrder']);
Route::get('/suspendedOrderDelete/{id}',['uses'=>'PosSaleController@deleteSuspendedOrder']);
Route::get('/updateOrderStatus/{id}',['uses'=>'PosSaleController@updateOrderStatus']);


