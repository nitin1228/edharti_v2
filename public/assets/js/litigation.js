document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('litigationSubmit');

    window.addEventListener('pageshow', function () {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerText = submitBtn.dataset.originalText || submitBtn.innerText;
        }
    });

    if (submitBtn) {
        submitBtn.dataset.originalText = submitBtn.innerText;
    }

    if (!form || !submitBtn) return;

    const litigationConfig = window.litigationConfig || {};
    const isEdit = litigationConfig.isEdit || false;
    const savedPropertyAddress = (litigationConfig.savedPropertyAddress || '').trim();
    const savedSection = String(litigationConfig.savedSection || '').trim();
    const routes = litigationConfig.routes || {};
    const csrfToken = litigationConfig.csrfToken || '';

    const partyTypes = litigationConfig.partyTypes || [];
    const partySubtypes = litigationConfig.partySubtypes || [];
    const designationTypes = litigationConfig.designationTypes || [];

    let partyIndex = document.querySelectorAll('#partyRepeater .party-item').length || 1;
    let counselIndex = document.querySelectorAll('#counselRepeater .counsel-item').length || 1;
    let hearingIndex = document.querySelectorAll('#hearingRepeater .hearing-item').length || 1;

    const propertyIdRegex = /^[a-zA-Z0-9]{1,5}$/;
    const propertyAddressRegex = /^[a-zA-Z0-9\s,#.\-()/]*$/;
    const caseNumberRegex = /^[A-Za-z0-9\s.\-\/\\]+$/;
    const associatedCaseRegex = /^[A-Za-z0-9\s.\-\/\\]+$/;
    const alphaRegex = /^[A-Za-z\s.]+$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const addressRegex = /^[a-zA-Z0-9\s,#.\-()/]*$/;
    const courtLocationRegex = /^[a-zA-Z0-9\s.\-\/]+$/;

    function getField(id) {
        return document.getElementById(id);
    }

    function getValue(id) {
        const field = getField(id);
        return field ? field.value.trim() : '';
    }

    function setValue(id, value) {
        const field = getField(id);
        if (field) field.value = value || '';
    }

    function addErrorClass(field) {
        if (!field) return;

        if (field.tagName === 'SELECT') {
            field.classList.add('select-error');
        } else {
            field.classList.add('input-error');
        }
    }

    function removeErrorClass(field) {
        if (!field) return;
        field.classList.remove('input-error', 'select-error');
    }

    function scrollToField(field) {
        if (!field) return;

        field.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        field.focus();
    }

    function showError(id, message) {
        const field = getField(id);
        const errorDiv = getField(id + '_error');

        addErrorClass(field);

        if (errorDiv) {
            errorDiv.innerText = message;
        }
    }

    function clearError(id) {
        const field = getField(id);
        const errorDiv = getField(id + '_error');

        removeErrorClass(field);

        if (errorDiv) {
            errorDiv.innerText = '';
        }
    }

    function makeOptions(items, placeholder) {
        let options = `<option value="">${placeholder}</option>`;

        items.forEach(function (item) {
            options += `<option value="${escapeHtml(item)}">${escapeHtml(item)}</option>`;
        });

        return options;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getRepeaterField(item, fieldName) {
        return item.querySelector(`[name*="[${fieldName}]"]`);
    }

    function getRepeaterValue(field) {
        return field ? field.value.trim() : '';
    }

    function getRepeaterErrorDiv(field, errorClass) {
        if (!field) return null;

        let errorDiv = field.parentElement.querySelector('.' + errorClass);

        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger text-left ' + errorClass;
            field.parentElement.appendChild(errorDiv);
        }

        return errorDiv;
    }

    function showRepeaterError(field, message, errorClass) {
        addErrorClass(field);

        const errorDiv = getRepeaterErrorDiv(field, errorClass);

        if (errorDiv) {
            errorDiv.innerText = message;
        }
    }

    function clearRepeaterError(field, errorClass) {
        removeErrorClass(field);

        if (!field) return;

        const errorDiv = field.parentElement.querySelector('.' + errorClass);

        if (errorDiv) {
            errorDiv.innerText = '';
        }
    }

    function getTodayDate() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');

        return `${yyyy}-${mm}-${dd}`;
    }

    function getTomorrowDate() {
        const today = new Date();
        today.setDate(today.getDate() + 1);

        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');

        return `${yyyy}-${mm}-${dd}`;
    }

    const propertySearchField = getField('search_property_id');
    const litigationSearchBtn = getField('litigationSearchBtn');

    function clearPropertyDetails() {
        const section = getField('propertyBasicDetailsSection');

        if (section) {
            section.style.display = 'none';
        }

        [
            'basic_colony_name',
            'basic_block',
            'basic_plot',
            'basic_land_type',
            'basic_property_type',
            'basic_property_subtype',
            'basic_file_no',
            'basic_known_as'
        ].forEach(function (id) {
            setValue(id, '');
        });
    }

    function setSearchError(message) {
        const errorBox = getField('litigationSearchError');

        if (errorBox) {
            errorBox.innerHTML = message || '';
        }
    }

    function autoSelectSection(sectionName) {
        const sectionDropdown = getField('section');

        if (!sectionDropdown || !sectionName) return;

        Array.from(sectionDropdown.options).forEach(function (option) {
            if (
                option.text.trim().toLowerCase() ===
                sectionName.trim().toLowerCase()
            ) {
                sectionDropdown.value = option.value;
            }
        });

        validateSectionDetails();
    }

    function searchPropertyById() {
        if (!propertySearchField) return;

        const propertyId = propertySearchField.value.trim();

        setSearchError('');
        clearError('search_property_id');
        clearPropertyDetails();

        if (propertyId === '') {
            showError('search_property_id', 'Please enter Property ID.');
            return;
        }

        if (propertyId.length !== 5) {
            showError('search_property_id', 'Property ID must be exactly 5 characters.');
            return;
        }

        if (!propertyIdRegex.test(propertyId)) {
            showError('search_property_id', 'Property ID should be alphanumeric only.');
            return;
        }

        if (!routes.propertyBasicDetails) {
            setSearchError('Property search route is not configured.');
            return;
        }

        fetch(routes.propertyBasicDetails, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                property_id: propertyId,
                skipAccessCheck: 0,
                withOutLeaseHold: 1
            })
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (response) {
                if (response.status !== 'success') {
                    setSearchError(response.message || 'Property not found.');
                    return;
                }

                const details = response.data || {};
                const meta = response.meta || {};

                const propertyBasicDetailsSection = getField('propertyBasicDetailsSection');

                if (propertyBasicDetailsSection) {
                    propertyBasicDetailsSection.style.display = 'block';
                }

                setValue('basic_colony_name', details.colony || '');
                setValue('basic_block', details.block_no || '');
                setValue('basic_plot', details.plot_or_property_no || '');
                setValue('basic_land_type', details.landTypeName || '');
                setValue('basic_property_type', details.proprtyTypeName || '');
                setValue('basic_property_subtype', details.proprtySubtypeName || '');
                setValue('basic_file_no', details.file_no || '');
                setValue('basic_known_as', details.address || '');

                setValue('property_id', propertyId);

                if (!isEdit) {
                    setValue('property_known_as', details.address || '');
                    autoSelectSection(meta.master_section_name || '');
                } else {
                    if (savedPropertyAddress === '') {
                        setValue('property_known_as', details.address || '');
                    }

                    if (savedSection !== '') {
                        setValue('section', savedSection);
                    }
                }

                validateCaseDetails();
            })
            .catch(function (error) {
                console.error(error);
                setSearchError('Something went wrong while fetching property details.');
            });
    }

    if (litigationSearchBtn) {
        litigationSearchBtn.addEventListener('click', searchPropertyById);
    }

    if (propertySearchField) {
        propertySearchField.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPropertyById();
            }
        });
    }

    if (
        propertySearchField &&
        propertySearchField.value.trim() !== '' &&
        propertySearchField.value.trim().length === 5
    ) {
        searchPropertyById();
    }

    function addPartyRepeater() {
        const partyRepeater = getField('partyRepeater');
        if (!partyRepeater) return;

        const html = `
            <div class="party-item border rounded p-3 mb-3">
                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Party Name <span class="text-danger">*</span></label>
                        <input type="text" name="parties[${partyIndex}][party_name]" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Party Type <span class="text-danger">*</span></label>
                        <select name="parties[${partyIndex}][party_type]" class="form-select">
                            ${makeOptions(partyTypes, 'Select Party Type')}
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Party Subtype <span class="text-danger">*</span></label>
                        <select name="parties[${partyIndex}][party_subtype]" class="form-select">
                            ${makeOptions(partySubtypes, 'Select Party Subtype')}
                        </select>
                    </div>
                </div>
            </div>
        `;

        partyRepeater.insertAdjacentHTML('beforeend', html);
        partyIndex++;
    }

    function addCounselRepeater() {
        const counselRepeater = getField('counselRepeater');
        if (!counselRepeater) return;

        const html = `
            <div class="counsel-item border rounded p-3 mb-3">
                <input type="hidden" name="counsels[${counselIndex}][is_existing]" value="0">

                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Counsel Name <span class="text-danger">*</span></label>
                        <input type="text" name="counsels[${counselIndex}][counsel_name]" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <select name="counsels[${counselIndex}][designation]" class="form-select">
                            ${makeOptions(designationTypes, 'Select Designation')}
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="counsels[${counselIndex}][mobile]" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="counsels[${counselIndex}][email]" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Appointment Date</label>
                        <input type="date" name="counsels[${counselIndex}][appointment_date]" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="counsels[${counselIndex}][address]" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        `;

        counselRepeater.insertAdjacentHTML('beforeend', html);
        counselIndex++;
        setCounselDateMin();
    }

    function addHearingRepeater() {
        const hearingRepeater = getField('hearingRepeater');
        if (!hearingRepeater) return;

        const html = `
            <div class="hearing-item border rounded p-3 mb-3">
                <input type="hidden" name="hearings[${hearingIndex}][is_existing]" value="0">

                <div class="d-flex justify-content-end mb-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Last Date Of Hearing</label>
                        <input type="date" name="hearings[${hearingIndex}][ldoh]" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Next Date Of Hearing</label>
                        <input type="date" name="hearings[${hearingIndex}][ndoh]" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Last Hearing Details</label>
                        <textarea name="hearings[${hearingIndex}][ldoh_hearing_details]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
        `;

        hearingRepeater.insertAdjacentHTML('beforeend', html);
        hearingIndex++;
        setHearingDateLimits();
    }

    const addPartyBtn = getField('addPartyBtn');
    const addCounselBtn = getField('addCounselBtn');
    const addHearingBtn = getField('addHearingBtn');

    if (addPartyBtn) {
        addPartyBtn.addEventListener('click', addPartyRepeater);
    }

    if (addCounselBtn) {
        addCounselBtn.addEventListener('click', addCounselRepeater);
    }

    if (addHearingBtn) {
        addHearingBtn.addEventListener('click', addHearingRepeater);
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            const item = e.target.closest('.party-item, .counsel-item, .hearing-item');

            if (item) {
                item.remove();
                validateFullForm(false);
            }
        }
    });

    const caseDetailFields = [
        'property_id',
        'property_known_as',
        'case_number',
        'associated_case_number',
        'case_type',
        'judicial_authority',
        'court_location',
        'case_brief'
    ];

    const sectionDetailFields = [
        'section',
        'section_counsel',
        'section_remarks'
    ];

    function validateCaseDetails() {
        let isValid = true;
        let firstErrorField = null;

        caseDetailFields.forEach(clearError);

        function setInvalid(id, message) {
            showError(id, message);

            if (!firstErrorField) {
                firstErrorField = getField(id);
            }

            isValid = false;
        }

        const propertyId = getValue('property_id');
        const propertyAddress = getValue('property_known_as');
        const caseNumber = getValue('case_number');
        const associatedCaseNumber = getValue('associated_case_number');
        const caseType = getValue('case_type');
        const judicialAuthority = getValue('judicial_authority');
        const courtLocation = getValue('court_location');
        const caseBrief = getValue('case_brief');

        if (propertyId === '' && propertyAddress === '') {
            setInvalid('property_id', 'Property ID or Property Address is required.');
            setInvalid('property_known_as', 'Property Address or Property ID is required.');
        }

        if (propertyId !== '' && propertyId.length > 5) {
            setInvalid('property_id', 'Property ID must be maximum 5 characters.');
        } else if (propertyId !== '' && !propertyIdRegex.test(propertyId)) {
            setInvalid('property_id', 'Property ID should be alphanumeric only.');
        }

        if (propertyAddress !== '' && !propertyAddressRegex.test(propertyAddress)) {
            setInvalid('property_known_as', 'Only letters, numbers, space, comma, #, dot, hyphen, brackets and slash are allowed.');
        }

        if (caseNumber === '') {
            setInvalid('case_number', 'Case Number is required.');
        } else if (!caseNumberRegex.test(caseNumber)) {
            setInvalid('case_number', 'Only letters, numbers, space, dot, slash, backslash and hyphen are allowed.');
        }

        if (associatedCaseNumber !== '' && !associatedCaseRegex.test(associatedCaseNumber)) {
            setInvalid(
                'associated_case_number',
                'Only letters, numbers, space, dot (.), slash (/), backslash (\\) and hyphen (-) are allowed.'
            );
        }

        if (caseType === '') {
            setInvalid('case_type', 'Please select Case Type.');
        }

        if (judicialAuthority === '') {
            setInvalid('judicial_authority', 'Please select Judicial Authority.');
        }

        if (courtLocation === '') {
            setInvalid('court_location', 'Court Location is required.');
        } else if (!courtLocationRegex.test(courtLocation)) {
            setInvalid('court_location', 'Only letters, numbers, space, dot (.), slash (/), and hyphen (-) are allowed.');
        }

        if (caseBrief === '') {
            setInvalid('case_brief', 'Case Brief is required.');
        } else if (caseBrief.length < 50) {
            setInvalid('case_brief', 'Case Brief must be at least 50 characters.');
        } else if (caseBrief.length > 500) {
            setInvalid('case_brief', 'Case Brief must not exceed 500 characters.');
        }

        return {
            isValid: isValid,
            firstErrorField: firstErrorField
        };
    }

    // function validatePartyDetails() {
    //     let isValid = true;
    //     let firstErrorField = null;

    //     document.querySelectorAll('#partyRepeater .party-item').forEach(function (item, index) {
    //         const partyName = getRepeaterField(item, 'party_name');
    //         const partyType = getRepeaterField(item, 'party_type');
    //         const partySubtype = getRepeaterField(item, 'party_subtype');

    //         clearRepeaterError(partyName, 'party-error');
    //         clearRepeaterError(partyType, 'party-error');
    //         clearRepeaterError(partySubtype, 'party-error');

    //         const partyNameVal = getRepeaterValue(partyName);
    //         const partyTypeVal = getRepeaterValue(partyType);
    //         const partySubtypeVal = getRepeaterValue(partySubtype);

    //         const hasAnyValue =
    //             partyNameVal !== '' ||
    //             partyTypeVal !== '' ||
    //             partySubtypeVal !== '';

    //         const isFirstParty = index === 0;

    //         if (isFirstParty || hasAnyValue) {
    //             if (partyNameVal === '') {
    //                 showRepeaterError(partyName, 'Party Name is required.', 'party-error');
    //                 isValid = false;
    //                 if (!firstErrorField) firstErrorField = partyName;
    //             }

    //             if (partyTypeVal === '') {
    //                 showRepeaterError(partyType, 'Please select Party Type.', 'party-error');
    //                 isValid = false;
    //                 if (!firstErrorField) firstErrorField = partyType;
    //             }

    //             if (partySubtypeVal === '') {
    //                 showRepeaterError(partySubtype, 'Please select Party Subtype.', 'party-error');
    //                 isValid = false;
    //                 if (!firstErrorField) firstErrorField = partySubtype;
    //             }
    //         }
    //     });

    //     return {
    //         isValid: isValid,
    //         firstErrorField: firstErrorField
    //     };
    // } commented by anil and added new js fucntion for this on 19-05-2026

    function validatePartyDetails() {
        let isValid = true;
        let firstErrorField = null;

        document.querySelectorAll('#partyRepeater .party-item').forEach(function (item) {
            const partyName = getRepeaterField(item, 'party_name');
            const partyType = getRepeaterField(item, 'party_type');
            const partySubtype = getRepeaterField(item, 'party_subtype');

            clearRepeaterError(partyName, 'party-error');
            clearRepeaterError(partyType, 'party-error');
            clearRepeaterError(partySubtype, 'party-error');

            if (getRepeaterValue(partyName) === '') {
                showRepeaterError(partyName, 'Party Name is required.', 'party-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = partyName;
            }

            if (getRepeaterValue(partyType) === '') {
                showRepeaterError(partyType, 'Please select Party Type.', 'party-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = partyType;
            }

            if (getRepeaterValue(partySubtype) === '') {
                showRepeaterError(partySubtype, 'Please select Party Subtype.', 'party-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = partySubtype;
            }
        });

        return {
            isValid: isValid,
            firstErrorField: firstErrorField
        };
    }

    function setCounselDateMin() {
        const tomorrow = getTomorrowDate();

        document.querySelectorAll('#counselRepeater input[name*="[appointment_date]"]').forEach(function (field) {
            const item = field.closest('.counsel-item');
            const isExisting = item ? getRepeaterField(item, 'is_existing') : null;
            const isExistingVal = getRepeaterValue(isExisting);

            if (isExistingVal !== '1') {
                field.setAttribute('min', tomorrow);
            } else {
                field.removeAttribute('min');
            }

            field.setAttribute('max', '9999-12-31');
        });
    }

    function validateCounselDetails() {
        let isValid = true;
        let firstErrorField = null;
        const tomorrow = getTomorrowDate();

        document.querySelectorAll('#counselRepeater .counsel-item').forEach(function (item, index) {
            const counselName = getRepeaterField(item, 'counsel_name');
            const designation = getRepeaterField(item, 'designation');
            const mobile = getRepeaterField(item, 'mobile');
            const email = getRepeaterField(item, 'email');
            const appointmentDate = getRepeaterField(item, 'appointment_date');
            const address = getRepeaterField(item, 'address');
            const isExisting = getRepeaterField(item, 'is_existing');

            clearRepeaterError(counselName, 'counsel-error');
            clearRepeaterError(designation, 'counsel-error');
            clearRepeaterError(mobile, 'counsel-error');
            clearRepeaterError(email, 'counsel-error');
            clearRepeaterError(appointmentDate, 'counsel-error');
            clearRepeaterError(address, 'counsel-error');

            const counselNameVal = getRepeaterValue(counselName);
            const designationVal = getRepeaterValue(designation);
            const mobileVal = getRepeaterValue(mobile);
            const emailVal = getRepeaterValue(email);
            const appointmentDateVal = getRepeaterValue(appointmentDate);
            const addressVal = getRepeaterValue(address);
            const isExistingVal = getRepeaterValue(isExisting);

            const hasAnyValue =
                counselNameVal !== '' ||
                designationVal !== '' ||
                mobileVal !== '' ||
                emailVal !== '' ||
                appointmentDateVal !== '' ||
                addressVal !== '';

            const isFirstCounsel = index === 0;

            // if (isFirstCounsel || hasAnyValue) {
            //     if (counselNameVal === '') {
            //         showRepeaterError(counselName, 'Counsel Name is required.', 'counsel-error');
            //         isValid = false;
            //         if (!firstErrorField) firstErrorField = counselName;
            //     } else if (!alphaRegex.test(counselNameVal)) {
            //         showRepeaterError(counselName, 'Only alphabets, space and dot are allowed.', 'counsel-error');
            //         isValid = false;
            //         if (!firstErrorField) firstErrorField = counselName;
            //     }

            //     if (designationVal === '') {
            //         showRepeaterError(designation, 'Please select Designation.', 'counsel-error');
            //         isValid = false;
            //         if (!firstErrorField) firstErrorField = designation;
            //     }
            // } commented by anil and added new js fucntion for this on 19-05-2026

            if (counselNameVal === '') {
                showRepeaterError(counselName, 'Counsel Name is required.', 'counsel-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = counselName;
            } else if (!alphaRegex.test(counselNameVal)) {
                showRepeaterError(counselName, 'Only alphabets, space and dot are allowed.', 'counsel-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = counselName;
            }

            if (designationVal === '') {
                showRepeaterError(designation, 'Please select Designation.', 'counsel-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = designation;
            }

            if (mobileVal !== '') {
                if (!/^[0-9]+$/.test(mobileVal)) {
                    showRepeaterError(mobile, 'Only numeric digits are allowed.', 'counsel-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = mobile;
                } else if (mobileVal.length !== 10) {
                    showRepeaterError(mobile, 'Mobile number must be exactly 10 digits.', 'counsel-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = mobile;
                }
            }

            if (emailVal !== '' && !emailRegex.test(emailVal)) {
                showRepeaterError(email, 'Please enter a valid email address.', 'counsel-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = email;
            }

            if (appointmentDateVal !== '') {
                const year = appointmentDateVal.split('-')[0];

                if (year.length !== 4) {
                    showRepeaterError(appointmentDate, 'Year must be 4 digits.', 'counsel-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = appointmentDate;
                } else if (isExistingVal !== '1' && appointmentDateVal < tomorrow) {
                    showRepeaterError(appointmentDate, 'Appointment Date must be from tomorrow onwards.', 'counsel-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = appointmentDate;
                }
            }

            if (addressVal !== '' && !addressRegex.test(addressVal)) {
                showRepeaterError(address, 'Only letters, numbers, space, comma, #, dot, hyphen, brackets and slash are allowed.', 'counsel-error');
                isValid = false;
                if (!firstErrorField) firstErrorField = address;
            }
        });

        return {
            isValid: isValid,
            firstErrorField: firstErrorField
        };
    }

    function setHearingDateLimits() {
        const today = getTodayDate();
        const tomorrow = getTomorrowDate();

        document.querySelectorAll('#hearingRepeater .hearing-item').forEach(function (item) {
            const isExisting = getRepeaterField(item, 'is_existing');
            const isExistingVal = getRepeaterValue(isExisting);

            const ldoh = getRepeaterField(item, 'ldoh');
            const ndoh = getRepeaterField(item, 'ndoh');

            if (ldoh) {
                if (isExistingVal !== '1') {
                    ldoh.setAttribute('max', today);
                } else {
                    ldoh.removeAttribute('max');
                }
            }

            if (ndoh) {
                if (isExistingVal !== '1') {
                    ndoh.setAttribute('min', tomorrow);
                } else {
                    ndoh.removeAttribute('min');
                }

                ndoh.setAttribute('max', '9999-12-31');
            }
        });
    }

    function validateHearingDetails() {
        let isValid = true;
        let firstErrorField = null;

        const today = getTodayDate();
        const tomorrow = getTomorrowDate();

        document.querySelectorAll('#hearingRepeater .hearing-item').forEach(function (item) {
            const lastDate = getRepeaterField(item, 'ldoh');
            const nextDate = getRepeaterField(item, 'ndoh');
            const hearingDetails = getRepeaterField(item, 'ldoh_hearing_details');
            const isExisting = getRepeaterField(item, 'is_existing');

            clearRepeaterError(lastDate, 'hearing-error');
            clearRepeaterError(nextDate, 'hearing-error');
            clearRepeaterError(hearingDetails, 'hearing-error');

            const lastDateVal = getRepeaterValue(lastDate);
            const nextDateVal = getRepeaterValue(nextDate);
            const hearingDetailsVal = getRepeaterValue(hearingDetails);
            const isExistingVal = getRepeaterValue(isExisting);

            if (lastDateVal !== '') {
                const year = lastDateVal.split('-')[0];

                if (year.length !== 4) {
                    showRepeaterError(lastDate, 'Year must be 4 digits.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = lastDate;
                } else if (isExistingVal !== '1' && lastDateVal > today) {
                    showRepeaterError(lastDate, 'Last Date Of Hearing cannot be a future date.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = lastDate;
                }

                if (hearingDetailsVal === '') {
                    showRepeaterError(hearingDetails, 'Last Hearing Details is required when Last Date Of Hearing is entered.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hearingDetails;
                }
            }

            if (hearingDetailsVal !== '') {
                if (lastDateVal === '') {
                    showRepeaterError(lastDate, 'Last Date Of Hearing is required when Last Hearing Details is entered.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = lastDate;
                }

                if (hearingDetailsVal.length < 50) {
                    showRepeaterError(hearingDetails, 'Last Hearing Details must be at least 50 characters.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hearingDetails;
                } else if (hearingDetailsVal.length > 500) {
                    showRepeaterError(hearingDetails, 'Last Hearing Details must not exceed 500 characters.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = hearingDetails;
                }
            }

            if (nextDateVal !== '') {
                const year = nextDateVal.split('-')[0];

                if (year.length !== 4) {
                    showRepeaterError(nextDate, 'Year must be 4 digits.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = nextDate;
                } else if (isExistingVal !== '1' && nextDateVal < tomorrow) {
                    showRepeaterError(nextDate, 'Next Date Of Hearing must be from tomorrow onwards.', 'hearing-error');
                    isValid = false;
                    if (!firstErrorField) firstErrorField = nextDate;
                }
            }
        });

        return {
            isValid: isValid,
            firstErrorField: firstErrorField
        };
    }

    function validateSectionDetails() {
        let isValid = true;
        let firstErrorField = null;

        sectionDetailFields.forEach(clearError);

        function setInvalid(id, message) {
            showError(id, message);

            if (!firstErrorField) {
                firstErrorField = getField(id);
            }

            isValid = false;
        }

        const section = getValue('section');
        const sectionCounsel = getValue('section_counsel');
        const sectionRemarks = getValue('section_remarks');

        if (section === '') {
            setInvalid('section', 'Please select Section.');
        }

        if (sectionCounsel === '') {
            setInvalid('section_counsel', 'Section Counsel is required.');
        } else if (!alphaRegex.test(sectionCounsel)) {
            setInvalid('section_counsel', 'Only alphabets, space and dot are allowed.');
        }

        if (sectionRemarks === '') {
            setInvalid('section_remarks', 'Remarks are required.');
        } else if (sectionRemarks.length < 50) {
            setInvalid('section_remarks', 'Remarks must be at least 50 characters.');
        } else if (sectionRemarks.length > 500) {
            setInvalid('section_remarks', 'Remarks must not exceed 500 characters.');
        }

        return {
            isValid: isValid,
            firstErrorField: firstErrorField
        };
    }

    function validateFullForm(scroll = false) {
        const caseValidation = validateCaseDetails();
        const partyValidation = validatePartyDetails();
        const counselValidation = validateCounselDetails();
        const hearingValidation = validateHearingDetails();
        const sectionValidation = validateSectionDetails();

        if (!caseValidation.isValid) {
            if (scroll) scrollToField(caseValidation.firstErrorField);
            return false;
        }

        if (!partyValidation.isValid) {
            if (scroll) scrollToField(partyValidation.firstErrorField);
            return false;
        }

        if (!counselValidation.isValid) {
            if (scroll) scrollToField(counselValidation.firstErrorField);
            return false;
        }

        if (!hearingValidation.isValid) {
            if (scroll) scrollToField(hearingValidation.firstErrorField);
            return false;
        }

        if (!sectionValidation.isValid) {
            if (scroll) scrollToField(sectionValidation.firstErrorField);
            return false;
        }

        return true;
    }

    setCounselDateMin();
    setHearingDateLimits();

    let isSubmitting = false;

    function lockSubmitButton() {
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';
    }

    submitBtn.addEventListener('click', function (e) {
        e.preventDefault();

        if (isSubmitting) {
            return false;
        }

        if (!validateFullForm(true)) {
            return false;
        }

        lockSubmitButton();

        document.getElementById('realLitigationSubmit').click();
    });

    form.addEventListener('submit', function (e) {
        if (isSubmitting) {
            return true;
        }

        if (!validateFullForm(true)) {
            e.preventDefault();
            return false;
        }

        lockSubmitButton();
    });

    caseDetailFields.forEach(function (id) {
        const field = getField(id);

        if (!field) return;

        field.addEventListener('input', validateCaseDetails);
        field.addEventListener('change', validateCaseDetails);
        field.addEventListener('blur', validateCaseDetails);
    });

    sectionDetailFields.forEach(function (id) {
        const field = getField(id);

        if (!field) return;

        field.addEventListener('input', validateSectionDetails);
        field.addEventListener('change', validateSectionDetails);
        field.addEventListener('blur', validateSectionDetails);
    });

    document.addEventListener('input', function (e) {
        if (e.target.matches('#counselRepeater input[name*="[mobile]"]')) {
            let value = e.target.value.replace(/[^0-9]/g, '');

            if (value.length > 10) {
                value = value.slice(0, 10);
            }

            e.target.value = value;
        }

        if (e.target.closest('#partyRepeater')) {
            validatePartyDetails();
        }

        if (e.target.closest('#counselRepeater')) {
            validateCounselDetails();
        }

        if (e.target.closest('#hearingRepeater')) {
            validateHearingDetails();
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.closest('#partyRepeater')) {
            validatePartyDetails();
        }

        if (e.target.closest('#counselRepeater')) {
            setCounselDateMin();
            validateCounselDetails();
        }

        if (e.target.closest('#hearingRepeater')) {
            setHearingDateLimits();
            validateHearingDetails();
        }
    });

    document.addEventListener('blur', function (e) {
        if (e.target.closest('#partyRepeater')) {
            validatePartyDetails();
        }

        if (e.target.closest('#counselRepeater')) {
            validateCounselDetails();
        }

        if (e.target.closest('#hearingRepeater')) {
            validateHearingDetails();
        }
    }, true);
});