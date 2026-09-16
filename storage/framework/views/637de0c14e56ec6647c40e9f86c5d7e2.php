<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <!-- <meta name="csrf-token" content="content"> -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?php echo e(asset('assets/images/logo-icon.png')); ?>" type="image/png" />
    <!--plugins-->

    <link href="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/simplebar/css/simplebar.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/metismenu/css/metisMenu.min.css')); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/jquery-ui.css')); ?>">
    <link href="<?php echo e(asset('assets/css/pace.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/common.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/bootstrap-extended.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap-select.min.css')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/css/app.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/custom.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/icons.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/dark-theme.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/semi-dark.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/header-colors.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/range.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/jquery.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/buttons.dataTables.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.dataTables.min.css')); ?>">
           <script src="<?php echo e(asset('assets/js/jquery-3.7.1.js')); ?>"></script>

   <!-- script moved to header by Nitin to resolve $ is not defined error for scrips not defined in footer -->
    <!-- Toaster CSS Added by Diwakar Sinha at 20-09-2024 -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="<?php echo e(asset('assets/css/sweetalert2.min.css')); ?>" rel="stylesheet">
   
    <title>e-Dharti 2.0 | <?php echo $__env->yieldContent('title'); ?></title>
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px !important;
        }
        .backButton {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8%;
            padding: 1px 12px;
            color: #ffffff;
        }


         #spinnerOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 1000;
        }
        
        /* commented and adeed by anil for replace the new loader on 24-07-2025  */
        /*.spinner {
            border: 8px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 8px solid #ffffff;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        } */
        .loader {
            width: 48px;
            height: 48px;
            border:6px solid #FFF;
            border-radius: 50%;
            position: relative;
            transform:rotate(45deg);
            box-sizing: border-box;
            }
            .loader::before {
            content: "";
            position: absolute;
            box-sizing: border-box;
            inset:-7px;
            border-radius: 50%;
            border:8px solid #116d6e;
            animation: prixClipFix 2s infinite linear;
            }

            @keyframes prixClipFix {
                0%   {clip-path:polygon(50% 50%,0 0,0 0,0 0,0 0,0 0)}
                25%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 0,100% 0,100% 0)}
                50%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,100% 100%,100% 100%)}
                75%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 100%)}
                100% {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 0)}
            }
            /* commented and adeed by anil for replace the new loader on 24-07-2025  */
            
    </style>
</head>

<body>
    <?php echo $__env->make('include.loader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!--wrapper-->
   <!-- <div class="wrapper <?php if(!auth()->user()->hasAnyRole(['applicant', 'section-officer', 'deputy-lndo'])): ?> toggled <?php endif; ?>"> -->
   <div class="wrapper">
        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="<?php echo e(asset('assets/images/logo-icon.png')); ?>" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h4 class="logo-text">e-Dharti 2.0</h4>
                </div>
                <div class="toggle-icon mobile-toggle"><i class='bx bx-menu'></i></div>

            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                <?php if(auth()->check() && auth()->user()->hasRole('applicant') && auth()->user()->roles->count() === 1): ?>
                    <li class="loaderRequired <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('dashboard')); ?>">
                            <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                            <div class="menu-title">Dashboard</div>
                        </a>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='bx bx-home-circle'></i></div>
                            <div class="menu-title">Dashboards</div>
                        </a>
                        <ul>
                            <li class="loaderRequired <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-right-arrow-alt"></i>My
                                    Dashboard</a>
                            </li>
                            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'main.dashboard')): ?>
                                <li class="loaderRequired <?php echo e(request()->is('dashboard/main') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('dashboard.main')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Dashboard</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
         
                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'viewDetails')): ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fadeIn animated bx bx-buildings"></i>
                        </div>
                        <div class="menu-title">Properties (MIS)</div>
                    </a>
                    <ul>
                        <?php if(auth()->user()->hasAnyPermission(['add.single.property','add.multiple.property','create.flat'])): ?>
                        <li> <a href="javascript:;" class="has-arrow submenu-parent"><i class="bx bx-right-arrow-alt"></i>Add</a>
                            <ul>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'add.single.property')): ?>
                                    <li class="<?php echo e(request()->is('property-form') ? 'active' : ''); ?>"> <a
                                            href="<?php echo e(route('mis.index')); ?>"><i class="bx bx-right-arrow-alt"></i>Single Plot</a>
                                    </li>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'add.multiple.property')): ?>
                                    <li class="<?php echo e(request()->is('property-form-multiple') ? 'active' : ''); ?>"> <a
                                            href="<?php echo e(route('mis.form.multiple')); ?>"><i class="bx bx-right-arrow-alt"></i>Multiple Plot</a>
                                    </li>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'create.flat')): ?>
                                    <li class="<?php echo e(request()->is('flat-form') ? 'active' : ''); ?>"> <a
                                            href="<?php echo e(route('create.flat.form')); ?>"><i class="bx bx-right-arrow-alt"></i>Flat</a>
                                    </li>
                                <?php endif; ?>
                                   <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'create.vacant.land')): ?>
    <li class="<?php echo e(request()->is('mis/add/vacant/land') ? 'active' : ''); ?>"> <a
            href="<?php echo e(route('create.vacant.land')); ?>"><i class="bx bx-right-arrow-alt"></i>Outside Delhi Property</a>
    </li>
<?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if(auth()->user()->hasAnyPermission(['viewDetails','view.flat'])): ?>
                        <li> <a href="javascript:;" class="has-arrow submenu-parent"><i class="bx bx-right-arrow-alt"></i>View</a>
                            <ul>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'viewDetails')): ?>
                                <li class="<?php echo e(request()->is('property-details') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('propertDetails')); ?>"><i class='bx bx-chevron-right'></i>Plots</a>
                                </li>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'view.flat')): ?>
                                <li class="<?php echo e(request()->is('flats') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('flats')); ?>"><i class='bx bx-chevron-right'></i>Flats</a>
                                </li>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'view.vacant.land')): ?>
    <li class="<?php echo e(request()->is('/get/vacant/land/list') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('vacant.land.list')); ?>"><i
                class='bx bx-chevron-right'></i>Outside Delhi Property</a>
    </li>
<?php endif; ?>
<?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'view.unallotted')): ?>
    <li class="<?php echo e(request()->is('/reports/unalloted-properties') ? 'active' : ''); ?>">
        <a href="<?php echo e(route('unallotedReport')); ?>"><i
                class='bx bx-chevron-right'></i>Unallotted Property</a>
    </li>
