<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Orden') }} #{{ $order->id }}
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
                    <form method="POST" action="{{ route('orders.update', $order) }}" id="orderForm" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nombre del Cliente (opcional para clientes no registrados) -->
                            <div>
                                <x-input-label for="customer_name" value="Nombre del Cliente" />
                                <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full" :value="old('customer_name', $order->customer_name)" />
                                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                            </div>

                            <!-- Empleado que atiende -->
                            <div>
                                <x-input-label for="employee_id" value="Empleado que atiende" />
                                <select id="employee_id" name="employee_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Seleccionar empleado</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $order->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <!-- Estado de la Orden -->
                            <div>
                                <x-input-label for="status" value="Estado de la Orden" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="preparing" {{ old('status', $order->status) == 'preparing' ? 'selected' : '' }}>En preparación</option>
                                    <option value="ready" {{ old('status', $order->status) == 'ready' ? 'selected' : '' }}>Listo</option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>Entregado</option>
                                    <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completado</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Estado de Pago -->
                            <div>
                                <x-input-label for="payment_status" value="Estado de Pago" />
                                <select id="payment_status" name="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="pending" {{ old('payment_status', $order->payment_status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Pagado</option>
                                    <option value="refunded" {{ old('payment_status', $order->payment_status) == 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_status')" class="mt-2" />
                            </div>

                            <!-- Método de Pago (visible si está pagado) -->
                            <div id="payment_method_container">
                                <x-input-label for="payment_method" value="Método de Pago" />
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Seleccionar método</option>
                                    <option value="efectivo" {{ old('payment_method', $order->payment_method) == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="tarjeta" {{ old('payment_method', $order->payment_method) == 'tarjeta' ? 'selected' : '' }}>Tarjeta de Crédito/Débito</option>
                                    <option value="transferencia" {{ old('payment_method', $order->payment_method) == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Notas -->
                        <div>
                            <x-input-label for="notes" value="Notas Adicionales" />
                            <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $order->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- Productos de la orden (sólo visualización) -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Productos en la Orden</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instrucciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        @if($item->product && $item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="h-10 w-10 object-cover rounded-full mr-3">
                                                        @endif
                                                        <div>
                                                            {{ $item->product ? $item->product->name : 'Producto no disponible' }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($item->unit_price, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($item->subtotal, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->special_instructions ?? 'Sin instrucciones' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right font-bold">Subtotal:</td>
                                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($order->subtotal, 2) }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right font-bold">Impuesto:</td>
                                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($order->tax, 2) }}</td>
                                            <td></td>
                                        </tr>
                                        @if($order->discount > 0)
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right font-bold">Descuento:</td>
                                            <td class="px-6 py-4 whitespace-nowrap">-${{ number_format($order->discount, 2) }}</td>
                                            <td></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
                                            <td class="px-6 py-4 whitespace-nowrap font-bold">${{ number_format($order->total, 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Para modificar los productos de la orden, debes crear una nueva orden.
                            </p>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Actualizar Orden') }}</x-primary-button>
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray active:bg-gray-900 transition ease-in-out duration-150">
                                Ver Detalles
                            </a>
                            <a href="{{ route('orders.index') }}" class="text-gray-600">Volver al listado</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            
            // Mostrar advertencia al cambiar a estado "cancelado"
            const statusSelect = document.getElementById('status');
            statusSelect.addEventListener('change', function() {
                if (this.value === 'cancelled' && '{{ $order->status }}' !== 'cancelled') {
                    if (!confirm('Al cancelar la orden se revertirán los cambios en el inventario. ¿Estás seguro?')) {
                        this.value = '{{ $order->status }}';
                    }
                }
            });
        });
    </script>
</x-app-layout>