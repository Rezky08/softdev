<?php

namespace App\Http\Controllers\Integrated;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\CustomerSellerTransactionController as CustomerSellerTransaction;
use App\Http\Controllers\Seller\SellerCustomerTransactionController as SellerCustomerTransaction;
use App\Http\Controllers\Coin\CoinBalanceController as CoinBalance;
use Illuminate\Http\Request;

class CustomerTransactionController extends Controller
{
    public function store(Request $request)
    {
        /* 
        check if request has key 'customerData','customerCartData' or not
        if not, then return error message 400 bad request
         */
        if (!$request->has(['customerCartData'])) {
            $response = [
                'status' => 400,
                'message' => "Oh no! We don't see what are you looking for"
            ];
            return response()->json($response, 400);
        }

        $customerCart  = $request->customerCartData;
        $customerData = $request->customerData;

        /* 
            make subtotal of product will be purchase and check the balance
        */
        $customerCart = $customerCart->map(function ($item) {
            $item->product_sub_total = $item->product_price * $item->product_qty;
            return $item;
        });
        $total = $customerCart->sum('product_sub_total');
        $coinBalance = new CoinBalance;
        $status = $coinBalance->validateBalance($customerData->customer_username, $total);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        /* 
            store transaction to seller transaction
         */
        $request->request->add(['seller_customer_transaction' => $customerCart]);
        $sellerCustomerTransaction = new SellerCustomerTransaction;
        $status = $sellerCustomerTransaction->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        //if transaction not added to system, then return error message 

        /* 
            store transaction to customer transaction
         */
        $customerSellerTransaction = new CustomerSellerTransaction;
        $status = $customerSellerTransaction->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        //if transaction not added to system, then return error message


        $response = [
            'status' => 200,
            'message' => 'Hurray!!! Your item has been purchased'
        ];
        return response()->json($response, 200);
    }
}
