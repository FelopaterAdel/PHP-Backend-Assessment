<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\PaginateProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        
        // Get cache version to invalidate all product cache at once
        $cacheVersion = Cache::get('products:cache_version', 0);
        
        // Generate cache key based on version, page and per_page parameters
        $cacheKey = "products:v{$cacheVersion}:page:{$page}:per_page:{$perPage}";
        
        // Try to get from cache, if not found execute the callback
        $products = Cache::remember($cacheKey, 3600, function () use ($perPage) {
            return Product::paginate($perPage);
        });
        
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $product = Product::create($validated);
        
        // Invalidate products cache when new product is created
        $this->clearProductsCache();
        
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validated();
        $product->update($validated);
        
        // Invalidate products cache when product is updated
        $this->clearProductsCache();
        
        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        
        // Invalidate products cache when product is deleted
        $this->clearProductsCache();
        
        return response()->json(['message' => 'Product deleted successfully'], 204);
    }

    /**
     * Display products with low stock.
     */
    public function lowStock(): JsonResponse
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->get();
        return response()->json([
        'data' => $products
         ]);
    }

    /**
     * Adjust stock quantity for a product.
     */
    public function adjustStock(AdjustStockRequest $request, string $id): JsonResponse
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validated = $request->validated();
        $product->stock_quantity += $validated['quantity'];
        $product->save();
        
        // Invalidate products cache when stock is adjusted
        $this->clearProductsCache();

        return response()->json($product);
    }

    /**
     * Clear all products listing cache entries.
     */
    private function clearProductsCache(): void
    {
        $currentVersion = Cache::get('products:cache_version', 0);
        Cache::forever('products:cache_version', $currentVersion + 1);
    }
}