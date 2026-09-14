<?php if(isset($downloading)): ?>
<div class="part-title mt-2">
    <h5>Details of Documents</h5>
</div>
<div class="part-details">
    <div class="container-fluid pb-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered theme-table" style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <thead>
                            <tr style="background-color: #f8f9fa; border: 1px solid #000;">
                                <th width="2%" style="border: 1px solid #000; padding: 8px; text-align: center;">S.No</th>
                                <th style="border: 1px solid #000; padding: 8px;">Document Name</th>
                                <?php if($roles != 'applicant'): ?>
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center;">Action by SO</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Required Documents Section -->
                            <tr>
                                <?php
                                    $colspan = $roles != 'applicant' ? 3 : 2;
                                ?>
                                <td colspan="<?php echo e($colspan); ?>" style="border: 1px solid #000; padding: 10px; background-color: #e9ecef; font-weight: bold;">
                                    <h4 style="margin: 0; font-size: 16px;">Required Documents</h4>
                                </td>
                            </tr>
                            
                            <?php
                                $stepTwoDocs = config('applicationDocumentType.NOC.Required');
                                $counter = 0;
                            ?>
                            
                            <?php $__currentLoopData = $stepTwoDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $uploadedDocuments = [];
                                    if (!empty($details->documentFinal)) {
                                        $uploadedDocuments = $details->documentFinal
                                            ->where('document_type', $document['id'])
                                            ->all();
                                    }
                                ?>
                                
                                <?php $__currentLoopData = $uploadedDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="border: 1px solid #000;">
                                        <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;"><?php echo e(++$counter); ?>.</td>
                                        <td style="border: 1px solid #000; padding: 8px; vertical-align: top;">
                                            <div style="margin-bottom: 5px; font-weight: bold;">
                                                <?php echo e($ud['title']); ?>

                                            </div>
                                            
                                            <?php if($ud->documentKeys->count() > 0): ?>
                                                <div style="margin-top: 8px; font-size: 12px;">
                                                    <?php $__currentLoopData = $ud->documentKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if(isset($document['inputs'][$data->key]['label'])): ?>
                                                            <?php
                                                                $value = $data->value;
                                                                $isDate = strtotime($value) !== false;
                                                                if($isDate){
                                                                    try{
                                                                        $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                    } catch (\Exception $e){
                                                                        $value = $data->value;
                                                                    }
                                                                }
                                                            ?>
                                                            <div style="margin-bottom: 2px;">
                                                                <strong><?php echo e($document['inputs'][$data->key]['label']); ?>:</strong> <?php echo e($value); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <?php if($roles != 'applicant'): ?>
                                            <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;">
                                                <?php if($checkList && $checkList->is_uploaded_doc_checked == 1): ?>
                                                    <span style="color: green; font-weight: bold;">✓ Checked</span>
                                                <?php else: ?>
                                                    <span style="color: #6c757d;">Not Checked</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <!-- Additional Documents By Applicant Section -->
                            <tr>
                                <td colspan="<?php echo e($colspan); ?>" style="border: 1px solid #000; padding: 10px; background-color: #e9ecef; font-weight: bold;">
                                    <h4 style="margin: 0; font-size: 16px;">Additional Documents By Applicant</h4>
                                </td>
                            </tr>
                            
                            <?php
                                $applicantAdditionalDocuments = [];
                                $counter = 0;
                                if (!empty($details->documentFinal)) {
                                    $applicantAdditionalDocuments = $details->documentFinal
                                        ->where('document_type', 'AdditionalDocument')
                                        ->whereNotNull('file_path')
                                        ->all();
                                }
                                $uplodedDocCount = count($applicantAdditionalDocuments);
                            ?>
                            
                            <?php if($uplodedDocCount > 0): ?>
                                <?php $__currentLoopData = $applicantAdditionalDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr style="border: 1px solid #000;">
                                        <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;"><?php echo e(++$counter); ?>.</td>
                                        <td style="border: 1px solid #000; padding: 8px; vertical-align: top;">
                                            <div style="margin-bottom: 5px; font-weight: bold;">
                                                <?php echo e($ud['title']); ?>

                                            </div>
                                            
                                            <?php if($ud->documentKeys->count() > 0): ?>
                                                <div style="margin-top: 8px; font-size: 12px;">
                                                    <?php $__currentLoopData = $ud->documentKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if(isset($document['inputs'][$data->key]['label'])): ?>
                                                            <?php
                                                                $value = $data->value;
                                                                $isDate = strtotime($value) !== false;
                                                                if($isDate){
                                                                    try{
                                                                        $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                    } catch (\Exception $e){
                                                                        $value = $data->value;
                                                                    }
                                                                }
                                                            ?>
                                                            <div style="margin-bottom: 2px;">
                                                                <strong><?php echo e($document['inputs'][$data->key]['label']); ?>:</strong> <?php echo e($value); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <?php if($roles != 'applicant'): ?>
                                            <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;">
                                                <?php if($checkList && $checkList->is_uploaded_doc_checked == 1): ?>
                                                    <span style="color: green; font-weight: bold;">✓ Checked</span>
                                                <?php else: ?>
                                                    <span style="color: #6c757d;">Not Checked</span>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?php echo e($colspan); ?>" style="border: 1px solid #000; padding: 15px; text-align: center;">
                                        <p style="margin: 0; color: #6c757d;">No Documents Available</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>

<div class="part-title mt-2">
    <h5>Details of Documents</h5>
