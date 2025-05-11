@extends('layouts.admin')

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ url('sources') }}">Campaigns</a></li>
                <li class="breadcrumb-item active">Add Campaign</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="m-b-0 text-white">Add Campaign</h4>
                    </div>
                    <div class="card-body">
                        <form id="" action="{{ route('sources.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-body add_custom_table">
                                <!-- Error Message Section -->
                                <div class="alert alert-danger print-error-msg" style="display:none">
                                    <ul></ul>
                                </div>

                                <div class="row p-t-20">
                                    <!-- Campaign Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Campaign</label>
                                            <input type="text" id="name" name="source_name" class="form-control"
                                                placeholder="Enter Campaign" value="{{ old('name') }}" required>
                                        </div>
                                    </div>

                                    <!-- Sub-campaign Description -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description" class="control-label">Sub-campaign</label>
                                            <textarea id="description" name="description" maxlength="255" placeholder="Enter Sub-campaign" rows="5"
                                                class="form-control" required>{{ old('description') }}</textarea>
                                        </div>
                                    </div>

                                    <!-- File Upload -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="source_file" class="control-label">Import Bulk Leads</label>
                                            <input type="file" name="source_file" id="source_file" class="form-control"
                                                style="padding-top: 10px" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions (Save and Back buttons) -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-success save-data">
                                    <i class="fa fa-check"></i> Save
                                </button>
                                <button type="button" class="btn btn-info" onclick="window.history.back();">Back</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
