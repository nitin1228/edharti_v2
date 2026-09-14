<div class="mt-3">
    <div class="container-fluid">
        <div class="row g-2">
            <div class="col-lg-12">
                <div class="part-title mb-2">
                    <h5>Fill Flat Details</h5>
                </div>
            </div>
            <input type="hidden" id="old_property_id" name="old_property_id"
                value="<?php echo e(isset($application) ? $application->old_property_id : old('old_property_id')); ?>">
            <input type="hidden" id="property_master_id" name="property_master_id"
                value="<?php echo e(isset($application) ? $application->property_master_id : old('property_master_id')); ?>">
            <input type="hidden" id="new_property_id" name="new_property_id"
                value="<?php echo e(isset($application) ? $application->new_property_id : old('new_property_id')); ?>">
            <input type="hidden" id="splited_property_detail_id" name="splited_property_detail_id"
                value="<?php echo e(isset($application) ? $application->splited_property_detail_id : old('splited_property_detail_id')); ?>">
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="applicantName" class="form-label">Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="applicantName" id="applicantName"
                        placeholder="Enter Name"
                        value="<?php echo e(isset($userDetails->name) ? $userDetails->name : old('applicantName')); ?>" readonly>
                    <?php $__errorArgs = ['applicantName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="text-danger" id="applicantNameError"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="applicantAddress" class="form-label">Communication Address<span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="applicantAddress" id="applicantAddress"
                        placeholder="Enter Communication Address"
                        value="<?php echo e(isset($userDetails->applicantUserDetails->address) ? $userDetails->applicantUserDetails->address : old('applicantAddress')); ?>"
                        readonly>
                    <?php $__errorArgs = ['applicantAddress'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="text-danger" id="applicantAddressError"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="buildingName" class="form-label">Building Name<span class="text-danger">*</span> <small
                            class="form-text text-muted">(In
                            which the apartment exists.)</small></label>
                    <input type="text" class="form-control" name="buildingName" id="buildingName"
                        placeholder="Building name"
                        value="<?php echo e(isset($application) ? $application->building_name : old('buildingName')); ?>">
                    <?php $__errorArgs = ['buildingName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="text-danger" id="buildingNameError"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="originalBuyerName" class="form-label">Name Of Original Buyer<span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="originalBuyerName" id="originalBuyerName"
                        placeholder="Name Of Original Buyer"
                        value="<?php echo e(isset($application) ? $application->original_buyer_name : old('originalBuyerName')); ?>">
                </div>
                <?php $__errorArgs = ['originalBuyerName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="text-danger" id="originalBuyerNameError"></div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="presentOccupantName" class="form-label">Name Of Present Occupant<span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="presentOccupantName" id="presentOccupantName"
                        placeholder="Name Of Present Occupant"
                        value="<?php echo e(isset($application) ? $application->present_occupant_name : old('presentOccupantName')); ?>">
                </div>
                <?php $__errorArgs = ['presentOccupantName'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="text-danger" id="presentOccupantNameError"></div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="purchasedFrom" class="form-label">Purchased From<span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="purchasedFrom" id="purchasedFrom"
                        placeholder="Purchased From"
                        value="<?php echo e(isset($application) ? $application->purchased_from : old('purchasedFrom')); ?>">
                </div>
                <?php $__errorArgs = ['purchasedFrom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="text-danger" id="purchasedFromError"></div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="purchaseDate" class="form-label">Date of Purchase<span
                            class="text-danger">*</span></label>
                    <input type="date" name="purchaseDate" class="form-control" id="purchaseDate"
                        pattern="\d{2} \d{2} \d{4}"
                        value="<?php echo e(isset($application) ? $application->purchased_date : old('purchaseDate')); ?>">
                </div>
                <?php $__errorArgs = ['purchaseDate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="errorMsg"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div id="purchaseDateError" class="text-danger"></div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="apartmentArea" class="form-label">Flat Area<span class="text-danger">*</span> <small
                            class="form-text text-muted">(In
                            Sq.
                            Mtr. including common area.)</small></label>
                    <input type="text" class="form-control" name="apartmentArea" id="apartmentArea"
                        placeholder="Flat Area"
                        value="<?php echo e(isset($application) ? $application->flat_area : old('apartmentArea')); ?>">
                </div>
                <?php $__errorArgs = ['apartmentArea'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="text-danger" id="apartmentAreaError"></div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="plotArea" class="form-label">Plot Area<span class="text-danger">*</span> <small
                            class="form-text text-muted">(Leased
                            From L&DO in Sq. Mtr.)</small> </label>
                    <input type="text" class="form-control" name="plotArea" id="plotArea"
                        placeholder="Plot Area"
                        value="<?php echo e(isset($application) ? $application->plot_area : old('plotArea')); ?>">
                </div>
                <?php $__errorArgs = ['plotArea'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="text-danger"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div class="text-danger" id="plotAreaError"></div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/deed_of_apartment/include/step-1.blade.php ENDPATH**/ ?>