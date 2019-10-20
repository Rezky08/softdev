<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerShop extends Model
{
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellerShops';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
