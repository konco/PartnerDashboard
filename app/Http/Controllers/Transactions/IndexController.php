<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Validation\Rule;
use Session;
use Carbon\Carbon;
use Helper;
use DB;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;

class IndexController extends Controller
{
	protected $status = '';
	protected $transactions = '';

    public function __construct()
    {
        $this->transactions = Helper::transaction_table('transactions');
        $this->status = [
        	'' 			=> 'All',
        	'SUCCESS' 	=> 'Success', 
        	'PENDING' 	=> 'Pending',
        	'FAIL' 		=> 'Fail',
        ];
    }

    public function index()
    {
    	$title  = "Transactions";
        $status = $this->status;

        Helper::logActivity("Read", "Transaction", null, "Success");
	    
        return view('transactions.index', compact('title', 'status'));

    }

    public function query(Request $request)
    {
    	$input_from_date = empty($request->fromDate) ? $request->exportFromDate : $request->fromDate;
        $input_to_date = empty($request->toDate) ? $request->exportToDate : $request->toDate;
        $input_search = empty($request->search) ? $request->exportSearch : $request->search;
        $input_status = empty($request->status) ? $request->exportStatus : $request->status;

        if (!empty($input_from_date) && !empty($input_to_date)) {
        	$from = Carbon::createFromFormat('m/d/Y', $input_from_date)->startOfDay();
        	$to = Carbon::createFromFormat('m/d/Y', $input_to_date)->endOfDay();
        }else{
        	$from = Carbon::createFromFormat('m/d/Y', date('m/d/Y'))->startOfDay();
        	$to = Carbon::createFromFormat('m/d/Y', date('m/d/Y'))->endOfDay();
        }
        

        $query = DB::table($this->transactions.' as trx')->whereBetween('trx.created_at', [$from, $to])->orderBy('trx.id', 'DESC');

        return Datatables::of($query)
        	->filter(function ($query) use ($request, $input_status) {
                if (!empty($request->search)) {
                    $query->where(function ($query_inside) use ($request) {
                        $query_inside->where('trx.trx_no', 'like', '%' . $request->search . '%');
                        $query_inside->orWhere('trx.reference_no', 'like', '%' . $request->search . '%');
                        $query_inside->orWhere('trx.customer_data', 'like', '%' . $request->search . '%');
                    });
                }

                if (!empty($input_status)) {
                	$query->where(function ($query_inside) use ($input_status) {
                        $query_inside->where('trx.status', $input_status);
                    });
                }
            })

            ->addColumn('product_price', function ($entry){
                return number_format($entry->price, 2, ',', '.');
            })

            ->toJson();
    }

    public function details(Request $request, $uuid)
    {
    	$transaction = DB::table($this->transactions.' as trx')->where('trx.uuid', $uuid)->first();

        $title  = "Transactions";

        if($transaction->status == 'SUCCESS'){
        	$badge_status = 'success';
        }elseif($transaction->status == 'PENDING'){
        	$badge_status = 'warning';
        }elseif($transaction->status == 'FAIL'){
        	$badge_status = 'danger';
        }

        $customer_data = json_decode($transaction->additional_data,1);
        
        Helper::logActivity("Read", "Transaction-Details", null, "Success");
	    
        return view('transactions.details', compact('title', 'transaction', 'badge_status', 'customer_data'));
    }

    public function search(Request $request)
    {
    	$transaction = '';
        $badge_status = '';
        $customer_data = '';
        $title  = "Transactions";
        $found_trx_message = '';

        if ($request->method() == 'POST') {
	        if($request->trxno){
	        	$transaction = DB::table($this->transactions.' as trx')->where('trx.trx_no', $request->trxno)->first();
		        
		        if(!empty($transaction)){
		        	if($transaction->status == 'SUCCESS'){
			        	$badge_status = 'success';
			        }elseif($transaction->status == 'PENDING'){
			        	$badge_status = 'warning';
			        }elseif($transaction->status == 'FAIL'){
			        	$badge_status = 'danger';
			        }

			        $customer_data = json_decode($transaction->additional_data,1);
		        }
		        

		        $found_trx_message = $transaction ? '' : 'Transaction not found!';
	        }
    	}

        Helper::logActivity("Read", "Transaction-Details", null, "Success");
	    
        return view('transactions.search', compact('title', 'transaction', 'badge_status', 'customer_data', 'found_trx_message'));

    }

    public function inquiry(Request $request, $uuid)
    {
        if($request->ajax()){
            $transaction = DB::table($this->transactions.' as trx')->select('trx.partner_trx_no', 'trx.reference_no')->where('trx.uuid', $uuid)->first();

            if(empty($transaction))
            {
                Helper::logActivity("Create", "inquiry", $uuid, "Error", $uuid);
                abort(404);
            }

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

            Helper::logActivity("Read", "inquiry", json_encode($api_response), "Success", $uuid);

            if(empty($api_response) || empty($api_response->status)){
                $response = array('error' => true, 'message' => "Something wrong from API"); 

            }elseif($api_response->status == 'ERROR') { 
                $response = array('error' => true, 'message' => $api_response->message);
            }elseif($api_response->status == 'SUCCESS'){
                if($api_response->data->trx_status == 'SUCCESS'){
                    DB::table($this->transactions.' as trx')
                    ->where('trx.uuid', $uuid)
                    ->update(['status' => empty($response) ? 'PENDING' : $api_response->data->trx_status, 'response' => json_encode($api_response), 'updated_at' => Carbon::now()]);
                }
                $response = array('error' => false, 'message' => $api_response->message);
            }
            
        }else{
            $response = array(
                'error'    => true,
                'message'   => "Bad Request",
            );
        }

        return response()->json($response);

    }

    public function export(Request $request)
    {
    	return Excel::download(new TransactionExport($request), "transactions".Carbon::now()->timestamp.'.xlsx');
    }

}
