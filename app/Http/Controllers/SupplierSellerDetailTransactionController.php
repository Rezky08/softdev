<?php

namespace App\Http\Controllers;

use App\Model\SupplierSellerDetailTransaction as supplier_seller_detail_transactions;
use Illuminate\Http\Request;

class SupplierSellerDetailTransactionController extends Controller
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
        $supplierDetailTransactions = $request->supplier_seller_detail_transaction;
        $transactionId = $request->supplier_seller_transaction_ids;
        // prepare insert detail transactions
        $transactionId->each(function ($id, $group) use ($supplierDetailTransactions) {
            $supplierDetailTransactions[$group] = $supplierDetailTransactions[$group]->map(function ($item) use ($id) {
                $transactionDetail = [
                    'supplier_seller_transaction_id' => $id,
                    'supplier_product_id' => $item->product_id,
                    'supplier_product_name' => $item->product_name,
                    'supplier_product_price' => $item->product_price,
                    'supplier_product_qty' => $item->product_qty,
                    'created_at' => date_format(now(), 'Y-m-d H:i:s'),
                    'updated_at' => date_format(now(), 'Y-m-d H:i:s')
                ];
                return $transactionDetail;
            });
        });

        $supplierDetailTransactions = $supplierDetailTransactions->flatten(1);
        // insert and check status
        $status = supplier_seller_detail_transactions::insert($supplierDetailTransactions->all());
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

        // update stock
        $supplierUpdateStocks = $supplierDetailTransactions->map(function ($item) {
            $product = [
                'supplier_product_id' => $item['supplier_product_id'],
                'supplier_product_qty' => $item['supplier_product_qty']
            ];
            return $product;
        });
        $supplierProducts = new SupplierProductController;
        $status = $supplierProducts->updateStock($supplierUpdateStocks);
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
        $supplierDetailTransactions = supplier_seller_detail_transactions::whereIn('id', $ids)->get();

        // // short variable name
        // $supplierDetailTransactions = $supplierDetailTransactions->map(function ($item) {
        //     $display = [
        //         'id' => $item->id,
        //         'transaction_id' => $item->supplier_seller_transaction_id,
        //         'product_id' => $item->supplier_product_id,
        //         'product_name' => $item->supplier_product_name,
        //         'product_price' => $item->supplier_product_price,
        //         'product_qty' => $item->supplier_product_qty,
        //         'created_at' => $item->created_at->toDateTimeString()
        //     ];
        //     return (object) $display;
        // });
        // //

        $supplierDetailTransactionIdChecks = $supplierDetailTransactions->map(function ($item) {
            return $item->id;
        });
        $supplierDetailTransactionIdChecks = $ids->diff($supplierDetailTransactionIdChecks);
        if (!$supplierDetailTransactionIdChecks->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'Transaction detail not found',
                'data' => ['transaction_detail_id' => $supplierDetailTransactionIdChecks]
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'data' => $supplierDetailTransactions
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
