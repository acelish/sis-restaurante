<!-- filepath: resources\views\admin\reports\sales.blade.php -->
@extends('layouts.app-content')

@section('title', 'Reporte de Ventas')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Reporte de Ventas</h1>
            <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                    <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Pedido</label>
                    <select id="order_type" name="order_type" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Todos</option>
                        <option value="dine_in" {{ $orderType == 'dine_in' ? 'selected' : '' }}>Comer en el local</option>
                        <option value="takeaway" {{ $orderType == 'takeaway' ? 'selected' : '' }}>Para llevar</option>
                        <option value="delivery" {{ $orderType == 'delivery' ? 'selected' : '' }}>Entrega a domicilio</option>
                    </select>
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
                <div class="md:col-span-4 flex items-center justify-between">
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-filter mr-1"></i> Filtrar
                    </button>
                    <a href="{{ route('admin.reports.export.sales') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}&order_type={{ $orderType }}&category_id={{ $categoryId }}" 
                       class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        <i class="fas fa-download mr-1"></i> Exportar a CSV
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Resumen de ventas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Ventas Totales -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Ventas Totales</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalSales, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ $orderCount }} pedidos</p>
            </div>
            
            <!-- Ticket Promedio -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500">Ticket Promedio</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">
                    ${{ $orderCount > 0 ? number_format($totalSales / $orderCount, 2) : '0.00' }}
                </p>
                <p class="text-xs text-gray-500 mt-2">Por pedido</p>
            </div>
            
            <!-- Impuestos -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <p class="text-sm font-medium text-gray-500">Impuestos</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalTax, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ number_format(($totalTax / $totalSales) * 100, 1) }}% de ventas</p>
            </div>
            
            <!-- Descuentos -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
                <p class="text-sm font-medium text-gray-500">Descuentos</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalDiscount, 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ number_format(($totalDiscount / ($totalSales + $totalDiscount)) * 100, 1) }}% de ventas brutas</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Gráfico de ventas por día -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ventas Diarias</h2>
                <div class="h-64">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>
            
            <!-- Gráfico de tipo de pedido -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Tipo de Pedido</h2>
                <div class="h-64">
                    <canvas id="orderTypeChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Lista de pedidos -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Lista de Pedidos</h2>
                <p class="text-sm text-gray-500">{{ count($orders) }} pedidos encontrados</p>
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
                                Cliente
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subtotal
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Impuestos
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Detalles
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->customer_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($order->order_type == 'dine_in')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Local
                                        </span>
                                    @elseif($order->order_type == 'takeaway')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Para llevar
                                        </span>
                                    @elseif($order->order_type == 'delivery')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Delivery
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ${{ number_format($order->subtotal, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    ${{ number_format($order->tax, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    @if($order->status == 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completado
                                        </span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Cancelado
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <button type="button" 
                                            class="text-indigo-600 hover:text-indigo-900 show-order-details"
                                            data-order-id="{{ $order->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="p-6">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalles de pedido -->
<div id="orderDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" aria-modal="true">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-xl font-semibold text-gray-800" id="orderDetailsTitle">Detalles del Pedido #123</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500 close-modal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="orderDetailsContent" class="mt-4 max-h-96 overflow-y-auto">
            <!-- El contenido se cargará dinámicamente mediante AJAX -->
            <div class="flex justify-center items-center h-32">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
            </div>
        </div>
        
        <div class="mt-4 pt-3 border-t flex justify-end">
            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 close-modal">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Scripts para los gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos para el gráfico de ventas diarias
    const dailySalesData = {
        labels: {!! json_encode($dailySalesChart['dates']) !!},
        datasets: [{
            label: 'Ventas por día',
            data: {!! json_encode($dailySalesChart['sales']) !!},
            backgroundColor: 'rgba(79, 70, 229, 0.2)',
            borderColor: 'rgba(79, 70, 229, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };
    
    const dailySalesChart = new Chart(
        document.getElementById('dailySalesChart'),
        {
            type: 'line',
            data: dailySalesData,
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
                                return '$' + context.raw;
                            }
                        }
                    }
                }
            }
        }
    );
    
    // Datos para el gráfico de tipos de pedido
    const orderTypeData = {
        labels: ['Local', 'Para llevar', 'Delivery'],
        datasets: [{
            data: [
                {{ $orderTypeCount['dine_in'] ?? 0 }},
                {{ $orderTypeCount['takeaway'] ?? 0 }},
                {{ $orderTypeCount['delivery'] ?? 0 }}
            ],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(139, 92, 246, 0.8)'
            ],
            borderWidth: 1
        }]
    };
    
    const orderTypeChart = new Chart(
        document.getElementById('orderTypeChart'),
        {
            type: 'doughnut',
            data: orderTypeData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        }
    );
    
    // Modal de detalles de pedido
    const modal = document.getElementById('orderDetailsModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    const orderDetailsButtons = document.querySelectorAll('.show-order-details');
    const orderDetailsTitle = document.getElementById('orderDetailsTitle');
    const orderDetailsContent = document.getElementById('orderDetailsContent');
    
    orderDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            orderDetailsTitle.textContent = `Detalles del Pedido #${orderId}`;
            orderDetailsContent.innerHTML = `
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                </div>
            `;
            
            // Mostrar modal
            modal.classList.remove('hidden');
            
            // Cargar detalles mediante AJAX
            fetch(`/admin/orders/${orderId}/details`)
                .then(response => response.text())
                .then(html => {
                    orderDetailsContent.innerHTML = html;
                })
                .catch(error => {
                    orderDetailsContent.innerHTML = `
                        <div class="text-center text-red-500 py-4">
                            <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                            <p>Error al cargar los detalles.</p>
                        </div>
                    `;
                    console.error('Error:', error);
                });
        });
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    });
    
    // Cerrar modal al hacer clic fuera del contenido
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection
