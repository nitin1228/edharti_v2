@extends('layouts.app')
@section('title', 'Revenues')
@section('content')
    <style>
        .btn-month {
            cursor: pointer;
            color: #116d6e !important;
            font-weight: bold !important;
            text-decoration: none !important;
            transition: opacity 0.3s ease;
        }

        .btn-month:hover {
            color: #0e5a5b !important;
            opacity: 0.8;
            text-decoration: underline !important;
        }

        #alertMessage .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            position: relative;
        }

        #alertMessage .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        #alertMessage .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        #alertMessage .alert i {
            margin-right: 10px;
        }

        #alertMessage .btn-close {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .nav-tabs .nav-link {
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: 600;
        }
    </style>

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Revenue</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ 'dashboard' }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Revenue</li>
                    <li class="breadcrumb-item active" aria-current="page">Add</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="revenueForm">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="financial_year" class="form-label">Financial Year <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="financial_year" name="financial_year" required>
                            <option value="">Select Financial Year</option>
                            @php
                                $currentMonth = date('n');
                                $currentYear = date('Y');
                                $currentFinancialYear = $currentMonth >= 4 ? $currentYear : $currentYear - 1;
                                $startYear = $currentFinancialYear - 5;
                                $endYear = $currentFinancialYear;
                            @endphp
                            @for ($year = $startYear; $year <= $endYear; $year++)
                                @php
                                    $financialYear = $year . '-' . ($year + 1);
                                    $isSelected = $year == $currentFinancialYear ? 'selected' : '';
                                @endphp
                                <option value="{{ $financialYear }}" {{ $isSelected }}>{{ $financialYear }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="month" class="form-label">Month <span class="text-danger">*</span></label>
                        <select class="form-select" id="month" name="month" required>
                            <option value="">Select Month</option>
                            @php
                                $months = [
                                    'April',
                                    'May',
                                    'June',
                                    'July',
                                    'August',
                                    'September',
                                    'October',
                                    'November',
                                    'December',
                                    'January',
                                    'February',
                                    'March',
                                ];
                            @endphp
                            @foreach ($months as $month)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="revenue_amount_without_ntrp" class="form-label">Revenue Amount (Without NTRP) <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="revenue_amount_without_ntrp"
                            name="revenue_amount_without_ntrp" required min="0" oninput="validatePositive(this)">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="total_revenue_amount_pfms" class="form-label">Total Revenue Amount (PFMS) <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="total_revenue_amount_pfms"
                            name="total_revenue_amount_pfms" required min="0" oninput="validatePositive(this)">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Save Revenue
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </form>

            <div id="alertMessage" class="mt-3" style="display: none;"></div>

            <!-- Tabs for Summary and Detailed Views -->
            <div class="row mt-5">
                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="revenueTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary"
                                type="button" role="tab">Revenue Summary</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details"
                                type="button" role="tab">Update Revenue Details</button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- Summary Tab -->
                        <div class="tab-pane fade show active table-responsive" id="summary" role="tabpanel">
                            <table id="summaryTable" class="table table-striped table-bordered"
                                style="width: 100% !important;">
                                <thead>
                                    <tr>
                                        <th>Financial Year</th>
                                        <th>Month (Count of months received revenue)</th>
                                        <th>Sum of Revenue Amount (Without NTRP)</th>
                                        <th>Sum of Total Revenue Amount (PFMS)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <!-- Details Tab -->
                        <div class="tab-pane fade table-responsive" id="details" role="tabpanel">
                            <table id="detailsTable" class="table table-striped table-bordered"
                                style="width:100% !important;">
                                <thead>
                                    <tr>
                                        <th>Financial Year</th>
                                        <th>Month</th>
                                        <th>Revenue Amount (Without NTRP)</th>
                                        <th>Total Revenue Amount (PFMS)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Details Modal -->
    <div class="modal fade" id="monthlyDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Monthly Revenue Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 id="modalFinancialYear" class="mb-3"></h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Revenue Amount (Without NTRP)</th>
                                    <th>Total Revenue Amount (PFMS)</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printBreakup()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Revenue Modal -->
    <div class="modal fade" id="editRevenueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Revenue Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editRevenueForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_financial_year" class="form-label">Financial Year</label>
                            <input type="text" class="form-control" id="edit_financial_year" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label for="edit_month" class="form-label">Month</label>
                            <input type="text" class="form-control" id="edit_month" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label for="edit_revenue_amount_without_ntrp" class="form-label">Revenue Amount (Without NTRP)
                                <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control"
                                id="edit_revenue_amount_without_ntrp" name="revenue_amount_without_ntrp" required
                                min="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_total_revenue_amount_pfms" class="form-label">Total Revenue Amount (PFMS)
                                <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control"
                                id="edit_total_revenue_amount_pfms" name="total_revenue_amount_pfms" required
                                min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="updateBtn">
                            <i class="fas fa-save"></i> Update Revenue
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('footerScript')
    <script type="text/javascript">
        function validatePositive(input) {
            if (input.value < 0) {
                input.value = 0;
            }
        }

        $(document).ready(function() {
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear();
            const currentFY = (currentMonth >= 4) ? currentYear : currentYear - 1;

            const monthMap = {
                'April': 4,
                'May': 5,
                'June': 6,
                'July': 7,
                'August': 8,
                'September': 9,
                'October': 10,
                'November': 11,
                'December': 12,
                'January': 1,
                'February': 2,
                'March': 3
            };

            function updateMonths() {
                const selectedFY = $('#financial_year').val();
                if (!selectedFY) {
                    $('#month option').each(function(idx) {
                        if (idx > 0) $(this).prop('disabled', true);
                    });
                    return;
                }
                const fyStart = parseInt(selectedFY.split('-')[0]);
                $('#month option').each(function(idx) {
                    if (idx === 0) return;
                    const monthName = $(this).val();
                    const monthNum = monthMap[monthName];
                    let isEnabled = false;
                    let text = monthName;
                    const isJanMar = (monthName === 'January' || monthName === 'February' || monthName ===
                        'March');
                    const monthYear = isJanMar ? fyStart + 1 : fyStart;
                    if (fyStart < currentFY) {
                        isEnabled = true;
                    } else if (fyStart > currentFY) {
                        isEnabled = false;
                        text += ' (FY not started)';
                    } else {
                        if (monthYear < currentYear) {
                            isEnabled = true;
                        } else if (monthYear > currentYear) {
                            isEnabled = false;
                        } else {
                            if (monthNum < currentMonth) {
                                isEnabled = true;
                            } else if (monthNum === currentMonth) {
                                isEnabled = true;
                            } else {
                                isEnabled = false;
                            }
                        }
                    }
                    $(this).prop('disabled', !isEnabled);
                    $(this).text(text);
                });
            }

            updateMonths();
            $('#financial_year').on('change', updateMonths);
        });

        $(document).ready(function() {
            let summaryTable = null;
            let detailsTable = null;

            function loadData() {
                $.ajax({
                    url: '{{ route('revenues.data') }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Load Summary Table
                        let summaryData = [];
                        if (response.summary && response.summary.length > 0) {
                            $.each(response.summary, function(index, item) {
                                summaryData.push([
                                    item.financial_year,
                                    '<span class="btn-month" data-financial-year="' +
                                    item.financial_year + '">' + item.months_count +
                                    '</span>',
                                    '₹ ' + parseFloat(item.total_revenue_without_ntrp)
                                    .toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }),
                                    '₹ ' + parseFloat(item.total_revenue_pfms)
                                    .toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    })
                                ]);
                            });
                        }

                        if (summaryTable) {
                            summaryTable.clear().destroy();
                        }
                        summaryTable = $('#summaryTable').DataTable({
                            data: summaryData,
                            columns: [{
                                    title: "Financial Year"
                                },
                                {
                                    title: "Month (Count of months received revenue)"
                                },
                                {
                                    title: "Sum of Revenue Amount (Without NTRP)"
                                },
                                {
                                    title: "Sum of Total Revenue Amount (PFMS)"
                                }
                            ],
                            language: {
                                emptyTable: "No data available"
                            }
                        });

                        // Load Details Table
                        let detailsData = [];
                        if (response.all_revenues && response.all_revenues.length > 0) {
                            $.each(response.all_revenues, function(index, item) {
                                detailsData.push([
                                    item.financial_year,
                                    item.month,
                                    '₹ ' + parseFloat(item.revenue_amount_without_ntrp)
                                    .toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }),
                                    '₹ ' + parseFloat(item.total_revenue_amount_pfms)
                                    .toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }),
                                    '<button class="btn btn-sm btn-warning edit-btn" data-id="' +
                                    item.id +
                                    '"><i class="fas fa-edit"></i> Edit</button> ' +
                                    '<button class="btn btn-sm btn-danger delete-btn" data-id="' +
                                    item.id +
                                    '"><i class="fas fa-trash"></i> Delete</button>'
                                ]);
                            });
                        }

                        if (detailsTable) {
                            detailsTable.clear().destroy();
                        }
                        detailsTable = $('#detailsTable').DataTable({
                            data: detailsData,
                            columns: [{
                                    title: "Financial Year"
                                },
                                {
                                    title: "Month"
                                },
                                {
                                    title: "Revenue Amount (Without NTRP)"
                                },
                                {
                                    title: "Total Revenue Amount (PFMS)"
                                },
                                {
                                    title: "Actions",
                                    orderable: false
                                }
                            ],
                            language: {
                                emptyTable: "No data available"
                            }
                        });

                        // Attach click events for month links
                        $('.btn-month').off('click').on('click', function() {
                            let financialYear = $(this).data('financial-year');
                            if (response.details) {
                                showMonthlyDetails(financialYear, response.details);
                            }
                        });

                        // Attach edit button click events
                        $('.edit-btn').off('click').on('click', function() {
                            let id = $(this).data('id');
                            editRevenue(id);
                        });

                        // Attach delete button click events
                        $('.delete-btn').off('click').on('click', function() {
                            let id = $(this).data('id');
                            deleteRevenue(id);
                        });
                    },
                    error: function(xhr) {
                        console.error('Error loading data:', xhr);
                        showAlert('danger', 'Error loading revenue data');
                    }
                });
            }

            function editRevenue(id) {

                var editUrl = "{{ route('revenues.edit', ':id') }}";
                editUrl = editUrl.replace(':id', id);
                // FIXED: Use the correct edit route URL
                $.ajax({
                    // url: '{{ url('revenues') }}/' + id + '/edit',
                    url: editUrl,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#edit_id').val(response.data.id);
                            $('#edit_financial_year').val(response.data.financial_year);
                            $('#edit_month').val(response.data.month);
                            $('#edit_revenue_amount_without_ntrp').val(response.data
                                .revenue_amount_without_ntrp);
                            $('#edit_total_revenue_amount_pfms').val(response.data
                                .total_revenue_amount_pfms);
                            $('#editRevenueModal').modal('show');
                        } else {
                            showAlert('danger', response.message || 'Failed to load revenue data');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        showAlert('danger', 'Error loading revenue data');
                    }
                });
            }

            $('#editRevenueForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                let formData = $(this).serialize();
                let updateBtn = $('#updateBtn');
                updateBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                var updateUrl = "{{ route('revenues.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);

                $.ajax({
                    url: updateUrl,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            $('#editRevenueModal').modal('hide');
                            loadData();
                            $('#editRevenueForm')[0].reset();
                        } else {
                            showAlert('danger', response.message || 'Failed to update record');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;
                            let errorMessage = '';
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    errorMessage += value[0] + '<br>';
                                });
                            } else if (response.message) {
                                errorMessage = response.message;
                            } else {
                                errorMessage = 'Validation failed. Please check your inputs.';
                            }
                            showAlert('danger', errorMessage);
                        } else {
                            showAlert('danger', 'An error occurred while updating the record');
                        }
                    },
                    complete: function() {
                        updateBtn.prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Update Revenue');
                    }
                });
            });

            function deleteRevenue(id) {
                var deleteUrl = "{{ route('revenues.destroy', ':id') }}";
                deleteUrl = deleteUrl.replace(':id', id);
                if (confirm('Are you sure you want to delete this revenue record? This action cannot be undone.')) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                loadData();
                            } else {
                                showAlert('danger', response.message || 'Failed to delete record');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            showAlert('danger', 'Error deleting revenue record');
                        }
                    });
                }
            }

            function showMonthlyDetails(financialYear, details) {
                let monthsData = details[financialYear] || [];
                let html = '';
                let totalWithoutNTRP = 0;
                let totalPFMS = 0;

                $.each(monthsData, function(index, item) {
                    html += '<tr>';
                    html += '<td>' + item.month + '</td>';
                    html += '<td class="text-end">₹ ' + parseFloat(item.revenue_amount_without_ntrp)
                        .toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + '</td>';
                    html += '<td class="text-end">₹ ' + parseFloat(item.total_revenue_amount_pfms)
                        .toLocaleString('en-IN', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + '</td>';
                    html += '</tr>';
                    totalWithoutNTRP += parseFloat(item.revenue_amount_without_ntrp);
                    totalPFMS += parseFloat(item.total_revenue_amount_pfms);
                });

                html += '<tr class="table-info fw-bold">';
                html += '<td class="text-end">Total:</td>';
                html += '<td class="text-end">₹ ' + totalWithoutNTRP.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + '</td>';
                html += '<td class="text-end">₹ ' + totalPFMS.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + '</td>';
                html += '</tr>';

                $('#modalFinancialYear').html('<strong>Financial Year: ' + financialYear + '</strong>');
                $('#modalTableBody').html(html);
                $('#monthlyDetailsModal').modal('show');
            }

            $('#revenueForm').on('submit', function(e) {
                e.preventDefault();
                $('#alertMessage').hide().empty();
                let formData = $(this).serialize();
                let submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

                $.ajax({
                    url: '{{ route('revenues.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            $('#revenueForm')[0].reset();
                            loadData();
                        } else {
                            showAlert('danger', response.message || 'Failed to save record');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let response = xhr.responseJSON;
                            let errorMessage = '';
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    errorMessage += value[0] + '<br>';
                                });
                            } else if (response.message) {
                                errorMessage = response.message;
                            } else {
                                errorMessage = 'Validation failed. Please check your inputs.';
                            }
                            showAlert('danger', errorMessage);
                        } else {
                            showAlert('danger',
                                'Network error. Please check your connection and try again.'
                            );
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-save"></i> Save Revenue');
                    }
                });
            });

            function showAlert(type, message) {
                let alertDiv = $('#alertMessage');
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                if (!message || message.trim() === '') {
                    message = type === 'success' ? 'Operation completed successfully!' :
                        'An error occurred. Please try again.';
                }
                alertDiv.html(`
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i> 
                        <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> 
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `);
                alertDiv.show('slow');
                setTimeout(function() {
                    alertDiv.fadeOut('slow');
                }, 5000);
            }

            $('button[data-bs-target="#details"]').on('shown.bs.tab', function() {
                if (detailsTable) {
                    detailsTable.columns.adjust().draw();
                }
            });

            loadData();
        });

        function printBreakup() {
            var modalContent = $('#monthlyDetailsModal .modal-content').clone();
            modalContent.find('.modal-footer .btn-secondary').remove();
            modalContent.find('.modal-header .btn-close').remove();

            var printHtml = '<!DOCTYPE html><html><head><title>Monthly Revenue Details</title><style>';
            printHtml += 'body { font-family: Arial, sans-serif; padding: 20px; margin: 0; }';
            printHtml += '.modal-content { border: none; box-shadow: none; }';
            printHtml += '.modal-header { background-color: #0d6efd !important; color: white !important; padding: 15px; }';
            printHtml += '.modal-body { padding: 20px; }';
            printHtml += 'table { width: 100%; border-collapse: collapse; }';
            printHtml += 'table, th, td { border: 1px solid #000; }';
            printHtml += 'th, td { padding: 8px; text-align: left; }';
            printHtml += 'th { background-color: #f2f2f2; }';
            printHtml += 'td:not(:first-child) { text-align: right; }';
            printHtml += '.text-end { text-align: right; }';
            printHtml += '.table-info { background-color: #e9ecef; }';
            printHtml += '@media print { body { margin: 0; padding: 15px; } }';
            printHtml += '</style></head><body>';
            printHtml += modalContent.prop('outerHTML');
            printHtml += '</body></html>';

            var printWindow = window.open('', '_blank', 'width=900,height=700');
            printWindow.document.write(printHtml);
            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    printWindow.close();
                };
            };
        }
    </script>
@endsection