# Documentación Arquitectura - NexusLogistics

## 1. Introducción General

**NexusLogistics** es una aplicación web de gestión logística construida con **Laravel 11**. Permite gestionar empresas, flotas, vehículos, rutas de distribución, clientes y entregas. La aplicación utiliza autenticación de usuarios con roles y permisos, incluye geolocalización mediante APIs gratuitas (Nominatim y OSRM) y dispone de mapas interactivos para visualizar rutas.

### Stack Tecnológico
- **Backend**: Laravel 11 con PHP 8.2+
- **Frontend**: Blade Templates con Bootstrap 5.3
- **Base de Datos**: PostgreSQL
- **Mapas**: Leaflet.js v1.9.4 + OpenStreetMap
- **Geocodificación**: Nominatim API (OpenStreetMap)
- **Routing**: OSRM (Open Source Routing Machine)
- **Autenticación**: Laravel's built-in authentication

---

## 2. Diagrama de Relaciones de Modelos

```
┌─────────────┐
│   Company   │ (Empresa)
├─────────────┤
│ - name      │
│ - email     │
│ - phone     │
│ - address   │
└──────┬──────┘
       │ 1─N (one-to-many)
       ├──────────────────────┬──────────────────┬──────────────────┐
       │                      │                  │                  │
       ▼                      ▼                  ▼                  ▼
   ┌────────┐           ┌──────────┐       ┌────────┐        ┌──────────┐
   │  User  │           │  Client  │       │ Fleet  │        │ (omitted)│
   └────────┘           └──────────┘       └────────┘        └──────────┘
       │ N─M                 │               │ 1─N
       │ (many-to-many)      │               │
    ┌──┴──┐            ┌─────┴─────┐        │
    │Role │            │ Delivery  │        ▼
    └─────┘            └───────────┘    ┌─────────┐
       │                    │           │ Vehicle │
    N─M relation with       ▼ 1─N       └─────────┘
    Permission         ┌────────┐
                       │ Route  │
                       └────────┘
```

### Relaciones Detalladas

#### Company → User (1:N)
- Una empresa tiene muchos usuarios
- Los usuarios pertenecen a una empresa específica

#### Company → Client (1:N)
- Una empresa tiene muchos clientes
- Los clientes son asociados a una empresa

#### Company → Fleet (1:N)
- Una empresa posee muchas flotas
- Cada flota pertenece a una empresa

#### Fleet → Vehicle (1:N)
- Una flota contiene muchos vehículos
- Cada vehículo es parte de una flota

#### Fleet → Route (1:N)
- Una flota realiza muchas rutas
- Cada ruta pertenece a una flota

#### Route → Delivery (1:N)
- Una ruta contiene muchas entregas
- Cada entrega forma parte de una ruta

#### Client → Delivery (1:N)
- Un cliente puede tener muchas entregas
- Cada entrega (paquete) pertenece a un cliente

#### User → Role (N:M)
- Un usuario puede tener múltiples roles
- Un rol puede asignarse a múltiples usuarios
- **Tabla intermedia**: `role_user`

#### Role → Permission (N:M)
- Un rol tiene múltiples permisos
- Un permiso puede pertenecer a múltiples roles
- **Tabla intermedia**: `permission_role`

---

## 3. Descripción Detallada de Modelos

### 3.1 Company (Empresa)

**Ubicación**: `app/Models/Company.php`

**Propósito**: Representa una empresa logística en el sistema.

**Campos**:
```
- id (PK)
- name: string - Nombre de la empresa
- email: string - Email de contacto
- phone: string - Teléfono
- address: string - Dirección
- city: string - Ciudad
- country: string - País
- postal_code: string - Código postal
- created_at, updated_at
```

**Relaciones**:
```php
- users(): HasMany           // Usuarios de la empresa
- clients(): HasMany         // Clientes de la empresa
- fleets(): HasMany          // Flotas de la empresa
```

**Ejemplo de uso**:
```php
// Obtener una empresa
$company = Company::find(1);

// Obtener todos los usuarios de una empresa
$users = $company->users()->get();

// Obtener todos los clientes
$clients = $company->clients;

// Crear una flota en la empresa
$fleet = $company->fleets()->create([
    'name' => 'Flota Express',
    'description' => 'Entregas rápidas'
]);
```

---

### 3.2 User (Usuario)

**Ubicación**: `app/Models/User.php`

**Propósito**: Representa un usuario del sistema con credenciales de acceso.

**Campos**:
```
- id (PK)
- name: string - Nombre completo
- email: string - Email único
- password: string - Contraseña (hasheada)
- company_id: FK - Empresa a la que pertenece
- email_verified_at: timestamp nullable
- remember_token: string nullable
- created_at, updated_at
```

