<div class="mt-3">
    <div class="container-fluid">
        <div class="row g-2">
            <div class="col-lg-12">
                <?php
                $inputGroups = config('applicationDocumentType.CONVERSION.optional.groups');
                $isLeaseDeedLost= isset($application) && $application->is_Lease_deed_lost==1 ? 1:0;
                ?>
                <?php $__currentLoopData = $inputGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="row row-mb-2">
                    <div class="col-lg-1 icons-flex"></div>
                    <div class="col-lg-11 selected-docs-field">
                        <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                        <div class="row pb-2">
                            <?php if(isset($ig['input']) && $ig['input']['type']=='radio'): ?>
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center">
                                    <h6 class="mr-5 mb-0"><?php echo e($ig['input']['label']); ?></h6>
                                    <?php $__currentLoopData = $ig['input']['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check mr-5">
                                        <input class="form-check-input" name="<?php echo e($ig['input']['name']); ?>" type="radio"
                                            value="<?php echo e($opt['value']); ?>" id="<?php echo e($ig['input']['name'].$opt['label']); ?>" <?php echo e($isLeaseDeedLost == $opt['value'] ? 'checked':''); ?>>
                                        <label class="form-check-label" for="<?php echo e($ig['input']['name'].$opt['label']); ?>">
                                            <h6 class="mb-0"><?php echo e($opt['label']); ?></h6>
                                        </label>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if(isset($ig['input'])): ?>
                            <!-- added class mt-2 for careate spacing between title and label by anil on 17-04-2025 -->
                            <div class="col-lg-12 mt-2" id="optionalInputs" style="display: <?php echo e($isLeaseDeedLost ? 'block':'none'); ?>;">
                                <div class="row">
                                    <?php endif; ?>
                                    <?php $__currentLoopData = $ig['documents']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php 
                                    $uploadedDocument = null;
                                    if(isset($application)&& $application->tempDocument){
                                        $uploadedDocument = $application->tempDocument->where('document_type',$document['id'])->first();
                                    }
                                        
                                    ?>
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <!-- added class form-label by anil on 17-04-2025 -->
                                            <label for="<?php echo e($document['id']); ?>" class="form-label"><?php echo e($document['label']); ?> 
                                                <?php if($document['id'] == 'documentpowerofattorney' || $document['id'] == 'convOptLeaseLostAffidevit' || $document['id'] == 'convLeaseLostAffidevitDocumentDate' || $document['id'] == 'convLeaseLostAffidevitAttestedBy' || $document['id'] == 'convOptLeaseLostPublicNotice' || $document['id'] == 'convOptLeaseLostPublicNoticeNameOfNewspaper'): ?>
                                                    <span class="text-danger">*</span>
                                                <?php endif; ?>

                                            </label><!-- class="quesLabel" data-toggle="tooltip" data-placement="top" title="Affidavit to the effect that the Lessee is alive" -->
                                            <input type="file" name="<?php echo e($document['id']); ?>" class="form-control"
                                                accept="application/pdf" id="<?php echo e($document['id']); ?>" onchange="handleFileUpload(this.files[0], '<?php echo e($document['label']); ?>','<?php echo e($document['id']); ?>', 'conversion', 'CONVERSION')" 
                                                data-should-validate = "<?php echo e(isset($uploadedDocument) && $uploadedDocument->file_path != ''); ?>">
                                                <?php if($uploadedDocument): ?>
                                                <a href="<?php echo e(asset('storage/'.$uploadedDocument->file_path)); ?>" target="_blank" class="fs-6">View Saved Document</a>
                                                <?php endif; ?>
                                            <div id="<?php echo e($document['id']); ?>Error" class="text-danger text-left">
                                            </div>
                                        </div>
                                    </div>
                                    <?php $__currentLoopData = $document['inputs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docInput): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $documentKey = $value = null;
                                        if(isset($uploadedDocument)){
                                            $documentKey = $uploadedDocument->tempDocumentKeys->where('key',$docInput['id'])->first();
                                            $value = !empty($documentKey) ? $documentKey->value :'';
                                        }
                                    ?>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <!-- added class form-label by anil on 17-04-2025 -->
                                            <label for="<?php echo e($docInput['id']); ?>" class="form-label"><?php echo e($docInput['label']); ?>

                                            <?php if($document['id'] == 'documentpowerofattorney' || $document['id'] == 'convOptLeaseLostAffidevit' || $document['id'] == 'convLeaseLostAffidevitDocumentDate' || $document['id'] == 'convLeaseLostAffidevitAttestedBy' || $document['id'] == 'convOptLeaseLostPublicNotice' || $document['id'] == 'convOptLeaseLostPublicNoticeNameOfNewspaper'): ?>
                                                    <span class="text-danger">*</span>
                                                <?php endif; ?>

                                            </label>
                                            <input type="<?php echo e($docInput['type']); ?>" name="<?php echo e($docInput['id']); ?>"
                                                class="form-control" id="<?php echo e($docInput['id']); ?>" value="<?php echo e($value); ?>">
                                                <div id="<?php echo e($docInput['id']); ?>Error" class="text-danger text-left"></div>
                                        </div>
                                    </div>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($ig['input'])): ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <!--  <div class="row row-mb-2">
                    <div class="col-lg-1 icons-flex"></div>
                    <div class="col-lg-11 selected-docs-field">
                        <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                        <div class="row pb-2">
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center">
                                    <h6 class="mr-5 mb-0">Where the Lease deed is lost?</h6>
                                    <div class="form-check mr-5">
                                        <input class="form-check-input" name="DeedLostConversion" type="radio"
                                            value="Yes" id="YesDeedLostConversion">
                                        <label class="form-check-label" for="YesDeedLostConversion">
                                            <h6 class="mb-0">Yes</h6>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" name="DeedLostConversion" type="radio"
                                            value="No" id="NoDeedLostConversion" checked>
                                        <label class="form-check-label" for="NoDeedLostConversion">
                                            <h6 class="mb-0">No</h6>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="yesDeedLostDivConversion" style="display: none;">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <label for="AffidavitsConversiondeedlost" class="quesLabel" data-toggle="tooltip" data-placement="top" title="Affidavit for Lease Deed is Lost">Affidavits <span><i class='bx bx-info-circle'></i></span></label>
                                            <input type="file" name="AffidavitsConversiondeedlost" class="form-control"
                                                accept="application/pdf" id="AffidavitsConversiondeedlost">
                                            <div id="AffidavitsConversiondeedlostError" class="text-danger text-left">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="dateattestationConversiondeedlost">Date of Document</label>
                                            <input type="date" name="dateattestationConversiondeedlost"
                                                class="form-control" id="dateattestationConversiondeedlost">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="attestedbyConversiondeedlost">Issuing Authority</label>
                                            <input type="text" name="attestedbyConversiondeedlost"
                                                class="form-control alpha-only" id="attestedbyConversiondeedlost"
                                                placeholder="Issuing Authority">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <label for="publicnoticeenhinLeaseDeed" class="quesLabel">Public
                                                Notice in National Daily (English &amp; Hindi)</label>
                                            <input type="file" name="publicnoticeenhinLeaseDeed" class="form-control" accept="application/pdf" id="publicnoticeenhinLeaseDeed">
                                            <div id="publicnoticeenhinLeaseDeedError" class="text-danger text-left"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="newspapernameengligh">Name of Newspaper
                                                (English or Hindi)</label>
                                            <input type="text" name="newspapernameengligh" class="form-control alpha-only" id="newspapernameengligh" placeholder="Name of Newspaper (English)">
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="publicnoticedate">Date of Public Notice<span class="text-danger">*</span></label>
                                                                    <input type="date" name="publicnoticedate" class="form-control" id="publicnoticedate">
                                                                </div>
                                                            </div> --
            </div>
        </div>
    </div>
</div>
</div>

<div class="row row-mb-2">
    <div class="col-lg-1 icons-flex"></div>
    <div class="col-lg-11 selected-docs-field">
        <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>

    </div>
</div> -->
            </div>


        </div>

        <div class="row mt-2">
            <div class="col-lg-12">
                <h6 class="mt-3 mb-0">Terms & Conditions</h6>
                <!-- <ul class="consent-agree"> -->
                    Processing fee of Rs.<?php echo e(getApplicationCharge(getServiceType('CONVERSION'))); ?> /- is non-refundable.
                <!-- </ul> -->
                <div class="form-check form-group">
                    <input class="form-check-input" type="checkbox" value=""
                        id="agreeConsentConversion" <?php echo e(isset($application) && $application->consent ? 'checked':''); ?>>
                    <label class="form-check-label" for="agreeconsent">I/we agree, all the
                        information provided is accurate. I
                        take full responsibility for any issues or failures that may arise from
                        its use.</label>
                        <div id="agreeConsentConversionError" class="text-danger text-left"></div>
                </div>

            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/conversion/include/step-3.blade.php ENDPATH**/ ?>