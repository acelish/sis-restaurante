<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles de la Orden') }} #{{ $order->id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('orders.edit', $order) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-1"></i> Editar Orden
                </a>
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <div class="flex">
                        <select name="status" class="rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>En preparación</option>
                            <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Listo</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregado</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-r-md">
                            Actualizar Estado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Información de la Orden</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="font-semibold">Cliente:</dt>
                                    <dd>{{ $order->user ? $order->user->name : ($order->customer_name ?? 'Cliente anónimo') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Empleado:</dt>
                                    <dd>{{ $order->employee ? $order->employee->name : 'No asignado' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Mesa:</dt>
                                    <dd>{{ $order->table ? 'Mesa ' . $order->table->number : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Tipo de Orden:</dt>
                                    <dd>{{ $order->order_type_text }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Estado:</dt>
                                    <dd>
                                        @if($order->status == 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        @elseif($order->status == 'preparing')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">En preparación</span>
                                        @elseif($order->status == 'ready')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Listo</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Entregado</span>
                                        @elseif($order->status == 'completed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completado</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelado</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Pago:</dt>
                                    <dd>
                                        @if($order->payment_status == 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                        @elseif($order->payment_status == 'paid')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Pagado</span>
                                        @elseif($order->payment_status == 'refunded')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Reembolsado</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Método de Pago:</dt>
                                    <dd>{{ $order->payment_method ?? 'No especificado' }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold">Fecha de Creación:</dt>
                                    <dd>{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>

                            @if($order->notes)
                                <div class="mt-4">
                                    <h4 class="font-semibold">Notas:</h4>
                                    <p class="mt-1">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold mb-2">Resumen de la Orden</h4>
                            <div class="flex justify-between py-2 border-b">
                                <span>Subtotal:</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span>Impuesto (16%):</span>
                                <span>${{ number_format($order->tax, 2) }}</span>
                            </div>
                            @if($order->discount > 0)
                                <div class="flex justify-between py-2 border-b">
                                    <span>Descuento:</span>
                                    <span>-${{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between py-2 font-bold text-lg">
                                <span>Total:</span>
                                <span>${{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Productos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unitario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instrucciones Especiales</th>
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
                                                    <div class="font-medium text-gray-900">
                                                        {{ $item->product ? $item->product->name : 'Producto no disponible' }}
                                                    </div>
                                                    @if($item->product && $item->product->category)
                                                        <div class="text-xs text-gray-500">
                                                            {{ $item->product->category->name }}
                                                        </div>
                                                    @endif
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
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>