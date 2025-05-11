<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <title>Mom Report</title>
</head>
<body>
    <section class="Prospect_sec">
        <div class="container">
            <!-- <div class="logo" style="text-align: center; padding: 20px 0; border-top: 10px solid #d3e215;">
                <img src="{{ asset('public/admin/assets/images/logo.png') }}">
            </div> -->
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px;">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Details:</h5>
            </div>

            <table style="width:100%; border: 1px solid #000 !important; border-collapse: collapse;">
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;  border-left: 2px solid black;background-color: #c9d8fe;">
                        Meeting Date & Time:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;background-color: #c9d8fe;">
                        {{ $data->meeting_datetime }} {{ $data->time_zone }}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;  border-left: 2px solid black;">
                        Account:</th>
                    <td style="width:35%; background-color: #c9d8fe; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{ $data->account }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        BDM:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black">
                        {{ $data->managerName }}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;  border-left: 2px solid black;">
                        Setup By:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{ $data->employeeName }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe;">
                        Company:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{ $data->company_name }}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black;background-color: #c9d8fe;">
                        Participants:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;background-color: #c9d8fe;  ">
                        {{ $data->exl_participants }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        Customer Participants and Designations:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black; ">
                        {{$data->customer_participants_and_designations}}
                    </td>
                    <th style="width:15%; background-color: #fff; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black;">
                        Owners:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;">
                        {{ $data->owners_id }}
                    </td>
                </tr>
                <tr>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;background-color: #c9d8fe;">
                        Actions :</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; background-color: #c9d8fe; border-right: 2px solid black">
                        {{ $data->actions }}
                    </td>
                    <th style="width:15%; background-color: #c9d8fe; font-size: 13px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left;background-color: #c9d8fe;">
                        Due By:</th>
                    <td style="width:35%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-right: 2px solid black;background-color: #c9d8fe;">
                        {{ $data->due_by }} {{ $data->time_zone }}
                    </td>
                </tr>
            </table>
            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px; ">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Call Notes</h5>
            </div>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td
                        style="width:100%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black; border-right: 2px solid black">
                        {!! $data->meeting_notes !!}
                    </td>
                </tr>
            </table>

            <div class="Prospect_heading" style="background-color: #404040; color: #fff; padding: 10px 10px; ">
                <h5 style="margin: 0; font-size: 13px; font-weight: bold;">Additional Notes</h5>
            </div>
            <table style="width:100%; border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td
                        style="width:100%; font-size: 12px; border: 1px solid black; border-collapse: collapse; padding: 10px; text-align: left; border-left: 2px solid black; border-right: 2px solid black">
                        {!! $data->additional_notes !!}
                    </td>
                </tr>
            </table>
        </div>
    </section>

</body>

</html>
