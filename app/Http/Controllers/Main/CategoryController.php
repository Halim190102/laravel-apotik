<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        protected ProductController $productController,
    ) {}
    public function index()
    {
        $categories = Category::with('products')->get();

        return response()->json([
            'message' => 'Get data success',
            'data' => $categories
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
    public function store(CategoryRequest $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }

        $category = Category::create([
            'name' => $request['name'],
        ]);

        return response()->json([
            'message' => 'Store data success',
            'data' => $category,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'message' => 'Get spesific data success',
            'data' => $category->with('products')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }

        $category->update([
            'name' => $request['name'] ?? $category->name,

        ]);

        return response()->json([
            'message' => 'Update data success',
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized']);
        }

        foreach ($category->products as $productData) {
            $this->productController->deleteProduct($productData);
        }

        $category->delete();

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
            'ids.*' => 'integer|exists:categories,id',
        ]);

        foreach ($validated['ids'] as $id_category) {
            $category = Category::find($id_category);
            if ($category) {
                foreach ($category->products as $productData) {
                    $this->productController->deleteProduct($productData);
                }
                $category->delete();
            }
        }
        return response()->json([
            'message' => 'Delete multi data success',
        ]);
    }
}
