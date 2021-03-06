<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDetailTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->create('customer_detail_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_transaction_id')->unsigned();
            $table->bigInteger('customer_seller_shop_id')->unsigned();
            $table->bigInteger('customer_seller_product_id')->unsigned();
            $table->string('customer_product_name', 100);
            $table->integer('customer_product_price')->default(12);
            $table->integer('customer_product_qty')->default(12);
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
        Schema::connection('dbmarketcustomers')->dropIfExists('customer_detail_transactions');
    }
}
