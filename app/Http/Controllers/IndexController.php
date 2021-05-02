<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;

class IndexController extends Controller
{
	protected $api_url = '';

	public function __construct()
    {
        $this->api_url = env('TOPBILL_API_URL');
    }

    public function index()
    {
        $title  = "Home";

        $timestamp = time();
        $balance = 0;

        $post_data = [
        	'guid' => env('TOPBILL_GUID'),
        	'timestamp' => $timestamp,
        	'signature' => hash_hmac('sha256',env('TOPBILL_GUID') . $timestamp . "balance", env('TOPBILL_SECRET')),
        ];

        $response = Curl::to($this->api_url."/balance")
                    ->withData( $post_data )
                    ->asJson()
                    ->post();

        if(empty($response) || empty($response->status)){ 
        	Session::flash('error', "Something wrong from API");
        }elseif($response->status == 'ERROR') { 
        	Session::flash('error', $response->message); 
        }elseif($response->status == 'SUCCESS'){
			$balance = $response->balance;
		}

        return view('home.index', compact('title', 'balance'));
    }

}
