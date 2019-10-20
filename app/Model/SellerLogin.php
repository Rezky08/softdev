<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SellerLogin extends Model
{
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellerLogins';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
