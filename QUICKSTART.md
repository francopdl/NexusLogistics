# Guía Rápida - Nexus Logistics

## 🚀 Inicio Rápido

### 1. Acceso Inicial
- La aplicación está lista para usar después de ejecutar las migraciones
- Para acceder, necesitas estar autenticado en Laravel
- Asegúrate de tener usuarios creados en la base de datos

### 2. Crear Admin por defecto
Ejecuta este comando para crear un usuario administrador:
```bash
php artisan tinker
```

Dentro de tinker:
```php
$admin = App\Models\User::create([
    'name' => 'Admin Usuario',
    'email' => 'admin@nexus.local',
    'password' => bcrypt('password123'),
    'company_id' => 1
]);

$adminRole = App\Models\Role::where('name', 'admin')->first();
$admin->roles()->attach($adminRole);
```

### 3. Acceso a Roles y Permisos
Todos los roles están disponibles en la tabla `roles`:
- **Admin**: Acceso total
- **Manager**: Gestión de operaciones
- **Driver**: Entregas
- **Client**: Consultas

Asignar un rol a un usuario:
```php
$user = App\Models\User::find(1);
$role = App\Models\Role::where('name', 'manager')->first();
$user->roles()->attach($role);
```

### 4. Usar Google Maps

#### Obtener tu API Key
1. Ir a [Google Cloud Console](https://console.cloud.google.com/)
2. Crear un nuevo proyecto
3. Habilitar APIs:
   - Google Maps JavaScript API
   - Geocoding API
   - Distance Matrix API
4. Crear una clave API
5. Guardar en `.env`:
```env
GOOGLE_MAPS_API_KEY=tu_clave_aqui
GOOGLE_MAPS_ENABLED=true
```

#### Usar el Servicio de Geolocalización
```php
use App\Services\GeoLocationService;

$geoService = new GeoLocationService();

// Obtener coordenadas desde una dirección
$coords = $geoService->getCoordinatesFromAddress("Madrid, España");
// Retorna: ['latitude' => 40.4168, 'longitude' => -3.7038]

// Obtener dirección desde coordenadas
$address = $geoService->getAddressFromCoordinates(40.4168, -3.7038);

// Calcular distancia entre dos puntos
$distance = $geoService->calculateDistance(
    ['latitude' => 40.4168, 'longitude' => -3.7038],
    ['latitude' => 41.3851, 'longitude' => 2.1734]
);
```

### 5. Proteger Rutas

En `routes/web.php`:
```php
// Solo para admin
Route::get('/admin', function() {
    // ...
})->middleware('role:admin');

// Para admin o manager
Route::get('/manage', function() {
    // ...
})->middleware('role:admin,manager');

// Requiere permiso específico
Route::delete('/company/{id}', function() {
    // ...
})->middleware('permission:manage_companies');
```

### 6. Usar Componentes Blade

#### Input
```blade
<x-form-input 
    name="email" 
    type="email"
    label="Email" 
    required
    placeholder="usuario@example.com"
/>
```

#### Select
```blade
<x-form-select 
    name="fleet_id"
    label="Flota"
    :options="$fleets->pluck('name', 'id')"
    :selected="$vehicle->fleet_id"
    required
/>
```

#### Textarea
```blade
<x-form-textarea 
    name="description"
    label="Descripción"
    rows="5"
    :value="$fleet->description"
/>
```

#### Botón
```blade
<x-button type="primary" href="{{ route('companies.create') }}">
    Nueva Empresa
</x-button>
```

#### Alerta
```blade
<x-alert type="success" message="Guardado exitosamente" />
<x-alert type="danger" message="Error en la validación" />
```

#### Mapa
```blade
<x-map 
    map-id="route-map"
    latitude="40.4168"
    longitude="-3.7038"
    zoom="8"
    height="500px"
/>
```

### 7. Validaciones en Controladores

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:companies',
        'phone' => 'required|string|max:20',
    ]);

    Company::create($validated);
    return redirect()->route('companies.index')
        ->with('success', 'Empresa creada exitosamente');
}
```

### 8. Relaciones Eloquent

```php
// Obtener empresa de un usuario
$company = $user->company;

// Obtener usuarios de una empresa
$users = $company->users;

// Obtener clientes de una empresa
$clients = $company->clients;

// Obtener roles de un usuario
$roles = $user->roles;

// Obtener permisos de un usuario a través de sus roles
$permissions = $user->roles()
    ->with('permissions')
    ->get()
    ->pluck('permissions')
    ->flatten();
```

### 9. Verificar Permisos en Vistas

```blade
@if(auth()->user()->hasRole('admin'))
    <!-- Solo usuarios con rol admin ven esto -->
@endif

@if(auth()->user()->hasAnyRole(['admin', 'manager']))
    <!-- Usuarios con rol admin o manager ven esto -->
@endif

@if(auth()->user()->hasAllRoles(['admin', 'manager']))
    <!-- Usuario debe tener TODOS estos roles -->
@endif
```

### 10. Comandos Útiles

```bash
# Crear empresa de prueba
php artisan tinker
> App\Models\Company::create(['name' => 'Test Co', ...])

# Resetear base de datos
php artisan migrate:refresh
php artisan migrate:refresh --seed

# Limpiar caché
php artisan config:clear
php artisan cache:clear

# Ver rutas
php artisan route:list

# Ejecutar seeder específico
php artisan db:seed --class=RolePermissionSeeder
```

## 📋 Workflow Típico

### Crear una Empresa Nueva
1. Ir a "Empresas" en el menú
2. Hacer clic en "Nueva Empresa"
3. Rellenar formulario con datos
4. Guardar
5. Ver detalles de la empresa creada

### Crear una Flota
1. Ir a "Flotas"
2. Hacer clic en "Nueva Flota"
3. Seleccionar empresa
4. Rellenar nombre y descripción
5. Guardar

### Crear una Entrega con Seguimiento
1. Ir a "Entregas"
2. Seleccionar ruta
3. Seleccionar cliente
4. Ingresar información del paquete
5. Sistema obtiene coordenadas automáticamente (si está Google Maps habilitado)
6. Guardar

### Ver Ruta en Mapa
1. Ir a detalle de ruta
2. Mapa se carga automáticamente
3. Ve marcadores de inicio y fin
4. Distancia y tiempo estimado se calculan

## 🔐 Seguridad

- **Validación**: Todos los formularios se validan servidor-side
- **Autenticación**: Requiere login para acceder
- **Autorización**: Middlewares CheckRole y CheckPermission
- **CSRF**: Protección incluida en formularios
- **SQL Injection**: Prevenido con consultas parametrizadas (Eloquent)
- **XSS**: Escapado automáticamente en Blade

## 🐛 Troubleshooting

### "Table does not exist"
```bash
php artisan migrate
php artisan config:clear
```

### "SQLSTATE[42P01]"
Asegúrate que todas las migraciones se han ejecutado en el orden correcto.

### Google Maps no funciona
1. Verifica la clave API en .env
2. Confirma que GOOGLE_MAPS_ENABLED=true
3. Revisa la consola del navegador para errores de JavaScript

### Rol/Permiso no funciona
```php
# Recarga la caché
php artisan config:clear
php artisan cache:clear

# Verifica que el usuario tiene el rol
$user->roles()->count()
```

---

¡Listo! El sistema está completamente operativo. 🎉
