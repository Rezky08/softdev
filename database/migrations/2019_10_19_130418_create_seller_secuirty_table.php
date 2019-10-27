<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerSecuirtyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('sellerSecuritys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sellerId')->unsigned();
            $table->string('sellerUsername', 100)->unique();
            $table->string('sellerQuestion1', 100);
            $table->string('sellerAnswer1', 100);
            $table->string('sellerQuestion2', 100);
            $table->string('sellerAnswer2', 100);
            $table->tinyInteger('sellerStatus')->default(0);
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
        Schema::connection('dbmarketsellers')->dropIfExists('sellerSecuritys');
    }
}
