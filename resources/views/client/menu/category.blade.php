<!-- filepath: resources\views\client\menu\category.blade.php -->
@extends('layouts.client')

@section('title', $category->name . ' - Menú del Restaurante')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('menu') }}" class="inline-flex items-center text-red-600 hover:text-red-800">
            <i class="fas fa-arrow-left mr-2"></i> Volver al menú completo
        </a>
    </div>

    <div class="text-center mb-10">
        <div class="w-24 h-24 mx-auto bg-white rounded-full shadow-sm overflow-hidden border-2 border-red-100 mb-4">
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" 
                    alt="{{ $category->name }}" 
                    class="w-full h-full object-cover"
                    loading="lazy">
            @else
                <div class="w-full h-full bg-red-50 flex items-center justify-center">
                    <i class="fas fa-utensils text-2xl text-red-400"></i>
                </div>
            @endif
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-lg text-gray-600 mt-2">{{ $category->description }}</p>
        @endif
    </div>

    <!-- Filtro y Ordenamiento -->
    <div class="mb-8 bg-white p-4 rounded-lg shadow-sm">
        <div class="flex flex-wrap justify-between items-center">
            <div class="mb-2 sm:mb-0">
                <span class="text-gray-700 mr-2">{{ $products->count() }} productos encontrados</span>
            </div>
            <div class="flex items-center">
                <span class="text-gray-700 mr-2">Ordenar por:</span>
                <select id="sortOrder" class="border-gray-300 rounded-md text-sm focus:ring-red-500 focus:border-red-500">
                    <option value="name_asc">Nombre (A-Z)</option>
                    <option value="name_desc">Nombre (Z-A)</option>
                    <option value="price_asc">Precio (menor a mayor)</option>
                    <option value="price_desc">Precio (mayor a menor)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Listado de Productos -->
    @if($products->count() > 0)
        <div id="productsContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="product-card bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-all"
                     data-name="{{ $product->name }}" 
                     data-price="{{ $product->price }}">
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                                loading="lazy">
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-utensils text-2xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Badge de precio -->
                        <div class="absolute top-0 right-0 bg-red-600 text-white font-bold px-3 py-1 m-2 rounded-full shadow-md">
                            ${{ number_format($product->price, 2) }}
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $product->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $product->description }}</p>
                        
                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                            <div class="text-sm">
                                @if($product->stock > 0 || !$product->track_inventory)
                                    <span class="text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i> Disponible
                                    </span>
                                @else
                                    <span class="text-red-600">
                                        <i class="fas fa-times-circle mr-1"></i> No disponible
                                    </span>
                                @endif
                            </div>
                            
                            <form action="{{ route('cart.add') }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                
                                <div class="relative">
                                    <select name="quantity" class="text-sm appearance-none bg-white border border-gray-300 rounded-md pl-2 pr-8 py-1">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow-sm transition duration-300"
                                        {{ (!$product->is_available || ($product->track_inventory && $product->stock <= 0)) ? 'disabled' : '' }}>
                                    <i class="fas fa-cart-plus mr-2"></i> Añadir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="text-gray-500 mb-4">
                <i class="fas fa-search text-5xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No hay productos disponibles</h3>
            <p class="mt-1 text-sm text-gray-500">En este momento no hay productos disponibles en esta categoría.</p>
            <div class="mt-6">
                <a href="{{ route('menu') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                    Ver otras categorías
                </a>
            </div>
        </div>
    @endif
    
    <!-- Productos Relacionados -->
    @if($products->count() > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">También te puede interesar</h2>
            
            <div class="overflow-x-auto no-scrollbar">
                <div class="flex space-x-4 pb-4 min-w-max">
                    @foreach(\App\Models\Product::where('is_available', true)
                        ->where('category_id', '!=', $category->id)
                        ->inRandomOrder()
                        ->take(6)
                        ->get() as $relatedProduct)
                        <div class="w-56 flex-shrink-0 bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
                            <div class="h-36 overflow-hidden bg-gray-100">
                                @if($relatedProduct->image)
                                    <img src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                        alt="{{ $relatedProduct->name }}" 
                                        class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                                        loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                        <i class="fas fa-utensils text-xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-3">
                                <p class="text-xs text-red-600 font-medium">{{ $relatedProduct->category->name }}</p>
                                <h3 class="font-bold text-sm text-gray-800 mt-1 mb-1">{{ $relatedProduct->name }}</h3>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-sm">${{ number_format($relatedProduct->price, 2) }}</span>
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortSelect = document.getElementById('sortOrder');
        const productsContainer = document.getElementById('productsContainer');
        const productCards = document.querySelectorAll('.product-card');
        
        // Función para ordenar productos
        function sortProducts() {
            const sortValue = sortSelect.value;
            const productsArray = Array.from(productCards);
            
            productsArray.sort((a, b) => {
                const aName = a.getAttribute('data-name');
                const bName = b.getAttribute('data-name');
                const aPrice = parseFloat(a.getAttribute('data-price'));
                const bPrice = parseFloat(b.getAttribute('data-price'));
                
                if (sortValue === 'name_asc') {
                    return aName.localeCompare(bName);
                } else if (sortValue === 'name_desc') {
                    return bName.localeCompare(aName);
                } else if (sortValue === 'price_asc') {
                    return aPrice - bPrice;
                } else if (sortValue === 'price_desc') {
                    return bPrice - aPrice;
                }
            });
            
            // Eliminar productos existentes
            productsContainer.innerHTML = '';
            
            // Añadir productos ordenados
            productsArray.forEach(product => {
                productsContainer.appendChild(product);
            });
        }
        
        // Escuchar cambios en el selector de ordenamiento
        sortSelect.addEventListener('change', sortProducts);
    });
</script>

<style>
    /* Eliminar scrollbar pero mantener funcionalidad */
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    /* Estilos para los botones deshabilitados */
    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection