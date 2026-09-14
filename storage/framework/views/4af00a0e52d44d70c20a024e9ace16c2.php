

<?php $__env->startSection('title', 'Litigation Management'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $viewOnly = $viewOnly ?? false;
    $isExistingRecord = isset($litigation);
    $isEdit = $isExistingRecord && !$viewOnly;

    $parties = old(
        'parties',
        $litigation->parties ?? [
            [
                'party_name' => '',
                'party_type' => '',
                'party_subtype' => '',
            ]
        ]
    );

    $counsels = old(
        'counsels',
        $litigation->counsels ?? [
            [
                'counsel_name' => '',
                'designation' => '',
                'mobile' => '',
                'email' => '',
                'appointment_date' => '',
                'address' => '',
            ]
        ]
    );

    $allHearings = $isExistingRecord && isset($litigation->hearings)
        ? $litigation->hearings->sortByDesc('ndoh')->values()
        : collect();

    $latestHearing = $allHearings->first();
    $previousHearings = $allHearings->slice(1)->values();

    $hearings = old(
        'hearings',
        $latestHearing
            ? [
                [
                    'id' => $latestHearing->id,
                    'is_existing' => 1,
                    'ldoh' => $latestHearing->ldoh ? \Carbon\Carbon::parse($latestHearing->ldoh)->format('Y-m-d') : '',
                    'ndoh' => $latestHearing->ndoh ? \Carbon\Carbon::parse($latestHearing->ndoh)->format('Y-m-d') : '',
                    'ldoh_hearing_details' => $latestHearing->ldoh_hearing_details,
                ]
            ]
            : [
                [
                    'is_existing' => 0,
                    'ldoh' => '',
                    'ndoh' => '',
                    'ldoh_hearing_details' => '',
                ]
            ]
    );
?>

<style>
    .input-error,
    .select-error {
        border-color: #dc3545 !important;
    }
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

    <div class="breadcrumb-title pe-3">
        Litigation Management
    </div>

    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">

                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('dashboard')); ?>">
                        <i class="bx bx-home-alt"></i>
                    </a>
                </li>

                <li class="breadcrumb-item active">
                    <?php echo e($viewOnly ? 'View Court Case' : ($isEdit ? 'Edit Court Case' : 'Add Court Case')); ?>

                </li>

            </ol>
        </nav>
    </div>

