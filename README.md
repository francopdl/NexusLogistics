🚚 NexusLogistics
Sistema de Gestión Logística Multiempresa desarrollado con Laravel 11.

📌 Descripción
NexusLogistics es una aplicación web que permite a diferentes empresas gestionar:
👥 Usuarios


🏢 Empresas


👨‍💼 Clientes


🚛 Flotas


🚐 Vehículos


🗺 Rutas


📦 Entregas


El sistema está diseñado bajo arquitectura MVC, utilizando Eloquent ORM, sistema de roles y permisos, e integración con APIs externas para geolocalización y cálculo de rutas.

🏗 Arquitectura
La aplicación está desarrollada con:
Backend: Laravel 11


Base de Datos: PostgreSQL


Frontend: Blade + Bootstrap 5


Mapas: Leaflet


Datos cartográficos: OpenStreetMap


Geocodificación: Nominatim


Cálculo de rutas: OSRM



🗄 Modelo de Base de Datos
El sistema sigue un diseño relacional en PostgreSQL con las siguientes relaciones principales:
Relaciones 1:N
Company → Users


Company → Clients


Company → Fleets


Fleet → Vehicles


Fleet → Routes


Route → Deliveries


Client → Deliveries


Relaciones N:M
User ↔ Role (tabla pivot: role_user)


Role ↔ Permission (tabla pivot: permission_role)


Esto permite un sistema flexible y escalable de control de acceso.

🔐 Sistema de Roles y Permisos
El sistema implementa control de acceso basado en roles:
Roles predefinidos
Admin


Manager


Driver


Ejemplo de permisos
create_routes


edit_routes


delete_routes


manage_users


manage_vehicles


view_deliveries


El acceso está controlado mediante:
Middlewares


Métodos personalizados (hasRole(), hasPermission())


Directivas Blade (@auth, @guest)



🗺 Integración de Mapas
Al crear una ruta:
El usuario introduce origen y destino.


Se geocodifican las direcciones usando Nominatim.


Se calcula distancia y duración con OSRM.


Se guardan automáticamente:


Coordenadas


Distancia en km


Duración en segundos


Se visualiza en un mapa interactivo con Leaflet.


Esto permite mostrar rutas dinámicas y estadísticas en tiempo real.

🎨 Vistas y Blade
Se aplicaron correctamente los conceptos clave:
@extends


@section


Componentes Blade (clases y anónimos)


Layout reutilizable


Validaciones con @error


Protección CSRF


Directivas @auth y @guest
<img width="1114" height="899" alt="Entidad relacion Nexus" src="https://github.com/user-attachments/assets/6163e5c7-497b-457f-adad-f547c5d61248" />

<img width="373" height="1227" alt="Casos de uso Nexus" src="https://github.com/user-attachments/assets/1e31f642-a1f9-4a21-bab3-72477b27acd2" />







