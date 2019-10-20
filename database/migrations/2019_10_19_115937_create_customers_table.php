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
        Schema::connection('dbmarketcustomers')->create('customerDetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customerFullname', 100);
            $table->date('customerDOB');
            $table->text('customerAddress')->nullable();
            $table->tinyInteger('customerSex')->default(0);
            $table->string('customerEmail', 100);
            $table->string('customerPhone', 20);
            $table->string('customerUsername', 100)->unique();
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
        Schema::connection('dbmarketcustomers')->dropIfExists('customerDetails');
    }
}
