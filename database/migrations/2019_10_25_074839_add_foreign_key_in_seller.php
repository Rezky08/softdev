<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInSeller extends Migration
{
    protected $connection = 'dbmarketsellers';
    protected $configForeign = [
        [
            'foreign_tables' => ['seller_login_logs', 'seller_logins', 'seller_securitys', 'seller_shops'],
            'foreign_key' => 'seller_id',
            'reference_key' => 'id',
            'reference_table' => 'seller_details'
        ],
        [
            'foreign_tables' => ['seller_detail_transactions'],
            'foreign_key' => 'seller_transaction_id',
            'reference_key' => 'id',
            'reference_table' => 'seller_transactions'
        ],
        [
            'foreign_tables' => ['seller_detail_transactions'],
            'foreign_key' => 'seller_product_id',
            'reference_key' => 'id',
            'reference_table' => 'seller_products'
        ],
        [
            'foreign_tables' => ['seller_products', 'seller_transactions'],
            'foreign_key' => 'seller_shop_id',
            'reference_key' => 'id',
            'reference_table' => 'seller_shops'
        ],
        [
            'foreign_tables' => ['seller_transactions'],
            'foreign_key' => 'customer_id',
            'reference_key' => 'id',
            'reference_table' => 'dbmarketcustomers.customer_details'
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
