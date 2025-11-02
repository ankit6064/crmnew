<style> .menu-item .submenu { display: none; } .menu-item.open .submenu { display: block; } .menu-title.active { background-color: #f0f0f0; /* or your highlight color */ font-weight: bold; } </style>
<div class="sidebar">

    <!-- Dashboard -->
    <a href="{{ url('managerdashboard') }}" 
       class="menu-link {{ request()->is('managerdashboard') ? 'active' : '' }}">
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
            <a href="{{ route('employee.manageremployeeindex') }}" 
               class="{{ request()->routeIs('employee.manageremployeeindex') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Employee
            </a>
            <a href="{{ route('employee.createmanageremployees') }}" 
               class="{{ request()->routeIs('employee.createmanageremployees') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>
    </div>

    <!-- Manage Campaigns -->
    @php
        $isCampaignActive = request()->routeIs('sources.create') || request()->routeIs('sources.getMangerSource');
    @endphp
    <div class="menu-item dropdown {{ $isCampaignActive ? 'open' : '' }}">
        <div class="menu-title {{ $isCampaignActive ? 'active' : '' }}">
            <span><i class="fa-solid fa-bullhorn"></i> Manage Campaigns</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('sources.create') }}" class="{{ request()->routeIs('sources.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> Add Campaign
            </a>
            <a href="{{ route('sources.getMangerSource') }}" class="{{ request()->routeIs('sources.getMangerSource') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> View Campaign
            </a>
        </div>
    </div>

    <!-- Manage Leads -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-address-card"></i> Manage Leads</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Leads</a>
            <a href="#"><i class="fas fa-plus"></i> Add Lead</a>
        </div>
    </div>

    <!-- Lead Chart -->
    <a href="{{ route('manageleadchart') }}" class="menu-link {{ request()->routeIs('manageleadchart') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line"></i> Lead Chart
    </a>

    <!-- Daily Report -->
    <a href="#" class="menu-link">
        <i class="fa-solid fa-file-lines"></i> Daily Report
    </a>

    <!-- Logs -->
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-list"></i> Logs</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Logs</a>
            <a href="#"><i class="fas fa-plus"></i> Add Log</a>
        </div>
    </div>

</div>

<!-- ✅ Add simple JS to toggle dropdowns -->
<script>
    document.querySelectorAll('.menu-title').forEach(title => {
        title.addEventListener('click', () => {
            const parent = title.parentElement;
            parent.classList.toggle('open');
        });
    });
</script>
