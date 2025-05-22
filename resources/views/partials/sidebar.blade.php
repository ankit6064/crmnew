<aside class="left-sidebar">
    <!-- Sidebar scroll -->
    <div class="scroll-sidebar">

        <!-- User profile -->
        <div class="user-profile">
            <!-- User profile image -->
            <div class="profile-img">
                <img src="@if (!empty(auth()->user()->image)) {{ url('storage/app/images/' . auth()->user()->image) }}
                @else 
                {{ url('img/common/default_green.png') }} @endif" alt="user" />
                <!-- Blinking heartbit notification -->
                <div class="notify setpos">
                    <span class="heartbit"></span>
                    <span class="point"></span>
                </div>
            </div>

            <!-- User profile text -->
            <div class="profile-text">
                <h5>{{ auth()->user()->name }}</h5>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="mdi mdi-power"></i>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <!-- Dropdown menu for user profile options -->
                <div class="dropdown-menu animated flipInY">
                    <a href="#" class="dropdown-item"><i class="ti-user"></i> My Profile</a>
                    <a href="#" class="dropdown-item"><i class="ti-wallet"></i> My Balance</a>
                    <a href="#" class="dropdown-item"><i class="ti-email"></i> Inbox</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item"><i class="ti-settings"></i> Account Settings</a>
                    <div class="dropdown-divider"></div>
                    <a href="login.html" class="dropdown-item"><i class="fa fa-power-off"></i> Logout</a>
                </div>
            </div>
        </div>
        <!-- End User profile -->

        <!-- Sidebar navigation -->
        <nav class="sidebar-nav">
            @if (auth()->user()->is_admin == USER)
                <ul id="sidebarnav">
                    <li class="nav-devider"></li>
                    <li class="nav-small-cap">Employee Panel</li>

                    <li>
                        <a class="waves-effect waves-dark" href="{{ url('employeedashboard') }}" aria-expanded="false">
                            <i class="mdi mdi-gauge"></i><span class="hide-menu">Home</span>
                        </a>
                    </li>

                    <li>
                        <a class="waves-effect waves-dark" href="{{ url('/campaign/camp_assign_emp') }}"
                            aria-expanded="false">
                            <i class="fa fa-file"></i><span class="hide-menu">Campaign</span>
                        </a>
                    </li>

                    <li
                            class="{{ request()->is('leads/create') || request()->is('leads/assign_lead_emp') || request()->is('leads/assign_lead_emp/*') || request()->is('leads/unapprovedLeadsemp') ? 'active' : '' }}">
                            <a class="has-arrow waves-effect waves-dark" href="#" data-bs-toggle="collapse"
                                data-bs-target="#leads-collapse-manager"
                                aria-expanded="{{ request()->is('leads/create') || request()->is('leads/assign_lead_emp') || request()->is('leads/unapprovedLeads') || request()->is('leads/assign_lead_emp/*') ? 'true' : 'false' }}">
                                <i class="fa fa-check-square-o"></i>
                                <span class="hide-menu">Leads</span>
                            </a>
                            <div class="collapse {{ request()->is('leads/create') || request()->is('leads/assign_lead_emp') || request()->is('leads/assign_lead_emp/*') || request()->is('leads/unapprovedLeads/*') ? 'show' : '' }}"
                                id="leads-collapse-manager">
                                <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                    <li class="{{ request()->is('leads/create') ? 'active' : '' }}">
                                        <a href="{{ route('leads.create') }}">Add Leads</a>
                                    </li>
                                
                                    <li class="{{ request()->is('leads/unapprovedLeadsemp') ? 'active' : '' }}">
                                        <a href="{{ route('leads.unapprovedLeadsemp') }}">Unapproved Leads</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                    <li>
                        <a class="waves-effect waves-dark"
                            href="{{ url('empdailyreport') }}"
                            aria-expanded="false">
                            <i class="fa fa-id-card-o"></i><span class="hide-menu">Daily Report</span>
                        </a>
                    </li>
                </ul>
            @elseif(auth()->user()->is_admin == MANAGER)
                <ul id="sidebarnav">
                    <li class="nav-devider"></li>
                    <li class="nav-small-cap">Manager Panel</li>

                    @if (auth()->user()->manager_type == 2)
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="{{ url('performanceExternalManager') }}"
                                aria-expanded="false">
                                <i class="fa fa-line-chart"></i><span class="hide-menu">Performance</span>
                            </a>
                        </li>

                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="{{ url('all-leads') }}" aria-expanded="false">
                                <i class="fa fa-check-square-o"></i><span class="hide-menu">All Leads</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a class="waves-effect waves-dark" href="{{ route('home') }}" aria-expanded="false">
                                <i class="fa fa-home"></i><span class="hide-menu">Home</span>
                            </a>
                        </li>

                        <li>
                            <a class="waves-effect waves-dark" href="{{ route('managercallbackleads') }}" aria-expanded="false">
                            <i class="fa fa-phone"></i><span class="hide-menu">Emp. Callback Leads</span>
                            </a>
                        </li>

                        <li
                            class="{{ request()->routeIs('employee.manageremployeeindex', 'employee.createmanageremployees') ? 'active' : '' }}">
                            <a class="has-arrow waves-effect waves-dark {{ request()->routeIs('employee.manageremployeeindex', 'employee.createmanageremployees') ? 'active' : '' }}"
                                href="#" data-bs-toggle="collapse" data-bs-target="#employee-collapse-admin"
                                aria-expanded="{{ request()->routeIs('employee.manageremployeeindex', 'employee.createmanageremployees') ? 'true' : 'false' }}">
                                <i class="fa fa-users"></i>
                                <span class="hide-menu">Add/View Employee</span>
                            </a>
                            <div class="collapse {{ request()->routeIs('employee.*') ? 'show' : '' }}"
                                id="employee-collapse-admin">
                                <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                    <li class="{{ request()->routeIs('employee.manageremployeeindex') ? 'active' : '' }}">
                                        <a href="{{ route('employee.manageremployeeindex') }}">View Employee</a>
                                    </li>
                                    <li class="{{ request()->routeIs('employee.createmanageremployees') ? 'active' : '' }}">
                                        <a href="{{ route('employee.createmanageremployees') }}">Add Employee</a>
                                    </li>
                                </ul>
                            </div>
                        </li>





                        <li class="{{ request()->is('getMangerSource') ? 'active' : '' }}">
                            <a class="has-arrow waves-effect waves-dark" href="#" data-bs-toggle="collapse"
                                data-bs-target="#leads-collapse-manager"
                                aria-expanded="{{ request()->is('getMangerSource') ? 'true' : 'false' }}">
                                <i class="fa fa-address-book"></i>
                                <span class="hide-menu">Campaign</span>
                            </a>
                            <div class="collapse {{ request()->is('getMangerSource') ? 'show' : '' }}"
                                id="leads-collapse-manager">
                                <ul class="list-unstyled fw-normal pb-1 small">
                                    <li class="{{ request()->is('getMangerSource') ? 'active' : '' }}">
                                        <a href="{{ route('sources.getMangerSource') }}">View Campaign</a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li
                            class="{{ request()->is('leads/create') || request()->is('leads/assign_lead_emp') || request()->is('leads/assign_lead_emp/*') || request()->is('leads/unapprovedLeads') || request()->is('leads/employeeclosedleads') ? 'active' : '' }}">
                            <a class="has-arrow waves-effect waves-dark" href="#" data-bs-toggle="collapse"
                                data-bs-target="#leads-collapse-manager"
                                aria-expanded="{{ request()->is('leads/create') || request()->is('leads/assign_lead_emp') || request()->is('leads/unapprovedLeads') || request()->is('leads/assign_lead_emp/*') || request()->is('leads/employeeclosedleads') ? 'true' : 'false' }}">
                                <i class="fa fa-check-square-o"></i>
                                <span class="hide-menu">Add/View Leads</span>
                            </a>
                            <div class="collapse {{ request()->is('leads/create') || request()->is('leads/assign_lead_emp') || request()->is('leads/assign_lead_emp/*') || request()->is('leads/unapprovedLeads') || request()->is('leads/employeeclosedleads') ? 'show' : '' }}"
                                id="leads-collapse-manager">
                                <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                    <li class="{{ request()->is('leads/create') ? 'active' : '' }}">
                                        <a href="{{ route('leads.create') }}">Add Leads</a>
                                    </li>
                                    <li
                                        class="{{ request()->is('leads/assign_lead_emp') || request()->is('leads/assign_lead_emp/*') ? 'active' : '' }}">
                                        <a href="{{ route('leads.assign_lead_emp') }}">Assign Leads</a>
                                    </li>
                                    <li class="{{ request()->is('leads/unapprovedLeads') ? 'active' : '' }}">
                                        <a href="{{ route('leads.unapprovedLeads') }}">Unapproved Leads</a>
                                    </li>
                                    <li class="{{ request()->is('leads/employeeclosedleads') ? 'active' : '' }}">
                                        <a href="{{ route('employeeclosedleads') }}">Emp. Closed Leads</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li>
                            <a class="waves-effect waves-dark" href="{{ url('man_daily_report') }}" aria-expanded="false">
                                <i class="fa fa-id-card-o"></i><span class="hide-menu">Daily Report</span>
                            </a>
                        </li>
                    @endif
                </ul>
            @else
                <ul id="sidebarnav">
                    <li class="nav-devider"></li>
                    <li class="nav-small-cap">Admin Panel</li>

                    <li>
                        <a class="waves-effect waves-dark" href="{{ route('dashboard') }}" aria-expanded="false">
                            <i class="mdi mdi-gauge"></i><span class="hide-menu">Home</span>
                        </a>
                    </li>

                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="{{ url('analysis') }}" aria-expanded="false">
                            <i class="fa fa-line-chart"></i><span class="hide-menu">Analysis</span>
                        </a>
                    </li>

                    {{-- <li>
                        <a class="has-arrow waves-effect waves-dark"
                            href="{{ url('employees_performance?employee_id=&compaign_id=&date_from=&date_to=') }}"
                            aria-expanded="false">
                            <i class="fa fa-line-chart"></i><span class="hide-menu">Performance</span>
                        </a>
                    </li> --}}

                    <li>
                        <a class="has-arrow waves-effect waves-dark collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#manager-collapse" aria-expanded="false">
                            <i class="fa fa-users"></i><span class="hide-menu">Add/View Manager</span>
                        </a>
                        <div class="collapse" id="manager-collapse">
                            <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                <li><a href="{{ route('manager.index') }}">View Manager</a></li>
                                <li><a href="{{ route('manager.create') }}">Add Manager</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a class="has-arrow waves-effect waves-dark collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#employee-collapse" aria-expanded="false">
                            <i class="fa fa-users"></i><span class="hide-menu">Add/View Employee</span>
                        </a>
                        <div class="collapse" id="employee-collapse">
                            <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                <li><a href="{{ route('employee.index') }}">View Employee</a></li>
                                <li><a href="{{ route('employee.create') }}">Add Employee</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a class="has-arrow waves-effect waves-dark collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#campaign-collapse" aria-expanded="false">
                            <i class="fa fa-address-book"></i><span class="hide-menu">Add/View Campaign</span>
                        </a>
                        <div class="collapse" id="campaign-collapse">
                            <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                <li><a href="{{ route('sources.index') }}">View Campaign</a></li>
                                <li><a href="{{ route('sources.create') }}">Add Campaign</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a class="has-arrow waves-effect waves-dark collapsed" href="#" data-bs-toggle="collapse"
                            data-bs-target="#leads-collapse" aria-expanded="false">
                            <i class="fa fa-check-square-o"></i><span class="hide-menu">Add/View Leads</span>
                        </a>
                        <div class="collapse" id="leads-collapse">
                            <ul aria-expanded="false" class="list-unstyled fw-normal pb-1 small">
                                <li><a href="{{ url('leads/create') }}">Add Leads</a></li>
                                <!-- <li><a href="{{ url('leads') }}">View Leads</a></li> -->
                                <li><a href="{{ url('leads/assign') }}">Assign Leads</a></li>
                                <!-- <li><a href="{{ url('leads/view') }}">View Employees Leads</a></li> -->
                                <!-- <li><a href="{{ url('feedbacks') }}">View Notes</a></li> -->
                            </ul>
                        </div>
                    </li>
                    
                    <li>
                            <a class="waves-effect waves-dark" href="{{ url('man_daily_report') }}" aria-expanded="false">
                                <i class="fa fa-id-card-o"></i><span class="hide-menu">Daily Report</span>
                            </a>
                        </li>
                </ul>
            @endif
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll -->
</aside>