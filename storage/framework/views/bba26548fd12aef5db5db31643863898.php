<?php $__empty_1 = true; $__currentLoopData = $dataWithPagination; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $propertyDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
	<tr>
		<td><?php echo e($index + 1); ?></td>
		<td>
			<div class="cursor-pointer text-primary" data-bs-toggle="modal"
				data-bs-target="#exampleScrollableModal<?php echo e($propertyDetail->id); ?>"><?php echo e($propertyDetail->unique_propert_id); ?></div>
			<span class="text-secondary">(<?php echo e($propertyDetail->old_propert_id); ?>)</span>
			<div class="modal fade" id="exampleScrollableModal<?php echo e($propertyDetail->id); ?>" tabindex="-1" aria-hidden="true">
				<div class="modal-dialog modal-dialog-scrollable modal-xl">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Modal title</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<table class="table mb-0">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th scope="col">Process Of Transfer</th>
										<th scope="col">Transfer Date</th>
										<th scope="col">Lessee Name</th>
										<th scope="col">Lessee Age</th>
										<th scope="col">Property Share</th>
										<th scope="col">Lessee pan No.</th>
										<th scope="col">Lessee aadhar No.</th>
									</tr>
								</thead>
								<tbody>
									<?php $__currentLoopData = $propertyDetail->propertyTransferredLesseeDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lesseeDetails): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<th scope="row"><?php echo e($key + 1); ?></th>
											<td><?php echo e($lesseeDetails->process_of_transfer); ?></td>
											<td><?php echo e(($lesseeDetails->transferDate) ? $lesseeDetails->transferDate : $propertyDetail->propertyLeaseDetail->date_of_conveyance_deed); ?>

											</td>
											<td><?php echo e($lesseeDetails->lessee_name); ?></td>
											<td><?php echo e($lesseeDetails->lessee_age); ?></td>
											<td><?php echo e($lesseeDetails->property_share); ?></td>
											<td><?php echo e($lesseeDetails->lessee_pan_no); ?></td>
											<td><?php echo e($lesseeDetails->lessee_aadhar_no); ?></td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</td>
	        <td>
            
            <?php
                $remark = $propertyDetail->additional_remark;
                $modalId = 'remarkModal_' . $propertyDetail->id; // Ensure unique modal ID
            ?>

            <?php if($propertyDetail->alert_flag == '1'): ?>
                <span class="badge bg-danger">Yes</span><br>
            <?php endif; ?>

            <?php if(!empty($remark)): ?>
                <?php if(Str::length($remark) > 20): ?>
                    <?php echo e(Str::limit($remark, 20)); ?>

                    <!-- Trigger Modal -->
                    <a href="#" data-bs-toggle="modal" data-bs-target="#<?php echo e($modalId); ?>"
                        class="text-primary">More</a>
                    <!-- Modal -->
                    <div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1"
                        aria-labelledby="<?php echo e($modalId); ?>Label" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="<?php echo e($modalId); ?>Label">Additional Remark</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body"
                                    style="white-space: normal !important; word-wrap: break-word !important; overflow-wrap: break-word !important;">
                                    <?php echo e($remark); ?>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php echo e($remark); ?>

                <?php endif; ?>
            <?php endif; ?>

        </td>
		<td>
			<div><?php echo e($propertyDetail->unique_file_no); ?></div>
			<span class="text-secondary">(<?php echo e($propertyDetail->file_no); ?>)</span>
		</td>
		<td>
			<?php $__currentLoopData = $propertyDetail->splitedPropertyDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $chldProperty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<p>
					<a href="<?php echo e(route('propertyChildDetails', ['id' => $chldProperty->id])); ?>"><?php echo e($chldProperty->child_prop_id); ?></a>
					<?php if(!empty($chldProperty->old_property_id)): ?>
						<span class="text-secondary">(<?php echo e($chldProperty->old_property_id); ?>)</span>
					<?php endif; ?>
				</p>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</td>

		<td><?php echo e($item->itemNameById($propertyDetail->property_type)); ?></td>
		<td><?php echo e($item->itemNameById($propertyDetail->property_sub_type)); ?></td>
		<td><?php echo e($item->itemNameById($propertyDetail->status)); ?></td>
		<td><?php echo e($propertyDetail->section_code); ?></td>
		<td><?php if($propertyDetail->block_no): ?>
				<?php echo e($propertyDetail->block_no); ?>/
			<?php endif; ?>
			<?php echo e($propertyDetail->plot_or_property_no); ?>/
			<?php echo e($propertyDetail->oldColony->name); ?>

		</td>

		<td>
			<?php if($propertyDetail->propertyLeaseDetail): ?>
				<?php echo e($propertyDetail->propertyLeaseDetail->premium); ?>.<?php echo e($propertyDetail->propertyLeaseDetail->premium_in_paisa); ?><?php echo e($propertyDetail->propertyLeaseDetail->premium_in_aana); ?>

			<?php endif; ?>
		</td>

		<td>
			<?php if($propertyDetail->propertyLeaseDetail): ?>
				<?php echo e($propertyDetail->propertyLeaseDetail->gr_in_re_rs); ?>.<?php echo e($propertyDetail->propertyLeaseDetail->gr_in_paisa); ?>

			<?php endif; ?>
		</td>

		<td>
			<?php if($propertyDetail->propertyLeaseDetail): ?>
				<?php echo e($propertyDetail->propertyLeaseDetail->plot_area); ?>

				<?php echo e($item->itemNameById($propertyDetail->propertyLeaseDetail->unit)); ?>

			<?php endif; ?>
		</td>
		<td><?php echo e($user->userNameById($propertyDetail->created_by)); ?></td>
		<td>

			<?php
				// Convert UTC time to IST using Carbon
				$utcTime = $propertyDetail->created_at; // Assuming $propertyDetail contains your UTC timestamp
				$istTime = $utcTime->setTimezone('Asia/Kolkata');
			?>

			<?php echo e($istTime->format('Y-m-d H:i:s')); ?>

		</td>
		<td>
			<div class="d-flex gap-3">
				<a href="<?php echo e(route('viewDetails', ['property' => $propertyDetail->id])); ?>">
					<button type="button" class="btn btn-success px-5">View</button>
				</a>
				
				<?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'edit.property.details')): ?>
				<a href="<?php echo e(route('editDetails', ['property' => $propertyDetail->id])); ?>">
					<button type="button" class="btn btn-primary px-5">Edit</button>
				</a>
				<?php endif; ?>				
				<?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'delete.property.details')): ?>
				<button type=" button" data-bs-toggle="modal" data-bs-target="#deleteProperty_<?php echo e($propertyDetail->id); ?>"
					class="btn btn-danger px-5">Delete</button>
				<div class="modal fade" id="deleteProperty_<?php echo e($propertyDetail->id); ?>" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Are You Sure ?</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">Do you really want to delete this property? <br>This process cannot be
								undone. </div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<form method="post" action="<?php echo e(route('property.destroy', ['id' => $propertyDetail->id])); ?>">
									<?php echo csrf_field(); ?>
									<?php echo method_field('delete'); ?>
									<button type="submit" class="btn btn-danger">Confim Delete</button>
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</td>
		<!-- <td>
																	<a href="#">
																		<div class="col">
																			<button type="button" class="btn btn-info px-5 radius-30">View Detail</button>
																		</div>
																	</a>
																</td> -->
	</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
	<p>No Properties available</p>
<?php endif; ?>
<tr>
	<td colspan="14">
		<?php echo $dataWithPagination->links('pagination.custom'); ?>

	</td>
</tr>
<?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/mis/pagination_child.blade.php ENDPATH**/ ?>