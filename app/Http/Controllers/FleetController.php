<?php

namespace App\Http\Controllers;

use App\Models\Fleet;
use App\Models\Company;
use Illuminate\Http\Request;

class FleetController extends Controller
{
    // Lista de flotas
    public function index()
    {
        $fleets = Fleet::with('company')
            ->paginate(6);
        
        return view('fleets.index', compact('fleets'));
    }

    // Formulario para crear flota
    public function create()
    {
        $companies = Company::all();
        
        return view('fleets.create', compact('companies'));
    }

    // Guardar flota
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Fleet::create($validated);

        return redirect()->route('fleets.index')
            ->with('success', 'Flota creada correctamente');
    }

    // Ver detalles de flota
    public function show(Fleet $fleet)
    {
        $fleet->load('company', 'vehicles', 'routes');
        
        return view('fleets.show', compact('fleet'));
    }

    // Formulario para editar flota
    public function edit(Fleet $fleet)
    {
        $companies = Company::all();
        
        return view('fleets.edit', compact('fleet', 'companies'));
    }

    // Actualizar flota
    public function update(Request $request, Fleet $fleet)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $fleet->update($validated);

        return redirect()->route('fleets.index')
            ->with('success', 'Flota actualizada correctamente');
    }

    // Eliminar flota
    public function destroy(Fleet $fleet)
    {
        $fleet->delete();

        return redirect()->route('fleets.index')
            ->with('success', 'Flota eliminada correctamente');
    }
}
