@if(isset($logs) && $logs->isNotEmpty())
    @foreach($logs as $logdetails)
        <tr>
            <td>{{ $logdetails->employeename ?? 'N/A' }}</td>
            <td>{{ $logdetails->campaign_name ?? 'N/A' }}</td>
            <td>{{ ucfirst($logdetails->lead_name ?? 'N/A') }}</td>
            <td>{{ $logdetails->description ?? 'N/A' }}</td>
            <td>{{ $logdetails->type ?? 'N/A' }}</td>
            <td>{{ $logdetails->created_at ? $logdetails->created_at->format('d-m-Y H:i') : 'N/A' }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center">No logs found.</td>
    </tr>
@endif
