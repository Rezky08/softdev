<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierProduct extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsuppliers';
    protected $table = 'supplier_products';
    protected $softDelete = true;

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function supplier()
    {
        return $this->belongsTo('App\Model\SupplierDetail', 'supplier_id');
    }
    public function detailTransaction()
    {
        return $this->belongsToMany('App\Model\SupplierSellerDetailTransaction', 'supplier_product_id');
    }
}
