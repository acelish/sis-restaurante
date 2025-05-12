<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Item') }}: {{ $inventory->name }}
            </h2>
            <a href="{{ route('inventory.edit', $inventory) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Editar Item
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Información del Item</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="font-semibold">Nombre:</dt>
                            <dd>{{ $inventory->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Unidad:</dt>
                            <dd>{{ $inventory->unit }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Cantidad Actual:</dt>
                            <dd>{{ $inventory->quantity }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Umbral de Alerta:</dt>
                            <dd>{{ $inventory->alert_threshold }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Costo:</dt>
                            <dd>${{ number_format($inventory->cost, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold">Estado:</dt>
                            <dd>
                                @if($inventory->isLowStock())
                                    <span class="text-red-600">Stock Bajo</span>
                                @else
                                    <span class="text-green-600">Normal</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Movimientos Recientes</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $movement->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ ucfirst($movement->type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $movement->user->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $movement->notes }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>