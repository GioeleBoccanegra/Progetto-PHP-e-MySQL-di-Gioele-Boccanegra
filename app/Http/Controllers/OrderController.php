<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersProducts;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'stato' => 'required|string|max:100',
            'prodotti' => 'required|array',
            'prodotti.*.product_id' => 'required|exists:products,id',
            'prodotti.*.quantita' => 'required|numeric|min:1',
        ]);

        try {
            $order = Order::create([
                'data_vendita' => now()->toDateString(),
                'stato' => $request->stato,
            ]);

            foreach ($request->prodotti as $prodotto) {
                OrdersProducts::create([
                    'order_id' => $order->id,
                    'product_id' => $prodotto['product_id'],
                    'quantita' => $prodotto['quantita']
                ]);
            }


            return response()->json($order, 201);
        } catch (\Exception $e) {
            // Cattura e restituisce l'errore se qualcosa va storto
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
