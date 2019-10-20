<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerProduct extends Model
{
    protected $connection = 'dbmarketsellers';
    protected $table = 'SellerProducts';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
