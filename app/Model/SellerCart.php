<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerCart extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_carts';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function seller()
    {
        return $this->belongsTo('App\Model\SellerDetail', 'seller_id');
    }
}
