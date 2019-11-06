<?php

namespace App\Http\Controllers;

use App\Model\SellerDetailTransaction as seller_detail_transactions;
use Illuminate\Http\Request;

class SellerDetailTransactionController extends Controller
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
        $sellerDetailTransactions = $request->seller_detail_transactions;
        $transactionId = $request->seller_transaction_ids;
        // prepare insert detail transactions
        $transactionId->each(function ($id, $group) use ($sellerDetailTransactions) {
            $sellerDetailTransactions[$group] = $sellerDetailTransactions[$group]->map(function ($item) use ($id) {
                $transactionDetail = [
                    'seller_transaction_id' => $id,
                    'seller_product_id' => $item->customer_seller_product_id,
                    'seller_product_name' => $item->customer_product_name,
                    'seller_product_price' => $item->customer_product_price,
                    'seller_product_qty' => $item->customer_product_qty,
                    'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                    'updated_at' => date_format(now(), 'Y-m-d H:i:s')
                ];
                return $transactionDetail;
            });
        });

        $sellerDetailTransactions = $sellerDetailTransactions->flatten(1);
        // insert and check status
        $status = seller_detail_transactions::insert($sellerDetailTransactions->all());
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

        // update stock
        $sellerUpdateStocks = $sellerDetailTransactions->map(function ($item) {
            $product = [
                'seller_product_id' => $item['seller_product_id'],
                'seller_product_qty' => $item['seller_product_qty']
            ];
            return $product;
        });
        $sellerProducts = new SellerProductController;
        $status = $sellerProducts->updateStock($sellerUpdateStocks);
        if ($status->getStatusCode() != 200) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

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
    public function show(...$id)
    {
        $ids = collect($id)->flatten();
        $sellerDetailTransactions = seller_detail_transactions::whereIn('id', $ids)->get();

        // // short variable name
        // $sellerDetailTransactions = $sellerDetailTransactions->map(function ($item) {
        //     $display = [
        //         'id' => $item->id,
        //         'transaction_id' => $item->seller_transaction_id,
        //         'product_id' => $item->seller_product_id,
        //         'product_name' => $item->seller_product_name,
        //         'product_price' => $item->seller_product_price,
        //         'product_qty' => $item->seller_product_qty,
        //         'created_at' => $item->created_at->toDateTimeString()
        //     ];
        //     return (object) $display;
        // });
        // //

        $sellerDetailTransactionIdChecks = $sellerDetailTransactions->map(function ($item) {
            return $item->id;
        });
        $sellerDetailTransactionIdChecks = $ids->diff($sellerDetailTransactionIdChecks);
        if (!$sellerDetailTransactionIdChecks->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'Transaction detail not found',
                'data' => ['transaction_detail_id' => $sellerDetailTransactionIdChecks]
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'data' => $sellerDetailTransactions
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
