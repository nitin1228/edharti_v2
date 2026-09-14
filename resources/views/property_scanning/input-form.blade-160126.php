@extends('layouts.app')
@section('title', 'Property Scanning')
<link rel="stylesheet" href="{{ asset('assets/css/rgr.css') }}" />

@php
    $isViewOnly = $isViewOnly ?? false;
@endphp

@section('content')
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Property Scanning</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Upload Scanned Files</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @unless($isViewOnly)
        <!-- <form action="{{ route('property.scanning.store') }}" method="POST" enctype="multipart/form-data"> -->
        <form id="scanningForm" action="{{ route('property.scanning.store') }}" method="POST" enctype="multipart/form-data" novalidate>

            @csrf
        @endunless

        @can('view.scanning.list')
            <div class="d-flex justify-content-end py-3">
                <a href="{{ route('scanning.index') }}">
                    <button type="button" class="btn btn-primary py-2">Scanned Property Files</button>
                </a>
            </div>
        @endcan

        <div class="row g-3 align-items-end">

            @include('include.parts.property-selector', [

                'isApplicant' => false,
                'colonies' => $colonies

            ])

            <div class="col-12 col-lg-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="submitButton1">Search</button>
            </div>
            <div class="col-12">
                <div id="propertIdSearchError" class="text-danger mt-2"></div>
            </div>
        </div>

        <div class="row mt-4" id="propertyDetailsSection" style="display: none;">
            <div class="col-12 col-lg-3">
                <label class="form-label">File Number</label>
                <input type="text" class="form-control" id="FileNumberNew" readonly>
            </div>
            <div class="col-12 col-lg-3" id="FlatNumberField" style="display: none;">
                <label class="form-label">Flat Number</label>
                <input type="text" class="form-control" name="flat_number" id="FlatNumber" readonly>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Plot</label>
                <input type="text" class="form-control" name="plot_number" id="PlotNumber" readonly>
            </div>
            <div class="col-12 col-lg-3">
                <label class="form-label">Block</label>
                <input type="text" class="form-control" name="block" id="BlockNumber" readonly>
            </div>
            <div class="col-12 col-lg-3 mt-4" id="ColonyField">
                <label class="form-label">Colony Name (Present)</label>
                <input type="text" class="form-control" name="present_colony_name" id="ColonyNameNew" readonly>
            </div>
            <div class="col-12 col-lg-3 mt-4">
                <label class="form-label">Presently Known As</label>
                <input type="text" class="form-control" name="presently_known_as" id="PresentlyKnownAs" readonly>
            </div>
            <div class="col-12 col-lg-3 mt-4">
                <label class="form-label">Property Status</label>
                <input type="text" class="form-control" name="property_status" id="PropertyStatus" readonly>
            </div>
            <div class="col-12 col-lg-3 mt-4">
                <label class="form-label">Section</label>
                <input type="text" class="form-control" name="section" id="Section" readonly>
            </div>

            <div class="col-12 mt-4" id="existingFilesSection" style="display: none;">
                <div class="bg-white border p-3 rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Already Uploaded Documents</h6>
                        <span id="uploadedCount"></span>
                    </div>
                    <div id="uploadedFilesContainer" class="row g-3"></div>
                </div>
            </div>

            @unless($isViewOnly)
            <div class="col-12 mt-4">
                <div class="row g-2">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="button" id="addMoreDocument" class="btn btn-primary">+ Add More Document</button>
                    </div>
                    <div class="col-12">
                        <div class="bg-light p-3 rounded shadow-sm" id="documentRepeater">
                            <div class="row document-entry align-items-start">
                                <div class="col-12 col-lg-6 mt-2">
                                    <label class="form-label">Document Name</label>
                                    <input type="text" class="form-control document-name" name="documents[0][document_name]" id="DocumentName" readonly>
                                </div>
                                <div class="col-12 col-lg-6 mt-2">
                                    <label class="form-label">Upload Document</label>
                                    <input type="file" class="form-control" name="documents[0][document]" id="DocumentFile" accept=".pdf">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endunless

            <input type="hidden" name="property_id" id="OldPropertyId">
            <input type="hidden" name="flat_id" id="FlatId">
            <input type="hidden" name="splited_property_detail_id" id="SplitId">
            <input type="hidden" name="property_master_id" id="PropertyMasterId">

            @unless($isViewOnly)
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
            @endunless
        </div>

        @unless($isViewOnly)
        </form>
        @endunless
    </div>
