@extends('layouts.admin')

@push('styles')
<!-- External CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">

@endpush

@section('content')
<div class="main-right addsubmanager Update leadchart">
    <div class="right-side add-sub">
        <div class="graph">
            <h2 class="section-heading">Lead Chart</h2>

            <!-- Filter Form -->
            <form id="filterForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="categoryFilter">Employee</label>
                        <select id="categoryFilter" name="employee_id" class="form-control">
                            <option value="">Select Employee</option>
                            @if(isset($employeelist))
                                @foreach($employeelist as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name . ' ' . $employee->last_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="dateFilter">Dates</label>
                        <input type="text" id="dateFilter" name="date_range" class="form-control" placeholder="Select dates">
                    </div>
                </div>

                <div class="header-btn-container employeetomanager lead">
                    <button type="button" id="searchBtn" class="back-btn">Search</button>
                    <button type="button" id="clearBtn" class="back-btn clear">Clear Filter</button>
                </div>
            </form>

            <!-- Campaign Cards -->
            <div class="cards">
                <div class="campaign-card">
                    <div class="campaign-header">
                        <h2>Campaign Detail</h2>
                    </div>
                    <div class="campaign-body">
                        <div class="chart-container">
                            <canvas id="leadsChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="campaign-card dates">
                    <div class="campaign-header">
                        <h2>Campaign Dates</h2>
                    </div>
                    <div class="campaign-body" id="campaignCounts">
                        <center><span>No data available</span></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Flatpickr setup
    const datePicker = flatpickr("#dateFilter", {
        mode: "multiple",
        dateFormat: "Y-m-d",
        defaultDate: []
    });

    // Chart.js setup
    const ctx = document.getElementById('leadsChart').getContext('2d');
    const leadsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Count',
                data: [],
                backgroundColor: 'rgba(0,123,255,0.6)',
                borderColor: '#007bff',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // Update campaign counts
    function updateCampaignCounts(data) {
        const container = document.getElementById('campaignCounts');
        container.innerHTML = '';
        if (!data || data.length === 0) {
            container.innerHTML = '<center><span>No data available</span></center>';
            return;
        }
        data.forEach(item => {
            const section = `
                <div class="date-section">
                    <h5>${item.date}</h5>
                    ${
                        item.campaigns.length
                        ? item.campaigns.map(c => `
                            <div class="campaign-card">
                                <h6>${c.name}</h6>
                                <p>${c.description}</p>
                                <p>Count: <span class="count">${c.count}</span></p>
                            </div>`).join('')
                        : '<p>No data available</p>'
                    }
                </div>
            `;
            container.innerHTML += section;
        });
    }

    // Search button logic
    document.getElementById('searchBtn').addEventListener('click', function () {
        $('#spinner-overlay').show();

        const emp = document.getElementById('categoryFilter').value;
        const dates = document.getElementById('dateFilter').value.split(', ');

        if (!emp || !dates.length) {
            alert('Please select employee and date(s).');
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('filtercampaign') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                "employee_id": emp,
                "date": dates
            },
            dataType: "json",
            success: function (response) {
                const chartData = response.datachart || [];
                leadsChart.data.labels = chartData.map(i => i.source?.source_name || 'Unknown');
                leadsChart.data.datasets[0].data = chartData.map(i => i.leadscount || 0);
                leadsChart.update();

                const campaigns = (response.data || []).map((arr, idx) => ({
                    date: dates[idx],
                    campaigns: arr.map(item => ({
                        name: item.source?.source_name || 'Unknown',
                        description: item.source?.description || '',
                        count: item.leadscount || 0
                    }))
                }));
                $('#spinner-overlay').hide();

                updateCampaignCounts(campaigns);
            },
            error: function () {
                updateCampaignCounts([]);
                leadsChart.data.labels = [];
                leadsChart.data.datasets[0].data = [];
                leadsChart.update();
            }
        });
    });

    // Clear filters
    document.getElementById('clearBtn').addEventListener('click', function () {
        document.getElementById('categoryFilter').value = '';
        datePicker.clear();
        updateCampaignCounts([]);
        leadsChart.data.labels = [];
        leadsChart.data.datasets[0].data = [];
        leadsChart.update();
    });
});
</script>
@endpush
