@extends('layouts.app')

@section('title', 'Application Listing')

@section('content')
    <!-- Add these CSS files in your head section or style section -->
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.bootstrap4.min.css') }}">
    <style>
        /* Custom styling for the table */
        #oldRecordsTable_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 10px;
            margin-left: 5px;
        }

        #oldRecordsTable_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
        }

        .badge {
            padding: 5px 10px;
            font-size: 12px;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* Styling for remark tooltip/clickable text */
        .remark-tooltip {
            cursor: pointer;
            border-bottom: 1px dotted #999;
            transition: all 0.2s ease;
            display: inline-block;
            max-width: 100%;
        }

        .remark-tooltip:hover {
            background-color: #f0f0f0;
            color: #007bff;
            border-bottom-color: #007bff;
        }

        /* Modal styling */
        #modalRemark {
            max-height: 300px;
            overflow-y: auto;
            font-size: 14px;
            line-height: 1.6;
            background-color: #f8f9fa;
        }

        /* Optional hover tooltip styling */
        .remark-hover-tooltip {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            pointer-events: none;
            font-family: monospace;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background-color: #17a2b8;
            color: #ffffff !important;
        }

        .modal-header .close {
            padding: 1rem;
            margin: -1rem -1rem -1rem auto;
            background: none;
            border: 0;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-shadow: 0 1px 0 #fff;
            opacity: 0.8;
            cursor: pointer;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        /* Ensure proper spacing */
        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
            color: #ffffff !important;
        }

        /* Fix for close button on dark background */
        .bg-info .close {
            color: #fff;
            text-shadow: none;
            opacity: 0.8;
        }

        .bg-info .close:hover {
            opacity: 1;
        }

        /* Make form controls more compact */
        .form-control-sm {
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            height: calc(1.5rem + 2px);
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
        }

        label {
            margin-bottom: 0.2rem;
            font-size: 0.75rem;
        }

        .form-group {
            margin-bottom: 0.5rem;
        }

        /* For the alternative inline form */
        .form-inline .form-group {
            margin-bottom: 0;
        }

        .form-select-sm {
            width: 79% !important;
        }
    </style>

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Applications / Registrations</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">30+ Days Old Pending Requests</li>
                </ol>
            </nav>
        </div>
    </div>

    <hr>
    <div class="container-fluid mt-4">
        <div class="card">
            <!-- <div class="card-header bg-primary text-white">
                <h5 class="mb-0" style="color: #ffffff">
                    <i class="fas fa-clock"></i> 30+ Days Old Pending Requests
                </h5>
            </div> -->
            <div class="card-body">
                <!-- Export Buttons -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="btn-group">
                            <button type="button" id="exportExcel" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </button>
                            <button type="button" id="exportCSV" class="btn btn-info">
                                <i class="fas fa-file-csv"></i> Export CSV
                            </button>
                            <button type="button" id="printTable" class="btn btn-secondary">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Section - Inline Form -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form class="form-inline" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end;">
                            <div class="form-group" style="flex: 1; min-width: 150px;">
                                <label for="typeFilter" style="font-size: 16px; display: block;"><i
                                        class="fas fa-filter"></i> Record Type</label>
                                <select id="typeFilter" class="form-control form-control-sm" style="width: 100%;">
                                    <option value="all">All Records</option>
                                    <option value="registration">Registrations</option>
                                    <option value="application">Applications</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1; min-width: 150px;">
                                <label for="sectionFilter" style="font-size: 16px; display: block;"><i
                                        class="fas fa-building"></i> Section</label>
                                <select id="sectionFilter" class="form-control form-control-sm" style="width: 100%;">
                                    <option value="all">All Sections</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1; min-width: 130px;">
                                <label for="dateFrom" style="font-size: 16px; display: block;"><i
                                        class="fas fa-calendar-alt"></i> From Date</label>
                                <input type="date" id="dateFrom" class="form-control form-control-sm"
                                    style="width: 100%;">
                            </div>
                            <div class="form-group" style="flex: 1; min-width: 130px;">
                                <label for="dateTo" style="font-size: 16px; display: block;"><i
                                        class="fas fa-calendar-alt"></i> To Date</label>
                                <input type="date" id="dateTo" class="form-control form-control-sm"
                                    style="width: 100%;">
                            </div>
                            <div class="form-group">
                                <div style="display: flex; gap: 5px;">
                                    <button type="button" id="searchBtn" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <button type="button" id="resetBtn" class="btn btn-secondary">
                                        <i class="fas fa-undo-alt"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="oldRecordsTable" width="100%">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th width="8%">Application Type</th>
                                <th width="15%">Applicant / Application Number</th>
                                <th width="15%">Applicant Name</th>
                                <th width="12%">Section Name</th>
                                <th width="12%">Assigned User</th>
                                <th width="10%">Section Remark</th>
                                <th width="8%">Days Pending</th>
                                <th width="12%">Application Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('include.alerts.ajax-alert')

    <!-- Remark Modal -->
    <div class="modal fade" id="remarkModal" tabindex="-1" role="dialog" aria-labelledby="remarkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remarkModalLabel">
                        <i class="fas fa-comment-alt"></i> Full Remark
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div id="modalRemark"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
    <!-- DataTable Buttons Extensions -->
    <script src="{{ asset('assets/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/js/buttons.print.min.js') }}"></script>

    <script type="text/javascript">
        // Helper function to escape HTML and prevent XSS attacks
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }


        $(document).ready(function() {
            // Load sections for filter dropdown
            function loadSections() {
                $.ajax({
                    url: '{{ route('get.sections.for.filter') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.sections && response.sections.length > 0) {
                            var sectionsHtml = '<option value="all">All Sections</option>';
                            $.each(response.sections, function(key, section) {
                                sectionsHtml += '<option value="' + section.id + '">' +
                                    section.name + '</option>';
                            });
                            $('#sectionFilter').html(sectionsHtml);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading sections:', xhr);
                    }
                });
            }

            // Load sections on page load
            loadSections();

            // Initialize DataTable with AJAX source and export buttons
            var table = $('#oldRecordsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('get.old.pending.records.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.type_filter = $('#typeFilter').val();
                        d.section_filter = $('#sectionFilter').val();
                        d.date_from = $('#dateFrom').val();
                        d.date_to = $('#dateTo').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.log('DataTable Error:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to load data. Please refresh the page.',
                        });
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type_label',
                        name: 'type_label'
                    },
                    {
                        data: 'identifier',
                        name: 'identifier'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'section_name',
                        name: 'section_name'
                    },
                    {
                        data: 'assigned_user_name',
                        name: 'assigned_user_name'
                    },
                    {
                        data: 'pending_remark',
                        name: 'pending_remark'
                    },
                    {
                        data: 'days_pending',
                        name: 'days_pending'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                columnDefs: [{
                    targets: 2, // This targets the identifier column (index 2, 0-based)
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var pendingAlertValue = btoa('true'); // Base64 encode 'true'
                            var viewUrl = '';

                            if (row.record_type === 'registration') {
                                // URL for registration: /edharti/register/user/{id}/view?pending_alert={encoded}
                                viewUrl = '/edharti/register/user/' + row.id +
                                    '/view?pending_alert=' + pendingAlertValue;
                            } else {
                                // You need to pass the model_name. If you don't have it in row, you might need to add it in controller
                                var encodedType = btoa(row
                                    .model_name); // or use row.model_name if available
                                viewUrl = '/edharti/applications/' + row.id + '?type=' +
                                    encodedType + '&pending_alert=' + pendingAlertValue;
                            }

                            // Return anchor tag with external link icon
                            return '<a href="' + viewUrl +
                                '" target="_blank" class="text-primary" style="text-decoration: none; font-weight: bold; display: inline-block;">' +
                                '<strong>' + escapeHtml(data) + '</strong>' +
                                '<i class="fas fa-external-link-alt" style="font-size: 12px; margin-left: 5px;"></i>' +
                                '</a>';
                        }
                        return data;
                    }
                }],
                order: [
                    [8, 'desc']
                ], // Order by created_at column (index 8)
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success',
                        title: 'Pending_Records',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Export all columns
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-info',
                        title: 'Pending_Records',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                        }
                    }
                ],
                language: {
                    processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
                    emptyTable: 'No records found',
                    zeroRecords: 'No matching records found',
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                }
            });

            // Search button click - reload DataTable with filters
            $('#searchBtn').on('click', function() {
                const dateFrom = $('#dateFrom').val();
                const dateTo = $('#dateTo').val();

                if (dateFrom && dateTo && dateFrom > dateTo) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date Range',
                        text: 'From date cannot be greater than To date!',
                    });
                    return;
                }
                table.ajax.reload();
            });

            // Reset button click - clear all filters and reload
            $('#resetBtn').on('click', function() {
                $('#typeFilter').val('all');
                $('#sectionFilter').val('all');
                $('#dateFrom').val('');
                $('#dateTo').val('');
                table.ajax.reload();
            });

            // Export to Excel using DataTable button
            $('#exportExcel').on('click', function() {
                table.button('.buttons-excel').trigger();
            });

            // Export to CSV using DataTable button
            $('#exportCSV').on('click', function() {
                table.button('.buttons-csv').trigger();
            });

            // Print using DataTable button
            $('#printTable').on('click', function() {
                table.button('.buttons-print').trigger();
            });

            // Auto-submit filters on Enter key
            $('#dateFrom, #dateTo').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });

            // Loading indicator
            $('#oldRecordsTable').on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $('#oldRecordsTable').css('opacity', '0.6');
                } else {
                    $('#oldRecordsTable').css('opacity', '1');
                }
            });
        });

        // Show remark in modal on click
        $(document).on('click', '.remark-tooltip', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const fullRemark = $(this).data('remark');
            console.log('Full Remark:', fullRemark);

            if (fullRemark && fullRemark !== '') {
                $('#modalRemark').text(fullRemark);
            } else {
                const fallbackRemark = $(this).text();
                $('#modalRemark').text(fallbackRemark || 'No remark available');
            }

            $('#remarkModal').modal('show');
        });

        // Modal close handlers
        $(document).ready(function() {
            $('#remarkModal .close, #remarkModal [data-dismiss="modal"]').on('click', function() {
                $('#remarkModal').modal('hide');
            });

            $('#remarkModal').on('click', function(e) {
                if ($(e.target).hasClass('modal')) {
                    $('#remarkModal').modal('hide');
                }
            });

            $(document).on('keyup', function(e) {
                if (e.key === 'Escape' && $('#remarkModal').hasClass('show')) {
                    $('#remarkModal').modal('hide');
                }
            });
        });
    </script>
@endsection
