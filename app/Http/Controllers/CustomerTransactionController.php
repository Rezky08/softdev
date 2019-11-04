<?php

namespace App\Http\Controllers;

use App\Model\CustomerCart as customer_carts;
use App\Model\CustomerTransaction as customer_transactions;
use App\Model\CustomerDetailTransaction as customer_detailTransactions;
use Illuminate\Http\Request;

class CustomerTransactionController extends Controller
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
        if ($request->has('cart_id')) {
            return $this->storeById($request);
        }
        $customerData = $request->customerData;
        $whereCond = [
            'customer_id' => $customerData->id,
            ['customer_status', '!=', 1]
        ];
        $customerCart  = customer_carts::where($whereCond);
        if (!$customerCart->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your product. Do want to buy something product?'
            ];
            return response()->json($response, 400);
        }
        $customerCart = $customerCart->get();

        // check product stock
        $productId = $customerCart->map(function ($item) {
            return $item->customer_seller_product_id;
        });
        $productAvailabes = new SellerProductController;
        $productAvailabes = $productAvailabes->availabeCheck($productId);
        if ($productAvailabes->getStatusCode() != 200) {
            $productAvailabes = collect($productAvailabes->getData()->data)->flatten();
            $productNotAvailabes = $customerCart->whereIn('customer_seller_product_id', $productAvailabes)->flatten();
            $response['status'] = 400;
            $response['message'] = $productNotAvailabes->map(function ($item) {
                return "Sorry, " . $item->customer_product_name . " Out of Stock";
            });
            return response()->json($response, 400);
        }

        $subTotal = $customerCart->map(function ($item) {
            $item->customer_product_sub_total = $item->customer_product_price * $item->customer_product_qty;
            return $item->customer_product_sub_total;
        });

        // send to seller transaction controller
        $customerCart->makeVisible(['id', 'customer_product_sub_total', 'customer_id']);
        $request->request->add(['seller_transactions' => $customerCart]);
        $sellerTransactions = new SellerTransactionController;
        $status = $sellerTransactions->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $transactions = [
            'customer_id' => $customerData->id,
            'customer_total_price' => $subTotal->sum(),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $transactionId = customer_transactions::insertGetId($transactions);
        $customerCart->makeHidden(['created_at', 'updated_at', 'deleted_at', 'customer_status', 'id', 'customer_product_sub_total', 'customer_id']);
        $customerCart->map(function ($item) use ($transactionId) {
            $item->customer_transaction_id = $transactionId;
            return $item;
        });



        // send to customer detail transaction controller
        $transactionDetails = $customerCart->toArray();
        $request->request->add(['transaction_details' => $transactionDetails]);
        $transactionDetails = new CustomerDetailTransactionController;

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
        $transactionId = customer_transactions::insertGetId($transactions);
        $customerCart->makeHidden(['created_at', 'updated_at', 'deleted_at', 'customer_status', 'id', 'customer_product_sub_total', 'customer_id']);
        $customerCart->map(function ($item) use ($transactionId) {
            $item->customer_transaction_id = $transactionId;
            return $item;
        });

        // send to detail transaction controller
        $transactionDetails = $customerCart->toArray();
        $request->request->add(['transaction_details' => $transactionDetails]);
        $transactionDetails = new CustomerDetailTransactionController;
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
