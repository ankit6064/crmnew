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
       class="{{ request()->is('managerdashboard*') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>


    <!-- Manage Submanager -->
    @php
        $isSubmanagerActive = request()->routeIs('employee.submanagerlisting*');
    @endphp

    <div class="menu-item dropdown {{ $isSubmanagerActive ? 'open' : '' }}">
        <div class="menu-title {{ $isSubmanagerActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-user-gear"></i> Manage Submanager</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>

        <div class="submenu">
            <a href="{{ route('employee.submanagerlisting') }}"
               class="{{ request()->routeIs('employee.submanagerlisting*') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Submanager
            </a>
        </div>
    </div>


    <!-- Manage Employee -->
    @php
        $isEmployeeActive = request()->routeIs('employee.manageremployeeindex*') 
                         || request()->routeIs('employee.createmanageremployees*');
    @endphp

    <div class="menu-item dropdown {{ $isEmployeeActive ? 'open' : '' }}">
        <div class="menu-title {{ $isEmployeeActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-user-tie"></i> Manage Employee</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>

        <div class="submenu">
            <a href="{{ route('employee.index') }}"
               class="{{ request()->routeIs('employee.manageremployeeindex*') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Employee
            </a>

            <a href="{{ route('employee.createmanageremployees') }}"
               class="{{ request()->routeIs('employee.createmanageremployees*') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>
    </div>


    <!-- Manage Campaigns -->
    @php
        $isCampaignActive = request()->routeIs('sources.*');
    @endphp

    <div class="menu-item dropdown {{ $isCampaignActive ? 'open' : '' }}">
        <div class="menu-title {{ $isCampaignActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-bullhorn"></i> Manage Campaigns</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>

        <div class="submenu">
            <a href="{{ route('sources.create') }}"
               class="{{ request()->routeIs('sources.create') ? 'active' : '' }}">
               <i class="fas fa-plus"></i> Add Campaign
            </a>

            <a href="{{ route('sources.getMangerSource') }}"
               class="{{ request()->routeIs('sources.getMangerSource') ? 'active' : '' }}">
               <i class="fas fa-eye"></i> View Campaign
            </a>
        </div>
    </div>


    <!-- Manage Leads -->
    @php
        $isLeadsActive = request()->routeIs('leads.*')
                       || request()->routeIs('employeeclosedleads')
                       || request()->routeIs('employeecompletedleads');
    @endphp

    <div class="menu-item dropdown {{ $isLeadsActive ? 'open' : '' }}">
        <div class="menu-title {{ $isLeadsActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-address-card"></i> Manage Leads</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>

        <div class="submenu">

            <a href="{{ route('leads.create') }}"
               class="{{ request()->routeIs('leads.create') ? 'active' : '' }}">
               <i class="fa-solid fa-user-plus"></i> Add Lead
            </a>

            <a href="{{ route('leads.assign_lead_emp') }}"
               class="{{ request()->routeIs('leads.assign_lead_emp') ? 'active' : '' }}">
               <i class="fa-solid fa-user-check"></i> Assign Lead
            </a>

            <a href="{{ route('leads.unapprovedLeads') }}"
               class="{{ request()->routeIs('leads.unapprovedLeads') ? 'active' : '' }}">
               <i class="fa-solid fa-user-clock"></i> Unapproved Leads
            </a>

            <a href="{{ route('employeeclosedleads') }}"
               class="{{ request()->routeIs('employeeclosedleads') ? 'active' : '' }}">
               <i class="fa-solid fa-circle-xmark"></i> Emp. Closed Leads
            </a>

            <a href="{{ route('employeecompletedleads') }}"
               class="{{ request()->routeIs('employeecompletedleads') ? 'active' : '' }}">
               <i class="fa-solid fa-circle-check"></i> Emp. Completed Leads
            </a>

        </div>
    </div>


    <!-- Lead Chart -->
    <a href="{{ route('manageleadchart') }}"
       class="{{ request()->routeIs('manageleadchart') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-chart-line"></i> Lead Chart
    </a>


    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}"
       class="{{ request()->is('man_daily_report*') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-file-lines"></i> Daily Report
    </a>


    <!-- Notifications -->
    <a href="{{ route('notifications.index') }}"
       class="{{ request()->routeIs('notifications.index') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-bell"></i> Notifications
    </a>


    <!-- Logs -->
    @php
        $isLogsActive = request()->routeIs('employeelogs') || request()->routeIs('managerlogs');
    @endphp

    <div class="menu-item dropdown {{ $isLogsActive ? 'open' : '' }}">
        <div class="menu-title {{ $isLogsActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-list"></i> Logs</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>

        <div class="submenu">
            <a href="{{ route('employeelogs') }}"
               class="{{ request()->routeIs('employeelogs') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> Employee logs
            </a>

            <a href="{{ route('managerlogs') }}"
               class="{{ request()->routeIs('managerlogs') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> Myself logs
            </a>
        </div>
    </div>

</div>

@elseif(auth()->user()->is_admin == USER)

<div class="sidebar">

    <!-- Dashboard -->
    <a href="{{ url('employeedashboard') }}" 
       class="{{ request()->is('employeedashboard*') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <!-- Campaigns -->
    <a href="{{ route('sources.employeecampaign') }}" 
       class="{{ request()->is('sources/employeecampaign*') ? 'active' : '' }} menu-link">
       <i class="fas fa-bullhorn"></i> Campaigns
    </a>
 
    <!-- Manage Leads -->
    <div class="menu-item dropdown {{ request()->is('leads/*') ? 'active' : '' }}">
        <div class="menu-title">
            <span><i class="fa-solid fa-address-card"></i> Manage Leads</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>

        <div class="submenu">

            <a href="{{ route('leads.create') }}"
               class="{{ request()->is('leads/create') ? 'active' : '' }}">
               <i class="fa-solid fa-user-plus"></i> Add Lead
            </a>

            <a href="{{ route('leads.unapprovedLeadsemp') }}"
               class="{{ request()->is('leads/unapproved*') ? 'active' : '' }}">
               <i class="fa-solid fa-user-clock"></i> Unapproved Leads
            </a>

        </div>
    </div>

    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}" 
       class="{{ request()->is('man_daily_report*') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-file-lines"></i> Daily Report
    </a>

    <!-- Notifications -->
    <a href="{{ route('notifications.index') }}"
       class="{{ request()->routeIs('notifications.index') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-bell"></i> Notifications
    </a>

</div>



@else

<div class="sidebar">

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-gauge"></i> Home
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

     <!-- Submanagers -->
     <a href="{{ route('employee.submanagerlisting') }}"
       class="{{ request()->is('employee/submanagerlisting') ? 'active' : '' }} menu-link">
        <i class="fa fa-users"></i> Submanagers
    </a>

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
            <span><i class="fa-solid fa-address-card"></i> Leads</span>
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

    <!-- Notifications -->
    <a href="{{ route('notifications.index') }}"
       class="{{ request()->routeIs('notifications.index') ? 'active' : '' }} menu-link">
        <i class="fa-solid fa-bell"></i> Notifications
    </a>

</div>


@endif


