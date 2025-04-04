<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:225',
            'co2_risparmiata' => 'required|numeric|min:0',
        ]);

        $post = Product::create([
            'nome' => $request->nome,
            'co2_risparmiata' => $request->co2_risparmiata
        ]);

        return response()->json($post, 201);
    }
}
