@extends('layouts.admin')
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('employees') }}">Employees</a></li>
                <li class="breadcrumb-item active">Add Employee</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Start Page Content -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0 text-white">Add Employee</h4>
                        <a href="{{ url()->previous() }}" class="btn btn-light d-flex align-items-center">
                            <span class="material-symbols-outlined mr-2">
                                arrow_back
                            </span>
                            Back
                        </a>    
                    </div>
                    <div class="card-body">
                        <form method='post' action="{{ route('employee.storemanageremployeedetail') }}">
                            @csrf
                            <div class="form-body add_custom_table">
                                <div class="row p-t-20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">First Name</label>
                                            <input type="text" id="first_name" name='first_name' class="form-control"
                                                placeholder="Enter First Name" value="{{ old('first_name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Last Name</label>
                                            <input type="text" id="last_name" name='last_name'
                                                class="form-control form-control-danger" placeholder="Enter Last Name"
                                                value="{{ old('last_name') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Phone No</label>
                                            <input type="text" id="phone_no" name='phone_no' class="form-control"
                                                placeholder="Enter Phone No" value="{{ old('phone_no') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Email</label>
                                            <input type="text" id="email" name='email' class="form-control"
                                                placeholder="Enter Email" value="{{ old('email') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Address</label>
                                            <input type="text" id="address" name='address' class="form-control"
                                                placeholder="Enter Address" value="{{ old('address') }}">
                                        </div>
                                    </div>
                                    @if( !empty(request('managerid')))
                                    <input type="text" name="manager" id="" value="{{ request('managerid') }}" style="display:none">

                                    @else
                                  <input type="text" name="manager" id="" value="{{Auth::id();}}" style="display:none">
                                  @endif
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                                <input type="reset" class="btn btn-inverse" value="Cancel" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
