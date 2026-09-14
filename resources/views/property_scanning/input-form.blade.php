@extends('layouts.app')
@section('title', 'Property Scanning')
<link rel="stylesheet" href="{{ asset('assets/css/rgr.css') }}" />
<style>
    #propertIdSearchError {
        font-size: 1.05rem;
        font-weight: 600;
    }
</style>


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
                    <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Already Uploaded Documents</h6>
                        <span id="uploadedCount"></span>
                    </div> -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Already Uploaded Documents</h6>

                        <div class="d-flex align-items-center gap-2">
                            <a href="#" id="downloadAllBtn" class="btn btn-primary py-2" style="display:none;">
                                Download All (ZIP)
                            </a>
                            <span id="uploadedCount"></span>
                        </div>
                    </div>

                    <div id="uploadedFilesContainer" class="row g-3"></div>
                </div>
            </div>

            @unless($isViewOnly)
            <!-- Single multiple upload field (repeater removed) -->
            <div class="col-12 mt-4">
                <div class="bg-light p-3 rounded shadow-sm">
                    <div class="row g-3 align-items-start">
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Upload Documents</label>
                            <input type="file" class="form-control" name="documents[]" id="Documents" accept=".pdf" multiple>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label">Selected Files</label>
                            <div class="bg-white border rounded p-2" style="min-height: 42px;">
                                <ul id="selectedFilesList" class="mb-0 ps-3"></ul>
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

    // New multiple upload input + list
    const docsInput = document.getElementById('Documents');
    const selectedFilesList = document.getElementById('selectedFilesList');

    // Inputs from the property-selector partial (do not modify partial, only listen here)
    const oldPropertyIdInput = document.getElementById('oldPropertyId');
    const colonySelect = document.getElementById('colony_id');
    const blockSelect  = document.getElementById('block');
    const plotSelect   = document.getElementById('plot');

    const MAX_FILE_SIZE = 20 * 1024 * 1024;
    const downloadAllBtn = document.getElementById('downloadAllBtn');
    const uploadFilesBtn = document.getElementById('uploadFilesBtn');

    // ✅ NEW: store existing uploaded file names (slugged)
    let existingFileNames = new Set();

    // ✅ NEW: match backend Str::slug($name, '_')
    function slugifyToUnderscore(str) {
        return (str || '')
            .toString()
            .trim()
            .toLowerCase()
            .replace(/\.[^/.]+$/, '')          // remove extension if present
            .replace(/[^a-z0-9]+/g, '_')       // non-alnum -> _
            .replace(/^_+|_+$/g, '')           // trim underscores
            .replace(/_+/g, '_');              // collapse underscores
    }

    // --- helper: reset upload selection + errors (repeater removed) ---
    function resetUploadSection() {
        // clear previous error
        errorContainer.innerHTML = '';

        // ✅ NEW: reset existing names for new context
        existingFileNames = new Set();

        // clear selected files
        if (docsInput) docsInput.value = '';
        if (selectedFilesList) selectedFilesList.innerHTML = '';
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

    // Show selected filenames
    if (docsInput && selectedFilesList) {
        docsInput.addEventListener('change', () => {
            selectedFilesList.innerHTML = '';
            const files = Array.from(docsInput.files || []);
            files.forEach(f => {
                const li = document.createElement('li');
                li.textContent = f.name;
                selectedFilesList.appendChild(li);
            });
        });
    }

    // --- SEARCH click ---
    if (searchBtn) {
        searchBtn.addEventListener('click', function () {
            // reset upload selection for new context
            resetUploadSection();

            const propertyId = (oldPropertyIdInput?.value || '').trim() || (plotSelect?.value || '').trim();
            if (!propertyId) {
                // errorContainer.innerHTML = 'Please select colony, block, plot/flat or enter a Property ID.';
                showError('Please select colony, block, plot/flat or enter a Property ID.');
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

                // ✅ NEW: reset existing names for this search result
                existingFileNames = new Set();

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

                    // Show "Download All" only if files exist
                    if (downloadAllBtn) {
                        if (Array.isArray(data.uploaded_files) && data.uploaded_files.length > 0) {
                            const pid = data.old_property_id || '';
                            downloadAllBtn.href = "{{ url('edharti/property-scanning') }}/" + encodeURIComponent(pid) + "/download-all";
                            downloadAllBtn.style.display = 'inline-block';
                        } else {
                            downloadAllBtn.style.display = 'none';
                            downloadAllBtn.href = '#';
                        }
                    }

                    // Already uploaded list
                    if (Array.isArray(data.uploaded_files) && data.uploaded_files.length > 0) {
                        data.uploaded_files.forEach(file => {
                            // ✅ NEW: collect existing filenames for duplicate check
                            if (file.old_property_file_name) {
                                existingFileNames.add(slugifyToUnderscore(file.old_property_file_name));
                            }

                            const fileRow = document.createElement('div');
                            fileRow.classList.add('col-12');
                            fileRow.innerHTML = `
                                <div class="row gx-4 align-items-end">
                                    <div class="col-lg-6">
                                        <label class="form-label">Document Name</label>
                                        <input type="text" class="form-control" value="${file.old_property_file_name || ''}" readonly>
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
                    // errorContainer.innerHTML = 'Property not found in records.';
                    showError('Property not found in records.');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                // errorContainer.innerHTML = 'An error occurred while searching.';
                showError('An error occurred while searching.');
                
            });
        });
    }

    // --- SUBMIT: require at least one PDF selected + block duplicates ---
    const form = document.getElementById('scanningForm');
    if (form) {
    form.addEventListener('submit', function (e) {
        // keep search errors separate
        errorContainer.innerHTML = '';

        if (docsInput && (!docsInput.files || docsInput.files.length === 0)) {
            e.preventDefault();
            showError('Please upload at least one PDF.');
            docsInput.focus();
            docsInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const selectedFiles = Array.from(docsInput.files || []);

        // ✅ NEW: per-file size validation (20MB each)
        const oversizedFiles = selectedFiles
            .filter(f => f.size > MAX_FILE_SIZE)
            .map(f => f.name);

        if (oversizedFiles.length > 0) {
            e.preventDefault();
            showError(
                'These file(s) exceed the maximum allowed size of 20 MB: ' +
                oversizedFiles.join(', ')
            );
            docsInput.focus();
            docsInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // ✅ prevent duplicates already uploaded for this property
        const selectedSlugs = selectedFiles.map(f => slugifyToUnderscore(f.name));

        // 1) duplicates inside current selection
        const seen = new Set();
        const internalDups = [];
        selectedSlugs.forEach(n => {
            if (seen.has(n)) internalDups.push(n);
            seen.add(n);
        });

        if (internalDups.length > 0) {
            e.preventDefault();
            showError('Duplicate file selected in upload: ' + [...new Set(internalDups)].join(', '));
            docsInput.focus();
            docsInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // 2) duplicates against already uploaded
        const alreadyExists = selectedSlugs.filter(n => existingFileNames.has(n));
        if (alreadyExists.length > 0) {
            e.preventDefault();
            showError('This file already exists for this property: ' + [...new Set(alreadyExists)].join(', '));
            docsInput.focus();
            docsInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
    });
}
    function showError(message) {
    errorContainer.innerHTML = message;

    // Scroll page to error message
    errorContainer.scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}

});
</script>
@endsection
