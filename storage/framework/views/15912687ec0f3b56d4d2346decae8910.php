
<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<style>
    .subtypes {
        display: flex;
        flex-direction: row;
        justify-content: space-around;
    }

    .typeName {
        text-align: center;
    }

    .custom-col {
        flex: 1;
        margin: 0 5px;
    }

    .custom-col:first-child {
        margin-left: 0;
    }

    .custom-col:last-child {
        margin-right: 0;
    }

    h6 {
        font-size: 11px !important;
    }

    .status_name {
        color: #101010;
        font-size: 16px;
        font-weight: 500;
    }

    .status_name:after {
        content: ':';
        display: inline
    }

    .status_value {
        color: #101010;
        font-size: 16px;
        font-weight: 500;
    }
    /* added by swati on 16-07-2025 for status of application  */
    .badge {
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

</style>
<div class="container-fluid">
    <div class="row justify-content-between mb-3">
        <div class="col-lg-6">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Dashboard</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">Dashboard</li>
                            <!-- <li class="breadcrumb-item active" aria-current="page">My Dashboard</li> -->
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid dashboardcards">
        <div class="row row-flex mb-3">
            
            <div class="col-md-4 mb-3">
                <a href="<?php echo e(route('applicant.properties')); ?>" style="color: inherit; text-decoration: none;">
                    <div class="card bg-light-green dash-cards">
                        <div class="card-body">
                            <!-- <h4>My Propert<?php echo e($userProperties->count() == 1 ? 'y':'ies'); ?></h4> -->
                             <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0" style="font-size:22px;">My Propert<?php echo e($userProperties->count() == 1 ? 'y' : 'ies'); ?></h4>
                                <!-- <span class="badge text-light"><?php echo e($userProperties->count()); ?></span> -->
                            </div>
                            <div class="dash-widgets">                            
                                <div class="widget-media-body">
                                    <div class="widget-count"><?php echo e($userProperties->count()); ?></div>
                                    <!-- <div class="property-list">
                                        <div class="row">
                                            <?php $__currentLoopData = $userProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $up): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-sm-12 col-xxl-6">
                                                <p style="font-size:16px;"><?php echo e($up->known_as); ?></p>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>                                                                 -->
                                </div>
                                <div class="dash-icons">
                                    <i class="fa-solid fa-building-user"></i>
                                </div>
                            </div>
                        </div>    
                    </div>
                </a>
                
            </div>
            
            <div class="col-md-4 mb-3">
                <a href="<?php echo e(route('applications.all.details')); ?>" style="color: inherit; text-decoration: none;">
                    <div class="card bg-primary dash-cards">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0"style="font-size:22px;">Application<?php echo e($userApplications->count() === 1 ? '' : 's'); ?></h4>
                               
                            </div>

                            <div class="dash-widgets">                            
                                <div class="widget-media-body">
                                    <div class="widget-count"><?php echo e($userApplications->count()); ?></div>                                
                                    
                                </div>
                                <div class="dash-icons">
                                    <i class="fa-solid fa-house-user"></i>
                                </div>
                            </div>
                        </div>   
                        
                    </div>
                </a>
            </div>
        <!-- Demands -->
            <div class="col-md-4 mb-3">
                <!-- by Swati on 16-07-2025 for adding hyperlink to redirect to demand page -->
                <a href="<?php echo e(route('applicant.pendingDemands')); ?>" style="color: inherit; text-decoration: none;">
                    <div class="card bg-dark-orange dash-cards">
                        <div class="card-body">
                            <!-- <h4>Pending Demands (<?php echo e($demandCount); ?>)</h4> -->
                             <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0"style="font-size:22px;">Pending Demands</h4>
                                <!-- <span class="badge badge text-light"><?php echo e($demandCount); ?></span> -->
                            </div>

                            <div class="dash-widgets">                            
                                <div class="widget-media-body">
                                    <div class="widget-count"><?php echo e($demandCount); ?></div>                                                   
                                    <!-- <div class="property-list">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-xl-6">
                                                    <p><?php echo e($demandCount); ?></p>
                                                     <p style="font-size:16px;">Total: ₹<?php echo e(number_format($demandTotal)); ?></p>

                                            </div>
                                        </div>
                                    </div>                                                                 -->
                                </div>
                                <div class="dash-icons">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                            </div>
                        </div> 
                        <!-- <div class="card-header">
                            <h4>Pending Demands</h4>
                        </div>
                        <div class="card-body">
                            <div class="dashboard-card-view">
                                <h4>
                                    <a href="<?php echo e(route('applicant.pendingDemands')); ?>" style="color: inherit">
                                        <span id="totalAppCount"><?php echo e($demandCount); ?></span>
                                    </a>
                                </h4>
                            </div>
                        </div> -->
                    </div>
                </a>    
            </div>
        

        
        
        </div>









        





        
