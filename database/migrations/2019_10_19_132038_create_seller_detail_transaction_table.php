<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerDetailTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('seller_detail_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_transaction_id')->unsigned();
            $table->bigInteger('seller_product_id')->unsigned();
            $table->string('seller_product_name', 100);
            $table->integer('seller_product_price')->default(12);
            $table->integer('seller_product_qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketsellers')->dropIfExists('seller_detail_transactions');
    }
}
