````blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de Mesa') }}: {{ $table->number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tables.edit', $table) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
                <form action="{{ route('tables.destroy', $table) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta mesa?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash mr-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Información de la Mesa</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="grid grid-cols-3 gap-4">
                                <div class="col-span-1 font-medium">Número:</div>
                                <div class="col-span-2">{{ $table->number }}</div>
                                
                                <div class="col-span-1 font-medium">Capacidad:</div>
                                <div class="col-span-2">{{ $table->capacity }} personas</div>
                                
                                <div class="col-span-1 font-medium">Estado:</div>
                                <div class="col-span-2">
                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $table->status_color }}-100 text-{{ $table->status_color }}-800">
                                        {{ $table->status_text }}
                                    </span>
                                </div>
                                
                                <div class="col-span-1 font-medium">Creada:</div>
                                <div class="col-span-2">{{ $table->created_at->format('d/m/Y H:i') }}</div>
                                
                                <div class="col-span-1 font-medium">Actualizada:</div>
                                <div class="col-span-2">{{ $table->updated_at->format('d/m/Y H:i') }}</div>
                            </dl>
                        </div>
                        
                        <div>
                            <h4 class="font-medium mb-2">Cambiar Estado Rápidamente</h4>
                            <form action="{{ route('tables.update-status', $table) }}" method="POST" class="flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Disponible</option>
                                    <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Ocupada</option>
                                    <option value="reserved" {{ $table->status == 'reserved' ? 'selected' : '' }}>Reservada</option>
                                    <option value="maintenance" {{ $table->status == 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                                </select>
                                <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Actualizar Estado
                                </button>
                            </form>

                            <div class="mt-6">
                                <h4 class="font-medium mb-2">Acciones Rápidas</h4>
                                <div class="flex gap-2">
                                    <a href="{{ route('reservations.create', ['table_id' => $table->id]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-calendar-plus mr-1"></i> Nueva Reserva
                                    </a>
                                    <a href="{{ route('orders.create', ['table_id' => $table->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-utensils mr-1"></i> Nueva Orden
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservas recientes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Reservas Recientes</h3>
                        <a href="{{ route('reservations.index', ['table_id' => $table->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                            Ver todas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @if($reservations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reservations as $reservation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-900">{{ $reservation->customer_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $reservation->customer_phone }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $reservation->reservation_time->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $reservation->num_guests }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($reservation->status == 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pendiente
                                                    </span>
                                                @elseif($reservation->status == 'confirmed')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Confirmada
                                                    </span>
                                                @elseif($reservation->status == 'cancelled')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Cancelada
                                                    </span>
                                                @elseif($reservation->status == 'completed')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Completada
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('reservations.show', $reservation) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('reservations.edit', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">No hay reservas recientes para esta mesa.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Órdenes recientes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Órdenes Recientes</h3>
                        <a href="{{ route('orders.index', ['table_id' => $table->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                            Ver todas <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($orders as $order)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                #{{ $order->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $order->user ? $order->user->name : ($order->customer_name ?? 'Cliente anónimo') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                ${{ number_format($order->total, 2) }}
                                            </td>
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
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('orders.edit', $order) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">No hay órdenes recientes para esta mesa.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>