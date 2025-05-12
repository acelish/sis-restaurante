<!-- filepath: resources\views\client\cart\confirmation.blade.php -->
@extends('layouts.client')

@section('title', 'Pedido Confirmado - Restaurante')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-4">
                <i class="fas fa-check text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">¡Pedido Confirmado!</h1>
            <p class="text-gray-600">Gracias por tu pedido. Hemos recibido tu solicitud correctamente.</p>
        </div>
        
        <!-- Número y detalles del pedido -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Pedido #{{ $order->id }}</h2>
                    <div class="text-sm">
                        <span class="text-gray-500">Fecha:</span> 
                        <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Detalles del Cliente</h3>
                        <div class="text-gray-700">
                            <p class="font-medium">{{ $order->customer_name }}</p>
                            @if($order->customer_email)
                                <p>{{ $order->customer_email }}</p>
                            @endif
                            <p>{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Detalles del Pedido</h3>
                        <div class="text-gray-700">
                            <p>
                                <span class="font-medium">Tipo:</span>
                                @if($order->order_type == 'dine_in')
                                    Comer en el local
                                    @if($order->table)
                                        (Mesa {{ $order->table->number }})
                                    @endif
                                @elseif($order->order_type == 'takeaway')
                                    Para llevar
                                @elseif($order->order_type == 'delivery')
                                    Entrega a domicilio
                                @endif
                            </p>
                            <p>
                                <span class="font-medium">Estado:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendiente
                                </span>
                            </p>
                            <p>
                                <span class="font-medium">Pago:</span>
                                {{ $order->payment_method == 'cash' ? 'Efectivo' : 'Tarjeta' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                @if($order->delivery_address)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Dirección de Entrega</h3>
                        <p class="text-gray-700">{{ $order->delivery_address }}</p>
                    </div>
                @endif
                
                @if($order->notes)
                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Notas</h3>
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
            
            <!-- Lista de productos -->
            <div class="p-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-4">Productos</h3>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <div class="py-3 flex justify-between">
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $item->quantity }}x {{ $item->product->name }}
                                </p>
                                @if($item->special_instructions)
                                    <p class="text-sm text-gray-500 italic">{{ $item->special_instructions }}</p>
                                @endif
                            </div>
                            <p class="font-medium">${{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Impuestos</span>
                        <span>${{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-gray-800 text-lg pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="bg-yellow-50 rounded-lg p-6 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Información importante</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Tu pedido ha sido recibido y pronto comenzaremos a prepararlo.</p>
                        <p class="mt-1">
                            @if($order->order_type == 'takeaway')
                                Por favor, dirígete al restaurante en aproximadamente 30 minutos para recoger tu pedido.
                            @elseif($order->order_type == 'delivery')
                                El tiempo estimado de entrega es de 45-60 minutos, dependiendo de tu ubicación.
                            @else
                                Tu mesa estará lista en aproximadamente 15 minutos.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex flex-col md:flex-row justify-center space-y-3 md:space-y-0 md:space-x-4">
            <a href="{{ route('menu') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                <i class="fas fa-utensils mr-2"></i> Volver al Menú
            </a>
            
            <button onclick="window.print()" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-print mr-2"></i> Imprimir Comprobante
            </button>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style media="print">
    header, footer, nav, .print-hide {
        display: none !important;
    }
    body {
        background-color: white !important;
    }
</style>
@endsection