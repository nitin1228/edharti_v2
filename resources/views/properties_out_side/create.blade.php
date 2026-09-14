@extends('layouts.app')
@section('title', 'Add Single Property')
@section('content')
    <link href="{{ asset('assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet" />
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Properties</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Properties</li>
                    <li class="breadcrumb-item active" aria-current="page">Add</li>
                    <li class="breadcrumb-item active" aria-current="page">Properties Outside Delhi</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <!--start stepper three-->
    <!-- <h6 class="text-uppercase">MIS</h6> -->
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="bs-stepper gap-4 vertical">
                <div class="bs-stepper-content">
                    <form id="propertyOutSideForm" method="POST" action="{{ route('vacant.land.store') }}"
                        autocomplete="off">
                        @csrf
                        <p class="mb-4"></p>
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <label for="ColonyNameOld" class="form-label">State</label>
                                <select class="form-select" name="state" id="state"
                                    aria-label="Default select example">
                                    <option value="">Select</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="stateError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="PropertyStatus" class="form-label">City</label>
                                <select class="form-select" id="city" name="city"
                                    aria-label="Default select example">
                                </select>
                                @error('city')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="cityError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-12">
                                <label for="LandType" class="form-label">Address</label>
                                <textarea id="address" name="address" class="form-control"></textarea>
                                @error('address')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="addressError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="area" class="form-label">Area in Sqm.</label>
                                <input type="text" id="area" name="area" class="form-control">
                                @error('area')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="areaError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="present_custodian" class="form-label">Present Custodian</label>
                                <select class="form-select" id="present_custodian" name="present_custodian"
                                    aria-label="Default select example">
                                    <option value="">Select</option>
                                    @foreach ($presentCustodians as $presentCustodian)
                                        <option value="{{ $presentCustodian->id }}">{{ $presentCustodian->item_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('present_custodian')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="present_custodianError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6" id="presentCustodianDetailsField" style="display: none;">
                                <label for="PresentCustodianDetails" class="form-label">Present Custodian Details</label>
                                <input type="text" id="present_custodian_details" name="present_custodian_details"
                                    class="form-control">
                                @error('present_custodian_details')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="present_custodian_detailsError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="custody_date" class="form-label">Date Of Custody <small
                                        class="form-text text-muted">(Please leave the date of custody blank if it is
                                        unknown or since pre-independence.)</small></label>
                                <input type="date" name="custody_date" class="form-control" id="custody_date"
                                    pattern="\d{2} \d{2} \d{4}" value="{{ old('custody_date') }}"
                                    max="{{ date('Y-m-d') }}">
                                @error('custody_date')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="custody_dateError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="present_status" class="form-label">Present Status</label>
                                <select class="form-select" id="present_status" name="present_status"
                                    aria-label="Default select example">
                                    <option value="">Select</option>
                                    @foreach ($presentStatus[0]->items as $status)
                                        <option value="{{ $status->id }}">{{ $status->item_name }}</option>
                                    @endforeach
                                </select>
                                @error('present_status')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="present_statusError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6" id="presentStatusDetailsField" style="display: none;">
                                <label for="PresentStatusDetails" class="form-label">Present Status Details</label>
                                <input type="text" id="present_status_details" name="present_status_details"
                                    class="form-control">
                                @error('present_status_details')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="present_status_detailsError" class="text-danger"></div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="land_use" class="form-label">Land Use</label>
                                <select class="form-select" id="land_use" aria-label="Type of Lease" name="land_use">
                                    <option value="" selected>Select</option>
                                    @foreach ($propertyTypes[0]->items as $propertyType)
                                        <option value="{{ $propertyType->id }}">{{ $propertyType->item_name }}</option>
                                    @endforeach
                                </select>
                                @error('land_use')
                                    <span class="errorMsg">{{ $message }}</span>
                                @enderror
                                <div id="land_useError" class="text-danger"></div>
                            </div>
                            <div class="col-12 col-lg-6">
                            </div>
                            <div class="col-12 col-lg-12">
                                <div class="row">
                                    <div class="col-12 col-lg-4" style="padding-top: 34px;">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mr-2 mb-0">Is there a court case?</h6>
                                            <div class="form-check mr-2">
                                                <input class="form-check-input" type="radio" name="court_case"
                                                    value="Yes" id="courtCaseYes">
                                                <label class="form-check-label" for="courtCaseYes">
                                                    <h6 class="mb-0">Yes</h6>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="court_case"
                                                    value="No" id="courtCaseNo" checked>
                                                <label class="form-check-label" for="courtCaseNo">
                                                    <h6 class="mb-0">No</h6>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-8" id="courtCaseDetailsField" style="display: none;">
                                        <label for="court_case_details" class="form-label">Court Case Details</label>
                                        <textarea id="court_case_details" name="court_case_details" class="form-control"></textarea>
                                        @error('court_case_details')
                                            <span class="errorMsg">{{ $message }}</span>
                                        @enderror
                                        <div id="courtCaseDetailsError" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-12">
                                <div class="row">
                                    <div class="col-12 col-lg-4" style="padding-top: 34px;">
                                        <div class="d-flex align-items-center">
                                            <h6 class="mr-2 mb-0">Is it being used by any department?</h6>
                                            <div class="form-check mr-2">
                                                <input class="form-check-input" type="radio"
                                                    name="user_by_any_department" value="Yes"
                                                    id="user_by_any_department_yes">
                                                <label class="form-check-label" for="user_by_any_department_yes">
                                                    <h6 class="mb-0">Yes</h6>
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="user_by_any_department" value="No"
                                                    id="user_by_any_department_no" checked>
                                                <label class="form-check-label" for="user_by_any_department_no">
                                                    <h6 class="mb-0">No</h6>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-8" id="departmentField" style="display: none;">
                                        <label for="department" class="form-label">Department</label>
                                        <textarea id="department" name="department" class="form-control"></textarea>
                                        @error('department')
                                            <span class="errorMsg">{{ $message }}</span>
                                        @enderror
                                        <div id="departmentError" class="text-danger"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-12">
                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <label for="LandType" class="form-label">Remarks</label>
                                        <textarea id="remarks" name="remarks" class="form-control"></textarea>
                                        @error('remarks')
                                            <span class="errorMsg">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-12 text-end pb-4">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                            </div>
                        </div><!---end row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end stepper three-->
    @include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
    <script>
        function validateInputLength(input) {
            if (input.value.length > 5) {
                input.value = input.value.slice(0, 5);
            }
        }
        document.getElementById('area').addEventListener('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            const value = e.target.value;
            // Allow: digits, one decimal point, and control keys (like backspace)
            if (!/[0-9]/.test(char) && char !== '.' || (char === '.' && value.includes('.'))) {
                e.preventDefault();
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            const yesRadio = document.getElementById("user_by_any_department_yes");
            const noRadio = document.getElementById("user_by_any_department_no");
            const departmentField = document.getElementById("departmentField");
            const departmentInput = document.getElementById("department");
            const departmentError = document.getElementById("departmentError");
            const form = document.querySelector("form"); // adjust if needed
            function toggleDepartmentField() {
                if (yesRadio.checked) {
                    departmentField.style.display = "block";
                } else {
                    departmentField.style.display = "none";
                    departmentInput.value = "";
                    departmentError.textContent = ""; // Clear any old error
                }
            }
            toggleDepartmentField();
            yesRadio.addEventListener("change", toggleDepartmentField);
            noRadio.addEventListener("change", toggleDepartmentField);
            form.addEventListener("submit", function(e) {
                let isValid = true;
                // Check department only if "Yes" is selected
                if (yesRadio.checked && departmentInput.value.trim() === "") {
                    departmentError.textContent = "Department is required.";
                    isValid = false;
                } else {
                    departmentError.textContent = "";
                }
                if (!isValid) {
                    e.preventDefault(); // stop form submission
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const courtCaseYes = document.getElementById("courtCaseYes");
            const courtCaseNo = document.getElementById("courtCaseNo");
            const courtCaseDetailsField = document.getElementById("courtCaseDetailsField");
            const courtCaseDetailsInput = document.getElementById("court_case_details");
            const courtCaseDetailsError = document.getElementById("courtCaseDetailsError");

            function toggleCourtCaseField() {
                if (courtCaseYes.checked) {
                    courtCaseDetailsField.style.display = "block";
                } else {
                    courtCaseDetailsField.style.display = "none";
                    courtCaseDetailsInput.value = "";
                    courtCaseDetailsError.textContent = "";
                }
            }
            toggleCourtCaseField();
            courtCaseYes.addEventListener("change", toggleCourtCaseField);
            courtCaseNo.addEventListener("change", toggleCourtCaseField);
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                let isValid = true;
                if (courtCaseYes.checked && courtCaseDetailsInput.value.trim() === "") {
                    courtCaseDetailsError.textContent = "Court case details are required.";
                    isValid = false;
                } else {
                    courtCaseDetailsError.textContent = "";
                }
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const presentStatusSelect = document.getElementById("present_status");
            const presentStatusDetailsField = document.getElementById("presentStatusDetailsField");
            const presentStatusDetailsInput = document.getElementById("present_status_details");
            const presentStatusDetailsError = document.getElementById("present_status_detailsError");

            function togglePresentStatusDetails() {
                if (presentStatusSelect.value) {
                    presentStatusDetailsField.style.display = "block";
                } else {
                    presentStatusDetailsField.style.display = "none";
                    presentStatusDetailsInput.value = "";
                    presentStatusDetailsError.textContent = "";
                }
            }
            togglePresentStatusDetails(); // Initial check
            presentStatusSelect.addEventListener("change", togglePresentStatusDetails);
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                let isValid = true;
                if (presentStatusSelect.value && presentStatusDetailsInput.value.trim() === "") {
                    presentStatusDetailsError.textContent = "Present status details are required.";
                    isValid = false;
                } else {
                    presentStatusDetailsError.textContent = "";
                }
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const presentCustodianSelect = document.getElementById("present_custodian");
            const presentCustodianDetailsField = document.getElementById("presentCustodianDetailsField");
            const presentCustodianDetailsInput = document.getElementById("present_custodian_details");
            const presentCustodianDetailsError = document.getElementById("present_custodian_detailsError");

            function toggleCustodianDetailsField() {
                const selectedText = presentCustodianSelect.options[presentCustodianSelect.selectedIndex].text
                    .trim();
                if (selectedText === "Other") {
                    presentCustodianDetailsField.style.display = "block";
                } else {
                    presentCustodianDetailsField.style.display = "none";
                    presentCustodianDetailsInput.value = "";
                    presentCustodianDetailsError.textContent = "";
                }
            }
            toggleCustodianDetailsField(); // Initial check on page load
            presentCustodianSelect.addEventListener("change", toggleCustodianDetailsField);
            const form = document.querySelector("form");
            form.addEventListener("submit", function(e) {
                let isValid = true;
                const selectedText = presentCustodianSelect.options[presentCustodianSelect.selectedIndex]
                    .text.trim();
                if (selectedText === "Other" && presentCustodianDetailsInput.value.trim() === "") {
                    presentCustodianDetailsError.textContent = "Custodian details are required.";
                    isValid = false;
                } else {
                    presentCustodianDetailsError.textContent = "";
                }
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
        $(document).ready(function() {
            function isEmpty(val) {
                return val === null || val.trim() === '';
            }

            function showErrorCustom(selector, message) {
                $(selector).text(message);
            }

            function clearErrors() {
                $('.errorMsg').text('');
                $('.text-danger').text('');
            }

            function validatePropertyForm() {
                let isValid = true;
                clearErrors();
                // Validate select dropdowns
                const requiredSelects = [{
                        id: '#state',
                        label: 'State'
                    },
                    {
                        id: '#city',
                        label: 'City'
                    },
                    {
                        id: '#address',
                        label: 'Address'
                    },
                    {
                        id: '#present_custodian',
                        label: 'Present Custodian'
                    },
                    {
                        id: '#present_status',
                        label: 'Present Status'
                    },
                ];
                requiredSelects.forEach(field => {
                    const val = $(field.id).val();
                    if (isEmpty(val)) {
                        showErrorCustom(`${field.id} ~ .text-danger`, `${field.label} is required.`);
                        isValid = false;
                    }
                });
                // Validate numeric field: Area
                const area = $('#area').val();
                if (!/^\d+(\.\d{1,2})?$/.test(area)) {
                    showErrorCustom('#area ~ .text-danger', 'Area must be a valid number.');
                    isValid = false;
                }
                // Department field if department usage is Yes
                if ($('input[name="user_by_any_department"]:checked').val() === 'Yes') {
                    const department = $('#department').val();
                    if (isEmpty(department)) {
                        showErrorCustom('#department ~ .text-danger', 'Department is required.');
                        isValid = false;
                    }
                }
                const selectPresentCustodian = document.getElementById("present_custodian");
                const selectedPresentCustodianText = selectPresentCustodian.options[selectPresentCustodian
                        .selectedIndex].text
                    .trim();
                if (selectedPresentCustodianText === 'Other') {
                    const pcd = $('#present_custodian_details').val();
                    if (isEmpty(pcd)) {
                        showErrorCustom('#present_custodian_details ~ .text-danger',
                            'Present custodian details is required.');
                        isValid = false;
                    }
                }
                const selectPresentStatus = document.getElementById("present_status");
                const selectedPresentStatusText = selectPresentStatus.options[selectPresentStatus.selectedIndex]
                    .text.trim();
                if (selectedPresentStatusText !== '') {
                    const psd = $('#present_status_details').val();
                    if (isEmpty(psd)) {
                        showErrorCustom('#present_status_details ~ .text-danger',
                            'Present status details is required.');
                        isValid = false;
                    }
                }

                // Validate court case details if "Yes" is selected
                if ($('#courtCaseYes').is(':checked')) {
                    const courtDetails = $('#court_case_details').val();
                    if (isEmpty(courtDetails)) {
                        showErrorCustom('#court_case_details ~ .text-danger', 'Court case details are required.');
                        isValid = false;
                    }
                }
                return isValid;
            }
            $('#propertyOutSideForm').on('submit', function(e) {
                e.preventDefault();

                if (!validatePropertyForm()) {
                    return; // Stop submission if validation fails
                }

                const $form = $(this);
                const $submitBtn = $form.find('button[type="submit"]');

                // Disable the submit button
                $submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Submitting...');

                const formData = $form.serialize();

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status !== false) {
                            showSuccess(response.message);
                            $form[0].reset();
                            $('#departmentField').hide();
                            setTimeout(() => {
                                window.location.href =
                                    "{{ route('vacant.land.list') }}";
                            }, 2000);
                        } else {
                            showError(response.message);
                            $submitBtn.prop('disabled', false).html(
                                'Submit'); // Re-enable on error
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('[name="' + key + '"]').next('.errorMsg').text(value[
                                    0]);
                            });
                        } else {
                            showErrorCustom('.form-general-error', xhr.responseJSON?.message ||
                                'An error occurred.');
                            alert('An error occurred. Please try again.');
                        }
                        $submitBtn.prop('disabled', false).html('Submit'); // Re-enable on error
                    }
                });
            });
        });
        $(document).ready(function() {
            $('input[name="user_by_any_department"]').change(function() {
                if ($(this).val() === 'Yes') {
                    $('#departmentField').show();
                } else {
                    $('#departmentField').hide();
                }
            });
            // Trigger on page load to handle pre-selected radio
            $('input[name="user_by_any_department"]:checked').trigger('change');
        });
        $(document).ready(function() {
            $('#state').on('change', function() {
                var stateId = $(this).val();
                $('#city').empty().append('<option value="">Loading...</option>');
                if (stateId) {
                    $.ajax({
                        url: "{{ route('get.cities.by.state') }}", // Laravel route
                        type: "GET",
                        data: {
                            state_id: stateId
                        },
                        success: function(response) {
                            $('#city').empty().append('<option value="">Select City</option>');
                            $.each(response.cities, function(key, city) {
                                $('#city').append('<option value="' + city.id + '">' +
                                    city.name + '</option>');
                            });
                        },
                        error: function() {
                            $('#city').empty().append(
                                '<option value="">Error loading cities</option>');
                        }
                    });
                } else {
                    $('#city').empty().append('<option value="">Select State first</option>');
                }
            });
        });

        function propertySearch(PropertyId) {
            if (PropertyId) {
                $.ajax({
                    url: "{{ route('propertySearch') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        property_id: PropertyId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        //console.log(response);
                        if (response.status === true) {
                            console.log(response);
                            $('#propertIdError').hide();
                            $("input[name='file_number']").val(response.data.file_number);
                            $("#ColonyNameOld option[value=" + response.data.colony_id + "]").attr('selected',
                                true);
                            // $("#PropertyStatus option[value=" + response.data.property_status + "]").attr('selected', true);
                            $("#PropertyStatus option[value='" + response.data.property_status + "']").prop(
                                'selected', true);
                            $("#LandType option[value=" + response.data.land_type + "]").attr('selected', true);
                            // for new form with 2 steps - SOURAV CHAUHAN (06/Dec/2024)
                            $("#ColonyNameOldNew option[value=" + response.data.colony_id + "]").attr(
                                'selected', true);
                            // $("#PropertyStatusNew option[value=" + response.data.property_status + "]").attr('selected', true);
                            $("#PropertyStatusNew option[value='" + response.data.property_status + "']").prop(
                                'selected', true);
                            $("#LandTypeNew option[value=" + response.data.land_type + "]").attr('selected',
                                true);
                        } else if (response.status === false) {
                            $('#propertIdError').text(response.message);
                            $("input[name='file_number']").val('');
                        } else {
                            $('#propertIdError').text('Property Id not available');
                            $("input[name='file_number']").val('');
                        }
                    },
                    error: function(response) {
                        console.log(response);
                    }
                })
            } else {
                $('#propertIdError').text('Please provide a valid Property ID');
            }
        }
        $("#PropertyIDSearchBtn").on('click', function() {
            var PropertyId = $('#PropertyID').val();
            propertySearch(PropertyId)
        });
        $("#PropertyIDSearchBtnNew").on('click', function() {
            var PropertyIdNew = $('#PropertyIDNew').val();
            propertySearch(PropertyIdNew)
        });
        $(document).ready(function() {
            $('#propertyType').on('change', function() {
                var idPropertyType = this.value;
                $("#propertySubType").html('');
                $.ajax({
                    url: "{{ route('prpertySubTypes') }}",
                    type: "POST",
                    data: {
                        property_type_id: idPropertyType,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#propertySubType').html('<option value="">Select Sub Type</option>');
                        $.each(result, function(key, value) {
                            $("#propertySubType").append('<option value="' + value
                                .id + '">' + value.item_name + '</option>');
                        });
                    }
                });
            });
            $('#oldPropertyType').on('change', function() {
                var idPropertyType = this.value;
                $("#oldPropertySubType").html('');
                $.ajax({
                    url: "{{ route('prpertySubTypes') }}",
                    type: "POST",
                    data: {
                        property_type_id: idPropertyType,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#oldPropertySubType').html(
                            '<option value="">Select Sub Type</option>');
                        $.each(result, function(key, value) {
                            $("#oldPropertySubType").append('<option value="' + value
                                .id + '">' + value.item_name + '</option>');
                        });
                    }
                });
            });
        });

        function checkPropExist() {
            // Get the Colony Name
            var localityDropdown = document.getElementById('colonyName');
            var blockInputField = document.getElementById('blockno');
            var plotInputField = document.getElementById('plotno');
            var locality = localityDropdown.value;
            var block = blockInputField.value;
            var plot = plotInputField.value;
            if (locality != '' && block != '' && plot != '') {
                $.ajax({
                    url: "{{ route('searchPropThroughLocalityBlockPlot') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        locality: locality,
                        block: block,
                        plot: plot,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === true) {
                            $('#propertExistIdError').text(response.message);
                        } else {
                            console.log(response.message);
                            $('#propertExistIdError').text('');
                        }
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            }
        }
    </script>
@endsection
