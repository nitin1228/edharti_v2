@extends('layouts.app')

@section('title', 'SMS')

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

        .pagination .active a {
            color: #ffffff !important;
        }

        .required-error-message {
            display: none;
        }

        .required-error-message {
            margin-left: -1.5em;
            margin-top: 3px;
        }

        .form-check-inputs[type=checkbox] {
            border-radius: .25em;
        }

        .form-check .form-check-inputs {
            float: left;
            margin-left: -1.5em;
        }

        .form-check-inputs {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0;
        }

        .testSms {
            cursor: pointer;
        }

        .loader {
            display: none;
            border: 8px solid #7e7487;
            border-top: 8px solid #4fc4c1;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            position: absolute;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
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
                    <li class="breadcrumb-item active" aria-current="page">SMS</li>
                </ol>
                </li>
                </ol>

            </nav>
        </div>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->

    <hr>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between py-3">
                <h6 class="mb-0 text-uppercase tabular-record_font align-self-end">SMS</h6>
                <a href="{{ route('settings.sms.create') }}"><button class="btn btn-primary">+ Add SMS</button></a>
            </div>
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Test Sms</th>
                        <th scope="col">Action</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Api</th>
                        <th scope="col">Secret ID</th>
                        <th scope="col">Secret Token</th>
                        <th scope="col">status</th>
                        @haspermission('settings.sms.update')
                            <th scope="col" class="not-export">Edit</th>
                        @endhaspermission
                    </tr>
                </thead>
            </table>
        </div>
    </div>


@endsection
@section('footerScript')
    <script>
        $('.testSms').on('click', function() {
            var urlTemplate = "{{ route('settings.sms.smsTest', ['id' => 'ID_PLACEHOLDER']) }}";
            var smsId = $(this).data('id');
            var url = urlTemplate.replace('ID_PLACEHOLDER', smsId);
            var span = $(this);
            var loader = $(this).siblings('.loader');
            var error = $(this).siblings('.testSmsError');
            var success = $(this).siblings('.testSmsSuccess');
            span.hide()
            loader.show();
            $.ajax({
                url: url,
                type: "GET",
                dataType: "JSON",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    //console.log(response);
                    if (response.success === true) {
                        span.show()
                        loader.hide();
                        success.html(response.message)
                    } else if (response.success === false) {
                        span.show()
                        loader.hide();
                        error.html(response.message)
                    } else {}
                },
                error: function(response) {
                    console.log(response);
                }

            })
        })

        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('get.sms.settings') }}",
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // Auto-increment ID based on row index
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'test_sms',
                        name: 'test_sms'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'vendor',
                        name: 'vendor'
                    },
                    {
                        data: 'api',
                        name: 'api'
                    },
                    {
                        data: 'secretId',
                        name: 'secretId'
                    },
                    {
                        data: 'secretToken',
                        name: 'secretToken'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'userAction',
                        name: 'userAction',
                        orderable: false,
                        searchable: false,
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
                buttons: [
                    'csv',
                    'excel',
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'portrait', // Set PDF orientation to portrait
                        pageSize: 'A4', // Optional: Set page size to A4
                        // exportOptions: {
                        //     columns: ':visible', // Export all visible columns
                        // },
                        exportOptions: {
                            columns: ':not(.not-export)', // Export all columns except those marked with .not-export
                        },
                        customize: function(doc) {
                            doc.pageOrientation = 'portrait'; // Set the page orientation explicitly
                            doc.defaultStyle.fontSize = 7; // Adjust font size to fit more content
                            doc.styles.tableHeader.fontSize = 8; // Table header size
                            doc.content[1].table.widths = ['5%', '10%', '5%', '5%', '20%', '20%',
                                '20%', '10%'
                            ]; // Adjust columns width manually
                            // Adjust page orientation by manipulating pdfMake layout directly
                            doc.pageSize = {
                                width: 700.89, // Width in points (A4 in portrait)
                                height: 841.89 // Height in points (A4 in portrait)
                            };
                        }
                    }
                ]
            });
        });
    </script>

@endsection
