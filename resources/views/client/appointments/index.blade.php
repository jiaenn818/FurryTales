@extends('layouts.app')

@section('content')

@push('styles')
<link href="{{ asset('css/appointments.css') }}" rel="stylesheet">
@endpush

<div class="appointments-index-page">
    <div class="container">
        <div class="appointments-index-container">
            <div class="appointments-header-wrapper">
                <h1 class="appointments-index-title">My Appointments</h1>
                <a href="{{ route('client.appointments.create') }}" class="appointments-new-btn">
                    <span>+</span> Make new Appointment
                </a>
            </div>

            @if(session('success'))
            <div class="appointments-message appointments-message-success" style="position: relative; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="appointments-message appointments-message-error" style="position: relative; margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
            @endif

            <!-- Filter Section -->
            <div class="appointments-filter-panel">
                <div class="appointments-filter-row">
                    <div class="appointments-filter-field">
                        <label class="appointments-filter-label">Filter by Status</label>
                        <select id="statusFilter" class="appointments-filter-select">
                            <option value="all">All Statuses</option>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="appointments-filter-field">
                        <label class="appointments-filter-label">Filter by Date</label>
                        <select id="dateFilter" class="appointments-filter-select">
                            <option value="all">All Dates</option>
                            <option value="upcoming">Upcoming Appointments</option>
                            <option value="past">Past Appointments</option>
                            <option value="today">Today</option>
                            <option value="this-week">This Week</option>
                            <option value="this-month">This Month</option>
                        </select>
                    </div>
                    <button onclick="resetFilters()" class="appointments-reset-button">
                        Reset Filters
                    </button>
                </div>
            </div>

            @if($appointments->isEmpty())
            <div class="appointments-empty">
                <div class="appointments-empty-icon">📅</div>
                <h2>No Appointments Yet</h2>
                <p>You haven't made any appointments. Browse our pets and schedule a visit!</p>
                <a href="{{ route('client.pets.index') }}" class="appointments-empty-button">
                    Browse Pets
                </a>
            </div>
            @else
            <div id="noAppointmentsMatched" class="appointments-empty" style="display: none; padding: 4rem 2rem;">
                <div class="appointments-empty-icon">🔍</div>
                <h2>No matching appointments found</h2>
                <p>No records match your selected filters. Try adjusting your status or date criteria.</p>
                <button onclick="resetFilters()" class="appointments-empty-button">
                    Clear All Filters
                </button>
            </div>

            <div class="appointments-list">
                @foreach($appointments as $appointment)
                <div class="appointment-card"
                     data-status="{{ $appointment->Status }}"
                     data-date="{{ $appointment->AppointmentDateTime->format('Y-m-d') }}"
                     data-timestamp="{{ $appointment->AppointmentDateTime->timestamp }}">
                    <div class="appointment-card-content">
                        <!-- Pet Image -->
                        <div class="appointment-pet-image">
                            <img src="{{ asset($appointment->pet->ImageURL1) }}" alt="{{ $appointment->pet->PetName }}">
                        </div>

                        <!-- Appointment Details -->
                        <div class="appointment-details">
                            <div class="appointment-header">
                                <div>
                                    <h3>{{ $appointment->pet->PetName }}</h3>
                                    <p>{{ $appointment->pet->Breed }}</p>
                                </div>
                                <span class="appointment-status 
                                    @if($appointment->Status === 'Upcoming') appointment-status-upcoming
                                    @elseif($appointment->Status === 'Ongoing') appointment-status-ongoing
                                    @elseif($appointment->Status === 'Completed') appointment-status-completed
                                    @else appointment-status-cancelled
                                    @endif">
                                    {{ $appointment->Status }}
                                </span>
                            </div>

                            <div class="appointment-info-grid">
                                <div class="appointment-info-item">
                                    <span class="icon">📅</span>
                                    <div>
                                        <p class="label">Date & Time</p>
                                        <p class="value">{{ $appointment->AppointmentDateTime->format('M d, Y • g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="appointment-info-item">
                                    <span class="icon">{{ $appointment->Method === 'In-Person' ? '🏢' : '📹' }}</span>
                                    <div>
                                        <p class="label">Method</p>
                                        <p class="value">{{ $appointment->Method }}</p>
                                    </div>
                                </div>

                                <div class="appointment-info-item">
                                    <span class="icon">👤</span>
                                    <div>
                                        <p class="label">Contact Name</p>
                                        <p class="value">{{ $appointment->CustomerName }}</p>
                                    </div>
                                </div>

                                <div class="appointment-info-item">
                                    <span class="icon">📞</span>
                                    <div>
                                        <p class="label">Phone</p>
                                        <p class="value">{{ $appointment->CustomerPhone }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="appointment-actions">
                                <a href="{{ route('client.pets.show', $appointment->pet->PetID) }}" 
                                   class="appointment-action-button appointment-action-button-view">
                                    View Pet
                                </a>

                                @if($appointment->Status === 'Upcoming')
                                <form action="{{ route('client.appointments.cancel', $appointment->AppointmentID) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                    @csrf
                                    <button type="submit" class="appointment-action-button appointment-action-button-cancel">
                                        Cancel Appointment
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="appointments-pagination" style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Filter appointments by status and date
function filterAppointments() {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const appointments = document.querySelectorAll('.appointment-card');
    
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const weekFromNow = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
    const monthFromNow = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
    
    let visibleCount = 0;
    appointments.forEach(card => {
        const cardStatus = card.dataset.status;
        const cardTimestamp = parseInt(card.dataset.timestamp) * 1000;
        const cardDate = new Date(cardTimestamp);
        
        let showStatus = statusFilter === 'all' || cardStatus === statusFilter;
        let showDate = true;
        
        if (dateFilter === 'upcoming') {
            showDate = cardDate >= today;
        } else if (dateFilter === 'past') {
            showDate = cardDate < today;
        } else if (dateFilter === 'today') {
            showDate = cardDate.toDateString() === today.toDateString();
        } else if (dateFilter === 'this-week') {
            showDate = cardDate >= today && cardDate < weekFromNow;
        } else if (dateFilter === 'this-month') {
            showDate = cardDate >= today && cardDate < monthFromNow;
        }
        
        if (showStatus && showDate) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const noMatchesMsg = document.getElementById('noAppointmentsMatched');
    const appointmentsList = document.querySelector('.appointments-list');
    
    if (visibleCount === 0) {
        noMatchesMsg.style.display = 'block';
        appointmentsList.style.display = 'none';
    } else {
        noMatchesMsg.style.display = 'none';
        appointmentsList.style.display = '';
    }
}

function resetFilters() {
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('dateFilter').value = 'all';
    filterAppointments();
}

// Add event listeners
document.getElementById('statusFilter').addEventListener('change', filterAppointments);
document.getElementById('dateFilter').addEventListener('change', filterAppointments);
</script>

@endsection
