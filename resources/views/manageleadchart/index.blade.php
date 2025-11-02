@extends('layouts.admin')

@push('styles')
    <!-- Additional CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <style>
        .align-right { text-align: right; }
        .chart-container { height: 400px; }

        /* Filter Section Styling */
        .filter-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .filter-card:hover {
            transform: translateY(-5px);
        }
        .form-inline .form-group label {
            font-weight: 600;
            color: #333;
        }
        .form-inline .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s;
        }
        .form-inline .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3); 
        }
        .btn-primary, .btn-secondary {
            border-radius: 8px;
            padding: 8px 20px;
            border: none;
            transition: background 0.3s, transform 0.2s;
        }
        .btn-primary {
            background: #007bff;
        }
        .btn-primary:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Card Styling for Chart and Campaign Counts */
        .card-outline-info {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card-outline-info:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(90deg, #007bff 0%, #00c4ff 100%);
            /* border-top-left-radius: 15px !important;
            border-top-right-radius: 15px !important; */
        }
        .card-body {
            background: #fff;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        /* Campaign Counts Styling */
        .date-section { margin-bottom: 25px; }
        .date-section h4 {
            margin-bottom: 15px;
            color: #333;
            font-weight: 700;
            font-size: 1.3rem;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }
        .campaign-card {
            margin-bottom: 5px;
            padding: 5px;
            background: #f8f9fa;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.3s;
        }
        .campaign-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .campaign-card h5 {
            margin-bottom: 5px;
            font-size: 1.15rem;
            color: #1a1a1a;
        }
        .campaign-card p {
            margin: 0;
            color: #666;
        }
        .campaign-card .count {
            font-weight: 600;
            color: #007bff;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">
        <!-- Filters Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card filter-card">
                    <div class="card-body">
                        <form id="filterForm" class="form-inline justify-content-between">
                            <div class="form-group mr-3">
                                <label for="categoryFilter" class="mr-2">Employee:</label>
                                <select id="categoryFilter" class="form-control">
                                    <option value="">Select Employee</option>
                                    @if(isset($employeelist) && !empty($employeelist))
                                    @foreach ($employeelist as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->first_name.' '.$employee->last_name }}</option>
                                    @endforeach
                                    @endif
                                    
                                </select>
                            </div>
                            
                            <div class="form-group mr-3">
                                <label for="dateFilter" class="mr-2">Dates:</label>
                                <input type="text" id="dateFilter" class="form-control" placeholder="Select dates">
                            </div>
                            <div>
                                <button type="button" id="searchBtn" class="btn btn-primary mr-2">Search</button>
                                <button type="button" id="clearBtn" class="btn btn-secondary">Clear Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart and Campaign Counts Sections -->
        <div class="row" >
            <!-- Chart Section -->
            <div class="col-md-6">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h6 class="m-b-0 text-white">Campaign Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="leadsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Counts Section -->
            <div class="col-md-6">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h6 class="m-b-0 text-white">Campaign Dates</h6>
                    </div>
                    <div class="card-body">
                        <div id="campaignCounts">
                            <!-- Campaign counts will be populated dynamically -->
                           <center>
                            <span>Not Data Available</span>
                            </center>
                                
                            </div>
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
        // Initial chart and campaign counts data
        const initialChartLabels = [];
        const initialChartData = [];
        const initialCampaignData = [];

        // Initialize Flatpickr for multiple date selection
        const datePicker = flatpickr("#dateFilter", {
            mode: "multiple",
            dateFormat: "Y-m-d",
            defaultDate: [],
            theme: "material_blue"
        });

        // Initialize Chart.js with enhanced styling
        const ctx = document.getElementById('leadsChart').getContext('2d');
        const leadsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: initialChartLabels,
                datasets: [{
                    label: 'Count',
                    data: initialChartData,
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e9ecef' },
                        ticks: { color: '#333', font: { size: 12 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#333', font: { size: 12 } }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: '#333', font: { size: 14 } }
                    }
                }
            }
        });

        // Function to update campaign counts
        function updateCampaignCounts(data) {
            const campaignCounts = document.getElementById('campaignCounts');
            campaignCounts.innerHTML = ''; // Clear existing counts
            data.forEach(item => {
                const dateSection = `
                    <div class="date-section">
                        <h5>${item.date}</h5>
                        ${item.campaigns.length > 0 ? 
    item.campaigns.map(campaign => `
        <div class="campaign-card">
            <h6>${campaign.name} - ${campaign.description}</h6>
            <p>Count: <span class="count">${campaign.count}</span></p>
        </div>
    `).join('') : 
    '<p>No data available</p>'
}
                    </div>`;
                campaignCounts.innerHTML += dateSection;
            });
        }

        // Search button event listener
        document.getElementById('searchBtn').addEventListener('click', function() {
            const category = document.getElementById('categoryFilter').value;
            const dates = document.getElementById('dateFilter').value.split(', ');
            if(category == ''){
             $('#categoryFilter').css('border','1px solid red');
            }else{
                $('#categoryFilter').css('border','');

            }
            if(dates == ''){
                $('#dateFilter').attr('style', 'border: 1px solid red !important');

            }else{
                $('#dateFilter').css('border','');
            }
            if(category == '' || dates == ''){
                return false;
            } 
            $('#spinner-overlay').show();

            $.ajax({
    type: 'POST',
    url: "{{ route('filtercampaign') }}",
    data: {
        "_token": "{{ csrf_token() }}",
        "employee_id": category,
        "date": dates
    },
    dataType: "json",
    success: function(response) {
        $('#spinner-overlay').hide();

        // Ensure response.data is an array of arrays
        const data = Array.isArray(response.data) ? response.data : [];

        const datachart = Array.isArray(response.datachart) ? response.datachart : [];
        // const datachart = Array.isArray(response.data) ? response.data : [];
        // Map response to chart and campaign data
        // Use the first date's data for chart (assuming chart displays one date's data)
        const chartData = datachart.length > 0 && Array.isArray(data[0]) ? datachart : [];
        const chartLabels = chartData.length > 0 ? chartData.map(item => item.source?.source_name + ' - ' + item.source?.description || 'Unknown Source') : dates.map(() => 'No Data');
        const chartValues = chartData.length > 0 ? chartData.map(item => item.leadscount || 0) : dates.map(() => 0);

        // Create campaign data for each date
        const campaignData = dates.map((date, index) => ({
            date: date,
            campaigns: Array.isArray(data[index]) && data[index].length > 0 
                ? data[index].map(item => ({
                    name: item.source?.source_name || 'Unknown Source',
                    count: item.leadscount || 0,
                    description: item.source?.description || 'No description available'

                }))
                : []
        }));

        // Update chart
        leadsChart.data.labels = chartLabels;
        leadsChart.data.datasets[0].data = chartValues;
        leadsChart.update();

        // Update campaign counts
        updateCampaignCounts(campaignData);
    },
    error: function(error) {
        console.error('Error fetching data:', error);
        // Reset chart and campaign counts
        leadsChart.data.labels = dates;
        leadsChart.data.datasets[0].data = dates.map(() => 0);
        leadsChart.update();
        updateCampaignCounts(dates.map(date => ({ date, campaigns: [] })));
    }
});
            // Placeholder for filtering logic
            // console.log('Filtering by:', { category, dates });

            // // Update chart data
            // leadsChart.data.labels = dates;
            // leadsChart.data.datasets[0].data = dates.map((_, i) => 10 * (i + 1));
            // leadsChart.update();

            // // Update campaign counts
            // const filteredCampaignData = dates.map((date, i) => ({
            //     date: date,
            //     campaigns: [
            //         { name: `Campaign ${String.fromCharCode(65 + i * 2)}`, count: 15 + i * 5 },
            //     ]
            // }));
            // updateCampaignCounts(filteredCampaignData);
        });

        // Clear filter button event listener
        document.getElementById('clearBtn').addEventListener('click', function() {
            // Reset category filter
            document.getElementById('categoryFilter').value = '';

            // Clear date filter
            datePicker.clear();

            // Reset chart to initial state
            leadsChart.data.labels = initialChartLabels;
            leadsChart.data.datasets[0].data = initialChartData;
            leadsChart.update();

            // Reset campaign counts to initial state
            updateCampaignCounts(initialCampaignData);
        });
    </script>
@endpush