**Relaciones**:
```php
- company(): BelongsTo       // Empresa del usuario
- roles(): BelongsToMany     // Roles asignados
```

**Métodos especiales**:
```php
- hasRole($roleName): bool   // Verificar si tiene un rol
- hasPermission($permName): bool  // Verificar si tiene permiso
```

**Ejemplo de uso**:
```php
// Crear usuario
$user = User::create([
    'name' => 'Juan García',
    'email' => 'juan@empresa.com',
    'password' => bcrypt('password123'),
    'company_id' => 1
]);

// Asignar rol
$user->roles()->attach(Role::where('name', 'admin')->first());

// Verificar permisos
if ($user->hasRole('admin')) {
    // Hacer algo
}
```

---

### 3.3 Role (Rol)

**Ubicación**: `app/Models/Role.php`

**Propósito**: Define un rol del sistema (admin, gerente, conductor, etc.)

**Campos**:
```
- id (PK)
- name: string - Nombre del rol (único)
- description: string - Descripción
- created_at, updated_at
```

**Relaciones**:
```php
- users(): BelongsToMany     // Usuarios con este rol
- permissions(): BelongsToMany  // Permisos del rol
```

**Roles predefinidos**:
- `admin` - Administrador del sistema
- `manager` - Gerente de empresa
- `driver` - Conductor/Repartidor

**Ejemplo de uso**:
```php
// Obtener un rol
$adminRole = Role::where('name', 'admin')->first();

// Obtener todos los permisos de un rol
$permissions = $adminRole->permissions;

// Agregar permiso a un rol
$adminRole->permissions()->attach(Permission::find(1));
```

---

### 3.4 Permission (Permiso)

**Ubicación**: `app/Models/Permission.php`

**Propósito**: Define una acción permitida en el sistema.

**Campos**:
```
- id (PK)
- name: string - Nombre del permiso (único)
- description: string - Descripción
- created_at, updated_at
```

**Relaciones**:
```php
- roles(): BelongsToMany     // Roles que tienen este permiso
```

**Permisos comunes**:
```
- create_routes
- edit_routes
- delete_routes
- view_deliveries
- create_deliveries
- edit_deliveries
- delete_deliveries
- manage_users
- manage_vehicles
```

**Ejemplo de uso**:
```php
// Crear permiso
$permission = Permission::create([
    'name' => 'create_routes',
    'description' => 'Crear nuevas rutas'
]);

// Agregar permiso a rol
$managerRole = Role::find(2);
$managerRole->permissions()->attach($permission);
```

---

### 3.5 Client (Cliente)

**Ubicación**: `app/Models/Client.php`

**Propósito**: Representa un cliente que utiliza los servicios de logística.

**Campos**:
```
- id (PK)
- name: string - Nombre del cliente
- email: string - Email
- phone: string - Teléfono
- address: string - Dirección
- city: string - Ciudad
- company_id: FK - Empresa propietaria
- created_at, updated_at
```

**Relaciones**:
```php
- company(): BelongsTo       // Empresa propietaria
- deliveries(): HasMany      // Entregas del cliente
```

**Ejemplo de uso**:
```php
// Crear cliente
$client = Client::create([
    'name' => 'Tienda XYZ',
    'email' => 'contacto@tienda.com',
    'phone' => '555-1234',
    'address' => 'Calle Principal 123',
    'city' => 'Madrid',
    'company_id' => 1
]);

// Obtener entregas del cliente
$deliveries = $client->deliveries;
```

---

### 3.6 Fleet (Flota)

**Ubicación**: `app/Models/Fleet.php`

**Propósito**: Agrupa vehículos y organiza rutas de entrega.

**Campos**:
```
- id (PK)
- name: string - Nombre de la flota
- company_id: FK - Empresa propietaria
- description: string - Descripción
- created_at, updated_at
```

**Relaciones**:
```php
- company(): BelongsTo       // Empresa propietaria
- vehicles(): HasMany        // Vehículos de la flota
- routes(): HasMany          // Rutas de la flota
```

**Ejemplo de uso**:
```php
// Crear flota
$fleet = Fleet::create([
    'name' => 'Flota Express Centro',
    'company_id' => 1,
    'description' => 'Entregas en zona centro'
]);

// Obtener vehículos
$vehicles = $fleet->vehicles;

// Obtener rutas
$routes = $fleet->routes;

// Crear vehículo en flota
$vehicle = $fleet->vehicles()->create([
    'license_plate' => 'MAD-1234',
    'vehicle_type' => 'truck'
]);
```

---

### 3.7 Vehicle (Vehículo)

**Ubicación**: `app/Models/Vehicle.php`

**Propósito**: Representa un vehículo en la flota.

