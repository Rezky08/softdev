<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('seller_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_shop_id')->unsigned();
            $table->bigInteger('customer_id')->unsigned();
            $table->integer('seller_total_price')->default(12)->comment('seller price received');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketsellers')->dropIfExists('seller_transactions');
    }
}
