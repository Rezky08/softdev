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

        $sellerDetailTransactions = $sellerDetailTransactions->flatten(1)->all();
        // insert and check status
        $status = seller_detail_transactions::insert($sellerDetailTransactions);
        if (!$status) {
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