<?php $__empty_1 = true; $__currentLoopData = $userProperties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <!-- Property 1 -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">

            <!-- Property Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="fw-bold">
                        <i class="bi bi-house-door text-primary me-1"></i>
                        Property ID: <?php echo e($property->old_property_id); ?> 
                    </span>
                </div>

                <span class="badge bg-primary-subtle text-primary">
                    Ground Rent Details
                </span>
            </div>

            <!-- Details -->
            <div class="row g-2 align-items-stretch">

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="bg-light rounded p-2 h-100">
                        <small class="text-muted d-block">Ground Rent</small>
                       
                        <span class="fw-bold text-primary">
                            <?php if($property->leaseDetails?->gr_in_re_rs !== null): ?>
                                ₹<?php echo e($property->leaseDetails->gr_in_re_rs); ?>.<?php echo e(str_pad($property->leaseDetails->gr_in_re_paise ?? 0, 2, '0', STR_PAD_LEFT)); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="bg-light rounded p-2 h-100">
                        <small class="text-muted d-block">Start Date</small>
                        <span class="fw-semibold"><?php echo e($property->leaseDetails?->start_date_of_gr
    ? \Carbon\Carbon::parse($property->leaseDetails->start_date_of_gr)->format('d-m-Y')
    : '-'); ?></span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="bg-light rounded p-2 h-100">
                        <small class="text-muted d-block">First RGR Due On</small>
                        <span class="fw-semibold"><?php echo e($property->leaseDetails?->first_rgr_due_on
    ? \Carbon\Carbon::parse($property->leaseDetails->first_rgr_due_on)->format('d-m-Y')
    : '-'); ?></span>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-6">
                    <div class="bg-light rounded p-2 h-100">
                        <small class="text-muted d-block">RGR Duration</small>
                        <span class="fw-semibold"><?php echo e($property->leaseDetails?->rgr_duration ?? '-'); ?> Years</span>
                    </div>
                </div>

                <div class="col-lg-4 col-md-8 col-12">
                    <div class="bg-light rounded p-2 h-100">
                        <small class="text-muted d-block">
                            <i class="bi bi-calculator me-1"></i>
                            Formula
                        </small>
                        <span class="fw-semibold">
                            Area × Rate × Period
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">

            <i class="bi bi-house-x fs-1 text-muted"></i>

            <h6 class="mt-3 mb-1">
                No Property Found
            </h6>

            <p class="text-muted mb-0">
                No property is currently associated with your account.
            </p>

        </div>
    </div>

<?php endif; ?>











        <?php if($userAppointments->count() > 0): ?>
        <div class="row justify-content-between mb-3">
            <div class="col">
                <div class="card darkbluecard">
                    <div class="card-body">
                        <div class="dashboard-card-view">
                            <h4>Appointment<?php echo e($userAppointments->count() == 1 ? '':'s'); ?></h4>
                            <div class="row p-2">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Application</th>
                                            <th>Valid till</th>
                                            <th>Appointment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $userAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uapt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td> <h5><?php echo e($uapt->application_no.'('.getApplicationTypeByApplicationNo($uapt->application_no).')'); ?></h5></td>
                                            <td><?php echo e(date('d-m-Y',strtotime($uapt->valid_till))); ?></td>
                                            <td><?php echo !is_null($uapt->schedule_date) ? date('d-m-Y',strtotime($uapt->schedule_date))  : '<a href="'.$uapt->link.'" target="_blank">Click to schedule</a>'; ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center"> No appointment</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                          
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php echo $__env->make('include.alerts.ajax-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('footerScript'); ?>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/dashboard/applicant.blade.php ENDPATH**/ ?>