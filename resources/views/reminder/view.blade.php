@extends('layouts.admin')
@section('content')
    <style>
        .responseconversrespo {
            float: left;
            width: 50%;
            margin-bottom: 10px;
        }

        i.fa {
            color: black;
        }
    </style>
    <?php date_default_timezone_set('Asia/Kolkata'); ?>

    <div class="main-right">
        <div class="right-side submanager">
            <h2>Reminder Listing</h2>

            <div class="graph campaignslist logstable lead-listing Reminder-listing">
                <div class="table">
                    <div class="table-container">
                        <table class="table table-striped table-hover" id="employee-table">
                            <thead class="thead-main">
                                <tr>
                                    <!--<th>Date</th>-->
                                    <th>Lead Name</th>
                                    <th>Job Title</th>
                                    <th>Note</th>
                                    <th>Reminder Date</th>
                                    <th>Reminder For</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                                
                            </thead>
                            <tbody>

@if(isset($data) && count($data) > 0)

    @foreach($data as $record)
    <tr>
        <!--<td>{{ $record['created_at'] }}</td>-->
        <td>{{ $record['lead']['prospect_first_name'].' '.$record['lead']['prospect_last_name'] }}</td>
        <td>{{ $record['lead']['lead_name'] }}</td>
        <td class="feedback_td">{{ $record['feedback'] }}</td>
        <td>{{ date('d M, Y', strtotime($record['reminder_date'])) }}</td>
        <td class="feedback_td">{{ $record['reminder_for'] }}</td>
    </tr>
    @endforeach

@else

<tr>
    <td colspan="5" style="text-align:center;font-weight:600;">
        No Data Available
    </td>
</tr>

@endif

</tbody>                         
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>











@endsection