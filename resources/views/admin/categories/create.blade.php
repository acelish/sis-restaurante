<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Categoría') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data" class="max-w-2xl mx-auto">
                        @csrf

                        <!-- Nombre -->
                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Orden -->
                        <div class="mb-6">
                            <x-input-label for="order" :value="__('Orden de visualización')" />
                            <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', 0)" min="0" />
                            <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Un número más bajo aparecerá primero en la lista. Predeterminado: 0</p>
                        </div>

                        <!-- Imagen -->
                        <div class="mb-6">
                            <x-input-label for="image" :value="__('Imagen')" />
                            <div class="mt-1 flex items-center">
                                <label class="block w-full">
                                    <span class="sr-only">Seleccionar imagen</span>
                                    <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100
                                    "/>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Formato recomendado: JPG o PNG, Max 2MB. Tamaño recomendado: 800x600px</p>
                        </div>

                        <!-- Previsualización de imagen -->
                        <div class="mb-6 hidden" id="image-preview-container">
                            <x-input-label :value="__('Previsualización')" />
                            <div class="mt-1 w-full h-48 bg-gray-100 rounded-md flex items-center justify-center overflow-hidden">
                                <img id="image-preview" class="max-h-full" src="#" alt="Previsualización" />
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end mt-8">
                            <a href="{{ route('categories.index') }}" class="text-gray-600 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Categoría') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Previsualización de imagen
        document.getElementById('image').onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const preview = document.getElementById('image-preview');
                const container = document.getElementById('image-preview-container');
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                }
                
                reader.readAsDataURL(file);
            }
        };
    </script>
</x-app-layout>