</div>
<div class="part-details">
    <div class="container-fluid pb-3">
        <div class="row">
            <div class="col-lg-12">

                <div class="table-responsive">
                    <table class="table table-bordered theme-table">
                        <thead>
                            <tr>
                                <th width="2%">S.No</th>
                                <th>Document Name</th>
                                <?php if($roles != 'applicant'): ?>
                                    <th>Action by SO</th>
                                <?php endif; ?>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="document-type-row">
                                    <h4 class="doc-type-title">Required Documents</h4>
                                </td>
                            </tr>
                            <?php
                                $stepTwoDocs = config('applicationDocumentType.NOC.Required');
                                $counter = 0;
                            ?>
                            <?php $__currentLoopData = $stepTwoDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $uploadedDocuments = [];
                                    if (!empty($details->documentFinal)) {
                                        $uploadedDocuments = $details->documentFinal
                                            ->where('document_type', $document['id'])
                                            ->all();
                                    }
                                    $uplodedDocCount = count($uploadedDocuments);
                                ?>
                                <?php $__currentLoopData = $uploadedDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr id="<?php echo e($ud['id']); ?>">
                                        <td><?php echo e(++$counter); ?>.</td>
                                        <td>
                                            <span class="doc-name">
                                                <?php echo e($ud['title']); ?> <a
                                                    href="<?php echo e(asset('storage/' . $ud['file_path'] ?? '')); ?>"
                                                    target="_blank" class="text-danger"><i
                                                        class="fa-solid fa-file-pdf ml-2"></i></a>
                                            </span>



                                            <?php if($ud->documentKeys->count() > 0): ?>
                                                <div class="required-info">
                                                    <ul class="required-info-list">
                                                        <?php $__currentLoopData = $ud->documentKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(isset($document['inputs'][$data->key]['label'])): ?>

                                                                <?php
                                                                    $value = $data->value;
                                                                    $isDate = strtotime($value) !== false;
                                                                    if($isDate){
                                                                        try{
                                                                            $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                        } catch (\Exception $e){
                                                                            $value = $data->value;
                                                                        }
                                                                    }
                                                                ?>
                                                                <li><?php echo e($document['inputs'][$data->key]['label']); ?>:
                                                                    <?php echo e($data->value); ?></li>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <?php if($roles != 'applicant'): ?>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input required-for-approve property-document-approval-chk"
                                                        type="checkbox" name="checkedAction" id="checkedAction"
                                                        <?php if($checkList && $checkList->is_uploaded_doc_checked == 1): ?> checked disabled <?php endif; ?>
                                                        <?php if($roles != 'section-officer' &&
                                                                 $roles != 'assistant-section-officer' &&
                                                                 auth()->user()->can('can.approve.application.mis.scanned.document')): ?> disabled <?php endif; ?>>
                                                    <label class="form-check-label" for="checkedAction">Checked</label>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <tr>
                                <td colspan="3" class="document-type-row">
                                    <h4 class="doc-type-title">Additional Documents By Applicant</h4>
                                </td>
                            </tr>
                            <?php
                                $applicantAdditionalDocuments = [];
                                $counter = 0;
                                if (!empty($details->documentFinal)) {
                                    $applicantAdditionalDocuments = $details->documentFinal
                                        ->where('document_type', 'AdditionalDocument')
                                        ->whereNotNull('file_path')
                                        ->all();
                                }
                                $uplodedDocCount = count($applicantAdditionalDocuments);
                            ?>
                            <?php if($uplodedDocCount > 0): ?>
                                <?php $__currentLoopData = $applicantAdditionalDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ud): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr id="<?php echo e($ud['id']); ?>">
                                        <td><?php echo e(++$counter); ?>.</td>


                                        <td>
                                            <span class="doc-name"><?php echo e($ud['title']); ?> <a
                                                    href="<?php echo e(asset('storage/' . $ud['file_path'] ?? '')); ?>"
                                                    target="_blank" class="text-danger"><i
                                                        class="fa-solid fa-file-pdf ml-2"></i></a></span>
                                            <?php if($ud->documentKeys->count() > 0): ?>
                                                <div class="required-doc">
                                                    <ul class="required-list">
                                                        <?php $__currentLoopData = $ud->documentKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if(isset($document['inputs'][$data->key]['label'])): ?>

                                                                <?php
                                                                    $value = $data->value;
                                                                    $isDate = strtotime($value) !== false;
                                                                    if($isDate){
                                                                        try{
                                                                            $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                        } catch (\Exception $e){
                                                                            $value = $data->value;
                                                                        }
                                                                    }
                                                                ?>
                                                                <li><?php echo e($document['inputs'][$data->key]['label']); ?>:
                                                                    <?php echo e($data->value); ?></li>
                                                            <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <?php if($roles != 'applicant'): ?>
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input required-for-approve property-document-approval-chk"
                                                        type="checkbox" name="checkedAction" id="checkedAction"
                                                        <?php if($checkList && $checkList->is_uploaded_doc_checked == 1): ?> checked disabled <?php endif; ?>
                                                        <?php if($roles != 'section-officer' &&
                                                                 $roles != 'assistant-section-officer' &&
                                                                 auth()->user()->can('can.approve.application.mis.scanned.document')): ?> disabled <?php endif; ?>>
                                                    <label class="form-check-label" for="checkedAction">Checked</label>
                                                </div>
                                            </td>
                                        <?php endif; ?>


                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="">
                                        <p class="text-center">No Documents Available</p>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/admin/application_document/noc.blade.php ENDPATH**/ ?>