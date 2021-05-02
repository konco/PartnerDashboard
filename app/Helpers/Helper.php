<?php
namespace App\Helpers;

//use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\UserActivityLog;
use Auth;
use Request;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Str;


class Helper{ 
   
	public static function indonesiaTime($tanggal){
        $bulan = array (1 =>   'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                );
        $splitTime = explode(' ', $tanggal);
        $split = explode('-', $splitTime[0]);
        return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0] . ', '. $splitTime[1];
    }

    public static function logActivity($type, $name = null, $additional_data = null, $status = null, $message = null, $userId=null)
    {
        $agent      = new Agent();
        $platform   = $agent->platform();
        $browser    = $agent->browser();
        $ip         = self::getUserIpAddr();

        $additional_data_json = json_encode(array(
            'ip' => $ip,
            'region' => '',
            'platform' => $platform,
            'device' => '',
            'browser' => $browser,
            'message' => $additional_data,
        ));

        $job_data = [];
        $job_data['user_id'] = empty($userId) ? 1 : self::userid();
        $job_data['type'] = $type;
        $job_data['name'] = $name;
        $job_data['additional_data'] = $additional_data_json;
        $job_data['status'] = $status;
        $job_data['message'] = $message;
        $job_data['created_at'] = Carbon::now()->toDateTimeString();

        try{
            $table_name = self::get_table_name_with_prefix('user_activity_logs');
            DB::table($table_name)->insert($job_data);
        } catch (\Exception $e){

        }

        return true;

    }

    public static function customLog($name, $type, $content, $ip = null, $folder = null)
    {
        $job_data = [];
        $job_data['type'] = $folder;
        $job_data['name'] = $name;
        $job_data['remark'] = $type;
        $job_data['message'] = $content;
        $job_data['created_at'] = Carbon::now()->toDateTimeString();

        if ($ip) {
            $job_data['ip'] = $ip;
        }

        try{
            $table_name = self::get_table_name_with_prefix('admin_logs');
            DB::table($table_name)->insert($job_data);
        } catch (\Exception $e){

        }

        return true;
    }

    public static function userLoginLog($user)
    {
        $agent      = new Agent();
        $ip         = self::getUserIpAddr();
        $platform   = $agent->platform();
        $browser    = $agent->browser();

        $additional_data = json_encode(array(
            'ip' => $ip,
            'region' => '',
            'platform' => $platform,
            'device' => '',
            'browser' => $browser,
        ));

        $job_data = [];
        $job_data['user_id'] = $user;
        $job_data['last_login_at'] = Carbon::now()->toDateTimeString();
        $job_data['additional_data'] = $additional_data;
        $job_data['created_at'] = Carbon::now()->toDateTimeString();
        $job_data['updated_at'] = Carbon::now()->toDateTimeString();
        
        try{
            $table_name = self::get_table_name_with_prefix('user_login_logs');
            DB::table($table_name)->insert($job_data);
        } catch (\Exception $e){

        }

        return true;
    }

    public static function get_table_name_with_prefix($origin_table_name)
    {
        if ($origin_table_name == 'admin_logs' || $origin_table_name == 'user_activity_logs' || $origin_table_name == 'user_login_logs') {
            $date = Carbon::now();
            $table_name = $origin_table_name  . '_q' . $date->quarter . '_' . date('Y');
            if(!Schema::hasTable($table_name)) {
                switch ($origin_table_name) {
                    case 'admin_logs':
                        Schema::create($table_name, function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->increments('id');
                            $table->string('type')->nullable()->index();
                            $table->string('name')->nullable()->index();
                            $table->string('remark')->nullable();
                            $table->string('ip')->nullable();
                            $table->timestamp('created_at')->nullable()->index();
                            $table->longText('message');
                            $table->string('reference')->nullable()->default(null)->index();
                        });
                        break;
                    case 'user_activity_logs':
                        Schema::create($table_name, function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->bigIncrements('id');
                            $table->unsignedBigInteger('user_id');
                            $table->enum('type', ['None', 'Create', 'Read', 'Edit', 'Update', 'Delete'])->default('None');
                            $table->text('name')->nullable();
                            $table->text('additional_data')->nullable();
                            $table->string('status')->nullable();
                            $table->longText('message')->nullable();
                            $table->timestamp('created_at')->nullable()->index();
                            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
                        });
                        break;
                    case 'user_login_logs':
                        Schema::create($table_name, function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->bigIncrements('id');
                            $table->unsignedBigInteger('user_id');
                            $table->text('last_login_at')->nullable();
                            $table->text('additional_data')->nullable();
                            $table->timestamp('created_at')->nullable()->index();
                            $table->timestamp('updated_at')->nullable();
                            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
                        });
                        break;
                    default :
                        $table_name = $origin_table_name;
                        break;
                }
            }
            return $table_name;
        } else {
            $table_name = $origin_table_name  . '_' . date('Y');
        }
        
        return $table_name;
    }

    public static function transaction_table($origin_table_name){
        if ($origin_table_name == 'transactions') {
            $table_name = $origin_table_name . '_' . date('Y');
            if(!Schema::hasTable($table_name)) {
                switch ($origin_table_name) {
                    case 'transactions':
                        Schema::create($table_name, function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->bigIncrements('id');
                            $table->string('uuid')->index()->unique();
                            $table->string('trx_no')->index()->nullable();
                            $table->string('partner_trx_no')->index()->nullable();
                            $table->enum('status', ['SUCCESS', 'PENDING', 'FAIL'])->default('PENDING')->index();
                            $table->string('sku_code')->index()->nullable();
                            $table->float('price',12)->default(0);
                            $table->string('reference_no')->index()->nullable();
                            $table->string('product_name')->index()->nullable();
                            $table->string('brand_name')->index()->nullable();
                            $table->string('customer_data')->index()->nullable();
                            $table->text('additional_data')->nullable()->default(null);
                            $table->text('response')->nullable()->default(null);
                            $table->integer('day')->index()->nullable()->default(0);
                            $table->integer('month')->index()->nullable()->default(0);
                            $table->integer('year')->index()->nullable()->default(0);
                            $table->string('created_ip')->nullable();
                            $table->timestamps();
                        });
                        break;
                    
                    default :
                        $table_name = $origin_table_name;
                        break;
                }
            }
            return $table_name;

        }else {
            $table_name = $origin_table_name;
        }

        return $table_name;
    }

    public static function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            return empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
        }
    }

    /*
    *Get client ip address
    */
    public static function getUserIpAddr(){
       $ipaddress = '';
       if (isset($_SERVER['HTTP_CLIENT_IP'])){
           $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
       }
       else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
           $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
       }
       else if(isset($_SERVER['HTTP_X_FORWARDED'])){
           $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
       }
       else if(isset($_SERVER['HTTP_FORWARDED_FOR'])){
           $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
       }
       else if(isset($_SERVER['HTTP_FORWARDED'])){
           $ipaddress = $_SERVER['HTTP_FORWARDED'];
       }
       else if(isset($_SERVER['REMOTE_ADDR'])){
           $ipaddress = $_SERVER['REMOTE_ADDR'];
       }
       else{
           $ipaddress = 'UNKNOWN';    
       }

       return $ipaddress;
    } 

    

    //
    public static function arr_data($data)
    {
        $list = [];
        foreach ($data as $value) {
                $list[] = ['name' => $value];
        }

        $json = json_encode($list);

        return $json;
    }

    public static function array2string($data){
        $string = "";
        foreach($data as $key => $value) {
            $string .= $value['name'].",";
        }
        $string = rtrim($string, ',');

        return $string;
    }


}
