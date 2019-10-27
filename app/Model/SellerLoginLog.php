<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerLoginLog extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellerLoginLogs';
    protected $softDelete = true;
}
