<style>
    .menu-item .submenu {
    display: none;
}

.menu-item.open .submenu {
    display: block;
}

.menu-title.active {
    background-color: #f0f0f0; /* or your highlight color */
    font-weight: bold;
}

</style>
@if(auth()->user()->is_admin == MANAGER)

<div class="sidebar">

    <!-- Dashboard -->
    <a href="{{ url('managerdashboard') }}" 
       class="{{ request()->is('managerdashboard') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <!-- Manage Submanager -->
    @php
        $isSubmanagerActive = request()->routeIs('employee.submanagerlisting');
    @endphp
    <div class="menu-item dropdown {{ $isSubmanagerActive ? 'open' : '' }}">
        <div class="menu-title {{ $isSubmanagerActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-user-gear"></i> Manage Submanager</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employee.submanagerlisting') }}"
               class="{{ request()->routeIs('employee.submanagerlisting') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Submanager
            </a>
        </div>
    </div>

    <!-- Manage Employee -->
    @php
        $isEmployeeActive = request()->routeIs('employee.manageremployeeindex') || request()->routeIs('employee.createmanageremployees');
    @endphp
    <div class="menu-item dropdown {{ $isEmployeeActive ? 'open' : '' }}">
        <div class="menu-title {{ $isEmployeeActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-user-tie"></i> Manage Employee</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employee.index') }}" class="{{ request()->routeIs('employee.manageremployeeindex') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Employee
            </a>
            <a href="{{ route('employee.createmanageremployees') }}" class="{{ request()->routeIs('employee.createmanageremployees') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Manage Campaigns -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-bullhorn"></i> Manage Campaigns</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('sources.create') }}"><i class="fas fa-plus"></i>Add Campaign</a>
            <a href="{{ route('sources.getMangerSource') }}"><i class="fas fa-eye"></i>View Campaign</a>
        </div>
    </div>

    <!-- Manage Leads -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-address-card"></i> Manage Leads</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
        <a href="{{ route('leads.create') }}"><i class="fa-solid fa-user-plus"></i>Add Lead</a>
        <a href="{{ route('leads.assign_lead_emp') }}"><i class="fa-solid fa-user-check"></i>Assign Lead</a>
        
        <a href="{{ route('leads.unapprovedLeads') }}"><i class="fa-solid fa-user-clock"></i>Unapproved Leads</a>

            <a href="{{ route('employeeclosedleads') }}"><i class="fa-solid fa-circle-xmark"></i>Emp. Closed Leads</a>
            <a href="{{ route('employeecompletedleads') }}"><i class="fa-solid fa-circle-check"></i>Emp. Completed Leads</a>
        </div>
    </div>

    <!-- Lead Chart -->
    <a href="{{ route('manageleadchart')}}"><i class="fa-solid fa-chart-line"></i> Lead Chart</a>

    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}"><i class="fa-solid fa-file-lines"></i> Daily Report</a>

    <!-- Logs -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-list"></i> Logs</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employeelogs') }}"><i class="fas fa-eye"></i>Employee logs</a>
            <a href="{{ route('managerlogs') }}"><i class="fas fa-eye"></i> Myself logs</a>
        </div>
    </div>

</div>

@elseif(auth()->user()->is_admin == USER)

<div class="sidebar">

    <!-- Dashboard -->
    <a href="{{ url('employeedashboard') }}" 
       class="{{ request()->is('employeedashboard') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

 

    <a href="{{ route('sources.getMangerSource') }}"><i class="fas fa-eye"></i>Campaigns</a>


 
    <!-- Manage Leads -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-address-card"></i> Manage Leads</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
        <a href="{{ route('leads.create') }}"><i class="fas fa-plus"></i>Add Lead</a>        
        <a href="{{ route('leads.unapprovedLeads') }}"><i class="fas fa-eye"></i>Unapproved Leads</a>
        </div>
    </div>


    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}"><i class="fa-solid fa-file-lines"></i> Daily Report</a>



</div>



@else

<div class="sidebar">

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-gauge"></i> Home
    </a>

    <!-- Analysis -->
    <a href="{{ url('analysis') }}"
       class="{{ request()->is('analysis') ? 'active' : '' }} menu-link">
        <i class="fa fa-line-chart"></i> Analysis
    </a>

    <!-- Manager -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa fa-users"></i> Manager</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('manager.index') }}">
                <i class="fas fa-eye"></i> View Manager
            </a>
            <a href="{{ route('manager.create') }}">
                <i class="fas fa-plus"></i> Add Manager
            </a>
        </div>
    </div>

    <!-- Employee -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa fa-users"></i> Employee</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employee.index') }}">
                <i class="fas fa-eye"></i> View Employee
            </a>
            <a href="{{ route('employee.create') }}">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Campaign -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa fa-address-book"></i> Campaign</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('sources.index') }}">
                <i class="fas fa-eye"></i> View Campaign
            </a>
            <a href="{{ route('sources.create') }}">
                <i class="fas fa-plus"></i> Add Campaign
            </a>
        </div>
    </div>

    <!-- Leads -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa fa-check-square-o"></i> Leads</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ url('leads/create') }}">
                <i class="fas fa-plus"></i> Add Leads
            </a>
            <a href="{{ url('leads/assign') }}">
                <i class="fas fa-user-check"></i> Assign Leads
            </a>
        </div>
    </div>

    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}"
       class="{{ request()->is('man_daily_report') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-file-lines"></i> Daily Report
    </a>

</div>


@endif


