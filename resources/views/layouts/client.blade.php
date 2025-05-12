<!-- filepath: e:\Proyectos_Desarrollos\Restaurante\restaurant-system\resources\views\layouts\client.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Restaurante')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">

    <!-- Añadir en el head para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Añadir en el head estilos personalizados -->
    <style>
        .nav-link {
            font-family: 'Montserrat', sans-serif;
            font-weight: 500;
            letter-spacing: 0.3px;
            position: relative;
            padding: 0.5rem 0;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #dc2626;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .nav-icon {
            margin-right: 0.5rem;
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover .nav-icon {
            transform: translateY(-2px);
        }
        
        .cart-badge {
            transition: all 0.3s ease;
        }
        
        .cart-link:hover .cart-badge {
            transform: scale(1.1);
            box-shadow: 0 0 8px rgba(220, 38, 38, 0.5);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .has-items {
            animation: pulse 2s infinite;
        }
    </style>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <header class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div>
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-red-600">Restaurante</a>
                </div>
                <nav>
                    <ul class="flex items-center space-x-6 lg:space-x-8">
                        <li>
                            <a href="{{ route('home') }}" class="nav-link group flex items-center text-gray-800 hover:text-red-600">
                                <i class="nav-icon fas fa-home text-red-500 group-hover:text-red-600"></i>
                                <span>Inicio</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('menu') }}" class="nav-link group flex items-center text-gray-800 hover:text-red-600">
                                <i class="nav-icon fas fa-utensils text-red-500 group-hover:text-red-600"></i>
                                <span>Menú</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reservation') }}" class="nav-link group flex items-center text-gray-800 hover:text-red-600">
                                <i class="nav-icon far fa-calendar-alt text-red-500 group-hover:text-red-600"></i>
                                <span>Reservaciones</span>
                            </a>
                        </li>
                        <li>
                            <div class="relative">
                                <a href="{{ route('cart') }}" class="nav-link cart-link group flex items-center text-gray-800 hover:text-red-600 pl-1 pr-1">
                                    <div class="relative flex items-center">
                                        <i class="nav-icon fas fa-shopping-cart text-red-500 group-hover:text-red-600"></i>
                                        @if(Session::has('cart') && count(Session::get('cart')) > 0)
                                            <span class="cart-badge absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-md has-items">
                                                {{ count(Session::get('cart')) }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="ml-1">Carrito</span>
                                </a>
                            </div>
                        </li>
                        @auth
                            <li class="ml-2">
                                <a href="{{ route('dashboard') }}" class="nav-link group flex items-center text-gray-800 hover:text-red-600">
                                    <i class="nav-icon fas fa-user-circle text-red-500 group-hover:text-red-600"></i>
                                    <span>DashBoard</span>
                                </a>
                            </li>
                        @else
                            <li class="ml-2">
                                <a href="{{ route('login') }}" class="flex items-center px-4 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white font-medium transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                    <i class="fas fa-sign-in-alt mr-2"></i>
                                    <span>Ingresar</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Restaurante</h3>
                    <p class="mb-4">La mejor experiencia gastronómica para tu paladar.</p>
                    <p class="mb-4">Dirección: Av. Principal #123</p>
                    <p>Teléfono: 123-456-7890</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Horario</h3>
                    <p class="mb-2">Lunes a Viernes: 11:00 - 22:00</p>
                    <p class="mb-2">Sábados: 11:00 - 23:00</p>
                    <p>Domingos: 12:00 - 21:00</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Enlaces</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-red-400">Sobre nosotros</a></li>
                        <li><a href="#" class="hover:text-red-400">Política de privacidad</a></li>
                        <li><a href="#" class="hover:text-red-400">Términos y condiciones</a></li>
                        <li><a href="#" class="hover:text-red-400">Contacto</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p>&copy; {{ date('Y') }} Restaurante. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Script para detectar la página actual (añadir en la parte inferior del body) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (href) {
                    const url = new URL(href, window.location.origin);
                    if (currentPath === url.pathname || 
                       (url.pathname !== '/' && currentPath.startsWith(url.pathname))) {
                        link.classList.add('text-red-600', 'font-semibold');
                        link.classList.remove('text-gray-800');
                        
                        // Añade la línea debajo del enlace activo
                        const afterElement = document.createElement('style');
                        afterElement.textContent = `.nav-link.text-red-600::after { width: 100%; }`;
                        document.head.appendChild(afterElement);
                    }
                }
            });
        });
    </script>

    <!-- En la parte inferior antes de cerrar el body -->
    <link rel="stylesheet" href="{{ asset('css/whatsapp-float.css') }}">
    <a href="https://wa.me/5215512345678?text=Hola,%20me%20gustaría%20hacer%20una%20reservación" class="whatsapp-float" target="_blank">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
    </a>
</body>
</html>