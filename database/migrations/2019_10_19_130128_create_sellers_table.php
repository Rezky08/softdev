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
        Schema::connection('dbmarketsellers')->create('seller_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('seller_fullname', 100);
            $table->date('seller_dob')->nullable();
            $table->text('seller_address')->nullable();
            $table->boolean('seller_sex')->default(0)->comment('0 Female, 1 Male');
            $table->string('seller_email', 100);
            $table->string('seller_phone', 20)->nullable();
            $table->string('seller_username', 100)->unique();
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
        Schema::connection('dbmarketsellers')->dropIfExists('seller_details');
    }
}
