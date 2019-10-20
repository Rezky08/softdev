<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('sellerDetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sellerFullname', 100);
            $table->date('sellerDOB')->nullable();
            $table->text('sellerAddress')->nullable();
            $table->tinyInteger('sellerSex')->default(0);
            $table->string('sellerEmail', 100);
            $table->string('sellerPhone', 20)->nullable();
            $table->string('sellerUsername', 100)->unique();
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
        Schema::connection('dbmarketsellers')->dropIfExists('sellerDetails');
    }
}
