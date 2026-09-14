<!-- <style>
    .mt-center{
        margin-top: 2.5rem;
    }
</style> -->
<div class="col-12">
    <div class="row flex-wrap g-3">
        <?php if(!empty($isApplicant)): ?>
        <div class="col-12 col-lg-6">
            <label for="colonyName" class="form-label">Select Property to Continue</label>
            <select class="form-control selectpicker" data-live-search="true" name="property" id="property" aria-label="Property">
                <option value="">Select</option>
                <?php $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($prop->old_property_id); ?>"><?php echo e($prop->known_as ?? $prop->block.'/'.$prop->plot.'/'.$prop->oldColony->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['colony_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="errorMsg"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="colonyIdError" class="text-danger"></div>
        </div>
        <?php else: ?>
        <div class="col-12 col-lg-3">
            <label for="colonyName" class="form-label">Colony Name (Present)</label>
            <select class="form-control selectpicker" data-live-search="true" name="colony_id" id="colony_id" aria-label="Colony Name (Present)">
                <option value="">Select</option>
                <?php $__currentLoopData = $colonies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colony): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($colony->id); ?>"><?php echo e($colony->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['colony_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="errorMsg"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="colonyIdError" class="text-danger"></div>
        </div>

        <div class="col-12 col-lg-2">
            <label for="LandType" class="form-label">Block</label>
            <select class="form-control selectpicker" id="block" name="block" aria-label="Default select example">
                <option value="">Select</option>

            </select>
            <?php $__errorArgs = ['block'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="errorMsg"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="blockError" class="text-danger"></div>
        </div>
        
        <div class="col-12 col-lg-3">
            <label for="LandType" class="form-label">Plot No./Flat No.</label>
            <select class=" form-control selectpicker" id="plot" name="plot" aria-label="Default select example">
                <option value="">Select</option>
            </select>
            
            <?php $__errorArgs = ['plot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="errorMsg"><?php echo e($message); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="plotError" class="text-danger"></div>
        </div>
        <?php endif; ?>
        <div class="col-12 col-lg-1 text-center">
            <!-- <h5 class="mt-center">OR</h5> -->
            <h5>OR</h5>

        </div>
        <div class="col col-lg-3">
            <label for="oldPropertyId" class="form-label">Search By Property Id</label>
            <input type="text" name="oldPropertyId" id="oldPropertyId" class="form-control" placeholder="Enter property id">
        </div>

        

    </div><!---end row-->
</div>
<div class="row g-3 mt-2" id="flatDiv" style="display: none;">
    <div class="col-12 col-lg-6">
        <label for="flat" class="form-label">Flat</label>
        <select class="form-control selectpicker" id="flat" name="flat" aria-label="Default select example">
            <option value="">Select</option>
        </select>
        <?php $__errorArgs = ['flat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="errorMsg"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <div id="flatError" class="text-danger"></div>
    </div>
</div>
<div class="row g-3 mt-2" id="knownAsDiv" style="display: none;">
    <!-- known as -->
    <div class="col-12 col-lg-6">
       <div style="
        background: #ffffff;
        padding: 10px 10px;
        border: 1px solid #ced4da;" class="form-group">
            <label class="form-label">Known as: &nbsp; &nbsp;<span id="known-as"></span></label>
       </div>
    </div>
<!-- //known as -->
</div>

<!-- <script src="<?php echo e(asset('assets/js/bootstrap-select.min.js')); ?>"></script> -->
<script>
    var selectedColonyId;
    var leaseHoldOnly = <?= isset($leaseHoldOnly) ? 1 : 0 ?>;
    var withOutLeaseHold = <?= isset($withOutLeaseHold) ? 1 : 0 ?>;
    $('#colony_id').change(function() {
        selectedColonyId = $(this).val();
        var targetSelect = $('#block')
        targetSelect.html('<option>Select</option>');
        targetSelect.selectpicker('refresh');
        var reponseUrl = "<?php echo e(route('blocksInColony', ['colonyId' => '__ID__', 'leaseHoldOnly' => '__LEASE__', 'withOutLeaseHold' => '__WITHOUTLEASEHOLD__'])); ?>";
            reponseUrl = reponseUrl.replace('__ID__', selectedColonyId).replace('__LEASE__', leaseHoldOnly ? 1 : 0).replace('__WITHOUTLEASEHOLD__', withOutLeaseHold ? 1 : 0);        
            if (selectedColonyId != "") {
            $.ajax({ // call for subtypes for selected property types
                url: reponseUrl,
                type: "get",
                success: function(res) {
                    if (leaseHoldOnly && res.length == 0) {
                        showError('No lease hold property found in the selected colony');
                    }
                    $.each(res, function(key, value) {

                        var newOption = $('<option>', {
                            value: value.block_no ?? "null",
                            text: value.block_no ?? 'Not Applicable'
                        });
                        targetSelect.append(newOption);
                    });
                    targetSelect.selectpicker('refresh');
                }
            });
        }
    })
    $('#block').change(function() {
        var selectedBlock = $(this).val();
        var targetSelect = $('#plot')
        targetSelect.html('<option value="">Select</option>')
        if (selectedColonyId != "") {
        var reponseUrl = "<?php echo e(route('propertiesInBlock', ['colonyId' => '__COLONY__', 'blockId' => '__BLOCK__', 'leaseHoldOnly' => '__LEASE__', 'withOutLeaseHold' => '__WITHOUTLEASEHOLD__'])); ?>";
            reponseUrl = reponseUrl
                .replace('__COLONY__', selectedColonyId)
                .replace('__BLOCK__', selectedBlock)
                .replace('__LEASE__', leaseHoldOnly ? 1 : 0)
                .replace('__WITHOUTLEASEHOLD__', withOutLeaseHold ? 1 : 0);            
                $.ajax({
                url: reponseUrl,
                type: "get",
                success: function(res) {

                    $.each(res, function(key, value) {
                        var newOption = $('<option>', {
                            value: (value.is_joint_property !== undefined) ? value.old_propert_id : value.property_master_id + '_' + value.id, // if not splited then old property id else parentPropertyId_splitedPropertyId
                            text: value.plot_or_property_no ?? value.plot_flat_no,
                            'data-known-as':value.presently_known_as
                        });
                        targetSelect.append(newOption);
                    });
                    targetSelect.selectpicker('refresh');

                }
            });
        }
    });
    $('#plot').change(function(){
        var knownAs = $(this).find(':selected').attr('data-known-as');
        $('#known-as').html(knownAs);
        $('#knownAsDiv').show();
    });
    $('#colony_id, #block').change(function(){
        $('#knownAsDiv').hide();
    })
</script>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/include/parts/property-selector.blade.php ENDPATH**/ ?>