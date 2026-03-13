<aside class="admin-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <h3><i class="fas fa-paw"></i> FurryTales</h3>
        <p>Management System</p>
    </div>

    <!-- Navigation -->
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ ($currentPage ?? '') === 'dashboard' ? 'active' : '' }}">
                <span><i class="fas fa-tachometer-alt"></i> Dashboard</span>
            </a>
        </li>

        <li>
            <a href="#" id="manage-link" class="{{ in_array($currentPage ?? '', ['pets','orders','users']) ? 'active' : '' }}">
                <span><i class="fas fa-th-large"></i> Manage</span>
                <i class="fas fa-chevron-right chevron"></i>
            </a>
            <ul class="manage-submenu {{ in_array($currentPage ?? '', ['pets','orders','users']) ? 'open' : '' }}" id="manage-submenu">
                <li>
                    <a href="{{ route('admin.pets.index') }}" class="{{ ($currentPage ?? '') === 'pets' ? 'active' : '' }}">
                        <i class="fas fa-dog"></i> Pets
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.accessories.index') }}" class="{{ ($currentPage ?? '') === 'pets' ? 'active' : '' }}">
                        <i class="fa-sharp fa-solid fa-bone"></i> Accessories
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.purchases') }}" class="{{ ($currentPage ?? '') === 'orders' ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i> Purchases
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.voucher.index') }}" class="{{ ($currentPage ?? '') === 'voucher' ? 'active' : '' }}">
                        <i class="fas fa-ticket"></i> Vouchers
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('admin.appointments.index') }}" class="{{ ($currentPage ?? '') === 'appointments' ? 'active' : '' }}">
                <span><i class="fas fa-calendar-alt"></i> Appointments</span>
                @if(!empty($appointmentCount) && $appointmentCount > 0)
                    <span class="badge">{{ $appointmentCount }}</span>
                @endif
            </a>
        </li>

        <li>
            <a href="{{ route('admin.rider.assignment') }}" class="{{ ($currentPage ?? '') === 'outlets' ? 'active' : '' }}">
                <span><i class="fas fa-motorcycle"></i> Rider Assignment</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.outlets.index') }}" class="{{ ($currentPage ?? '') === 'outlets' ? 'active' : '' }}">
                <span><i class="fas fa-shop"></i> Outlet</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.suppliers.index') }}" class="{{ ($currentPage ?? '') === 'outlets' ? 'active' : '' }}">
                <span><i class="fas fa-truck"></i> Suppliers</span>
            </a>
        </li>

        <li>
            <a href="{{ route('admin.reports') }}" class="{{ ($currentPage ?? '') === 'reports' ? 'active' : '' }}">
                <span><i class="fas fa-chart-bar"></i> Reports</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}</div>
            <div class="user-details">
                <h4>{{ auth()->user()->name ?? 'Admin User' }}</h4>
                <p>{{ auth()->user()->staff -> Role ?? 'Administrator' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn" type="submit">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const manageLink = document.getElementById('manage-link');
    const manageSubmenu = document.getElementById('manage-submenu');
    const chevron = document.querySelector('#manage-link .chevron');
    if(manageLink){
        manageLink.addEventListener('click', function(e){
            e.preventDefault();
            manageSubmenu.classList.toggle('open');
            chevron.classList.toggle('open');
        });
    }
});
</script>
@endpush