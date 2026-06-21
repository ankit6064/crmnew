<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/common/favicon(32x32).png') }}">
    <!-- Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
    <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <link href="{{ asset('css/guest/stylenew.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div class="cuver-bg"></div>
    <div class="login-bg">
        <div class="container">
            <!-- Left Panel -->
            <div class="col-md-4 left-panel">
                <img src="{{ asset('images/left-img.png') }}" alt="Illustration" class="illustration">
                <p>At Revvelocity, we combine our passion for sales with our extensive experience – helping our clients
                    leapfrog ahead to become bigger and stronger.</p>
            </div>

            <!-- Right Panel -->
            <div class="col-md-8 right-panel">
                <div class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Revvelocity Logo">
                </div>
                <h2>Welcome Back</h2>
                <div class="subtitle">
                    Enter your email address & password to login
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    @if(Session::has('status'))
                        <div class="alert alert-success">{{Session::get('status')}}</div>
                    @endif
                    <input id="email" type="email" class=" @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" placeholder="Enter Your Email" required autocomplete="email"
                        autofocus>

                    <div id="role-container" style="display: none; margin-bottom: 15px;">
                        <select name="role" id="role" disabled style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 10px; font-size: 14px; box-shadow: 0 3px 10px #dddddd52; background-color: #fff; font-family: 'Poppins', sans-serif;">
                            <option value="3">Login as Submanager</option>
                            <option value="1">Login as Employee</option>
                        </select>
                    </div>

                        <div style="position: relative;">
    <input id="password" type="password"
           class="@error('password') is-invalid @enderror"
           name="password" placeholder="Enter Your Password"
           required autocomplete="current-password"
           style="padding-right: 40px;">

    <span onclick="togglePassword()" style="
        position: absolute;
        right: 20px;
        top: 40%;
        transform: translateY(-50%);
        cursor: pointer;
        user-select: none;">
        <i id="eyeIcon" class="fa-solid fa-eye"></i>
    </span>
</div>



                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <div class="remember">
                        <input type="checkbox" id="remember">
                        <label for="remember">Remember Me</label>
                    </div>
                    <button class="btn">
                        LOGIN
                    </button>
                </form>
            </div>
        </div>
    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
        integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ"
        crossorigin="anonymous"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"
        integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd"
        crossorigin="anonymous"></script>
</body>
<script>
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    const isPasswordHidden = passwordInput.type === "password";

    passwordInput.type = isPasswordHidden ? "text" : "password";

    // Toggle eye / eye-slash icon
    eyeIcon.classList.toggle("fa-eye");
    eyeIcon.classList.toggle("fa-eye-slash");
}

$(document).ready(function() {
    function checkEmailRole() {
        var email = $('#email').val();
        if (email) {
            $.ajax({
                url: "{{ route('check-submanager') }}",
                type: "GET",
                data: { email: email },
                success: function(response) {
                    if (response.is_submanager) {
                        $('#role').prop('disabled', false);
                        $('#role-container').slideDown();
                    } else {
                        $('#role').prop('disabled', true);
                        $('#role-container').slideUp();
                    }
                },
                error: function() {
                    $('#role').prop('disabled', true);
                    $('#role-container').slideUp();
                }
            });
        } else {
            $('#role').prop('disabled', true);
            $('#role-container').slideUp();
        }
    }

    // Check on load
    checkEmailRole();

    // Check on change, blur, or input (with debounce)
    var roleCheckTimeout;
    $('#email').on('change blur input', function() {
        clearTimeout(roleCheckTimeout);
        roleCheckTimeout = setTimeout(checkEmailRole, 300);
    });
});
</script>


</html>