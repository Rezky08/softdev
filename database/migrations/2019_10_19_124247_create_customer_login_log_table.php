<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLoginLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->create('customerLoginLogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customerId', 100);
            $table->string('customerUsername', 100);
            $table->tinyInteger('loginSuccess')->default(0);
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
        Schema::connection('dbmarketcustomers')->dropIfExists('customerLoginLogs');
    }
}
