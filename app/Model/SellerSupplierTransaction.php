<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerSupplierTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_supplier_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function seller()
    {
        return $this->belongsTo('App\Model\SellerDetail', 'seller_id');
    }
    public function detail()
    {
        return $this->hasMany('App\Model\SellerSupplierDetailTransaction', 'seller_supplier_transaction_id');
    }
}
