<?php

namespace App\Http\Controllers;

use App\Model\CustomerCart as customer_cart;
use App\Model\CustomerDetail as customer_detail;
use App\Model\CustomerLogin as customer_logins;
use App\Model\SellerProduct as seller_product;
use App\Model\SellerShop as seller_shop;
use Illuminate\Http\Request;
use Validator;

class CustomerCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customerCartData = [];
        $customerData = $request->customerData;
        $customerCart = customer_cart::where(['customerId' => $customerData->id, 'customerStatus' => 0]);
        if (!$customerCart->exists()) {
            $response = [
                'status' => 400,
                'Message' => 'Are you want to buy something?'
            ];
            return response()->json($response, 400);
        }
        $customerCart = $customerCart->get();
        foreach ($customerCart as $cartId => $cartData) {
            $productImage = seller_product::where('id', $cartData->customerSellerProductId)->first();
            $customerCartData[] = [
                'id' => $cartData->id,
                'idShop' => $cartData->customerSellerShopId,
                'productId' => $cartData->customerSellerProductId,
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
        $customerData = $request->customerData;
        $productDetails = seller_product::where('id', $request->productId);
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
            'customerSellerShopId' => $productDetails->sellerShopId,
            'customerSellerProductId' => $request->productId,
            'customerProductName' => $productDetails->sellerProductName,
            'customerProductPrice' => $productDetails->sellerProductPrice,
            'customerProductQty' => $request->qty,
            'customerStatus' => 0,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $whereCond = [
            'customerId' => $productAddtoCart['customerId'],
            'customerSellerProductId' => $productAddtoCart['customerSellerProductId'],
            'customerStatus' => 0,
        ];
        $status = customer_cart::where($whereCond);
        $cartData = $status->first();
        if ($status->exists()) {
            if ($productDetails->sellerProductStock < $request->qty + $cartData->customerProductQty) {
                $response = [
                    'status' => 400,
                    'Message' => $productDetails->sellerProductName . " " . $productDetails->sellerProductStock . "  Left"
                ];
                return response()->json($response, 400);
            }
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
            $cartData = customer_cart::find($cartData->id);
            $customerCartData = [];
            $productImage = seller_product::where('id', $cartData->customerSellerProductId)->first();
            $customerCartData[] = [
                'id' => $cartData->id,
                'idShop' => $cartData->customerSellerShopId,
                'productId' => $cartData->customerSellerProductId,
                'productImage' => $productImage->sellerProductImage,
                'productName' => $cartData->customerProductName,
                'productPrice' => $cartData->customerProductPrice,
                'productQty' => $cartData->customerProductQty,
            ];
            $response = [
                'status' => 200,
                'data' => $customerCartData
            ];
            return response()->json($response, 200);
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
            'status' => 200,
            'Message' => 'Success add ' . $productDetails->sellerProductName . ' to Cart'
        ];
        return response()->json($response, 200);
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
        $productImage = seller_product::where('id', $customerCartData->customerSellerProductId)->first();
        $customerCartData = [
            'id' => $customerCartData->id,
            'idShop' => $customerCartData->customerSellerShopId,
            'productId' => $customerCartData->customerSellerProductId,
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
    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'qty' => ['required', 'numeric']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 400,
                'message' => $validation->errors()
            ];
            return response()->json($response, 400);
        }
        $customerData = $request->customerData;
        $whereCond = [
            'id' => $request->id,
            'customerId' => $customerData->id,
        ];
        $cartData = customer_cart::where($whereCond);
        if (!$cartData->exists()) {
            $response = [
                'status' => 404,
                'message' => 'Cart Not Found!'
            ];
            return response()->json($response, 404);
        }
        if ($request->qty <= 0) {
            // cancel product
            $cartDelete = $cartData->delete();
            $cartData = customer_cart::onlyTrashed()->where($whereCond)->first();
            $response = [
                'status ' => 200,
                'message' => $cartData->customerProductName . " has been removed from the cart"
            ];
            return response()->json($response);
        } else {
            $cartUpdate = $cartData->update([
                'customerProductQty' => $request->qty
            ]);
        }
        $cartData = customer_cart::where($whereCond)->first();
        $customerCartData = [];
        $productImage = seller_product::where('id', $cartData->customerSellerProductId)->first();
        $customerCartData[] = [
            'id' => $cartData->id,
            'idShop' => $cartData->customerSellerShopId,
            'productId' => $cartData->customerSellerProductId,
            'productImage' => $productImage->sellerProductImage,
            'productName' => $cartData->customerProductName,
            'productPrice' => $cartData->customerProductPrice,
            'productQty' => $cartData->customerProductQty,
        ];
        $response = [
            'status' => 200,
            'data' => $customerCartData
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 400,
                'message' => $validation->errors()
            ];
            return response()->json($response, 400);
        }
        $customerData = $request->customerData;
        $whereCond = [
            'id' => $request->id,
            'customerId' => $customerData->id,
        ];
        $cartData = customer_cart::where($whereCond);
        if (!$cartData->exists()) {
            $response = [
                'status' => 404,
                'message' => 'Cart Not Found!'
            ];
            return response()->json($response, 404);
        }

        // cancel product
        $cartUpdate = $cartData->delete();
        $cartData = customer_cart::onlyTrashed()->where($whereCond)->first();
        $response = [
            'status ' => 200,
            'message' => $cartData->customerProductName . " has been removed from the cart"
        ];
        return response()->json($response);
    }
}
