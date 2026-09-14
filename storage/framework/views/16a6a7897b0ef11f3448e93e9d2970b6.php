<div class="mt-3">
    <div class="container-fluid">
        <div class="row g-2">
            <div class="col-lg-12">

                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                <?php
                    $stepTwoDocs = config('applicationDocumentType.DOA.documents');
                ?>
                <?php $__currentLoopData = $stepTwoDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $uploadeddocsWithDocType = isset($stepSecondFinalDocuments)
                            ? collect($stepSecondFinalDocuments)->where('document_type', $document['id'])->all()
                            : [];
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $uploadeddocsWithDocType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $uploadeddocsWithDoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="row row-mb-2">
                            <div class="col-lg-1 icons-flex"></div>
                            <div class="col-lg-11 selected-docs-field">
                                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <label for="<?php echo e($document['id']); ?>"
                                                class="quesLabel"><?php echo e($document['label']); ?>

                                                <?php if($document['id'] != 'OtherDocument'): ?>
                                                    <span class="text-danger">*</span>
                                                <?php endif; ?>
                                            </label>
                                            <input type="file" name="<?php echo e($document['id']); ?>" class="form-control <?php echo e($document['id'] == 'OtherDocument' ? 'otherDocuemntByApplicant' : ''); ?>"
                                                accept="application/pdf" id="<?php echo e($document['id']); ?>"
                                                onchange="handleFileUpload(this.files[0], '<?php echo e($document['label']); ?>', '<?php echo e($document['id']); ?>', 'deed_of_apartment', 'DOA')"
                                                data-name="<?php echo e($document['id']); ?>"
                                                data-should-validate="<?php echo e(isset($uploadeddocsWithDoc->file_path) ? '1' : ''); ?>">
                                            <input type="hidden" value="<?php echo e($uploadeddocsWithDoc->id); ?>"
                                                name="<?php echo e($uploadeddocsWithDoc->id); ?>"
                                                data-name="<?php echo e($document['id']); ?>_oldId" data-repeaterId="id">
                                            <div id="<?php echo e($document['id']); ?>Error" class="text-danger text-left"></div>
                                            <a href="<?php echo e(asset('storage/' . $uploadeddocsWithDoc->file_path ?? '')); ?>"
                                                data-document-type="<?php echo e($document['id']); ?>" target="_blank"
                                                class="fs-6">View saved document</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="row row-mb-2">
                            <div class="col-lg-1 icons-flex"></div>
                            <div class="col-lg-11 selected-docs-field">
                                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <label for="<?php echo e($document['id']); ?>"
                                                class="quesLabel"><?php echo e($document['label']); ?>

                                                <?php if($document['id'] != 'OtherDocument'): ?>
                                                    <span class="text-danger">*</span>
                                                <?php endif; ?>
                                            </label>
                                            <input type="file" name="<?php echo e($document['id']); ?>" class="form-control <?php echo e($document['id'] == 'OtherDocument' ? 'otherDocuemntByApplicant' : ''); ?>" 
                                                accept="application/pdf" id="<?php echo e($document['id']); ?>"
                                                onchange="handleFileUpload(this.files[0], '<?php echo e($document['label']); ?>', '<?php echo e($document['id']); ?>', 'deed_of_apartment', 'DOA')"
                                                data-name="<?php echo e($document['id']); ?>">
                                            <div id="<?php echo e($document['id']); ?>Error" class="text-danger text-left"></div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="row row-mb-2">
                <div class="col-lg-12">
                    <h6 class="mt-3 mb-0" id="LUCHideTitle">Terms & Conditions</h6>
                    <ul class="consent-agree">

                        <li>
                            Processing fee <?php echo e(getApplicationCharge(getServiceType('DOA'))); ?> /- Rs. is non-refundable.
                        </li>
                    </ul>
                    <div class="form-check form-group">
                        <?php if(isset($application)): ?>
                            <input class="form-check-input" name="agreeConsent" type="checkbox" id="agreeDOAConsent">
                        <?php else: ?>
                            <input class="form-check-input" name="agreeConsent" type="checkbox" id="agreeDOAConsent">
                        <?php endif; ?>

                        <label class="form-check-label" for="doaagreeconsent">I agree, all the
                            information provided by me is accurate to the best of my knowledge. I
                            take full responsibility for any issues or failures that may arise from
                            its use.</label>

                        <div id="agreeDOAConsentError" class="text-danger text-left"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/deed_of_apartment/include/step-2.blade.php ENDPATH**/ ?>