<?php

namespace App\Http\Controllers\admin;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        //check user type
        if (!Auth::check() || !Auth::user()->isManager()) {
            abort(403, 'Unauthorized User');
        }

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // If no filter is applied, do NOT set default month.
        // This will show ALL data.
        $baseQuery = $this->baseQuery($startDate, $endDate);

        return view('admin.report.report', [
            'totalOrders'      => $this->totalOrders($baseQuery),
            'totalRevenue'     => $this->totalRevenue($baseQuery),
            'monthlySales'     => $this->monthlySales(),
            'statusReport'     => $this->statusReport($baseQuery),
            'latestMonthSales' => $this->latestMonthSales($startDate, $endDate),
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'currentDateTime'  => now(),
            'topBreeds'        => $this->top5BestSellingBreed($baseQuery),
            'topAccessories'   => $this->top5BestSellingAccessories($baseQuery),
            'topOutlets'       => $this->top5Outlets($baseQuery),
        ]);
    }

    private function baseQuery($startDate, $endDate)
    {
        $query = Purchase::query();

        if ($startDate && $endDate) {
            $query->whereBetween('OrderDate', [$startDate, $endDate]);
        }

        return $query;
    }

    private function totalOrders($query)
    {
        return (clone $query)->count();
    }

    private function totalRevenue($query)
    {
        return (clone $query)->sum('TotalAmount');
    }

    private function monthlySales()
    {
        return Purchase::query()
            ->select(
                DB::raw("DATE_FORMAT(OrderDate, '%Y-%m') as month"),
                DB::raw("COUNT(*) as total_orders"),
                DB::raw("SUM(TotalAmount) as total_revenue")
            )
            ->groupBy(DB::raw("DATE_FORMAT(OrderDate, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();
    }

    private function statusReport($query)
    {
        return (clone $query)
            ->select(
                'Status',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(TotalAmount) as total_amount')
            )
            ->groupBy('Status')
            ->get();
    }

    private function latestMonthSales($startDate, $endDate)
    {
        $query = Purchase::query();

        // Only apply filter if both start and end dates exist
        if ($startDate && $endDate) {
            $query->whereBetween('OrderDate', [$startDate, $endDate]);
        }

        // Get latest month from filtered range OR from all data
        $latestMonth = $query
            ->selectRaw("YEAR(OrderDate) AS year, MONTH(OrderDate) AS month")
            ->groupByRaw("YEAR(OrderDate), MONTH(OrderDate)")
            ->orderByRaw("YEAR(OrderDate) DESC, MONTH(OrderDate) DESC")
            ->first();

        if (!$latestMonth) {
            return [
                'month' => null,
                'amount' => 0,
                'difference' => 0,
                'isIncrease' => false,
                'orders' => 0,
                'order_difference' => 0,
                'order_isIncrease' => false,
            ];
        }

        $latestYear = $latestMonth->year;
        $latestMonthNum = $latestMonth->month;

        $prevDate = Carbon::create($latestYear, $latestMonthNum, 1)->subMonth();
        $prevYear = $prevDate->year;
        $prevMonth = $prevDate->month;

        // Latest month stats
        $latestStats = Purchase::query()
            ->whereYear('OrderDate', $latestYear)
            ->whereMonth('OrderDate', $latestMonthNum)
            ->selectRaw("SUM(TotalAmount) AS total_sales, COUNT(*) AS total_orders")
            ->first();

        // Previous month stats
        $prevStats = Purchase::query()
            ->whereYear('OrderDate', $prevYear)
            ->whereMonth('OrderDate', $prevMonth)
            ->selectRaw("SUM(TotalAmount) AS total_sales, COUNT(*) AS total_orders")
            ->first();

        $latestSales = $latestStats->total_sales ?? 0;
        $prevSales = $prevStats->total_sales ?? 0;

        $latestOrders = $latestStats->total_orders ?? 0;
        $prevOrders = $prevStats->total_orders ?? 0;

        return [
            'month' => $latestYear . '-' . str_pad($latestMonthNum, 2, '0', STR_PAD_LEFT),
            'amount' => $latestSales,
            'difference' => $latestSales - $prevSales,
            'isIncrease' => $latestSales >= $prevSales,
            'orders' => $latestOrders,
            'order_difference' => $latestOrders - $prevOrders,
            'order_isIncrease' => $latestOrders >= $prevOrders,
        ];
    }
    private function top5BestSellingBreed($query)
    {
        return (clone $query)
            ->join('purchase_items', 'purchases.PurchaseID', '=', 'purchase_items.PurchaseID')
            ->join('pet', 'purchase_items.ItemID', '=', 'pet.PetID')
            ->select('pet.Breed', DB::raw('SUM(purchase_items.Quantity) as total_sold'))
            ->groupBy('pet.Breed')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }

    private function top5BestSellingAccessories($query)
    {
        return (clone $query)
            ->join('purchase_items', 'purchases.PurchaseID', '=', 'purchase_items.PurchaseID')
            ->join('accessories', 'purchase_items.AccessoryID', '=', 'accessories.AccessoryID')
            ->select('accessories.AccessoryName', DB::raw('SUM(purchase_items.Quantity) as total_sold'))
            ->whereNotNull('purchase_items.AccessoryID')
            ->groupBy('accessories.AccessoryName')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }

    private function top5Outlets($query)
    {
        return (clone $query)
            ->join('purchase_items', 'purchases.PurchaseID', '=', 'purchase_items.PurchaseID')
            ->join('outlet', 'purchase_items.OutletID', '=', 'outlet.OutletID')
            ->select(
                'outlet.State',
                DB::raw('SUM(purchase_items.Price * purchase_items.Quantity) as total_sales'),
                DB::raw('COUNT(DISTINCT purchases.PurchaseID) as order_count')
            )
            ->groupBy('outlet.State')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
    }
}
