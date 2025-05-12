<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Producto') }}: {{ $product->name }}
            </h2>
            <a href="{{ route('products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-edit mr-1"></i> Editar Producto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-bold mb-4">Información del Producto</h3>
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="font-semibold">Nombre:</dt>
                                    <dd>{{ $product->name }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Categoría:</dt>
                                    <dd>{{ $product->category->name }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Precio:</dt>
                                    <dd>${{ number_format($product->price, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Costo:</dt>
                                    <dd>${{ number_format($product->cost ?? 0, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Margen:</dt>
                                    <dd>
                                        @if($product->cost && $product->cost > 0)
                                            {{ number_format((($product->price - $product->cost) / $product->cost) * 100, 2) }}%
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Estado:</dt>
                                    <dd>
                                        @if($product->is_available)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Disponible
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                No disponible
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Control de Inventario:</dt>
                                    <dd>{{ $product->track_inventory ? 'Sí' : 'No' }}</dd>
                                </div>
                                @if($product->track_inventory)
                                <div>
                                    <dt class="font-semibold">Stock Actual:</dt>
                                    <dd>{{ $product->stock }}</dd>
                                </div>
                                @endif
                            </dl>

                            <div class="mt-4">
                                <h4 class="font-semibold">Descripción:</h4>
                                <p class="mt-1">{{ $product->description }}</p>
                            </div>
                        </div>
                        
                        <div>
                            @if($product->image)
                                <div class="text-center">
                                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="max-h-64 mx-auto object-cover rounded">
                                </div>
                            @else
                                <div class="h-64 bg-gray-200 rounded flex items-center justify-center">
                                    <span class="text-gray-500">No hay imagen disponible</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($product->inventoryItems->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Ingredientes</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingrediente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Disponible</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($product->inventoryItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->pivot->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($item->cost * $item->pivot->quantity, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->quantity < $item->pivot->quantity)
                                        <span class="text-red-600">{{ $item->quantity }} {{ $item->unit }} (insuficiente)</span>
                                    @else
                                        <span class="text-green-600">{{ $item->quantity }} {{ $item->unit }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>