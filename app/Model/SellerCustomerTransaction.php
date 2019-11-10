<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerCustomerTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_customer_transactions';
    protected $softDelete = true;
    // protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function shop()
    {
        return $this->belongsTo('App\Model\SellerShop', 'seller_shop_id');
    }
    public function detail()
    {
        return $this->hasMany('App\Model\SellerCustomerDetailTransaction', 'seller_customer_transaction_id');
    }
}
