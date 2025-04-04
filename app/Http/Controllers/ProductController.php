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

    public function update(Request $request, $id)
    {
        $post = Product::find($id);

        if ($post) {
            $request->validate([
                'nome' => 'required|string|max:225',
                'co2_risparmiata' => 'required|numeric|min:0',
            ]);

            $post->update($request->only(['nome', 'co2_risparmiata']));
            return response()->json($post, 200);
        };

        return response()->json(['message' => 'Prodotto non trovato'], 404);
    }

    public function destroy($id)
    {
        $post = Product::find($id);
        if ($post) {
            $post->delete();
            return response()->json(['message' => 'prodotto eliminato con successo'], 200);
        } else {
            return response()->json(['message' => 'prodotto non trovato'], 404);
        }
    }
}
