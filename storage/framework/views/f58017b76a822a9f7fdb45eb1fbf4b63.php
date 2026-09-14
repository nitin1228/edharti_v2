    

    <div class="mt-3">
        <div class="container-fluid">
            <div class="row g-2">
                <div class="col-lg-12">

                    <?php
                        $stepTwoDocs = config('applicationDocumentType.NOC.Required');
                        $isIndian = false;
                    ?>
                    <?php $__currentLoopData = $stepTwoDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            /**UPLOADED DOCS MATCHING $document['id'] */
                            $uploadeddocsWithDocType = isset($stepSecondFinalDocuments)
                                ? collect($stepSecondFinalDocuments)->where('document_type', $document['id'])->all()
                                : [];
                        ?>

                        <?php if(
                            !empty($userDetails->applicantUserDetails->isIndian) &&
                                $userDetails->applicantUserDetails->isIndian == 1 &&
                                ($document['id'] == 'conveyanceaadhardoc' || $document['id'] == 'conveyancepandoc')): ?>
                            <?php
                                $isIndian = true;
                            ?>
                        <?php endif; ?>


                        <?php if($isIndian): ?>
                            <?php if($document['id'] == 'nocNriUploadDocument'): ?>
                                <?php continue; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if($document['id'] == 'conveyanceaadhardoc' || $document['id'] == 'conveyancepandoc'): ?>
                                <?php continue; ?>
                            <?php endif; ?>
                        <?php endif; ?>




                        <div class="row row-mb-2">
                            <div class="col-lg-1 icons-flex"></div>
                            <div class="col-lg-11 selected-docs-field">
                                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                                <?php if($document['multiple']): ?>
                                    <div id="<?php echo e($document['id']); ?>_repeater" class="position-relative doc-items">
                                        <div class="position-sticky text-end mt-2 <?php echo e($document['multiple']); ?>"
                                            style="top: 70px; margin-right: 10px; margin-bottom: 10px; z-index: 9;">
                                            <button type="button" class="btn btn-primary repeater-add-btn"
                                                data-toggle="tooltip" data-placement="bottom"
                                                title="Click here to add more co-applicant."><i
                                                    class="bx bx-plus me-0"></i></button>
                                        </div>
                                <?php endif; ?>


                                <!-- Repeater Items -->
                                <div class="duplicate-field-tab">
                                    <?php $__empty_1 = true; $__currentLoopData = $uploadeddocsWithDocType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$uploadeddocsWithDoc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="<?php echo e($document['multiple'] == 1 ? 'items' : ''); ?>"
                                            data-group="<?php echo e($document['id']); ?>" data-type="document"
                                            data-document-type="<?php echo e($document['id']); ?>">
                                            <!-- Repeater Content -->
                                            <div class="item-content mb-2">
                                                <input type="hidden" data-name="indexValue" value="">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="form-group form-box">
                                                            <!-- added class form-label by anil on 17-04-2025 -->
                                                            <label for="<?php echo e($document['id']); ?>"
                                                                class="quesLabel form-label">
                                                                <?php echo e($document['label'] == 'Property Photo' ? $document['label'] . ' (Showing the use of property)' : $document['label']); ?>

                                                                <?php if($document['id'] != 'otherDocumentbyApplicant' && $document['id'] != 'conveyancedeed'): ?>
                                                                    <span class="text-danger">*</span>
                                                                <?php endif; ?>
                                                                
                                                            </label>

                                                            <input type="file" name="<?php echo e($document['id']); ?>"
                                                                class="form-control" accept="application/pdf"
                                                                id="<?php echo e($document['id']); ?>"
                                                                <?php if(!$document['multiple']): ?> onchange="handleFileUpload(this.files[0], '<?php echo e($document['label']); ?>', '<?php echo e($document['id']); ?>', 'noc', 'NOC')" <?php endif; ?>
                                                                data-name="<?php echo e($document['id']); ?>"
                                                                data-should-validate="<?php echo e(isset($uploadeddocsWithDoc->file_path) ? '1' : ''); ?>">
                                                            <input type="hidden"
                                                                value="<?php echo e($uploadeddocsWithDoc->id); ?>"
                                                                name="<?php echo e($uploadeddocsWithDoc->id); ?>"
                                                                data-name="<?php echo e($document['id']); ?>_oldId"
                                                                data-repeaterId="id">
                                                            <div id="<?php echo e($document['id']); ?>Error"
                                                                class="text-danger text-left"></div>
                                                            <a href="<?php echo e(asset('storage/' . $uploadeddocsWithDoc->file_path ?? '')); ?>"
                                                                data-document-type="<?php echo e($document['id']); ?>"
                                                                target="_blank" class="fs-6">View Saved
                                                                Document</a>
                                                        </div>
                                                    </div>
                                                    <?php
                                                        $count = 1;
                                                        $length = count($document['inputs']);
                                                    ?>

                                                    <?php $__currentLoopData = $document['inputs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $input): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $oldValue = '';
                                                            $id = '';
                                                            if ($uploadeddocsWithDoc) {
                                                                $values = $uploadeddocsWithDoc->values;
                                                                if ($values) {
                                                                    $value = collect($values)
                                                                        ->where('key', $input['id'])
                                                                        ->first();
                                                                    if ($value) {
                                                                        $oldValue = $value->value;
                                                                        $id = $value->id;
                                                                    }
                                                                }
                                                            }

                                                        ?>
                                                        <div
                                                            class="col-lg-4 <?php echo e($document['multiple'] && $count === $length ? 'mix-field' : ''); ?>">
                                                            <div class="form-group">
                                                                <!-- added class form-label by anil on 17-04-2025 -->
                                                                <label for="<?php echo e($input['id']); ?>" class="form-label">
                                                                    <?php echo e($input['label']); ?><span
                                                                        class="text-danger">*</span>
                                                                </label>

                                                                <?php if($input['type'] == 'select'): ?>
                                                                    <select name="<?php echo e($input['id']); ?>"
                                                                        id="<?php echo e($input['id']); ?>" class="form-select">
                                                                        <option value="">Select</option>
                                                                        <?php $__currentLoopData = $input['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <option value="<?php echo e($option); ?>"
                                                                                <?php echo e(isset($uploadedValue) && $uploadedValue->value == $option ? 'selected' : ''); ?>>
                                                                                <?php echo e($option); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </select>
                                                                    <div id="<?php echo e($input['id']); ?>Error"
                                                                        class="text-danger text-left"></div>
                                                                <?php else: ?>
                                                                    <input type="<?php echo e($input['type']); ?>"
                                                                        name="<?php echo e($input['id']); ?>" class="form-control"
                                                                        id="<?php echo e($input['id']); ?>"
                                                                        data-name="<?php echo e($input['id']); ?>"
                                                                        value="<?php echo e($oldValue); ?>">
                                                                    <input type="hidden" value="<?php echo e($id); ?>"
                                                                        name="<?php echo e($id); ?>"
                                                                        data-name="<?php echo e($input['id']); ?>_oldId">
                                                                    <div id="<?php echo e($input['id']); ?>Error"
                                                                        class="text-danger text-left">
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>

                                                            <?php if($document['multiple'] && $count === $length): ?>
                                                                <div class="repeater-remove-btn"
                                                                    style="margin-bottom: 0px;">
                                                                    <button type="button"
                                                                        class="btn-invisible remove-btn px-4"
                                                                        data-toggle="tooltip" data-placement="bottom"
                                                                        title="Click here to delete this document.">
                                                                        <i class="fadeIn animated bx bx-trash"></i>
                                                                    </button>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php
                                                                $count++;
                                                            ?>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                                        <div class="<?php echo e($document['multiple'] == 1 ? 'items' : ''); ?>"
                                            data-group="<?php echo e($document['id']); ?>" data-type="document"
                                            data-document-type="<?php echo e($document['id']); ?>">
                                            <!-- Repeater Content -->
                                            <div class="item-content mb-2">
                                                <input type="hidden" name="indexValue" value="">
                                                <div class="row">
                                                    <div class="col-lg-5">
                                                        <div class="form-group form-box">
                                                            <!-- added class form-label by anil on 17-04-2025 -->
                                                            <label for="<?php echo e($document['id']); ?>"
                                                                class="quesLabel form-label">
                                                                <?php echo e($document['label'] == 'Property Photo' ? $document['label'] . ' (Showing the use of property)' : $document['label']); ?>

                                                                <?php if($document['id'] != 'otherDocumentbyApplicant'): ?>
                                                                    <span class="text-danger">*</span>
                                                                <?php endif; ?>
                                                            </label>
                                                            <input type="file" name="<?php echo e($document['id']); ?>"
                                                                class="form-control" accept="application/pdf"
                                                                id="<?php echo e($document['id']); ?>"
                                                                <?php if(!$document['multiple']): ?> onchange="handleFileUpload(this.files[0], '<?php echo e($document['label']); ?>', '<?php echo e($document['id']); ?>', 'noc', 'NOC')" <?php endif; ?>
                                                                data-name="<?php echo e($document['id']); ?>">
                                                            <input type="hidden" value=""
                                                                name="<?php echo e($document['id']); ?>"
                                                                data-name="<?php echo e($document['id']); ?>_oldId"
                                                                data-repeaterId="id">
                                                            <div id="<?php echo e($document['id']); ?>Error"
                                                                class="text-danger text-left"></div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                        $count = 1;
                                                        $length = count($document['inputs']);
                                                    ?>

                                                    <?php $__currentLoopData = $document['inputs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $input): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div
                                                            class="col-lg-4 <?php echo e($document['multiple'] && $count === $length ? 'icon-feild' : ''); ?>">

                                                            <div class="form-group">
                                                                <!-- added class form-label by anil on 17-04-2025 -->
                                                                <label for="<?php echo e($input['id']); ?>" class="form-label">
                                                                    <?php echo e($input['label']); ?><span
                                                                        class="text-danger">*</span>
                                                                </label>
                                                                <?php if($input['type'] == 'select'): ?>
                                                                    <select name="<?php echo e($input['id']); ?>"
                                                                        id="<?php echo e($input['id']); ?>" class="form-select">
                                                                        <option value="">Select</option>
                                                                        <?php $__currentLoopData = $input['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <option value="<?php echo e($option); ?>"
                                                                                <?php echo e(isset($uploadedValue) && $uploadedValue->value == $option ? 'selected' : ''); ?>>
                                                                                <?php echo e($option); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </select>
                                                                    <div id="<?php echo e($input['id']); ?>Error"
                                                                        class="text-danger text-left"></div>
                                                                <?php else: ?>
                                                                    <input type="<?php echo e($input['type']); ?>"
                                                                        name="<?php echo e($input['id']); ?>"
                                                                        class="form-control" id="<?php echo e($input['id']); ?>"
                                                                        data-name="<?php echo e($input['id']); ?>">
                                                                    <div id="<?php echo e($input['id']); ?>Error"
                                                                        class="text-danger text-left">
                                                                    </div>
                                                                <?php endif; ?>

                                                            </div>

                                                            <?php
                                                                $count++;
                                                            ?>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if($document['multiple']): ?>
                            </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12">
            <!-- <h6 class="mt-3 mb-0">Terms & Conditions</h6>
            <ul class="consent-agree">
                <li> Processing fee of Rs.<?php echo e(getApplicationCharge(getServiceType('NOC'))); ?> /- is non-refundable.
                </li>
            </ul> -->
            <div class="form-check form-group">
                <?php if(isset($application)): ?>
                    <input class="form-check-input" name="agreeConsentNoc" type="checkbox" id="agreeConsentNoc">
                <?php else: ?>
                    <input class="form-check-input" name="agreeConsentNoc" type="checkbox" id="agreeConsentNoc">
                <?php endif; ?>

                <label class="form-check-label" for="agreeconsent">I/we agree, all the
                    information provided is accurate. I
                    take full responsibility for any issues or failures that may arise from
                    its use.</label>

                <div id="agreeConsentNocError" class="text-danger text-left"></div>
            </div>
        </div>
    </div>
    </div>
    </div>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/noc/include/step-2.blade.php ENDPATH**/ ?>