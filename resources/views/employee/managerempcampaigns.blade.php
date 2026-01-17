<table class="table table-bordered">
    <thead>
        <tr>
            <th>Campaign Name</th>
            <th>Sub Campaign Name</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($campaigns) && $campaigns->isNotEmpty())
            @foreach($campaigns as $campaigns)
                <tr>
                    <td>{{ $campaigns->source_name }}</td>
                    <td>{{ $campaigns->description }}</td>
                </tr>
            @endforeach 
        @else
            <tr>
                <td colspan="2" style="text-align:center; color:#999;">No campaigns available.</td>
            </tr>
        @endif
    </tbody>
</table>
