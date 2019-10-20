<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerShop extends Model
{
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellershops';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
