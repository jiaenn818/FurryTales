<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentReminderMail;

class AdminAppointmentController extends Controller
{
    public function index(Request $request)
    {
        //check user type
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized User');
        }

        // Auto update Upcoming -> Ongoing (time-based)
        Appointment::where('Status', 'Upcoming')
            ->where('AppointmentDateTime', '<=', now())
            ->update(['Status' => 'Ongoing']);


        if ($request->has('clear')) {
            return redirect()->route('admin.appointments.index');
        }

        $selectedDate = $request->date
            ? Carbon::parse($request->date)
            : Carbon::now();

        $startOfWeek = $selectedDate->copy()->startOfWeek(); // current selected week
        $endOfWeek = $selectedDate->copy()->endOfWeek();

        // NEXT WEEK
        $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();
        $nextWeekEnd = Carbon::now()->addWeek()->endOfWeek();

        // Current week appointments
        $appointments = Appointment::with(['pet', 'customer'])
            ->whereBetween('AppointmentDateTime', [$startOfWeek, $endOfWeek])
            ->get();

        // Next week appointments (for reminder)
        $nextWeekAppointments = Appointment::with(['pet', 'customer'])
            ->whereBetween('AppointmentDateTime', [$nextWeekStart, $nextWeekEnd])
            ->get();

        $allAppointments = Appointment::with(['pet', 'customer'])
            ->orderBy('AppointmentDateTime', 'desc')
            ->paginate(10);

$query = Appointment::with(['pet','customer']);

if ($request->start_date && $request->end_date) {

    $startDate = Carbon::parse($request->start_date)->startOfDay();
    $endDate = Carbon::parse($request->end_date)->endOfDay();

    $query->whereBetween('AppointmentDateTime', [$startDate, $endDate]);
}

$allAppointments = $query
    ->orderBy('AppointmentDateTime', 'desc')
    ->get();

        return view('admin.viewAllAppointment', compact(
            'appointments',
            'nextWeekAppointments',
            'allAppointments',
            'selectedDate',
            'startOfWeek',
            'endOfWeek',
            'nextWeekStart',
            'nextWeekEnd'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:Completed,Cancelled',
        ]);

        $appointment = Appointment::findOrFail($id);
        $currentStatus = $appointment->Status;
        $newStatus = $request->status;

        // Allowed transitions
        $allowedTransitions = [
            'Upcoming' => ['Cancelled'],
            'Ongoing'  => ['Completed', 'Cancelled'],
        ];

        if (
            !isset($allowedTransitions[$currentStatus]) ||
            !in_array($newStatus, $allowedTransitions[$currentStatus])
        ) {
            abort(403, 'Invalid status change');
        }

        $appointment->update([
            'Status' => $newStatus,
        ]);

        return redirect()->back()->with('success', 'Appointment status updated.');
    }

    public function sendReminder($id)
    {
        // Check user
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized');
        }

        // Get appointment with customer & user
        $appointment = Appointment::with(['customer.user', 'pet'])->findOrFail($id);

        // Only allow Upcoming appointments
        if ($appointment->Status !== 'Upcoming') {
            return redirect()->back()->with('error', 'Only upcoming appointments can receive reminders.');
        }

        // Get email from related user
        $email = $appointment->customer?->user?->email;

        if (!$email) {
            return redirect()->back()->with('error', 'Customer email not found.');
        }

        // Send email
        Mail::to($email)->send(new AppointmentReminderMail($appointment));

        return redirect()->back()->with('success', 'Reminder email sent successfully.');
    }
}