<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerLoginLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->create('sellerLoginLogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sellerId')->unsigned();
            $table->string('sellerUsername', 100);
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
        Schema::connection('dbmarketsellers')->dropIfExists('sellerLoginLogs');
    }
}
