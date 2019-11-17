<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Supplier\SupplierProductController as SupplierProduct;
use App\Model\SellerCart as seller_carts;
use Illuminate\Http\Request;
use Validator;

class SellerCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sellerData = $request->sellerData;
        $sellerCart = seller_carts::where(['seller_id' => $sellerData->id, 'seller_status' => 0]);
        if (!$sellerCart->exists()) {
            $response = [
                'status' => 400,
                'message' => 'Are you want to buy something?'
            ];
            return response()->json($response, 400);
        }
        $sellerCart = $sellerCart->get();
        $sellerProductIds = $sellerCart->map(function ($item) {
            return [$item->id => $item->seller_supplier_product_id];
        });
        $supplierProduct = new SupplierProduct;
        $status = $supplierProduct->showById($sellerProductIds);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $sellerCart = $sellerCart->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->seller_supplier_product_id);
            $product = $product->first();
            $sellerCartData = [
                'id' => $item->id,
                'seller_id' => $item->seller_id,
                'supplier_id' => $item->seller_supplier_id,
                'product_id' => $item->seller_supplier_product_id,
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'product_qty' => $item->seller_product_qty,
            ];
            return $sellerCartData;
        });

        $response = [
            'status' => 200,
            'data' => $sellerCart
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
        $supplierProduct = new SupplierProduct;
        $status = $supplierProduct->checkStock($request->product_id);
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
            $sellerCart = json_decode($status->getContent())->data;
            $sellerCart = collect($sellerCart)->first();

            /* 
            THIS STAGE MOVE BEFORE PURCHASE

            // check if qty>stock
            if ($sellerCart->product_qty + 1 > $productDetails->product_stock) {
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
                'seller_product_qty' => $sellerCart->product_qty + 1
            ];
            $status = seller_carts::where('id', $sellerCart->id)->update($updateCart);
            if (!$status) {
                $response = [
                    'status' => 500,
                    'message' => 'Internal Server Error'
                ];
                return response()->json($response, 500);
            }
            $sellerCart->product_qty += 1;
            $response = [
                'status' => 200,
                'data' => $sellerCart
            ];
            return response()->json($response, 200);
        }
        //

        $sellerData = $request->sellerData;
        // get product data
        $status = $supplierProduct->showById($request->product_id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $productDetails = $productDetails->first();
        //

        $productAddtoCart = [
            'seller_id' => $sellerData->id,
            'seller_supplier_id' => $productDetails->supplier_id,
            'seller_supplier_product_id' => $request->product_id,
            'seller_product_qty' => $request->qty,
            'seller_status' => 0,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = seller_carts::insert($productAddtoCart);
        if (!$status) {
            $response = [
                'status' => 405,
                'message' => 'Failed add ' . $productDetails->product_name . 'to cart'
            ];
            return response()->json($response, 405);
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
     * @param  \App\Model\SellerCart  $SellerCart
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ...$cartId)
    {
        $sellerData = $request->sellerData;
        $cartId = collect($cartId);
        $cartId = $cartId->flatten();
        $whereCond = ['seller_id' => $sellerData->id];
        $sellerCarts = seller_carts::where($whereCond)->whereIn('id', $cartId)->get();
        $cartIdCheck = $sellerCarts->map(function ($item) {
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
        $sellerProductIds = $sellerCarts->map(function ($item) {
            return [$item->id => $item->seller_supplier_product_id];
        });
        $supplierProduct = new SupplierProduct;
        $status = $supplierProduct->showById($sellerProductIds);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $sellerCarts = $sellerCarts->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->seller_supplier_product_id);
            $product = $product->first();
            $sellerCartData = [
                'id' => $item->id,
                'supplier_id' => $item->seller_supplier_id,
                'product_id' => $item->seller_supplier_product_id,
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'product_qty' => $item->seller_product_qty,
            ];
            return $sellerCartData;
        });

        $response = [
            'status' => 200,
            'data' => $sellerCarts
        ];
        return response()->json($response, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Model\SellerCart  $SellerCart
     * @return \Illuminate\Http\Response
     */
    public function showByProduct(Request $request, ...$productId)
    {
        $sellerData = $request->sellerData;
        $productId = collect($productId);
        $productId = $productId->flatten();
        $whereCond = ['seller_id' => $sellerData->id, 'seller_status' => 0];
        $sellerCarts = seller_carts::where($whereCond)->whereIn('seller_supplier_product_id', $productId)->get();
        $productIdCheck = $sellerCarts->map(function ($item) {
            return $item->seller_supplier_product_id;
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
        $sellerProductIds = $sellerCarts->map(function ($item) {
            return [$item->id => $item->seller_supplier_product_id];
        });
        $supplierProduct = new SupplierProduct;
        $status = $supplierProduct->showById($sellerProductIds);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $productDetails = json_decode($status->getContent())->data;
        $productDetails = collect($productDetails);
        $sellerCarts = $sellerCarts->map(function ($item) use ($productDetails) {
            $product = $productDetails->where('id', $item->seller_supplier_product_id);
            $product = $product->first();
            $sellerCartData = [
                'id' => $item->id,
                'supplier_id' => $item->seller_supplier_id,
                'product_id' => $item->seller_supplier_product_id,
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'product_qty' => $item->seller_product_qty,
            ];
            return $sellerCartData;
        });

        $response = [
            'status' => 200,
            'data' => $sellerCarts
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\SellerCart  $SellerCart
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
        $sellerData = $request->sellerData;
        $status = $this->show($request, $request->id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $whereCond = [
            'id' => $request->id,
        ];
        $sellerCart = json_decode($status->getContent())->data;
        $sellerCart = collect($sellerCart);
        $sellerCart = $sellerCart->first();
        if ($request->qty <= 0) {
            // cancel product
            $status = $this->destroy($request);
            return $status;
        } else {
            $cartUpdate = seller_carts::where($whereCond);
            $cartUpdate = $cartUpdate->update([
                'seller_product_qty' => $request->qty
            ]);
        }
        $status = $this->show($request, $request->id);
        return $status;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\SellerCart  $SellerCart
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
        $sellerData = $request->sellerData;
        $whereCond = [
            'id' => $request->id,
            'seller_id' => $sellerData->id,
        ];
        $status = $this->show($request, $request->id);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $cartData = json_decode($status->getContent())->data;
        $cartData = collect($cartData);
        $cartData = $cartData->first();

        // cancel product
        $cartDelete = seller_carts::where('id', $request->id)->delete();
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
        $supplierProduct = new SupplierProduct;
        $status = $supplierProduct->checkStock($productIds);
        return $status;
        //
    }
}
