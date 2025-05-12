<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Mostrar el contenido del carrito
     */
    public function index()
    {
        $cartItems = Session::get('cart', []);
        $products = [];
        $total = 0;
        
        if (!empty($cartItems)) {
            foreach ($cartItems as $id => $item) {
                $product = Product::find($id);
                
                if ($product) {
                    $product->quantity = $item['quantity'];
                    $product->special_instructions = $item['special_instructions'] ?? '';
                    $product->subtotal = $product->price * $item['quantity'];
                    $total += $product->subtotal;
                    
                    $products[] = $product;
                }
            }
        }
        
        // Obtener mesas disponibles para reservar al hacer el pedido
        $tables = Table::where('status', 'available')->orderBy('number')->get();
        
        return view('client.cart.index', compact('products', 'total', 'tables'));
    }
    
    /**
     * Añadir un producto al carrito
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:20',
            'special_instructions' => 'nullable|string|max:255',
        ]);
        
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $specialInstructions = $request->special_instructions;
        
        // Verificar disponibilidad del producto
        $product = Product::findOrFail($productId);
        
        if (!$product->is_available) {
            return redirect()->back()->with('error', 'El producto no está disponible actualmente.');
        }
        
        if ($product->track_inventory && $product->stock < $quantity) {
            return redirect()->back()->with('error', 'No hay suficiente stock disponible.');
        }
        
        // Obtener el carrito actual de la sesión
        $cart = Session::get('cart', []);
        
        // Si el producto ya está en el carrito, actualizar la cantidad
        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            
            // Verificar que no exceda el stock disponible
            if ($product->track_inventory && $newQuantity > $product->stock) {
                return redirect()->back()->with('error', 'La cantidad solicitada excede el stock disponible.');
            }
            
            $cart[$productId]['quantity'] = $newQuantity;
            $cart[$productId]['special_instructions'] = $specialInstructions;
        } else {
            // Añadir nuevo producto al carrito
            $cart[$productId] = [
                'quantity' => $quantity,
                'special_instructions' => $specialInstructions,
            ];
        }
        
        // Guardar el carrito actualizado en la sesión
        Session::put('cart', $cart);
        
        return redirect()->back()->with('success', 'Producto añadido al carrito correctamente.');
    }
    
    /**
     * Actualizar la cantidad de un producto en el carrito
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:20',
            'special_instructions' => 'nullable|string|max:255',
        ]);
        
        $productId = $request->product_id;
        $quantity = $request->quantity;
        $specialInstructions = $request->special_instructions;
        
        // Verificar disponibilidad y stock
        $product = Product::findOrFail($productId);
        
        if ($product->track_inventory && $quantity > $product->stock) {
            return redirect()->back()->with('error', 'La cantidad solicitada excede el stock disponible.');
        }
        
        // Obtener el carrito actual
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            $cart[$productId]['special_instructions'] = $specialInstructions;
            
            Session::put('cart', $cart);
            return redirect()->route('cart')->with('success', 'Carrito actualizado correctamente.');
        }
        
        return redirect()->route('cart')->with('error', 'El producto no está en el carrito.');
    }
    
    /**
     * Eliminar un producto del carrito
     */
    public function remove($productId)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
            
            return redirect()->route('cart')->with('success', 'Producto eliminado del carrito.');
        }
        
        return redirect()->route('cart')->with('error', 'El producto no está en el carrito.');
    }
    
    /**
     * Vaciar todo el carrito
     */
    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('cart')->with('success', 'Carrito vaciado correctamente.');
    }
    
    /**
     * Procesar el pedido desde el carrito
     */
    public function checkout(Request $request)
    {
        // Validar datos del pedido
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'table_id' => 'nullable|required_if:order_type,dine_in|exists:tables,id',
            'address' => 'nullable|required_if:order_type,delivery|string|max:500',
            'payment_method' => 'required|in:cash,card',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Obtener carrito
        $cartItems = Session::get('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('cart')->with('error', 'No hay productos en el carrito.');
        }
        
        DB::beginTransaction();
        
        try {
            // Calcular totales
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($cartItems as $productId => $item) {
                $product = Product::findOrFail($productId);
                
                // Verificar disponibilidad y stock nuevamente
                if (!$product->is_available) {
                    throw new \Exception("El producto {$product->name} ya no está disponible.");
                }
                
                if ($product->track_inventory && $product->stock < $item['quantity']) {
                    throw new \Exception("No hay suficiente stock para {$product->name}.");
                }
                
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;
                
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
            }
            
            // Calcular impuestos (16% por ejemplo)
            $taxRate = 0.16;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;
            
            // Crear la orden
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'table_id' => $validated['order_type'] === 'dine_in' ? $validated['table_id'] : null,
                'customer_name' => $validated['customer_name'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => 0, // Sin descuentos por ahora
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending', // Asumimos que el pago se realiza al entregar
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'order_type' => $validated['order_type'],
                'delivery_address' => $validated['order_type'] === 'delivery' ? $validated['address'] : null,
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
            ]);
            
            // Crear los items de la orden
            foreach ($orderItems as $item) {
                $order->items()->create($item);
                
                // Actualizar inventario
                $product = Product::find($item['product_id']);
                if ($product->track_inventory) {
                    $product->stock -= $item['quantity'];
                    $product->save();
                }
            }
            
            // Actualizar estado de la mesa si es necesario
            if ($validated['order_type'] === 'dine_in') {
                $table = Table::find($validated['table_id']);
                $table->status = 'reserved'; // Marcamos como reservada en lugar de ocupada
                $table->save();
            }
            
            DB::commit();
            
            // Limpiar carrito después de procesar el pedido
            Session::forget('cart');
            
            // Guardar el ID del pedido en la sesión para mostrarlo en la página de confirmación
            Session::put('last_order_id', $order->id);
            
            return redirect()->route('cart.confirmation')->with('success', 'Pedido realizado correctamente.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart')->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }
    
    /**
     * Mostrar pantalla de confirmación del pedido
     */
    public function confirmation()
    {
        $orderId = Session::get('last_order_id');
        
        if (!$orderId) {
            return redirect()->route('menu');
        }
        
        $order = Order::with('items.product')->find($orderId);
        
        if (!$order) {
            return redirect()->route('menu');
        }
        
        return view('client.cart.confirmation', compact('order'));
    }

    /**
     * Verificar disponibilidad de mesa
     */
    public function checkTableAvailability(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:30|max:240',
        ]);
        
        $tableId = $request->table_id;
        $dateTime = Carbon::parse($request->date . ' ' . $request->time);
        $duration = $request->duration;
        
        // Verificar si hay reservaciones que se solapen con la hora elegida
        $endTime = $dateTime->copy()->addMinutes($duration);
        
        $conflictingReservations = Reservation::where('table_id', $tableId)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($dateTime, $endTime) {
                // La reserva comienza durante nuestra reserva
                $query->whereBetween('reservation_time', [$dateTime, $endTime])
                    // O la reserva termina durante nuestra reserva
                    ->orWhere(function($q) use ($dateTime, $endTime) {
                        $q->where('reservation_time', '<=', $dateTime)
                          ->whereRaw('DATE_ADD(reservation_time, INTERVAL duration MINUTE) >= ?', [$dateTime]);
                    });
            })
            ->count();
        
        // Verificar si hay pedidos activos para esta mesa
        $activeOrders = Order::where('table_id', $tableId)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->count();
        
        $isAvailable = $conflictingReservations == 0 && $activeOrders == 0;
        
        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Mesa disponible' : 'Mesa no disponible para el horario seleccionado'
        ]);
    }
}
