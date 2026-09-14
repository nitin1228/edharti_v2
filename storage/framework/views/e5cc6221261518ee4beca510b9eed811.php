

<?php $__env->startSection('title', 'Revenue  Deatils'); ?>

<?php $__env->startSection('content'); ?>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Payment</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a></li>

               <!-- <li class="breadcrumb-item">Demand</li>-->
         
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <li class="breadcrumb-item active" aria-current="page">All Payment</li>
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
            <h5 class="card-title"><?php echo e(!empty($applications[0]['subhead_id'])? "Total Amount for ".getServiceNameById($applications[0]['subhead_id']) :"All Payments"); ?> </h5>
            <div class="table-responsive mt-2">
           
                    <?php if($query_type == "processingfee"): ?>
                          <table class="table table-bordered" id="tab-all-applications">
					        <thead>
					            <tr class="table-success">
					                <th>S. No.</th>
					                 <th>Property Id</th>
					                <th>Application No.</th>
					                <th>Application type</th>
					                 <th>Payment Id</th>  
					                <th>Transaction No.</th>                
					                <th>Amount</th>             
					                <th>Payment Mode</th>              
					                <th>Payment Date</th>
					                <th>Payee Details</th>
					                <th>Status</th>
                                     <th>Receipt</th> 
					                
					            </tr>
					        </thead>
       						<tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($app->unique_propert_id ?? 'N/A'); ?> <br><small>(<?php echo e($app->old_propert_id ?? 'N/A'); ?>) </small></td>
                                    <td><?php echo e($app->application_no ??'N/A'); ?></td>
                                    <td><?php echo e(getServiceNameById($app->service_type) ?? 'N/A'); ?></td>
                                    <td> <?php echo e($app->unique_payment_id ?? 'N/A'); ?></td>
                                   	<td> <?php echo e($app->transaction_id ?? 'N/A'); ?></td>
                                    <td>  &#8377; <?php echo e(customNumFormat($app->amount)); ?></td>
                                    <td> <?php echo e(getServiceNameById($app->payment_mode) ?? 'N/A'); ?></td>
                                    <td><?php echo e(date('d-m-Y',strtotime($app->updated_at))); ?></td>
                                     <td><?php echo e($app->first_name); ?><br>
                                     	<?php echo e($app->email); ?><br>
                                     	<?php echo e($app->mobile); ?>

                                     </td>
                                    <td><?php echo e(getServiceNameById($app->status)); ?></td>                                                                     
                                   <td>  <?php if($app->status == "1546"): ?><a href="<?php echo e(route('downloadPaymentReceiptPdf',$app->unique_payment_id)); ?>">  
                                   <button class="btn btn-primary btn-sm" type="button">Receipt</button></a>
                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>
                                   </td> 
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" align="center">
                                        <h5>No Data to Display</h5>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
							    <tr class="table-secondary">
							        <th colspan="6" class="text-end">Total:</th>
							        <th class="text-wrap" style="max-width: 150px; white-space: normal;">
							            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('amount'), 2))); ?><br>           
							           
							        </th> 
							        <th colspan="5"></th>
							    </tr>
							</tfoot>
							    </table>
							<?php elseif($query_type == "summary_report"): ?>
                          <table class="table table-bordered" id="tab-all-applications">
					        <thead>
					            <tr class="table-success">
					                <th>S. No.</th>
					                 <th>Property Id</th>
					                <th>Application No.</th>
					                <th>Application type</th>
					                 <th>Payment Id</th>  
					                <th>Transaction No.</th>                
					                <th>Amount</th>             
					                <th>Payment Mode</th>              
					                <th>Payment Date</th>
					                <th>Payee Details</th>
					                <th>Status</th>		
                                     <th>Receipt</th> 			                
					            </tr>
					        </thead>
       						<tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($app->unique_propert_id ?? 'N/A'); ?> <br><small>(<?php echo e($app->old_propert_id ?? 'N/A'); ?>) </small></td>
                                    <td><?php echo e($app->application_no ??'N/A'); ?></td>
                                    <td><?php echo e(getServiceNameById($app->service_type) ?? 'N/A'); ?></td>
                                    <td> <?php echo e($app->unique_payment_id ?? 'N/A'); ?></td>
                                   	<td> <?php echo e($app->transaction_id ?? 'N/A'); ?></td>
                                    <td>  &#8377; <?php echo e(customNumFormat($app->amount)); ?></td>
                                    <td> <?php echo e(getServiceNameById($app->payment_mode) ?? 'N/A'); ?></td>
                                    <td><?php echo e(date('d-m-Y',strtotime($app->updated_at))); ?></td>
                                     <td><?php echo e($app->first_name); ?><br>
                                     	<?php echo e($app->email); ?><br>
                                     	<?php echo e($app->mobile); ?>

                                     </td>
                                    <td><?php echo e(getServiceNameById($app->status)); ?></td>                                                                     
                                   <td>  <?php if($app->status == "1546"): ?><a href="<?php echo e(route('downloadPaymentReceiptPdf',$app->unique_payment_id)); ?>">  
                                   <button class="btn btn-primary btn-sm" type="button">Receipt</button></a>
                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>
                                   </td> 
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" align="center">
                                        <h5>No Data to Display</h5>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
							    <tr class="table-secondary">
							     <th></th>
							      <th></th>
							      <th></th>
							      <th></th>
							      <th></th>							      
							        <th  class="text-end">Total:</th>
							        <th class="text-wrap" style="max-width: 150px; white-space: normal;">
							            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('amount'), 2))); ?><br>           
							           
							        </th> 
							        <th colspan="5"></th>
							    </tr>
							</tfoot>
							    </table>
					<?php elseif($query_type == 'summaryallreport'): ?>	
					<table class="table table-bordered" id="tab-all-applications">
						    <thead>
						        <tr class="table-success">
						            <th>S. No.</th>
						            <th>Type</th>
						            <th>Property ID</th>						            
						            <th>Reference No.</th>
						             <th>Section</th>
						             <th>Known as</th>
						            <th>Payment ID</th>
						            <th>Transaction No.</th>
						            <th>Amount</th>
						            <th>Payment Date</th>
						            <th>Payee Details</th>
						            <th>Status</th>
                                     <th>Receipt</th> 	
						        </tr>
						    </thead>

    <tbody>
        <?php
            $i = 1;
        ?>

        <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i++); ?></td>
                <td>
                
                <?php echo e(getServiceNameById($row->subhead_id)); ?>

                <?php echo e(getServiceNameById($row->type) == 'Application' ? "Application Processing Fee" : (getServiceNameById($row->type) =='Demand' ?  getServiceNameById($row->type) : getServiceNameById($row->type))); ?> 
                </td>
                <td> <?php echo e($row->unique_propert_id ?? 'N/A'); ?><br>
                    <small>(<?php echo e($row->old_propert_id ?? 'N/A'); ?>)</small>
                </td>               
                <td> <?php echo e($row->application_no  ?? $row->demand_unique_id  ?? 'N/A'); ?> </td>
                 <td><?php echo e($row->section_code); ?></td>
                <td><div class="break-text"><?php echo e($row->property_known_as); ?></div></td>
                <td><?php echo e($row->unique_payment_id ?? 'N/A'); ?></td>
                <td><?php echo e($row->transaction_id ?? 'N/A'); ?></td>
                <td>               
                    ₹ <?php echo e(customNumFormat( $row->demand_detail_amount  != 0 ? $row->demand_detail_amount :
                        $row->amount 
                        ?? $row->paid_amount 
                        ?? 0
                    )); ?>

                </td>
                <td>
                    <?php echo e(isset($row->updated_at) 
                        ? date('d-m-Y', strtotime($row->updated_at)) 
                        : 'N/A'); ?>

                </td>
                <td>
                    <?php echo e($row->first_name ?? 'N/A'); ?><br>
                    <?php echo e($row->email ?? ''); ?><br>
                    <?php echo e($row->mobile ?? ''); ?>

                </td>
                <td>
                    <?php if(isset($row->balance_amount)): ?>
                        <?php echo e($row->balance_amount == 0 
                            ? 'Success' 
                            : getServiceNameById($row->status)); ?>

                    <?php else: ?>
                        <?php echo e(getServiceNameById($row->status)); ?>

                    <?php endif; ?>
                </td>
                <td>  <?php if($row->status == "1546"): ?><a href="<?php echo e(route('downloadPaymentReceiptPdf',$row->unique_payment_id)); ?>">  
                                   <button class="btn btn-primary btn-sm" type="button">Receipt</button></a>
                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>
                                   </td> 
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="10" class="text-center">
                    No Records Found
                </td>
            </tr>
        <?php endif; ?>
    </tbody>

   <tfoot>
    <tr class="table-secondary">
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
        <th  class="text-end">Total Paid Amount:</th>
        <th>
        
            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('amount'), 2))); ?><br>   
          <!--  ₹ <?php echo e(customNumFormat(
                collect($applications)->sum(function ($r) {

                    // Case 1: demand exists → sum demand details ONLY
                    if (!empty($r->demand_id)) {
                        return (float) ($r->demand_detail_amount ?? 0);
                    }
                    // Case 2: no demand → sum payment amount
                    return (float) ($r->amount ?? $r->paid_amount ?? 0);
                })
            )); ?>-->
        </th>
        <th></th>
          <th ></th>
            <th></th>
             <th></th>
    </tr>
