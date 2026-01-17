@extends('layouts.admin')
@section('content')


<div class="main-right">
	<div class="right-side submanager">
		<h2>Employee Logs</h2>

		<!-- Filter Section -->
		<div class="employee-filterss">
			<div class="filter-container">
				<select id="employee_id" class="filter-select">
					<option value="">Select Employee</option>
					@if(isset($employee) && !empty($employee))
						@foreach($employee as $employeelisting)
							<option value="{{ $employeelisting->id }}">
								{{ $employeelisting->first_name . ' ' . $employeelisting->last_name }}
							</option>
						@endforeach
					@endif
				</select>

				<select id="source_id" class="filter-select">
					<option value="">Select Campaign</option>
					@if(isset($sources) && !empty($sources))
						@foreach($sources as $sourcelisting)
							<option value="{{ $sourcelisting->id }}">
								{{ $sourcelisting->source_name . '-' . $sourcelisting->description }}
							</option>
						@endforeach
					@endif
				</select>

				<select id="type" class="filter-select">
					<option value="">Select Type</option>
					<option value="1">Note Added</option>
					<option value="2">Lead Status Updated</option>
					<option value="3">LHS Report Created</option>
					<option value="6">LHS Report Updated</option>
					<option value="4">MOM Report Generated</option>
					<option value="5">New Lead Added</option>
				</select>

				<input type="text" id="daterange" class="filter-date" placeholder="Select Date Range">
				<button type="button" id="filterLogs" class="filter-btn filter-blue">Filter</button>
				<button type="button" id="reset" class="filter-btn filter-red">Reset</button>
			</div>
		</div>

		<!-- Messages -->
		@if (session('success'))
			<div class="message-box success">{{ session('success') }}</div>
		@endif
		@if (session('error'))
			<div class="message-box error">{{ session('error') }}</div>
		@endif

		<!-- Logs Table -->
		<div class="graph campaignslist logstable">
							<div class="table">
							<div class="table-container">
						  <table class="custom-table" id="employee-table">
                <thead class="thead-main">
						<tr>
							<th>Employee Name</th>
							<th>Campaign Name</th>
							<th>Lead Name</th>
							<th>Description</th>
							<th>Type</th>
							<th>Created On</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
            </div>

			<!-- Pagination handled by DataTables -->
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(document).ready(function () {
	const url = "{{ route('filteremployeelogs') }}";

	var table = $('#employee-table').DataTable({
		processing: false,
		serverSide: true,
		searching: false,
		ordering: true,
		ajax: {
			url: url,
			data: function (d) {
				d.employeeid = $('#employee_id').val();
				d.sourceid = $('#source_id').val();
				d.date = $('#daterange').val();
				d.type = $('#type').val();
			}
		},
		columns: [
			{ data: 'employeename', name: 'employeename', orderable: true },
			{ data: 'campaign_name', name: 'campaign_name', orderable: true },
			{ data: 'lead_name', name: 'lead_name', orderable: false },
			{ data: 'description', name: 'description', orderable: false },
			{ data: 'type', name: 'type', orderable: false },
			{ data: 'created_at', name: 'created_at', orderable: true },
		],
		order: [[5, 'desc']]
	});

	$('#filterLogs').on('click', function () {
		table.ajax.reload();
	});

	$('#reset').on('click', function () {
		$('#employee_id, #source_id, #type, #daterange').val('');
		table.ajax.reload();
	});
});
</script>

<script>
$(function () {
	$('#daterange').daterangepicker({
		autoUpdateInput: false,
		opens: 'left',
		locale: {
			format: 'YYYY-MM-DD',
			cancelLabel: 'Clear'
		}
	});

	$('#daterange').on('apply.daterangepicker', function(ev, picker) {
		$(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
	});

	$('#daterange').on('cancel.daterangepicker', function() {
		$(this).val('');
	});
});
</script>
@endpush
