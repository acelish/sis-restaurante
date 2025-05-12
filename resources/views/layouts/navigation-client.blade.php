<!-- Agregar enlace de reservaciones en el menú de navegación -->
<a href="{{ route('reservation') }}" class="text-gray-700 hover:text-red-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('reservation*') ? 'text-red-600 font-semibold' : '' }}">
    Reservaciones
</a>