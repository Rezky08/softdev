<?php

namespace App\Http\Controllers;

use App\Model\CustomerCart as customer_cart;
use App\Model\CustomerDetail as customer_detail;
use App\Model\CustomerLogin as customer_logins;
use App\Model\SellerProduct as seller_product;
use App\Model\SellerShop as seller_shop;
use Illuminate\Http\Request;

class CustomerCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($customerId)
    {
        $customerCartData = [];
        $customerCart = customer_cart::where(['customerId' => $customerId, 'customerStatus' => 0]);
        if (!$customerCart->exists()) {
            $response = [
                'status' => 400,
                'Message' => 'Are you want to buy something?'
            ];
            return response()->json($response, 400);
        }
        foreach ($customerCart as $cartId => $cartData) {
            $productImage = seller_product::where('id', $cartData->customerIdProduct)->first();
            $customerCartData[] = [
                'id' => $cartData->id,
                'idShop' => $cartData->customerSellerIdShop,
                'idProduct' => $cartData->customerIdProduct,
                'productImage' => $productImage->sellerProductImage,
                'productName' => $cartData->customerProductName,
                'productPrice' => $cartData->customerProductPrice,
                'productQty' => $cartData->customerProductQty,
            ];
        }
        $response = [
            'status' => 200,
            'data' => $customerCartData
        ];
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Authorization
        $customerData = customer_logins::where('customerToken', $request->header('Authorization'));
        if (!$customerCartData->exists()) {
            $response = [
                'status' => 401,
                'Message' => "You're Not Authorized"
            ];
            return response()->json($response, 401);
        }
        $customerData = $customerData->first();

        $productDetails = seller_product::where('id', $request->idProduct);
        if (!$productDetails->exists()) {
            $response = [
                'status' => 404,
                'Message' => 'Product Not Found'
            ];
            return response()->json($response, 404);
        }
        $productDetails = $productDetails->first();

        $productAddtoCart = [
            'customerId' => $customerData->id,
            'customerSellerIdShop' => $productDetails->sellerId,
            'customerIdProduct' => $request->idProduct,
            'customerProductName' => $productDetails->sellerProductName,
            'customerProductPrice' => $productDetails->sellerProductPrice,
            'customerProductQty' => $request->qty,
            'customerStatus' => 0,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $whereCond = [
            'customerId' => $productAddtoCart['customerId'],
            'customerIdProduct' => $productAddtoCart['customerIdProduct'],
            'customerStatus' => 0,
        ];
        $status = customer_cart::where($whereCond);
        $cartData = $status->first();
        if ($productDetails->sellerProductStock < $request->qty + $cartData->customerProductQty) {
            $response = [
                'status' => 400,
                'Message' => $productDetails->sellerProductName . " " . $productDetails->sellerProductStock . "  Left"
            ];
            return response()->json($response, 400);
        }
        if ($status->exists()) {
            $updateCart = [
                'customerProductQty' => $cartData->customerProductQty + $request->qty,
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            $status = $status->update($updateCart);
            if (!$status) {
                $response = [
                    'status' => 500,
                    'Message' => 'Internal Server Error'
                ];
                return response()->json($response, 500);
            }
            $response = [
                'status' => 201,
                'Message' => 'Success add ' . $productDetails->sellerProductName . ' to Cart'
            ];
            return response()->json($response, 201);
        }
        $status = customer_cart::insert($productAddtoCart);
        if (!$status) {
            $response = [
                'status' => 405,
                'Message' => 'Failed add ' . $productDetails->sellerProductName . 'to cart'
            ];
            return response()->json($response, 405);
        }
        $response = [
            'status' => 201,
            'Message' => 'Success add ' . $productDetails->sellerProductName . ' to Cart'
        ];
        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function show($customerId, $cartId)
    {
        $whereCond = ['customerId' => $customerId, 'id' => $cartId];
        $customerCartData = customer_cart::where($whereCond);
        if (!$customerCartData->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your product. Do want to buy something product?'
            ];
            return response()->json($response, 400);
        }
        $customerCartData = $customerCartData->first();
        $productImage = seller_product::where('id', $customerCartData->customerIdProduct)->first();
        $customerCartData = [
            'id' => $customerCartData->id,
            'idShop' => $customerCartData->customerSellerIdShop,
            'idProduct' => $customerCartData->customerIdProduct,
            'productImage' => $productImage->sellerProductImage,
            'productName' => $customerCartData->customerProductName,
            'productPrice' => $customerCartData->customerProductPrice,
            'productQty' => $customerCartData->customerProductQty,
        ];
        $response = [
            'status' => 200,
            'data' => $customerCartData
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerCart $CustomerCart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerCart $CustomerCart)
    {
        //
    }
}
