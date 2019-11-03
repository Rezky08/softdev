<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->create('customer_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_fullname', 100);
            $table->date('customer_dob')->nullable();
            $table->text('customer_address')->nullable();
            $table->boolean('customer_sex')->default(0)->comment('0 Female , 1 Male');
            $table->string('customer_email', 100);
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_username', 100)->unique();
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
        Schema::connection('dbmarketcustomers')->dropIfExists('customer_details');
    }
}
