<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Mostrar el dashboard principal de reportes
     */
    public function index()
    {
        // Datos para resumen rápido - Últimos 30 días
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Ventas totales
        $totalSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        // Número de pedidos
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->count();

        // Ticket promedio
        $averageTicket = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Porcentaje de cambio respecto al periodo anterior (30 días antes)
        $previousStart = Carbon::now()->subDays(60);
        $previousEnd = Carbon::now()->subDays(30);

        $previousSales = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $salesChange = $previousSales > 0
            ? (($totalSales - $previousSales) / $previousSales) * 100
            : 100;

        // Productos más vendidos
        $topProducts = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                'products.id',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();

        // Ventas por categoría
        $salesByCategory = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('categories.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // Ventas por día (para gráfico)
        $salesByDay = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d/m'),
                    'total' => $item->total_sales
                ];
            });

        // Costos (gastos en inventario)
        $inventoryCosts = InventoryMovement::whereBetween('inventory_movements.created_at', [$startDate, $endDate])
            ->whereIn('type', ['purchase', 'entrada'])
            ->join('inventory_items', 'inventory_movements.inventory_item_id', '=', 'inventory_items.id')
            ->select(DB::raw('SUM(inventory_items.cost * inventory_movements.quantity) as total_cost'))
            ->first();

        $totalCosts = $inventoryCosts ? $inventoryCosts->total_cost : 0;

        // Ganancia estimada
        $estimatedProfit = $totalSales - $totalCosts;

        return view('admin.reports.index', compact(
            'totalSales',
            'totalOrders',
            'averageTicket',
            'salesChange',
            'topProducts',
            'salesByCategory',
            'salesByDay',
            'totalCosts',
            'estimatedProfit'
        ));
    }

    /**
     * Reporte de ventas
     */
    public function sales(Request $request)
    {
        // Filtros de fecha
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        
        // Filtros adicionales
        $orderType = $request->order_type ?? '';
        $categoryId = $request->category_id ?? '';
        
        // Consulta base
        $ordersQuery = Order::with(['items.product.category', 'table', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        // Aplicar filtros adicionales
        if ($orderType) {
            $ordersQuery->where('order_type', $orderType);
        }
        
        if ($categoryId) {
            $ordersQuery->whereHas('items.product', function($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }
        
        // Obtener órdenes paginadas
        $orders = $ordersQuery->latest()->paginate(20);
        
        // Estadísticas
        $totalSales = $ordersQuery->where('status', '!=', 'cancelled')->sum('total');
        $orderCount = $ordersQuery->where('status', '!=', 'cancelled')->count();
        $totalTax = $ordersQuery->where('status', '!=', 'cancelled')->sum('tax');
        $totalDiscount = $ordersQuery->where('status', '!=', 'cancelled')->sum('discount');
        
        // Datos para el gráfico de ventas diarias
        $dailySales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $dailySalesChart = [
            'dates' => $dailySales->pluck('date')->toArray(),
            'sales' => $dailySales->pluck('total')->toArray()
        ];
        
        // Conteo por tipo de pedido
        $orderTypeCount = [
            'dine_in' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->where('order_type', 'dine_in')
                ->count(),
            'takeaway' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->where('order_type', 'takeaway')
                ->count(),
            'delivery' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->where('order_type', 'delivery')
                ->count()
        ];
        
        // Obtener todas las categorías para el filtro
        $categories = Category::orderBy('name')->get();
        
        return view('admin.reports.sales', compact(
            'orders',
            'startDate',
            'endDate',
            'orderType',
            'categoryId',
            'totalSales',
            'orderCount',
            'totalTax',
            'totalDiscount',
            'dailySalesChart',
            'orderTypeCount',
            'categories'
        ));
    }

    /**
     * Reporte de productos
     */
    public function products(Request $request)
    {
        // Filtrado por fechas
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Filtrado por categoría
        $categoryId = $request->category_id;
        
        // Añadir variable de ordenamiento
        $sortBy = $request->sort_by ?? 'quantity';

        // Query base
        $productsQuery = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('order_items.created_at', [$startDate, $endDate]);

        // Aplicar filtro de categoría
        if ($categoryId) {
            $productsQuery->where('products.category_id', $categoryId);
        }

        // Obtener datos de productos vendidos
        $productSales = $productsQuery->select(
            'products.id',
            'products.name',
            'products.price',
            'products.cost',
            'categories.name as category_name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.subtotal) as total_revenue')
        )
            ->groupBy('products.id', 'products.name', 'products.price', 'products.cost', 'categories.name')
            ->orderBy($sortBy === 'revenue' ? 'total_revenue' : 'total_quantity', 'desc')
            ->get();

        // Calcular margen de beneficio para cada producto
        $productSales->transform(function ($product) {
            $product->profit_margin = $product->cost ? (($product->price - $product->cost) / $product->price) * 100 : null;
            $product->total_profit = $product->cost ? ($product->price - $product->cost) * $product->total_quantity : null;
            return $product;
        });

        // Totales
        $totalRevenue = $productSales->sum('total_revenue');
        $totalQuantity = $productSales->sum('total_quantity');
        $totalProfit = $productSales->sum('total_profit');
        $productCount = $productSales->count();

        // Datos para gráficos - Top productos por cantidad
        $topProductsByQuantity = $productSales->take(10);
        
        // Datos para gráficos - Top productos por ingresos
        $topProductsByRevenue = $productSales->sortByDesc('total_revenue')->take(10);
        
        // Obtener el producto más vendido
        $topProduct = $productSales->sortByDesc('total_quantity')->first();

        // Top categorías - Definir esta variable antes de usarla
        $topCategories = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // Obtener la categoría más vendida
        $topCategory = $topCategories->first();
        if ($topCategory) {
            $topCategory->product_count = $productSales->where('category_name', $topCategory->name)->count();
            $topCategory->total_sales = $productSales->where('category_name', $topCategory->name)->sum('total_revenue');
        }

        // Paginación manual para la tabla de productos
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $productSales->slice($offset, $perPage)->values(),
            $productSales->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Datos para el gráfico de tendencias
        // Tendencias de venta por categoría (últimos 6 meses)
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        
        $monthlyCategorySales = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->where('order_items.created_at', '>=', $sixMonthsAgo)
            ->select(
                'categories.name',
                DB::raw('YEAR(order_items.created_at) as year'),
                DB::raw('MONTH(order_items.created_at) as month'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('categories.name', 'year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Preparar datos para el gráfico
        $categoryTrends = [
            'dates' => [],
            'categories' => []
        ];
        
        // Generar fechas para los últimos 6 meses
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths(5 - $i)->startOfMonth();
            $months[] = [
                'year' => $date->year,
                'month' => $date->month,
                'label' => $date->format('M Y')
            ];
            $categoryTrends['dates'][] = $date->format('M Y');
        }
        
        // Obtener las 5 categorías más vendidas
        $topCategoryNames = $topCategories->take(5)->pluck('name');
        
        foreach ($topCategoryNames as $categoryName) {
            $salesByMonth = [];
            
            foreach ($months as $month) {
                $monthlySale = $monthlyCategorySales
                    ->where('name', $categoryName)
                    ->where('year', $month['year'])
                    ->where('month', $month['month'])
                    ->first();
                    
                $salesByMonth[] = $monthlySale ? $monthlySale->total_sales : 0;
            }
            
            $categoryTrends['categories'][] = [
                'name' => $categoryName,
                'sales' => $salesByMonth
            ];
        }

        // Obtener todas las categorías para el filtro de selección
        $categories = Category::orderBy('name')->get();
        
        return view('admin.reports.products', compact(
            'productSales',
            'totalRevenue',
            'totalQuantity',
            'totalProfit',
            'topCategories',
            'startDate',
            'endDate',
            'categoryId',
            'categories',  // Ahora está definida correctamente
            'sortBy',
            'products',
            'productCount',
            'topProductsByQuantity',
            'topProductsByRevenue',
            'topProduct',
            'topCategory',
            'categoryTrends'
        ));
    }

    /**
     * Reporte de inventario
     */
    public function inventory(Request $request)
    {
        // Filtrado por fechas
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Filtrado por tipo de movimiento
        $movementType = $request->movement_type;
        
        // Filtrado por categoría (para cuando añadamos esta columna)
        $itemCategory = $request->item_category ?? '';

        // Obtener todos los items de inventario con su stock actual
        $inventoryItems = InventoryItem::withCount(['movements as total_entrada' => function ($query) {
            $query->whereIn('type', ['purchase', 'entrada', 'ajuste'])
                ->where('quantity', '>', 0);
        }])
            ->withCount(['movements as total_salida' => function ($query) {
                $query->whereIn('type', ['usage', 'salida', 'ajuste'])
                    ->where('quantity', '<', 0);
            }])
            ->get();

        // Calcular el valor total del inventario
        $inventoryValue = $inventoryItems->sum(function ($item) {
            return $item->quantity * $item->cost;
        });

        // Query base para movimientos
        $movementsQuery = InventoryMovement::with(['inventoryItem', 'user', 'order'])
            ->whereBetween('inventory_movements.created_at', [$startDate, $endDate]);

        // Aplicar filtro de tipo de movimiento
        if ($movementType) {
            $movementsQuery->where('type', $movementType);
        }

        // Obtener movimientos
        $movements = $movementsQuery->orderBy('created_at', 'desc')->get();
        
        // Paginar los movimientos (añadir esta línea)
        $inventoryMovements = $movementsQuery->orderBy('created_at', 'desc')->paginate(15);

        // Calcular el costo total de los movimientos
        $movementCost = $movements->sum(function ($movement) {
            return $movement->quantity * $movement->inventoryItem->cost;
        });

        // Items con stock bajo
        $lowStockItems = $inventoryItems->filter(function ($item) {
            return $item->quantity <= $item->alert_threshold;
        });

        // Items más utilizados
        $mostUsedItems = InventoryMovement::whereIn('type', ['usage', 'salida'])
            ->whereBetween('inventory_movements.created_at', [$startDate, $endDate])
            ->select(
                'inventory_item_id',
                DB::raw('SUM(ABS(quantity)) as total_used')
            )
            ->groupBy('inventory_item_id')
            ->orderBy('total_used', 'desc')
            ->take(5)
            ->with('inventoryItem')
            ->get();
            
        // Contar los items con stock bajo y agotados
        $lowStockCount = $lowStockItems->count();
        $outOfStockCount = $inventoryItems->filter(function ($item) {
            return $item->quantity <= 0;
        })->count();
        
        // Contar todos los ítems de inventario
        $inventoryItemCount = $inventoryItems->count();
        
        // Definir las categorías de inventario (simulación hasta que se añada la columna)
        $itemCategories = ['Alimentos', 'Bebidas', 'Utensilios', 'Limpieza', 'Otros'];
        
        // Datos para el gráfico de movimientos de inventario
        $inventoryMovementsChart = [
            'dates' => [],
            'purchases' => [],
            'sales' => [],
            'adjustments' => [],
            'waste' => []
        ];
        
        // Datos para el gráfico de categorías
        $inventoryCategoryChart = [];
        
        // Paginar los items de inventario
        $inventoryItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $inventoryItems->slice(0, 15)->values(),
            $inventoryItems->count(),
            15,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.reports.inventory', compact(
            'inventoryItems',
            'inventoryValue',
            'movements',
            'movementCost',
            'lowStockItems',
            'lowStockCount',
            'outOfStockCount',
            'startDate',
            'endDate',
            'movementType',
            'itemCategory',
            'itemCategories',
            'inventoryItemCount',
            'mostUsedItems',
            'inventoryMovements',           // Añadir esta variable
            'inventoryMovementsChart',      // Añadir esta variable
            'inventoryCategoryChart'        // Añadir esta variable
        ));
    }

    /**
     * Reporte financiero
     */
    public function financial(Request $request)
    {
        // Filtrado por fechas
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Ingresos (ventas)
        $sales = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(subtotal) as subtotal'),
                DB::raw('SUM(tax) as tax'),
                DB::raw('SUM(discount) as discount'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Gastos (compras de inventario)
        $expenses = InventoryMovement::whereIn('type', ['purchase', 'entrada'])
            ->whereBetween('inventory_movements.created_at', [$startDate, $endDate])
            ->join('inventory_items', 'inventory_movements.inventory_item_id', '=', 'inventory_items.id')
            ->select(
                DB::raw('DATE(inventory_movements.created_at) as date'),
                DB::raw('SUM(inventory_items.cost * inventory_movements.quantity) as total_cost')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Combinar datos por fecha
        $financialData = [];
        $dates = collect($sales->pluck('date'))->merge($expenses->pluck('date'))->unique()->sort();

        foreach ($dates as $date) {
            $saleData = $sales->firstWhere('date', $date);
            $expenseData = $expenses->firstWhere('date', $date);

            $financialData[] = [
                'date' => Carbon::parse($date)->format('d/m/Y'),
                'sales' => $saleData ? $saleData->total : 0,
                'expenses' => $expenseData ? $expenseData->total_cost : 0,
                'profit' => ($saleData ? $saleData->total : 0) - ($expenseData ? $expenseData->total_cost : 0)
            ];
        }

        // Totales
        $totalSales = $sales->sum('total');
        $totalExpenses = $expenses->sum('total_cost');
        $totalProfit = $totalSales - $totalExpenses;
        $totalTax = $sales->sum('tax');

        // Método de pago
        $paymentMethods = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->orderBy('total', 'desc')
            ->get();

        // Margen de beneficio por categoría
        $categoryMargins = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('SUM(products.cost * order_items.quantity) as cost')
            )
            ->groupBy('categories.name')
            ->get()
            ->map(function ($item) {
                $item->profit = $item->revenue - $item->cost;
                $item->margin = $item->revenue > 0 ? ($item->profit / $item->revenue) * 100 : 0;
                return $item;
            });

        return view('admin.reports.financial', compact(
            'financialData',
            'totalSales',
            'totalExpenses',
            'totalProfit',
            'totalTax',
            'paymentMethods',
            'categoryMargins',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Reporte de presupuestos
     */
    public function budget(Request $request)
    {
        // Obtener el mes para el presupuesto
        $selectedMonth = $request->month ? Carbon::parse($request->month . '-01') : Carbon::now()->startOfMonth();

        // Meses para seleccionar
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->addMonths($i)->startOfMonth();
            $months[$date->format('Y-m')] = $date->format('F Y');
        }

        // Datos del mes anterior para comparativas
        $lastMonth = $selectedMonth->copy()->subMonth();

        // Ventas del mes anterior por categoría
        $lastMonthSales = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();

        // Gastos del mes anterior por tipo
        $lastMonthExpenses = InventoryMovement::whereIn('type', ['purchase', 'entrada'])
            ->whereBetween('inventory_movements.created_at', [$lastMonth->startOfMonth(), $lastMonth->endOfMonth()])
            ->join('inventory_items', 'inventory_movements.inventory_item_id', '=', 'inventory_items.id')
            ->select(
                'inventory_items.id',
                'inventory_items.name',
                DB::raw('SUM(inventory_items.cost * inventory_movements.quantity) as total_cost')
            )
            ->groupBy('inventory_items.id', 'inventory_items.name')
            ->get();

        // Cargar presupuesto existente si lo hay
        // Aquí podrías cargar de una tabla de presupuestos, pero para simplificar usaremos datos estimados

        // Categorías para presupuesto de ventas
        $categories = Category::orderBy('name')->get()->map(function ($category) use ($lastMonthSales) {
            $lastSales = $lastMonthSales->firstWhere('id', $category->id);
            $category->last_month_sales = $lastSales ? $lastSales->total_sales : 0;

            // Estimación para el próximo mes (5% de crecimiento por defecto)
            $category->estimated_sales = $category->last_month_sales * 1.05;

            return $category;
        });

        // Items de inventario para presupuesto de compras
        $inventoryItems = InventoryItem::orderBy('name')->get()->map(function ($item) use ($lastMonthExpenses) {
            $lastExpense = $lastMonthExpenses->firstWhere('id', $item->id);
            $item->last_month_cost = $lastExpense ? $lastExpense->total_cost : 0;

            // Estimación para el próximo mes basada en inventario actual y uso histórico
            $item->estimated_cost = $item->last_month_cost;

            return $item;
        });

        // Totales para presupuesto
        $totalEstimatedSales = $categories->sum('estimated_sales');
        $totalEstimatedExpenses = $inventoryItems->sum('estimated_cost');
        $estimatedProfit = $totalEstimatedSales - $totalEstimatedExpenses;

        // Comparativa con mes anterior
        $totalLastMonthSales = $lastMonthSales->sum('total_sales');
        $totalLastMonthExpenses = $lastMonthExpenses->sum('total_cost');
        $lastMonthProfit = $totalLastMonthSales - $totalLastMonthExpenses;

        // Cambios porcentuales
        $salesChange = $totalLastMonthSales > 0 ? (($totalEstimatedSales - $totalLastMonthSales) / $totalLastMonthSales) * 100 : 0;
        $expensesChange = $totalLastMonthExpenses > 0 ? (($totalEstimatedExpenses - $totalLastMonthExpenses) / $totalLastMonthExpenses) * 100 : 0;
        $profitChange = $lastMonthProfit > 0 ? (($estimatedProfit - $lastMonthProfit) / $lastMonthProfit) * 100 : 0;

        return view('admin.reports.budget', compact(
            'selectedMonth',
            'months',
            'categories',
            'inventoryItems',
            'totalEstimatedSales',
            'totalEstimatedExpenses',
            'estimatedProfit',
            'totalLastMonthSales',
            'totalLastMonthExpenses',
            'lastMonthProfit',
            'salesChange',
            'expensesChange',
            'profitChange'
        ));
    }

    /**
     * Exportar reporte de ventas a CSV
     */
    public function exportSales(Request $request)
    {
        // Parámetros de filtro
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // Obtener órdenes
        $orders = Order::with(['items.product', 'table', 'user'])
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Crear CSV
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=ventas_" . $startDate->format('Y-m-d') . "_" . $endDate->format('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Encabezados
            fputcsv($file, [
                'ID Pedido',
                'Fecha',
                'Cliente',
                'Tipo',
                'Mesa',
                'Subtotal',
                'Impuestos',
                'Descuento',
                'Total',
                'Estado',
                'Método de Pago'
            ]);

            // Datos
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->customer_name,
                    $order->order_type,
                    $order->table ? $order->table->number : 'N/A',
                    $order->subtotal,
                    $order->tax,
                    $order->discount,
                    $order->total,
                    $order->status,
                    $order->payment_method
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
