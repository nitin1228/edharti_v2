@extends('layouts.app')
@section('title', 'MIS User Action Logs Details')
@section('content')
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

        .modal-dialog {
            pointer-events: all;
        }
    </style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Settings</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">User Action Activity Logs</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date" class="form-label">Enter from date</label>
                        <input type="date" name="start_date" id="start_date" placeholder="Start Date"
                            class="form-control" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="dateEnd" class="form-label">Enter to date</label>
                        <input type="date" name="end_date" id="end_date" placeholder="End Date" class="form-control" />
                    </div>
                </div>
                <div class="col-md-1 align-self-end">
                    <div class="form-group">
                        <button id="filter" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>User Name</th>
                        <th>Module Name</th>
                        <th>Action Name</th>
                        <th>Action Url</th>
                        <th>Action Date & Time</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Description Modal -->
    <div class="modal fade" id="descModal" tabindex="-1" aria-labelledby="descModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descModalLabel">Full Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-wrap" style="word-wrap: break-word; white-space: normal;">
                    <!-- Full description will be injected here -->
                </div>
            </div>
        </div>
    </div>

@endsection
@section('footerScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ route('getUserActionLogs') }}",
                    type: "GET",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: null,
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // Auto-increment ID based on row index
                        },
                        orderable: false, // Disable ordering on this column
                        searchable: false // Disable searching on this column
                    },
                    {
                        data: 'uname',
                        name: 'uname'
                    },
                    {
                        data: 'mname',
                        name: 'mname'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    /* {
                        data: 'description',
                        name: 'description'
                    }, */
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data, type, row, meta) {
                            if (type === 'display') {
                                // Create a temporary element to extract text content
                                var tempDiv = document.createElement("div");
                                tempDiv.innerHTML = data;
                                var textContent = tempDiv.textContent || tempDiv.innerText || "";

                                if (textContent.length > 50) {
                                    var truncated = textContent.substring(0, 50) + '...';
                                    return '<span>' + truncated + '</span> ' +
                                        '<button type="button" style="text-decoration: none; color:#116d6e;" class="btn btn-sm btn-link view-more" ' +
                                        'data-bs-toggle="modal" data-bs-target="#descModal" ' +
                                        'data-description="' + encodeURIComponent(data) + '">' +
                                        'View More</button>';
                                } else {
                                    return data;
                                }
                            }
                            return data;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
                buttons: ['csv', 'excel', 'pdf']
            });

            // Filter button click event
            $('#filter').click(function() {
                table.draw(); // Redraw the DataTable to apply the date filters
            });
        });

        $(document).on('click', '.view-more', function() {
            var description = decodeURIComponent($(this).data('description'));
            $('#descModal .modal-body').html(description);
        });
    </script>
@endsection
