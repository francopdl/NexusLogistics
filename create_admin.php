<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Create admin user
    $admin_role = Role::where('name', 'admin')->first();
    
    if (!$admin_role) {
        echo "❌ Error: Role 'admin' not found\n";
        exit(1);
    }

    // Check if user already exists
    $existingUser = User::where('email', 'admin@nexus.local')->first();
    if ($existingUser) {
        echo "⚠️ El usuario admin@nexus.local ya existe\n";
        echo "Email: admin@nexus.local\n";
        echo "Password: password123\n";
        exit(0);
    }

    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@nexus.local',
        'password' => Hash::make('password123'),
    ]);

    $user->roles()->attach($admin_role->id);

    echo "✅ Usuario admin creado exitosamente:\n";
    echo "Email: admin@nexus.local\n";
    echo "Password: password123\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
