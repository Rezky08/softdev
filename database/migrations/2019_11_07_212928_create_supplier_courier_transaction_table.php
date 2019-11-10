<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierCourierTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsuppliers')->create('supplier_courier_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_seller_transaction_id')->default(12);
            column::$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketsuppliers')->dropIfExists('supplier_courier_transactions');
    }
}
