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
                'shopId' => $productData->sellerId,
                'productName' => $productData->sellerProductName,
                'productPrice' => $productData->sellerProductPrice,
                'productImage' => $productData->sellerProductImage ?: Storage::url('public/image/default/ImageCrash.png')
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
            'productName' => ['required'],
            'productPrice' => ['required', 'numeric'],
            'productStock' => ['required', 'numeric'],
            'productImage' => ['required', 'mimes:png,jpg,bmp,jpeg']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 200,
                'message' => $validation->errors()
            ];
            return response()->json($response);
        }

        $sellerData = $request->sellerData;
        $sellerShopId = seller_shops::where('sellerId', $sellerData->id)->first();
        $productDetail = [
            'sellerShopId' => $sellerShopId->id,
            'sellerProductName' => $request->input('productName'),
            'sellerProductPrice' => $request->input('productPrice'),
            'sellerProductStock' => $request->input('productStock'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];

        $productImage = $request->file('productImage');
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
            $productImagePath = Storage::url($productImage->store('public/image/sellers/' . $sellerData->sellerUsername));
            $productDetail['sellerProductImage'] = $productImagePath;
        }
        $status = seller_products::insert($productDetail);

        $response = [
            'status' => 200,
            'message' => $request->productName . ' has been added successfully'
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
        $sellerProductData = seller_products::where('sellerShopId', $sellerShopId);
        if ($productId) {
            $sellerProductData = $sellerProductData->where('id', $productId)->first();
        } else {
            $sellerProductData = $sellerProductData->get();
        }
        foreach ($sellerProductData as $productCount => $productData) {
            $showProductData[] = [
                'id' => $productData->id,
                'sellerShopId' => $productData->sellerShopId,
                'productName' => $productData->sellerProductName,
                'productPrice' => $productData->sellerProductPrice,
                'productImage' => $productData->sellerProductImage ?: Storage::url('public/image/default/ImageCrash.png')
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
            'productName' => ['required'],
            'productPrice' => ['required', 'numeric'],
            'productStock' => ['required', 'numeric'],
            'productImage' => ['required', 'image', 'mimes:png,jpg,bmp,jpeg', 'max:102400']
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
            'sellerId' => $sellerData->id,
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
            'sellerShopId' => $sellerShopData->id,
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
            'sellerShopId' => $sellerShopData->id,
            'sellerProductName' => $request->input('productName'),
            'sellerProductPrice' => $request->input('productPrice'),
            'sellerProductStock' => $request->input('productStock'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $productImage = $request->file('productImage');
        $productImagePath = Storage::url($productImage->store('public/image/sellers/' . $sellerShopData->sellerShopName));
        $productDetail['sellerProductImage'] = $productImagePath;

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
            'sellerId' => $sellerData->id
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
            'sellerShopId' => $sellerShopData->id,
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
                'message' => $sellerProductData->sellerProductName . ' was deleted'
            ];
            return response()->json($response);
        }
    }
}
