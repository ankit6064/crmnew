@extends('layouts.admin')

@section('content')

@php
    $urls = '?employee_id=' . request()->get('employee_id') . '&campaign_id=' . request()->get('campaign_id') . '&date_from=' . request()->get('date_from') . '&date_to=' . request()->get('date_to') . '&filter_by=' . request()->get('filter_by') . '&reminder_for_conversation=' . request()->get('reminder_for_conversation');
@endphp

<style>
    /* MAIN FILTER STYLING */
    .filter-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 180px;
    }
    
    .filter-label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #495057;
    }
    
    .filter-select,
    .filter-date {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .filter-select:focus,
    .filter-date:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .filter-actions {
        display: flex;
        gap: 10px;
        margin-top: 5px;
    }
    
    .filter-btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
        flex: 1;
    }
    
    .filter-blue { 
        background: #007bff; 
        color: white; 
    }
    

    
    .filter-red { 
        background: #dc3545; 
        color: white; 
    }
    

    
    /* TABLE STYLING */
    .table-container {
        overflow-x: auto;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    
    .custom-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }
    
    .custom-table th {
        background-color: #f8f9fa;
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }
    
    .custom-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: top;
    }
    
  
    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .filter-group {
            min-width: 160px;
    }
    }
    
    @media (max-width: 992px) {
        .filter-group {
            flex: 0 0 calc(50% - 15px);
        }
        
        .filter-actions {
            flex: 0 0 100%;
            margin-top: 10px;
    }
    }
    
    @media (max-width: 768px) {
        .filter-group {
            flex: 0 0 100%;
        }
        
        .filter-actions {
            flex-direction: column;
        }
    }
    
    /* Export button styling */
    .add-submanager {
        margin-bottom: 20px;
        text-align: right;
    }
    
    .add-submanager a {
        display: inline-block;
        padding: 10px 20px;
        background: #28a745;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        transition: background 0.2s;
    }
    
  

    .employee-filterss .filter-select, .filter-date {
    min-width: 306px;
    }
</style>

