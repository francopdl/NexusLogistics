<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nexus Logistics')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            border-left-color: var(--secondary-color);
            color: white;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px 8px 0 0;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 50px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-truck-fast"></i> Nexus Logistics
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="#">{{ auth()->user()->name ?? 'Usuario' }}</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="nav-link btn btn-link" type="submit">Cerrar sesión</button>
                        </form>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Iniciar sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 sidebar">
                <div class="menu">
                    @auth
                    <a href="{{ route('dashboard') }}" class="nav-link @active(request()->is('dashboard'))">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    
                    @if(auth()->user()?->hasAnyRole(['admin', 'manager']))
                    <a href="{{ route('companies.index') }}" class="nav-link @active(request()->is('companies*'))">
                        <i class="fas fa-building"></i> Empresas
                    </a>
                    @endif
                    
                    @if(auth()->user()?->hasAnyRole(['admin', 'manager']))
                    <a href="{{ route('clients.index') }}" class="nav-link @active(request()->is('clients*'))">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                    @endif
                    
                    @if(auth()->user()?->hasAnyRole(['admin', 'manager']))
                    <a href="{{ route('fleets.index') }}" class="nav-link @active(request()->is('fleets*'))">
                        <i class="fas fa-boxes"></i> Flotas
                    </a>
                    @endif
                    
                    @if(auth()->user()?->hasAnyRole(['admin', 'manager']))
                    <a href="{{ route('vehicles.index') }}" class="nav-link @active(request()->is('vehicles*'))">
                        <i class="fas fa-car"></i> Vehículos
                    </a>
                    @endif
                    
                    @if(auth()->user()?->hasAnyRole(['admin', 'manager']))
                    <a href="{{ route('routes.index') }}" class="nav-link @active(request()->is('routes*'))">
                        <i class="fas fa-map"></i> Rutas
                    </a>
                    @endif
                    
                    @if(auth()->user()?->hasAnyRole(['admin', 'manager', 'driver']))
                    <a href="{{ route('deliveries.index') }}" class="nav-link @active(request()->is('deliveries*'))">
                        <i class="fas fa-box"></i> Entregas
                    </a>
                    @endif
                    
                    @if(auth()->user()?->hasRole('admin'))
                    <a href="{{ route('users.index') }}" class="nav-link @active(request()->is('users*'))">
                        <i class="fas fa-user-tie"></i> Usuarios
                    </a>
                    @endif
                    @endauth
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" />
                @endif

                @if(session('error'))
                    <x-alert type="danger" :message="session('error')" />
                @endif

                @if($errors->any())
                    <x-alert type="danger" :message="'Errores en el formulario'" />
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Nexus Logistics. Todos los derechos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    @stack('scripts')
</body>
</html>
