<style>
    .menu-item .submenu {
        display: none;
    }

    .menu-item.open .submenu,
    .menu-item.active .submenu {
        display: block;
    }

    .menu-item.open .arrow i,
    .menu-item.active .arrow i {
        transform: rotate(90deg);
        transition: transform 0.2s ease;
    }

    .menu-title.active {
        background-color: transparent;
        font-weight: 600;
    }
</style>

@php
    $submanagerPermissions = null;
    if (auth()->user()->is_admin == SUBMANAGER) {
        $permRecord = \App\Models\SubmanagerPermissions::where('user_id', auth()->id())->first();
        if ($permRecord) {
            $submanagerPermissions = json_decode($permRecord->user_permissions);
        }
    }
@endphp

<div class="sidebar" id="appSidebar">

@if(auth()->user()->is_admin == MANAGER || auth()->user()->is_admin == SUBMANAGER)

    <!-- Dashboard / Home -->
    @if(auth()->user()->is_admin == MANAGER)
    <a href="{{ url('managerdashboard') }}" class="{{ request()->is('managerdashboard*') ? 'active' : '' }} menu-link" data-title="Dashboard">
        <i class="fa-solid fa-gauge"></i>
        <span class="menu-text">Dashboard</span>
    </a>
    @else
    <a href="{{ route('home') }}" class="{{ request()->is('home*') ? 'active' : '' }} menu-link" data-title="Home">
        <i class="fa-solid fa-house"></i>
        <span class="menu-text">Home</span>
    </a>
    @endif

    <!-- Manage Submanager -->
    @if(auth()->user()->is_admin == MANAGER)
    @php
        $isSubmanagerActive = request()->routeIs('employee.submanagerlisting*');
    @endphp
    <div class="menu-item dropdown {{ $isSubmanagerActive ? 'open' : '' }}" data-title="Manage Submanager">
        <div class="menu-title {{ $isSubmanagerActive ? 'active' : '' }}">
            <div class="menu-title-left">
                <i class="fa-solid fa-user-gear"></i>
                <span class="menu-text">Manage Submanager</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employee.submanagerlisting') }}"
                class="{{ request()->routeIs('employee.submanagerlisting*') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> <span class="sub-text">View Submanager</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Manage Employee -->
    @php
        $isEmployeeActive = request()->routeIs('employee.manageremployeeindex*')
            || request()->routeIs('employee.createmanageremployees*');
    @endphp
    <div class="menu-item dropdown {{ $isEmployeeActive ? 'open' : '' }}" data-title="Manage Employee">
        <div class="menu-title {{ $isEmployeeActive ? 'active' : '' }}">
            <div class="menu-title-left">
                <i class="fa-solid fa-user-tie"></i>
                <span class="menu-text">Manage Employee</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employee.index') }}"
                class="{{ request()->routeIs('employee.manageremployeeindex*') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> <span class="sub-text">View Employee</span>
            </a>
            @if(auth()->user()->is_admin == MANAGER || (!empty($submanagerPermissions) && isset($submanagerPermissions->employee->add) && $submanagerPermissions->employee->add == 1))
            <a href="{{ route('employee.createmanageremployees') }}"
                class="{{ request()->routeIs('employee.createmanageremployees*') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> <span class="sub-text">Add Employee</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Manage Campaigns -->
    @php
        $isCampaignActive = request()->routeIs('sources.*');
    @endphp
    <div class="menu-item dropdown {{ $isCampaignActive ? 'open' : '' }}" data-title="Manage Campaigns">
        <div class="menu-title {{ $isCampaignActive ? 'active' : '' }}">
            <div class="menu-title-left">
                <i class="fa-solid fa-bullhorn"></i>
                <span class="menu-text">Manage Campaigns</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            @if(auth()->user()->is_admin == MANAGER || (!empty($submanagerPermissions) && isset($submanagerPermissions->campaign->add) && $submanagerPermissions->campaign->add == 1))
            <a href="{{ route('sources.create') }}" class="{{ request()->routeIs('sources.create') ? 'active' : '' }}">
                <i class="fas fa-plus"></i> <span class="sub-text">Add Campaign</span>
            </a>
            @endif
            <a href="{{ route('sources.getMangerSource') }}"
                class="{{ request()->routeIs('sources.getMangerSource') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> <span class="sub-text">View Campaign</span>
            </a>
        </div>
    </div>

    <!-- Manage Leads -->
    @php
        $isLeadsActive = request()->routeIs('leads.*')
            || request()->routeIs('employeeclosedleads')
            || request()->routeIs('employeefailedleads')
            || request()->routeIs('employeecompletedleads')
            || request()->routeIs('meeting_scheduled');
    @endphp
    <div class="menu-item dropdown {{ $isLeadsActive ? 'open' : '' }}" data-title="Manage Leads">
        <div class="menu-title {{ $isLeadsActive ? 'active' : '' }}">
            <div class="menu-title-left">
                <i class="fa-solid fa-address-card"></i>
                <span class="menu-text">Manage Leads</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            @if(auth()->user()->is_admin == MANAGER || (!empty($submanagerPermissions) && isset($submanagerPermissions->lead->add) && $submanagerPermissions->lead->add == 1))
            <a href="{{ route('leads.create') }}" class="{{ request()->routeIs('leads.create') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus"></i> <span class="sub-text">Add Lead</span>
            </a>
            @endif

            @if(auth()->user()->is_admin == MANAGER || (!empty($submanagerPermissions) && isset($submanagerPermissions->lead->approve) && $submanagerPermissions->lead->approve == 1))
            <a href="{{ route('leads.assign_lead_emp') }}"
                class="{{ request()->routeIs('leads.assign_lead_emp') ? 'active' : '' }}">
                <i class="fa-solid fa-user-check"></i> <span class="sub-text">Assign Lead</span>
            </a>
            @endif

            <a href="{{ route('leads.unapprovedLeads') }}"
                class="{{ request()->routeIs('leads.unapprovedLeads') ? 'active' : '' }}">
                <i class="fa-solid fa-user-clock"></i> <span class="sub-text">Unapproved Leads</span>
            </a>

            <a href="{{ route('employeeclosedleads') }}"
                class="{{ request()->routeIs('employeeclosedleads') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-xmark"></i> <span class="sub-text">Emp. Closed Leads</span>
            </a>

            <a href="{{ route('employeefailedleads') }}"
                class="{{ request()->routeIs('employeefailedleads') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-minus"></i> <span class="sub-text">Emp. Failed Leads</span>
            </a>

            <a href="{{ route('employeecompletedleads') }}"
                class="{{ request()->routeIs('employeecompletedleads') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-check"></i> <span class="sub-text">Emp. Completed Leads</span>
            </a>

            <a href="{{ route('meeting_scheduled') }}"
                class="{{ request()->routeIs('meeting_scheduled') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i> <span class="sub-text">Active Meetings</span>
            </a>

            <a href="{{ route('leads.search_lead') }}"
                class="{{ request()->routeIs('leads.search_lead') ? 'active' : '' }}">
                <i class="fa-solid fa-magnifying-glass"></i> <span class="sub-text">Search Lead</span>
            </a>
        </div>
    </div>

    <!-- Lead Chart -->
    <a href="{{ route('manageleadchart') }}"
        class="{{ request()->routeIs('manageleadchart') ? 'active' : '' }} menu-link" data-title="Lead Chart">
        <i class="fa-solid fa-chart-line"></i>
        <span class="menu-text">Lead Chart</span>
    </a>

    <!-- Daily Report -->
    @if(auth()->user()->is_admin == MANAGER || auth()->user()->is_admin == SUBMANAGER)
    <a href="{{ url('man_daily_report') }}" class="{{ request()->is('man_daily_report*') ? 'active' : '' }} menu-link" data-title="Daily Report">
        <i class="fa-solid fa-file-lines"></i>
        <span class="menu-text">Daily Report</span>
    </a>
    @endif

    <!-- Notifications -->
    <a href="{{ route('notifications.index') }}"
        class="{{ request()->routeIs('notifications.index') ? 'active' : '' }} menu-link" data-title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span class="menu-text">Notifications</span>
    </a>

    <!-- Logs -->
    @php
        $isLogsActive = request()->routeIs('employeelogs') || request()->routeIs('managerlogs');
    @endphp
    <div class="menu-item dropdown {{ $isLogsActive ? 'open' : '' }}" data-title="Logs">
        <div class="menu-title {{ $isLogsActive ? 'active' : '' }}">
            <div class="menu-title-left">
                <i class="fa-solid fa-list"></i>
                <span class="menu-text">Logs</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employeelogs') }}" class="{{ request()->routeIs('employeelogs') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> <span class="sub-text">Employee logs</span>
            </a>
            @if(auth()->user()->is_admin == MANAGER)
            <a href="{{ route('managerlogs') }}" class="{{ request()->routeIs('managerlogs') ? 'active' : '' }}">
                <i class="fas fa-eye"></i> <span class="sub-text">Myself logs</span>
            </a>
            @endif
        </div>
    </div>