**Campos**:
```
- id (PK)
- fleet_id: FK - Flota a la que pertenece
- license_plate: string - Matrícula (única)
- vehicle_type: string - Tipo (truck, van, car, motorcycle)
- manufacturer: string - Fabricante
- model: string - Modelo
- year: integer - Año de fabricación
- status: string - Estado (active, maintenance, retired)
- created_at, updated_at
```

**Relaciones**:
```php
- fleet(): BelongsTo         // Flota del vehículo
```

**Ejemplo de uso**:
```php
// Crear vehículo
$vehicle = Vehicle::create([
    'fleet_id' => 1,
    'license_plate' => 'MAD-1234-ABC',
    'vehicle_type' => 'truck',
    'manufacturer' => 'Mercedes',
    'model' => 'Sprinter',
    'year' => 2023,
    'status' => 'active'
]);

// Obtener flota del vehículo
$fleet = $vehicle->fleet;
```

---

### 3.8 Route (Ruta)

**Ubicación**: `app/Models/Route.php`

**Propósito**: Representa una ruta de entrega con información geográfica y de tiempo.

**Campos**:
```
- id (PK)
- fleet_id: FK - Flota que realiza la ruta
- origin: string - Ciudad/dirección de origen
- destination: string - Ciudad/dirección de destino
- origin_latitude: DECIMAL(10,8) - Latitud origen (geocodificada)
- origin_longitude: DECIMAL(11,8) - Longitud origen
- destination_latitude: DECIMAL(10,8) - Latitud destino
- destination_longitude: DECIMAL(11,8) - Longitud destino
- distance_km: DECIMAL(8,2) - Distancia en kilómetros (calculada)
- duration_seconds: INTEGER - Duración en segundos (calculada)
- estimated_departure: DATETIME - Salida estimada
- estimated_arrival: DATETIME - Llegada estimada
- status: ENUM - Estado (pending, in_progress, completed, cancelled)
- created_at, updated_at
```

**Relaciones**:
```php
- fleet(): BelongsTo         // Flota responsable
- deliveries(): HasMany      // Entregas en esta ruta
```

**Estados posibles**:
- `pending` - Pendiente de comenzar
- `in_progress` - En curso
- `completed` - Completada
- `cancelled` - Cancelada

**Ejemplo de uso**:
```php
// Crear ruta (la API geocodifica automáticamente)
$route = Route::create([
    'fleet_id' => 1,
    'origin' => 'Madrid',
    'destination' => 'Barcelona',
    'estimated_departure' => '2026-02-20 08:00',
    'estimated_arrival' => '2026-02-20 15:30',
    'status' => 'pending'
]);

// Se llenan automáticamente:
// - origin_latitude, origin_longitude (geocodificación)
// - destination_latitude, destination_longitude
// - distance_km, duration_seconds (cálculo OSRM)

// Obtener entregas de la ruta
$deliveries = $route->deliveries;

// Cambiar estado
$route->update(['status' => 'in_progress']);
```

---

### 3.9 Delivery (Entrega)

**Ubicación**: `app/Models/Delivery.php`

**Propósito**: Representa un paquete a entregar en una ruta.

**Campos**:
```
- id (PK)
- route_id: FK - Ruta en la que se entrega
- client_id: FK - Cliente destinatario
- package_info: string - Descripción del paquete
- status: string - Estado (pending, delivered, failed, returned)
- latitude: DECIMAL - Latitud de entrega (opcional)
- longitude: DECIMAL - Longitud de entrega (opcional)
- created_at, updated_at
```

**Relaciones**:
```php
- route(): BelongsTo         // Ruta que contiene la entrega
- client(): BelongsTo        // Cliente destinatario
```

**Estados posibles**:
- `pending` - Pendiente de entregar
- `delivered` - Entregado
- `failed` - Intento fallido
- `returned` - Devuelto

**Ejemplo de uso**:
```php
// Crear entrega
$delivery = Delivery::create([
    'route_id' => 1,
    'client_id' => 1,
    'package_info' => 'Caja de libros - 15kg',
    'status' => 'pending'
]);

// Actualizar después de entrega
$delivery->update([
    'status' => 'delivered',
    'latitude' => 41.3825802,
    'longitude' => 2.177073
]);

// Obtener detalles completos
$client = $delivery->client;
$route = $delivery->route;
```

---

## 4. Controladores

### 4.1 AuthController

**Ubicación**: `app/Http/Controllers/AuthController.php`

**Responsabilidad**: Gestionar autenticación de usuarios (login, registro, logout).

**Métodos principales**:

```php
showLogin()          // Mostrar formulario de login
login()              // Procesar login (POST)
showRegister()       // Mostrar formulario de registro
register()           // Procesar registro (POST)
logout()             // Cerrar sesión
```

**Rutas**:
```
GET/POST  /login          - Formulario/procesamiento de login
GET/POST  /register       - Formulario/procesamiento de registro
POST      /logout         - Cerrar sesión
```

