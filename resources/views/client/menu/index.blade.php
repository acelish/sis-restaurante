<!-- filepath: e:\Proyectos_Desarrollos\Restaurante\restaurant-system\resources\views\client\menu\index.blade.php -->
@extends('layouts.client')

@section('title', 'Restaurante - Bienvenidos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Bienvenidos a nuestro restaurante</h1>
        <p class="text-lg text-gray-600 mt-2">Disfruta de la mejor comida de la ciudad</p>
    </div>

    <!-- Categorías en formato cápsula/sticker -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl md:text-2xl font-semibold">Nuestras categorías</h2>
            <a href="{{ route('menu') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                Ver todas <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </a>
        </div>
        
        <!-- Contenedor principal con ancho máximo para mejor centrado -->
        <div class="max-w-4xl mx-auto">
            <!-- Cápsulas de categorías en formato sticker animado -->
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3 md:gap-4">
                @foreach($categories as $category)
                <a href="{{ route('menu.category', $category) }}" 
                   class="group w-20 sm:w-24 md:w-28 text-center transform transition-all duration-300 hover:scale-110">
                    <div class="mx-auto w-14 sm:w-16 md:w-20 h-14 sm:h-16 md:h-20 rounded-full bg-white shadow-md overflow-hidden border-2 border-red-100 mb-2 
                              transition-all duration-300 hover:shadow-lg hover:border-red-300">
                        @if($category->image)
                        <div class="w-full h-full overflow-hidden">
                            <img src="{{ asset('storage/' . $category->image) }}" 
                                 alt="{{ $category->name }}" 
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center">
                            <i class="fas fa-utensils text-red-400"></i>
                        </div>
                        @endif
                    </div>
                    <h3 class="text-xs sm:text-sm font-medium truncate">{{ $category->name }}</h3>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Productos destacados organizados por categorías -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl md:text-2xl font-semibold">Destacados del día</h2>
            <a href="{{ route('menu') }}" class="text-red-600 hover:text-red-800 text-sm font-medium">
                Ver todo el menú <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </a>
        </div>
        
        <!-- Tabs de categorías -->
        <div class="mb-6 overflow-x-auto no-scrollbar">
            <div class="flex space-x-2 min-w-max pb-2">
                <button type="button" class="category-tab active bg-red-600 text-white text-sm py-1 px-3 rounded-full" 
                        data-category="all">
                    Todos
                </button>
                @if(isset($categoriesWithFeatured))
                    @foreach($categoriesWithFeatured as $categoryTab)
                    <button type="button" class="category-tab bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm py-1 px-3 rounded-full" 
                            data-category="{{ $categoryTab->id }}">
                        {{ $categoryTab->name }}
                    </button>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Contenedor con scroll horizontal para los productos -->
        <div class="relative max-w-7xl mx-auto">
            <!-- Indicadores de scroll -->
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 hidden md:block">
                <button class="scroll-btn scroll-left bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md focus:outline-none" aria-label="Desplazar izquierda">
                    <i class="fas fa-chevron-left text-red-600"></i>
                </button>
            </div>
            <div class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 hidden md:block">
                <button class="scroll-btn scroll-right bg-white bg-opacity-70 hover:bg-opacity-100 rounded-full p-2 shadow-md focus:outline-none" aria-label="Desplazar derecha">
                    <i class="fas fa-chevron-right text-red-600"></i>
                </button>
            </div>
            
            <!-- Estilos mejorados para responsividad -->
            <style>
                .featured-product-card {
                    width: 170px !important;
                    height: 260px !important;
                    max-width: 170px !important;
                    max-height: 260px !important;
                    min-width: 170px !important;
                    min-height: 260px !important;
                    box-sizing: border-box !important;
                    overflow: hidden !important;
                    flex: none !important;
                }
                
                @media (max-width: 640px) {
                    .featured-product-card {
                        width: 150px !important;
                        min-width: 150px !important;
                        max-width: 150px !important;
                    }
                }
                
                @media (max-width: 400px) {
                    .featured-product-card {
                        width: 140px !important;
                        min-width: 140px !important;
                        max-width: 140px !important;
                    }
                }
                
                .featured-product-image {
                    width: 100% !important;
                    height: 100px !important;
                    min-height: 100px !important;
                    max-height: 100px !important;
                    overflow: hidden !important;
                }
                
                .featured-product-content {
                    width: 100% !important;
                    height: 160px !important;
                    min-height: 160px !important;
                    max-height: 160px !important;
                    overflow: hidden !important;
                }
                
                .featured-product-desc {
                    height: 38px !important;
                    min-height: 38px !important;
                    max-height: 38px !important;
                    overflow: hidden !important;
                }
                
                /* Mejora del scroll container */
                .products-scroll-container {
                    scrollbar-width: none;
                    -ms-overflow-style: none;
                    scroll-behavior: smooth;
                    -webkit-overflow-scrolling: touch;
                    padding-bottom: 10px;
                }
                
                /* Mejora de espaciado para móviles */
                @media (max-width: 640px) {
                    .product-spacing {
                        gap: 10px;
                    }
                }
            </style>

            <!-- Contenedor con productos en línea horizontal - Grupo "Todos" -->
            <div class="products-scroll-container product-category active overflow-x-auto pb-2 no-scrollbar" data-category="all">
                <div class="flex justify-center sm:justify-start px-1 py-2 space-x-4 sm:space-x-3 product-spacing">
                    @isset($featuredProducts)
                        @foreach($featuredProducts as $product)
                        <div class="featured-product-card bg-white rounded-lg shadow-sm hover:shadow-md overflow-hidden border border-gray-100 transition duration-300 ease-in-out transform hover:-translate-y-1">
                            <!-- Contenedor de imagen con altura fija -->
                            <div class="featured-product-image relative bg-gray-50">
                                @if($product->image)
                                <img 
                                    class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-110" 
                                    src="{{ asset('storage/' . $product->image) }}" 
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                >
                                @else
                                <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                    <i class="fas fa-utensils text-xl text-gray-400"></i>
                                </div>
                                @endif
                                
                                <!-- Badge de precio más pequeño -->
                                <div class="absolute top-1 right-1">
                                    <span class="bg-red-600 text-white text-xs font-bold py-0.5 px-2 rounded-full shadow-sm">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                </div>
                                
                                <!-- Badge de categoría más compacto -->
                                <div class="absolute bottom-1 left-1">
                                    <span class="bg-white bg-opacity-90 text-xs text-gray-700 py-0.5 px-1.5 rounded-full shadow-sm truncate max-w-[80px] inline-block">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Contenido con altura fija -->
                            <div class="featured-product-content p-2 flex flex-col">
                                <h3 class="text-sm font-semibold mb-1 text-gray-800 truncate">{{ $product->name }}</h3>
                                
                                <!-- Descripción con truncado -->
                                <div class="featured-product-desc mb-2">
                                    <p class="text-gray-600 text-xs line-clamp-2">
                                        {{ Str::limit($product->description, 60) }}
                                    </p>
                                </div>
                                
                                <!-- Botón de añadir al carrito ajustado al fondo -->
                                <div class="mt-auto pt-2 border-t border-gray-100">
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <div class="flex space-x-1">
                                            <div class="relative w-12">
                                                <select name="quantity" class="w-full text-xs appearance-none bg-white border border-gray-300 rounded pl-1 pr-4 py-0.5">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-gray-700">
                                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                                </div>
                                            </div>
                                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs py-0.5 px-1 rounded flex items-center justify-center transition duration-200">
                                                <i class="fas fa-cart-plus mr-1 text-[10px]"></i> Añadir
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="w-full text-center py-8 text-gray-500">
                            <p>No hay productos destacados disponibles.</p>
                        </div>
                    @endisset
                </div>
            </div>
            
            <!-- Contenedores por categoría - Inicialmente ocultos -->
            @isset($categoriesWithFeatured)
                @foreach($categoriesWithFeatured as $categoryGroup)
                <div class="products-scroll-container product-category overflow-x-auto pb-2 no-scrollbar hidden" data-category="{{ $categoryGroup->id }}">
                    <div class="flex justify-center sm:justify-start px-1 py-2 space-x-4 sm:space-x-3 product-spacing">
                        @isset($featuredProductsByCategory[$categoryGroup->id])
                            @foreach($featuredProductsByCategory[$categoryGroup->id] as $product)
                            <div class="featured-product-card bg-white rounded-lg shadow-sm hover:shadow-md overflow-hidden border border-gray-100 transition duration-300 ease-in-out transform hover:-translate-y-1">
                                <!-- Contenedor de imagen con altura y ancho fijos -->
                                <div class="featured-product-image relative bg-gray-50">
                                    @if($product->image)
                                    <img 
                                        class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-110" 
                                        src="{{ asset('storage/' . $product->image) }}" 
                                        alt="{{ $product->name }}"
                                        loading="lazy"
                                    >
                                    @else
                                    <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                                        <i class="fas fa-utensils text-xl text-gray-400"></i>
                                    </div>
                                    @endif
                                    
                                    <div class="absolute top-1 right-1">
                                        <span class="bg-red-600 text-white text-xs font-bold py-0.5 px-2 rounded-full shadow-sm">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Badge de categoría idéntico al de "Todos" -->
                                    <div class="absolute bottom-1 left-1">
                                        <span class="bg-white bg-opacity-90 text-xs text-gray-700 py-0.5 px-1.5 rounded-full shadow-sm truncate max-w-[80px] inline-block">
                                            {{ $product->category->name }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="featured-product-content p-2 flex flex-col">
                                    <h3 class="text-sm font-semibold mb-1 text-gray-800 truncate">{{ $product->name }}</h3>
                                    
                                    <div class="featured-product-desc mb-2">
                                        <p class="text-gray-600 text-xs line-clamp-2">
                                            {{ Str::limit($product->description, 60) }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-auto pt-2 border-t border-gray-100">
                                        <form action="{{ route('cart.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <div class="flex space-x-1">
                                                <div class="relative w-12">
                                                    <select name="quantity" class="w-full text-xs appearance-none bg-white border border-gray-300 rounded pl-1 pr-4 py-0.5">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1 text-gray-700">
                                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                                    </div>
                                                </div>
                                                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs py-0.5 px-1 rounded flex items-center justify-center transition duration-200">
                                                    <i class="fas fa-cart-plus mr-1 text-[10px]"></i> Añadir
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="w-full text-center py-8 text-gray-500">
                                <p>No hay productos destacados en esta categoría.</p>
                            </div>
                        @endisset
                    </div>
                </div>
                @endforeach
            @endisset
        </div>
    </div>
    
    <!-- Sección de Promociones con cápsulas alternando -->
    <div class="mb-12">
        <h2 class="text-xl md:text-2xl font-semibold mb-6">Promociones especiales</h2>
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="flex overflow-x-auto pb-2 space-x-4 no-scrollbar">
                <div class="flex-shrink-0 w-72 h-24 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center p-4 transform transition hover:scale-105">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-red-600 mr-4">
                        <i class="fas fa-pizza-slice text-xl"></i>
                    </div>
                    <div class="text-white">
                        <h3 class="font-bold">2x1 en Pizzas</h3>
                        <p class="text-sm text-red-100">Todos los martes</p>
                    </div>
                </div>
                
                <div class="flex-shrink-0 w-72 h-24 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center p-4 transform transition hover:scale-105">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-yellow-600 mr-4">
                        <i class="fas fa-hamburger text-xl"></i>
                    </div>
                    <div class="text-white">
                        <h3 class="font-bold">Combo Familiar</h3>
                        <p class="text-sm text-yellow-100">4 hamburguesas + papas</p>
                    </div>
                </div>
                
                <div class="flex-shrink-0 w-72 h-24 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center p-4 transform transition hover:scale-105">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-blue-600 mr-4">
                        <i class="fas fa-coffee text-xl"></i>
                    </div>
                    <div class="text-white">
                        <h3 class="font-bold">Café + Postre</h3>
                        <p class="text-sm text-blue-100">Por solo $9.99</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Información con iconos animados -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
        <div class="bg-white p-6 rounded-lg shadow-sm group hover:shadow-md transition-all">
            <div class="w-16 h-16 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-4 
                      transition-all duration-300 group-hover:scale-110 group-hover:bg-red-100">
                <i class="fas fa-clock text-2xl text-red-500 group-hover:animate-pulse"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">Horario</h3>
            <p class="text-sm text-gray-600">Lunes a Domingo<br>12:00 - 22:00</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm group hover:shadow-md transition-all">
            <div class="w-16 h-16 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-4 
                      transition-all duration-300 group-hover:scale-110 group-hover:bg-red-100">
                <i class="fas fa-map-marker-alt text-2xl text-red-500 group-hover:animate-bounce"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">Ubicación</h3>
            <p class="text-sm text-gray-600">Av. Principal #123<br>Ciudad</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm group hover:shadow-md transition-all">
            <div class="w-16 h-16 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-4 
                      transition-all duration-300 group-hover:scale-110 group-hover:bg-red-100">
                <i class="fas fa-phone-alt text-2xl text-red-500 group-hover:animate-tada"></i>
            </div>
            <h3 class="font-bold text-lg mb-2">Reservaciones</h3>
            <p class="text-sm text-gray-600">(123) 456-7890</p>
            <a href="{{ route('reservation') }}" class="mt-2 inline-block text-sm text-red-600 hover:text-red-800">
                Reservar ahora <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

<!-- Añadir animación personalizada para el icono del teléfono -->
<style>
    @keyframes tada {
        0% {transform: scale(1);}
        10%, 20% {transform: scale(0.9) rotate(-3deg);}
        30%, 50%, 70%, 90% {transform: scale(1.1) rotate(3deg);}
        40%, 60%, 80% {transform: scale(1.1) rotate(-3deg);}
        100% {transform: scale(1) rotate(0);}
    }
    .group:hover .group-hover\:animate-tada {
        animation: tada 1s ease infinite;
    }
    
    /* Eliminar scrollbar pero mantener funcionalidad */
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>

<!-- Script para las pestañas de categorías y el scroll horizontal -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración de tabs para categorías
        const categoryTabs = document.querySelectorAll('.category-tab');
        const productCategories = document.querySelectorAll('.product-category');
        
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Actualizar estados de los tabs
                categoryTabs.forEach(t => t.classList.remove('active', 'bg-red-600', 'text-white'));
                categoryTabs.forEach(t => t.classList.add('bg-gray-100', 'text-gray-800'));
                this.classList.add('active', 'bg-red-600', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-800');
                
                // Mostrar el contenido correspondiente
                const categoryId = this.getAttribute('data-category');
                productCategories.forEach(container => {
                    if (container.getAttribute('data-category') === categoryId) {
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                });
                
                // Inicializar scroll buttons para el nuevo contenedor visible
                updateScrollButtons();
            });
        });
        
        // Configuración de los botones de desplazamiento
        const scrollLeftBtns = document.querySelectorAll('.scroll-left');
        const scrollRightBtns = document.querySelectorAll('.scroll-right');
        
        function updateScrollButtons() {
            const activeContainer = document.querySelector('.product-category:not(.hidden)');
            
            if (activeContainer) {
                const canScrollLeft = activeContainer.scrollLeft > 0;
                const canScrollRight = activeContainer.scrollLeft < activeContainer.scrollWidth - activeContainer.clientWidth;
                
                scrollLeftBtns.forEach(btn => {
                    btn.style.opacity = canScrollLeft ? '1' : '0.5';
                });
                
                scrollRightBtns.forEach(btn => {
                    btn.style.opacity = canScrollRight ? '1' : '0.5';
                });
            }
        }
        
        scrollLeftBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const activeContainer = document.querySelector('.product-category:not(.hidden)');
                if (activeContainer) {
                    activeContainer.scrollBy({ left: -220, behavior: 'smooth' });
                }
            });
        });
        
        scrollRightBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const activeContainer = document.querySelector('.product-category:not(.hidden)');
                if (activeContainer) {
                    activeContainer.scrollBy({ left: 220, behavior: 'smooth' });
                }
            });
        });
        
        // Establecer manejadores de eventos para actualizar el estado de los botones
        productCategories.forEach(container => {
            container.addEventListener('scroll', updateScrollButtons);
        });
        
        window.addEventListener('resize', updateScrollButtons);
        
        // Inicializar estado de botones
        updateScrollButtons();
    });
</script>

<style>
    /* Estilos para las pestañas activas */
    .category-tab.active {
        font-weight: 500;
    }
    
    /* Eliminar scrollbar pero mantener funcionalidad */
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>
@endsection