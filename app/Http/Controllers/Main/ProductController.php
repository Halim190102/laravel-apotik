<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\FileService;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(protected FileService $fileService) {}

    public function index()
    {
        $products = Product::with('categories')->get();

        return response()->json([
            'message' => 'Get data success',
            'data' => $products
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
    public function store(ProductRequest $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }
        $category = Category::where('id', $request['category_id'])->first();
        $picture = $request['picture'] !== null ? $this->fileService->postFile($request['picture'], $category->name) : env('DRUGS_DEFAULT');

        $product = Product::create([
            'category_id' => $request['category_id'],
            'name' => $request['name'],
            'description' => $request['description'],
            'price' => $request['price'],
            'stock' => $request['stock'],
            'picture' => $picture
        ]);

        return response()->json([
            'message' => 'Store data success',
            'data' => $product,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'message' => 'Get spesific data success',
            'data' => $product->with('categories')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }

        if (isset($request['picture'])) {
            $category = Category::where('id', $request['category_id'])->first();
            $picture = $this->fileService->updateFile($request['picture'], $category->name, $product->picture);
        } else {
            $picture = $product->picture;
        }
        $product->update([
            'category_id' => $request['category_id'] ?? $product->category_id,
            'name' => $request['name'] ?? $product->name,
            'description' => $request['description'] ?? $product->description,
            'price' => $request['price'] ?? $product->price,
            'stock' => $request['stock'] ?? $product->stock,
            'picture' => $picture,
        ]);

        return response()->json([
            'message' => 'Update data success',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }

        $this->deleteProduct($product);

        $product->delete();

        return response()->json([
            'message' => 'Delete data success',
        ]);
    }


    public function multiDestroy(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:products,id',
        ]);
        foreach ($validated['ids'] as $id_product) {
            $product = Product::find($id_product);
            $this->deleteProduct($product);
            $product->delete();
        }
        return response()->json([
            'message' => 'Delete multi data success',
        ]);
    }

    public function deleteProduct(Product $product)
    {
        $product->picture !== env('DRUGS_DEFAULT') ? File::delete($product->picture) : null;
    }
}
