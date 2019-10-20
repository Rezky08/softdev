<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\File;

class TesterController extends Controller
{
    public function index()
    {
        $response = [
            'message' => Str::random(60)
        ];
        return response()->json($response, 200);
    }
    public function readFile(Request $request)
    {
        $productImage = $request->file('productImage');
        $productImage->store('public/Sellers/Product/');
        if ($productImage->getClientSize() <= 100) {
            return response()->json($productImage->getClientSize());
        } else {
            return response()->json('Melebihi batas');
        }
    }
    public function paramTest($param, $param2)
    {
        var_dump($param, $param2);
    }
}
