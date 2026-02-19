<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Lista de clientes
    public function index()
    {
        $clients = Client::with('company')->paginate(15);
        return view('clients.index', compact('clients'));
    }

    // Formulario para crear cliente
    public function create()
    {
        $companies = Company::all();
        return view('clients.create', compact('companies'));
    }

    // Guardar cliente
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Cliente creado exitosamente');
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    // Formulario para editar cliente
    public function edit(Client $client)
    {
        $companies = Company::all();
        return view('clients.edit', compact('client', 'companies'));
    }

    // Actualizar cliente
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
        ]);

        $client->update($validated);

        return redirect()->route('clients.show', $client)->with('success', 'Cliente actualizado exitosamente');
    }

    // Eliminar cliente
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Cliente eliminado exitosamente');
    }
}
