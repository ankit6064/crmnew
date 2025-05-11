@extends('layouts.admin')

 
@section('content')
<style>
    textarea#description1 {
    display: block;
    width: 100%;
    height: calc(1.5em + 1.30rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    resize: auto;
}
</style>


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
                <div>
                    <!--<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>--->
                </div>
            </div>


    <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-12">
                    @if (Session::has('success'))
                       <div class="alert alert-success" role="alert">
                           {{Session::get('success')}}
                       </div>
                    @elseif (Session::has('error'))
                       <div class="alert alert-danger" role="alert">
                           {{Session::get('error')}}
                       </div>
                    @endif
                        <div class="card card-outline-info">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Add Leads to This {{$source['source_name']}} </h4>
                            </div>
                            <div class="card-body">
                                @php $id = $source['id']    @endphp
                                <form id="ajaxform" action="{{route('import_leads')}}" method="POST" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                      <meta name="csrf-token" content="{{ csrf_token() }}" />
                                    
                                    <div class="form-body add_custom_table">
                                        <!--<h3 class="card-title">Add Lead Details</h3>
                                        <hr>-->

                                        <div class="alert alert-danger print-error-msg" style="display:none">
                                            <ul></ul>
                                        </div>

                                        <div class="row p-t-20">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Campaign</label>
                                                    <input type="text" id="source" name='source' readonly class="form-control" placeholder="Enter Campaign" value="{{$source['source_name']}}">
                                                    <input type="hidden" id="source_name1" name='source_name' readonly class="form-control" placeholder="Enter Campaign" value="{{$source['id']}}">
                                                    @if($errors->has('source_name'))
                                                        <div class="alert alert-danger">{{ $errors->first('source_name') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--/span-->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Sub-campaign</label>
                                                    <input type="text"  readonly class="form-control" placeholder="Enter Campaign" value="{{$source['description']}}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Start Date</label>
                                                    <input type="date" name="start_date" readonly class="form-control" value="{{ $source['start_date'] }}">
                                                    @if($errors->has('start_date'))
                                                        <div class="alert alert-danger">{{ $errors->first('start_date') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <!--/span-->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">End Date</label>
                                                    <input type="date" readonly name="end_date" class="form-control" value="{{ $source['end_date'] }}" >
                                                    @if($errors->has('end_date'))
                                                        <div class="alert alert-danger">{{ $errors->first('end_date') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Import Bulk Leads </label>
                                                    <input style="padding-top: 10px" type="file" name="file" class="form-control">
                                                        @if($errors->has('file'))
                                                                <div class="alert alert-danger">{{ $errors->first('file') }}</div>
                                                        @endif
                                                        <br><br>
                                                   
                                                    @if($errors->has('file'))
                                                        <div class="alert alert-danger">{{ $errors->first('file') }}</div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                <br>
                                                <!-- <a class="btn btn-warning" href="{{ url('/public/leads.csv') }}">Download Format</a> -->
                                                </div>
                                            </div>

                                            

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
