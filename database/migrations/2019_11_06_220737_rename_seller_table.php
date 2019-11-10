<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSellerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        schema::connection('dbmarketsellers')->rename('seller_transactions', 'seller_customer_transactions');
        schema::connection('dbmarketsellers')->rename('seller_detail_transactions', 'seller_customer_detail_transactions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        schema::connection('dbmarketsellers')->rename('seller_customer_transactions', 'seller_transactions');
        schema::connection('dbmarketsellers')->rename('seller_customer_detail_transactions', 'seller_detail_transactions');
    }
}
