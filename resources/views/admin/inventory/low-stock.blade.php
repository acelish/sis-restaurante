<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items con Stock Bajo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($items->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-gray-500">No hay items con stock bajo en este momento.</p>
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad Actual</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Umbral de Alerta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-red-600">
                                        {{ $item->quantity }} {{ $item->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $item->alert_threshold }} {{ $item->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('inventory.show', $item) }}" 
                                           class="text-blue-600 hover:text-blue-900">Ver Detalles</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>