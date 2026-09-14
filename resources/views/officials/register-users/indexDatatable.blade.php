@extends('layouts.app')

@section('title', 'Register User Listing')

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
       .alertDot {
        width: 9px;
        height: 9px;
        background-color: #007bff;
        border-radius: 50%;
        box-shadow: 0 0 10px #007bff, 0 0 20px #007bff, 0 0 30px #007bff;
        animation: pulse 1.5s infinite;
    }
    
        tr.inactive-approved td,
        table.dataTable.display tbody tr.inactive-approved.odd > .sorting_1, 
        table.dataTable.order-column.stripe tbody tr.inactive-approved.odd > .sorting_1,
        tr.inactive-approved td.sorting_1,
        tr.inactive-approved td.sorting_2,
        tr.inactive-approved td.sorting_3 {
            background-color: #f8d7da !important;
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
        <div class="breadcrumb-title pe-3">Registrations</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Registrations</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            @if (Auth::user()->roles[0]->name != 'it-cell')
                <div class="d-flex justify-content-end">
                    <ul class="d-flex gap-3 flex-wrap">
                   @if (Auth::user()->roles[0]->name == 'deputy-lndo')
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <div class="alertRed"></div>
                            <span class="text-secondary">Action Not Taken By SO</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">|</li>
                    @endif
                   <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <div class="alertGreen"></div>
                            <span class="text-secondary">First Actionable</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">|</li>
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <div class="alertDot"></div>
                            <span class="text-secondary">Actionable</span>
                        </li>     
                   <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <i class="fadeIn animated bx bx-list-ul fs-5" style="color:#6610f2"></i>
                            <span class="text-secondary">MIS Checked</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">|</li>
                       {{--  <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <i class="fadeIn animated bx bx-file-find fs-5" style="color:#20c997"></i>
                            <span class="text-secondary">Scanned Files Checked</span>
                        </li>
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">|</li> --}}
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <i class="lni lni-cloud-upload fs-5" style="color:#fd7e14"></i>
                            <span class="text-secondary">Uploaded Documents Checked</span>
                        </li>
                    </ul>
                </div> 
            @else
                {{-- For IT-Cell role - show transfer history link --}}
                <div class="d-flex justify-content-end">
                    <ul class="d-flex gap-3 flex-wrap">
                        <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                            <a href="{{ route('it-cell.transfer-history.index') }}" class="btn btn-info btn-sm">
                                <i class="bx bx-history"></i> View Transfer History
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
            
            <table id="example" class="display nowrap applicant_list_table" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Applicant Number</th>
                        <th>Name</th>
                        <th>Land Type</th>
                        <th>Block/Plot/Locality</th>
                        <th>Is Flat</th>
                        <th>Registration Type</th>
                        <th>Purpose Of Registration</th>
                        @if ($user->roles[0]['name'] == 'deputy-lndo')
                            <th>Section</th>
                        @endif
                        <th>Document</th>
                        @if ($user->roles[0]['name'] != 'it-cell')
                            <th>Activity</th>
                        @endif
                        
                        <th>
                            <div style="width: 110px; overflow: hidden;">
                                <select class="form-control form-select form-select-sm" name="status" id="status"
                                    style="font-weight: bold;">
                                    <option value="">Status</option>
                                    @foreach ($items as $item)
                                        <option class="text-capitalize" value="{{ $item->id }}" @if ($getStatusId == $item->id)
                                            @selected(true)
                                        @endif>{{ ucfirst($item->item_name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </th>
                        @if ($user->roles[0]['name'] != 'it-cell')
                            <th>Remark</th>
                        @endif
                        <th>Created On / Updated On</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
    <div id="tooltip"></div>
    @include('include.loader')
    @include('include.alerts.ajax-alert')

<!-- Modal Popup for Transfer Property To section - Lalit (23/Jan/2025) -->
<div class="modal fade" id="transferPropertyModel" tabindex="-1" aria-labelledby="transferPropertyModelLabel"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="transferPropertyModelLabel">Transfer to Section</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="propertyTransferForm">
            @csrf
            <div class="modal-body">
                <!-- Hidden Input Field to Store User ID -->
                <input type="hidden" id="userId" name="userId">
                <div class="mb-3">
                    <label for="transferPropertyId" class="form-label">Enter Transfer Property Id</label>
                    <input type="text" class="form-control" id="transferPropertyId" name="transferPropertyId"
                        required>
                </div>
                <div id="transferPropertyIdError" class="text-danger"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Modal Popup for Reject User Registration Property - Lalit (3/March/2025) -->
<div class="modal fade" id="rejectPropertyModel" tabindex="-1" aria-labelledby="rejectPropertyModelLabel"
aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="rejectPropertyModelLabel">Are you sure want to reject?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="rejectUserRegisteredForm">
            @csrf
            <div class="modal-body">
                <!-- Hidden Input Field to Store User ID -->
                <input type="hidden" id="rejectUserId" name="rejectUserId">
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter Remarks"></textarea>
                    <div id="remarksError" class="text-danger"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
</div>
    
@include('include.remark') 
@endsection


@section('footerScript')

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                // responsive: true,
                ajax: {
                    url: "{{ route('get.registered.users') }}",
                    data: function(d) {
                        d.status = $('#status').val(); // Add selected status to the request
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'applicant_number',
                        name: 'applicant_number',
                        render: function (data, type, row) {

                            // Append "Inactive" label for inactive-approved rows
                            if (row.is_inactive_approved) {
                                return `
                                    <div>
                                        ${data}
                                        <div style="font-size:15px; color:#8B0000; font-weight:600;">
                                            Inactive
                                        </div>
                                    </div>
                                `;
                            }

                            return data;
                        }
                    },

                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            // Display name on top and email below in smaller text
                            return `<div>
                                    <div>${data}</div>
                                    <div class="text-muted small">${row.email || 'No email'}</div>
                                </div>`;
                        }
                    },
                    {
                        data: 'land_type_name',
                        name: 'land_type_name'
                    },
                    {
                        data: 'property_details',
                        name: 'property_details'
                    },
                    {
                        data: 'isFlat',
                        name: 'isFlat'
                    },
                    {
                        data: 'user_type',
                        name: 'user_type'
                    },
                    {
                        data: 'purpose_of_registation',
                        name: 'purpose_of_registation',
                        orderable: false,
                        searchable: false,
                    },
                    @if ($user->roles[0]['name'] == 'deputy-lndo')
                        {
                            data: 'section',
                            name: 'section'
                        },
                    @endif
                    
                    /*{
                        data: 'documents',
                        name: 'documents',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let documentLinks = '';

                            $.each(data, function(key, doc) {
                                if (doc) {
                                    let docParts = doc.split('/');
                                    let docName = docParts[docParts.length - 1];
                                    let docUrl = "{{ asset('storage/') }}/" + doc;

                                    // Extract the file name without extension and the prefix
                                    let nameParts = docName.split('_');
                                    let prefix = nameParts[0]; // The part before the underscore
                                    let displayName = '';

                                    // Use a switch statement to map prefixes to display names
                                    switch (prefix) {
                                        case 'saledeed':
                                            displayName = 'Sale Deed';
                                            break;
                                        case 'BuilderAgreement':
                                            displayName = 'Builder Buyer Agreement';
                                            break;
                                        case 'leaseDeed':
                                            displayName = 'Lease Deed';
                                            break;
                                        case 'subsMutLetter':
                                            displayName = 'Substitution/Mutation Letter';
                                            break;
                                        case 'other':
                                            displayName = 'Other Document';
                                            break;
                                        case 'otherDocuments':
                                            displayName = 'Other Document';
                                            break;
                                        case 'ownerLessee':
                                            displayName = 'Owner/Lessee Document';
                                            break;
                                        case 'authSignatory':
                                            displayName = 'Authorized Signatory';
                                            break;
                                        case 'scannedIDOrg':
                                            displayName = 'Scanned Aadhar Card';
                                            break;
                                        default:
                                            displayName = 'Unknown Document';
                                    }

                                    // Construct the document link with the display name
                                    documentLinks +=
                                        "<span><i class='bx bx-chevron-right'></i> " +
                                        "<a href='" + docUrl +
                                        "' target='_blank' class='link-primary'>" +
                                        displayName + "</a></span><br>";
                                }
                            });

                            // Return the HTML for the document links with a tooltip
                            return '<a href="javascript:void(0);" class="text-danger pdf-icons" data-bs-toggle="tooltip" data-bs-html="true">' +
                                '<i class="bx bxs-file-pdf fs-4"></i></a><div class="tooltip-data">' +
                                documentLinks + '</div>';
                        }
                    },*/
                    {
                        data: 'documents',
                        name: 'documents',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // Check if lease_deed_doc exists in the data object
                            if (!data || !data.lease_deed_doc) {
                                return '<span class="text-muted">No Lease Deed</span>';
                            }

                            let leaseDeedDoc = data.lease_deed_doc;
                            let docUrl = "{{ asset('storage/') }}/" + leaseDeedDoc;

                            // Create direct clickable link
                            return `
                                <a href="${docUrl}" target="_blank" class="text-danger pdf-icon-link" 
                                data-bs-toggle="tooltip" data-bs-title="Click to view Lease Deed">
                                    <i class="bx bxs-file-pdf fs-4"></i>
                                </a>
                            `;
                        }
                    },
                    @if ($user->roles[0]['name'] != 'it-cell')
                    {
                        data: 'activity',
                        name: 'activity',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let mis = $('<div>').text(data.mis).html();
                         //   let scannedFiles = $('<div>').text(data.scanned_files).html();
                            let uploadedDoc = $('<div>').text(data.uploaded_doc).html();
                         //   console.log(mis,scannedFiles,uploadedDoc);
                            let misCheckedBy = data.mis_checked_by || '';
                       //     let scanFileCheckedBy = data.scan_file_checked_by || '';
                            let uploadedDocCheckedBy = data.uploaded_doc_checked_by || '';
                            let misColor = data.mis_color_code || '';
                       //     let scannedFilesColor = data.scan_file_color_code || '';
                            let uploadedDocColor = data.uploaded_doc_color_code || '';

                            let misHtml = mis == 1 ? `<div class="list-inline-item d-flex align-items-center">
            <i class="fadeIn animated bx bx-list-ul fs-5" style="color:${misColor}"></i> <span class="px-2 fst-italic">${misCheckedBy}</span></div>` : '';

         //                   let scannedFilesHtml = scannedFiles == 1 ?
           //                     `<div class="list-inline-item d-flex align-items-center pt-1">
          //  <i class="fadeIn animated bx bx-file-find fs-5" style="color:${scannedFilesColor}"></i><span class="px-2 fst-italic">${scanFileCheckedBy}</span></div>` : '';

                            let uploadedDocHtml = uploadedDoc == 1 ?
                                `<div class="list-inline-item d-flex align-items-center pt-1">
            <i class="lni lni-cloud-upload fs-5" style="color:${uploadedDocColor}"></i> <span class="px-2 fst-italic">${uploadedDocCheckedBy}</span></div>` : '';

                            return `<div>
                            ${misHtml}
         
                            ${uploadedDocHtml}
                        </div>`;
                        }
                    },
                    @endif
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                    },
                    @if ($user->roles[0]['name'] != 'it-cell')
                    {
                        data: 'remark',
                        name: 'remark',
                        render: function(data, type, row) {
                            // Check if both remark and assigned_by_name are empty
                            if (!data.remark) {
                                return '<span>NA</span>';
                            }

                            // Escape remark and assigned_by_name
                            let escapedRemark = $('<div>').text(data.remark || '').html();
                            let assignedByName = data.assigned_by_name ? 
                                $('<span>').text(' (' + data.assigned_by_name + ')')
                                .css({
                                    'font-size': '13px',
                                    'color': '#7e7e7ea1',
                                    'font-weight': '700'
                                }).html() 
                                : '';

                            // Combine escaped remark and assigned_by_name
                            let escapedData = escapedRemark + assignedByName;

                            // Truncate if too long
                            let shortRemark = escapedData.length > 30 ? escapedData.substring(0, 30) + '...' : escapedData;

                            // Return formatted HTML
                            return `<div class="text-wrap custom-tooltip" data-bs-toggle="tooltip" data-bs-html="true" title="${escapedData}">${shortRemark}</div>`;
                        }
                    },
                    @endif
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data, type, row) {
                            return `
                            <div>
                                <div><strong>Created:</strong> ${data}</div>
                                <div class="text-muted" style="font-size:12px;">
                                <strong>Updated:</strong> ${row.updated_at}
                                </div>
                            </div>
                            `;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
                buttons: [
                    'csv', 'excel', {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':not(:nth-child(7))', // Exclude the 7th column (documents)
                            format: {
                                header: function(data, columnIdx) {
                                    if (columnIdx === 7) {
                                        // For the status column, return only "Status" in the export
                                        return 'Status';
                                    }
                                    return data; // return original header for other columns
                                }
                            }
                        }
                    }
                ],
                scrollX: true, // Enable horizontal scrolling
                createdRow: function(row, data, dataIndex) {
                    // Highlight Approved (RS_APP) registrations where mapped user is inactive (users.status = 0)
                    if (data.is_inactive_approved) {
                        $(row).addClass('inactive-approved');
                    }
                    // Apply classes to the specific column (assuming documents is the 6th column)
                    var index;
                    @if ($user->roles[0]['name'] == 'deputy-lndo')
                        index = 8;
                    @else
                        index = 7;
                    @endif
                    $('td', row).eq(index).addClass('view-hover-data show-toggle-data');
                },
                drawCallback: function(settings) {
                    // Initialize tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });
            // Trigger table reload on status filter change
            $('#status').change(function() {
                table.ajax.reload();
            });

            
        });

        $(document).ready(function() {
            // Open Modal Popup for Transfer Property To section - Lalit (23/Jan/2025)
            $(document).on('click', '.open-modal-btn', function() {
                //Reset transfer property id input field
                $('#transferPropertyId').val('');
                var userId = $(this).data('user-id'); // Get user ID from the data attribute
                console.log('User ID:', userId); // For debugging
                // Set the user ID in the hidden input field
                $('#userId').val(userId);
            });


            // Handle form submission ajax request for Transfer Property To section - Lalit (23/Jan/2025)
            $('#propertyTransferForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                let transferPropertyId = $('#transferPropertyId').val();
                if (transferPropertyId == '') {
                    $('#transferPropertyIdError').text('Please enter property id');
                    return false;
                }
                // Prepare form data
                let formData = $(this).serialize(); // Serialize all form fields, including userId

                // AJAX request
                $.ajax({
                    url: '{{ route('transfer.property.section') }}', // Replace with your actual route
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#transferPropertyModel').modal('hide'); // Hide the modal
                            $('#propertyTransferForm')[0].reset(); // Reset the form
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showSuccess(response.message);
                            // Ensure checkbox is checked and disabled after success
                            setTimeout(function() {
                                window.location.href =
                                    '{{ route('regiserUserListings') }}';
                            }, 2000); // Slight delay to ensure modal is fully hidden
                        } else {
                            // Handle success response
                            $('#transferPropertyModel').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showError(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Log the error for debugging
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

            // Open Modal Popup for Reject User Registered User Property - Lalit (3/March/2025)
            $(document).on('click', '.open-reject-modal-btn', function() {
                //Reset remarks text area input field
                $('#remarks').val('');
                var rejectUserId = $(this).data('reject-user-id'); // Get user ID from the data attribute
                // Set the user ID in the hidden input field
                $('#rejectUserId').val(rejectUserId);
            });

            // Handle form submission ajax request fremarksor Reject User Registered Property - Lalit (3/March/2025)
            $('#rejectUserRegisteredForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                let remarks = $('#remarks').val().trim(); // Trim spaces and newlines

                // Check if remarks is empty
                if (remarks === '') {
                    $('#remarksError').text('Please enter remarks').show();
                    return false;
                }

                // Check if remarks has at least 50 characters
                if (remarks.length < 50) {
                    $('#remarksError').text('Remarks must be at least 50 characters long.').show();
                    return false;
                }

                $('#remarksError').hide(); // Hide error message if input is valid


                // Prepare form data
                let formData = $(this).serialize(); // Serialize all form fields, including userId

                // AJAX request
                $.ajax({
                    url: '{{ route('reject.user.registered.property') }}', // Replace with your actual route
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#rejectPropertyModel').modal('hide'); // Hide the modal
                            $('#rejectUserRegisteredForm')[0].reset(); // Reset the form
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showSuccess(response.message);
                            // Ensure checkbox is checked and disabled after success
                            setTimeout(function() {
                                window.location.href =
                                    '{{ route('regiserUserListings') }}';
                            }, 2000); // Slight delay to ensure modal is fully hidden
                        } else {
                            // Handle success response
                            $('#rejectPropertyModel').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showError(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText); // Log the error for debugging
                        alert('Something went wrong. Please try again.');
                    }
                });
            });

        });
//added by swati mishra for resend communication on 18082025
$(document).on('click', '.resend-comm-btn', function () {
    const id = $(this).data('id');
    const btn = $(this);
    btn.prop('disabled', true);

    $.ajax({
        url: "{{ route('register.user.resendComms', ['id' => 'ID_PLACEHOLDER']) }}".replace('ID_PLACEHOLDER', id),
        method: 'POST',
        data: { _token: "{{ csrf_token() }}" },
        success: function (res) {
            if (res.status === 'success') {
                showSuccess(res.message || 'Resent successfully.');
            } else {
                showError(res.message || 'Could not resend.');
            }
        },
        error: function () {
            showError('Something went wrong. Please try again.');
        },
        complete: function () { btn.prop('disabled', false); }
    });
});
 let revokeTargetId = null;

    $(document).on('click', '.revoke-approval-btn', function () {
        revokeTargetId = $(this).data('id');              // store the registration id
        $('#rejectionReason').val('');                    // reset textarea
        $('#rejectionReasonError').hide();
        $('#rejectReasonModal').modal('show');            // open modal
    });

    // Submit inside the modal
    $(document).on('click', '.submit-reason', function () {
        const reason = $('#rejectionReason').val().trim();
        if (!reason /* || reason.length < 50 */) {        // uncomment length check if you want ≥50 chars
            $('#rejectionReasonError').show();
            return;
        }
        $('#rejectionReasonError').hide();

        const btn = $(this).prop('disabled', true);

        $.ajax({
            url: "{{ route('register.user.revokeApproval', ['id' => 'ID_PLACEHOLDER']) }}".replace('ID_PLACEHOLDER', revokeTargetId),
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                remarks: reason
            },
            success: function (res) {
                if (res.status === 'success') {
                    $('#rejectReasonModal').modal('hide');
                    showSuccess(res.message || 'Approval revoked.');
                    $('#example').DataTable().ajax.reload(null, false);
                } else {
                    showError(res.message || 'Could not revoke.');
                }
            },
            error: function () {
                showError('Something went wrong. Please try again.');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
        
    </script>
@endsection
