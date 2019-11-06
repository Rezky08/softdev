<?php

namespace App\Http\Controllers;

use App\Model\CoinDetail as coin_details;
use App\Model\CoinTransaction as coin_transactions;
use App\Model\CustomerDetail as customer_details;
use App\Model\SellerDetail as seller_details;
use App\Model\SellerShop as seller_shops;
use App\Model\SellerTransaction as seller_transactions;
use Illuminate\Http\Request;

class CoinTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCustomerSellerTransaction(Request $request)
    {
        $sellerTransactionIds = $request->seller_transaction_ids;
        $sellerTransactionIds = $sellerTransactionIds->flatten();

        // get seller transactions
        $sellerTransactions = new SellerTransactionController;
        $sellerTransactions = $sellerTransactions->show($sellerTransactionIds);
        if ($sellerTransactions->getStatusCode() != 200) {
            return $sellerTransactions;
        }
        $sellerTransactions = json_decode($sellerTransactions->getContent());
        $sellerTransactions = collect($sellerTransactions->data);
        //

        $sellersId = $sellerTransactions->mapToGroups(function ($item) {
            return [$item->id => $item->seller_shop_id];
        });
        $customersId = $sellerTransactions->mapToGroups(function ($item) {
            return [$item->id => $item->customer_id];
        });
        $sellers = seller_shops::whereIn('seller_shops.id', $sellersId)->rightJoin('seller_details', 'seller_shops.seller_id', '=', 'seller_details.id')->get(['seller_details.*', 'seller_shops.id as seller_shop_id']);

        $sellers = $sellers->map(function ($item) use ($sellerTransactions) {
            $transaction = $sellerTransactions->where('seller_shop_id', $item->seller_shop_id)->first();
            $item->transaction_id = $transaction->id;
            return $item;
        });
        $sellersUsernames = $sellers->flatten()->map(function ($item, $key) {
            return $item->seller_username;
        });
        $customers = customer_details::find($customersId)->first();

        $coin_source = coin_details::where('coin_username', $customers->customer_username)->get()->first();
        $coin_destination = coin_details::whereIn('coin_username', $sellersUsernames)->get();
        $credit = $coin_destination->map(function ($item, $key) use ($sellers, $coin_source, $sellerTransactions) {
            $transaction = $sellers->where('seller_username', $item->coin_username)->first();
            $transaction = $sellerTransactions->where('id', $transaction->transaction_id)->first();
            $credit = [
                'coin_id_source' => $item->id,
                'coin_id_destination' => $coin_source->id,
                'coin_transaction_code' => 1,
                'coin_transaction_type' => 0,
                'coin_balance' => $transaction->seller_total_price,
                'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                'updated_at' => date_format(now(), 'Y-m-d H:i:s'),
            ];
            return $credit;
        });
        $debit = $credit->map(function ($item) {
            $source = $item['coin_id_destination'];
            $item['coin_id_destination'] = $item['coin_id_source'];
            $item['coin_id_source'] = $source;
            $item['coin_transaction_type'] = 1;
            return $item;
        });
        $transactions = $debit->concat($credit);
        $status = coin_transactions::insert($transactions->all());
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

        $coinBalance = new CoinBalanceController;
        $debit->map(function ($item) use ($coinBalance) {
            $status = $coinBalance->balanceDebit($item['coin_id_source'], $item['coin_balance']);
        });
        $credit->map(function ($item) use ($coinBalance) {
            $status = $coinBalance->balanceCredit($item['coin_id_source'], $item['coin_balance']);
        });

        $response = [
            'status' => 200,
            'message' => 'Transaction has been created'
        ];
        return response()->json($response, 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
