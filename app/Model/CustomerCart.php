<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerCart extends Model
{
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customercarts';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
