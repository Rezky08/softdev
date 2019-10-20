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
        Schema::connection('dbmarketsellers')->create('sellerDetailTransactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sellerTransactionId', 100);
            $table->string('sellerProductId', 100);
            $table->string('sellerProductName', 100);
            $table->integer('sellerProductPrice')->default(12);
            $table->integer('sellerProductQty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketsellers')->dropIfExists('sellerDetailTransactions');
    }
}
