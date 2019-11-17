<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

class SellerDetail extends Authenticable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $connection = 'dbmarketsellers';
    protected $table = 'seller_details';
    protected $softDelete = true;

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    public function shop()
    {
        return $this->hasOne('App\Model\SellerShop', 'seller_id');
    }
    public function login()
    {
        return $this->hasOne('App\Model\SellerLogin', 'seller_id');
    }
    public function loginLogs()
    {
        return $this->hasMany('App\Model\SellerLoginLog', 'seller_id');
    }
    public function cart()
    {
        return $this->hasMany('App\Model\SellerCart', 'seller_id');
    }
    public function transaction()
    {
        return $this->hasMany('App\Model\SellerSupplierTransaction','seller_id');
    }
}
