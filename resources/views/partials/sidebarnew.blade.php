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

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

/* Only change font for <a> tags inside sidebar */
.sidebar a  {
    font-family: "Poppins", sans-serif !important;
}

.menu-title span p {
    font-family: "Poppins", sans-serif !important;

}

.menu-item .submenu {
    display: none;
}

.menu-item.open .submenu {
    display: block;
}

.menu-title.active {
    background-color: #f0f0f0;
    font-weight: bold;
}

.sidebar .menu-title span p {
    display: inline-block;
    margin-bottom: 0;
}

.sidebar p {
    display: inline-block;
    margin-bottom: 0;
}


</style>
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
            <span><i class="fa-solid fa-user-gear"></i> <p>Manage Submanager</p></span>
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
            <span><i class="fa-solid fa-user-tie"></i> <p>Manage Employee</p></span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
        <a href="{{ route('employee.createmanageremployees') }}" class="{{ request()->routeIs('employee.createmanageremployees') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add Employee
            </a>
            <a href="{{ route('employee.index') }}" class="{{ request()->routeIs('employee.manageremployeeindex') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Employee
            </a>
         
        </div>
    </div>

    <!-- Manage Campaigns -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-bullhorn"></i> <p>Manage Campaigns</p></span>
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
            <span><i class="fa-solid fa-address-card"></i> <p>Manage Leads</p></span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
        <a href="{{ route('leads.create') }}"><i class="fa-solid fa-user-plus"></i>Add Lead</a>
        <a href="{{ route('leads.assign_lead_emp') }}"><i class="fa-solid fa-share-from-square"></i>Assign Lead</a>

        <a href="{{ route('leads.unapprovedLeads') }}"><i class="fas fa-eye"></i>Unapproved Leads</a>
            <a href="{{ route('employeeclosedleads') }}"><i class="fa-solid fa-folder-closed"></i>Emp. Closed Leads</a>
            <a href="{{ route('employeecompletedleads') }}"><i class="fa-solid fa-circle-check"></i>Emp. Completed Leads</a>
        </div>
    </div>


    <!-- Lead Chart -->
    <a href="{{ route('manageleadchart')}}"><i class="fa-solid fa-chart-line"></i> <p>Lead Chart</p></a>

    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}"><i class="fa-solid fa-file-lines"></i> <p>Daily Report</p></a>

    <!-- Logs -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-list"></i> <p>Logs</p></span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employeelogs') }}"><i class="fa-solid fa-users"></i>Employee logs</a>
            <a href="{{ route('managerlogs') }}"><i class="fa-solid fa-user"></i> Myself logs</a>
        </div>
    </div>

</div>
