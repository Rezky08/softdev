<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

class SupplierDetail extends Authenticable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $connection = 'dbmarketsuppliers';
    protected $table = 'supplier_details';
    protected $softDelete = true;

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function login()
    {
        return $this->hasOne('App\Model\SupplierLogin', 'supplier_id');
    }
    public function loginLogs()
    {
        return $this->hasMany('App\Model\SupplierLoginLog', 'supplier_id');
    }
    public function product()
    {
        return $this->hasMany('App\Model\SupplierProduct', 'supplier_id');
    }
    public function transaction()
    {
        return $this->hasMany('App\Model\SupplierTransaction', 'supplier_id');
    }
}
