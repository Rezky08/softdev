<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('sellerShops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sellerId', 100);
            $table->string('sellerShopName', 100);
            $table->string('sellerShopOwnerName', 100);
            $table->string('sellerShopPhone', 20);
            $table->string('sellerShopCertificate', 100);
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
        Schema::connection('dbmarketsellers')->dropIfExists('sellerShops');
    }
}