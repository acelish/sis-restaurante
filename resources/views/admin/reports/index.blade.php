<!-- filepath: resources\views\admin\reports\index.blade.php -->
@extends('layouts.app-content')

@section('title', 'Dashboard de Reportes')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard de Reportes</h1>
        
        <!-- Tarjetas de resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Ventas Totales -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ventas Totales (30 días)</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalSales, 2) }}</p>
                    </div>
                    <div class="p-2 bg-indigo-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <span class="{{ $salesChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm font-medium">
                            @if($salesChange >= 0)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            @endif
                            {{ number_format(abs($salesChange), 1) }}%
                        </span>
                        <span class="text-gray-500 text-sm ml-1">vs periodo anterior</span>
                    </div>
                </div>
            </div>
            
            <!-- Número de Pedidos -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pedidos (30 días)</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalOrders }}</p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <span class="text-gray-500 text-sm">
                            Ticket promedio: <span class="text-gray-800 font-medium">${{ number_format($averageTicket, 2) }}</span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Costos -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Costos (30 días)</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalCosts, 2) }}</p>
                    </div>
                    <div class="p-2 bg-red-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <span class="text-gray-500 text-sm">
                            % sobre ventas: <span class="text-gray-800 font-medium">{{ $totalSales > 0 ? number_format(($totalCosts / $totalSales) * 100, 1) : 0 }}%</span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Ganancia Estimada -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ganancia Estimada (30 días)</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($estimatedProfit, 2) }}</p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center">
                        <span class="text-gray-500 text-sm">
                            Margen: <span class="text-gray-800 font-medium">{{ $totalSales > 0 ? number_format(($estimatedProfit / $totalSales) * 100, 1) : 0 }}%</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enlaces rápidos a reportes detallados -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.reports.sales') }}" class="bg-white rounded-lg shadow-sm p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800">Reporte de Ventas</h3>
                    <p class="text-sm text-gray-500">Detalles de ventas por periodo</p>
                </div>
            </a>
            
            <a href="{{ route('admin.reports.products') }}" class="bg-white rounded-lg shadow-sm p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800">Productos Vendidos</h3>
                    <p class="text-sm text-gray-500">Análisis de productos populares</p>
                </div>
            </a>
            
            <a href="{{ route('admin.reports.inventory') }}" class="bg-white rounded-lg shadow-sm p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800">Inventario</h3>
                    <p class="text-sm text-gray-500">Movimientos y estado de stock</p>
                </div>
            </a>
            
            <a href="{{ route('admin.reports.financial') }}" class="bg-white rounded-lg shadow-sm p-5 flex items-center hover:shadow-md transition-shadow">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800">Finanzas</h3>
                    <p class="text-sm text-gray-500">Ingresos, gastos y ganancias</p>
                </div>
            </a>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Gráfico de ventas por día -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Día (Últimos 30 días)</h2>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            
            <!-- Ventas por Categoría -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Categoría</h2>
                <div class="h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            
            <!-- Productos Más Vendidos -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Productos Más Vendidos</h2>
                    <a href="{{ route('admin.reports.products') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topProducts as $product)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $product->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800 text-right">{{ $product->total_quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800 text-right">${{ number_format($product->total_revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Acceso rápido a presupuesto -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Planificación de Presupuesto</h2>
                    <a href="{{ route('admin.reports.budget') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver detalles</a>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-500">Ventas Estimadas (Próximo mes)</span>
                        <span class="text-sm font-medium text-gray-800">${{ number_format($totalSales * 1.05, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-500">Gastos Estimados</span>
                        <span class="text-sm font-medium text-gray-800">${{ number_format($totalCosts * 1.02, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-sm font-medium text-gray-500">Ganancia Estimada</span>
                        <span class="text-sm font-medium text-green-600">${{ number_format(($totalSales * 1.05) - ($totalCosts * 1.02), 2) }}</span>
                    </div>
                </div>
                
                <a href="{{ route('admin.reports.budget') }}" 
                   class="w-full block text-center px-4 py-2 border border-indigo-500 text-indigo-500 rounded-md hover:bg-indigo-500 hover:text-white transition-colors">
                    Crear Presupuesto para el Próximo Mes
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para gráfico de ventas por día
        const salesData = {
            labels: @json($salesByDay->pluck('date')),
            datasets: [{
                label: 'Ventas Diarias',
                data: @json($salesByDay->pluck('total')),
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        };

        // Configuración del gráfico de ventas
        const salesConfig = {
            type: 'line',
            data: salesData,
            options: {
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
                        grid: {
                            display: false
                        }
                    },
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
        };

        // Crear gráfico de ventas
        new Chart(
            document.getElementById('salesChart'),
            salesConfig
        );

        // Datos para gráfico de categorías
        const categoryData = {
            labels: @json($salesByCategory->pluck('name')),
            datasets: [{
                data: @json($salesByCategory->pluck('total_sales')),
                backgroundColor: [
                    'rgba(79, 70, 229, 0.7)',
                    'rgba(59, 130, 246, 0.7)',
                    'rgba(16, 185, 129, 0.7)',
                    'rgba(245, 158, 11, 0.7)',
                    'rgba(239, 68, 68, 0.7)',
                    'rgba(139, 92, 246, 0.7)',
                ],
                borderWidth: 1
            }]
        };

        // Configuración del gráfico de categorías
        const categoryConfig = {
            type: 'doughnut',
            data: categoryData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                },
            }
        };

        // Crear gráfico de categorías
        new Chart(
            document.getElementById('categoryChart'),
            categoryConfig
        );
    });
</script>
@endpush
@endsection