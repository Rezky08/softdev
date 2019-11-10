<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnSellerCustomerDetailTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketsellers')->table('seller_customer_detail_transactions', function (Blueprint $table) {
            $table->renameColumn('seller_transaction_id', 'seller_customer_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketsellers')->table('seller_customer_transactions', function (Blueprint $table) {
            $table->renameColumn('seller_customer_transaction_id', 'seller_transaction_id');
        });
    }
}
