<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Mesas') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tables.map') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-map mr-1"></i> Ver Mapa
                </a>
                <a href="{{ route('tables.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-1"></i> Nueva Mesa
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
                        <form action="{{ route('tables.index') }}" method="GET" class="flex flex-wrap gap-4">
                            <div>
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos los estados</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponibles</option>
                                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Ocupadas</option>
                                    <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reservadas</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>En mantenimiento</option>
                                </select>
                            </div>

                            <div>
                                <input type="text" name="search" placeholder="Buscar por número o capacidad..." value="{{ request('search') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-search mr-1"></i> Filtrar
                                </button>
                            </div>

                            @if(request()->hasAny(['status', 'search']))
                                <div>
                                    <a href="{{ route('tables.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-times mr-1"></i> Limpiar
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>

                    <!-- Listado de mesas en tarjetas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse($tables as $table)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden border-t-4 border-{{ $table->status_color }}-500">
                                <div class="p-5">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-bold">Mesa {{ $table->number }}</h3>
                                            <p class="text-sm text-gray-600">Capacidad: {{ $table->capacity }} personas</p>
                                            <div class="mt-2">
                                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $table->status_color }}-100 text-{{ $table->status_color }}-800">
                                                    {{ $table->status_text }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col space-y-1">
                                            <a href="{{ route('tables.show', $table) }}" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tables.edit', $table) }}" class="text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tables.destroy', $table) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta mesa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Acciones rápidas -->
                                    <div class="mt-4 pt-3 border-t border-gray-100">
                                        <form action="{{ route('tables.update-status', $table) }}" method="POST" class="flex gap-1">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Disponible</option>
                                                <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Ocupada</option>
                                                <option value="reserved" {{ $table->status == 'reserved' ? 'selected' : '' }}>Reservada</option>
                                                <option value="maintenance" {{ $table->status == 'maintenance' ? 'selected' : '' }}>Mantenimiento</option>
                                            </select>
                                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-700 text-white text-xs font-bold py-1 px-2 rounded">
                                                Actualizar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="text-gray-500 mb-4">
                                    <i class="fas fa-chair text-5xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No hay mesas disponibles</h3>
                                <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva mesa para tu restaurante.</p>
                                <div class="mt-6">
                                    <a href="{{ route('tables.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-800 transition ease-in-out duration-150">
                                        <i class="fas fa-plus mr-2"></i> Crear mesa
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $tables->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>