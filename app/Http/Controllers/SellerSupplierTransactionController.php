<?php

namespace App\Http\Controllers;

use App\Model\SellerCart as seller_carts;
use App\Model\SellerSupplierTransaction as seller_supplier_transaction;
use App\Model\SellerSupplierDetailTransaction as seller_supplier_detail_transaction;
use Illuminate\Http\Request;

class SellerSupplierTransactionController extends Controller
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
        $sellerData = $request->sellerData;
        $sellerCart  = $request->sellerCartData;
        $whereCond = [
            'seller_id' => $sellerData->id,
            ['seller_status', '!=', 1]
        ];

        $subTotal = $sellerCart->map(function ($item) {
            $item->product_sub_total = $item->product_price * $item->product_qty;
            return $item->product_sub_total;
        });

        // send to supplier seller transaction controller
        $supplierSellerTransactions = $sellerCart;
        $request->request->add(['supplier_seller_transaction' => $supplierSellerTransactions]);
        $supplierTransactions = new SupplierSellerTransactionController;
        $status = $supplierTransactions->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $transactions = [
            'seller_id' => $sellerData->id,
            'seller_total_price' => $subTotal->sum(),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $transactionId = seller_supplier_transaction::insertGetId($transactions);
        $sellerCart = $sellerCart->map(function ($item) {
            $item = collect($item);
            $item = $item->except(['created_at', 'updated_at', 'deleted_at', 'seller_status', 'id', 'product_sub_total', 'seller_id']);
            $item = (object) $item->toArray();
            return $item;
        });
        $sellerCart->map(function ($item) use ($transactionId) {
            $item->seller_supplier_transaction_id = $transactionId;
            return $item;
        });



        // send to seller detail transaction controller
        $transactionDetails = $sellerCart;
        $request->request->add(['transaction_details' => $transactionDetails]);
        $transactionDetails = new SellerSupplierDetailTransactionController;

        $status = $transactionDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // change status item from cart
        $deleteFromCart  = seller_carts::where($whereCond)->update(['seller_status' => 1]);
        $response = [
            'status' => 200,
            'message' => 'item has been purchased'
        ];
        return response()->json($response, 200);
    }

    public function storeById(Request $request)
    {
        $sellerData = $request->sellerData;
        $cartId = $request->cart_id;
        $whereCond = [
            'seller_id' => $sellerData->id,
            ['seller_status', '!=', 1]
        ];
        $sellerCart  = seller_carts::where($whereCond)->whereIn('id', $cartId);
        if (!$sellerCart->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your product. Do want to buy something product?'
            ];
            return response()->json($response, 400);
        }
        $sellerCart = $sellerCart->get();
        $subTotal = $sellerCart->map(function ($item) {
            $item->seller_product_sub_total = $item->seller_product_price * $item->seller_product_qty;
            return $item->seller_product_sub_total;
        });
        $transactions = [
            'seller_id' => $sellerData->id,
            'seller_total_price' => $subTotal->sum(),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $transactionId = seller_supplier_transaction::insertGetId($transactions);
        $sellerCart->makeHidden(['created_at', 'updated_at', 'deleted_at', 'seller_status', 'id', 'seller_product_sub_total', 'seller_id']);
        $sellerCart->map(function ($item) use ($transactionId) {
            $item->seller_supplier_transaction_id = $transactionId;
            return $item;
        });

        // send to detail transaction controller
        $transactionDetails = $sellerCart->toArray();
        $request->request->add(['transaction_details' => $transactionDetails]);
        $transactionDetails = new SellerSupplierDetailTransactionController;
        $status = $transactionDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // delete item from cart
        $deleteFromCart  = seller_carts::where($whereCond)->whereIn('id', $cartId)->update(['seller_status' => 1]);
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
