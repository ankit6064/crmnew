<table class="table table-bordered">
    <thead>
        <tr>
            <th>Employee First Name</th>
            <th>Employee Last Name</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($employees) && $employees->isNotEmpty())
            @foreach($employees as $emp)
                <tr>
                    <td>{{ $emp->first_name }}</td>
                    <td>{{ $emp->last_name }}</td>
                </tr>
            @endforeach 
        @else
            <tr>
                <td colspan="2" style="text-align:center; color:#999;">No employees available.</td>
            </tr>
        @endif
    </tbody>
</table>
