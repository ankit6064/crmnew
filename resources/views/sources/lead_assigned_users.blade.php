<div class="table-responsive m-t-40" id="table_data">
    <table id="sources" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
        <thead class="heading-custom">
            <tr>
                <th width="100" style="text-align:center">Employee Name</th>                             
                <th width="50" style="text-align:center">Select</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($assignedLeadsUsers))
                @foreach ($assignedLeadsUsers as $key => $assignedLeadsUser)
                    <tr>          
                        <td style="white-space: pre-wrap" width="200"> {{ $assignedLeadsUser->user->user_name }}</td>
                        <td>                          
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="selectedUser" id="selectedUser_{{$key}}" value="{{ $assignedLeadsUser->asign_to }}" data-user = "{{ $assignedLeadsUser->asign_to }}">
                                <label class="form-check-label" for="selectedUser_{{$key}}">
                                </label>
                            </div>                   
                        </td>              
                    </tr>            
                @endforeach
            @else
                <tr>          
                    <td colspan="2">No record found</td>                               
                </tr>     
            @endif
       </tbody>
    </table>
</div>