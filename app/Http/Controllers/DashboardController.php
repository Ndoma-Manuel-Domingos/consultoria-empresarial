<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenant = app('currentTenant');

        // Example data for the charts
        $revenueChart = [12000, 14500, 13200, 17800, 19500, 22000, 21000, 24500, 26800, 29100, 31500, 34200];
        $distributionChart = [45, 25, 20, 10];

        return view('dashboard', compact('revenueChart', 'distributionChart', 'tenant'));
    }
}
