@extends('layouts.app')

@section('title', 'Unallotted Outside Delhi Property Details')

@section('content')

    <style>
        div.dt-buttons {
            float: none !important;
            /* width: 19%; */
            width: 33%;
            /* chagned by anil on 28-08-2025 to fix in resposive */
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
            div.dt-buttons {
                width:100%;
            }

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
        <div class="breadcrumb-title pe-3">Properties</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Properties</li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                    <li class="breadcrumb-item active" aria-current="page">Properties Outside Delhi</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>

    <div class="card">
        <div class="card-body">
          {{--  <div class="d-flex justify-content-between py-3">
                <h6 class="mb-0 text-uppercase tabular-record_font align-self-end"></h6>
                <a href="{{ route('create.vacant.land') }}"><button class="btn btn-primary">+ Add
                       Outside Delhi Property</button></a>
            </div>  --}}
            <div class="row align-items-end pb-4">
                <!-- State Dropdown -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mb-3">
                        <label for="state" class="form-label">State </label>
                        <select name="localityRecord" id="state" class="form-select">
                            <option value="">All</option>
                            @foreach ($uniqueStates as $state)
                                <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- City Dropdown -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mb-3">
                        <label for="city" class="form-label">City </label>
                        <select name="cityRecord" id="city" class="form-select">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>

                <!-- Status Dropdown -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="statusRecord" id="status" class="form-select">
                            <option value="">All</option>
                            @foreach ($uniqueStatuses as $status)
                                <option value="{{ $status->id }}">{{ $status->item_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Buttons aligned to right -->
                <div class="col-lg-3 col-md-6 col-12 d-flex justify-content-between align-items-end gap-2 pb-3">
                    <div>
                        <label class="d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="resetValues">Reset</button>
                    </div>
                    <div>
                        <label class="d-block">&nbsp;</label>
                        <a href="{{ route('create.vacant.land') }}">
                            <button type="button" class="btn btn-primary">+ Add Outside Delhi Property</button>
                        </a>
                    </div>
                </div>
            </div>
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Area (In Sq.Mt.)</th>
                        <th>Present Custodian</th>
                        <th>Date Of Custody</th>
                        <th>Status</th>
                        <th>Action</th>
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
                serverSide: false,
                scrollX: true,
         //       ajax: '{{ route('get.vacant.land.list.list') }}',
                ajax: {
                    url: "{{ route('get.vacant.land.list.list') }}",
                    data: function(d) {
                        d.state = $('#state').val();
                        d.city = $('#city').val();
                        d.status = $('#status').val();
                        
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
                        data: 'state_name',
                        name: 'states.name'
                    },
                    {
                        data: 'city_name',
                        name: 'cities.name'
                    },
                    {
                        data: 'address',
                        name: 'property_outsides.address'
                    },
                    {
                        data: 'area',
                        name: 'property_outsides.area'
                    },
                    {
                        data: 'custodian_name',
                        name: 'present_custodians.item_name'
                    },
                    {
                        data: 'custody_date',
                        name: 'property_outsides.custody_date'
                    },
                    {
                        data: 'status_name',
                        name: 'items.item_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
                buttons: ['csv', 'excel']
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
            $(document).on('change', '#city', function() {
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
                $('#city').val('');
                $('#status').val('');
                $('#example').DataTable().ajax.reload();
            });

            $(document).ready(function() {
            $('#state').on('change', function() {
                let stateId = $(this).val();

                // Clear current cities
                $('#city').html('<option value="">All</option>');

                if (stateId) {
                    $.ajax({
                        url: getBaseURL() + "/get-vacant-land-cities" + "/" + stateId,
                        type: 'GET',
                        success: function(response) {
                            if (response.length > 0) {
                                $.each(response, function(key, city) {
                                    $('#city').append('<option value="' + city.id +
                                        '">' + city.city_name + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
