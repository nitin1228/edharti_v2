<div class="page-content">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card radius-10">
                <div class="card-body">
                    <table class="table table-striped text-center" id="area-wise-details">
                        <thead>
                            <tr>
                                <th>Area (Sqm)</th>
                                <th>No. of Properties</th>
                                <th>Area of Properties</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="col-12 col-lg-6">
            <div class="card radius-10">
                <div class="card-body no-padding">
                    <div class="d-flex tabs-progress-container">
                        <div class="nav-tabs-left-aside">
                            <ul class="nav nav-tabs nav-primary" role="tablist" style="display: block !important;">

                                <?php if(count($tabHeader) > 0): ?>
                                <?php $__currentLoopData = $tabHeader; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$th): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link <?php echo e($i == 0 ? 'active': ''); ?>" data-bs-toggle="tab" href="#" role="tab" aria-selected="true" onclick="getTabData('<?php echo e($th->id); ?>')">
                                        <div class="text-center">
                                            <div class="tab-title"><?php echo e($th->property_type_name); ?></div>
                                            <span class="tab-total-no" id="tab_total_<?php echo e(strtolower($th->property_type_name)); ?>"><?php echo e($th->counter); ?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="nav-tabs-right-aside">
                            <div class="tab-content py-3">
                                <div class="tab-pane fade show active" id="" role="tabpanel">
                                    <ul class="progress-report">
                                        <!-- new code -->
                                        <?php
                                        $max = 0;
                                        foreach ($tab1Details as $row) {
                                            $max = $row->counter > $max ? $row->counter : $max;
                                        }
                                        ?>

                                        <?php $__currentLoopData = $tab1Details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <?php
                                            $width = $max > 0 ? ($detail->counter / $max) * 100 : 0;
                                            ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="progress-title"><?php echo e($detail->PropSubType); ?></span>
                                                <span class="progress-result"><?php echo e($detail->counter); ?></span>
                                            </div>
                                            <div class="progress mb-4" style="height:7px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $width ?>%" aria-valuenow="<?php echo e($detail->counter); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </ul>

                                    <!--- new code -->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/include/parts/dashboard-filtered.blade.php ENDPATH**/ ?>