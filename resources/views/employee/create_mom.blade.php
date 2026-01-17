@extends('layouts.admin')

@section('content')
    @php
        $managerData = App\Models\User::find(90);
        $fullName = $data->prospect_first_name . ' ' . $data->prospect_last_name;
    @endphp

<div class="main-right addsubmanager addlead">
    <div class="right-side add-sub">
            <div class="graph">
                
                <!-- Header -->
                <div class="form-header mb-4">
                    <h2>Create MOM Report</h2>
                    <p class="text-muted">For: {{ $fullName }} | {{ $data->company_name }}</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('employee.create_mom') }}" class="mom-form">
                    @csrf

                    <input type="hidden" name="lead_id" value="{{ $data->id }}">
                    <input type="hidden" name="bdm_id" value="{{ $data->user_id }}">

                    <!-- Meeting Details Row -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Meeting Date & Time</label>
                            <input type="datetime-local" 
                                   name="meeting_datetime" 
                                   class="form-control"
                                   value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                                   required>
                            @error('meeting_datetime')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">Timezone</label>
                            @php $tzlist = DateTimeZone::listAbbreviations(); @endphp
                            <select name="time_zone" class="form-control" required>
                                <option value="">Select Timezone</option>
                                @foreach (array_keys($tzlist) as $timezone)
                                    <option value="{{ strtoupper($timezone) }}" 
                                            {{ old('time_zone', $data->timezone_2) == strtoupper($timezone) ? 'selected' : '' }}>
                                        {{ strtoupper($timezone) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('time_zone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Account & Participants Row -->
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Account</label>
                            <input type="text" 
                                   class="form-control bg-light"
                                   value="{{ $data->source->source_name }} - {{ $data->source->description }}"
                                   readonly>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">BDM</label>
                            <input type="text" 
                                   class="form-control bg-light"
                                   value="{{ $managerData->first_name }} {{ $managerData->last_name }}"
                                   readonly>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">Setup By</label>
                            <input type="text" 
                                   class="form-control bg-light"
                                   value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}"
                                   readonly>
                        </div>
                    </div>

                    <!-- Company Details Row -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" 
                                   name="company_name" 
                                   class="form-control"
                                   value="{{ old('company_name', $data->company_name) }}"
                                   required>
                            @error('company_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <label class="form-label">EXL Participants</label>
                            <input type="text" 
                                   name="exl_participants" 
                                   class="form-control"
                                   value="{{ old('exl_participants') }}"
                                   placeholder="Enter EXL participants">
                            @error('exl_participants')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Customer Participants -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Customer Participants & Designations</label>
                            <input type="text" 
                                   name="customer_participants_and_designations" 
                                   class="form-control"
                                   value="{{ old('customer_participants_and_designations', $fullName . ' (' . $data->designation . ')') }}"
                                   required>
                            @error('customer_participants_and_designations')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Meeting Notes -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Meeting Notes</label>
                            <textarea name="meeting_notes" 
                                      class="form-control"
                                      rows="4"
                                      placeholder="Enter detailed meeting notes"
                                      required>{{ old('meeting_notes', $data->call_notes) }}</textarea>
                            @error('meeting_notes')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="additional_notes" 
                                      class="form-control"
                                      rows="3"
                                      placeholder="Any additional notes or observations">{{ old('additional_notes') }}</textarea>
                            @error('additional_notes')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Items -->
                    <div class="action-items-section mb-4">
                        <h5 class="section-title">Action Items</h5>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="form-label">Actions</label>
                                <input type="text" 
                                       name="actions" 
                                       class="form-control"
                                       value="{{ old('actions', $data->revenue) }}"
                                       placeholder="Specific actions to be taken">
                                @error('actions')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">Owners</label>
                                <input type="text" 
                                       name="owners_id" 
                                       class="form-control"
                                       value="{{ old('owners_id', $data->revenue) }}"
                                       placeholder="Who is responsible">
                                @error('owners_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">Due By</label>
                                <input type="date" 
                                       name="due_by" 
                                       class="form-control"
                                       value="{{ old('due_by') }}"
                                       min="{{ date('Y-m-d') }}">
                                @error('due_by')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="btn-group">
                    <button type="submit" class="btn btn-save">Save</button>
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">Back</button>
                </div>

                </form>
            </div>
        </div>
    </div>

   

    <script>
        // Set minimum date for due_by field to today
        document.addEventListener('DOMContentLoaded', function() {
            var dueByField = document.querySelector('input[name="due_by"]');
            if (dueByField && !dueByField.value) {
                dueByField.value = new Date().toISOString().split('T')[0];
            }
        });
    </script>
@endsection