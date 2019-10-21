<?php

namespace App\Http\Controllers;

use App\Model\SellerLogin as seller_logins;
use App\Model\SellerProduct as seller_products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                'sellerId' => $productData->sellerId,
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
        // Authorization
        $sellerData = seller_logins::where('sellerToken', $request->header('Authorization'));
        $sellerData = $sellerData->first();

        $productDetail = [
            'sellerId' => $sellerData->sellerId,
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
            'message' => 'Add product successfully'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function show($sellerId, $productId = null)
    {
        $showProductData = [];
        $sellerProductData = seller_products::where('sellerId', $sellerId);
        if ($productId) {
            $sellerProductData = $sellerProductData->where('id', $productId)->first();
        } else {
            $sellerProductData = $sellerProductData->get();
        }
        foreach ($sellerProductData as $productCount => $productData) {
            $showProductData[] = [
                'id' => $productData->id,
                'sellerId' => $productData->sellerId,
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
    public function update(Request $request, SellerProduct $sellerProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellerProduct $sellerProduct)
    {
        //
    }
}
