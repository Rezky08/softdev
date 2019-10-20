<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customerLoginLogs extends Model
{
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerLoginLogs';
}
