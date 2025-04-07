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
        try {
            $product = Product::create([
                'nome' => $request->nome,
                'co2_risparmiata' => $request->co2_risparmiata
            ]);
        } catch (\Exception $e) {
            // Cattura e restituisce l'errore se qualcosa va storto
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if ($product) {
            try {
                $request->validate([
                    'nome' => 'required|string|max:225',
                    'co2_risparmiata' => 'required|numeric|min:0',
                ]);

                $product->update($request->only(['nome', 'co2_risparmiata']));
                return response()->json($product, 200);
            } catch (\Exception $e) {
                // Cattura e restituisce l'errore se qualcosa va storto
                return response()->json(['error' => $e->getMessage()], 500);
            }
        };

        return response()->json(['message' => 'Prodotto non trovato'], 404);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            try {
                $product->delete();
                return response()->json(['message' => 'prodotto eliminato con successo'], 200);
            } catch (\Exception $e) {
                // Cattura e restituisce l'errore se qualcosa va storto
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'prodotto non trovato'], 404);
        }
    }
}
