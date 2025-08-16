<div class="sidebar">
<a href="{{ url('managerdashboard') }}" 
   class="{{ request()->is('managerdashboard') ? 'active' : '' }} menu-link">
   <i class="fa-solid fa-gauge"></i> Dashboard
</a>


    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-user-gear"></i> Manage Submanagers</span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Submanager</a>
            <a href="#"><i class="fas fa-plus"></i> Add Submanager</a>
        </div>
    </div>
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-user-tie"></i> Manage Employess </span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Submanager</a>
            <a href="#"><i class="fas fa-plus"></i> Add Submanager</a>
        </div>
    </div>
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-bullhorn"></i> Manage Campaigns </span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Submanager</a>
            <a href="#"><i class="fas fa-plus"></i> Add Submanager</a>
        </div>
    </div>
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-address-card"></i>Manage Leads </span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Submanager</a>
            <a href="#"><i class="fas fa-plus"></i> Add Submanager</a>
        </div>
    </div>
    <!-- Normal items -->
    <a href="#"><i class="fa-solid fa-chart-line"></i> Lead Chart</a>
    <a href="#"><i class="fa-solid fa-file-lines"></i> Daily Report</a>
    <div class="menu-item dropdown">
        <div class="menu-title">
            <span><i class="fa-solid fa-list"></i> Logs </span>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="#"><i class="fas fa-eye"></i> View Submanager</a>
            <a href="#"><i class="fas fa-plus"></i> Add Submanager</a>
        </div>
    </div>
</div>