

<?php $__env->startSection('title', 'Revenue  Summary'); ?>

<?php $__env->startSection('content'); ?>

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Revenue </div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Revenue </li>
                <li class="breadcrumb-item active" aria-current="page">Summary</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>

<hr>

<div class="row">
<style>.card {
    margin-bottom: 0.5rem;
}</style>
    <div class="col-lg-12">
        <div class="card border-primary">
           <!-- <div class="card-header">Filter by Date</div>-->
            <div class="card-body">
<div  class="row mb-3">
                <form class="row mb-3">
                        
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Start Date:</label>
                                <input type="text" class="form-control datepicker" id="startDate" autocomplete="off" name="start_date" value="<?php echo e($request->input('start_date')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">End Date:</label>
                                <input type="text" class="form-control datepicker" id="endDate" autocomplete="off" name="end_date" value="<?php echo e($request->input('end_date')); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex justify-content-start gap-4 col-lg-12">
                            <button type="submit" class="btn btn-primary" id="btn-apply-filter">Apply</button>
                             <button type="button" class="btn btn-warning" id="btn-reset-filter">Reset</button>
                        </div>
                    </div>
                </form></div>
                <div class="row">
 
                        <!-- <div class="col-sm-6 col-xl-3 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-dark-orange  b-r-4 card-body">
                                    <a href="javascript:;"  class="app-query-link" data-status="inprogress">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Total Pending Payment</span>
                                                <h4 class="mb-0 counter" id="totalinProgressApplicationCount"  ><?php echo e(customNumFormat($statuswise['PAY_PENDING']['amount'])); ?></h4>
                                                <i class="fa-solid fa-solid fa-thumbs-up"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>-->
    
</div>
            </div>
            
        </div>
        
    </div>
    
</div>
<div class="row">
        <div class="col-lg-12 mb-2">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title"><!--Application Fee Payments Summary by Application Type-->Revenue Summary eDharti 2.0<!--Payment Summary--></h5>
               				    <div class="table-responsive mt-2 mb-3">

    <?php
        $grandTotal = collect($summary)->sum('amount');
    ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Items</th>
                <th>Total Payments</th>
                <th>Total  Property</th>
            </tr>
        </thead>

        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="submitted-data">
                        <?php echo e($item['name'] == 'Application' ? "Application Processing Fee" : $item['name']); ?>

                    </td>

                    <td class="submitted-data">
                        <a href="javascript:;"
                           class="app-query-link"
                           data-status="summary"
                           data-service="<?php echo e($item['code']); ?>">
                            ₹ <?php echo e(customNumFormat(round($item['amount'] ?? 0, 2))); ?>

                        </a>
                    </td>
                     <td>
                    <?php echo e($item['property_count'] ?? 0); ?> 
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        No data found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

        <tfoot>
            <tr class="table-secondary">
                <th class="text-end">Total:</th>
                <th>
                <a href="javascript:;"
                           class="app-query-link"
                           data-status="summaryall"
                           data-service="all">     ₹ <?php echo e(customNumFormat(round($grandTotal, 2))); ?>

                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

                </div>
            </div>
        </div>
    </div>
<div class="row">
        <div class="col-lg-12 mb-2">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title"><!--Application Fee Payments Summary by Application Type-->Revenue Summary  eDharti 1.0 <!--Payment Summary--></h5>
               				    <div class="table-responsive mt-2 mb-3">

    <?php
        $grandTotal = collect($summaryold)->sum('amount');
    ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Items</th>
                <th>Total Payments</th>
                <th>Total  Property</th>
            </tr>
        </thead>

        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $summaryold; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="submitted-data">
                        <?php echo e($item['name'] == 'Application' ? "Application Processing Fee" : $item['name']); ?>

                    </td>

                    <td class="submitted-data">
                        <a href="javascript:;"
                           class="app-query-link"
                           data-status="summaryold"
                           data-service="<?php echo e($item['code']); ?>">
                            ₹ <?php echo e(customNumFormat(round($item['amount'] ?? 0, 2))); ?>

                        </a>
                    </td>
                     <td>
                    <?php echo e($item['property_count'] ?? 0); ?> 
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        No data found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>

        <tfoot>
            <tr class="table-secondary">
                <th class="text-end">Total:</th>
                <th>
                <a href="javascript:;"
                           class="app-query-link"
                           data-status="summaryallold"
                           data-service="allold">     ₹ <?php echo e(customNumFormat(round($grandTotal, 2))); ?> </a>
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

                </div>
            </div>
        </div>
    </div>  

 
  <div class="row">   
    
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                <a href="<?php echo e(route('demandSummaryDetails')); ?>" class="app-query-link1" data-status="allpayment">
                    <div class="card-header">Total Demand Raised</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; <?php echo e(customNumFormat($demandData['total'])); ?></h5>
                        
                    </div>
                     </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                 <a href="<?php echo e(route('demandSummaryDetails')); ?>" class="app-query-link1" data-status="allpayment">
                    <div class="card-header">Total Payment Received for Demands</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; <?php echo e(customNumFormat($demandData['paid'])); ?></h5>
                        
                    </div>
                    </a>
                </div>
            </div>
             <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                 <a href="<?php echo e(route('demandSummaryDetails')); ?>" class="app-query-link1" data-status="allpayment">
                    <div class="card-header">Total Pending Payments for Demands</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; <?php echo e(customNumFormat($demandData['pending'])); ?></h5>
                        
                    </div>
                    </a>
                </div>
            </div>
        
	</div> 
    
<?php echo $__env->make('include.alerts.ajax-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footerScript'); ?>
<script>
	 let statusKeyArray = {
	    	'allpayment': 'PAY_PENDING,PAY_SUCCESS',
	        'successpayment': 'PAY_SUCCESS',
	        'pendingpayment': 'PAY_PENDING'
	    }
       $(function() {
        var dateFormat = "dd-mm-yy";
        $("#startDate").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            // Set min date for endDate when startDate is selected
            $("#endDate").datepicker("option", "minDate", selectedDate);
        }
        });
        $("#endDate").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            // Set max date for startDate when endDate is selected
        $("#startDate").datepicker("option", "maxDate", selectedDate);
        }
        });
    });
//    kindly include the following details for each candidate:

	//==================================================================================
     $(document).on("click",".app-query-link",function(e) {
	 	e.preventDefault();
        let selectedService = $(this).data('service');  
        let selectedStatus = $(this).data('status');  
        let startDate = $("#startDate").val();
        let endDate = $("#endDate").val();
        let request = {};       
        if (selectedService !== undefined) {
            request.service = selectedService;
        }
        if (selectedStatus !== undefined) {
            //request.status = statusKeyArray[selectedStatus];
            request.status = selectedStatus;
        }
        if (startDate !== undefined) {
            request.start = startDate;
        }
        if (endDate !== undefined) {
            request.end = endDate;
        }        
        let queryString = new URLSearchParams(request).toString(); 
       // console.log(queryString);
        //alert(queryString);
        //return false;   
    	let encoded = btoa(queryString);
    	let url = "<?php echo e(route('paymentSummaryDetails')); ?>" + "?data=" + encoded;
    	window.open(url, "_blank");
		// let url = "<?php echo e(route('paymentSummaryDetails')); ?>" + '?' + new URLSearchParams(request).toString();
		///////   window.open(url, "_blank");
    })    
	    $("#btn-reset-filter").click(function(){
	    	  window.location.href = "<?php echo e(route('paymentSummary')); ?>";
	    })
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/payment/summary.blade.php ENDPATH**/ ?>