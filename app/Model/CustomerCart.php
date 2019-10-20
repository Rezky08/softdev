<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerCart extends Model
{
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerCarts';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
