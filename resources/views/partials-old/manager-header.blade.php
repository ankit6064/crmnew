<!--topbar-->
<style>
    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .notification-container {
        position: relative;
    }

    .notification-link {
        font-size: 22px;
        color: #192e62;
        position: relative;
        text-decoration: none;
        transition: color 0.2s;
        display: flex;
        align-items: center;
    }

    .notification-link:hover {
        color: #0d1b3e;
    }

    .notification-link .badge {
        position: absolute;
        top: -3px;
        right: -6px;
        background: #ff4d4d;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        border: 2px solid #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        pointer-events: none;
        font-family: sans-serif;
    }
</style>

<div class="topbar">
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Revvelocity Logo">
    </div>
    <div class="topbar-right">
        @php
            $unreadCount = Auth::check() ? \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count() : 0;
        @endphp
        <div class="notification-container">
            <a href="{{ route('notifications.index') }}" class="notification-link">
                <i class="fa-solid fa-bell"></i>
                <span class="badge" id="nav-unread-count" {!! $unreadCount > 0 ? '' : 'style="display: none;"' !!}>{{ $unreadCount }}</span>
            </a>
        </div>

        <div class="profile-container">
            <div class="profile-box" id="profileToggle">
                <img src="{{ asset('images/dummyiconimage.png') }}" alt="User Photo" class="profile-img">
                <span class="profile-name">{{ Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : 'Guest' }}</span>
                <span class="dropdown-arrow"><i class="fas fa-chevron-right"></i></span>
            </div>
            <ul class="profile-dropdown" id="profileMenu" style="display: none;">
                <li>
                    <a class="dropdown-item" href="javascript:void(0)"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--topbar-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateNavNotificationCount() {
        $.ajax({
            url: "{{ route('notifications.count') }}",
            type: 'GET',
            success: function (response) {
                if (response.count > 0) {
                    $('#nav-unread-count').text(response.count).show();
                } else {
                    $('#nav-unread-count').hide();
                }
            },
            error: function (err) {
                console.error("Error fetching notification count:", err);
            }
        });
    }

    $(document).ready(function () {
        updateNavNotificationCount();
        // Update every 1 minute
        setInterval(updateNavNotificationCount, 60000);
    });
</script>