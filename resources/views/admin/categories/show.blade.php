<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de Categoría') }}: {{ $category->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('categories.edit', $category) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> Editar
                </a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Información de la Categoría</h3>
                            <dl class="grid grid-cols-3 gap-4">
                                <div class="col-span-1 font-medium">Nombre:</div>
                                <div class="col-span-2">{{ $category->name }}</div>
                                
                                <div class="col-span-1 font-medium">Descripción:</div>
                                <div class="col-span-2">{{ $category->description ?: 'Sin descripción' }}</div>
                                
                                <div class="col-span-1 font-medium">Orden:</div>
                                <div class="col-span-2">{{ $category->order }}</div>
                                
                                <div class="col-span-1 font-medium">Creado:</div>
                                <div class="col-span-2">{{ $category->created_at->format('d/m/Y H:i') }}</div>
                                
                                <div class="col-span-1 font-medium">Actualizado:</div>
                                <div class="col-span-2">{{ $category->updated_at->format('d/m/Y H:i') }}</div>
                                
                                <div class="col-span-1 font-medium">Total de productos:</div>
                                <div class="col-span-2">{{ $category->products->count() }}</div>
                            </dl>
                        </div>
                        
                        <div>
                            @if($category->image)
                                <h3 class="text-lg font-semibold mb-4">Imagen</h3>
                                <div class="w-full h-64 bg-gray-100 rounded-md overflow-hidden">
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <h3 class="text-lg font-semibold mb-4">Imagen</h3>
                                <div class="w-full h-64 bg-gray-100 rounded-md flex items-center justify-center">
                                    <div class="text-center text-gray-500">
                                        <i class="fas fa-image text-4xl mb-2"></i>
                                        <p>No hay imagen disponible</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Productos en esta Categoría</h3>
                        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green active:bg-green-800 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-1"></i> Nuevo Producto
                        </a>
                    </div>

                    @if($category->products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($category->products as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 object-cover rounded">
                                                @else
                                                    <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                                        <i class="fas fa-utensils text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($product->price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($product->is_available)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Disponible
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        No disponible
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 mb-4">
                                <i class="fas fa-coffee text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">No hay productos en esta categoría</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza agregando productos a esta categoría</p>
                            <div class="mt-6">
                                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-800 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> Crear producto
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>