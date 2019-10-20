<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerDetail extends Model
{
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellerDetails';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
