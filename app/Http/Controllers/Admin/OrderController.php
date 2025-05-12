<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'table', 'employee']);
        
        // Filtros
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->has('order_type') && $request->order_type != '') {
            $query->where('order_type', $request->order_type);
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $products = Product::where('is_available', true)->orderBy('name')->get();
        $tables = Table::where('status', 'available')->orderBy('number')->get();
        
        // En lugar de utilizar role(), puedes:
        // Opción 1: Obtener todos los usuarios (si no tienes muchos)
        $employees = User::orderBy('name')->get();
        
        // Opción 2: Si tienes un campo is_employee o similar en tu tabla users
        // $employees = User::where('is_employee', true)->orderBy('name')->get();
        
        return view('admin.orders.create', compact('products', 'tables', 'employees'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
            'employee_id' => 'nullable|exists:users,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery,online',
            'payment_method' => 'nullable|string',
            'payment_status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.special_instructions' => 'nullable|string',
        ]);
        
        // Iniciar transacción para garantizar que todo se guarde correctamente
        DB::beginTransaction();
        
        try {
            // Calcular totales
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($validated['products'] as $item) {
                $product = Product::findOrFail($item['id']);
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;
                
                // Preparar los items de la orden
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $itemSubtotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
                
                // Verificar stock si se controla inventario
                if ($product->track_inventory && $product->stock < $item['quantity']) {
                    return redirect()->back()->withErrors(['stock' => "No hay suficiente stock para {$product->name}"]);
                }
            }
            
            // Calcular impuestos (16% por ejemplo)
            $taxRate = 0.16;
            $tax = $subtotal * $taxRate;
            $total = $subtotal + $tax;
            
            // Crear la orden
            $order = Order::create([
                'user_id' => $validated['user_id'] ?? null,
                'table_id' => $validated['table_id'] ?? null,
                'employee_id' => $validated['employee_id'] ?? null,
                'customer_name' => $validated['customer_name'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => 0, // Por ahora sin descuentos
                'total' => $total,
                'status' => 'pending',
                'payment_status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'order_type' => $validated['order_type'],
            ]);
            
            // Crear los items de la orden
            foreach ($orderItems as $item) {
                $order->items()->create($item);
                
                // Actualizar inventario si se controla
                $product = Product::find($item['product_id']);
                if ($product->track_inventory) {
                    $product->stock -= $item['quantity'];
                    $product->save();
                    
                    // Reducir stock de ingredientes si tiene
                    foreach ($product->inventoryItems as $inventoryItem) {
                        $requiredQuantity = $inventoryItem->pivot->quantity * $item['quantity'];
                        $inventoryItem->quantity -= $requiredQuantity;
                        $inventoryItem->save();
                        
                        // Registrar movimiento de inventario
                        InventoryMovement::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'type' => 'usage',
                            'quantity' => $requiredQuantity,
                            'order_id' => $order->id,
                            'user_id' => Auth::id(),
                            'notes' => "Usado en orden #{$order->id}",
                        ]);
                    }
                }
            }
            
            // Actualizar estado de la mesa si es necesario
            if ($validated['table_id']) {
                $table = Table::find($validated['table_id']);
                $table->status = 'occupied';
                $table->save();
            }
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Orden creada exitosamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al crear la orden: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'table', 'employee']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $order->load(['items.product', 'user', 'table', 'employee']);
        $products = Product::where('is_available', true)->orderBy('name')->get();
        $tables = Table::orderBy('number')->get();
        
        // Mismo cambio que en el método create()
        $employees = User::orderBy('name')->get();
        
        return view('admin.orders.edit', compact('order', 'products', 'tables', 'employees'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'employee_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,preparing,ready,delivered,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        // Si la orden se cancela, restaurar stock
        if ($validated['status'] == 'cancelled' && $order->status != 'cancelled') {
            foreach ($order->items as $item) {
                $product = $item->product;
                
                if ($product->track_inventory) {
                    $product->stock += $item->quantity;
                    $product->save();
                    
                    // Restaurar stock de ingredientes
                    foreach ($product->inventoryItems as $inventoryItem) {
                        $quantityToRestore = $inventoryItem->pivot->quantity * $item->quantity;
                        $inventoryItem->quantity += $quantityToRestore;
                        $inventoryItem->save();
                        
                        // Registrar movimiento de inventario
                        InventoryMovement::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'type' => 'adjustment',
                            'quantity' => $quantityToRestore,
                            'order_id' => $order->id,
                            'user_id' => Auth::id(),
                            'notes' => "Restaurado por cancelación de orden #{$order->id}",
                        ]);
                    }
                }
            }
        }
        
        // Si la orden se completa, liberar la mesa
        if ($validated['status'] == 'completed' && $order->status != 'completed' && $order->table_id) {
            $table = $order->table;
            $table->status = 'available';
            $table->save();
        }
        
        $order->update($validated);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Orden actualizada exitosamente');
    }

    /**
     * Actualizar solo el estado de la orden.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,delivered,completed,cancelled',
        ]);
        
        // Lógica igual a la del método update pero solo para el estado
        if ($validated['status'] == 'cancelled' && $order->status != 'cancelled') {
            // Restablecer inventario (código similar al método update)
            // ...
        }
        
        if ($validated['status'] == 'completed' && $order->status != 'completed' && $order->table_id) {
            $table = $order->table;
            $table->status = 'available';
            $table->save();
        }
        
        $order->status = $validated['status'];
        $order->save();
        
        return redirect()->back()->with('success', 'Estado de la orden actualizado exitosamente');
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(Order $order)
    {
        // No permitir eliminar órdenes completadas o en proceso
        if ($order->status != 'cancelled' && $order->status != 'completed') {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar una orden en proceso.']);
        }
        
        $order->delete();
        
        return redirect()->route('orders.index')
            ->with('success', 'Orden eliminada exitosamente');
    }
}