</tfoot>

</table>	        
						<?php elseif($query_type == "summaryall"): ?>						
							<table class="table table-bordered" id="tab-all-applications">
						    <thead>
						        <tr class="table-success">
						            <th>S. No.</th>
						            <th>Type</th>
						            <th>Property ID</th>						            
						            <th>Reference No.</th>
						             <th>Section</th>
						             <th>Known as</th>
						            <th>Payment ID</th>
						            <th>Transaction No.</th>
						            <th>Amount</th>
						            <th>Payment Date</th>
						            <th>Payee Details</th>
						            <th>Status</th>
                                    <th>Receipt</th> 
						        </tr>
						    </thead>

    <tbody>
        <?php
            $i = 1;
        ?>

        <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i++); ?></td>
                <td>
                
                <?php echo e(getServiceNameById($row->subhead_id)); ?>

                <?php echo e(getServiceNameById($row->type) == 'Application' ? "Application Processing Fee" : (getServiceNameById($row->type) =='Demand' ?  getServiceNameById($row->type) : getServiceNameById($row->type))); ?> 
                </td>
                <td> <?php echo e($row->unique_propert_id ?? 'N/A'); ?><br>
                    <small>(<?php echo e($row->old_propert_id ?? 'N/A'); ?>)</small>
                </td>               
                <td> <?php echo e($row->application_no  ?? $row->demand_unique_id  ?? 'N/A'); ?> </td>
                 <td><?php echo e($row->section_code); ?></td>
                <td><div class="break-text"><?php echo e($row->property_known_as); ?></div></td>
                <td><?php echo e($row->unique_payment_id ?? 'N/A'); ?></td>
                <td><?php echo e($row->transaction_id ?? 'N/A'); ?></td>
                <td>               
                    ₹ <?php echo e(customNumFormat( $row->demand_detail_amount  != 0 ? $row->demand_detail_amount :
                        $row->amount 
                        ?? $row->paid_amount 
                        ?? 0
                    )); ?>

                </td>
                <td>
                    <?php echo e(isset($row->updated_at) 
                        ? date('d-m-Y', strtotime($row->updated_at)) 
                        : 'N/A'); ?>

                </td>
                <td>
                    <?php echo e($row->first_name ?? 'N/A'); ?><br>
                    <?php echo e($row->email ?? ''); ?><br>
                    <?php echo e($row->mobile ?? ''); ?>

                </td>
                <td>
                    <?php if(isset($row->balance_amount)): ?>
                        <?php echo e($row->balance_amount == 0 
                            ? 'Success' 
                            : getServiceNameById($row->status)); ?>

                    <?php else: ?>
                        <?php echo e(getServiceNameById($row->status)); ?>

                    <?php endif; ?>
                </td>
                 <td>  <?php if($row->status == "1546"): ?><a href="<?php echo e(route('downloadPaymentReceiptPdf',$row->unique_payment_id)); ?>">  
                                   <button class="btn btn-primary btn-sm" type="button">Receipt</button></a>
                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>
                                   </td> 
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="10" class="text-center">
                    No Records Found
                </td>
            </tr>
        <?php endif; ?>
    </tbody>

   <tfoot>
    <tr class="table-secondary">
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
        <th  class="text-end">Total Paid Amount:</th>
        <th>
        
            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('amount'), 2))); ?><br>   
          <!--  ₹ <?php echo e(customNumFormat(
                collect($applications)->sum(function ($r) {

                    // Case 1: demand exists → sum demand details ONLY
                    if (!empty($r->demand_id)) {
                        return (float) ($r->demand_detail_amount ?? 0);
                    }
                    // Case 2: no demand → sum payment amount
                    return (float) ($r->amount ?? $r->paid_amount ?? 0);
                })
            )); ?>-->
        </th>
        <th></th>
          <th ></th>
            <th></th>
             <th></th>
    </tr>
