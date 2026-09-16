

<?php $__env->startSection('title', 'Plot Details'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        @media print {

            /* Hide all navigation and layout parts */
            .sidebar-wrapper,
            .backButton,
            .switcher-wrapper,
            .menu,
            .navbar,
            .page-breadcrumb,
            .btn,
            .card .btn-group,
            footer,
            .footer,
            header,
            hr,
            .no-print {
                display: none !important;
                width: 0 !important;
                visibility: hidden !important;
            }

            /* Expand the content area */
            .content,
            #content,
            .main-content,
            .card {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
            }
            .wrapper.toggled .page-wrapper,
            .wrapper .page-wrapper{
                margin:0!important;
                width: 100%;
            }
            .page-content, .page-content > .card > .card-body{
                padding:0!important;
            }
            .sidebar-wrapper{
                width:0!important;
            }
        }        

        .pagination .active a {
            color: #ffffff !important;

        }
        .mainPropertyDetail{
            font-weight: 600;
            font-size: 22px;
            padding-bottom: 12px;
            display: flex;
            justify-content: center;
            text-transform: uppercase;
        }
    </style>
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Properties
</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item active" aria-current="page">Properties</li>
                <li class="breadcrumb-item active" aria-current="page">Views</li>
                <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo e(route('propertDetails')); ?>">Plots</a></li>
                <li class="breadcrumb-item active" aria-current="page">details</li>
            </ol>
        </nav>
    </div>
</div>
    <!--breadcrumb-->

    <hr>

    <div class="card shadow-sm mb-4">
        <div class="card-body">


            <?php if(isset($flatData['flatDetails']->flat_number)): ?>
                    <!-- <h5 class="mb-4 pt-3 text-decoration-underline">FLAT DETAILS</h5> -->
                    <div class="part-title">
                        <h5>FLAT DETAILS</h5>
                    </div>

                    <!-- <div class="part-details">
                        <div class="container-fluid">
                        
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td><b>Flat Id : </b><?php echo e($flatData['flatDetails']->unique_flat_id); ?></td>
                                        <td><b>Flat File No. : </b><?php echo e($flatData['flatDetails']->unique_file_no); ?></td>
                                        <td><b>Flat No : </b><?php echo e($flatData['flatDetails']->flat_number); ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Builder / Developer Name : </b><?php echo e($flatData['flatDetails']->builder_developer_name); ?></td>
                                        <td><b>Original Buyer Name : </b><?php echo e($flatData['flatDetails']->original_buyer_name); ?></td>
                                        <td><b>Purchase Date : </b><?php echo e(\Carbon\Carbon::parse($flatData['flatDetails']->purchase_date)->format('m/d/Y')); ?>

                                        </td>                                            </td>
                                    </tr>
                                    <tr>
                                        <td><b>Present Occupent Name : </b><?php echo e($flatData['flatDetails']->present_occupant_name); ?></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->

                     <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered property-table-info">
                                            <tbody>
                                                <tr>
                                                    <th>Flat Id :</th>
                                                    <td><?php echo e($flatData['flatDetails']->unique_flat_id ?? ''); ?></td>
                                                    <th>File Number :</th>
                                                    <td><?php echo e($flatData['flatDetails']->unique_file_no ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Floor :</th>
                                                    <td><?php echo e($flatData['flatDetails']->floor ?? ''); ?></td>
                                                    <th>Flat Number :</th>
                                                    <td><?php echo e($flatData['flatDetails']->flat_number ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Flat Status :</th>
                                                    <td><?php echo e(optional($flatData['flatDetails'])->property_flat_status == 951 ? 'Lease Hold' : 'Free Hold'); ?>

                                                    </td>
                                                    <th>Flat Area in Sqm :</th>
                                                    <td><?php echo e($flatData['flatDetails']->area_in_sqm ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Purchase Date :</th>
                                                    <td><?php echo e(!empty($flatData['flatDetails']->purchase_date) ? \Carbon\Carbon::parse($flatData['flatDetails']->purchase_date)->format('d-m-Y') : ''); ?>

                                                    </td>
                                                    <th>Original Buyer :</th>
                                                    <td><?php echo e($flatData['flatDetails']->original_buyer_name ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Present Occupant :</th>
                                                    <td><?php echo e($flatData['flatDetails']->present_occupant_name ?? ''); ?></td>
                                                    <th>Builder / Developer Name :</th>
                                                    <td><?php echo e($flatData['flatDetails']->builder_developer_name ?? ''); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                   
                            
            <?php else: ?>
                <?php if(!empty($flatData['flatDetails']['flat_number'])): ?>
                <!-- <h5 class="mb-4 pt-3 text-decoration-underline">FLAT DETAILS</h5> -->
                <div class="part-title">
                    <h5>FLAT DETAILS</h5>
                </div>

                <div class="part-details">
                    <div class="container-fluid">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><b>Flat Number : </b><?php echo e($flatData['flatDetails']['flat_number']); ?></td>
                                    <td><b>No MIS Found For This Flat Property </b></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(isset($flatData['flatDetails']->flat_number) || !empty($flatData['flatDetails']['flat_number'])): ?>
            <div>
                <hr>
                <div class="mainPropertyDetail">
                    Main Property Details
                </div>
            </div>
            <?php endif; ?>



            <!-- <div class="container"> -->
                <div class="part-title">
                    <h5>BASIC DETAILS</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><b>New Property Id: </b> <?php echo e($viewDetails->unique_propert_id); ?></td>
                                    <td><b>Old Property Id: </b> <?php echo e($viewDetails->old_propert_id); ?></td>
                                </tr>
                                <tr>
                                    <td><b>More than 1 Property IDs: </b> <?php echo e($viewDetails->is_multiple_ids ? 'Yes' : 'No'); ?>

                                    </td>
                                    <td><b>File No.: </b> <?php echo e($viewDetails->file_no); ?></td>
                                </tr>
                                <tr>
                                    <td><b>Computer generated file no: </b> <?php echo e($viewDetails->unique_file_no); ?> </td>
                                    <td><b>Colony Name(Old): </b> <?php echo e($viewDetails->oldColony->name); ?> </td>
                                </tr>
                                <tr>
                                    <td><b>Colony Name(Present):</b> <?php echo e($viewDetails->newColony->name); ?> </td>
                                    <td><b>Property Status: </b> <?php echo e($item->itemNameById($viewDetails->status)); ?> </td>

                                </tr>
                                <tr>
                                    <td><b>Land Type:</b> <?php echo e($item->itemNameById($viewDetails->land_type)); ?></td>
                                    <td> </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                    
                

                <!-- <h5 class="mb-4 pt-3 text-decoration-underline">LEASE DETAILS</h5> -->
                <div class="part-title">
                    <h5>LEASE DETAILS</h5>
                </div>

                <div class="part-details">
                    <div class="container-fluid">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td><b>Type of Lease: </b>
                                        <?php echo e($item->itemNameById($viewDetails->propertyLeaseDetail->type_of_lease)); ?></td>
                                    <td><b>Date of Execution: </b>
                                    <?php echo e(!empty($viewDetails->propertyLeaseDetail->doe) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->doe)->format('d-m-Y') : ''); ?>

                                </td>
                                </tr>
                                <tr>
                                    <td><b>Lease/Allotment No.: </b> <?php echo e($viewDetails->lease_no ?? 'NA'); ?></td>
                                     <td><b>Date of Expiration:
                                    </b><?php echo e(!empty($viewDetails->propertyLeaseDetail->date_of_expiration) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->date_of_expiration)->format('d-m-Y') : ''); ?>

                                </td>
                                </tr>
                                <tr>
                                    <td><b>Date of Allotment:
                                    </b><?php echo e(!empty($viewDetails->propertyLeaseDetail->doa) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->doa)->format('d-m-Y') : ''); ?>

                                </td>
                                    <td><b>Block No.: </b> <?php echo e($viewDetails->block_no); ?></td>
                                </tr>
                                <tr>
                                    <td><b>Plot No.: </b> <?php echo e($viewDetails->plot_or_property_no); ?> </td>
                                    <?php
                                    $names = [];
                                    foreach ($viewDetails->propertyTransferredLesseeDetails as $transferDetail) {
                                        $name = $transferDetail->process_of_transfer;
                                        if ($name == 'Original') {
                                            $names[] = $transferDetail->lessee_name;
                                        }
                                    }
                                    ?>
                                    <td><b>In Favour Of: </b><?php echo e(implode(', ', $names)); ?> </td>
                                </tr>
                                <tr>
                                    <td><b>Presently Known As: </b><?php echo e($viewDetails->propertyLeaseDetail->presently_known_as); ?>

                                    </td>
                                    <td><b>Area: </b> <?php echo e($viewDetails->propertyLeaseDetail->plot_area); ?>

                                        <?php echo e($item->itemNameById($viewDetails->propertyLeaseDetail->unit)); ?> <span
                                            class="text-secondary">(<?php echo e($viewDetails->propertyLeaseDetail->plot_area_in_sqm); ?>

                                            Sq
                                            Meter)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Premium (Re/ Rs): </b>₹
                                        <?php echo e($viewDetails->propertyLeaseDetail->premium ?? '0'); ?>.<?php echo e($viewDetails->propertyLeaseDetail->premium_in_paisa); ?><?php echo e($viewDetails->propertyLeaseDetail->premium_in_aana); ?>

                                    </td>
                                    <td><b>Ground Rent (Re/ Rs):
                                        </b>₹
                                        <?php echo e($viewDetails->propertyLeaseDetail->gr_in_re_rs ?? '0'); ?>.<?php echo e($viewDetails->propertyLeaseDetail->gr_in_paisa); ?><?php echo e($viewDetails->propertyLeaseDetail->gr_in_aana); ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Start Date of Ground Rent:
                                    </b><?php echo e(!empty($viewDetails->propertyLeaseDetail->start_date_of_gr) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->start_date_of_gr)->format('d-m-Y') : 'NA'); ?>

                                </td>
                                    <td><b>RGR Duration (Yrs): </b>
                                        <?php echo e($viewDetails->propertyLeaseDetail->rgr_duration ?? 'NA'); ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><b>First Revision of GR due on:
                                    </b><?php echo e(!empty($viewDetails->propertyLeaseDetail->first_rgr_due_on) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->first_rgr_due_on)->format('d-m-Y') : 'NA'); ?>

                                </td>
                                    <td><b>Purpose for which leased/<br> allotted (As per lease):
                                        </b><?php echo e($item->itemNameById($viewDetails->propertyLeaseDetail->property_type_as_per_lease) ?? 'NA'); ?>

                                    </td>
                                </tr>

                                <tr>
                                    <td><b>Sub-Type (Purpose , at present):
                                        </b><?php echo e($item->itemNameById($viewDetails->propertyLeaseDetail->property_sub_type_as_per_lease) ?? 'NA'); ?>

                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan=2><b>Land Use Change:
                                        </b><?php echo e($viewDetails->propertyLeaseDetail->is_land_use_changed ? 'Yes' : 'No'); ?> </td>

                                </tr>
                                <tr>
                                    <td><b>If yes,<br>Purpose for which leased/<br> allotted (As per lease):
                                        </b><?php echo e($item->itemNameById($viewDetails->propertyLeaseDetail->property_type_at_present) ?? 'NA'); ?>

                                    </td>
                                    <td><b>Sub-Type (Purpose , at present):
                                        </b><?php echo e($item->itemNameById($viewDetails->propertyLeaseDetail->property_sub_type_at_present) ?? 'NA'); ?>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="part-title">
                    <h5>LAND TRANSFER DETAILS</h5>
                </div>

                <div class="part-details">
                    <div class="container-fluid">
                        <?php if($separatedData): ?>
                            <?php $__currentLoopData = $separatedData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $dayTransferDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <!-- Added by Nitin to group land transfer by date ---->
                                <?php $__currentLoopData = $dayTransferDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $transferDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <!-- Modified By Nitin--->
                                    <!-- <div class="border border-primary p-3 mt-3"> -->
                                        <!-- <p><b>Process Of Transfer: </b><?php echo e($key); ?></p>
                                        <?php if($key == 'Conversion'): ?>
                                            <p><b>Date: </b><?php echo e($viewDetails->propertyLeaseDetail->date_of_conveyance_deed); ?>

                                            </p>
                                        <?php else: ?>
                                            <p><b>Date: </b><?php echo e($date); ?></p>
                                        <?php endif; ?> -->
                                        <table class="table table-bordered">
                                            <tr>
                                                <td colspan="5" class="address_data"><b>Process Of Transfer: </b><?php echo e($key); ?><!-- </td> -->
                                               <!--  <td colspan="2" class="address_data"> -->
                                                    &nbsp;
                                                    <?php if($key == 'Conversion'): ?>
                                                       <!-- <b>Date: </b> -->(<?php echo e(!empty($viewDetails->propertyLeaseDetail->date_of_conveyance_deed) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->date_of_conveyance_deed)->format('d-m-Y') : ''); ?>)
                                                        
                                                    <?php else: ?>
                                                        <!-- <b>Date: </b> -->(<?php echo e(!empty($date) ? \Carbon\Carbon::parse($date)->format('d-m-Y') : ''); ?>)
                                                    <?php endif; ?>
                                                </td>
                                                
                                            </tr>
                                            <tr>
                                                <th>Lessee Name</th>
                                                <th>Lessee Age (in Years)</th>
                                                <th>Lessee Share</th>
                                                <th>Lessee PAN Number</th>
                                                <th>Lessee Aadhar Number</th>
                                            </tr>
                                            <?php $__currentLoopData = $transferDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($details->lessee_name); ?></td>
                                                    <td><?php echo e($details->lessee_age); ?></td>
                                                    <td><?php echo e($details->property_share); ?></td>
                                                    <td><?php echo e($details->lessee_pan_no); ?></td>
                                                    <td><?php echo e($details->lessee_aadhar_no); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </table>
                                    <!-- </div> -->
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <p class="font-weight-bold">No Records Available</p>
                        <?php endif; ?>
                    </div>
                </div>

               
                  
                                
                            <!-- <h5 class="mb-4 pt-3 text-decoration-underline">PROPERTY STATUS DETAILS</h5> -->
                            <div class="part-title">
                                <h5>PROPERTY STATUS DETAILS</h5>
                            </div>
                            <div class="part-details">
                                <div class="container-fluid">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <?php if($viewDetails->propertyLeaseDetail): ?>
                                                <?php
                                                $namesConversion = [];
                                                foreach ($viewDetails->propertyTransferredLesseeDetails as $transferDetail) {
                                                    $name = $transferDetail->process_of_transfer;
                                                    if ($name == 'Conversion') {
                                                        $namesConversion[] = $transferDetail->lessee_name;
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td><b>Free Hold (F/H): </b><?php echo e($viewDetails->status == 952 ? 'Yes' : 'No'); ?></td>
                                                    <td><b>Date of Conveyance Deed:
                                                        </b>
                                                        <?php echo e(!empty($viewDetails->propertyLeaseDetail->date_of_conveyance_deed) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->date_of_conveyance_deed)->format('d-m-Y') : ''); ?>

                                                       </td>
                                                    <td>
                                                        <b>In Favour of, Name: </b><?php echo e(implode(', ', $namesConversion)); ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Vaccant: </b><?php echo e($viewDetails->status == 1124 ? 'Yes' : 'No'); ?></td>
                                                    <td><b>In Possession Of:
                                                        </b><?php echo e($viewDetails->propertyLeaseDetail->in_possession_of_if_vacant ?? 'NA'); ?>

                                                    </td>
                                                    <td><b>Date Of Transfer:
                                                        </b>
                                                        <?php echo e(!empty($viewDetails->propertyLeaseDetail->date_of_transfer) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->date_of_transfer)->format('d-m-Y') : ''); ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Others: </b><?php echo e($viewDetails->status == 1342 ? 'Yes' : 'No'); ?></td>
                                                    <td><b>Remark: </b><?php echo e($viewDetails->propertyLeaseDetail->remarks ?? 'NA'); ?></td>
                                                </tr>
                                            <?php else: ?>
                                                <p class="font-weight-bold">No Records Available</p>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>   
                            </div>

                            <!-- <h5 class="mb-4 pt-3 text-decoration-underline">INSPECTION & DEMAND DETAILS</h5> -->
                            <div class="part-title">
                                <h5>INSPECTION & DEMAND DETAILS</h5>
                            </div>
                            <div class="part-details">
                                <div class="container-fluid">
                                    <table class="table table-bordered">
                                        <tbody>
                                    <?php if(
                                        $viewDetails->propertyInspectionDemandDetail &&
                                            is_null($viewDetails->propertyInspectionDemandDetail->splited_property_detail_id)): ?>
                                        <tr>
                                            <td colspan=2><b>Date of Last Inspection Report:
                                                </b>
                                                <?php echo e(!empty($viewDetails->propertyInspectionDemandDetail->last_inspection_ir_date) ? \Carbon\Carbon::parse($viewDetails->propertyInspectionDemandDetail->last_inspection_ir_date)->format('d-m-Y') : ''); ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Date of Last Demand Letter:
                                                </b>
                                                <?php echo e(!empty($viewDetails->propertyInspectionDemandDetail->last_demand_letter_date) ? \Carbon\Carbon::parse($viewDetails->propertyInspectionDemandDetail->last_demand_letter_date)->format('d-m-Y') : ''); ?>

                                            </td>
                                            <td><b>Demand ID:
                                                </b><?php echo e($viewDetails->propertyInspectionDemandDetail->last_demand_id ?? 'NA'); ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan=2><b>Amount of Last Demand Letter:
                                                </b>₹
                                                <?php echo e($viewDetails->propertyInspectionDemandDetail->last_demand_amount ?? '0'); ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Last Amount Received:
                                                </b>₹
                                                <?php echo e($viewDetails->propertyInspectionDemandDetail->last_amount_received ?? '0'); ?>

                                            </td>
                                            <td><b>Date of Last Amount Received:
                                                </b><?php echo e($viewDetails->propertyInspectionDemandDetail->last_amount_received_date ?? 'NA'); ?>

                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <p class="font-weight-bold">No Records Available</p>
                                    <?php endif; ?>
                                </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- <h5 class="mb-4 pt-3 text-decoration-underline">MISCELLANEOUS DETAILS</h5> -->
                            <div class="part-title">
                                <h5>MISCELLANEOUS DETAILS</h5>
                            </div>
                            <div class="part-details">
                                <div class="container-fluid">
                            
                                    <table class="table table-bordered">
                                        <tbody>
                                            <?php if($viewDetails->propertyMiscDetail): ?>
                                                <tr>
                                                    <td><b>GR Revised Ever:
                                                        </B><?php echo e($viewDetails->propertyMiscDetail->is_gr_revised_ever ? 'Yes' : 'No'); ?>

                                                    </td>
                                                    <td><b>Date of GR Revised:
                                                        </b>
                                                        <?php echo e(!empty($viewDetails->propertyMiscDetail->gr_revised_date) ? \Carbon\Carbon::parse($viewDetails->propertyMiscDetail->gr_revised_date)->format('d-m-Y') : ''); ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Supplementary Lease Deed Executed:
                                                        </b>
                                                        <?php echo e(!empty($viewDetails->propertyMiscDetail->is_supplimentry_lease_deed_executed) ? \Carbon\Carbon::parse($viewDetails->propertyMiscDetail->is_supplimentry_lease_deed_executed)->format('d-m-Y') : ''); ?>

                                                       
                                                    </td>
                                                    <td><b>Date of Supplementary Lease Deed Executed:
                                                        </b>
                                                        <?php echo e(!empty($viewDetails->propertyMiscDetail->supplimentry_lease_deed_executed_date) ? \Carbon\Carbon::parse($viewDetails->propertyMiscDetail->supplimentry_lease_deed_executed_date)->format('d-m-Y') : ''); ?>

                                                      
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td><b>Supplementary Area: </b>
                                                        <?php echo e($viewDetails->propertyMiscDetail->supplementary_area); ?>

                                                        <?php echo e($item->itemNameById($viewDetails->propertyMiscDetail->supplementary_area_unit)); ?>

                                                        <span
                                                            class="text-secondary">(<?php echo e($viewDetails->propertyMiscDetail->supplementary_area_in_sqm); ?>

                                                            Sq
                                                            Meter)</span>
                                                    </td>
                                                    <td><b>Supplementary Total Premium (in Rs):
                                                        </b>₹ <?php echo e($viewDetails->propertyMiscDetail->supplementary_total_premium ?? '0'); ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Supplementary Total GR (in Rs):
                                                        </b>₹ <?php echo e($viewDetails->propertyMiscDetail->supplementary_total_gr ?? '0'); ?>

                                                    </td>
                                                    <td><b>Supplementary Remark:
                                                        </b><?php echo e($viewDetails->propertyMiscDetail->supplementary_remark ?? 'NA'); ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><b>Re-entered:
                                                        </b><?php echo e($viewDetails->propertyMiscDetail->is_re_rented ? 'Yes' : 'No'); ?>

                                                    </td>
                                                    <td><b>Date of Re-entry:
                                                        </b><?php echo e($viewDetails->propertyMiscDetail->re_rented_date ?? 'NA'); ?>

                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <p class="font-weight-bold">No Records Available</p>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- <h5 class="mb-4 pt-3 text-decoration-underline">Latest Contact Details</h5> -->
                            <div class="part-title">
                                <h5>LATEST CONTACT DETAILS</h5>
                            </div>
                            <div class="part-details">
                                <div class="container-fluid">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <td><b>Address: </b><?php echo e($viewDetails->propertyContactDetail->address ?? 'NA'); ?></td>
                                                <td><b>Phone No.: </b><?php echo e($viewDetails->propertyContactDetail->phone_no ?? 'NA'); ?></td>
                                            </tr>
                                            <tr>
                                                <td><b>Email: </b><?php echo e($viewDetails->propertyContactDetail->email ?? 'NA'); ?></td>
                                                <td><b>As on Date: </b>
                                                    <?php if(isset($viewDetails->propertyContactDetail->as_on_date)): ?>
                                                        <?php echo e(!empty($viewDetails->propertyContactDetail->as_on_date) ? \Carbon\Carbon::parse($viewDetails->propertyContactDetail->as_on_date)->format('d-m-Y') : ''); ?>

                                                    <?php else: ?>
                                                         <?php echo e(!empty($viewDetails->propertyLeaseDetail->date_of_conveyance_deed) ? \Carbon\Carbon::parse($viewDetails->propertyLeaseDetail->date_of_conveyance_deed)->format('d-m-Y') : ''); ?>

                                                    <?php endif; ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                                
                        
                
                
                
                <?php if(($roles === 'section-officer' || $roles === 'assistant-section-officer') && auth()->user()->can('can.approve.application.mis.scanned.document')): ?>            
                    <?php if($isChecked == 1): ?>
                        <div class="d-flex pb-5 gap-1 flex-row justify-content-end align-items-end ">
                            <?php if($disableButtons): ?>
                                
                                <?php if($hideRequestEditButtons): ?>
                                    <div class="btn-group">
                                        <button type="button" id="requestMisEdit" class="btn btn-primary ml-2">Request
                                            Edit</button>
                                    </div>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <?php if($disableApproveButtons): ?>
                                    <div class="btn-group">
                                        <button type="button" id="MISChecked" class="btn btn-primary ml-2">Approve</button>
                                    </div>
                                <?php endif; ?>

                                <?php
                                    $serviceType = !empty($additionalData[0]) ? $additionalData[0] : '';
                                    $modalId = !empty($additionalData[1]) ? $additionalData[1] : '';
                                    $applicantNo = !empty($additionalData[2]) ? $additionalData[2] : '';
                                    $masterId = !empty($additionalData[3]) ? $additionalData[3] : '';
                                    $uniquePropertyId = !empty($additionalData[4]) ? $additionalData[4] : '';
                                    $oldPropertyId = !empty($additionalData[5]) ? $additionalData[5] : '';
                                    $sectionCode = !empty($additionalData[6]) ? $additionalData[6] : '';
                                    //Flat Id added by Lalit on 06/Nov/2024
                                    $flatId = !empty($flatData['flatDetails']->flat_id) ? $flatData['flatDetails']->flat_id : '';
                                    $additionalData = [
                                        $serviceType,
                                        $modalId,
                                        $applicantNo,
                                        $masterId,
                                        $uniquePropertyId,
                                        $oldPropertyId,
                                        $sectionCode,
                                        $flatId,
                                    ]; //service type,modalId, applicant no
                                    $additionalDataJson = json_encode($additionalData);
                                ?>
                                <?php if(isset($flatData['flatDetails']->id)): ?>
                                    <div class="btn-group">
                                        <a
                                            href="<?php echo e(route('editFlatDetails', ['id' => $flatData['flatDetails']->id])); ?>?params=<?php echo e(urlencode($additionalDataJson)); ?>">
                                            <button type="button" class="btn btn-secondary ml-2">Edit</button>
                                        </a>
                                    </div>
                                <?php else: ?>
                                        <?php if(empty($flatData['flatDetails']['is_property_flat'])): ?>
                                            <div class="btn-group">
                                                <a
                                                    href="<?php echo e(route('editDetails', ['property' => $viewDetails->id])); ?>?params=<?php echo e(urlencode($additionalDataJson)); ?>">
                                                    <button type="button" id="PropertyIDSearchBtn"
                                                        class="btn btn-secondary ml-2">Edit</button>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- </div> -->
                <?php if($roles === 'deputy-lndo' && $isApproved): ?>
                    <div class="d-flex pb-5 gap-1 flex-row justify-content-end align-items-end ">
                        <div class="btn-group">
                            <button type="button" id="MISChecked" class="btn btn-primary ml-2">Approve</button>
                        </div>
                    </div>
                <?php endif; ?>
        </div>
    </div>
    <?php echo $__env->make('include.loader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('include.alerts.ajax-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('include.alerts.section.mis-checked', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('include.alerts.section.request-mis-edit-model-popup', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php $__env->stopSection(); ?>


<?php $__env->startSection('footerScript'); ?>
    <script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('#MISChecked').on('click', function() {
                $('#MISChecked').prop('disabled', true).html('Approving...');
                $('#MISCheckedModal').modal('show');
            });

            $('#confirmApproveMisCheckedCloseBtn').on('click', function(){
                $('#MISChecked').prop('disabled', false).html('Approve');
            })
            
            $('.btn-close').on('click', function(){
                $('#MISChecked').prop('disabled', false).html('Approve');
                $('#requestMisEdit').prop('disabled', false).html('Request Edit');
            })



            // Optionally, you can handle the closing of the modal
            $('#MISCheckedModal').on('hidden.bs.modal', function() {
                // Do something after the modal is hidden, like unchecking the checkbox if needed
                $('#MISChecked').prop('checked', false);
            });


            // confirm and aproove MIS is checked by section - Sourav Chauhan (06/sep/2024)
            $('#confirmApproveMisChecked').on('click', function(e) {
                e.preventDefault();
                $('#confirmApproveMisChecked').prop('disabled', true).html('Submitting...');
                // Serialize form data
                let formData = $('#misCheckedForm').serialize();
                // $('#misCheckedForm').submit();
                // Send AJAX request
                $.ajax({
                    url: "<?php echo e(route('approveMis')); ?>", // Your form action URL
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.status == 'success'){
                            // Handle success response
                            $('#MISCheckedModal').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showSuccess(response.message);
                            window.location.href = "<?php echo e(url()->previous()); ?>";
                            // Ensure checkbox is checked and disabled after success
                            // Slight delay to ensure modal is fully hidden
                        } else {
                            // Handle success response
                            $('#MISCheckedModal').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showError(response.message);
                            window.location.href = "<?php echo e(url()->previous()); ?>";
                            // Ensure checkbox is checked and disabled after success
                           // Slight delay to ensure modal is fully hidden
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        $('.loader_container').addClass('d-none');
                        if ($('.results').hasClass('d-none'))
                            $('.results').removeClass('d-none');
                        if (response.responseJSON && response.responseJSON.message) {
                            showError(response.responseJSON.message)
                        }
                    }
                });
            });

            $('#requestMisEdit').on('click', function() {
                $('#requestMisEdit').prop('disabled', true).html('Requesting...');
                $('#requestMisEditModal').modal('show');
            });

            $('#confirmrequestEditMisCheckedCloseBtn').on('click', function(){
                $('#requestMisEdit').prop('disabled', false).html('Request Edit');
            })

            // confirm and request edit mis is checked by section officer - Lalit Tiwari (09/sep/2024)
            /*$('#confirmrequestEditMisChecked').on('click', function(e) {
                e.preventDefault();
                let remarks = $('#remarks').val();
                if (remarks == '') {
                    $('#remarksError').show();
                    return false;
                }
                $('#confirmrequestEditMisChecked').prop('disabled', true);
                $('#confirmrequestEditMisChecked').html('Submitting...');
                $('#requestEditMisForm').submit();
            });*/

            $('#confirmrequestEditMisChecked').on('click', function(e) {
                e.preventDefault();
                $('#confirmrequestEditMisChecked').prop('disabled', true).html('Submitting...');
                // Serialize form data
                let formData = $('#requestEditMisForm').serialize();
                // Send AJAX request
                $.ajax({
                    url: "<?php echo e(route('requestEditMis')); ?>", // Your form action URL
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if(response.status == 'success'){
                            // Handle success response
                            $('#requestMisEditModal').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showSuccess(response.message);
                            // Ensure checkbox is checked and disabled after success
                            setTimeout(function() {
                                location.reload();
                            }, 3000); // Slight delay to ensure modal is fully hidden
                        } else {
                            // Handle success response
                            $('#requestMisEditModal').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showError(response.message);
                            // Ensure checkbox is checked and disabled after success
                            setTimeout(function() {
                                location.reload();
                            }, 100); // Slight delay to ensure modal is fully hidden
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        $('.loader_container').addClass('d-none');
                        if ($('.results').hasClass('d-none'))
                            $('.results').removeClass('d-none');
                        if (response.responseJSON && response.responseJSON.message) {
                            showError(response.responseJSON.message)
                        }
                    }
                });
            });


        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/mis/preview.blade.php ENDPATH**/ ?>