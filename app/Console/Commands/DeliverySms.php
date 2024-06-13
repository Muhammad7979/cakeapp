<?php

namespace App\Console\Commands;

use App\Branch;
use App\Configuration;
use App\Order;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class DeliverySms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Delivery Sms to all orders that are scheduled for today.';

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
     */
    public function handle()
    {
        //



        $smsLog = new Logger("Sms");


        try
        {
           $hoursBefore= Configuration::where('key','=','Delivery_sms_time')->first();
           $delivery_order_status= Configuration::where('key','=','Delivery_sms_order_status')->first();
            $smsTemplate=Configuration::where('key','=','Sms_Message_Delivery')->first();

        }catch (\Exception $exception)
        {
            Log::info('Sms Delivery Time Not set in Configuration'.$exception->getMessage());
        }

       $fromDate = Carbon::now()->addHours($hoursBefore->value);

        //Minutes should be equal to the cron repetition time ..

        $toDate = Carbon::now()->addHours($hoursBefore->value)->addMinutes(5);

        $explode_id =  explode(',', $delivery_order_status->value);

        Log::info('Explode'.print_r($explode_id,true));
        $orders = Order::whereBetween('delivery_date', [$fromDate, $toDate])
            ->where('delivery_sms', '=', 0)->whereIn('order_status',$explode_id)->get();
//
        Log::info('Current Time > '.Carbon::now(). " From Time : ".$fromDate. ' To Time '.$toDate);

        try{
            $smsTemplate=Configuration::where('key','=','Sms_Message_Delivery')->first();



        }catch (\Exception $exception)
        {
            Log::info("Error Getting Sms template Database ".$exception->getMessage());
        }
        if($orders->count()>0) {
         //   $branch = Branch::where('code', '=', $branchCode)->first();
            Log::info('Orders> '.Carbon::now());

            foreach ($orders as $order)
            {

                $toBranch= Branch::where('code','=',$order->assigned_to)->first();

                Log::info('Branch'.$toBranch->name);

                $replace = [":name", ":order_number", ":branch", ":delivery_date", ":delivery_time"];
                $replaceWith = [$order->customer_name, $order->order_number, $toBranch->name, Carbon::parse($order->delivery_date)->format('d-m-Y'), $order->delivery_time];

                $message = str_replace($replace, $replaceWith, $smsTemplate->value);

                $client = new \GuzzleHttp\Client(['base_uri' => 'http://119.160.92.2:7700/sendsms_url.html', 'headers' => ['Accept' => 'application/json']]);

                Log::info('Sms Message'.$message);

                try {
                    $response = $client->request('GET', '?Username='.env('SMS_API_URL').'&Password='.env('SMS_API_PASS').'&From='.env('SMS_API_FROM_MASK').'&To='.$order->customer_phone.'&Message='.$message);
                } catch (GuzzleException $e) {

                    Log::error("Error Syncing Products From Live".$e->getMessage());

                }

                Log::info('Message Sent');
                $order->delivery_sms = 1;
                $order->delivery_sms_response=$response->getBody()->getContents();
                $order->save();

            }

        }

    }
}
