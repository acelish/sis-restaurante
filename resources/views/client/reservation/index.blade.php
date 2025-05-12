@extends('layouts.client')

@section('title', 'Reservaciones - Restaurante')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .date-card {
        transition: all 0.3s ease;
    }
    .date-card:hover {
        transform: translateY(-5px);
    }
    .date-card.selected {
        border-color: #EF4444;
        background-color: #FEF2F2;
    }
    .time-btn {
        transition: all 0.2s ease;
    }
    .time-btn:hover {
        background-color: #FEF2F2;
        border-color: #EF4444;
    }
    .time-btn.selected {
        background-color: #EF4444;
        color: white;
        border-color: #EF4444;
    }
    .reservation-card {
        transition: transform 0.5s ease;
    }
    .reservation-steps .step {
        position: relative;
    }
    .reservation-steps .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 15px;
        right: -50%;
        width: 100%;
        height: 2px;
        background-color: #E5E7EB;
        z-index: 0;
    }
    .reservation-steps .step.active:not(:last-child)::after {
        background-color: #EF4444;
    }
    .reservation-steps .step .step-circle {
        z-index: 1;
        position: relative;
    }
    .step.active .step-circle {
        background-color: #EF4444;
        color: white;
    }
    .step.active .step-text {
        color: #EF4444;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="bg-white py-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Encabezado -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Reserva tu Mesa</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Disfruta de una experiencia culinaria excepcional reservando con anticipación y asegurando el mejor lugar en nuestro restaurante.
                </p>
            </div>
            
            <!-- Pasos de la reservación -->
            <div class="flex justify-between mb-16 reservation-steps">
                <div class="step active flex-1 text-center">
                    <div class="step-circle mx-auto flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white text-sm font-bold mb-2">1</div>
                    <div class="step-text text-sm font-medium text-red-600">Seleccionar fecha y hora</div>
                </div>
                <div class="step flex-1 text-center" id="step2">
                    <div class="step-circle mx-auto flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-sm font-bold mb-2">2</div>
                    <div class="step-text text-sm font-medium text-gray-500">Verificar disponibilidad</div>
                </div>
                <div class="step flex-1 text-center" id="step3">
                    <div class="step-circle mx-auto flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-sm font-bold mb-2">3</div>
                    <div class="step-text text-sm font-medium text-gray-500">Confirmar reservación</div>
                </div>
            </div>

            <!-- Tarjeta de reservación principal -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden reservation-card" id="reservation-card">
                <!-- Paso 1: Seleccionar fecha y hora -->
                <div id="step1-content" class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Selecciona la fecha y hora</h2>
                    
                    <form id="availability-form" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="reservation-date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de reserva</label>
                                <input type="date" id="reservation-date" name="date" min="{{ $today }}" max="{{ $maxDate }}" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" required>
                                <div class="text-xs text-gray-500 mt-1">Puedes reservar con hasta 2 meses de anticipación</div>
                            </div>
                            
                            <div>
                                <label for="reservation-time" class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                                <select id="reservation-time" name="time" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50" required>
                                    <option value="">Selecciona una hora</option>
                                    @foreach($availableTimes as $time)
                                        <option value="{{ $time }}">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('h:i A') }}</option>
                                    @endforeach
                                </select>
                                <div class="text-xs text-gray-500 mt-1">Horario de reservaciones: 12:00 PM - 9:00 PM</div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="guests" class="block text-sm font-medium text-gray-700 mb-2">Número de personas</label>
                            <div class="flex items-center">
                                <button type="button" id="decrease-guests" class="p-2 border border-gray-300 rounded-l-md bg-gray-50 text-gray-500 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <input type="number" id="guests" name="guests" min="1" max="20" value="2" 
                                    class="w-20 text-center border-y border-gray-300 focus:ring-0 focus:outline-none" readonly>
                                <button type="button" id="increase-guests" class="p-2 border border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" id="check-availability" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-md transition-colors flex justify-center items-center">
                                <span>Verificar disponibilidad</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Paso 2: Verificar disponibilidad -->
                <div id="step2-content" class="p-8 hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Disponibilidad</h2>
                        <button id="back-to-step1" class="text-gray-600 hover:text-gray-800 flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Volver a selección
                        </button>
                    </div>
                    
                    <div id="availability-results" class="mb-6">
                        <div class="p-4 rounded-lg bg-gray-100 mb-4">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-gray-600 text-sm">Reservación para</p>
                                    <p class="text-lg font-semibold text-gray-800" id="summary-guests"></p>
                                </div>
                                <div class="mt-2 md:mt-0">
                                    <p class="text-gray-600 text-sm">Fecha y hora</p>
                                    <p class="text-lg font-semibold text-gray-800">
                                        <span id="summary-date"></span> - <span id="summary-time"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="availability-message" class="mb-6"></div>
                    </div>
                    
                    <div id="reservation-form-container" class="hidden">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Completa tu información</h3>
                        
                        <form id="reservation-form" action="{{ route('reservation.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" id="form-reservation-date" name="reservation_date">
                            <input type="hidden" id="form-reservation-time" name="reservation_time">
                            <input type="hidden" id="form-num-guests" name="num_guests">
                            
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                                <input type="text" id="customer_name" name="customer_name" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                    value="{{ auth()->check() ? auth()->user()->name : old('customer_name') }}" required>
                            </div>
                            
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                <input type="email" id="customer_email" name="customer_email" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                    value="{{ auth()->check() ? auth()->user()->email : old('customer_email') }}" required>
                            </div>
                            
                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="tel" id="customer_phone" name="customer_phone" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                    value="{{ old('customer_phone') }}" required>
                            </div>
                            
                            <div>
                                <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-1">Solicitudes especiales (opcional)</label>
                                <textarea id="special_requests" name="special_requests" rows="3" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 focus:ring-opacity-50">{{ old('special_requests') }}</textarea>
                                <div class="text-xs text-gray-500 mt-1">Menciona si necesitas una silla para bebé, celebras una ocasión especial o tienes alguna preferencia.</div>
                            </div>
                            
                            <div class="pt-4">
                                <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-md transition-colors">
                                    Confirmar Reservación
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sección WhatsApp -->
            <div class="mt-12 bg-green-50 rounded-xl p-8 shadow-sm border border-green-100">
                <div class="flex flex-col md:flex-row md:items-center">
                    <div class="md:w-2/3 mb-6 md:mb-0 md:pr-8">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">¿Necesitas ayuda con tu reservación?</h3>
                        <p class="text-gray-600 mb-4">Chatea directamente con nuestro equipo de atención al cliente vía WhatsApp para solicitudes especiales o aclarar cualquier duda.</p>
                        
                        <form id="whatsapp-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="whatsapp-name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input type="text" id="whatsapp-name" name="name" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" required>
                                </div>
                                
                                <div>
                                    <label for="whatsapp-phone" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="tel" id="whatsapp-phone" name="phone" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="whatsapp-message" class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                                <textarea id="whatsapp-message" name="message" rows="3" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    placeholder="Escribe tu consulta aquí..."></textarea>
                            </div>
                            
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                Contactar por WhatsApp
                            </button>
                        </form>
                    </div>
                    
                    <div class="md:w-1/3 flex justify-center">
                        <img src="{{ asset('img/whatsapp-support.svg') }}" alt="WhatsApp Support" class="h-48 w-auto">
                    </div>
                </div>
            </div>
            
            <!-- Política de reservaciones -->
            <div class="mt-12 p-6 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Política de reservaciones</h3>
                <ul class="space-y-2 text-gray-600 text-sm">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Las reservaciones deben realizarse con al menos 2 horas de anticipación.
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Le pedimos llegar 10 minutos antes de su hora reservada.
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Mantendremos su reserva por 15 minutos después de la hora programada.
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Para cancelar o modificar su reserva, contacte al restaurante con al menos 3 horas de anticipación.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar selector de fecha
        flatpickr("#reservation-date", {
            minDate: "{{ $today }}",
            maxDate: "{{ $maxDate }}",
            dateFormat: "Y-m-d",
        });
        
        // Control de número de personas
        const guestsInput = document.getElementById('guests');
        const decreaseBtn = document.getElementById('decrease-guests');
        const increaseBtn = document.getElementById('increase-guests');
        
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(guestsInput.value);
            if (currentValue > 1) {
                guestsInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(guestsInput.value);
            if (currentValue < 20) {
                guestsInput.value = currentValue + 1;
            }
        });
        
        // Verificar disponibilidad
        const availabilityForm = document.getElementById('availability-form');
        const availabilityResults = document.getElementById('availability-results');
        const availabilityMessage = document.getElementById('availability-message');
        const reservationFormContainer = document.getElementById('reservation-form-container');
        
        availabilityForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const date = document.getElementById('reservation-date').value;
            const time = document.getElementById('reservation-time').value;
            const guests = document.getElementById('guests').value;
            
            if (!date || !time) {
                alert('Por favor selecciona fecha y hora para tu reservación.');
                return;
            }
            
            // Actualizar resumen
            document.getElementById('summary-guests').textContent = `${guests} ${guests > 1 ? 'personas' : 'persona'}`;
            
            // Mostrar cargando
            availabilityMessage.innerHTML = '<div class="text-center py-4"><div class="animate-spin rounded-full h-10 w-10 border-b-2 border-red-700 mx-auto"></div><p class="mt-2 text-gray-600">Verificando disponibilidad...</p></div>';
            
            // Fetch para verificar disponibilidad
            fetch('{{ route("reservation.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    date: date,
                    time: time,
                    guests: guests
                })
            })
            .then(response => response.json())
            .then(data => {
                // Actualizar resumen
                document.getElementById('summary-date').textContent = data.date;
                document.getElementById('summary-time').textContent = data.time;
                
                // Mostrar mensaje de disponibilidad
                if (data.available) {
                    availabilityMessage.innerHTML = `
                        <div class="bg-green-50 border-l-4 border-green-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        ${data.message}
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Mostrar formulario de reserva
                    reservationFormContainer.classList.remove('hidden');
                    
                    // Llenar datos ocultos del formulario
                    document.getElementById('form-reservation-date').value = date;
                    document.getElementById('form-reservation-time').value = time;
                    document.getElementById('form-num-guests').value = guests;
                    
                } else {
                    availabilityMessage.innerHTML = `
                        <div class="bg-red-50 border-l-4 border-red-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        ${data.message}
                                    </p>
                                    <p class="text-sm text-red-700 mt-2">
                                        Intenta con otra fecha u hora, o contáctanos a través de WhatsApp para opciones personalizadas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Ocultar formulario de reserva
                    reservationFormContainer.classList.add('hidden');
                }
                
                // Cambiar a paso 2
                document.getElementById('step1-content').classList.add('hidden');
                document.getElementById('step2-content').classList.remove('hidden');
                document.getElementById('step2').classList.add('active');
            })
            .catch(error => {
                console.error('Error:', error);
                availabilityMessage.innerHTML = `
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Hubo un error al verificar la disponibilidad. Por favor, inténtalo de nuevo.
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
        });
        
        // Volver al paso 1
        document.getElementById('back-to-step1').addEventListener('click', function() {
            document.getElementById('step1-content').classList.remove('hidden');
            document.getElementById('step2-content').classList.add('hidden');
            document.getElementById('step2').classList.remove('active');
        });
        
        // Formulario de reserva - Paso 3
        document.getElementById('reservation-form').addEventListener('submit', function() {
            document.getElementById('step3').classList.add('active');
        });
        
        // Formulario de WhatsApp
        document.getElementById('whatsapp-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('whatsapp-name').value;
            const phone = document.getElementById('whatsapp-phone').value;
            const message = document.getElementById('whatsapp-message').value;
            
            if (!name || !phone) {
                alert('Por favor completa nombre y teléfono para contactarte por WhatsApp.');
                return;
            }
            
            // Fetch para obtener enlace de WhatsApp
            fetch('{{ route("reservation.whatsapp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name,
                    phone: phone,
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                // Abrir WhatsApp en nueva ventana
                window.open(data.url, '_blank');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al procesar tu solicitud de WhatsApp.');
            });
        });
    });
</script>
@endsection