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
        Schema::connection('dbmarketsellers')->create('sellerProducts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sellerShopId')->unsigned();
            $table->string('sellerProductName', 100);
            $table->integer('sellerProductPrice')->default(12);
            $table->integer('sellerProductStock');
            $table->text('sellerProductImage')->nullable()->comment('path Image');
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
        Schema::connection('dbmarketsellers')->dropIfExists('sellerProducts');
    }
}
