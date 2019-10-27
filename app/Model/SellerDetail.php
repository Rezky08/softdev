<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticable;

class SellerDetail extends Authenticable
{
    use Notifiable, HasApiTokens;
    protected $connection = 'dbmarketsellers';
    protected $table = 'sellerDetails';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
