<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $transactions = DB::table($this->transactions.' as trx')->select('trx.uuid', 'trx.partner_trx_no', 'trx.reference_no')->where('trx.status', 'PENDING')->first();

        if(!empty($transactions)){
            $timestamp = time();
            $post_data = [
                'guid' => env('TOPBILL_GUID'),
                'timestamp' => $timestamp,
                'trx_no' => $transactions->partner_trx_no,
                'ref_no' => $transactions->reference_no,
                'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "inquiry", env('TOPBILL_SECRET')),
            ];

            $api_response = Curl::to(env('TOPBILL_API_URL').'/inquiry')
                ->withData( $post_data )
                ->asJson()
                ->post();

            Helper::logActivity("Read", "inquiry", json_encode($api_response), "Success", $transactions->reference_no);

            if(empty($api_response) || empty($api_response->status)){
                Helper::logActivity("Create", "inquiry", 'Something wrong from API'), "Error", $transactions->reference_no);

            }elseif($api_response->status == 'ERROR') { 
                Helper::logActivity("Create", "inquiry", $api_response->message, "Error", $transactions->reference_no);
                
            }elseif($api_response->status == 'SUCCESS'){
                if($api_response->data->trx_status == 'SUCCESS'){
                    DB::table($this->transactions.' as trx')
                    ->where('trx.uuid', $transactions->uuid)
                    ->update(['status' => empty($response) ? 'PENDING' : $api_response->data->trx_status, 'response' => json_encode($api_response), 'updated_at' => Carbon::now()]);
                }

                Helper::logActivity("Create", "inquiry", json_encode($api_response), "Success", $transactions->reference_no);
                
            }

        }

    }
}
