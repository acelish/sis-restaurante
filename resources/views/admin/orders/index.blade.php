<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Órdenes') }}
            </h2>
            <a href="{{ route('orders.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-1"></i> Nueva Orden
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtros -->
                    <div class="mb-4 flex flex-wrap items-center gap-4">
                        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-wrap gap-2">
                            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos los estados</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>En preparación</option>
                                <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Listo</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>

                            <select name="payment_status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos los pagos</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Pagado</option>
                                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                            </select>

                            <select name="order_type" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Todos los tipos</option>
                                <option value="dine_in" {{ request('order_type') == 'dine_in' ? 'selected' : '' }}>En restaurante</option>
                                <option value="takeaway" {{ request('order_type') == 'takeaway' ? 'selected' : '' }}>Para llevar</option>
                                <option value="delivery" {{ request('order_type') == 'delivery' ? 'selected' : '' }}>Entrega a domicilio</option>
                                <option value="online" {{ request('order_type') == 'online' ? 'selected' : '' }}>Pedido online</option>
                            </select>

                            <div class="relative">
                                <input type="text" name="search" placeholder="Buscar..." value="{{ request('search') }}" 
                                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-search mr-1"></i> Filtrar
                            </button>
                            
                            @if(request()->hasAny(['status', 'payment_status', 'order_type', 'search']))
                                <a href="{{ route('orders.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    <i class="fas fa-times mr-1"></i> Limpiar
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Tabla de Órdenes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pago</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $order->user ? $order->user->name : ($order->customer_name ?? 'Cliente anónimo') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->order_type_text }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${{ number_format($order->total, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pendiente
                                                </span>
                                            @elseif($order->status == 'preparing')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    En preparación
                                                </span>
                                            @elseif($order->status == 'ready')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    Listo
                                                </span>
                                            @elseif($order->status == 'delivered')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Entregado
                                                </span>
                                            @elseif($order->status == 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Completado
                                                </span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Cancelado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->payment_status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pendiente
                                                </span>
                                            @elseif($order->payment_status == 'paid')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Pagado
                                                </span>
                                            @elseif($order->payment_status == 'refunded')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Reembolsado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                                <a href="{{ route('orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                @if($order->status == 'cancelled' || $order->status == 'completed')
                                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                                onclick="return confirm('¿Estás seguro de eliminar esta orden?')">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center">No hay órdenes disponibles</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>