**Ejemplo de uso en plantilla**:
```php
@auth
    <p>Bienvenido {{ auth()->user()->name }}</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Cerrar Sesión</button>
    </form>
@endauth

@guest
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Registro</a>
@endguest
```

---

### 4.2 DashboardController

**Ubicación**: `app/Http/Controllers/DashboardController.php`

**Responsabilidad**: Mostrar el panel principal del usuario (dashboard).

**Métodos principales**:
```php
index()              // Mostrar dashboard con estadísticas
```

**Rutas**:
```
GET /dashboard  - Panel principal
```

**Información típica mostrada**:
- Total de rutas
- Rutas completadas
- Entregas pendientes
- Flota disponible
- Últimas actividades

---

### 4.3 CompanyController

**Ubicación**: `app/Http/Controllers/CompanyController.php`

**Responsabilidad**: Gestionar CRUD de empresas.

**Métodos (Recurso REST)**:
```php
index()              // Listar todas las empresas (GET /companies)
create()             // Formulario crear (GET /companies/create)
store()              // Guardar nueva (POST /companies)
show($id)            // Ver detalles (GET /companies/{id})
edit($id)            // Formulario editar (GET /companies/{id}/edit)
update($id)          // Actualizar (PUT /companies/{id})
destroy($id)         // Eliminar (DELETE /companies/{id})
```

**Rutas**:
```
GET      /companies              - Listar
GET      /companies/create       - Formulario crear
POST     /companies              - Guardar
GET      /companies/{id}         - Ver detalles
GET      /companies/{id}/edit    - Formulario editar
PUT      /companies/{id}         - Actualizar
DELETE   /companies/{id}         - Eliminar
```

**Ejemplo de uso**:
```php
// En controlador
$companies = Company::all();
return view('companies.index', compact('companies'));

// En plantilla
<a href="{{ route('companies.create') }}">Crear Empresa</a>
<form action="{{ route('companies.update', $company->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{ $company->name }}">
    <button type="submit">Actualizar</button>
</form>
```

---

### 4.4 ClientController

**Ubicación**: `app/Http/Controllers/ClientController.php`

**Responsabilidad**: Gestionar CRUD de clientes.

**Métodos (Recurso REST)**:
```php
index()              // Listar clientes
create()             // Formulario crear
store()              // Guardar nuevo cliente
show($id)            // Ver detalles
edit($id)            // Formulario editar
update($id)          // Actualizar
destroy($id)         // Eliminar
```

**Campos del formulario**:
```
- name: Nombre del cliente
- email: Email
- phone: Teléfono
- address: Dirección
- city: Ciudad
```

**Relación con entregas**:
```php
// En el controlador show()
$client = Client::with('deliveries')->find($id);
```

---

### 4.5 FleetController

**Ubicación**: `app/Http/Controllers/FleetController.php`

**Responsabilidad**: Gestionar CRUD de flotas.

**Métodos (Recurso REST)**:
```php
index()              // Listar flotas
create()             // Formulario crear
store()              // Guardar nueva flota
show($id)            // Ver detalles (incluye vehículos y rutas)
edit($id)            // Formulario editar
update($id)          // Actualizar
destroy($id)         // Eliminar
```

**Relaciones cargadas**:
```php
Fleet::with('vehicles', 'routes')->find($id);
```

**Información en show()**:
- Detalles de la flota
- Vehículos pertenecientes
- Rutas activas
- Estadísticas

---

### 4.6 VehicleController

**Ubicación**: `app/Http/Controllers/VehicleController.php`

**Responsabilidad**: Gestionar CRUD de vehículos.

**Métodos (Recurso REST)**:
```php
index()              // Listar vehículos
create()             // Formulario crear
store()              // Guardar nuevo vehículo
show($id)            // Ver detalles
edit($id)            // Formulario editar
update($id)          // Actualizar
destroy($id)         // Eliminar
```

**Campos del formulario**:
```
- fleet_id: Flota (select)
- license_plate: Matrícula
- vehicle_type: Tipo (truck, van, car, motorcycle)
- manufacturer: Fabricante
- model: Modelo
- year: Año
- status: Estado (active, maintenance, retired)
```

---

### 4.7 RouteController

**Ubicación**: `app/Http/Controllers/RouteController.php`

**Responsabilidad**: Gestionar CRUD de rutas con geocodificación automática.

**Métodos (Recurso REST)**:
```php
index()              // Listar rutas (con mapa)
create()             // Formulario crear
store()              // Guardar nueva ruta (+ geocodificación y cálculo)
show($id)            // Ver detalles con mapa interactivo
edit($id)            // Formulario editar
update($id)          // Actualizar
destroy($id)         // Eliminar
```

