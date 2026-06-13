@extends('layouts.admin')

@section('content')

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<style>
    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        height: 50px;
        border-radius: 8px;
    }

    .btn-group {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .btn-save {
        background: #63c100;
        color: #fff;
        padding: 12px 40px;
        border-radius: 10px;
        border: none;
    }

    .btn-cancel {
        background: #ff0000;
        color: #fff;
        padding: 12px 40px;
        border-radius: 10px;
        border: none;
    }

    .popupcenter {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }


    .text-danger {
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }
</style>

<div class="main-right addsubmanager">
    <div class="right-side">
        <h2>Update Manager</h2>

        <div class="graph">
            <form id="managerUpdateForm">
                @csrf
                @method('PUT')

                <!-- Row 1 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" id="first_name" name="first_name"
                               value="{{ $manager->first_name }}"
                               placeholder="Enter First Name">
                        <span class="text-danger error-first_name"></span>
                    </div>

                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                               value="{{ $manager->last_name }}"
                               placeholder="Enter Last Name">
                        <span class="text-danger error-last_name"></span>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" id="phone_no" name="phone_no"
                               value="{{ $manager->phone_no }}"
                               placeholder="Enter Phone Number">
                        <span class="text-danger error-phone_no"></span>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="email" name="email"
                               value="{{ $manager->email }}"
                               placeholder="Enter Email">
                        <span class="text-danger error-email"></span>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" id="address" name="address"
                               value="{{ $manager->address }}"
                               placeholder="Enter Address">
                        <span class="text-danger error-address"></span>
                    </div>

                    <div class="form-group">
                        <label>Manager Type</label>
                        <select id="manager_type" name="manager_type">
                            <option value="">Select Manager Type</option>
                            <option value="1" {{ $manager->manager_type == 1 ? 'selected' : '' }}>Internal</option>
                            <option value="2" {{ $manager->manager_type == 2 ? 'selected' : '' }}>External</option>
                        </select>
                        <span class="text-danger error-manager_type"></span>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="btn-group">
                    <button type="button" class="btn-save" onclick="updateManager();">
                        Update
                    </button>

                    <button type="button" class="btn-cancel" onclick="window.location.href='{{ route('manager.index') }}'">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="popupcenter" id="successModal">
    <div class="popupp">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <p>Manager has been <br> updated successfully.</p>
    </div>
</div>

<script>
function updateManager() {

    $('.text-danger').text('');
    $('input, select').css('border', '');

    if (!$("#managerUpdateForm").valid()) {
        return;
    }

    let formData = new FormData($('#managerUpdateForm')[0]);

    $.ajax({
        url: "{{ route('manager.update', $manager->id) }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val()
        },
        success: function () {
            $('#successModal').css('display', 'flex');

            setTimeout(() => {
                window.location.href = "{{ route('manager.index') }}";
            }, 2000);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;

                $.each(errors, function (key, value) {
                    $('.error-' + key).text(value[0]);
                    $('#' + key).css('border', '1px solid red');
                });
            } else {
                alert('Something went wrong. Please try again.');
            }
        }
    });
}
</script>

@endsection
