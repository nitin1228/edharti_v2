@extends('layouts.app')
@section('title', 'Add Flat')
@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <style>
        table {
            width: 100%;
            table-layout: fixed;
            /* border-collapse: collapse; */
            border-color: none !important;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 0px !important;
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            overflow: hidden;
        }

        td:nth-child(odd) {
            background-color: #f1f1f166;
            vertical-align: middle;
        }

        td:nth-child(even) {
            background-color: #f1f1f166;
            vertical-align: middle;
        }
    </style>
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">APPLICATION</div>
        <style>
    .breadcrumb-item.active span {
        font-weight: 500;
    }
</style>

<div class="ps-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>

            
                            <li class="breadcrumb-item active" aria-current="">
                                            <span>Application Remove</span>
                                    </li>
                            <!-- <li class="breadcrumb-item " aria-current="page">
                                            <span></span>
                                    </li> -->
                    </ol>
    </nav>
</div>
    </div>
    <!--breadcrumb-->
    <div class="card shadow-sm mb-4">
        <form action="{{ route('store.flat.details') }}" method="POST" enctype="multipart/form-data"
            id="applicationRemoveForm">
            @csrf
            <div class="card-body">
                <div class="part-title">
                    <h5>Remove ( Application / Registration ) </h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="col-lg-12 col-12">
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-end">
                                        <div class="flex-grow-1 me-3">
                                            <label for="applicationNumber" class="form-label">Enter Number ( Application /
                                                Registration )
                                            </label>
                                            <div class="d-flex align-item-center">
                                                <input type="text" class="form-control" name="applicationNumber"
                                                    id="applicationNumber" placeholder="Enter Application Number"
                                                    value="{{ old('applicationNumber') }}"
                                                    oninput="this.value = this.value.toUpperCase()">
                                                <div style="min-width: 150px; text-align: right;">
                                                    <button type="button" id="submitFormBtn"
                                                        class="btn btn-primary btn-theme h-100">Submit</button>
                                                </div>
                                            </div>

                                            <!-- Server-side validation error -->
                                            @error('applicationNumber')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror

                                            <!-- AJAX validation error container -->
                                            <div class="text-danger" id="applicationNumberError"></div>

                                            <!-- Success message container -->
                                            <div class="text-success" id="applicationNumberSuccess"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Dynamic Element --}}
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-labelledby="confirmRemoveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmRemoveModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Warning: Permanent Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger border-2 border-danger mb-3">
                        <i class="fas fa-skull-crosswalk me-2"></i>
                        <strong>This action CANNOT be undone!</strong>
                    </div>

                    <p class="fw-bold mb-2">You are about to permanently delete this record:</p>

                    <div class="bg-light p-3 rounded border-start border-danger border-4 mb-3">
                        <p class="mb-1"><strong>Application Number:</strong> <span id="confirmApplicationNumber"
                                class="fw-bold text-danger"></span></p>
                    </div>

                    <p class="mb-0 text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        This record will be permanently removed from the database and will not be recoverable.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveBtn">
                        <i class="fas fa-trash-alt me-1"></i>Yes, Permanently Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footerScript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const applicationNumberInput = document.getElementById('applicationNumber');

            if (applicationNumberInput) {
                applicationNumberInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });

                // Also convert on blur (when user leaves the field)
                applicationNumberInput.addEventListener('blur', function() {
                    this.value = this.value.toUpperCase();
                });
            }
        });
        /*$(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#submitFormBtn').click(function(e) {
                e.preventDefault();

                // Clear previous messages
                $('#applicationNumberError').text('');
                $('#applicationNumberSuccess').text('');
                $('.text-danger').text('');

                let isValid = true;
                const applicationNumber = $('#applicationNumber').val().trim();

                // Basic client-side validation
                if (!applicationNumber) {
                    $('#applicationNumberError').text('Please enter the application number.');
                    isValid = false;
                    $('#applicationNumber').focus();
                    return;
                }

                // Disable button and show loading
                $(this).prop('disabled', true);
                $(this).html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );

                // AJAX request
                $.ajax({
                    url: "{{ route('remove.application.action') }}",
                    type: 'POST',
                    data: {
                        applicationNumber: applicationNumber
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Re-enable button
                        $('#submitFormBtn').prop('disabled', false);
                        $('#submitFormBtn').html('Submit');

                        if (response.success) {
                            // Show success message
                            $('#applicationNumberSuccess').text(response.message);

                            // Optional: Clear the input field
                            $('#applicationNumber').val('');

                            // Optional: Redirect or show more data
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }

                            // Optional: Display application details if needed
                            if (response.application) {
                                // You can display the application details here
                                console.log(response.application);
                            }
                        } else {
                            // Show error message - handle specific cases
                            let errorMessage = response.message || 'An error occurred.';

                            // Special styling for approved application/registration messages
                            if (errorMessage === "Approved applications cannot be removed." ||
                                errorMessage === "Approved registration cannot be removed.") {
                                $('#applicationNumberError').html(
                                    '<span class="text-warning" style="font-weight: 500;">⚠️ ' +
                                    errorMessage +
                                    '</span>'
                                );
                            } else {
                                $('#applicationNumberError').text(errorMessage);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Re-enable button
                        $('#submitFormBtn').prop('disabled', false);
                        $('#submitFormBtn').html('Submit');

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            if (errors.applicationNumber) {
                                $('#applicationNumberError').text(errors.applicationNumber[0]);
                            }
                        } else {
                            // Only show generic error for network issues or server errors
                            $('#applicationNumberError').text(
                                'Request failed. Please check your connection and try again.'
                            );
                        }
                    }
                });
            });

            // Optional: Clear error when user starts typing
            $('#applicationNumber').on('input', function() {
                $('#applicationNumberError').text('');
                $('#applicationNumberSuccess').text('');
            });
        });*/

        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Store application number for confirmation
            let pendingApplicationNumber = '';

            // Show confirmation modal on submit button click
            $('#submitFormBtn').click(function(e) {
                e.preventDefault();

                // Clear previous messages
                $('#applicationNumberError').text('');
                $('#applicationNumberSuccess').text('');
                $('.text-danger').text('');

                const applicationNumber = $('#applicationNumber').val().trim();

                // Basic client-side validation
                if (!applicationNumber) {
                    $('#applicationNumberError').text('Please enter the application number.');
                    $('#applicationNumber').focus();
                    return;
                }

                // Store the application number and show confirmation modal
                pendingApplicationNumber = applicationNumber;
                $('#confirmApplicationNumber').text(applicationNumber);
                $('#confirmRemoveModal').modal('show');
            });

            // Handle confirmation
            $('#confirmRemoveBtn').click(function() {
                // Close the modal
                $('#confirmRemoveModal').modal('hide');

                // Disable button and show loading
                $('#submitFormBtn').prop('disabled', true);
                $('#submitFormBtn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
                );

                // AJAX request
                $.ajax({
                    url: "{{ route('remove.application.action') }}",
                    type: 'POST',
                    data: {
                        applicationNumber: pendingApplicationNumber
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Re-enable button
                        $('#submitFormBtn').prop('disabled', false);
                        $('#submitFormBtn').html('Submit');

                        if (response.success) {
                            // Show success message
                            $('#applicationNumberSuccess').text(response.message);

                            // Clear the input field
                            $('#applicationNumber').val('');
                            pendingApplicationNumber = '';

                            // Optional: Redirect or show more data
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        } else {
                            // Show error message
                            let errorMessage = response.message || 'An error occurred.';

                            // Special styling for approved application/registration messages
                            if (errorMessage === "Approved applications cannot be removed." ||
                                errorMessage === "Approved registration cannot be removed.") {
                                $('#applicationNumberError').html(
                                    '<span class="text-warning" style="font-weight: 500;">⚠️ ' +
                                    errorMessage + '</span>'
                                );
                            } else {
                                $('#applicationNumberError').text(errorMessage);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Re-enable button
                        $('#submitFormBtn').prop('disabled', false);
                        $('#submitFormBtn').html('Submit');

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            if (errors.applicationNumber) {
                                $('#applicationNumberError').text(errors.applicationNumber[0]);
                            }
                        } else {
                            $('#applicationNumberError').text(
                                'Request failed. Please check your connection and try again.'
                            );
                        }
                    }
                });
            });

            // Optional: Clear error when user starts typing
            $('#applicationNumber').on('input', function() {
                $('#applicationNumberError').text('');
                $('#applicationNumberSuccess').text('');
            });
        });
    </script>

@endsection