</div>
@endsection

@section('footerScript')
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchBtn = document.getElementById('submitButton1'); // Search button
    const errorContainer = document.getElementById('propertIdSearchError');
    const detailsSection = document.getElementById('propertyDetailsSection');

    // Details fields
    const fileNumberField = document.getElementById('FileNumberNew');
    const flatNameField = document.getElementById('FlatNumber');
    const plotNameField = document.getElementById('PlotNumber');
    const blockNameField = document.getElementById('BlockNumber');
    const colonyNameField = document.getElementById('ColonyNameNew');
    const statusNameField = document.getElementById('PropertyStatus');
    const sectionNameField = document.getElementById('Section');
    const presentlyKnownAsField = document.getElementById('PresentlyKnownAs');

    // Hidden ids
    const flatIdField = document.getElementById('FlatId');
    const splitIdField = document.getElementById('SplitId');
    const propertyMasterIdField = document.getElementById('PropertyMasterId');
    const oldPropertyIdField = document.getElementById('OldPropertyId');

    // Upload area
    const existingFilesSection = document.getElementById('existingFilesSection');
    const uploadedFilesContainer = document.getElementById('uploadedFilesContainer');
    const documentNameField = document.getElementById('DocumentName'); // first row name
    const repeater = document.getElementById('documentRepeater');

    // Inputs from the property-selector partial (do not modify partial, only listen here)
    const oldPropertyIdInput = document.getElementById('oldPropertyId');
    const colonySelect = document.getElementById('colony_id');
    const blockSelect  = document.getElementById('block');
    const plotSelect   = document.getElementById('plot');

    let documentIndex = 1; // will be set to next VOL on successful search

    // --- helper: reset upload repeater (clear chosen files, remove extra rows, clear errors, restore names) ---
    function resetUploadSection() {
        if (!repeater) return;

        // clear previous inline errors
        document.querySelectorAll('#documentRepeater .invalid-feedback.dynamic-error').forEach(n => n.remove());
        document.querySelectorAll('#documentRepeater input.is-invalid').forEach(i => i.classList.remove('is-invalid'));

        const rows = Array.from(repeater.querySelectorAll('.document-entry'));
        // remove all rows except the first
        rows.slice(1).forEach(r => r.remove());

        // reset first row inputs + attributes
        const firstRow = rows[0];
        if (firstRow) {
            const nameInput = firstRow.querySelector('input.document-name');
            const fileInput = firstRow.querySelector('input[type="file"]');
            if (nameInput) {
                nameInput.value = '';
                nameInput.name = 'documents[0][document_name]';
                nameInput.removeAttribute('disabled');
            }
            if (fileInput) {
                fileInput.value = '';
                fileInput.name = 'documents[0][document]';
                fileInput.removeAttribute('disabled');
            }
        }

        // reset local counter; search will set it to next VOL
        documentIndex = 1;
    }

    // Reset uploads whenever search criteria change
    [oldPropertyIdInput, colonySelect, blockSelect, plotSelect].forEach(el => {
        if (!el) return;
        el.addEventListener('change', resetUploadSection);
        el.addEventListener('input',  resetUploadSection);
    });

    // Prefill route support
    @if(!empty($prefillPropertyId))
        const prefillInput = document.getElementById('oldPropertyId');
        if (prefillInput) prefillInput.value = "{{ $prefillPropertyId }}";
        if (searchBtn) setTimeout(() => { searchBtn.click(); }, 200);
    @endif

    // View-only open support
    @if($isViewOnly && isset($propertyData))
        if (oldPropertyIdInput) oldPropertyIdInput.value = @json($propertyData->old_property_id ?? '');
        if (searchBtn) setTimeout(() => { searchBtn.click(); }, 100);
    @endif

    // --- SEARCH click ---
    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            // reset upload rows for new context
            resetUploadSection();

            const propertyId = (oldPropertyIdInput?.value || '').trim() || (plotSelect?.value || '').trim();
            if (!propertyId) {
                errorContainer.innerHTML = 'Please select colony, block, plot/flat or enter a Property ID.';
                return;
            }

            const formData = new FormData();
            formData.append('property_id', propertyId);

            fetch("{{ route('property.scanning.search') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // clear UI
                errorContainer.innerHTML = '';
                detailsSection.style.display = 'none';
                uploadedFilesContainer.innerHTML = '';
                existingFilesSection.style.display = 'none';

                if (data.status === 'found') {
                    // Fill details
                    fileNumberField.value = data.file_no || '';
                    colonyNameField.value = data.colony_name || '';
                    if (data.type === 'flat') {
                        document.getElementById('FlatNumberField').style.display = 'block';
                        flatNameField.value = data.flat_no || '';
                        document.getElementById('ColonyField').classList.add('mt-4');
                    } else {
                        document.getElementById('FlatNumberField').style.display = 'none';
                        flatNameField.value = '';
                        document.getElementById('ColonyField').classList.remove('mt-4');
                    }

                    plotNameField.value = data.plot || '';
                    blockNameField.value = data.block || '';
                    presentlyKnownAsField.value = data.presently_known_as || '';
                    statusNameField.value = data.property_status || '';
                    sectionNameField.value = data.section || '';
                    oldPropertyIdField.value = data.old_property_id || '';
                    propertyMasterIdField.value = data.property_master_id || '';
                    flatIdField.value = data.flat_id || '';
                    splitIdField.value = data.split_id || '';

                    // Determine next volume and prefill first row's document name
                    const cleanColony = (data.colony_name || '').replace(/\s+/g, '_').toUpperCase();
                    let maxVol = 0;
                    if (Array.isArray(data.uploaded_files)) {
                        data.uploaded_files.forEach(file => {
                            const match = (file.document_name || '').match(/_VOL_(\d+)/);
                            if (match && match[1]) {
                                const volNum = parseInt(match[1], 10);
                                if (!isNaN(volNum) && volNum > maxVol) maxVol = volNum;
                            }
                        });
                    }
                    const nextVol = maxVol + 1;
                    if (documentNameField) {
                        documentNameField.value = (data.old_property_id || propertyId) + '_' + cleanColony + `_VOL_${nextVol}`;
                    }
                    documentIndex = nextVol;

                    // Already uploaded list
                    if (Array.isArray(data.uploaded_files) && data.uploaded_files.length > 0) {
                        data.uploaded_files.forEach(file => {
                            const fileRow = document.createElement('div');
                            fileRow.classList.add('col-12');
                            fileRow.innerHTML = `
                                <div class="row gx-4 align-items-end">
                                    <div class="col-lg-6">
                                        <label class="form-label">Document Name</label>
                                        <input type="text" class="form-control" value="${file.document_name || ''}" readonly>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">View Document</label><br>
                                        <a href="/storage/${file.document_path}" target="_blank" class="text-danger fs-4" title="View PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </div>
                                </div>
                            `;
                            uploadedFilesContainer.appendChild(fileRow);
                        });
                        document.getElementById('uploadedCount').textContent = `Total Uploads: ${data.uploaded_files.length} file(s)`;
                        existingFilesSection.style.display = 'block';
                    }

                    detailsSection.style.display = 'flex';
                } else {
                    errorContainer.innerHTML = 'Property not found in records.';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                errorContainer.innerHTML = 'An error occurred while searching.';
            });
        });
    }

    // --- ADD MORE Document ---
    const addMoreBtn = document.getElementById('addMoreDocument');
    if (addMoreBtn) {
        addMoreBtn.addEventListener('click', function () {
            const propertyId = (oldPropertyIdInput?.value || '').trim() || (plotSelect?.value || '').trim();
            const colony = (colonyNameField.value || '').trim().replace(/\s+/g, '_').toUpperCase();
            const volume = documentIndex + 1;

            const newRow = document.createElement('div');
            newRow.classList.add('row', 'document-entry');
            newRow.innerHTML = `
                <div class="col-12 col-lg-6 mt-3">
                    <label class="form-label">Document Name</label>
                    <input type="text" class="form-control document-name" name="documents[${documentIndex}][document_name]" value="${propertyId}_${colony}_VOL_${volume}" readonly>
                </div>
                <div class="col-12 col-lg-6 mt-3">
                    <label class="form-label">Upload Document</label>
                    <input type="file" class="form-control" name="documents[${documentIndex}][document]" accept=".pdf">
                </div>
            `;
            repeater.appendChild(newRow);
            documentIndex++;
        });
    }

    // --- SUBMIT: inline errors, no gaps, only trailing empties, keep DOM visible ---
    const form = document.getElementById('scanningForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const rows = Array.from(document.querySelectorAll('#documentRepeater .document-entry'));
            const clearRowErrors = () => {
                errorContainer.innerHTML = ''; // keep search errors separate
                document.querySelectorAll('#documentRepeater .invalid-feedback.dynamic-error').forEach(n => n.remove());
                document.querySelectorAll('#documentRepeater input.is-invalid').forEach(i => i.classList.remove('is-invalid'));
            };
            const setRowError = (fileInput, msg) => {
                fileInput.classList.add('is-invalid');
                const holder = fileInput.closest('.col-12, .col-lg-6') || fileInput.parentElement;
                let fb = holder.querySelector('.invalid-feedback.dynamic-error');
                if (!fb) {
                    fb = document.createElement('div');
                    fb.className = 'invalid-feedback dynamic-error d-block';
                    holder.appendChild(fb);
                }
                fb.textContent = msg;
                fileInput.focus();
                fileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            };

            clearRowErrors();

            // 1) find first empty row
            let firstEmpty = -1;
            for (let i = 0; i < rows.length; i++) {
                const fi = rows[i].querySelector('input[type="file"]');
                const hasFile = fi && fi.files && fi.files.length > 0;
                if (!hasFile) { firstEmpty = i; break; }
            }

            // 2) block gaps (file after empty row)
            if (firstEmpty !== -1) {
                for (let j = firstEmpty + 1; j < rows.length; j++) {
                    const fi = rows[j].querySelector('input[type="file"]');
                    const hasFile = fi && fi.files && fi.files.length > 0;
                    if (hasFile) {
                        e.preventDefault();
                        setRowError(fi, 'Cannot upload here because there is an empty row above. Move empty rows to the end.');
                        return;
                    }
                }
            }

            // 3) require at least one file
            const allFileInputs = rows.map(r => r.querySelector('input[type="file"]')).filter(Boolean);
            const hasAny = allFileInputs.some(fi => fi.files && fi.files.length > 0);
            if (!hasAny) {
                e.preventDefault();
                const target = allFileInputs[0] || document.querySelector('#documentRepeater input[type="file"]');
                if (target) setRowError(target, 'Please upload at least one PDF.');
                return;
            }

            // 4) strip trailing empties from submission (don’t remove DOM)
            if (firstEmpty !== -1) {
                for (let j = firstEmpty; j < rows.length; j++) {
                    rows[j].querySelectorAll('input').forEach(inp => {
                        inp.removeAttribute('name');          // exclude from submission
                        inp.setAttribute('disabled', 'true'); // optional: lock inputs
                    });
                }
            }
        });
    }
});
</script>
@endsection

