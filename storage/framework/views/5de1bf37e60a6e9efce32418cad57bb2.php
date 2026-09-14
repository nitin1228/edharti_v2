<?php $__env->startSection('title', 'Application'); ?>
<?php $__env->startSection('content'); ?>
    <?php if($application->Signed_letter && getStatusDetailsById($details->status ?? '')->item_code == "APP_APR"): ?>
        <style>
            body::before {
                content: "APPROVED";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -30%) rotate(-15deg);
                font-size: 8rem;
                font-weight: 900;
                color: rgba(99, 207, 175, 0.19); /* Light gray and transparent */
                border: 5px solid rgba(30, 144, 109, 0.19);
                padding: 1rem 2rem;
                z-index: 2;
                white-space: nowrap;
                pointer-events: none; /* Let clicks pass through */
            }
            .content {
                position: relative;
                z-index: 1; /* Ensure content is above the watermark */
                padding: 2rem;
            }
        </style>
    <?php endif; ?>

    <?php if(getStatusDetailsById($details->status ?? '')->item_code == "APP_REJ"): ?>
        <style>
            body::before {
                content: "REJECTED";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -30%) rotate(-15deg);
                font-size: 8rem;
                font-weight: 900;
                color: rgba(235, 76, 76, 0.19); /* Light gray and transparent */
                border: 5px solid rgba(233, 77, 77, 0.19);
                padding: 1rem 2rem;
                z-index: 2;
                white-space: nowrap;
                pointer-events: none; /* Let clicks pass through */
            }
            .content {
                position: relative;
                z-index: 1; /* Ensure content is above the watermark */
                padding: 2rem;
            }
        </style>
    <?php endif; ?>

    <style>
        .pagination .active a {
            color: #ffffff !important;
        }
        .object_remarks{
            background: #418a8b42;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            margin-bottom: 20px;

        }

        .required-error-message {
            display: none;
        }

        .required-error-message {
            margin-left: -1.5em;
            margin-top: 3px;
        }

        .form-check-inputs[type=checkbox] {
            border-radius: .25em;
        }

        .form-check .form-check-inputs {
            float: left;
            margin-left: -1.5em;
        }

        .form-check-inputs {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0;
        }

        /** Added By Nitin */
        .duesDetails {
            display: flex;
        }

        .duesDetails span {
            flex: 1;
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
            z-index: 1000;
            /* Ensure it covers other content */
        }

        /* .spinner {
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
        /* commented and adeed by anil for replace the new loader on 08-08-2025  */

        /* for offic activity By Diwakar */
        div.dt-buttons {
            float: none !important;
            /* width: 19%; */
            width: 33%;
            /* chagned by anil on 28-08-2025 to fix in resposive */
        }

        div.dt-buttons.btn-group {
            margin-bottom: 20px;
        }

        div.dt-buttons.btn-group .btn {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .form-check .form-check-inputs {
            float: left;
            margin-left: -1.5em;
        }

        .form-check-inputs[type=checkbox] {
            border-radius: .25em;
        }

        .form-check-inputs {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0;
        }

        /* Ensure responsiveness on smaller screens */
        @media (max-width: 768px) {
            div.dt-buttons {
                width:100%;
            }

            div.dt-buttons.btn-group {
                flex-direction: column;
                align-items: flex-start;
            }

            div.dt-buttons.btn-group .btn {
                width: 100%;
                text-align: left;
            }
        }

        .text-muted {
            color: #6c757dad !important;
        }
    </style>
    <!-- dd($details, $details->documentFinal) -->
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Application</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.applications')); ?>">Applications</a></li>
                    <li class="breadcrumb-item">
                        
                             
                <?php echo e($applicationType); ?>

                        </li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    <hr>
    <div class="card content">
        <div class="card-body">
            <div>
               
                <div class="mb-3 d-flex justify-content-between align-items-center px-3">
                        <?php if(!empty($uniquePaymentId) && !empty($details->application_no)): ?>
                            <a href="<?php echo e(route('downloadPaymentReceiptPdfByAppNo', [
                            'id' => $uniquePaymentId,
                            'application_no' => $details->application_no
                        ])); ?>">

                                <button type="button" class="btn btn-success px-2 mx-2">
                                    <span id="receiptButtonText">Payment Receipt</span>
                                    <i class="bx bx-download"></i>
                                </button>
                            </a>
                        <?php endif; ?>
                        
                        <?php if($roles == 'applicant'): ?>
                            <?php if($application->Signed_letter && getStatusDetailsById($details->status ?? '')->item_code == "APP_APR"): ?>
                                <div class="d-flex justigy-content-center align-items-center">
                                    <a href="<?php echo e(asset('storage/' . $application->Signed_letter)); ?>" class="btn btn-success px-2 mx-2" target="_blank">View
                                        Signed
                                        Letter  <i class="bx bxs-file"></i></a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                         
                        <a href="<?php echo e(url('edharti/applications/' . $details->id) . '?type=' . base64_encode($details->model_name ?? $application->model_name) . '&downloading=1'); ?>">
                            <button class="btn btn-primary px-2 mx-2" title="Download Application">
                                Download Application 
                                <i class="bx bx-download"></i>
                            </button>
                        </a>
                    </div>
                       
                <div class="parent_table_container pb-3">
                    <!-- added calss tm-tiles by anil on 19-11-2025 -->
                    <table class="table report-item tm-tiles">
                        <tbody>

                            <tr>
                                <td>Application No.: <span
                                        class="highlight_value"><?php echo e($details->application_no ?? ''); ?></span></td>
                                <td>Application Type: <span class="highlight_value">
                                        <span
                                            class="ml-2 badge rounded-pill text-info bg-light-info p-2 text-uppercase px-3">
                                            <?php echo e($applicationType); ?>

                                        </span>
                                    </span></td>
                                <td>Current Status: <span class="highlight_value">
                                        <!-- spelling correction by anil on 04-03-2025 -->
                                        <?php switch(getStatusDetailsById( $details->status ?? '' )->item_code):
                                            case ('APP_REJ'): ?>
                                                <span class=" statusRejected">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                            <?php break; ?>

                                            <?php case ('APP_NEW'): ?>
                                                <span class=" statusNew">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                            <?php break; ?>

                                            <?php case ('APP_IP'): ?>
                                                <span class=" statusSecondary">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                            <?php break; ?>

                                            <?php case ('APP_OBJ'): ?>
                                                <span class=" statusObject">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                            <?php break; ?>

                                            <?php case ('APP_APR'): ?>
                                                <span class=" landtypeFreeH">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                            <?php break; ?>

                                            <?php case ('APP_HOLD'): ?>
                                                <span class="statusHold">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                            <?php break; ?>

                                            <?php default: ?>
                                                <span class="">
                                                    <?php echo e(getStatusDetailsById($details->status ?? '')->item_name); ?>

                                                </span>
                                        <?php endswitch; ?>
                                    </span></td>
                                
                            </tr>

                            <tr>
                                <td>Status of Applicant: <span
                                        class="highlight_value"><?php echo e(getServiceNameById($details->status_of_applicant ?? ($details->applicant_status ?? ''))); ?></span>
                                </td>
                                <td>
                                    Received Date:
                                    <span class="highlight_value">
                                        <?php echo e(!empty($application->created_at) ? \Carbon\Carbon::parse($application->created_at)->format('d-m-Y') : ''); ?>

                                    </span>
                                </td>
                                <td>
                                    Disposed Date:
                                    <span class="highlight_value">
                                        <?php echo e(!empty($application->disposed_at) ? \Carbon\Carbon::parse($application->disposed_at)->format('d-m-Y') : ''); ?>

                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($latestMovement['action'] == 'APP_OBJ'): ?>
            <div class="object_remarks">
                <b>Objection Remark: </b>
                <?php echo e($latestMovement['remarks']); ?>

                
            </div>
             <?php endif; ?>

             <?php if($details->flatDetails && $applicationType != 'Deed Of Apartment'): ?>
                <div class="part-title">
                    <h5>Flat Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <!-- added responsive div by anil on 19-11-2025 -->
                                <div class="table-responsive">
                                    <table class="table table-bordered property-table-info">
                                        <tbody>
                                            <tr>
                                                <th>Flat Id :</th>
                                                <td><?php echo e($details->flatDetails->unique_flat_id ?? ''); ?></td>
                                                <th>File Number :</th>
                                                <td><?php echo e($details->flatDetails->unique_file_no ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Floor :</th>
                                                <td><?php echo e($details->flatDetails->floor ?? ''); ?></td>
                                                <th>Flat Number :</th>
                                                <td><?php echo e($details->flatDetails->flat_number ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Flat Status :</th>
                                                <td><?php echo e(optional($details->flatDetails)->property_flat_status == 951 ? 'Lease Hold' : 'Free Hold'); ?>

                                                </td>
                                                <th>Flat Area in Sqm :</th>
                                                <td><?php echo e($details->flatDetails->area_in_sqm ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Purchase Date :</th>
                                                <td><?php echo e(!empty($details->flatDetails->purchase_date) ? \Carbon\Carbon::parse($details->flatDetails->purchase_date)->format('d-m-Y') : ''); ?>

                                                </td>
                                                <th>Original Buyer :</th>
                                                <td><?php echo e($details->flatDetails->original_buyer_name ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Present Occupant :</th>
                                                <td><?php echo e($details->flatDetails->present_occupant_name ?? ''); ?></td>
                                                <th>Builder / Developer Name :</th>
                                                <td><?php echo e($details->flatDetails->builder_developer_name ?? ''); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="part-title">
                <h5>Property Details</h5>
            </div>
            <div class="part-details">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <!-- added responsive div by anil on 19-11-2025 -->
                            <div class="table-responsive">
                                <table class="table table-bordered property-table-info">
                                    <tbody>
                                        <tr>
                                            <th>Old Property ID:</th>
                                            <td><?php echo e($details->old_property_id ?? ''); ?></td>
                                            <th>New Property ID:</th>
                                            <td><?php echo e($uniquePropertyId ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Lease Type:</th>
                                            <td><?php echo e($propertyCommonDetails['leaseType'] ?? ''); ?></td>
                                            <th>Lease Execution Date:</th>
                                            <td id="leaseExecutionDate"></td>
                                        </tr>
                                        <tr>
                                            <th>Property Type:</th>
                                            <td><?php echo e($propertyCommonDetails['propertyType'] ?? ''); ?></td>
                                            <th>Property Sub Type:</th>
                                            <td><?php echo e($propertyCommonDetails['propertySubType'] ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Presently Known As:</th>
                                            <td><?php echo e($propertyCommonDetails['presentlyKnownAs'] ?? ''); ?></td>
                                            <th>Original Lessee:</th>
                                            <td><?php echo e($propertyCommonDetails['inFavourOf'] ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Property Status:</th>
                                            <td colspan="3"><?php echo e($propertyCommonDetails['status'] ?? ''); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="part-title d-flex justify-content-between align-items-center">
                <h5>Name & Details of Registered Applicant</h5>
                 <?php if(auth()->user()->getRoleNames()->first() == 'CDV'): ?>
                    <div class="editApplicantDetails">
                        <button class="btn" id="editApplicantBtn">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
             <div id="applicantDetailsSection">
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <!-- added responsive div by anil on 19-11-2025 -->
                                <div class="table-responsive"> 
                                    <table class="table table-bordered property-table-info">
                                        <tbody>
                                            <tr>
                                                <th>Applicant Number:</th>
                                                <td><?php echo e($user->applicantUserDetails->applicant_number ?? ''); ?></td>
                                                <th>User Type:</th>
                                                <td class="text-uppercase"><?php echo e($user->applicantUserDetails->user_sub_type ?? ''); ?>

                                                </td>
                                                <td rowspan="6" class="text-center"><img style="width: 120px;"
                                                        src="<?php echo e($user->applicantUserDetails ? asset('storage/' . $user->applicantUserDetails->profile_photo) : ''); ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Name:</th>
                                                <td>
                                                    <?php echo e($user->name ?? ''); ?>

                                                </td>
                                                <th>Email:</th>
                                                <td>
                                                    <?php
                                                        if ($user->email) {
                                                            $useremail = $user->email;
                                                            $position = strpos($useremail, '@');
                                                            $email = $useremail
                                                                /*substr($useremail, 0, 2) .
                                                                str_repeat('*', $position - 2) .
                                                                substr($useremail, $position)*/;
                                                        } else {
                                                            $email = '';
                                                        }
                                                    ?>
                                                    <?php echo e($email); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Gender:</th>
                                                <td><?php echo e($user->applicantUserDetails->gender ?? ''); ?></td>
                                                <th><?php echo e($user->applicantUserDetails->so_do_spouse); ?>:</th>
                                                <td> <?php echo e($user->applicantUserDetails->second_name); ?></td>
                                            </tr>
                                            <?php if(!empty($user->applicantUserDetails->isIndian) && $user->applicantUserDetails->isIndian == 1): ?>
                                                <tr>
                                                    <th>PAN:</th>
                                                    <td><?php echo e(decryptString($user->applicantUserDetails->pan_card ?? '')); ?>

                                                    </td>
                                                    <th>Aadhaar:</th>
                                                    <?php
                                                        $applicantAadhaar = $user->applicantUserDetails->aadhar_card;
                                                    ?>
                                                    <td> <?php echo e(decryptString($applicantAadhaar)); ?>

                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <th>Document Type:</th>
                                                <td>
                                                    <?php if($user->applicantUserDetails->documentType && $user->applicantUserDetails->documentType === 'pion'): ?>
                                                        Person of Indian Origin Number
                                                    <?php elseif($user->applicantUserDetails->documentType && $user->applicantUserDetails->documentType === 'ocin'): ?>
                                                        Overseas Citizen of India Number
                                                    <?php else: ?>
                                                        Passport Number
                                                    <?php endif; ?>
                                                </td>
                                                <th>Document Type Number:</th>
                                                <?php
                                                    $documentTypeNumber = $user->applicantUserDetails->documentTypeNumber ?? '';
                                                ?>
                                                <td> <?php echo e(decryptString($documentTypeNumber) ?? ''); ?>

                                                </td>
                                                </tr>
                                            <?php endif; ?>

                                            <tr>
                                                <th>Mobile:</th>
                                                <td><?php echo e($user->mobile_no /*? substr($user->mobile_no, 0, 3) . str_repeat('*', 4) . substr($user->mobile_no, -3) : ''*/); ?>

                                                </td>
                                                <th>Address:</th>
                                                <td class="w-50"><?php echo e($user->applicantUserDetails->address ?? ''); ?></td>
                                            </tr>
                                            <?php if($applicationType == 'Mutation' && auth()->user()->getRoleNames()->first() !== 'applicant'): ?>
                                                <tr>
                                                    <th>Share <small class="text-danger">*</small></th>
                                                    <td colspan=3><input type="text" class="form-control" id="applicantShare"
                                                            name="applicantShare" placeholder="Enter Share Details"
                                                            value="<?php echo e($applicantShare); ?>" /></td>
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
            </div>

            <div id="editApplicantSection" style="display:none">

                <div class="part-details">
                    <div class="container-fluid">
                        <form action="<?php echo e(route('applicant.cdv.update', $user->id)); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="applicant_id" value="<?php echo e($user->id); ?>">
                            <input type="hidden" name="applicant_no" value="<?php echo e($user->applicantUserDetails->applicant_number); ?>">
                            <input type="hidden" name="application_no" value="<?php echo e($details->application_no); ?>">
                            <div class="row">

                                <div class="col-md-5 mb-3">
                                    <label>Name</label>
                                    <input type="text"
                                        name="name"
                                        class="form-control"
                                        value="<?php echo e(old('name',$user->name)); ?>">
                                </div>
                                 <div class="col-md-1 mb-3">
                                    <label>Gender</label>

                                    <select name="gender" class="form-control">
                                        <option value="Male"
                                            <?php echo e($user->applicantUserDetails->gender=='Male'?'selected':''); ?>>
                                            Male
                                        </option>

                                        <option value="Female"
                                            <?php echo e($user->applicantUserDetails->gender=='Female'?'selected':''); ?>>
                                            Female
                                        </option>
                                    </select>
                                </div>

                                 <div class="col-md-2 mb-3">
                                    <label>S/o, D/o, Spouse Of</label>

                                    <select name="relation" class="form-control">
                                        <option value="S/o"
                                            <?php echo e($user->applicantUserDetails->so_do_spouse=='S/o'?'selected':''); ?>>
                                            S/o
                                        </option>

                                        <option value="D/o"
                                            <?php echo e($user->applicantUserDetails->so_do_spouse=='D/o'?'selected':''); ?>>
                                            D/o
                                        </option>

                                        <option value="Spouse Of"
                                            <?php echo e($user->applicantUserDetails->so_do_spouse=='Spouse Of'?'selected':''); ?>>
                                            Spouse Of
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label>Second Name</label>
                                    <input type="text"
                                        name="second_name"
                                        class="form-control"
                                        value="<?php echo e(old('second_name',$user->applicantUserDetails->second_name)); ?>">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label>Address</label>

                                    <textarea name="address"
                                        class="form-control"><?php echo e(old('address',$user->applicantUserDetails->address)); ?></textarea>
                                </div>

                                <?php if($applicationType == 'Mutation' && auth()->user()->getRoleNames()->first() !== 'applicant'): ?>
                                    <div class="col-md-4 mb-3">
                                        <label>Share</label>
                                        <input type="text"
                                            name="share"
                                            class="form-control"
                                            value="<?php echo e($applicantShare); ?>">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button class="btn btn-success">
                                Save Changes
                            </button>

                            <button type="button"
                                    class="btn btn-secondary"
                                    id="cancelEditBtn">
                                Cancel
                            </button>

                        </form>
                    </div>
                </div>
            </div>




            <?php if(isset($coapplicants) && count($coapplicants) > 0): ?>
                <div class="part-title">
                    <h5>Name & Details of Co-Applicants</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <?php $__currentLoopData = $coapplicants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $coapplicant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-12 col-12 items" style="position: relative;">
                                    <div
                                        style="
                                position: absolute;
                                right: 10px;
                                padding: 2px 8px;
                                background: #126a6ba8;
                                border-radius: 50%;
                                color: #ffffff;
                            ">
                                        <?php echo e($key + 1); ?>

                                    </div>
                                    <div class="parent_table_container">
                                        <!-- added responsive div by anil on 19-11-2025 -->
                                        <div class="table-responsive"> 
                                            <table class="table table-bordered property-table-info" style="margin: 5px 0px;">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2">Name: <span
                                                                class="highlight_value"><?php echo e($coapplicant->co_applicant_name); ?></span>
                                                        </td>
                                                        <td>Gender/DOB: <span
                                                                class="highlight_value"><?php echo e($coapplicant->co_applicant_gender); ?>/
                                                                <?php echo e(\Carbon\Carbon::parse($coapplicant->co_applicant_age)->format('d-m-Y')); ?>

                                                            </span></td>
                                                        <td><?php echo e($coapplicant->prefix); ?>: <span
                                                                class="highlight_value"><?php echo e($coapplicant->co_applicant_father_name); ?></span>
                                                        </td>
                                                        <td rowspan="2" class="text-center"><img
                                                                style="width: 120px;height: 120px"
                                                                src="<?php echo e(asset('storage/' . $coapplicant->image_path ?? '')); ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Aadhaar:  <?php
                                                                    $aadhaar = $coapplicant->co_applicant_aadhar;
                                                                    $aadhaar =  preg_match('/[a-zA-Z]/', $aadhaar) ? decryptString($aadhaar) : $aadhaar;
                                                                ?>
                                                                <span
                                                                class="highlight_value"><?php echo e(/* strlen($aadhaar) > 8 ? substr($aadhaar, 0, 4). str_repeat('*',strlen($aadhaar) - 8).substr($aadhaar,-4):*/ $aadhaar); ?>

                                                                <span><a target="_blank"
                                                                    href="<?php echo e(asset('storage/' . $coapplicant->aadhaar_file_path ?? '')); ?>">
                                                                    (View)
                                                                </a></span>
                                                        </td>
                                                        <td>PAN: <span
                                                                class="highlight_value text-uppercase"><?php echo e($coapplicant->co_applicant_pan); ?></span><span><a
                                                                    target="_blank"
                                                                    href="<?php echo e(asset('storage/' . $coapplicant->pan_file_path ?? '')); ?>">
                                                                    (View)</a></span>
                                                        </td>
                                                        <td>Mobile Number: <span
                                                                class="highlight_value"><?php echo e($coapplicant->co_applicant_mobile); ?></span>
                                                        </td>
                                                        <?php if($applicationType == 'Mutation' && auth()->user()->getRoleNames()->first() !== 'applicant'): ?>
                                                        <td>Share: <small class="text-danger">*</small><input
                                                                class="form-control"
                                                                name="coapplicantShare[<?php echo e($key); ?>]" type="text"
                                                                placeholder="Enter Share Details"
                                                                value="<?php echo e($coapplicant->share ?? ''); ?>" />
                                                            <input type="hidden"
                                                                name="coapplicantShareId[<?php echo e($key); ?>]"
                                                                value="<?php echo e($coapplicant->id); ?>" />
                                                        </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Check if application type is not Deed Of Apartment then only show Details of Lease Deed - Lalit Tiwari (13/02/2025) -->
            <?php if(!empty($applicationType) && $applicationType != 'Deed Of Apartment'): ?>
                <!-- For mutation application -->
                <?php if(isset($details->name_as_per_lease_conv_deed)): ?>
                    <div class="part-title">
                        <?php if($details->property_status == 952): ?>
                            <h5>Details of Conveyance Deed</h5>
                        <?php else: ?>
                            <h5>Details of Lease Deed</h5>
                        <?php endif; ?>
                    </div>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <!-- added responsive div by anil on 19-11-2025 -->
                                    <div class="table-responsive"> 
                                        <table class="table table-bordered property-table-info">
                                            <tbody>
                                                <tr>
                                                    <th>Executed In Favour of:</th>
                                                    <td><?php echo e($details->name_as_per_lease_conv_deed ?? ''); ?></td>
                                                    <th>Executed On:</th>
                                                    <td id="executedOn"><?php echo e($details->executed_on ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Registration No.:</th>
                                                    <td><?php echo e($details->reg_no_as_per_lease_conv_deed ?? ''); ?></td>
                                                    <th>Book No.:</th>
                                                    <td><?php echo e($details->book_no_as_per_lease_conv_deed ?? ''); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Volume No.:</th>
                                                    <td><?php echo e($details->volume_no_as_per_lease_conv_deed ?? ''); ?></td>
                                                    <th>Page No.:</th>
                                                    <td><?php echo e($details->page_no_as_per_lease_conv_deed ?? $details->page_no_as_per_deed); ?>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Registration Date:</th>
                                                    <td id="regnDate"><?php echo e($details->reg_date_as_per_lease_conv_deed ?? ''); ?>

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- For conversion application -->
                <?php if(isset($details->applicant_name)): ?>
                    <div class="part-title">
                        <?php if($details->status == 952): ?>
                            <h5>Details of Conveyance Deed</h5>
                        <?php else: ?>
                            <h5>Details of Lease Deed</h5>
                        <?php endif; ?>
                    </div>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered property-table-info">
                                        <tbody>
                                            <tr>
                                                <th>Executed In Favour of:</th>
                                                <td><?php echo e($details->applicant_name ?? ''); ?></td>
                                                <th>Executed On:</th>
                                                <td id="executedOn"><?php echo e($details->executed_on ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Registration No.:</th>
                                                <td><?php echo e($details->reg_no ?? ''); ?></td>
                                                <th>Book No.:</th>
                                                <td><?php echo e($details->book_no ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Volume No.:</th>
                                                <td><?php echo e($details->volume_no ?? ''); ?></td>
                                                <th>Page No.:</th>
                                                <td><?php echo e($details->page_no ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Registration Date:</th>
                                                <td id="leaseDeedRegnDateNoc"><?php echo e($details->reg_date ?? ''); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- For noc application -->
                <?php if(isset($details->name_as_per_noc_conv_deed)): ?>
                    <div class="part-title">
                        <?php if($details->property_status == 952): ?>
                            <h5>Details of Conveyance Deed</h5>
                        <?php else: ?>
                            <h5>Details of Lease Deed</h5>
                        <?php endif; ?>
                    </div>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered property-table-info">
                                        <tbody>
                                            <tr>
                                                <th>Executed In Favour of:</th>
                                                <td><?php echo e($details->name_as_per_noc_conv_deed ?? ''); ?></td>
                                                <th>Executed On:</th>
                                                <td id="executedOnNoc">
                                                    <?php echo e($details->executed_on_as_per_noc_conv_deed ?? ''); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Registration No.:</th>
                                                <td><?php echo e($details->reg_no_as_per_noc_conv_deed ?? ''); ?></td>
                                                <th>Book No.:</th>
                                                <td><?php echo e($details->book_no_as_per_noc_conv_deed ?? ''); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Volume No.:</th>
                                                <td><?php echo e($details->volume_no_as_per_noc_conv_deed ?? ''); ?></td>
                                                <th>Page No.:</th>
                                                <td><?php echo e($details->page_no_as_per_noc_conv_deed ?? ''); ?>

                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Registration Date:</th>
                                                <td id="regnDateNoc"><?php echo e($details->reg_date_as_per_noc_conv_deed ?? ''); ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


            <?php if(isset($details->property_stands_mortgaged)): ?>
                <?php if($details->property_stands_mortgaged == 1 || $details->is_basis_of_court_order == 1): ?>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered property-table-info">
                                        <tbody>
                                            <tr>
                                                <?php if($details->property_stands_mortgaged == 1): ?>
                                                    <th>Mortgaged Remark:</th>
                                                    <td><?php echo e($details->mortgaged_remark ?? ''); ?></td>
                                                <?php endif; ?>
                                                <?php if($details->is_basis_of_court_order == 1): ?>
                                                    <th>Court Case No.:</th>
                                                    <td><?php echo e($details->court_case_no ?? ''); ?></td>
                                                    <th>Court Case Details:</th>
                                                    <td><?php echo e($details->court_case_details ?? ''); ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


            <!-- for conversion application -->
             <!-- commented this code by anil not in use sugested by sourav on 24-04-2025 -->
            <!-- <?php if(isset($details->is_mortgaged)): ?>
                <?php if($details->is_mortgaged == 1 || $details->is_Lease_deed_lost == 1): ?>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered property-table-info">
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?> -->

            <!-- for LUC application -->
            <?php if(isset($details->property_type_change_to)): ?>
                <div class="part-title">
                    <h5>Application Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <table class="table table-bordered property-table-info">
                                    <tbody>
                                        <tr>
                                            <td colspan="<?php echo e($details->mixed_use == 1 ? 6 : 2); ?>"> Application to change
                                                use
                                                of property from
                                                <b><?php echo e($propertyCommonDetails['propertyType']); ?>

                                                    (<?php echo e($propertyCommonDetails['propertySubType']); ?>)</b> to
                                                <b><?php echo e(getServiceNameById($details->property_type_change_to) ?? ''); ?>

                                                    (<?php echo e(getServiceNameById($details->property_subtype_change_to) ?? ''); ?>)</b>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <th>Mixed Use</th>
                                            <td><?php echo e($details->mixed_use == 1 ? 'Yes' : 'No'); ?></td>
                                            <?php if($details->mixed_use == 1): ?>
                                                <th>Total built up area</th>
                                                <td><?php echo e($details->total_built_up_area . ' Sq. Mtr.'); ?></td>
                                                <th>Area sought as commercial</th>
                                                <td><?php echo e($details->commercial_area . ' Sq. Mtr.'); ?></td>
                                            <?php endif; ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($applicationType == 'Deed Of Apartment'): ?>
                <!-- for DOA pplication -->

                <div class="part-title">
                    <h5>Flat Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <table class="table table-bordered property-table-info">
                                    <tbody>
                                        <tr>
                                            <th>Name:</th>
                                            <td><?php echo e($details->applicant_name ?? ''); ?></td>
                                            <th>Communication Address:</th>
                                            <td><?php echo e($details->applicant_address ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Building Name:</th>
                                            <td><?php echo e($details->building_name ?? ''); ?></td>
                                            <th>Locality:</th>
                                            <td><?php echo e($details->locality ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Block:</th>
                                            <td><?php echo e($details->block ?? ''); ?></td>
                                            <th>Plot No.:</th>
                                            <td><?php echo e($details->plot ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Presently Known As:</th>
                                            <td><?php echo e($details->known_as ?? ''); ?></td>
                                            <th>Flat No.:</th>
                                            <td><?php echo e($details->flat_number ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Is Flat not listed:</th>
                                            <td><?php echo e($details->flat_id ? 'True' : 'False'); ?></td>
                                            <th>Name of Builder / Developer:</th>
                                            <td><?php echo e($details->builder_developer_name ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Name Of Original Buyer:</th>
                                            <td><?php echo e($details->original_buyer_name ?? ''); ?></td>
                                            <th>Name Of Present Occupant:</th>
                                            <td><?php echo e($details->present_occupant_name ?? ''); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Purchased From:</th>
                                            <td><?php echo e($details->purchased_from ?? ''); ?></td>
                                            <th>Date of Purchase:</th>
                                            <td><?php echo e(!empty($details->purchased_date) ? \Carbon\Carbon::parse($details->purchased_date)->format('d-m-Y') : ''); ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Flat Area In (Sq. Mtr.) :</th>
                                            <td><?php echo e($details->flat_area ?? ''); ?></td>
                                            <th>Plot Area In (Sq. Mtr.) :</th>
                                            <td><?php echo e($details->plot_area ?? ''); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
           

             <?php
             if(isset($applicationAppointmentLink['schedule_date'])){
                    $currentDate = new \DateTime();
                    $scheduleDate = new \DateTime($applicationAppointmentLink['schedule_date']);
                    $endDate = clone $scheduleDate;
                    $endDate->modify('+15 days');
                }

            ?> 

            <!-- For Property Document Details Section- SOURAV CHAUHAN (12/Dec/2024)-->
            <?php echo $__env->make('application.admin.application_document.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php
                // $serviceType = $details->serviceType;
                $serviceType = $actionServiceType;
            ?>
            <?php if($roles != 'applicant'): ?>
                <!-- For Office activity section- SOURAV CHAUHAN (12/Dec/2024) -->
                <?php echo $__env->make('application.admin.office_activity.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Scanned Files Modal -->
    <div class="modal fade" id="viewScannedFiles" data-backdrop="static" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewScannedFilesLabel">View Scanned Files</h5>
                    <?php if(!empty($scannedFiles['files'])): ?>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            <?php if(
                            ($roles === 'section-officer' || $roles === 'assistant-section-officer') &&
                                auth()->user()->can('can.approve.application.mis.scanned.document')): ?> onclick='checkScannedFiles()' <?php endif; ?>></button>
                    <?php else: ?>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <?php endif; ?>
                </div>
                <div class="modal-body">
                    <?php if(!empty($scannedFiles['files'])): ?>
                        <ul class="files-link">
                            <?php $__currentLoopData = $scannedFiles['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scannedFile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                               
                                         <li><a href="<?php echo e($scannedFiles['baseUrl']); ?><?php echo e($scannedFile['document_path']); ?>"
                                        target="_blank"><?php echo e($scannedFile['document_name']); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-danger fs-4">No scanned files available.</p>
                    <?php endif; ?>

                     <!-- added on 06-11-2025 for scanned files -->
                    <?php if(auth()->user()->getRoleNames()->first() === 'section-officer'): ?>
                        <div class="btn-group">
                            <a target="_blank" href="<?php echo e(route('property.scanning.create', ['property_id' => $oldPropertyId])); ?>"
                            class="btn btn-success ml-2">
                                + Upload Scanned Files
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer justify-content-end">
                        <?php if(!empty($scannedFiles['files']) && count($scannedFiles['files']) > 0 && !empty($propertyMasterId)): ?>
                            <a class="btn btn-primary me-2"
                            href="<?php echo e(route('property.scanning.downloadZipByMasterSplit', [$propertyMasterId, $splited_primary_id ?? null])); ?>">
                                Download All (ZIP)
                            </a>
                        <?php endif; ?>
                         <?php if(!empty($scannedFiles['files'])): ?>
                           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        <?php if(
                            ($roles === 'section-officer' || $roles === 'assistant-section-officer') &&
                                auth()->user()->can('can.approve.application.mis.scanned.document')): ?> onclick='checkScannedFiles()' <?php endif; ?>>Close</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- object action confirmation modal -->
    <div class="modal fade" id="objectConfirmation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title">Are You Sure?</h5> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- <div class="modal-body">
                                                                                                                                                                                                                Please give remark.
                                                                                                                                                                                                            </div> -->
                <div class="modal-body input-class-reject">
                    <label for="rejection">Remarks</label>
                    <textarea id="objectRemarkTextarea" name="remarks" class="form-control" placeholder="Remarks" rows="5"></textarea>
                    <div class="error-label text-danger mt-2" style="display:none; margin-left:0px;">Please enter
                        remark.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="objectConfirmButton" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="serviceType" name="serviceType" value="<?php echo e(getServiceCodeById($serviceType)); ?>">
    <input type="hidden" id="modalId" name="modalId" value="<?php echo e($details->id); ?>">
    <input type="hidden" id="applicantNo" name="applicantNo" value="<?php echo e($details->application_no); ?>">
    <!-- End Modal -->
    <?php echo $__env->make('include.loader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('include.alerts.ajax-alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
     

    <!-- commented and adeed by anil for replace the new loader on 08-08-2025  -->
    <!-- <div id="spinnerOverlay" style="display:none;">
        <div class="spinner"></div>
        <img src="<?php echo e(asset('assets/images/chatbot_icongif.gif')); ?>">
    </div> -->
    <div id="spinnerOverlay" style="display:none;">
        <span class="loader"></span>
        <h1 style="color: white;font-size: 20px; margin-top:10px;">Loading... Please wait</h1>
    </div>
    <!-- commented and adeed by anil for replace the new loader on 08-08-2025  -->

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Please enter remark.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo e(route('applications.checklist')); ?>" id="checklistRemarkForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" name="applicationNo" id="applicationNo"
                            value="<?php echo e($details->application_no); ?>">
                        <input type="hidden" name="type" id="type" value="0">
                        <textarea name="checklistRemark" class="form-control" placeholder="Remarks" rows="3" required=""
                            spellcheck="false"></textarea>
                        <div class="error-label text-danger mt-2" style="display:none; margin-left:0px;">Please enter
                            remark.
                        </div>
                        <input type="hidden" name="documentId" id="documentId" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitchecklistRemarkForm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="remarkScrollableModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="remarkInModal"></p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footerScript'); ?>
    <script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js')); ?>"></script>
    <script>

        $(function () {

            $("#editApplicantBtn").click(function () {
                $("#applicantDetailsSection").hide();
                $("#editApplicantSection").show();
            });

            $("#cancelEditBtn").click(function () {
                $("#editApplicantSection").hide();
                $("#applicantDetailsSection").show();
            });

        });


        function handleApplicationAction(action, applicationNo, button, remark = null) {

            var needToconfirm = ((action == 'RECOMMENDED' || action == 'OBJECT' || action == 'HOLD' || action == 'REJECT_APP') && remark == null);
            if (!needToconfirm) {
                let allChecked = true;
                $('.required-for-approve').each(function() {
                    const $checkbox = $(this);
                    const $errorMsg = $checkbox.siblings('.required-error-message');
                    if (!$checkbox.is(':checked')) {
                        $errorMsg.show();
                        allChecked = false;
                    } else {
                        $errorMsg.hide();
                    }
                });

                if (allChecked) {
                     const applicationType = <?php echo json_encode($applicationType, 15, 512) ?>;
                   // For Mutation Application, validate documents AND shares
                    if (applicationType === 'Mutation') {

                        if (!validateApplicants()) {
                            $('html, body').animate({
                                scrollTop: $('.is-invalid').first().offset().top - 100
                            }, 500);

                            return;
                        }


                        // Then validate share fields for Mutation only
                        const sharesValid = validateAndCollectShareFields();
                        if (!sharesValid.isValid) {
                            // Scroll to first error
                            $('html, body').animate({
                                scrollTop: $('.is-invalid').first().offset().top - 100
                            }, 500);
                            return;
                        }

                        // Validate documents first
                        if (!validateAllDocumentsWithMessage()) {
                            return; // Stop if document validation fails
                        }

                        // Store the share data for later use
                        window.shareData = sharesValid.data;
                    } else {
                        // For non-Mutation applications, clear any share data
                        window.shareData = null;
                    }
                    $('#actionConfirmationModal').modal('show');
                    $('#actionConfirmationButton').off('click').on('click', function() {
                        $('#actionConfirmationModal').modal('hide');
                        storeApplicationAction(action, applicationNo, button, remark)
                    })

                }

            } else {
                let allChecked = true;
                if (action != 'OBJECT') {
                $('.required-for-approve').each(function() {
                    const $checkbox = $(this);
                    const $errorMsg = $checkbox.siblings('.required-error-message');
                    if (!$checkbox.is(':checked')) {
                        $errorMsg.show();
                        allChecked = false;
                    } else {
                        $errorMsg.hide();
                    }
                });
                }

                if (allChecked) {

                    // for getting the latest object remark
                    $.ajax({
                        url: "<?php echo e(route('applications.object.remark')); ?>",
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            applicationNo: applicationNo,
                        },
                        success: function(response) {
                            if (response.status === true) {
                                $('#objectRemarkTextarea').val(response.data.remark)
                            }
                        },

                    });
                    $('#objectConfirmation').modal('show');
                    $('#objectConfirmButton').off('click').on('click', function() {
                        var confirmButton = $('#objectConfirmButton');
                        confirmButton.prop('disabled', true);
                        confirmButton.html('Submitting...');
                        var remark = $('#objectRemarkTextarea').val();
                        if (!remark) {
                            $('.error-label').css('display', 'block');
                            $('.error-label').html('Remark is requied');
                            confirmButton.prop('disabled', false);
                            confirmButton.html('Confirm');
                        } else if (remark.length >= 50) {
                            $('.error-label').css('display', 'none');
                            $('#objectConfirmation').modal('hide');
                            storeApplicationAction(action, applicationNo, button, remark)
                        } else {
                            $('.error-label').css('display', 'block');
                            $('.error-label').html('Atleast 50 characters are required.');
                            confirmButton.prop('disabled', false);
                            confirmButton.html('Confirm');
                        }
                    })
                }
            }
        }


        function storeApplicationAction(action, applicationNo, button, remark) {
            let allChecked = true;
            if (action != 'OBJECT') {
            $('.required-for-approve').each(function() {
                const $checkbox = $(this);
                const $errorMsg = $checkbox.siblings('.required-error-message');
                if (!$checkbox.is(':checked')) {
                    $errorMsg.show();
                    allChecked = false;
                } else {
                    $errorMsg.hide();
                }
            });
            }

            if (allChecked) {
                /** code modified by Nitin as we need to disable everything once user click a button  - 13 Dec 2024*/
                const spinnerOverlay = document.getElementById('spinnerOverlay');
                const actionButtonContainer = document.getElementById('action-btn-container');
                if (spinnerOverlay) {
                    spinnerOverlay.style.display = 'flex';
                }
                actionButtonContainer.style.display = 'none';

                var buttonOriginalText = $(button)
                    .html(); //Added by NItin - In case of error, we need to restore original text of Button - 12 Dec 2024
                $(button).prop('disabled', true);
                $(button).html('Submitting...');
                // Collect document data
                const documents = collectDocumentData();
                const applicants = collectApplicants();

                // Only include documents if at least one document has data
                const hasDocuments = documents.length > 0;
                //Check Property link with other applicant.
                $.ajax({
                    url: "<?php echo e(route('applications.action')); ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        action: action,
                        applicationNo: applicationNo,
                        modelName: '<?php echo e($application->model_name); ?>',
                        remark: remark,
                        ...(window.shareData?.applicantShare !== undefined && {
                            applicantShare: window.shareData.applicantShare
                        }),
                        ...(window.shareData?.coapplicantShares !== undefined && {
                            coapplicantShares: window.shareData.coapplicantShares
                        }),
                        ...(window.shareData?.coapplicantShareIds !== undefined && {
                            coapplicantShareIds: window.shareData.coapplicantShareIds
                        }),
                         // Add documents to the request if they exist
                        ...(hasDocuments && {
                            documentNameAndDate: documents
                        }),
                        ...(applicants.length > 0 && {
                            applicants: applicants
                        })
                    },
                    success: function(response) {
                        if (response.status === true) {
                            // spinnerOverlay.style.display = 'none';
                            showSuccess(response.message, window.location.href)
                        } else {
                            $('#checkProperty').modal('show');
                            spinnerOverlay.style.display = 'none';
                            actionButtonContainer.style.display = 'block';
                            $(button).prop('disabled', false);
                            $(button).html(buttonOriginalText);
                            showError(response.message)
                        }
                    },

                });
            }
        }

         function collectApplicants() {

            const applicants = [];

            $('.applicant-item').each(function () {

                applicants.push({
                    name: $(this).find('.applicant-name').val(),
                    relation: $(this).find('.applicant-relation').val(),
                    age: $(this).find('.applicant-age').val(),
                    gender: $(this).find('.applicant-gender').val(),
                    share: $(this).find('.applicant-share').val()
                });

            });

            return applicants;
        }

        $(document).ready(function() {
            //Reject the application
            $('#rejectButton').click(function() {
                var isDocumentCorrect = $('#isDocumentCorrect');
                if (isDocumentCorrect.is(':checked')) {
                    $('#isDocumentCorrectError').hide();
                    var value1 = 0;
                    var input1 = $('#isMISCorrect');
                    if (input1.is(':checked')) {
                        value1 = 1;
                    }
                    var value2 = 0;
                    var input2 = $('#isScanningCorrect');
                    if (input2.is(':checked')) {
                        value2 = 1;
                    }
                    var value3 = 0;
                    var input3 = $('#isDocumentCorrect');
                    if (input3.is(':checked')) {
                        value3 = 1;
                    }
                    // Dynamically create input elements and append to the modal
                    $('#modalInputs').html(`
                    <input type="hidden" id="input1" name="is_mis_checked" value="${value1}">
                    <input type="hidden" id="input2" name="is_scan_file_checked" value="${value2}">
                    <input type="hidden" id="input3" name="is_uploaded_doc_checked" value="${value3}">
                    <br>
                    `);
                    $('#rejectUserStatus').modal('show');
                } else {
                    $('#isDocumentCorrectError').show();
                }
            })
            // $('#approveBtn').click(function() {

            // let allChecked = true;
            // $('.required-for-approve').each(function() {
            //     const $checkbox = $(this);
            //     // console.log($checkbox);
            //     const $errorMsg = $checkbox.siblings('.required-error-message');
            //     // console.log($errorMsg);
            //     if (!$checkbox.is(':checked')) {
            //         console.log('inside if');
            //         $errorMsg.show();
            //         allChecked = false;
            //     } else {
            //         console.log('inside else');
            //         $errorMsg.hide();
            //     }
            // });
            //     if (allChecked) {
            //         console.log("inside if");
            //         //Check Property link with other applicant.
            //         var button = $('#approveBtn');
            //         var error = $('#isPropertyFree');
            //         button.prop('disabled', true);
            //         button.html('Submitting...');
            //         var propertyId = $('#SuggestedPropertyID').val();
            //         $.ajax({
            //             url: "<?php echo e(route('applications.action', ['id' => '__propertyId__'])); ?>"
            //                 .replace('__propertyId__', propertyId),
            //             type: "POST",
            //             dataType: "JSON",
            //             data: {
            //                 _token: '<?php echo e(csrf_token()); ?>',
            //             },
            //             success: function(response) {
            //                 //console.log(response);
            //                 if (response.success === true) {
            //                     $('#approvePropertyModal').modal('show');
            //                 } else {
            //                     $('#checkProperty').modal('show');
            //                     error.html(response.details)
            //                     button.prop('disabled', false);
            //                     button.html('Approve');
            //                 }
            //             },
            //             error: function(response) {
            //                 console.log(response);
            //             }
            //         });
            //     }
            // });
            $('#confirmApproveSubmit').on('click', function(e) {
                e.preventDefault();
                $('#approveBtn').prop('disabled', true);
                $('#approveBtn').html('Submitting...');
                $('#confirmApproveSubmit').prop('disabled', true);
                $('#confirmApproveSubmit').html('Submitting...');
                $('#approvalForm').submit();
            });
            $('#closeApproveModelButton').on('click', function(e) {
                e.preventDefault();
                e.preventDefault();
                $('#approveBtn').prop('disabled', false);
                $('#approveBtn').html('Approve');
            });
            $('#rejectBtn').on('click', function(e) {
                e.preventDefault();
                $('#rejectModal').modal('show');
                $('#rejectModal').modal('show');
            });
        });
        $(document).ready(function() {
            $('#isUnderReview').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#modelReview').modal('show');
                }
            });
            // Optionally, you can handle the closing of the modal
            $('#modelReview').on('hidden.bs.modal', function() {
                // Do something after the modal is hidden, like unchecking the checkbox if needed
                $('#isUnderReview').prop('checked', false);
            });
        });
        //confirmation for scanned files checked - Sourav Chauhan - 9/sep/2024
        $(document).ready(function() {
            $('#isScanningCorrect').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).prop('checked', false);
                    $('#isScanningCheckedError').text(
                        'Please check Scanned Files, Click on View Scanned Files button.').show();
                } else {
                    $('#isScanningCheckedError').hide();
                    $(this).prop('checked', false);
                }
            });
        });



        // Event delegation for dynamically added elements by lalit on 01/08-2024 for remarks validation
        $(document).on('click', '.confirm-reject-btn', function(event) {
            event.preventDefault();

            var form = $('#rejectUserStatusForm');
            var remarksInput = form.find('textarea[name="remarks"]');
            var remarksValue = remarksInput.val().trim();
            var errorLabel = form.find('.error-label');
            var url = "<?php echo e(route('update.registration.status', ['id' => $details->id])); ?>";

            if (remarksValue === '') {
                // Show the error label if remarks are empty
                errorLabel.show();
            } else {
                // Hide the error label and submit the form via AJAX
                errorLabel.hide();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(), // Serialize the form data
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#rejectUserStatus').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showSuccess(response.message);
                        } else {
                            $('#rejectUserStatus').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showError(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error (show error message or take appropriate action)
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        });

        $(document).on('click', '.confirm-user-review-btn', function(event) {
            event.preventDefault();

            var form = $('#reviewUserRegistrationForm');
            var remarksInput = form.find('textarea[name="remarks"]');
            var remarksValue = remarksInput.val().trim();
            var errorLabel = form.find('.error-label');
            var url = "<?php echo e(route('review.user.registration', ['id' => $details->id])); ?>";

            if (remarksValue === '') {
                // Show the error label if remarks are empty
                errorLabel.show();
            } else {
                // Hide the error label and submit the form via AJAX
                errorLabel.hide();

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(), // Serialize the form data
                    success: function(response) {
                        if (response.status == 'success') {
                            // Handle success response
                            $('#modelReview').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showSuccess(response.message);
                            // Ensure checkbox is checked and disabled after success
                            setTimeout(function() {
                                $('#isUnderReview').prop('checked', true).prop(
                                    'disabled', true);
                            }, 500); // Slight delay to ensure modal is fully hidden
                        } else {
                            // Handle success response
                            $('#modelReview').modal('hide');
                            $('.loader_container').addClass('d-none');
                            if ($('.results').hasClass('d-none'))
                                $('.results').removeClass('d-none');
                            showError(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error (show error message or take appropriate action)
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var remarksTextarea = document.getElementById('remarks');
            if (remarksTextarea) {
                var $reviewBtnNew = $('#reviewBtnNew');
                // Event listener for keypress event
                remarksTextarea.addEventListener('keypress', function(event) {
                    // You can perform actions here based on the key pressed
                    $reviewBtnNew.prop('disabled', false); // Enable button
                });
            }
        });
        $(document).ready(function() {
            var $errorMsg = $('#errorMsgNew');
            $('#reviewBtnNew').click(function() {
                var allChecked = true;
                var remark = $('#remarks').val();
                if (remark.trim() === '') {
                    $errorMsg.show();
                    allChecked = false;
                } else {
                    $errorMsg.hide();
                    allChecked = true;
                }
                if (allChecked) {
                    $('#reviewBtnNew').prop('disabled', true);
                    $('#reviewBtnNew').html('Submitting...');
                    $('#approvalForm').submit();
                }
            });
        });
        $(document).ready(function() {
            // When the checkbox with name "is_uploaded_doc_checked" is checked or unchecked
            $('input[name="is_uploaded_doc_checked"]').change(function() {
                // Check if any checkbox with class "property-document-approval-chk" is unchecked
                if ($(this).prop('checked') && $('.property-document-approval-chk:not(:checked)').length >
                    0) {
                    // Show error message
                    $('#isDocumentCorrectError').text('Please check the uploaded documents.').show();
                    $(this).prop('checked', false); // Uncheck the checkbox
                } else {
                    // Hide error message
                    $('#isDocumentCorrectError').hide();
                }
            });
            // When any checkbox with the class "property-document-approval-chk" is checked or unchecked
            $('.property-document-approval-chk').change(function() {
                // Check if all checkboxes with class "property-document-approval-chk" are checked
                var allChecked = $('.property-document-approval-chk').length === $(
                    '.property-document-approval-chk:checked').length;
                // Check or uncheck the checkbox with name "is_uploaded_doc_checked"

                // Hide error message if all checkboxes are checked
                if (allChecked) {
                    var serviceType = $('#serviceType').val();
                    var modalId = $('#modalId').val();
                    var applicantNo = $('#applicantNo').val();
                    $.ajax({
                        url: "<?php echo e(route('uploadedDocsChecked')); ?>",
                        type: "POST",
                        data: {
                            serviceType: serviceType,
                            modalId: modalId,
                            applicantNo: applicantNo,
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                $('input[name="is_uploaded_doc_checked"]').prop('checked',
                                    allChecked);
                                $('#isDocumentCorrect').prop('disabled', true)
                                $('.property-document-approval-chk').prop('disabled', true)
                                $('#isDocumentCorrectError').hide();
                            } else {
                                $('.property-document-approval-chk').prop('checked', false);
                                $('#isDocumentCorrectError').show();
                                $('#isDocumentCorrectError').html('Some issue in saving');

                            }
                        }
                    });
                }
            });
        });
        $(document).ready(function() {
            $('#isMISCorrect').on('change', function() {
                if ($(this).is(':checked')) {
                    $(this).prop('checked', false);
                    $('#misCheckedError').text('Please check MIS, Click on Go to Details button.').show();
                } else {
                    $('#misCheckedError').hide();
                    $(this).prop('checked', false);
                }
            });
        });

        // Comment Given below code for checking validation for upload signed letter by Lalit - 20/02/2025
        /*function handleLetterUpload() {
            const uploadButton = document.getElementById('uploadButton');
            uploadButton.innerHTML = 'Uploading...';
            const form = document.getElementById('signedLetterForm');
            form.submit();
        }*/

        function handleLetterUpload() {
            const fileInput = document.getElementById('signedLetter');
            const errorMessage = document.getElementById('signedLetterError');
            const uploadButton = document.getElementById('uploadButton');
            if (!fileInput.files.length) {
                document.getElementById('signedLetterError').innerHTML = "Please Upload a signed letter."
                errorMessage.style.display = 'block'; // Show error message
                return;
            } else {
                errorMessage.style.display = 'none'; // Hide error if file is selected
            }
            uploadButton.innerHTML = 'Uploading...';
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            const form = document.getElementById('signedLetterForm');
            form.submit();
        }

        $(document).ready(function() {
            $('#forwardToDepartment').on('change', function() {
                console.log('Called');

                if ($(this).is(':checked')) {
                    $('#divForwardAppDept').show();
                } else {
                    $('#divForwardAppDept').hide();
                }
            });

            $('#forwardAppForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission


                $('#forwardAppBtn').prop('disabled', true);
                $('#forwardAppBtn').html('Submitting...');

                // Clear previous error messages
                $('#forwardToError').hide();
                $('#forwardRemarkError').hide();

                // Validate form inputs
                const forwardTo = $('#forwardTo').val();
                const forwardRemark = $('#forwardRemark').val().trim();

                let isValid = true;

                if (!forwardTo) {
                    $('#forwardToError').show();
                    isValid = false;
                    $('#forwardAppBtn').prop('disabled', false);
                    $('#forwardAppBtn').html('Submit');
                }

                if (!forwardRemark) {
                    $('#forwardRemarkError').show();
                    isValid = false;
                    $('#forwardAppBtn').prop('disabled', false);
                    $('#forwardAppBtn').html('Submit');
                }

                // If validation fails, stop form submission
                if (!isValid) {
                    return;
                }

                /** code modified by Nitin as we need to disable everything once user click a button  - 13 Dec 2024*/
                const spinnerOverlay = document.getElementById('spinnerOverlay');
                const actionButtonContainer = document.getElementById('action-btn-container');
                if (spinnerOverlay) {
                    spinnerOverlay.style.display = 'flex';
                }
                actionButtonContainer.style.display = 'none';
                // Collect form data and additional variables
                const formData = $(this).serialize() +
                    `&serviceType=${encodeURIComponent($('#serviceType').val())}` +
                    `&modalId=${encodeURIComponent($('#modalId').val())}` +
                    `&applicantNo=${encodeURIComponent($('#applicantNo').val())}`;

                // Set up CSRF token for AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Perform AJAX request
                $.ajax({
                    url: "<?php echo e(route('forwardApplicationToDepartment')); ?>", // Your form action URL
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#forwardAppForm')[0].reset(); // Reset the form
                            $('#divForwardAppDept').hide(); // Hide the div

                            showSuccess(response.message);

                            setTimeout(function() {
                                location
                                    .reload(); // Reload the page after successful form submission
                            }, 500); // Slight delay to ensure modal is fully hidden
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        spinnerOverlay.style.display = 'none';
                        actionButtonContainer.style.display = 'block';
                        // Handle error response
                        if (xhr.status === 422) { // Laravel validation error
                            const errors = xhr.responseJSON.errors;
                            if (errors.forwardTo) {
                                $('#forwardToError').text(errors.forwardTo[0]).show();
                            }
                            if (errors.forwardRemark) {
                                $('#forwardRemarkError').text(errors.forwardRemark[0]).show();
                            }
                        } else {
                            alert('An error occurred: ' + xhr.responseText);
                        }
                    }
                });
            });

            $('#revertAppForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                $('#revertAppBtn').prop('disabled', true).text('Submitting...');
                $('#revertRemarkError').hide(); // Clear previous error messages

                const revertRemark = $('#revertRemark').val().trim();
                const serviceType = $('#serviceType').val();
                const modalId = $('#modalId').val();
                const applicantNo = $('#applicantNo').val();

                if (!revertRemark) {
                    $('#revertRemarkError').show();
                    $('#revertAppBtn').prop('disabled', false).text('Submit');
                    return;
                }

                // Collect form data
                const revertFormData = {
                    revertRemark: revertRemark,
                    serviceType: serviceType,
                    modalId: modalId,
                    applicantNo: applicantNo,
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
                };

                // Perform AJAX request
                $.ajax({
                    url: "<?php echo e(route('revertApplicationToAssignee')); ?>",
                    method: "POST",
                    data: revertFormData,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#revertAppForm')[0].reset(); // Reset form
                            $('#revertModal').modal('hide'); // Hide modal
                            showSuccess(response.message); // Display success notification
                            setTimeout(function() {
                                location.reload(); // Reload page
                            }, 500);
                        } else {
                            showError(response.message); // Display error notification
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) { // Laravel validation error
                            const errors = xhr.responseJSON.errors;
                            if (errors.revertRemark) {
                                $('#revertRemarkError').text(errors.revertRemark[0]).show();
                            }
                        } else {
                            alert('An error occurred: ' + xhr.responseText);
                        }
                    },
                    complete: function() {
                        $('#revertAppBtn').prop('disabled', false).text('Submit');
                    },
                });
            });
        });

        //         document.querySelectorAll('.doc-check-no').forEach(function(radioButton) {
        //         radioButton.addEventListener('change', function() {
        //             if (this.checked) {
        //             // Get the value of the clicked radio button
        //             var radioValue = this.value;
        //             $('#documentId').val(radioValue);
        //             $('#exampleModal').modal('show');
        //         }
        //         });
        //     });

        //     $('#exampleModal').on('hidden.bs.modal', function () {
        //     // Uncheck the radio button
        //     document.querySelector('input[type="radio"][id="doc-check-no"]').checked = false;
        // });


        document.querySelectorAll('.doc-check-no').forEach(function(radioButton) {
            radioButton.addEventListener('change', function() {
                if (this.checked) {
                    // Get the value of the clicked radio button
                    var radioValue = this.value;

                    // Set the value of the radio button into the modal
                    $('#documentId').val(radioValue);

                    // Store the reference of the clicked radio button
                    var selectedRadioButton = this;

                    // Show the modal
                    $('#exampleModal').modal('show');

                    // When the modal is closed, uncheck the selected radio button
                    $('#exampleModal').on('hidden.bs.modal', function() {
                        selectedRadioButton.checked = false; // Uncheck the radio button
                    });
                }
            });
        });


        document.querySelectorAll('.doc-check-yes').forEach(function(radioButton) {
            radioButton.addEventListener('change', function() {
                if (this.checked) {
                    const spinnerOverlay = document.getElementById('spinnerOverlay');
                    if (spinnerOverlay) {
                        spinnerOverlay.style.display = 'flex';
                    }

                    // Get the value of the clicked radio button
                    var radioValue = this.value;
                    var applicationNo = $('#applicationNo').val();
                    let tableRow = document.getElementById(radioValue)



                    // Call AJAX function to save data to the database
                    $.ajax({
                        url: "<?php echo e(route('applications.checklist')); ?>", // Your Laravel route for saving the data
                        method: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            documentId: radioValue,
                            applicationNo: applicationNo,
                            type: '1'
                        },
                        success: function(response) {
                            // Use .closest() to find the nearest ancestor with the class .required-doc
                            let requiredDocDiv = tableRow.querySelector("td .notCorrectRemark");
                            if (requiredDocDiv) {
                                requiredDocDiv.style.display = "none"; // Hide the div
                            }
                            spinnerOverlay.style.display = 'none';
                            showSuccess('Data saved successfully');
                        },
                        error: function(xhr, status, error) {
                            spinnerOverlay.style.display = 'none';
                            showError('There was an error saving the data');
                            console.log(error);
                        }
                    });
                }
            });
        });




        $(document).ready(function() {
            $("#submitchecklistRemarkForm").click(function() {
                var remark = $('textarea[name="checklistRemark"]').val()
                if (remark == '') {
                    $('.error-label').show()
                } else {
                    $('#submitchecklistRemarkForm').prop('disabled', false).text('Submiting...');
                    $('.error-label').hide()
                    $("#checklistRemarkForm").submit();
                }
            });
        });




        function checkAllYesSelected() {
            var allYesSelected = true; // To track if all "Yes" radio buttons are selected
            var allRadioButtonsSelected = true; // To track if all radio button groups have a selection

            // Check if all "Yes" radio buttons are selected
            document.querySelectorAll('.doc-check-yes').forEach(function(radioButton) {
                // If any "Yes" radio button is not checked, disable the first button
                if (!radioButton.checked) {
                    allYesSelected = false;
                }
            });

            // Check if every radio button group has a selection (either Yes or No)
            document.querySelectorAll('.doc-check-yes, .doc-check-no').forEach(function(radioButton) {
                // If any radio button in a group is not selected, disable the second button
                var name = radioButton.name;
                var groupSelected = document.querySelector(`input[name="${name}"]:checked`);
                if (!groupSelected) {
                    allRadioButtonsSelected = false;
                }
            });

            // Enable or disable the 'sendProofReadingLink' button based on all "Yes" selections
            let sendProofReadingLink = document.getElementById('sendProofReadingLink')
            if (sendProofReadingLink) {
                sendProofReadingLink.disabled = !allYesSelected;
            }

            // Enable or disable the second button when all radio buttons (Yes or No) are selected
            let objectButton = document.getElementById('objectButton')
            if (objectButton) {
                objectButton.disabled = !allRadioButtonsSelected;
            }
        }

        // Event listeners to trigger the check when a radio button changes
        document.querySelectorAll('.doc-check-yes, .doc-check-no').forEach(function(radioButton) {
            radioButton.addEventListener('change', function() {
                checkAllYesSelected(); // Check the radio buttons each time one is clicked
            });
        });

        // Call on page load to check the initial state of the radio buttons
        window.onload = function() {
            checkAllYesSelected();
        };



        function getRemark(documentId) {
            var remark = $('#fullRemark_' + documentId).val()
            console.log(remark);

            $('.remarkInModal').html(remark)
            $('#remarkScrollableModal').modal('show');
        }



        $(document).ready(function() {
            const fileUploadBox = $('.file-upload-box');
            const fileList = $('.file-list');
            const fileInput = $('.file-upload-input');

            // Handle drag and drop events
            fileUploadBox
                .on('dragover dragenter', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('drag-over');
                })
                .on('dragleave dragend drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('drag-over');
                });

            // Handle file selection
            fileInput.on('change', function(e) {
                const files = e.target.files;

                var filesSize = files[0].size;
                var maxSize = 5 * 1024 * 1024;

                var fileType = files[0].type;
                if(fileType !== 'application/pdf') {
                    fileList.empty()
                    $('#signedLetter').val('');
                    $('#signedLetterError').html('Only PDF files are allowed. Please select a PDF file.')
                    $('#signedLetterError').css('display', 'flex');
                } else if (filesSize > maxSize) {
                    fileList.empty()
                    $('#signedLetter').val('');
                    $('#signedLetterError').html('File size exceeds 5 MB. Please select a smaller file.')
                    $('#signedLetterError').css('display', 'flex');
                } else {
                    $('#signedLetterError').html('')
                    handleFiles(files);
                }

            });

            // Handle dropped files
            fileUploadBox.on('drop', function(e) {
                const files = e.originalEvent.dataTransfer.files;
                handleFiles(files);
            });

            function handleFiles(files) {
                fileList.empty()
                Array.from(files).forEach(file => {
                    // Create progress bar element
                    const progressBar = $('<div class="upload-progress"></div>');

                    const fileItem = $(`
                        <div class="file-item">
                            <i class="fas fa-file file-icon"></i>
                            <span class="file-name" title="${file.name}">${file.name}</span>
                            <i class="fas fa-times remove-file"></i>
                            ${progressBar.prop('outerHTML')}
                        </div>
                    `);

                    fileList.append(fileItem);

                    // Remove progress bar after animation
                    setTimeout(() => {
                        fileItem.find('.upload-progress').remove();
                    }, 1000);

                    // Handle file removal
                    fileItem.find('.remove-file').on('click', function(e) {
                        e.stopPropagation();
                        $('#signedLetter').val('');
                        fileItem.fadeOut(300, function() {
                            $(this).remove();
                        });
                    });

                    // Get appropriate FontAwesome icon based on file type
                    const fileIcon = fileItem.find('.file-icon');
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    const iconMap = {
                        'pdf': 'fa-file-pdf',
                        'jpg': 'fa-file-image',
                        'jpeg': 'fa-file-image',
                        'png': 'fa-file-image',
                    };

                    if (iconMap[fileExtension]) {
                        fileIcon.removeClass('fa-file').addClass(iconMap[fileExtension]);
                    }
                });
            }
            // Forward To Department

            // $('#forwardToDepartment').on('change', function() {
            //     if ($(this).is(':checked')) {
            //         $('#divForwardAppDept').show();
            //     } else {
            //         $('#divForwardAppDept').hide();
            //     }
            // });
            // End
        });

        /** funciton added by Nitin to automatically check scanned files */

        function checkScannedFiles() {
            $('#isScanningCorrect').prop('checked', true);
            $('#isScanningCorrect').prop('disabled', true); //user can not uncheck once checked
            saveScannedFileChecked(); // call the function to store value of scanned files
        }

        /** onclick function modified by nitin */

        // confirm and aproove Scanned files checked by section - Sourav Chauhan (09/sep/2024)
        function saveScannedFileChecked() {
            $('#confirmScannFileChecked').prop('disabled', true).html('Submitting...');
            // Serialize form data
            //let formData = $('#scannFileCheckedForm').serialize();
            // Send AJAX request
            $.ajax({
                url: "<?php echo e(route('scannedFilesChecked')); ?>", // Your form action URL
                type: 'POST',
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>",
                    'serviceType': "<?php echo e(getServiceCodeById($serviceType)); ?>",
                    'modalId': "<?php echo e($details->id); ?>",
                    'applicationNo': "<?php echo e($details->application_no); ?>",
                }, //formData,
                success: function(response) {
                    if (response.status == 'success') {
                        // Handle success response
                        $('#ModelScannFile').modal('hide');
                        $('.loader_container').addClass('d-none');
                        if ($('.results').hasClass('d-none'))
                            $('.results').removeClass('d-none');
                        showSuccess(response.message);
                        // Ensure checkbox is checked and disabled after success
                        setTimeout(function() {
                            $('#isScanningCorrect').prop('checked', true).prop(
                                'disabled', true);
                            $('#isScanningCheckedError').text('').hide();
                        }, 500); // Slight delay to ensure modal is fully hidden
                    } else {
                        // Handle success response
                        $('#ModelScannFile').modal('hide');
                        $('.loader_container').addClass('d-none');
                        if ($('.results').hasClass('d-none'))
                            $('.results').removeClass('d-none');
                        showError(response.message);
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
        };
    </script>


    <script>
        //for uploading files by CDV - SOURAV CHAUHAN (12/Dec/2024)
        function handleFIleUploadForCdv(file, id) {
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            const baseUrl = "<?php echo e(asset('storage')); ?>";

            const formData = new FormData();
            formData.append('file', file); // Append the file to the FormData object
            formData.append('id', id); // Append the document id
            formData.append('_token', '<?php echo e(csrf_token()); ?>'); // Append the CSRF token

            $.ajax({
                url: "<?php echo e(route('uploadFileforCdv')); ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status) {
                        var anchorTag = $('a[data-document-type="' + name + '"]');
                        if (anchorTag.length > 0) {
                            const newPath = baseUrl + '/' + response.path;
                            anchorTag.attr('href', newPath);
                        }
                        spinnerOverlay.style.display = 'none';
                        showSuccess('File Uploaded Successfully')
                    }
                },
                error: function(response) {
                    spinnerOverlay.style.display = 'none'
                    showError('File Not Uploaded')
                }
            });
        }

        if (document.getElementById("leaseExecutionDate")) {
            document.getElementById("leaseExecutionDate").innerText = formatDateToDDMMYYYY(
                "<?php echo e($propertyCommonDetails['leaseExectionDate'] ?? ''); ?>");
        }
        if (document.getElementById("executedOn")) {
            document.getElementById("executedOn").innerText = formatDateToDDMMYYYY("<?php echo e($details->executed_on ?? ''); ?>");
        }
        if (document.getElementById("regnDate")) {
            document.getElementById("regnDate").innerText = formatDateToDDMMYYYY(
                "<?php echo e($details->reg_date_as_per_lease_conv_deed ?? ''); ?>");
        }
        if (document.getElementById("warningMailSentDate")) {
            document.getElementById("warningMailSentDate").innerText = formatDateToDDMMYYYY(
                "<?php echo e($application->warning_sent_on ?? ''); ?>");
        }

        if (document.getElementById("executedOnNoc")) {
            document.getElementById("executedOnNoc").innerText = formatDateToDDMMYYYY(
                "<?php echo e($details->executed_on_as_per_noc_conv_deed ?? ''); ?>");
        }
        if (document.getElementById("regnDateNoc")) {
            document.getElementById("regnDateNoc").innerText = formatDateToDDMMYYYY(
                "<?php echo e($details->reg_date_as_per_noc_conv_deed ?? ''); ?>");
        }
        if (document.getElementById("leaseDeedRegnDateNoc")) {
            document.getElementById("leaseDeedRegnDateNoc").innerText = formatDateToDDMMYYYY(
                "<?php echo e($details->reg_date ?? ''); ?>");
        }
        if (document.getElementById("proofReadValidDate")) {
            document.getElementById("proofReadValidDate").innerText = formatDateToDDMMYYYY(
                "<?php echo e($applicationAppointmentLink['valid_till'] ?? ''); ?>");
        }
        if (document.getElementById("proofReadScheduleDate")) {
            document.getElementById("proofReadScheduleDate").innerText = formatDateToDDMMYYYY(
                "<?php echo e($applicationAppointmentLink['schedule_date'] ?? ''); ?>");
        }


        //for downloading the payment  - SOURAV CHAUHAN (07 March 2025)
        const originalButtonText = $('#receiptButtonText').text();
        // Comment Given below Code - Lalit Tiwari (25/03/2025) To make it Download PDF Automatically
        /*$('#downloadPayRec').on('click', function(e) {
            $('#receiptButtonText').text('Downloading...');
            const applicationNo = <?php echo json_encode($details->application_no, 15, 512) ?>;
            $.ajax({
                url: "<?php echo e(route('createPaymentReceipt')); ?>",
                type: "POST",
                dataType: "JSON",
                data: {
                    applicationNo: applicationNo,
                    _token: '<?php echo e(csrf_token()); ?>',
                },
                success: function(response) {
                    // Handle the successful response here
                    console.log('Success:', response);


                    // Reset the button text to the original text
                    $('#receiptButtonText').text(originalButtonText);
                },
                error: function(xhr, status, error) {
                    // Handle any errors that occur during the AJAX request
                    console.error('AJAX error:', error);

                    // Reset the button text to the original text
                    $('#receiptButtonText').text(originalButtonText);
                }
            });
        });*/
        // Change Above code to make it Download PDF Automatically - Lalit Tiwari (25/03/2025) 
        $('#downloadPayRec').on('click', function(e) {
            e.preventDefault();

            $('#receiptButtonText').text('Downloading...');
            const applicationNo = <?php echo json_encode($details->application_no, 15, 512) ?>;

            // Create a hidden form to send the request as a traditional form submission
            let form = $('<form>', {
                action: "<?php echo e(route('createPaymentReceipt')); ?>",
                method: 'POST',
                target: '_blank' // Opens in a new tab for automatic download
            }).append($('<input>', {
                type: 'hidden',
                name: 'applicationNo',
                value: applicationNo
            })).append($('<input>', {
                type: 'hidden',
                name: '_token',
                value: '<?php echo e(csrf_token()); ?>'
            }));

            // Append form to body and submit
            $('body').append(form);
            form.submit();
            form.remove();

            $('#receiptButtonText').text('Download Receipt'); // Reset button text
        });

         // Function to collect document data from the form
        function collectDocumentData() {
            const documents = [];

            // Loop through all document inputs (0 to 3 as you have 4 rows)
            for (let i = 0; i < 4; i++) {
                const nameInput = document.querySelector(`input[name="documents[${i}][name]"]`);
                const dateInput = document.querySelector(`input[name="documents[${i}][date]"]`);

                // Get values
                const name = nameInput ? nameInput.value.trim() : '';
                const date = dateInput ? dateInput.value : '';

                // Only add to array if BOTH fields have values
                if (name && date) {
                    documents.push({
                        name: name,
                        date: date
                    });
                }
            }

            return documents;
        }

        // Main validation function - VALIDATES ALL 4 ROWS
        function validateDocuments() {
            let isValid = true;
            let hasCompleteDocument = false;

            // Clear all previous error messages and styling
            document.querySelectorAll('.document-error').forEach(error => {
                error.textContent = '';
                error.style.display = 'none';
            });

            document.querySelectorAll('.document-name, .document-date').forEach(input => {
                input.classList.remove('is-invalid', 'border-danger');
            });

            // Validate each row (0 to 3) - ALL 4 ROWS
            for (let i = 0; i < 4; i++) {
                const nameInput = document.querySelector(`input[name="documents[${i}][name]"]`);
                const dateInput = document.querySelector(`input[name="documents[${i}][date]"]`);

                // Find the parent row for error elements
                const row = nameInput ? nameInput.closest('.document-row') : null;

                // Find error divs
                const nameError = row ? row.querySelector('[data-error="name"]') : null;
                const dateError = row ? row.querySelector('[data-error="date"]') : null;

                if (!nameInput || !dateInput) {
                    console.warn(`Inputs not found for row ${i}`);
                    continue;
                }

                const nameValue = nameInput.value.trim();
                const dateValue = dateInput.value;

                // Check if this row has any data
                const hasName = nameValue !== '';
                const hasDate = dateValue !== '';

                // ALWAYS validate each row, even if empty
                // Validate name field
                if (!hasName) {
                    if (nameError) {
                        nameError.textContent = 'Document name is required';
                        nameError.style.display = 'block';
                    }
                    nameInput.classList.add('is-invalid', 'border-danger');
                    isValid = false;
                } else if (nameValue.length < 3) {
                    if (nameError) {
                        nameError.textContent = 'Document name must be at least 3 characters';
                        nameError.style.display = 'block';
                    }
                    nameInput.classList.add('is-invalid', 'border-danger');
                    isValid = false;
                }

                // Validate date field
                if (!hasDate) {
                    if (dateError) {
                        dateError.textContent = 'Document date is required';
                        dateError.style.display = 'block';
                    }
                    dateInput.classList.add('is-invalid', 'border-danger');
                    isValid = false;
                } else {
                    const selectedDate = new Date(dateValue);
                    const today = new Date();
                    today.setHours(23, 59, 59, 999); // End of today

                    if (selectedDate > today) {
                        if (dateError) {
                            dateError.textContent = 'Date cannot be in the future';
                            dateError.style.display = 'block';
                        }
                        dateInput.classList.add('is-invalid', 'border-danger');
                        isValid = false;
                    }
                }

                // Track if this row is complete
                if (hasName && hasDate) {
                    hasCompleteDocument = true;
                }
            }

            return isValid;
        }

        // Alternative: Validation that requires ALL 4 documents to be filled
        function validateAllDocumentsRequired() {
            let isValid = true;
            let allRowsComplete = true;

            // Clear all previous error messages and styling
            document.querySelectorAll('.document-error').forEach(error => {
                error.textContent = '';
                error.style.display = 'none';
            });

            document.querySelectorAll('.document-name, .document-date').forEach(input => {
                input.classList.remove('is-invalid', 'border-danger');
            });

            // Validate each row (0 to 3)
            for (let i = 0; i < 4; i++) {
                const nameInput = document.querySelector(`input[name="documents[${i}][name]"]`);
                const dateInput = document.querySelector(`input[name="documents[${i}][date]"]`);

                const row = nameInput ? nameInput.closest('.document-row') : null;
                const nameError = row ? row.querySelector('[data-error="name"]') : null;
                const dateError = row ? row.querySelector('[data-error="date"]') : null;

                if (!nameInput || !dateInput) continue;

                const nameValue = nameInput.value.trim();
                const dateValue = dateInput.value;

                let rowHasError = false;

                // Validate name field
                if (!nameValue) {
                    if (nameError) {
                        nameError.textContent = 'Document name is required';
                        nameError.style.display = 'block';
                    }
                    nameInput.classList.add('is-invalid', 'border-danger');
                    rowHasError = true;
                    allRowsComplete = false;
                } else if (nameValue.length < 3) {
                    if (nameError) {
                        nameError.textContent = 'Document name must be at least 3 characters';
                        nameError.style.display = 'block';
                    }
                    nameInput.classList.add('is-invalid', 'border-danger');
                    rowHasError = true;
                }

                // Validate date field
                if (!dateValue) {
                    if (dateError) {
                        dateError.textContent = 'Document date is required';
                        dateError.style.display = 'block';
                    }
                    dateInput.classList.add('is-invalid', 'border-danger');
                    rowHasError = true;
                    allRowsComplete = false;
                } else {
                    const selectedDate = new Date(dateValue);
                    const today = new Date();
                    today.setHours(23, 59, 59, 999);

                    if (selectedDate > today) {
                        if (dateError) {
                            dateError.textContent = 'Date cannot be in the future';
                            dateError.style.display = 'block';
                        }
                        dateInput.classList.add('is-invalid', 'border-danger');
                        rowHasError = true;
                    }
                }

                if (rowHasError) {
                    isValid = false;
                }
            }

            if (!allRowsComplete) {
                isValid = false;
            }

            return isValid;
        }

        // Function to validate all documents and show message
        function validateAllDocumentsWithMessage() {
            if (!validateDocuments()) {
                // Count how many rows have errors
                const errorInputs = document.querySelectorAll('.is-invalid');
                const errorRows = new Set();

                errorInputs.forEach(input => {
                    const row = input.closest('.document-row');
                    if (row) {
                        errorRows.add(row.getAttribute('data-row'));
                    }
                });

                if (errorRows.size > 0) {
                    const errorMessage =
                        `Please fill in all required document fields. ${errorRows.size} row(s) have errors.`;
                    // alert(errorMessage);
                    return false;
                } else {
                    // alert('Please fix the document errors before proceeding.');
                    return false;
                }

                // Scroll to first error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstError.focus();
                }

                return false;
            }

            return true;
        }



         function validateApplicants() {
            let isValid = true;

            // Remove previous validation
            $('.applicant-item .form-control').removeClass('is-invalid');

            $('.applicant-item').each(function () {

                const name = $(this).find('.applicant-name');
                const age = $(this).find('.applicant-age');
                const share = $(this).find('.applicant-share');

                if ($.trim(name.val()) === '') {
                    name.addClass('is-invalid');
                    isValid = false;
                }

               if (parseInt(age.val()) <= 0) {
                    age.addClass('is-invalid');
                    isValid = false;
                }

                if ($.trim(share.val()) === '') {
                    share.addClass('is-invalid');
                    isValid = false;
                }
            });

            return isValid;
        }

        // Real-time validation that shows errors on all fields
        function setupRealTimeValidation() {
            // Validate document names as user types
            document.addEventListener('input', function(e) {
                const target = e.target;

                // Check if it's a document name input
                if (target.name && target.name.match(/documents\[\d+\]\[name\]/)) {
                    const nameInput = target;
                    const row = nameInput.closest('.document-row');
                    const nameError = row ? row.querySelector('[data-error="name"]') : null;
                    const nameValue = nameInput.value.trim();

                    // Clear previous error
                    if (nameError) {
                        nameError.textContent = '';
                        nameError.style.display = 'none';
                    }
                    nameInput.classList.remove('is-invalid', 'border-danger');

                    // Validate field
                    if (!nameValue) {
                        if (nameError) {
                            nameError.textContent = 'Document name is required';
                            nameError.style.display = 'block';
                        }
                        nameInput.classList.add('is-invalid', 'border-danger');
                    } else if (nameValue.length < 3) {
                        if (nameError) {
                            nameError.textContent = 'Minimum 3 characters required';
                            nameError.style.display = 'block';
                        }
                        nameInput.classList.add('is-invalid', 'border-danger');
                    }
                }
            });

            // Validate document dates when changed
            document.addEventListener('change', function(e) {
                const target = e.target;

                // Check if it's a document date input
                if (target.name && target.name.match(/documents\[\d+\]\[date\]/)) {
                    const dateInput = target;
                    const row = dateInput.closest('.document-row');
                    const dateError = row ? row.querySelector('[data-error="date"]') : null;
                    const dateValue = dateInput.value;

                    // Clear previous error
                    if (dateError) {
                        dateError.textContent = '';
                        dateError.style.display = 'none';
                    }
                    dateInput.classList.remove('is-invalid', 'border-danger');

                    // Validate field
                    if (!dateValue) {
                        if (dateError) {
                            dateError.textContent = 'Document date is required';
                            dateError.style.display = 'block';
                        }
                        dateInput.classList.add('is-invalid', 'border-danger');
                    } else {
                        const selectedDate = new Date(dateValue);
                        const today = new Date();
                        today.setHours(23, 59, 59, 999);

                        if (selectedDate > today) {
                            if (dateError) {
                                dateError.textContent = 'Date cannot be in the future';
                                dateError.style.display = 'block';
                            }
                            dateInput.classList.add('is-invalid', 'border-danger');
                        }
                    }
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            setupRealTimeValidation();
        });

        // Function to validate share fields and collect data (FOR MUTATION ONLY)
        function validateAndCollectShareFields() {
            let isValid = true;
            const shareData = {
                applicantShare: '',
                coapplicantShares: {},
                coapplicantShareIds: {} // New: Store the IDs
            };

            // Clear previous errors
            $('.share-error').remove();
            $('.is-invalid').removeClass('is-invalid');

            // Validate and collect applicant share
            const applicantShare = $('#applicantShare').val();
            if (!applicantShare || applicantShare.trim() === '') {
                $('#applicantShare').addClass('is-invalid');
                $('#applicantShare').after('<div class="invalid-feedback share-error">Please enter applicant share</div>');
                isValid = false;
            } else {
                shareData.applicantShare = applicantShare.trim();
            }

            // Validate and collect co-applicant shares AND their IDs
            $('input[name^="coapplicantShare"]').each(function(index) {
                const shareInput = $(this);
                const shareValue = shareInput.val();

                // Find the corresponding ID input
                const inputName = shareInput.attr('name');
                const match = inputName.match(/coapplicantShare\[(\d+)\]/);

                if (match) {
                    const idx = match[1];

                    // Get the ID from the hidden field
                    const idInput = $(`input[name="coapplicantShareId[${idx}]"]`);
                    const coapplicantId = idInput.length > 0 ? idInput.val() : null;

                    // Validate share value
                    if (!shareValue || shareValue.trim() === '') {
                        shareInput.addClass('is-invalid');
                        shareInput.after(
                            `<div class="invalid-feedback share-error">Please enter share for co-applicant ${parseInt(idx) + 1}</div>`
                        );
                        isValid = false;
                    } else {
                        // Store both share value and ID
                        shareData.coapplicantShares[idx] = shareValue.trim();
                        if (coapplicantId) {
                            shareData.coapplicantShareIds[idx] = coapplicantId;
                        }
                    }
                }
            });

            return {
                isValid: isValid,
                data: shareData
            };
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/view.blade.php ENDPATH**/ ?>