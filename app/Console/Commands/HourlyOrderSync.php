<?php

namespace App\Console\Commands;

use App\Configuration;
use App\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class HourlyOrderSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync order with live every thirty minutes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
//        Log::info('do_sync');
        //
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

            ->select('orders.*', 'order_product.product_sku')->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->where('live_synced', '=', 0)->get();



        if ($query->count()) {




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


            try {
                $updated_orders = Order::whereBetween('created_at', [$fromDate, $toDate])
                    ->where('live_synced', '=', 0)->update(['live_synced' => '1']);
            }catch (\Exception $exception)
            {
                Log::error('Error while updation local orders live sync to 1');
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

            $data = $response->getBody()->getContents();
//



//        $decodedData= json_decode($data);

        $log =['Order Synced at '.Carbon::now()];
        $orderLog->pushHandler(new StreamHandler(storage_path('logs/Order_Sync.log')), Logger::INFO);
        $orderLog->info('Order Sync Log', $log);

        }
    }



}
