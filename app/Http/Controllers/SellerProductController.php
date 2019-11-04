<?php

namespace App\Http\Controllers;

use App\Model\SellerLogin as seller_logins;
use App\Model\SellerProduct as seller_products;
use App\Model\SellerShop as seller_shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class SellerProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $showProductData = [];
        $sellerProductData = seller_products::all();
        foreach ($sellerProductData as $productCount => $productData) {
            $showProductData[] = [
                'id' => $productData->id,
                'shop_id' => $productData->seller_id,
                'product_name' => $productData->seller_product_name,
                'product_price' => $productData->seller_product_price,
                'product_image' => $productData->seller_product_image ?: Storage::url('public/image/default/ImageCrash.png')
            ];
        }
        $response = [
            'status' => 200,
            'product' => $showProductData
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
        // Validation
        $validation = Validator::make($request->all(), [
            'product_name' => ['required'],
            'product_price' => ['required', 'numeric'],
            'product_stock' => ['required', 'numeric'],
            'product_image' => ['required', 'mimes:png,jpg,bmp,jpeg']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 200,
                'message' => $validation->errors()
            ];
            return response()->json($response);
        }

        $sellerData = $request->sellerData;
        $sellerShopId = seller_shops::where('seller_id', $sellerData->id)->first();
        $productDetail = [
            'seller_shop_id' => $sellerShopId->id,
            'seller_product_name' => $request->input('product_name'),
            'seller_product_price' => $request->input('product_price'),
            'seller_product_stock' => $request->input('product_stock'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];

        $productImage = $request->file('product_image');
        if ($productImage != null) {
            // check image size
            $imageMaxSize = 102400;
            if ($productImage->getClientSize() > $imageMaxSize) {
                $response = [
                    'status' => 400,
                    'message' => "File size is " . $productImage->getClientSize() . " Max 100KB"
                ];
                return response()->json($response, 400);
            }
            $productImagePath = Storage::url($productImage->store('public/image/sellers/' . $sellerData->seller_username));
            $productDetail['seller_product_image'] = $productImagePath;
        }
        $status = seller_products::insert($productDetail);

        $response = [
            'status' => 200,
            'message' => $request->product_name . ' has been added successfully'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function show($sellerShopId, $productId = null)
    {
        $showProductData = [];
        $sellerProductData = seller_products::where('seller_shop_id', $sellerShopId);
        if ($productId) {
            $sellerProductData = $sellerProductData->where('id', $productId)->first();
        } else {
            $sellerProductData = $sellerProductData->get();
        }
        foreach ($sellerProductData as $productCount => $productData) {
            $showProductData[] = [
                'id' => $productData->id,
                'seller_shop_id' => $productData->seller_shop_id,
                'product_name' => $productData->seller_product_name,
                'product_price' => $productData->seller_product_price,
                'product_image' => $productData->seller_product_image ?: Storage::url('public/image/default/ImageCrash.png')
            ];
        }
        $response = [
            'status' => 200,
            'product' => $showProductData
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validation
        $validation = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'product_name' => ['required'],
            'product_price' => ['required', 'numeric'],
            'product_stock' => ['required', 'numeric'],
            'product_image' => ['required', 'image', 'mimes:png,jpg,bmp,jpeg', 'max:102400']
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
            'seller_id' => $sellerData->id,
        ];
        $sellerShopData = seller_shops::where($whereCond);
        if (!$sellerShopData->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, we cannot find your shop'
            ];
            return response()->json($response, 400);
        }
        $sellerShopData = $sellerShopData->first();
        $whereCond = [
            'id' => $request->id,
            'seller_shop_id' => $sellerShopData->id,
        ];
        $sellerProductData = seller_products::where($whereCond);
        if (!$sellerProductData->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, we cannot find your product'
            ];
            return response()->json($response, 400);
        }

        $productDetail = [
            'seller_shop_id' => $sellerShopData->id,
            'seller_product_name' => $request->input('product_name'),
            'seller_product_price' => $request->input('product_price'),
            'seller_product_stock' => $request->input('product_stock'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $productImage = $request->file('product_image');
        $productImagePath = Storage::url($productImage->store('public/image/sellers/' . $sellerShopData->seller_shop_name));
        $productDetail['seller_product_image'] = $productImagePath;

        // update Product
        $status = seller_products::where($whereCond)->update($productDetail);
        $productDetail = seller_products::where($whereCond)->first();
        if ($status) {
            $response = [
                'status' => 200,
                'data' => $productDetail
            ];
            return response()->json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // validation
        $validation = Validator::make($request->all(), [
            'id' => ['required', 'numeric']
        ]);
        $sellerData = $request->sellerData;
        $whereCond = [
            'seller_id' => $sellerData->id
        ];
        $sellerShopData = seller_shops::where($whereCond);
        if (!$sellerShopData->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, we cannot find your shop'
            ];
            return response()->json($response, 400);
        }
        $sellerShopData =  $sellerShopData->first();
        $whereCond = [
            'id' => $request->id,
            'seller_shop_id' => $sellerShopData->id,
        ];
        $sellerProductData = seller_products::where($whereCond);
        if (!$sellerProductData->exists()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, we cannot find your product'
            ];
            return response()->json($response, 400);
        }
        $sellerProductData = $sellerProductData->first();
        $status = seller_products::where($whereCond)->delete();
        if ($status) {
            $response = [
                'status' => 200,
                'message' => $sellerProductData->seller_product_name . ' was deleted'
            ];
            return response()->json($response);
        }
    }

    public function updateStock(...$product)
    {
        $product = collect($product)->flatten(1);
        $product = $product->map(function ($item) {
            $item = (object) $item;
            return $item;
        });
        $productId = $product->map(function ($item) {
            return $item->seller_product_id;
        });
        $productUpdated = seller_products::find($productId);

        // check product
        $productIdCheck = $productUpdated->map(function ($item) {
            return $item->id;
        });
        $status = $productId->diff($productIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }

        $product = $product->groupBy('seller_product_id');
        $productUpdated = $productUpdated->groupBy('id');
        $productUpdated = $productUpdated->each(function ($item, $key) use ($product) {
            $item[0]->seller_product_stock = $item[0]->seller_product_stock - $product[$key][0]->seller_product_qty;
            $item = $item[0]->only(['seller_product_stock']);
            // update stock
            seller_products::where('id', $key)->update($item);
        });

        $response = [
            'status' => 200,
            'message' => 'Stock updated'
        ];
        return response()->json($response, 200);
    }
    public function checkStock(...$productId)
    {
        $productId = collect($productId);
        $productId = $productId->flatten();
        $status = $this->availabeCheck($productId);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        $product = seller_products::find($productId);
        $status = $product->map(function ($item) {
            if ($item->seller_product_stock <= 0) {
                $item = [
                    'id' => $item->id,
                    'shop_id' => $item->seller_shop_id,
                    'product_price' => $item->seller_product_price,
                    'product_stock' => $item->seller_product_stock,
                    'product_image' => $item->seller_product_image
                ];
                return $item;
            }
        });
        $status = $status->filter();
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'out of stock',
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'message' => 'Product Available'
        ];
        return response()->json($response, 200);
    }
    public function availabeCheck(...$productId)
    {
        $productId = collect($productId);
        $productId = $productId->flatten();
        $productIdCheck =  seller_products::find($productId);
        $productIdCheck = $productIdCheck->map(function ($item) {
            return $item->id;
        });
        $status = $productId->diff($productIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'message' => 'Product Available'
        ];
        return response()->json($response, 200);
    }
}
