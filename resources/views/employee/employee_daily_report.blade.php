@extends('layouts.admin')

@section('content')

@php
    $urls = '?employee_id=' . Auth::id() . '&campaign_id=' . request()->get('campaign_id') . '&date_from=' . request()->get('date_from') . '&date_to=' . request()->get('date_to') . '&filter_by=' . request()->get('filter_by') . '&reminder_for_conversation=' . request()->get('reminder_for_conversation');
@endphp

<style>
    /* MAIN FILTER STYLING */
    .filter-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-bottom: 25px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        align-items: end;
    }
    
    @media (max-width: 1200px) {
        .filter-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 900px) {
        .filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 600px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .filter-label {
        font-weight: 600;
        font-size: 13px;
        color: #475569;
        margin-bottom: 0px;
        font-family: 'Poppins', sans-serif;
    }
    
    .filter-select,
    .filter-date {
        width: 100% !important;
        min-width: unset !important;
        height: 42px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0 14px;
        font-size: 13px;
        color: #1e293b;
        background-color: #f8fafc;
        box-sizing: border-box;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }
    
    .filter-select:focus,
    .filter-date:focus {
        border-color: #4b3fb3;
        background-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(75, 63, 179, 0.15);
    }
    
    .filter-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        align-items: center;
        height: 42px;
    }
    
    .btn-filter {
        background-color: #4b3fb3;
        color: white;
        border: none;
        padding: 0 24px;
        border-radius: 8px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        height: 42px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(75, 63, 179, 0.2);
    }
    
    .btn-filter:hover {
        background-color: #3c3293;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(75, 63, 179, 0.3);
    }
    
    .btn-reset {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 0 20px;
        border-radius: 8px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        height: 42px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-reset:hover {
        background-color: #e2e8f0;
        color: #0f172a;
        border-color: #94a3b8;
    }
    
    /* TABLE STYLING */
    .table-container {
        width: 100% !important;
        overflow-x: auto !important;
    }
    
    /* Export button styling */
    .add-submanager {
        margin-bottom: 20px;
        text-align: right;
    }
    
    .btn-export {
        background-color: #1e3a8a;
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 8px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        height: 42px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        box-sizing: border-box;
        text-decoration: none !important;
    }

    .btn-export:hover {
        background-color: #172554;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.2);
        color: white !important;
    }
</style>

<div class="main-right">
    <div class="right-side submanager">
        <h2>Daily Reports</h2>

        {{-- Session Messages --}}
        @if (Session::has('success'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('success') }}
            </div>
        @elseif (Session::has('error'))
            <div class="alert alert-danger" role="alert">
                {{ Session::get('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="filter-card">
            <form action="{{ url('/employee/' . Auth::id() . '/daily_report') }}" method="get" id="searchform">
                @csrf
                
                <div class="filter-grid">
                    {{-- Campaign Filter --}}
                    <div class="filter-group">
                        <label class="filter-label" for="campaign_id">Campaign</label>
                        <select class="filter-select" name="campaign_id" id="campaign_id">
                            <option value="">Select Campaign</option>
                            @foreach($campaigns as $camp)
                                @if (isset($camp['source']))
                                    <option value="{{ $camp['source']['id'] }}" {{ request('campaign_id') == $camp['source']['id'] ? 'selected' : '' }}>
                                        {{ $camp['source']['source_name'] }} - {{ $camp['source']['description'] }}
                                    </option>
                                @endif
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
                        <button type="button" id="sub_cmap" class="btn-filter">Filter</button>
                        <button type="button" class="btn-reset"
                            onclick="window.location.href='{{ route('employee.empdailyreport') }}'">
                            Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="graph campaignslist logstable myself-logs daily-report">
            <div class="row" style="margin-bottom: 20px; text-align: right;">
                <div class="add-submanager" style="margin-bottom: 0;">
                    <a href="#" onclick="exportreport();" class="btn-export">
                        <i class="fa fa-file-excel"></i> Export Report
                    </a>
                </div>
            </div>

            <!-- Datatable -->
            <div class="table">
                <div class="table-container">
                    <table id="leadsTable">
                        <thead class="thead-main">
                            <tr>
                                <th>Sr. no</th>
                                <th>Lead Name</th>
                                <th>Conversation Type</th>
                                <th>Note</th>
                                <th>Note Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
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
    let table = $('#leadsTable').DataTable({
        processing: false,
        serverSide: true,
        searching: false,
        ordering: true,
        ajax: {
            url: "{{ url('empdailyreport') }}",
            type: "GET",
            data: function (d) {
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
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'prospect_first_name', name: 'prospect_first_name', orderable: true },
            { data: 'conversation_type', name: 'conversation_type', orderable: false },
            { data: 'note', name: 'note', orderable: false },
            { data: 'updated_at', name: 'updated_at', orderable: true },
            { data: 'status', name: 'status', orderable: false }
        ],

        order: [[4, 'desc']],

        columnDefs: [
            { targets: 0, className: 'text-center' }
        ],

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

    // ===================== AUTO FILTER ON ENTER =====================
    $('#searchform input, #searchform select').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            table.ajax.reload(null, false);
        }
    });

});

// ===================== EXPORT FUNCTION =====================
function exportreport() {
    $('#searchform').submit();
}
</script>

@endsection