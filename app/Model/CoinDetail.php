<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

class CoinDetail extends Authenticable
{
    use Notifiable, HasApiTokens, SoftDeletes;
    protected $connection = 'dbmarketcoins';
    protected $table = 'coin_details';
    protected $softDelete = true;
    protected $hidden = ['updated_at', 'deleted_at'];
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}