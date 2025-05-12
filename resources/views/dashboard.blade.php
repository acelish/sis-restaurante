<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Mensaje de bienvenida estilizado -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl shadow-sm mb-8 overflow-hidden border border-gray-200">
                <div class="p-6 relative">
                    <h3 class="text-xl font-bold text-gray-800 mb-2 relative z-10">¡Bienvenido al Sistema de Gestión!</h3>
                    <p class="text-gray-600 relative z-10">{{ __("Selecciona una de las opciones para comenzar a trabajar.") }}</p>
                </div>
            </div>
            
            <!-- Primera fila: 3 tarjetas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                <!-- 1. Tarjeta Categorías -->
                <a href="/admin/categories" class="group bg-blue-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-blue-100/70 border border-blue-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 p-3 rounded-full bg-blue-100 text-blue-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-700 transition-colors duration-300">Categorías</h3>
                                <p class="text-sm text-gray-500">Gestionar categorías de productos</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-blue-200/50">
                            <span class="text-sm font-medium text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                            <span class="text-blue-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                
                <!-- 2. Tarjeta Productos -->
                <a href="/admin/products" class="group bg-emerald-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-emerald-100/70 border border-emerald-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 p-3 rounded-full bg-emerald-100 text-emerald-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m7.875 14.25 1.214 1.942a2.25 2.25 0 0 0 1.908 1.058h3.006a2.25 2.25 0 0 0 1.908-1.058l1.214-1.942M2.41 9h4.636a2.25 2.25 0 0 1 1.872 1.002l.164.246a2.25 2.25 0 0 0 1.872 1.002h2.092a2.25 2.25 0 0 0 1.872-1.002l.164-.246A2.25 2.25 0 0 1 16.954 9h4.636M2.41 9a2.25 2.25 0 0 0-.16.832V12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 12V9.832c0-.287-.055-.57-.16-.832M2.41 9a2.25 2.25 0 0 1 .382-.632l3.285-3.832a2.25 2.25 0 0 1 1.708-.786h8.43c.657 0 1.281.287 1.709.786l3.284 3.832c.163.19.291.404.382.632M4.5 20.25h15A2.25 2.25 0 0 0 21.75 18v-2.625c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125V18a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-emerald-700 transition-colors duration-300">Productos</h3>
                                <p class="text-sm text-gray-500">Administrar catálogo de productos</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-emerald-200/50">
                            <span class="text-sm font-medium text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                            <span class="text-emerald-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                
                <!-- 3. Tarjeta Inventario -->
                <a href="/admin/inventory" class="group bg-orange-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-orange-100/70 border border-orange-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-orange-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 p-3 rounded-full bg-orange-100 text-orange-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-orange-700 transition-colors duration-300">Inventario</h3>
                                <p class="text-sm text-gray-500">Control de stock y existencias</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-orange-200/50">
                            <span class="text-sm font-medium text-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                            <span class="text-orange-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Segunda fila: 3 tarjetas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                <!-- 4. Tarjeta Órdenes -->
                <a href="/admin/orders" class="group bg-red-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-red-100/70 border border-red-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-red-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 p-3 rounded-full bg-red-100 text-red-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-red-700 transition-colors duration-300">Órdenes</h3>
                                <p class="text-sm text-gray-500">Gestión de órdenes de pedidos</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-red-200/50">
                            <span class="text-sm font-medium text-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                            <span class="text-red-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                
                <!-- 5. Tarjeta Mesas -->
                <a href="/admin/tables" class="group bg-purple-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-purple-100/70 border border-purple-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-purple-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 p-3 rounded-full bg-purple-100 text-purple-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-purple-700 transition-colors duration-300">Mesas</h3>
                                <p class="text-sm text-gray-500">Gestión de mesas del restaurante</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-purple-200/50">
                            <span class="text-sm font-medium text-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                            <span class="text-purple-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
                
                <!-- 6. Tarjeta Reservaciones -->
                <a href="/admin/reservations" class="group bg-teal-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-teal-100/70 border border-teal-100">
                    <div class="p-6 relative">
                        <div class="absolute top-0 right-0 w-20 h-20 bg-teal-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 p-3 rounded-full bg-teal-100 text-teal-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 group-hover:text-teal-700 transition-colors duration-300">Reservaciones</h3>
                                <p class="text-sm text-gray-500">Gestión de reservaciones programadas</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-teal-200/50">
                            <span class="text-sm font-medium text-teal-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                            <span class="text-teal-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Tercera fila: 1 tarjeta centrada -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-start-2">
                    <!-- 7. Tarjeta Reportes -->
                    <a href="/admin/reports" class="group bg-amber-50 rounded-xl shadow-md overflow-hidden transform transition-all duration-300 hover:shadow-lg hover:-translate-y-1 hover:bg-amber-100/70 border border-amber-100">
                        <div class="p-6 relative">
                            <div class="absolute top-0 right-0 w-20 h-20 bg-amber-200 rounded-full opacity-20 -mt-6 -mr-6 group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 p-3 rounded-full bg-amber-100 text-amber-600 mr-4 group-hover:scale-110 transition-transform duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-amber-700 transition-colors duration-300">Reportes</h3>
                                    <p class="text-sm text-gray-500">Reportes generales, gastos y estadísticas</p>
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-amber-200/50">
                                <span class="text-sm font-medium text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">Acceder</span>
                                <span class="text-amber-600 transform translate-x-0 group-hover:translate-x-1 transition-transform duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
