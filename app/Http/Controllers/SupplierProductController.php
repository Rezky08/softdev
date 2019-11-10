<?php

namespace App\Http\Controllers;

use App\Model\SupplierDetail as supplier_details;
use App\Model\SupplierLogin as supplier_logins;
use App\Model\SupplierProduct as supplier_products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class SupplierProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplierProductData = supplier_products::all();
        $supplierProductData  = $supplierProductData->map(function ($item) {
            $showProductData = [
                'id' => $item->id,
                'supplier_id' => $item->supplier_id,
                'product_name' => $item->supplier_product_name,
                'product_price' => $item->supplier_product_price,
                'product_stock' => $item->supplier_product_stock,
                'product_image' => $item->supplier_product_image ?: Storage::url('public/image/default/ImageCrash.png')
            ];
            return $showProductData;
        });

        $response = [
            'status' => 200,
            'data' => $supplierProductData
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
        $supplierData = $request->supplierData;
        $productDetail = [
            'supplier_id' => $supplierData->id,
            'supplier_product_name' => $request->input('product_name'),
            'supplier_product_price' => $request->input('product_price'),
            'supplier_product_stock' => $request->input('product_stock'),
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
            $productImagePath = Storage::url($productImage->store('public/image/suppliers/' . $supplierData->supplier_username));
            $productDetail['supplier_product_image'] = $productImagePath;
        }
        $status = supplier_products::insert($productDetail);
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 200);
        }
        $response = [
            'status' => 200,
            'message' => $request->product_name . ' has been added successfully'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SupplierProduct  $supplierProduct
     * @return \Illuminate\Http\Response
     */
    public function showBySupplier(...$id)
    {
        $supplierId = collect($id)->flatten();
        $supplierData = supplier_details::whereIn('id', $supplierId)->get();
        // check supplier exists
        $supplierIdCheck = $supplierData->map(function ($item) {
            return $item->id;
        });
        $status = $supplierId->diff($supplierIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your supplier',
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }
        //

        $supplierProductData = $supplierData->map(function ($item) {
            return $item->product;
        });
        $supplierProductData = $supplierProductData->flatten();

        $supplierProductData = $supplierProductData->map(function ($item) {
            $showProductData = [
                'id' => $item->id,
                'supplier_id' => $item->supplier_id,
                'product_name' => $item->supplier_product_name,
                'product_price' => $item->supplier_product_price,
                'product_stock' => $item->supplier_product_stock,
                'product_image' => $item->supplier_product_image ?: Storage::url('public/image/default/ImageCrash.png')
            ];
            return $showProductData;
        });

        $response = [
            'status' => 200,
            'data' => $supplierProductData
        ];

        return response()->json($response, 200);
    }

    public function showById(...$id)
    {
        $supplierProductId = collect($id)->flatten();
        $supplierProduct = supplier_products::whereIn('id', $supplierProductId)->get();
        // check supplier exists
        $supplierProductIdCheck = $supplierProduct->map(function ($item) {
            return $item->id;
        });
        $status = $supplierProductId->diff($supplierProductIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, cannot find your supplier',
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }
        //

        $supplierProductData = $supplierProduct->map(function ($item) {
            $showProductData = [
                'id' => $item->id,
                'supplier_id' => $item->supplier_id,
                'product_name' => $item->supplier_product_name,
                'product_price' => $item->supplier_product_price,
                'product_stock' => $item->supplier_product_stock,
                'product_image' => $item->supplier_product_image ?: Storage::url('public/image/default/ImageCrash.png'),
            ];
            return $showProductData;
        });

        $response = [
            'status' => 200,
            'data' => $supplierProductData
        ];

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SupplierProduct  $supplierProduct
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

        $supplierData = $request->supplierData;
        $whereCond = [
            'id' => $request->id
        ];
        $supplierProductData = $supplierData->product->where('id', $request->id);
        if ($supplierProductData->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, we cannot find your product'
            ];
            return response()->json($response, 400);
        }

        $productDetail = [
            'supplier_id' => $supplierData->id,
            'supplier_product_name' => $request->input('product_name'),
            'supplier_product_price' => $request->input('product_price'),
            'supplier_product_stock' => $request->input('product_stock'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $productImage = $request->file('product_image');
        $productImagePath = Storage::url($productImage->store('public/image/suppliers/' . $supplierData->supplier_name));
        $productDetail['supplier_product_image'] = $productImagePath;

        // update Product
        $status = supplier_products::where($whereCond)->update($productDetail);
        $productDetail = supplier_products::where($whereCond)->first();
        $showProductData = [
            'id' => $productDetail->id,
            'supplier_id' => $productDetail->supplier_id,
            'product_name' => $productDetail->supplier_product_name,
            'product_price' => $productDetail->supplier_product_price,
            'product_stock' => $productDetail->supplier_product_stock,
            'product_image' => $productDetail->supplier_product_image ?: Storage::url('public/image/default/ImageCrash.png'),
        ];
        if ($status) {
            $response = [
                'status' => 200,
                'data' => $showProductData
            ];
            return response()->json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SupplierProduct  $supplierProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // validation
        $validation = Validator::make($request->all(), [
            'id' => ['required', 'numeric']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 400,
                'message' => $validation->errors()
            ];
            return response()->json($response, 400);
        }

        $supplierData = $request->supplierData;
        $whereCond = [
            'supplier_id' => $supplierData->id
        ];
        $whereCond = [
            'id' => $request->id,
            'supplier_id' => $supplierData->id,
        ];
        $supplierProductData = $supplierData->product->where('id', $request->id);
        if ($supplierProductData->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, we cannot find your product'
            ];
            return response()->json($response, 400);
        }
        $supplierProductData = $supplierProductData->first();
        $status = supplier_products::where($whereCond)->delete();
        if ($status) {
            $response = [
                'status' => 200,
                'message' => $supplierProductData->supplier_product_name . ' was deleted'
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
            return $item->supplier_product_id;
        });
        $productUpdated = supplier_products::find($productId);

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

        $product = $product->groupBy('supplier_product_id');
        $productUpdated = $productUpdated->groupBy('id');
        $productUpdated = $productUpdated->each(function ($item, $key) use ($product) {
            $item[0]->supplier_product_stock = $item[0]->supplier_product_stock - $product[$key][0]->supplier_product_qty;
            $item = $item[0]->only(['supplier_product_stock']);
            // update stock
            supplier_products::where('id', $key)->update($item);
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
        $product = supplier_products::find($productId);
        $product = $product->map(function ($item) {
            $item = (object) [
                'id' => $item->id,
                'supplier_id' => $item->supplier_id,
                'product_price' => $item->supplier_product_price,
                'product_stock' => $item->supplier_product_stock,
                'product_image' => $item->supplier_product_image,
            ];
            return $item;
        });
        $status = $product->map(function ($item) {
            if ($item->product_stock <= 0) {
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
            'message' => 'Product Available',
            'data' => $product
        ];
        return response()->json($response, 200);
    }
    public function availabeCheck(...$productId)
    {
        $productId = collect($productId);
        $productId = $productId->flatten();
        $product =  supplier_products::find($productId);
        $productIdCheck = $product->map(function ($item) {
            return $item->id;
        });
        $status = $productId->diff($productIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'Product Not Available',
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }
        $response = [
            'status' => 200,
            'message' => 'Product Available',
            'data' => $product
        ];
        return response()->json($response, 200);
    }
}
