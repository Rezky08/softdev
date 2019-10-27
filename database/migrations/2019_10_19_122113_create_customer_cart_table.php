<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->create('customerCarts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customerId')->unsigned();
            $table->bigInteger('customerSellerShopId')->unsigned();
            $table->bigInteger('customerSellerProductId')->unsigned();
            $table->string('customerProductName');
            $table->integer('customerProductPrice')->default(12);
            $table->integer('customerProductQty')->default(12);
            $table->tinyInteger('customerStatus')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketcustomers')->dropIfExists('customerCarts');
    }
}
