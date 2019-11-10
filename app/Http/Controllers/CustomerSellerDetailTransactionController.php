<?php

namespace App\Http\Controllers;

use App\Model\CustomerSellerDetailTransaction as customer_seller_detail_transaction;
use Illuminate\Http\Request;

class CustomerSellerDetailTransactionController extends Controller
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
        $transactionDetails = $request->transaction_details;
        $transactionDetails = $transactionDetails->map(function ($item) {
            $item = [
                'customer_seller_shop_id' => $item->shop_id,
                'customer_seller_product_id' => $item->product_id,
                'customer_product_name' => $item->product_name,
                'customer_product_price' => $item->product_price,
                'customer_seller_transaction_id' => $item->customer_seller_transaction_id,
                'customer_product_qty' => $item->product_qty,
                'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            return $item;
        });
        $status = customer_seller_detail_transaction::insert($transactionDetails->toArray());
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'sorry, system overload'
            ];
            return response()->json($response, 500);
        }
        $response = [
            'status' => 200,
            'message' => 'item has been purchased'
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
