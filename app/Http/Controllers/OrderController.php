<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrdersProducts;
use Illuminate\Validation\ValidationException;
use App\Models\Product;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'paese_destinazione' => 'required|string|max:100',
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
                'paese_destinazione' => $request->paese_destinazione,
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
                    'paese_destinazione' => 'required|string|max:100',
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
                $order->update($request->only(['paese_destinazione', 'data_vendita']));

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

    public function destroy($id)
    {
        $order = Order::find($id);
        if ($order) {
            try {
                $order->delete();

                return response()->json(['message' => 'ordine eliminato con successo'], 200);
            } catch (\Exception $e) {
                // Cattura e restituisce l'errore se qualcosa va storto
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'ordine non trovato'], 404);
        }
    }

    public function co2saved()
    {

        try {
            $orderProducts = OrdersProducts::all();
            $totalco2 = 0;

            foreach ($orderProducts as $orderProducts) {
                $product = Product::find($orderProducts->product_id);

                if ($product) {
                    $totalco2 += $product->co2_risparmiata * $orderProducts->quantita;
                }
            }

            return response()->json(['message' => "in totale è stata rispartmiata $totalco2 co2"]);
        } catch (\Exception $e) {
            // Cattura e restituisce l'errore se qualcosa va storto
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
