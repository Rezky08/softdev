<?php

namespace App\Http\Controllers;

use App\Model\SellerDetailTransaction as seller_detail_transactions;
use App\Model\SellerTransaction as seller_transactions;
use Illuminate\Http\Request;

class SellerTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customerData = $request->customerData;
        $sellerTransactions = $request->seller_transactions;
        $sellerDetailTransactions = $sellerTransactions->groupBy('customer_seller_shop_id');
        $sellerTransactions = $sellerDetailTransactions->map(function ($item) {
            $transaction = [
                'customer_id' => $item[0]->customer_id,
                'seller_shop_id' => $item[0]->customer_seller_shop_id,
                'seller_total_price' => $item->sum('customer_product_sub_total'),
                'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            return $transaction;
        });

        // balance validation
        $totalPrice = $sellerTransactions->sum('seller_total_price');
        $coinBalance = new CoinBalanceController;
        $status = $coinBalance->validateBalance($customerData->customer_username, $totalPrice);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // insert transactions
        $transactionId = $sellerTransactions->map(function ($item) {
            return seller_transactions::insertGetId($item);
        });

        // send to sellerDetailTransactionController
        $request->request->add(['seller_transaction_ids' => $transactionId, 'seller_detail_transactions' => $sellerDetailTransactions]);

        $sellerDetailTransactions = new  SellerDetailTransactionController;
        $status = $sellerDetailTransactions->store($request);

        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // insert transaction to coin transaction
        $coinTransactions = new CoinTransactionController;
        $status = $coinTransactions->storeCustomerSellerTransaction($request);
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
        $sellerTransactions = seller_transactions::whereIn('id', $ids)->get();

        // // short variable name
        // $sellerTransactions = $sellerTransactions->map(function ($item) {
        //     $display = [
        //         'id' => $item->id,
        //         'shop_id' => $item->seller_shop_id,
        //         'customer_id' => $item->customer_id,
        //         'total_price' => $item->seller_total_price,
        //         'created_at' => $item->created_at->toDateTimeString()
        //     ];
        //     return (object) $display;
        // });
        // //

        $sellerTransactionIdChecks = $sellerTransactions->map(function ($item) {
            return $item->id;
        });
        $sellerTransactionIdChecks = $ids->diff($sellerTransactionIdChecks);
        if (!$sellerTransactionIdChecks->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'Transaction not found',
                'data' => ['transaction_id' => $sellerTransactionIdChecks]
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'data' => $sellerTransactions
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
