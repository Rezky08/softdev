<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierSecurity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsuppliers')->create('supplier_securitys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id')->unsigned();
            $table->string('supplier_username', 100)->unique();
            $table->string('supplier_question1', 100)->nullable();
            $table->string('supplier_answer1', 100)->nullable();
            $table->string('supplier_question2', 100)->nullable();
            $table->string('supplier_answer2', 100)->nullable();
            $table->boolean('supplier_status')->default(0);
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
        Schema::connection('dbmarketsuppliers')->dropIfExists('supplier_securitys');
    }
}
