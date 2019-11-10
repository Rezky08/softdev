<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierSellerDetailTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsuppliers';
    protected $table = 'supplier_seller_detail_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function transaction()
    {
        return $this->belongsTo('App\Model\SupplierSellerTransaction', 'supplier_seller_transaction_id');
    }
}
