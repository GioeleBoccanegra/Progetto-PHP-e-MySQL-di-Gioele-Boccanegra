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

    public function co2saved(Request $request)
    {


        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $paeseDestinazione = $request->query('paese_destinazione');
        $productId = $request->query('product_id');

        //Inizializza una query sulla tabella orders_products,Unisci la tabella orders_products con la tabella orders in base all'ID dell'ordine con join e combina la tabella orders_products con la tabella products in base all'ID del prodotto.
        $query = OrdersProducts::query()
            ->join('orders', 'orders.id', '=', 'orders_products.order_id')
            ->join('products', 'products.id', '=', 'orders_products.product_id');

        if ($startDate) {
            $query->where('orders.data_vendita', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('orders.data_vendita', '<=', $endDate);
        }

        if ($paeseDestinazione) {

            $query->where('orders.paese_destinazione', '=', $paeseDestinazione);
        }

        if ($productId) {
            $query->where('orders_products.product_id', '=', $productId);
        }

        try {
            $totalCo2Saved = $query->get()->sum(function ($orderProduct) {
                $product = $orderProduct->product;
                if ($product) {
                    return $product->co2_risparmiata * $orderProduct->quantita;
                }

                return 0; // Se il prodotto è null, ritorna 0
            });


            return response()->json([
                'total_co2_saved' => $totalCo2Saved
            ]);
        } catch (\Exception $e) {
            // Cattura e restituisce l'errore se qualcosa va storto
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
