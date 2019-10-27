<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerLoginLog extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerLoginLogs';
    protected $softDelete = true;
}
