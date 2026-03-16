@extends('layouts.admin')

@section('title', 'View All Appointments')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* ===============================
                               Layout Cards
                    ================================ */
        .week-card,
        .section-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .week-card {
            padding: 15px;
        }

        .section-card {
            padding: 25px;
            border: 1px solid #f0f0f0;
            transition: box-shadow 0.2s ease;
        }

        .section-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .section-card h4 {
            color: #2f3e46;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }

        .section-card h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 60px;
            height: 2px;
            background: #2196f3;
        }

        /* ===============================
                               Timetable (Weekly View)
                            ================================ */
        .timetable {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 0.8rem;
        }

        .timetable th {
            background: #2f3e46;
            color: #fff;
            padding: 8px;
            font-size: 0.75rem;
        }

        .timetable td {
            border: 1px solid #dee2e6;
            height: 70px;
            padding: 4px;
            vertical-align: top;
        }

        .time-col {
            background: #f8f9fa;
            font-weight: 600;
            width: 70px;
        }

        /* ===============================
                               Appointment Cards
                            ================================ */
        .appt-card {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 4px;
            border-radius: 6px;
            font-size: 0.7rem;
            margin-bottom: 4px;
        }

        .appt-card.completed {
            background: #e8f5e9;
            border-left-color: #28a745;
        }

        .appt-card.cancelled {
            background: #fdecea;
            border-left-color: #dc3545;
        }

        .appt-card.upcoming {
            background: #fff3cd;
            border-left-color: #ffc107;
        }

        /* ===============================
                               Notices
                            ================================ */
        .notice {
            margin: 15px 0;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 6px;
        }

        /* ===============================
                               Appointment Table
                            ================================ */
        .simple-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            font-size: 0.9rem;
        }

        .simple-table thead {
            background: linear-gradient(135deg, #2f3e46, #354f52);
        }

        .simple-table th {
            color: #fff;
            padding: 16px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .simple-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #444;
        }

        .simple-table tbody tr {
            transition: background 0.2s ease;
        }

        .simple-table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Emphasis Columns */
        .simple-table td:first-child {
            font-weight: 600;
            color: #2f3e46;
        }

        .simple-table td:nth-child(3) {
            color: #2196f3;
        }

        .simple-table td:nth-child(4) {
            font-family: monospace;
            font-size: 0.85rem;
            color: #666;
        }

        /* ===============================
                               Status Badges
                            ================================ */
        .badge-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            min-width: 90px;
            text-align: center;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-completed {
            background: #d4edda;
            color: #155724;
        }

        .badge-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        /* ===============================
                       Status Action Buttons
                    ================================ */
        .status-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .status-btn {
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-btn i {
            font-size: 0.7rem;
        }

        /* Complete */
        .status-btn-complete {
            background: linear-gradient(135deg, #28a745, #5dd28e);
            color: #fff;
        }

        .status-btn-complete:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }

        /* Cancel */
        .status-btn-cancel {
            background: linear-gradient(135deg, #dc3545, #ff6b6b);
            color: #fff;
        }

        .status-btn-cancel:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }


        /* ===============================
                       Success Alert
                    ================================ */
        .alert-success {
            background: linear-gradient(135deg, #d4edda, #b7e4c7);
            color: #155724;
            border-left: 5px solid #28a745;
            border-radius: 10px;
            padding: 14px 18px;
            font-weight: 500;
            box-shadow: 0 6px 18px rgba(40, 167, 69, 0.15);
            animation: slideFade 0.4s ease;
        }

        /* ===============================
        Filters - Search & DatePicker
       =============================== */
        .filters-container form {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .filters-container input[type="text"],
        .filters-container input[type="date"],
        .filters-container select {
            flex: 1 1 220px;
            min-width: 180px;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        .filters-container input[type="text"]:focus,
        .filters-container input[type="date"]:focus,
        .filters-container select:focus {
            outline: none;
            border-color: #2196f3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15);
        }

        /* Enhance datepicker styling */
        .flatpickr-input {
            cursor: pointer;
            background: #fff;
            border-radius: 12px !important;
            padding: 10px 16px !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.9rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .flatpickr-input:focus {
            border-color: #2196f3 !important;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15) !important;
        }

        /* Style the buttons */
        .filters-container button[type="submit"] {
            background: linear-gradient(135deg, #2196f3, #1b6dd5);
            color: #fff;
            font-weight: 600;
            border-radius: 12px;
            padding: 10px 20px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .filters-container button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(33, 150, 243, 0.3);
        }

        .filters-container a {
            border: 1px solid #d1d5db;
            background: #f9fafb;
            color: #374151;
            font-weight: 500;
            border-radius: 12px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .filters-container a:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        /* =========================
                   PAGINATION
                ========================= */
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding-left: 0;
            margin: 30px;
            gap: 5px;
        }

        .pagination li a,
        .pagination li span {
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid var(--color-brand-medium);
            text-decoration: none;
            color: var(--color-brand-dark);
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .pagination li a:hover {
            background-color: var(--color-brand-light);
        }

        .pagination li.active span {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: #fff;
            border-color: transparent;
        }

        .pagination li.disabled span {
            color: #aaa;
            cursor: not-allowed;
            background-color: #f8f8f8;
            border-color: #ddd;
        }

        /* subtle animation */
        @keyframes slideFade {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===============================
                Responsive
            ================================ */
        .table-responsive {
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .section-card {
                padding: 15px;
                margin: 0 -15px;
                border-radius: 0;
            }

            .simple-table {
                font-size: 0.8rem;
            }

            .badge-status {
                min-width: 80px;
                font-size: 0.75rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">

        <h2 class="mb-4">
            <i class="fas fa-calendar-alt"></i> All Appointments
        </h2>

        <div class="week-card">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="week-title">
                    Week: {{ $startOfWeek->format('d M') }} – {{ $endOfWeek->format('d M Y') }}
                </h4>

                <div class="d-flex gap-2">
                    <input type="date" id="datePicker" class="form-control w-auto"
                        value="{{ $selectedDate->format('Y-m-d') }}" style = "width:90%;">

                    <button id="clearBtn" class="btn btn-secondary">
                        Clear
                    </button>
                </div>
            </div>

            @if ($nextWeekAppointments->isEmpty())
                <div class="notice">
                    No appointments scheduled upcoming (Reminder).
                </div>
            @else
                <div class="notice">
                    <i class="fas fa-bell"></i>
                    Reminder: There are <strong>{{ $nextWeekAppointments->count() }}</strong> appointment(s) next week.
                    <br><br>

                    @foreach ($nextWeekAppointments as $appt)
                        <div>
                            • {{ $appt->AppointmentDateTime->format('D, d M Y') }}
                            at {{ $appt->AppointmentDateTime->format('H:i') }} —
                            <strong>{{ optional($appt->pet)->PetName ?? $appt->PetID }}</strong>
                            with
                            <strong>{{ optional($appt->customer)->CustomerName ?? $appt->CustomerName }}</strong>
                            (Method: {{ $appt->Method }})
                            — Status: <strong>{{ $appt->Status }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            <p class="text-muted mt-3" style="margin 10px">
                Total Appointments this week : {{ $appointments->count() }}
            </p>
            <table class="timetable">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Mon <br> {{ $startOfWeek->format('d/m') }}</th>
                        <th>Tue <br> {{ $startOfWeek->copy()->addDay(1)->format('d/m') }}</th>
                        <th>Wed <br> {{ $startOfWeek->copy()->addDay(2)->format('d/m') }}</th>
                        <th>Thu <br> {{ $startOfWeek->copy()->addDay(3)->format('d/m') }}</th>
                        <th>Fri <br> {{ $startOfWeek->copy()->addDay(4)->format('d/m') }}</th>
                        <th>Sat <br> {{ $startOfWeek->copy()->addDay(5)->format('d/m') }}</th>
                        <th>Sun <br> {{ $startOfWeek->copy()->addDay(6)->format('d/m') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @for ($hour = 9; $hour <= 18; $hour++)
                        @foreach ([0, 30] as $minute)
                            @php
                                if ($hour == 18 && $minute == 30) {
                                    continue;
                                }
                            @endphp

                            <tr>
                                <td class="time-col">
                                    {{ sprintf('%02d:%02d', $hour, $minute) }}
                                </td>
                                @foreach (range(1, 7) as $day)
                                    <td>
                                        @foreach ($appointments as $appt)
                                            @if (
                                                $appt->AppointmentDateTime->hour == $hour &&
                                                    $appt->AppointmentDateTime->minute == $minute &&
                                                    $appt->AppointmentDateTime->dayOfWeekIso == $day)
                                                <div class="appt-card {{ strtolower($appt->Status) }}">
                                                    <strong>
                                                        {{ optional($appt->pet)->PetName ?? $appt->PetID }}
                                                    </strong><br>

                                                    {{ optional($appt->customer)->CustomerName ?? $appt->CustomerName }}<br>
                                                    <small>{{ $appt->Method }}</small>
                                                </div>
                                            @endif
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endfor
                </tbody>
            </table>



        </div>

        <hr class="my-4">
        <div class="d-flex align-items-center gap-2 mb-3">

            <div class="d-flex align-items-center gap-2 mb-3 filters-container">

                <form method="GET" class="d-flex gap-2 flex-wrap w-100">

                    <input type="text" name="search" placeholder="Search by Appointment / Customer / Pet"
                        class="form-control" value="{{ request('search') }}">

                    <input type="text" name="date_range" id="dateRange" placeholder="Select date range"
                        class="form-control"
                        value="{{ request('start_date') && request('end_date') ? request('start_date') . ' to ' . request('end_date') : '' }}">

                    <button type="submit">Filter</button>
                    <a href="{{ route('admin.appointments.index') }}">Clear</a>

                </form>

            </div>
        </div>
        <div class="section-card">
            <h4>All Appointment Details</h4>

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif


            <table class="simple-table w-100">
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Customer Name</th>
                        <th>Pet Name</th>
                        <th>Date & Time</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($allAppointments as $appt)
                        <tr style="text-align: center;">
                            <td>{{ $appt->AppointmentID }}</td>
                            <td>{{ optional($appt->customer)->CustomerName ?? $appt->CustomerName }}</td>
                            <td>{{ optional($appt->pet)->PetName ?? $appt->PetID }}</td>
                            <td>
                                <div class="cell-main">
                                    <strong>{{ $appt->AppointmentDateTime->format('d M Y') }}</strong>
                                </div>
                                <div class="cell-sub monospace">
                                    [{{ $appt->AppointmentDateTime->format('H:i') }}]
                                </div>
                            </td>
                            <td>{{ $appt->Method }}</td>

                            @php
                                $statusClass = match ($appt->Status) {
                                    'Completed' => 'badge-completed',
                                    'Cancelled' => 'badge-cancelled',
                                    default => 'badge-pending',
                                };
                            @endphp

                            <td>
                                <span class="badge-status {{ $statusClass }}">
                                    {{ $appt->Status }}
                                </span>
                            </td>

                            <td>
                                <div class="status-actions">
                                    @if ($appt->Status === 'Upcoming')
                                        <form method="POST"
                                            action="{{ route('admin.appointments.updateStatus', $appt->AppointmentID) }}">
                                            @csrf
                                            @method('PUT')
                                            <button name="status" value="Cancelled" class="status-btn status-btn-cancel"
                                                onclick="return confirm('Cancel this appointment?')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                    @endif

                                    @if ($appt->Status === 'Ongoing')
                                        <form method="POST"
                                            action="{{ route('admin.appointments.updateStatus', $appt->AppointmentID) }}">
                                            @csrf
                                            @method('PUT')

                                            <button name="status" value="Completed" class="status-btn status-btn-complete"
                                                onclick="return confirm('Mark as completed?')">
                                                <i class="fas fa-check"></i> Complete
                                            </button>

                                            <button name="status" value="Cancelled" class="status-btn status-btn-cancel"
                                                onclick="return confirm('Cancel this appointment?')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                            <td>
                                <form action="{{ route('admin.appointments.sendReminder', $appt->AppointmentID) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit"
                                        style="
                                                    background:#a95c68;
                                                    color:white; 
                                                    border:none;
                                                    padding:6px 10px;
                                                    border-radius:4px;
                                                    cursor:pointer;
                                                ">
                                        Send Reminder
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No appointments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $allAppointments->appends(request()->query())->links() }}
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.getElementById('datePicker').addEventListener('change', function() {
            window.location.href = `?date=${this.value}`;
        });

        document.getElementById('clearBtn').addEventListener('click', function() {
            window.location.href = `?clear=1`;
        });

        flatpickr("#dateRange", {

            mode: "range",
            dateFormat: "Y-m-d",

            onClose: function(selectedDates, dateStr) {

                if (selectedDates.length === 2) {

                    let start = selectedDates[0].toISOString().split('T')[0];
                    let end = selectedDates[1].toISOString().split('T')[0];

                    window.location.href = `?start_date=${start}&end_date=${end}`;

                }

            }

        });


        document.getElementById('resetFilterBtn').addEventListener('click', function() {

            window.location.href = "{{ route('admin.appointments.index') }}";

        });
    </script>
@endpush
