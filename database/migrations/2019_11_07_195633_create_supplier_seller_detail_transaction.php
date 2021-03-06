<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierSellerDetailTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsuppliers')->create('supplier_seller_detail_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_seller_transaction_id')->unsigned();
            $table->bigInteger('supplier_product_id')->unsigned();
            $table->string('supplier_product_name', 100);
            $table->integer('supplier_product_price')->default(12);
            $table->integer('supplier_product_qty');
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
        Schema::connection('dbmarketsuppliers')->dropIfExists('supplier_seller_detail_transactions');
    }
}
