<div class="modal fade" id="requestMisEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" id="requestEditMisForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" id="serviceType" name="serviceType" value="<?php echo e(isset($additionalData[0]) ? $additionalData[0] : ''); ?>">
                <input type="hidden" id="modalId" name="modalId" value="<?php echo e(isset($additionalData[1]) ? $additionalData[1] : ''); ?>">
                <input type="hidden" id="applicantNo" name="applicantNo" value="<?php echo e(isset($additionalData[2]) ? $additionalData[2] : ''); ?>">
                <input type="hidden" id="newPropertyId" name="newPropertyId" value="<?php echo e($viewDetails->unique_propert_id); ?>">
                <input type="hidden" id="oldPropertyId" name="oldPropertyId" value="<?php echo e($viewDetails->old_propert_id); ?>">
                <input type="hidden" id="masterId" name="masterId" value="<?php echo e($viewDetails->id); ?>">
                <input type="hidden" id="sectionCode" name="sectionCode" value="<?php echo e($viewDetails->section_code); ?>">
                <input type="hidden" id="flatId" name="flatId" value="<?php echo e(isset($flatData['flatDetails']->flat_id) ? $flatData['flatDetails']->flat_id : ''); ?>">
                <div class="modal-header">
                    <h4 class="modal-title mb-2">Are you sure?</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Do you want to send edit request.</p>
                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter remarks"></textarea>
                    <div id="remarksError" class="error-label text-danger mt-2" style="display:none; margin-left:0px;">Please enter
                        remarks.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="confirmrequestEditMisCheckedCloseBtn" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="confirmrequestEditMisChecked" class="btn btn-primary confirm-review-btn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/include/alerts/section/request-mis-edit-model-popup.blade.php ENDPATH**/ ?>