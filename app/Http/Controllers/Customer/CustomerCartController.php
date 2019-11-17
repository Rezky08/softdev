<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Seller\SellerProductController as SellerProduct;
use App\Http\Controllers\Controller;
use App\Model\CustomerCart as customer_carts;
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
        $customerData = $request->customerData;
        $customerCart = customer_carts::where(['customer_id' => $customerData->id, 'customer_status' => 0]);
        if (!$customerCart->exists()) {
            $response = [
                'status' => 400,
                'message' => 'Are you want to buy something?'
            ];
            return response()->json($response, 400);
        }
        $customerCart = $customerCart->get();
        $customerProductIds = $customerCart->map(function ($item) {
            return [$item->id => $item->customer_seller_product_id];
        });
        $sellerProduct = new SellerProduct;
        $status = $sellerProduct->showById($customerProductIds);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $customerCart = $customerCart->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->customer_seller_product_id);
            $product = $product->first();
            $customerCartData = [
                'id' => $item->id,
                'customer_id' => $item->customer_id,
                'shop_id' => $item->customer_seller_shop_id,
                'product_id' => $item->customer_seller_product_id,
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'product_qty' => $item->customer_product_qty,
            ];
            return $customerCartData;
        });

        $response = [
            'status' => 200,
            'data' => $customerCart
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
        // check Stock
        $sellerProduct = new SellerProduct;
        $status = $sellerProduct->checkStock($request->product_id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $productDetails = $productDetails->first();
        //

        // check product is exists
        $status = $this->showByProduct($request, $request->product_id);
        if ($status->getStatusCode() == 200) {
            $customerCart = json_decode($status->getContent())->data;
            $customerCart = collect($customerCart)->first();

            /* 
                THIS STAGE MOVE BEFORE PURCHASE
                
                // check if qty>stock
                if ($customerCart->product_qty + 1 > $productDetails->product_stock) {
                $response = [
                    'status' => 400,
                    'message' => 'sorry, ' . $productDetails->product_name . " only " . $productDetails->product_stock . " left"
                ];
                return response()->json($response, 400);
            }
            //
            */

            // update qty
            $updateCart = [
                'customer_product_qty' => $customerCart->product_qty + 1
            ];
            $status = customer_carts::where('id', $customerCart->id)->update($updateCart);
            if (!$status) {
                $response = [
                    'status' => 500,
                    'message' => 'Internal Server Error'
                ];
                return response()->json($response, 500);
            }
            $customerCart->product_qty += 1;
            $response = [
                'status' => 200,
                'data' => $customerCart
            ];
            return response()->json($response, 200);
        }
        //

        $customerData = $request->customerData;
        // get product data
        $status = $sellerProduct->showById($request->product_id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $productDetails = $productDetails->first();
        //

        $productAddtoCart = [
            'customer_id' => $customerData->id,
            'customer_seller_shop_id' => $productDetails->seller_shop_id,
            'customer_seller_product_id' => $request->product_id,
            'customer_product_qty' => $request->qty,
            'customer_status' => 0,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = customer_carts::insert($productAddtoCart);
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Failed add ' . $productDetails->product_name . 'to cart'
            ];
            return response()->json($response, 500);
        }
        $response = [
            'status' => 200,
            'message' => 'Success add ' . $productDetails->product_name . ' to Cart'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ...$cartId)
    {
        $customerData = $request->customerData;
        $cartId = collect($cartId);
        $cartId = $cartId->flatten();
        $whereCond = ['customer_id' => $customerData->id];
        $customerCarts = customer_carts::where($whereCond)->whereIn('id', $cartId)->get();
        $cartIdCheck = $customerCarts->map(function ($item) {
            return $item->id;
        });
        $status = $cartId->diff($cartIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your cart. Do want to buy something product?',
                'data' => ['id' => $status->all()]
            ];
            return response()->json($response, 400);
        }
        $customerProductIds = $customerCarts->map(function ($item) {
            return [$item->id => $item->customer_seller_product_id];
        });
        $sellerProduct = new SellerProduct;
        $status = $sellerProduct->showById($customerProductIds);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $customerCarts = $customerCarts->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->customer_seller_product_id);
            $product = $product->first();
            $customerCartData = [
                'id' => $item->id,
                'shop_id' => $item->customer_seller_shop_id,
                'product_id' => $item->customer_seller_product_id,
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'product_qty' => $item->customer_product_qty,
            ];
            return $customerCartData;
        });

        $response = [
            'status' => 200,
            'data' => $customerCarts
        ];
        return response()->json($response, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function showByProduct(Request $request, ...$productId)
    {
        $customerData = $request->customerData;
        $productId = collect($productId);
        $productId = $productId->flatten();
        $whereCond = ['customer_id' => $customerData->id, 'customer_status' => 0];
        $customerCarts = customer_carts::where($whereCond)->whereIn('customer_seller_product_id', $productId)->get();
        $productIdCheck = $customerCarts->map(function ($item) {
            return $item->customer_seller_product_id;
        });
        $status = $productId->diff($productIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your cart. Do want to buy something product?',
                'data' => ['id' => $status->all()]
            ];
            return response()->json($response, 400);
        }
        $customerProductIds = $customerCarts->map(function ($item) {
            return [$item->id => $item->customer_seller_product_id];
        });
        $sellerProduct = new SellerProduct;
        $status = $sellerProduct->showById($customerProductIds);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $customerCarts = $customerCarts->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->customer_seller_product_id);
            $product = $product->first();
            $customerCartData = [
                'id' => $item->id,
                'shop_id' => $item->customer_seller_shop_id,
                'product_id' => $item->customer_seller_product_id,
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'product_qty' => $item->customer_product_qty,
            ];
            return $customerCartData;
        });

        $response = [
            'status' => 200,
            'data' => $customerCarts
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
        $status = $this->show($request, $request->id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $whereCond = [
            'id' => $request->id,
        ];
        $customerCart = json_decode($status->getContent())->data;
        $customerCart = collect($customerCart);
        $customerCart = $customerCart->first();
        if ($request->qty <= 0) {
            // cancel product
            $status = $this->destroy($request);
            return $status;
        } else {
            $cartUpdate = customer_carts::where($whereCond);
            $cartUpdate = $cartUpdate->update([
                'customer_product_qty' => $request->qty
            ]);
        }
        $status = $this->show($request, $request->id);
        return $status;
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
        $status = $this->show($request, $request->id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $cartData = json_decode($status->getContent())->data;
        $cartData = collect($cartData);
        $cartData = $cartData->first();

        // cancel product
        $cartDelete = customer_carts::where('id', $request->id)->delete();
        if (!$cartDelete) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }
        $response = [
            'status ' => 200,
            'message' => $cartData->product_name . " has been removed from the cart"
        ];
        return response()->json($response);
    }
    public function cartValidation(Request $request, ...$cartId)
    {
        $cartId = collect($cartId)->flatten();

        // get cart data
        if ($cartId->isEmpty()) {
            $status = $this->index($request);
        } else {
            $status = $this->show($request, $cartId);
        }
        //

        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $cartData = json_decode($status->getContent())->data;
        $cartData = collect($cartData);

        // get product id
        $productIds = $cartData->map(function ($item) {
            $item = (object) [
                'product_id' => $item->product_id,
                'product_qty' => $item->product_qty
            ];
            return $item;
        });
        //

        // get product data
        $sellerProduct = new SellerProduct;
        $status = $sellerProduct->checkStock($productIds);
        return $status;
        //
    }
}
