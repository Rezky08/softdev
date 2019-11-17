<?php
/**
 * This file handle supplier seller transaction, only transaction not other action
 */
namespace App\Http\Controllers;

use App\Model\SupplierSellerDetailTransaction as supplier_seller_detail_transaction;
use App\Model\SupplierSellerTransaction as supplier_seller_transaction;
use Illuminate\Http\Request;

class SupplierSellerTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sellerData = $request->sellerData;
        $supplierTransactions = $request->supplier_seller_transaction;
        $supplierDetailTransactions = $supplierTransactions->groupBy('supplier_id');
        $supplierTransactions = $supplierDetailTransactions->map(function ($item) {
            $item = collect($item);
            $transaction = [
                'seller_id' => $item[0]->seller_id,
                'supplier_id' => $item[0]->supplier_id,
                'supplier_total_price' => $item->sum('product_sub_total'),
                'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            return $transaction;
        });
        $seller = new SellerRegisterController;
        $supplier = new SupplierRegisterController;

        // balance validation
        $totalPrice = $supplierTransactions->sum('supplier_total_price');
        $coinBalance = new CoinBalanceController;
        $status = $coinBalance->validateBalance($sellerData->seller_username, $totalPrice);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // insert transactions
        $transactionId = $supplierTransactions->map(function ($item) {
            return supplier_seller_transaction::insertGetId($item);
        });
        //

        // send to supplierDetailTransactionController
        $request->request->add(['supplier_seller_transaction_ids' => $transactionId, 'supplier_seller_detail_transaction' => $supplierDetailTransactions]);

        $supplierDetailTransactions = new  SupplierSellerDetailTransactionController;
        $status = $supplierDetailTransactions->store($request);

        if ($status->getStatusCode() != 200) {
            return $status;
        }
        //

        //coin transaction preparation
        $coinTransactionsPrep = $supplierTransactions->map(function ($item) use ($seller, $supplier) {
            // get username seller
            $item = (object) $item;
            $status = $seller->show($item->seller_id);
            if ($status->getStatusCode() != 200) {
                return $status;
            }
            $status = json_decode($status->getContent())->data;
            $status = collect($status);
            $status = $status->first();
            $prep['username_source'] = $status->username;
            //

            // get username supplier
            $item = (object) $item;
            $status = $supplier->show($item->supplier_id);
            if ($status->getStatusCode() != 200) {
                return $status;
            }
            $status = json_decode($status->getContent())->data;
            $status = collect($status);
            $status = $status->first();
            $prep['username_destination'] = $status->username;
            //

            $prep['transaction_balance'] = $item->supplier_total_price;
            $prep = (object) $prep;
            return $prep;
        });
        $coinTransactionsPrep = $coinTransactionsPrep->flatten();
        //

        // insert transaction to coin transaction
        $coinTransactions = new CoinTransactionController;
        $request->request->add(['coin_transactions' => $coinTransactionsPrep]);
        $status = $coinTransactions->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }


        return $status;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(...$id)
    {
        $ids = collect($id)->flatten();
        $supplierTransactions = supplier_seller_transaction::whereIn('id', $ids)->get();

        // // short variable name
        // $supplierTransactions = $supplierTransactions->map(function ($item) {
        //     $display = [
        //         'id' => $item->id,
        //         'supplier_id' => $item->supplier_id,
        //         'seller_id' => $item->seller_id,
        //         'total_price' => $item->supplier_total_price,
        //         'created_at' => $item->created_at->toDateTimeString()
        //     ];
        //     return (object) $display;
        // });
        // //

        $supplierTransactionIdChecks = $supplierTransactions->map(function ($item) {
            return $item->id;
        });
        $supplierTransactionIdChecks = $ids->diff($supplierTransactionIdChecks);
        if (!$supplierTransactionIdChecks->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'Transaction not found',
                'data' => ['transaction_id' => $supplierTransactionIdChecks]
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'data' => $supplierTransactions
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
