<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerLogin extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellerLogins';
    protected $softDelete = true;

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
