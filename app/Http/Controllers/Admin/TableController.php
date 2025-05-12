<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtrar por estado si se proporciona
        $query = Table::query();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('capacity', 'like', "%{$search}%");
            });
        }

        $tables = $query->orderBy('number')->paginate(15);
        
        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tables.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:20|unique:tables,number',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        Table::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Mesa creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        // Cargar reservas recientes
        $reservations = $table->reservations()
            ->where('reservation_time', '>=', now()->subDays(7))
            ->orderBy('reservation_time', 'desc')
            ->take(5)
            ->get();

        // Cargar órdenes recientes
        $orders = $table->orders()
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.tables.show', compact('table', 'reservations', 'orders'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tables')->ignore($table)
            ],
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update($validated);

        return redirect()->route('tables.show', $table)
            ->with('success', 'Mesa actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        // Verificar si la mesa tiene órdenes activas o reservas pendientes
        $hasActiveOrders = $table->orders()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        $hasPendingReservations = $table->reservations()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->where('reservation_time', '>=', now())
            ->exists();

        if ($hasActiveOrders) {
            return redirect()->route('tables.show', $table)
                ->with('error', 'No se puede eliminar la mesa porque tiene órdenes activas');
        }

        if ($hasPendingReservations) {
            return redirect()->route('tables.show', $table)
                ->with('error', 'No se puede eliminar la mesa porque tiene reservas pendientes');
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Mesa eliminada exitosamente');
    }

    /**
     * Actualiza rápidamente el estado de una mesa
     */
    public function updateStatus(Request $request, Table $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Estado de la mesa actualizado correctamente');
    }

    /**
     * Muestra el mapa de mesas
     */
    public function map()
    {
        $tables = Table::orderBy('number')->get();
        return view('admin.tables.map', compact('tables'));
    }
}
