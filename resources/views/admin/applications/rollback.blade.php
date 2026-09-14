@extends('layouts.app')

@section('title', 'Application Rollback')

@section('content')

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Application</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Application Rollback</li>
                </ol>
            </nav>
        </div>
    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            @include('include.alerts.ajax-alert')

            <form method="POST" action="{{ route('admin.rollback.store') }}" id="rollbackForm">
                @csrf

                <div class="row">

                    <!-- Application No -->
                    <div class="col-md-6 mb-3">
                        <label for="application_no" class="form-label">Application No</label>
                        <input
                            type="text"
                            class="form-control"
                            id="application_no"
                            name="application_no"
                            placeholder="Enter Application Number"
                            required
                        >
                        <small id="app_fetch_msg" class="text-muted d-block mt-1"></small>
                    </div>

                    <!-- Application Type (readonly) -->
                    <div class="col-md-6 mb-3">
                        <label for="application_type" class="form-label">Application Type</label>
                        <input
                            type="text"
                            class="form-control"
                            id="application_type"
                            name="application_type"
                            value=""
                            readonly
                        >
                    </div>

                    <!-- Current Application Status (readonly) -->
                    <div class="col-md-6 mb-3">
                        <label for="current_status" class="form-label">Current Application Status</label>
                        <input
                            type="text"
                            class="form-control"
                            id="current_status"
                            name="current_status"
                            value=""
                            readonly
                        >
                    </div>

                    <!-- Reset To Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label for="reset_to" class="form-label">Reset To</label>
                        <select class="form-select" id="reset_to" name="reset_to" required disabled>
                            <option value="">-- Select Action --</option>
                        </select>
                    </div>

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="submit_btn" disabled>
                        Submit
                    </button>
                </div>

            </form>
        </div>
    </div>

<script>
(function () {
    const applicationNoEl = document.getElementById('application_no');
    const typeEl = document.getElementById('application_type');
    const statusEl = document.getElementById('current_status');
    const resetToEl = document.getElementById('reset_to');
    const msgEl = document.getElementById('app_fetch_msg');
    const submitBtn = document.getElementById('submit_btn');
    const formEl = document.getElementById('rollbackForm');

    const fetchUrl = "{{ route('admin.rollback-applications.fetch') }}";
    const storeUrl = "{{ route('admin.rollback.store') }}";

    // Endpoint that returns whether APP_OBJ exists in application_movements for the application_no
    const hasAppObjUrl = "{{ route('admin.rollback-applications.has-app-obj') }}";

    let t = null;

    async function hasAppObjMovement(appNo) {
        const res = await fetch(`${hasAppObjUrl}?application_no=${encodeURIComponent(appNo)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await res.json();
        if (!res.ok) return false;

        return !!data.has_app_obj;
    }

    function buildOptions(options) {
        resetToEl.innerHTML = '';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '-- Select Action --';
        resetToEl.appendChild(placeholder);

        options.forEach(opt => {
            const o = document.createElement('option');
            o.value = opt.value;
            o.textContent = opt.text;
            resetToEl.appendChild(o);
        });

        resetToEl.disabled = false;
        return true;
    }

    function resetReadonlyFields() {
        typeEl.value = '';
        statusEl.value = '';
        submitBtn.disabled = true;

        resetToEl.innerHTML = '<option value="">-- Select Action --</option>';
        resetToEl.disabled = true;
    }

    async function setResetOptions(appNo, serviceType, status) {
        const st = (serviceType || '').trim().toLowerCase();
        const cs = (status || '').trim().toLowerCase();

        // Only on Approved we show rollback actions (as per your current flows)
        if (cs !== 'approved') {
            resetToEl.disabled = true;
            return false;
        }

        // Only for NOC / Mutation we apply APP_OBJ logic
        if (st !== 'noc' && st !== 'mutation') {
            resetToEl.disabled = true;
            return false;
        }

        const appObjExists = await hasAppObjMovement(appNo);

        // ✅ NOC rules
        if (st === 'noc') {
            if (appObjExists) {
                // APP_OBJ present
                return buildOptions([
                    { value: 'INITIAL_IN_PROGRESS', text: 'Initial In-Progress' },
                    { value: 'RESUBMITTED_BY_APPLICANT', text: 'Resubmitted By Applicant' },
                ]);
            }

            // APP_OBJ not present
            return buildOptions([
                { value: 'INITIAL_IN_PROGRESS', text: 'Initial In-Progress' },
                { value: 'RECOMMEND_BY_SO', text: 'Recommend By SO' },
            ]);
        }

        // ✅ Mutation rules
        if (st === 'mutation') {
            if (appObjExists) {
                // APP_OBJ present
                return buildOptions([
                    { value: 'INITIAL_IN_PROGRESS', text: 'Initial In-Progress' },
                    { value: 'RECOMMEND_BY_CDV', text: 'Recommend By CDV' },
                    { value: 'RESUBMITTED_BY_APPLICANT', text: 'Resubmitted By Applicant' },
                ]);
            }

            // APP_OBJ not present
            return buildOptions([
                { value: 'INITIAL_IN_PROGRESS', text: 'Initial In-Progress' },
                { value: 'RECOMMEND_BY_SO', text: 'Recommend By SO' },
                { value: 'RECOMMEND_BY_CDV', text: 'Recommend By CDV' },
            ]);
        }

        resetToEl.disabled = true;
        return false;
    }

    async function fetchAppInfo(appNo) {
        msgEl.className = "text-muted d-block mt-1";
        msgEl.textContent = "Fetching application details...";
        resetReadonlyFields();

        try {
            const res = await fetch(`${fetchUrl}?application_no=${encodeURIComponent(appNo)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok) {
                msgEl.className = "text-danger d-block mt-1";
                msgEl.textContent = data?.message || "Unable to fetch details.";
                return;
            }

            typeEl.value = data.service_type ?? '';
            statusEl.value = data.status ?? '';

            const hasActions = await setResetOptions(appNo, data.service_type, data.status);

            if (!hasActions) {
                msgEl.className = "text-warning d-block mt-1";
                msgEl.textContent = "No rollback actions available for this Service Type / Status.";
                submitBtn.disabled = true;
                return;
            }

            msgEl.className = "text-success d-block mt-1";
            msgEl.textContent = "Application details loaded. Select Reset To action.";
            submitBtn.disabled = true;

        } catch (e) {
            msgEl.className = "text-danger d-block mt-1";
            msgEl.textContent = "Network error while fetching details.";
        }
    }

    resetToEl.addEventListener('change', function () {
        submitBtn.disabled = (this.value === '');
    });

    applicationNoEl.addEventListener('input', function () {
        const appNo = this.value.trim();

        msgEl.textContent = "";
        resetReadonlyFields();

        if (appNo.length < 3) return;

        clearTimeout(t);
        t = setTimeout(() => fetchAppInfo(appNo), 400);
    });

    if (formEl) {
        formEl.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (submitBtn.disabled) return;

            submitBtn.disabled = true;
            const formData = new FormData(formEl);

            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data?.errors) {
                        showError(Object.values(data.errors).flat());
                    } else {
                        showError(data?.message || 'Rollback failed.');
                    }
                    submitBtn.disabled = false;
                    return;
                }

                showSuccess(data?.message || 'Rollback completed successfully.');

                formEl.reset();
                resetReadonlyFields();
                msgEl.className = "text-muted d-block mt-1";
                msgEl.textContent = "";

            } catch (err) {
                showError('Network error. Please try again.');
                submitBtn.disabled = false;
            }
        });
    }
})();
</script>


@endsection
