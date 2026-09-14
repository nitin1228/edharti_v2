<?php $__env->startSection('title', 'Demand Listing'); ?>

<?php $__env->startSection('content'); ?>

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Demand</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Demand</li>
                <li class="breadcrumb-item active" aria-current="page">Created Demands</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>

<hr>
<div class="card">
    <div class="card-body">
        
        <!-- added div table-responsive by anil on 21-11-2025 -->
        <div class="table-responsive">
            <table id="example" class="table table-striped display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Unique Demand Id</th>
                        <th>Property ID</th>
                        <th>Known As</th>
                        <th> Financial Year</th>
                        <th>Net Total</th>
                        <th>Approved Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $demands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($demand->unique_id); ?></td>
                        <td><?php echo e($demand->old_property_id); ?> 
                            <?php if($demand->flat): ?>
                            <br>
                            <span style="font-size: 12px; color: #6c757d;">(Flat No. - <?php echo e($demand->flat->flat_number); ?>)</span>
                            <?php endif; ?>  
                        </td>
                        <td><?php echo e($demand->property_known_as); ?></td>
                        <td><?php echo e($demand->current_fy); ?></td>
                        <td>₹&nbsp;<?php echo e(customNumFormat(round($demand->net_total,2))); ?></td>
                         <td> <?php echo e(!empty($demand->approved_at)  ? \Carbon\Carbon::parse($demand->approved_at)->format('d-m-Y')  : 'N/A'); ?></td>

                        <td><?php echo e(getServiceNameById($demand->status)); ?></td>
                        <td>
                            <a href="<?php echo e(route('ViewDemand',$demand->id)); ?>">
                                <button class="btn btn-info">View</button>
                            </a>
                            <?php if (! \Illuminate\Support\Facades\Blade::check('role', 'internal-audit-cell')): ?>
                                <?php if(getServiceCodeById($demand->status) == "DEM_DRAFT" || getServiceCodeById($demand->status) == 'DEM_WD'): ?>
                                <a href="<?php echo e(route('EditDemand',$demand->id)); ?>">
                                    <button class="btn btn-warning">Edit</button>
                                </a>
                                <?php endif; ?>
                                <?php if(getServiceCodeById($demand->status) == "DEM_PENDING"): ?>
                                <a href="<?php echo e(route('withdrawDemand',$demand->id)); ?>" onclick="withdrawConfirmModal('Are you sure to withdraw this demand?', this); return false;">
                                    <button class="btn btn-danger">Withdraw</button>
                                </a>
                                
                                
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8"> No Data to Display</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php echo $__env->make('include.alerts.ajax-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('include.alerts.delete-confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('footerScript'); ?>
<script>
let confirmationCallback = null;

    function withdrawConfirmModal(customMessage, element) {
        document.getElementById('customConfirmationMessage').textContent = customMessage;
        confirmationCallback = function() {
            window.location.href = element.getAttribute('href');
        };
        $('#ModalDelete').modal('show');
    }

    $('#confirmDelete').click(() => {
        // If the callback is defined, call it
        if (confirmationCallback) {
            confirmationCallback();
            $('#ModalDelete').modal('hide'); // Close the modal after confirming
        }
    });
    </script>
    <script>
$(document).ready(function() {
    $('#example').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                text: 'Export CSV',
                className: 'btn btn-info',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/demand/index.blade.php ENDPATH**/ ?>