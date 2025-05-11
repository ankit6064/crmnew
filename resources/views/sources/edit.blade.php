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
                        <li class="breadcrumb-item active">Edit Campaign</li>
                    </ol>
                </div>
                <div>
                    <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
                </div>
            </div>


    <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-outline-info">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Edit Campaign</h4>
                            </div>
                            <div class="card-body">
                                  <form method='post' action="{{route('sources.update', [$data->id])}}" enctype="multipart/form-data">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    
                                    <div class="form-body add_custom_table">
                                        <!--<h3 class="card-title">Edit Lead Details</h3>
                                        <hr>-->

                                        <div class="alert alert-danger print-error-msg" style="display:none">
                                            <ul></ul>
                                        </div>
                                        <div class="row p-t-20">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Campaign</label>
                                                    <input type="text" id="source_name" name='source_name' class="form-control" placeholder="Enter Campaign" value="{{ $data->source_name }}">
                                                    @if($errors->has('source_name'))
                                                        <div class="alert alert-danger">{{ $errors->first('source_name') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Sub-campaign</label>
                                                    <input type="text" id="description" name='description' class="form-control" placeholder="Enter Sub-campaign" value="{{ $data->description }}">
                                                    @if($errors->has('description'))
                                                        <div class="alert alert-danger">{{ $errors->first('description') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Start Date</label>
                                                    <input type="date" name="start_date" class="form-control" value="{{ $data->start_date }}">
                                                    @if($errors->has('start_date'))
                                                        <div class="alert alert-danger">{{ $errors->first('start_date') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--/span-->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">End Date</label>
                                                    <input type="date" name="end_date" class="form-control" value="{{ $data->end_date }}">
                                                    @if($errors->has('end_date'))
                                                        <div class="alert alert-danger">{{ $errors->first('end_date') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--/span-->
                                        </div>
                                        <!--/row-->
                                        
                                
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-success save-data"> <i class="fa fa-check"></i> Save</button>
                                        <!-- <input type="reset" class="btn btn-inverse" value="Cancel" /> -->
                                        <a href="{{ url('sources') }}"><button type="button" class="btn btn-info">Back</button></a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row -->
     
    </div>
	
    
  
@endsection
