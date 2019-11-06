<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTransaction extends Model
{
    use SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customer_transactions';
    protected $softDelete = true;
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function customer()
    {
        return $this->belongsTo('App\Model\CustomerDetail', 'customer_id');
    }
    public function detail()
    {
        return $this->hasMany('App\Model\CustomerDetailTransaction', 'customer_transaction_id');
    }
}