</tfoot>

</table>
<?php elseif($query_type == "summaryallold"): ?>						
							<table class="table table-bordered" id="tab-all-applications">
						    <thead>
						        <tr class="table-success">
						            <th>S. No.</th>
						            <th>Type</th>
						            <th>Property ID</th>						            
						            <th>Reference No.</th>
						             <th>Section</th>
						             <th>Known as</th>
						            <th>Payment ID</th>
						            <th>Transaction No.</th>
						            <th>Amount</th>
						            <th>Payment Date</th>
						            <th>Payee Details</th>
						            <th>Status</th>
						        </tr>
						    </thead>

    <tbody>
        <?php
            $i = 1;
        ?>

        <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($i++); ?></td>
                <td>
                
                <?php echo e(getServiceNameById($row->subhead_id)); ?>

                <?php echo e(getServiceNameById($row->type) == 'Application' ? "Application Processing Fee" : (getServiceNameById($row->type) =='Demand' ?  getServiceNameById($row->type) : getServiceNameById($row->type))); ?> 
                </td>
                <td> <?php echo e($row->unique_propert_id ?? 'N/A'); ?><br>
                    <small>(<?php echo e($row->old_propert_id ?? 'N/A'); ?>)</small>
                </td>               
                <td> <?php echo e($row->application_no  ?? $row->demand_unique_id  ?? 'N/A'); ?> </td>
                 <td><?php echo e($row->section_code); ?></td>
                <td><div class="break-text"><?php echo e($row->property_known_as); ?></div></td>
                <td><?php echo e($row->unique_payment_id ?? 'N/A'); ?></td>
                <td><?php echo e($row->transaction_id ?? 'N/A'); ?></td>
                <td>               
                    ₹ <?php echo e(customNumFormat( $row->demand_detail_amount  != 0 ? $row->demand_detail_amount :
                        $row->amount 
                        ?? $row->paid_amount 
                        ?? 0
                    )); ?>

                </td>
                <td>
                    <?php echo e(isset($row->updated_at) 
                        ? date('d-m-Y', strtotime($row->updated_at)) 
                        : 'N/A'); ?>

                </td>
                <td>
                    <?php echo e($row->first_name ?? 'N/A'); ?><br>
                    <?php echo e($row->email ?? ''); ?><br>
                    <?php echo e($row->mobile ?? ''); ?>

                </td>
                <td>
                    <?php if(isset($row->balance_amount)): ?>
                        <?php echo e($row->balance_amount == 0 
                            ? 'Success' 
                            : getServiceNameById($row->status)); ?>

                    <?php else: ?>
                        <?php echo e(getServiceNameById($row->status)); ?>

                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="10" class="text-center">
                    No Records Found
                </td>
            </tr>
        <?php endif; ?>
    </tbody>

   <tfoot>
    <tr class="table-secondary">
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
        <th  class="text-end">Total Paid Amount:</th>
        <th>
        
            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('paid_amount'), 2))); ?><br>   
          <!--  ₹ <?php echo e(customNumFormat(
                collect($applications)->sum(function ($r) {

                    // Case 1: demand exists → sum demand details ONLY
                    if (!empty($r->demand_id)) {
                        return (float) ($r->demand_detail_amount ?? 0);
                    }
                    // Case 2: no demand → sum payment amount
                    return (float) ($r->amount ?? $r->paid_amount ?? 0);
                })
            )); ?>-->
        </th>
        <th></th>
          <th ></th>
            <th></th>
    </tr>
