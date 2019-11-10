<?php

namespace App\Http\Controllers;

use App\Model\SellerSupplierDetailTransaction as seller_supplier_detail_transaction;
use Illuminate\Http\Request;

class SellerSupplierDetailTransactionController extends Controller
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
                'seller_supplier_id' => $item->supplier_id,
                'seller_supplier_product_id' => $item->product_id,
                'seller_product_name' => $item->product_name,
                'seller_product_price' => $item->product_price,
                'seller_supplier_transaction_id' => $item->seller_supplier_transaction_id,
                'seller_product_qty' => $item->product_qty,
                'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            return $item;
        });
        $status = seller_supplier_detail_transaction::insert($transactionDetails->toArray());
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
