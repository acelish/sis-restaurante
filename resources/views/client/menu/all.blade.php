<!-- filepath: resources\views\client\menu\all.blade.php -->
@extends('layouts.client')

@section('title', 'Menú Completo - Restaurante')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Nuestro Menú Completo</h1>
        <p class="text-lg text-gray-600 mt-2">Disfruta de nuestra variedad de platos y bebidas</p>
    </div>

    <!-- Filtro/Navegación Rápida -->
    <div class="mb-8 overflow-x-auto no-scrollbar">
        <div class="flex space-x-2 min-w-max pb-2">
            <a href="#all-categories" class="bg-red-600 text-white text-sm py-1 px-3 rounded-full">
                Todas las categorías
            </a>
            @foreach($categories as $category)
                <a href="#category-{{ $category->id }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-1 px-3 rounded-full">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Categorías -->
    <div id="all-categories" class="mb-10">
        <div class="max-w-7xl mx-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($categories as $category)
                <a href="#category-{{ $category->id }}" 
                   class="group bg-white rounded-lg shadow-sm hover:shadow-md overflow-hidden transition-all duration-300 flex flex-col items-center p-4">
                    <div class="w-16 h-16 mb-4 overflow-hidden rounded-full border-2 border-red-100 group-hover:border-red-300 transition-all">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" 
                                alt="{{ $category->name }}" 
                                class="w-full h-full object-cover"
                                loading="lazy">
                        @else
                            <div class="w-full h-full bg-red-50 flex items-center justify-center">
                                <i class="fas fa-utensils text-red-400"></i>
                            </div>
                        @endif
                    </div>
                    <h3 class="font-semibold text-center">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $category->products->count() }} productos</p>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Productos por Categoría -->
    @foreach($categories as $category)
        <div id="category-{{ $category->id }}" class="mb-14 scroll-mt-16">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" 
                            alt="{{ $category->name }}" 
                            class="w-8 h-8 object-cover rounded-full"
                            loading="lazy">
                    @else
                        <i class="fas fa-utensils text-red-500"></i>
                    @endif
                </div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
            </div>
            
            @if($category->description)
                <p class="text-gray-600 mb-6 ml-16">{{ $category->description }}</p>
            @endif
            
            @if($category->products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($category->products as $product)
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-all">
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
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
                                
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
                                    
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
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
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <p class="text-gray-500">No hay productos disponibles en esta categoría.</p>
                </div>
            @endif
            
            <div class="text-right mt-4 mb-8">
                <a href="#all-categories" class="inline-flex items-center text-red-600 hover:text-red-800">
                    <i class="fas fa-arrow-up mr-1"></i> Volver arriba
                </a>
            </div>
            <hr class="border-gray-200 my-8">
        </div>
    @endforeach
    
    <!-- Información de contacto y horario -->
    <div class="mt-16 mb-8 bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Información adicional</h3>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-red-100 p-3 rounded-full mr-4">
                    <i class="fas fa-clock text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Horario</h4>
                    <p class="text-sm text-gray-600">Lunes a Domingo<br>12:00 - 22:00</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-red-100 p-3 rounded-full mr-4">
                    <i class="fas fa-map-marker-alt text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Ubicación</h4>
                    <p class="text-sm text-gray-600">Av. Principal #123<br>Ciudad</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <div class="flex-shrink-0 bg-red-100 p-3 rounded-full mr-4">
                    <i class="fas fa-phone text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Contacto</h4>
                    <p class="text-sm text-gray-600">(123) 456-7890<br>info@restaurante.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Scroll suave */
    html {
        scroll-behavior: smooth;
    }
    
    /* Eliminar scrollbar pero mantener funcionalidad */
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    
    /* Line clamp para descripción */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection