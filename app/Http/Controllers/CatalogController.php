<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display a listing of products with optional category filtering.
     * Price column is strictly excluded from SQL query for guest visitors.
     */
    public function index(Request $request): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        $selectedCategorySlug = $request->query('category');

        // Select columns: price column is strictly excluded so it never reaches the view
        $columns = ['id', 'category_id', 'name', 'slug', 'description', 'image', 'stock'];

        $query = Product::select($columns)->with('category:id,name,slug');

        if ($selectedCategorySlug) {
            $query->whereHas('category', function ($q) use ($selectedCategorySlug) {
                $q->where('slug', $selectedCategorySlug);
            });
        }

        $products = $query->latest()->get();

        return view('catalog.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategorySlug,
        ]);
    }

    /**
     * Display the specified product.
     * Price column is strictly excluded from SQL query for guest visitors.
     */
    public function show(string $slug): View
    {
        $columns = ['id', 'category_id', 'name', 'slug', 'description', 'image', 'stock'];

        $product = Product::select($columns)
            ->with('category:id,name,slug')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('catalog.show', [
            'product' => $product,
        ]);
    }
}