**Proceso de creación/actualización**:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'fleet_id' => 'required|exists:fleets,id',
        'origin' => 'required|string|max:255',
        'destination' => 'required|string|max:255',
        'estimated_departure' => 'required|date_format:Y-m-d\TH:i',
        'estimated_arrival' => 'required|date_format:Y-m-d\TH:i|after:estimated_departure',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
    ]);

    // 1. Geocodificar origen (convertir ciudad a coordenadas)
    $originCoords = $geoService->getCoordinatesFromAddress($validated['origin']);
    if (!$originCoords) throw error();

    // 2. Geocodificar destino
    $destCoords = $geoService->getCoordinatesFromAddress($validated['destination']);
    if (!$destCoords) throw error();

    // 3. Calcular distancia y duración usando OSRM
    $routeInfo = $geoService->calculateDistance($originCoords, $destCoords);

    // 4. Guardar con coordenadas calculadas
    $validated['origin_latitude'] = $originCoords['latitude'];
    $validated['origin_longitude'] = $originCoords['longitude'];
    $validated['destination_latitude'] = $destCoords['latitude'];
    $validated['destination_longitude'] = $destCoords['longitude'];
    $validated['distance_km'] = $routeInfo['distance_km'];
    $validated['duration_seconds'] = (int)$routeInfo['duration_seconds'];

    Route::create($validated);
    return redirect()->route('routes.index');
}
```

**Rutas**:
```
GET      /routes              - Listar (con mapa de todas las rutas)
GET      /routes/create       - Formulario crear
POST     /routes              - Guardar (geocodificación automática)
GET      /routes/{id}         - Ver detalles (con mapa interactivo)
GET      /routes/{id}/edit    - Formulario editar
PUT      /routes/{id}         - Actualizar
DELETE   /routes/{id}         - Eliminar
```

---

### 4.8 DeliveryController

**Ubicación**: `app/Http/Controllers/DeliveryController.php`

**Responsabilidad**: Gestionar CRUD de entregas.

**Métodos (Recurso REST)**:
```php
index()              // Listar entregas
create()             // Formulario crear
store()              // Guardar nueva entrega
show($id)            // Ver detalles
edit($id)            // Formulario editar
update($id)          // Actualizar
destroy($id)         // Eliminar
```

**Campos del formulario**:
```
- route_id: Ruta (select)
- client_id: Cliente (select)
- package_info: Información del paquete (texto)
- status: Estado (pending, delivered, failed, returned)
- latitude/longitude: Coordenadas (opcionales)
```

**Estados y transiciones**:
```
pending ──> delivered
     └──> failed ──> returned
