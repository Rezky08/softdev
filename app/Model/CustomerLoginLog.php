<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerLoginLog extends Model
{
    protected $connection = 'dbmarketcustomers';
    protected $table = 'CustomerLoginLogs';
}
