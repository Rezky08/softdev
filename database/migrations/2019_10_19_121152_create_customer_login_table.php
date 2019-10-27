<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->create('customerLogins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customerId')->unsigned();
            $table->string('customerUsername', 100)->unique();
            $table->text('customerPassword');
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
        Schema::connection('dbmarketcustomers')->dropIfExists('customerLogins');
    }
}
