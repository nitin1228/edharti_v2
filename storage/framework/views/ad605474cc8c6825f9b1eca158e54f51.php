<!-- New Design of conversion appliction for Office activty - SOURAV CHAUHAN (9/Jan/2025) -->
<div class="row border-top-1">
    <!-- For Showing Latest Remark and Showing Revert Button START ********************************************************* -->
    <?php if(!empty($latestMovement->remarks)): ?>
        <div class="col-lg-12">
            <div class="remark-container">
                <h4 class="remark-title">Remark</h4>
                <p class="remark-content"> <?php echo e($latestMovement->remarks); ?><span class="author-name">-
                        <?php echo e(!empty($latestMovement->assigned_by) ? getUserNamebyId($latestMovement->assigned_by) : ''); ?>

                        <!--(JE)--></span>, <span
                        class="author-time"><?php echo e(date('d-m-Y h:i a', strtotime($latestMovement->created_at))); ?></span>
                </p>
            </div>
            <?php if($showRevertButton): ?>
                <div class="revert-btn">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revertModal">Revert <i
                            class="fa-solid fa-reply-all"></i></a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <!-- For Showing Latest Remark and Showing Revert Button END ********************************************************* -->

    <!-- For Forwarding the application START ******************************************************************** -->
    <?php echo $__env->make('application.admin.office_activity.forward_application', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- For Forwarding the application END ******************************************************************** -->



    <!-- For Deputy L&do Role  START************************************************************************************ -->

    <?php if($application->Signed_letter): ?>
        <div class="col-lg-4 mt-4 text-end">
            <a href="<?php echo e(asset('storage/' . $application->Signed_letter)); ?>" target="_blank">View
                Signed
                NOC</a>
        </div>
    <?php else: ?>
        <?php if($application->letter): ?>
            <div class="col-lg-4 mt-4">
                <div class="view-generated text-end">
                    <a target="_blank" href="<?php echo e(asset('storage/' . $application->letter).'?t='.time()); ?>">View
                        Generated Letter</a>
                </div>
                <?php if($roles === 'deputy-lndo'): ?>
                    <?php if($showUploadSignedLetter): ?>
                        
                        <?php if(
                            isset($latestAppAction) &&
                            $latestAppAction['latest_action'] !== 'OBJECT' &&
                                $latestAppAction['latest_action'] !== 'REJECT_APP' &&
                                $latestAppAction['latest_action'] !== 'APP_OBJ'): ?>
                                <?php echo $__env->make('application/admin/office_activity/upload-signed-letter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <!-- For Deputy L&do Role END************************************************************************************ -->


    <!-- For Section Officer Role START************************************************************************************ -->
    <?php if($roles === 'section-officer'): ?>
        <?php if($showCreateLetterButtons): ?>
            <?php if($application->letter): ?>
                <div class="col-lg-8 mt-4">
                    <button type="button" class="btn btn-success"
                        onclick="handleApplicationAction('LETTER_GEN','<?php echo e($details->application_no); ?>',this)">Regenerate
                        Draft NOC</button>
                </div>
            <?php else: ?>
                
                    <div class="col-lg-8 mt-4">
                        <button type="button" class="btn btn-success"
                            onclick="handleApplicationAction('LETTER_GEN','<?php echo e($details->application_no); ?>',this)">Generate Draft
                            NOC</button>
                    </div>
                
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <!-- For Section Officer Role END************************************************************************************ -->






</div>

<!-- All Action Buttons Started START *********************************************************************************** -->

<div class="row">
    <div class="d-flex justify-content-end gap-4 col-lg-12" id="action-btn-container">
        <?php if(
            ($roles === 'section-officer' || $roles === 'deputy-lndo') &&
                $showActionButtons &&
                empty($application->letter) &&
                !isset($latestAppAction)): ?>
            <button type="button" onclick="handleApplicationAction('OBJECT','<?php echo e($details->application_no); ?>',this)"
                class="btn btn-warning">Object</button>
        <?php endif; ?>
        
        <?php if($showActionButtons): ?>
            <?php if($roles === 'section-officer'): ?>
                <?php if($application->letter): ?>
                    <button type="button" class="btn btn-primary"
                        onclick="handleApplicationAction('RECOMMENDED','<?php echo e($details->application_no); ?>',this)">Recommend</button>
                    <button type="button"
                        onclick="handleApplicationAction('OBJECT','<?php echo e($details->application_no); ?>',this)"
                        class="btn btn-warning">Object</button>
                <?php endif; ?>
            <?php endif; ?>


            <?php if($roles === 'deputy-lndo'): ?>
                <?php if($showApproveButton): ?>
                    <button type="button" class="btn btn-primary"
                        onclick="handleApplicationAction('APPROVE','<?php echo e($details->application_no); ?>',this)">Approve</button>
                <?php elseif(isset($latestAppAction) && ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT')): ?>
                    <button type="button" class="btn btn-primary"
                        onclick="handleApplicationAction('RECOMMENDED','<?php echo e($details->application_no); ?>',this)">Recommend</button>
                    <button type="button" class="btn btn-warning"
                        onclick="handleApplicationAction('OBJECT','<?php echo e($details->application_no); ?>',this)">Object</button>
                <?php endif; ?>
                <?php if(isset($latestAppAction) && ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT')): ?>
                    <button type="button" class="btn btn-danger"
                        onclick="handleApplicationAction('REJECT_APP','<?php echo e($details->application_no); ?>',this)">Reject</button>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/admin/office_activity/noc.blade.php ENDPATH**/ ?>