<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Reservación') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('reservations.store') }}" class="max-w-xl mx-auto">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información del Cliente -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Información del Cliente</h3>
                            </div>

                            <!-- Cliente Registrado (Opcional) -->
                            <div>
                                <x-input-label for="user_id" :value="__('Cliente Registrado (Opcional)')" />
                                <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Seleccionar cliente registrado</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                                <p class="text-xs text-gray-500 mt-1">Si selecciona un cliente registrado, algunos campos se autocompletarán.</p>
                            </div>

                            <!-- Nombre del Cliente -->
                            <div>
                                <x-input-label for="customer_name" :value="__('Nombre del Cliente')" />
                                <x-text-input id="customer_name" class="block mt-1 w-full" type="text" name="customer_name" :value="old('customer_name')" required autofocus />
                                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                            </div>

                            <!-- Email del Cliente -->
                            <div>
                                <x-input-label for="customer_email" :value="__('Email del Cliente')" />
                                <x-text-input id="customer_email" class="block mt-1 w-full" type="email" name="customer_email" :value="old('customer_email')" />
                                <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                                <p class="text-xs text-gray-500 mt-1">Se enviará un correo de confirmación si se confirma la reserva.</p>
                            </div>

                            <!-- Teléfono del Cliente -->
                            <div>
                                <x-input-label for="customer_phone" :value="__('Teléfono del Cliente')" />
                                <x-text-input id="customer_phone" class="block mt-1 w-full" type="text" name="customer_phone" :value="old('customer_phone')" />
                                <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
                            </div>

                            <!-- Detalles de la Reserva -->
                            <div class="md:col-span-2 mt-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Detalles de la Reserva</h3>
                            </div>

                            <!-- Número de Personas -->
                            <div>
                                <x-input-label for="num_guests" :value="__('Número de Personas')" />
                                <x-text-input id="num_guests" class="block mt-1 w-full" type="number" name="num_guests" :value="old('num_guests', 2)" min="1" max="99" required />
                                <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                            </div>

                            <!-- Mesa -->
                            <div>
                                <x-input-label for="table_id" :value="__('Mesa')" />
                                <select id="table_id" name="table_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccionar mesa</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id', $selectedTableId) == $table->id ? 'selected' : '' }}>
                                            Mesa {{ $table->number }} ({{ $table->capacity }} personas)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('table_id')" class="mt-2" />
                            </div>

                            <!-- Fecha de Reserva -->
                            <div>
                                <x-input-label for="reservation_date" :value="__('Fecha de Reserva')" />
                                <x-text-input id="reservation_date" class="block mt-1 w-full" type="date" name="reservation_date" :value="old('reservation_date', $selectedDate)" required />
                                <x-input-error :messages="$errors->get('reservation_date')" class="mt-2" />
                            </div>

                            <!-- Hora de Reserva -->
                            <div>
                                <x-input-label for="reservation_time" :value="__('Hora de Reserva')" />
                                <x-text-input id="reservation_time" class="block mt-1 w-full" type="time" name="reservation_time" :value="old('reservation_time', $selectedTime)" required />
                                <x-input-error :messages="$errors->get('reservation_time')" class="mt-2" />
                            </div>

                            <!-- Duración -->
                            <div>
                                <x-input-label for="duration" :value="__('Duración (minutos)')" />
                                <select id="duration" name="duration" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="60" {{ old('duration') == 60 ? 'selected' : '' }}>1 hora</option>
                                    <option value="90" {{ old('duration') == 90 ? 'selected' : '' }}>1 hora 30 minutos</option>
                                    <option value="120" {{ old('duration') == 120 ? 'selected' : '' }}>2 horas</option>
                                    <option value="150" {{ old('duration') == 150 ? 'selected' : '' }}>2 horas 30 minutos</option>
                                    <option value="180" {{ old('duration') == 180 ? 'selected' : '' }}>3 horas</option>
                                    <option value="240" {{ old('duration') == 240 ? 'selected' : '' }}>4 horas</option>
                                </select>
                                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div>
                                <x-input-label for="status" :value="__('Estado')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Peticiones Especiales -->
                            <div class="md:col-span-2">
                                <x-input-label for="special_requests" :value="__('Peticiones Especiales')" />
                                <textarea id="special_requests" name="special_requests" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('special_requests') }}</textarea>
                                <x-input-error :messages="$errors->get('special_requests')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Botón de Verificar Disponibilidad -->
                        <div class="mt-6">
                            <button type="button" id="checkAvailability" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                <i class="fas fa-search mr-2"></i> Verificar Disponibilidad
                            </button>
                            <div id="availabilityResult" class="mt-2 hidden"></div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('reservations.index') }}" class="text-gray-600 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Crear Reservación') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Autocompletar información del cliente cuando se selecciona un usuario
            const userSelect = document.getElementById('user_id');
            const customerNameInput = document.getElementById('customer_name');
            const customerEmailInput = document.getElementById('customer_email');
            
            userSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedOption = this.options[this.selectedIndex];
                    const userName = selectedOption.text.split(' (')[0];
                    const userEmail = selectedOption.text.match(/\((.*?)\)/)[1];
                    
                    customerNameInput.value = userName;
                    customerEmailInput.value = userEmail;
                }
            });
            
            // Verificar disponibilidad
            const checkAvailabilityBtn = document.getElementById('checkAvailability');
            const availabilityResult = document.getElementById('availabilityResult');
            
            checkAvailabilityBtn.addEventListener('click', function() {
                const date = document.getElementById('reservation_date').value;
                const time = document.getElementById('reservation_time').value;
                const duration = document.getElementById('duration').value;
                const numGuests = document.getElementById('num_guests').value;
                const tableId = document.getElementById('table_id').value;
                
                if (!date || !time || !duration || !numGuests) {
                    availabilityResult.innerHTML = '<div class="text-red-600">Por favor complete los campos de fecha, hora, duración y número de personas.</div>';
                    availabilityResult.classList.remove('hidden');
                    return;
                }
                
                availabilityResult.innerHTML = '<div class="text-blue-600">Verificando disponibilidad...</div>';
                availabilityResult.classList.remove('hidden');
                
                fetch(`/admin/reservations/check-availability?date=${date}&time=${time}&duration=${duration}&num_guests=${numGuests}&table_id=${tableId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (tableId && data.is_specific_table_available) {
                            availabilityResult.innerHTML = '<div class="text-green-600"><i class="fas fa-check-circle"></i> La mesa seleccionada está disponible para este horario.</div>';
                        } else if (tableId && !data.is_specific_table_available) {
                            availabilityResult.innerHTML = '<div class="text-red-600"><i class="fas fa-times-circle"></i> La mesa seleccionada NO está disponible para este horario.</div>';
                            
                            // Si hay otras mesas disponibles, mostrarlas
                            if (data.available_tables.length > 0) {
                                let tablesHtml = '<div class="mt-2">Mesas disponibles:</div><ul class="mt-1">';
                                data.available_tables.forEach(table => {
                                    tablesHtml += `<li><a href="#" class="text-blue-600" onclick="document.getElementById('table_id').value=${table.id}; return false;">Mesa ${table.number} (${table.capacity} personas)</a></li>`;
                                });
                                tablesHtml += '</ul>';
                                availabilityResult.innerHTML += tablesHtml;
                            } else {
                                availabilityResult.innerHTML += '<div class="mt-1">No hay mesas disponibles para este horario y número de personas.</div>';
                            }
                        } else if (data.available_tables.length > 0) {
                            let tablesHtml = '<div class="text-green-600"><i class="fas fa-check-circle"></i> Mesas disponibles:</div><ul class="mt-1">';
                            data.available_tables.forEach(table => {
                                tablesHtml += `<li><a href="#" class="text-blue-600" onclick="document.getElementById('table_id').value=${table.id}; return false;">Mesa ${table.number} (${table.capacity} personas)</a></li>`;
                            });
                            tablesHtml += '</ul>';
                            availabilityResult.innerHTML = tablesHtml;
                        } else {
                            availabilityResult.innerHTML = '<div class="text-red-600"><i class="fas fa-times-circle"></i> No hay mesas disponibles para este horario y número de personas.</div>';
                        }
                    })
                    .catch(error => {
                        availabilityResult.innerHTML = '<div class="text-red-600">Error al verificar disponibilidad. Intente nuevamente.</div>';
                    });
            });
        });
    </script>
</x-app-layout>