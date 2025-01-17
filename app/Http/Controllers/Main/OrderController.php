<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            $userId = auth()->user()->id;
            $orders = Order::with('cartItems')->where('user_id', $userId)->get();
        } else {
            $orders = Order::with('cartItems')->get();
        }

        return response()->json([
            'message' => 'Get data success',
            'data' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        if (auth()->user()->role !== 'pasien') {
            return response()->json(['message' => 'Unauthorized']);
        }
        $userId = auth()->user()->id;

        $total_price = 0;

        foreach ($request['order_ids'] as $cartItemId) {
            $cartItem = CartItem::where('id', $cartItemId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $product = Product::findOrFail($cartItem->product_id);
            $product->stock -= $cartItem->qty;
            $product->save();

            $otherCartItems = CartItem::where('product_id', $product->id)
                ->where('user_id', '!=', $userId)
                ->get();

            foreach ($otherCartItems as $otherCartItem) {
                if ($product->stock < $otherCartItem->qty) {
                    $otherCartItem->delete();
                }
            }

            $cartItem->status = 'inactive';
            $cartItem->save();
            $total_price += $cartItem->price;
        }

        $order = Order::create([
            'user_id' => $userId,
            'status' => 'pending',
            'total_price' => $total_price,
            'messages' => 'Wait for the pharmacy',
        ]);

        if (isset($request['order_ids'])) {
            $order->cartItems()->attach($request['order_ids']);
        }

        return response()->json([
            'message' => 'Store data success',
            'data' => $order->with('cartItems')->get()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json([
            'data' => $order->with('products')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        if (auth()->user()->role !== 'pasien') {
            return response()->json(['message' => 'Unauthorized']);
        }
        if ($order->status !== 'finished' || $order->status !== 'cancelled') {
            return response()->json([
                'message' => "You can't delete",
            ]);
        }

        foreach ($order->cartItems as $carts) {
            $carts->delete();
        }
        $order->delete();

        return response()->json([
            'message' => 'Delete data success',
        ]);
    }

    public function multiDestroy(Request $request)
    {

        if (auth()->user()->role !== 'pasien') {
            return response()->json(['message' => 'Unauthorized']);
        }
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:categories,id',
        ]);

        $orders = Order::whereIn('id', $validated['ids'])->get();
        $invalidOrders = $orders->filter(function ($order) {
            return $order->status !== 'finished' && $order->status !== 'cancelled';
        });

        if ($invalidOrders->isNotEmpty()) {
            return response()->json([
                'message' => "You can't delete",
            ]);
        }

        foreach ($validated['ids'] as $id_order) {
            $order = Order::find($id_order);
            if ($order) {
                foreach ($order->cartItems as $carts) {
                    $carts->delete();
                }
                $order->delete();
            }
        }
        return response()->json([
            'message' => 'Delete multi data success',
        ]);
    }

    public function change_status(Request $request, Order $order)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }
        if ($order->status === 'canceled') {
            return response()->json([
                'message' => 'failed',
            ]);
        }

        switch ($request['status_option']) {
            case 0:
                $order->update([
                    'status' => 'approved',
                    'messages' => 'Medicine is being prepared',
                ]);
                break;
            case 1:
                $order->update([
                    'status' => 'finished',
                    'messages' => 'Your order has been prepared, please pick it up',
                ]);
                break;
        }

        return response()->json([
            'message' => 'success',
            'data' => $order
        ]);
    }

    public function cancelled(Order $order)
    {
        if (auth()->user()->role !== 'pasien') {
            return response()->json(['message' => 'Unauthorized']);
        }
        if ($order->status === 'approved' || $order->status === 'pending') {

            $order->update([
                'status' => 'cancelled',
                'messages' => 'You cancelled your order'
            ]);
            if ($order->cartItems) {
                foreach ($order->cartItems as $cartItem) {
                    $product = Product::findOrFail($cartItem->product_id);
                    $product->stock += $cartItem->qty; // Jika qty ada di pivot
                    $product->save();
                }
            }
            return response()->json([
                'message' => 'success',
                'data' => $order
            ]);
        } else {
            return response()->json([
                'message' => 'failed',
            ]);
        }
    }
}