</tfoot>

</table>
 <?php elseif($query_type == "eDhartiolddemands"): ?>
                          <table class="table table-bordered" id="tab-all-applications">
					        <thead>
					            <tr class="table-success">
					                <th>S. No.</th>
					                 <th>Property Id</th>
					                <th>Demand Id</th>	
					               <th>Payment ID</th>
						            <th>Transaction No.</th>
						             <th>Transaction Date </th>
						              <th>Amount</th> 
						            <th>Payee Details</th>
						            <th>Status</th> 
					                
					            </tr>
					        </thead>
       						<tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="<?php if($app->data_source == 'OLD'): ?> lightred <?php else: ?> lightgreen <?php endif; ?>" >
                                    <td><?php echo e($loop->iteration); ?> </td>
                                    <td><?php echo e($app->unique_propert_id ?? 'N/A'); ?> <br><small>(<?php echo e($app->old_propert_id ?? 'N/A'); ?>) </small></td>
                                    <td><?php echo e($app->DemandID ??'N/A'); ?><br>
                                      <a href="javascript:void(0);" 
								       class="text-primary viewdemanddetails" 
								       data-id="<?php echo e($app->DemandID); ?>" 
								       data-toggle="tooltip" 
									   data-placement="top" 
									   title="View Demand Details">
								        (View Details)
								    </a>
								    </td>
                                      <td><?php echo e($app->unique_payment_id ??'N/A'); ?></td>    
                                        <td><?php echo e($app->transaction_id ??'N/A'); ?></td>                                         
                                     <td><?php echo e(isset($app->updated_at)? date('d-m-Y',strtotime($app->updated_at)):"N/A"); ?></td>    
                                      <td>  &#8377; <?php echo e(customNumFormat($app->paid_amount)); ?></td>                                   
                                     <td><?php echo e($app->first_name); ?><br>
                                     	<?php echo e($app->email); ?><br>
                                     	<?php echo e($app->mobile); ?>

                                     </td>
                                    <td><?php echo e(getServiceNameById($app->status)); ?></td>                                                                    
                                  
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" align="center">
                                        <h5>No Data to Display</h5>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
							    <tr class="table-secondary">
							        <th colspan="6" class="text-end">Total:</th>
							        <th class="text-wrap" style="max-width: 150px; white-space: normal;">
							            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('paid_amount'), 2))); ?><br>           
							           
							        </th> 
							        <th colspan="4"></th>
							    </tr>
							</tfoot>
							    </table>	
							    <?php elseif($query_type == "combinedold"): ?>
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
				                <th>Total Amount</th>
				                <th>Paid Amount</th>
				                <th>Outstanding Amount</th>
				                  <th>Payee Details</th>
				                   <th>Payment Id</th>  
					                <th>Transaction No.</th>  
				                <th>Status</th>
				            </tr>
				        </thead>
				        <tbody>				       
				            <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
				                <tr>
				                    <td><?php echo e($loop->iteration); ?></td>
				                    <td> 
				                  
				                    <?php if( $demand->unique_id != ""): ?>                 
				                    <a href="<?php echo e(route('ViewDemand',$demand->demaid)); ?>"  data-toggle="tooltip" 
									   data-placement="top" 
									   title="View Details"><?php echo e($demand->unique_id); ?></a>
				                     <a href="<?php echo e(asset('storage/public' . $demand->demand_path)); ?>" 
									   target="_blank"
									   data-toggle="tooltip" 
									   data-placement="top" 
									   title="Download Demand Letter">
									    <i class="lni lni-cloud-download text-danger" 
									       style="font-size: 25px; vertical-align: middle;">
									    </i>
									</a>
									<?php else: ?> 
									N/A
									<?php endif; ?>
									
				                    </td>
				                    <td><?php echo e(date('d-m-Y',strtotime($demand->updated_at))); ?></td>
				                    <td><?php echo e($demand->unique_propert_id); ?><br><small>(<?php echo e($demand->old_propert_id); ?>)</small></td>
				                    <td><?php echo e($demand->section_code ?? "N/A"); ?></td>
				                    <td><div class="break-text"><?php echo e($demand->property_known_as); ?></div></td>
				                    <td><?php echo e($demand->current_fy ?? "N/A"); ?></td>
				                    <td>₹ <?php echo e(customNumFormat(round($demand->net_total, 2)) ?? 0); ?></td>                    
				                    <td>₹ <?php echo e(customNumFormat(round($demand->paid_amount, 2)) ?? 0); ?></td>	
				                    <td>₹ <?php echo e(customNumFormat(round($demand->balance_amount, 2)) ?? 0); ?></td>	
				                      <td><?php echo e($demand->first_name ?? "N/A"); ?><br>
                                     	<?php echo e($demand->email); ?><br>
                                     	<?php echo e($demand->mobile); ?>

                                     </td> 	
                                     <td>
                                        <?php if($demand->unique_id != ""): ?>
											    <?php echo $demand->unique_payment_ids 
										        ? str_replace(',', '<br>', $demand->unique_payment_ids) 
										        : 'N/A'; ?>

											    <?php else: ?> 
											     <?php echo e($demand->unique_payment_id ?? 'N/A'); ?>

											    <?php endif; ?>
											</td>
											<td> <?php if($demand->unique_id != ""): ?>
											<?php echo $demand->transaction_ids  
										        ? str_replace(',', '<br>', $demand->transaction_ids ) 
										        : 'N/A'; ?>											   
											    <?php else: ?>
											     <?php echo e($demand->transaction_id ?? 'N/A'); ?>

											     <?php endif; ?>
											</td>	 
											                                    	                   
				                    <td><?php echo e($demand->balance_amount == 0 ?"Success" : getServiceNameById($demand->status)); ?></td>                   
				                </tr>
				            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
				                <tr>
				                    <td colspan="12" align="center">Sorry, No records found.</td>
				                </tr>
				            <?php endif; ?>
				        </tbody>      

				<tfoot>
				    <tr class="table-secondary">
				        <th colspan="7" class="text-end">Total:</th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				        
				       
				            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('net_total'), 2))); ?>          
				          
				        </th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('paid_amount'), 2))); ?>

				          
				        </th>
				       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				          ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('balance_amount'), 2))); ?>

				        </th>
				        <th colspan="4"></th>
				    </tr>
				</tfoot>
				    </table>
				    <?php else: ?> 
				    				    
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
				                <th>Total Amount</th>
				                <th>Paid Amount</th>
				                <th>Outstanding Amount</th>
				                  <th>Payee Details</th>
				                   <th>Payment Id</th>  
					                <th>Transaction No.</th>  
				                <th>Status</th>
                                <th>Receipt</th> 
				            </tr>
				        </thead>
				        <tbody>				       
				            <?php $__empty_1 = true; $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
				                <tr>
				                    <td><?php echo e($loop->iteration); ?></td>
				                    <td> 				                 
				                    <?php if( $demand->unique_id != ""): ?>                 
				                    <a href="<?php echo e(route('ViewDemand',$demand->demaid)); ?>"  data-toggle="tooltip" 
									   data-placement="top" 
									   title="View Details"><?php echo e($demand->unique_id); ?></a>
				                     <a href="<?php echo e(asset('storage/public/' . $demand->demand_path)); ?>" 
									   target="_blank"
									   data-toggle="tooltip" 
									   data-placement="top" 
									   title="Download Demand Letter">
									    <i class="lni lni-cloud-download text-danger" 
									       style="font-size: 25px; vertical-align: middle;">
									    </i>
									</a>
									<?php else: ?> 
									N/A
									<?php endif; ?>
									
				                    </td>
				                    <td><?php echo e(date('d-m-Y',strtotime($demand->updated_at))); ?></td>
				                    <td><?php echo e($demand->unique_propert_id); ?><br><small>(<?php echo e($demand->old_propert_id); ?>)</small></td>
				                    <td><?php echo e($demand->section_code ?? "N/A"); ?></td>
				                    <td><div class="break-text"><?php echo e($demand->property_known_as); ?></div></td>
				                    <td><?php echo e($demand->current_fy ?? "N/A"); ?></td>
				                    <td>₹ <?php echo e(customNumFormat(round($demand->net_total, 2)) ?? 0); ?></td>                    
				                    <td>₹ <?php echo e(customNumFormat(round($demand->paid_amount, 2)) ?? 0); ?></td>	
				                    <td>₹ <?php echo e(customNumFormat(round($demand->balance_amount, 2)) ?? 0); ?></td>	
				                      <td><?php echo e($demand->first_name ?? "N/A"); ?><br>
                                     	<?php echo e($demand->email); ?><br>
                                     	<?php echo e($demand->mobile); ?>

                                     </td> 	
                                     <td>
                                        <?php if($demand->unique_id != ""): ?>
											    <?php echo $demand->unique_payment_ids 
										        ? str_replace(',', '<br>', $demand->unique_payment_ids) 
										        : 'N/A'; ?>

											    <?php else: ?> 
											     <?php echo e($demand->unique_payment_id ?? 'N/A'); ?>

											    <?php endif; ?>
											</td>
											<td> <?php if($demand->unique_id != ""): ?>
											<?php echo $demand->transaction_ids  
										        ? str_replace(',', '<br>', $demand->transaction_ids ) 
										        : 'N/A'; ?>											   
											    <?php else: ?>
											     <?php echo e($demand->transaction_id ?? 'N/A'); ?>

											     <?php endif; ?>
											</td>	 
											                                    	                   
				                    <td><?php echo e($demand->balance_amount == 0 ?"Success" : getServiceNameById($demand->status)); ?></td>     
                                    <td>  <?php if($demand->status == "1546"): ?><a href="<?php echo e(route('downloadPaymentReceiptPdf',$demand->unique_payment_id)); ?>">  
                                   <button class="btn btn-primary btn-sm" type="button">Receipt</button></a>
                                   <?php else: ?> 
                                   N/A
                                   <?php endif; ?>
                                   </td>               
				                </tr>
				            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
				                <tr>
				                    <td colspan="12" align="center">Sorry, No records found.</td>
				                </tr>
				            <?php endif; ?>
				        </tbody>      

				<tfoot>
				    <tr class="table-secondary">
				        <th colspan="7" class="text-end">Total:</th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				        
				       
				            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('net_total'), 2))); ?>          
				          
				        </th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				            ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('paid_amount'), 2))); ?>

				          
				        </th>
				       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				          ₹ <?php echo e(customNumFormat(round(collect($applications)->sum('balance_amount'), 2))); ?>

				        </th>
				        <th colspan="5"></th>
				    </tr>
				</tfoot>
				    </table>
				    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="demandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">      
      <div class="modal-header">
    <h5 class="modal-title">eDharti 1.0 Demand Headwise Breakup</h5>
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
          🖨 Print
        </button>
      </div>

    </div>
  </div>
