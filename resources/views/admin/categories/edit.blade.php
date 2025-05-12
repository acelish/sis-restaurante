<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Categoría') }}: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data" class="max-w-2xl mx-auto">
                        @csrf
                        @method('PATCH')

                        <!-- Nombre -->
                        <div class="mb-6">
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $category->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Orden -->
                        <div class="mb-6">
                            <x-input-label for="order" :value="__('Orden de visualización')" />
                            <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', $category->order)" min="0" />
                            <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            <p class="text-sm text-gray-500 mt-1">Un número más bajo aparecerá primero en la lista.</p>
                        </div>

                        <!-- Imagen Actual -->
                        @if($category->image)
                            <div class="mb-6">
                                <x-input-label :value="__('Imagen Actual')" />
                                <div class="mt-1 w-full h-48 bg-gray-100 rounded-md flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="max-h-full" />
                                </div>
                                <div class="mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="delete_image" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                                        <span class="ml-2 text-sm text-gray-600">Eliminar imagen actual</span>
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- Nueva Imagen -->
                        <div class="mb-6">
                            <x-input-label for="image" :value="__('Nueva Imagen')" />
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
                            <p class="text-sm text-gray-500 mt-1">Deja este campo vacío si no deseas cambiar la imagen.</p>
                        </div>

                        <!-- Previsualización de nueva imagen -->
                        <div class="mb-6 hidden" id="image-preview-container">
                            <x-input-label :value="__('Previsualización')" />
                            <div class="mt-1 w-full h-48 bg-gray-100 rounded-md flex items-center justify-center overflow-hidden">
                                <img id="image-preview" class="max-h-full" src="#" alt="Previsualización" />
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-between mt-8">
                            <div>
                                <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                    <i class="fas fa-eye mr-2"></i> Ver Detalles
                                </a>
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ route('categories.index') }}" class="text-gray-600">
                                    Cancelar
                                </a>
                                <x-primary-button>
                                    {{ __('Actualizar Categoría') }}
                                </x-primary-button>
                            </div>
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