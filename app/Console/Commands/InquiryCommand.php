<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;
use Helper;
use DB;

class InquiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cek:status-transaksi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $transactions = '';

    public function __construct()
    {
        parent::__construct();
        $this->transactions = Helper::transaction_table('transactions');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $now = Carbon::now();
        
        $transactions = DB::table($this->transactions.' as trx')->select('trx.uuid', 'trx.partner_trx_no', 'trx.reference_no')->where('trx.status', 'PENDING')->whereBetween('created_at', [$yesterday, $now])->get();

        if(!empty($transactions)){
            foreach ($transactions as $key => $transaction) {
                $timestamp = time();
                $post_data = [
                    'guid' => env('TOPBILL_GUID'),
                    'timestamp' => $timestamp,
                    'trx_no' => $transaction->partner_trx_no,
                    'ref_no' => $transaction->reference_no,
                    'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "inquiry", env('TOPBILL_SECRET')),
                ];

                $api_response = Curl::to(env('TOPBILL_API_URL').'/inquiry')
                    ->withData( $post_data )
                    ->asJson()
                    ->post();

                Helper::logActivity("Read", "inquiry", json_encode($api_response), "Success", $transaction->reference_no);

                if(empty($api_response) || empty($api_response->status)){
                    Helper::logActivity("Create", "inquiry", 'Something wrong from API', "Error", $transaction->reference_no);

                }elseif($api_response->status == 'ERROR') { 
                    Helper::logActivity("Create", "inquiry", $api_response->message, "Error", $transaction->reference_no);
                    
                }elseif($api_response->status == 'SUCCESS'){
                    if($api_response->data->trx_status == 'SUCCESS'){
                        DB::table($this->transactions)
                        ->where('uuid', $transaction->uuid)
                        ->update(['status' => empty($api_response) ? 'PENDING' : $api_response->data->trx_status, 'response' => json_encode($api_response), 'updated_at' => Carbon::now()]);
                    }

                    Helper::logActivity("Create", "inquiry", json_encode($api_response), "Success", $transaction->reference_no);
                    
                }

            }
            

        }

    }
}
