<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Lead Management')</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/common/favicon(32x32).png') }}">
    <!-- <link href="{{ asset('css/guest/stylenew.css') }}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('css/guest/dashboard.css') }}" rel="stylesheet"> -->

    @include('partials.common-style')
    @include('partials.admin-style')
    @stack('styles')
</head>

<body class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
            @include('partials.manager-header')
            <div class="dashboard-screen">
		<div class="container-fluid">
			<div class="main-content">
        @include('partials.sidebarnew')
     
                            <!-- <main> -->
                @yield('content')
            <!-- </main> -->
            <!-- @include('partials.spinner') -->
            <footer>
                @include('partials.footer')
            </footer>
            </div>
            </div>
            </div>
        <!-- </div> -->
    </div>
    @include('partials.common-script')
    @include('partials.admin-script')
    @stack('scripts') <!-- For additional scripts -->
</body>

</html>
