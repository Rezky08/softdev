<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('seller_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('seller_shop_id')->unsigned();
            $table->string('seller_product_name', 100);
            $table->integer('seller_product_price')->default(12);
            $table->integer('seller_product_stock');
            $table->text('seller_product_image')->nullable()->comment('path Image');
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
        Schema::connection('dbmarketsellers')->dropIfExists('seller_products');
    }
}
