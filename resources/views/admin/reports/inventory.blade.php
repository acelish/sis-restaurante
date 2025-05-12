<!-- filepath: resources\views\admin\reports\inventory.blade.php -->
@extends('layouts.app-content')

@section('title', 'Reporte de Inventario y Costos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Reporte de Inventario y Costos</h1>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reports.inventory') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="item_category" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select id="item_category" name="item_category" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Todas</option>
                        @foreach($itemCategories as $category)
                            <option value="{{ $category }}" {{ $itemCategory == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3 flex items-center justify-between">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.reports.export.inventory') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}&item_category={{ $itemCategory }}" 
                       class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-download mr-1"></i> Exportar a CSV
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Resumen de inventario -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total de Productos en Inventario -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Total en Inventario</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $inventoryItemCount }}</p>
                <p class="text-xs text-gray-500 mt-2">productos registrados</p>
            </div>
            
            <!-- Valor Total del Inventario -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500">Valor del Inventario</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($inventoryValue, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">basado en precios de compra</p>
            </div>
            
            <!-- Productos en Stock Bajo -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Stock Bajo</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $lowStockCount }}</p>
                <p class="text-xs text-gray-500 mt-2">productos por debajo del mínimo</p>
            </div>
            
            <!-- Productos Agotados -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500">Agotados</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $outOfStockCount }}</p>
                <p class="text-xs text-gray-500 mt-2">productos sin existencias</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Gráfico de movimientos de inventario -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Movimientos de Inventario</h2>
                <div class="h-64">
                    <canvas id="inventoryMovementsChart"></canvas>
                </div>
            </div>
            
            <!-- Gráfico de categorías de inventario -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Inventario por Categoría</h2>
                <div class="h-64">
                    <canvas id="inventoryCategoryChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Lista de movimientos de inventario -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Movimientos de Inventario</h2>
                <p class="text-sm text-gray-500">{{ count($inventoryMovements) }} movimientos encontrados</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cantidad
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Costo Unitario
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registrado por
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inventoryMovements as $movement)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $movement->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $movement->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $movement->inventoryItem->name }} ({{ $movement->inventoryItem->category }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($movement->type == 'purchase')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Compra
                                        </span>
                                    @elseif($movement->type == 'sale')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Venta
                                        </span>
                                    @elseif($movement->type == 'adjustment')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Ajuste
                                        </span>
                                    @elseif($movement->type == 'waste')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Desperdicio
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    @if($movement->type == 'sale' || $movement->type == 'waste')
                                        <span class="text-red-600">-{{ $movement->quantity }} {{ $movement->inventoryItem->unit }}</span>
                                    @else
                                        <span class="text-green-600">+{{ $movement->quantity }} {{ $movement->inventoryItem->unit }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ${{ number_format($movement->unit_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    ${{ number_format($movement->quantity * $movement->unit_cost, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $movement->user ? $movement->user->name : 'Sistema' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="p-6">
                {{ $inventoryMovements->appends(request()->query())->links() }}
            </div>
        </div>
        
        <!-- Lista de productos en inventario -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Estado Actual del Inventario</h2>
                <p class="text-sm text-gray-500">{{ count($inventoryItems) }} productos en inventario</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Producto
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock Actual
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock Mínimo
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Último Precio
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor Total
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inventoryItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ $item->current_stock }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    {{ $item->min_stock }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ${{ number_format($item->last_purchase_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    ${{ number_format($item->current_stock * $item->last_purchase_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($item->current_stock <= 0)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Agotado
                                        </span>
                                    @elseif($item->current_stock < $item->min_stock)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Stock Bajo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            OK
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="p-6">
                {{ $inventoryItems->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Scripts para los gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de movimientos de inventario
    const inventoryMovementsData = {
        labels: {!! json_encode($inventoryMovementsChart['dates']) !!},
        datasets: [
            {
                label: 'Compras',
                data: {!! json_encode($inventoryMovementsChart['purchases']) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 2
            },
            {
                label: 'Ventas',
                data: {!! json_encode($inventoryMovementsChart['sales']) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2
            },
            {
                label: 'Ajustes',
                data: {!! json_encode($inventoryMovementsChart['adjustments']) !!},
                backgroundColor: 'rgba(245, 158, 11, 0.2)',
                borderColor: 'rgba(245, 158, 11, 1)',
                borderWidth: 2
            },
            {
                label: 'Desperdicios',
                data: {!! json_encode($inventoryMovementsChart['waste']) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 2
            }
        ]
    };
    
    const inventoryMovementsChart = new Chart(
        document.getElementById('inventoryMovementsChart'),
        {
            type: 'bar',
            data: inventoryMovementsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.raw;
                            }
                        }
                    }
                }
            }
        }
    );
    
    // Datos para el gráfico de categorías de inventario
    const inventoryCategoryData = {
        labels: {!! json_encode(array_keys($inventoryCategoryChart)) !!},
        datasets: [{
            data: {!! json_encode(array_values($inventoryCategoryChart)) !!},
            backgroundColor: [
                'rgba(79, 70, 229, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(236, 72, 153, 0.8)',
                'rgba(139, 92, 246, 0.8)'
            ],
            borderWidth: 1
        }]
    };
    
    const inventoryCategoryChart = new Chart(
        document.getElementById('inventoryCategoryChart'),
        {
            type: 'pie',
            data: inventoryCategoryData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.raw;
                            }
                        }
                    }
                }
            }
        }
    );
});
</script>
@endsection