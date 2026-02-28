<form action="" method="post" id="submanagerform">
    <input type="hidden" name="old_employee_id" value="{{ $request->employeeid }}">
    <p class="message-box">This employee have {{ count($sources) }} assigned campaigns please assign these campaings to any other employee to proceed next.</p>
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Source Name</th>
            <th>Transfer Campaign To</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($sources) && count($sources) > 0)
            @foreach($sources as $source)
            <input type="hidden" name="campaignid[]" value="{{ $source->id }}">
                <tr>
                    <td>{{ $source->source_name }}</td>
                    <td>
                        <select name="transferleadtoemployee[]" 
                                class="form-control custom-select" 
                                data-placeholder="Select Employee"
                                required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->first_name . ' ' . $emp->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="2" style="text-align:center; color:#999;">No sources available.</td>
            </tr>
        @endif
    </tbody>
</table>
</form>