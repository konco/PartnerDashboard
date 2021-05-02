<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use app\User;
use App\Helpers\Helper;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $title  = "Users";
        Helper::logActivity("Read", "Users", null, "Success");

        return view('users.index', compact('title'));
    }

    public function query()
    {
        $data = User::select('id', 'username', 'name', 'email', 'last_login_at', 'last_login_ip')
                ->orderBy('id','desc')
                ->get();
                
        return Datatables::of($data)
            ->whitelist(['name','username','email'])
            ->addColumn('link', function ($data) {
                return route('users.edit', $data->id); 
            })
            ->rawColumns(['id', 'last_login_at', 'link','last_login_ip'])
            ->toJson();
    }

    public function create()
    {
        $title  = "Create Users";

        Helper::logActivity("Create", "Users", null, "Success");

        return view('users.add', compact('title'));
    }

    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|unique:users,username',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'pin' => 'required|string',
        ];

        $messages = [
            'username.required' => 'Username is empty!',
            'username.unique' => 'Username already in use!',
            'name.required' => 'Name is empty!',
            'email.required' => 'Email is empty!',
            'email.unique' => 'Email already in use!',
            'pin.required' => 'PIN is empty!',
        ];

        $this->validate($request, $rules, $messages);
        
        DB::beginTransaction();
        try {
            
            $pass_string = Str::random(8);

            $user = User::create(
                [
                    'username' => $request['username'],  
                    'name'  => $request['name'],
                    'email' => $request['email'],
                    'password'  => bcrypt($pass_string),
                    'pin'  => bcrypt($request['pin']),
                ]
            );
            
            Helper::logActivity("Create", "Users", $request->all(), "Success", $request['name']);
            DB::commit();

            return redirect()->route('users.index')->with('success', 'successfully added with email : ' .$request['email'] .' & password: '. $pass_string);

        }catch(\Exception $e){
            DB::rollback();
            Helper::logActivity("Create", "Users" , $request->all(), "Error", $e->getMessage());
            $message = str_replace(array("\r", "\n","'","`"), ' ', $e->getMessage());
            return redirect()->route('users.create')->with("error",$message);
        }
    }

    public function edit(Request $request, $id)
    {
        $data   = User::findOrFail($id);
                
        if(empty($data))
        {
            Helper::logActivity("Edit", "Users", $id, "Error", $id);
            abort(404);
        }

        $title  = $data->name;

        Helper::logActivity("Edit", "Users", $id, "Success", $title);

        return view('users.edit', compact('title', 'data'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name'      => 'required',
            'email'     => 'required|email',
            'password'  => 'nullable',
        ];

        $messages = [
            'name.required'     => 'Name is empty!',
            'email.required'    => 'Email is empty!',
            'email.email'       => 'Email format is wrong!',
        ];

        $this->validate($request, $rules, $messages);

        $data   = User::findOrFail($id);
        
        if(empty($data))
        {
            Helper::logActivity("Update", "Users", $id, "Error", $id);
            abort(404);
        }

        $check_email = User::where(['email' => $request['email']])->where('id', '<>',$id)->first();
        if($check_email){
            return redirect()->route('users.edit',$request['id'])->with('error', "Email : ".$request['email']." already in use!");
        }

        DB::beginTransaction();
        try {
            if(!empty($request['password'])){
                $data->password = bcrypt($request['password']);
            }
            
            if(!empty($request['pin'])){
                $data->pin = bcrypt($request['pin']);
            }

            $data->name         = $request['name'];
            $data->email        = $request['email'];
            $data->save();
            
            Helper::logActivity("Update", "Users", $request->all(), "Success", $data->name);
            DB::commit();

            return redirect()->route('users.index')->with('success', 'successfully changed');

        }catch(\Exception $e){
            DB::rollback();
            Helper::logActivity("Update", "Users" , $request->all(), "Error", $data->name ." | ".$e->getMessage());
            $message = str_replace(array("\r", "\n","'","`"), ' ', $e->getMessage());
            return redirect()->route('users.edit',['id'=>$id])->with("error",$message);
        }

    }

}
