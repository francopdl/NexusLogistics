<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\Delivery;
use App\Models\Route;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard con estadísticas
    public function index()
    {
        $totalCompanies = Company::count();
        $totalClients = Client::count();
        $totalVehicles = Vehicle::count();
        $pendingDeliveries = Delivery::where('status', '!=', 'delivered')->count();
        
        $recentDeliveries = Delivery::with('client')
            ->latest()
            ->limit(5)
            ->get();
        
        $activeRoutes = Route::whereIn('status', ['pending', 'in_progress'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalCompanies',
            'totalClients',
            'totalVehicles',
            'pendingDeliveries',
            'recentDeliveries',
            'activeRoutes'
        ));
    }
}
