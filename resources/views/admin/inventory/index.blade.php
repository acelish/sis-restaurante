<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Gestión de Inventario') }}
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('inventory.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-1"></i> Nuevo Item
                </a>
                <a href="{{ route('inventory.low-stock') }}" class="bg-amber-500 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Stock Bajo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtros y búsqueda -->
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex space-x-2">
                            <div>
                                <select id="filter-status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Todos los estados</option>
                                    <option value="low">Stock Bajo</option>
                                    <option value="normal">Normal</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <input type="text" id="search" placeholder="Buscar item..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->isLowStock())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Stock Bajo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('inventory.show', $item) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ route('inventory.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    onclick="return confirm('¿Estás seguro de eliminar este item?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Opciones de stock -->
                                    <div class="mt-2 flex space-x-2">
                                        <button type="button" class="text-green-600 hover:text-green-900 text-xs" 
                                                onclick="openAddStockModal({{ $item->id }})">
                                            <i class="fas fa-plus-circle"></i> Agregar Stock
                                        </button>
                                        <button type="button" class="text-amber-600 hover:text-amber-900 text-xs"
                                                onclick="openRemoveStockModal({{ $item->id }})">
                                            <i class="fas fa-minus-circle"></i> Reducir Stock
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para agregar stock -->
    <div id="addStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Agregar Stock</h3>
                <form id="addStockForm" method="POST" class="mt-2 text-left">
                    @csrf
                    <input type="hidden" id="addStockItemId" name="item_id">
                    
                    <div class="mt-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad a agregar</label>
                        <input type="number" name="quantity" id="addQuantity" min="0.01" step="0.01" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    
                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea name="notes" id="addNotes" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                    </div>
                    
                    <div class="mt-4 flex justify-between">
                        <button type="button" onclick="closeAddStockModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para reducir stock -->
    <div id="removeStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reducir Stock</h3>
                <form id="removeStockForm" method="POST" class="mt-2 text-left">
                    @csrf
                    <input type="hidden" id="removeStockItemId" name="item_id">
                    
                    <div class="mt-4">
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Cantidad a reducir</label>
                        <input type="number" name="quantity" id="removeQuantity" min="0.01" step="0.01" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    
                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notas</label>
                        <textarea name="notes" id="removeNotes" rows="2"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                    </div>
                    
                    <div class="mt-4 flex justify-between">
                        <button type="button" onclick="closeRemoveStockModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reducir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript para los modales y funcionalidad -->
    <script>
        // Funciones para el modal de agregar stock
        function openAddStockModal(itemId) {
            document.getElementById('addStockItemId').value = itemId;
            document.getElementById('addStockModal').classList.remove('hidden');
            document.getElementById('addStockForm').action = `/admin/inventory/${itemId}/add-stock`;
        }
        
        function closeAddStockModal() {
            document.getElementById('addStockModal').classList.add('hidden');
        }
        
        // Funciones para el modal de reducir stock
        function openRemoveStockModal(itemId) {
            document.getElementById('removeStockItemId').value = itemId;
            document.getElementById('removeStockModal').classList.remove('hidden');
            document.getElementById('removeStockForm').action = `/admin/inventory/${itemId}/remove-stock`;
        }
        
        function closeRemoveStockModal() {
            document.getElementById('removeStockModal').classList.add('hidden');
        }
        
        // Cerrar modales si se hace clic fuera de ellos
        window.onclick = function(event) {
            let addModal = document.getElementById('addStockModal');
            let removeModal = document.getElementById('removeStockModal');
            
            if (event.target == addModal) {
                closeAddStockModal();
            }
            
            if (event.target == removeModal) {
                closeRemoveStockModal();
            }
        }
    </script>
</x-app-layout>