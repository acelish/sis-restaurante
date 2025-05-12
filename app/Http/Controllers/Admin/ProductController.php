<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        $categories = Category::orderBy('name')->get();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        
        return view('admin.products.create', compact('categories', 'inventoryItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'required|boolean',
            'track_inventory' => 'required|boolean',
            'stock' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:1024', // máximo 1MB
            'ingredients' => 'nullable|array',
            'ingredients.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0.001',
        ]);
        
        // Si no se controla el inventario, el stock no es relevante
        if (!$validated['track_inventory']) {
            $validated['stock'] = 0;
        }
        
        // Manejar la carga de imagen
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        // Crear el producto
        $product = Product::create($validated);
        
        // Sincronizar ingredientes si se proporcionaron
        if ($request->has('ingredients')) {
            $ingredients = collect($request->ingredients)
                ->filter(function ($ingredient) {
                    return !empty($ingredient['inventory_item_id']) && $ingredient['quantity'] > 0;
                })
                ->mapWithKeys(function ($ingredient) {
                    return [$ingredient['inventory_item_id'] => ['quantity' => $ingredient['quantity']]];
                })
                ->all();
            
            $product->inventoryItems()->sync($ingredients);
        }
        
        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'inventoryItems']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $inventoryItems = InventoryItem::orderBy('name')->get();
        $product->load('inventoryItems');
        
        return view('admin.products.edit', compact('product', 'categories', 'inventoryItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'required|boolean',
            'track_inventory' => 'required|boolean',
            'stock' => 'nullable|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:1024', // máximo 1MB
            'delete_image' => 'nullable|boolean',
            'ingredients' => 'nullable|array',
            'ingredients.*.inventory_item_id' => 'nullable|exists:inventory_items,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0.001',
        ]);
        
        // Si no se controla el inventario, el stock no es relevante
        if (!$validated['track_inventory']) {
            $validated['stock'] = 0;
        }
        
        // Manejar la carga de imagen
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->has('delete_image') && $request->delete_image) {
            // Eliminar la imagen si se marcó el checkbox
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = null;
        } else {
            // Mantener la imagen actual
            unset($validated['image']);
        }
        
        // Actualizar el producto
        $product->update($validated);
        
        // Sincronizar ingredientes si se proporcionaron
        if ($request->has('ingredients')) {
            $ingredients = collect($request->ingredients)
                ->filter(function ($ingredient) {
                    return !empty($ingredient['inventory_item_id']) && $ingredient['quantity'] > 0;
                })
                ->mapWithKeys(function ($ingredient) {
                    return [$ingredient['inventory_item_id'] => ['quantity' => $ingredient['quantity']]];
                })
                ->all();
            
            $product->inventoryItems()->sync($ingredients);
        } else {
            // Si no se proporcionaron ingredientes, eliminar todos
            $product->inventoryItems()->detach();
        }
        
        return redirect()->route('products.show', $product)
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Eliminar imagen si existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        // Eliminar el producto
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
