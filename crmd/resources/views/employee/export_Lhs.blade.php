@extends('layouts.admin')
@section('content')
    <style>
        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef !important;
            border: 1px solid #ced4da !important;
            opacity: 0.7;
        }

        .btn {
            font-size: 13px !important;
        }
        .card-body.lhs label {
         position: inherit;
        }
    </style>
    <?php
    $boardNumber = '';
    if (isset($data->contact_number_1) && !empty($data->contact_number_1)) {
        $board_no = $data->contact_number_1;
        if (strpos($board_no, ' , ') > -1) {
            $array = explode(',', $board_no);
            $boardNumber = $array[0];
        } elseif (strpos($board_no, '/') > -1) {
            $array = explode('/', $board_no);
            $boardNumber = $array[0];
        } else {
            $boardNumber = substr($board_no, 0, 15);
        }
    }
    
    $cellNumber = '';
    if (isset($data->contact_number_2) && !empty($data->contact_number_2)) {
        $cell_no = $data->contact_number_2;
        if (strpos($cell_no, ',') > -1) {
            $array = explode(',', $cell_no);
            $cellNumber = $array[0];
        } elseif (strpos($cell_no, '/') > -1) {
            $array = explode('/', $cell_no);
            $cellNumber = $array[0];
        } else {
            $cellNumber = substr($cell_no, 0, 15);
        }
    }
    
    ?>

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('employees') }}">Closed Leads</a></li>
                <li class="breadcrumb-item active">Add LHS Report</li>
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
        <!-- Row -->
        <div class="row">
            <div class="col-lg-12">

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
                        <h4 class="m-b-0 text-white">LHS Report</h4>
                    </div>
                    <div class="card-body lhs">
                        <form method='post' action="{{ url('lhs_report_save') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-body ">
                                <div class="row p-t-20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" id="lead_id" name='lead_id'class="form-control"
                                                placeholder="NA" value="{{ $data->id }}">
                                            <?php if(isset($_GET["status"])){  ?>
                                            <input type="hidden" id="lead_status" name='status'class="form-control"
                                                placeholder="NA" value="{{ intval($_GET['status']) }}">
                                            <?php } ?>
                                            <input type="hidden" id="previous_url" name='previous_url'class="form-control"
                                                placeholder="NA" value="{{ URL::previous() }}">

                                            <label class="control-label read-only-label">Contact's Name:</label>
                                            <input type="text" id="prospect_first_name"
                                                name='prospect_first_name'class="form-control"
                                                placeholder="Enter Contact Name"
                                                value="{{ $data->prospect_first_name . ' ' . $data->prospect_last_name }}"
                                                readonly>
                                            @if ($errors->has('prospect_first_name'))
                                                <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Board Number:</label>
                                            <input type="text" id="board_no" name='board_no'
                                                class="form-control form-control-danger" placeholder="Enter Board Number"
                                                value="{{ $boardNumber }}">
                                            @if ($errors->has('board_no'))
                                                <div class="alert alert-danger">{{ $errors->first('board_no') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label read-only-label">Contact's Designation:</label>
                                            <input type="text" id="designation" name='designation'class="form-control"
                                                placeholder="Enter Contact's Designation" value="{{ $data->designation }}">
                                            @if ($errors->has('designation'))
                                                <div class="alert alert-danger">{{ $errors->first('designation') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Direct Number:</label>
                                            <input type="text" id="direct_no" name='direct_no'class="form-control"
                                                placeholder="Enter Direct Number" value="{{ old('direct_no') }}">
                                            @if ($errors->has('direct_no'))
                                                <div class="alert alert-danger">{{ $errors->first('direct_no') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label read-only-label">Company:</label>
                                            <input type="text" id="company_name" name='company_name'class="form-control"
                                                placeholder="Enter Company" value="{{ $data->company_name }}">
                                            @if ($errors->has('company_name'))
                                                <div class="alert alert-danger">{{ $errors->first('company_name') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Ext (if any):</label>
                                            <input type="text" id="ext_if_any" name='ext_if_any'class="form-control"
                                                placeholder="Enter Ext (if any)" value="{{ old('ext_if_any') }}">
                                            @if ($errors->has('ext_if_any'))
                                                <div class="alert alert-danger">{{ $errors->first('ext_if_any') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label read-only-label">Industry:</label>
                                            <input type="text" id="company_industry"
                                                name='company_industry'class="form-control" placeholder="Enter Industry"
                                                value="{{ $data->company_industry }}">
                                            @if ($errors->has('company_industry'))
                                                <div class="alert alert-danger">{{ $errors->first('company_industry') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label read-only-label">Cell Number:</label>
                                            <input type="text" id="contact_number_1"
                                                name='contact_number_1'class="form-control"
                                                placeholder="Enter Cell Number" value="{{ $cellNumber }}">
                                            @if ($errors->has('contact_number_1'))
                                                <div class="alert alert-danger">{{ $errors->first('contact_number_1') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Employees:</label>
                                            <input type="text" id="employees_strength"
                                                name='employees_strength'class="form-control"
                                                placeholder="Enter Employees" value="{{ old('employees_strength') }}">
                                            @if ($errors->has('employees_strength'))
                                                <div class="alert alert-danger">{{ $errors->first('employees_strength') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label read-only-label">Email</label>
                                            <input type="text" id="prospect_email" name='prospect_email'
                                                class="form-control" placeholder="Enter Email"
                                                value="{{ $data->prospect_email }}">
                                            @if ($errors->has('prospect_email'))
                                                <div class="alert alert-danger">{{ $errors->first('prospect_email') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Revenue:</label>
                                            <input type="text" id="revenue" name='revenue'class="form-control"
                                                placeholder="Enter Revenue" value="{{ old('revenue') }}">
                                            @if ($errors->has('revenue'))
                                                <div class="alert alert-danger">{{ $errors->first('revenue') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">EA Name:</label>
                                            <input type="text" id="ea_name" name='ea_name'class="form-control"
                                                placeholder="Enter EA Name" value="{{ old('ea_name') }}">
                                            @if ($errors->has('ea_name'))
                                                <div class="alert alert-danger">{{ $errors->first('ea_name') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Address</label>
                                            <input type="text" id="address" name='address' class="form-control"
                                                placeholder="Enter Address" value="{{ old('address') }}">
                                            @if ($errors->has('address'))
                                                <div class="alert alert-danger">{{ $errors->first('address') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">EA Phone Number:</label>
                                            <input type="text" id="ea_phone_no" name='ea_phone_no'class="form-control"
                                                placeholder="Enter EA Phone Number" value="{{ old('ea_phone_no') }}">
                                            @if ($errors->has('ea_phone_no'))
                                                <div class="alert alert-danger">{{ $errors->first('ea_phone_no') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label read-only-label">LinkedIn Profile:</label>
                                            <input type="text" id="linkedin_address"
                                                name='linkedin_address'class="form-control"
                                                placeholder="Enter EA LinkedIn Profile"
                                                value="{{ $data->linkedin_address }}" readonly>
                                            @if ($errors->has('linkedin_address'))
                                                <div class="alert alert-danger">{{ $errors->first('linkedin_address') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">EA Email:</label>
                                            <input type="text" id="ea_email" name='ea_email' class="form-control"
                                                placeholder="Enter EA Email:" value="{{ old('ea_email') }}">
                                            @if ($errors->has('ea_email'))
                                                <div class="alert alert-danger">{{ $errors->first('ea_email') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Prospect Level:</label>
                                            <input type="text" id="prospects_level"
                                                name='prospects_level'class="form-control"
                                                placeholder="Enter Prospect Level" value="{{ old('prospects_level') }}">
                                            @if ($errors->has('prospects_level'))
                                                <div class="alert alert-danger">{{ $errors->first('prospects_level') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Website:</label>
                                            <input type="text" id="website" name='website'class="form-control"
                                                placeholder="Enter Website" value="{{ old('website') }}">
                                            @if ($errors->has('website'))
                                                <div class="alert alert-danger">{{ $errors->first('website') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Prospect Vertical:</label>
                                            <input type="text" id="prospect_vertical"
                                                name='prospect_vertical'class="form-control"
                                                placeholder="Enter Prospect Vertical"
                                                value="{{ old('prospect_vertical') }}">
                                            @if ($errors->has('prospect_vertical'))
                                                <div class="alert alert-danger">{{ $errors->first('prospect_vertical') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Opt-in Status:</label>
                                            <input type="text" id="opt_in_status"
                                                name='opt_in_status'class="form-control" placeholder="Enter Opt-in Status"
                                                value="{{ old('opt_in_status') }}">
                                            @if ($errors->has('opt_in_status'))
                                                <div class="alert alert-danger">{{ $errors->first('opt_in_status') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Company Description</label>
                                            <textarea placeholder="Enter Company Description" name='company_desc' value="{{ old('company_desc') }}"
                                                class="form-control company_desc">{{ old('company_desc') }}</textarea>
                                            @if ($errors->has('company_desc'))
                                                <div class="alert alert-danger">{{ $errors->first('company_desc') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Responsibilities:</label>
                                            <textarea placeholder="Enter Responsibilities" id="responsibilities" name='responsibilities' class="form-control">{{ old('responsibilities') }}</textarea>
                                            @if ($errors->has('responsibilities'))
                                                <div class="alert alert-danger">{{ $errors->first('responsibilities') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Team Size:</label>
                                            <textarea placeholder="Enter Team Size" id="team_size" name='team_size' class="form-control">{{ old('team_size') }}</textarea>
                                            @if ($errors->has('team_size'))
                                                <div class="alert alert-danger">{{ $errors->first('team_size') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Pain Areas:</label>
                                            <textarea placeholder="Enter Pain Areas" id="pain_areas" name='pain_areas' class="form-control">{{ old('pain_areas') }}</textarea>
                                            @if ($errors->has('pain_areas'))
                                                <div class="alert alert-danger">{{ $errors->first('pain_areas') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Interest/New Initiatives:</label>
                                            <textarea placeholder="Enter Interest/New Initiatives" id="interest_new_initiatives" name='interest_new_initiatives'
                                                class="form-control">{{ old('interest_new_initiatives') }}</textarea>
                                            @if ($errors->has('interest_new_initiatives'))
                                                <div class="alert alert-danger">
                                                    {{ $errors->first('interest_new_initiatives') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Budget:</label>
                                            <textarea placeholder="Enter Budget" id="budget" name='budget' class="form-control">{{ old('budget') }}</textarea>
                                            @if ($errors->has('budget'))
                                                <div class="alert alert-danger">{{ $errors->first('budget') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Defined Agenda:</label>
                                            <textarea placeholder="Enter Defined Agenda" id="defined_agenda" name='defined_agenda' class="form-control">{{ old('defined_agenda') }}</textarea>
                                            @if ($errors->has('defined_agenda'))
                                                <div class="alert alert-danger">{{ $errors->first('defined_agenda') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">Call Notes:</label>
                                            <textarea placeholder="Enter Call Notes" id="editor" name='call_notes' class="form-control editor">{{ old('call_notes') }}</textarea>
                                            @if ($errors->has('call_notes'))
                                                <div class="alert alert-danger">{{ $errors->first('call_notes') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <h5>Does the prospect wish to have a Face to Face meeting or teleconference?
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <select id="meeting_teleconference"
                                                name='meeting_teleconference'class="form-control">
                                                @if (old('meeting_teleconference') !== null)
                                                    <option value="{{ old('meeting_teleconference') }}">
                                                        {{ old('meeting_teleconference') }}</option>
                                                @else
                                                    <option vlaue="">Select Any Option</option>
                                                @endif
                                                <option vlaue="Face to Face meeting"> Face to Face meeting </option>
                                                <option vlaue="Teleconference"> Teleconference </option>
                                            </select>
                                        </div>
                                        @if ($errors->has('meeting_teleconference'))
                                            <div class="alert alert-danger">{{ $errors->first('meeting_teleconference') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <h5>Is the contact the decision maker? If No, then who is?</h5>
                                            {{-- <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control" placeholder="" value="{{ old('prospect_first_name') }}">
                              @if ($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <select id="contact_decision_maker"
                                                name='contact_decision_maker'class="form-control">
                                                @if (old('contact_decision_maker') !== null)
                                                    <option value="{{ old('contact_decision_maker') }}">
                                                        {{ old('contact_decision_maker') }}</option>
                                                @else
                                                    <option vlaue="">Select Any Option</option>
                                                @endif
                                                <option vlaue="Yes">Yes</option>
                                                <option vlaue="No">No</option>
                                            </select>
                                        </div>
                                        @if ($errors->has('contact_decision_maker'))
                                            <div class="alert alert-danger">{{ $errors->first('contact_decision_maker') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <h5>Who else would be the influencers in the decision making process?</h5>
                                            {{-- <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control" placeholder="Who else would be the influencers in the decision making process?" value="{{ old('prospect_first_name') }}">
                              @if ($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <input type="text" id="influencers_decision_making_process"
                                                name='influencers_decision_making_process'class="form-control"
                                                placeholder="NA"
                                                value="{{ old('influencers_decision_making_process') }}">
                                            @if ($errors->has('influencers_decision_making_process'))
                                                <div class="alert alert-danger">
                                                    {{ $errors->first('influencers_decision_making_process') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <h5>Is the Company already affiliated with any other similar services? If Yes,
                                                Name?</h5>
                                            {{-- <input type="text" id="prospect_first_name" name='prospect_first_name'class="form-control" placeholder="Is the Company already affiliated with any other similar services? If Yes, Name?" value="{{ old('prospect_first_name') }}">
                              @if ($errors->has('prospect_first_name'))
                              <div class="alert alert-danger">{{ $errors->first('prospect_first_name') }}</div>
                              @endif --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"></label>
                                            <input type="text" id="company_already_affiliated"
                                                name='company_already_affiliated'class="form-control" placeholder="NA"
                                                value="{{ old('company_already_affiliated') }}">
                                            @if ($errors->has('company_already_affiliated'))
                                                <div class="alert alert-danger">
                                                    {{ $errors->first('company_already_affiliated') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Date 1:</label>
                                            <input type="date" class="form-control date" placeholder="23-Dec-21"
                                                name="meeting_date1" value="{{ old('meeting_date1') }}">
                                        </div>
                                        @if ($errors->has('meeting_date1'))
                                            <div class="alert alert-danger">{{ $errors->first('meeting_date1') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Time 1:</label>
                                            <input type="time" class="form-control"
                                                value="{{ old('meeting_time1') }}" id="appt" name="meeting_time1">
                                        </div>
                                        @if ($errors->has('meeting_time1'))
                                            <div class="alert alert-danger">{{ $errors->first('meeting_time1') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label"> TimeZone 1:</label>
                                            <?php $tzlist = DateTimeZone::listAbbreviations();
                                            $tzzlistrr = array_keys($tzlist);
                                            ?>
                                            <select id="time_zone" required name='timezone_1' class="form-control">
                                                @if (old('timezone_1') !== null)
                                                    <option value="{{ old('timezone_1') }}">{{ old('timezone_1') }}
                                                    </option>
                                                @else
                                                    <option vlaue="">Select An Timezone</option>
                                                @endif
                                                <?php $i = 0; ?>
                                                @foreach ($tzzlistrr as $tzzlistrrrr)
                                                    <?php $tzzlist = array_keys($tzlist); ?>
                                                    <option vlaue={{ strtoupper($tzzlist[$i]) }}>
                                                        {{ strtoupper($tzzlist[$i]) }}</option>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('timezone_1'))
                                            <div class="alert alert-danger">{{ $errors->first('timezone_1') }}</div>
                                        @endif
                                    </div>


                                </div>
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Date 2:</label>
                                            <input type="date" class="form-control date" placeholder="23-Dec-21"
                                                name="meeting_date2" value="{{ old('meeting_date2') }}">
                                        </div>
                                        @if ($errors->has('meeting_date2'))
                                            <div class="alert alert-danger">{{ $errors->first('meeting_date2') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Time 2:</label>
                                            <input type="time" class="form-control"
                                                value="{{ old('meeting_time2') }}" id="appt" name="meeting_time2">
                                            @if ($errors->has('meeting_time2'))
                                                <div class="alert alert-danger">{{ $errors->first('meeting_time2') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">TimeZone 2:</label>
                                            <?php $tzlist = DateTimeZone::listAbbreviations();
                                            $tzzlistrr = array_keys($tzlist);
                                            ?>
                                            <select id="time_zone" required name='timezone_2'class="form-control">
                                                @if (old('timezone_2') !== null)
                                                    <option value="{{ old('timezone_2') }}">{{ old('timezone_2') }}
                                                    </option>
                                                @else
                                                    <option vlaue="">Select An Timezone</option>
                                                @endif
                                                <?php $i = 0; ?>
                                                @foreach ($tzzlistrr as $tzzlistrrrr)
                                                    <?php $tzzlist = array_keys($tzlist); ?>
                                                    <option vlaue={{ strtoupper($tzzlist[$i]) }}>
                                                        {{ strtoupper($tzzlist[$i]) }}</option>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($errors->has('timezone_2'))
                                            <div class="alert alert-danger">{{ $errors->first('timezone_2') }}</div>
                                        @endif
                                    </div>


                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        {{-- <label class="control-label">Upload File</label> --}}
                                        <div class="col-md-12">
                                            <input type="file" id="upload_file" name="upload_file[]"
                                                class="form-control form-control-line" accept=".mp3,.zip,.rar">
                                            @if ($errors->has('upload_file'))
                                                <div class="alert alert-danger">{{ $errors->first('upload_file') }}</div>
                                            @endif
                                        </div>
                                        @if ($errors->has('image'))
                                            <div class="alert alert-danger">{{ $errors->first('image') }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i>
                                        Save</button>
                                    <input type="reset" class="btn btn-inverse" value="Cancel" />
                                    <a href="{{ url()->previous() }}"><button type="button" class="btn btn-info">Back</button></a>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Row -->
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
      
    </div>
@endsection

{{-- <script src="https://code.jquery.com/jquery-3.6.0.slim.min.js" integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI=" crossorigin="anonymous"></script>


<link href="{{ asset('resources/views/employees/datetimepicker/css/jquery.datetimepicker.min.css') }}" rel="stylesheet">
<script src = "{{ asset('resources/views/employees/datetimepicker/js/datetimepicker.js') }}"></script>

<script type="text/javascript">
   jQuery(document).ready(function () {
      jQuery("#datetimepicker").datetimepicker();
   });
 </script> --}}
