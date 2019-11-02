<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->table('customer_logins', function (Blueprint $table) {
            $table->string('customer_token', 100)->after('customer_password');
        });
        Schema::connection('dbmarketsellers')->table('seller_logins', function (Blueprint $table) {
            $table->string('seller_token', 100)->after('seller_password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_logins', function (Blueprint $table) {
            //
        });
    }
}
