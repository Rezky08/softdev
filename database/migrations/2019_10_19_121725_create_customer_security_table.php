<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSecurityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->create('customerSecuritys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customerId')->unsigned();
            $table->string('customerUsername', 100)->unique();
            $table->string('customerQuestion1', 100);
            $table->string('customerAnswer1', 100);
            $table->string('customerQuestion2', 100);
            $table->string('customerAnswer2', 100);
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
        Schema::connection('dbmarketcustomers')->dropIfExists('customerSecuritys');
    }
}
