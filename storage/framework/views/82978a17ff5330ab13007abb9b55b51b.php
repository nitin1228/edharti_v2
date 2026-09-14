<div class="mt-3">
    <div class="container-fluid">
        <div class="row g-2">
            <div class="col-lg-12">

                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                <?php
                $docInputs = config('applicationDocumentType.LUC.documents');
                $rowNum = 1;
                $maxRowNum = collect($docInputs)->max('rowOrder');
                ?>

                <?php for($i = $rowNum; $i <= $maxRowNum; $i++): ?> <?php $docsForOrder=collect($docInputs)->where('rowOrder',
                    $i)->all();
                    ?>
                    <?php if(count($docsForOrder) > 0): ?>
                    <div class="row row-mb-2">
                        <div class="col-lg-1 icons-flex"></div>
                        <div class="col-lg-11 selected-docs-field">
                            <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                            <div class="row">
                                <?php $__currentLoopData = $docsForOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-4">
                                    <div class="form-group form-box">
                                        <label for="<?php echo e($doc['id']); ?>" class="quesLabel"><?php echo e($doc['label']); ?>

                                            <?php if($doc['required'] == 1): ?>
                                            <span class="text-danger">*</span>
                                            <?php endif; ?>
                                            <?php if($doc['id'] == 'documentpowerofattorney'): ?>
                                                <span class="text-danger">*</span>
                                            <?php endif; ?>
                                            
                                        </label>
                                        <input type="file" name="<?php echo e($doc['id']); ?>" class="form-control"
                                            data-should-validate="<?php echo e(isset($stepSecondFinalDocuments) && isset($stepSecondFinalDocuments[$key]) ? 1 : 0); ?>"
                                            accept="application/pdf" id="<?php echo e($doc['id']); ?>"
                                            onchange="handleFileUpload(this.files[0], &quot;<?php echo e($doc['label']); ?>&quot;,&quot;<?php echo e($doc['id']); ?>&quot;,'LUC','LUC')">
                                        <div id="<?php echo e($doc['id']); ?>Error" class="text-danger text-left">
                                        </div>
                                    </div>
                                    <?php

                                    $showViewLink = false;
                                    $finalDocument = null;
                                    if (
                                    isset($stepSecondFinalDocuments) &&
                                    isset($stepSecondFinalDocuments[$key])
                                    ) {
                                    $finalDocument = $stepSecondFinalDocuments[$key];
                                    $showViewLink = true;
                                    }
                                    ?>
                                    <a href="<?php echo e(!is_null($finalDocument) ? asset('storage/' . $finalDocument['file_path']) : ''); ?>"
                                        target="_blank" class="fs-6 <?php echo e($showViewLink ? '' : 'd-none'); ?>">View
                                        saved document</a>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endfor; ?>

            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/luc/include/step-2.blade.php ENDPATH**/ ?>