<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Fleet;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Lista de vehículos
    public function index()
    {
        $vehicles = Vehicle::with('fleet')
            ->paginate(10);
        
        return view('vehicles.index', compact('vehicles'));
    }

    // Formulario para crear vehículo
    public function create()
    {
        $fleets = Fleet::all();
        
        return view('vehicles.create', compact('fleets'));
    }

    // Guardar vehículo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'license_plate' => 'required|string|unique:vehicles|max:15',
            'vehicle_type' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'status' => 'required|in:available,in_use,maintenance,inactive',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo creado correctamente');
    }

    // Ver detalles de vehículo
    public function show(Vehicle $vehicle)
    {
        $vehicle->load('fleet');
        
        return view('vehicles.show', compact('vehicle'));
    }

    // Formulario para editar vehículo
    public function edit(Vehicle $vehicle)
    {
        $fleets = Fleet::all();
        
        return view('vehicles.edit', compact('vehicle', 'fleets'));
    }

    // Actualizar vehículo
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $vehicle->id . '|max:15',
            'vehicle_type' => 'required|string|max:255',
            'manufacturer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:' . date('Y'),
            'status' => 'required|in:available,in_use,maintenance,inactive',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo actualizado correctamente');
    }

    // Eliminar vehículo
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehículo eliminado correctamente');
    }
}
