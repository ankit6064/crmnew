@extends('layouts.admin')
@section('content')

  <style>
    .message-box {
      padding: 15px 20px;
      margin: 15px 0;
      border-radius: 5px;
      font-size: 16px;
      font-weight: 500;
      color: #fff;
      background-color: red;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
      transition: all 0.3s ease;
    }

    /* --- Filter Card Styles --- */
    .stat-card {
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-card.active-card {
      border-color: #192e62;
      background-color: #f0f4ff;
    }

    /* --- Icon Cursor Styles --- */
    .fa,
    .fas,
    .fa-solid,
    .fa-regular {
      cursor: pointer !important;
    }

    table.employee-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      font-family: Arial, sans-serif;
    }

    .employee-table th,
    .employee-table td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    .employee-table th {
      background-color: #f2f2f2;
      font-weight: bold;
    }

    .employee-table tr:nth-child(even) {
      background-color: #fafafa;
    }

    .employee-table tr:hover {
      background-color: #f1f1f1;
    }

    .employee-table input[type="checkbox"] {
      transform: scale(1.2);
      cursor: pointer;
    }

    .view_emp {
      cursor: pointer;
      color: blue;
      font-weight: bold;
    }
  </style>

  <div class="main-right">
    <div class="right-side">
      <div class="row">
        <div class="row align-items-center mb-3">
          <div class="col-md-8">
            <h2 class="mb-0">Employee Listing</h2>
          </div>

          <div class="col-md-4 text-end">
            <button type="button" class="btn return-btn"
              onclick="window.history.back() || (window.location.href='/managerdashboard');">
              <i class="fas fa-arrow-left me-2"></i> Back
            </button>
          </div>
        </div>

      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="stat-card filter-card active-card" data-filter="total">
            <div class="card-header">
              <h4>Total</h4>
              <div class="card-icon">
                <i class="fa-solid fa-users"></i>
              </div>
            </div>
            <div class="card-body">
              <h2>{{$total}}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card filter-card" data-filter="active">
            <div class="card-header">
              <h4>Active</h4>
              <div class="card-icon acti">
                <i class="fa-regular fa-user"></i>
              </div>
            </div>
            <div class="card-body">
              <h2>{{$active}}</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card filter-card" data-filter="inactive">
            <div class="card-header">
              <h4>Inactive</h4>
              <div class="card-icon inactive">
                <i class="fa-solid fa-user-tie"></i>
              </div>
            </div>
            <div class="card-body">
              <h2>{{$deactive}}</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="assignsubmanager" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Create Sub Manager</h5>
              <button type="button" class="close" data-dismiss="modal" onclick="closemodal()">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div id="submanagerbody" class="modal-body"></div>
            <div class="modal-footer">
              <button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="transfer();">Assign Campaign
                & Assign Role</button>
              <button style="background-color:#192e62;color:#fff;border-radius:3px" onclick="skip();">Skip & Assign
                Role</button>
              <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodal()">Close</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="assignempmanager" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Assign Employees to Manager</h5>
              <button type="button" class="close" data-dismiss="modal" onclick="closemodalemp()">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div id="assignempmanagerbody" class="modal-body">
              <input type="hidden" name="submanagerid" id="manageremployeeid">
              <div class="container">
                @if(isset($employees) && !empty($employees))
                  <div class="row">
                    @foreach($employees as $emp)
                      <div class="col-md-4">
                        <div class="card mb-3">
                          <div class="card-body">
                            <div class="form-check">
                              <input class="form-check-input" type="checkbox" name="employees[]" value="{{ $emp->id }}"
                                id="emp{{ $emp->id }}">
                              <label class="form-check-label" for="emp{{ $emp->id }}">
                                {{ $emp->first_name . ' ' . $emp->last_name }}
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="alert alert-info">No sources available.</div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary" style="background-color:#192e62;" onclick="assignemployees();">Assign
                Employees</button>
              <button type="button" class="btn btn-info" onclick="closemodalemp()">Skip for Now</button>
            </div>
          </div>
        </div>
      </div>

      <div class="graph customclass">
        <div class="row">
          <div class="add-submanager">
            <a href="{{ route('employee.createmanageremployees') }}">Add Employee</a>
          </div>
        </div>

        <div class="table">
          <div class="table-container">
            <table class="table table-striped table-hover" id="employee-table">
              <thead class="thead-main">
                <tr>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Password</th>
                  <th>Phone No</th>
                  <th>Assigned Campaigns</th>
                  <th>Sub Manager</th>
                  <th>Actions</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="modal fade" id="campaignlisting" tabindex="-1" role="dialog" aria-labelledby="totalLeadsLabel"
          aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="totalLeadsLabel">Campaign Listing</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closemodal()">
                  <span aria-hidden="true" style="color: black;">&times;</span>
                </button>
              </div>
              <div id="campaignlistingbody" class="modal-body"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closemodal()">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    $(document).ready(function () {
      $('#spinner-overlay').show();

      var currentStatus = 'total'; // Default filter state

      var table = $('#employee-table').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
          url: '{{ route('employee.manageremployeedata') }}',
          data: function (d) {
            d.status_filter = currentStatus;
          }
        },
        pageLength: 10,
        columns: [
          { data: 'first_name', name: 'first_name', render: data => data || "N/A" },
          { data: 'last_name', name: 'last_name', render: data => data || "N/A" },
          { data: 'email', name: 'email', orderable: false, render: data => data || "N/A" },
          { data: 'orignal_password', name: 'orignal_password', orderable: false, render: data => data || "N/A" },
          { data: 'phone_no', name: 'phone_no', orderable: false, render: data => data || "N/A" },
          { data: 'totalcampaigns', name: 'totalcampaigns', orderable: false, render: data => data || "N/A" },
          { data: 'sub_manager', name: 'sub_manager', orderable: false, render: data => data || "N/A" },
          { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        drawCallback: function () {
          $('.switchery').each(function () {
            if (this.switcheryInstance) return;
            const sw = new Switchery(this, {
              color: '#192e62',
              secondaryColor: '#f9f9f9',
              jackColor: '#d3da44',
              size: 'small'
            });
            this.switcheryInstance = sw;
            tippy(sw.switcher, { content: this.getAttribute('data-tooltip') || 'Change Status', placement: 'top' });
          });

          tippy('.editEmployee', { content: 'Edit Employee', placement: 'top' });
          tippy('.deleteEmployee', { content: 'Delete Employee', placement: 'top' });
        }
      });

      // --- Filter Card Click Logic ---
      $('.filter-card').on('click', function () {
        $('.filter-card').removeClass('active-card');
        $(this).addClass('active-card');
        currentStatus = $(this).data('filter');
        table.draw();
      });
      table.on('init.dt', function () {
        $('div.dataTables_filter input').attr('placeholder', 'Search by name,email').css({ 'width': '250px' });
      });

      table.on('preXhr.dt', () => $('#spinner-overlay').show());
      table.on('xhr.dt', () => $('#spinner-overlay').hide());

      // Delete Functionality
      $(document).on('click', '.deleteEmployee', function (event) {
        event.preventDefault();
        const employeeId = $(this).parent().data('id');
        Swal.fire({
          title: 'Are you sure?',
          text: 'You won\'t be able to revert this!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `/employee/${employeeId}`,
              type: 'POST',
              data: { _token: $('meta[name="csrf-token"]').attr('content') },
              success: function () {
                Swal.fire('Deleted!', 'The employee has been deleted.', 'success');
                table.draw();
              }
            });
          }
        });
      });
    });

    // Global Modal & Status Functions
    function change_status(id) {
      $.ajax({
        url: "{{ route('employee.statusUpdate') }}",
        method: 'post',
        data: { id: id, _token: $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
          if (response.status == 200) {
            Swal.fire({ icon: 'success', title: 'Success!', text: response.message });
            $('#employee-table').DataTable().draw(false);
          }
        }
      });
    }

    function assignsubmanager(employeeid) {
      $('#manageremployeeid').val(employeeid);
      $.ajax({
        url: "{{ route('employee.assignsubmanager') }}",
        method: 'post',
        data: { employeeid: employeeid, _token: $('meta[name="csrf-token"]').attr('content') },
        dataType: "json",
        success: function (response) {
          $('#submanagerbody').html(response.html);
          $('#assignsubmanager').modal('show');
        }
      });
    }

    function transfer() {
      var formdata = new FormData($('#submanagerform')[0]);
      formdata.append('_token', $('meta[name="csrf-token"]').attr('content'));
      $.ajax({
        url: "{{ route('employee.transferleademployee') }}",
        method: 'post',
        data: formdata,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function () {
          $('#assignsubmanager').modal('hide');
          $('#assignempmanager').modal('show');
        }
      });
    }

    function skip() {
      transfer(); // Reusing transfer logic for skip as per original flow
    }

    function assignemployees() {
      var submanagerid = $("#manageremployeeid").val();
      let selectedEmployees = [];
      $('input[name="employees[]"]:checked').each(function () {
        selectedEmployees.push($(this).val());
      });
      $.ajax({
        url: "{{ route('employee.assignemployees') }}",
        method: 'post',
        data: { submanagerid, selectedEmployees, _token: $('meta[name="csrf-token"]').attr('content') },
        success: function () {
          location.reload();
        }
      });
    }

    function viewCampaigns(managerid) {
      $.ajax({
        url: "{{ route('employee.viewcampaigns') }}",
        method: 'get',
        data: { managerid: managerid },
        dataType: "json",
        success: function (response) {
          if (response.status == 200) {
            let modal = new bootstrap.Modal(document.getElementById('campaignlisting'));
            modal.show();
            $('#campaignlistingbody').html(response.html);
          }
        }
      });
    }

    function disablelogin(employeeid, employeemail) {
      $.ajax({
        url: "{{ route('employee.manageemployeelogin') }}", // Corrected route syntax
        method: 'post',
        data: {
          employeeid,
          employeemail,
          _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
        },
        dataType: "json",

        success: function (response) {
          if (response.status == 200) {
            alert(response.message);
          } else {
            alert('Failed to toggle status.');
          }
        },
        error: function (error) {
          console.error('Error:', error);
          alert('An error occurred. Please try again.');
        }
      });

    }


    function closemodal() {
      $('#assignsubmanager').modal('hide');
      $('#campaignlisting').modal('hide');

    }
    function closemodalemp() { $('#assignempmanager').modal('hide'); location.reload(); }
  </script>
@endpush