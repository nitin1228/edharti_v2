<table class="table table-bordered">
	<tr>
	<th><h6>Demand Id : <?php echo e($demandUniqueId); ?> </h6>
	</th>
	<th><h6> Property Id : <?php echo e($newPropertyId); ?> <small>(<?php echo e($oldPropertyId); ?>)</small></h6> </th></tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Demand Sub Head</th>
            <th>Demand Amount</th>
            <th>Paid Amount</th>
            <th>Balance Amount</th>
        </tr>
    </thead>

    <tbody>
        <?php
            $totalDemand = 0;
            $totalPaid = 0;
            $totalBalance = 0;
        ?>

        <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $totalDemand += $d->net_total;
                $totalPaid += $d->paid_amount;
                $totalBalance += $d->balance_amount;
            ?>

            <tr>
                <td><?php echo e($key+1); ?></td>
                <td><?php echo $d->subhead_name."<br>".($d->subhead_code == "DEM_MANUAL" ? '('. ($d->subhead_keys['manual_title'] ??'No description').')' : ''); ?></td>
                <td>₹ <?php echo e(number_format($d->net_total, 2)); ?></td>
                <td>₹ <?php echo e(number_format($d->paid_amount, 2)); ?></td>
                <td>₹ <?php echo e(number_format($d->balance_amount, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr style="font-weight: bold; background: #f8f8f8;">
            <td colspan="2" style="text-align:right;">Total:</td>
            <td>₹ <?php echo e(number_format($totalDemand, 2)); ?></td>
            <td>₹ <?php echo e(number_format($totalPaid, 2)); ?></td>
            <td>₹ <?php echo e(number_format($totalBalance, 2)); ?></td>
        </tr>

    </tbody>
</table>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/include/partials/demand_breakup.blade.php ENDPATH**/ ?>