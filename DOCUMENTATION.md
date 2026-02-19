# Nexus Logistics - Sistema de Gestión Logística

## Descripción del Proyecto

Nexus Logistics es un sistema completo de gestión logística que permite a diferentes empresas registrarse para gestionar sus flotas, rutas y entregas en tiempo real. El sistema incluye características avanzadas como integración con Google Maps API, sistema de roles y permisos, y seguimiento de entregas en vivo.

## Características Principales

### 1. **Gestión de Empresas**
- Registro y administración de múltiples empresas
- Perfil completo con información de contacto y ubicación
- Estadísticas de usuarios, clientes y flotas

### 2. **Sistema de Roles y Permisos**
- Roles disponibles: Admin, Manager, Driver, Client
- Permisos granulares para cada rol:
  - **Admin**: Acceso completo al sistema
  - **Manager**: Gestión de clientes, flotas, vehículos, rutas y entregas
  - **Driver**: Vista de entregas y actualización de estado
  - **Client**: Vista del dashboard

### 3. **Gestión de Flotas**
- Creación y administración de flotas por empresa
- Descripción detallada de flotas
- Control de vehículos por flota

### 4. **Gestión de Vehículos**
- Registro de vehículos con información completa:
  - Placa de matrícula (única)
  - Tipo, fabricante, modelo y año
  - Estados: Disponible, En uso, Mantenimiento
- Asociación a flotas específicas

### 5. **Gestión de Rutas**
- Creación de rutas con origen y destino
- Horarios estimados de salida y llegada
- Estados: Pendiente, En progreso, Completada, Cancelada
- Seguimiento en mapa de Google Maps

### 6. **Gestión de Entregas**
- Creación de entregas asociadas a rutas y clientes
- Información del paquete
- Geolocalización en tiempo real (latitud, longitud)
- Estados: Pendiente, En tránsito, Entregada, Fallida
- Historial completo de entregas

### 7. **Integración con Google Maps**
- Visualización de rutas en mapa
- Geolocalización de entregas
- Cálculo de distancias y tiempos
- Búsqueda de direcciones con geocodificación

## Arquitectura del Sistema

### Base de Datos (PostgreSQL)

#### Relaciones Principales
```
Company (1) ──── (N) User
Company (1) ──── (N) Client
Company (1) ──── (N) Fleet
Fleet (1) ──── (N) Vehicle
Fleet (1) ──── (N) Route
Route (1) ──── (N) Delivery
Client (1) ──── (N) Delivery
User (N) ──── (N) Role (mediante tabla pivot role_user)
Role (N) ──── (N) Permission (mediante tabla pivot permission_role)
```

### Tablas de la Base de Datos

1. **companies**: Almacena información de empresas
2. **users**: Usuarios del sistema con referencia a empresa
3. **clients**: Clientes de cada empresa
4. **fleets**: Flotas de vehículos
5. **vehicles**: Vehículos individuales
6. **routes**: Rutas de distribución
7. **deliveries**: Entregas individuales
8. **roles**: Roles del sistema
9. **permissions**: Permisos disponibles
10. **role_user**: Relación N:N entre usuarios y roles
11. **permission_role**: Relación N:N entre permisos y roles

## Stack Tecnológico

- **Backend**: Laravel 12
- **Frontend**: Blade Templates con Bootstrap 5
- **Base de Datos**: PostgreSQL
- **API Externa**: Google Maps API (Geocoding, Distance Matrix)
- **Autenticación**: Laravel built-in authentication
- **ORM**: Eloquent

## Componentes Blade Creados

### 1. **Componentes Reutilizables**

#### `alert`
```blade
<x-alert type="success" :message="session('success')" />
```

#### `form-input`
```blade
<x-form-input 
    name="email" 
    type="email"
    label="Correo Electrónico" 
    required 
    placeholder="ejemplo@email.com"
/>
```

#### `form-textarea`
```blade
<x-form-textarea 
    name="description" 
    label="Descripción" 
    required 
    :value="$model->description"
/>
```

#### `form-select`
```blade
<x-form-select 
    name="status" 
    label="Estado"
    :options="['active' => 'Activo', 'inactive' => 'Inactivo']"
    :selected="$model->status"
/>
```

#### `button`
```blade
<x-button type="primary" href="{{ route('companies.create') }}">
    <i class="fas fa-plus"></i> Nueva Empresa
</x-button>
```

#### `map`
```blade
<x-map 
    map-id="delivery-map"
    :latitude="$delivery->latitude"
    :longitude="$delivery->longitude"
    :markers="$markers"
    height="500px"
/>
```

### 2. **Layout Principal** (`layouts/app`)
- Navbar con información de usuario
- Sidebar con navegación por roles
- Área de contenido principal
- Footer

## Vistas Creadas

### Estructura de Carpetas
```
resources/views/
├── layouts/
│   └── app.blade.php
├── components/
│   ├── alert.blade.php
│   ├── button.blade.php
│   ├── form-input.blade.php
│   ├── form-select.blade.php
│   ├── form-textarea.blade.php
│   └── map.blade.php
├── companies/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── dashboard.blade.php
└── ... (otras vistas)
```

## Rutas del Sistema

