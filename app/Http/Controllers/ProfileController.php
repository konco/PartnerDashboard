<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Helpers\Helper;
use app\User;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->user_login_logs = Helper::get_table_name_with_prefix('user_login_logs');
        $this->user_activity_logs = Helper::get_table_name_with_prefix('user_activity_logs');
    }

    public function index()
    {
        $title  = "Home";
        return view('profile.index', compact('title'));
    }

    public function profile(Request $request)
    {
    	$title 	= auth()->user()->name;

    	Helper::logActivity("Read", "Profile", null, "Success");
    	
    	return view('profile.index', compact('title'));
    }

    public function query_login()
    {
        $data = DB::table($this->user_login_logs)
                ->select('last_login_at','additional_data')
                ->where('user_id',auth()->user()->id)
                ->orderBy('created_at','desc')
                ->get();

        return Datatables::of($data)
            ->whitelist(['last_login_at','additional_data'])
            ->addColumn('ip', function ($data) {
                $json = json_decode($data->additional_data,1);
                return $json['ip'];
            })
            ->addColumn('platform', function ($data) {
                $json = json_decode($data->additional_data,1);
                return $json['platform'];
            })
            ->addColumn('browser', function ($data) {
                $json = json_decode($data->additional_data,1);
                return $json['browser'];
            })
            ->addColumn('region', function ($data) {
                $json = json_decode($data->additional_data,1);
                return $json['region'];
            })
            ->addColumn('device', function ($data) {
                $json = json_decode($data->additional_data,1);
                return $json['device'];
            })
            ->rawColumns(['ip', 'platform', 'browser', 'region', 'device'])
            ->toJson();
    }

    public function query_activity()
    {
        $data = DB::table($this->user_activity_logs)
                ->select('type','additional_data', 'name', 'status', 'message', 'created_at')
                ->where('user_id',auth()->user()->id)
                ->orderBy('created_at','desc')
                ->get();

        return Datatables::of($data)
            ->whitelist(['type','additional_data', 'name', 'status', 'message', 'created_at'])
            ->toJson();
    }

    public function edit_profile(Request $request)
    {
    	$title 	= auth()->user()->name;

        Helper::logActivity("Edit", "Profile", null, "Success");	
    	
    	return view('profile.edit_profile', compact('title'));	
    }

    public function update_profile(Request $request)
    {
    	DB::beginTransaction();
        try {
            $data   = User::findOrFail(auth()->user()->id);
            if(empty($request['password']))
            {
                $data->username     = auth()->user()->username;
                $data->name         = $request['name'];
                $data->email        = $request['email'];
                $data->telegram     = $request['telegram'];
                $data->save();

            }else{
                $data->username     = auth()->user()->username;
                $data->name         = $request['name'];
                $data->email        = $request['email'];
                $data->telegram     = $request['telegram'];
                $data->password     = bcrypt($request['password']);
                $data->save();

                $message = "Your password has been changed : \nName : " .$data->name. "\nUsername : " .$data->username. "\nEmail : " .$data->email. " \nTime : " .Carbon::now()->toDateTimeString(). " \nIP : " .Helper::getUserIpAddr();
		        if(!empty($data->telegram)) {
		            Helper::BotsendMessage($message, $data->telegram);
		        }
            }

            Helper::logActivity("Update", "Profile", null, "Success");
	    	DB::commit();

            return redirect()->route('profile.profile')->with('success', 'Password successfully changed');

        }catch(\Exception $e){
            DB::rollback();
            Helper::logActivity("Update", "Profile", null, "Error", $e->getMessage());
            $message = str_replace(array("\r", "\n","'","`"), ' ', $e->getMessage());
            return redirect()->route('profile.edit.profile')->with("error",$message);
        }

    }


}
