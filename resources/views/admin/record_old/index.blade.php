@extends('layouts.app')

@section('title', 'Record Room Details')

@section('contentMain') 

    <style>
        div.dt-buttons {
            float: none !important;
            width: 19%;
        }

        div.dt-buttons.btn-group {
            margin-bottom: 20px;
        }

        div.dt-buttons.btn-group .btn {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* Ensure responsiveness on smaller screens */
        @media (max-width: 768px) {
            div.dt-buttons.btn-group {
                flex-direction: column;
                align-items: flex-start;
            }

            div.dt-buttons.btn-group .btn {
                width: 100%;
                text-align: left;
            }
        }
    </style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
         <div class="breadcrumb-title pe-3">Record Room</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Total Record List</li>
                    </ol>
                </nav>
            </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-end pb-4">
                <!-- State Dropdown -->
              <!--  <div class="col-lg-4 col-md-6 col-12">
                    <div class="mb-3">
                        <label for="state" class="form-label">Locality </label>
                        <select name="localityRecord" id="state" class="form-select">
                            <option value="">All</option>
                           
                        </select>
                    </div>
                </div>

                
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Block</label>
                        <select name="statusRecord" id="status" class="form-select">
                            <option value="">All</option>
                           
                        </select>
                    </div>
                </div>


                <div class="col-lg-2 col-md-4 col-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Plot</label>
                        <select name="statusRecord" id="status" class="form-select">
                            <option value="">All</option>
                           
                        </select>
                    </div>
                </div> -->

                <!-- Buttons aligned to right -->
                <div class="col-lg-4 col-md-6 col-12 d-flex justify-content-between align-items-end gap-2 pb-3">
                  <!--  <div>
                        <label class="d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="resetValues">Reset</button>
                    </div> -->
                    <div>
                        <label class="d-block">&nbsp;</label>
                        <a href="{{ route('recordRoom.create') }}">
                            <button type="button" class="btn btn-primary">+ Add New File</button>
                        </a>
                    </div>
                </div>
            </div>


            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Record ID</th>
                        <th>Colony Code</th>
                        <th>Block</th>
                        <th>Plot No.</th>
                        <th>File Location</th>
                        <th>Section</th>
                        <th>Current Section</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="tooltip"></div>

    @include('include.loader')
    @include('include.alerts.ajax-alert')
    @include('include.alerts.delete-confirmation')

@endsection


@section('footerScript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('recordRoom.files') }}",
                    data: function(d) {
                        // d.state = $('#state').val();
                        // d.status = $('#status').val();
                        
                        // d.searchPropertyId = $('#searchPropertyId').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'record_id',
                        name: 'record_id',
                    },
                    {
                        data: 'colony_code',
                        name: 'colony_code'
                    },
                    {
                        data: 'block',
                        name: 'block'
                    },
                    {
                        data: 'plot',
                        name: 'plot'
                    },
                    {
                        data: 'file_location',
                        name: 'file_location'
                    },
                    {
                        data: 'section_code',
                        name: 'section_code'
                    },
                    {
                        data: 'transaction_section_code',
                        name: 'transaction_section_code'
                    }
                ]
            });
        });


        $(document).ready(function() {
            let deleteId;

            // Trigger the modal and set the record ID
            $(document).on('click', '.delete-btn', function() {
                deleteId = $(this).data('id'); // Get the record ID from the button
                $('#ModalDelete').modal('show'); // Show the modal
            });


            $(document).on('change', '#state', function() {
               $('#example').DataTable().ajax.reload();
            });
            $(document).on('change', '#status', function() {
               $('#example').DataTable().ajax.reload();
            });

            // Handle delete confirmation
            $('#confirmDelete').on('click', function() {
                if (deleteId) {
                    $.ajax({
                        url: "{{ route('flat.destroy') }}",
                        type: 'POST',
                        data: {
                            deleteId: deleteId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                // Handle success response
                                $('#ModalDelete').modal('hide');
                                $('.loader_container').addClass('d-none');
                                if ($('.results').hasClass('d-none'))
                                    $('.results').removeClass('d-none');
                                showSuccess(response.message);
                                // Ensure checkbox is checked and disabled after success
                                setTimeout(function() {
                                    $('#example').DataTable().ajax.reload();
                                }, 3000); // Slight delay to ensure modal is fully hidden
                            } else {
                                // Handle success response
                                $('#ModalDelete').modal('hide');
                                $('.loader_container').addClass('d-none');
                                if ($('.results').hasClass('d-none'))
                                    $('.results').removeClass('d-none');
                                showError(response.message);
                                // Ensure checkbox is checked and disabled after success
                                setTimeout(function() {
                                    $('#example').DataTable().ajax.reload();
                                }, 100); // Slight delay to ensure modal is fully hidden
                            }
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseText); // Handle errors here
                        }
                    });
                } else {
                    alert('No record selected for deletion.');
                }
            });
        });

            $('#resetValues').click(function() {
                $('#state').val('');
                $('#status').val('');
                $('#example').DataTable().ajax.reload();
            });
    </script>
@endsection