```
GET  /                              Redirige a dashboard
GET  /dashboard                     Dashboard principal
GET  /companies                     Lista de empresas
POST /companies                     Crear empresa
GET  /companies/{company}           Ver detalle empresa
PUT  /companies/{company}           Actualizar empresa
DELETE /companies/{company}         Eliminar empresa

GET  /clients                       Lista de clientes
GET  /fleets                        Lista de flotas
GET  /vehicles                      Lista de vehículos
GET  /routes                        Lista de rutas
GET  /deliveries                    Lista de entregas

POST /logout                        Cerrar sesión
```

## Servicio de Geolocalización

### GeoLocationService
Ubicación: `app/Services/GeoLocationService.php`

Métodos disponibles:
- `getCoordinatesFromAddress($address)`: Convierte dirección a coordenadas
- `getAddressFromCoordinates($latitude, $longitude)`: Convierte coordenadas a dirección
- `calculateDistance($origin, $destination)`: Calcula distancia entre dos puntos

## Middlewares de Autenticación

### CheckRole
Verifica que el usuario tenga uno de los roles especificados:
```php
Route::get('/admin', function () {
    // ...
})->middleware('role:admin,manager');
```

### CheckPermission
Verifica que el usuario tenga los permisos necesarios:
```php
Route::get('/editar', function () {
    // ...
})->middleware('permission:edit_users');
```

## Configuración de Google Maps

### Variables de Entorno
```env
GOOGLE_MAPS_API_KEY=tu_clave_aqui
GOOGLE_MAPS_ENABLED=true
```

### Archivo de Configuración
`config/geoservices.php` - Contiene toda la configuración de servicios geográficos

## Seeds del Sistema

### RolePermissionSeeder
Crea automáticamente:
- 4 roles: Admin, Manager, Driver, Client
- 10 permisos asociados a cada rol
- Se ejecuta con: `php artisan db:seed --class=RolePermissionSeeder`

## Instalación y Configuración

### Prerequisites
- PHP 8.2+
- PostgreSQL 12+
- Composer
- Node.js (para Vite)

### Pasos de Instalación

1. **Clonar repositorio**
```bash
git clone <repositorio>
cd Proyecto_NexusLogistics
```

2. **Instalar dependencias PHP**
```bash
composer install
```

3. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos en .env**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=Nexus
DB_USERNAME=Franco
DB_PASSWORD=1234
```

5. **Ejecutar migraciones**
```bash
php artisan migrate
```

6. **Ejecutar seeders**
```bash
php artisan db:seed
```

7. **Configurar Google Maps API**
```env
GOOGLE_MAPS_API_KEY=tu_clave_de_api
GOOGLE_MAPS_ENABLED=true
```

8. **Instalar dependencias Frontend**
```bash
npm install
npm run dev
```

9. **Iniciar servidor (con Laravel Herd o artisan)**
```bash
php artisan serve
# o si usas Herd, el proyecto se sirve automáticamente
```

## Uso de Métodos de Autenticación

### Verificar rol del usuario
```blade
@if(auth()->user()->hasRole('admin'))
    <!-- Solo admin ve esto -->
@endif

@if(auth()->user()->hasAnyRole(['admin', 'manager']))
    <!-- Admin y manager ven esto -->
@endif
```

### Proteger rutas
```php
Route::middleware('role:admin')->get('/admin', function() {
    // Solo admin tiene acceso
});

Route::middleware('permission:manage_companies')->get('/companies', function() {
    // Requiere permiso manage_companies
});
```

## Estadísticas del Dashboard

El dashboard muestra:
- Total de empresas registradas
- Total de clientes
- Total de vehículos
- Entregas pendientes
- Entregas recientes (últimas 5)
- Rutas activas (últimas 5)

## Estructura de Carpetas del Proyecto

```
Proyecto_NexusLogistics/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CompanyController.php
│   │   │   ├── ClientController.php
│   │   │   ├── FleetController.php
│   │   │   ├── VehicleController.php
│   │   │   ├── RouteController.php
│   │   │   ├── DeliveryController.php
│   │   │   └── DashboardController.php
│   │   └── Middleware/
│   │       ├── CheckRole.php
│   │       └── CheckPermission.php
│   ├── Models/
│   │   ├── Company.php
│   │   ├── Client.php
│   │   ├── Fleet.php
│   │   ├── Vehicle.php
│   │   ├── Route.php
│   │   ├── Delivery.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   └── User.php
│   └── Services/
│       └── GeoLocationService.php
├── config/
│   └── geoservices.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── RolePermissionSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── companies/
│       └── dashboard.blade.php
├── routes/
│   ├── web.php
│   └── console.php
└── ...
```

## Notas Importantes

- **Seguridad**: Todas las rutas están protegidas por autenticación
- **Validación**: Se validan todos los datos de entrada en los controladores
- **Relaciones**: Las relaciones se definen mediante Eloquent con restricciones de integridad referencial
- **Estilos**: Se utiliza Bootstrap 5 para una interfaz moderna
- **Iconos**: Se usan iconos de Font Awesome 6.4

## Próximas Mejoras Sugeridas

- [ ] Sistema de notificaciones en tiempo real
- [ ] Reportes avanzados y exportación de datos
- [ ] Integración con sistemas de pago
- [ ] API REST para terceros
- [ ] Vista móvil mejorada
- [ ] Sistema de chat entre conductores y clientes

## Licencia

Este proyecto está bajo licencia MIT.

## Soporte

Para reportar errores o sugerencias, contacta con el equipo de desarrollo.

---

**Versión**: 1.0.0  
**Última actualización**: Febrero 2026
