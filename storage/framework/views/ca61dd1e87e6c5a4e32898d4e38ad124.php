<div class="table-responsive mt-2 mb-3">
<?php
function toRoman($number) {
    $map = [
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
    ];
    $returnValue = '';
    foreach ($map as $roman => $int) {
        while ($number >= $int) {
            $returnValue .= $roman;
            $number -= $int;
        }
    }
    return $returnValue;
}
?>

<?php if($filtertype == "sectionWise"): ?>
<h5 >Section wise Demand Summary </h5>
    <table class="table table-bordered mb-5">
        <thead>
            <tr class="table-success">
                <th>S. No. </th>
                <th>Demand ID</th>
                <th>Demand Date</th>           
                <th>Property ID</th>
               <th>File Number</th>
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
                    <td> <a href="<?php echo e(route('ViewDemand',$demand->id)); ?>"><?php echo e($demand->unique_id); ?></a> <a href="<?php echo e(route('demand.demand_letter_pdf', $demand->id)); ?>" target="_blank"><i class="lni lni-cloud-download text-danger" style="font-size: 25px; vertical-align: middle;"></i></a></td>
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
    </a>
                    </td>                    
                    <td>₹ <?php echo e(customNumFormat(round($demand->paid_amount, 2)) ?? 0); ?></td>
                    <td>₹ <?php echo e(customNumFormat(round($demand->balance_amount, 2)) ?? 0); ?></td>
                    <td><?php echo e(getServiceNameById($demand->status)); ?></td>
                    <!--<td>
                        
                        <a href="<?php echo e(route('ViewDemand',$demand->id)); ?>" class="btn btn-sm btn-flat btn-primary">View</a>
                       
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
        <th colspan="7" class="text-end">Total:</th>
        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ <?php echo e(customNumFormat(round(collect($queryResult)->sum('net_total'), 2))); ?><br>           
           <!-- <?php echo e(collect($queryResult)->sum('net_total') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('net_total'), 2)) 
                : 'Zero Rupees Only'); ?>-->
        </th>
        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ <?php echo e(customNumFormat(round(collect($queryResult)->sum('paid_amount'), 2))); ?><br>
          <!--  <?php echo e(collect($queryResult)->sum('paid_amount') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('paid_amount'), 2)) 
                : 'Zero Rupees Only'); ?>-->
        </th>
       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ <?php echo e(customNumFormat(round(collect($queryResult)->sum('balance_amount'), 2))); ?><br>            
           <!-- <?php echo e(collect($queryResult)->sum('balance_amount') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('balance_amount'), 2)) 
                : 'Zero Rupees Only'); ?>-->
        </th>
        <th colspan="2"></th>
    </tr>
</tfoot>

    </table>
   <?php elseif($filtertype == "dyLDoWise"): ?>
    <div class="mb-4">
        <h5 >Dy. L&DO Section wise Demand Summary 
            <?php if(isset($selectedDyUser)): ?> 
               <small> (Mr. <?php echo e($selectedDyUser->name); ?>) </small>
            <?php endif; ?>
        </h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>S. No.</th>
                    <th>Section Name</th>
                   <!-- <th>Section Code</th>-->
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                     <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $dySectionWiseSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($section['section_name']); ?></td>
                       <!-- <td><?php echo e($section['section_code']); ?></td>-->
                        <td><?php echo e($section['total_demands']); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($section['total_amount'], 2))); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($section['total_paid'], 2))); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($section['total_balance'], 2))); ?></td>
                        <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="<?php echo e($section['section_code']); ?>" data-type="<?php echo e($demandType); ?>" data-from="<?php echo e($filterDateFrom); ?>" data-to="<?php echo e($filterDateTo); ?>" target="_blank">View More</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" align="center">No section-wise data found for selected Dy. L&DO.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
               <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th><?php echo e(collect($dySectionWiseSummary)->sum('total_demands')); ?><br>
    	<!--<?php echo e(ucfirst(convertNumberToWords(collect($dySectionWiseSummary)->sum('total_demands')))); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($dySectionWiseSummary)->sum('total_amount'), 2))); ?><br>
   <!-- <?php echo e(collect($dySectionWiseSummary)->sum('total_amount') > 0 ? convertToIndianCurrencyWords(round(collect($dySectionWiseSummary)->sum('total_amount'), 2)) : 'Zero Rupees Only'); ?>  -->  	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($dySectionWiseSummary)->sum('total_paid'), 2))); ?><br>
    <!--	<?php echo e(collect($dySectionWiseSummary)->sum('total_paid') > 0 ? convertToIndianCurrencyWords(round(collect($dySectionWiseSummary)->sum('total_paid'), 2)) : 'Zero Rupees Only'); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($dySectionWiseSummary)->sum('total_balance'), 2))); ?><br>
   <!-- <?php echo e(collect($dySectionWiseSummary)->sum('total_balance') > 0 ? convertToIndianCurrencyWords(round(collect($dySectionWiseSummary)->sum('total_balance'), 2)) : 'Zero Rupees Only'); ?>   --> 	
    </th>
    <th></th>
