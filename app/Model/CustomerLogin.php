<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerLogin extends Model
{
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerLogins';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}