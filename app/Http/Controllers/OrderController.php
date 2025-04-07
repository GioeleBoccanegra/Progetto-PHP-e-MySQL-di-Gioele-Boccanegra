<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersProducts;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'stato' => 'required|string|max:100',
                'data_vendita' => 'required|date',
                'prodotti' => 'required|array',
                'prodotti.*.product_id' => 'required|exists:products,id',
                'prodotti.*.quantita' => 'required|numeric|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $order = Order::create([
                'data_vendita' => $request->data_vendita,
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

    public function update(Request $request, $id)
    {

        $order = Order::find($id);

        if ($order) {
            try {
                $request->validate([
                    'stato' => 'required|string|max:100',
                    'data_vendita' => 'required|date',
                    'prodotti' => 'required|array',
                    'prodotti.*.product_id' => 'required|exists:products,id',
                    'prodotti.*.quantita' => 'required|numeric|min:1',
                ]);
            } catch (ValidationException $e) {
                return response()->json([
                    'errors' => $e->errors()
                ], 422);
            }

            try {
                $order->update($request->only(['stato', 'data_vendita']));

                // Rimuovi le associazioni precedenti
                OrdersProducts::where('order_id', $order->id)->delete();

                // Inserisci le nuove associazioni

                foreach ($request->prodotti as $prodotto) {
                    OrdersProducts::create([
                        'order_id' => $order->id,
                        'product_id' => $prodotto['product_id'],
                        'quantita' => $prodotto['quantita']
                    ]);
                }

                return response()->json(['message' => 'ordine aggiornato con successo'], 200);
            } catch (\Exception $e) {
                // Cattura e restituisce l'errore se qualcosa va storto
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return response()->json(['message' => 'Ordine non trovato'], 404);
    }
}
