<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\LunchKit;
use App\Order;
use App\PosSale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     $possale = Order::with('posSale')->get();

//     foreach ($possale as $p) {
//         if ($p->posSale) {
//             dd($p->posSale->sale_id);
//         }
//     } // This should output the related order

// });
// Route::get('/', function () {
//     // return view('client/login');
//     $lunchkit = LunchKit::where('lunch_kit_id', 3)->first();
//     dd($lunchkit->lunchkit_items);
// });
Route::get('/', function () {
    return view('client/login');
});

// Route::get('/pos', 'PosItemsController@index');
Route::get('/client/login', function () {
    return view('client/login');
});

Route::post('clientOrder/create', ['uses' => 'ClientOrderController@storeOrder']);
Route::post('/positems/save', ['uses' => 'PosSaleController@sale']);
Route::post('/onlyitems/save', ['uses' => 'PosSaleController@itemSale']);

// Route::get('/lunchkit/save', 'LunchKitController@index');

Route::post('/lunchkititems/save', 'LunchKitItemsController@save');
Route::get('/lunchkititems/', 'LunchKitItemsController@lunchkitItems');
/*
 * For Printing Order
 */
Route::post('print/order/{id}', ['uses' => 'ClientOrderController@generatePdf']);
Route::get('get/order/{id}', ['uses' => 'ClientOrderController@getOrderInformation']);

Auth::routes();
// comment this for server
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/admin', function () {

    return view('admin.index');

});


Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin', function () {

        return view('admin.index');

    });
    Route::get('/admin', ['uses' => 'AdminDashboardController@index']);



    Route::resource('admin/users', 'AdminUsersController');

    Route::resource('admin/groups', 'AdminGroupsController');

    Route::resource('admin/branches', 'AdminBranchesController');
    Route::post('branches/get', ['uses' => 'AdminBranchesController@getBranches']);

    Route::post('branches/sync', ['uses' => 'AdminBranchesController@syncBranches']);

    Route::resource('admin/roles', 'AdminRolesController');

    Route::resource('admin/materials', 'AdminMaterialsController');
    Route::resource('admin/bakeman', 'BakemanController')->except(['show']);
    Route::get('admin/bakeman/orders-reports/{id?}', 'BakemanController@reports')->name('bakeman.reports');
    Route::get('admin/bakeman/pos-orders-reports', 'BakemanController@reports')->name('bakeman.pos_reports');
    Route::get('admin/bakeman/pos-orders/{param}', 'BakemanController@index')->name('bakeman.pos_items');
    Route::resource('admin/flavourCategory', 'AdminFlavourCategoryController');
    Route::resource('admin/flavours', 'AdminFlavoursController');
    Route::resource('admin/categories', 'AdminCategoriesController');
    Route::resource('admin/products', 'AdminProductsController');
    Route::resource('admin/orderTypes', 'AdminOrderTypeController');
    Route::resource('admin/orderStatuses', 'AdminOrderStatusController');
    Route::resource('admin/paymentTypes', 'AdminPaymentTypeController');
    Route::post('admin/systemConfig', ['uses' => 'SystemConfigurationsController@SystemConfiguration']);
    Route::resource('admin/configurations', 'SystemConfigurationsController');


    //i may have to change the route from this middleware to outside the middleware
    Route::resource('admin/orders', 'AdminOrderController');
    Route::resource('admin/sales', 'AdminSalesController');

    Route::post('/sales/search', ['uses' => 'AdminSalesController@search']);
    Route::get('/sales/reset', ['uses' => 'AdminSalesController@searchReset']);
    Route::post('/generate/salesCsv', ['uses' => 'AdminSalesController@generateCsv']);

    Route::post('order/get', ['uses' => 'AdminOrderController@getOrderDetails']);

    Route::get('order/search/reset', ['uses' => 'AdminOrderController@searchReset']);

    Route::post('orderStatus/set', ['uses' => 'AdminOrderController@setOrderStatus']);
    Route::post('bakeman/orderStatus/set', ['uses' => 'BakemanController@setOrderStatus']);
    Route::any('products/search', ['uses' => 'AdminProductsController@search']);
    Route::any('materials/search', ['uses' => 'AdminMaterialsController@search']);
    Route::any('categories/search', ['uses' => 'AdminCategoriesController@search']);
    Route::any('flavours/search', ['uses' => 'AdminFlavoursController@search']);
    Route::any('flavourCategory/search', ['uses' => 'AdminFlavourCategoryController@search']);

    Route::get('donutChart/date', ['uses' => 'AdminDashboardController@getDonutChartData']);
    Route::post('lineChart/date', ['uses' => 'AdminDashboardController@getLineChartData']);
    Route::post('orderLineChart/date', ['uses' => 'AdminDashboardController@getOrderLineChartData']);


    Route::any('order/information', ['uses' => 'AdminOrderController@getOrderInformation']);
    Route::post('bakeman/search', ['uses' => 'BakemanController@getOrderInformation']);
    Route::post('bakeman/searchbynumber', ['uses' => 'BakemanController@searchOrderNumber']);
    Route::post('bakeman/reorder', 'BakemanController@reOrderSale')->name('bakeman.reorder');
  
    Route::post('bakeman/order/get', ['uses' => 'BakemanController@getOrderDetails']);
    Route::post('orderBranch/get', ['uses' => 'AdminOrderController@getBranchOrders']);

    Route::post('order/payment', ['uses' => 'AdminOrderController@setPaymentStatus']);

    Route::get('import/data', ['uses' => 'ImportDataController@importExport']);
    Route::post('import/productCategories/data', ['uses' => 'ImportDataController@importProductCategories']);
    Route::post('import/flavourCategories/data', ['uses' => 'ImportDataController@importFlavourCategories']);
    Route::post('import/flavours/data', ['uses' => 'ImportDataController@importFlavour']);
    Route::post('import/products/data', ['uses' => 'ImportDataController@importProduct']);

    Route::post('manualUpload/orders', ['uses' => 'AdminOrderController@manualUploadOrders']);
    Route::get('/admin/allpositems', 'PosItemsController@index')->name('pos.items');
    Route::any('positems/search', ['uses' => 'PosItemsController@search']);

});