</tr>
</tfoot>
        </table>
    </div>
    
<?php elseif($filtertype == "yearWise"): ?>
    <div class="mb-4">
        <h5 >Demand Summary</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                <td>S.No.</td>
                    <th>Designation</th>
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr> 
                	<td>1.</td>
                	<td>Land and Development Office</td>
                    <td><?php echo e($totalSummary->total_demands ?? 0); ?></td>
                    <td>₹ <?php echo e(customNumFormat(round($totalSummary->total_amount ?? 0, 2))); ?></td>
                    <td>₹ <?php echo e(customNumFormat(round($totalSummary->total_paid ?? 0, 2))); ?></td>
                    <td>₹ <?php echo e(customNumFormat(round($totalSummary->total_balance ?? 0, 2))); ?></td>
                    <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="<?php echo e(trim($totalSummary->section_codes)); ?>" data-type="<?php echo e($demandType); ?>" data-from="<?php echo e($filterDateFrom); ?>" data-to="<?php echo e($filterDateTo); ?>" target="_blank">View More</a></td>
                    
                </tr>
            </tbody>
             <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th><?php echo e($totalSummary->total_demands ?? 0); ?><br>
    	<!--<?php echo e(ucfirst(convertNumberToWords($totalSummary->total_demands ?? 0))); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round($totalSummary->total_amount ?? 0, 2))); ?><br>
  <!--  <?php echo e(round($totalSummary->total_amount ?? 0, 2) > 0 ? convertToIndianCurrencyWords(round($totalSummary->total_amount ?? 0, 2)) : 'Zero Rupees Only'); ?>-->
    	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round($totalSummary->total_paid ?? 0, 2))); ?><br>
    	<!--<?php echo e(round($totalSummary->total_paid ?? 0, 2) > 0 ? convertToIndianCurrencyWords(round($totalSummary->total_paid ?? 0, 2)) : 'Zero Rupees Only'); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round($totalSummary->total_balance ?? 0, 2))); ?><br>
   <!-- <?php echo e(round($totalSummary->total_balance ?? 0, 2) > 0 ? convertToIndianCurrencyWords(round($totalSummary->total_balance ?? 0, 2)) : 'Zero Rupees Only'); ?>-->
    	
    </th>
    <th></th>
