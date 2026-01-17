@extends('layouts.admin')
@section('content')

<div class="main-right addsubmanager addlead">
    <div class="right-side add-sub">
        <div class="graph">

            <h2>Edit Lead</h2>

            <form method="post" 
                  action="{{ route('leads.update', $data->id) }}"
                  id="leadForm">

                @csrf
                {{ method_field('PATCH') }}

                <input type="hidden" id="edit_mode" value="1">

                <!-- Row 1 -->
                <div class="form-row">

                    <!-- CAMPAIGN -->
                    <div class="form-group">
                        <label>Select Source</label>

                        <select name="source_id" id="source_id" disabled>
                            <option value="">Select Source</option>

                            @foreach($sources as $sources)
                                @if ($data->source_id == $sources['id'])
                                    <option value="{{ $sources['id'] }}" selected>
                                        {{ $sources['source_name'] }}
                                    </option>
                                @else
                                    <option value="{{ $sources['id'] }}">
                                        {{ $sources['source_name'] }}
                                    </option>
                                @endif
                            @endforeach
                        </select>

                        <small class="text-danger error">{{ $errors->first('source_id') }}</small>
                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" id="prospect_email"
                               name="prospect_email"
                               placeholder="Enter Email"
                               value="{{ $data->prospect_email }}">
                        <small class="text-danger error">{{ $errors->first('prospect_email') }}</small>
                    </div>
                </div>

                <!-- AJAX LOADED SUB-CAMPAIGN -->
                <div class="form-row appended_items"></div>

                <!-- Row 2 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="prospect_first_name"
                               name="prospect_first_name"
                               value="{{ $data->prospect_first_name }}"
                               readonly>
                        <small class="text-danger error">{{ $errors->first('prospect_first_name') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="prospect_last_name"
                               name="prospect_last_name"
                               value="{{ $data->prospect_last_name }}"
                               readonly>
                        <small class="text-danger error">{{ $errors->first('prospect_last_name') }}</small>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="form-row">

                    <div class="form-group">
                        <label>Organization Industry</label>
                        <input type="text" id="company_industry"
                               name="company_industry"
                               value="{{ $data->company_industry }}">
                        <small class="text-danger error">{{ $errors->first('company_industry') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Organization</label>
                        <input type="text" id="company_name"
                               name="company_name"
                               value="{{ $data->company_name }}">
                        <small class="text-danger error">{{ $errors->first('company_name') }}</small>
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Contact No</label>
                        <input type="text" id="contact_number_1"
                               name="contact_number_1"
                               value="{{ $data->contact_number_1 }}">
                        <small class="text-danger error">{{ $errors->first('contact_number_1') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Second Contact No</label>
                        <input type="text" id="contact_number_2"
                               name="contact_number_2"
                               value="{{ $data->contact_number_2 }}">
                        <small class="text-danger error">{{ $errors->first('contact_number_2') }}</small>
                    </div>
                </div>

                <!-- Row 5 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Prospect Name</label>
                        <input type="text" id="prospect_name"
                               name="prospect_name"
                               value="{{ $data->prospect_first_name }} {{ $data->prospect_last_name }}"
                               readonly>
                        <small class="text-danger error">{{ $errors->first('prospect_name') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Designation</label>
                        <input type="text" id="designation"
                               name="designation"
                               value="{{ $data->designation }}">
                        <small class="text-danger error">{{ $errors->first('designation') }}</small>
                    </div>
                </div>

                <!-- Row 6 -->
                <div class="form-row">

                    <div class="form-group">
                        <label>LinkedIn Address</label>
                        <input type="text" id="linkedin_address"
                               name="linkedin_address"
                               value="{{ $data->linkedin_address }}"
                               readonly>
                        <small class="text-danger error">{{ $errors->first('linkedin_address') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Business Function</label>
                        <input type="text" id="bussiness_function"
                               name="bussiness_function"
                               value="{{ $data->bussiness_function }}">
                        <small class="text-danger error">{{ $errors->first('bussiness_function') }}</small>
                    </div>
                </div>

                <!-- Row 7 -->
                <div class="form-row">

                    <div class="form-group">
                        <label>Designation Level</label>
                        <input type="text" id="designation_level"
                               name="designation_level"
                               value="{{ $data->designation_level }}">
                        <small class="text-danger error">{{ $errors->first('designation_level') }}</small>
                    </div>

                    <div class="form-group">
                        <label>Time Zone</label>
                        <input type="text" id="timezone"
                               name="timezone"
                               value="{{ $data->timezone }}">
                        <small class="text-danger error">{{ $errors->first('timezone') }}</small>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="btn-group">
                    <button type="submit" class="btn btn-save">Save</button>
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">Back</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
