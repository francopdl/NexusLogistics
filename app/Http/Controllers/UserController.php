<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Lista de usuarios
    public function index()
    {
        $users = User::with('roles', 'company')
            ->paginate(10);
        
        return view('users.index', compact('users'));
    }

    // Formulario para crear usuario
    public function create()
    {
        $roles = Role::all();
        
        return view('users.create', compact('roles'));
    }

    // Guardar usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Asignar roles
        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    // Ver detalles de usuario
    public function show(User $user)
    {
        $user->load('roles', 'company');
        
        return view('users.show', compact('user'));
    }

    // Formulario para editar
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        
        return view('users.edit', compact('user', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Actualizar roles
        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    // Eliminar usuario
    public function destroy(User $user)
    {
        // No permitir borrar la propia cuenta
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }
}
