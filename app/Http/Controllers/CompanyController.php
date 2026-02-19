<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Lista de empresas
    public function index()
    {
        $companies = Company::paginate(15);
        return view('companies.index', compact('companies'));
    }

    // Formulario para crear empresa
    public function create()
    {
        return view('companies.create');
    }

    // Guardar empresa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        Company::create($validated);

        return redirect()->route('companies.index')->with('success', 'Empresa creada exitosamente');
    }

    // Ver detalles de empresa
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    // Formulario para editar empresa
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    // Actualizar empresa
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
        ]);

        $company->update($validated);

        return redirect()->route('companies.show', $company)->with('success', 'Empresa actualizada exitosamente');
    }

    // Eliminar empresa
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')->with('success', 'Empresa eliminada exitosamente');
    }
}
