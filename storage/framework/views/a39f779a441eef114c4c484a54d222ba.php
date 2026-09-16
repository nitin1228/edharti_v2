<?php
$currentUrl = url()->current();
$isPublic = strpos($currentUrl, 'public-dashboard') !== false;
$layout = $isPublic ? 'layouts.public.app' : 'layouts.app';
?>




<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<?php if($isPublic ||( Auth::check() && Auth::user()->hasPermissionTo('view dashboard'))): ?>

<?php if($isPublic): ?>
<link href="<?php echo e(asset('assets/css/app.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('assets/css/bootstrap-extended.css')); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap-select.min.css')); ?>">
<?php endif; ?>

<?php
$labels = [];
$barData = [];
$seriesLabels = [];
foreach ($barChartData as $key => $row) {
    $labels[] = $row->land;
    $data = [];
    $series = [];
    $counter = 0;
    foreach ($row as $index => $val) {
        if ($counter > 0) {
            $data[] = $val;
            if (count($seriesLabels) == 0) {
                $series[] = "'" . $index . "'"; // must wrap in quotes as javascript throws error on < and >
            }
        }
        $counter++;
    }
    if (count($seriesLabels) == 0 && count($series) > 0) {
        $seriesLabels = $series;
    }

    $barData[] = $data;
}

//Get the number of colonies --Amita [15-01-2025]
$number_of_colonies = getMisDoneColoniesCount();
?>
<style>
    .colony-dropdown {
        width: 48.5% !important;
    }

    .dropdown.bootstrap-select {
        min-width: 100% !important;
    }

    .btn.dropdown-toggle {
        min-width: 100% !important;
    }

    a.active .text {
        color: #fff !important;
    }

    .view-list {
        cursor: pointer;
    }
</style>
<!--breadcrumb-->
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
                            <li class="breadcrumb-item">Dashboards</li>
                           <?php if(Auth::user()->hasRole('super-admin')): ?>
                                        <li class="breadcrumb-item active" aria-current="page">My Dashboard</li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                    <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="colony-dropdown ms-auto">
                <select id="colony-filter" class="selectpicker" data-live-search="true">
                    <option value="">Filter by Colony (<?php echo e($number_of_colonies); ?>)</option>
                    <?php $__currentLoopData = $colonies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colony): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($colony->id); ?>"><?php echo e($colony->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>


    </div>
   
</div>




