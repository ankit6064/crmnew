@extends('layouts.admin')

@section('content')
    <style>
        .form-control:disabled,
        .form-control[readonly] {
            background-color: #e9ecef !important;
            border: 1px solid #ced4da !important;
            opacity: 0.7;
        }

        .card-body.lhs label {
            position: inherit;
        }
    </style>

    @php
        $managerData = App\Models\User::find(90);
    @endphp

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('leads/closed') }}">Closed Leads</a></li>
                <li class="breadcrumb-item active">Create MOM</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">MOM Report</h4>
                    </div>
                    <div class="card-body lhs">
                        <form method="POST" action="{{ route('employee.create_mom') }}">
                            @csrf

                            <input type="hidden" name="lead_id" value="{{ $data->id }}">
                            <input type="hidden" name="bdm_id" value="{{ $data->user_id }}">

                            <div class="form-body">
                                <!-- Meeting Date & Time and Timezone -->
                                <div class="row p-t-20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meeting_datetime">Meeting Date & Time:</label>
                                            <input id="meeting_datetime" type="datetime-local" name="meeting_datetime" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                            @error('meeting_datetime')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="time_zone">TimeZone:</label>
                                            @php $tzlist = DateTimeZone::listAbbreviations(); @endphp
                                            <select id="time_zone" name="time_zone" class="form-control">
                                                <option value="">Select An Timezone</option>
                                                @foreach (array_keys($tzlist) as $timezone)
                                                    <option value="{{ strtoupper($timezone) }}" {{ old('time_zone', $data->timezone_2) == strtoupper($timezone) ? 'selected' : '' }}>
                                                        {{ strtoupper($timezone) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('time_zone')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Account, BDM, Setup By -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="account">Account:</label>
                                            <input type="text" id="account" name="account" class="form-control" value="{{ $data->source->source_name }} - {{ $data->source->description }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="company_name">BDM:</label>
                                            <input type="text" id="company_name" name="company_name" class="form-control" value="{{ $managerData->first_name }} {{ $managerData->last_name }}" disabled>
                                            @error('company_name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="setup_by_id">Setup By:</label>
                                            <input type="text" id="setup_by_id" name="setup_by_id" class="form-control" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}" disabled>
                                            @error('setup_by_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Company, Participants, Customer Participants -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_name">Company:</label>
                                            <input type="text" id="company_name" name="company_name" class="form-control" value="{{ old('company_name', $data->company_name) }}">
                                            @error('company_name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="exl_participants">Participants:</label>
                                            <input type="text" id="exl_participants" name="exl_participants" class="form-control" value="{{ old('exl_participants') }}">
                                            @error('exl_participants')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Participants and Designations -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="customer_participants_and_designations">Customer Participants and Designations:</label>
                                            <input type="text" id="customer_participants_and_designations" name="customer_participants_and_designations" class="form-control" value="{{ old('customer_participants_and_designations', $data->prospect_first_name . ' ' . $data->prospect_last_name . ' (' . $data->designation . ')') }}">
                                            @error('customer_participants_and_designations')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Call Notes -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="meeting_notes">Call Notes:</label>
                                            <textarea id="meeting_notes" name="meeting_notes" class="form-control editor">{{ old('meeting_notes', $data->call_notes) }}</textarea>
                                            @error('meeting_notes')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Notes -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="additional_notes">Additional Notes:</label>
                                            <textarea id="additional_notes" name="additional_notes" class="form-control editor">{{ old('additional_notes') }}</textarea>
                                            @error('additional_notes')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions and Owners -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="actions">Actions:</label>
                                            <input type="text" id="actions" name="actions" class="form-control" value="{{ old('actions', $data->revenue) }}">
                                            @error('actions')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="owners_id">Owners:</label>
                                            <input type="text" id="owners_id" name="owners_id" class="form-control" value="{{ old('owners_id', $data->revenue) }}">
                                            @error('owners_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="due_by">Due By:</label>
                                            <input type="date" id="due_by" name="due_by" class="form-control" value="{{ old('due_by') }}">
                                            @error('due_by')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                                    <button type="reset" class="btn btn-inverse">Cancel</button>
                                    <a href="{{ url('leads/closed') }}" class="btn btn-info">Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