@elseif(auth()->user()->is_admin == USER)

    <!-- Dashboard -->
    <a href="{{ url('employeedashboard') }}"
        class="{{ request()->is('employeedashboard*') ? 'active' : '' }} menu-link" data-title="Dashboard">
        <i class="fa-solid fa-gauge"></i>
        <span class="menu-text">Dashboard</span>
    </a>

    <!-- Campaigns -->
    <a href="{{ route('sources.employeecampaign') }}"
        class="{{ request()->is('sources/employeecampaign*') ? 'active' : '' }} menu-link" data-title="Campaigns">
        <i class="fas fa-bullhorn"></i>
        <span class="menu-text">Campaigns</span>
    </a>

    <!-- Manage Leads -->
    <div class="menu-item dropdown {{ request()->is('leads/*') ? 'active' : '' }}" data-title="Manage Leads">
        <div class="menu-title">
            <div class="menu-title-left">
                <i class="fa-solid fa-address-card"></i>
                <span class="menu-text">Manage Leads</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('leads.create') }}" class="{{ request()->is('leads/create') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus"></i> <span class="sub-text">Add Lead</span>
            </a>
            <a href="{{ route('leads.unapprovedLeadsemp') }}"
                class="{{ request()->is('leads/unapproved*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-clock"></i> <span class="sub-text">Unapproved Leads</span>
            </a>
            <a href="{{ route('leads.search_lead') }}"
                class="{{ request()->routeIs('leads.search_lead') ? 'active' : '' }}">
                <i class="fa-solid fa-magnifying-glass"></i> <span class="sub-text">Search Lead</span>
            </a>
        </div>
    </div>

    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}" class="{{ request()->is('man_daily_report*') ? 'active' : '' }} menu-link" data-title="Daily Report">
        <i class="fa-solid fa-file-lines"></i>
        <span class="menu-text">Daily Report</span>
    </a>

    <!-- Notifications -->
    <a href="{{ route('notifications.index') }}"
        class="{{ request()->routeIs('notifications.index') ? 'active' : '' }} menu-link" data-title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span class="menu-text">Notifications</span>
    </a>

@else

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }} menu-link" data-title="Home">
        <i class="fa-solid fa-gauge"></i>
        <span class="menu-text">Home</span>
    </a>

    <!-- Manager -->
    <div class="menu-item dropdown" data-title="Manager">
        <div class="menu-title">
            <div class="menu-title-left">
                <i class="fa-solid fa-user-shield"></i>
                <span class="menu-text">Manager</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('manager.index') }}">
                <i class="fas fa-eye"></i> <span class="sub-text">View Manager</span>
            </a>
            <a href="{{ route('manager.create') }}">
                <i class="fas fa-plus"></i> <span class="sub-text">Add Manager</span>
            </a>
        </div>
    </div>

    <!-- Submanagers -->
    <a href="{{ route('employee.submanagerlisting') }}"
        class="{{ request()->is('employee/submanagerlisting') ? 'active' : '' }} menu-link" data-title="Submanagers">
        <i class="fa-solid fa-user-gear"></i>
        <span class="menu-text">Submanagers</span>
    </a>

    <!-- Employee -->
    <div class="menu-item dropdown" data-title="Employee">
        <div class="menu-title">
            <div class="menu-title-left">
                <i class="fa-solid fa-user-tie"></i>
                <span class="menu-text">Employee</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('employee.index') }}">
                <i class="fas fa-eye"></i> <span class="sub-text">View Employee</span>
            </a>
            <a href="{{ route('employee.create') }}">
                <i class="fas fa-plus"></i> <span class="sub-text">Add Employee</span>
            </a>
        </div>
    </div>

    <!-- Campaign -->
    <div class="menu-item dropdown" data-title="Campaign">
        <div class="menu-title">
            <div class="menu-title-left">
                <i class="fa fa-address-book"></i>
                <span class="menu-text">Campaign</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ route('sources.index') }}">
                <i class="fas fa-eye"></i> <span class="sub-text">View Campaign</span>
            </a>
            <a href="{{ route('sources.create') }}">
                <i class="fas fa-plus"></i> <span class="sub-text">Add Campaign</span>
            </a>
        </div>
    </div>

    <!-- Leads -->
    <div class="menu-item dropdown" data-title="Leads">
        <div class="menu-title">
            <div class="menu-title-left">
                <i class="fa-solid fa-address-card"></i>
                <span class="menu-text">Leads</span>
            </div>
            <span class="arrow"><i class="fas fa-chevron-right"></i></span>
        </div>
        <div class="submenu">
            <a href="{{ url('leads/create') }}">
                <i class="fas fa-plus"></i> <span class="sub-text">Add Leads</span>
            </a>
            <a href="{{ url('leads/assign') }}">
                <i class="fas fa-user-check"></i> <span class="sub-text">Assign Leads</span>
            </a>
        </div>
    </div>

    <!-- Daily Report -->
    <a href="{{ url('man_daily_report') }}" class="{{ request()->is('man_daily_report') ? 'active' : '' }} menu-link" data-title="Daily Report">
        <i class="fa-solid fa-file-lines"></i>
        <span class="menu-text">Daily Report</span>
    </a>

    <!-- Notifications -->
    <a href="{{ route('notifications.index') }}"
        class="{{ request()->routeIs('notifications.index') ? 'active' : '' }} menu-link" data-title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span class="menu-text">Notifications</span>
    </a>

