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
        $customerCart = customer_cart::where(['customer_id' => $customerData->id, 'customer_status' => 0])->leftJoin('dbmarketsellers.seller_products as products', 'customer_carts.customer_seller_product_id', '=', 'products.id');
        if (!$customerCart->exists()) {
            $response = [
                'status' => 400,
                'message' => 'Are you want to buy something?'
            ];
            return response()->json($response, 400);
        }
        $customerCart = $customerCart->get(['customer_carts.*', 'products.seller_product_image AS customer_product_image']);
        foreach ($customerCart as $cartId => $cartData) {
            $customerCartData[] = [
                'id' => $cartData->id,
                'id_shop' => $cartData->customer_seller_shop_id,
                'product_id' => $cartData->customer_seller_product_id,
                'product_image' => $cartData->customer_product_image,
                'product_name' => $cartData->customer_product_name,
                'product_price' => $cartData->customer_product_price,
                'product_qty' => $cartData->customer_product_qty,
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
                'message' => 'Product Not Found'
            ];
            return response()->json($response, 404);
        }
        $productDetails = $productDetails->first();

        $productAddtoCart = [
            'customer_id' => $customerData->id,
            'customer_seller_shop_id' => $productDetails->seller_shop_id,
            'customer_seller_product_id' => $request->productId,
            'customer_product_name' => $productDetails->seller_product_name,
            'customer_product_price' => $productDetails->seller_product_price,
            'customer_product_qty' => $request->qty,
            'customer_status' => 0,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $whereCond = [
            'customer_id' => $productAddtoCart['customer_id'],
            'customer_seller_product_id' => $productAddtoCart['customer_seller_product_id'],
            'customer_status' => 0,
        ];
        $status = customer_cart::where($whereCond);
        $cartData = $status->first();
        if ($status->exists()) {
            if ($productDetails->seller_product_stock < $request->qty + $cartData->customer_product_qty) {
                $response = [
                    'status' => 400,
                    'message' => $productDetails->seller_product_name . " " . $productDetails->seller_product_stock . "  Left"
                ];
                return response()->json($response, 400);
            }
            $updateCart = [
                'customer_product_qty' => $cartData->customer_product_qty + $request->qty,
                'updated_at' => date_format(now(), 'Y-m-d H:i:s')
            ];
            $status = $status->update($updateCart);
            if (!$status) {
                $response = [
                    'status' => 500,
                    'message' => 'Internal Server Error'
                ];
                return response()->json($response, 500);
            }
            $cartData = customer_cart::where('customer_carts.id', $cartData->id)->leftJoin('dbmarketsellers.seller_products as products', 'customer_carts.customer_seller_product_id', '=', 'products.id')->get(['customer_carts.*', 'products.seller_product_image AS customer_product_image'])->first();
            $customerCartData = [];
            $customerCartData[] = [
                'id' => $cartData->id,
                'id_shop' => $cartData->customer_seller_shop_id,
                'product_id' => $cartData->customer_seller_product_id,
                'product_image' => $cartData->customer_product_image,
                'product_name' => $cartData->customer_product_name,
                'product_price' => $cartData->customer_product_price,
                'product_qty' => $cartData->customer_product_qty,
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
                'message' => 'Failed add ' . $productDetails->seller_product_name . 'to cart'
            ];
            return response()->json($response, 405);
        }
        $response = [
            'status' => 200,
            'message' => 'Success add ' . $productDetails->seller_product_name . ' to Cart'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $cartId)
    {
        $customerData = $request->customerData;
        $whereCond = ['customer_carts.customerId' => $customerData->id, 'customer_carts.id' => $cartId];
        $customerCartData = customer_cart::where($whereCond)->leftJoin('dbmarketsellers.seller_products as products', 'customer_carts.customer_seller_product_id', '=', 'products.id');
        if (!$customerCartData->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your product. Do want to buy something product?'
            ];
            return response()->json($response, 400);
        }
        $customerCartData = $customerCartData->get(['customer_carts.*', 'products.seller_product_image AS customer_product_image'])->first();
        $productImage = seller_product::where('id', $customerCartData->customer_seller_product_id)->first();
        $customerCartData = [
            'id' => $customerCartData->id,
            'id_shop' => $customerCartData->customer_seller_shop_id,
            'product_id' => $customerCartData->customer_seller_product_id,
            'product_image' => $productImage->seller_product_image,
            'product_name' => $customerCartData->customer_product_name,
            'product_price' => $customerCartData->customer_product_price,
            'product_qty' => $customerCartData->customer_product_qty,
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
            'customer_id' => $customerData->id,
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
                'message' => $cartData->customer_product_name . " has been removed from the cart"
            ];
            return response()->json($response);
        } else {
            $cartUpdate = $cartData->update([
                'customer_product_qty' => $request->qty
            ]);
        }
        $whereCond = [
            'customer_carts.id' => $request->id,
            'customer_carts.customerId' => $customerData->id
        ];
        $cartData = customer_cart::where($whereCond)->leftJoin('dbmarketsellers.seller_products as products', 'customer_carts.customer_seller_product_id', '=', 'products.id')->get(['customer_carts.*', 'products.seller_product_image AS customer_product_image'])->first();
        $customerCartData = [];
        $customerCartData[] = [
            'id' => $cartData->id,
            'id_shop' => $cartData->customer_seller_shop_id,
            'product_id' => $cartData->customer_seller_product_id,
            'product_image' => $cartData->customer_product_image,
            'product_name' => $cartData->customer_product_name,
            'product_price' => $cartData->customer_product_price,
            'product_qty' => $cartData->customer_product_qty,
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
            'customer_id' => $customerData->id,
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
            'message' => $cartData->customer_product_name . " has been removed from the cart"
        ];
        return response()->json($response);
    }
}
