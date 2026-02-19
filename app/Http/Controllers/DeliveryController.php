<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Route;
use App\Models\Client;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    // Lista de entregas
    public function index()
    {
        $deliveries = Delivery::with('route', 'client')
            ->paginate(10);
        
        return view('deliveries.index', compact('deliveries'));
    }

    // Formulario para crear entrega
    public function create()
    {
        $routes = Route::all();
        $clients = Client::all();
        
        // Crear etiqueta para la ruta
        $routes = $routes->map(function($route) {
            $route->routeLabel = $route->origin . ' → ' . $route->destination;
            return $route;
        });
        
        return view('deliveries.create', compact('routes', 'clients'));
    }

    // Guardar entrega
    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'client_id' => 'required|exists:clients,id',
            'package_info' => 'required|string|max:1000',
            'status' => 'required|in:pending,in_transit,delivered,failed,cancelled',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        Delivery::create($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega creada correctamente');
    }

    // Ver detalles de entrega
    public function show(Delivery $delivery)
    {
        $delivery->load('route', 'client');
        
        return view('deliveries.show', compact('delivery'));
    }

    // Formulario para editar entrega
    public function edit(Delivery $delivery)
    {
        $routes = Route::all();
        $clients = Client::all();
        
        // Crear etiqueta para la ruta
        $routes = $routes->map(function($route) {
            $route->routeLabel = $route->origin . ' → ' . $route->destination;
            return $route;
        });
        
        return view('deliveries.edit', compact('delivery', 'routes', 'clients'));
    }

    // Actualizar entrega
    public function update(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'client_id' => 'required|exists:clients,id',
            'package_info' => 'required|string|max:1000',
            'status' => 'required|in:pending,in_transit,delivered,failed,cancelled',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $delivery->update($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega actualizada correctamente');
    }

    // Eliminar entrega
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Entrega eliminada correctamente');
    }
}
