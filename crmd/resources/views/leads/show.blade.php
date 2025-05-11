@extends('layouts.admin')


@section('content')

    <?php
    $fullName = '';
    if ($data['prospect_first_name'] != 'NA' && $data['prospect_last_name'] == 'NA') {
        $fullName = $data['prospect_first_name'];
    } elseif ($data['prospect_first_name'] == 'NA' && $data['prospect_last_name'] != 'NA') {
        $fullName = $data['prospect_last_name'];
    } elseif ($data['prospect_first_name'] != 'NA' && $data['prospect_last_name'] != 'NA') {
        $fullName = $data['prospect_first_name'] . ' ' . $data['prospect_last_name'];
    }
    ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                @if (auth()->user()->manager_type != 2)
                    <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ url('performanceExternalManager') }}">Performance</a></li>
                @endif
                @if (Auth::user()->is_admin == 1)
                    <?php $last_url = redirect()
                        ->getUrlGenerator()
                        ->previous(); ?>
                    <li class="breadcrumb-item"><a href="{{ $last_url }}">View Campaign Leads</a></li>
                @else
                    @if (auth()->user()->manager_type != 2)
                        <li class="breadcrumb-item"><a href="{{ url('leads') }}">Leads</a></li>
                    @endif
                @endif

                @if (Auth::user()->is_admin == 1)
                    <?php $last_url = redirect()
                        ->getUrlGenerator()
                        ->previous(); ?>
                    <li class="breadcrumb-item active">View Lead</li>
                @else
                    <li class="breadcrumb-item active">View Leads</li>
                @endif


            </ol>
        </div>
        <div>
            <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
        </div>
    </div>

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">

                @if (Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @elseif (Session::has('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ Session::get('error') }}
                    </div>
                @endif

                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">{{ $fullName }}</h4>
                    </div>

                    <div class="button_edit edit_leads">
                        @if (auth()->user()->manager_type != 2)
                            <a class="button_edit_anchor" href="{{ url('/editlead/' . $lead_ID) }}">
                                <span class="label label-success edit_leads">Edit Lead</span>
                            </a>
                        @endif
                        <?php
                        $lhs = App\Models\LhsReport::where(['lead_id' => $lead_ID])->first();
                        $urls = '?employee_id=' . request()->get('employee_id') . '&campaign_id=' . request()->get('campaign_id') . '&date_from=' . request()->get('date_from') . '&date_to=' . request()->get('date_to');
                        ?>
                        @if (!empty($lhs))
                            <a href="{{ url('/employee/export/' . $lead_ID . '/word_single_down') . $urls }}"
                                class="button_edit_anchor"><span class="label label-warning">
                                    <i class="ti-download"> </i> Word</span></a>
                            <a href="{{ route('employee.show_mom', [$data['id']]) }}" class="button_edit_anchor"><span
                                    class="label label-warning"> Create MoM</span></a>
                        @endif
                        @if (!empty($lhsFiles))
                            <a href="{{ url($lhsFiles->file_path . '/' . $lhsFiles->file_name) }}"
                                class="button_edit_anchor"><span class="label label-warning" download="file.mp3">
                                    <i class="ti-download"> </i>File</span>
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <!--<h4 class="card-title">Data Export</h4>
                                    <h6 class="card-subtitle">Export data to Copy, CSV, Excel, PDF & Print</h6>-->



                        <div class="form-body vw-lead ">

                            <!--<div class="row p-t-20">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label labelstyle">Job Title</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                          
                                                                <input class="form-control" type="text" name="lead_name" value="{{ $data['lead_name'] }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label labelstyle">Company Name</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                              
                                                                <input class="form-control" type="text" name="company_name" value="{{ $data['company_name'] }}" readonly>
                                                            </div>
                                                        </div>
                                                    
                                                    </div>
                                                </div>
                                            </div>-->


                            <!-- <div class="row p-t-20">
                                                <div class="col-md-6 show_lead">
                                                    <div class="row ">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label labelstyle">First Name</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                   
                                                                <input class="form-control" type="text" name="prospect_first_name" value="{{ $data['prospect_first_name'] }}" readonly>
                                                            </div>
                                                        </div>
                                                    
                                                    </div>
                                                </div>
                                                <div class="col-md-6 show_lead">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label labelstyle">Last Name</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                
                                                                <input class="form-control" type="text" name="prospect_last_name" value="{{ $data['prospect_last_name'] }}" readonly>
                                                             </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> -->
                            <div class="row p-t-20">
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Email</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                @if ($fiedsArray['emailAccessible'] == true)
                                                    <input class="form-control" type="text" name="prospect_email"
                                                        value="{{ $data['prospect_email'] }}" readonly>
                                                @else
                                                    <input class="form-control" type="text" name="prospect_email"
                                                        value="**********" readonly>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Campaign Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="source_name"
                                                    value="{{ $data['source']['source_name'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-t-20">
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Contact No 1</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                @if ($fiedsArray['phoneAccessible'] == true)
                                                    <input class="form-control" type="text" name="contact_number_1"
                                                        value="{{ $data['contact_number_1'] }}" readonly>
                                                @else
                                                    <input class="form-control" type="text" name="contact_number_1"
                                                        value="**********" readonly>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Contact No 2</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                @if ($fiedsArray['phoneAccessible'] == true)
                                                    <input class="form-control" type="text" name="contact_number_2"
                                                        value="{{ $data['contact_number_2'] }}" readonly>
                                                @else
                                                    <input class="form-control" type="text" name="contact_number_2"
                                                        value="**********" readonly>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row p-t-20">

                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Organization Industry</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <input class="form-control" type="text" name="company_industry"
                                                    value="{{ $data['company_industry'] }}" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Organization</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="company_name"
                                                    value="{{ $data['company_name'] }}" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row p-t-20">
                                <!-- <div class="col-md-6 show_lead">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                            <label class="control-label labelstyle">Prospect Name</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                           
                                                            <input class="form-control" type="text" name="prospect_name" value="{{ $data['prospect_first_name'] }} {{ $data['prospect_last_name'] }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Designation</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="designation"
                                                    value="{{ $data['designation'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Linkedin Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                @if ($fiedsArray['linkdinAccessible'] == true)
                                                    <a href="<?php
                                                    $var = $data['linkedin_address'];
                                                    // $var = $data[6]['linkedin_address'];
                                                    if (strpos($var, 'http://') !== 0 && strpos($var, 'https://') !== 0) {
                                                        echo $kasa = 'https://' . $var;
                                                    } else {
                                                        echo $var;
                                                    }
                                                    ?>" target="_blank">
                                                        <input type="text" class="form-control"
                                                            value="<?php
                                                            $var = $data['linkedin_address'];
                                                            // $var = $data[6]['linkedin_address'];
                                                            if (strpos($var, 'http://') !== 0 && strpos($var, 'https://') !== 0) {
                                                                echo $kasa = 'https://' . $var;
                                                            } else {
                                                                echo $var;
                                                            }
                                                            ?>" readonly></a>
                                                @else
                                                    <input type="text" class="form-control" value="**********"
                                                        readonly>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="row p-t-20">
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Business Function</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="bussiness_function"
                                                    value="{{ $data['bussiness_function'] }}" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Designation Level</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="designation_level"
                                                    value="{{ $data['designation_level'] }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row p-t-20">
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Timezone</label>

                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="timezone"
                                                    value="{{ $data['timezone'] }}" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Location</label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">

                                                <input class="form-control" type="text" name="location"
                                                    value="{{ $data['location'] }}" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!--<div class="row p-t-20">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                            <label class="control-label labelstyle">Country</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                   
                                                            <input class="form-control" type="text" name="country" value="{{ $data['country'] }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                            <label class="control-label labelstyle">Linkedin Address</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                           
                                                            <input class="form-control" type="text" name="linkedin_address" value="{{ $data['linkedin_address'] }}" readonly>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>-->

                            <div class="row p-t-20">
                                <div class="col-md-6 show_lead">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label labelstyle">Status</label><br>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                @if ($data['status'] == 1)
                                                    <span class="label label-warning statusbtnmargin">Pending</span>
                                                    @if (auth()->user()->manager_type != 2)
                                                        <span class="label label-info"
                                                            onclick="showstatusmodal('{{ $data['id'] }}')"
                                                            data-toggle="modal" data-target="#status-modal">Change
                                                            Status</span>
                                                    @endif
                                                @elseif($data['status'] == 2)
                                                    <span class="label label-danger statusbtnmargin">Failed</span>
                                                @elseif($data['status'] == 4)
                                                    <span class="label label-warning statusbtnmargin">In Progress</span>
                                                    @if (auth()->user()->manager_type != 2)
                                                        <span class="label label-info"
                                                            onclick="document.getElementById('lead_id').value={{ $data['id'] }}"
                                                            data-toggle="modal" data-target="#status-modal">Change
                                                            Status</span>
                                                    @endif
                                                @else
                                                    <span class="label label-success statusbtnmargin">Closed</span>
                                                    @if (auth()->user()->manager_type != 2)
                                                        <a
                                                            href="{{ url('/employee/lhs_report/view_lhs', [$data['id']]) }}"><button
                                                                type="button" class="btn btn-info">View
                                                                Report</button></a>
                                                    @endif
                                                @endif
                                                </h6>
                                                @if (auth()->user()->manager_type != 2)
                                                    <span class="label label-info"
                                                        onclick="showaddmodal('{{ $data['id'] }}')"
                                                        data-toggle="modal" data-target="#status-modal-quicknote">Add
                                                        Quick note</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $notesCount = App\Models\Note::where(['lead_id' => $lead_ID])->count();
                            $LhsReportCount = App\Models\LhsReport::where(['lead_id' => $lead_ID])->count();
                            ?>
                            <input type="hidden" id="notes_count_{{ $lead_ID }}" name="notes_count"
                                value="{{ $notesCount }}">
                            <input type="hidden" id="Lhsreport_count_{{ $lead_ID }}" name="Lhsreport_count"
                                value="{{ $LhsReportCount }}">

                            <table id="example23d" class="display nowrap table table-hover table-striped table-bordered"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>


                                        {{-- <th>Lead Name</th> --}}
                                        <th>Note</th>
                                        <th>Reminder Date </th>
                                        <th>Conversation Type</th>
                                        <th>Updated On</th>
                                        {{-- @if (auth()->user()->is_admin == 1)
                                                    <th>Action</th>
                                                    @endif --}}


                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($record['notes'] as $record)
                                        <tr>
                                            <!--<td>{{ $record['created_at'] }}</td>-->
                                            {{-- <td>{{ $record['lead']['prospect_first_name'].' '.$record['lead']['prospect_last_name'] }}</td> --}}
                                            <td class="feedback_td" width="300">
                                                <p class="notes_comment">{{ $record['feedback'] }}</p>
                                            </td>
                                            <td>
                                                <?php
                                                if (isset($record['reminder_date']) && !empty($record['reminder_date'])) {
                                                    echo date('d M, Y', strtotime($record['reminder_date']));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                
                                                ?>

                                            </td>
                                            <td class="feedback_td">{{ $record['reminder_for'] }}</td>
                                            <td style="text-align:center;white-space:nowrap !important">
                                                {{ date('d M, Y h:i A', strtotime($record['updated_at'])) }}
                                            </td>
                                            {{-- @if (auth()->user()->is_admin == 1)
                                                    <td><a href="{{ url('/notes/' . $record['id'] . '/edit') }}"><span class="label" data-toggle="tooltip" data-placement="top" title="Edit Lead" style="color:#000;font-size: 15px;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span></a></td>
                                                    @endif --}}

                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>

            </div>
        </div>



    </div>
    <!-- sample modal content -->
    <div id="status-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="ajaxform">

                <meta name="csrf-token" content="{{ csrf_token() }}" />

                <div>
                    <ul></ul>
                </div>

                <div class="modal-header">
                    <h4 class="modal-title">Change Status</h4>
                    <button type="button" class="close close-status-modal" data-dismiss="modal" aria-hidden="true"
                        style="color:black">×</button>
                </div>
                <div class="alert alert-danger print-error-msg" style="display:none">
                    <ul></ul>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Select Status: </label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="4">In progress</option>
                            <option value="3">Closed</option>
                            <option value="2">Failed</option>
                        </select>
                        @if($errors->has('status'))
                            <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" id="lead_id" name="lead_id">
                    <button type="button" class="btn btn-default waves-effect close-status-modal"
                        data-dismiss="modal">Close</button>
                    <button id="save-data" type="button" class="btn btn-info waves-effect waves-light ">Save
                        changes</button>
                </div>
        </div>
        </form>
    </div>
</div>
    <!-- Quick Notes Add -->
    {{-- <form action="{{route('notes.store')}}" method="post"> --}}
    {{-- @csrf --}}
    <form id="form">
                    <div id="status-modal-quicknote" class="modal fade" tabindex="-1" role="dialog"
                        aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <meta name="csrf-token" content="{{ csrf_token() }}" />
                                <div>
                                    <ul></ul>
                                </div>
                                <style type="text/css">
                                    .responseconversrespo {
                                        float: left;
                                        width: 50%;
                                        margin-bottom: 10px;
                                    }
                                </style>
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Quick Note</h4>
                                    <button type="button" id="modelclose" class="close modal-close" data-dismiss="modal"
                                        aria-hidden="true" style="color:black">×</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group responseconvers">
                                        <div class="responseconversrespo">
                                            <input type="radio" class="conversation_type" id="NoResponse"
                                                name="conversation_type" value="NoResponse" checked="checked">
                                              <label for="NoResponse">VM/No Response</label><br>
                                        </div>
                                        <div class="responseconversrespo">
                                              <input type="radio" class="conversation_type" id="Conversation"
                                                name="conversation_type" value="Conversation">
                                              <label for="Conversation">Conversation</label><br>
                                        </div>
                                    </div>
                                    <div class="NoResponseData">
                                        <div class="form-group" id="status" name="status">
                                            <label class="control-label">Reminder Date</label>
                                            <input type="date" class="form-control" placeholder="Reminder Date"
                                                name="reminder_date" value="{{ old('reminder_date') }}" id="min-date"
                                                data-dtp="dtp_2827e">
                                            <label class="control-label">Reminder Time</label>
                                            <input type="time" class="form-control" id="reminder_time"
                                                name="reminder_time">
                                            <label class="control-label">Conversation Type</label>
                                            {{-- <input type="text" class="form-control required"
                                                placeholder="Reminder Type" id="reminder_for" name="reminder_for"
                                                value="{{ old('reminder_for') }}"> --}}
                                            <select id="reminder_for" class="form-control required" name="reminder_for">
                                                <option value="">Choose Conversation Type</option>
                                                <option value="Declined">Declined</option>
                                                <option value="DNC">DNC</option>
                                                <option value="Follow-up Call">Follow-up Call</option>
                                                <option value="Follow-up Email/Info Requested">Follow-up Email/Info
                                                    Requested</option>
                                                <option value="Meeting Set-up">Meeting Set-up</option>
                                                <option value="Not Interested">Not Interested</option>
                                                <option value="Not Right Party">Not Right Party</option>
                                                <option value="Reference Shared">Reference Shared</option>
                                            </select>
                                            <div class="alert alert-danger print-error-msg-1" style="display:none">
                                                <ul class="custom_text-1"></ul>
                                            </div>
                                            <label class="control-label">Note</label>
                                            <input type="hidden" class="form-control" name="lead_id"
                                                placeholder="Lead Id" value="{{isset($data['id'])}}">
                                            <textarea required type="text" class="form-control required" name="feedback"
                                                id="feedback" placeholder="Enter Note"
                                                style="min-height: 130px;">{{ old('note') }}</textarea>
                                            <div class="alert alert-danger print-error-msg" style="display:none">
                                                <ul class="custom_text"></ul>
                                            </div>
                                            @if($errors->has('status'))
                                                <div class="alert alert-danger">{{ $errors->first('status') }}</div>
                                            @endif
                                        </div>
                                    </div>

                                </div>


                                <div class="modal-footer">
                                    <input type="hidden" id="lead_id_quick_note" name="lead_id_quick_note">
                                    <button type="button" class="btn btn-default waves-effect modal-close"
                                        data-dismiss="modal">Close</button>
                                    {{-- <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i>
                                        Save</button> --}}
                                    <button id="save-data-quick-note" type="button"
                                        class="btn btn-info waves-effect waves-light ">Add Note</button>
                                </div>
                            </div>
                </form>
    {{-- </form> --}}
    </div>
    </div>

    @push('scripts')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#example23').DataTable();
            table
                .order([3, 'asc'])
                .draw();
        });
        $('.modal').on('hidden.bs.modal', function() {
            $("#reminder_for").val('');
            //$("#feedback").val('');
            $('.alert.alert-danger.print-error-msg').hide();
            $('#status').prop('selectedIndex', 0);
        });
        // $(".label-info").click(function(){
        //   $(".form-control").toggleClass("main");
        // });
        $("#save-data-quick-note").click(function(event) {
            event.preventDefault();
            let feedback = $("[name=feedback]").val();
            var selectedVal = "";
            var selected = $("input[type=radio][name=conversation_type]:checked");
            if (selected.length > 0) {
                selectedVal = selected.val();
            }
            var selecteddata = ""
            if (selectedVal == 'Conversation') {
                selecteddata = $('#reminder_for').val();
            } else {
                selecteddata = 1;
            }
            if (selecteddata == 0) {

                $('.alert.alert-danger.print-error-msg-1').show();
                $('ul.custom_text-1').html(
                    '<li class="error_list"><span class="tab">Conversation Type Cannot Be Empty!</span></li>');
            } else if (feedback == 0) {
                $('.alert.alert-danger.print-error-msg-1').hide();
                $('.alert.alert-danger.print-error-msg').show();
                $('ul.custom_text').html(
                    '<li class="error_list"><span class="tab">Note Field Cannot Be Empty!</span></li>');
            } else {
                $('.alert.alert-danger.print-error-msg').hide();
                $('ul.custom_text').html('');
                //   $('alert.alert-success.print-error-msg').show();
                //   $('ul.custom_text').html('<li><span class="error_list">Note Added Successfully</span></li>');
                 let phone_number = $("[name=phone_number]").val();
                let feedback = $("[name=feedback]").val();
                let reminder_date = $("[name=reminder_date]").val();
                let reminder_time = $("[name=reminder_time]").val();
                let reminder_for = $("[name=reminder_for]").val();
                let lead_id = $("input[name=lead_id_quick_note]").val();
                let source_id = $("input[name=source_id]").val();
                let _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route('add_note') }}',
                    type: "POST",
                    data: {
                        reminder_date: reminder_date,
                        reminder_time: reminder_time,
                        reminder_for: reminder_for,
                        lead_id: lead_id,
                        source_id: source_id,
                        feedback: feedback,
                        phone_number:phone_number,
                        _token: _token
                    },
                    success: function(response) {
                        if ($.isEmptyObject(response.error)) {
                            console.log(response);
                            toastr.success(response.success, 'Success!')
                            location.reload(true);
                        } else {
                            toastr.error(response.error, 'Error!');
                        }

                    },
                });
            }

            function printErrorMsg(msg) {
                console.log(msg);
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").css('display', 'block');
                $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
            }
        });
    </script>

    <script>
        $(document).ready(function() {

            $("#save-data").click(function(event) {
                event.preventDefault();

                let status = $("select[name=status]").val();
                let lead_id = $("input[name=lead_id]").val();
                let _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '{{ route('changeStatus') }}',
                    type: "POST",
                    data: {
                        lead_id: lead_id,
                        status: status,
                        _token: _token
                    },
                    success: function(response) {


                        if ($.isEmptyObject(response.error)) {
                            console.log(response);
                            toastr.success(response.success, 'Success!')
                            if (response) {
                                $(".print-error-msg").css('display', 'none');
                                $('.success').text(response.success);
                                var base_url = $('meta[name="base_url"]').attr('content');

                                if (response.status == 'failed') {
                                    var Current_url = base_url + "/leads/failed";
                                    window.location.href = Current_url;
                                } else if (response.status == 'close') {
                                    var Current_url = base_url + "/leads/closed";
                                    window.location.href = Current_url;
                                } else {
                                    // var  Current_url = base_url+"/leads/closed";
                                    //  window.location.href = Current_url;
                                    location.reload(true); // inprogress
                                }

                                // location.reload(true);
                                $("#ajaxform")[0].reset();
                            }

                        } else {
                            $('.alert.alert-danger.print-error-msg').show();
                            $('ul.custom_text').html(response.lhs_link);
                            toastr.error(response.error, 'Error!');
                        }

                    },
                });


                function printErrorMsg(msg) {
                    console.log(msg);
                    $(".print-error-msg").find("ul").html('');
                    $(".print-error-msg").css('display', 'block');
                    //$.each( msg, function( key, value ) {
                    $(".print-error-msg").find("ul").append('<li>' + msg + '</li>');
                    // });
                }


            });

        });

        $(document).ready(function() {
            $('#feedback').text('VM/No Response');
            $('input[type=radio][name=conversation_type]').change(function() {
                console.log(this.value);
                if (this.value == 'NoResponse') {
                    $('#feedback').val('VM/No Response');
                    // $('#min-date').prop("disabled", false); // Element(s) are now enabled.
                    //$('#reminder_time').prop("disabled", false); // Element(s) are now enabled.
                    //$('#reminder_for').prop("disabled", false); // Element(s) are now enabled.

                    $('#min-date').val("");
                    $('#reminder_for').val("");
                    $('#reminder_time').val("");
                     $('#phone_number').val('');
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').hide();

                } else if (this.value == 'Conversation') {
                    $('.alert.alert-danger.print-error-msg-1').hide();
                    $('.alert.alert-danger.print-error-msg').hide();
                    $('#feedback').val('');
                    $('#phone_number').val('');
                    $('#min-date').val("");
                    $('#reminder_for').val("");
                    $('#reminder_time').val("");
                    // $('#min-date').prop("disabled", false); // Element(s) are now enabled.
                    //$('#reminder_time').prop("disabled", false); // Element(s) are now enabled.
                    //$('#reminder_for').prop("disabled", false); // Element(s) are now enabled.



                }
            });
        });
        $(document).ready(function() {
            $('#example23d').dataTable({
                "order": [],
                "columnDefs": [{
                        "type": "date",
                        "targets": 3
                    } //date column formatted like "03/23/2018 10:25:13 AM".
                ],
            });
        });
    </script>

<script>
        function showaddmodal(id) {
            $('#lead_id_quick_note').val(id);
            $('#status-modal-quicknote').modal('show');
        }

        $('.modal-close').on('click', function (event) {
            $('#status-modal-quicknote').modal('hide');
        });

        $('.largemodal-close').on('click', function (event) {
            $('#largeModal').modal('hide');
        });

        function showstatusmodal(id) {
            $('#lead_id').val(id);
            $('#status-modal').modal('show');

        }

        $('.close-status-modal').on('click', function (event) {
            $('#status-modal').modal('hide');
        });


       


    </script>

    @endpush
@endsection
