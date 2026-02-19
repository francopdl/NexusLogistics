<?php

/**
 * EJEMPLOS DE USO DEL SISTEMA NEXUS LOGISTICS
 * 
 * Este archivo contiene ejemplos prácticos de cómo usar las diferentes
 * partes del sistema dentro de controladores, servicios o en Tinker.
 */

// ============================================================================
// 1. GESTIÓN DE USUARIOS Y ROLES
// ============================================================================

// Crear un usuario nuevo
$user = \App\Models\User::create([
    'name' => 'Juan Pérez',
    'email' => 'juan@nexus.local',
    'password' => bcrypt('segura123'),
    'company_id' => 1
]);

// Asignar un rol
$managerRole = \App\Models\Role::where('name', 'manager')->first();
$user->roles()->attach($managerRole);

// Asignar múltiples roles
$user->roles()->sync([
    \App\Models\Role::where('name', 'driver')->first()->id,
    \App\Models\Role::where('name', 'client')->first()->id
]);

// Verificar rol
if ($user->hasRole('admin')) {
    // Hacer algo solo para admins
}

// Verificar si tiene cualquiera de estos roles
if ($user->hasAnyRole(['admin', 'manager'])) {
    // Usuario es admin o manager
}

// ============================================================================
// 2. GESTIÓN DE EMPRESAS
// ============================================================================

// Crear empresa
$company = \App\Models\Company::create([
    'name' => 'Transporte España S.L.',
    'email' => 'contacto@transporte.es',
    'phone' => '+34 912 345 678',
    'address' => 'Calle Principal 123',
    'city' => 'Madrid',
    'country' => 'España',
    'postal_code' => '28001'
]);

// Acceder a información de empresa
$company->name;           // Nombre
$company->users;          // Todos los usuarios
$company->clients;        // Todos los clientes
$company->fleets;         // Todas las flotas

// Contar recursos
$company->users()->count();     // Número de usuarios
$company->clients()->count();   // Número de clientes
$company->fleets()->count();    // Número de flotas

// ============================================================================
// 3. GESTIÓN DE FLOTAS Y VEHÍCULOS
// ============================================================================

// Crear flota
$fleet = \App\Models\Fleet::create([
    'name' => 'Flota Centro',
    'company_id' => 1,
    'description' => 'Flota para distribución en zona centro'
]);

// Crear vehículo
$vehicle = \App\Models\Vehicle::create([
    'fleet_id' => $fleet->id,
    'license_plate' => 'MAD-1234-ABC',
    'vehicle_type' => 'Furgoneta',
    'manufacturer' => 'Mercedes-Benz',
    'model' => 'Sprinter',
    'year' => 2023,
    'status' => 'available'  // available, in_use, maintenance
]);

// Obtener vehículos de una flota
$fleet->vehicles;

// Cambiar estado de vehículo
$vehicle->update(['status' => 'in_use']);

// ============================================================================
// 4. GESTIÓN DE CLIENTES Y ENTREGAS
// ============================================================================

// Crear cliente
$client = \App\Models\Client::create([
    'name' => 'Tienda Online S.A.',
    'email' => 'info@tienda.es',
    'phone' => '+34 933 456 789',
    'address' => 'Avenida Comercial 45',
    'city' => 'Barcelona',
    'company_id' => 1
]);

// Crear ruta
$route = \App\Models\Route::create([
    'fleet_id' => $fleet->id,
    'origin' => 'Madrid, Polígono Industrial',
    'destination' => 'Barcelona, Centro Urbano',
    'estimated_departure' => now()->addDay(),
    'estimated_arrival' => now()->addDay()->addHours(8),
    'status' => 'pending'  // pending, in_progress, completed, cancelled
]);

// Crear entrega
$delivery = \App\Models\Delivery::create([
    'route_id' => $route->id,
    'client_id' => $client->id,
    'package_info' => 'Paquete de 50kg, frágil',
    'status' => 'pending',  // pending, in_transit, delivered, failed
    'latitude' => null,
    'longitude' => null
]);

// Actualizar estado de entrega
$delivery->update([
    'status' => 'in_transit',
    'latitude' => 40.4168,
    'longitude' => -3.7038
]);

// Obtener entregas de un cliente
$client->deliveries;

// Obtener entregas de una ruta
$route->deliveries;

// ============================================================================
// 5. INTEGRACIÓN CON GOOGLE MAPS
// ============================================================================

use App\Services\GeoLocationService;

$geoService = new GeoLocationService();

// Obtener coordenadas desde una dirección
$coords = $geoService->getCoordinatesFromAddress('Barcelona, España');
// Resultado: ['latitude' => 41.3851, 'longitude' => 2.1734]

if ($coords) {
    $delivery->update([
        'latitude' => $coords['latitude'],
        'longitude' => $coords['longitude']
    ]);
}

// Obtener dirección desde coordenadas
$address = $geoService->getAddressFromCoordinates(41.3851, 2.1734);
// Resultado: "Barcelona, Spain"

