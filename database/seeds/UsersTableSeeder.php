<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        User::truncate();

        $user = User::create([
        	'username' => 'superadmin',	
	        'name'  => 'Admin',
	        'email' => 'admin@mail.com',
	        'password'  => bcrypt('123456'),
	        'created_at'  => \Carbon\Carbon::now(),
	        'updated_at'  => \Carbon\Carbon::now(),
		]);

    }
}
