<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnInCustomerCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->table('customer_carts', function (Blueprint $table) {
            $table->dropColumn(['customer_product_name', 'customer_product_price']);
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
            $table->string('customer_product_name');
            $table->integer('customer_product_price')->default(12);
        });
    }
}