// Calcular distancia entre dos puntos
$distance = $geoService->calculateDistance(
    ['latitude' => 40.4168, 'longitude' => -3.7038],  // Madrid
    ['latitude' => 41.3851, 'longitude' => 2.1734]    // Barcelona
);
// Resultado:
// [
//     'distance' => 505000,           // metros
//     'distance_km' => 505,           // kilómetros
//     'duration' => '5 hours 15 mins',
//     'duration_seconds' => 18900
// ]

// ============================================================================
// 6. CONSULTAS AVANZADAS CON ELOQUENT
// ============================================================================

// Obtener entregas pendientes
$pending = \App\Models\Delivery::where('status', 'pending')->get();

// Obtener entregas entregadas en últimos 7 días
$recent = \App\Models\Delivery::where('status', 'delivered')
    ->where('updated_at', '>=', now()->subDays(7))
    ->orderBy('updated_at', 'desc')
    ->get();

// Obtener rutas activas
$activeRoutes = \App\Models\Route::whereIn('status', ['pending', 'in_progress'])
    ->with('deliveries')  // Eager load entregas
    ->get();

// Contar vehículos disponibles por flota
$availableVehicles = \App\Models\Fleet::withCount([
    'vehicles' => function ($query) {
        $query->where('status', 'available');
    }
])->get();

foreach ($availableVehicles as $fleet) {
    echo "{$fleet->name}: {$fleet->vehicles_count} disponibles\n";
}

// Obtener usuarios admin de una empresa
$admins = \App\Models\User::where('company_id', 1)
    ->whereHas('roles', function ($query) {
        $query->where('name', 'admin');
    })
    ->get();

// ============================================================================
// 7. EVENTOS Y OBSERVADORES (recomendado para agregar)
// ============================================================================

// Ejemplo de observer para Delivery:
// Podría dispararse automáticamente cuando se crea una entrega
// for example:
/*
DeliveryObserver:
    - public function created(Delivery $delivery)
      // Obtener coordenadas automáticamente
      // Enviar notificación al cliente
      // Actualizar estado del vehículo
*/

// ============================================================================
// 8. VALIDACIONES EN FORMULARIOS
// ============================================================================

// Validación típica en un controlador
$validated = request()->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:delivery,email,' . $delivery->id,
    'phone' => 'required|regex:/^[+\d\s\-()]+$/',
    'address' => 'required|string|max:500',
    'package_info' => 'required|string|min:10',
    'status' => 'required|in:pending,in_transit,delivered,failed',
]);

// ============================================================================
// 9. TRANSACCIONES (para operaciones complejas)
// ============================================================================

\DB::transaction(function () {
    // Crear ruta
    $route = \App\Models\Route::create([...]);
    
    // Crear entregas
    foreach ($deliveries as $data) {
        \App\Models\Delivery::create([
            'route_id' => $route->id,
            ...$data
        ]);
    }
    
    // Actualizar vehículo
    \App\Models\Vehicle::find($vehicleId)
        ->update(['status' => 'in_use']);
    
    // Si algo falla, se revierte todo
});

// ============================================================================
// 10. PAGINACIÓN Y BÚSQUEDA
// ============================================================================

// En controlador
public function index(Request $request)
{
    $query = \App\Models\Delivery::query();
    
    // Búsqueda
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->whereHas('client', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }
    
    // Filtro por estado
    if ($request->has('status')) {
        $query->where('status', $request->input('status'));
    }
    
    // Ordenar
    $query->orderBy('created_at', 'desc');
    
    // Paginar
    $deliveries = $query->paginate(15);
    
    return view('deliveries.index', compact('deliveries'));
}

// ============================================================================
// 11. MÉTODOS ÚTILES EN MODELOS
// ============================================================================

// Agregar en modelo User
public function getFullNameAttribute()
{
    return "{$this->name}";
}

// Agregar en modelo Delivery
public function isDelivered()
{
    return $this->status === 'delivered';
}

// Agregar en modelo Route
public function getDurationAttribute()
{
    return $this->estimated_arrival->diffInHours($this->estimated_departure);
}

// ============================================================================
// 12. API HELPER METHODS
// ============================================================================

// Obtener todas las empresas con estadísticas
$companies = \App\Models\Company::withCount([
    'users',
    'clients',
    'fleets'
])->get();

foreach ($companies as $company) {
    echo "{$company->name}: ";
    echo "{$company->users_count} users, ";
    echo "{$company->clients_count} clients, ";
    echo "{$company->fleets_count} fleets\n";
}

// ============================================================================
// NOTA FINAL
// ============================================================================

/*
Todos estos ejemplos pueden ejecutarse en:

1. Controladores:
   $user = User::find(1);
   
2. Tinker:
   php artisan tinker
   >>> $user = User::find(1);
   
3. Migraciones/Seeders:
   $user = User::create([...]);
   
4. Jobs/Observers:
   Similar a controladores

Recuerda:
- Siempre validar entrada del usuario
- Usar transacciones para operaciones multi-paso
- Implementar eager loading para evitar N+1 queries
- Respetar los roles y permisos definidos
- Documentar las relaciones complejas
*/
