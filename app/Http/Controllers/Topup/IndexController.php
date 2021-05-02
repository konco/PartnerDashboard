<?php

namespace App\Http\Controllers\Topup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Session;
use Carbon\Carbon;
use Helper;
use DB;

class IndexController extends Controller
{
	protected $api_url = '';
    protected $transactions = '';

	public function __construct()
    {
        $this->api_url = env('TOPBILL_API_URL');
        $this->transactions = Helper::transaction_table('transactions');
    }

    public function index()
    {
    	$timestamp = time();
        $productCategory = [];

        $post_data = [
        	'guid' => env('TOPBILL_GUID'),
        	'timestamp' => $timestamp,
        	'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "category", env('TOPBILL_SECRET')),
        ];

        $response = Curl::to($this->api_url."/category")
                    ->withData( $post_data )
                    ->asJson()
                    ->post();

        if(empty($response) || empty($response->status)){ 
        	Session::flash('error', "Something wrong from API");
        }elseif($response->status == 'ERROR') { 
        	Session::flash('error', $response->message); 
        }elseif($response->status == 'SUCCESS'){
			$productCategory = $response->data;
		}
		
    	$title  = "Products";
        Helper::logActivity("Read", "Product", null, "Success");
	    
        return view('topup.index', compact('title', 'productCategory'));
    }

    public function getBrands(Request $request)
    {
    	$timestamp = time();
        $productBrand = [];

        if($request->ajax()){
        	$code = $request->code;

        	$post_data = [
	        	'guid' => env('TOPBILL_GUID'),
	        	'timestamp' => $timestamp,
	        	'category_code' => $code,
	        	'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "brand", env('TOPBILL_SECRET')),
	        ];

	        $response = Curl::to($this->api_url."/brand")
                ->withData( $post_data )
                ->asJson()
                ->post();

            if(empty($response) || empty($response->status)){ 
	        	return response()->json(['error' => true, 'message' => "Something wrong from API"],200);
	        }elseif($response->status == 'ERROR') { 
	        	return response()->json(['error' => true, 'message' => $response->message],200);
	        }elseif($response->status == 'SUCCESS'){
				$productBrand = $response->data;
			}

            $result = [
                "error"     => false,
                "category"     => json_encode($productBrand),
            ];

            return response()->json($result,200);

        }else {
            return response()->json(['error' => true, 'message' => "Not Acceptable"],406);
        }

    }

    public function getProductList(Request $request)
    {
    	$timestamp = time();
        $productField = [];
        $productList = [];

        if($request->ajax()){
        	$code = $request->code;

        	$post_data = [
	        	'guid' => env('TOPBILL_GUID'),
	        	'timestamp' => $timestamp,
	        	'brand_code' => $code,
	        	'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "product", env('TOPBILL_SECRET')),
	        ];

	        $response = Curl::to($this->api_url."/product-list")
                ->withData( $post_data )
                ->asJson()
                ->post();

            if(empty($response) || empty($response->status)){ 
	        	return response()->json(['error' => true, 'message' => "Something wrong from API"],200);
	        }elseif($response->status == 'ERROR') { 
	        	return response()->json(['error' => true, 'message' => $response->message],200);
	        }elseif($response->status == 'SUCCESS'){
				$productField = $response->field;
				$productList = $response->data;
			}

            $result = [
                "error"     => false,
                "field"     => json_encode($productField),
                "list"     => json_encode($productList),
            ];

            return response()->json($result,200);

        }else {
            return response()->json(['error' => true, 'message' => "Not Acceptable"],406);
        }

    }

    public function createTransaction(Request $request)
    {
    	$rules = [
            'product_code' 	=> 'required',
            'product_price'  => 'required',
            'product_name'  => 'required',
            'brand_name'  => 'required',
            'key' 			=> 'required',
            'field' 		=> 'required|array',
        ];

        $this->validate($request, $rules);

        if($request->ajax()){
        	$product_code = $request->product_code;
        	$keyPassword = $request->key;

        	$input_fields = [];
            $customer_data = "";
        	foreach ($request->field as $key => $value) {
        		$input_fields[$value['name']] = $value['value'];
                $customer_data .= $value['value'];
        	}
        	
            if (!Hash::check($keyPassword, auth()->user()->pin)) {
        		return response()->json(['error' => true, 'message' => "PIN salah!"],200);
        	}
            
            try{
                $transactions = Helper::transaction_table('transactions');

                $timestamp = time();
                $ref_no = Str::random(10).$timestamp;

                //transaction
                $uuid = Str::uuid();
                $insert_data = [
                    'uuid' => $uuid,
                    'sku_code' => $product_code,
                    'price' => $request->product_price,
                    'reference_no' => $ref_no,
                    'product_name' => $request->product_name,
                    'brand_name' => $request->brand_name,
                    'customer_data' => $customer_data,
                    'additional_data' => json_encode($input_fields),
                    'day' => date('d'),
                    'month' => date('m'),
                    'year' => date('Y'),
                    'created_ip' => Helper::getUserIpAddr(),
                    'created_at' => Carbon::now(),
                ];

                $transaction_id = DB::table($transactions)->insertGetId($insert_data);

                $post_data = [
                    'guid' => env('TOPBILL_GUID'),
                    'timestamp' => $timestamp,
                    'product_code' => $product_code,
                    'ref_no' => $ref_no,
                    'fields' => $input_fields,
                    'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "transaction", env('TOPBILL_SECRET')),
                ];

                $response = Curl::to($this->api_url."/transaction")
                    ->withData( $post_data )
                    ->asJson()
                    ->post();

                $message = '';
                $trx_no = 'TRX'.sprintf("%05s", $transaction_id);
                $trx_status = '';
                $reference_no = '';
                if(empty($response) || empty($response->status)){ 
                    return response()->json(['error' => true, 'message' => "Something wrong from API"],200);
                }elseif($response->status == 'ERROR') { 
                    return response()->json(['error' => true, 'message' => $response->message],200);
                }elseif($response->status == 'SUCCESS'){
                    $message = $response->message;
                    $trx_status = $response->data->trx_status;
                    $reference_no = $response->data->ref_no;
                }

                $transaction = DB::table($transactions)
                ->where(['id' => $transaction_id])
                ->update(['trx_no'=> $trx_no, 'partner_trx_no' => empty($response) ? null : $response->data->trx_no, 'status' => empty($response) ? 'PENDING' : $response->data->trx_status, 'response' => json_encode($response), 'updated_at' => Carbon::now()]);

                $result = [
                    "error"         => false,
                    "message"       => $message,
                    "trx_no"        => $trx_no,
                    "trx_status"    => $trx_status,
                    "reference_no"  => $reference_no,
                    "brand_name"    => $request->brand_name,
                    "product_name"  => $request->product_name,
                    "price"         => $request->product_price,
                ];

                return response()->json($result,200);

            } catch (\Exception $e) {
                Helper::customLog('error', null, $e->getMessage(). "\n" . $e->getTraceAsString(), Helper::getUserIpAddr(), 'post');

                return response()->json(
                    [
                        'error' => true, 
                        'message' => $e->getMessage()
                    ],500);
            }

            

        }else {
            return response()->json(['error' => true, 'message' => "Not Acceptable"],406);
        }
    }

}
