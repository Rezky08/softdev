<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerLoginLog extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_login_logs';
    protected $softDelete = true;
    public function seller()
    {
        return $this->belongsTo('App\Model\SellerDetail', 'seller_id');
    }
}