</div>
<style>.break-text {
    max-width: 450px;   
    white-space: normal;
    word-break: normal;    
    overflow-wrap: break-word;
}
</style>



<?php $__env->stopSection(); ?>
<?php $__env->startSection('footerScript'); ?>
	<script>
	$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
	function printBreakup() {
    var printContents = document.getElementById("breakupBody").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = `
        <html>
        <head>
            <title>eDharti 1.0 Headwise Breakup</title>
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
	 //$('[data-toggle="tooltip"]').tooltip();	 
	  $('.viewdemanddetails').click(function(){
	        let demand_id = $(this).data('id');	        
	        $('#demandModal').modal('show');
	        $('#breakupBody').html("Loading...");
	        $.ajax({
	            url: "<?php echo e(route('get.olddemandbreakup')); ?>",
	            type: "GET",
	            data: { demand_id: demand_id },
	            success: function(response){	            	
	                $('#breakupBody').html(response);
	            }
	        });
	    });
		$(document).ready(function () {

    var table = $('#tab-all-applications').DataTable({
        responsive: false,
        searching: true,
        paging: true,
        search: false,
        info: false,
        dom: 'Bfrtip',

        buttons: [
            {
                extend: 'excelHtml5',
                text: 'EXCEL',

                footer: true, // ✅ IMPORTANT

                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return data.replace(/₹|,/g, '').trim(); // clean currency
                        },
                        footer: function (data) {
                            return data.replace(/₹|,/g, '').trim(); // clean footer
                        }
                    }
                }
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',

                footer: true,

                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                pageSize: 'A3',
                orientation: 'landscape',

                footer: true, // ✅ IMPORTANT

                exportOptions: {
                    columns: ':visible'
                },

                // 🔥 MAIN FIX FOR PDF FOOTER
//                customize: function (doc) {
//
//                    var footer = [];
//
//                    $('#tab-all-applications tfoot th').each(function () {
//                        footer.push({
//                            text: $(this).text(),
//                            style: 'tableFooter'
//                        });
//                    });
//
//                    // add footer row in PDF table
//                    doc.content[1].table.body.push(footer);
//                }
            }
        ],

        columnDefs: [
            { orderable: false, targets: 5 },
            { orderable: true, targets: '_all' }
        ]
    });

});
	</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/payment/payment-summary-details.blade.php ENDPATH**/ ?>