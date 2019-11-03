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
        Schema::connection('dbmarketcustomers')->create('customer_securitys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('customer_id')->unsigned();
            $table->string('customer_username', 100)->unique();
            $table->string('customer_question1', 100);
            $table->string('customer_answer1', 100);
            $table->string('customer_question2', 100);
            $table->string('customer_answer2', 100);
            $table->boolean('customer_status')->default(0);
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
        Schema::connection(dbmarketcustomers)->dropIfExists(customer_securitys);
    }
}
