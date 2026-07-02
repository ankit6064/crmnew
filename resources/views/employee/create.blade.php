@extends('layouts.admin')

@section('content')

    <div class="main-right addsubmanager">
        <div class="right-side">
            <div class="row align-items-center mb-3">
                <div class="col-md-8">
                    <h2 class="mb-0">Add Employee</h2>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn return-btn"
                        onclick="window.history.back() || (window.location.href='{{ route('employee.index') }}');">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </button>
                </div>
            </div>

            <div class="graph">
                <form method="POST" action="{{ route('employee.store') }}">
                    @csrf

                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" id="first_name" name="first_name" class="form-control"
                                placeholder="Enter First Name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="last_name" name="last_name" class="form-control"
                                placeholder="Enter Last Name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="phone_no" name="phone_no" class="form-control"
                                placeholder="Enter Phone No" value="{{ old('phone_no') }}" required>
                            @error('phone_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" id="email" name="email" class="form-control"
                                placeholder="Enter Email" value="{{ old('email') }}" required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="address" name="address" class="form-control"
                                placeholder="Enter Address" value="{{ old('address') }}" required>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Select Manager</label>
                            <select name="manager" class="form-control custom-select" required>
                                <option value="">Select Manager</option>
                                @foreach ($managers as $id => $manager)
                                    <option value='{{ $id }}' {{ (old('manager') == $id || request('manager_id') == $id || request('managerid') == $id) ? 'selected' : '' }}>{{ $manager }}</option>
                                @endforeach
                            </select>
                            @error('manager')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Dialer ID</label>
                            <input type="text" id="dialer_id" name="dialer_id" class="form-control"
                                placeholder="Enter Dialer ID" value="{{ old('dialer_id') }}">
                            @error('dialer_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Dialer Password</label>
                            <input type="text" id="dialer_password" name="dialer_password" class="form-control"
                                placeholder="Enter Dialer Password" value="{{ old('dialer_password') }}">
                            @error('dialer_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-save">Save</button>
                        <button type="button" class="btn btn-cancel" onclick="window.location.href='{{ route('employee.index') }}'">Cancel</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
