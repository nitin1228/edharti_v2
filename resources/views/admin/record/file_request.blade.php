@extends('layouts.app')
@section('title', 'Request File From Record Room')
@section('content')
    <link href="{{ asset('assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet" />
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Record Room</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Record Room</li>
                    <li class="breadcrumb-item active" aria-current="page">File Request</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>

    @if(auth()->user()->hasRole('record-room'))
      <div class="card">
        <div class="card-body">
            <div class="row align-items-end pb-4">
                <!-- State Dropdown -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="mb-3">
                        <label for="state" class="form-label">Locality </label>
                        <select name="localityRecord" id="locality_record_at_record" class="form-select">
                            <option value="">Select</option>
                            @foreach ($colonyList as $colony)
                                <option value="{{ $colony->id }}">{{ $colony->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status Dropdown -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Block</label>
                       <select name="blockRecord" id="block_record_at_record"
                                    class="form-select alphaNum-hiphenForwardSlash">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>


                <div class="col-lg-2 col-md-4 col-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Plot</label>
                         <select name="plotRecord" id="plot_record_at_record"
                            class="form-select plotNoAlpaMix">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons aligned to right -->
                <div class="col-lg-4 col-md-6 col-12 d-flex justify-content-between align-items-end gap-2 pb-3">
                    <div>
                        <label class="d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="resetValuesForRecordRoomRole">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="bs-stepper gap-4 vertical">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="form-group py-3">
                                    <label for="locality_record" class="quesLabel">Locality<span
                                            class="text-danger">*</span></label>
                                    <select name="localityRecord" id="locality_record" class="form-select">
                                        <option value="">Select</option>
                                        @foreach ($colonyList as $colony)
                                            <option value="{{ $colony->id }}">{{ $colony->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="locality_recordError" class="text-danger text-left"></span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="form-group py-3">
                                    <label for="block_record" class="quesLabel">Block No. / Sector<span
                                            class="text-danger">*</span></label>
                                    <select name="blockRecord" id="block_record"
                                        class="form-select alphaNum-hiphenForwardSlash">
                                        <option value="">Select</option>
                                    </select>
                                    <span id="block_recordError" class="text-danger text-left"></span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="form-group py-3">
                                    <label for="plot_record" class="quesLabel">Plot<span
                                            class="text-danger">*</span></label>
                                    <select name="plotRecord" id="plot_record"
                                        class="form-select plotNoAlpaMix">
                                        <option value="">Select</option>
                                    </select>
                                    <span id="plot_recordError" class="text-danger text-left"></span>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end gap-4 pb-3">
                                <div>
                                    <label class="d-block">&nbsp;</label>
                                    <button type="button" class="btn btn-primary" id="search">Search</button>
                                </div>
                                <div>
                                    <label class="d-block">&nbsp;</label>
                                    <button type="button" class="btn btn-primary" id="reset">Reset</button>
                                </div>
                            </div>
                        </div>

                    <div class="mt-3 fw-bold text-danger fs-5">
                        Note: This page is intended only for requesting files from records for section use of physical files. It is not meant for requesting scans for new applications. Scanning requests are automatically generated by the system upon receipt of the application.
                    </div>
                </div>

                <div class="pt-4" id="fileRequestSearchDiv" style="display: none;">
                    <table id="fileRequestSearch" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Colony Code</th>
                                <th>Block</th>
                                <th>Plot No.</th>
                                <th>File Location</th>
                                <th>Transaction Section</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    @endif

    
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4 text-uppercase">Already Requested Files</h5>
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Property ID</th>
                        <th>Colony</th>
                        <th>Block</th>
                        <th>Plot No.</th>
                        <th>File Location</th>
                        <th>Request Section</th>
                        <th>Date of Request</th>
                        <!-- <th>Current Section</th> -->
                        <th>Request Remark</th>
                        @if(auth()->user()->hasRole('section-officer'))
                            <th>File Upload</th>
                            <th>File Returned</th>
                            <th>Action</th>
                        @endif
                        @if(auth()->user()->hasRole('record-room'))
                            <th>File Upload</th>
                            <th>File Received</th>
                            <th>Action</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>




<div class="modal fade" id="requestFileModal" tabindex="-1" aria-labelledby="requestFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestFileModalLabel">Request File</h5>
            </div>
            <form id="requestFileForm" method="POST" action="{{ route('recordRoom.requestFile') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="recordIdInput" name="record_id" />
                    <!-- Add any other inputs here if needed -->
                    Are you sure you want to request this file?
                </div>
                <div class="modal-body">
                    <textarea class="form-control mb-3" name="request_remark" id="request_remark" rows="3" placeholder="Enter any remarks for request" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Yes, Request</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeFileRequestModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <div class="modal fade" id="cancelRequestFileModal" tabindex="-1" aria-labelledby="cancelRequestFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelRequestFileModalLabel">Cancel File</h5>
                </div>
                <form id="requestFileForm" method="POST" action="{{ route('recordRoom.cancelRequestFile') }}">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="cancelRecordIdInput" name="cancel_record_id" />
                        <!-- Add any other inputs here if needed -->
                        Are you sure you want to cancel this file request?
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control mb-3" name="cancel_request_remark" id="cancel_request_remark" rows="3" placeholder="Enter any remarks for cancel request" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Yes, Request</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeCancelFileRequestModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--end stepper three-->
    @include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
   <script>
     //get all blocks of selected locality
        $('#locality_record').on('change', function() {
            var locality = this.value;
            $("#block_record").html('');
            $.ajax({
                url: "{{ route('localityBlocks') }}",
                type: "POST",
                data: {
                    locality: locality,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#block_record').html('<option value="">Select</option>');
                    $.each(result, function(key, value) {
                        $("#block_record").append('<option value="' + value.block_no + '">' + value
                            .block_no + '</option>');
                    });
                    //#Start :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                    $("#landUse_org, #landUseSubtype_org").html('<option value="">Select</option>');
                    $.ajax({
                        url: "{{ route('landTypes') }}",
                        type: "POST",
                        data: {
                            locality: locality,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            console.log(result);
                            // Populate Property Types
                            if (result.propertyTypes?.length) {
                                $("#landUse_org").append(
                                    result.propertyTypes.map(type =>
                                        `<option value="${type.id}">${type.item_name}</option>`
                                    ).join('')
                                );
                            }
                            // Populate Property Sub Types
                            // if (result.propertySubtypes?.length) {
                            //     $("#landUseSubtype_org").append(
                            //         result.propertySubtypes.map(subtype => `<option value="${subtype.id}">${subtype.item_name}</option>`).join('')
                            //     );
                            // }
                        }
                    });
                    //#End :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                }
            });
        });


         //get all plots of selected block
        $('#block_record').on('change', function() {
            var locality = $('#locality_record').val();
            var block = this.value;
            $("#plot_record").html('');
            $.ajax({
                url: "{{ route('blockPlots') }}",
                type: "POST",
                data: {
                    locality: locality,
                    block: block,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    // console.log(result);
                    $('#plot_record').html('<option value="">Select Plot</option>');
                    $.each(result, function(key, value) {

                        $("#plot_record").append('<option value="' + value + '">' + value +
                            '</option>');
                    });
                }
            });
        });






        //get all blocks of selected locality
        $('#locality_record_at_record').on('change', function() {
            var locality = this.value;
            $("#block_record_at_record").html('');
            $.ajax({
                url: "{{ route('localityBlocks') }}",
                type: "POST",
                data: {
                    locality: locality,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#block_record_at_record').html('<option value="">Select</option>');
                    $.each(result, function(key, value) {
                        $("#block_record_at_record").append('<option value="' + value.block_no + '">' + value
                            .block_no + '</option>');
                    });
                    //#Start :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                    $("#landUse_org, #landUseSubtype_org").html('<option value="">Select</option>');
                    $.ajax({
                        url: "{{ route('landTypes') }}",
                        type: "POST",
                        data: {
                            locality: locality,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            console.log(result);
                            // Populate Property Types
                            if (result.propertyTypes?.length) {
                                $("#landUse_org").append(
                                    result.propertyTypes.map(type =>
                                        `<option value="${type.id}">${type.item_name}</option>`
                                    ).join('')
                                );
                            }
                            // Populate Property Sub Types
                            // if (result.propertySubtypes?.length) {
                            //     $("#landUseSubtype_org").append(
                            //         result.propertySubtypes.map(subtype => `<option value="${subtype.id}">${subtype.item_name}</option>`).join('')
                            //     );
                            // }
                        }
                    });
                    //#End :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                }
            });
        });


         //get all plots of selected block
        $('#block_record_at_record').on('change', function() {
            var locality = $('#locality_record_at_record').val();
            var block = this.value;
            $("#plot_record_at_record").html('');
            $.ajax({
                url: "{{ route('blockPlots') }}",
                type: "POST",
                data: {
                    locality: locality,
                    block: block,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    // console.log(result);
                    $('#plot_record_at_record').html('<option value="">Select Plot</option>');
                    $.each(result, function(key, value) {

                        $("#plot_record_at_record").append('<option value="' + value + '">' + value +
                            '</option>');
                    });
                }
            });
        });




        $(document).ready(function() {
            $('#example').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('recordRoom.getFilesRequestData') }}",
                    data: function(d) {
                        d.locality_record_at_record = $('#locality_record_at_record').val();
                        d.block_record_at_record = $('#block_record_at_record').val();
                        d.plot_record_at_record = $('#plot_record_at_record').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'old_property_id',
                        name: 'record_room_files.old_property_id'
                    },
                    {
                        data: 'colony_code',
                        name: 'old_colonies.name'
                    },
                    {
                        data: 'block',
                        name: 'block'
                    },
                    {
                        data: 'plot',
                        name: 'plot'
                    },
                    {
                        data: 'file_location',
                        name: 'record_room_files.file_location'
                    },
                    {
                        data: 'request_section',
                        name: 'request_section'
                    },
                    {
                        data: 'date_of_request',
                        name: 'date_of_request',
                        render: function (data) {
                            if (!data) return '';
                            const date = new Date(data);
                            return date.toLocaleDateString('en-GB'); // dd-mm-yyyy
                        }
                    },
                    // {
                    //     data: 'current_section',
                    //     name: 'current_section'
                    // },
                    {
                        data: 'request_remark',
                        name: 'request_remark'
                    },
                    @if(auth()->user()->hasRole('section-officer'))
                     {
                            data: 'sectionFileUpload',
                            name: 'sectionFileUpload',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'sectionFileReturned',
                            name: 'sectionFileReturned',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'sectionAction',
                            name: 'sectionAction',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                    @if(auth()->user()->hasRole('record-room'))
                        {
                            data: 'fileUpload',
                            name: 'fileUpload',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'fileReceived',
                            name: 'fileReceived',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    @endif
                ]
            });



            $(document).on('change', '#locality_record_at_record', function() {
               $('#example').DataTable().ajax.reload();
            });
            $(document).on('change', '#block_record_at_record', function() {
               $('#example').DataTable().ajax.reload();
            });
            $(document).on('change', '#plot_record_at_record', function() {
               $('#example').DataTable().ajax.reload();
            });



            $('#search').on('click', function() {
                const locality = $('#locality_record').val();
                const block = $('#block_record').val();
                const plot = $('#plot_record').val();
                if (locality === '') {
                    $('#locality_recordError').text('Please select locality');
                    return false;
                } else {
                    $('#locality_recordError').text('');
                }
                if (block === '') {
                    $('#block_recordError').text('Please select block');
                    return false;
                } else {
                    $('#block_recordError').text('');
                }
                if (plot === '') {
                    $('#plot_recordError').text('Please select plot');
                    return false;
                } else {
                    $('#plot_recordError').text('');
                }
                $("#fileRequestSearchDiv").css("display", "block");
                if ($.fn.DataTable.isDataTable('#fileRequestSearch')) {
                    $('#fileRequestSearch').DataTable().clear().destroy();
                }
                $('#fileRequestSearch').DataTable({
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    ajax: {
                        url: "{{ route('recordRoom.files') }}",
                        data: function(d) {
                            d.locality_record = $('#locality_record').val();
                            d.block_record = $('#block_record').val();
                            d.plot_record = $('#plot_record').val();
                        }
                    },
                    columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'colony_code',
                                name: 'colony_code'
                            },
                            {
                                data: 'block',
                                name: 'block'
                            },
                            {
                                data: 'plot',
                                name: 'plot'
                            },
                            {
                                data: 'file_location',
                                name: 'file_location'
                            },
                            {
                                data: 'transaction_section_code',
                                name: 'transaction_section_code'
                            },
                            {
                                data: 'action',
                                name: 'action'
                            }
                        ]
                });
            });
        });


        $('#reset').click(function() {
            $('#locality_record').val('');
            $('#block_record').val('');
            $('#plot_record').val('');
            $("#fileRequestSearchDiv").css("display", "none");

        });

        $('#resetValuesForRecordRoomRole').click(function() {
            $('#locality_record_at_record').val('');
            $('#block_record_at_record').val('');
            $('#plot_record_at_record').val('');
            $('#example').DataTable().ajax.reload();

        });

        function openRequestFileModal(recordId) {
        // Set the recordId in a hidden input or somewhere in the modal
        $('#recordIdInput').val(recordId);

        // Open the modal
        $('#requestFileModal').modal('show');
    }

    $('#closeFileRequestModal').click(function () {
        $('#requestFileModal').modal('hide');
    });



    function uploadFile(data){
        const file = data.files[0];
        const recordId = data.dataset.recordId;
        const name = data.name;
        

        if (!file) {
            console.error("No file selected");
            return;
        }

        console.log(recordId,name,file);
        

        // Prepare form data
        let formData = new FormData();
        formData.append("file", file);        // file input
        formData.append("record_id", recordId); // extra data
        formData.append("name", name); // extra data
        formData.append("_token", "{{ csrf_token() }}"); // Laravel CSRF token

        console.log(formData);
        

        // AJAX request
        try{
            $.ajax({
                url: "{{ route('request.file.upload') }}", // Update with your route
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                     showSuccess(response.message, window.location.href)
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        } catch(err){
            console.log(err.message);
            
        }
    }

    function acceptRequest(id) {
        $.ajax({
            url: "{{ route('request.file.accept') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);

                if (response && response.status === 'failure') {
                    showError(response.message, window.location.href);
                    return;
                }

                showSuccess(response.message, window.location.href);
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
            }
        });
    }

function returnRequest(id) {
        $.ajax({
            url: "{{ route('request.file.return') }}",
            type: "POST",
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log(response);

                if (response && response.status === 'failure') {
                    showError(response.message, window.location.href);
                    return;
                }

                showSuccess(response.message, window.location.href);
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
            }
        });
    }

   



    function openCancelRequestFileModal(recordId) {
        // Set the recordId in a hidden input or somewhere in the modal
        $('#cancelRecordIdInput').val(recordId);

        // Open the modal
        $('#cancelRequestFileModal').modal('show');
    }

    $('#closeCancelFileRequestModal').click(function () {
        $('#cancelRequestFileModal').modal('hide');
    });



   
    


   </script>
@endsection
