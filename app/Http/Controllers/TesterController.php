<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TesterController extends Controller
{
    public function index()
    {
        $response = [
            'message' => Str::random(60)
        ];
        return response()->json($response, 200);
    }
}
