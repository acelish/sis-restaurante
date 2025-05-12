<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mapa de Mesas') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tables.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-list mr-1"></i> Ver Listado
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

                    <!-- Leyenda -->
                    <div class="mb-6 flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded-full mr-2"></div>
                            <span>Disponible</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                            <span>Ocupada</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                            <span>Reservada</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-500 rounded-full mr-2"></div>
                            <span>Mantenimiento</span>
                        </div>
                    </div>

                    <!-- Mapa de mesas interactivo -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach($tables as $table)
                            <div class="group">
                                <div class="relative aspect-square flex items-center justify-center rounded-full border-4 border-{{ $table->status_color }}-500 bg-{{ $table->status_color }}-100 hover:bg-{{ $table->status_color }}-200 transition-all cursor-pointer">
                                    <div class="text-center">
                                        <div class="text-xl font-bold">{{ $table->number }}</div>
                                        <div class="text-sm">{{ $table->capacity }} <i class="fas fa-user-friends"></i></div>
                                    </div>
                                    
                                    <!-- Menú de acciones al pasar el cursor -->
                                    <div class="absolute inset-0 bg-black bg-opacity-70 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('tables.show', $table) }}" class="bg-blue-500 hover:bg-blue-700 text-white p-2 rounded-full">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tables.edit', $table) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white p-2 rounded-full">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('orders.create', ['table_id' => $table->id]) }}" class="bg-green-500 hover:bg-green-700 text-white p-2 rounded-full">
                                                <i class="fas fa-utensils"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-1 text-sm font-medium text-{{ $table->status_color }}-800">
                                    {{ $table->status_text }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>