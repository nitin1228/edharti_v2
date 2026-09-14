<style>
    .subhead-details{
        margin: 10px;
        padding: 10px;
        box-shadow: 0px 0px 7px 0px #76797c;
    }
</style>

<?php $__empty_1 = true; $__currentLoopData = $subheads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="subhead-details">
        <?php
            $keys = collect($item)->except(['is_added_to_new_demand','created_at','updated_at','id','DemandID','ComputerCode','BreachType','Floor', 'Area', 'AreaUnit','PaymentType'])->keys()->all();
        ?>
        <table class="table table-bordered">
            <?php $__currentLoopData = array_chunk($keys, 2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pair): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <?php $__currentLoopData = $pair; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th><?php echo e(camelToTitle($key)); ?></th>
                <?php
                    $value = $item->{$key};
                    if(in_array(strtolower($key),['amount', 'rate'])){
                        $value = '₹ '.customNumFormat($value);
                    }
                    if(strtolower($key) == 'area')
                    {
                        $value = customNumFormat($value);
                    }
                    if(strpos(strtolower($key),'date') !== false && !is_null($value))
                    {
                        $value = date('d-m-Y',strtotime($value));
                    }
                ?>
                <td><?php echo e($value); ?></td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(count($pair) < 2): ?>
                    <th></th><td></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="subhead-details">
        <h5>No data to display</h5>
    </div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/include/parts/old-demand-subheads.blade.php ENDPATH**/ ?>