<x-admin-layout>
    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-gray-800">KPI Dashboard</h2>
            <a href="{{ route('admin.kpi.export') }}"
               class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm hover:bg-gray-700">
                Download CSV
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Bookings</p>
                <p id="totalBookings" class="text-3xl font-bold text-gray-900">...</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Revenue (PKR)</p>
                <p id="totalRevenue" class="text-3xl font-bold text-gray-900">...</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="font-semibold mb-3">Bookings per Month</h3>
                <canvas id="bookingsChart" height="220"></canvas>
            </div>
            <div class="bg-white p-5 rounded-lg shadow">
                <h3 class="font-semibold mb-3">Revenue Trend</h3>
                <canvas id="revenueChart" height="220"></canvas>
            </div>
            <div class="bg-white p-5 rounded-lg shadow lg:col-span-2">
                <h3 class="font-semibold mb-3">Bookings vs Revenue (Monthly)</h3>
                <canvas id="combinedChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const res = await fetch("{{ route('admin.kpi.data') }}", {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const d = await res.json();

            document.getElementById('totalBookings').textContent = d.summary.total_bookings;
            document.getElementById('totalRevenue').textContent =
                Number(d.summary.total_revenue).toLocaleString('en-PK');

            new Chart(document.getElementById('bookingsChart'), {
                type: 'bar',
                data: { labels: d.labels, datasets: [{ label: 'Bookings', data: d.bookings, backgroundColor: '#6366f1' }] },
                options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });

            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: { labels: d.labels, datasets: [{ label: 'Revenue (PKR)', data: d.revenue, borderColor: '#10b981', tension: 0.3, fill: false }] },
                options: { scales: { y: { beginAtZero: true } } }
            });

            new Chart(document.getElementById('combinedChart'), {
                data: {
                    labels: d.labels,
                    datasets: [
                        { type: 'bar',  label: 'Bookings', data: d.bookings, backgroundColor: '#a5b4fc', yAxisID: 'y' },
                        { type: 'line', label: 'Revenue (PKR)', data: d.revenue, borderColor: '#f59e0b', yAxisID: 'y1' }
                    ]
                },
                options: {
                    scales: {
                        y:  { beginAtZero: true, position: 'left', ticks: { precision: 0 } },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                    }
                }
            });
        } catch (e) {
            console.error('KPI load failed', e);
            document.getElementById('totalBookings').textContent = 'Error';
            document.getElementById('totalRevenue').textContent = 'Error';
        }
    });
    </script>
</x-admin-layout>