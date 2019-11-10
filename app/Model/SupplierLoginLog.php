<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierLoginLog extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsuppliers';
    protected $table = 'supplier_login_logs';
    protected $softDelete = true;
    public function seller()
    {
        return $this->belongsTo('App\Model\SupplierDetail', 'supplier_id');
    }
}