Route::get('events/', ['as' => 'Events', 'uses' => 'ClientEventCategoryController@showCategories']);
Route::get('events/{id}/products', ['as' => 'Event.products', 'uses' => 'ClientProductController@showCategoryProducts']);
Route::get('product/{id}', ['uses' => 'ClientProductController@getProductData']);

Route::get('positems/', 'PosItemsController@positems')->name('positems');
Route::get('customOrder', ['uses' => 'ClientProductController@getCustomOrderData']);



Route::post('products/liveSync', ['uses' => 'AdminProductsController@getProductsLive']);

Route::post('positems/liveSync', ['uses' => 'PosItemsController@getPosItemsLive']);

Route::post('/positems/saleUpload', ['uses' => 'PosSaleController@upload']);

Route::post('/positems/syncItemKits', ['uses' => 'PosItemKitController@sync']);
Route::get('allitemkits', 'PosItemKitController@index')->name('itemkits');
Route::post('itemkits/get', 'PosItemKitController@getItemKitInfo');

Route::get('item_kits_categories/', ['as' => 'ItemKits', 'uses' => 'PosItemKitController@showCategories']);

Route::get('item_kits_items/{id}', ['uses' => 'PosItemKitController@getProductData']);

Route::get('/custom_item_kits_items', ['uses' => 'PosItemKitController@getCustomProductData']);

Route::post('itemkitorder/create', ['uses' => 'PosItemKitController@storeOrder']);

Route::post('customItemkitorder/create', ['uses' => 'PosItemKitController@storeCustomOrder']);

// Route::get('/ItemKitOrder/create', ['uses' => 'PosItemKitController@storeOrder']);


/*
 * FOR CREATING ORDER FROM CLIENT SIDE
 */






Route::post('system/config', ['uses' => 'SystemConfigurationsController@getSystemConfiguration']);

Route::post('system/config/store', ['uses' => 'SystemConfigurationsController@saveSystemConfiguration']);
Route::post('flavours/material/get', ['uses' => 'AdminOrderController@getflavourMaterial']);
Route::get('/Invoice', function () {
    return view('InvoiceTemplate');
});
Route::get('/generateInvoice/{id}', 'AdminOrderController@generatePdf');
Route::get('/reGenerateInvoice/{id}', 'AdminOrderController@reGeneratePdf');

Route::get('/pending_sale', 'SystemConfigurationsController@pending_sale');

Route::post('/uploadItemKitImage', 'PosItemKitController@uploadImage')->name('image.upload');




// React App Route
//disable this for server (comment this )
Route::any('{all}', 'HomeController@index')->where(['all' => '.*']);





