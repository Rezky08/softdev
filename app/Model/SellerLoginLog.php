<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerLoginLog extends Model
{
    protected $connection = 'dbmarketsellers';
    protected $table = 'SellerLoginLogs';
}
