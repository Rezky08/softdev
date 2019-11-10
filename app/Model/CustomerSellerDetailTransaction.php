<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSellerDetailTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customer_seller_detail_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function transaction()
    {
        return $this->belongsTo('App\Model\CustomerSellerTrasnsaction', 'customer_seller_transaction_id');
    }
}
