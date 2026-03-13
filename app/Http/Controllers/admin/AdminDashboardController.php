<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }
    
        $totalPets = Pet::count();
        $totalUsers = User::count();
        $totalOrders = Purchase::count();

        // Appointments
        $appointmentCount = Appointment::where('Status', 'Upcoming')->count();

        $recentAppointments = Appointment::with(['customer', 'pet'])
            ->orderBy('AppointmentDateTime')
            ->take(5)
            ->get();

        return view('admin.adminDashboard', [
            'currentPage'        => 'dashboard',
            'totalPets'          => $totalPets,
            'totalUsers'         => $totalUsers,
            'totalOrders'        => $totalOrders,
            'appointmentCount'   => $appointmentCount,
            'recentAppointments' => $recentAppointments,
        ]);
    }
}
