<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Reservación') }} #{{ $reservation->id }}
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
                    <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="max-w-xl mx-auto">
                        @csrf
                        @method('PATCH')

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
                                        <option value="{{ $user->id }}" {{ old('user_id', $reservation->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <!-- Nombre del Cliente -->
                            <div>
                                <x-input-label for="customer_name" :value="__('Nombre del Cliente')" />
                                <x-text-input id="customer_name" class="block mt-1 w-full" type="text" name="customer_name" :value="old('customer_name', $reservation->customer_name)" required />
                                <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                            </div>

                            <!-- Email del Cliente -->
                            <div>
                                <x-input-label for="customer_email" :value="__('Email del Cliente')" />
                                <x-text-input id="customer_email" class="block mt-1 w-full" type="email" name="customer_email" :value="old('customer_email', $reservation->customer_email)" />
                                <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                                <p class="text-xs text-gray-500 mt-1">Se enviará un correo de confirmación si se actualiza el estado.</p>
                            </div>

                            <!-- Teléfono del Cliente -->
                            <div>
                                <x-input-label for="customer_phone" :value="__('Teléfono del Cliente')" />
                                <x-text-input id="customer_phone" class="block mt-1 w-full" type="text" name="customer_phone" :value="old('customer_phone', $reservation->customer_phone)" />
                                <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
                            </div>

                            <!-- Detalles de la Reserva -->
                            <div class="md:col-span-2 mt-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Detalles de la Reserva</h3>
                            </div>

                            <!-- Número de Personas -->
                            <div>
                                <x-input-label for="num_guests" :value="__('Número de Personas')" />
                                <x-text-input id="num_guests" class="block mt-1 w-full" type="number" name="num_guests" :value="old('num_guests', $reservation->num_guests)" min="1" max="99" required />
                                <x-input-error :messages="$errors->get('num_guests')" class="mt-2" />
                            </div>

                            <!-- Mesa -->
                            <div>
                                <x-input-label for="table_id" :value="__('Mesa')" />
                                <select id="table_id" name="table_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="">Seleccionar mesa</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}" {{ old('table_id', $reservation->table_id) == $table->id ? 'selected' : '' }}>
                                            Mesa {{ $table->number }} ({{ $table->capacity }} personas)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('table_id')" class="mt-2" />
                            </div>

                            <!-- Fecha de Reserva -->
                            <div>
                                <x-input-label for="reservation_date" :value="__('Fecha de Reserva')" />
                                <x-text-input id="reservation_date" class="block mt-1 w-full" type="date" name="reservation_date" :value="old('reservation_date', $reservation->reservation_time->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('reservation_date')" class="mt-2" />
                            </div>

                            <!-- Hora de Reserva -->
                            <div>
                                <x-input-label for="reservation_time" :value="__('Hora de Reserva')" />
                                <x-text-input id="reservation_time" class="block mt-1 w-full" type="time" name="reservation_time" :value="old('reservation_time', $reservation->reservation_time->format('H:i'))" required />
                                <x-input-error :messages="$errors->get('reservation_time')" class="mt-2" />
                            </div>

                            <!-- Duración -->
                            <div>
                                <x-input-label for="duration" :value="__('Duración (minutos)')" />
                                <select id="duration" name="duration" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    <option value="60" {{ old('duration', $reservation->duration) == 60 ? 'selected' : '' }}>1 hora</option>
                                    <option value="90" {{ old('duration', $reservation->duration) == 90 ? 'selected' : '' }}>1 hora 30 minutos</option>
                                    <option value="120" {{ old('duration', $reservation->duration) == 120 ? 'selected' : '' }}>2 horas</option>
                                    <option value="150" {{ old('duration', $reservation->duration) == 150 ? 'selected' : '' }}>2 horas 30 minutos</option>
                                    <option value="180" {{ old('duration', $reservation->duration) == 180 ? 'selected' : '' }}>3 horas</option>
                                </select>
                                <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mb-6">
                            <x-input-label for="status" :value="__('Estado de la Reservación')" />
                            <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                                <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>Completada</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Notas -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('Notas Adicionales')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $reservation->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <div>
                                <a href="{{ route('reservations.show', $reservation) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300">
                                    <i class="fas fa-eye mr-2"></i> Ver Detalles
                                </a>
                            </div>
                            <div class="flex space-x-4">
                                <a href="{{ route('reservations.index') }}" class="text-gray-600">
                                    Cancelar
                                </a>
                                <x-primary-button>
                                    {{ __('Actualizar Reservación') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Combinar fecha y hora en un solo campo al enviar
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const dateInput = document.getElementById('reservation_date');
                const timeInput = document.getElementById('reservation_time');
                
                if (dateInput.value && timeInput.value) {
                    const datetimeInput = document.createElement('input');
                    datetimeInput.type = 'hidden';
                    datetimeInput.name = 'reservation_time';
                    datetimeInput.value = `${dateInput.value}T${timeInput.value}`;
                    
                    this.appendChild(datetimeInput);
                }
                
                this.submit();
            });

            // Validación de número de personas vs capacidad de la mesa
            const tableSelect = document.getElementById('table_id');
            const guestsInput = document.getElementById('num_guests');
            
            function validateCapacity() {
                if (!tableSelect.value) return;
                
                const selectedOption = tableSelect.options[tableSelect.selectedIndex];
                const capacity = parseInt(selectedOption.textContent.match(/\((\d+) personas\)/)[1]);
                
                if (parseInt(guestsInput.value) > capacity) {
                    alert(`Atención: La mesa seleccionada tiene capacidad para ${capacity} personas, pero se han indicado ${guestsInput.value}.`);
                }
            }
            
            guestsInput.addEventListener('change', validateCapacity);
            tableSelect.addEventListener('change', validateCapacity);

            // Confirmar cancelación
            const statusSelect = document.getElementById('status');
            statusSelect.addEventListener('change', function() {
                if (this.value === 'cancelled' && '{{ $reservation->status }}' !== 'cancelled') {
                    if (!confirm('¿Estás seguro de que deseas cancelar esta reservación?')) {
                        this.value = '{{ $reservation->status }}';
                    }
                }
            });
        });
    </script>
</x-app-layout>