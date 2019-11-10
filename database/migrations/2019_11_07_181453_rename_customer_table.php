<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::connection('dbmarketcustomers')->rename('customer_transactions', 'customer_seller_transactions');
        schema::connection('dbmarketcustomers')->rename('customer_detail_transactions', 'customer_seller_detail_transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        schema::connection('dbmarketcustomers')->rename('customer_seller_transactions', 'customer_transactions');
        schema::connection('dbmarketcustomers')->rename('customer_seller_detail_transactions', 'customer_detail_transactions');
    }
}
