<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentCustomerCarts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->table('customer_carts', function (Blueprint $table) {
            $table->boolean('customer_status')->unsigned()->comment('0 not purchased, 1 purchased')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketcustomers')->table('customer_carts', function (Blueprint $table) {
            $table->boolean('customer_status')->unsigned();
        });
    }
}
