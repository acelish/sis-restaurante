<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Item de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('inventory.update', $inventory) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" 
                                class="mt-1 block w-full" 
                                :value="old('name', $inventory->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="unit" value="Unidad" />
                            <x-text-input id="unit" name="unit" type="text" 
                                class="mt-1 block w-full" 
                                :value="old('unit', $inventory->unit)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('unit')" />
                        </div>

                        <div>
                            <x-input-label for="quantity" value="Cantidad" />
                            <x-text-input id="quantity" name="quantity" type="number" 
                                step="0.01" class="mt-1 block w-full" 
                                :value="old('quantity', $inventory->quantity)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        </div>

                        <div>
                            <x-input-label for="alert_threshold" value="Umbral de Alerta" />
                            <x-text-input id="alert_threshold" name="alert_threshold" 
                                type="number" step="0.01" class="mt-1 block w-full" 
                                :value="old('alert_threshold', $inventory->alert_threshold)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('alert_threshold')" />
                        </div>

                        <div>
                            <x-input-label for="cost" value="Costo" />
                            <x-text-input id="cost" name="cost" type="number" 
                                step="0.01" class="mt-1 block w-full" 
                                :value="old('cost', $inventory->cost)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('cost')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Actualizar') }}</x-primary-button>
                            <a href="{{ route('inventory.index') }}" class="text-gray-600">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>