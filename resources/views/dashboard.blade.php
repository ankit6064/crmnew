@extends('layouts.admin')
@section('content')
    <style>
        .stat-button.active {
            background: #51c1c8;
            color: #fff;
        }

        .chart-container {
            width: 70%;
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        }

        .filter-bar {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-bar label {
            font-weight: bold;
            margin-right: 5px;
        }

        #barChart {
            width: 100% !important;
            height: 400px !important;
        }

        .stat-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="main-right managersubmanagerclass">
        <div class="right-side">
            <h2>Dashboard</h2>

            <div class="row first-card">

                <div class="col-md-3">
                    <a href="{{ route('manager.index') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Managers</h4>
                                <div class="card-icon">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalmanagers }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employee.submanagerlisting') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Submanagers</h4>
                                <div class="card-icon icon2">
                                    <i class="fa-solid fa-users-gear"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalsubmanagers }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employee.index') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Employees</h4>
                                <div class="card-icon icon2">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalemployees }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('sources.index') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Campaigns</h4>
                                <div class="card-icon icon3">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalcampaigns }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="row second-card">

                <div class="col-md-3">
                    <a href="{{ route('allleadview') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Leads</h4>
                                <div class="card-icon icon4">
                                    <i class="fa-solid fa-user-plus"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalLeads }}</h2>
                                <div class="arrow-icon">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('meeting_scheduled') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Meeting Setup</h4>
                                <div class="card-icon icon6">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totallhscount }}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employeecompletedleads') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Completed Leads</h4>
                                <div class="card-icon icon7">
                                    <i class="fas fa-clipboard-check"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalmomcount }}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('employeefailedleads') }}" class="stat-card-link"
                        style="text-decoration:none; color:inherit;">
                        <div class="stat-card">
                            <div class="card-header">
                                <h4>Total Failed Leads</h4>
                                <div class="card-icon icon8">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                            <div class="card-body">
                                <h2>{{ $totalfailedcount }}</h2>
                                <div class="arrow-icon"><i class="fa-solid fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        </div>
    </div>

@endsection