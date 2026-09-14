@extends('layouts.app')
@section('title', 'Revenues')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Manual Payments</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ 'dashboard' }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Manual Payment</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="login-8">
                <div class="container">
                    <div class="row login-box mb-2">
                        <div class="col-lg-12 mx-auto form-section">
                            <div class="form-inner">
                                <form id="paymentForm">
                                    <div class="row align-items-end g-3 mb-4">
                                        <!-- Payment Type Dropdown -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="paymentType" class="form-label fw-bold">Payment By Property Id /
                                                    Demand
                                                    ID</label>
                                                <select id="paymentType" name="paymentType" class="form-select">
                                                    <option value="">-- Select --</option>
                                                    <option value="propertyId">By PropertyID</option>
                                                    <option value="demandId">By DemandID</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Input Text -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="paymentInput" class="form-label fw-bold">Enter Property Id /
                                                    Demand
                                                    ID</label>
                                                <input type="text" id="paymentInput" class="form-control"
                                                    placeholder="Enter ID" disabled>
                                                <div id="paymentInputError" class="invalid-feedback" style="display: none;">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Search Button -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <button type="button" id="searchBtn" class="btn btn-primary w-100"
                                                    disabled>
                                                    <i class="fas fa-search me-2"></i>Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Property Details Display -->
                                <div id="propertyDetails" style="display: none; margin-top: 20px;">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-success">
                                            <h5 class="mb-0 text-white">
                                                <i class="fas fa-building me-2 text-white"></i>Property Details
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3 align-items-center">
                                                <!-- Property ID - Displayed First -->
                                                <div class="col-md-2">
                                                    <div class="detail-item p-2">
                                                        <label class="fw-bold text-muted d-block mb-1"
                                                            style="font-size: 0.8rem;">PROPERTY ID</label>
                                                        <span id="oldPropertyId" class="fw-semibold text-primary">-</span>
                                                    </div>
                                                </div>
                                                <!-- Unique Property ID - Displayed First -->
                                                <div class="col-md-2">
                                                    <div class="detail-item p-2">
                                                        <label class="fw-bold text-muted d-block mb-1"
                                                            style="font-size: 0.8rem;">UNIQUE ID</label>
                                                        <span id="uniquePropertyId"
                                                            class="fw-semibold text-primary">-</span>
                                                    </div>
                                                </div>

                                                <!-- Locality -->
                                                <div class="col-md-2">
                                                    <div class="detail-item p-2">
                                                        <label class="fw-bold text-muted d-block mb-1"
                                                            style="font-size: 0.8rem;">LOCALITY</label>
                                                        <span id="locality" class="fw-semibold">-</span>
                                                    </div>
                                                </div>

                                                <!-- Block -->
                                                <div class="col-md-2">
                                                    <div class="detail-item p-2">
                                                        <label class="fw-bold text-muted d-block mb-1"
                                                            style="font-size: 0.8rem;">BLOCK</label>
                                                        <span id="block" class="fw-semibold">-</span>
                                                    </div>
                                                </div>

                                                <!-- Plot -->
                                                <div class="col-md-2">
                                                    <div class="detail-item p-2">
                                                        <label class="fw-bold text-muted d-block mb-1"
                                                            style="font-size: 0.8rem;">PLOT</label>
                                                        <span id="plot" class="fw-semibold">-</span>
                                                    </div>
                                                </div>

                                                <!-- Known As -->
                                                <div class="col-md-2">
                                                    <div class="detail-item p-2">
                                                        <label class="fw-bold text-muted d-block mb-1"
                                                            style="font-size: 0.8rem;">KNOWN AS</label>
                                                        <span id="knownAs" class="fw-semibold">-</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="is_joint_property" name="is_joint_property">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Payment Details Form -->
                                    <div class="card shadow-sm mt-4">
                                        <div class="card-header bg-success">
                                            <h5 class="mb-0 text-white">
                                                <i class="fas fa-building me-2 text-white"></i>Payment Details
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <form id="paymentDetailsForm">
                                                <div class="row g-3">
                                                    <!-- Payment Purpose -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="paymentPurpose" class="form-label fw-bold">Payment
                                                                Purpose
                                                                <span class="text-danger">*</span></label>
                                                            <select id="paymentPurpose" name="payment_purpose"
                                                                class="form-select" required>
                                                                <option value="">-- Select Purpose --</option>
                                                                @foreach ($paymentTypeProperty as $paymentType)
                                                                    <option value="{{ $paymentType->item_code }}">
                                                                        {{ $paymentType->item_name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Transaction Date -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="transactionDate"
                                                                class="form-label fw-bold">Transaction
                                                                Date
                                                                <span class="text-danger">*</span></label>
                                                            <input type="date" id="transactionDate"
                                                                name="transaction_date" class="form-control" required>
                                                        </div>
                                                    </div>

                                                    <!-- Financial Year -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="financialYear"
                                                                class="form-label fw-bold">Financial Year
                                                                <span class="text-danger">*</span></label>
                                                            <select id="financialYear" name="financial_year"
                                                                class="form-select" required>
                                                                <option value="">-- Select Financial Year --</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Transaction Number / Reference Number -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="transactionNumber"
                                                                class="form-label fw-bold">Transaction
                                                                / Reference Number <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text" id="transactionNumber"
                                                                name="transaction_number" class="form-control"
                                                                placeholder="Enter Transaction Number" required>
                                                        </div>
                                                    </div>

                                                    <!-- Amount -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="amount" class="form-label fw-bold">Amount (₹)
                                                                <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">₹</span>
                                                                <input type="number" id="amount" name="amount"
                                                                    class="form-control" placeholder="Enter Amount"
                                                                    step="0.01" min="0" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Submit Button -->
                                                    <div class="col-12">
                                                        <hr>
                                                        <div class="d-flex justify-content-end gap-2">
                                                            <button type="submit" id="submitPaymentBtn"
                                                                class="btn btn-success">
                                                                <i class="fas fa-save me-1"></i>Submit Payment
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div id="demandDetailsContainer" style="display: none; margin-top: 20px;">
                                    <!-- Demand details will be loaded here via AJAX -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

    @include('include.alerts.ajax-alert')

@endsection
@section('footerScript')
    <script>
        const manualPaymentRoute = "{{ route('manual.payment.details') }}";
        const submitPaymentRoute = "{{ route('submit.manual.payment.details') }}";

        $(document).ready(function() {

            function showPaymentInputError(message) {
                const input = $('#paymentInput');
                const errorDiv = $('#paymentInputError');

                input.addClass('is-invalid');
                errorDiv.text(message).show();
            }

            function hidePaymentInputError() {
                const input = $('#paymentInput');
                const errorDiv = $('#paymentInputError');

                input.removeClass('is-invalid');
                errorDiv.hide();
            }

            // Populate Financial Year Dropdown
            function populateFinancialYears() {
                const currentYear = new Date().getFullYear();
                const currentMonth = new Date().getMonth();

                let startYear, endYear;
                if (currentMonth >= 3) {
                    startYear = currentYear;
                    endYear = currentYear + 1;
                } else {
                    startYear = currentYear - 1;
                    endYear = currentYear;
                }

                const currentFinancialYear = startYear + '-' + endYear;
                const select = $('#financialYear');
                select.empty();
                select.append(`<option value="${currentFinancialYear}" selected>${currentFinancialYear}</option>`);
            }

            populateFinancialYears();

            // Clear error on input
            $('#paymentInput').on('input', function() {
                hidePaymentInputError();
            });

            // Enable/disable input and search button based on dropdown selection
            $('#paymentType').on('change', function() {
                hidePaymentInputError();
                $('#paymentInput').val('');
                const inputField = $('#paymentInput');
                const searchBtn = $('#searchBtn');

                if (this.value === 'propertyId') {
                    inputField.prop('disabled', false);
                    inputField.attr('placeholder', 'Enter Property ID');
                    searchBtn.prop('disabled', false);
                    $('#propertyDetails').hide();
                    $('#demandDetailsContainer').hide();
                } else if (this.value === 'demandId') {
                    inputField.prop('disabled', false);
                    inputField.attr('placeholder', 'Enter Demand ID');
                    searchBtn.prop('disabled', false);
                    $('#propertyDetails').hide();
                    $('#demandDetailsContainer').hide();
                } else {
                    inputField.prop('disabled', true);
                    inputField.attr('placeholder', 'Enter ID');
                    searchBtn.prop('disabled', true);
                    $('#propertyDetails').hide();
                    $('#demandDetailsContainer').hide();
                }
            });

            // Set default transaction date to today
            $('#transactionDate').val(new Date().toISOString().split('T')[0]);

            // Search button click handler
            $('#searchBtn').on('click', function() {
                const paymentType = $('#paymentType').val();
                const searchValue = $('#paymentInput').val().trim();

                // Hide previous errors
                hidePaymentInputError();

                // Validate payment type
                if (!paymentType) {
                    showPaymentInputError('Please select a payment type first');
                    return;
                }

                // Validate search value
                if (!searchValue) {
                    showPaymentInputError('Please enter a value to search');
                    return;
                }

                $(this).prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Searching...'
                );

                $.ajax({
                    url: manualPaymentRoute,
                    type: 'POST',
                    data: {
                        payment_type: paymentType,
                        search_value: searchValue,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            hidePaymentInputError();
                            if (response.type === 'demand') {
                                // Show demand details
                                $('#demandDetailsContainer').html(response.html).show();
                                $('#propertyDetails').hide();

                                // Set default transaction date for demand form
                                $('#demandTransactionDate').val(new Date().toISOString().split(
                                    'T')[0]);

                                // Setup demand payment form handlers
                                setupDemandPaymentForm();
                            } else {
                                // Show property details
                                displayPropertyDetails(response.data);
                                $('#propertyDetails').show();
                                $('#demandDetailsContainer').hide();
                            }
                        } else {
                            showPaymentInputError(response.message || 'Data not found');
                            $('#propertyDetails').hide();
                            $('#demandDetailsContainer').hide();
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while searching';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        showPaymentInputError(errorMsg);
                        $('#propertyDetails').hide();
                        $('#demandDetailsContainer').hide();
                    },
                    complete: function() {
                        $('#searchBtn').prop('disabled', false).html(
                            '<i class="fas fa-search me-2"></i>Search');
                    }
                });
            });

            function displayPropertyDetails(data) {
                $('#oldPropertyId').text(data.old_property_id || '');
                $('#uniquePropertyId').text(data.unique_property_id || '');
                $('#locality').text(data.locality || '');
                $('#block').text(data.block || '');
                $('#plot').text(data.plot || '');
                $('#knownAs').text(data.known_as || '');
                $('#is_joint_property').val(data.is_joint_property || '');
            }

            // ============================================
            // DEMAND PAYMENT FUNCTIONS
            // ============================================
            function setupDemandPaymentForm() {
                // Calculate total amount
                $(document).on('input', '.amountToPay', function() {
                    let total = 0;
                    $('.amountToPay').each(function() {
                        const val = parseFloat($(this).val()) || 0;
                        total += val;
                    });
                    $('#totalAmountToPay').text('₹ ' + total.toFixed(2));

                    // Hide error when user starts typing
                    $('#paymentAmountError').hide();
                    $(this).removeClass('is-invalid');
                });

                // Validate individual amount against max balance on blur
                $(document).on('blur', '.amountToPay', function() {
                    const val = parseFloat($(this).val()) || 0;
                    const maxVal = parseFloat($(this).data('max')) || 0;

                    if (val > 0 && val > maxVal) {
                        $(this).addClass('is-invalid');
                        $('#paymentAmountErrorMessage').text('Amount cannot exceed ₹' + maxVal.toFixed(2));
                        $('#paymentAmountError').show();
                    } else {
                        $(this).removeClass('is-invalid');
                        // Only hide if no other errors
                        if ($('.amountToPay.is-invalid').length === 0) {
                            $('#paymentAmountError').hide();
                        }
                    }
                });

                // Handle demand payment submission - Remove any existing handler and attach new one
                $('#btnSubmitDemandPayment').off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const form = $('#manualDemandPaymentForm');

                    // Hide any previous errors
                    $('#paymentAmountError').hide();
                    $('.amountToPay').removeClass('is-invalid');
                    $('#demandTransactionNumberError').hide();

                    // Validate Transaction Date
                    const transactionDate = $('#demandTransactionDate').val();
                    if (!transactionDate) {
                        showError('Please select Transaction Date');
                        $('#demandTransactionDate').focus();
                        return;
                    }

                    // Validate Transaction Number
                    const transactionNumber = $('#demandTransactionNumber').val().trim();
                    if (!transactionNumber) {
                        $('#demandTransactionNumberError').show();
                        $('#demandTransactionNumber').focus();
                        return;
                    }

                    // Validate at least one amount is entered and not exceeding max
                    let hasAmount = false;
                    let totalAmount = 0;
                    let isValid = true;
                    let firstInvalidInput = null;

                    $('.amountToPay').each(function() {
                        const val = parseFloat($(this).val()) || 0;
                        const maxVal = parseFloat($(this).data('max')) || 0;

                        if (val > 0) {
                            hasAmount = true;
                            totalAmount += val;

                            // Check if amount exceeds max
                            if (val > maxVal) {
                                isValid = false;
                                $(this).addClass('is-invalid');
                                if (!firstInvalidInput) {
                                    firstInvalidInput = $(this);
                                }
                            }
                        }
                    });

                    // If any amount exceeds max balance
                    if (!isValid) {
                        $('#paymentAmountErrorMessage').text(
                            'Some payment amounts exceed the maximum allowed balance. Please correct them.'
                        );
                        $('#paymentAmountError').show();
                        if (firstInvalidInput) {
                            firstInvalidInput.focus();
                            $('html, body').animate({
                                scrollTop: firstInvalidInput.offset().top - 100
                            }, 300);
                        }
                        return;
                    }

                    // If no amount is entered - STOP SUBMISSION HERE
                    if (!hasAmount) {
                        $('#paymentAmountErrorMessage').text('Please enter at least one payment amount');
                        $('#paymentAmountError').show();
                        // Scroll to the error
                        $('html, body').animate({
                            scrollTop: $('#paymentAmountError').offset().top - 100
                        }, 300);
                        // Focus on the first enabled amount input
                        $('.amountToPay:not(:disabled)').first().focus();
                        return; // This stops the submission
                    }

                    // Confirm before submitting
                    if (!confirm('Are you sure you want to submit payment of ₹' + totalAmount.toFixed(2) +
                            '?')) {
                        return;
                    }

                    // Submit the form
                    submitDemandPayment(form, $(this));
                });
            }

            function submitDemandPayment(form, button) {
                const submitBtn = button || $('#btnSubmitDemandPayment');

                // Get form data
                let formData = form.serialize();

                // Add payment type
                formData += '&paymentType=demandId';

                // Add payment mode (manual)
                formData += '&payment_mode=PAY_MANUAL';

                // Add CSRF token
                formData += '&_token=' + $('meta[name="csrf-token"]').attr('content');

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...'
                );

                $.ajax({
                    url: submitPaymentRoute,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showSuccess(response.message || 'Payment submitted successfully');
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        } else {
                            showError(response.message || 'Payment submission failed');
                            submitBtn.prop('disabled', false).html('Submit Payment');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while submitting payment';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg += '\n\n';
                            $.each(errors, function(key, value) {
                                errorMsg += value.join('\n') + '\n';
                            });
                        }
                        showError(errorMsg);
                        submitBtn.prop('disabled', false).html('Submit Payment');
                    }
                });
            }

            // ============================================
            // PROPERTY PAYMENT FUNCTIONS
            // ============================================
            // Property payment form submission
            $('#paymentDetailsForm').on('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    this.reportValidity();
                    return;
                }

                const submitBtn = $('#submitPaymentBtn');
                const formData = {
                    old_property_id: $('#oldPropertyId').text(),
                    unique_property_id: $('#uniquePropertyId').text(),
                    payment_purpose: $('#paymentPurpose').val(),
                    transaction_date: $('#transactionDate').val(),
                    financial_year: $('#financialYear').val(),
                    transaction_number: $('#transactionNumber').val(),
                    amount: $('#amount').val(),
                    paymentType: $("#paymentType").val(),
                    is_joint_property: $('#is_joint_property').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Submitting...'
                );

                $.ajax({
                    url: submitPaymentRoute,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // alert(response.message || 'Payment submitted successfully');
                            showSuccess(response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            // alert(response.message || 'Payment submission failed');
                            showError(response.message);
                            submitBtn.prop('disabled', false).html(
                                '<i class="fas fa-save me-1"></i>Submit Payment');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while submitting payment';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg += '\n\n';
                            $.each(errors, function(key, value) {
                                errorMsg += value.join('\n') + '\n';
                            });
                        }
                        // alert(errorMsg);
                        showError(errorMsg)
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-save me-1"></i>Submit Payment');
                    }
                });
            });

            // Enter key press on input field
            $('#paymentInput').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    if (!$('#searchBtn').prop('disabled')) {
                        $('#searchBtn').click();
                    }
                }
            });

            // Amount input validation
            $('#amount').on('input', function() {
                if (this.value < 0) {
                    this.value = 0;
                }
            });
        });
    </script>
@endsection
