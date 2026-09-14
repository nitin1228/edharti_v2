@extends('layouts.app')
@section('title', 'Club Membership Details')
@section('content')
    <style>
        .pagination .active a {
            color: #ffffff !important;
        }

        #spinnerOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            /* Ensure it covers other content */
        }

        /* commented and adeed by anil for replace the new loader on 24-07-2025  */
        /* .spinner {
                                                                            border: 8px solid rgba(255, 255, 255, 0.3);
                                                                            border-radius: 50%;
                                                                            border-top: 8px solid #ffffff;
                                                                            width: 50px;
                                                                            height: 50px;
                                                                            animation: spin 1s linear infinite;
                                                                        }

                                                                        @keyframes spin {
                                                                            0% {
                                                                                transform: rotate(0deg);
                                                                            }

                                                                            100% {
                                                                                transform: rotate(360deg);
                                                                            }
                                                                        } */
        .loader {
            width: 48px;
            height: 48px;
            border: 6px solid #FFF;
            border-radius: 50%;
            position: relative;
            transform: rotate(45deg);
            box-sizing: border-box;
        }

        .loader::before {
            content: "";
            position: absolute;
            box-sizing: border-box;
            inset: -7px;
            border-radius: 50%;
            border: 8px solid #116d6e;
            animation: prixClipFix 2s infinite linear;
        }

        @keyframes prixClipFix {
            0% {
                clip-path: polygon(50% 50%, 0 0, 0 0, 0 0, 0 0, 0 0)
            }

            25% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 0, 100% 0, 100% 0)
            }

            50% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 100% 100%, 100% 100%)
            }

            75% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 0 100%, 0 100%)
            }

            100% {
                clip-path: polygon(50% 50%, 0 0, 100% 0, 100% 100%, 0 100%, 0 0)
            }
        }

        /* commented and adeed by anil for replace the new loader on 24-07-2025  */
    </style>
    <!--breadcrumb-->
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
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    <hr>
    <div class="card">
        <div class="card-body">
            <!-- <div class="container"> -->
            <div class="part-title">
                <h5>Application Details</h5>
            </div>
            <div class="part-details">
                <div class="container-fluid">
                    @if (!empty($getClubMembershipDetails->club_type))
                        @if ($getClubMembershipDetails->club_type == 'IHC')
                            <table class="table table-bordered">
                                <tbody>
                                    @if (!empty($getClubMembershipDetails->membership_id))
                                        <tr>
                                            <td colspan="2"><b>Membership ID : </b>
                                                <span
                                                    style="color: #116d6e;">{{ $getClubMembershipDetails->membership_id ?? '' }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2"><b>Application Number : </b>
                                            <span
                                                style="color: #116d6e;">{{ $getClubMembershipDetails->unique_id ?? '' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Membership Type : </b> {{ $getClubMembershipDetails->club_type ?? '' }}</td>
                                        @if (!empty($getClubMembershipDetails->category) && $getClubMembershipDetails->category == 'other')
                                            <td><b>Category : </b> {{ $getClubMembershipDetails->other_category ?? '' }}
                                            </td>
                                        @else
                                            <td><b>Category : </b> {{ $getClubMembershipDetails->category ?? '' }}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td><b>Name : </b> {{ $getClubMembershipDetails->name ?? '' }}</td>
                                        <td><b>Email : </b> {{ $getClubMembershipDetails->email ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Mobile No. : </b> {{ $getClubMembershipDetails->mobile ?? '' }}</td>
                                        <td><b>Office Address : </b> {{ $getClubMembershipDetails->office_address ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Designation : </b> {{ $getClubMembershipDetails->designation ?? '' }}</td>
                                        @if (
                                            !empty($getClubMembershipDetails->designation_equivalent_to) &&
                                                $getClubMembershipDetails->designation_equivalent_to == 'OTHER')
                                            <td><b>Equivalent Designation : </b>
                                                {{ $getClubMembershipDetails->other_designation_equivalent_to ?? '' }}</td>
                                        @else
                                            <td><b>Equivalent Designation : </b>
                                                {{ $getClubMembershipDetails->designation_equivalent_to ?? '' }}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td><b>Service : </b> {{ $getClubMembershipDetails->name_of_service ?? '' }}</td>
                                        <td><b>Allotment Year : </b>
                                            {{ $getClubMembershipDetails->year_of_allotment ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Date of Joining : </b>
                                            {{ $getClubMembershipDetails->date_of_joining_central_deputation ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_joining_central_deputation)->format('d/m/Y') : '' }}
                                        </td>
                                        <td><b>Date of Completion : </b>
                                            {{ $getClubMembershipDetails->expected_date_of_tenure_completion ? \Carbon\Carbon::parse($getClubMembershipDetails->expected_date_of_tenure_completion)->format('d/m/Y') : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Application Date : </b>
                                            {{ $getClubMembershipDetails->date_of_application ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_application)->format('d/m/Y') : '' }}
                                        </td>
                                        <td><b>Date of Superannuation : </b>
                                            {{ $getClubMembershipDetails->date_of_superannuation ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_superannuation)->format('d/m/Y') : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Pay Scale : </b> {{ $getClubMembershipDetails->pay_scale ?? '' }}</td>
                                        <td><b>Do you hold a central staffing position : </b>
                                            {{ $getClubMembershipDetails->is_central_deputated == 1 ? 'Yes' : 'No' }}
                                        </td>
                                    </tr>
                                    @if ($getClubMembershipDetails->is_central_deputated == 1)
                                        <tr>
                                            <td><b>Date of Joining on Central Deputation in Delhi : </b>
                                                {{ $getClubMembershipDetails->date_of_joining_central_deputation ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_joining_central_deputation)->format('d/m/Y') : '' }}
                                            </td>
                                            <td><b>Expected Date of Completion of Tenure : </b>
                                                {{ $getClubMembershipDetails->expected_date_of_tenure_completion ? \Carbon\Carbon::parse($getClubMembershipDetails->expected_date_of_tenure_completion)->format('d/m/Y') : '' }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><b>Name Of Present/Previous Membership Of Other Clubs : </b>
                                            {{ $getClubMembershipDetails->present_previous_membership_of_other_clubs ?? '' }}
                                        </td>
                                        <td style="display: flex; align-items: center;">
                                            @if (!empty($getClubMembershipDetails->ihcDetails?->ihcs_doc))
                                                <b style="margin-right: 5px;">IHC Membership Document :</b>
                                                <a href="{{ asset('storage/' . $getClubMembershipDetails->ihcDetails?->ihcs_doc) }}"
                                                    target="_blank" class="text-danger pdf-icons" data-bs-toggle="tooltip"
                                                    data-bs-html="true">
                                                    <i class="bx bxs-file-pdf fs-4"></i>
                                                </a>
                                            @else
                                                <b style="margin-right: 5px;">Document Not Available</b>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td colspan="2"><b>Application Number : </b>
                                            <span
                                                style="color: #116d6e;">{{ $getClubMembershipDetails->unique_id ?? '' }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Membership Type : </b> {{ $getClubMembershipDetails->club_type ?? '' }}</td>
                                        <td><b>Category : </b> {{ $getClubMembershipDetails->category ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Name : </b> {{ $getClubMembershipDetails->name ?? '' }}</td>
                                        <td><b>Email : </b> {{ $getClubMembershipDetails->email ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Mobile No. : </b> {{ $getClubMembershipDetails->mobile ?? '' }}</td>
                                        <td><b>Office Address : </b> {{ $getClubMembershipDetails->office_address ?? '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Designation : </b> {{ $getClubMembershipDetails->designation ?? '' }}</td>
                                        <td><b>Equivalent Designation : </b>
                                            {{ $getClubMembershipDetails->designation_equivalent_to ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Service : </b> {{ $getClubMembershipDetails->name_of_service ?? '' }}</td>
                                        <td><b>Allotment Year : </b>
                                            {{ $getClubMembershipDetails->year_of_allotment ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Date of Joining : </b>
                                            {{ $getClubMembershipDetails->date_of_joining_central_deputation ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_joining_central_deputation)->format('d/m/Y') : '' }}
                                        </td>
                                        <td><b>Date of Completion : </b>
                                            {{ $getClubMembershipDetails->expected_date_of_tenure_completion ? \Carbon\Carbon::parse($getClubMembershipDetails->expected_date_of_tenure_completion)->format('d/m/Y') : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Application Date : </b>
                                            {{ $getClubMembershipDetails->date_of_application ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_application)->format('d/m/Y') : '' }}
                                        </td>
                                        <td><b>Date of Superannuation : </b>
                                            {{ $getClubMembershipDetails->date_of_superannuation ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_superannuation)->format('d/m/Y') : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Pay Scale : </b> {{ $getClubMembershipDetails->pay_scale ?? '' }}</td>
                                        <td><b>Do you hold a central staffing position : </b>
                                            {{ $getClubMembershipDetails->is_central_deputated == 1 ? 'Yes' : 'No' }}
                                        </td>
                                    </tr>
                                    @if ($getClubMembershipDetails->is_central_deputated == 1)
                                        <tr>
                                            <td><b>Date of Joining on Central Deputation in Delhi : </b>
                                                {{ $getClubMembershipDetails->date_of_joining_central_deputation ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_joining_central_deputation)->format('d/m/Y') : '' }}
                                            </td>
                                            <td><b>Expected Date of Completion of Tenure : </b>
                                                {{ $getClubMembershipDetails->expected_date_of_tenure_completion ? \Carbon\Carbon::parse($getClubMembershipDetails->expected_date_of_tenure_completion)->format('d/m/Y') : '' }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><b>Name Of Present/Previous Membership Of Other Clubs : </b>
                                            {{ $getClubMembershipDetails->present_previous_membership_of_other_clubs ?? '' }}
                                        </td>
                                        <td colspan="2" style="display: flex; align-items: center;">
                                            @if (!empty($getClubMembershipDetails->dgcDetails?->dgcs_doc))
                                                <b style="margin-right: 5px;">DGC Membership Document :</b>
                                                <a href="{{ asset('storage/' . $getClubMembershipDetails->dgcDetails?->dgcs_doc) }}"
                                                    target="_blank" class="text-danger pdf-icons" data-bs-toggle="tooltip"
                                                    data-bs-html="true">
                                                    <i class="bx bxs-file-pdf fs-4"></i>
                                                </a>
                                            @else
                                                <b style="margin-right: 5px;">Document Not Available</b>
                                            @endif

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    @endif
                </div>
                <div class="container-fluid">
                    <div class="col-lg-12 col-12">
                        <div class="row mb-3">
                            <div class="d-flex justify-content-end">
                                @if ($isActionButtonVisible)
                                    {{-- Check club membership id & status should in New - Lalit (30/01/2025) --}}
                                    @if (!empty($getClubMembershipDetails->id) && !empty($getClubMembershipDetails->status))
                                        @if ($getClubMembershipDetails->status == getServiceType('CM_NEW'))
                                            <button id="membershipVerifiedBtn" class="btn btn-primary btn-theme mr-5"
                                                data-id="{{ $getClubMembershipDetails->id }}"
                                                data-status="{{ getServiceType('CM_INP') }}">
                                                Approve
                                            </button>
                                            <button type="button" id="rejectButton" class="btn btn-danger">Reject</button>
                                        @elseif ($getClubMembershipDetails->status == getServiceType('CM_INP'))
                                            <button id="membershipAllotmentBtn" class="btn btn-primary btn-theme mr-5"
                                                data-id="{{ $getClubMembershipDetails->id }}"
                                                data-status="{{ getServiceType('CM_PEN') }}">
                                                Approval of membership
                                            </button>
                                            <button type="button" id="rejectButton" class="btn btn-danger">Reject</button>
                                        @elseif ($getClubMembershipDetails->status == getServiceType('CM_PEN'))
                                            <button id="membershipApproveBtn" class="btn btn-primary btn-theme mr-5"
                                                data-id="{{ $getClubMembershipDetails->id }}"
                                                data-status="{{ getServiceType('CM_APP') }}">
                                                Final membership allotment
                                            </button>
                                            <button type="button" id="rejectButton" class="btn btn-danger">Reject</button>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('include.loader')
    @include('include.alerts.ajax-alert')
    <div class="modal fade" id="membershipVerfiedModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-end">
                    <h5 class="modal-title">Membership Verified</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 4px; padding-bottom: 4px;">
                    Do you really want to continue?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update-verfied-status"
                        data-id="{{ $getClubMembershipDetails->id }}"
                        data-status="{{ getServiceType('CM_INP') }}">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="membershipApproveModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title">Membership Approve </h5> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 4px; padding-bottom: 4px;">
                    Do you really want to continue?
                </div>
                <div class="modal-body input-class-approve" style="padding-top: 4px; padding-bottom: 0px;">
                    {{-- <label for="rejection">Enter Membership Id</label> --}}
                    <input type="text" class="form-control" name="membership_id" id="membership_id"
                        placeholder="Enter Membership Id">
                    <div class="text-danger" id="membership_idError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update-approve-status"
                        data-id="{{ $getClubMembershipDetails->id }}"
                        data-status="{{ getServiceType('CM_APP') }}">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectClubMembership" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Membership Rejection </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 4px; padding-bottom: 4px;">
                    Do you really want to continue?
                </div>
                <div class="modal-body input-class-reject" style="padding-top: 4px; padding-bottom: 0px;">
                    <label for="rejection">Remarks</label>
                    <textarea name="remark" id="remark" class="form-control" placeholder="Remarks"></textarea>
                    <div class="text-danger" id="remarkError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update-status-with-remark"
                        data-id="{{ $getClubMembershipDetails->id }}"
                        data-status="{{ getServiceType('CM_REJ') }}">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="membershipAllotmentModel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title">Membership Allotment</h5> --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding-top: 4px; padding-bottom: 4px;">
                    Do you really want to continue?
                </div>
                {{-- <div class="modal-body input-class-approve" style="padding-top: 4px; padding-bottom: 0px;">
                    <label for="rejection">Enter Membership Id.</label>
                    <input type="text" class="form-control" name="membership_id" id="membership_id"
                        placeholder="Enter Membership Id">
                    <div class="text-danger" id="membership_idError"></div>
                </div> --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary update-approve-status-with-remark"
                        data-id="{{ $getClubMembershipDetails->id }}"
                        data-status="{{ getServiceType('CM_PEN') }}">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!-- commented and adeed by anil for replace the new loader on 01-08-2025  -->
    <!-- <div id="spinnerOverlay" style="display:none;">
                                                                        <img src="{{ asset('assets/images/chatbot_icongif.gif') }}">
                                                                    </div> -->
    <div id="spinnerOverlay" style="display:none;">
        <span class="loader"></span>
        <h1 style="color: white;font-size: 20px; margin-top:10px;">Loading... Please wait</h1>
    </div>
    <!-- commented and adeed by anil for replace the new loader on 01-08-2025  -->
@endsection
@section('footerScript')
    <script>
        $(document).ready(function() {
            $('#rejectButton').click(function() {
                $('#rejectClubMembership').modal('show');
            });
            $('#membershipAllotmentBtn').click(function() {
                $('#membershipAllotmentModel').modal('show');
            });
            $('#membershipVerifiedBtn').click(function() {
                $('#membershipVerfiedModel').modal('show');
            });
            $('#membershipApproveBtn').click(function() {
                $('#membershipApproveModel').modal('show');
            });
        });
        $(document).on("click", ".update-verfied-status", function() {
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            var clubMembershipId = $(this).data("id");
            var newStatus = $(this).data("status");
            $.ajax({
                url: "{{ route('update.club.membership.status') }}",
                type: "POST",
                data: {
                    id: clubMembershipId,
                    status: newStatus,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status) {
                        showSuccess(response.message);
                        spinnerOverlay.style.display = 'none';
                        // Ensure checkbox is checked and disabled after success
                        setTimeout(function() {
                            window.location.href = response.redirect_url; // Redirect on success
                        }, 1000); // Slight delay to ensure modal is fully hidden
                    } else {
                        showError(response.message);
                        spinnerOverlay.style.display = 'none';
                        // Ensure checkbox is checked and disabled after success
                        setTimeout(function() {
                            window.location.href = response.redirect_url; // Redirect on success
                        }, 1000); // Slight delay to ensure modal is fully hidden
                    }
                }
            });
        });
        $(document).on("click", ".update-approve-status", function() {
            let membershipId = $("#membership_id").val().trim();
            $("#membership_idError").text(""); // Clear previous error

            if (membershipId === "") {
                $("#membership_idError").text("Membership ID is required.");
                $("#membership_id").focus();
                return;
            }

            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }

            var clubMembershipId = $(this).data("id");
            var newStatus = $(this).data("status");

            $.ajax({
                url: "{{ route('update.club.membership.status') }}",
                type: "POST",
                data: {
                    id: clubMembershipId,
                    membershipId: membershipId,
                    status: newStatus,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    spinnerOverlay.style.display = 'none';
                    if (response.status) {
                        showSuccess(response.message);
                    } else {
                        showError(response.message);
                    }
                    setTimeout(function() {
                        window.location.href = response.redirect_url;
                    }, 1000);
                }
            });
        });

        $(document).on("click", ".update-status-with-remark", function(e) {
            e.preventDefault();
            let isValid = true;
            var clubMembershipId = $(this).data("id");
            var newStatus = $(this).data("status");
            var remark = $("#remark").val();
            if (!remark || remark.length <= 50) {
                $('#remarkError').text(!remark ? 'Please enter the remark for rejection.' :
                    'Remark should be minimum 50 characters.');
                isValid = false;
                $('#remark').focus();
            }
            if (!isValid) {
                return;
            }
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            $.ajax({
                url: "{{ route('update.club.membership.status') }}",
                type: "POST",
                data: {
                    id: clubMembershipId,
                    status: newStatus,
                    remark: remark,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status) {
                        $('#rejectClubMembership').modal('hide');
                        showSuccess(response.message);
                        spinnerOverlay.style.display = 'none';
                        // Ensure checkbox is checked and disabled after success
                        setTimeout(function() {
                            window.location.href = response.redirect_url; // Redirect on success
                        }, 1000); // Slight delay to ensure modal is fully hidden
                    } else {
                        $('#rejectClubMembership').modal('hide');
                        showError(response.message);
                        spinnerOverlay.style.display = 'none';
                        // Ensure checkbox is checked and disabled after success
                        setTimeout(function() {
                            window.location.href = response.redirect_url; // Redirect on success
                        }, 1000); // Slight delay to ensure modal is fully hidden
                    }
                }
            });
        });
        $(document).on("click", ".update-approve-status-with-remark", function(e) {
            e.preventDefault();
            let clubMembershipId = $(this).data("id");
            let newStatus = $(this).data("status");
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            // Send AJAX request if validation passes
            $.ajax({
                url: "{{ route('allotment.club.membership') }}",
                type: "POST",
                data: {
                    id: clubMembershipId,
                    status: newStatus,
                    _token: "{{ csrf_token() }}"
                },
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                success: function(response) {
                    if (response.status) {
                        $("#membershipAllotmentModel").modal("hide");
                        showSuccess(response.message);
                        spinnerOverlay.style.display = 'none';
                        setTimeout(function() {
                            window.location.href = response
                                .redirect_url; // Redirect on success
                        }, 1000);
                    } else {
                        $("#membershipAllotmentModel").modal("hide");
                        showError(response.message);
                        spinnerOverlay.style.display = 'none';
                        setTimeout(function() {
                            window.location.href = response
                                .redirect_url; // Redirect on success
                        }, 1000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    showError("Something went wrong. Please try again.");
                }
            });
        });
    </script>
@endsection
