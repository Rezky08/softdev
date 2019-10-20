<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $product = $request->file('productImage')->getClientMimeType();
        return response()->json($product);
    }
}
