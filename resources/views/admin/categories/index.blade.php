<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Categorías') }}
            </h2>
            <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-green font-bold py-2 px-4 rounded shadow-md transition duration-150 ease-in-out transform hover:scale-105">
                <i class="fas fa-plus mr-1"></i> Nueva Categoría
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p>{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(count($categories) > 0)
                        <!-- Contenedor principal con flexbox para mejor centrado -->
                        <div class="flex flex-wrap justify-center sm:justify-start gap-6">
                            @foreach($categories as $category)
                                <!-- Tarjeta con ancho fijo para mantener consistencia -->
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100 transition duration-300 ease-in-out transform hover:shadow-lg hover:-translate-y-1 w-full sm:w-64 md:w-72 lg:w-64 xl:w-72">
                                    <div class="relative bg-gray-50">
                                        <!-- Contenedor de imagen con altura fija -->
                                        @if($category->image)
                                            <div class="h-40 overflow-hidden">
                                                <img 
                                                    class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-110" 
                                                    src="{{ $category->image_url ?? asset('storage/' . $category->image) }}" 
                                                    alt="{{ $category->name }}"
                                                    loading="lazy"
                                                >
                                            </div>
                                        @else
                                            <div class="w-full h-40 bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                                <i class="fas fa-utensils text-4xl text-gray-400"></i>
                                            </div>
                                        @endif
                                        
                                        <!-- Botones de acción con un fondo más sutil -->
                                        <div class="absolute top-2 right-2 flex space-x-1">
                                            <a href="{{ route('categories.edit', $category) }}" 
                                               class="bg-white bg-opacity-80 text-blue-600 p-2 rounded-full shadow hover:bg-blue-500 hover:text-white transition-colors duration-200"
                                               title="Editar categoría">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" 
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="bg-white bg-opacity-80 text-red-600 p-2 rounded-full shadow hover:bg-red-500 hover:text-white transition-colors duration-200"
                                                        title="Eliminar categoría">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenido con altura fija para mantener uniformidad -->
                                    <div class="p-4 h-[140px] flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold mb-2 text-gray-800 truncate">{{ $category->name }}</h3>
                                            
                                            <!-- Descripción con truncado elegante -->
                                            <div class="mb-3 h-12 overflow-hidden">
                                                <p class="text-gray-600 text-sm line-clamp-2">
                                                    {{ $category->description ?: 'Sin descripción' }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                            <span class="text-sm bg-blue-100 text-blue-800 py-1 px-2 rounded-full">
                                                <i class="fas fa-tag mr-1"></i> {{ $category->products_count ?? $category->products->count() }} productos
                                            </span>
                                            <a href="{{ route('categories.show', $category) }}" 
                                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
                                                Ver
                                                <i class="fas fa-chevron-right ml-1 text-xs"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginación con estilo mejorado -->
                        <div class="mt-8">
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-folder-open text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No hay categorías disponibles</h3>
                            <p class="mt-1 text-sm text-gray-500 max-w-md mx-auto">
                                Las categorías te permiten organizar tus productos para que los clientes puedan encontrarlos fácilmente.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('categories.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> Crear categoría
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>