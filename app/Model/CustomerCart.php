<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCart extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerCarts';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
