<!-- Scripts -->
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- jQuery and DataTables JS -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<!-- Switchery JS -->
<script src="{{ asset('vendor/switchery/js/switchery.min.js') }}"></script>
<!-- Popper -->
<script src="{{ asset('vendor/popper/js/popper.min.js') }}"></script>
<!-- Tippy -->
<script src="{{ asset('vendor/tippy/js/tippy-bundle.umd.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>
<!-- Sweetalert2-->
<script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
<script src="https://unpkg.com/wavesurfer.js/dist/wavesurfer.min.js"></script>
<!--stickey kit -->
{{-- <script src="{{ asset('vendor/sticky-kit-master/js/sticky-kit.min.js') }}"></script> --}}
<script src="{{ asset('js/common.js') }}"></script>
{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
<!-- Laravel Javascript Validation -->
<script type="text/javascript" src="{{ url('vendor/jsvalidation/js/jsvalidation.js') }}"></script>

<!-- Dynamic JS Validation Script Injection -->
@if (isset($request))
    @php
        // Dynamically build the full request class name and inject JS validation
        $requestClass = 'App\\Http\\Requests\\' . $request;
    @endphp

    {!! JsValidator::formRequest($requestClass) !!}
@endif
<script>
    // Waves.init();
    // Waves.attach('.nav-link', ['waves-effect', 'waves-dark']);
    // Ensure jQuery is loaded before running this code
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // $('.switchery').each(function() {
        //     new Switchery(this, {
        //         color: '#1AB394',
        //         secondaryColor: '#f9f9f9'
        //     });
        // });
    });
</script>
<!-- Toastr options-->
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @elseif (session('error'))
            toastr.error("{{ session('error') }}");
        @elseif (session('info'))
            toastr.info("{{ session('info') }}");
        @elseif (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    });
</script>

<!-- Sweetalert2-->

<script>
    // const Toast = Swal.mixin({
    //     toast: true,
    //     position: "top-end",
    //     showConfirmButton: false,
    //     timer: 3000,
    //     timerProgressBar: true,
    //     didOpen: (toast) => {
    //         toast.onmouseenter = Swal.stopTimer;
    //         toast.onmouseleave = Swal.resumeTimer;
    //     }
    // });
    // document.addEventListener('DOMContentLoaded', function() {
    //     @if (session('success'))
    //         Swal.fire({
    //             toast: true,
    //             title: 'Success!',
    //             text: "{{ session('success') }}",
    //             icon: 'success',
    //             confirmButtonText: 'OK'
    //         });
    //     @elseif (session('error'))
    //         Swal.fire({
    //             title: 'Error!',
    //             text: "{{ session('error') }}",
    //             icon: 'error',
    //             confirmButtonText: 'OK'
    //         });
    //     @elseif (session('info'))
    //         Swal.fire({
    //             title: 'Information',
    //             text: "{{ session('info') }}",
    //             icon: 'info',
    //             confirmButtonText: 'OK'
    //         });
    //     @elseif (session('warning'))
    //         Swal.fire({
    //             title: 'Warning!',
    //             text: "{{ session('warning') }}",
    //             icon: 'warning',
    //             confirmButtonText: 'OK'
    //         });
    //     @endif
    // });
</script>
<!-- Submit logout form -->
<script>
    function logout() {
        $('form#logout-form').submit();
    }
</script>
