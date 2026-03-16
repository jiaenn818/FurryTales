<?php
use Carbon\Carbon;
?>

@extends('layouts.admin')

@section('title', 'Rider Assignment')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    <style>
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-brand-light);
        }

        .admin-header h1 {
            font-family: var(--font-heading);
            color: var(--color-brand-dark);
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

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
            background: linear-gradient(135deg,
                    var(--color-brand-primary-gradient-start),
                    var(--color-brand-primary-gradient-end));
            color: white;
        }

        table.table tbody tr:hover {
            background-color: #fdf6f5;
            box-shadow: inset 4px 0 0 var(--color-brand-medium);
        }

        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-active {
            background-color: #5a2c2c;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <main class="admin-main">
        <div class="container">

            <div class="admin-header">
                <h1>
                    <i class="fas fa-motorcycle"></i>
                    Rider Assignment
                </h1>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Rider ID</th>
                        <th>Postcode</th>
                        <th>Order Count</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($riders as $rider)
                        <tr>
                            <td>
                                {{ $rider->riderID }}<br />
                                <small>User ID: {{ $rider->userID }}</small>
                            </td>
                            <td>{{ $rider->postCode }}</td>
                            <td>
                                {{ $rider->order_count }}
                                @if ($rider->order_count >= 0)
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#riderModal{{ $rider->riderID }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="riderModal{{ $rider->riderID }}" tabindex="-1"
                                        aria-labelledby="riderModalLabel{{ $rider->riderID }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="riderModalLabel{{ $rider->riderID }}">
                                                        Purchases Assigned to {{ $rider->riderID }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <strong>Rider's Postcode: {{ $rider->postCode }}</strong> <br>
                                                    @if ($rider->purchases->count())
                                                        <table class="table table-sm table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Purchase ID</th>
                                                                    <th>Customer ID</th>
                                                                    <th>Order Date</th>
                                                                    <th>Total Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($rider->purchases as $purchase)
                                                                    <tr>
                                                                        <td>{{ $purchase->PurchaseID }}</td>
                                                                        <td>{{ $purchase->customer->name ?? $purchase->CustomerID }}
                                                                        </td>
                                                                        <td>{{ \Carbon\Carbon::parse($purchase->OrderDate)->format('Y-m-d H:i') }}
                                                                        </td>
                                                                        <td>RM
                                                                            {{ number_format($purchase->TotalAmount, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @else
                                                        <p>No purchases assigned.</p>
                                                    @endif
                                                    <!-- Add Purchase Form -->
                                                    <div class="mt-3">
                                                        <form
                                                            action="{{ route('admin.rider.assignPurchase', $rider->riderID) }}"
                                                            method="POST">
                                                            @csrf
                                                            <p class="text-success mb-2">
                                                                <i class="fas fa-info-circle"></i>
                                                                Suggested deliveries are highlighted in green based on
                                                                postcode similarity.
                                                            </p>

                                                            <div class="input-group">
                                                                <select name="purchaseID" class="form-select" required>
                                                                    <option value="">-- Select Purchase --</option>
                                                                    @foreach ($rider->unassignedPurchases as $purchase)
                                                                        @php
                                                                            // Count leading digit matches
                                                                            $riderPostcode = (string) $rider->postCode;
                                                                            $purchasePostcode =
                                                                                (string) ($purchase->Postcode ?? '');
                                                                            $matchCount = 0;
                                                                            for (
                                                                                $i = 0;
                                                                                $i <
                                                                                min(
                                                                                    strlen($riderPostcode),
                                                                                    strlen($purchasePostcode),
                                                                                );
                                                                                $i++
                                                                            ) {
                                                                                if (
                                                                                    $riderPostcode[$i] ===
                                                                                    $purchasePostcode[$i]
                                                                                ) {
                                                                                    $matchCount++;
                                                                                } else {
                                                                                    break;
                                                                                }
                                                                            }

                                                                            // Determine green intensity (max 5 digits)
                                                                            $greenIntensity = min($matchCount, 5) * 40;
                                                                            $color = "rgb(0,{$greenIntensity},0)";

                                                                            // Calculate how many days ago
                                                                            $daysAgo = (int) Carbon::parse(
                                                                                $purchase->OrderDate,
                                                                            )->diffInDays(Carbon::now());
                                                                            $daysAgoText =
                                                                                $daysAgo === 0
                                                                                    ? 'Today'
                                                                                    : "{$daysAgo} day" .
                                                                                        ($daysAgo > 1 ? 's' : '') .
                                                                                        ' ago';
                                                                        @endphp
                                                                        <option value="{{ $purchase->PurchaseID }}"
                                                                            style="color:{{ $color }}; font-weight: {{ $matchCount > 0 ? 'bold' : 'normal' }}">
                                                                            {{ $purchase->PurchaseID }} |
                                                                            Customer:
                                                                            {{ $purchase->customer->name ?? $purchase->CustomerID }}
                                                                            |
                                                                            RM
                                                                            {{ number_format($purchase->TotalAmount, 2) }}
                                                                            |
                                                                            Postcode: {{ $purchase->Postcode ?? 'N/A' }} |
                                                                            {{ $daysAgoText }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <button class="btn btn-success" type="submit">Add</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No riders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <h4 class="text-warning mb-3" style="margin-top: 80px;">
                Reminder: A rider can only have up to 5 active deliveries. Please check before assigning!
            </h4>

        </div>
    </main>
@endsection
