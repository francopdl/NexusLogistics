<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Fleet;
use App\Services\GeoLocationService;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    // Lista de rutas
    public function index()
    {
        $routes = Route::with('fleet')
            ->paginate(10);
        
        return view('routes.index', compact('routes'));
    }

    // Formulario para crear ruta
    public function create()
    {
        $fleets = Fleet::all();
        
        return view('routes.create', compact('fleets'));
    }

    // Guardar ruta
    public function store(Request $request)
    {
        $geoService = new GeoLocationService();
        
        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'estimated_departure' => 'required|date_format:Y-m-d\TH:i',
            'estimated_arrival' => 'required|date_format:Y-m-d\TH:i|after:estimated_departure',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        // Convertir formato datetime-local
        $validated['estimated_departure'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['estimated_departure']);
        $validated['estimated_arrival'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['estimated_arrival']);

        // Geocodificar origen
        $originCoords = $geoService->getCoordinatesFromAddress($validated['origin']);
        if ($originCoords) {
            $validated['origin_latitude'] = $originCoords['latitude'];
            $validated['origin_longitude'] = $originCoords['longitude'];
        }

        // Geocodificar destino
        $destCoords = $geoService->getCoordinatesFromAddress($validated['destination']);
        if ($destCoords) {
            $validated['destination_latitude'] = $destCoords['latitude'];
            $validated['destination_longitude'] = $destCoords['longitude'];
        }

        // Calcular distancia y duración
        if ($originCoords && $destCoords) {
            $routeInfo = $geoService->calculateDistance($originCoords, $destCoords);
            if ($routeInfo) {
                $validated['distance_km'] = $routeInfo['distance_km'];
                $validated['duration_seconds'] = (int)$routeInfo['duration_seconds'];
            }
        }

        Route::create($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Ruta creada correctamente');
    }

    // Ver detalles de ruta
    public function show(Route $route)
    {
        $route->load('fleet', 'deliveries');
        
        return view('routes.show', compact('route'));
    }

    // Formulario para editar ruta
    public function edit(Route $route)
    {
        $fleets = Fleet::all();
        
        return view('routes.edit', compact('route', 'fleets'));
    }

    // Actualizar ruta
    public function update(Request $request, Route $route)
    {
        $geoService = new GeoLocationService();
        
        $validated = $request->validate([
            'fleet_id' => 'required|exists:fleets,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'estimated_departure' => 'required|date_format:Y-m-d\TH:i',
            'estimated_arrival' => 'required|date_format:Y-m-d\TH:i|after:estimated_departure',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        // Convertir formato datetime-local
        $validated['estimated_departure'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['estimated_departure']);
        $validated['estimated_arrival'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['estimated_arrival']);

        // Geocodificar origen si cambió
        if ($validated['origin'] !== $route->origin) {
            $originCoords = $geoService->getCoordinatesFromAddress($validated['origin']);
            if ($originCoords) {
                $validated['origin_latitude'] = $originCoords['latitude'];
                $validated['origin_longitude'] = $originCoords['longitude'];
            }
        }

        // Geocodificar destino si cambió
        if ($validated['destination'] !== $route->destination) {
            $destCoords = $geoService->getCoordinatesFromAddress($validated['destination']);
            if ($destCoords) {
                $validated['destination_latitude'] = $destCoords['latitude'];
                $validated['destination_longitude'] = $destCoords['longitude'];
            }
        }

        // Recalcular distancia si cambió origen o destino
        if ($validated['origin'] !== $route->origin || $validated['destination'] !== $route->destination) {
            $originCoords = [
                'latitude' => $validated['origin_latitude'] ?? $route->origin_latitude,
                'longitude' => $validated['origin_longitude'] ?? $route->origin_longitude,
            ];
            $destCoords = [
                'latitude' => $validated['destination_latitude'] ?? $route->destination_latitude,
                'longitude' => $validated['destination_longitude'] ?? $route->destination_longitude,
            ];
            
            if ($originCoords['latitude'] && $destCoords['latitude']) {
                $routeInfo = $geoService->calculateDistance($originCoords, $destCoords);
                if ($routeInfo) {
                    $validated['distance_km'] = $routeInfo['distance_km'];
                    $validated['duration_seconds'] = (int)$routeInfo['duration_seconds'];
                }
            }
        }

        $route->update($validated);

        return redirect()->route('routes.index')
            ->with('success', 'Ruta actualizada correctamente');
    }

    // Eliminar ruta
    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('routes.index')
            ->with('success', 'Ruta eliminada correctamente');
    }
}
