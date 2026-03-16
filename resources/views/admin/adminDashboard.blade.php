    @extends('layouts.admin')

    @section('title', 'Dashboard')

    @push('styles')
        <style>
            /* Dashboard Layout */
            .dashboard-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 25px;
            }

            .dashboard-header h2 {
                font-size: 2.2rem;
                color: var(--color-brand-dark);
                margin-bottom: 5px;
            }

            .dashboard-header p {
                color: #777;
                margin-bottom: 30px;
            }

            /* Stats Cards */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 20px;
                margin-bottom: 40px;
            }

            .stat-card {
                background: #fff;
                border-radius: 14px;
                padding: 22px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .stat-info h3 {
                font-size: 1.8rem;
                margin: 0;
                color: var(--color-brand-dark);
            }

            .stat-info p {
                margin: 5px 0 0;
                color: #888;
            }

            .stat-icon {
                font-size: 2.2rem;
                padding: 15px;
                border-radius: 12px;
                background: rgba(0, 0, 0, 0.05);
            }

            /* Sections */
            .dashboard-sections {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 25px;
            }

            .section-card {
                background: #fff;
                border-radius: 14px;
                padding: 22px;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            }

            .section-card h4 {
                margin-bottom: 18px;
                font-size: 1.4rem;
                color: var(--color-brand-dark);
            }

            /* Table */
            .simple-table {
                width: 100%;
                border-collapse: collapse;
            }

            .simple-table th,
            .simple-table td {
                padding: 12px;
                border-bottom: 1px solid #eee;
                text-align: left;
            }

            .simple-table th {
                color: #666;
                font-weight: 600;
            }

            .badge-status {
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
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

            /* Quick Links */
            .quick-links a {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 10px;
                background: #f7f7f7;
                color: #333;
                text-decoration: none;
                transition: 0.2s;
            }

            .quick-links a:hover {
                background: var(--color-brand-light);
                color: #fff;
            }
        </style>
    @endpush

    @section('content')
        <div class="dashboard-container">

            {{-- Header --}}
            <div class="dashboard-header">
                <h2>Welcome back, {{ auth()->user()->name ?? 'Admin' }} 👋</h2>
                <p>Here's what's happening in your system today</p>
            </div>

            {{-- Stats --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $totalPets ?? 0 }}</h3>
                        <p>Total Pets</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-dog"></i></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $totalOrders ?? 0 }}</h3>
                        <p>Total Purchases</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $appointmentCount ?? 0 }}</h3>
                        <p>Upcoming Appointments</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3>{{ $totalUsers ?? 0 }}</h3>
                        <p>Registered Users</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>

            {{-- Sections --}}
            <div class="dashboard-sections">

                {{-- Recent Appointments --}}
                <div class="section-card">
                    <h4>Recent Appointments</h4>

                    <table class="simple-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAppointments ?? [] as $appointment)
                                <tr>
                                    <td>{{ $appointment->CustomerName ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->AppointmentDateTime)->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match ($appointment->Status) {
                                                'Completed' => 'badge-completed',
                                                'Cancelled' => 'badge-cancelled',
                                                default => 'badge-pending',
                                            };
                                        @endphp

                                        <span class="badge-status {{ $statusClass }}">
                                            {{ $appointment->Status }}
                                        </span>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No appointments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Quick Actions --}}
                <div class="section-card">
                    <h4>Quick Actions</h4>

                    <div class="quick-links">
                        <a href="{{ route('admin.pets.index') }}">
                            <i class="fas fa-dog"></i> Manage Pets
                        </a>
                        <a href="{{ route('admin.appointments.index') }}">
                            <i class="fas fa-calendar-alt"></i> View Appointments
                        </a>
                        <a href="{{ route('admin.users') }}">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.reports') }}">
                            <i class="fas fa-chart-bar"></i> View Reports
                        </a>
                    </div>
                </div>

            </div>
        </div>
    @endsection