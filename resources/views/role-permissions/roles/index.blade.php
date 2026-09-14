@extends('layouts.app')

@section('title', 'Roles')

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
        <div class="breadcrumb-title pe-3">Settings</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item">Application Configuration</li>
                    <li class="breadcrumb-item active" aria-current="page">Roles</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>
    <div>
        <div class="col pt-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        @haspermission('create role')
                            <a href="{{ url('edharti/roles/create') }}"><button class="btn btn-primary">+ Add Role</button></a>
                        @endhaspermission
                    </div>
                    <h6 class="mb-0 text-uppercase tabular-record_font pb-4">Roles</h6>
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                    </table>

                    {{-- <table class="table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                                <tr class="">
                                    <th scope="row">{{ $key + 1 }}</th>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            @haspermission('update role')
                                                <a href="{{ url('roles/' . $role->id . '/edit') }}"><button type="button"
                                                        class="btn btn-primary px-5">Edit</button></a>
                                            @endhaspermission
                                            @haspermission('delete role')
                                                <a href="{{ url('roles/' . $role->id . '/delete') }}"> <button type="button"
                                                        class="btn btn-danger px-5">Delete</button></a>
                                            @endhaspermission
                                            @haspermission('create role')
                                                <a href="{{ url('roles/' . $role->id . '/give-permissions') }}"> <button
                                                        type="button" class="btn btn-warning px-5">Add / Edit Role
                                                        Permission</button></a>
                                            @endhaspermission
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> --}}
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
                    url: "{{ route('get.roles') }}",
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
