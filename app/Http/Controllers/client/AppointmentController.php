<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Show the appointment booking form for a specific pet or all available pets
     */
    public function create($petID = null)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login to make an appointment.');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return redirect()->route('client.home')->with('error', 'Customer profile missing.');
        }

        // Fetch all available pets (not sold, and no upcoming/ongoing appointments)
        $allPets = Pet::whereDoesntHave('purchaseItem')
            ->whereDoesntHave('appointments', function ($query) {
                $query->whereIn('Status', ['Ongoing']);
            })
            ->get();

        if ($allPets->isEmpty()) {
            return redirect()->route('client.pets.index')->with('error', 'No pets are currently available for appointments.');
        }

        if (!$petID) {
            return redirect()->route('client.appointments.create', $allPets->first()->PetID);
        }

        $pet = Pet::findOrFail($petID);

        return view('Client.appointments.create', compact('pet', 'user', 'allPets'));
    }

    /**
     * Get available time slots for a specific date and pet (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date'  => 'required|date',
            'petID' => 'required|string',
        ]);

        $tz = 'Asia/Kuala_Lumpur';

        $date = Carbon::parse($request->input('date'), $tz)->startOfDay();
        $petID = $request->input('petID');
        $now   = Carbon::now($tz);

        $openingTime = $date->copy()->setTime(9, 0);
        $closingTime = $date->copy()->setTime(18, 0);

        // If today and after 6 PM, no slots
        if ($date->isToday() && $now >= $closingTime) {
            return response()->json(['slots' => []]);
        }

        $slots = [];
        $startTime = $openingTime->copy();

        while ($startTime < $closingTime) {
            $slotEndTime = $startTime->copy()->addMinutes(30);

            // Mark as past if today and slot starts before or at current time
            $isPast = $date->isToday() && $startTime <= $now;

            // Check if slot is already booked across ANY pet
            $isBooked = Appointment::where('AppointmentDateTime', $startTime->format('Y-m-d H:i:s'))
                ->whereIn('Status', ['Upcoming', 'Ongoing'])
                ->exists();

            $slots[] = [
                'time'     => $startTime->format('H:i'),
                'display'  => $startTime->format('g:i A'),
                'datetime' => $startTime->format('Y-m-d H:i:s'),
                'booked'   => $isPast || $isBooked,
            ];

            $startTime->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }



    /**
     * Store a new appointment
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login to make an appointment.');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return redirect()->route('client.home')->with('error', 'Customer profile missing.');
        }

        $request->validate([
            'petID' => 'required|string',
            'appointmentDateTime' => 'required|date',
            'method' => 'required|in:In-Person,Video Call',
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:20',
        ]);

        $customerID = $user->customer->customerID;
        $petID = $request->input('petID');
        $appointmentDateTime = $request->input('appointmentDateTime');

        // Prevent booking in the past
        $tz = 'Asia/Kuala_Lumpur';
        if (Carbon::parse($appointmentDateTime, $tz)->isPast()) {
            return back()->with('error', 'You cannot book an appointment in the past.');
        }

        // Check if the slot is still available (prevent race conditions across ALL pets)
        $existingAppointment = Appointment::where('AppointmentDateTime', $appointmentDateTime)
            ->whereIn('Status', ['Upcoming', 'Ongoing'])
            ->first();

        if ($existingAppointment) {
            return back()->with('error', 'This time slot has already been booked. Please select another time.');
        }

        // Create the appointment
        Appointment::create([
            'CustomerID' => $customerID,
            'PetID' => $petID,
            'AppointmentDateTime' => $appointmentDateTime,
            'Method' => $request->input('method'),
            'CustomerName' => $request->input('customerName'),
            'CustomerPhone' => $request->input('customerPhone'),
            'Status' => 'Upcoming',
        ]);

        return redirect()->route('client.appointments.index')->with('success', 'Appointment booked successfully!');
    }

    /**
     * Display customer's appointment history
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login to view appointments.');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return redirect()->route('client.home')->with('error', 'Customer profile missing.');
        }

        $this->updateAppointmentStatuses();

        $customerID = $user->customer->customerID;

        $appointments = Appointment::where('CustomerID', $customerID)
            ->with('pet')
            ->orderBy('AppointmentDateTime', 'desc')
            ->paginate(5);

        return view('Client.appointments.index', compact('appointments'));
    }

    /**
     * Cancel an upcoming appointment
     */
    public function cancel($appointmentID)
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        if (!$user->customer) {
            return redirect()->route('client.home')->with('error', 'Customer profile missing.');
        }

        $customerID = $user->customer->customerID;

        $appointment = Appointment::where('AppointmentID', $appointmentID)
            ->where('CustomerID', $customerID)
            ->where('Status', 'Upcoming')
            ->firstOrFail();

        $appointment->Status = 'Cancelled';
        $appointment->save();

        return back()->with('success', 'Appointment cancelled successfully.');
    }

    private function updateAppointmentStatuses()
    {
        $now = Carbon::now();

        // 1️⃣ Upcoming → Ongoing
        Appointment::where('Status', 'Upcoming')
            ->where('AppointmentDateTime', '<=', $now)
            ->where('AppointmentDateTime', '>', $now->copy()->subMinutes(30))
            ->update(['Status' => 'Ongoing']);

        // 2️⃣ Ongoing / Upcoming → Completed
        Appointment::whereIn('Status', ['Upcoming', 'Ongoing'])
            ->where('AppointmentDateTime', '<=', $now->copy()->subMinutes(30))
            ->update(['Status' => 'Completed']);
    }
}
