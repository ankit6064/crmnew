<nav class="navbar top-navbar navbar-expand-md navbar-light">
    <style>
        .swal2-confirm.swal-confirm-button {
            background-color: #192e62 !important;
            color: white !important;
            border: none;
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <b>
                {{-- <img src="{{ asset('img/common/relocity_logo.png') }}" alt="homepage" class="dark-logo" /> --}}
                {{-- <img src="{{ asset('img/common/relocity_logo.png') }}" class="light-logo" alt="homepage" /> --}}
            </b>
        </a>
    </div>
    <div class="navbar-collapse">
        <ul class="navbar-nav mr-auto mt-md-0">
            <!-- Left-aligned items here -->
            <li class="nav-item">
                <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                    href="javascript:void(0)">
                    <i class="mdi mdi-menu"></i>
                </a>
            </li>
            <li class="nav-item m-l-10">
                <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
                    href="javascript:void(0)">
                    <i class="ti-menu"></i>
                </a>
            </li>
            <!-- More left-aligned items -->
        </ul>
      <input type="hidden" name="usertype" id="usertype" value="{{ Auth::user()->is_admin }}">
        <ul class="navbar-nav my-lg-0 ml-auto">
            <!-- Right-aligned items -->
            <li class="bell-area">
                <a id="notf_user" class="dropdown-toggle-1" href="javascript:;" data-href="">
                    <img class="bell_icon" src="/img/common/bell.png">
                    <span id="user-notf-count">250</span>
                </a>
                <div class="dropdown-menu_new" style="display: none;">
                    <div class="dropdownmenu-wrapper" data-href="" id="user-notf-show">
                        <a class="clear">New Notification(s).
                            <span id="user-notf-clear" class="clear-notf" data-href="">
                                Clear All
                            </span>
                        </a>
                        <ul class="notification_data">
                            <li>
                                <a href="javascript:void(0);">No New Notification Found.</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="#" id="userDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset('img/common/default_green.png') }}" alt="user" class="profile-pic">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li>
                        <div class="d-flex align-items-center p-3">
                            <img src="{{ asset('img/common/default_green.png') }}" alt="user"
                                class="rounded-circle me-2" style="width: 50px; height: 50px;">
                            <div>
                                <h5 class="mb-0">admin admin</h5>
                                <p class="text-muted mb-0">admin@gmail.com</p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="http://203.190.154.131/profile">Edit Profile</a></li>
                    <!-- Additional items can be added here if needed -->
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0)"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>

        </ul>
    </div>

</nav>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function checkPendingLeads() {
        let _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '{{url("leads/checkpendingcallback")}}',
            type: "POST",
            dataType: "json",
            data: {
                id: {{Auth::id()}},
                _token: _token,
            },
            success: function (response) {
                if (response.status == 200) {
                    Swal.fire({
                        title: 'Pending Callbacks!',
                        text: `You have ${response.data} pending callbacks for today.`,
                        icon: 'info',
                        iconColor: '#192e62',
                        showCancelButton: true,
                        confirmButtonText: 'View Details',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-button'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/leads/callbackleads'; // replace with your target URL
                        }
                    });
                }

            },
        });
    }

    function checkPendingLeadsManager() {
        let _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '{{url("leads/checkpendingcallbackmanager")}}',
            type: "POST",
            dataType: "json",
            data: {
                id: {{Auth::id()}},
                _token: _token,
            },
            success: function (response) {
                if (response.status == 200) {
                    Swal.fire({
                        title: 'Passed Callbacks!',
                        text: `Your employee have ${response.data} passed callbacks for today.`,
                        icon: 'info',
                        iconColor: '#192e62',
                        showCancelButton: true,
                        confirmButtonText: 'View Details',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'swal-confirm-button'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/leads/managercallbackleads?status=passed'; // replace with your target URL
                        }
                    });
                }

            },
        });
    }
    
    $(document).ready(function () {
        var checkleads;

        if ($('#usertype').val() == 1) {
            checkleads = checkPendingLeads;
        } else if ($('#usertype').val() == 2) {
            checkleads = checkPendingLeadsManager;
        }

        // Call it once immediately
        checkleads();

        // Then call every 5 minutes
        setInterval(checkleads, 3 * 60 * 1000);
    });
</script>