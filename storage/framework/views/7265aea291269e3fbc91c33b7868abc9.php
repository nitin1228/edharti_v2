<div class="row">
    <div class="col-lg-12">
      <div class="part-title">
        <h5>Old Demand Details</h5>
      </div>
      <div class="part-details">
        <div class="container-fluid">
            <?php $__empty_1 = true; $__currentLoopData = $oldDemands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demKey=>$oldDemand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <input type="hidden" name="oldDemandId[<?php echo e($demKey); ?>]" value="<?php echo e($oldDemand->demand_id); ?>">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Demand Id</th>
                                <td><?php echo e($oldDemand->demand_id); ?></td>
                                <th>Demand Amount</th>
                                <td>₹ <?php echo e(customNumFormat($oldDemand->amount)); ?></td>
                                <th>Paid Amount</th>
                                <td>₹ <?php echo e(customNumFormat($oldDemand->paid_amount)); ?></td>
                                <th>Outstanding Amount</th>
                                <td>₹ <?php echo e(customNumFormat($oldDemand->outstanding)); ?></td>
                                <th><buton class="btn btn-primary" id="full-demand-details" onclick="viewFullDemandDetails('<?php echo e($oldDemand->demand_id); ?>')">View Details</button></th>
                            </tr>
                        </table>
                        <br>
                        <h5>Breakup of Previous Demand</h5>
                        <table class="table table-bordered table-striped">
                           
                            <tr>
                                <th width="180" class="hideSection">Include in New Demand</th>
                                <th>Details</th>
                                <th>Demand Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding Amount</th>
                            </tr>
                            <?php $__empty_2 = true; $__currentLoopData = $oldDemand->subheadwiseBreakup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <?php
                                        $outstandingAmount = $item['demand_amount'] - $item['paid_amount'];
                                        $shouldCheck = $item['checked'] || $outstandingAmount < 0;
                                    ?>
                                
                            <input type="hidden" name="oldDemandSubheadkey[<?php echo e($demKey); ?>][<?php echo e($key); ?>]" value="<?php echo e($outstandingAmount); ?>">
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" name="check[<?php echo e($demKey); ?>][<?php echo e($key); ?>]" class="check-include-in-demand form-check-input" data-demand-id="<?php echo e($oldDemand->demand_id); ?>" data-subhead-key="<?php echo e($key); ?>" data-subhead-amount='<?php echo e($outstandingAmount); ?>' <?php echo e((isset($openInReadOnlyMode) ||$outstandingAmount < 0 )? 'data-readonly':''); ?> <?php echo e($shouldCheck ? 'checked' : ''); ?> onchange="calculateTotalAmount()">
                                        </div>
                                    </td>
                                    <td><?php echo e($key); ?></td>
                                    <td>₹ <?php echo e(customNumFormat($item['demand_amount'])); ?></td>
                                    <td>₹ <?php echo e(customNumFormat($item['paid_amount'])); ?></td>
                                    <td>₹ <?php echo e(customNumFormat($outstandingAmount)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                <tr>
                                    <td colspan="5">Nothing to display here.</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="row">
                    <div class="col-lg-12">
                        <h5>No old demand found.</h5>
                    </div>
                </div>
            <?php endif; ?>
        </div>
      </div>
    </div>
</div>

<?php echo $__env->make('include.parts.old-demand-details-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
    function viewFullDemandDetails( demandId){
        let baseUrl = '<?php echo e(route("oldDemandBreakUp", ["oldDemandId" => "__ID__"])); ?>';
        let url = baseUrl.replace('__ID__', demandId);
        $('#oldDemandSubheadsModal .modal-body').load(url);
        $('#oldDemandSubheadsModal').modal("show");
        $('#oldDemandSubheadsModal #demandId').html(demandId);
    }
</script><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/include/parts/old-demand-details.blade.php ENDPATH**/ ?>