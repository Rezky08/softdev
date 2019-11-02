<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignInCustomer extends Migration
{
    protected $connection = 'dbmarketcustomers';
    protected $configForeign = [
        [
            'foreign_tables' => ['customer_logins', 'customer_securitys', 'customer_login_logs', 'customer_carts', 'customer_transactions'],
            'foreign_key' => 'customer_id',
            'reference_key' => 'id',
            'reference_table' => 'customer_details'
        ],
        [
            'foreign_tables' => ['customer_detail_transactions'],
            'foreign_key' => 'customer_transaction_id',
            'reference_key' => 'id',
            'reference_table' => 'customer_transactions'
        ],
        [
            'foreign_tables' => ['customer_carts', 'customer_detail_transactions'],
            'foreign_key' => 'customer_seller_shop_id',
            'reference_key' => 'id',
            'reference_table' => 'dbmarketsellers.seller_shops'
        ],
        [
            'foreign_tables' => ['customer_carts', 'customer_detail_transactions'],
            'foreign_key' => 'customer_seller_product_id',
            'reference_key' => 'id',
            'reference_table' => 'dbmarketsellers.seller_products'
        ],
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add Foreign
        foreach ($this->configForeign as $config) {
            $tablesWantToAdd = $config['foreign_tables'];
            $foreignKey = $config['foreign_key'];
            $referenceKey = $config['reference_key'];
            $referenceTable = $config['reference_table'];
            foreach ($tablesWantToAdd as $tableWTA) {
                if (Schema::connection($this->connection)->hasColumn($tableWTA, $foreignKey)) {
                    Schema::connection($this->connection)->table($tableWTA, function (Blueprint $table) use ($referenceKey, $referenceTable, $foreignKey) {
                        $table->bigInteger($foreignKey)->unsigned()->change();
                        $table->foreign($foreignKey)->references($referenceKey)->on($referenceTable)->onDelete('cascade');
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->disableForeignKeyConstraints();
        foreach ($this->configForeign as $config) {
            // Remove ForeignKey
            $tablesWantToRemove = $config['foreign_tables'];
            $foreignKey = $config['foreign_key'];
            foreach ($tablesWantToRemove as $tableWTR) {
                if (Schema::connection($this->connection)->hasColumn($tableWTR, $foreignKey)) {
                    Schema::connection($this->connection)->table($tableWTR, function (Blueprint $table) use ($tableWTR, $foreignKey) {
                        $table->dropForeign($tableWTR . '_' . $foreignKey . '_foreign');
                    });
                }
            }
        }
        schema::connection($this->connection)->disableForeignKeyConstraints();
    }
}
