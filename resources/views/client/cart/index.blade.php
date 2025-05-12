<!-- filepath: resources\views\client\cart\index.blade.php -->
@extends('layouts.client')

@section('title', 'Tu Carrito - Restaurante')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Tu Carrito</h1>
        <a href="{{ route('menu') }}" class="text-red-600 hover:text-red-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Seguir Comprando
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if(count($products) > 0)
        <div class="lg:flex lg:space-x-8">
            <!-- Lista de productos en el carrito -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Productos en tu carrito ({{ count($products) }})</h2>
                    </div>
                    
                    <ul class="divide-y divide-gray-200">
                        @foreach($products as $product)
                            <li class="p-4 md:p-6 flex flex-col md:flex-row md:items-center">
                                <div class="flex flex-1 mb-4 md:mb-0">
                                    <!-- Imagen del producto -->
                                    <div class="w-20 h-20 flex-shrink-0 rounded-md overflow-hidden bg-gray-100 mr-4">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                alt="{{ $product->name }}" 
                                                class="w-full h-full object-cover"
                                                loading="lazy">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                <i class="fas fa-utensils text-gray-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Detalles del producto -->
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-800">{{ $product->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
                                        <p class="text-red-600 font-semibold mt-1">${{ number_format($product->price, 2) }}</p>
                                        
                                        @if($product->special_instructions)
                                            <div class="mt-2 text-sm text-gray-600">
                                                <span class="font-semibold">Instrucciones especiales:</span>
                                                <p class="italic">{{ $product->special_instructions }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Controles de cantidad y eliminación -->
                                <div class="flex items-center justify-between md:justify-end md:ml-6 space-x-2">
                                    <form action="{{ route('cart.update') }}" method="POST" class="flex items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="special_instructions" value="{{ $product->special_instructions }}">
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button type="button" 
                                                    class="px-2 py-1 text-gray-600 hover:bg-gray-100 decrease-quantity"
                                                    data-input="quantity-{{ $product->id }}">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" 
                                                   id="quantity-{{ $product->id }}"
                                                   name="quantity" 
                                                   min="1" 
                                                   max="20" 
                                                   value="{{ $product->quantity }}" 
                                                   class="w-12 text-center border-0 focus:ring-0">
                                            <button type="button" 
                                                    class="px-2 py-1 text-gray-600 hover:bg-gray-100 increase-quantity"
                                                    data-input="quantity-{{ $product->id }}">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                        <button type="submit" class="ml-2 text-sm text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                    
                                    <span class="hidden md:inline-block mx-4 font-medium">
                                        ${{ number_format($product->subtotal, 2) }}
                                    </span>
                                    
                                    <form action="{{ route('cart.remove', $product->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Subtotal (visible solo en móvil) -->
                                <div class="mt-2 text-right md:hidden">
                                    <span class="font-medium">Subtotal: ${{ number_format($product->subtotal, 2) }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="p-4 bg-gray-50 flex justify-between items-center">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-800 flex items-center text-sm">
                                <i class="fas fa-trash-alt mr-1"></i> Vaciar carrito
                            </button>
                        </form>
                        
                        <span class="font-medium">
                            {{ count($products) }} productos
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Resumen y checkout -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-4">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Resumen del Pedido</h2>
                    </div>
                    
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>${{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Impuestos (16%)</span>
                                <span>${{ number_format($total * 0.16, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-3 mt-3">
                                <span class="font-semibold">Total</span>
                                <span class="font-bold text-lg">${{ number_format($total * 1.16, 2) }}</span>
                            </div>
                        </div>
                        
                        <form action="{{ route('cart.checkout') }}" method="POST" class="mt-6 space-y-4">
                            @csrf
                            
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" id="customer_name" name="customer_name" 
                                       class="w-full rounded-md border-gray-300" 
                                       value="{{ old('customer_name', Auth::check() ? Auth::user()->name : '') }}" required>
                                @error('customer_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="text" id="customer_phone" name="customer_phone" 
                                       class="w-full rounded-md border-gray-300" 
                                       value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email (opcional)</label>
                                <input type="email" id="customer_email" name="customer_email" 
                                       class="w-full rounded-md border-gray-300"
                                       value="{{ old('customer_email', Auth::check() ? Auth::user()->email : '') }}">
                                @error('customer_email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de pedido</label>
                                <select id="order_type" name="order_type" class="w-full rounded-md border-gray-300">
                                    <option value="takeaway" {{ old('order_type') == 'takeaway' ? 'selected' : '' }}>Para llevar</option>
                                    <option value="dine_in" {{ old('order_type') == 'dine_in' ? 'selected' : '' }}>Comer en el local</option>
                                    <option value="delivery" {{ old('order_type') == 'delivery' ? 'selected' : '' }}>Entrega a domicilio</option>
                                </select>
                                @error('order_type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Campos condicionales -->
                            <div id="dine_in_fields" class="hidden">
                                <label for="table_id" class="block text-sm font-medium text-gray-700 mb-1">Mesa</label>
                                <select id="table_id" name="table_id" class="w-full rounded-md border-gray-300">
                                    <option value="">Seleccionar mesa</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            Mesa {{ $table->number }} ({{ $table->capacity }} personas)
                                        </option>
                                    @endforeach
                                </select>
                                @error('table_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div id="delivery_fields" class="hidden">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Dirección de entrega</label>
                                <textarea id="address" name="address" rows="3" 
                                          class="w-full rounded-md border-gray-300">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Método de pago</label>
                                <select id="payment_method" name="payment_method" class="w-full rounded-md border-gray-300">
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Tarjeta (pago en entrega)</option>
                                </select>
                                @error('payment_method')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas adicionales (opcional)</label>
                                <textarea id="notes" name="notes" rows="2" 
                                          class="w-full rounded-md border-gray-300">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-md transition-colors">
                                    Completar Pedido
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="text-gray-500 mb-4">
                <i class="fas fa-shopping-cart text-5xl"></i>
            </div>
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Tu carrito está vacío</h2>
            <p class="text-gray-600 mb-6">Añade deliciosos platos desde nuestro menú.</p>
            <a href="{{ route('menu') }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-md transition-colors">
                Ver Menú
            </a>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Control de cantidades
        const decreaseBtns = document.querySelectorAll('.decrease-quantity');
        const increaseBtns = document.querySelectorAll('.increase-quantity');
        
        decreaseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const inputId = this.getAttribute('data-input');
                const input = document.getElementById(inputId);
                const currentValue = parseInt(input.value);
                
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                }
            });
        });
        
        increaseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const inputId = this.getAttribute('data-input');
                const input = document.getElementById(inputId);
                const currentValue = parseInt(input.value);
                
                if (currentValue < 20) {
                    input.value = currentValue + 1;
                }
            });
        });
        
        // Campos condicionales según tipo de pedido
        const orderTypeSelect = document.getElementById('order_type');
        const dineInFields = document.getElementById('dine_in_fields');
        const deliveryFields = document.getElementById('delivery_fields');
        
        function updateFields() {
            const orderType = orderTypeSelect.value;
            
            dineInFields.classList.add('hidden');
            deliveryFields.classList.add('hidden');
            
            if (orderType === 'dine_in') {
                dineInFields.classList.remove('hidden');
            } else if (orderType === 'delivery') {
                deliveryFields.classList.remove('hidden');
            }
        }
        
        orderTypeSelect.addEventListener('change', updateFields);
        
        // Inicializar campos
        updateFields();
    });
</script>
@endsection