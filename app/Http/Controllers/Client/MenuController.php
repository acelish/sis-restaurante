<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class MenuController extends Controller
{
    /**
     * Muestra la página principal con categorías destacadas y productos
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener todas las categorías
        $categories = Category::orderBy('order', 'asc')->get();
        
        // Obtener productos destacados (aleatoriamente para variedad)
        $featuredProducts = Product::where('is_available', true)
                            ->inRandomOrder()
                            ->take(12)
                            ->get();
        
        // Organizar productos destacados por categoría
        $categoriesWithFeatured = Category::whereHas('products', function($query) {
            $query->where('is_available', true);
        })->get();
        
        $featuredProductsByCategory = [];
        
        foreach ($categoriesWithFeatured as $category) {
            $featuredProductsByCategory[$category->id] = Product::where('category_id', $category->id)
                ->where('is_available', true)
                ->inRandomOrder()
                ->take(8)
                ->get();
        }
        
        return view('client.menu.index', compact(
            'categories', 
            'featuredProducts', 
            'categoriesWithFeatured',
            'featuredProductsByCategory'
        ));
    }

    /**
     * Muestra todo el menú
     *
     * @return \Illuminate\View\View
     */
    public function all()
    {
        $categories = Category::with(['products' => function($query) {
            $query->where('is_available', true);
        }])->orderBy('order', 'asc')->get();
        
        return view('client.menu.all', compact('categories'));
    }

    /**
     * Muestra productos de una categoría específica
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function category(Category $category)
    {
        $products = $category->products()
                    ->where('is_available', true)
                    ->orderBy('name')
                    ->get();
        
        // Obtener categorías relacionadas para mostrar en la navegación
        $relatedCategories = Category::where('id', '!=', $category->id)
                            ->orderBy('order')
                            ->take(5)
                            ->get();
        
        return view('client.menu.category', compact('category', 'products', 'relatedCategories'));
    }
    
    /**
     * Muestra detalles de un producto específico
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function product(Product $product)
    {
        // Obtener productos similares de la misma categoría
        $similarProducts = Product::where('category_id', $product->category_id)
                          ->where('id', '!=', $product->id)
                          ->where('is_available', true)
                          ->inRandomOrder()
                          ->take(4)
                          ->get();
        
        return view('client.menu.product', compact('product', 'similarProducts'));
    }
}
