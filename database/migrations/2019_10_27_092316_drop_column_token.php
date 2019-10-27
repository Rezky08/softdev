<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->table('customerLogins', function (Blueprint $table) {
            $table->dropColumn('customerToken');
        });
        Schema::connection('dbmarketsellers')->table('sellerLogins', function (Blueprint $table) {
            $table->dropColumn('sellerToken');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketcustomers')->table('customerLogins', function (Blueprint $table) {
            $table->string('customerToken', 100)->after('customerPassword');
        });
        Schema::connection('dbmarketsellers')->table('sellerLogins', function (Blueprint $table) {
            $table->string('sellerToken', 100)->after('sellerPassword');
        });
    }
}
