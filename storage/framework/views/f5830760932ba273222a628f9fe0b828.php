<?php $__env->startSection('title', 'Demand Summary Deatils'); ?>

<?php $__env->startSection('content'); ?>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Demand</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a></li>

               <!-- <li class="breadcrumb-item">Demand</li>-->
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <li class="breadcrumb-item active" aria-current="page">All Demands</li>
            </ol>
        </nav>
    </div>
</div>
<hr>
<div class="container-fluid general-widget g-0">
    

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title">All Demands</h5>
                    <div class="table-responsive mt-2">
                          <table class="table table-bordered mb-5" id="tab-all-applications">
        <thead>
            <tr class="table-success">
                <th>S. No.</th>
                <th>Demand ID</th>
                <th>Demand Date</th>           
                <th>Property ID</th>
               <th>Section</th>
                <th>Known As</th>
                <th>Financial Year</th>
                <th>Demand Amount</th>
                <th>Paid Amount</th>
                <th>Outstanding Amount</th>
                <th>Status</th>
                <!--<th>Action</th>-->
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $queryResult; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td>                    
                    <a href="<?php echo e(route('ViewDemand',$demand->id)); ?>"  data-toggle="tooltip" 
					   data-placement="top" 
					   title="View Details"><?php echo e($demand->unique_id); ?></a>
                       <?php if($demand->demand_path == ''): ?>
                     <a href="<?php echo e(route('demand.demand_letter_pdf', $demand->id)); ?>" 
					   target="_blank"
					   data-toggle="tooltip" 
					   data-placement="top" 
					   title="Download Demand Letter">
					    <i class="lni lni-cloud-download text-danger" 
					       style="font-size: 25px; vertical-align: middle;">
					    </i>
					</a>
                    <?php else: ?> 
                     <a href="<?php echo e(asset('storage/public/'.$demand->demand_path)); ?>" 
					   target="_blank"
					   data-toggle="tooltip" 
					   data-placement="top" 
					   title="Download Demand Letter">
					    <i class="lni lni-cloud-download text-danger" 
					       style="font-size: 25px; vertical-align: middle;">
					    </i>
					</a>
                    <?php endif; ?>
                    </td>
                    <td><?php echo e(date('d-m-Y',strtotime($demand->created_at))); ?></td>
                    <td><?php echo e($demand->unique_propert_id); ?><br><small>(<?php echo e($demand->old_property_id); ?>)</small></td>
                    <td><?php echo e($demand->section_code); ?></td>
                    <td><?php echo e($demand->property_known_as); ?></td>
                    <td><?php echo e($demand->current_fy); ?></td>
                    <td>₹ <?php echo e(customNumFormat(round($demand->net_total, 2))); ?><br>
                     <a href="javascript:void(0);" 
       class="text-primary viewBreakup" 
       data-id="<?php echo e($demand->id); ?>" 
       data-toggle="tooltip" 
       title="View Breakup">
        (View Breakup)
    </a></td>                    
                    <td>₹ <?php echo e(customNumFormat(round($demand->paid_amount, 2)) ?? 0); ?></td>
                    <td>₹ <?php echo e(customNumFormat(round($demand->balance_amount, 2)) ?? 0); ?></td>
                    <td><?php echo e(getServiceNameById($demand->status)); ?></td>
                   <!-- <td>
                      
                        
                    </td>-->
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="12" align="center">Sorry, no records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>      


<tfoot>
    <tr class="table-secondary"> 
       <th ></th>  
       <th ></th>  
       <th ></th>  
       <th ></th>  
       <th ></th>  
       <th ></th>  
        <th  class="text-end">Total:</th>   
        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ <?php echo e(customNumFormat(round(collect($queryResult)->sum('net_total'), 2))); ?><br>           
           <!-- <?php echo e(collect($queryResult)->sum('net_total') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('net_total'), 2)) 
                : 'Zero Rupees Only'); ?>-->
        </th>
        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ <?php echo e(customNumFormat(round(collect($queryResult)->sum('paid_amount'), 2))); ?><br>
           <!-- <?php echo e(collect($queryResult)->sum('paid_amount') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('paid_amount'), 2)) 
                : 'Zero Rupees Only'); ?>-->
        </th>
       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ <?php echo e(customNumFormat(round(collect($queryResult)->sum('balance_amount'), 2))); ?><br>            
           <!-- <?php echo e(collect($queryResult)->sum('balance_amount') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('balance_amount'), 2)) 
                : 'Zero Rupees Only'); ?>-->
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
</div>
<div class="modal fade" id="breakupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
    <h5 class="modal-title">Demand Headwise Breakup</h5>
    <button type="button" class="close ms-auto btn btn-danger" data-bs-dismiss="modal">×</button>
</div>
      <div class="modal-body" id="breakupBody">
        Loading...
      </div>
<div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" class="btn btn-primary" onclick="printBreakup()">
           Print
        </button>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>
<!-- Modal -->


<?php $__env->startSection('footerScript'); ?>
<script>
    function printBreakup() {
    var printContents = document.getElementById("breakupBody").innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = `
        <html>
        <head>
            <title>Demand Headwise Breakup</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                table, th, td { border: 1px solid #000; }
                th, td { padding: 8px; text-align: left; }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
    `;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // modal & JS restore ke liye
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $('.viewBreakup').click(function(){
        let demand_id = $(this).data('id');
        $('#breakupModal').modal('show');
        $('#breakupBody').html("Loading...");
        $.ajax({
            url: "<?php echo e(route('get.breakup')); ?>",
            type: "GET",
            data: { demand_id: demand_id },
            success: function(response){
                $('#breakupBody').html(response);
            }
        });
    });
})

$(document).ready(function () {
    var table = $('#tab-all-applications').DataTable({
        responsive: false,
        searching: true,
        paging: false,
        info: false,
        dom: 'Bfrtip',
                 buttons: [
    {
        extend: 'excelHtml5',
         text: 'EXCEL',
        exportOptions: {
            columns: ':not(:last-child)'  ,
            footer: true         
        }
    },
    {
        extend: 'csvHtml5',
         text: 'CSV',
        exportOptions: {
           columns: ':not(:last-child)',
           footer: true
                    }
    },
    {
        extend: 'pdfHtml5',
         text: 'PDF',
        orientation: 'landscape',
        pageSize: 'A4',
         footer: true ,
        exportOptions: {
           columns: ':not(:last-child)',
                    
        },

    }
]
,
        columnDefs: [
            { orderable: false, targets: 5 }, 
    { orderable: true, targets: '_all' } 
        ]
    });
	});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/demand/demand-summary-details.blade.php ENDPATH**/ ?>