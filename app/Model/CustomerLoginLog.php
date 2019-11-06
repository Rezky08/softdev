<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerLoginLog extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customer_login_logs';
    protected $softDelete = true;
    public function customer()
    {
        return $this->belongsTo('App\Model\CustomerDetail', 'customer_id');
    }
}
