<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnInSellerCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->table('seller_carts', function (Blueprint $table) {
            $table->dropColumn(['seller_product_name', 'seller_product_price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketsellers')->table('seller_carts', function (Blueprint $table) {
            $table->string('seller_product_name');
            $table->integer('seller_product_price')->default(12);
        });
    }
}
