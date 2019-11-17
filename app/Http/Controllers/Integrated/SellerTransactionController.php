<?php

namespace App\Http\Controllers\Integrated;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Seller\SellerSupplierTransactionController as SellerSupplierTransaction;
use App\Http\Controllers\Supplier\SupplierSellerTransactionController as SupplierSellerTransaction;
use Illuminate\Http\Request;

class SellerTransactionController extends Controller
{
    public function store(Request $request)
    {
        /* 
        check if request has key 'sellerData','sellerCartData' or not
        if not, then return error message 400 bad request
         */
        if (!$request->has(['sellerCartData'])) {
            $response = [
                'status' => 400,
                'message' => "Oh no! We don't see what are you looking for"
            ];
            return response()->json($response, 400);
        }

        $sellerData = $request->sellerData;
        $sellerCart  = $request->sellerCartData;

        /* 
            store transaction to supplier transaction
         */
        $request->request->add(['supplier_seller_transaction' => $sellerCart]);
        $supplierSellerTransaction = new SupplierSellerTransaction;
        $status = $supplierSellerTransaction->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        //if transaction not added to system, then return error message 

        /* 
            store transaction to seller transaction
         */
        $sellerSupplierTransaction = new SellerSupplierTransaction;
        $status = $sellerSupplierTransaction->store($request);
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
