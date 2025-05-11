<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>Table</title>


</head>

<body>
    <?php
    // $data = App\Models\Lead::where('status', 3)->with('lhsreport')->first();
    ?>

    <section class="Prospect_sec">
        <div class="container">
            <div class="logo" style="text-align: center; padding: 20px 0; border-top: 10px solid #d3e215;">
                <img src="{{ url('/admin/assests/images/logo.png') }}">
            </div>
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px;">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Prospect Information</h5>
            </div>
            <table style="width:100%; border: 1px solid #000 !important; border-collapse: collapse;">
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;  border-left: 2px solid black;">
                        Contact's Name:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{ $data->prospect_first_name.' '.$data->prospect_last_name }}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Board Number:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black">
                        {{ $data['lhsreport']->board_no }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;  border-left: 2px solid black;">
                        Contact's Designation:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;     background-color: #c9d8fe;">
                        {{ $data->designation }}
                    </td>
                    <th style="width:15%; background-color: #ebebeb; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        Direct Number:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{$data['lhsreport']->direct_no}}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black;">
                        Company:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;  ">
                        {{ $data->company_name }}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Ext (if any):</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black; ">
                        {{$data['lhsreport']->ext_if_any}}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Industry:</th>
                    <td style="width:35%; font-size: 12px; border: 1p   x solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        {{ $data->company_industry }}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Cell Number:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{ $data->contact_number_1 }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Employees:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{$data['lhsreport']->employees_strength}}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Email:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black">
                        {{ $data->prospect_email }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Revenue:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        {{$data['lhsreport']->revenue}}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        EA Name:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{$data['lhsreport']->ea_name}}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Address:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{$data['lhsreport']->address}}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        EA Phone Number:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black">
                        {{$data['lhsreport']->ea_phone_no}}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        LinkedIn Profile:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        {{ $data->linkedin_address }}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        EA Email:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{$data['lhsreport']->ea_email}}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Prospect Level:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{$data['lhsreport']->prospects_level}}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Website:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #fff; border-right: 2px solid black">
                        {{$data['lhsreport']->website}}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Prospect Vertical:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        {{$data['lhsreport']->prospect_vertical}}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Opt-in Status:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{$data['lhsreport']->opt_in_status}}
                    </td>
                </tr>

            </table>
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px;width:100% ">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Company Description</h5>
            </div>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td style="width:100%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black; border-right: 2px solid black">
                        {!!  $data['lhsreport']->company_desc !!}
                    </td>
                </tr>
            </table>
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px;">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Lead Comments</h5>
            </div>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Responsibilities:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black">
                        {!! $data['lhsreport']->responsibilities !!}
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Team Size:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;     background-color: #c9d8fe; border-right: 2px solid black;">
                        {{$data['lhsreport']->team_size}}
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Pain Areas:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black;">
                        {!! $data['lhsreport']->pain_areas !!}
                       
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Interest/New Initiatives:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;     background-color: #c9d8fe; border-right: 2px solid black;">
                        {!! $data['lhsreport']->interest_new_initiatives !!}
                        
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Budget:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;border-right: 2px solid black;">

                         {!! $data['lhsreport']->budget !!}

                    
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;border-left: 2px solid black">
                        Defined Agenda:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;background-color: #c9d8fe; border-right: 2px solid black;">

                        {!! $data['lhsreport']->defined_agenda !!}
                     
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Call Notes:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;border-right: 2px solid black;">
                        {!! $data['lhsreport']->call_notes !!}
                       
                    </td>
                </tr>
            </table>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; font-weight: bold; border-left: 2px solid black">
                        Does the prospect wish to have a Face to Face meeting or teleconference?</td>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black;">
                        {{$data['lhsreport']->meeting_teleconference}}
                    </td>
                </tr>
                <tr>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;font-weight: bold; border-left: 2px solid black">
                        Is the contact the decision maker? If No, then who is?</td>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black;">
                        {{$data['lhsreport']->contact_decision_maker}}
                    </td>
                </tr>
                <tr>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; font-weight: bold; border-left: 2px solid black">
                        Who else would be the influencers in the decision making process?</td>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black;">
                        {{$data['lhsreport']->influencers_decision_making_process}}
                    </td>
                </tr>
                <tr>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; font-weight: bold; border-left: 2px solid black">
                        Is the Company already affiliated with any other similar services? If Yes, Name?</td>
                    <td style="width:60%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black;">
                        {{$data['lhsreport']->company_already_affiliated}}
                    </td>
                </tr>
            </table>
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px;">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Meeting Information</h5>
            </div>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Date 1:</th>
                    <td style="width:30%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{$data['lhsreport']->meeting_date1}}
                    </td>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Time 1:(24 Hours format)</th>
                    <td style="width:30%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black;">
                        {{$data['lhsreport']->meeting_time1}} {{$data['lhsreport']->timezone_1}}
                    </td>
                </tr>
                <tr>
                    <th style="width:20%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;border-left: 2px solid black">
                        Date 2:</th>
                    <td style="width:30%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;     background-color: #c9d8fe;">
                        {{$data['lhsreport']->meeting_date2}}
                    </td>
                    <th style="width:20%; background-color: #ebebeb; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        Time 2:(24 Hours format)</th>
                    <td style="width:30%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black;">
                        {{$data['lhsreport']->meeting_time2}} {{$data['lhsreport']->timezone_2}}
                    </td>
                </tr>
            </table>
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px;">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">LHS</h5>
            </div>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <th style="width:20%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black">
                        Created At:</th>
                    <td style="width:80%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black">
                        {{\Carbon\Carbon::parse($data['lhsreport']->created_at)->format('d-m-Y h:i A') }}
                    </td>
                </tr>
            </table>
        </div>
    </section>

</body>

</html>
