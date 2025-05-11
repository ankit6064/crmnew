@extends('layouts.admin')

@section('content')

    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- Row -->
        @if (auth()->user()->is_admin == null)
            <div class="card-group">
                <!-- Campaign Total Leads Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <a href="{{ url('leadslist') }}">
                                <div class="col-12">
                                    <h2 class="m-b-0"><i class="fa fa-calculator text-warning"></i></h2>
                                    <h3 class="">{{ $totalLeads }}</h3>
                                    <h6 class="card-subtitle">Campaign Total Leads</h6>
                                </div>
                            </a>
                            <div class="col-12">
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: 100%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pending Leads Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <a href="{{ url('leadslist/?status=1') }}">
                                <div class="col-12">
                                    <h2 class="m-b-0"><i class="fa fa-clock-o text-info"></i></h2>
                                    <h3 class="">{{ $totalPendingLeads }}</h3>
                                    <h6 class="card-subtitle">Campaign Total Pending Leads</h6>
                                </div>
                            </a>
                            <div class="col-12">
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%; height: 6px;"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Closed Leads Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <a href="{{ url('leadslist/?status=3') }}">
                                <div class="col-12">
                                    <h2 class="m-b-0"><i class="fa fa-check text-success"></i></h2>
                                    <h3 class="">{{ $totalClosedLeads }}</h3>
                                    <h6 class="card-subtitle">Campaign Total Closed Leads</h6>
                                </div>
                            </a>
                            <div class="col-12">
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: 100%; height: 6px;" aria-valuenow="25" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Failed Leads Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <a href="{{ url('leadslist/?status=2') }}">
                                <div class="col-12">
                                    <h2 class="m-b-0"><i class="fa fa-exclamation-triangle text-danger"></i></h2>
                                    <h3 class="">{{ $totalFailedLeads }}</h3>
                                    <h6 class="card-subtitle">Campaign Total Failed Leads</h6>
                                </div>
                            </a>
                            <div class="col-12">
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%; height: 6px;"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (auth()->user()->is_admin == null)
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Users</h4>
                            <div id="morris-donut-left"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Campaign Leads</h4>
                            <div id="morris-donut-right"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (auth()->user()->is_admin == 2)
            <div class="container-fluid" style="padding: 0px;">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-12">
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
                                <h4 class="m-b-0 text-white">Campaigns</h4>
                            </div>
                            <div id="myModal" class="modal fade in" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel">Add Label</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="ajaxform">
                                                <meta name="csrf-token" content="{{ csrf_token() }}" />
                                                <div class="alert alert-danger print-error-msg" style="display:none">
                                                    <ul></ul>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-12">Date</label>
                                                    <div class="col-md-12">
                                                        <input type="date" class="form-control" placeholder="Date"
                                                            name="date">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-12">Amount</label>
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control" placeholder="Amount"
                                                            name="amount">
                                                    </div>
                                                </div>
                                                <input type="hidden" id="source_id" name="source_id">
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-info waves-effect save-data"
                                                data-dismiss="modal">Save</button>
                                            <button type="button" class="btn btn-default waves-effect"
                                                data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive m-t-40">
                                    <table id="example23"
                                        class="display nowrap table table-hover table-striped table-bordered"
                                        cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Campaign</th>
                                                <th>Sub-Campaign</th>
                                                <th>Leads</th>
                                                <th>Last Login</th>
                                                <th>Comments since last session</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $count = 0;
                                                $data_new2 = App\Models\Relation::where(
                                                    'assign_to_manager',
                                                    auth()->user()->id,
                                                )
                                                    ->orderBy('assign_to_employee', 'desc')
                                                    ->groupBy('assign_to_employee')
                                                    ->get();
                                                $data_new = [];
                                                foreach ($data_new2 as $dnewdata) {
                                                    $data_new[] = App\Models\User::where([
                                                        'id' => $dnewdata['assign_to_employee'],
                                                    ])->first();
                                                }
                                            @endphp
                                            @if(!empty($data))
                                            @foreach ($data as $data)
                                                @php
                                                    $user_name = App\Models\User::where([
                                                        'id' => $data['assign_to_employee'],
                                                    ])->first();
                                                    if (isset($user_name) && !empty($user_name)) {
                                                        $camp_name = App\Models\Source::where([
                                                            'id' =>
                                                                $data[
                                                                    '

assign_to_cam'
                                                                ],
                                                        ])->first();
                                                        $futureDate = date('Y-m-d h:i:s', strtotime('+1 year'));
                                                        $count = App\Models\Note::where([
                                                            'source_id' => $data['assign_to_cam'],
                                                        ])
                                                            ->whereBetween('created_at', [
                                                                $user_name['last_login'],
                                                                $futureDate,
                                                            ])
                                                            ->count();
                                                        $lastlogin_new = date(
                                                            'd-m-Y H:m:s',
                                                            strtotime($user_name->last_login),
                                                        );
                                                        $date = date_create($user_name->last_login);
                                                        $lastlogin =
                                                            $lastlogin_new == '01-01-1970 05:01:00'
                                                                ? ''
                                                                : $lastlogin_new;
                                                        $name = $user_name->name;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $name }}</td>
                                                    <td>
                                                        @php
                                                            echo isset($camp_name->source_name) &&
                                                            !empty($camp_name->source_name)
                                                                ? $camp_name->source_name
                                                                : '--';
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            echo isset($camp_name->description) &&
                                                            !empty($camp_name->description)
                                                                ? $camp_name->description
                                                                : '--';
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            echo isset($data['lead_assigned']) &&
                                                            !empty($data['lead_assigned'])
                                                                ? $data['lead_assigned']
                                                                : '--';
                                                        @endphp
                                                    </td>
                                                    <td>
                                                        @php
                                                            echo isset($date) && !empty($date)
                                                                ? date_format($date, 'd-m-Y H:i:s')
                                                                : '--';
                                                        @endphp
                                                    </td>
                                                    <td>{{ $count }}</td>
                                                </tr>
                                                @php $count++; @endphp
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection
