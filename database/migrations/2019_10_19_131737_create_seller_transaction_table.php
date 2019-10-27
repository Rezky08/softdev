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
        Schema::connection('dbmarketsellers')->create('sellerTransactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sellerShopId')->unsigned();
            $table->bigInteger('customerId')->unsigned();
            $table->integer('sellerTotalPrice')->default(12)->comment('seller price received');
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
        Schema::connection('dbmarketsellers')->dropIfExists('sellerTransactions');
    }
}
