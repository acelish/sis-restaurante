<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de Reservación') }} #{{ $reservation->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reservations.edit', $reservation) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
                <form action="{{ route('reservations.destroy', $reservation) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta reservación?');" class="inline">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Información de la Reservación</h3>
                            <p class="text-sm text-gray-500">Creada el {{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-{{ $reservation->status_color }}-100 text-{{ $reservation->status_color }}-800">
                                {{ $reservation->status_text }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-2">Detalles del Cliente</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-2">
                                    <span class="text-gray-600 font-medium">Nombre:</span>
                                    <span class="ml-2">{{ $reservation->customer_name }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-gray-600 font-medium">Teléfono:</span>
                                    <span class="ml-2">{{ $reservation->customer_phone }}</span>
                                </div>
                                @if($reservation->customer_email)
                                    <div class="mb-2">
                                        <span class="text-gray-600 font-medium">Email:</span>
                                        <span class="ml-2">{{ $reservation->customer_email }}</span>
                                    </div>
                                @endif
                                @if($reservation->user)
                                    <div class="mb-2">
                                        <span class="text-gray-600 font-medium">Usuario Registrado:</span>
                                        <span class="ml-2">{{ $reservation->user->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-2">Detalles de la Reserva</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="mb-2">
                                    <span class="text-gray-600 font-medium">Mesa:</span>
                                    <span class="ml-2">
                                        @if($reservation->table)
                                            Mesa {{ $reservation->table->number }} ({{ $reservation->table->capacity }} personas)
                                        @else
                                            No asignada
                                        @endif
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-gray-600 font-medium">Fecha y Hora:</span>
                                    <span class="ml-2">{{ $reservation->reservation_time->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-gray-600 font-medium">Duración:</span>
                                    <span class="ml-2">{{ $reservation->duration }} minutos</span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-gray-600 font-medium">Número de Personas:</span>
                                    <span class="ml-2">{{ $reservation->num_guests }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($reservation->notes)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Notas</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                {{ $reservation->notes }}
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-700 mb-2">Acciones Rápidas</h4>
                        <div class="flex flex-wrap gap-2">
                            @if($reservation->status == 'pending')
                                <form action="{{ route('reservations.update-status', $reservation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-check mr-1"></i> Confirmar Reservación
                                    </button>
                                </form>
                            @endif

                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                                <form action="{{ route('reservations.update-status', $reservation) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta reservación?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-times mr-1"></i> Cancelar Reservación
                                    </button>
                                </form>
                            @endif

                            @if(in_array($reservation->status, ['pending', 'confirmed']) && !$reservation->reservation_time->isFuture())
                                <form action="{{ route('reservations.update-status', $reservation) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-flag-checkered mr-1"></i> Marcar como Completada
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('orders.create', ['reservation_id' => $reservation->id]) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-utensils mr-1"></i> Crear Orden
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('reservations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al listado
                </a>
                <a href="{{ route('reservations.calendar') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <i class="fas fa-calendar-alt mr-2"></i> Ver en Calendario
                </a>
            </div>
        </div>
    </div>
</x-app-layout>