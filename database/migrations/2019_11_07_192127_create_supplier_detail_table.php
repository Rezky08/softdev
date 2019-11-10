<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsuppliers')->create('supplier_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier_fullname', 100);
            $table->date('supplier_dob')->nullable();
            $table->text('supplier_address')->nullable();
            $table->boolean('supplier_sex')->default(0)->comment('0 Female, 1 Male');
            $table->string('supplier_email', 100);
            $table->string('supplier_phone', 20)->nullable();
            $table->string('supplier_username', 100)->unique();
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
        Schema::connection('dbmarketsuppliers')->dropIfExists('supplier_details');
    }
}