</div>

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-end mb-3">
            <a href="<?php echo e(route('litigation.index')); ?>"
                class="btn btn-primary">
                Court Cases List
            </a>
        </div>

        <form action="<?php echo e($viewOnly ? '#' : ($isEdit ? route('litigation.update', $litigation->id) : route('litigation.store'))); ?>"
            method="POST"
            novalidate>

            <?php echo csrf_field(); ?>

            <?php if($isEdit): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="row mb-4 align-items-start">

                <div class="col-md-4">
                    <label class="form-label">Property ID</label>

                    <input type="text"
                        name="search_property_id"
                        id="search_property_id"
                        class="form-control"
                        maxlength="5"
                        value="<?php echo e(old('search_property_id', request('property_id', $litigation->property_id ?? ''))); ?>">

                    <div id="search_property_id_error" class="text-danger text-left"></div>
                </div>

                <?php if(!$viewOnly): ?>
                <div class="col-md-3">
                    <label class="form-label w-100">&nbsp;</label>
                    <button type="button"
                        class="btn btn-primary"
                        id="litigationSearchBtn">
                        Search Property Details
                    </button>
                </div>
                <?php endif; ?>

                <div class="col-md-6">
                    <div id="litigationSearchError"
                        class="text-danger fw-bold pt-2">
                    </div>
                </div>

            </div>

            <div id="propertyBasicDetailsSection" style="display:none;">
                <div class="part-title mb-3 mt-4">
                    <h5>Property Basic Details</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Colony Name</label>
                        <input type="text" id="basic_colony_name" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Block</label>
                        <input type="text" id="basic_block" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Plot</label>
                        <input type="text" id="basic_plot" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Land Type</label>
                        <input type="text" id="basic_land_type" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Property Type</label>
                        <input type="text" id="basic_property_type" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Property Subtype</label>
                        <input type="text" id="basic_property_subtype" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">File No.</label>
                        <input type="text" id="basic_file_no" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Known As</label>
                        <input type="text" id="basic_known_as" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="part-title mb-3">
                <h5>Case Details</h5>
            </div>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Property ID</label>
                    <input type="text"
                        name="property_id"
                        id="property_id"
                        class="form-control"
                        maxlength="5"
                        minlength="5"
                        value="<?php echo e(old('property_id', $litigation->property_id ?? '')); ?>">
                    <div id="property_id_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Property Address</label>
                    <input type="text"
                        name="property_known_as"
                        id="property_known_as"
                        class="form-control"
                        value="<?php echo e(old('property_known_as', $litigation->property_known_as ?? '')); ?>">
                    <div id="property_known_as_error" class="text-danger text-left"></div>
                </div>


                <div class="col-md-4">
                    <label class="form-label">Case Number <span class="text-danger">*</span></label>
                    <input type="text"
                        name="case_number"
                        id="case_number"
                        class="form-control <?php $__errorArgs = ['case_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        value="<?php echo e(old('case_number', $litigation->case_number ?? '')); ?>"
                        required>
                    <!-- <div id="case_number_error" class="text-danger text-left"></div> -->
                     <div id="case_number_error" class="text-danger text-left">
                        <?php $__errorArgs = ['case_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <?php echo e($message); ?>

                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Unique Case Number <span class="text-danger">*</span></label>
                    <input type="text"
                        name="unique_case_id"
                        id="unique_case_id"
                        class="form-control"
                        value="<?php echo e($uniqueCaseId ?? ($litigation->unique_case_id ?? '')); ?>"
                        readonly>
                    <div id="unique_case_id_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Associated Case Number</label>
                    <input type="text"
                        name="associated_case_number"
                        id="associated_case_number"
                        class="form-control"
                        value="<?php echo e(old('associated_case_number', $litigation->associated_case_number ?? '')); ?>">
                    <div id="associated_case_number_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Case Type <span class="text-danger">*</span></label>
                    <select name="case_type" id="case_type" class="form-select" required>
                        <option value="">Select Case Type</option>
                        <?php $__currentLoopData = $caseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type); ?>"
                                <?php echo e(old('case_type', $litigation->case_type ?? '') == $type ? 'selected' : ''); ?>>
                                <?php echo e($type); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="case_type_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Judicial Authority <span class="text-danger">*</span></label>
                    <select name="judicial_authority" id="judicial_authority" class="form-select">
                        <option value="">Select Judicial Authority</option>
                        <?php $__currentLoopData = $judicialAuthorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $authority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($authority); ?>"
                                <?php echo e(old('judicial_authority', $litigation->judicial_authority ?? '') == $authority ? 'selected' : ''); ?>>
                                <?php echo e($authority); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="judicial_authority_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Court Location <span class="text-danger">*</span></label>
                    <input type="text"
                        name="court_location"
                        id="court_location"
                        class="form-control"
                        value="<?php echo e(old('court_location', $litigation->court_location ?? '')); ?>">
                    <div id="court_location_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Case Brief <span class="text-danger">*</span></label>
                    <textarea name="case_brief"
                        id="case_brief"
                        class="form-control"
                        rows="3"><?php echo e(old('case_brief', $litigation->case_brief ?? '')); ?></textarea>
                    <div id="case_brief_error" class="text-danger text-left"></div>
                </div>
            </div>

            <div class="part-title mt-4 mb-2">
                <h5>Party Details</h5>
            </div>

            <?php if(!$viewOnly): ?>
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="addPartyBtn">
                        Add Party
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <div id="partyRepeater">
                <?php $__currentLoopData = $parties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $party): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="party-item border rounded p-3 mb-3">
                        <?php if(!$viewOnly && $index > 0): ?>
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    Remove
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Party Name <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="parties[<?php echo e($index); ?>][party_name]"
                                    class="form-control"
                                    value="<?php echo e($party['party_name'] ?? ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Party Type <span class="text-danger">*</span></label>
                                <select name="parties[<?php echo e($index); ?>][party_type]" class="form-select">
                                    <option value="">Select Party Type</option>
                                    <?php $__currentLoopData = $partyTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type); ?>"
                                            <?php echo e(($party['party_type'] ?? '') == $type ? 'selected' : ''); ?>>
                                            <?php echo e($type); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Party Subtype <span class="text-danger">*</span></label>
                                <select name="parties[<?php echo e($index); ?>][party_subtype]" class="form-select">
                                    <option value="">Select Party Subtype</option>
                                    <?php $__currentLoopData = $partySubtypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subtype): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($subtype); ?>"
                                            <?php echo e(($party['party_subtype'] ?? '') == $subtype ? 'selected' : ''); ?>>
                                            <?php echo e($subtype); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="part-title mt-4 mb-3">
                <h5>Counsel Details</h5>
            </div>

            <?php if(!$viewOnly): ?>
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" id="addCounselBtn">
                        Add Counsel
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <div id="counselRepeater">
                <?php $__currentLoopData = $counsels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $counsel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="counsel-item border rounded p-3 mb-3">

                        <input type="hidden"
                            name="counsels[<?php echo e($index); ?>][is_existing]"
                            value="<?php echo e($isEdit ? 1 : 0); ?>">

                        <?php if(!$viewOnly && $index > 0): ?>
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    Remove
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Counsel Name <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="counsels[<?php echo e($index); ?>][counsel_name]"
                                    class="form-control"
                                    value="<?php echo e($counsel['counsel_name'] ?? ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Designation <span class="text-danger">*</span></label>
                                <select name="counsels[<?php echo e($index); ?>][designation]" class="form-select">
                                    <option value="">Select Designation</option>
                                    <?php $__currentLoopData = $designationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $designation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($designation); ?>"
                                            <?php echo e(($counsel['designation'] ?? '') == $designation ? 'selected' : ''); ?>>
                                            <?php echo e($designation); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Mobile</label>
                                <input type="text"
                                    name="counsels[<?php echo e($index); ?>][mobile]"
                                    class="form-control"
                                    value="<?php echo e($counsel['mobile'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email"
                                    name="counsels[<?php echo e($index); ?>][email]"
                                    class="form-control"
                                    value="<?php echo e($counsel['email'] ?? ''); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Appointment Date</label>
                                <input type="date"
                                    name="counsels[<?php echo e($index); ?>][appointment_date]"
                                    class="form-control"
                                    value="<?php echo e($counsel['appointment_date'] ?? ''); ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="counsels[<?php echo e($index); ?>][address]"
                                    class="form-control"><?php echo e($counsel['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="part-title mt-4 mb-3">
                <h5>Hearing Details</h5>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-end gap-2">
                    <?php if($isExistingRecord): ?>
                        <button type="button"
                            class="btn btn-info view-allow"
                            data-bs-toggle="modal"
                            data-bs-target="#previousHearingsModal">
                            View Previous Hearing Details
                        </button>
                    <?php endif; ?>

                    <?php if(!$viewOnly): ?>
                        <button type="button" class="btn btn-primary" id="addHearingBtn">
                            Add Hearing
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div id="hearingRepeater">
                <?php $__currentLoopData = $hearings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hearing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="hearing-item border rounded p-3 mb-3">

                        <?php if(!$viewOnly && empty($hearing['id']) && $index > 0): ?>
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                    Remove
                                </button>
                            </div>
                        <?php endif; ?>

                        <input type="hidden"
                            name="hearings[<?php echo e($index); ?>][is_existing]"
                            value="<?php echo e(!empty($hearing['id']) ? 1 : 0); ?>">

                        <?php if(!empty($hearing['id'])): ?>
                            <input type="hidden"
                                name="hearings[<?php echo e($index); ?>][id]"
                                value="<?php echo e($hearing['id']); ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Last Date Of Hearing</label>
                                <input type="date"
                                    name="hearings[<?php echo e($index); ?>][ldoh]"
                                    class="form-control"
                                    value="<?php echo e($hearing['ldoh'] ?? ''); ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Next Date Of Hearing</label>
                                <input type="date"
                                    name="hearings[<?php echo e($index); ?>][ndoh]"
                                    class="form-control"
                                    value="<?php echo e($hearing['ndoh'] ?? ''); ?>">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Last Hearing Details</label>
                                <textarea name="hearings[<?php echo e($index); ?>][ldoh_hearing_details]"
                                    class="form-control"
                                    rows="3"><?php echo e($hearing['ldoh_hearing_details'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="part-title mt-4 mb-3">
                <h5>SECTION DETAILS</h5>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section" id="section" class="form-select">
                        <option value="">Select Section</option>
                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($section->id); ?>"
                                <?php echo e(old('section', $litigation->section ?? '') == $section->id ? 'selected' : ''); ?>>
                                <?php echo e($section->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="section_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Section Counsel <span class="text-danger">*</span></label>
                    <input type="text"
                        name="section_counsel"
                        id="section_counsel"
                        class="form-control"
                        value="<?php echo e(old('section_counsel', $litigation->section_counsel ?? '')); ?>">
                    <div id="section_counsel_error" class="text-danger text-left"></div>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Remarks <span class="text-danger">*</span></label>
                    <textarea name="section_remarks"
                        id="section_remarks"
                        class="form-control"
                        rows="3"><?php echo e(old('section_remarks', $litigation->section_remarks ?? '')); ?></textarea>
                    <div id="section_remarks_error" class="text-danger text-left"></div>
                </div>
            </div>

            <?php if(!$viewOnly): ?>
            <div class="mt-4">
                <button type="button" class="btn btn-success" id="litigationSubmit">
                    <?php echo e($isEdit ? 'Update' : 'Submit'); ?>

                </button>

                <button type="submit" id="realLitigationSubmit" style="display:none;"></button>
            </div>
            <?php endif; ?>

        </form>

    </div>
</div>

<?php if($isExistingRecord): ?>
    <div class="modal fade" id="previousHearingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Previous Hearing Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="overflow-x:hidden;">
                    <?php if($previousHearings->count()): ?>
                        <div style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Last Date Of Hearing</th>
                                        <th>Next Date Of Hearing</th>
                                        <th style="width:50%;">Last Hearing Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $previousHearings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hearing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td>
                                                <?php echo e($hearing->ldoh ? \Carbon\Carbon::parse($hearing->ldoh)->format('d-m-Y') : 'N/A'); ?>

                                            </td>
                                            <td>
                                                <?php echo e($hearing->ndoh ? \Carbon\Carbon::parse($hearing->ndoh)->format('d-m-Y') : 'N/A'); ?>

                                            </td>
                                            <td style="
                                                    white-space: normal;
                                                    word-break: break-word;
                                                    overflow-wrap: anywhere;
                                                    max-width: 500px;
                                                ">
                                                    <?php echo e($hearing->ldoh_hearing_details ?? 'N/A'); ?>

                                                </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="mb-0 text-muted">No previous hearing details found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footerScript'); ?>
<script>
    window.litigationConfig = {
        routes: {
            propertyBasicDetails: "<?php echo e(route('propertyCommonBasicdetail')); ?>"
        },
        csrfToken: "<?php echo e(csrf_token()); ?>",
        isEdit: <?php echo json_encode($isEdit, 15, 512) ?>,
        viewOnly: <?php echo json_encode($viewOnly, 15, 512) ?>,
        savedPropertyAddress: <?php echo json_encode(old('property_known_as', $litigation->property_known_as ?? ''), 512) ?>,
        savedSection: <?php echo json_encode(old('section', $litigation->section ?? ''), 512) ?>,
        partyTypes: <?php echo json_encode($partyTypes, 15, 512) ?>,
        partySubtypes: <?php echo json_encode($partySubtypes, 15, 512) ?>,
        designationTypes: <?php echo json_encode($designationTypes, 15, 512) ?>
    };
</script>



<?php if($viewOnly): ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');

        if (!form) return;

        form.querySelectorAll('input, select, textarea, button').forEach(function (element) {
            if (!element.classList.contains('view-allow')) {
                element.disabled = true;
            }
        });
    });
</script>
<?php endif; ?>

<?php if(!$viewOnly): ?>
<script src="<?php echo e(asset('assets/js/litigation.js')); ?>"></script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/litigation/input-form.blade.php ENDPATH**/ ?>