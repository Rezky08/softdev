<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetailTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customer_detail_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function transaction()
    {
        return $this->belongsTo('App\Model\CustomerTransaction', 'customer_transaction_id');
    }
}
