@extends('layouts.app')

@section('title', 'Permissions List')

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
    <!--Breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Settings</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item">Application Configuration</li>
                    <li class="breadcrumb-item active" aria-current="page">Permissions</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End -->
    <div>
        <div class="col pt-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        @haspermission('create permission')
                            <a href="{{ url('edharti/permissions/create') }}"><button class="btn btn-primary">+ Add
                                    Permission</button></a>
                        @endhaspermission
                    </div>
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: false, // Disable responsive behavior
                scrollX: true, // Enable horizontal scroll when columns exceed container width
                ajax: {
                    url: "{{ route('get.permissions') }}",
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        width: "5%",
                        targets: 0
                    }, // Set width for "S.No" column
                    {
                        width: "75%",
                        targets: 1
                    }, // Set width for "Name" column
                    {
                        width: "20%",
                        targets: 2
                    }, // Set width for "Action" column
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
                buttons: ['csv', 'excel', 'pdf'],
                autoWidth: false, // Prevent automatic column width adjustment
                order: [] // Ensure no default ordering
            });
        });
    </script>
@endsection
