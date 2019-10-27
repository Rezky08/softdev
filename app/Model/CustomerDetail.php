<?php

namespace App\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticable;

class CustomerDetail extends Authenticable
{
    use Notifiable, HasApiTokens;
    protected $connection = 'dbmarketcustomers';
    protected $table = 'customerDetails';
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
