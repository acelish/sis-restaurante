<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Mesa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tables.store') }}" class="max-w-xl mx-auto">
                        @csrf

                        <!-- Número de Mesa -->
                        <div class="mb-6">
                            <x-input-label for="number" :value="__('Número de Mesa')" />
                            <x-text-input id="number" class="block mt-1 w-full" type="text" name="number" :value="old('number')" required autofocus />
                            <x-input-error :messages="$errors->get('number')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Puede ser un número o un identificador único como "A1", "Terraza 3", etc.</p>
                        </div>

                        <!-- Capacidad -->
                        <div class="mb-6">
                            <x-input-label for="capacity" :value="__('Capacidad (personas)')" />
                            <x-text-input id="capacity" class="block mt-1 w-full" type="number" name="capacity" :value="old('capacity', 4)" min="1" max="99" required />
                            <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
                        </div>

                        <!-- Estado -->
                        <div class="mb-6">
                            <x-input-label for="status" :value="__('Estado')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Ocupada</option>
                                <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reservada</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>En mantenimiento</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('tables.index') }}" class="text-gray-600 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Mesa') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>