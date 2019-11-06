<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

class CustomerDetail extends Authenticable
{
    use Notifiable, HasApiTokens, SoftDeletes;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customer_details';
    protected $softDelete = true;
    protected $hidden = ['updated_at', 'deleted_at'];
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function login()
    {
        return $this->hasOne('App\Model\CustomerLogin', 'customer_id');
    }
    public function loginLog()
    {
        return $this->hasMany('App\Model\CustomerLoginLog', 'customer_id');
    }
    public function cart()
    {
        return $this->hasMany('App\Model\CustomerCart', 'customer_id');
    }
    public function transaction()
    {
        return $this->hasMany('App\Model\CustomerTransaction', 'customer_id');
    }
}
