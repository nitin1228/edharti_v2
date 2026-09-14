@extends('layouts.app')

@section('title', 'Club Membership List')

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

        .duplicate-row, .duplicate-row > td {
            /* background-color: #df7d7d94 !important; */
            background-color: #ecb4b4 !important;
            /* Light red background */
        }

        .duplicate-row td:first-child {

            background-color: #ffa8a836 !important;
        }

        #bulk-email-message {
            margin-left: auto;
        }

        table.dataTable>thead th:first-child.sorting:after,
        table.dataTable>thead th:first-child.sorting_asc:after,
        table.dataTable>thead th:first-child.sorting_desc:after,
        table.dataTable>thead th:first-child.sorting_asc_disabled:after,
        table.dataTable>thead th:first-child.sorting_desc_disabled:after {
            display: none !important;
        }

        table.dataTable>thead th:first-child.sorting:before,
        table.dataTable>thead th:first-child.sorting_asc:before,
        table.dataTable>thead th:first-child.sorting_desc:before,
        table.dataTable>thead th:first-child.sorting_asc_disabled:before,
        table.dataTable>thead th:first-child.sorting_desc_disabled:before {
            display: none !important;
        }

        table.dataTable>thead>tr>th:not(.sorting_disabled),
        table.dataTable>thead>tr>td:not(.sorting_disabled) {
            padding-right: 20px !important;
        }

        /* commented and adeed by anil for replace the new loader on 24-07-2025  */
        .loader {
            width: 48px;
            height: 48px;
            border: 6px solid #FFF;
            border-radius: 50%;
            position: relative;
            transform: rotate(45deg);
            box-sizing: border-box;
        }

        .loader::before {
            content: "";
            position: absolute;
            box-sizing: border-box;
            inset: -7px;
            border-radius: 50%;
            border: 8px solid #116d6e;
            animation: prixClipFix 2s infinite linear;
        }

        @keyframes prixClipFix {
            0% {
                clip-path: polygon(50% 50%, 0 0, 0 0, 0 0, 0 0, 0 0)
            }

            25% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 0, 100% 0, 100% 0)
            }

            50% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 100% 100%, 100% 100%)
            }

            75% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 0 100%, 0 100%)
            }

            100% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 0 100%, 0 0)
            }
        }

        /* commented and adeed by anil for replace the new loader on 24-07-2025  */
    </style>
    {{-- <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Public Services</div>
        @include('include.partials.breadcrumbs')
    </div> --}}

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Club Membership</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Public Services</li>
                    <li class="breadcrumb-item" aria-current="page">Club Membership</li>
                    <li class="breadcrumb-item active" aria-current="page">Received Applications</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--breadcrumb-->
    <hr>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-end flex-wrap py-3 gap-2">
                <h6 class="mb-0 text-uppercase tabular-record_font"></h6>

                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('create.club.membership.form') }}">
                            <button class="btn btn-primary">+ Add New Membership</button>
                        </a>
                        <button class="btn btn-primary send-bulk-email" data-type="pending">
                            Send Bulk Email Pending
                        </button>
                        <button class="btn btn-primary send-bulk-email" data-type="withdrawl">
                            Send Bulk Email Withdrawl
                        </button>
                    </div>
                    <div id="bulk-email-message" class="text-danger mt-1" style="min-height: 1.5em;"></div>
                </div>

            </div>


            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center"><input type="checkbox" id="select-all"></th>
                        <th>S.No.</th>
                        <th>Application No.</th>
                        <th>Name</th>
                        <th>Club Type</th>
                        <th>Designation</th>
                        <th>Central Deputation</th>
                        <th>Superannuation Date</th>
                        <th>
                            <div style="width: 110px; overflow: hidden;">
                                <select class="form-control form-select form-select-sm" name="status" id="status"
                                    style="font-weight: bold;">
                                    <option value="">Status</option>
                                    @foreach ($items as $item)
                                        <option class="text-capitalize" value="{{ $item->id }}"
                                            @if ($getStatusId == $item->id) @selected(true) @endif>
                                            {{ $item->item_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="modal fade" id="textModal" tabindex="-1" aria-labelledby="textModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="textModalLabel">Full Text</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="textModalBody">
                    <!-- Full text will appear here -->
                </div>
            </div>
        </div>
    </div>
    @include('include.alerts.ajax-alert')
    <!-- commented and adeed by anil for replace the new loader on 01-08-2025  -->
    <!-- <div id="spinnerOverlay" style="display:none;">
                                                                                                            <img src="{{ asset('assets/images/chatbot_icongif.gif') }}">
                                                                                                        </div> -->
    <div id="spinnerOverlay" style="display:none;">
        <span class="loader"></span>
        <h1 style="color: white;font-size: 20px; margin-top:10px;">Loading... Please wait</h1>
    </div>
    <!-- commented and adeed by anil for replace the new loader on 01-08-2025  -->

@endsection
@section('footerScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                order: [
                    [2, 'desc']
                ], // Assuming 2nd index is `unique_id` in columns array
                ajax: {
                    url: "{{ route('get.club.membership.received.list') }}",
                    data: function(d) {
                        d.status = $('#status').val(); // Capture the status filter value
                    }
                },
                columns: [{
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center', // ✅ centers the checkbox in <td>
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="bulk-email-checkbox" value="' +
                                row.id + '">';
                        }
                    },
                    {
                        data: null,
                        name: 'serial_number',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'unique_id',
                        name: 'unique_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'club_type',
                        name: 'club_type'
                    },
                    {
                        data: 'designation',
                        name: 'designation',
                        render: function(data, type, row, meta) {
                            if (data && data.length > 20) {
                                return `${data.substring(0, 20)}... <button class="btn btn-link p-0 view-full-text" style="text-decoration: none;" data-title="Designation" data-content="${data.replace(/"/g, '&quot;')}">View</button>`;
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'is_central_deputated',
                        name: 'is_central_deputated'
                    },
                    {
                        data: 'date_of_superannuation',
                        name: 'date_of_superannuation'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                scrollX: true,
                // scrollY: '400px',
                scrollCollapse: true,
                dom: '<"top"Blf>rt<"bottom"ip><"clear">',
                buttons: ['csv', 'excel'],
            });

            // Fix: Trigger table reload on status filter change
            $('#status').change(function() {
                table.ajax.reload();
            });

            // Handle "Select All" checkbox
            $('#select-all').on('click', function() {
                $('.bulk-email-checkbox').prop('checked', this.checked);
            });

            // Handle all buttons with class `.send-bulk-email`
            $('.send-bulk-email').on('click', function() {
                $('#bulk-email-message').text(''); // Clear previous message

                let selectedIds = [];
                $('.bulk-email-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    $('#bulk-email-message').text('Please select at least one record.');
                    return;
                }

                let type = $(this).data('type'); // Get type from clicked button

                const spinnerOverlay = document.getElementById('spinnerOverlay');
                if (spinnerOverlay) {
                    spinnerOverlay.style.display = 'flex';
                }

                $.ajax({
                    url: '{{ route('club.membership.bulk.email') }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds,
                        type: type,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#select-all').prop('checked', false);
                            $('.bulk-email-checkbox').prop('checked', false);
                            showSuccess(response.message);
                        } else {
                            showError(response.message);
                        }
                        if (spinnerOverlay) spinnerOverlay.style.display = 'none';
                    },
                    error: function() {
                        showError('An error occurred while sending emails.');
                        if (spinnerOverlay) spinnerOverlay.style.display = 'none';
                    }
                });
            });

        });

        // Handle View button clicks for long text
        $(document).on('click', '.view-full-text', function() {
            const title = $(this).data('title');
            const content = $(this).data('content');
            $('#textModalLabel').text(title);
            $('#textModalBody').text(content);
            $('#textModal').modal('show');
        });
    </script>
@endsection
