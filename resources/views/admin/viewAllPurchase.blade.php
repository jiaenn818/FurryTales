@extends('layouts.admin')

@section('title', 'All Purchases Management')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
/* =========================
   TABLE STYLING
========================= */
table.table {
    background: #fff;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    width: 100%;
}

table.table th,
table.table td {
    padding: 14px 16px;
    font-size: 14px;
}

table.table thead th {
    text-transform: uppercase;
    font-weight: bold;
    background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
    color: #fff;
}

table.table tbody tr:hover {
    background-color: #fdf6f5;
    box-shadow: inset 4px 0 0 var(--color-brand-medium);
}

/* =========================
   STATUS DROPDOWN
========================= */
table.table select {
    width: 100%;
    padding: 6px 10px;
    font-size: 13px;
    border-radius: 6px;
    border: 1px solid #ccc;
    cursor: pointer;
    transition: all 0.2s ease;
}

.status-completed { background-color: #d4edda; color: #155724; font-weight: bold; }
.status-pending { background-color: #fff3cd; color: #856404; font-weight: bold; }
.status-out-for-delivery { background-color: #cce5ff; color: #004085; font-weight: bold; }

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

.pagination li a:hover { background-color: var(--color-brand-light); }
.pagination li.active span { background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end)); color: #fff; border-color: transparent; }
.pagination li.disabled span { color: #aaa; cursor: not-allowed; background-color: #f8f8f8; border-color: #ddd; }

/* =========================
   FILTERS
========================= */
.filters-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin: 20px;
    padding: 30px;
}

.filters-container input,
.filters-container select {
    padding: 10px 16px;
    border-radius: 30px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: all 0.2s ease;
}

.filters-container input:focus,
.filters-container select:focus {
    outline: none;
    border-color: var(--color-brand-medium);
    box-shadow: 0 0 0 3px rgba(143, 93, 84, 0.15);
}
</style>
@endpush

@section('content')
<main class="admin-content">
    <div class="container">
        <h1><i class="fas fa-shopping-cart"></i> All Purchases Management</h1>

        <form method="GET" action="{{ route('admin.purchases') }}" class="filters-container">
            <input type="text" name="search" placeholder="Search by Purchase ID or Customer ID..." value="{{ request('search') }}">

            <input type="text" name="date_range" placeholder="Select Date Range" value="{{ request('from') && request('to') ? request('from').' to '.request('to') : '' }}" id="dateRange">

            <select name="method">
                <option value="">All Method</option>
                <option value="PickUp" {{ request('method')=='PickUp'?'selected':'' }}>PickUp</option>
                <option value="Delivery" {{ request('method')=='Delivery'?'selected':'' }}>Delivery</option>
            </select>
            <select name="status">
                <option value="">All Status</option>
                <option value="Pending" {{ request('status')=='Pending'?'selected':'' }}>Pending</option>
                <option value="Out for Delivery" {{ request('status')=='Out for Delivery'?'selected':'' }}>Out for Delivery</option>
                <option value="Picked Up,Delivered" {{ request('status')=='Picked Up,Delivered'?'selected':'' }}>Picked Up / Delivered</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.purchases') }}" class="btn btn-secondary">Clear</a>
        </form>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Purchase ID</th>
                    <th>Customer ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Items</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->PurchaseID }}</td>
                    <td>{{ $purchase->customer->name ?? $purchase->CustomerID }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->OrderDate)->format('Y-m-d H:i') }}</td>
                    <td>RM {{ number_format($purchase->TotalAmount, 2) }}</td>
                    <td>
                        [{{ $purchase->Method }}]
                        @if ($purchase->Method=='Delivery' && $purchase->riderID) Rider: {{ $purchase->riderID }} @endif
                        <ul class="mb-0">
                            @foreach($purchase->items as $item)
                                <li>
                                    @if($item->pet)
                                        Pet: {{ $item->pet->PetID }} -- [{{ $item->pet->PetName }}] ({{ $item->Quantity }} x RM {{ number_format($item->Price,2) }})
                                    @elseif($item->accessory)
                                        Accessory: {{ $item->accessory->AccessoryID }} -- [{{ $item->accessory->AccessoryName }}] @if($item->variant) - Variant: {{ $item->variant->VariantKey }} @endif ({{ $item->Quantity }} x RM {{ number_format($item->Price,2) }})
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td>
                        <form action="{{ route('admin.purchase.updateStatus', $purchase->PurchaseID) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()">
                                @php
                                    $allowedStatuses = $purchase->Method=='PickUp'?['Pending','Picked Up']:['Pending','Out for Delivery','Delivered'];
                                @endphp
                                @foreach($allowedStatuses as $status)
                                    <option value="{{ $status }}" {{ $purchase->Status==$status?'selected':'' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No purchases found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $purchases->links() }}
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Initialize date pickers
flatpickr("#dateRange", {
    mode: "range",
    dateFormat: "Y-m-d",
    defaultDate: "{{ request('from') && request('to') ? request('from').' to '.request('to') : '' }}"
});

// Status color highlighting
document.querySelectorAll('table select').forEach(select => {
    function updateStatus() {
        select.classList.remove('status-completed','status-pending','status-out-for-delivery');
        if(select.value=='Delivered'||select.value=='Picked Up') select.classList.add('status-completed');
        else if(select.value=='Pending') select.classList.add('status-pending');
        else if(select.value=='Out for Delivery') select.classList.add('status-out-for-delivery');
    }
    updateStatus();
    select.addEventListener('change', updateStatus);
});
</script>
@endpush