# Guía: Relación entre Controladores, Vistas y Modelos en Nexus Logistics

## 📚 Tabla de Contenidos
1. [Arquitectura MVC](#arquitectura-mvc)
2. [Flujo de Datos](#flujo-de-datos)
3. [Ejemplos Prácticos](#ejemplos-prácticos)
4. [Relaciones entre Modelos](#relaciones-entre-modelos)
5. [Patrones Utilizados](#patrones-utilizados)

---

## 🏗️ Arquitectura MVC

**MVC = Model-View-Controller**

```
┌─────────────┐
│  Usuario    │
└──────┬──────┘
       │
       │ 1. Solicitud HTTP
       ▼
┌─────────────────────────────┐
│   CONTROLLER                │
│ - Procesa la solicitud      │
│ - Valida datos              │
│ - Interactúa con modelos    │
└──────────┬──────────────────┘
           │
           │ 2. Consulta/Modifica datos
           ▼
┌─────────────────────────────┐
│   MODEL                     │
│ - Representa tabla BD       │
│ - Define relaciones         │
│ - Lógica de negocio         │
└──────────┬──────────────────┘
           │
           │ 3a. Datos procesados
           ├──────────────────┐
           │                  │
           ▼                  ▼
        BASE DE DATOS    CONTROLADOR
                             │
                             │ 3b. Devuelve datos
                             ▼
                    ┌─────────────────────────────┐
                    │   VIEW/VISTA                │
                    │ - HTML/Blade                │
                    │ - Muestra al usuario        │
                    │ - Envía formularios         │
                    └─────────────────────────────┘
```

---

## 🔄 Flujo de Datos (Ejemplo: Listar Clientes)

### 1️⃣ **Usuario accede a la URL**
```
GET /clients → Route en web.php
```

### 2️⃣ **Route direcciona al Controlador**
```php
// routes/web.php
Route::resource('clients', ClientController::class);
// Equivale a:
Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
```

### 3️⃣ **Controller procesa la solicitud**
```php
// app/Http/Controllers/ClientController.php
public function index()
{
    // Consulta el modelo para obtener datos
    $clients = Client::with('company')
        ->paginate(10);
    
    // Envía los datos a la vista
    return view('clients.index', compact('clients'));
}
```

### 4️⃣ **Modelo consulta la base de datos**
```php
// app/Models/Client.php
class Client extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'city', 'company_id'];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

// El método with('company') carga la empresa de cada cliente
// SELECT * FROM clients LEFT JOIN companies ON ...
```

### 5️⃣ **Vista muestra los datos**
```blade
{{-- resources/views/clients/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <table class="table">
        <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->company->name }}</td>
                    {{-- Accede a la empresa a través de la relación --}}
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $clients->links() }} {{-- Paginación --}}
@endsection
```

### 6️⃣ **HTML se envía al navegador**
```html
<table class="table">
    <tbody>
        <tr>
            <td>Acme Corp</td>
            <td>TechCompany</td>
        </tr>
    </tbody>
</table>
```

---

## 📋 Ejemplos Prácticos

### Ejemplo 1: CRUD Completo (Clientes)

#### **CREAR (Create)**

**Formulario (Vista):**
```blade
{{-- resources/views/clients/create.blade.php --}}
<form action="{{ route('clients.store') }}" method="POST">
    @csrf
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <button type="submit">Guardar</button>
</form>
```

**Procesar (Controlador):**
```php
// app/Http/Controllers/ClientController.php
public function store(Request $request)
{
    // 1. VALIDAR datos
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:clients',
        'phone' => 'required|string',
    ]);
    
    // 2. CREAR mediante modelo (INSERT en BD)
    Client::create($validated);
    
    // 3. REDIRECCIONAR con mensaje
    return redirect()->route('clients.index')
        ->with('success', 'Cliente creado correctamente');
}
```

**Modelo (Definir estructura):**
```php
// app/Models/Client.php
class Client extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'city', 'company_id'];
    
    // Relación: Un cliente pertenece a una empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    // Relación: Un cliente puede tener múltiples entregas
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
```

---

#### **LEER (Read) - Ver detalles**

**Vista:**
```blade
{{-- resources/views/clients/show.blade.php --}}
<h1>{{ $client->name }}</h1>
<p>Email: {{ $client->email }}</p>
<p>Empresa: {{ $client->company->name }}</p>

<table>
    @foreach($client->deliveries as $delivery)
        <tr>
            <td>{{ $delivery->package_info }}</td>
            <td>{{ $delivery->status }}</td>
        </tr>
    @endforeach
</table>
```

**Controlador:**
```php
public function show(Client $client)
{
    // Laravel inyecta automáticamente el cliente por ID (Route Model Binding)
    // Se carga con sus relaciones
    $client->load('deliveries');
    
    return view('clients.show', compact('client'));
}
```

---

#### **ACTUALIZAR (Update) - Editar**

**Formulario (Vista):**
```blade
<form action="{{ route('clients.update', $client) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="text" name="name" value="{{ $client->name }}">
    <input type="email" name="email" value="{{ $client->email }}">
    <button type="submit">Actualizar</button>
</form>
```

**Procesar (Controlador):**
```php
public function update(Request $request, Client $client)
{
    // 1. VALIDAR
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:clients,email,' . $client->id,
    ]);
    
    // 2. ACTUALIZAR (UPDATE en BD)
    $client->update($validated);
    
    // 3. REDIRECCIONAR
    return redirect()->route('clients.index')
        ->with('success', 'Cliente actualizado');
}
```

---

#### **ELIMINAR (Delete)**

**Botón en Vista:**
```blade
<form action="{{ route('clients.destroy', $client) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" onclick="return confirm('¿Está seguro?')">
        Eliminar
    </button>
</form>
```

**Procesar (Controlador):**
```php
public function destroy(Client $client)
{
    // DELETE de la BD
    $client->delete();
    
    return redirect()->route('clients.index')
        ->with('success', 'Cliente eliminado');
}
```

---

## 🔗 Relaciones entre Modelos

### Tipos de Relaciones en Nexus Logistics

#### 1️⃣ **One-to-Many (Uno a Muchos)**

```php
// Una Empresa TIENE muchos Clientes
// app/Models/Company.php
public function clients()
{
    return $this->hasMany(Client::class);
}

// Un Cliente PERTENECE A una Empresa
// app/Models/Client.php
public function company()
{
    return $this->belongsTo(Company::class);
}

// En la Vista:
$company->clients  // Obtener todos los clientes de la empresa
$client->company   // Obtener la empresa del cliente
```

#### 2️⃣ **Many-to-Many (Muchos a Muchos)**

```php
// Un Usuario TIENE múltiples Roles
// Un Rol TIENE múltiples Usuarios
// app/Models/User.php
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user');
}

// app/Models/Role.php
public function users()
{
    return $this->belongsToMany(User::class, 'role_user');
}

// En el Controlador:
$user->roles()->attach($roleId);           // Asignar rol
$user->roles()->detach($roleId);           // Quitar rol
$user->roles()->sync([1, 2, 3]);           // Reemplazar roles
```

#### 3️⃣ **Has-Many (Muchos)**

```php
// Una Flota TIENE múltiples Vehículos
// app/Models/Fleet.php
public function vehicles()
{
    return $this->hasMany(Vehicle::class);
}

// En la Vista:
@foreach($fleet->vehicles as $vehicle)
    <p>{{ $vehicle->license_plate }}</p>
@endforeach
```

#### 4️⃣ **Belongs-To (Pertenece A)**

```php
// Un Vehículo PERTENECE A una Flota
// app/Models/Vehicle.php
public function fleet()
{
    return $this->belongsTo(Fleet::class);
}

// En el Controlador:
$fleet = $vehicle->fleet;  // Obtener la flota del vehículo
```

---

## 🎯 Patrones Utilizados

### Patrón 1: **Eager Loading (Carga anticipada)**

❌ **MAL - N+1 Problem:**
```php
$clients = Client::all();  // 1 query

foreach($clients as $client) {
    echo $client->company->name;  // 100 queries adicionales
}
// Total: 101 queries ❌
```

✅ **BIEN - Eager Loading:**
```php
$clients = Client::with('company')->paginate(10);  // 1 query

foreach($clients as $client) {
    echo $client->company->name;  // Sin queries adicionales
}
// Total: 1 query ✅
```

### Patrón 2: **Route Model Binding**

```php
// routes/web.php
Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');

// Controller - Laravel inyecta automáticamente el modelo
public function show(Client $client)
{
    // $client es automáticamente el cliente con ese ID
    // Equivalente a: $client = Client::findOrFail($id);
    return view('clients.show', compact('client'));
}

// En la Vista:
{{ route('clients.show', $client) }}  // Genera: /clients/1
```

### Patrón 3: **Mutadores de Atributos**

```php
// app/Models/User.php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function email(): Attribute
{
    return Attribute::make(
        set: fn (string $value) => strtolower($value),  // Guardar en minúsculas
    );
}

// Al crear/actualizar: $user->email = 'JUAN@EXAMPLE.COM' → se guarda 'juan@example.com'
```

### Patrón 4: **Validación en Modelo vs Controlador**

```php
// ✅ VALIDACIÓN EN CONTROLADOR (Recomendado)
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:clients',
    ]);
    
    Client::create($validated);
}

// El Controlador:
// - Valida que los datos sean correctos
// - Muestra errores al usuario
// - Controla el flujo de la aplicación
```

---

## 📊 Diagrama Completo: Crear una Entrega

```
┌─────────────────────────────────────────────────────────────────┐
│                      USUARIO                                     │
│              Completa formulario de entrega                      │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                   VISTA (Blade)                                  │
│    @extends('layouts.app')                                      │
│    <form action="{{ route('deliveries.store') }}" ...           │
│    <input name="route_id">                                       │
│    <input name="client_id">                                      │
│    <textarea name="package_info"></textarea>                    │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           │ POST /deliveries
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│               ROUTE (routes/web.php)                             │
│    Route::resource('deliveries', DeliveryController::class)     │
│    → Route::post('/deliveries', 'DeliveryController@store')     │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│            CONTROLLER (DeliveryController)                       │
│    public function store(Request $request) {                    │
│        1. Validar datos                                         │
│        $validated = $request->validate([...]);                  │
│                                                                  │
│        2. Crear mediante Modelo                                 │
│        Delivery::create($validated);                            │
│                                                                  │
│        3. Redireccionar                                         │
│        return redirect()->with('success', '...');               │
│    }                                                            │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│              MODEL (Delivery.php)                                │
│    class Delivery extends Model {                               │
│        protected $fillable = [                                  │
│            'route_id', 'client_id', 'package_info', 'status'   │
│        ];                                                        │
│                                                                  │
│        public function route() {                                │
│            return $this->belongsTo(Route::class);              │
│        }                                                        │
│    }                                                            │
│                                                                  │
│    ↓ INSERT INTO deliveries (...) VALUES (...)                │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│            DATABASE (PostgreSQL)                                 │
│    Table: deliveries                                            │
│    ┌─────┬──────────┬───────────┬─────────────────────┐         │
│    │ id  │ route_id │ client_id │ package_info │...  │         │
│    ├─────┼──────────┼───────────┼─────────────────────┤         │
│    │ 42  │ 5        │ 3         │ 'Documento...' │... │         │
│    └─────┴──────────┴───────────┴─────────────────────┘         │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                           ▼ Redireccionar a deliveries.index
┌─────────────────────────────────────────────────────────────────┐
│            VISTA (deliveries/index.blade.php)                   │
│    @foreach($deliveries as $delivery)                           │
│        <td>{{ $delivery->package_info }}</td>                   │
│        <td>{{ $delivery->route->origin }}</td>                  │
│        <td>{{ $delivery->client->name }}</td>                   │
│    @endforeach                                                  │
│                                                                  │
│    Muestra la nueva entrega creada                              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔧 Cheat Sheet: Comandos Útiles

```bash
# Crear Modelo + Migration + Controller
php artisan make:model Client -mrc

# Solo Modelo
php artisan make:model Client

# Solo Controlador (Resource)
php artisan make:controller ClientController --resource

# Vista
# (Se crean manualmente en resources/views/)

# Ejecutar migraciones
php artisan migrate

# Rollback última migración
php artisan migrate:rollback

# Abrir consola interactiva
php artisan tinker
> $client = App\Models\Client::find(1);
> $client->company->name;
> $client->delete();
```

---

## 📌 Resumen

| Componente | Responsabilidad | Ejemplo |
|---|---|---|
| **Modelo** | Representa datos | `Client::find(1)` |
| **Controlador** | Procesa lógica | Validar, crear, actualizar |
| **Vista** | Muestra HTML | `{{ $client->name }}` |
| **Ruta** | Direcciona solicitud | `Route::resource('clients', ...)` |
| **BD** | Almacena datos | Tabla `clients` |

**Flujo:** Usuario → Ruta → Controlador → Modelo → BD → Modelo → Vista → Usuario

---

## 🎓 Siguientes Pasos

1. **Estudia los 4 tipos de relaciones** en la BD
2. **Practica Eager Loading** para optimizar queries
3. **Aprende validaciones** en el Controlador
4. **Usa Tinker** para experimentar: `php artisan tinker`
5. **Lee el código** del proyecto: `ClientController` y `Client`

¡Ahora ya entiendes cómo funciona MVC en Nexus Logistics! 🚀
