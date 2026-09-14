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
    </style>
    {{-- <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Club Membership</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Finalized Applications</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--breadcrumb-->
    <hr>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between py-3">
                <h6 class="mb-0 text-uppercase tabular-record_font align-self-end"></h6>
                <a href="{{ route('create.club.membership.form') }}"><button class="btn btn-primary">+ Add
                        New Membership</button></a>
            </div>
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Application No.</th>
                        <th>Name</th>
                        <th>Club Type</th>
                        <th>Designation</th>
                        <th>Central Deputation</th>
                        <th>Superannuation Date</th>
                        {{-- <th>Category</th> --}}
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


                        {{-- <th>Service</th>
                        <th>Allotment</th>
                        <th>Application Date</th>
                        <th>Joining Date</th>
                        <th>Pay Scale</th>
                        <th>Other Info.</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                        <th>Created At</th> --}}
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

@endsection
@section('footerScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                order: [
                    [1, 'desc']
                ], // Assuming 2nd index is `unique_id` in columns array
                ajax: {
                    url: "{{ route('get.club.membership.list') }}",
                    data: function(d) {
                        d.status = $('#status').val(); // Capture the status filter value
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            const isDuplicate = row.DT_RowClass && row.DT_RowClass.includes(
                                'duplicate-row');
                            const style = isDuplicate ?
                                'style="background-color:#ffa8a836;"' : '';
                            return `<span ${style}>${data}</span>`;
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
                    // {
                    //     data: 'category',
                    //     name: 'category'
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },


                    /* {
                        data: 'name_of_service',
                        name: 'name_of_service',
                        render: function(data, type, row, meta) {
                            if (data && data.length > 20) {
                                return `${data.substring(0, 20)}... <button class="btn btn-link p-0 view-full-text" style="text-decoration: none;" data-title="Name of Service" data-content="${data.replace(/"/g, '&quot;')}">View</button>`;
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'year_of_allotment',
                        name: 'year_of_allotment'
                    },
                    {
                        data: 'date_of_application',
                        name: 'date_of_application'
                    },
                    {
                        data: 'date_of_joining_central_deputation',
                        name: 'date_of_joining_central_deputation'
                    },
                    {
                        data: 'pay_scale',
                        name: 'pay_scale'
                    },
                    {
                        data: 'other_relevant_information',
                        name: 'other_relevant_information'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'updated_by',
                        name: 'updated_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }, */
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
