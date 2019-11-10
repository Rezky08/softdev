<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierLogin extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsuppliers';
    protected $table = 'supplier_logins';
    protected $softDelete = true;

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function seller()
    {
        return $this->belongsTo('App\Model\SupplierDetail', 'supplier_id');
    }
}