```

---

### 4.9 UserController

**Ubicación**: `app/Http/Controllers/UserController.php`

**Responsabilidad**: Gestionar CRUD de usuarios (solo administradores).

**Métodos (Recurso REST)**:
```php
index()              // Listar usuarios
create()             // Formulario crear
store()              // Guardar nuevo usuario
show($id)            // Ver detalles
edit($id)            // Formulario editar
update($id)          // Actualizar
destroy($id)         // Eliminar
```

**Protección**: Requiere rol `admin`

**Middlewares**:
```
Route::middleware(['role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});
```

**Campos del formulario**:
```
- name: Nombre
- email: Email (único)
- password: Contraseña
- company_id: Empresa (select)
- roles: Roles (multi-select)
```

---

## 5. Servicios

### 5.1 GeoLocationService

**Ubicación**: `app/Services/GeoLocationService.php`

**Responsabilidad**: Proporcionar servicios de geolocalización mediante APIs externas.

**APIs utilizadas**:
1. **Nominatim** (OpenStreetMap) - Geocodificación
2. **OSRM** (Open Source Routing Machine) - Routing y distancias

**Métodos**:

#### `getCoordinatesFromAddress($address): array|null`

Convierte una dirección (ciudad, calle, etc.) en coordenadas (latitud, longitud).

```php
$coordinates = $geoService->getCoordinatesFromAddress('Madrid');
// Resultado:
// [
//     'latitude' => 40.416782,
//     'longitude' => -3.703507
// ]

// Con dirección completa
$coordinates = $geoService->getCoordinatesFromAddress(
    'Calle Principal 123, Madrid, España'
);
```

**Usado en**: Creación/edición de rutas

---

#### `getAddressFromCoordinates($latitude, $longitude): string|null`

Convierte coordenadas en una dirección legible (geocodificación inversa).

```php
$address = $geoService->getAddressFromCoordinates(40.416782, -3.703507);
// Resultado: "Madrid"

$address = $geoService->getAddressFromCoordinates(
    41.3825802, 
    2.177073
);
// Resultado: "Barcelona"
```

**Usado en**: Actualizar datos de entregas con ubicación GPS

---

#### `calculateDistance($origin, $destination): array|null`

Calcula la distancia y duración entre dos puntos geográficos.

```php
$origin = [
    'latitude' => 40.416782,
    'longitude' => -3.703507
];

$destination = [
    'latitude' => 41.3825802,
    'longitude' => 2.177073
];

$route = $geoService->calculateDistance($origin, $destination);
// Resultado:
// [
//     'distance' => 621400,              // metros
//     'distance_km' => 621.4,            // kilómetros
//     'duration' => '8 horas 34 minutos',
//     'duration_seconds' => 30840        // segundos
// ]
```

**Usado en**: Creación/edición de rutas

---

#### `formatDuration($seconds): string`

Formatea segundos a un formato legible.

```php
$duration = $geoService->formatDuration(30840);
// Resultado: "8 horas 34 minutos"

$duration = $geoService->formatDuration(3600);
// Resultado: "1 hora"

$duration = $geoService->formatDuration(1800);
// Resultado: "30 minutos"
```

---

**Manejo de errores en GeoLocationService**:

```php
// Nominatim puede no encontrar la dirección
$coords = $geoService->getCoordinatesFromAddress('Lugar inexistente');
if (!$coords) {
    // Manejar error
    return back()->withError('No se encontraron coordenadas');
}

// OSRM puede fallar por timeout o no disponibilidad
$distance = $geoService->calculateDistance($origin, $destination);
if (!$distance) {
    // Manejar error
    \Log::error('Error calculando distancia');
}
```

---

## 6. Rutas y API

### 6.1 Estructura General

La aplicación utiliza **Resource Routes** de Laravel, que generan automáticamente las 7 rutas CRUD:

```
GET      /resource              → index()      - Listar
GET      /resource/create       → create()     - Formulario crear
POST     /resource              → store()      - Guardar
GET      /resource/{id}         → show()       - Ver detalles
GET      /resource/{id}/edit    → edit()       - Formulario editar
PUT      /resource/{id}         → update()     - Actualizar
DELETE   /resource/{id}         → destroy()    - Eliminar
```

### 6.2 Rutas Implementadas

**Archivo**: `routes/web.php`

```php
// ===== RUTAS PÚBLICAS (sin autenticación) =====
GET/POST  /login              - Autenticación
GET/POST  /register           - Registro
GET       /                   - Redirige a dashboard o login

// ===== RUTAS PROTEGIDAS (requieren autenticación) =====
GET       /dashboard          - Panel principal

// Resource routes (CRUD automático)
GET/POST  /companies/*        - Empresas
GET/POST  /clients/*          - Clientes
GET/POST  /fleets/*           - Flotas
GET/POST  /vehicles/*         - Vehículos
GET/POST  /routes/*           - Rutas
GET/POST  /deliveries/*       - Entregas

// ===== RUTAS ADMINISTRATIVAS =====
GET/POST  /users/*            - Usuarios (solo admin)

POST      /logout             - Cerrar sesión
```

### 6.3 Ejemplos de Uso de Rutas

#### Manejo de Rutas en Plantillas

```php
<!-- Enlace a lista -->
<a href="{{ route('companies.index') }}">Empresas</a>

<!-- Formulario crear -->
<form action="{{ route('companies.store') }}" method="POST">
    @csrf
    <input type="text" name="name">
    <button type="submit">Crear</button>
</form>

<!-- Formulario editar -->
<form action="{{ route('companies.update', $company->id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{ $company->name }}">
    <button type="submit">Actualizar</button>
</form>

<!-- Botón eliminar -->
<form action="{{ route('companies.destroy', $company->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="confirm('¿Eliminar?')">Eliminar</button>
</form>

<!-- Enlace show -->
<a href="{{ route('companies.show', $company->id) }}">Ver detalles</a>
```

#### En Controladores

```php
// Redireccionar a lista
return redirect()->route('companies.index');

// Redireccionar a detalles
return redirect()->route('companies.show', $company->id);

// Con mensaje flash
return redirect()->route('companies.index')->with('success', 'Creado!');
```

---

## 7. Flujo de Autenticación y Autorización

### 7.1 Flujo de Login

```
Usuario abre /login
    ↓
AuthController::showLogin() muestra formulario
    ↓
Usuario envía credenciales (POST /login)
    ↓
AuthController::login() verifica credenciales
    ↓
    ├─ Válidas ──> Crea sesión ──> Redirecciona a /dashboard
    └─ Inválidas ──> Error ──> Regresa a login
```

### 7.2 Verificación de Permisos

```php
// En middleware (rutas)
Route::middleware(['auth'])->group(function () {
    // Usuario debe estar autenticado
});

Route::middleware(['role:admin'])->group(function () {
    // Usuario debe tener rol 'admin'
});

// En controlador
if (auth()->user()->hasRole('admin')) {
    // Hacer algo
}

if (auth()->user()->hasPermission('create_routes')) {
    // Hacer algo
}

// En plantilla Blade
@can('crear rutas')
    <button>Crear ruta</button>
@endcan

@auth
    Contenido solo para autenticados
@endauth

@guest
    Contenido solo para visitantes
@endguest
```

---

## 8. Ejemplo Práctico Completo: Crear una Ruta

**Paso 1: Usuario navega a `/routes`**
```
RouteController::index() listra todas las rutas
↳ Carga vista: resources/views/routes/index.blade.php
↳ Muestra mapa Leaflet con todas las rutas
↳ Botón "Crear Nueva Ruta"
```

**Paso 2: Usuario hace clic en "Crear Nueva Ruta"**
```
GET /routes/create
↳ RouteController::create()
↳ Carga vista: resources/views/routes/create.blade.php
↳ Formulario con campos:
   - Fleet (select)
   - Origen (ej: Madrid)
   - Destino (ej: Barcelona)
   - Salida estimada (datetime)
   - Llegada estimada (datetime)
   - Estado (select: pending, in_progress, completed, cancelled)
```

**Paso 3: Usuario completa el formulario y envía**
```
POST /routes (datos guardados)
↳ RouteController::store()
   ├─ Valida datos
   ├─ Geocodifica "Madrid"
   │  ↳ GeoLocationService::getCoordinatesFromAddress('Madrid')
   │  ↳ Nominatim API retorna: {'lat': 40.416782, 'lon': -3.703507}
   ├─ Geocodifica "Barcelona"
   │  ↳ Nominatim API retorna: {'lat': 41.3825802, 'lon': 2.177073}
   ├─ Calcula distancia
   │  ↳ GeoLocationService::calculateDistance($origin, $dest)
   │  ↳ OSRM API retorna: {distance: 621400m, duration: 30840s}
   ├─ Guarda en base de datos
   └─ Redirecciona a /routes (con mensaje de éxito)
```

**Paso 4: Usuario ve la ruta en la lista**
```
GET /routes
↳ Mapa Leaflet muestra:
   - Marcador verde (origen: Madrid)
   - Marcador rojo (destino: Barcelona)
   - Línea azul o naranja según status
   - Información: 621.4 km, ~8.5 horas
```

**Paso 5: Usuario hace clic en detalles**
```
GET /routes/{id}
↳ RouteController::show()
↳ Carga vista: resources/views/routes/show.blade.php
↳ Mapa con zoom en la ruta específica
↳ Lista de entregas en esta ruta
↳ Opciones de editar/eliminar
```

---

## 9. Ejemplo Práctico: Crear una Entrega

```
Paso 1: Vista /routes/{id}/show muestra entregas
Paso 2: Usuario hace clic "Crear Entrega"
   ↳ GET /deliveries/create
   ↳ Formulario con:
      - Ruta (pre-seleccionada)
      - Cliente (select)
      - Información paquete
      - Estado (pending)

Paso 3: Usuario envía formulario
   ↳ POST /deliveries
   ↳ DeliveryController::store()
   ↳ Crea Delivery::create()
   ↳ Redirecciona a /routes/{id}

Paso 4: Entrega visible en lista
```

---

## 10. Roles y Permisos Predefinidos

### Tabla: Role-Permission Matrix

| Permiso | Admin | Manager | Driver |
|---------|-------|---------|--------|
| create_routes | ✓ | ✓ | ✗ |
| edit_routes | ✓ | ✓ | ✗ |
| delete_routes | ✓ | ✗ | ✗ |
| view_deliveries | ✓ | ✓ | ✓ |
| create_deliveries | ✓ | ✓ | ✓ |
| edit_deliveries | ✓ | ✓ | ✓ |
| delete_deliveries | ✓ | ✗ | ✗ |
| manage_users | ✓ | ✗ | ✗ |
| manage_vehicles | ✓ | ✓ | ✗ |

---

## 11. Estructuras de Datos Clave

### Request Payload: Crear Ruta

```json
{
  "fleet_id": 1,
  "origin": "Madrid, España",
  "destination": "Barcelona, España",
  "estimated_departure": "2026-02-20T08:00",
  "estimated_arrival": "2026-02-20T16:00",
  "status": "pending"
}
```

### Response: Ruta Creada (JSON)

```json
{
  "id": 5,
  "fleet_id": 1,
  "origin": "Madrid, España",
  "destination": "Barcelona, España",
  "origin_latitude": 40.416782,
  "origin_longitude": -3.703507,
  "destination_latitude": 41.3825802,
  "destination_longitude": 2.177073,
  "distance_km": 621.4,
  "duration_seconds": 30840,
  "estimated_departure": "2026-02-20 08:00:00",
  "estimated_arrival": "2026-02-20 16:00:00",
  "status": "pending",
  "created_at": "2026-02-19T14:30:00Z",
  "updated_at": "2026-02-19T14:30:00Z"
}
```

---

## 12. Diagrama de Flujo: Geocodificación en Ruta

```
┌─────────────────────────────┐
│ POST /routes (crear)        │
│ {origin: "Madrid"}          │
└──────────────┬──────────────┘
               │
               ▼
       ┌───────────────────┐
       │ Validar input     │
       └─────────┬─────────┘
                 │
                 ▼
       ┌───────────────────────────────┐
       │ GeoLocationService             │
       │ ::getCoordinatesFromAddress()  │
       └─────────┬─────────────────────┘
                 │
                 ▼
      ┌──────────────────────────┐
      │ Nominatim API Request    │
      │ GET /search?q=Madrid     │
      └──────────┬───────────────┘
                 │
                 ▼
      ┌──────────────────────────┐
      │ Nominatim Response       │
      │ {lat: 40.416782,         │
      │  lon: -3.703507}         │
      └──────────┬───────────────┘
                 │
                 ▼
     ┌─────────────────────────┐
     │ Guardar coordenadas     │
     │ en Route model          │
     └─────────┬───────────────┘
               │
               ▼
     ┌─────────────────────────┐
     │ Calcular distancia      │
     │ (GeoLocationService)    │
     └─────────┬───────────────┘
               │
               ▼
      ┌────────────────────────┐
      │ OSRM API Request       │
      │ /route?coords=[lat,lon]│
      └──────────┬─────────────┘
                 │
                 ▼
      ┌────────────────────────┐
      │ OSRM Response          │
      │ {distance: 621400m,    │
      │  duration: 30840s}     │
      └──────────┬─────────────┘
                 │
                 ▼
     ┌──────────────────────────┐
     │ Guardar distance_km,     │
     │ duration_seconds en DB   │
     └──────────┬───────────────┘
                 │
                 ▼
     ┌──────────────────────────┐
     │ Redireccionar a          │
     │ routes.show con éxito    │
     └──────────────────────────┘
```

---

## 13. Referencias Rápidas

### URLs Comunes

| Acción | URL | Método |
|--------|-----|--------|
| Dashboard | `/dashboard` | GET |
| Listar rutas | `/routes` | GET |
| Crear ruta | `/routes/create` | GET |
| Guardar ruta | `/routes` | POST |
| Ver ruta | `/routes/1` | GET |
| Editar ruta | `/routes/1/edit` | GET |
| Actualizar ruta | `/routes/1` | PUT |
| Eliminar ruta | `/routes/1` | DELETE |

### Métodos Eloquent Frecuentes

```php
// Obtener todos
Route::all();
Route::get();

// Obtener uno
Route::find(1);
Route::where('status', 'pending')->first();

// Crear
Route::create(['field' => 'value']);

// Actualizar
$route->update(['status' => 'completed']);

// Eliminar
$route->delete();

// Con relaciones
Route::with('fleet', 'deliveries')->get();
```

### Helpers Blade

```php
@csrf                      // Token CSRF
@method('PUT')             // Simulador de método HTTP
{{ route('name', $id) }}   // Generar URL
{{ auth()->user() }}       // Usuario actual
@auth ... @endauth         // Bloque autenticado
@guest ... @endguest       // Bloque visitante
{{ old('field') }}         // Valor previo en formulario
@error('field') @enderror  // Mostrar errores
```

---

## 14. Tabla de Referencia de Campos

| Modelo | Campo | Tipo | Restricción |
|--------|-------|------|-------------|
| Company | id | INT | PK |
| | name | STRING | UNIQUE |
| | email | STRING | UNIQUE |
| User | id | INT | PK |
| | email | STRING | UNIQUE |
| | company_id | INT | FK → Company |
| Route | id | INT | PK |
| | distance_km | DECIMAL | Calculado |
| | duration_seconds | INT | Calculado |
| | status | ENUM | pending, in_progress... |
| Delivery | id | INT | PK |
| | route_id | INT | FK → Route |
| | client_id | INT | FK → Client |
| | status | STRING | pending, delivered... |

---

## Conclusión

Esta arquitectura proporciona:
- **Modularidad**: Cada clase tiene una responsabilidad clara
- **Escalabilidad**: Fácil agregar nuevas entidades
- **Seguridad**: Autenticación, roles y permisos integrados
- **Automatización**: Geocodificación y cálculo automático de rutas
- **Usabilidad**: Interfaz web intuitiva con mapas interactivos

El sistema está diseñado siguiendo patrones de Laravel (MVC, Resource Controllers, Eloquent ORM) para facilitar el desarrollo y mantenimiento.
