@extends('layouts.admin')

@section('title', 'Report')

@push('styles')
    <style>
        /* Container and overall layout */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            font-family: var(--font-heading);
            color: var(--color-brand-dark);
            margin-bottom: 15px;
            font-size: 2.2rem;
            border-bottom: 2px solid var(--color-brand-light);
            padding-bottom: 10px;
        }

        h3 {
            font-family: var(--font-heading);
            color: var(--color-brand-medium);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        /* Date display */
        .container>div:first-of-type {
            background: linear-gradient(135deg, var(--color-brand-primary-gradient-start), var(--color-brand-primary-gradient-end));
            color: white;
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            margin-bottom: 25px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Filter form styling */
        #filterForm {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--color-brand-light);
        }

        #purchaseDateRange {
            padding: 12px 16px;
            border: 2px solid var(--color-brand-light);
            border-radius: 8px;
            font-family: var(--font-body);
            font-size: 1rem;
            color: var(--color-brand-dark);
            width: 300px;
            transition: all 0.3s;
            background-color: white;
        }

        #purchaseDateRange:focus {
            outline: none;
            border-color: var(--color-brand-medium);
            box-shadow: 0 0 0 3px rgba(143, 93, 84, 0.1);
        }

        .btn-clear {
            background-color: white;
            color: var(--color-brand-medium);
            border: 2px solid var(--color-brand-medium);
            border-radius: 8px;
            padding: 12px 24px;
            margin-left: 15px;
            font-family: var(--font-body);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-clear:hover {
            background-color: var(--color-brand-accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Grid layout enhancements */
        .parent {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: auto auto auto;
            gap: 20px;
            margin-top: 30px;
        }

        .parent>div {
            border-radius: 12px;
            padding: 25px;
            background-color: white;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--color-brand-light);
            transition: all 0.3s ease;
        }

        .parent>div:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Individual grid item positioning */
        .div1 {
            grid-column: 1 / 2;
            grid-row: 1 / 2;
            background: linear-gradient(135deg, var(--color-brand-light), white);
        }

        .div2 {
            grid-column: 2 / 3;
            grid-row: 1 / 2;
            background: linear-gradient(135deg, var(--color-brand-accent), white);
        }

        .div3 {
            grid-column: 3 / 4;
            grid-row: 1 / 4;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .div4 {
            grid-column: 1 / 3;
            grid-row: 2 / 4;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .div5 {
            grid-column: 1 / 4;
            grid-row: 4 / 5;
            margin-top: 10px;
        }

        /* Chart containers */
        .div3 canvas,
        .div4 canvas,
        .div5 canvas {
            max-height: 300px;
            width: 100% !important;
        }

        /* Stats cards styling */
        .div1 h2,
        .div2 h2 {
            font-size: 2.5rem;
            color: var(--color-brand-dark);
            margin: 15px 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .div1 small,
        .div2 small {
            display: block;
            font-size: 0.9rem;
            margin-top: 15px;
            padding: 8px 12px;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 6px;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .parent {
                grid-template-columns: repeat(2, 1fr);
            }

            .div3 {
                grid-column: 1 / 3;
                grid-row: 3 / 4;
            }

            .div4 {
                grid-column: 1 / 3;
                grid-row: 4 / 5;
            }

            .div5 {
                grid-column: 1 / 3;
                grid-row: 5 / 6;
            }
        }

        @media (max-width: 768px) {
            .parent {
                grid-template-columns: 1fr;
            }

            .div1,
            .div2,
            .div3,
            .div4,
            .div5 {
                grid-column: 1 / 2;
            }

            .div1 {
                grid-row: 1 / 2;
            }

            .div2 {
                grid-row: 2 / 3;
            }

            .div3 {
                grid-row: 3 / 4;
            }

            .div4 {
                grid-row: 4 / 5;
            }

            .div5 {
                grid-row: 5 / 6;
            }

            #purchaseDateRange {
                width: 100%;
                margin-bottom: 15px;
            }

            .btn-clear {
                width: 100%;
                margin-left: 0;
            }

            #filterForm {
                padding: 15px;
            }
        }

        /* Animation for stats */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .parent>div {
            animation: fadeInUp 0.5s ease forwards;
        }

        .div1 {
            animation-delay: 0.1s;
        }

        .div2 {
            animation-delay: 0.2s;
        }

        .div3 {
            animation-delay: 0.3s;
        }

        .div4 {
            animation-delay: 0.4s;
        }

        .div5 {
            animation-delay: 0.5s;
        }

        .flatpickr-calendar {
            border-radius: 14px;
            border: 1px solid var(--color-brand-light);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            font-family: var(--font-body);
            padding: 12px;
        }

        .flatpickr-current-month {
            font-family: var(--font-heading);
            font-weight: 600;
            color: var(--color-brand-dark);
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            border-radius: 50%;
            padding: 6px;
            transition: background 0.3s ease;
        }

        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background-color: var(--color-brand-accent);
        }

        .flatpickr-monthSelect-months {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            padding: 10px;
        }

        .flatpickr-innerContainer {
            justify-content: center;
        }

        .flatpickr-monthSelect-month {
            border-radius: 10px;
            padding: 14px 0;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--color-brand-dark);
            background-color: #f9f9f9;
            border: 1px solid var(--color-brand-light);
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
            padding: 4px;
        }

        .flatpickr-monthSelect-month:hover {
            background: linear-gradient(135deg,
                    var(--color-brand-primary-gradient-start),
                    var(--color-brand-primary-gradient-end));
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
        }

        .flatpickr-monthSelect-month.selected {
            background: linear-gradient(135deg,
                    var(--color-brand-medium),
                    var(--color-brand-dark));
            color: white;
            border: none;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2);
        }

        .flatpickr-monthSelect-month.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        #purchaseDateRange.flatpickr-input.active {
            border-color: var(--color-brand-medium);
            box-shadow: 0 0 0 4px rgba(143, 93, 84, 0.15);
        }

        @media (max-width: 768px) {
            .flatpickr-calendar {
                width: 100% !important;
            }

            .flatpickr-monthSelect-months {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        #purchaseDateRange {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%23905c54' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M8 7V3M16 7V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 20px;
        }
    </style>
@endpush
@section('content')
    <div class="container">
        <h2>Report</h2>
        <div>
            Today is {{ $currentDateTime->format('d M Y, h:i A') }}
        </div>

            <form method="GET" action="{{ route('admin.reports') }}" id="filterForm">
                <div class="filter-container">
                <input type="text" id="purchaseDateRange" placeholder="Select month..." autocomplete="off">
                <input type="hidden" name="start_date" id="startDate" value="{{ $startDate ?? '' }}">
                <input type="hidden" name="end_date" id="endDate" value="{{ $endDate ?? '' }}">
                <button type="button" id="clearBtn" class="btn-clear">
                    Clear Filter
                </button>
            </div>
        </form>

        <div class="parent">
            <div class="div1">
                <h3>Latest Month Sales</h3>
                <h2>RM {{ number_format($latestMonthSales['amount'], 2) }}</h2>

                <small style="color: {{ $latestMonthSales['isIncrease'] ? 'green' : 'red' }};">
                    Compared to last month:
                    {{ $latestMonthSales['difference'] >= 0 ? '+' : '-' }}
                    RM {{ number_format(abs($latestMonthSales['difference']), 2) }}
                </small>
            </div>

            <div class="div2">
                <h3>Latest Month Orders</h3>
                <h2>{{ $latestMonthSales['orders'] }} Orders</h2>

                <small style="color: {{ $latestMonthSales['order_isIncrease'] ? 'green' : 'red' }};">
                    Compared to last month:
                    {{ $latestMonthSales['order_difference'] >= 0 ? '+' : '-' }}
                    {{ abs($latestMonthSales['order_difference']) }}
                </small>
            </div>

            <div class="div3">
                @if($statusReport->isEmpty())
                    <h3>Order Status Distribution</h3>
                    <small>No record yet</small>
                @else
                    <h3>Order Status Distribution</h3>
                    <canvas id="statusChart"></canvas>
                @endif
                    <br />
                @if($topOutlets->isEmpty())
                    <h3>Outlet Performances</h3>
                    <small>No record yet</small>
                @else
                    <h3>Outlet Performances</h3>
                    <canvas id="outletChart"></canvas>
                @endif
            </div>

            <div class="div4">
                <h3>Top 5 Best Selling Breeds</h3>
                <canvas id="breedChart"></canvas>
                <br />
                <h3>Top 5 Best Selling Accessories</h3>
                <canvas id="accessoryChart"></canvas>
            </div>

            <div class="div5">
                <h3>Monthly Revenue Report</h3>
                <canvas id="salesChart"></canvas>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dateRangeInput = document.getElementById('purchaseDateRange');
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            const clearBtn = document.getElementById('clearBtn');
            const form = document.getElementById('filterForm');

            // chart variables
            let salesChart, statusChart, breedChart, accessoryChart, outletChart;

            const fp = flatpickr(dateRangeInput, {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "Y-m",
                        altFormat: "F Y",
                        theme: "light"
                    })
                ],
                defaultDate: "{{ isset($startDate) ? \Carbon\Carbon::parse($startDate)->format('Y-m') : '' }}",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 1) {
                        const year = selectedDates[0].getFullYear();
                        const month = selectedDates[0].getMonth(); // 0-based index

                        const monthNames = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG",
                            "SEP", "OCT", "NOV", "DEC"
                        ];
                        const monthText = monthNames[month];

                        startDateInput.value = `${year}-${String(month + 1).padStart(2, '0')}-01`;
                        endDateInput.value =
                            `${year}-${String(month + 1).padStart(2, '0')}-${new Date(year, month + 1, 0).getDate()}`;

                        // ⭐ Set input value as "2026 JAN"
                        instance.input.value = `${year} ${monthText}`;

                        form.submit();
                    }
                }
            });

            clearBtn.addEventListener('click', function() {
                dateRangeInput.value = '';
                startDateInput.value = '';
                endDateInput.value = '';

                // Destroy charts
                if (salesChart) salesChart.destroy();
                if (statusChart) statusChart.destroy();
                if (breedChart) breedChart.destroy();
                if (accessoryChart) accessoryChart.destroy();
                if (outletChart) outletChart.destroy();

                // Reload without query params
                window.location.href = "{{ route('admin.reports') }}";
            });

            // SALES CHART
            const salesLabels = @json($monthlySales->pluck('month')->values());
            const salesData = @json($monthlySales->pluck('total_revenue')->values());

            salesChart = new Chart(document.getElementById('salesChart'), {
                type: 'line',
                data: {
                    labels: salesLabels,
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: salesData,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // STATUS CHART
            const statusLabels = @json($statusReport->pluck('Status'));
            const statusData = @json($statusReport->pluck('total_orders'));

            statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Order Count',
                        data: statusData
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // BREED CHART
            const breedLabels = @json($topBreeds->pluck('Breed'));
            const breedData = @json($topBreeds->pluck('total_sold'));

            breedChart = new Chart(document.getElementById('breedChart'), {
                type: 'bar',
                data: {
                    labels: breedLabels,
                    datasets: [{
                        label: 'Total Sold',
                        data: breedData,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // ACCESSORY CHART
            const accessoryLabels = @json($topAccessories->pluck('AccessoryName'));
            const accessoryData = @json($topAccessories->pluck('total_sold'));

            accessoryChart = new Chart(document.getElementById('accessoryChart'), {
                type: 'bar',
                data: {
                    labels: accessoryLabels,
                    datasets: [{
                        label: 'Total Sold',
                        data: accessoryData,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // OUTLET CHART
            const outletStateLabels = @json($topOutlets->pluck('State'));
            const outletSalesData = @json($topOutlets->pluck('total_sales'));
            const outletOrderCounts = @json($topOutlets->pluck('order_count'));

            outletChart = new Chart(document.getElementById('outletChart'), {
                type: 'doughnut',
                data: {
                    labels: outletStateLabels,
                    datasets: [{
                        label: 'Outlet Performances',
                        data: outletSalesData
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    const state = context.label;
                                    const sales = context.raw;
                                    const orders = outletOrderCounts[index];

                                    return [
                                        `State: ${state}`,
                                        `Sales: RM ${Number(sales).toFixed(2)}`,
                                        `Orders: ${orders}`
                                    ];
                                }
                            }
                        }
                    }
                }
            });

        });
    </script>
@endpush