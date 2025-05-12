<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre del Producto -->
                            <div>
                                <x-input-label for="name" value="Nombre" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- Categoría -->
                            <div>
                                <x-input-label for="category_id" value="Categoría" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccionar categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>

                            <!-- Precio -->
                            <div>
                                <x-input-label for="price" value="Precio" />
                                <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>

                            <!-- Costo -->
                            <div>
                                <x-input-label for="cost" value="Costo" />
                                <x-text-input id="cost" name="cost" type="number" step="0.01" class="mt-1 block w-full" :value="old('cost')" />
                                <x-input-error class="mt-2" :messages="$errors->get('cost')" />
                            </div>

                            <!-- Disponibilidad -->
                            <div>
                                <x-input-label for="is_available" value="Disponibilidad" />
                                <select id="is_available" name="is_available" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="1" {{ old('is_available', 1) == 1 ? 'selected' : '' }}>Disponible</option>
                                    <option value="0" {{ old('is_available') == 0 ? 'selected' : '' }}>No disponible</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('is_available')" />
                            </div>

                            <!-- Control de Inventario -->
                            <div>
                                <x-input-label for="track_inventory" value="Control de Inventario" />
                                <select id="track_inventory" name="track_inventory" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="1" {{ old('track_inventory', 1) == 1 ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ old('track_inventory') == 0 ? 'selected' : '' }}>No</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('track_inventory')" />
                            </div>

                            <!-- Stock -->
                            <div id="stock-container">
                                <x-input-label for="stock" value="Stock Inicial" />
                                <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" :value="old('stock', 0)" />
                                <x-input-error class="mt-2" :messages="$errors->get('stock')" />
                            </div>

                            <!-- Imagen -->
                            <div>
                                <x-input-label for="image" value="Imagen" />
                                <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full">
                                <x-input-error class="mt-2" :messages="$errors->get('image')" />
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <x-input-label for="description" value="Descripción" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Ingredientes (Opcional, según tu negocio) -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Ingredientes del Inventario</h3>
                            <div id="ingredients-container">
                                @if(old('ingredients'))
                                    @foreach(old('ingredients') as $index => $ingredient)
                                        <div class="ingredient-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div>
                                                <x-input-label for="ingredients[{{ $index }}][inventory_item_id]" value="Ingrediente" />
                                                <select name="ingredients[{{ $index }}][inventory_item_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                    <option value="">Seleccionar ingrediente</option>
                                                    @foreach($inventoryItems as $item)
                                                        <option value="{{ $item->id }}" {{ $ingredient['inventory_item_id'] == $item->id ? 'selected' : '' }}>
                                                            {{ $item->name }} ({{ $item->unit }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <x-input-label for="ingredients[{{ $index }}][quantity]" value="Cantidad" />
                                                <x-text-input name="ingredients[{{ $index }}][quantity]" type="number" step="0.001" class="mt-1 block w-full" :value="$ingredient['quantity']" />
                                            </div>
                                            <div class="flex items-end">
                                                <button type="button" class="remove-ingredient bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <div class="mt-2">
                                <button type="button" id="add-ingredient" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-plus mr-1"></i> Agregar Ingrediente
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Guardar Producto') }}</x-primary-button>
                            <a href="{{ route('products.index') }}" class="text-gray-600">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para mostrar/ocultar campo de stock según el control de inventario
        document.addEventListener('DOMContentLoaded', function() {
            const trackInventorySelect = document.getElementById('track_inventory');
            const stockContainer = document.getElementById('stock-container');
            
            function toggleStockVisibility() {
                if (trackInventorySelect.value === '1') {
                    stockContainer.style.display = 'block';
                } else {
                    stockContainer.style.display = 'none';
                }
            }
            
            toggleStockVisibility();
            trackInventorySelect.addEventListener('change', toggleStockVisibility);
            
            // Manejo de ingredientes
            let ingredientIndex = {{ old('ingredients') ? count(old('ingredients')) : 0 }};
            const addIngredientBtn = document.getElementById('add-ingredient');
            const ingredientsContainer = document.getElementById('ingredients-container');
            
            addIngredientBtn.addEventListener('click', function() {
                const ingredientRow = document.createElement('div');
                ingredientRow.className = 'ingredient-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4';
                
                ingredientRow.innerHTML = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ingrediente</label>
                        <select name="ingredients[${ingredientIndex}][inventory_item_id]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Seleccionar ingrediente</option>
                            @foreach($inventoryItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                        <input type="number" step="0.001" name="ingredients[${ingredientIndex}][quantity]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-ingredient bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                
                ingredientsContainer.appendChild(ingredientRow);
                ingredientIndex++;
                
                // Agregar el evento para eliminar ingrediente
                const removeBtn = ingredientRow.querySelector('.remove-ingredient');
                removeBtn.addEventListener('click', function() {
                    ingredientRow.remove();
                });
            });
            
            // Agregar evento a los botones existentes de eliminar ingrediente
            document.querySelectorAll('.remove-ingredient').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.ingredient-row').remove();
                });
            });
        });
    </script>
</x-app-layout>