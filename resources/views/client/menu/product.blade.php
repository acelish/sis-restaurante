<!-- filepath: resources\views\client\menu\product.blade.php -->
@extends('layouts.client')

@section('title', $product->name . ' - Restaurante')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center text-red-600 hover:text-red-800">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="md:flex">
            <!-- Imagen del producto -->
            <div class="md:w-1/2">
                <div class="relative h-72 md:h-full overflow-hidden bg-gray-100">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" 
                            alt="{{ $product->name }}" 
                            class="w-full h-full object-cover"
                            loading="lazy">
                    @else
                        <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                            <i class="fas fa-utensils text-4xl text-gray-400"></i>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Detalles del producto -->
            <div class="md:w-1/2 p-6">
                <div class="mb-2">
                    <span class="inline-block bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full mb-2">
                        {{ $product->category->name }}
                    </span>
                    @if($product->stock > 0 || !$product->track_inventory)
                        <span class="inline-block bg-green-100 text-green-600 text-xs font-medium px-2 py-1 rounded-full ml-2">
                            Disponible
                        </span>
                    @else
                        <span class="inline-block bg-red-100 text-red-600 text-xs font-medium px-2 py-1 rounded-full ml-2">
                            No disponible
                        </span>
                    @endif
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>
                
                <div class="mb-6">
                    <div class="text-2xl font-bold text-red-600">${{ number_format($product->price, 2) }}</div>
                </div>
                
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-2">Descripción</h2>
                    <p class="text-gray-700">{{ $product->description }}</p>
                </div>
                
                @if($product->stock > 0 || !$product->track_inventory)
                    <form action="{{ route('cart.add') }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                <div class="flex items-center">
                                    <button type="button" id="decreaseQuantity" class="bg-gray-200 px-3 py-2 rounded-l-md">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="quantity" name="quantity" min="1" max="20" value="1" 
                                        class="border-gray-300 w-16 text-center py-2">
                                    <button type="button" id="increaseQuantity" class="bg-gray-200 px-3 py-2 rounded-r-md">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex-1">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-md shadow-sm transition duration-300 flex items-center justify-center">
                                    <i class="fas fa-cart-plus mr-2"></i> Añadir al carrito
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">Lo sentimos, este producto no está disponible actualmente.</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Información adicional -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                        <span>Preparado al momento</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-truck mr-2 text-green-500"></i>
                        <span>Disponible para delivery y take-away</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Productos Similares -->
    @if($similarProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">También te puede gustar</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($similarProducts as $similarProduct)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition-all">
                        <a href="{{ route('menu.product', $similarProduct) }}" class="block">
                            <div class="h-48 overflow-hidden bg-gray-100">
                                @if($similarProduct->image)
                                    <img src="{{ asset('storage/' . $similarProduct->image) }}" 
                                        alt="{{ $similarProduct->name }}" 
                                        class="w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                                        loading="lazy">
                                @else
                                    <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                        <i class="fas fa-utensils text-2xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                        
                        <div class="p-4">
                            <a href="{{ route('menu.product', $similarProduct) }}" class="block">
                                <h3 class="font-bold text-lg text-gray-800 mb-2 hover:text-red-600 transition-colors">
                                    {{ $similarProduct->name }}
                                </h3>
                            </a>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-red-600">${{ number_format($similarProduct->price, 2) }}</span>
                                
                                <form action="{{ route('cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $similarProduct->id }}">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow-sm transition duration-300 text-sm">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decreaseQuantity');
        const increaseBtn = document.getElementById('increaseQuantity');
        
        // Función para manejar el clic en el botón de disminuir
        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        // Función para manejar el clic en el botón de aumentar
        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < 20) {
                quantityInput.value = currentValue + 1;
            }
        });
        
        // Validar entrada manual
        quantityInput.addEventListener('change', function() {
            let currentValue = parseInt(quantityInput.value);
            if (isNaN(currentValue) || currentValue < 1) {
                quantityInput.value = 1;
            } else if (currentValue > 20) {
                quantityInput.value = 20;
            }
        });
    });
</script>
@endsection