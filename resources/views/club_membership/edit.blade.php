@extends('layouts.app')
@section('title', 'Add Club Membership Details')
@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
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
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--breadcrumb-->
    <div class="card shadow-sm mb-4">
        <form action="{{ route('update.club.membership.form') }}" method="POST" enctype="multipart/form-data"
            id="clubMembershipForm">
            @csrf
            <input type="hidden" name="clubMembershipId" id="clubMembershipId" value="{{ $getClubMembershipDetails->id }}">
            <div class="card-body">
                <div class="part-title">
                    <h5>Club Membership Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="col-lg-12 col-12">
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label for="club_type" class="form-label">Club Membership Type<span
                                            class="text-danger">*</span></label>
                                    <select name="club_type" id="club_type" class="form-select">
                                        @if ($getClubMembershipDetails->club_type == 'IHC')
                                            <option value="IHC" selected>India Habitat Centre</option>
                                        @else
                                            <option value="DGC" selected>Delhi Golf Club</option>
                                        @endif
                                    </select>
                                    @error('club_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="club_typeError"></div>
                                </div>

                                <div class="col-lg-4">
                                    <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control alphabets-input" name="name" id="name"
                                        placeholder="Enter Name" value="{{ $getClubMembershipDetails->name ?? '' }}"
                                        maxlength="30">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="nameError"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="category" class="form-label">Category<span
                                            class="text-danger">*</span></label>
                                    @if ($getClubMembershipDetails->club_type == 'IHC')
                                        <select name="category" id="category" class="form-select">
                                            <option value="Member of Parliament"
                                                @if ($getClubMembershipDetails->category == 'Member of Parliament') selected @endif>
                                                Member of Parliament</option>
                                            <option value="Secretary/Spl. Secretary/Additional Secretary and equivalent"
                                                @if ($getClubMembershipDetails->category == 'Secretary/Spl. Secretary/Additional Secretary and equivalent') selected @endif>
                                                Secretary/Spl. Secretary/Additional Secretary and equivalent</option>
                                            <option value="Joint Secretaries / Directors and equivalent"
                                                @if ($getClubMembershipDetails->category == 'Joint Secretaries / Directors and equivalent') selected @endif>Joint Secretaries /
                                                Directors and equivalent</option>
                                            <option value="other" @if ($getClubMembershipDetails->category == 'other') selected @endif>Other
                                            </option>
                                        </select>
                                    @else
                                        <select name="category" id="category" class="form-select">
                                            <option value="Member of Parliament"
                                                @if ($getClubMembershipDetails->category == 'Member of Parliament') selected @endif>Member of Parliament
                                            </option>
                                            <option value="Secretary/ Special Secretary and equivalent"
                                                @if ($getClubMembershipDetails->category == 'Secretary/ Special Secretary and equivalent') selected @endif>Secretary/ Special
                                                Secretary and equivalent</option>
                                            <option value="Additional Secretary and equivalent"
                                                @if ($getClubMembershipDetails->category == 'Additional Secretary and equivalent') selected @endif>Additional Secretary and
                                                equivalent</option>
                                            <option value="Joint Secretary and equivalent"
                                                @if ($getClubMembershipDetails->category == 'Joint Secretary and equivalent') selected @endif>Joint Secretary and
                                                equivalent
                                            </option>
                                            <option value="Director and equivalent"
                                                @if ($getClubMembershipDetails->category == 'Director and equivalent') selected @endif>Director and equivalent
                                            </option>
                                            <option value="other" @if ($getClubMembershipDetails->category == 'other') selected @endif>Other
                                            </option>
                                        </select>
                                    @endif
                                    @error('category')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="categoryError"></div>
                                </div>
                                <div class="col-lg-3" id="otherCategoryDiv" @style(!empty($getClubMembershipDetails->other_category && $getClubMembershipDetails->category == 'other') ? 'display: block;' : 'display: none;')>
                                    <label for="other_category" class="form-label">Other Category<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control alphanumeric-input" name="other_category"
                                        id="other_category" placeholder="Enter Other Category"
                                        value="{{ $getClubMembershipDetails->other_category }}" maxlength="50">
                                    @error('other_category')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="other_categoryError"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label for="designation" class="form-label">Designation<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control alphanumeric-input" name="designation"
                                        id="designation" placeholder="Enter designation"
                                        value="{{ $getClubMembershipDetails->designation ?? '' }}" maxlength="50">
                                    @error('designation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="designationError"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="designation_equivalent_to" class="form-label">Equivalent to
                                        Designation<span class="text-danger">*</span></label>
                                    <select name="designation_equivalent_to" id="designation_equivalent_to"
                                        class="form-select">
                                        <option value="">Select</option>
                                        <option value="Member of Parliament"
                                            @if ($getClubMembershipDetails->designation_equivalent_to == 'Member of Parliament') selected @endif>Member
                                            of
                                            Parliament
                                        </option>
                                        <option value="Secretary" @if ($getClubMembershipDetails->designation_equivalent_to == 'Secretary') selected @endif>
                                            Secretary
                                        </option>

                                        <option value="Additional Secretary"
                                            @if ($getClubMembershipDetails->designation_equivalent_to == 'Additional Secretary') selected @endif>
                                            Additional
                                            Secretary
                                        </option>
                                        <option value="Joint Secretary"
                                            @if ($getClubMembershipDetails->designation_equivalent_to == 'Joint Secretary') selected @endif>Joint
                                            Secretary
                                        </option>
                                        <option value="Director" @if ($getClubMembershipDetails->designation_equivalent_to == 'Director') selected @endif>
                                            Director
                                        </option>

                                        {{-- <option value="OTHER" @if ($getClubMembershipDetails->designation_equivalent_to == 'OTHER') selected @endif>
                                            Other
                                        </option> --}}
                                    </select>
                                    @error('designation_equivalent_to')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="designation_equivalent_toError"></div>
                                </div>
                                <div class="col-lg-3" id="otherEquivalentDesignationDiv" @style(!empty($getClubMembershipDetails->other_designation_equivalent_to && $getClubMembershipDetails->designation_equivalent_to == 'OTHER') ? 'display: block;' : 'display: none;')>
                                    <label for="other_designation_equivalent_to" class="form-label">Equivalent to
                                        Other
                                        Designation<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control alphanumeric-input"
                                        name="other_designation_equivalent_to" id="other_designation_equivalent_to"
                                        placeholder="Enter Equivalent to Other Designation"
                                        value="{{ $getClubMembershipDetails->other_designation_equivalent_to ?? '' }}"
                                        maxlength="50">
                                    @error('other_designation_equivalent_to')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="other_designation_equivalent_toError"></div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="email" class="form-label">Email<span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="Enter email" value="{{ $getClubMembershipDetails->email ?? '' }}">
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="emailError"></div>
                                </div>



                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="mobile" class="form-label">Mobile Number<span
                                            class="text-danger">*</span></label>
                                    <input type="numeric" class="form-control" name="mobile" id="mobile"
                                        placeholder="Enter Mobile Number"
                                        value="{{ $getClubMembershipDetails->mobile ?? '' }}" maxlength="10">
                                    @error('mobile')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="mobileError"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label for="department" class="form-label">Department<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control alphanumeric-input" name="department"
                                        id="department" placeholder="Enter department"
                                        value="{{ $getClubMembershipDetails->department ?? '' }}" maxlength="100">
                                    @error('department')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="departmentError"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label for="name_of_service" class="form-label">Name of Service<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control alphanumeric-input" name="name_of_service"
                                        id="name_of_service" placeholder="Enter Name of Service"
                                        value="{{ $getClubMembershipDetails->name_of_service ?? '' }}" maxlength="50">
                                    @error('name_of_service')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="name_of_serviceError"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label for="year_of_allotment" class="form-label">Allotment Year<span
                                            class="text-danger">*</span></label>
                                    {{-- <input type="numeric" class="form-control" name="year_of_allotment"
                                        id="year_of_allotment" placeholder="Enter Allotment Year"
                                        value="{{ $getClubMembershipDetails->year_of_allotment ?? '' }}" maxlength="4"> --}}
                                    @php
                                        $selectedYear = $getClubMembershipDetails->year_of_allotment ?? old('year'); // selected year from DB or previous input
                                    @endphp
                                    <select name="year_of_allotment" id="year_of_allotment" class="form-select">
                                        @for ($year = date('Y'); $year >= date('Y') - 60; $year--)
                                            <option value="{{ $year }}"
                                                {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('year_of_allotment')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="year_of_allotmentError"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label for="date_of_application" class="form-label">Date of Application<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_of_application" class="form-control"
                                        id="date_of_application" max="{{ date('Y-m-d') }}"
                                        value="{{ $getClubMembershipDetails->date_of_application ?? '' }}">
                                    @error('date_of_application')
                                        <span class="errorMsg">{{ $message }}</span>
                                    @enderror
                                    <div id="date_of_applicationError" class="text-danger"></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="is_central_deputated" class="form-label">Do you hold a central staffing
                                        position?<span class="text-danger">*</span></label>
                                    <select id="is_central_deputated" name="is_central_deputated" class="form-control">
                                        <option value="">Select</option>
                                        <option value="1" @if ($getClubMembershipDetails->is_central_deputated == 1) selected @endif>Yes
                                        </option>
                                        <option value="0" @if ($getClubMembershipDetails->is_central_deputated == 0) selected @endif>No
                                        </option>
                                    </select>
                                    @error('is_central_deputated')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="is_central_deputatedError"></div>
                                </div>
                            </div>
                            <div class="row mb-3" id="centralStaffing"
                                style="{{ $getClubMembershipDetails->is_central_deputated == 0 ? 'display: none;' : '' }}">
                                <div class="col-lg-6">
                                    <label for="date_of_joining_central_deputation" class="form-label">Date of Joining on
                                        Central Deputation in Delhi<span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_joining_central_deputation" class="form-control"
                                        id="date_of_joining_central_deputation" max="{{ date('Y-m-d') }}"
                                        value="{{ !empty($getClubMembershipDetails->date_of_joining_central_deputation) ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_joining_central_deputation)->format('Y-m-d') : '' }}">
                                    @error('date_of_joining_central_deputation')
                                        <span class="errorMsg">{{ $message }}</span>
                                    @enderror
                                    <div id="date_of_joining_central_deputationError" class="text-danger"></div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="expected_date_of_tenure_completion" class="form-label">Expected Date of
                                        Completion of Tenure<span class="text-danger">*</span></label>
                                    <input type="date" name="expected_date_of_tenure_completion" class="form-control"
                                        id="expected_date_of_tenure_completion" min="{{ date('Y-m-d') }}"
                                        value="{{ !empty($getClubMembershipDetails->expected_date_of_tenure_completion) ? \Carbon\Carbon::parse($getClubMembershipDetails->expected_date_of_tenure_completion)->format('Y-m-d') : '' }}">
                                    @error('expected_date_of_tenure_completion')
                                        <span class="errorMsg">{{ $message }}</span>
                                    @enderror
                                    <div id="expected_date_of_tenure_completionError" class="text-danger"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="date_of_superannuation" class="form-label">Date of Superannuation<span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date_of_superannuation" class="form-control"
                                        id="date_of_superannuation" min="{{ date('Y-m-d') }}"
                                        value="{{ !empty($getClubMembershipDetails->date_of_superannuation) ? \Carbon\Carbon::parse($getClubMembershipDetails->date_of_superannuation)->format('Y-m-d') : '' }}">
                                    @error('date_of_superannuation')
                                        <span class="errorMsg">{{ $message }}</span>
                                    @enderror
                                    <div id="date_of_superannuationError" class="text-danger"></div>
                                </div>

                                <div class="col-lg-3">
                                    <label for="office_address" class="form-label">Office Address<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control alpha-numeric-comma-dot-slash-input"
                                        name="office_address" id="office_address" placeholder="Enter Office Address"
                                        value="{{ $getClubMembershipDetails->office_address ?? '' }}" maxlength="150">
                                    @error('office_address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="office_addressError"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label for="telephone_no" class="form-label">Telephone Number<span
                                            class="text-danger">*</span></label>
                                    <input type="numeric" name="telephone_no" maxlength="10" class="form-control"
                                        id="telephone_no" value="{{ $getClubMembershipDetails->telephone_no ?? '' }}">
                                    @error('telephone_no')
                                        <span class="errorMsg">{{ $message }}</span>
                                    @enderror
                                    <div id="telephone_noError" class="text-danger"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label for="pay_scale" class="form-label">Pay Level<span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="pay_scale" name="pay_scale">
                                        <option value="">Select</option>
                                        <option value="13" @if ($getClubMembershipDetails->pay_scale == '13') selected @endif>13
                                        </option>
                                        <option value="13A" @if ($getClubMembershipDetails->pay_scale == '13A') selected @endif>13A
                                        </option>
                                        <option value="14" @if ($getClubMembershipDetails->pay_scale == '14') selected @endif>14
                                        </option>
                                        <option value="15" @if ($getClubMembershipDetails->pay_scale == '15') selected @endif>15
                                        </option>
                                        <option value="16" @if ($getClubMembershipDetails->pay_scale == '16') selected @endif>16
                                        </option>
                                        <option value="17" @if ($getClubMembershipDetails->pay_scale == '17') selected @endif>17
                                        </option>
                                        <option value="18" @if ($getClubMembershipDetails->pay_scale == '18') selected @endif>18
                                        </option>
                                    </select>
                                    @error('pay_scale')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="pay_scaleError"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    <label for="present_previous_membership_of_other_clubs" class="form-label">Name Of
                                        Present/Previous Membership Of Other Clubs</label>
                                    <input type="text" class="form-control alphanumeric-input"
                                        name="present_previous_membership_of_other_clubs"
                                        id="present_previous_membership_of_other_clubs"
                                        placeholder="Enter Name Of Present/Previous Membership Of Other Clubs"
                                        value="{{ $getClubMembershipDetails->present_previous_membership_of_other_clubs ?? '' }}">
                                    @error('present_previous_membership_of_other_clubs')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="present_previous_membership_of_other_clubsError">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="document" class="form-label">Upload Document</label>
                                    <input type="file" class="form-control" name="document" id="document"
                                        accept="application/pdf">
                                    @error('document')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="documentError"></div>
                                    @if ($getClubMembershipDetails->club_type == 'IHC')
                                        @if (!empty($getClubMembershipDetails->ihcDetails?->ihcs_doc))
                                            <span><a href="{{ asset('storage/' . $getClubMembershipDetails->ihcDetails->ihcs_doc ?? '') }}"
                                                    target="_blank">View Uploaded Document</a></span>
                                        @endif
                                    @else
                                        @if (!empty($getClubMembershipDetails->dgcDetails->dgcs_doc))
                                            <span><a href="{{ asset('storage/' . $getClubMembershipDetails->dgcDetails->dgcs_doc ?? '') }}"
                                                    target="_blank">View Uploaded Document</a></span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="col-lg-12 col-12">
                            <div class="row mb-3">
                                <div class="d-flex justify-content-end">
                                    <button type="button" id="updateClubMembershipFormBtn"
                                        class="btn btn-primary btn-theme">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Dynamic Element --}}
@endsection
@section('footerScript')
    <script>
        $(document).ready(function() {
            $('#is_central_deputated').on('change', function() {
                var value = $(this).val();
                if (value === "1") {
                    $('#centralStaffing').show(); // display block
                } else {
                    $('#centralStaffing').hide(); // hide
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const dateFields = [
                "date_of_application",
                "date_of_joining_central_deputation",
                "expected_date_of_tenure_completion",
                "date_of_superannuation"
            ];

            dateFields.forEach(function(fieldId) {
                const input = document.getElementById(fieldId);
                if (input) {
                    // Prevent typing
                    input.addEventListener("keydown", e => e.preventDefault());
                    // Prevent paste
                    input.addEventListener("paste", e => e.preventDefault());
                }
            });
        });

        document.getElementById('is_central_deputated').addEventListener('change', validateDates);
        document.getElementById('date_of_joining_central_deputation').addEventListener('change', validateDates);
        document.getElementById('expected_date_of_tenure_completion').addEventListener('change', validateDates);
        document.getElementById('date_of_superannuation').addEventListener('change', validateDates);

        function validateDates() {
            const deputed = document.getElementById('is_central_deputated').value;
            const joining = new Date(document.getElementById('date_of_joining_central_deputation').value);
            const tenure = new Date(document.getElementById('expected_date_of_tenure_completion').value);
            const superannuation = new Date(document.getElementById('date_of_superannuation').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            let valid = true;
            let errors = {};

            if (deputed === "1") {
                if (tenure <= joining) {
                    errors.tenure = "Tenure completion must be after date of joining.";
                    valid = false;
                }
                if (superannuation < tenure) {
                    errors.superannuation = "Superannuation must be on or after tenure completion.";
                    valid = false;
                }
            } else if (deputed === "0") {
                if (superannuation <= today) {
                    errors.superannuation = "Superannuation must be a future date.";
                    valid = false;
                }
            }

            document.getElementById('expected_date_of_tenure_completionError').innerText = errors.tenure || "";
            document.getElementById('date_of_superannuationError').innerText = errors.superannuation || "";

            return valid;
        }


        //On Club Type Dropdown change, change the options value of category dropdow for IHC & DGC - LALIT (30/Jan/2025)
        document.getElementById("club_type").addEventListener("change", function() {
            let categoryDropdown = document.getElementById("category");
            categoryDropdown.innerHTML = ""; // Clear existing options
            let club_type = this.value;
            let options = {
                IHC: ["Member of Parliament", "Secretary/Spl. Secretary/Additional Secretary and equivalent",
                    "Joint Secretaries / Directors and equivalent",
                    // "Other",
                ],
                DGC: ["Member of Parliament", "Secretary/ Special Secretary and equivalent",
                    "Additional Secretary and equivalent",
                    "Joint Secretary and equivalent", "Director and equivalent",
                    // "Other",
                ]
            };
            if (options[club_type]) {
                options[club_type].forEach(item => {
                    let option = document.createElement("option");
                    option.value = item.toLowerCase();
                    option.textContent = item;
                    categoryDropdown.appendChild(option);
                });
            }

            let categoryValue = document.getElementById("category").value;
            if (categoryValue != '' && categoryValue == 'other') {
                $('#otherCategoryDiv').show();
            } else {
                $('#otherCategoryDiv').hide();
            }
        });
        //On Category Dropdown change visible other category input field - LALIT (31/Jan/2025)
        document.getElementById("category").addEventListener("change", function() {
            let categoryValue = this.value;
            if (categoryValue != '' && categoryValue == 'other') {
                $('#otherCategoryDiv').show();
            } else {
                $('#otherCategoryDiv').hide();
            }
        });

        //On Equivalant Designation change visible other equivalant designation input field - LALIT (31/Jan/2025)
        document.getElementById("designation_equivalent_to").addEventListener("change", function() {
            let categoryValue = this.value;
            if (categoryValue != '' && categoryValue == 'OTHER') {
                $('#otherEquivalentDesignationDiv').show();
            } else {
                $('#otherEquivalentDesignationDiv').hide();
            }
        });

        $(document).ready(function() {
            $('#updateClubMembershipFormBtn').click(function(e) {
                e.preventDefault(); // Prevent default form submission

                // Clear previous error messages
                let isValid = true;

                // Validate presentOccupantName
                if (!$('#club_type').val().trim()) {
                    $('#club_typeError').text('Please select club membership type.');
                    isValid = false;
                    $('#club_type').focus();
                } else {
                    $("#club_typeError").text(""); // Clear the error message
                }
                if (!$('#category').val().trim()) {
                    $('#categoryError').text('Please select category.');
                    isValid = false;
                    $('#category').focus();
                } else {
                    $("#categoryError").text(""); // Clear the error message
                }

                // Get the selected value of the dropdown
                var categoryDropdownValue = $('#category').val();
                if (categoryDropdownValue != '' && categoryDropdownValue == 'other') {
                    if (!$('#other_category').val().trim()) {
                        $('#other_categoryError').text('Enter other category.');
                        isValid = false;
                        $('#other_category').focus();
                    } else {
                        $("#other_categoryError").text(""); // Clear the error message
                    }
                }

                if (!$('#date_of_application').val().trim()) {
                    $('#date_of_applicationError').text('Select application date.');
                    isValid = false;
                    $('#date_of_application').focus();
                } else {
                    $("#date_of_applicationError").text(""); // Clear the error message
                }

                if (!$('#name').val().trim()) {
                    $('#nameError').text('Enter the name.');
                    isValid = false;
                    $('#name').focus();
                } else {
                    $("#nameError").text(""); // Clear the error message
                }

                if (!$('#designation').val().trim()) {
                    $('#designationError').text('Enter the designation.');
                    isValid = false;
                    $('#designation').focus();
                } else {
                    $("#designationError").text(""); // Clear the error message
                }

                if (!$('#designation_equivalent_to').val().trim()) {
                    $('#designation_equivalent_toError').text('Please select equivalent designation.');
                    isValid = false;
                    $('#designation_equivalent_to').focus();
                } else {
                    $("#designation_equivalent_toError").text(""); // Clear the error message
                }

                // Get the selected value of the dropdown
                var eqDesignationDropdownValue = $('#designation_equivalent_to').val();
                if (eqDesignationDropdownValue != '' && eqDesignationDropdownValue == 'OTHER') {
                    if (!$('#other_designation_equivalent_to').val().trim()) {
                        $('#other_designation_equivalent_toError').text(
                            'Enter other equivalent designation.');
                        isValid = false;
                        $('#other_designation_equivalent_to').focus();
                    } else {
                        $("#other_designation_equivalent_toError").text(""); // Clear the error message
                    }
                }

                if (!$('#name_of_service').val().trim()) {
                    $('#name_of_serviceError').text('Enter the service name.');
                    isValid = false;
                    $('#name_of_service').focus();
                } else {
                    $("#name_of_serviceError").text(""); // Clear the error message
                }

                const yearField = $('#year_of_allotment');
                const yearError = $('#year_of_allotmentError');

                if (!yearField.val()) {
                    yearError.text('Select the allotment year.');
                    isValid = false;
                    yearField.focus();
                } else {
                    yearError.text('');
                }

                const deputedSelect = document.getElementById('is_central_deputated');
                const joiningDate = $('#date_of_joining_central_deputation').val().trim();
                const completionDate = $('#expected_date_of_tenure_completion').val().trim();

                // Reset error messages first
                $('#date_of_joining_central_deputationError').text('');
                $('#expected_date_of_tenure_completionError').text('');

                if (deputedSelect.value === '1') {
                    if (!joiningDate) {
                        $('#date_of_joining_central_deputationError').text('Select joining date.');
                        isValid = false;
                    }

                    if (!completionDate) {
                        $('#expected_date_of_tenure_completionError').text('Select completion date.');
                        isValid = false;
                    }

                    // Only compare dates if both are present
                    if (joiningDate && completionDate) {
                        const startDate = new Date(joiningDate);
                        const endDate = new Date(completionDate);
                        if (startDate > endDate) {
                            $('#date_of_joining_central_deputationError').text(
                                'Joining date should not be after completion date.');
                            isValid = false;
                        }
                    }
                }

                if (!$('#date_of_superannuation').val().trim()) {
                    $('#date_of_superannuationError').text('Select superannuatation date.');
                    isValid = false;
                    $('#date_of_superannuation').focus();
                } else {
                    $("#date_of_superannuationError").text(""); // Clear the error message
                }

                if (!$('#office_address').val().trim()) {
                    $('#office_addressError').text('Enter office address.');
                    isValid = false;
                    $('#office_address').focus();
                } else {
                    $("#office_addressError").text(""); // Clear the error message
                }

                const email = $('#email').val().trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!email) {
                    $('#emailError').text('Enter email address.');
                    isValid = false;
                    $('#email').focus();
                } else if (!emailRegex.test(email)) {
                    $('#emailError').text('Enter a valid email address.');
                    isValid = false;
                    $('#email').focus();
                } else {
                    $('#emailError').text('');
                }

                if (!$('#mobile').val().trim()) {
                    $('#mobileError').text('Enter mobile number.');
                    isValid = false;
                    $('#mobile').focus();
                } else {
                    $("#mobileError").text(""); // Clear the error message
                }



                if (!$('#pay_scale').val().trim()) {
                    $('#pay_scaleError').text('Enter pay scale.');
                    isValid = false;
                    $('#pay_scale').focus();
                } else {
                    $("#pay_scaleError").text(""); // Clear the error message
                }

                if (!$('#telephone_no').val().trim()) {
                    $('#telephone_noError').text('Enter telephone number.');
                    isValid = false;
                    $('#telephone_no').focus();
                } else {
                    $("#telephone_noError").text(""); // Clear the error message
                }

                const fileInput = $('#document')[0];
                const file = fileInput.files[0];
                let errorMessage = '';

                // Check if a file is selected
                if (file) {
                    const fileName = file.name;
                    const extension = fileName.split('.').pop().toLowerCase();

                    // Check file extension
                    if (extension !== 'pdf') {
                        errorMessage = 'Only PDF files are allowed.';
                        isValid = false;
                    }

                    // Check file size > 5MB
                    if (file.size > 2 * 1024 * 1024) {
                        errorMessage = 'File size must not exceed 2MB.';
                        isValid = false;
                    }

                    // Show error if any
                    if (!isValid) {
                        $('#documentError').text(errorMessage);
                        $('#document').focus();
                    } else {
                        $('#documentError').text('');
                    }
                }

                const selectClubType = document.getElementById('club_type');
                const clubTypeValue = selectClubType.value; // Get the value of the selected option
                if (clubTypeValue != '' && clubTypeValue == 'IHC') {

                    if ($('#appliedFIMIHCY').is(':checked')) {
                        if (!$('#individual_membership_date_and_remark').val().trim()) {
                            $('#individual_membership_date_and_remarkError').text(
                                'Enter individual membership date/details.');
                            isValid = false;
                            $('#individual_membership_date_and_remark').focus();
                        } else {
                            $("#individual_membership_date_and_remarkError").text(
                                ""); // Clear the error message
                        }
                    }



                }



                // Prevent form submission if any validation fails
                if (!isValid) {
                    return;
                }
                // Submit the form if valid
                $('#updateClubMembershipFormBtn').prop('disabled', true);
                $('#updateClubMembershipFormBtn').html('Submitting...');
                $('#clubMembershipForm').submit();
            });

            document.getElementById("telephone_no").oninput = function() {
                let inputValue = this.value;
                let errorMsg = document.getElementById("telephone_noError");

                if (!/^\d*$/.test(inputValue)) {
                    errorMsg.innerText = "Only numeric values are allowed!";
                    this.value = inputValue.replace(/\D/g, ''); // Remove non-numeric characters
                } else {
                    errorMsg.innerText = "";
                }
            };

            document.getElementById("mobile").oninput = function() {
                let inputValue = this.value;
                let errorMsg = document.getElementById("mobileError");

                if (!/^\d*$/.test(inputValue)) {
                    errorMsg.innerText = "Only numeric values are allowed!";
                    this.value = inputValue.replace(/\D/g, ''); // Remove non-numeric characters
                } else {
                    errorMsg.innerText = "";
                }
            };

            document.getElementById("year_of_allotment").oninput = function() {
                let inputValue = this.value;
                let errorMsg = document.getElementById("year_of_allotmentError");

                if (!/^\d*$/.test(inputValue)) {
                    errorMsg.innerText = "Only numeric values are allowed!";
                    this.value = inputValue.replace(/\D/g, ''); // Remove non-numeric characters
                } else {
                    errorMsg.innerText = "";
                }
            };

            document.getElementById("email").oninput = function() {
                let inputValue = this.value;
                let errorMsg = document.getElementById("emailError");
                let regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

                if (!regex.test(inputValue)) {
                    errorMsg.innerText = "Please enter a valid email address!";
                } else {
                    errorMsg.innerText = "";
                }
            };

            document.querySelectorAll('.alphanumeric-input').forEach(input => {
                input.addEventListener('input', function() {
                    // Remove any character that is not a letter (a-z, A-Z) or digit (0-9)
                    this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');
                });
            });

            document.querySelectorAll('.alpha-numeric-comma-dot-slash-input').forEach(input => {
                input.addEventListener('input', function() {
                    // Remove any character that is not a letter (a-z, A-Z) or digit (0-9)
                    this.value = this.value.replace(/[^a-zA-Z0-9\s,/.]/g, '');
                });
            });

            document.querySelectorAll('.alphabets-input').forEach(input => {
                input.addEventListener('input', function() {
                    // Remove any character that is not a letter (a-z, A-Z) or digit (0-9)
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
                });
            });

        });
    </script>
@endsection
