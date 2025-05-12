<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Orden') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('orders.store') }}" id="orderForm" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tipo de Orden -->
                            <div>
                                <x-input-label for="order_type" value="Tipo de Orden" />
                                <select id="order_type" name="order_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccionar tipo de orden</option>
                                    <option value="dine_in" {{ old('order_type') == 'dine_in' ? 'selected' : '' }}>En restaurante</option>
                                    <option value="takeaway" {{ old('order_type') == 'takeaway' ? 'selected' : '' }}>Para llevar</option>
                                    <option value="delivery" {{ old('order_type') == 'delivery' ? 'selected' : '' }}>Entrega a domicilio</option>
                                    <option value="online" {{ old('order_type') == 'online' ? 'selected' : '' }}>Pedido online</option>
                                </select>
                                <x-input-error :messages="$errors->get('order_type')" class="mt-2" />
                            </div>

                            <!-- Nombre del Cliente (opcional para clientes no registrados) -->
                            <div>
                                <x-input-label for="customer_name" value="Nombre del Cliente" />
                                <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full" :value="old('customer_name')" />
                                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                            </div>

                            <!-- Mesa (si es en restaurante) -->
                            <div id="table_container">
                                <x-input-label for="table_id" value="Mesa" />
                                <select id="table_id" name="table_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Seleccionar mesa</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                            Mesa {{ $table->number }} ({{ $table->capacity }} personas)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('table_id')" class="mt-2" />
                            </div>

                            <!-- Empleado que atiende -->
                            <div>
                                <x-input-label for="employee_id" value="Empleado que atiende" />
                                <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Seleccionar empleado</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <!-- Estado de Pago -->
                            <div>
                                <x-input-label for="payment_status" value="Estado de Pago" />
                                <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Pagado</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                            </div>

                            <!-- Método de Pago (visible si está pagado) -->
                            <div id="payment_method_container">
                                <x-input-label for="payment_method" value="Método de Pago" />
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Seleccionar método</option>
                                    <option value="efectivo" {{ old('payment_method') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="tarjeta" {{ old('payment_method') == 'tarjeta' ? 'selected' : '' }}>Tarjeta de Crédito/Débito</option>
                                    <option value="transferencia" {{ old('payment_method') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Notas -->
                        <div>
                            <x-input-label for="notes" value="Notas Adicionales" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Productos -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Productos</h3>
                            
                            <!-- Contenedor para la búsqueda de productos -->
                            <div class="mb-4">
                                <x-input-label for="search_product" value="Buscar Producto" />
                                <div class="flex">
                                    <select id="search_product" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Seleccionar producto</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-name="{{ $product->name }}">
                                                {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="add_product" class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Tabla de productos seleccionados -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instrucciones</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selected_products" class="bg-white divide-y divide-gray-200">
                                        <!-- Los productos se agregarán aquí con JS -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
                                            <td class="px-6 py-4 whitespace-nowrap font-bold" id="total_amount">$0.00</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button id="submitOrder">{{ __('Crear Orden') }}</x-primary-button>
                            <a href="{{ route('orders.index') }}" class="text-gray-600">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Control de visibilidad de campos según el tipo de orden
            const orderTypeSelect = document.getElementById('order_type');
            const tableContainer = document.getElementById('table_container');
            
            function updateFormBasedOnOrderType() {
                if (orderTypeSelect.value === 'dine_in') {
                    tableContainer.style.display = 'block';
                } else {
                    tableContainer.style.display = 'none';
                    document.getElementById('table_id').value = '';
                }
            }
            
            orderTypeSelect.addEventListener('change', updateFormBasedOnOrderType);
            updateFormBasedOnOrderType();
            
            // Control de visibilidad del método de pago
            const paymentStatusSelect = document.getElementById('payment_status');
            const paymentMethodContainer = document.getElementById('payment_method_container');
            
            function updatePaymentMethodVisibility() {
                if (paymentStatusSelect.value === 'paid') {
                    paymentMethodContainer.style.display = 'block';
                } else {
                    paymentMethodContainer.style.display = 'none';
                    document.getElementById('payment_method').value = '';
                }
            }
            
            paymentStatusSelect.addEventListener('change', updatePaymentMethodVisibility);
            updatePaymentMethodVisibility();
            
            // Manejo de productos
            let selectedProducts = [];
            let productIndex = 0;
            
            const searchProductSelect = document.getElementById('search_product');
            const addProductBtn = document.getElementById('add_product');
            const selectedProductsTable = document.getElementById('selected_products');
            const totalAmountElement = document.getElementById('total_amount');
            
            // Agregar producto a la orden
            addProductBtn.addEventListener('click', function() {
                const productId = searchProductSelect.value;
                if (!productId) return;
                
                const productOption = searchProductSelect.options[searchProductSelect.selectedIndex];
                const productName = productOption.dataset.name;
                const productPrice = parseFloat(productOption.dataset.price);
                
                // Verificar si el producto ya está en la lista
                const existingProduct = selectedProducts.find(p => p.id === productId);
                if (existingProduct) {
                    existingProduct.quantity += 1;
                    updateProductTable();
                } else {
                    // Agregar nuevo producto
                    selectedProducts.push({
                        index: productIndex++,
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 1,
                        specialInstructions: ''
                    });
                    
                    updateProductTable();
                }
            });
            
            // Actualizar la tabla de productos
            function updateProductTable() {
                selectedProductsTable.innerHTML = '';
                let total = 0;
                
                selectedProducts.forEach((product, index) => {
                    const subtotal = product.price * product.quantity;
                    total += subtotal;
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap">
                            ${product.name}
                            <input type="hidden" name="products[${product.index}][id]" value="${product.id}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">$${product.price.toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="number" name="products[${product.index}][quantity]" value="${product.quantity}" min="1" class="w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="updateProductQuantity(${index}, this.value)">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">$${subtotal.toFixed(2)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="text" name="products[${product.index}][special_instructions]" value="${product.specialInstructions}" placeholder="Instrucciones especiales" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="updateSpecialInstructions(${index}, this.value)">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button type="button" class="text-red-600 hover:text-red-900" onclick="removeProduct(${index})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    `;
                    
                    selectedProductsTable.appendChild(row);
                });
                
                totalAmountElement.textContent = `$${total.toFixed(2)}`;
            }
            
            // Funciones para manipular productos (deben estar en el ámbito global)
            window.updateProductQuantity = function(index, quantity) {
                selectedProducts[index].quantity = parseInt(quantity, 10);
                updateProductTable();
            };
            
            window.updateSpecialInstructions = function(index, instructions) {
                selectedProducts[index].specialInstructions = instructions;
            };
            
            window.removeProduct = function(index) {
                selectedProducts.splice(index, 1);
                updateProductTable();
            };
            
            // Validación antes de enviar el formulario
            document.getElementById('orderForm').addEventListener('submit', function(e) {
                if (selectedProducts.length === 0) {
                    e.preventDefault();
                    alert('Por favor, agrega al menos un producto a la orden.');
                }
            });
        });
    </script>
</x-app-layout>