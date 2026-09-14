@extends('layouts.app')
@section('title', 'Section Wise Report')
@section('content')
    <div class="container-fluid">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Section Wise Report</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Section Wise Report</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filterForm" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Filter Type</label>
                        <select name="filter_type" id="filterType" class="form-select">
                            <option value="week">Last Week</option>
                            <option value="month">Last Month</option>
                            <option value="year">Last Year</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="customDateRange" style="display: none;">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="startDate" class="form-control">
                    </div>
                    <div class="col-md-3" id="customDateRangeEnd" style="display: none;">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" id="endDate" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Print All Button -->
        <div class="mb-3">
            <button class="btn btn-primary" onclick="printBoth()">
                <i class="fas fa-print"></i> Print Both Tables
            </button>
        </div>

        <!-- Registration Summary Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" id="registrationHeading">Registration Summary (Loading...)</h5>
                <div>
                    <button class="btn btn-sm btn-success" onclick="exportCSV()">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="exportPDF()">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="registrationTable">
                        <thead>
                            <tr>
                                <th class="text-center">S.No</th>
                                <th class="text-center">Section Name</th>
                                <th class="text-center">Total Pending</th>
                                <th class="text-center">Total Activated</th>
                                <th class="text-center" id="regRejectedHeader">Total Rejected (Last Week)</th>
                                <th class="text-center" id="regApprovedHeader">Total Approved (Last Week)</th>
                            </tr>
                        </thead>
                        <tbody id="registrationBody">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                        <tfoot id="registrationFooter" style="display: none;">
                            <tr style="font-weight: bold; background-color: #f8f9fa;">
                                <td class="text-center" colspan="2">Total</td>
                                <td class="text-center" id="regTotalPending">0</td>
                                <td class="text-center" id="regTotalActivated">0</td>
                                <td class="text-center" id="regTotalRejected">0</td>
                                <td class="text-center" id="regTotalApproved">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- Print Button for Registration Table -->
                <div class="mt-3 text-end">
                    <button class="btn btn-primary" onclick="printRegistration()">
                        <i class="fas fa-print"></i> Print Registration Table
                    </button>
                </div>
            </div>
        </div>

        <!-- Applications Section Wise Report -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" id="applicationHeading">Section Wise Report (Loading...)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="applicationTable">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">S.No</th>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">Section Name</th>
                                <th colspan="6" class="text-center">NOC APPLICATION</th>
                                <th colspan="6" class="text-center">MUTATION APPLICATION</th>
                            </tr>
                            <tr>
                                <!-- NOC Sub-headers -->
                                <th class="text-center">Total Submitted</th>
                                <th class="text-center">Total Disposed</th>
                                <th class="text-center">Total Objected</th>
                                <th class="text-center">Total Pending</th>
                                <th class="text-center">Total Pending (Obj + Pending)</th>
                                <th class="text-center" id="nocDisposedHeader">Last Week Disposed</th>
                                <!-- Mutation Sub-headers -->
                                <th class="text-center">Total Submitted</th>
                                <th class="text-center">Total Disposed</th>
                                <th class="text-center">Total Objected</th>
                                <th class="text-center">Total Pending</th>
                                <th class="text-center">Total Pending (Obj + Pending)</th>
                                <th class="text-center" id="mutationDisposedHeader">Last Week Disposed</th>
                            </tr>
                        </thead>
                        <tbody id="applicationBody">
                            <!-- Data loaded via AJAX -->
                        </tbody>
                        <tfoot id="applicationFooter" style="display: none;">
                            <tr style="font-weight: bold; background-color: #f8f9fa;">
                                <td class="text-center" colspan="2">Total</td>
                                <!-- NOC Totals -->
                                <td class="text-center" id="nocTotalSubmitted">0</td>
                                <td class="text-center" id="nocTotalDisposed">0</td>
                                <td class="text-center" id="nocTotalObjected">0</td>
                                <td class="text-center" id="nocTotalPending">0</td>
                                <td class="text-center" id="nocTotalPendingObjected">0</td>
                                <td class="text-center" id="nocTotalDisposedLastWeek">0</td>
                                <!-- Mutation Totals -->
                                <td class="text-center" id="mutTotalSubmitted">0</td>
                                <td class="text-center" id="mutTotalDisposed">0</td>
                                <td class="text-center" id="mutTotalObjected">0</td>
                                <td class="text-center" id="mutTotalPending">0</td>
                                <td class="text-center" id="mutTotalPendingObjected">0</td>
                                <td class="text-center" id="mutTotalDisposedLastWeek">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- Print Button for Application Table -->
                <div class="mt-3 text-end">
                    <button class="btn btn-primary" onclick="printApplication()">
                        <i class="fas fa-print"></i> Print Application Table
                    </button>
                </div>

                <!-- Future Applications Placeholder -->
                <div class="mt-4" id="futureApplications">
                    <!-- Future application tables will be added here dynamically -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')
    <style>
        /* Make all anchor tags black color */
        .table tbody td a {
            color: #000000 !important;
            text-decoration: none;
        }

        .table tbody td a:hover {
            color: #000000 !important;
            text-decoration: underline;
        }

        /* Total row styling */
        .table tfoot tr {
            font-weight: bold !important;
            background-color: #f8f9fa !important;
        }

        .table tfoot td {
            font-weight: bold !important;
        }

        @media print {
            .btn {
                display: none !important;
            }

            .page-breadcrumb {
                display: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
            }

            .card-header {
                background-color: #f8f9fa !important;
            }
        }
    </style>

    <script>
        let currentData = null;
        let currentDateRange = null;
        let currentFilterType = 'week';
        let currentStartDate = null;
        let currentEndDate = null;

        // Status mapping for different application statuses
        const statusMapping = {
            'submitted': 'APP_NEW,APP_PEN,APP_IP,APP_OBJ,APP_APR,APP_REJ,APP_CAN,APP_HOLD',
            'disposed': 'APP_APR,APP_REJ',
            'objected': 'APP_OBJ',
            'pending': 'APP_NEW,APP_PEN,APP_IP',
            'pending_objected': 'APP_NEW,APP_PEN,APP_IP,APP_OBJ',
            'disposed_last_week': 'APP_APR,APP_REJ'
        };

        $(document).ready(function() {
            // Handle filter type change
            $('#filterType').change(function() {
                if ($(this).val() === 'custom') {
                    $('#customDateRange, #customDateRangeEnd').show();
                } else {
                    $('#customDateRange, #customDateRangeEnd').hide();
                }
            });

            // Set default dates for custom range
            const today = new Date();
            const oneWeekAgo = new Date(today);
            oneWeekAgo.setDate(today.getDate() - 7);

            $('#startDate').val(oneWeekAgo.toISOString().split('T')[0]);
            $('#endDate').val(today.toISOString().split('T')[0]);

            // Handle form submission
            $('#filterForm').submit(function(e) {
                e.preventDefault();
                loadData();
            });

            // Load initial data
            loadData();
        });

        function loadData() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams(formData);
            currentFilterType = formData.get('filter_type') || 'week';

            // Get date values for URL parameters
            const startDateInput = formData.get('start_date');
            const endDateInput = formData.get('end_date');

            // Only calculate dates if we don't have a date range from the server yet
            if (currentFilterType === 'custom' && startDateInput && endDateInput) {
                currentStartDate = formatDate(startDateInput);
                currentEndDate = formatDate(endDateInput);
            } else {
                // Set default dates that will be overwritten by server response
                const today = new Date();
                const oneWeekAgo = new Date(today);
                oneWeekAgo.setDate(today.getDate() - 7);
                currentStartDate = formatDate(oneWeekAgo);
                currentEndDate = formatDate(today);
            }

            // Show loading
            $('#registrationBody').html(
                '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
            $('#applicationBody').html(
                '<tr><td colspan="18" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

            $.ajax({
                url: "{{ route('registrations.get-report-data') }}",
                type: 'GET',
                data: params.toString(),
                success: function(response) {
                    if (response.success) {
                        currentData = response.data;
                        currentDateRange = response.date_range;

                        // IMPORTANT: Use the date range from the server response for URL parameters
                        // Convert date format from DD/MM/YYYY to DD-MM-YYYY for URL
                        if (response.date_range) {
                            currentStartDate = response.date_range.start.replace(/\//g, '-');
                            currentEndDate = response.date_range.end.replace(/\//g, '-');
                        }

                        renderData(response.data, response.date_range);
                    } else {
                        showError('Failed to load data');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    showError('Failed to load data. Please try again.');
                }
            });
        }

        function formatDate(date) {
            // Format date as DD-MM-YYYY
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function renderData(data, dateRange) {
            // Get filter label based on filter type
            const filterLabel = getFilterLabel(currentFilterType, dateRange);

            // Update headings with date range - DYNAMIC
            $('#registrationHeading').text(`Registration Summary (${dateRange.start} to ${dateRange.end})`);
            $('#applicationHeading').text(`Section Wise Report (${dateRange.start} to ${dateRange.end})`);

            // Update column headers with dynamic labels
            updateColumnHeaders(filterLabel);

            // Render Registration Table
            renderRegistrationTable(data.registration, filterLabel);

            // Render Applications Table (NOC + Mutation in same table)
            renderApplicationTable(data.applications, filterLabel);

            // Handle future applications
            handleFutureApplications(data.applications, filterLabel);
        }

        function getFilterLabel(filterType, dateRange) {
            switch (filterType) {
                case 'week':
                    return 'Last Week';
                case 'month':
                    return 'Last Month';
                case 'year':
                    return 'Last Year';
                case 'custom':
                    return `${dateRange.start} to ${dateRange.end}`;
                default:
                    return 'Last Week';
            }
        }

        function updateColumnHeaders(filterLabel) {
            // Update Registration table headers
            $('#regRejectedHeader').text(`Total Rejected (${filterLabel})`);
            $('#regApprovedHeader').text(`Total Approved (${filterLabel})`);

            // Update Application table headers
            $('#nocDisposedHeader').text(`Disposed (${filterLabel})`);
            $('#mutationDisposedHeader').text(`Disposed (${filterLabel})`);
        }

        /**
         * Build Registration Summary URL - FIXED with /edharti/ subdirectory
         */
        function buildRegistrationUrl(sectionId, value, statusCodes) {
            if (value == 0 || !sectionId) {
                return '#';
            }
            const baseUrl = "{{ url('/') }}";
            // Add /edharti/ subdirectory to the URL
            const url =
                `${baseUrl}/edharti/registration-summary/details?section_id=${sectionId}&date_from=${currentStartDate}&date_to=${currentEndDate}&status=${encodeURIComponent(statusCodes)}`;
            return url;
        }

        /**
         * Build Application Summary URL
         */
        function buildApplicationUrl(sectionId, serviceType, statusKey) {
            // Get the status codes based on the status key
            const statusCodes = statusMapping[statusKey] ||
                'APP_NEW,APP_PEN,APP_IP,APP_OBJ,APP_APR,APP_REJ,APP_CAN,APP_HOLD';

            // Build the URL with date parameters from the server response
            const baseUrl = "{{ url('/') }}";
            const url =
                `${baseUrl}/edharti/applicatoin-summary/details?section_id=${sectionId}&date_from=${currentStartDate}&date_to=${currentEndDate}&status=${encodeURIComponent(statusCodes)}&service=${serviceType}`;

            return url;
        }

        function renderRegistrationTable(data, filterLabel) {
            let html = '';
            let totals = {
                total_pending: 0,
                total_activated: 0,
                last_week_rejected: 0,
                last_week_activated: 0
            };

            if (data.length === 0) {
                html = '<tr><td colspan="6" class="text-center">No registration data found</td></tr>';
                $('#registrationFooter').hide();
            } else {
                data.forEach(function(row) {
                    // Calculate totals
                    totals.total_pending += row.total_pending;
                    totals.total_activated += row.total_activated;
                    totals.last_week_rejected += row.last_week_rejected;
                    totals.last_week_activated += row.last_week_activated;

                    // Build registration URLs with proper status codes
                    const pendingUrl = buildRegistrationUrl(row.section_id, row.total_pending, 'RS_NEW,RS_PEN');
                    const activatedUrl = buildRegistrationUrl(row.section_id, row.total_activated, 'RS_APP');
                    const rejectedUrl = buildRegistrationUrl(row.section_id, row.last_week_rejected, 'RS_REJ');
                    const approvedUrl = buildRegistrationUrl(row.section_id, row.last_week_activated, 'RS_APP');

                    html += `
                        <tr>
                            <td class="text-center">${row.s_no}</td>
                            <td>${row.section_name}</td>
                            <td class="text-center"><a href="${pendingUrl}" target="_blank" data-section="${row.section_name}" data-type="registration" data-status="pending">${row.total_pending}</a></td>
                            <td class="text-center"><a href="${activatedUrl}" target="_blank" data-section="${row.section_name}" data-type="registration" data-status="activated">${row.total_activated}</a></td>
                            <td class="text-center"><a href="${rejectedUrl}" target="_blank" data-section="${row.section_name}" data-type="registration" data-status="rejected" data-filter="${filterLabel}">${row.last_week_rejected}</a></td>
                            <td class="text-center"><a href="${approvedUrl}" target="_blank" data-section="${row.section_name}" data-type="registration" data-status="approved" data-filter="${filterLabel}">${row.last_week_activated}</a></td>
                        </tr>
                    `;
                });

                // Update totals in footer
                $('#regTotalPending').text(totals.total_pending);
                $('#regTotalActivated').text(totals.total_activated);
                $('#regTotalRejected').text(totals.last_week_rejected);
                $('#regTotalApproved').text(totals.last_week_activated);
                $('#registrationFooter').show();
            }
            $('#registrationBody').html(html);
        }

        function renderApplicationTable(data, filterLabel) {
            let html = '';
            let totals = {
                noc_total_submitted: 0,
                noc_total_disposed: 0,
                noc_total_objected: 0,
                noc_total_pending: 0,
                noc_total_pending_with_objected: 0,
                noc_disposed_last_week: 0,
                mut_total_submitted: 0,
                mut_total_disposed: 0,
                mut_total_objected: 0,
                mut_total_pending: 0,
                mut_total_pending_with_objected: 0,
                mut_disposed_last_week: 0
            };

            if (data.length === 0) {
                html = '<tr><td colspan="18" class="text-center">No application data found</td></tr>';
                $('#applicationFooter').hide();
            } else {
                data.forEach(function(row) {
                    const sectionId = row.section_id;

                    // Calculate NOC totals
                    totals.noc_total_submitted += row.noc.total_submitted;
                    totals.noc_total_disposed += row.noc.total_disposed;
                    totals.noc_total_objected += row.noc.total_objected;
                    totals.noc_total_pending += row.noc.total_pending;
                    totals.noc_total_pending_with_objected += row.noc.total_pending_with_objected;
                    totals.noc_disposed_last_week += row.noc.disposed_last_week;

                    // Calculate Mutation totals
                    totals.mut_total_submitted += row.mutation.total_submitted;
                    totals.mut_total_disposed += row.mutation.total_disposed;
                    totals.mut_total_objected += row.mutation.total_objected;
                    totals.mut_total_pending += row.mutation.total_pending;
                    totals.mut_total_pending_with_objected += row.mutation.total_pending_with_objected;
                    totals.mut_disposed_last_week += row.mutation.disposed_last_week;

                    html += `
                        <tr>
                            <td class="text-center">${row.s_no}</td>
                            <td>${row.section_name}</td>
                            <!-- NOC Data -->
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'NOC', 'submitted')}" target="_blank" data-section="${row.section_name}" data-type="NOC" data-status="submitted">${row.noc.total_submitted}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'NOC', 'disposed')}" target="_blank" data-section="${row.section_name}" data-type="NOC" data-status="disposed">${row.noc.total_disposed}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'NOC', 'objected')}" target="_blank" data-section="${row.section_name}" data-type="NOC" data-status="objected">${row.noc.total_objected}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'NOC', 'pending')}" target="_blank" data-section="${row.section_name}" data-type="NOC" data-status="pending">${row.noc.total_pending}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'NOC', 'pending_objected')}" target="_blank" data-section="${row.section_name}" data-type="NOC" data-status="pending_objected">${row.noc.total_pending_with_objected}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'NOC', 'disposed_last_week')}" target="_blank" data-section="${row.section_name}" data-type="NOC" data-status="disposed_last_week" data-filter="${filterLabel}">${row.noc.disposed_last_week}</a></td>
                            <!-- Mutation Data -->
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'SUB_MUT', 'submitted')}" target="_blank" data-section="${row.section_name}" data-type="Mutation" data-status="submitted">${row.mutation.total_submitted}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'SUB_MUT', 'disposed')}" target="_blank" data-section="${row.section_name}" data-type="Mutation" data-status="disposed">${row.mutation.total_disposed}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'SUB_MUT', 'objected')}" target="_blank" data-section="${row.section_name}" data-type="Mutation" data-status="objected">${row.mutation.total_objected}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'SUB_MUT', 'pending')}" target="_blank" data-section="${row.section_name}" data-type="Mutation" data-status="pending">${row.mutation.total_pending}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'SUB_MUT', 'pending_objected')}" target="_blank" data-section="${row.section_name}" data-type="Mutation" data-status="pending_objected">${row.mutation.total_pending_with_objected}</a></td>
                            <td class="text-center"><a href="${buildApplicationUrl(sectionId, 'SUB_MUT', 'disposed_last_week')}" target="_blank" data-section="${row.section_name}" data-type="Mutation" data-status="disposed_last_week" data-filter="${filterLabel}">${row.mutation.disposed_last_week}</a></td>
                        </tr>
                    `;
                });

                // Update totals in footer
                $('#nocTotalSubmitted').text(totals.noc_total_submitted);
                $('#nocTotalDisposed').text(totals.noc_total_disposed);
                $('#nocTotalObjected').text(totals.noc_total_objected);
                $('#nocTotalPending').text(totals.noc_total_pending);
                $('#nocTotalPendingObjected').text(totals.noc_total_pending_with_objected);
                $('#nocTotalDisposedLastWeek').text(totals.noc_disposed_last_week);

                $('#mutTotalSubmitted').text(totals.mut_total_submitted);
                $('#mutTotalDisposed').text(totals.mut_total_disposed);
                $('#mutTotalObjected').text(totals.mut_total_objected);
                $('#mutTotalPending').text(totals.mut_total_pending);
                $('#mutTotalPendingObjected').text(totals.mut_total_pending_with_objected);
                $('#mutTotalDisposedLastWeek').text(totals.mut_disposed_last_week);

                $('#applicationFooter').show();
            }
            $('#applicationBody').html(html);
        }

        function handleFutureApplications(data, filterLabel) {
            // This function will handle future application types
            $('#futureApplications').html('');
        }

        function showError(message) {
            $('#registrationBody').html(`<tr><td colspan="6" class="text-center text-danger">${message}</td></tr>`);
            $('#applicationBody').html(`<tr><td colspan="18" class="text-center text-danger">${message}</td></tr>`);
            $('#registrationFooter').hide();
            $('#applicationFooter').hide();
        }

        // Print Registration Table Only
        function printRegistration() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams(formData);
            window.open(`{{ route('registrations.print-registration') }}?${params.toString()}`, '_blank');
        }

        // Print Application Table Only
        function printApplication() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams(formData);
            window.open(`{{ route('registrations.print-application') }}?${params.toString()}`, '_blank');
        }

        // Print Both Tables
        function printBoth() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams(formData);
            window.open(`{{ route('registrations.print-both') }}?${params.toString()}`, '_blank');
        }

        // Export functions
        function exportPDF() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams(formData);
            window.location.href = `{{ route('registrations.export-pdf') }}?${params.toString()}`;
        }

        function exportCSV() {
            const formData = new FormData($('#filterForm')[0]);
            const params = new URLSearchParams(formData);
            window.location.href = `{{ route('registrations.export-csv') }}?${params.toString()}`;
        }

        // Auto-refresh data every 5 minutes
        setInterval(loadData, 300000);
    </script>
@endsection
