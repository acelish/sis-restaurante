<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Reservaciones') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reservations.calendar') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-calendar-alt mr-1"></i> Calendario
                </a>
                <a href="{{ route('reservations.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-1"></i> Nueva Reservación
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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

                    <!-- Filtros -->
                    <div class="mb-6">
                        <form action="{{ route('reservations.index') }}" method="GET" class="flex flex-wrap gap-4">
                            <div>
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos los estados</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmadas</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completadas</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                                    <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Asistió</option>
                                </select>
                            </div>

                            <div>
                                <input type="date" name="date" value="{{ request('date') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <select name="table_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todas las mesas</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ request('table_id') == $table->id ? 'selected' : '' }}>
                                            Mesa {{ $table->number }} ({{ $table->capacity }} pers.)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <input type="text" name="search" placeholder="Buscar cliente..." value="{{ request('search') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-search mr-1"></i> Filtrar
                                </button>
                            </div>

                            @if(request()->hasAny(['status', 'date', 'table_id', 'search']))
                                <div>
                                    <a href="{{ route('reservations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-times mr-1"></i> Limpiar
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>

                    <!-- Listado de reservaciones -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mesa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($reservations as $reservation)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $reservation->customer_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                @if($reservation->customer_phone)
                                                    <div><i class="fas fa-phone text-xs mr-1"></i>{{ $reservation->customer_phone }}</div>
                                                @endif
                                                @if($reservation->customer_email)
                                                    <div><i class="fas fa-envelope text-xs mr-1"></i>{{ $reservation->customer_email }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($reservation->table)
                                                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800">
                                                    Mesa {{ $reservation->table->number }} ({{ $reservation->table->capacity }} pers.)
                                                </span>
                                            @else
                                                <span class="text-gray-500">Mesa no disponible</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium">{{ $reservation->reservation_time->format('d/m/Y') }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ $reservation->reservation_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}
                                                <span class="text-xs">({{ $reservation->duration }} min)</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{ $reservation->num_guests }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $reservation->status_color }}-100 text-{{ $reservation->status_color }}-800">
                                                {{ $reservation->status_text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('reservations.show', $reservation) }}" class="text-blue-600 hover:text-blue-900">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('reservations.edit', $reservation) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar esta reservación?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            No hay reservaciones que coincidan con los criterios de búsqueda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>