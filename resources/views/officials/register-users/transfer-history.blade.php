@extends('layouts.app')

@section('title', 'IT Cell Transfer History')

@section('content')

<style>
    div.dt-buttons {
        float: none !important;
        width: 33%;
    }

    div.dt-buttons.btn-group {
        margin-bottom: 20px;
    }

    div.dt-buttons.btn-group .btn {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 4px;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .status-rejected {
        color: #dc3545;
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
    }
    
    .view-remark-link {
        color: #007bff;
        font-size: 11px;
        cursor: pointer;
        text-decoration: underline;
        margin-left: 5px;
    }
    
    .view-remark-link:hover {
        color: #0056b3;
    }

    @media (max-width: 768px) {
        div.dt-buttons {
            width: 100%;
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
    <div class="breadcrumb-title pe-3">IT Cell</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-alt"></i>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Transfer History</li>
            </ol>
        </nav>
    </div>
</div>

<hr>

<div class="card">
    <div class="card-body">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-4">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from_date" name="from_date">
                </div>
                <div class="col-md-4">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to_date" name="to_date">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" id="filterBtn" class="btn btn-primary d-block">Filter</button>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <table id="transferHistoryTable" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Applicant Number / Status</th>
                    <th>Property ID</th>
                    <th>Manual Entry Address</th>
                    <th>Transferred to Address</th>
                    <th>Transferred to Section</th>
                    <th>Transferred By</th>
                    <th>Transferred At</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated by DataTable -->
            </tbody>
        </table>
    </div>
</div>

<!-- View Remark Modal -->
<div class="modal fade" id="viewRemarkModal" tabindex="-1" aria-labelledby="viewRemarkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRemarkModalLabel">
                    <i class="bx bx-comment-detail"></i> Rejection Remarks
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Applicant Number:</label>
                    <p id="remark_applicant_number" class="mb-3"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Remarks:</label>
                    <div id="remark_content" class="p-3 bg-light rounded" style="white-space: pre-wrap; word-wrap: break-word;"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Rejected By:</label>
                    <p id="remark_rejected_by" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Rejected At:</label>
                    <p id="remark_rejected_at" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('include.loader')
@include('include.alerts.ajax-alert')

@endsection

@section('footerScript')

<script type="text/javascript">
$(document).ready(function() {
    var table = $('#transferHistoryTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: "{{ route('it-cell.transfer-history.data') }}",
            data: function(d) {
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
            }
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
            },
            {
                data: 'reg_app_no',
                name: 'reg_app_no',
                render: function(data, type, row) {
                    var statusHtml = '';
                    if (row.status_code === 'RS_REJ') {
                        statusHtml = `<div class="status-rejected">
                            <i class="bx bx-x-circle"></i> Rejected 
                            <span class="view-remark-link" data-id="${row.id}" data-applicant="${data}" data-remark="${row.remarks}" data-rejected-by="${row.transferred_by}" data-rejected-at="${row.created_at}">
                                (View Remark)
                            </span>
                        </div>`;
                    }
                    return `<div class="fw-bold">${data}</div>${statusHtml}`;
                }
            },
            {
                data: 'old_property_id',
                name: 'old_property_id',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'manual_entry_address',
                name: 'manual_entry_address',
                render: function(data, type, row) {
                    if (data && data !== 'N/A') {
                        return `<div class="text-wrap" style="max-width: 250px;" data-bs-toggle="tooltip" title="${data}">${data.length > 50 ? data.substring(0, 50) + '...' : data}</div>`;
                    }
                    return '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'transferred_to_address',
                name: 'transferred_to_address',
                render: function(data, type, row) {
                    if (data && data !== 'N/A') {
                        return `<div class="text-wrap" style="max-width: 250px;" data-bs-toggle="tooltip" title="${data}">${data.length > 50 ? data.substring(0, 50) + '...' : data}</div>`;
                    }
                    return '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: 'section',
                name: 'section',
                orderable: false,
                searchable: false,
            },
            {
                data: 'transferred_by',
                name: 'transferred_by',
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    return `<div class="text-nowrap">${data}</div>`;
                }
            }
        ],
        dom: '<"top"Blf>rt<"bottom"ip><"clear">',
        buttons: [
            'csv', 
            'excel'
        ],
        order: [[7, 'desc']],
        drawCallback: function(settings) {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Apply filter on button click
    $('#filterBtn').click(function() {
        table.ajax.reload();
    });
    
    // View Remark click handler
    $(document).on('click', '.view-remark-link', function() {
        var applicantNumber = $(this).data('applicant');
        var remark = $(this).data('remark');
        var rejectedBy = $(this).data('rejected-by');
        var rejectedAt = $(this).data('rejected-at');
        
        // Populate modal
        $('#remark_applicant_number').text(applicantNumber || 'N/A');
        $('#remark_content').text(remark || 'No remarks provided.');
        $('#remark_rejected_by').text(rejectedBy || 'N/A');
        $('#remark_rejected_at').text(rejectedAt || 'N/A');
        
        // Show modal
        $('#viewRemarkModal').modal('show');
    });
});
</script>

@endsection