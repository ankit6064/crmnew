@extends('layouts.admin')
<style>
.table td, .table th {
    padding: 7px 0 !important;
    font-size: 12.5px;
    vertical-align: middle !important;
}
.table-striped tbody tr:nth-of-type(odd) {
    background: #f2f4f859 !important;
}
.wraping {
    height: auto !important;
}
</style>
 
@section('content')
<style>

.checl_all_leads {
    width: 34%;
    float: right;
    margin-top: 18px;
    padding: 10px 10px 10px 10px;
    background: #192e62;
    border: 1px solid #192e62;
    color: white;
    border-radius: 0.25rem;
}
.checl_all_leads h4 {
    color: white;
    font-size: 16px;
}
</style>

<div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-themecolor">Dashboard</h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('leads') }}">Leads</a></li>
                        <li class="breadcrumb-item active">Assign Leads</li>
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
                               {{Session::get('success')}}
                           </div>
                        @elseif (Session::has('error'))
                           <div class="alert alert-danger" role="alert">
                               {{Session::get('error')}}
                           </div>
                        @endif

                         @if($errors->has('lead_id'))
                            <div class="alert alert-danger">{{ $errors->first('lead_id') }}</div>
                        @endif
                        
                        <div class="card card-outline-info">
                            <div class="card-header">
                                <h4 class="m-b-0 text-white">Assign Leads</h4>
                            </div>
                            <?php
                             $admin = App\Models\User::where(['is_admin'=>Null,'id'=>auth()->user()->id])->first();
                             if(!empty($admin)){  ?>
                            <form class="form-horizontal form-label-left input_mask" id="assignForm" method='post' action="{{ route('assignLeadsManager') }}">
                            <?php } else { ?>
                                <form class="form-horizontal form-label-left input_mask" id="assignForm" method='post' action="{{ route('assignLeadsEmployee') }}">
                            <?php } ?>
                            <div class="card-body">

                                

                                <div class="table-responsive m-t-40">

                                        
                                            @csrf
                                            <div class="mian-dp">
                                            <?php
                                            $admin = App\Models\User::where(['is_admin'=>Null,'id'=>auth()->user()->id])->first();
                                            if(!empty($admin)){  ?>
                                            <div class="main-first">
                                                    <select class="form-control"  name="employee_id" id="employee_id">
                                                        <option value="">Select Manager</option>
                                                        @foreach($employees as $employees)
                                                            @if (old('employee_id') == $employees['id'])
                                                                <option value="{{ $employees['id'] }}" selected>{{ $employees['name'] }}</option>
                                                            @else
                                                                <option value="{{ $employees['id'] }}">{{ $employees['name'] }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @if($errors->has('employee_id'))
                                                        <div class="alert alert-danger">{{ $errors->first('employee_id') }}</div>
                                                    @endif
                                                </div>

                                                <div class="second">
                                                <button type="submit" id="submitButton" class="btn btn-success save-data">Assign</button>
                                              
                                            
                                            </div>
                                                
                                            </div>
                                          <?php  } else { ?>
                                               <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" id="ByCheck" data-toggle="tab" href="#searchByCheck" role="tab" aria-controls="check" aria-selected="true">Assign by Checkbox</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" id="ByRange" data-toggle="tab" href="#searchByRange" role="tab" aria-controls="range" aria-selected="false">Assign by Range</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="myTabContent">
                                                <div class="tab-pane fade show active" id="searchByCheck" role="tabpanel" aria-labelledby="ByCheck">
                                                        
                                                        <div class="main-first">
                                                            <select class="form-control"  name="employee_id" id="employee_id">
                                                                <option value="">Select Employee</option>
                                                                @foreach($employees as $employees)
                                                                @if (old('employee_id') == $employees['id'])
                                                                <option value="{{ $employees['id'] }}" selected>{{ $employees['name'] }}</option>
                                                                @else
                                                                <option value="{{ $employees['id'] }}">{{ $employees['name'] }}</option>
                                                                @endif
                                                                @endforeach
                                                            </select>
                                                            @if($errors->has('employee_id'))
                                                            <div class="alert alert-danger">{{ $errors->first('employee_id') }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="second">
                                                            <button type="submit" id="submitButton" class="btn btn-success save-data">Assign</button>
                                                        </div>
                                                         <div class="checl_all_leads">
                                                                <input class="switch-input all_leads" id="all_leads" name="lead_id[]" type="checkbox" value="all_leads">
                                                                <label for ="all_leads" >
                                                                <h4> Check All Leads</h4>
                                                                </label>
                                                         </div>
                                                    </div>
                                                <div class="tab-pane fade" id="searchByRange" role="tabpanel" aria-labelledby="ByRange">
                                                   
                                                    <div class="col-md-12 mannual_assign">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                            <input type="text" class=" form-control assign_start_id" name="assign_start_id" placeholder="Enter Start Lead ID">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                            <input type="text" class=" form-control assign_end_id" name="assign_end_id" placeholder="Enter End Lead ID">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                              
                                                    
                                                </div>
                                            <!-- </div> -->
                                            <?php } ?>
                                            
                            

                                    <table id="example23" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Campaign Name</th>

                                                <th>Lead Name</th>
                                                <th>Lead ID</th>
                                               <!-- <th>Job Title</th>-->
                                                <th>Email</th>
                                                <th>Phone No.</th>
                                                <th>Date</th>
                            
                                                <th>Action</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            @foreach($data as $data)
                                                 <?php
                                                    if(!empty($data['source_id'])){
                                                    $campaign_id = $data['source_id'];
                                                    $source_data = App\Models\Source::where(['id'=>$data['source_id']])->first();
                                                    // echo"<pre>";print_r($source_data);echo"</pre>";
                                                    $campaign_name = $source_data->source_name;
                                                    
                                                    }else{
                                                        $campaign_name = '';
                                                    }
                                                ?>
                                                <td class="wraping">{{ $campaign_name }}</td>
                                                
                                                <td class="wraping"><a href="{{ url('/leads', [$data['id']]) }}">{{ $data['prospect_first_name'].' '.$data['prospect_last_name'] }}</a></td>
                                                <!--<td class="wraping">{{ $data['company_name'] }}</td>
                                                <td>{{ $data['job_title'] }}</td>-->
                                                <td class="wraping">{{ $data['id'] }}</td>
                                                <td class="wraping">{{ $data['prospect_email'] }}</td>
                                                <td>{{ $data['contact_number_1'] }}</td>
                                                <td>

                                                    @php

                                                    $date=date_create($data['created_at']);

                                                    @endphp

                                                    {{date_format($date,"d M, Y")}}

                                                </td>

                                                
                                                <td><input type="checkbox" class="basic_checkbox" name="lead_id[]" value="{{ $data['id'] }}" id="basic_checkbox_{{ $data['id'] }}" >
                                    <label for="basic_checkbox_{{ $data['id'] }}"></label></td>


                      
                                            </tr>
                                            @endforeach
                                            
                               
                              
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </form>
                        </div>

                    </div>
                </div>

               
        </div>
        

 
  
@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- jQuery (necessary for DataTables plugin) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


<script>
    $(document).ready(function() {
        // Initialize DataTable with client-side processing
        $('#example23').DataTable({
            "processing": false,  // Turn off processing as it's client-side
            "serverSide": false,  // Turn off server-side processing
            "paging": true,       // Enable pagination
            "searching": true,    // Enable search functionality
            "ordering": false,     // Enable column sorting
            "info": true,         // Display information about the data
            "lengthChange": false // Disable the option to change number of rows per page
        });
    });
</script>
<script>
     $(document).ready(function () {
        $('input#all_leads').click(function () {     
        $(".basic_checkbox").prop('checked', $(this).prop('checked'));
        });
});
</script>