<!-- filepath: resources\views\admin\reports\budget.blade.php -->
@extends('layouts.app-content')

@section('title', 'Planificación de Presupuesto')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Planificación de Presupuesto</h1>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
        </div>
        
        <!-- Formulario de selección de período -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reports.budget') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                    <select id="month" name="month" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                    <select id="year" name="year" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @foreach(range(date('Y'), date('Y') + 2) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-calendar-alt mr-1"></i> Seleccionar Período
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Resumen de presupuesto -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            <!-- Ingresos proyectados -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Ingresos Proyectados</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($projectedRevenue, 2) }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <span class="{{ $revenueTrend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        @if($revenueTrend >= 0)
                            <i class="fas fa-arrow-up mr-1"></i>
                        @else
                            <i class="fas fa-arrow-down mr-1"></i>
                        @endif
                        {{ number_format(abs($revenueTrend), 1) }}%
                    </span>
                    <span class="text-gray-500 ml-1">vs mes anterior</span>
                </div>
            </div>
            
            <!-- Costos proyectados -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500">Costos Proyectados</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($projectedCosts, 2) }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <span class="{{ $costsTrend <= 0 ? 'text-green-600' : 'text-red-600' }}">
                        @if($costsTrend <= 0)
                            <i class="fas fa-arrow-down mr-1"></i>
                        @else
                            <i class="fas fa-arrow-up mr-1"></i>
                        @endif
                        {{ number_format(abs($costsTrend), 1) }}%
                    </span>
                    <span class="text-gray-500 ml-1">vs mes anterior</span>
                </div>
            </div>
            
            <!-- Ganancia proyectada -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Ganancia Proyectada</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($projectedProfit, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">Margen: {{ number_format(($projectedProfit / $projectedRevenue) * 100, 1) }}%</p>
            </div>
        </div>
        
        <!-- Gráfico de comparación histórica -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Comparación Histórica</h2>
            <div class="h-80">
                <canvas id="historicalComparisonChart"></canvas>
            </div>
        </div>
        
        <!-- Formulario de presupuesto -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Presupuesto para {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h2>
                <p class="text-sm text-gray-500">Ajuste las estimaciones según sus objetivos</p>
            </div>
            
            <form action="{{ route('admin.reports.budget.save') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-md font-medium text-gray-700 mb-4">Proyección de Ingresos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="revenue_dine_in" class="block text-sm font-medium text-gray-700 mb-1">Local</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="revenue_dine_in" name="revenue[dine_in]" 
                                           value="{{ $budget['revenue']['dine_in'] ?? $projectedRevenueByType['dine_in'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="revenue_takeaway" class="block text-sm font-medium text-gray-700 mb-1">Para llevar</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="revenue_takeaway" name="revenue[takeaway]" 
                                           value="{{ $budget['revenue']['takeaway'] ?? $projectedRevenueByType['takeaway'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="revenue_delivery" class="block text-sm font-medium text-gray-700 mb-1">Delivery</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="revenue_delivery" name="revenue[delivery]" 
                                           value="{{ $budget['revenue']['delivery'] ?? $projectedRevenueByType['delivery'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-md font-medium text-gray-700 mb-4">Proyección de Costos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="costs_products" class="block text-sm font-medium text-gray-700 mb-1">Productos/Ingredientes</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="costs_products" name="costs[products]" 
                                           value="{{ $budget['costs']['products'] ?? $projectedCostsByCategory['products'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="costs_personal" class="block text-sm font-medium text-gray-700 mb-1">Personal</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="costs_personal" name="costs[personal]" 
                                           value="{{ $budget['costs']['personal'] ?? $projectedCostsByCategory['personal'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="costs_servicios" class="block text-sm font-medium text-gray-700 mb-1">Servicios</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="costs_servicios" name="costs[servicios]" 
                                           value="{{ $budget['costs']['servicios'] ?? $projectedCostsByCategory['servicios'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="costs_alquiler" class="block text-sm font-medium text-gray-700 mb-1">Alquiler</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="costs_alquiler" name="costs[alquiler]" 
                                           value="{{ $budget['costs']['alquiler'] ?? $projectedCostsByCategory['alquiler'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                            <div>
                                <label for="costs_otros" class="block text-sm font-medium text-gray-700 mb-1">Otros gastos</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" step="0.01" min="0" id="costs_otros" name="costs[otros]" 
                                           value="{{ $budget['costs']['otros'] ?? $projectedCostsByCategory['otros'] ?? 0 }}" 
                                           class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notas y Observaciones</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ $budget['notes'] ?? '' }}</textarea>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-md font-medium text-gray-700 mb-2">Resumen</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="p-3 bg-gray-100 rounded-md flex justify-between items-center">
                                <span class="font-medium">Total Ingresos:</span>
                                <span id="total_revenue" class="font-semibold">${{ number_format($projectedRevenue, 2) }}</span>
                            </div>
                            <div class="p-3 bg-gray-100 rounded-md flex justify-between items-center">
                                <span class="font-medium">Total Costos:</span>
                                <span id="total_costs" class="font-semibold">${{ number_format($projectedCosts, 2) }}</span>
                            </div>
                            <div class="p-3 bg-green-50 rounded-md flex justify-between items-center">
                                <span class="font-medium">Ganancia Estimada:</span>
                                <span id="total_profit" class="font-semibold text-green-600">${{ number_format($projectedProfit, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-1"></i> Guardar Presupuesto
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Consejos y recomendaciones -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Consejos para planificación de presupuestos</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Revise el historial de ventas de meses anteriores para establecer objetivos realistas</li>
                            <li>Considere la estacionalidad y eventos especiales para ajustar proyecciones</li>
                            <li>Identifique oportunidades para reducir costos sin comprometer la calidad</li>
                            <li>Establezca un fondo de emergencia para gastos imprevistos</li>
                            <li>Revise y ajuste el presupuesto regularmente durante el mes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para los gráficos -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de comparación histórica
        const historicalContext = document.getElementById('historicalComparisonChart').getContext('2d');
        
        const historicalData = {
            labels: {!! json_encode($historicalLabels) !!},
            datasets: [
                {
                    label: 'Ingresos',
                    data: {!! json_encode($historicalRevenue) !!},
                    borderColor: 'rgba(79, 70, 229, 1)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Costos',
                    data: {!! json_encode($historicalCosts) !!},
                    borderColor: 'rgba(239, 68, 68, 1)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Ganancia',
                    data: {!! json_encode($historicalProfit) !!},
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }
            ]
        };
        
        const historicalChart = new Chart(historicalContext, {
            type: 'line',
            data: historicalData,
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
        
        // Actualizar totales en tiempo real
        function updateTotals() {
            // Ingresos
            const dineIn = parseFloat(document.getElementById('revenue_dine_in').value) || 0;
            const takeaway = parseFloat(document.getElementById('revenue_takeaway').value) || 0;
            const delivery = parseFloat(document.getElementById('revenue_delivery').value) || 0;
            const totalRevenue = dineIn + takeaway + delivery;
            
            // Costos
            const products = parseFloat(document.getElementById('costs_products').value) || 0;
            const personal = parseFloat(document.getElementById('costs_personal').value) || 0;
            const servicios = parseFloat(document.getElementById('costs_servicios').value) || 0;
            const alquiler = parseFloat(document.getElementById('costs_alquiler').value) || 0;
            const otros = parseFloat(document.getElementById('costs_otros').value) || 0;
            const totalCosts = products + personal + servicios + alquiler + otros;
            
            // Ganancia
            const totalProfit = totalRevenue - totalCosts;
            
            // Actualizar elementos HTML
            document.getElementById('total_revenue').textContent = '$' + totalRevenue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('total_costs').textContent = '$' + totalCosts.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('total_profit').textContent = '$' + totalProfit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            // Cambiar color de ganancia según sea positiva o negativa
            const profitElement = document.getElementById('total_profit');
            if (totalProfit >= 0) {
                profitElement.classList.remove('text-red-600');
                profitElement.classList.add('text-green-600');
            } else {
                profitElement.classList.remove('text-green-600');
                profitElement.classList.add('text-red-600');
            }
        }
        
        // Añadir event listeners a todos los campos de entrada
        const inputFields = document.querySelectorAll('input[type="number"]');
        inputFields.forEach(input => {
            input.addEventListener('input', updateTotals);
        });
        
        // Inicializar totales
        updateTotals();
    });
</script>
@endpush
@endsection