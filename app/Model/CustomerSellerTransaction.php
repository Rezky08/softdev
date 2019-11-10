<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerSellerTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customer_seller_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function customer()
    {
        return $this->belongsTo('App\Model\CustomerDetail', 'customer_id');
    }
    public function detail()
    {
        return $this->hasMany('App\Model\CustomerSellerDetailTransaction', 'customer_seller_transaction_id');
    }
}