<div class="main-right">
    <div class="right-side submanager">
        <h2>Daily Reports</h2>

        {{-- Filters --}}
        <div class="employee-filterss daily-report-filter">
            <div class="filter-container">
            <form action="{{ route('employee.man_daily_report') }}" method="get" id="searchform">
                @csrf
                
                    <div class="filter-row">
                    {{-- Employee Filter --}}
                    @if(Auth::user()->is_admin != 1)
                    <div class="filter-group">
                        <label class="filter-label" for="employee_id">Employee</label>
                        <select class="filter-select" name="employee_id" id="employee_id">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp['id'] }}" {{ request('employee_id') == $emp['id'] ? 'selected' : '' }}>
                                    {{ $emp['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Campaign Filter --}}
                    <div class="filter-group">
                        <label class="filter-label" for="campaign_id">Campaign</label>
                        <select class="filter-select" name="campaign_id" id="campaign_id">
                            <option value="">Select Campaign</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign['id'] }}" {{ request('campaign_id') == $campaign['id'] ? 'selected' : '' }}>
                                    {{ $campaign['source_name']}} - {{  $campaign['description'] }}
                                </option>
                            @endforeach                    
                        </select>
                    </div>

                    {{-- Filter By --}}
                    <div class="filter-group">
                        <label class="filter-label" for="filter_by">Type</label>
                        <select class="filter-select" name="filter_by" id="filter_by">
                            <option value="">Select Type</option>
                            <option value="1" {{ request('filter_by') == 1 ? 'selected' : '' }}>VM/No Response</option>
                            <option value="2" {{ request('filter_by') == 2 ? 'selected' : '' }}>Conversation</option>
                        </select>
                    </div>

                    {{-- Conversation Type --}}
                    <div class="filter-group" id="conversation_div" style="display:none;">
                        <label class="filter-label" for="reminder_for_conversation">Conversation Type</label>
                        <select class="filter-select" id="reminder_for_conversation" name="reminder_for_conversation">
                            <option value="">Choose Conversation Type</option>
                            @foreach($conversationTypes as $type)
                                <option value="{{ $type['type'] }}"
                                    {{ request('reminder_for_conversation') == $type['type'] ? 'selected' : '' }}>
                                    {{ $type['type'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        </div>

                        <div class="filter-row">


                    {{-- Date From --}}
                    <div class="filter-group">
                        <label class="filter-label" for="date_from_new">Date From</label>
                        <input type="datetime-local" class="filter-date" name="date_from" id="date_from_new"
                            value="{{ request('date_from', $date_from) }}">
                    </div>

                    {{-- Date To --}}
                    <div class="filter-group">
                        <label class="filter-label" for="date_to_new">Date To</label>
                        <input type="datetime-local" class="filter-date" name="date_to" id="date_to_new"
                            value="{{ request('date_to', $date_to) }}">
                    </div>

                    {{-- Filter Actions --}}
                    <div class="filter-actions">
                            <button type="button" id="sub_cmap" class="filter-btn filter-blue">Filter</button>
                            <button type="reset" class="filter-btn filter-red"
                            onclick="window.location.href='{{ route('employee.man_daily_report') }}'">
                            Reset
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="graph campaignslist logstable myself-logs daily-report">
            <div class="row">
                <div class="add-submanager">
                    <a href="#" onclick="exportreport();">Export Report</a>
                </div>
            </div>


            <!-- Datatable -->
            <div class="table">
                <div class="table-container">
                    <table id="employee-table">
                        <thead class="thead-main">
                            <tr>
                                <th>Lead Name</th>
                                <th>Conversation Type</th>
                                <th>Note</th>
                                <th>Note & Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination Placeholder (DataTables will inject) --}}
            <div id="pagination" class="pagination"></div>
            </div>
        </div>
    </div>
</div>


{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {

    // ===================== CONVERSATION FILTER =====================
    $('#filter_by').on('change', function() {
        if ($(this).val() == "2") {
            $('#conversation_div').show();
        } else {
            $('#conversation_div').hide();
            $('#reminder_for_conversation').val('');
        }
    });

    // Trigger on load
    $('#filter_by').trigger('change');

    // ===================== DATATABLE =====================
    let table = $('#employee-table').DataTable({
        processing: false,
        serverSide: true,
        searching: false,
        ordering: true,
        ajax: {
            url: "{{ route('getLeadsData') }}",
            type: "GET",
            data: function (d) {
                d.employee_id = $('#employee_id').val();
                d.campaign_id = $('#campaign_id').val();
                d.date_from = $('#date_from_new').val();
                d.date_to = $('#date_to_new').val();
                d.filter_by = $('#filter_by').val();
                d.reminder_for_conversation = $('#reminder_for_conversation').val();
            },
            beforeSend: function() {
                // Show loader
                $('#spinner-overlay').show();

                // Disable filter button
                $('#sub_cmap').prop('disabled', true);
            },
            error: function() {
                // Hide loader on error
                $('#spinner-overlay').hide();
                $('#sub_cmap').prop('disabled', false);
            }
        },

        columns: [
            { data: 'lead_name', name: 'lead_name', orderable: true },
            { data: 'conversation_type', name: 'conversation_type', orderable: false },
            { data: 'note', name: 'note', orderable: false },
            { data: 'note_date_time', name: 'note_date_time', orderable: true },
            { data: 'status', name: 'status', orderable: false }
        ],

        order: [[3, 'desc']],

        language: {
            emptyTable: "No records found",
            zeroRecords: "No matching records found",
            processing: "Loading..."
        }
    });

    // ===================== AFTER DATA LOAD =====================
    table.on('xhr.dt', function() {
        // Small delay for smooth UI
        setTimeout(function() {
            $('#spinner-overlay').hide();
            $('#sub_cmap').prop('disabled', false);
        }, 200);
    });

    // ===================== FILTER BUTTON =====================
    $('#sub_cmap').click(function() {
        table.ajax.reload(null, false);
    });

    // ===================== OPTIONAL: AUTO FILTER ON ENTER =====================
    $('#searchform input, #searchform select').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            table.ajax.reload(null, false);
        }
    });

});


// ===================== EXPORT FUNCTION =====================
function exportreport() {
    $('#searchform').attr('action', "{{ route('employee.man_daily_report') }}");
    $('#searchform').submit();
}
</script>

@endsection