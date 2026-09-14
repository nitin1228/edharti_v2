<?php switch($applicationType):
    case ('Mutation'): ?>
        <?php echo $__env->make('application.admin.application_document.mutation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>

    <?php case ('Land Use Change'): ?>
        <?php echo $__env->make('application.admin.application_document.luc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>

    <?php case ('Deed Of Apartment'): ?>
        <?php echo $__env->make('application.admin.application_document.doa', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>

    <?php case ('Conversion'): ?>
        <?php echo $__env->make('application.admin.application_document.conversion', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>
    <?php case ('Noc'): ?>
        <?php echo $__env->make('application.admin.application_document.noc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>

    <?php default: ?>
        <div class="part-title mt-2">
            <h5>Details of Documents</h5>
        </div>
        <div class="part-details">
            <div class="container-fluid">
                <div class="row g-2">
                    <div class="col-lg-12">
                        <p>Documents Not Available.</p>
                    </div>
                </div>
            </div>
        </div>
<?php endswitch; ?>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/admin/application_document/index.blade.php ENDPATH**/ ?>