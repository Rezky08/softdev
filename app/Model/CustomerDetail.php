<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerDetails';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
