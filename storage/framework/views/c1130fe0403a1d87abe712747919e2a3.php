<?php if(!request()->has('pending_alert') || base64_decode(request()->query('pending_alert')) !== 'true'): ?>
<div class="part-title mt-2">
    <h5>Action Taken</h5>
</div>
<div class="part-details">
    <?php if($application->Signed_letter): ?>
    <?php else: ?>
        <div class="container-fluid pb-3">
            <div class="row">
                <!-- For Showing Pending Dues START ******************************************************************************* -->
                <div class="col-lg-12 mt-4">
                    <div class="payment-due">
                        <div class="pending-amount-group">
                            <div class="pending-wrap">
                                <h5 class="pending-title">Demand Amount</h5>
                                <p class="pending-amount">₹ <?php echo e(customNumFormat($demandAmount)); ?></p>
                            </div>
                            <div class="pending-wrap">
                                <h5 class="pending-title">Paid Amount</h5>
                                <p class="pending-amount">₹ <?php echo e(customNumFormat($paidAmount)); ?></p>
                            </div>
                            <div class="pending-wrap">
                                <h5 class="pending-title">Outstanding Dues</h5>
                                <p class="pending-amount">₹ <?php echo e(customNumFormat($pendingAmount)); ?></p>
                            </div>
                            <?php if($latestMovement->assigned_to == auth()->id() && $latestMovement->is_forwarded ==2): ?>
                                <div class="pending-amount-group">
                                    <h4 class="pending-title">New Demand created. Please approve the demand</h4>
                                </div>
                            <?php endif; ?>
                            <!-- <div class="other-pendings">
                                <p class="other-pendings-titles">Demand Amount</p>
                                <p class="other-pending-amount">₹ <?php echo e(customNumFormat($demandAmount)); ?></p>
                                <p class="other-pendings-titles">Paid Amount</p>
                                <p class="other-pending-amount">₹ <?php echo e(customNumFormat($paidAmount)); ?></p>
                            </div> -->
                            
                        </div>
                         <?php if($pendingAmount > 0): ?>
                            <div class="view-details">
                                <?php if(!$isNewDemand): ?>
                                <div class="text-danger fw-semibold">Old demand is available. Create a new demand on eDharti 2.0 using the Create Demand Button.</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($pendingDemands->isNotEmpty()): ?>
                        <div style="text-align:right">
                            <a target="_blank" href="<?php echo e(route('ViewDemand', ['demandId' => $pendingDemands->first()->id ?? 0])); ?>">
                                View Details
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- For Showing Pending Dues END ******************************************************************************* -->
                <div class="col-lg-12">

                    <!-- For View Scanned files an Go to Poperty Details Button START **************************************************************** -->
                    <div class="mis-view-group-btn">
                        <?php
                            $serviceCode = getServiceCodeById($serviceType) ?? '';
                            $modalId = $details->id ?? '';
                            $applicant_no = $details->application_no ?? '';
                            // $masterId = $details->property_master_id ?? '';
                            $masterId = $isSplited == 1 ? $details->$splittedColumn : $details->property_master_id ?? '';
                            $uniquePropertyId = $details->new_property_id ?? '';
                            $oldPropertyId = $details->old_property_id ?? '';
                            $sectionCode = $details->sectionCode ?? '';
                            //Flat Id added by Lalit on 24/Dec/2024
                            $flatId = !empty($details->flat_id) ? $details->flat_id : '';
                            $additionalData = [
                                $serviceCode,
                                $modalId,
                                $applicant_no,
                                $masterId,
                                $uniquePropertyId,
                                $oldPropertyId,
                                $sectionCode,
                                $flatId,
                            ];
                            $additionalDataJson = json_encode($additionalData);
                        ?>
                        <div class="btn-group">
                            <a href="javascript:void(0);" id="PropertyIDSearchBtn" class="btn btn-grey pdf-btn"
                                data-bs-toggle="modal" data-bs-target="#viewScannedFiles">View Scanned Files <i
                                    class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                        <div class="btn-group">
                            <?php
                                if ($isSplited == 0) {
                                    $url =
                                        route('viewDetails', [
                                            'property' => $propertyMasterId,
                                        ]) .
                                        '?params=' .
                                        urlencode($additionalDataJson);
                                } else {
                                    $url =
                                        route('propertyChildDetails', ['id' => $details->$splittedColumn]) .
                                        '?params=' .
                                        urlencode($additionalDataJson);
                                }
                            ?>

                            <a href="<?php echo e($url); ?>">
                                <button type="button" id="PropertyIDSearchBtn" class="btn btn-primary ml-2">Go to
                                    Property Details</button>
                            </a>
                            
                        </div>
                        <div class="btn-group">
                            <a href="<?php echo e(route('createDemandView')); ?>"
                                target="_blank">
                                <button type="button" id="createDemandBtn" class="btn btn-primary ml-2">Create
                                    Demand</button>
                            </a>

                        </div>
                    </div>
                    <!-- For View Scanned files an Go to Poperty Details Button END **************************************************************** -->


                    <!-- For Checks of MIS, Scanned files and upladed documents START *********************************************** -->
                    <div class="row py-3">
                        <div class="col-lg-12 mt-4">
                            <div class="checkbox-options">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label" for="isMISCorrect">

                                        MIS Checked
                                    </label>
                                    <?php
                                        $misChecked = $checkList && $checkList->is_mis_checked == 1 ? 'checked' : '';
                                        $misDisabled =
                                            ($checkList && $checkList->is_mis_checked == 1) ||
                                            $roles != 'section-officer'
                                                ? 'disabled'
                                                : '';
                                    ?>
                                    <input class="form-check-input required-for-approve" <?php echo e($misChecked); ?>

                                        <?php echo e($misDisabled); ?> name="is_mis_checked" type="checkbox" value="1"
                                        id="isMISCorrect">
                                    <div class="text-danger required-error-message" id="misCheckedError">This
                                        field is required.
                                    </div>
                                </div>
                            </div>

                            <div class="checkbox-options">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label" for="isScanningCorrect">
                                        Scanned File Checked
                                    </label>
                                    <?php
                                        $scanChecked =
                                            $checkList && $checkList->is_scan_file_checked == 1 ? 'checked' : '';
                                        $scanDisabled =
                                            ($checkList && $checkList->is_scan_file_checked == 1) ||
                                            $roles != 'section-officer'
                                                ? 'disabled'
                                                : '';
                                    ?>
                                    <input class="form-check-input required-for-approve" <?php echo e($scanChecked); ?>

                                        <?php echo e($scanDisabled); ?> name="is_scan_file_checked" type="checkbox" value="1"
                                        id="isScanningCorrect">
                                    <div class="text-danger required-error-message" id="isScanningCheckedError">This
                                        field is required.
                                    </div>
                                </div>
                            </div>

                            <div class="checkbox-options">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label" for="isDocumentCorrect">
                                        Uploaded Documents Checked
                                    </label>
                                    <?php
                                        $uploadDocChecked =
                                            $checkList && $checkList->is_uploaded_doc_checked == 1 ? 'checked' : '';
                                        $uploadDocDisabled =
                                            ($checkList && $checkList->is_uploaded_doc_checked == 1) ||
                                            ($roles != 'section-officer' &&
                                                $roles != 'assistant-section-officer' &&
                                                auth()->user()->can('can.approve.application.mis.scanned.document'))
                                                ? 'disabled'
                                                : '';
                                    ?>
                                    <input class="form-check-input required-for-approve" <?php echo e($uploadDocChecked); ?>

                                        <?php echo e($uploadDocDisabled); ?> name="is_uploaded_doc_checked" type="checkbox"
                                        value="1" id="isDocumentCorrect"></input>
                                    <div class="text-danger required-error-message" id="isDocumentCorrectError">
                                        This field is required.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- For Checks of MIS, Scanned files and upladed documents START *********************************************** -->


                </div>



            </div>
        </div>
    <?php endif; ?>

    <!-- Seperate blade files for each application according to service type  START *********************************-->
    <?php switch($applicationType):
        case ('Mutation'): ?>
            
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Applicants (Other share holder's of the property)</h5>
                        <?php if($roles == 'section-officer'): ?>
                        <button type="button" class="btn btn-primary" id="addApplicant">
                            Add +
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div id="applicantContainer"></div>
                    </div>
                </div>
            
            <?php echo $__env->make('application.admin.office_activity.mutation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>

        <?php case ('Deed Of Apartment'): ?>
            <?php echo $__env->make('application.admin.office_activity.doa', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>

        <?php case ('Land Use Change'): ?>
            <?php echo $__env->make('application.admin.office_activity.luc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>

        <?php case ('Conversion'): ?>
            <?php echo $__env->make('application.admin.office_activity.conversion', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>

        <?php case ('Noc'): ?>
            <?php echo $__env->make('application.admin.office_activity.noc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>

        <?php default: ?>
            <h5>No Actions Available</h5>
    <?php endswitch; ?>

    <!-- Seperate blade files for each application according to service type  END *********************************-->

</div>



<!-- Modal for Revert START *************************************************************************** -->
<div class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="revertAppForm">
                <div class="modal-header">
                    
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="revertRemark" class="form-label">Remarks</label>
                        <textarea name="revertRemark" id="revertRemark" class="form-control" placeholder="Remarks" rows="3" required></textarea>
                        <div id="revertRemarkError" class="text-danger" style="display: none;">
                            Please enter remarks.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="revertAppBtn" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal for Revert END *************************************************************************** -->




<!-- Modal for Start proof reading START *************************************************************************** -->
<div class="modal fade" id="startProofReadingModal" tabindex="-1" aria-labelledby="startProofReadingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revertModalLabel">Proof Reading of Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to perform this action? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a
                    href="<?php echo e(route('startProofReading', ['id' => $details->id])); ?>?type=<?php echo e(request()->query('type')); ?>">
                    <button type="button" id="startProofReadingButton" class="btn btn-primary">Proceed</button>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Start proof reading END *************************************************************************** -->


<!-- Modal for Start proof reading START *************************************************************************** -->
<div class="modal fade" id="actionConfirmationModal" tabindex="-1" aria-labelledby="actionConfirmationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                Do you really want to perform this action? This can't be reverted.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a>
                    <button type="button" id="actionConfirmationButton" class="btn btn-primary">Proceed</button>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Modal for Start proof reading END *************************************************************************** -->
 <?php endif; ?>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/admin/office_activity/index.blade.php ENDPATH**/ ?>