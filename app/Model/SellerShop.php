<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerShop extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_shops';
    protected $softDelete = true;

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function seller()
    {
        return $this->belongsTo('App\Model\SellerDetail', 'seller_id');
    }
    public function transaction()
    {
        return $this->hasMany('App\Model\SellerTransaction', 'seller_shop_id');
    }
    public function product()
    {
        return $this->hasMany('App\Model\SellerProduct', 'seller_shop_id');
    }
}
