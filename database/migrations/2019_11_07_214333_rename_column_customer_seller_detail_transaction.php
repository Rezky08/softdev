<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnCustomerSellerDetailTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('dbmarketcustomers')->table('customer_seller_detail_transactions', function (Blueprint $table) {
            $table->renameColumn('customer_transaction_id', 'customer_seller_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('dbmarketcustomers')->table('customer_seller_detail_transactions', function (Blueprint $table) {
            $table->renameColumn('customer_seller_transaction_id', 'customer_transaction_id');
        });
    }
}
