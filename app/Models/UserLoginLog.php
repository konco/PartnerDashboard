<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class UserLoginLog extends Model
{
	protected $connection = 'mysql';

	//protected $table = Helper::get_table_name_with_prefix('user_login_logs');
	//protected $table = 'user_login_logs_q1_2021';

    protected $fillable = [
        'user_id', 'last_login_at', 'additional_data',
    ];
}
