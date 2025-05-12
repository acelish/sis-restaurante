<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Calendario de Reservaciones') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reservations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-list mr-1"></i> Listado
                </a>
                <a href="{{ route('reservations.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-1"></i> Nueva Reservación
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                        <!-- Navegación de fechas -->
                        <div class="flex items-center space-x-4">
                            <a href="#" id="prevDay" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <div class="text-lg font-semibold" id="currentDate">{{ now()->format('d/m/Y') }}</div>
                            <a href="#" id="nextDay" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="#" id="today" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                                Hoy
                            </a>
                        </div>

                        <!-- Filtro de mesas -->
                        <div class="flex items-center space-x-4">
                            <label for="tableFilter" class="text-sm font-medium text-gray-700">Filtrar por mesa:</label>
                            <select id="tableFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="all">Todas las mesas</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">Mesa {{ $table->number }} ({{ $table->capacity }} pers.)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="mb-4 flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-200 rounded-sm mr-2"></div>
                            <span>Pendiente</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-200 rounded-sm mr-2"></div>
                            <span>Confirmada</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-200 rounded-sm mr-2"></div>
                            <span>Completada</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-200 rounded-sm mr-2"></div>
                            <span>Cancelada</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-gray-200 rounded-sm mr-2"></div>
                            <span>No Asistió</span>
                        </div>
                    </div>

                    <!-- Calendario -->
                    <div class="overflow-x-auto">
                        <div class="calendar-container" style="min-width: 1000px;">
                            <!-- Horarios en columna -->
                            <div class="grid grid-cols-25 gap-1">
                                <!-- Header con horarios -->
                                <div class="h-12 flex items-center justify-center font-bold text-gray-700">Mesa</div>
                                @for($hour = 9; $hour <= 23; $hour++)
                                    @foreach(['00', '30'] as $minute)
                                        <div class="h-12 flex items-center justify-center text-xs text-gray-500">
                                            {{ $hour }}:{{ $minute }}
                                        </div>
                                    @endforeach
                                @endfor

                                <!-- Filas de mesas -->
                                @foreach($tables as $table)
                                    <div class="table-row" data-table-id="{{ $table->id }}">
                                        <div class="h-12 flex items-center px-2 font-medium bg-gray-100 text-gray-800">
                                            <span>Mesa {{ $table->number }}</span>
                                            <span class="ml-1 text-xs text-gray-500">({{ $table->capacity }}p)</span>
                                        </div>
                                        @for($hour = 9; $hour <= 23; $hour++)
                                            @foreach(['00', '30'] as $minute)
                                                <div class="h-12 border border-gray-100 reservation-slot" 
                                                    data-hour="{{ $hour }}" 
                                                    data-minute="{{ $minute }}" 
                                                    data-table-id="{{ $table->id }}"
                                                    onclick="createReservation({{ $table->id }}, '{{ $hour }}:{{ $minute }}')">
                                                </div>
                                            @endforeach
                                        @endfor
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Línea que indica la hora actual -->
                    <div id="currentTimeLine" class="absolute left-0 w-full border-t-2 border-red-500 z-10" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver/editar reservación -->
    <div id="reservationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium" id="modalTitle">Detalles de Reservación</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent">
                <!-- Contenido cargado dinámicamente -->
            </div>
        </div>
    </div>

    <style>
        .grid-cols-25 {
            grid-template-columns: 100px repeat(30, minmax(45px, 1fr));
        }
        
        .reservation-event {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            border-radius: 4px;
            padding: 2px 4px;
            overflow: hidden;
            font-size: 11px;
            cursor: pointer;
            z-index: 5;
        }
        
        .reservation-slot {
            position: relative;
        }
        
        .reservation-slot:hover {
            background-color: rgba(79, 70, 229, 0.1);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos de reservaciones
            const reservationsData = @json($reservations);
            const currentDate = new Date();
            let displayDate = new Date();
            
            // Actualizar título de fecha
            function updateDateTitle() {
                document.getElementById('currentDate').textContent = displayDate.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }
            
            // Botones de navegación
            document.getElementById('prevDay').addEventListener('click', function(e) {
                e.preventDefault();
                displayDate.setDate(displayDate.getDate() - 1);
                updateDateTitle();
                renderReservations();
            });
            
            document.getElementById('nextDay').addEventListener('click', function(e) {
                e.preventDefault();
                displayDate.setDate(displayDate.getDate() + 1);
                updateDateTitle();
                renderReservations();
            });
            
            document.getElementById('today').addEventListener('click', function(e) {
                e.preventDefault();
                displayDate = new Date();
                updateDateTitle();
                renderReservations();
            });
            
            // Filtro de mesas
            document.getElementById('tableFilter').addEventListener('change', function() {
                const tableId = this.value;
                const tableRows = document.querySelectorAll('.table-row');
                
                tableRows.forEach(row => {
                    if (tableId === 'all' || row.getAttribute('data-table-id') === tableId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
            
            // Renderizar reservaciones
            function renderReservations() {
                // Limpiar todas las reservaciones previas
                document.querySelectorAll('.reservation-event').forEach(el => el.remove());
                
                // Filtrar reservaciones para la fecha seleccionada
                const dateStr = displayDate.toISOString().split('T')[0];
                const filteredReservations = reservationsData.filter(res => {
                    return res.reservation_time.startsWith(dateStr);
                });
                
                // Añadir cada reservación al calendario
                filteredReservations.forEach(reservation => {
                    const startTime = new Date(reservation.reservation_time);
                    const durationMinutes = reservation.duration || 90; // Default 90 min
                    
                    // Calcular posición en la cuadrícula
                    const hour = startTime.getHours();
                    const minute = startTime.getMinutes();
                    const startColumn = (hour - 9) * 2 + (minute >= 30 ? 1 : 0) + 2; // +2 por la columna de mesas
                    const durationCells = durationMinutes / 30;
                    
                    // Encontrar el slot correspondiente
                    const tableRow = document.querySelector(`.table-row[data-table-id="${reservation.table_id}"]`);
                    if (!tableRow) return;
                    
                    const slots = tableRow.querySelectorAll('.reservation-slot');
                    if (!slots[startColumn - 2]) return; // Ajuste para la columna de mesa
                    
                    // Crear elemento de evento
                    const eventEl = document.createElement('div');
                    eventEl.className = `reservation-event bg-${reservation.status_color}-200 border border-${reservation.status_color}-300 text-${reservation.status_color}-800`;
                    eventEl.style.gridColumnStart = startColumn;
                    eventEl.style.gridColumnEnd = startColumn + durationCells;
                    eventEl.setAttribute('data-id', reservation.id);
                    eventEl.style.width = `calc(${durationCells * 100}% - 2px)`;
                    
                    // Contenido del evento
                    eventEl.innerHTML = `
                        <div class="font-bold">${reservation.customer_name}</div>
                        <div>${reservation.num_guests} personas</div>
                    `;
                    
                    // Añadir evento de clic
                    eventEl.addEventListener('click', () => {
                        showReservationDetails(reservation);
                    });
                    
                    // Añadir al slot
                    slots[startColumn - 2].appendChild(eventEl);
                });
                
                // Actualizar línea de tiempo actual
                updateCurrentTimeLine();
            }
            
            // Mostrar detalles de reservación en modal
            function showReservationDetails(reservation) {
                const modal = document.getElementById('reservationModal');
                const modalContent = document.getElementById('modalContent');
                
                const startTime = new Date(reservation.reservation_time);
                const endTime = new Date(startTime.getTime() + (reservation.duration * 60000));
                
                // Formatear fechas
                const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const timeOptions = { hour: '2-digit', minute: '2-digit' };
                
                modalContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="font-medium">Estado:</span>
                            <span class="px-2 py-1 text-xs rounded-full bg-${reservation.status_color}-100 text-${reservation.status_color}-800">
                                ${reservation.status_text}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Mesa:</span>
                            <span>Mesa ${reservation.table ? reservation.table.number : 'No asignada'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Cliente:</span>
                            <span>${reservation.customer_name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Contacto:</span>
                            <div class="text-right">
                                ${reservation.customer_phone ? `<div>${reservation.customer_phone}</div>` : ''}
                                ${reservation.customer_email ? `<div>${reservation.customer_email}</div>` : ''}
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Fecha:</span>
                            <span>${startTime.toLocaleDateString('es-ES', dateOptions)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Hora:</span>
                            <span>${startTime.toLocaleTimeString('es-ES', timeOptions)} - ${endTime.toLocaleTimeString('es-ES', timeOptions)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Comensales:</span>
                            <span>${reservation.num_guests} personas</span>
                        </div>
                        ${reservation.special_requests ? `
                        <div>
                            <span class="font-medium">Peticiones especiales:</span>
                            <p class="mt-1 text-sm text-gray-600">${reservation.special_requests}</p>
                        </div>
                        ` : ''}
                        
                        <div class="flex justify-end space-x-2 mt-6">
                            <a href="${window.location.origin}/admin/reservations/${reservation.id}/edit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            <a href="${window.location.origin}/admin/reservations/${reservation.id}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-eye mr-1"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                `;
                
                modal.classList.remove('hidden');
            }
            
            // Función para crear nueva reservación
            window.createReservation = function(tableId, time) {
                const parts = time.split(':');
                const hour = parseInt(parts[0]);
                const minute = parseInt(parts[1]);
                
                // Crear fecha para la reservación
                const date = new Date(displayDate);
                date.setHours(hour, minute, 0, 0);
                
                // Redireccionar a la página de creación con parámetros
                window.location.href = `/admin/reservations/create?table_id=${tableId}&date=${date.toISOString().split('.')[0]}`;
            };
            
            // Cerrar modal
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('reservationModal').classList.add('hidden');
            });
            
            // Línea de tiempo actual
            function updateCurrentTimeLine() {
                const now = new Date();
                const currentDateStr = displayDate.toISOString().split('T')[0];
                const todayStr = now.toISOString().split('T')[0];
                
                // Solo mostrar línea si estamos viendo el día actual
                if (currentDateStr === todayStr) {
                    const hour = now.getHours();
                    const minute = now.getMinutes();
                    
                    // Solo mostrar entre 9:00 y 23:30
                    if (hour >= 9 && hour <= 23) {
                        const timeLine = document.getElementById('currentTimeLine');
                        const calendarContainer = document.querySelector('.calendar-container');
                        
                        // Calcular posición
                        const minutesSince9am = (hour - 9) * 60 + minute;
                        const totalMinutes = 14 * 60 + 30; // 9:00 a 23:30
                        const percentOfDay = (minutesSince9am / totalMinutes) * 100;
                        
                        // Posicionar línea
                        const containerRect = calendarContainer.getBoundingClientRect();
                        const firstSlot = document.querySelector('.reservation-slot');
                        const lastSlot = document.querySelector('.table-row:last-child .reservation-slot:last-child');
                        
                        if (firstSlot && lastSlot) {
                            const firstSlotRect = firstSlot.getBoundingClientRect();
                            const lastSlotRect = lastSlot.getBoundingClientRect();
                            const width = lastSlotRect.right - firstSlotRect.left;
                            const left = 100 + (percentOfDay * width / 100);
                            
                            timeLine.style.top = `${firstSlotRect.top}px`;
                            timeLine.style.left = `${left}px`;
                            timeLine.style.height = `${calendarContainer.offsetHeight}px`;
                            timeLine.style.display = 'block';
                        }
                    } else {
                        document.getElementById('currentTimeLine').style.display = 'none';
                    }
                } else {
                    document.getElementById('currentTimeLine').style.display = 'none';
                }
            }
            
            // Inicializar
            updateDateTitle();
            renderReservations();
            
            // Actualizar cada minuto
            setInterval(() => {
                if (displayDate.toDateString() === new Date().toDateString()) {
                    updateCurrentTimeLine();
                }
            }, 60000);
        });
    </script>
</x-app-layout>