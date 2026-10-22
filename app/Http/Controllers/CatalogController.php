<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display a listing of products with optional category filtering.
     * Price column is selected only for approved accounts; strictly omitted for guests and unapproved users.
     */
    public function index(Request $request): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        $selectedCategorySlug = $request->query('category');

        $canSeePrices = $this->canSeePrices();

        // Select columns: price is strictly omitted from the SQL query unless authorized
        $columns = ['id', 'category_id', 'name', 'slug', 'description', 'image', 'stock'];
        if ($canSeePrices) {
            $columns[] = 'price';
        }

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
            'canSeePrices' => $canSeePrices,
        ]);
    }

    /**
     * Display the specified product.
     * Price column is selected only for approved accounts; strictly omitted for guests and unapproved users.
     */
    public function show(string $slug): View
    {
        $canSeePrices = $this->canSeePrices();

        $columns = ['id', 'category_id', 'name', 'slug', 'description', 'image', 'stock'];
        if ($canSeePrices) {
            $columns[] = 'price';
        }

        $product = Product::select($columns)
            ->with('category:id,name,slug')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('catalog.show', [
            'product' => $product,
            'canSeePrices' => $canSeePrices,
        ]);
    }

    /**
     * Determine whether the current requester is authorized to view wholesale prices.
     * Enforces fresh status checks directly from the database on every request.
     */
    protected function canSeePrices(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        $user = Auth::user();
        if ($user && $user->exists) {
            $user = $user->fresh() ?? $user;
        }

        return $user ? $user->canSeePrices() : false;
    }
}