@endif

</div>

<!-- Floating Elements for Collapsed Sidebar -->
<div id="sidebarFlyoutMenu" class="sidebar-flyout" style="display: none;"></div>
<div id="sidebarFloatingTooltip" class="sidebar-tooltip" style="display: none;"></div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const topbarToggleBtn = document.getElementById("topbarSidebarToggleBtn");
        const flyout = document.getElementById("sidebarFlyoutMenu");
        const tooltip = document.getElementById("sidebarFloatingTooltip");
        let flyoutHideTimer = null;

        if (topbarToggleBtn) {
            topbarToggleBtn.addEventListener("click", function (e) {
                e.preventDefault();
                hideFlyoutImmediate();
                hideTooltipImmediate();

                const isCurrentlyCollapsed = document.body.classList.toggle("sidebar-collapsed");
                try {
                    localStorage.setItem("sidebar_collapsed", isCurrentlyCollapsed ? "true" : "false");
                } catch (err) {}

                // Trigger window resize so Chart.js, DataTables, and responsive layouts adjust smoothly
                setTimeout(function () {
                    window.dispatchEvent(new Event("resize"));
                    if (window.jQuery) {
                        window.jQuery(window).trigger("resize");
                    }
                }, 300);
            });
        }

        function hideFlyoutImmediate() {
            clearTimeout(flyoutHideTimer);
            if (flyout) flyout.style.display = "none";
        }

        function hideTooltipImmediate() {
            if (tooltip) tooltip.style.display = "none";
        }

        // Hover handling for Flyout in collapsed mode (Dropdown menus only)
        const dropdownItems = document.querySelectorAll("#appSidebar .menu-item.dropdown");
        dropdownItems.forEach(item => {
            item.addEventListener("mouseenter", function () {
                if (!document.body.classList.contains("sidebar-collapsed")) return;
                hideTooltipImmediate();
                clearTimeout(flyoutHideTimer);

                const titleText = item.getAttribute("data-title") || "";
                const iconClass = (item.querySelector(".menu-title i") || {}).className || "fa-solid fa-folder";
                const submenuLinks = item.querySelectorAll(".submenu a");

                if (!submenuLinks.length) return;

                let linksHtml = "";
                submenuLinks.forEach(link => {
                    const href = link.getAttribute("href") || "#";
                    const linkIcon = (link.querySelector("i") || {}).className || "fas fa-chevron-right";
                    const linkText = link.textContent.trim();
                    const isActive = link.classList.contains("active") ? "active" : "";
                    linksHtml += `<a href="${href}" class="${isActive}"><i class="${linkIcon}"></i><span>${linkText}</span></a>`;
                });

                flyout.innerHTML = `
                    <div class="sidebar-flyout-header">
                        <i class="${iconClass}"></i>
                        <span>${titleText}</span>
                    </div>
                    ${linksHtml}
                `;

                const rect = item.getBoundingClientRect();
                flyout.style.display = "block";
                const flyoutHeight = flyout.offsetHeight;
                const windowHeight = window.innerHeight;

                let top = rect.top;
                if (top + flyoutHeight > windowHeight - 15) {
                    top = Math.max(15, windowHeight - flyoutHeight - 15);
                }

                flyout.style.top = top + "px";
                flyout.style.left = (rect.right + 8) + "px";
            });

            item.addEventListener("mouseleave", function () {
                if (!document.body.classList.contains("sidebar-collapsed")) return;
                flyoutHideTimer = setTimeout(function () {
                    hideFlyoutImmediate();
                }, 160);
            });
        });

        // Keep flyout open when mouse is inside it
        if (flyout) {
            flyout.addEventListener("mouseenter", function () {
                clearTimeout(flyoutHideTimer);
            });
            flyout.addEventListener("mouseleave", function () {
                flyoutHideTimer = setTimeout(function () {
                    hideFlyoutImmediate();
                }, 160);
            });
        }

        // Hover tooltip for single menu-links in collapsed mode
        const menuLinks = document.querySelectorAll("#appSidebar .menu-link");
        menuLinks.forEach(link => {
            link.addEventListener("mouseenter", function () {
                if (!document.body.classList.contains("sidebar-collapsed")) return;
                hideFlyoutImmediate();

                const titleText = link.getAttribute("data-title") || link.textContent.trim();
                if (!titleText || !tooltip) return;

                tooltip.textContent = titleText;
                tooltip.style.display = "block";

                const rect = link.getBoundingClientRect();
                const tipHeight = tooltip.offsetHeight;
                const top = rect.top + (rect.height / 2) - (tipHeight / 2);

                tooltip.style.top = top + "px";
                tooltip.style.left = (rect.right + 8) + "px";
            });

            link.addEventListener("mouseleave", function () {
                if (!document.body.classList.contains("sidebar-collapsed")) return;
                hideTooltipImmediate();
            });
        });

        // Hide floating elements on window scroll or sidebar scroll
        window.addEventListener("scroll", function () {
            hideFlyoutImmediate();
            hideTooltipImmediate();
        }, true);
    });
</script>