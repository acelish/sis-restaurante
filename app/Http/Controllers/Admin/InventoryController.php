<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::with('movements')->get();
        return view('admin.inventory.index', compact('items'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0'
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item de inventario creado exitosamente.');
    }

    public function show(InventoryItem $inventory)
    {
        $movements = $inventory->movements()->latest()->paginate(10);
        return view('admin.inventory.show', compact('inventory', 'movements'));
    }

    public function edit(InventoryItem $inventory)
    {
        return view('admin.inventory.edit', compact('inventory'));
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'quantity' => 'required|numeric|min:0',
            'alert_threshold' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0'
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item de inventario actualizado exitosamente.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')
            ->with('success', 'Item de inventario eliminado exitosamente.');
    }

    public function lowStock()
    {
        $items = InventoryItem::whereColumn('quantity', '<=', 'alert_threshold')->get();
        return view('admin.inventory.low-stock', compact('items'));
    }

    /**
     * Agregar stock a un item de inventario.
     */
    public function addStock(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        // Actualizar la cantidad del inventario
        $inventory->quantity += $validated['quantity'];
        $inventory->save();

        // Registrar el movimiento
        InventoryMovement::create([
            'inventory_item_id' => $inventory->id,
            'type' => 'entrada',
            'quantity' => $validated['quantity'],
            'user_id' => Auth::id(),
            'notes' => $validated['notes'] ?? 'Adición manual de stock',
        ]);

        return redirect()->route('inventory.index')
            ->with('success', "Se agregaron {$validated['quantity']} {$inventory->unit} al inventario de {$inventory->name}");
    }

    /**
     * Reducir stock de un item de inventario.
     */
    public function removeStock(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        // Verificar que hay suficiente stock
        if ($inventory->quantity < $validated['quantity']) {
            return redirect()->back()
                ->with('error', "No hay suficiente stock disponible. Stock actual: {$inventory->quantity} {$inventory->unit}");
        }

        // Actualizar la cantidad del inventario
        $inventory->quantity -= $validated['quantity'];
        $inventory->save();

        // Registrar el movimiento
        InventoryMovement::create([
            'inventory_item_id' => $inventory->id,
            'type' => 'salida',
            'quantity' => $validated['quantity'],
            'user_id' => Auth::id(),
            'notes' => $validated['notes'] ?? 'Reducción manual de stock',
        ]);

        return redirect()->route('inventory.index')
            ->with('success', "Se redujeron {$validated['quantity']} {$inventory->unit} del inventario de {$inventory->name}");
    }

    /**
     * Mostrar los movimientos de un item de inventario.
     */
    public function movements(InventoryItem $inventory)
    {
        $movements = $inventory->movements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.inventory.movements', compact('inventory', 'movements'));
    }

    /**
     * Ajustar el inventario (correcciones de inventario).
     */
    public function adjust(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'new_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $oldQuantity = $inventory->quantity;
        $newQuantity = $validated['new_quantity'];
        $difference = $newQuantity - $oldQuantity;

        // Actualizar la cantidad del inventario
        $inventory->quantity = $newQuantity;
        $inventory->save();

        // Registrar el movimiento
        InventoryMovement::create([
            'inventory_item_id' => $inventory->id,
            'type' => 'ajuste',
            'quantity' => abs($difference),
            'user_id' => Auth::id(),
            'notes' => "Ajuste de inventario: {$validated['reason']}. Cambio de {$oldQuantity} a {$newQuantity}.",
        ]);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', "Inventario ajustado correctamente. Nueva cantidad: {$newQuantity} {$inventory->unit}");
    }

    /**
     * Generar reporte de movimientos de inventario.
     */
    public function movementsReport(Request $request)
    {
        $query = InventoryMovement::with(['inventoryItem', 'user']);

        // Filtros
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('item_id') && $request->item_id) {
            $query->where('inventory_item_id', $request->item_id);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $items = InventoryItem::orderBy('name')->get();

        return view('admin.inventory.reports.movements', compact('movements', 'items'));
    }

    /**
     * Generar reporte de valuación de inventario.
     */
    public function valuationReport()
    {
        $items = InventoryItem::orderBy('name')->get();
        $totalValue = $items->sum(function ($item) {
            return $item->quantity * $item->cost;
        });

        return view('admin.inventory.reports.valuation', compact('items', 'totalValue'));
    }
}