<div class="container-fluid">
    <div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 custom_card_container">
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                src="<?php echo e(asset('assets/images/properties-icon-hand-Total.svg')); ?>" alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark view-list" id="tile_total_count"
                                data-original="<?php echo e(customNumFormat($totalCount)); ?>"><?php echo e(customNumFormat($totalCount)); ?></h4>
                            <p class="mb-0 text-secondary">Total No. of Properties in Delhi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #FFF3E0;"><img src="<?php echo e(asset('assets/images/pageless-Total.svg')); ?>"
                                alt="Pageless">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_total_area_formatted"
                                data-original="<?php echo e(customNumFormat(round($totalArea))); ?>">
                                <?php echo e(customNumFormat(round($totalArea))); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total Area (Sqm)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($totalLdoValue)); ?>" id="tile_total_land_value_ldo"
            data-original="₹<?php echo e(customNumFormat($totalLdoValue)); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #F3E5F5;"><img src="<?php echo e(asset('assets/images/sell-Total.svg')); ?>"
                                alt="Land Value">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_total_land_value_ldo_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($totalLdoValue/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($totalLdoValue/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total Land Value (L&DO land rates)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($totalCircleValue)); ?>" id="tile_total_land_value_circle"
            data-original="₹<?php echo e(customNumFormat($totalCircleValue)); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #E0F2F1;"><img src="<?php echo e(asset('assets/images/payments-Total.svg')); ?>"
                                alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 text-dark grid-icons-size" id="tile_total_land_value_circle_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($totalCircleValue/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($totalCircleValue/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total Land Value (Circle rates)

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 custom_card_container">
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                src="<?php echo e(asset('assets/images/properties-icon-hand-LH.svg')); ?>" alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark view-list" id="tile_lease_hold_count"
                                data-original="<?php echo e(customNumFormat($statusCount['lease_hold'])); ?>"
                                data-filter-name="property_status" data-filter-value="951">
                                <?php echo e(customNumFormat($statusCount['lease_hold'])); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total No. of LH Properties in Delhi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #FFF3E0;"><img src="<?php echo e(asset('assets/images/pageless-LH.svg')); ?>"
                                alt="Pageless">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_lease_hold_area"
                                data-original="<?php echo e(customNumFormat(round($statusArea['lease_hold']))); ?>">
                                <?php echo e(customNumFormat(round($statusArea['lease_hold']))); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total Area of LH Properties (Sqm)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($statusLdoValue['lease_hold'])); ?>"
            id="tile_lease_hold_land_value_ldo" data-original="₹<?php echo e(customNumFormat($statusLdoValue['lease_hold'])); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #F3E5F5;"><img src="<?php echo e(asset('assets/images/sell-LH.svg')); ?>"
                                alt="Land Value">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_lease_hold_land_value_ldo_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($statusLdoValue['lease_hold']/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($statusLdoValue['lease_hold']/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total LH Land Value (L&DO land rates)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($statusCircleValue['lease_hold'])); ?>"
            id="tile_lease_hold_land_value_circle"
            data-original="₹<?php echo e(customNumFormat($statusCircleValue['lease_hold'])); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #E0F2F1;"><img src="<?php echo e(asset('assets/images/payments-LH.svg')); ?>"
                                alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 text-dark grid-icons-size" id="tile_lease_hold_land_value_circle_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($statusCircleValue['lease_hold']/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($statusCircleValue['lease_hold']/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total LH Land Value (Circle rates)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 custom_card_container">
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                src="<?php echo e(asset('assets/images/properties-icon-hand-FH.svg')); ?>" alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark view-list" id="tile_free_hold_count"
                                data-original="<?php echo e(customNumFormat($statusCount['free_hold'])); ?>"
                                data-filter-name="property_status" data-filter-value="952">
                                <?php echo e(customNumFormat($statusCount['free_hold'])); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total No. of FH Properties in Delhi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #FFF3E0;"><img src="<?php echo e(asset('assets/images/pageless-FH.svg')); ?>"
                                alt="Pageless">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_free_hold_area"
                                data-original="<?php echo e(customNumFormat(round($statusArea['free_hold']))); ?>">
                                <?php echo e(customNumFormat(round($statusArea['free_hold']))); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total Area of FH Properties (Sqm)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($statusLdoValue['free_hold'])); ?>"
            id="tile_free_hold_land_value_ldo" data-original="₹<?php echo e(customNumFormat($statusLdoValue['free_hold'])); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #F3E5F5;"><img src="<?php echo e(asset('assets/images/sell-FH.svg')); ?>"
                                alt="Land Value">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_free_hold_land_value_ldo_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($statusLdoValue['free_hold']/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($statusLdoValue['free_hold']/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total FH Land Value (L&DO land rates)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($statusCircleValue['free_hold'])); ?>"
            id="tile_free_hold_land_value_circle" data-original="₹<?php echo e(customNumFormat($statusCircleValue['free_hold'])); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"
                            style="background-color: #E0F2F1;"><img src="<?php echo e(asset('assets/images/payments-FH.svg')); ?>"
                                alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 text-dark grid-icons-size" id="tile_free_hold_land_value_circle_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($statusCircleValue['free_hold']/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($statusCircleValue['free_hold']/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total FH Land Value (Circle rates)

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- unalloted properties -->

    <div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 custom_card_container">
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                src="<?php echo e(asset('assets/images/properties-icon-hand.svg')); ?>" alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark view-list" id="tile_unallotted_count"
                                data-original="<?php echo e(customNumFormat($statusCount['unallotted'])); ?>" data-filter-name="property_status"
                                data-filter-value="unalloted"><?php echo e(customNumFormat($statusCount['unallotted'])); ?></h4>
                            <p class="mb-0 text-secondary">Total No. of Unallotted Properties in Delhi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="<?php echo e(asset('assets/images/pageless.svg')); ?>"
                                alt="Pageless">
                        </div>

                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_unallotted_area"
                                data-original=" <?php echo e(customNumFormat(round($statusArea['unallotted']))); ?>">
                                <?php echo e(customNumFormat(round($statusArea['unallotted']))); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total Area of Unallotted Properties (Sqm)</p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($statusLdoValue['unallotted'])); ?>" id="tile_unallotted_land_value_ldo"
            data-original="₹<?php echo e(customNumFormat($statusLdoValue['unallotted'])); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="<?php echo e(asset('assets/images/sell.svg')); ?>"
                                alt="Land Value">
                        </div>
                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="tile_unallotted_land_value_ldo_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($statusLdoValue['unallotted']/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($statusLdoValue['unallotted']/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total Unallotted Land Value (L&DO land rates)</p>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col hoverable" title="₹<?php echo e(customNumFormat($statusCircleValue['unallotted'])); ?>" id="tile_unallotted_land_value_circle"
            data-original="₹<?php echo e(customNumFormat($statusCircleValue['unallotted'])); ?>">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="<?php echo e(asset('assets/images/payments.svg')); ?>"
                                alt="properties">
                        </div>
                        <div>
                            <h4 class="my-1 text-dark grid-icons-size" id="tile_unallotted_land_value_circle_formatted"
                                data-original="₹<?php echo e(customNumFormat(round($statusCircleValue['unallotted']/10000000))); ?> Cr.">
                                ₹<?php echo e(customNumFormat(round($statusCircleValue['unallotted']/10000000))); ?> Cr.</h4>
                            <p class="mb-0 text-secondary">Total Unallotted Land Value (Circle rates)

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 custom_card_container  outside-tiles">
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                src="<?php echo e(asset('assets/images/properties-icon-hand-OD.svg')); ?>" alt="properties">
                        </div>
                        <div>
                          
                           <h4 class="my-1 grid-icons-size text-dark view-list" id="outside_count"
                                data-filter-value="vaccant"><?php echo e(customNumFormat($outsideProperty['count'])); ?></h4>
                            <p class="mb-0 text-secondary">Total No. of Properties Outside Delhi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="<?php echo e(asset('assets/images/pageless-OD.svg')); ?>"
                                alt="Pageless">
                        </div>

                        <div>
                            <h4 class="my-1 grid-icons-size text-dark" id="outside_area">
                                <?php echo e(customNumFormat(round($outsideProperty['total_area']))); ?>

                            </h4>
                            <p class="mb-0 text-secondary">Total Area of Properties Outside Delhi (Sqm)</p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <?php if(Auth::user()->hasRole('lndo') && Auth::user()->can('thirty.days.old.pending.application.report')): ?>
        <div class="row row-cols-2 row-cols-md-2 row-cols-xl-4 custom_card_container days-30">
            <div class="col">
                <div class="card radius-10 border-start border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center dashboard-cards">
                            <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                    src="<?php echo e(asset('assets/images/sell-30.svg')); ?>" alt="properties">
                            </div>
                            <div>

                                <h4 class="my-1 grid-icons-size text-dark view-list" id="outside_count"
                                    data-filter-value="pending_request"><?php echo e($totalOldRecordsCount ?? 0); ?></h4>
                                <p class="mb-0 text-secondary">Total No. of 30+ Days Old Pending Requests</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center dashboard-cards">
                            <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                    src="<?php echo e(asset('assets/images/properties-icon-hand-30.svg')); ?>"
                                    alt="properties">
                            </div>
                            <div>

                                <h4 class="my-1 grid-icons-size text-dark view-list" id="outside_count"
                                    data-filter-value="pending_request"><?php echo e($totalOldRegistrationsCount ?? 0); ?>

                                </h4>
                                <p class="mb-0 text-secondary">Total No. of 30+ Days Old Registrations</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center dashboard-cards">
                            <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img
                                    src="<?php echo e(asset('assets/images/pageless-30.svg')); ?>" alt="properties">
                            </div>
                            <div>

                                <h4 class="my-1 grid-icons-size text-dark view-list" id="outside_count"
                                    data-filter-value="pending_request"><?php echo e($totalOldApplicationsCount ?? 0); ?></h4>
                                <p class="mb-0 text-secondary">Total No. of 30+ Days Old Applications</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>        


    <!-- ---------------------------------------------------------------------------- -->
    
<div class="row row-cols-2 row-cols-md-2 row-cols-xl-6 custom_card_container">

    <div class="col">
        <div class="card radius-10 border-start border-0">
            <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-2 rounded-circle text-white m-auto"
                        style="background-color: #E0F2F1;"><img src="<?php echo e(asset('assets/images/residential.svg')); ?>"
                            alt="properties">
                    </div>
                    <div>
                        <h4 class="my-1 text-dark view-list" id="residential_count"
                            data-original="<?php echo e(customNumFormat($propertyTypeCount['Residential'])); ?>"
                            data-filter-name="property_type" data-filter-value="47">
                            <?php echo e(customNumFormat($propertyTypeCount['Residential'])); ?>

                        </h4>
                        <p class="mb-0 text-secondary">Residential</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0">
            <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-2 rounded-circle text-white m-auto"
                        style="background-color: #FFF3E0;"><img src="<?php echo e(asset('assets/images/commercial.svg')); ?>"
                            alt="properties">
                    </div>
                    <div>
                        <h4 class="my-1 text-dark view-list" id="commercial_count"
                            data-original="<?php echo e(customNumFormat($propertyTypeCount['Commercial'])); ?>"
                            data-filter-name="property_type" data-filter-value="48">
                            <?php echo e(customNumFormat($propertyTypeCount['Commercial'])); ?>

                        </h4>
                        <p class="mb-0 text-secondary">Commercial</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0">
            <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-2 rounded-circle text-white m-auto" style="background-color: #fde7e6;"><img src="<?php echo e(asset('assets/images/factory.svg')); ?>"
                            alt="Industrial">
                    </div>
                    <div>
                        <h4 class="my-1 text-dark view-list" id="industrial_count"
                            data-original="<?php echo e(customNumFormat($propertyTypeCount['Industrial'])); ?>"
                            data-filter-name="property_type" data-filter-value="469">
                            <?php echo e(customNumFormat($propertyTypeCount['Industrial'])); ?>

                        </h4>
                        <p class="mb-0 text-secondary">Industrial</p>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0">
            <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-2 rounded-circle text-white m-auto" style="background-color: #e2eff5;"><img
                            src="<?php echo e(asset('assets/images/institute-Inst.svg')); ?>" alt="properties">
                    </div>
                    <div>
                        <h4 class="my-1 text-dark view-list" id="institutional_count"
                            data-original="<?php echo e(customNumFormat($propertyTypeCount['Institutional'])); ?>"
                            data-filter-name="property_type" data-filter-value="49">
                            <?php echo e(customNumFormat($propertyTypeCount['Institutional'])); ?>

                        </h4>
                        <p class="mb-0 text-secondary">Institutional</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0">
            <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-2 rounded-circle text-white m-auto" style="background-color: #d6eff1;"><img
                            src="<?php echo e(asset('assets/images/institute-Mix.svg')); ?>" alt="properties">
                    </div>
                    <div>
                        <h4 class="my-1 text-dark view-list" id="mixed_count"
                            data-original="<?php echo e(customNumFormat($propertyTypeCount['Mixed'])); ?>"
                            data-filter-name="property_type" data-filter-value="72">
                            <?php echo e(customNumFormat($propertyTypeCount['Mixed'])); ?>

                        </h4>
                        <p class="mb-0 text-secondary">Mixed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card radius-10 border-start border-0">
            <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-2 rounded-circle text-white m-auto" style="background-color: #f5e2d5;"><img
                            src="<?php echo e(asset('assets/images/institute-Other.svg')); ?>" alt="properties">
                    </div>
                    <div>
                        <h4 class="my-1 text-dark view-list" id="others_count"
                            data-original="<?php echo e(customNumFormat($propertyTypeCount['Others'])); ?>"
                            data-filter-name="property_type" data-filter-value="1353">
                            <?php echo e(customNumFormat($propertyTypeCount['Others'])); ?>

                        </h4>
                        <p class="mb-0 text-secondary">Others</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!--end row-->
<div id="no-filter-applied">
    <div class="row">
        <div class="col-12 col-lg-5">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">No. of Properties Land Type Wise</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i
                                    class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Export PDF</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Export CSV</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="chart-container-1" style="height: 390px;">
                        <canvas id="chartProperties1"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="card radius-10">
                <div class="card-body">
                    <!-- added responsive div for table responsiveness by anil on 30-05-2025 -->
                    <div class="table-responsive">
                        <table class="table table-striped text-center">
                            <thead>
                                <tr>
                                    <th>Area (Sqm.)</th>
                                    <th>No. of Properties</th>
                                    <th>No. of Lease Hold Properties</th>
                                    <th>No. of Free Hold Properties</th>
                                    <th>Area of Properties (Sqm.)</th>
                                </tr>
                            </thead>
                            <?php
                            /*$labels = $propertyAreaDetails['labels'];*/
                            $totalCount = array_sum(array_column($propertyAreaDetails, 'count'));
                            $totalAreaInSqm = array_sum(array_column($propertyAreaDetails, 'area'));
                            $totalAreaInAcre = $totalAreaInSqm * 0.0002471053815;
                            $leaseHoldCount = 0;
                            $freeHoldCount = 0;
                            ?>
                            <tbody>
                                <?php $__currentLoopData = $propertyAreaDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $leaseHoldCount += $row['leaseHold'];
                                    $freeHoldCount += $row['freeHold'];
                                ?>
                                <tr>
                                    <td><?php echo e($key); ?></td>
                                    <td><?php echo e($row['count'].' ('. round(($row['count']/$totalCount)*100, 2).'%)'); ?></td>
                                    <td><?php echo e($row['leaseHold'].' ('. round(($row['leaseHold']/$totalCount)*100, 2).'%)'); ?></td>
                                    <td><?php echo e($row['freeHold'].' ('. round(($row['freeHold']/$totalCount)*100, 2).'%)'); ?></td>
                                    <td><?php echo e($row['area'].' ('. round(($row['area']/$totalAreaInSqm)*100, 2). '%)'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th>Grand Total</th>
                                    <th colspan="1"><?php echo e(customNumformat($totalCount)); ?></th>
                                    <th colspan="1"><?php echo e(customNumformat($leaseHoldCount)); ?></th>
                                    <th colspan="1"><?php echo e(customNumformat($freeHoldCount)); ?></th>
                                    <th colspan="1"><?php echo e(customNumFormat(round($totalAreaInSqm, 3))); ?>

                                        (<?php echo e(customNumFormat(round($totalAreaInAcre))); ?> Acres)</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    
    <!-- end row -->

    <div class="row">
        <div class="col-12 col-lg-7">
            

            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">Revenue (INR)</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i
                                    class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Export PDF</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Export CSV</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="chart-container-2" style="height: 564px;">
                        <canvas id="revenueINR"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-5">
            <div class="card radius-10">
                <div class="card-body">
                <h6 class="">Outstanding Dues</h6>
                    <div class="table-demand">
                        <div class="table-responsive">
                            <table class="table align-middle m-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color:#116d6e17;">
                                            <h6 class="pt-2">Total Outstanding Dues </h6>
                                            <h5 class="pb-1">&#8377; <?php echo e(customNumFormat(round(($oldDemandData['total']->outstanding_amount)/10000000,2))); ?> Cr. (<?php echo e(customNumFormat($oldDemandData['total']->property_count)); ?> Properties)</h5>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                        <table class="table align-middle table-striped m-0">
                                            <thead>
                                                <tr>
                                                    <th>Property Status</th>
                                                    <th>Outstanding Dues</th>
                                                    <th>No. of Properties</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>Lease Hold</td>
                                                <td>&#8377; <?php echo e(customNumFormat(round(($oldDemandData['leaseHold']->outstanding_amount)/10000000,2))); ?> Cr. </td>
                                                <td><?php echo e(customNumFormat($oldDemandData['leaseHold']->property_count)); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Free Hold</td>
                                                <td>&#8377; <?php echo e(customNumFormat(round(($oldDemandData['freeHold']->outstanding_amount)/10000000,2))); ?> Cr. </td>
                                                <td><?php echo e(customNumFormat($oldDemandData['freeHold']->property_count)); ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        </td>                                        
                                    </tr>
                                </tbody>
                            </table>
                       
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>




            <?php if($applicationCountData && Auth::user()->can('view.application.reports')): ?>
                
            <?php endif; ?>

    <!-- end row -->
    
</div>
</div>
<div id="filter-applied" class="d-none">
    <?php echo $__env->make('include.parts.dashboard-filtered', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
</div>

<?php endif; ?>
<?php $__env->stopSection(); ?>
<!-- end row -->

<?php $__env->startSection('footerScript'); ?>

<?php if($isPublic ||( Auth::check() && Auth::user()->hasPermissionTo('view dashboard'))): ?>
<script src="<?php echo e(asset('assets/js/Chart.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/bootstrap-select.min.js')); ?>"></script> <!-- added By Nitin to for colony filter -->
<script src="<?php echo e(asset('assets/js/chartjs-plugin-datalabels.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/index.js')); ?>"></script>
<script>
    /** variabe added to imlemend colony filter */
    let colony_id = null;
    /**---------------------------------- */
    const datalabelOptions = {
                color: '#eee',
                anchor: 'center',
                align: 'center',
                font: {
                    size: 14,
                    weight: 600
                }
            }
    $(document).ready(() => {

        //chart no. (nazul rehabilitation)
        var ctx2 = document.getElementById("chartProperties1").getContext('2d');

        var gradientStroke1 = ctx2.createLinearGradient(0, 0, 0, 300);
        gradientStroke1.addColorStop(0, '#64B5F6');
        // gradientStroke1.addColorStop(1, '#17c5ea');

        var gradientStroke2 = ctx2.createLinearGradient(0, 0, 0, 300);
        gradientStroke2.addColorStop(0, '#81C784');
        // gradientStroke2.addColorStop(1, '#ffdf40');

        var myChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['No. of Properties'],
                datasets: [{
                        label: 'Nazul',
                        data: ["<?php echo e($landTypeCount['Nazul']); ?>"],
                        borderColor: gradientStroke1,
                        backgroundColor: gradientStroke1,
                        hoverBackgroundColor: gradientStroke1,
                        pointRadius: 0,
                        fill: false,
                        borderWidth: 0
                    },
                    {
                        label: 'Rehabilitation',
                        data: ["<?php echo e($landTypeCount['Rehabilitation']); ?>"],
                        borderColor: gradientStroke2,
                        backgroundColor: gradientStroke2,
                        hoverBackgroundColor: gradientStroke2,
                        pointRadius: 0,
                        fill: false,
                        borderWidth: 0
                    }
                ]
            },

            options: {
                plugins: {
                    datalabels: datalabelOptions
                },
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom',
                    display: true,
                    labels: {
                        boxWidth: 8
                    }
                },
                tooltips: {
                    displayColors: true,
                    display: true
                },
                scales: {
                    xAxes: [{
                        barPercentage: .5
                    }]
                }
            }
        });

        /*var ctx3 = document.getElementById("chart-area").getContext('2d');

        var gradientStroke1 = ctx3.createLinearGradient(0, 0, 0, 300);
        gradientStroke1.addColorStop(0, '#64B5F6');
        // gradientStroke1.addColorStop(1, '#17c5ea'); 

        var gradientStroke2 = ctx3.createLinearGradient(0, 0, 0, 300);
        gradientStroke2.addColorStop(0, '#E57373');
        // gradientStroke2.addColorStop(1, '#ffdf40');

        var myChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: [<?= implode(", ", $seriesLabels) ?>],
                datasets: [{
                        label: '<?php echo e($labels[0]); ?>',
                        data: [<?= implode(", ", $barData[0]) ?>],
                        borderColor: gradientStroke1,
                        backgroundColor: gradientStroke1,
                        hoverBackgroundColor: gradientStroke1,
                        pointRadius: 0,
                        fill: false,
                        borderWidth: 0
                    },
                    {
                        label: '<?php echo e($labels[1]); ?>',
                        data: [<?= implode(", ", $barData[1]) ?>],
                        borderColor: gradientStroke2,
                        backgroundColor: gradientStroke2,
                        hoverBackgroundColor: gradientStroke2,
                        pointRadius: 1,
                        fill: false,
                        borderWidth: 1
                    }
                ]
            },

            options: {
                plugins: {
                    datalabels: datalabelOptions
                },
                maintainAspectRatio: false,
                legend: {
                    position: 'top',
                    display: false,
                    labels: {
                        boxWidth: 0
                    }
                },
                tooltips: {
                    displayColors: false,
                },
                scales: {
                    XAxes: [{
                        barPercentage: .5
                    }]
                }
            }
        });*/
    });


    /** Land Value chart -------------*/

    /*var ctx4 = document.getElementById("landvalueChart").getContext("2d");

    var gradientStroke1 = ctx4.createLinearGradient(0, 0, 0, 300);
    gradientStroke1.addColorStop(0, "#81C784");
    // gradientStroke1.addColorStop(1, '#17c5ea');

    var gradientStroke2 = ctx4.createLinearGradient(0, 0, 0, 300);
    gradientStroke2.addColorStop(0, "#81C784");
    // gradientStroke2.addColorStop(1, '#ffdf40');

    var myChart = new Chart(ctx4, {
        type: "bar",
        data: {
            labels: [<?= implode(", ", $landValueData['labels']) ?>],
            datasets: [{
                label: "Land Value",
                data: [<?= implode(", ", $landValueData['values']) ?>],
                borderColor: gradientStroke1,
                backgroundColor: gradientStroke1,
                hoverBackgroundColor: gradientStroke1,
                pointRadius: 0,
                fill: false,
                borderWidth: 0,
            }, ],
        },

        options: {
            plugins: {
                datalabels: datalabelOptions,
            },
            maintainAspectRatio: false,
            legend: {
                position: "bottom",
                display: false,
                labels: {
                    boxWidth: 2,
                },
            },
            tooltips: {
                displayColors: false,
            },
            scales: {
                xAxes: [{
                    barPercentage: 0.5,
                }, ],
            },
        },
    });*/


    var a = document.getElementById("revenueINR").getContext("2d");
    const labels = <?php echo json_encode($revenueData['years']); ?>;
    const barPercentage = labels.length > 2 ? undefined : 0.5;
    const stacked = labels.length > 1 ? true : false;
    new Chart(a, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
            <?php $__currentLoopData = $revenueData['services']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                {
                    label: "<?php echo e($service); ?>",
                    backgroundColor: '<?php echo e($revenueColors[$key]); ?>',
                    data: <?php echo json_encode($revenueData['data'][$service]); ?>

                },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ]
        },
        options: {
            plugins: {
                datalabels: {
                    display: !1,
                    color: "#fff"
                }
            },
            cornerRadius: 0,
            tooltips: {
                displayColors: !1,
                callbacks: {
                    mode: "x"
                },
                enabled: !1
            },
            scales: {
                xAxes: [{
                    stacked: stacked,
                    gridLines: {
                        display: !1
                    },
                     barPercentage: barPercentage
                }],
                yAxes: [{
                    stacked: stacked,
                    ticks: {
                        beginAtZero: !0
                    },
                    type: "linear"
                }]
            },
            
            responsive: !0,
            maintainAspectRatio: !1,
            legend: {
                position: "bottom"
            }
        }
    });


    /** Land Value chart -------------*/

    /*** new code by Nitin ---  */
    const getTabData = (tabId) => {
        var ajaxUrl = "<?php echo e(route('propertyTypeDetails', [':typeId', ':colonyId'])); ?>"
    .replace(':typeId', tabId)
    .replace(':colonyId', colony_id ? colony_id : '');
        $.ajax({
            type: 'get',
            url: ajaxUrl,
            success: response => {
                let updatedHTML = '';
                //get max counter 
                let max = 0;
                response.map(row => {
                    max = row.counter > max ? row.counter : max;
                });
                //get progressbar html for each row
                response.map(row => {
                    let width = (max > 0) ? (row.counter / max) * 100 : 0;
                    updatedHTML += `<li>
						<div class = "d-flex justify-content-between align-items-center mb-2">
							<span class = "progress-title">${row.PropSubType}</span>
							<span class="progress-result">${row.counter}</span>
						</div>
						<div class = "progress mb-4" style = "height:7px;">
						<div class="progress-bar" role="progressbar" style="width: ${width}%" aria-valuenow="${row.counter}" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</li>`

                })
                $('ul.progress-report').html(updatedHTML);
            },
            error: errorResponse => {
                console.log(errorResponse);
            }
        })
    }

    /** on selecting  a colony froom colony dropdown*/

    $('#colony-filter').change(() => {
        colony_id = $('#colony-filter').val();
        if (colony_id != "") {
            $('#colony-filter').find('option:first').text('Remove Colony Filter');
            $('#colony-filter').selectpicker('refresh');
            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('dashbordColonyFilter')); ?>",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    colony_id: colony_id
                },
                success: (response) => {
		       $('.outside-tiles').addClass('d-none');
             if (Object.keys(response).length > 0) {
                        $('#no-filter-applied').addClass('d-none'); // hide main dashboaed
                        $('#filter-applied').removeClass('d-none'); // show filtered table and tabs

                        for (const key in response) {
                            // update the tiles
                            if (response.hasOwnProperty(key)) {
                                const value = response[key];
                                let targetElement = $('#tile_' + key);
                                if (targetElement.length) {
                                    if (targetElement.hasClass('hoverable')) {
                                        targetElement.attr('title', value);
                                    } else {
                                        targetElement.html(value);
                                    }
                                }

                            }
                        }
                    }

                    //update property types

                    if (response.property_types && Object.keys(response.property_types).length > 0) {
                       // $('.tab-total-no').html(0);
                        let ptypes = response.property_types
                        for (const ind in ptypes) {
                            // debugger;
                            if (ptypes.hasOwnProperty(ind)) {
                                const value = ptypes[ind];
                                let targetElement = $('#' + ind + '_count');
                                if (targetElement.length) {
                                    targetElement.html(value);
                                }

                                //update the tab data
                                let tabElement = $('#tab_total_' + ind);
                                if (tabElement.length) {
                                    tabElement.html(value);
                                }
                            }
                        }
                    }

                    //update table
                    if (response.areaRangeData.length > 0) {
                        const tbody = $('#area-wise-details tbody');
                        $('#area-wise-details tbody').empty();
                        response.areaRangeData.forEach(item => {
                            tbody.append(`<tr>
								<td>${item.label}</td>
								<td>${item.count} (${item.percent_count}%)</td>
								<td>${item.leaseHoldCount} (${item.percent_leaseHold}%)</td>
								<td>${item.freeHoldCount} (${item.percent_freeHold}%)</td>
								<td>${item.area.toFixed(2)} (${item.percent_area}%)</td>
							</tr>`);
                        });
                        let totalAreaInSqm = response.total_area;
                        let totalAreaInAcre = Math.round(totalAreaInSqm * 0.0002471053815);
                        tbody.append(`<tr>
								<th>Grand Total</th>
								<th>${response.total_count}</th>
								<th colspan="3">${totalAreaInSqm.toFixed(3)} (${totalAreaInAcre} Acres)</th>
							</tr>`);
                    }

                    //click the first tab

                    var elemFound = $('[id^=tab_total_]');
                    if (elemFound.length > 0) {
                        elemFound[0].closest('a').click();
                    }


                },
                error: (err) => {
                    console.log(err);
                }
            })
        } else {
            //reset the filter
            $('.text-dark').each((i, elm) => { // reset text content
                if ($(elm).attr('data-original') !== undefined) {
                    $(elm).html($(elm).attr('data-original'));
                }
            })
            $('.hoverable').each((i, elm) => { // reset text content
                if ($(elm).attr('data-original') !== undefined) {
                    $(elm).attr('title', $(elm).attr('data-original'));
                }
            })

             var elemFound = $('[id^=original_tab_total_]');
            if (elemFound.length > 0) {
                elemFound[0].click();
            }


            $('#no-filter-applied').removeClass('d-none'); // show main dashboaed
            $('#filter-applied').addClass('d-none'); // hide filtered table and tabs

            //Chnage the dropdown first option

            $('#colony-filter').find('option:first').text('Filter by Colony (<?php echo e($number_of_colonies); ?>)');
            $('#colony-filter').selectpicker('refresh');

            // reset colony id
            colony_id = null;
$('.outside-tiles').removeClass('d-none');
        }
    })


    $('.view-list').click(function() {
        let linkEnabled = '<?= $isPublic == true ? 0 : 1 ?>';
        if (linkEnabled == '1') {
            let filterName = $(this).data('filterName');
            let filterValue = $(this).data('filterValue');
            let filterData = (filterName && filterValue) ? {
                [`${filterName}[]`]: filterValue
            } : {};

            if (colony_id) {
                filterData = {
                    ...filterData,
                    "colony[]": colony_id
                }
            }

            // Create a query string from the filterData
            let queryString = $.param(filterData);

            // Construct the URL with the query string
            let url = '';
            if (filterValue == 'unalloted') {
                url = `<?php echo e(route("unallotedReport")); ?>`;
            } else if(filterValue == 'vaccant') {
                url = `<?php echo e(route("vacant.land.list")); ?>`;
            } else if (filterValue == 'pending_request') {
                url = `<?php echo e(route('old.pending.records')); ?>`;
            } else {
                url = `<?php echo e(route("detailedReport")); ?>?${queryString}`;
            }

            // Open the URL in a new tab
            window.open(url, '_blank');
        }

    });
</script>

<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make($layout, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>