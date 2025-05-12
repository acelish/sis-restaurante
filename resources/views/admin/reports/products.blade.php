<!-- filepath: resources\views\admin\reports\products.blade.php -->
@extends('layouts.app-content')

@section('title', 'Reporte de Productos Vendidos')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Reporte de Productos Vendidos</h1>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reports.products') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select id="category_id" name="category_id" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                    <select id="sort_by" name="sort_by" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="quantity" {{ $sortBy == 'quantity' ? 'selected' : '' }}>Cantidad vendida</option>
                        <option value="revenue" {{ $sortBy == 'revenue' ? 'selected' : '' }}>Ingresos generados</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex items-center justify-between">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.reports.export.products') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}&category_id={{ $categoryId }}&sort_by={{ $sortBy }}" 
                       class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-download mr-1"></i> Exportar a CSV
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total de productos vendidos -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Productos Vendidos</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalQuantity }}</p>
                <p class="text-xs text-gray-500 mt-2">unidades totales</p>
            </div>
            
            <!-- Ingresos totales generados -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500">Ingresos Generados</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalRevenue, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">por {{ $productCount }} productos distintos</p>
            </div>
            
            <!-- Producto más vendido -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Producto Más Vendido</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ $topProduct ? $topProduct->name : 'N/A' }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    @if($topProduct)
                        {{ $topProduct->total_quantity }} unidades (${{ number_format($topProduct->total_revenue, 2) }})
                    @else
                        No hay datos disponibles
                    @endif
                </p>
            </div>
            
            <!-- Categoría más vendida -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Categoría Más Vendida</p>
                <p class="text-xl font-bold text-gray-800 mt-1">{{ $topCategory ? $topCategory->name : 'N/A' }}</p>
                <p class="text-xs text-gray-500 mt-2">
                    @if($topCategory)
                        {{ $topCategory->product_count }} productos (${{ number_format($topCategory->total_sales, 2) }})
                    @else
                        No hay datos disponibles
                    @endif
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Gráfico de los 10 productos más vendidos por cantidad -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Top 10 - Por Cantidad Vendida</h2>
                <div class="h-80">
                    <canvas id="topProductsQuantityChart"></canvas>
                </div>
            </div>
            
            <!-- Gráfico de los 10 productos más vendidos por ingresos -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Top 10 - Por Ingresos Generados</h2>
                <div class="h-80">
                    <canvas id="topProductsRevenueChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Lista de productos vendidos -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Listado de Productos Vendidos</h2>
                <p class="text-sm text-gray-500">{{ $products->total() }} productos encontrados</p>
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
                                Cantidad Vendida
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio Unitario
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ingresos Totales
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                % de Ventas
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $product->category_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ $product->total_quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ${{ number_format($product->price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    ${{ number_format($product->total_revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    {{ $totalRevenue > 0 ? number_format(($product->total_revenue / $totalRevenue) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="p-6">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
        
        <!-- Análisis de tendencias -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tendencias de Venta por Categoría</h2>
            <div class="h-80">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Top 10 por cantidad
    const topProductsQuantityChart = new Chart(
        document.getElementById('topProductsQuantityChart'),
        {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProductsByQuantity->pluck('name')) !!},
                datasets: [{
                    label: 'Cantidad Vendida',
                    data: {!! json_encode($topProductsByQuantity->pluck('total_quantity')) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.7)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Unidades vendidas'
                        }
                    }
                }
            }
        }
    );
    
    // Gráfico de Top 10 por ingresos
    const topProductsRevenueChart = new Chart(
        document.getElementById('topProductsRevenueChart'),
        {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProductsByRevenue->pluck('name')) !!},
                datasets: [{
                    label: 'Ingresos Generados',
                    data: {!! json_encode($topProductsByRevenue->pluck('total_revenue')) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Ingresos ($)'
                        }
                    }
                }
            }
        }
    );
    
    // Gráfico de tendencias por categoría
    const categorySalesChart = new Chart(
        document.getElementById('categorySalesChart'),
        {
            type: 'line',
            data: {
                labels: {!! json_encode($categoryTrends['dates']) !!},
                datasets: [
                    @foreach($categoryTrends['categories'] as $index => $category)
                    {
                        label: '{{ $category['name'] }}',
                        data: {!! json_encode($category['sales']) !!},
                        borderColor: [
                            'rgba(79, 70, 229, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(245, 158, 11, 1)',
                            'rgba(239, 68, 68, 1)',
                            'rgba(59, 130, 246, 1)',
                            'rgba(236, 72, 153, 1)'
                        ][{{ $index % 6 }}],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.1)',
                            'rgba(16, 185, 129, 0.1)',
                            'rgba(245, 158, 11, 0.1)',
                            'rgba(239, 68, 68, 0.1)',
                            'rgba(59, 130, 246, 0.1)',
                            'rgba(236, 72, 153, 0.1)'
                        ][{{ $index % 6 }}],
                        tension: 0.4,
                        fill: true
                    }{{ $loop->last ? '' : ',' }}
                    @endforeach
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
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