</tr>
</tfoot>

        </table>
    </div>
   
    <div class="mb-4">
        <h5 >Dy. L&DO wise Demand Summary</h5>
        <table class="table table-bordered mb-5">
            <thead >
                <tr class="table-success">
                    <th>S. No.</th>
                    <th>Designation</th>
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php //print_r($dyLdoWiseSummary); ?>
                <?php $__empty_1 = true; $__currentLoopData = $dyLdoWiseSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo $item['designation']; ?> Officer <br><small>( Mr. <?php echo e(ucfirst($item['user'])); ?> )</small> </td>
                        <td><?php echo e($item['total_demands']); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($item['total_amount'], 2))); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($item['total_paid'], 2))); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($item['total_balance'], 2))); ?></td>
                       <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="<?php echo e($item['dyassingsection']); ?>"  data-type="<?php echo e($demandType); ?>" data-from="<?php echo e($filterDateFrom); ?>" data-to="<?php echo e($filterDateTo); ?>" target="_blank">View More</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" align="center">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
    <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th><?php echo e(collect($dyLdoWiseSummary)->sum('total_demands')); ?><br>
    	<!--<?php echo e(ucfirst(convertNumberToWords(collect($dyLdoWiseSummary)->sum('total_demands')))); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($dyLdoWiseSummary)->sum('total_amount'), 2))); ?><br>
    <!-- <?php echo e(collect($dyLdoWiseSummary)->sum('total_amount') > 0 ? convertToIndianCurrencyWords(round(collect($dyLdoWiseSummary)->sum('total_amount'), 2)) : 'Zero Rupees Only'); ?> -->    	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($dyLdoWiseSummary)->sum('total_paid'), 2))); ?><br>
    	<!--<?php echo e(collect($dyLdoWiseSummary)->sum('total_paid') > 0 ? convertToIndianCurrencyWords(round(collect($dyLdoWiseSummary)->sum('total_paid'), 2)) : 'Zero Rupees Only'); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($dyLdoWiseSummary)->sum('total_balance'), 2))); ?><br>
   <!-- <?php echo e(collect($dyLdoWiseSummary)->sum('total_balance') > 0 ? convertToIndianCurrencyWords(round(collect($dyLdoWiseSummary)->sum('total_balance'), 2)) : 'Zero Rupees Only'); ?> -->   	
    </th>
    <th></th>
</tr>
</tfoot>
        </table>
    </div>
    <div>
        <h5 >Section wise Demand Summary</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>S. No.</th>
                    <th>Section Name</th>
                   <!-- <th>Section Code</th>-->
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
$sortedSections = collect($sectionWiseSummary)->sortBy('section_name');
?>
                <?php $__empty_1 = true; $__currentLoopData = $sortedSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($section['section_name']); ?></td>
                       <!-- <td><?php echo e($section['section_code']); ?></td>-->
                        <td><?php echo e($section['total_demands']); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($section['total_amount'], 2))); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($section['total_paid'], 2))); ?></td>
                        <td>₹ <?php echo e(customNumFormat(round($section['total_balance'], 2))); ?></td>
                        <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="<?php echo e($section['section_code']); ?>" data-type="<?php echo e($demandType); ?>" data-from="<?php echo e($filterDateFrom); ?>" data-to="<?php echo e($filterDateTo); ?>" >View</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" align="center">No section-wise data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
              <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th><?php echo e(collect($sectionWiseSummary)->sum('total_demands')); ?><br>
    	<!--<?php echo e(ucfirst(convertNumberToWords(collect($sectionWiseSummary)->sum('total_demands')))); ?>-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($sectionWiseSummary)->sum('total_amount'), 2))); ?><br>
   <!-- <?php echo e(collect($sectionWiseSummary)->sum('total_amount') > 0 ? convertToIndianCurrencyWords(round(collect($sectionWiseSummary)->sum('total_amount'), 2)) : 'Zero Rupees Only'); ?>  -->  	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($sectionWiseSummary)->sum('total_paid'), 2))); ?><br>
    	<!--<?php echo e(collect($sectionWiseSummary)->sum('total_paid') > 0 ? convertToIndianCurrencyWords(round(collect($sectionWiseSummary)->sum('total_paid'), 2)) : 'Zero Rupees Only'); ?>-->

    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ <?php echo e(customNumFormat(round(collect($sectionWiseSummary)->sum('total_balance'), 2))); ?><br>
   <!-- <?php echo e(collect($sectionWiseSummary)->sum('total_balance') > 0 ? convertToIndianCurrencyWords(round(collect($sectionWiseSummary)->sum('total_balance'), 2)) : 'Zero Rupees Only'); ?>-->
    	
    </th>
    <th></th>
</tr>
</tfoot>
        </table>
    </div>
<?php endif; ?>
</div>

<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/include/partials/filtered_demand_data.blade.php ENDPATH**/ ?>