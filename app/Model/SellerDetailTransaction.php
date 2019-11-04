<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellerDetailTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_detail_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}