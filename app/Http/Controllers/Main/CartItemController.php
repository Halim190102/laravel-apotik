<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        if (auth()->user()->role === 'admin') {
            $cartItem = CartItem::with('products')->get();
        } else {
            $cartItem = CartItem::with('products')->where('user_id', $userId)->get();
        }

        return response()->json([
            'message' => 'Get data success',
            'data' => $cartItem
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
    public function store(CartItemRequest $request)
    {
        $product = Product::findOrFail($request['product_id']);

        $userId = auth()->user()->id;

        $cartItem = CartItem::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->qty += $request['qty'];
            $cartItem->price += ($request['qty'] * $product->price);
            $cartItem->save();
        } else {
            $price = $request['qty'] * $product->price;
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'product_id' => $request['product_id'],
                'qty' => $request['qty'],
                'price' => $price,
                'status' => 'active',
            ]);
        }

        return response()->json([
            'message' => 'Store data success',
            'data' => $cartItem,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CartItem $cartItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CartItemRequest $request, CartItem $cartItem)
    {

        $cartItem->qty = $request['qty'];
        $cartItem->price = $request['qty'] * $cartItem->products->price;
        $cartItem->save();

        return response()->json([
            'message' => 'Update data success',
            'data' => $cartItem,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

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
            'ids.*' => 'integer|exists:cart_items,id',
        ]);

        foreach ($validated['ids'] as $id_cart_item) {
            $cart_item = CartItem::find($id_cart_item);
            if ($cart_item) {
                $cart_item->delete();
            }
        }
        return response()->json([
            'message' => 'Delete multi data success',
        ]);
    }
}
