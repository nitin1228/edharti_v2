 

<?php $__env->startSection('content'); ?>

<div class="container">

    <div class="card">

        <div class="card-header">
            <h4>Payment Status Search</h4>
        </div>

        <div class="card-body">

            <?php if(session('error')): ?>
                <div class="alert alert-danger mt-3">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('payment.livestatus.result')); ?>" method="POST">

                <?php echo csrf_field(); ?>

                <div class="mb-3">

                    <label>Search By</label>

                    <select class="form-control" id="search_by" name="search_by" required>

                        <option value="">Select</option>

                        <option value="old_property_id">
                            Property ID
                        </option>

                        <option value="unique_payment_id">
                            Payment ID
                        </option>

                        <option value="transaction_id">
                            Transaction Number
                        </option>

                        <option value="demand_id">
                            Demand ID
                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label id="searchLabel">
                        Enter Value
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="search_value"
                        required>

                </div>

                <button class="btn btn-primary">

                    Search

                </button>

            </form>

        </div>

    </div>



    <?php if(isset($payment) && $payment): ?>

    <div class="card mt-4">

        <div class="card-header">
            <h4>Payment Details</h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>Property ID</th>
                    <td><?php echo e($payment->splited_old_property_id ? $payment->splited_old_property_id : $payment->master_old_property_id); ?></td>
                </tr>

                <tr>
                    <th>Demand ID</th>
                    <td><?php echo e($payment->demand ? $payment->demand->unique_id : 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Payment ID</th>
                    <td><?php echo e($payment->unique_payment_id); ?></td>
                </tr>

                 <tr>
                    <th>Amount</th>
                    <td><?php echo e($payment->amount); ?></td>
                <tr>

                <tr>
                    <th>Transaction Number</th>
                    <td><?php echo e($payment->transaction_id); ?></td>
                <tr>
                    <th>Database Status</th>
                    <td>
                        <?php if($payment->status == 1546): ?>
                            <div class="badge bg-success"><?php echo e(getServiceNameById($payment->status)); ?></div>
                        <?php elseif($payment->status == 1545): ?>
                            <div class="badge bg-danger"><?php echo e(getServiceNameById($payment->status)); ?></div>
                        <?php else: ?>
                            <div class="badge bg-warning"><?php echo e(getServiceNameById($payment->status)); ?></div>
                        <?php endif; ?>
                    </td>
                        
                </tr>

            </table>

        </div>

    </div>

    <?php endif; ?>




    <?php if(isset($liveStatus) && $liveStatus): ?>
<div class="card mt-3">

    <div class="card-header">
        <h4>Live BharatKosh Status</h4>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th>Order ID</th>
                <td><?php echo e($liveStatus['orderId']); ?></td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <?php if( $liveStatus['status'] == 'Success'): ?>
                        <div class="badge bg-success"><?php echo e($liveStatus['status']); ?></div>
                    <?php elseif( $liveStatus['status'] == 'FAIL'): ?>
                        <div class="badge bg-danger"><?php echo e($liveStatus['status']); ?></div>
                    <?php else: ?>
                        <div class="badge bg-warning"><?php echo e($liveStatus['status']); ?></div>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Transaction ID</th>
                <td><?php echo e($liveStatus['transactionId']); ?></td>
            </tr>

            <tr>
                <th>Payment Method</th>
                <td><?php echo e($liveStatus['method']); ?></td>
            </tr>

            <tr>
                <th>Amount</th>
                <td><?php echo e($liveStatus['amount']); ?></td>
            </tr>
            <tr>
                <th>Transaction Date/ Time</th>
                <td><?php echo e($liveStatus['transactionDate']); ?></td>
            </tr>
            <tr>
                <th>Message</th>
                <td><?php echo e($liveStatus['message']); ?></td>
            </tr>

        </table>

    </div>

</div>

<?php endif; ?>

</div>

<script>

document.getElementById('search_by').addEventListener('change', function(){

    let labels = {

        old_property_id : 'Enter Property ID',

        unique_payment_id : 'Enter Payment ID',

        transaction_id : 'Enter Transaction Number',

        demand_id : 'Enter Demand ID'

    };

    document.getElementById('searchLabel').innerHTML = labels[this.value];

});

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/payment/payment-live-status-search.blade.php ENDPATH**/ ?>