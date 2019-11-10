<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerSupplierDetailTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('seller_supplier_detail_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_supplier_transaction_id')->unsigned();
            $table->bigInteger('seller_supplier_id')->unsigned();
            $table->bigInteger('seller_supplier_product_id')->unsigned();
            $table->string('seller_product_name', 100);
            $table->integer('seller_product_price')->default(12);
            $table->integer('seller_product_qty')->default(12);
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
        Schema::connection('dbmarketsellers')->dropIfExists('seller_supplier_detail_transactions');
    }
}
