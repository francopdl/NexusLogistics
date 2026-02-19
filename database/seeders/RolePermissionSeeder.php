<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            ['name' => 'view_dashboard', 'description' => 'Ver panel de control'],
            ['name' => 'manage_companies', 'description' => 'Gestionar empresas'],
            ['name' => 'manage_clients', 'description' => 'Gestionar clientes'],
            ['name' => 'manage_fleets', 'description' => 'Gestionar flotas'],
            ['name' => 'manage_vehicles', 'description' => 'Gestionar vehículos'],
            ['name' => 'manage_routes', 'description' => 'Gestionar rutas'],
            ['name' => 'manage_deliveries', 'description' => 'Gestionar entregas'],
            ['name' => 'view_reports', 'description' => 'Ver reportes'],
            ['name' => 'manage_users', 'description' => 'Gestionar usuarios'],
            ['name' => 'manage_roles', 'description' => 'Gestionar roles'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador del sistema']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Gerente de empresa']
        );

        $driverRole = Role::firstOrCreate(
            ['name' => 'driver'],
            ['description' => 'Conductor']
        );

        $clientRole = Role::firstOrCreate(
            ['name' => 'client'],
            ['description' => 'Cliente']
        );

        // Asignar permisos a roles
        $adminPerms = Permission::all();
        $adminRole->permissions()->sync($adminPerms->pluck('id')->toArray());

        $managerPerms = Permission::whereIn('name', [
            'view_dashboard',
            'manage_clients',
            'manage_fleets',
            'manage_vehicles',
            'manage_routes',
            'manage_deliveries',
            'view_reports'
        ])->get();
        $managerRole->permissions()->sync($managerPerms->pluck('id')->toArray());

        $driverPerms = Permission::whereIn('name', [
            'view_dashboard',
            'manage_deliveries',
        ])->get();
        $driverRole->permissions()->sync($driverPerms->pluck('id')->toArray());

        $clientPerms = Permission::whereIn('name', [
            'view_dashboard',
        ])->get();
        $clientRole->permissions()->sync($clientPerms->pluck('id')->toArray());
    }
}

