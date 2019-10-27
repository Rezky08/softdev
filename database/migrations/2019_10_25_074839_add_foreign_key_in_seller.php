<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInSeller extends Migration
{
    protected $connection = 'dbmarketsellers';
    protected $configForeign = [
        [
            'foreignTables' => ['sellerLoginLogs', 'sellerLogins', 'sellerSecuritys', 'sellerShops'],
            'foreignKey' => 'sellerId',
            'referenceKey' => 'id',
            'referenceTable' => 'sellerDetails'
        ],
        [
            'foreignTables' => ['sellerDetailTransactions'],
            'foreignKey' => 'sellerTransactionId',
            'referenceKey' => 'id',
            'referenceTable' => 'sellerTransactions'
        ],
        [
            'foreignTables' => ['sellerDetailTransactions'],
            'foreignKey' => 'sellerProductId',
            'referenceKey' => 'id',
            'referenceTable' => 'sellerProducts'
        ],
        [
            'foreignTables' => ['sellerProducts', 'sellerTransactions'],
            'foreignKey' => 'sellerShopId',
            'referenceKey' => 'id',
            'referenceTable' => 'sellerShops'
        ],
        [
            'foreignTables' => ['sellerTransactions'],
            'foreignKey' => 'customerId',
            'referenceKey' => 'id',
            'referenceTable' => 'dbmarketcustomers.customerDetails'
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
            $tablesWantToAdd = $config['foreignTables'];
            $foreignKey = $config['foreignKey'];
            $referenceKey = $config['referenceKey'];
            $referenceTable = $config['referenceTable'];
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
            $tablesWantToRemove = $config['foreignTables'];
            $foreignKey = $config['foreignKey'];
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