<?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>


            
                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'registrations.listing')): ?>
                     <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='fa-solid fa-box'></i></div>
                            <div class="menu-title">Registration</div>
                        </a>
                        <ul>
                            <li class="<?php echo e(request()->is('register/users') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('regiserUserListings')); ?>"><i
                                        class="bx bx-right-arrow-alt"></i>All</a>
                            </li>
                            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'view.new.added.properties')): ?>
                                <li class="<?php echo e(request()->is('applicant/new/properties') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('applicantNewProperties')); ?>"><i class="bx bx-right-arrow-alt"></i>Additional Property</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                 <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'index.application')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='bx bxs-file'></i></div>
                            <div class="menu-title">Application</div>
                        </a>
                        <ul>
                            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'list.application')): ?>
                                <li class="<?php echo e(request()->is('admin/applications') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('admin.applications')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Submitted</a>
                                </li>
                                <li class="<?php echo e(request()->is('applications/disposed') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('applications.disposed')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Disposed</a>
                                </li>
                                <?php if(!auth()->user()->hasRole('Joint Secretary')): ?>
                                <li class="<?php echo e(request()->is('admin/assigned-applications') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('admin.myapplications')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Assigned</a>
                                </li>
                                <?php endif; ?>

                               
                            <?php endif; ?>

                            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'apply.application')): ?>
                                <li class="<?php echo e(request()->is('application/new') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('new.application')); ?>"><i class="bx bx-right-arrow-alt"></i>New</a>
                                </li>
                                <li class="<?php echo e(request()->is('applications/draft') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('draftApplications')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Draft</a>
                                </li>
                                <li>
                                    <a href="javascript:;" class="has-arrow submenu-parent"><i
                                            class="bx bx-right-arrow-alt"></i> History</a>
                                    <ul>
                                        <li class="<?php echo e(request()->is('applications/history/details') ? 'active' : ''); ?>"> <a
                                                href="<?php echo e(route('applications.history.details')); ?>"><i
                                                    class='bx bx-chevron-right'></i> Submitted Applications</a></li>
                                        <li class="<?php echo e(request()->is('applications/history/withdraw') ? 'active' : ''); ?>"> <a
                                                href="<?php echo e(route('applications.history.withdraw.details')); ?>"><i
                                                    class='bx bx-chevron-right'></i> Withdrawn Applications</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>


                <?php
                    $hasAccess = Auth::user()->can('view.appointment') || Auth::user()->can('view.grievance');
                ?>

                <?php if($hasAccess): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fas fa-users-cog"></i></div>
                            <div class="menu-title">Public Services</div>
                        </a>
                        <ul>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.appointment')): ?>
                            <li class="<?php echo e(request()->is('appointments*') ? 'active' : ''); ?>"> 
                                <a href="<?php echo e(route('appointments.index')); ?>"><i class="bx bx-right-arrow-alt"></i>Appointments</a>
                            </li>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.grievance')): ?>
                            <li class="<?php echo e(request()->is('grievances*') ? 'active' : ''); ?>"> 
                                <a href="<?php echo e(route('grievance.index')); ?>"><i class="bx bx-right-arrow-alt"></i>Grievances</a>
                            </li>
                            <?php endif; ?>



                             <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('club.membership')): ?>
                                <li> <a href="javascript:;" class="has-arrow submenu-parent"><i
                                            class="bx bx-right-arrow-alt"></i>Club Membership</a>
                                    <ul>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('club.membership.create')): ?>
                                            <li class="<?php echo e(request()->is('property-form-multiple') ? 'active' : ''); ?>"> <a
                                                    href="<?php echo e(route('create.club.membership.form')); ?>"><i
                                                        class="bx bx-right-arrow-alt"></i>Add New</a>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('club.membership.list')): ?>
                                            <li class="<?php echo e(request()->is('property-form') ? 'active' : ''); ?>"> <a
                                                    href="<?php echo e(route('club.membership.received.index')); ?>"><i
                                                        class="bx bx-right-arrow-alt"></i>Received</a>
                                            </li>
                                            <li class="<?php echo e(request()->is('property-form') ? 'active' : ''); ?>"> <a
                                                    href="<?php echo e(route('club.membership.index')); ?>"><i
                                                        class="bx bx-right-arrow-alt"></i>Finalized</a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'view reports')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="bx bx-message-square-edit"></i>
                            </div>
                            <div class="menu-title">Reports</div>
                        </a>
                        <ul>
                            <li class="<?php echo e(request()->is('reports') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('reports.index')); ?>"><i class="bx bx-right-arrow-alt"></i>Filter
                                    Report</a>
                            </li>

                            <li class="<?php echo e(request()->is('detailed-report') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('detailedReport')); ?>"><i class="bx bx-right-arrow-alt"></i>Detailed
                                    Report</a>
                            </li>
                            <li class="<?php echo e(request()->is('customize-report') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('customizeReport')); ?>"><i class="bx bx-right-arrow-alt"></i>Customized
                                    Report</a>
                            </li>
                            <li class="<?php echo e(request()->is('colony-wise-filter-report') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('colony.wise.reports.index')); ?>"><i
                                        class="bx bx-right-arrow-alt"></i>Colony-Wise
                                    Report</a>
                            </li>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('registrations.and.applications.reports')): ?>
                                <li class="<?php echo e(request()->routeIs('registrations.report') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('registrations.report')); ?>">
                                        <i class="bx bx-right-arrow-alt"></i>Registrations And Applications Report
                                    </a>
                                </li>                                                               
                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('thirty.days.old.pending.application.report')): ?>
                                <li class="<?php echo e(request()->is('old-pending-records') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('old.pending.records')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>30+ Days Old Pending Requests</a>
                                </li>
                            <?php endif; ?>
                                
                                <li class="<?php echo e(request()->is('lease-hold-demands') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('leaseHoldDemands')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Lease hold demand report</a>
                                </li>
                                
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('record.room.list')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fadeIn animated bx bx-file-find"></i>
                            </div>
                            <div class="menu-title">Record Room</div>
                        </a>
                        <ul>
                            <li class="<?php echo e(request()->is('recordRoom.index') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('recordRoom.index')); ?>"><i class="bx bx-right-arrow-alt"></i>Total
                                    Record List</a>
                            </li>
                            <li class="<?php echo e(request()->is('recordRoom.fileRequest') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('recordRoom.fileRequest')); ?>"><i
                                        class="bx bx-right-arrow-alt"></i>File Request</a>
                            </li>
                            <li class="<?php echo e(request()->is('recordRoom.create') ? 'active' : ''); ?>"> <a
                                    href="<?php echo e(route('recordRoom.create')); ?>"><i class="bx bx-right-arrow-alt"></i>New
                                    Entry</a>
                            </li>
                        </ul>

                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.financial.reports')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fas fa-receipt"></i>
                            </div>
                            <div class="menu-title">Revenue</div>
                        </a>
                        <ul>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('revenue.create')): ?>
                                <li class="<?php echo e(request()->is('revenues.index') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('revenues.index')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Create Revenue</a>
                                </li>
                            <?php endif; ?>
                            <li class="<?php echo e(request()->is('paymentSummary') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('paymentSummary')); ?>"><i class="bx bx-right-arrow-alt"></i>Revenue Summary</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!--
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.financial.reports')): ?>
                     <li class="<?php echo e(request()->is('paymentSummary') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('paymentSummary')); ?>">
                            <div class="parent-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="menu-title"> Revenue Summary</div>
                        </a>
                    </li>
                <?php endif; ?>
                -->
                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.payment.reports')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fas fa-receipt"></i>
                            </div>
                            <div class="menu-title">Payment</div>
                        </a>
                        <ul>
                            <!-- <li class="<?php echo e(request()->is('paymentReport') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('paymentReport')); ?>">
                                    <div class="parent-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="menu-title"> Payment Report</div>
                                </a>
                            </li> -->
                            <li class="<?php echo e(request()->is('paymentReport') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('paymentReport')); ?>"><i class="bx bx-right-arrow-alt"></i>Payment Report</a>
                            </li>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add.manual.payment')): ?>
                            <li class="<?php echo e(request()->is('manualPaymentInputForm') ? 'active' : ''); ?>">
                                <a href="<?php echo e(route('manualPaymentInputForm')); ?>"><i class="bx bx-right-arrow-alt"></i>Manual Payments</a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>  
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.application.reports')): ?>
                    <li class="<?php echo e(request()->is('applicationSummary') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('applicationSummary')); ?>">
                            <div class="parent-icon">
                                <i class="fas fa-bar-chart"></i>
                            </div>
                            <div class="menu-title"> Applications Summary</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.demand.reports')): ?>
                    <li class="<?php echo e(request()->is('demandSummary*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('demandSummary')); ?>">
                            <div class="parent-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="menu-title">Demand Summary</div>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.scanning.list')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="bx bx-scan"></i>
                            </div>
                            <div class="menu-title">Scanned Files</div>
                        </a>
                        <ul>
                            <li><a href="<?php echo e(route('scanning.report')); ?>"><i class='bx bx-chevron-right'></i>Scanned
                                    Files Report</a></li>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add.scanning.files')): ?>
                                <li><a href="<?php echo e(route('property.scanning.create')); ?>"><i class='bx bx-chevron-right'></i>Upload Scanned File</a></li>
                            <?php endif; ?>
                            
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add.request.scan')): ?>
                                <li><a href="<?php echo e(route('scanned.request.index')); ?>"><i class='bx bx-chevron-right'></i>Scanning Request List</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>    
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.litigation.list')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="bx bx-scan"></i>
                            </div>
                            <div class="menu-title">Litigation</div>
                        </a>
                        <ul>
                            <li><a href="<?php echo e(route('litigation.create')); ?>"><i class='bx bx-chevron-right'></i>Add Court Case</a></li>
                            <li><a href="<?php echo e(route('litigation.index')); ?>"><i
                                            class='bx bx-chevron-right'></i>Court Cases List</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if(auth()->user()->hasAnyPermission(['applicant.view.property.details','section.property.mis.update.request'])): ?>

                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fa-solid fa-house-user"></i>
                        </div>
                        <div class="menu-title">Property Details</div>
                    </a>
                    <ul>
                      <!-- <li class="<?php echo e(request()->is('applicant/profile') ? 'active' : ''); ?>"> <a
                                href="<?php echo e(route('applicant.profile')); ?>"><i class="bx bx-right-arrow-alt"></i>Profile
                                <?php echo e(request()->is()); ?></a>
                        </li>  -->
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('applicant.view.property.details')): ?>
                        <li class="<?php echo e(request()->is('applicant/property/details') ? 'active' : ''); ?>"> <a
                                href="<?php echo e(route('applicant.properties')); ?>"><i
                                    class="bx bx-right-arrow-alt"></i>Add New Property <?php echo e(request()->is()); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('section.property.mis.update.request')): ?>
                        <li class="<?php echo e(request()->is('mis/update/request/list') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('misUpdateRequestList')); ?>"><i class="bx bx-right-arrow-alt"></i>MIS
                                Update Request <?php echo e(request()->is()); ?></a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['create.demand', 'view.demand'])): ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="fadeIn animated bx bx-rupee"></i>
                        </div>
                        <div class="menu-title">Demand</div>
                    </a>
                    <ul>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create.demand')): ?>
                        <li class="<?php echo e(request()->is('demand') ? 'active' : ''); ?>"> <a
                                href="<?php echo e(route('createDemandView')); ?>"><i class="bx bx-right-arrow-alt"></i>Create</a>
                        </li>
                        <?php endif; ?>
                        <li class="<?php echo e(request()->is('demandList') ? 'active' : ''); ?>"> <a
                                href="<?php echo e(route('demandList')); ?>"><i class="bx bx-right-arrow-alt"></i>Created Demands</a>
                        </li>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('old.demand.list')): ?>
                        <li class="<?php echo e(request()->is('oldDemandList') ? 'active' : ''); ?>"> <a
                                href="<?php echo e(route('oldDemandList')); ?>"><i class="bx bx-right-arrow-alt"></i>eDharti 1.0 Demands</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['create.rgr', 'create.rgr.draft', 'send.rgr.draft', 'view.rgr.list'])): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class='fadeIn animated bx bx-rupee'></i>
                            </div>
                            <div class="menu-title">RGR</div>
                        </a>
                        <ul>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create.rgr')): ?>
                            <li class="<?php echo e(request()->is('rgr') ? 'active' : ''); ?>"> <a href="<?php echo e(route('rgr')); ?>"><i class="bx bx-right-arrow-alt"></i>Calculate</a>
                            <li class="<?php echo e(request()->is('completeList') ? 'active' : ''); ?>"> <a href="<?php echo e(route('completeList')); ?>"><i class="bx bx-right-arrow-alt"></i>Revised Properties</a>
                            </li>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view.rgr.list')): ?>
                                <li class="<?php echo e(request()->is('rgrList') ? 'active' : ''); ?>"><a href="<?php echo e(route('rgrList')); ?>"><i class='bx bx-chevron-right'></i> Detailed Listing </a></li>
                                <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['create.inspection', 'view.inspection'])): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-puzzle-piece"></i>
                            </div>
                            <div class="menu-title">Inspection</div>
                        </a>
                        <ul>    
                             <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create.inspection')): ?>                      
                            <li class="<?php echo e(request()->is('inspectionRequest') ? 'active' : ''); ?>"> <a href="<?php echo e(route('inspectionRequest')); ?>"><i class="bx bx-right-arrow-alt"></i>Create Inspection</a> </li>
                             <?php endif; ?>
                            <li class="<?php echo e(request()->is('inspectionlist') ? 'active' : ''); ?>"> <a href="<?php echo e(route('inspectionlist')); ?>"><i class="bx bx-right-arrow-alt"></i>View  Inspection</a>
                            </li>                                                   
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('miscellaneous')): ?>
                    <li>
                        <a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="fa-solid fa-puzzle-piece"></i>
                            </div>
                            <div class="menu-title">Miscellaneous</div>
                        </a>
                        <ul>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('miscellaneous.property.transfer')): ?>
                                <li class="<?php echo e(request()->is('property-transfer') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('miscellaneous.property.transfer')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Property Transfer</a>
                                </li>
                            <?php endif; ?>
                            <?php if(auth()->user()->hasRole('it-cell')): ?>
                                <li class="<?php echo e(request()->is('colony-wise-filter-report') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('colony.wise.reports.index')); ?>"><i
                                            class="bx bx-right-arrow-alt"></i>Search Property</a>
                                </li>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('registrations.and.applications.reports')): ?>
                                <li class="<?php echo e(request()->routeIs('registrations.report') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('registrations.report')); ?>">
                                        <i class="bx bx-right-arrow-alt"></i>Registrations And Applications Report
                                    </a>
                                </li>                                                               
                            <?php endif; ?>

                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('remove.application.or.registration')): ?>
                                <li class="<?php echo e(request()->is('remove.application') ? 'active' : ''); ?>"> <a
                                        href="<?php echo e(route('remove.application')); ?>"><i class="bx bx-right-arrow-alt"></i>Remove
                                        Application / Registration</a>
                                </li>
                            <?php endif; ?>
                            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'super-admin')): ?>
                                <li> <a href="<?php echo e(route('admin.rollback')); ?>"><i
                                                class="bx bx-right-arrow-alt"></i>Application Rollback</a>
                                </li>
                            <?php endif; ?>
                            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'super-admin')): ?>
                                <li> <a href="<?php echo e(route('payment.livestatus.search')); ?>"><i
                                                class="bx bx-right-arrow-alt"></i>Payment Live Status Search</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                
                
                <!-- <li class="<?php echo e(request()->is('/application/deed_of_apartment/fill_application_details') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('application.apartment.create')); ?>">
                        <div class="parent-icon"><i class='bx bxs-file'></i>
                        </div>
                        <div class="menu-title">Deed of Apartment</div>
                    </a>
                </li>
                <li class="menu-label">UI Elements</li>
                <li>
                    <a href="widgets.html">
                        <div class="parent-icon"><i class='bx bx-cookie'></i>
                        </div>
                        <div class="menu-title">Widgets</div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-cart'></i>
                        </div>
                        <div class="menu-title">eCommerce</div>
                    </a>
                    <ul>
                        <li> <a href="ecommerce-products.html"><i class="bx bx-right-arrow-alt"></i>Products</a>
                        </li>
                        <li> <a href="ecommerce-products-details.html"><i class="bx bx-right-arrow-alt"></i>Product
                                Details</a>
                        </li>
                        <li> <a href="ecommerce-add-new-products.html"><i class="bx bx-right-arrow-alt"></i>Add New
                                Products</a>
                        </li>
                        <li> <a href="ecommerce-orders.html"><i class="bx bx-right-arrow-alt"></i>Orders</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i>
                        </div>
                        <div class="menu-title">Components</div>
                    </a>
                    <ul>
                        <li> <a href="component-alerts.html"><i class="bx bx-right-arrow-alt"></i>Alerts</a>
                        </li>
                        <li> <a href="component-accordions.html"><i class="bx bx-right-arrow-alt"></i>Accordions</a>
                        </li>
                        <li> <a href="component-badges.html"><i class="bx bx-right-arrow-alt"></i>Badges</a>
                        </li>
                        <li> <a href="component-buttons.html"><i class="bx bx-right-arrow-alt"></i>Buttons</a>
                        </li>
                        <li> <a href="component-cards.html"><i class="bx bx-right-arrow-alt"></i>Cards</a>
                        </li>
                        <li> <a href="component-carousels.html"><i class="bx bx-right-arrow-alt"></i>Carousels</a>
                        </li>
                        <li> <a href="component-list-groups.html"><i class="bx bx-right-arrow-alt"></i>List Groups</a>
                        </li>
                        <li> <a href="component-media-object.html"><i class="bx bx-right-arrow-alt"></i>Media
                                Objects</a>
                        </li>
                        <li> <a href="component-modals.html"><i class="bx bx-right-arrow-alt"></i>Modals</a>
                        </li>
                        <li> <a href="component-navs-tabs.html"><i class="bx bx-right-arrow-alt"></i>Navs & Tabs</a>
                        </li>
                        <li> <a href="component-navbar.html"><i class="bx bx-right-arrow-alt"></i>Navbar</a>
                        </li>
                        <li> <a href="component-paginations.html"><i class="bx bx-right-arrow-alt"></i>Pagination</a>
                        </li>
                        <li> <a href="component-popovers-tooltips.html"><i class="bx bx-right-arrow-alt"></i>Popovers
                                & Tooltips</a>
                        </li>
                        <li> <a href="component-progress-bars.html"><i class="bx bx-right-arrow-alt"></i>Progress</a>
                        </li>
                        <li> <a href="component-spinners.html"><i class="bx bx-right-arrow-alt"></i>Spinners</a>
                        </li>
                        <li> <a href="component-notifications.html"><i
                                    class="bx bx-right-arrow-alt"></i>Notifications</a>
                        </li>
                        <li> <a href="component-avtars-chips.html"><i class="bx bx-right-arrow-alt"></i>Avatrs &
                                Chips</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-repeat"></i>
                        </div>
                        <div class="menu-title">Content</div>
                    </a>
                    <ul>
                        <li> <a href="content-grid-system.html"><i class="bx bx-right-arrow-alt"></i>Grid System</a>
                        </li>
                        <li> <a href="content-typography.html"><i class="bx bx-right-arrow-alt"></i>Typography</a>
                        </li>
                        <li> <a href="content-text-utilities.html"><i class="bx bx-right-arrow-alt"></i>Text
                                Utilities</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"> <i class="bx bx-donate-blood"></i>
                        </div>
                        <div class="menu-title">Icons</div>
                    </a>
                    <ul>
                        <li> <a href="icons-line-icons.html"><i class="bx bx-right-arrow-alt"></i>Line Icons</a>
                        </li>
                        <li> <a href="icons-boxicons.html"><i class="bx bx-right-arrow-alt"></i>Boxicons</a>
                        </li>
                        <li> <a href="icons-feather-icons.html"><i class="bx bx-right-arrow-alt"></i>Feather Icons</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Forms & Tables</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Forms</div>
                    </a>
                    <ul>
                        <li> <a href="form-elements.html"><i class="bx bx-right-arrow-alt"></i>Form Elements</a>
                        </li>
                        <li> <a href="form-input-group.html"><i class="bx bx-right-arrow-alt"></i>Input Groups</a>
                        </li>
                        <li> <a href="form-radios-and-checkboxes.html"><i class="bx bx-right-arrow-alt"></i>Radios &
                                Checkboxes</a>
                        </li>
                        <li> <a href="form-layouts.html"><i class="bx bx-right-arrow-alt"></i>Forms Layouts</a>
                        </li>
                        <li> <a href="form-validations.html"><i class="bx bx-right-arrow-alt"></i>Form Validation</a>
                        </li>
                        <li> <a href="form-wizard.html"><i class="bx bx-right-arrow-alt"></i>Form Wizard</a>
                        </li>
                        <li> <a href="form-text-editor.html"><i class="bx bx-right-arrow-alt"></i>Text Editor</a>
                        </li>
                        <li> <a href="form-file-upload.html"><i class="bx bx-right-arrow-alt"></i>File Upload</a>
                        </li>
                        <li> <a href="form-date-time-pickes.html"><i class="bx bx-right-arrow-alt"></i>Date
                                Pickers</a>
                        </li>
                        <li> <a href="form-select2.html"><i class="bx bx-right-arrow-alt"></i>Select2</a>
                        </li>
                        <li> <a href="form-repeater.html"><i class="bx bx-right-arrow-alt"></i>Form Repeater</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Tables</div>
                    </a>
                    <ul>
                        <li> <a href="table-basic-table.html"><i class="bx bx-right-arrow-alt"></i>Basic Table</a>
                        </li>
                        <li> <a href="table-datatable.html"><i class="bx bx-right-arrow-alt"></i>Data Table</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Pages</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-lock"></i>
                        </div>
                        <div class="menu-title">Authentication</div>
                    </a>
                    <ul>
                        <li><a class="has-arrow" href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Basic</a>
                            <ul>
                                <li><a href="auth-basic-signin.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Sign In</a></li>
                                <li><a href="auth-basic-signup.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Sign Up</a></li>
                                <li><a href="auth-basic-forgot-password.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Forgot Password</a></li>
                                <li><a href="auth-basic-reset-password.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Reset Password</a></li>
                            </ul>
                        </li>
                        <li><a class="has-arrow" href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Cover</a>
                            <ul>
                                <li><a href="auth-cover-signin.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Sign In</a></li>
                                <li><a href="auth-cover-signup.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Sign Up</a></li>
                                <li><a href="auth-cover-forgot-password.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Forgot Password</a></li>
                                <li><a href="auth-cover-reset-password.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Reset Password</a></li>
                            </ul>
                        </li>
                        <li><a class="has-arrow" href="javascript:;"><i class="bx bx-right-arrow-alt"></i>With Header
                                Footer</a>
                            <ul>
                                <li><a href="auth-header-footer-signin.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Sign In</a></li>
                                <li><a href="auth-header-footer-signup.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Sign Up</a></li>
                                <li><a href="auth-header-footer-forgot-password.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Forgot Password</a></li>
                                <li><a href="auth-header-footer-reset-password.html" target="_blank"><i
                                            class="bx bx-right-arrow-alt"></i>Reset Password</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="user-profile.html">
                        <div class="parent-icon"><i class="bx bx-user-circle"></i>
                        </div>
                        <div class="menu-title">User Profile</div>
                    </a>
                </li>
                <li>
                    <a href="timeline.html">
                        <div class="parent-icon"> <i class="bx bx-video-recording"></i>
                        </div>
                        <div class="menu-title">Timeline</div>
                    </a>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-error"></i>
                        </div>
                        <div class="menu-title">Errors</div>
                    </a>
                    <ul>
                        <li> <a href="errors-404-error.html" target="_blank"><i class="bx bx-right-arrow-alt"></i>404
                                Error</a>
                        </li>
                        <li> <a href="errors-500-error.html" target="_blank"><i class="bx bx-right-arrow-alt"></i>500
                                Error</a>
                        </li>
                        <li> <a href="errors-coming-soon.html" target="_blank"><i
                                    class="bx bx-right-arrow-alt"></i>Coming Soon</a>
                        </li>
                        <li> <a href="error-blank-page.html" target="_blank"><i
                                    class="bx bx-right-arrow-alt"></i>Blank Page</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="faq.html">
                        <div class="parent-icon"><i class="bx bx-help-circle"></i>
                        </div>
                        <div class="menu-title">FAQ</div>
                    </a>
                </li>
                <li>
                    <a href="pricing-table.html">
                        <div class="parent-icon"><i class="bx bx-diamond"></i>
                        </div>
                        <div class="menu-title">Pricing</div>
                    </a>
                </li>
                <li class="menu-label">Charts & Maps</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-line-chart"></i>
                        </div>
                        <div class="menu-title">Charts</div>
                    </a>
                    <ul>
                        <li> <a href="charts-apex-chart.html"><i class="bx bx-right-arrow-alt"></i>Apex</a>
                        </li>
                        <li> <a href="charts-chartjs.html"><i class="bx bx-right-arrow-alt"></i>Chartjs</a>
                        </li>
                        <li> <a href="charts-highcharts.html"><i class="bx bx-right-arrow-alt"></i>Highcharts</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-map-alt"></i>
                        </div>
                        <div class="menu-title">Maps</div>
                    </a>
                    <ul>
                        <li> <a href="map-google-maps.html"><i class="bx bx-right-arrow-alt"></i>Google Maps</a>
                        </li>
                        <li> <a href="map-vector-maps.html"><i class="bx bx-right-arrow-alt"></i>Vector Maps</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Others</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-menu"></i>
                        </div>
                        <div class="menu-title">Menu Levels</div>
                    </a>
                    <ul>
                        <li> <a class="has-arrow" href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Level
                                One</a>
                            <ul>
                                <li> <a class="has-arrow" href="javascript:;"><i
                                            class="bx bx-right-arrow-alt"></i>Level Two</a>
                                    <ul>
                                        <li> <a href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Level
                                                Three</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="https://codervent.com/rocker/documentation/index.html" target="_blank">
                        <div class="parent-icon"><i class="bx bx-folder"></i>
                        </div>
                        <div class="menu-title">Documentation</div>
                    </a>
                </li>
                <li>
                    <a href="https://themeforest.net/user/codervent" target="_blank">
                        <div class="parent-icon"><i class="bx bx-support"></i>
                        </div>
                        <div class="menu-title">Support</div>
                    </a>
                </li> -->
            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand">
                    <div class="mob-logo">
                        <img src="<?php echo e(asset('assets/images/logo-icon.png')); ?>" class="logo-icon" alt="logo icon">
                    </div>
                    <div class="toggle-icon"><i class='bx bx-menu'></i></div>
                    <!-- <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
                </div> -->
                    <!-- <div class="search-bar flex-grow-1">
                    <div class="position-relative search-bar-box">
                        <input type="text" class="form-control search-control"
                            placeholder="Type to search..."> <span
                            class="position-absolute top-50 search-show translate-middle-y"><i
                                class='bx bx-search'></i></span>
                            <span class="position-absolute top-50 search-close translate-middle-y"><i
                                    class='bx bx-x'></i></span>
                        </div>
                    </div> -->
                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center">
                            <!-- <li class="nav-item mobile-search-icon">
                                <a class="nav-link" href="#"> <i class='bx bx-search'></i>
                                </a>
                            </li> -->
                            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'setting')): ?>
                            <?php echo $__env->make('layouts.settings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                            <li class="d-none nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="alert-count">7</span>
                                    <i class='bx bx-bell'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Notifications</p>
                                            <p class="msg-header-clear ms-auto">Marks all as read</p>
                                        </div>
                                    </a>
                                    <div class="header-notifications-list">
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-primary text-primary"><i
                                                        class="bx bx-group"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Customers<span
                                                            class="msg-time float-end">14 Sec
                                                            ago</span></h6>
                                                    <p class="msg-info">5 new user registered</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-danger text-danger"><i
                                                        class="bx bx-cart-alt"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Orders <span class="msg-time float-end">2
                                                            min
                                                            ago</span></h6>
                                                    <p class="msg-info">You have recived new orders</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-success text-success"><i
                                                        class="bx bx-file"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">24 PDF File<span class="msg-time float-end">19
                                                            min
                                                            ago</span></h6>
                                                    <p class="msg-info">The pdf files generated</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-warning text-warning"><i
                                                        class="bx bx-send"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Time Response <span
                                                            class="msg-time float-end">28 min
                                                            ago</span></h6>
                                                    <p class="msg-info">5.1 min avarage time response</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-info text-info"><i
                                                        class="bx bx-home-circle"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Product Approved <span
                                                            class="msg-time float-end">2 hrs
                                                            ago</span>
                                                    </h6>
                                                    <p class="msg-info">Your new product has approved</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-danger text-danger"><i
                                                        class="bx bx-message-detail"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Comments <span class="msg-time float-end">4
                                                            hrs
                                                            ago</span></h6>
                                                    <p class="msg-info">New customer comments recived</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-success text-success"><i
                                                        class='bx bx-check-square'></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Your item is shipped <span
                                                            class="msg-time float-end">5 hrs
                                                            ago</span></h6>
                                                    <p class="msg-info">Successfully shipped your item</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-primary text-primary"><i
                                                        class='bx bx-user-pin'></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New 24 authors<span
                                                            class="msg-time float-end">1 day
                                                            ago</span></h6>
                                                    <p class="msg-info">24 new authors joined last week</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-warning text-warning"><i
                                                        class='bx bx-door-open'></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Defense Alerts <span
                                                            class="msg-time float-end">2 weeks
                                                            ago</span></h6>
                                                    <p class="msg-info">45% less alerts last 4 weeks</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">View All Notifications</div>
                                    </a>
                                </div>
                            </li>
                            <li class="d-none nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="alert-count">8</span>
                                    <i class='bx bx-comment'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:;">
                                        <div class="msg-header">
                                            <p class="msg-header-title">Messages</p>
                                            <p class="msg-header-clear ms-auto">Marks all as read</p>
                                        </div>
                                    </a>
                                    <div class="header-message-list">
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-1.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Daisy Anderson <span
                                                            class="msg-time float-end">5 sec
                                                            ago</span></h6>
                                                    <p class="msg-info">The standard chunk of lorem</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-2.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Althea Cabardo <span
                                                            class="msg-time float-end">14
                                                            sec ago</span></h6>
                                                    <p class="msg-info">Many desktop publishing packages</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-3.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Oscar Garner <span class="msg-time float-end">8
                                                            min
                                                            ago</span></h6>
                                                    <p class="msg-info">Various versions have evolved over</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-4.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Katherine Pechon <span
                                                            class="msg-time float-end">15
                                                            min ago</span></h6>
                                                    <p class="msg-info">Making this the first true generator</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-5.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Amelia Doe <span class="msg-time float-end">22
                                                            min
                                                            ago</span></h6>
                                                    <p class="msg-info">Duis aute irure dolor in reprehenderit</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-6.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Cristina Jhons <span
                                                            class="msg-time float-end">2 hrs
                                                            ago</span></h6>
                                                    <p class="msg-info">The passage is attributed to an unknown</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-7.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">James Caviness <span
                                                            class="msg-time float-end">4 hrs
                                                            ago</span></h6>
                                                    <p class="msg-info">The point of using Lorem</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-8.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Peter Costanzo <span
                                                            class="msg-time float-end">6 hrs
                                                            ago</span></h6>
                                                    <p class="msg-info">It was popularised in the 1960s</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-9.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">David Buckley <span
                                                            class="msg-time float-end">2 hrs
                                                            ago</span></h6>
                                                    <p class="msg-info">Various versions have evolved over</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-10.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Thomas Wheeler <span
                                                            class="msg-time float-end">2 days
                                                            ago</span></h6>
                                                    <p class="msg-info">If you are going to use a passage</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="user-online">
                                                    <img src="<?php echo e(asset('assets/images/avatars/avatar-11.png')); ?>"
                                                        class="msg-avatar" alt="user avatar">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Johnny Seitz <span class="msg-time float-end">5
                                                            days
                                                            ago</span></h6>
                                                    <p class="msg-info">All the Lorem Ipsum generators</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <a href="javascript:;">
                                        <div class="text-center msg-footer">View All Messages</div>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div onclick="handleBackButtonClick()" class="backButton bg-primary">
                        <i class="fadeIn animated bx bx-left-arrow-alt align-middle font-22 text-white"></i>
                        Go Back
                    </div>
                    <div class="user-box dropdown">
                        <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo e(Auth::user()->applicantUserDetails ? asset('storage/' . Auth::user()->applicantUserDetails->profile_photo) : asset('assets/images/avatars/avatar-1.png')); ?>"
                                class="user-img" alt="user avatar">
                            <div class="user-info ps-3">
                                <p class="user-name mb-0"><?php echo e(Auth::user()->name); ?></p>
                                <p class="designattion mb-0"><?php echo e(Auth::user()->email); ?></p>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo e(route('applicant.profile')); ?>"><i
                                        class="bx bx-user"></i><span>Profile</span></a>
                            </li>
                            <li><a class="dropdown-item" href="<?php echo e(route('password.reset')); ?>"><i
                                        class="bx bx-lock"></i><span>Change Password</span></a>
                            </li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>

                                    <a class="dropdown-item" href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                        <i class='bx bx-log-out-circle'></i> <span><?php echo e(__('Log Out')); ?></span>
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--end header -->


        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                <?php if(session('success')): ?>
                <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
                    <div class="text-white"><?php echo e(session('success')); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if(session('failure')): ?>
                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                    <div class="text-white"><?php echo e(session('failure')); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-white"><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
        <!--end page wrapper -->



        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">Copyright © <?php echo e(date('Y')); ?>. All right reserved.</p>
        </footer>
    </div>
    <!--end wrapper-->
    <div class="overlay"></div>


    <!--start switcher-->
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['calculate.conversion', ])): ?>
    <div class="switcher-wrapper">
        <div class="switcher-btn"> <!--<i class="fa-solid fa-screwdriver-wrench bx-spin"></i>-->
            <h6 class="charges_title"><i class='bx bx-info-circle'></i> Know the Charges</h6>
        </div>
        <div class="switcher-body">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-uppercase">Utilities</h5>
                <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
            </div>

            <hr />
            <div class="header-colors-indigators">

                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <h5 class="utilities-title">Calculator <i class='bx bxs-calculator'></i></h5>
                    </div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('calculate.conversion')): ?>
                    <div class="col">
                        <a href="<?php echo e(route('calculateConversionCharges')); ?>">
                            <span><i class='bx bx-chevron-right'></i> Conversion</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <!-- <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('calculate.landUseChange')): ?>
                    <div class="col">
                        <a href="<?php echo e(route('calculateLandUseChangeCharges')); ?>">
                            <span><i class='bx bx-chevron-right'></i> Land Use Change</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('calculate.unearnedIncrease')): ?>
                    <div class="col">
                        <a href="<?php echo e(route('calculateUnearnedIncrease')); ?>">
                            <span><i class='bx bx-chevron-right'></i> Unearned Increase</span>
                        </a>
                    </div>
                    <?php endif; ?> -->
                </div>

            </div>
        </div>
    </div>
    <?php endif; ?>
    <!--end switcher-->

    <div id="spinnerOverlay" style="display:none;">
        <span class="loader"></span>
        <h1 style="color: white;font-size: 20px; margin-top:10px;">Loading... Please wait</h1>
    </div>

    <div class="modal fade" id="fileSizeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">File Upload Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fileSizeModalText"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
            </div>
        </div>
    </div>

    <?php if(session('hide_old_records_popup')): ?>
        <?php echo e(session()->forget('hide_old_records_popup')); ?>

    <!-- Old Registrations Modal - Shows on any page when session has data -->
    <?php elseif(session('show_old_records_popup') && session('old_records_data') && count(session('old_records_data')) > 0): ?>
    <!-- CSS Styles -->
    <style>
        /* Custom modal styles */
        .modal-xl {
            max-width: 95%;
        }

        @media (min-width: 1400px) {
            .old-records-modal .modal-dialog {
                max-width: 90%;
                width: 1400px;
            }
        }

        @media (min-width: 1920px) {
            .old-records-modal .modal-dialog {
                max-width: 85%;
                width: 1600px;
            }
        }

        @media (max-width: 768px) {
            .old-records-modal .modal-dialog {
                margin: 10px;
                max-width: calc(100% - 20px);
            }

            .remark-textarea {
                min-width: 200px !important;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }

        /* Sticky header for table */
        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
        }

        /* Better textarea styling */
        .remark-textarea:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }

        /* Button hover effects */
        .submit-remark:hover {
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        /* Badge styling */
        .badge-danger {
            background-color: #dc3545;
            color: white;
            font-size: 13px;
        }
        
        .badge-registration {
            background-color: #17a2b8;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
        }
        
        .badge-application {
            background-color: #6610f2;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
        }

        /* Scrollbar styling */
        .modal-body::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Type-specific row styling */
        .row-registration {
            border-left: 4px solid #17a2b8;
        }
        
        .row-application {
            border-left: 4px solid #6610f2;
        }
    </style>

    <div class="modal fade old-records-modal" id="oldRecordsModal" tabindex="-1" aria-labelledby="oldRecordsModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" style="max-width: 90%; width: 1600px; margin: 1.75rem auto;">
            <div class="modal-content" style="max-height: 90vh; border-radius: 12px; box-shadow: 0 5px 25px rgba(0,0,0,0.2);">
                <div class="modal-header bg-warning" style="border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title" id="oldRecordsModalLabel">
                        <i class="fas fa-exclamation-triangle"></i> 30+ Days Old Pending Requests
                    </h5>
                </div>
                <div class="modal-body" style="max-height: calc(90vh - 120px); overflow-y: auto; padding: 20px;">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="color: white;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Attention!</strong> The following records have been pending for more than 90 days.
                        Please take necessary action and add remarks.
                    </div>

                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-bordered table-hover table-striped">
                            <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th style="width: 4%; min-width: 50px;">#</th>
                                    <th style="width: 8%; min-width: 100px;">Type</th>
                                    <th style="width: 12%; min-width: 150px;">Identifier</th>
                                    <th style="width: 18%; min-width: 180px;"><?php echo e($record['name_label'] ?? 'Name/Details'); ?></th>
                                    <th style="width: 10%; min-width: 120px;">Last Action Date</th>
                                    <th style="width: 8%; min-width: 100px;">Days Pending</th>
                                    <th style="width: 30%; min-width: 350px;">Remark</th>
                                    <th style="width: 10%; min-width: 130px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = session('old_records_data'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr id="row-<?php echo e($record['type']); ?>-<?php echo e($record['id']); ?>" 
                                        class="row-<?php echo e($record['type']); ?>"
                                        style="vertical-align: middle;">
                                        <td><strong><?php echo e($index + 1); ?></strong></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($record['type'] == 'registration' ? 'registration' : 'application'); ?>">
                                                <i class="fas fa-<?php echo e($record['type'] == 'registration' ? 'user-plus' : 'file-alt'); ?>"></i>
                                                <?php echo e($record['type_label']); ?>

                                            </span>
                                        </td>
                                        <td>
                                            
                                            <?php
                                                // Generate the appropriate URL based on type
                                                $pendingAlertValue = base64_encode('true');
                                                if ($record['type'] == 'registration') {
                                                    $viewUrl = url("/edharti/register/user/{$record['id']}/view?pending_alert=" . $pendingAlertValue);
                                                } else {
                                                    $encodedType = base64_encode($record['model_name']);
                                                    $viewUrl = url("/edharti/applications/{$record['model_id']}?type={$encodedType}&pending_alert=" . $pendingAlertValue);
                                                }
                                                ?>
                    
                                            <a href="<?php echo e($viewUrl); ?>" 
                                            target="_blank" 
                                            class="text-primary" 
                                            style="text-decoration: none; font-weight: bold; display: inline-block;">
                                                <strong><?php echo e($record['identifier']); ?></strong>
                                                <i class="fas fa-external-link-alt" style="font-size: 12px; margin-left: 5px;"></i>
                                            </a>
                                            <br>
                                            <small class="text-muted"><?php echo e($record['identifier_label']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo e($record['name']); ?>

                                            
                                        </td>
                                        <td>
                                            <?php echo e(\Carbon\Carbon::parse($record['created_at'])->format('d-m-Y')); ?>

                                            <br>
                                            <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($record['created_at'])->format('h:i A')); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger" style="font-size: 14px; padding: 6px 12px;">
                                                <i class="fas fa-hourglass-half"></i>
                                                <?php echo e(\Carbon\Carbon::parse($record['created_at'])->diffInDays(now())); ?> days
                                            </span>
                                        </td>
                                        <td>
                                            <textarea id="remark-<?php echo e($record['type']); ?>-<?php echo e($record['id']); ?>" 
                                                    class="form-control remark-textarea" 
                                                    rows="4"
                                                    placeholder="Enter your remark here... (Min 2 characters, Max 1000 characters)"
                                                    style="width: 100%; min-width: 300px; resize: vertical; font-size: 13px; line-height: 1.5;" 
                                                    maxlength="1000"><?php echo e($record['pending_remark'] ?? ''); ?></textarea>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> Press Ctrl+Enter to submit
                                                </small>
                                                <small class="char-counter text-muted" id="char-count-<?php echo e($record['type']); ?>-<?php echo e($record['id']); ?>">
                                                    <?php echo e(strlen($record['pending_remark'] ?? '')); ?>/1000 characters
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-primary submit-remark"
                                                    style="width: 100%; margin-bottom: 5px;"
                                                    data-type="<?php echo e($record['type']); ?>"
                                                    data-id="<?php echo e($record['id']); ?>"
                                                    data-identifier="<?php echo e($record['identifier']); ?>">
                                                Submit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 12px 12px;">
                    <div class="d-flex justify-content-between w-100">
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-list"></i> Total Records: <?php echo e(count(session('old_records_data'))); ?>

                            </span>
                        </div>
                        <div>
                            <span class="text-muted">
                                <i class="fas fa-user-plus"></i> Registrations: 
                                <?php echo e(collect(session('old_records_data'))->where('type', 'registration')->count()); ?>

                            </span>
                            <span class="text-muted ml-3">
                                <i class="fas fa-file-alt"></i> Applications: 
                                <?php echo e(collect(session('old_records_data'))->where('type', 'application')->count()); ?>

                            </span>
                            
                            <a href="<?php echo e(url('/edharti/dashboard')); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                            </a>
                        </div>
                         
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>


    <!-- Bootstrap JS -->

    <script src="<?php echo e(asset('assets/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/buttons.flash.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jszip.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/buttons.html5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/buttons.print.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/pdfmake.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/vfs_fonts.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/pace.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/jquery-ui.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/simplebar/js/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/metismenu/js/metisMenu.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js')); ?>"></script>
    <!-- Commented By Lalit on 09/18/2024 Duplicate we are already using jquery.dataTables.min.js that's why we commented jquery.dataTables.min.js  -->
    
    <script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/summernote-lite.min.js')); ?>"></script>
    <!-- Toast JS Added by Diwakar Sinha at 20-09-2024 -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="<?php echo e(asset('assets/js/sweetalert2@11.js')); ?>"></script>

    <script>

         function showPopup(message) {
            document.getElementById("fileSizeModalText").textContent = message;
            var modal = new bootstrap.Modal(document.getElementById("fileSizeModal"));
            modal.show();
        }

        $(document).ready(function () {
            $('#myDataTable').DataTable();
        });

         function handleBackButtonClick() {
            if (document.referrer !== "") {
                window.history.back();
            } else {
                window.location.href = "<?php echo e(route('dashboard')); ?>";
            }
        }
    </script>

    <script>
        /* $(document).ready(function () {
            var $alertElement = $('.alert');
            if ($alertElement.length) {
                setTimeout(function () {
                    $alertElement.fadeOut();
                }, 3000);
            }
               const spinnerOverlay = document.getElementById('spinnerOverlay');
               if(spinnerOverlay){
                spinnerOverlay.style.display = 'none';
               }
        }); */
    </script>

    <script>
        $(document).ready(function() {
            // Initialize character counters for all textareas
            $('.remark-textarea').each(function() {
                initializeCharacterCounter($(this));
            });
            
            function initializeCharacterCounter($element) {
                const id = $element.attr('id');
                const maxLength = 1000;
                const currentLength = $element.val().length;
                
                if (id && id.startsWith('remark-')) {
                    const parts = id.split('-');
                    const type = parts[1];
                    const recordId = parts[2];
                    
                    if ($('#char-count-' + type + '-' + recordId).length) {
                        updateCharCount(type, recordId, currentLength, maxLength);
                    }
                }
                
                $element.on('input', function() {
                    const length = $(this).val().length;
                    const currentId = $(this).attr('id');
                    
                    if (currentId.startsWith('remark-')) {
                        const parts = currentId.split('-');
                        const type = parts[1];
                        const recordId = parts[2];
                        
                        if ($('#char-count-' + type + '-' + recordId).length) {
                            updateCharCount(type, recordId, length, maxLength);
                        }
                    }
                });
            }
            
            function updateCharCount(type, recordId, length, maxLength) {
                const $charCount = $('#char-count-' + type + '-' + recordId);
                $charCount.text(`${length}/${maxLength} characters`);
                if (length > maxLength) {
                    $charCount.css('color', 'red');
                } else if (length >= 900) {
                    $charCount.css('color', 'orange');
                } else {
                    $charCount.css('color', '#6c757d');
                }
            }

            // Show modal automatically if exists
            if ($('#oldRecordsModal').length > 0) {
                setTimeout(function() {
                    $('#oldRecordsModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#oldRecordsModal').modal('show');
                }, 500);
            }

            // Handle remark submission for both types
            $(document).on('click', '.submit-remark', function() {
                const recordId = $(this).data('id');
                const recordType = $(this).data('type');
                const identifier = $(this).data('identifier');
                const remarkText = $('#remark-' + recordType + '-' + recordId).val().trim();
                const $button = $(this);
                const $textarea = $('#remark-' + recordType + '-' + recordId);
                
                submitRemark(recordId, recordType, identifier, remarkText, $button, $textarea);
            });
            
            function submitRemark(recordId, recordType, identifier, remarkText, $button, $textarea) {
                // Validate remark
                if (remarkText === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remark Required',
                        text: 'Please enter a remark before submitting.',
                        confirmButtonColor: '#3085d6'
                    });
                    $textarea.focus();
                    return;
                }

                if (remarkText.length < 2) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remark Too Short',
                        text: 'Remark must be at least 2 characters long.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                
                if (remarkText.length > 1000) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Remark Too Long',
                        text: 'Remark cannot exceed 1000 characters.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                // Confirm before updating
                const typeLabel = recordType === 'registration' ? 'Registration' : 'Application';
                const identifierLabel = recordType === 'registration' ? 'applicant number' : 'application number';
                    
                Swal.fire({
                    title: `Update ${typeLabel} Remark?`,
                    text: `Are you sure you want to update remark for ${identifierLabel} ${identifier}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Disable button and show loading state
                        $button.prop('disabled', true);
                        $button.html('<i class="fas fa-spinner fa-spin"></i> Updating...');

                        // Get CSRF token
                        const token = $('meta[name="csrf-token"]').attr('content');
                        
                        // Prepare data
                        const postData = {
                            _token: token,
                            record_id: recordId,
                            type: recordType,
                            remark: remarkText
                        };

                        // Make AJAX request
                        $.ajax({
                            url: '<?php echo e(route("old.record.update.pending.remark")); ?>',
                            type: 'POST',
                            data: postData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        confirmButtonColor: '#3085d6',
                                        timer: 2000,
                                        timerProgressBar: true
                                    });

                                    $textarea.css('background-color', '#e8f5e9');
                                    $button.html('<i class="fas fa-check"></i> Updated');
                                    $button.removeClass('btn-primary').addClass('btn-success');
                                    // location.href = "<?php echo e(route('dashboard')); ?>";
                                    /* setTimeout(function() {
                                        location.href = "<?php echo e(route('dashboard')); ?>";
                                    }, 500); */
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                        confirmButtonColor: '#d33'
                                    });
                                    $button.prop('disabled', false);
                                    $button.html('<i class="fas fa-paper-plane"></i> Submit');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'An error occurred. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Update Failed',
                                    text: errorMessage,
                                    confirmButtonColor: '#d33'
                                });
                                $button.prop('disabled', false);
                                $button.html('<i class="fas fa-paper-plane"></i> Submit');
                            }
                        });
                    }
                });
            }

            // Auto-save remark on Ctrl+Enter
            $(document).on('keydown', '.remark-textarea', function(e) {
                if (e.ctrlKey && e.keyCode === 13) {
                    const id = $(this).attr('id');
                    if (id && id.startsWith('remark-')) {
                        const parts = id.split('-');
                        const type = parts[1];
                        const recordId = parts[2];
                        $(`.submit-remark[data-type="${type}"][data-id="${recordId}"]`).click();
                    }
                }
            });
        });
    </script>


    <?php echo $__env->yieldContent('footerScript'); ?>
</body>

</html>
<?php /**PATH C:\Users\WORK\Laravel\Development Server\edharti_v2\resources\views/layouts/app.blade.php ENDPATH**/ ?>