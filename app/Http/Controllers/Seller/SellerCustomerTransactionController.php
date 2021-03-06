<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Customer\CustomerRegisterController as CustomerRegister;
use App\Http\Controllers\Seller\SellerShopController as SellerShop;
use App\Http\Controllers\Coin\CoinBalanceController as CoinBalance;
use App\Http\Controllers\Coin\CoinTransactionController as CoinTransaction;
use App\Http\Controllers\Controller;


use App\Model\SellerCustomerDetailTransaction as seller_customer_detail_transaction;
use App\Model\SellerCustomerTransaction as seller_customer_transaction;
use Illuminate\Http\Request;

class SellerCustomerTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sellerData = $request->sellerData;
        $sellerShop = $sellerData->shop;
        $shopCustomerTransaction = $sellerShop->transaction;
        $sellerSupplierTransaction = $sellerData->transaction;
        return dd($shopCustomerTransaction, $sellerSupplierTransaction);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customerData = $request->customerData;
        $sellerTransactions = $request->seller_customer_transaction;
        $sellerDetailTransactions = $sellerTransactions->groupBy('shop_id');
        $sellerTransactions = $sellerDetailTransactions->map(function ($item) {
            $item = collect($item);
            $transaction = [
                'customer_id' => $item[0]->customer_id,
                'seller_shop_id' => $item[0]->shop_id,
                'seller_total_price' => $item->sum('product_sub_total'),
                'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            return $transaction;
        });
        $customer = new CustomerRegister;
        $shop = new SellerShop;

        // balance validation
        $totalPrice = $sellerTransactions->sum('seller_total_price');
        $coinBalance = new CoinBalance;
        $status = $coinBalance->validateBalance($customerData->customer_username, $totalPrice);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // insert transactions
        $transactionId = $sellerTransactions->map(function ($item) {
            return seller_customer_transaction::insertGetId($item);
        });

        // send to sellerDetailTransactionController
        $request->request->add(['seller_customer_transaction_ids' => $transactionId, 'seller_customer_detail_transaction' => $sellerDetailTransactions]);

        $sellerDetailTransactions = new  SellerCustomerDetailTransactionController;
        $status = $sellerDetailTransactions->store($request);

        if ($status->getStatusCode() != 200) {
            return $status;
        }

        //coin transaction preparation
        $coinTransactionsPrep = $sellerTransactions->map(function ($item) use ($customer, $shop) {
            // get username customer
            $item = (object) $item;
            $status = $customer->show($item->customer_id);
            if ($status->getStatusCode() != 200) {
                return $status;
            }
            $status = json_decode($status->getContent())->data;
            $status = collect($status);
            $status = $status->first();
            $prep['username_source'] = $status->username;
            //

            // get username seller
            $item = (object) $item;
            $status = $shop->show($item->seller_shop_id);
            if ($status->getStatusCode() != 200) {
                return $status;
            }
            $status = json_decode($status->getContent())->data;
            $status = collect($status);
            $status = $status->first();
            $prep['username_destination'] = $status->seller->username;
            //

            $prep['transaction_balance'] = $item->seller_total_price;
            $prep = (object) $prep;
            return $prep;
        });
        $coinTransactionsPrep = $coinTransactionsPrep->flatten();
        //

        // insert transaction to coin transaction
        $coinTransactions = new CoinTransaction;
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
        $sellerTransactions = seller_customer_transaction::whereIn('id', $ids)->get();

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
