@extends('layouts.app-content')

@section('title', 'Reporte Financiero')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Reporte Financiero</h1>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reports.financial') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Resumen financiero -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Ingresos totales -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Ingresos Totales</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalSales, 2) }}</p>
            </div>
            
            <!-- Gastos totales -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500">Gastos Totales</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalExpenses, 2) }}</p>
            </div>
            
            <!-- Ganancia neta -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Ganancia Neta</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalProfit, 2) }}</p>
            </div>
            
            <!-- Impuestos recaudados -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Impuestos Recaudados</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalTax, 2) }}</p>
            </div>
        </div>
        
        <!-- Gráfico principal: Ingresos vs Gastos -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Ingresos vs Gastos</h2>
            <div class="h-80">
                <canvas id="financialChart"></canvas>
            </div>
        </div>
        
        <!-- Contenido en columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Métodos de pago -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Métodos de Pago</h3>
                </div>
                <div class="p-6">
                    <div class="h-60">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pedidos</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($paymentMethods as $method)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ucfirst($method->payment_method) }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{ $method->count }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-900 text-right">
                                        ${{ number_format($method->total, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Margen de beneficio por categoría -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Margen de Beneficio por Categoría</h3>
                </div>
                <div class="p-6">
                    <div class="h-60">
                        <canvas id="categoryMarginsChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Costos</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Margen</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categoryMargins as $category)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $category->name }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                        ${{ number_format($category->revenue, 2) }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 text-right">
                                        ${{ number_format($category->cost, 2) }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->margin >= 30 ? 'bg-green-100 text-green-800' : ($category->margin >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ number_format($category->margin, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Datos detallados -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Informe Financiero Diario</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gastos</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ganancia</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Margen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($financialData as $day)
                        <tr>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $day['date'] }}</td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($day['sales'], 2) }}</td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 text-right">${{ number_format($day['expenses'], 2) }}</td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-900 text-right {{ $day['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($day['profit'], 2) }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm text-right">
                                @if($day['sales'] > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($day['profit'] / $day['sales'] * 100) >= 30 ? 'bg-green-100 text-green-800' : (($day['profit'] / $day['sales'] * 100) >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($day['profit'] / $day['sales'] * 100, 1) }}%
                                    </span>
                                @else
                                    <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">Total</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${{ number_format($totalSales, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">${{ number_format($totalExpenses, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($totalProfit, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right">
                                @if($totalSales > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($totalProfit / $totalSales * 100) >= 30 ? 'bg-green-100 text-green-800' : (($totalProfit / $totalSales * 100) >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($totalProfit / $totalSales * 100, 1) }}%
                                    </span>
                                @else
                                    <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico principal
    const financialData = @json($financialData);
    const labels = financialData.map(day => day.date);
    const sales = financialData.map(day => day.sales);
    const expenses = financialData.map(day => day.expenses);
    const profits = financialData.map(day => day.profit);
    
    // Gráfico principal: Ingresos vs Gastos
    const financialCtx = document.getElementById('financialChart').getContext('2d');
    new Chart(financialCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: sales,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Gastos',
                    data: expenses,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Ganancia',
                    data: profits,
                    type: 'line',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de métodos de pago
    const paymentMethods = @json($paymentMethods);
    const paymentLabels = paymentMethods.map(method => method.payment_method.charAt(0).toUpperCase() + method.payment_method.slice(1));
    const paymentCounts = paymentMethods.map(method => method.count);
    const paymentTotals = paymentMethods.map(method => method.total);
    
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(paymentMethodsCtx, {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentTotals,
                backgroundColor: [
                    'rgba(79, 70, 229, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(6, 182, 212, 0.8)',
                    'rgba(124, 58, 237, 0.8)'
                ],
                borderColor: [
                    'rgba(79, 70, 229, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(6, 182, 212, 1)',
                    'rgba(124, 58, 237, 1)'
                ],
                borderWidth: 1
            }]
        },
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
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                            const percentage = Math.round((value / total) * 100);
                            return label + ': $' + value.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Gráfico de márgenes por categoría
    const categoryMargins = @json($categoryMargins);
    const categoryLabels = categoryMargins.map(cat => cat.name);
    const categoryRevenues = categoryMargins.map(cat => cat.revenue);
    const categoryCosts = categoryMargins.map(cat => cat.cost);
    const categoryProfits = categoryMargins.map(cat => cat.profit);
    const categoryMarginPercentages = categoryMargins.map(cat => cat.margin);
    
    const categoryMarginsCtx = document.getElementById('categoryMarginsChart').getContext('2d');
    new Chart(categoryMarginsCtx, {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: categoryRevenues,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Costos',
                    data: categoryCosts,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Margen (%)',
                    data: categoryMarginPercentages,
                    type: 'line',
                    yAxisID: 'y1',
                    backgroundColor: 'rgba(16, 185, 129, 0)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Margen (%)') {
                                return context.dataset.label + ': ' + context.raw.toFixed(1) + '%';
                            }
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection