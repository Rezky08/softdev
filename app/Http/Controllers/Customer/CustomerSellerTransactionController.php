<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\Model\CustomerCart as customer_carts;
use App\Model\CustomerSellerTransaction as customer_seller_transaction;
use Illuminate\Http\Request;

class CustomerSellerTransactionController extends Controller
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
        $customerData = $request->customerData;
        $customerCart  = $request->customerCartData;
        $whereCond = [
            'customer_id' => $customerData->id,
            ['customer_status', '!=', 1]
        ];

        $subTotal = $customerCart->map(function ($item) {
            $item->product_sub_total = $item->product_price * $item->product_qty;
            return $item->product_sub_total;
        });

        $transactions = [
            'customer_id' => $customerData->id,
            'customer_total_price' => $subTotal->sum(),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $transactionId = customer_seller_transaction::insertGetId($transactions);
        $customerCart = $customerCart->map(function ($item) {
            $item = collect($item);
            $item = $item->except(['created_at', 'updated_at', 'deleted_at', 'customer_status', 'id', 'product_sub_total', 'customer_id']);
            $item = (object) $item->toArray();
            return $item;
        });
        $customerCart->map(function ($item) use ($transactionId) {
            $item->customer_seller_transaction_id = $transactionId;
            return $item;
        });


        // send to customer detail transaction controller
        $transactionDetails = $customerCart;
        $request->request->add(['transaction_details' => $transactionDetails]);
        $transactionDetails = new CustomerSellerDetailTransactionController;

        $status = $transactionDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // change status item from cart
        $deleteFromCart  = customer_carts::where($whereCond)->update(['customer_status' => 1]);
        $response = [
            'status' => 200,
            'message' => 'item has been purchased'
        ];
        return response()->json($response, 200);
    }

    public function storeById(Request $request)
    {
        $customerData = $request->customerData;
        $cartId = $request->cart_id;
        $whereCond = [
            'customer_id' => $customerData->id,
            ['customer_status', '!=', 1]
        ];
        $customerCart  = customer_carts::where($whereCond)->whereIn('id', $cartId);
        if (!$customerCart->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your product. Do want to buy something product?'
            ];
            return response()->json($response, 400);
        }
        $customerCart = $customerCart->get();
        $subTotal = $customerCart->map(function ($item) {
            $item->customer_product_sub_total = $item->customer_product_price * $item->customer_product_qty;
            return $item->customer_product_sub_total;
        });
        $transactions = [
            'customer_id' => $customerData->id,
            'customer_total_price' => $subTotal->sum(),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $transactionId = customer_seller_transaction::insertGetId($transactions);
        $customerCart->makeHidden(['created_at', 'updated_at', 'deleted_at', 'customer_status', 'id', 'customer_product_sub_total', 'customer_id']);
        $customerCart->map(function ($item) use ($transactionId) {
            $item->customer_seller_transaction_id = $transactionId;
            return $item;
        });

        // send to detail transaction controller
        $transactionDetails = $customerCart->toArray();
        $request->request->add(['transaction_details' => $transactionDetails]);
        $transactionDetails = new CustomerSellerDetailTransactionController;
        $status = $transactionDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // delete item from cart
        $deleteFromCart  = customer_carts::where($whereCond)->whereIn('id', $cartId)->update(['customer_status' => 1]);
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
