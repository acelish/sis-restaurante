@extends('layouts.client')

@section('title', 'Reservación Confirmada - Restaurante')

@section('content')
<div class="bg-white py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center justify-center p-4 bg-green-100 rounded-full mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-bold text-gray-900 mb-4">¡Reservación Recibida!</h1>
            <p class="text-lg text-gray-600 mb-8">Gracias por elegir nuestro restaurante. Hemos recibido tu solicitud de reservación.</p>
            
            <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 mb-8">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Detalles de tu reservación</h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Nombre</p>
                            <p class="text-lg font-medium text-gray-800">{{ $reservation->customer_name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Número de personas</p>
                            <p class="text-lg font-medium text-gray-800">{{ $reservation->num_guests }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Fecha</p>
                            <p class="text-lg font-medium text-gray-800">{{ $reservation->reservation_time->format('d/m/Y') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Hora</p>
                            <p class="text-lg font-medium text-gray-800">{{ $reservation->reservation_time->format('h:i A') }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Estado</p>
                            <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pendiente de confirmación
                            </p>
                        </div>
                        
                        @if($reservation->special_requests)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Solicitudes especiales</p>
                            <p class="text-gray-800">{{ $reservation->special_requests }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="bg-gray-50 p-6 border-t border-gray-200">
                    <h3 class="font-medium text-gray-900 mb-2">¿Qué sigue?</h3>
                    <p class="text-gray-600 mb-4">Recibirás un correo electrónico con la confirmación de tu reserva. Un miembro de nuestro equipo se pondrá en contacto contigo pronto para confirmar los detalles.</p>
                    
                    <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4 mt-2">
                        <a href="{{ route('menu') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                            Ver nuestro menú
                        </a>
                        
                        <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-gray-600 mb-2">¿Tienes preguntas sobre tu reservación?</p>
                <a href="#" class="inline-flex items-center text-red-600 hover:text-red-800 font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Contactar al restaurante
                </a>
            </div>
        </div>
    </div>
</div>
@endsection