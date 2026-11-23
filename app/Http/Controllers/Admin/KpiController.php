<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KpiController extends Controller
{
    public function index()
    {
        return view('admin.kpi');
    }

    public function data()
    {
        // 60 second cache: free host par DB hits kam, load time fast
        $payload = Cache::remember('kpi.data', 60, fn () => $this->buildData());

        return response()->json($payload);
    }

    public function export(): StreamedResponse
    {
        $filename = 'bookings-kpi-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM: Excel mein Urdu/special chars theek dikhein

            // Section 1: summary
            $summary = $this->summary();
            fputcsv($out, ['Metric', 'Value']);
            fputcsv($out, ['Total Bookings', $summary['total_bookings']]);
            fputcsv($out, ['Total Revenue (PKR)', $summary['total_revenue']]);
            fputcsv($out, []);

            // Section 2: monthly breakdown
            fputcsv($out, ['Month', 'Bookings', 'Revenue (PKR)']);
            foreach ($this->monthly() as $row) {
                fputcsv($out, [$row->month, $row->bookings, $row->revenue]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /*private function buildData(): array
    {
        $monthly = $this->monthly();

        return [
            'summary' => $this->summary(),
            'labels'   => $monthly->pluck('month'),
            'bookings' => $monthly->pluck('bookings'),
            'revenue'  => $monthly->pluck('revenue'),
        ];
    }*/

    private function summary(): array
    {
        $row = Booking::where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(amount), 0) as revenue')
            ->first();

        return [
            'total_bookings' => (int) $row->total,
            'total_revenue'  => (float) $row->revenue,
        ];
    }

    /*private function monthly()
    {
        return Booking::where('status', '!=', 'cancelled')
            ->selectRaw("DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as bookings, COALESCE(SUM(amount), 0) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
            */

    private function monthExpr(): string
{
    return match (DB::connection()->getDriverName()) {
        'pgsql'  => "to_char(event_date, 'YYYY-MM')",
        'sqlite' => "strftime('%Y-%m', event_date)",
        default  => "DATE_FORMAT(event_date, '%Y-%m')",
    };
}

private function monthly()
{
    return Booking::where('status', '!=', 'cancelled')
        ->selectRaw($this->monthExpr() . ' as month, COUNT(*) as bookings, COALESCE(SUM(amount), 0) as revenue')
        ->groupBy('month')
        ->orderBy('month')
        ->get();
}

private function buildData(): array
{
    $monthly = $this->monthly();

    return [
        'summary'  => $this->summary(),
        'labels'   => $monthly->pluck('month')->values(),
        'bookings' => $monthly->pluck('bookings')->map(fn ($v) => (int) $v)->values(),
        'revenue'  => $monthly->pluck('revenue')->map(fn ($v) => (float) $v)->values(),
    ];
}
}