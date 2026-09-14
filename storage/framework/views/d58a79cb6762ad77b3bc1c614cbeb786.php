<div class="mt-3">
    <div class="container-fluid g-0"> <!-- add g-0 class for alignment by anil on 11-02-2025 -->
        <!-- row end -->
        <div class="row g-2">
            <div class="col-lg-12">
                <div class="part-title mb-2">
                    <h5>Name & Details of Registered Applicant</h5>
                    <!-- add name in title same as in mutation by anil on 11-02-2025 -->
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convname" class="form-label">Name</label>
                    <input type="text" name="convname" class="form-control alpha-only" id="convname"
                        placeholder="Name" readonly>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convgender" class="form-label">Gender</label>
                    <select class="form-select" name="convgender" id="convgender" disabled>
                        <option value="">Gender</option>
                        <option value="Male" selected>Male</option>
                        <option value="Female">Female</option>
                        <option value="Transgender">Transgender</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="dateOfBirth" class="form-label">Date of Birth<span class="text-danger"></span></label>
                    <div class="mix-field dob-wrap">
                        <input type="date" id="conDateOfBirth" name="dateOfBirth" max="<?php echo e(date('Y-m-d')); ?>"
                            class="form-control" readonly />
                        <div class="age-box">
                            <h4>Age (in years): </h4>
                            <input type="text" id="conAge" name="age" class="form-control" placeholder="0"
                                readonly />
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convfathername" class="form-label">S/o, D/o, Spouse Of</label>
                    <!-- changed title as same mutation by anil on 11-02-2025 -->
                    <div class="input-group mb-3"> <span style="border-radius: 0;background-color: #dddddd70;"
                            class="input-group-text" id="convprefixApp"></span>
                        <!-- add inline css by anil on 11-02-2025 -->
                        <input type="text" name="fathername" class="form-control alpha-only" id="convfathername"
                            placeholder="Name" readonly>
                    </div>
                </div>
            </div>


            <input type="hidden" id="isApplicantIndian" name="isApplicantIndian"
                value="<?php echo e($userDetails->applicantUserDetails->isIndian ?? ''); ?>">
            <?php if(!empty($userDetails->applicantUserDetails->isIndian) && $userDetails->applicantUserDetails->isIndian == 1): ?>
                <div class="col-lg-4">
                <div class="form-group">
                    <label for="convaadhar" class="form-label">Aadhaar Number</label>
                    <input type="text" name="convaadhar" class="form-control numericOnly" id="convaadhar"
                        maxlength="12" placeholder="Aadhaar Number" readonly>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convpan" class="form-label">PAN Number</label>
                    <input type="text" name="convpan" class="form-control pan_number_format text-transform-uppercase"
                        id="convpan" maxlength="10" placeholder="PAN Number" readonly>
                </div>
            </div>
            <?php else: ?>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="convdocType" class="form-label">Document Type</label>
                        <input type="text" name="convdocType" class="form-control numericOnly" id="convdocType"
                            maxlength="12" placeholder="Document Type" readonly>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label for="convdocTypeNumber" class="form-label">Document Type Number</label>
                        <input type="text" name="convdocTypeNumber" class="form-control text-transform-uppercase"
                            id="convdocTypeNumber" maxlength="10" placeholder="PAN Number" readonly>
                    </div>
                </div>
            <?php endif; ?>


            
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convmobilenumber" class="form-label">Mobile Number</label>
                    <input type="text" name="convmobilenumber" class="form-control numericOnly" id="convmobilenumber"
                        maxlength="10" placeholder="Mobile Number" readonly>
                </div>
            </div>
        </div>
        <!-- row end -->

        <div class="row g-2">
            <div class="col-lg-12">
                <div id="CONrepeater" class="position-relative conversion-coapplicants">
                    <div class="part-title mb-2">
                        <h5>Name & Details of Co-Applicants</h5>
                        <!-- add name in title same as in mutation by anil on 11-02-2025 -->
                    </div>
                    <div class="position-sticky text-end mt-2"
                        style="top: 70px; margin-right: 10px; margin-bottom: 10px; z-index: 9;">
                        <!-- <label>Add Co-Applicant</label> -->
                        <button type="button" class="btn btn-primary repeater-add-btn fullwidthbtn"
                            data-toggle="tooltip" data-placement="bottom"
                            title="Click here to add more co-applicant."><i class="bx bx-plus me-0"></i> Add
                            More</button>
                    </div>

                    <!-- Repeater Items -->
                    <div class="duplicate-field-tab">
                        <?php
                            $tempCoapplicant = isset($tempCoapplicant) ? $tempCoapplicant : [];
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $tempCoapplicant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coapplicant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="items coapplicant-block" data-group="convcoapplicant" data-type='coapplicant'>
                                <!-- add coapplicant-block class by anil on 28-03-2025  -->
                                <!-- Repeater Content -->
                                <input type="hidden" data-name="coapplicantId" value="<?php echo e($coapplicant->id); ?>">
                                <input type="hidden" data-name="indexNo"
                                    value="<?php echo e($coapplicant->index_no ?? $loop->iteration); ?>"><!-- if index-no is not preset (for old data). then assign loop count-->
                                <div class="item-content mb-2">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="namergapp" class="form-label">Name</label>
                                                <input type="text" name="convcoapplicant"
                                                    class="form-control alpha-only" placeholder="Name"
                                                    id="convcoapplicant" data-name="name"
                                                    value="<?php echo e($coapplicant->co_applicant_name); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="gender" class="form-label">Gender</label>
                                                <select class="form-select" name="gender" id="gender"
                                                    data-name="gender">
                                                    <option value="">Select</option>
                                                    <option value="Male"
                                                        <?php echo e($coapplicant->co_applicant_gender == 'Male' ? 'selected' : ''); ?>>
                                                        Male</option>
                                                    <option value="Female"
                                                        <?php echo e($coapplicant->co_applicant_gender == 'Female' ? 'selected' : ''); ?>>
                                                        Female</option>
                                                    <option value="Transgender"
                                                        <?php echo e($coapplicant->co_applicant_gender == 'Transgender' ? 'selected' : ''); ?>>
                                                        Transgender</option>
                                                </select>
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="dateOfBirth" class="quesLabel form-label">Date of Birth
                                                    <span class="text-danger"></span></label>
                                                <!-- ui chagnes by anil on 01-04-2025 for relation input merge with error -->
                                                <div class="row merge-inputs">
                                                    <div class="col-lg-6">
                                                        <!-- remove max-date attribute form DOB input by anil on 6-02-2025 -->
                                                        <input type="date" id="dateOfBirth" name="dateOfBirth"
                                                            value="<?php echo e($coapplicant->co_applicant_age); ?>"
                                                            data-name="dateOfBirth" class="form-control">
                                                        <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                        <span class="error-message text-danger"></span>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="age-box">
                                                            <h4>Age (in years): </h4>
                                                            <input type="text" id="age" name="age"
                                                                value="" data-name="age" class="form-control"
                                                                placeholder="0" readonly="">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <!-- <div class="form-group">
                                            <label for="age" class="form-label">Age</label>
                                            <input type="text" name="age"
                                                class="form-control numericOnly" id="age"
                                                maxlength="2" placeholder="Age" data-name="age" value="<?php echo e($coapplicant->co_applicant_age); ?>">
                                        </div> -->
                                        </div>
                                        <!-- <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="fathername" class="form-label">Father's
                                                name</label>
                                            <input type="text" name="fathername"
                                                class="form-control alpha-only" id="fathername"
                                                placeholder="Father's Name"
                                                data-name="fathername" value="<?php echo e($coapplicant->co_applicant_father_name); ?>">
                                        </div>
                                    </div> -->

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <!-- added form-label class on lable by anil on 27-03-2025 -->
                                                <label for="IndSecondName" class="quesLabel form-label">S/o, D/o,
                                                    Spouse Of</label>
                                                <!-- changed title as same mutation by anil on 11-02-2025 -->
                                                <!-- ui chagnes by anil on 08-04-2025 for relation input merge with error -->
                                                <div class="row merge-inputs">
                                                    <div class="col-lg-6">
                                                        <select name="conPrefixInv" data-name="conPrefixInv"
                                                            id="prefix" class="form-select prefix">
                                                            <option value="S/o"
                                                                <?php echo e($coapplicant->prefix == 'S/o' ? 'selected' : ''); ?>>
                                                                S/o
                                                            </option>
                                                            <option value="D/o"
                                                                <?php echo e($coapplicant->prefix == 'D/o' ? 'selected' : ''); ?>>
                                                                D/o
                                                            </option>
                                                            <option value="Spouse Of"
                                                                <?php echo e($coapplicant->prefix == 'Spouse Of' ? 'selected' : ''); ?>>
                                                                Spouse Of</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <input type="text" name="fathername"
                                                            value="<?php echo e($coapplicant->co_applicant_father_name); ?>"
                                                            data-name="fathername" id="IndSecondName"
                                                            class="form-control alpha-only" placeholder="Relation">
                                                        <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                        <span class="error-message text-danger"></span>
                                                    </div>
                                                </div>
                                                <!-- <div id="IndSecondNameError" class="text-danger text-left"></div> -->
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="aadhar" class="form-label">Aadhaar Number</label>
                                                <input type="text" name="aadhar" class="form-control numericOnly"
                                                    id="aadhar" maxlength="12" placeholder="Aadhaar Number"
                                                    data-name="aadharnumber"
                                                    value="<?php echo e(decryptString($coapplicant->co_applicant_aadhar)); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="photo" class="form-label">Upload Aadhaar</label>
                                                <input type="file" name="convcoapplicant[0][aadhaarFile]"
                                                    class="form-control" accept=".pdf" data-name="aadhaarFile"
                                                    data-should-validate = "<?php echo e(isset($coapplicant) && $coapplicant->aadhaar_file_path != ''); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                                <?php if($coapplicant->aadhaar_file_path != ''): ?>
                                                    <a href="<?php echo e(asset('storage/' . $coapplicant->aadhaar_file_path)); ?>"
                                                        target="_blank" class="fs-6">View Uploaded Aadhaar</a>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="pan" class="form-label">PAN Number</label>
                                                <input type="text" name="pan"
                                                    class="form-control pan_number_format text-transform-uppercase"
                                                    id="pan" maxlength="10" placeholder="PAN Number"
                                                    data-name="pannumber"
                                                    value="<?php echo e(decryptString($coapplicant->co_applicant_pan)); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="photo" class="form-label">Upload PAN</label>
                                                <input type="file" name="convcoapplicant[0][panFile]"
                                                    class="form-control" accept=".pdf" data-name="panFile"
                                                    data-should-validate = "<?php echo e(isset($coapplicant) && $coapplicant->pan_file_path != ''); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                                <?php if(isset($coapplicant->pan_file_path)): ?>
                                                    <a href="<?php echo e(asset('storage/' . $coapplicant->pan_file_path ?? '')); ?>"
                                                        target="_blank" class="fs-6">View Uploaded PAN</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="mobilenumber" class="form-label">Mobile
                                                    Number</label>
                                                <input type="text" name="mobilenumber"
                                                    class="form-control numericOnly" maxlength="10" id="mobilenumber"
                                                    placeholder="Mobile Number" data-name="mobilenumber"
                                                    value="<?php echo e($coapplicant->co_applicant_mobile); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 d-flex justify-content-between">
                                            <!-- add justify-content-between class by anil on 28-03-2025  -->
                                            <div class="form-group form-box">
                                                <label for="photo" class="form-label">Upload Passport Size
                                                    Photo</label>
                                                <input type="file" name="convcoapplicantphoto[0][photo]"
                                                    class="form-control" accept=".jpg, .png, .jpeg" data-name="photo"
                                                    data-should-validate = "<?php echo e(isset($coapplicant) && $coapplicant->image_path != ''); ?>">
                                                <!-- add error span tag by anil on 02-04-2025 for show error in draft view-->
                                                <span class="error-message text-danger"></span>
                                            </div>
                                            <div class="preview_img">
                                                <input type="hidden" data-name="preview_img"
                                                    class="preview_img_hidden" name="coapplicant[0][preview_img]">
                                                <?php if(isset($coapplicant) && $coapplicant->image_path): ?>
                                                    <img class="preview" alt="Photo Preview"
                                                        src="<?php echo e(asset('storage/' . $coapplicant->image_path)); ?>"
                                                        style="display: block;" />
                                                <?php else: ?>
                                                    <img class="preview" alt="Photo Preview" />
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- Repeater Remove Btn -->
                                <div class="repeater-remove-btn">
                                    <button type="button" class="btn btn-danger remove-btn px-4"
                                        data-toggle="tooltip" data-placement="bottom"
                                        title="Click here to delete this co-applicant."
                                        onclick="removeRepeater($(this).parents('.items'),'<?php echo e($coapplicant->index_no ?? $loop->iteration); ?>')">
                                        <i class="fadeIn animated bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="items coapplicant-block" data-group="convcoapplicant"
                                data-type='coapplicant'> <!-- add new class coapplicant-block by anil on 10-02-2025 -->
                                <input type="hidden" data-name="indexNo" value="1">
                                <!-- Repeater Content -->
                                <input type="hidden" name="coapplicantId" value="0">
                                <div class="item-content mb-2">
                                    <div class="row">
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="namergapp" class="form-label">Name</label>
                                                <input type="text" name="convcoapplicant"
                                                    class="form-control alpha-only" placeholder="Name"
                                                    id="convcoapplicant" data-name="name">
                                                <span class="error-message text-danger"></span>
                                                <!-- add span tag by anil on 10-02-2025 -->
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="gender" class="form-label">Gender</label>
                                                <select class="form-select" name="gender" id="gender"
                                                    data-name="gender">
                                                    <option value="">Select</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Transgender">Transgender</option>
                                                </select>
                                                <span class="error-message text-danger"></span>
                                                <!-- add span tag by anil on 10-02-2025 -->
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <!-- <div class="form-group">
                                            <label for="age" class="form-label">Age</label>
                                            <input type="text" name="age"
                                                class="form-control numericOnly" id="age"
                                                maxlength="2" placeholder="Age" data-name="age">
                                        </div> -->


                                            <!-- <div class="form-group">
                                            <label for="dateOfBirth" class="quesLabel">Date of Birth<span class="text-danger">*</span></label>
                                            <div class="mix-field">
                                                <input type="date" id="dateOfBirth" name="dateOfBirth" data-name="dateOfBirth" max="2006-12-26" class="form-control">
                                                <div class="age-box">
                                                    <h4>Age: </h4>
                                                    <input type="text" id="age" name="age"  value="" data-name="age" class="form-control" placeholder="0" readonly="">
                                                </div>
                                            </div>
                                        </div> -->
                                            <!-- commented by anil and using new html code for this date of birth on 10-02-2025 -->

                                            <div class="form-group">
                                                <!-- added class form-label by anil on 17-04-2025 -->
                                                <label for="dateOfBirth" class="quesLabel form-label">Date of Birth
                                                    <span class="text-danger"></span>
                                                </label>
                                                <div class="row merge-inputs">
                                                    <!-- ui chagnes by anil on 11-02-2025 for relation input merge with error -->
                                                    <div class="col-lg-6">
                                                        <input type="date" id="dateOfBirth" name="dateOfBirth"
                                                            data-name="dateOfBirth" class="form-control">
                                                        <span class="error-message text-danger"></span>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="age-box">
                                                            <h4>Age (in years): </h4>
                                                            <input type="text" id="age" name="age"
                                                                value="" data-name="age" class="form-control"
                                                                placeholder="0" readonly="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ui chagnes by anil on 11-02-2025 for relation input merge with error -->
                                            </div>

                                        </div>
                                        <!-- <div class="col-lg-6 col-xl-6 col-xxl-4">
                                        <div class="form-group">
                                            <label for="fathername" class="form-label">Father's
                                                name</label>
                                            <input type="text" name="fathername"
                                                class="form-control alpha-only" id="fathername"
                                                placeholder="Father's Name"
                                                data-name="fathername">
                                        </div>
                                    </div> -->
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <!-- added form-label class on lable by anil on 27-03-2025 -->
                                                <label for="IndSecondName" class="quesLabel form-label">S/o, D/o,
                                                    Spouse Of
                                                </label>
                                                <div class="row merge-inputs">
                                                    <!-- ui chagnes by anil on 11-02-2025 for relation input merge with error -->
                                                    <div class="col-lg-6">
                                                        <select name="conPrefixInv" data-name="conPrefixInv"
                                                            id="prefix" class="form-select prefix">
                                                            <option value="S/o">S/o</option>
                                                            <option value="D/o">D/o</option>
                                                            <option value="Spouse Of">Spouse Of</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <input type="text" name="fathername"
                                                            data-name="fathername" id="fathername"
                                                            class="form-control alpha-only" placeholder="Relation">
                                                        <span class="error-message text-danger"></span>
                                                    </div>
                                                </div>
                                                <!-- ui chagnes by anil on 11-02-2025 for relation input merge with error -->
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="aadhar" class="form-label">Aadhaar Number</label>
                                                <input type="text" name="aadhar" class="form-control numericOnly"
                                                    id="aadhar" maxlength="12" placeholder="Aadhaar Number"
                                                    data-name="aadharnumber">
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="photo" class="form-label">Upload Aadhaar</label>
                                                <input type="file" name="convcoapplicant[0][aadhaarFile]"
                                                    class="form-control" accept=".pdf" data-name="aadhaarFile"
                                                    data-should-validate="">
                                                <span class="error-message text-danger"></span>
                                                <!-- add span tag by anil on 11-02-2025 -->
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="pan" class="form-label">PAN Number</label>
                                                <input type="text" name="pan"
                                                    class="form-control pan_number_format text-transform-uppercase"
                                                    id="pan" maxlength="10" placeholder="PAN Number"
                                                    data-name="pannumber">
                                                <span class="error-message text-danger"></span>
                                                <!-- add span tag by anil on 11-02-2025 -->
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="photo" class="form-label">Upload PAN</label>
                                                <input type="file" name="convcoapplicant[0][panFile]"
                                                    class="form-control" accept=".pdf" data-name="panFile"
                                                    data-should-validate="">
                                                <span class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        <!-- ------------------------ -->
                                        <div class="col-lg-6 col-xl-6 col-xxl-4">
                                            <div class="form-group">
                                                <label for="mobilenumber" class="form-label">Mobile
                                                    Number</label>
                                                <input type="text" name="mobilenumber"
                                                    class="form-control numericOnly" maxlength="10" id="mobilenumber"
                                                    placeholder="Mobile Number" data-name="mobilenumber">
                                                <span class="error-message text-danger"></span>
                                                <!-- add span tag by anil on 11-02-2025 -->
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-6 col-xl-6 col-xxl-4">
                                        <div class="form-group">
                                            <label for="photo" class="form-label">Photo</label>
                                            <input type="file" name="convcoapplicantphoto[0][photo]" class="form-control" accept=".jpg, .png, .jpeg" data-name="photo">
                                        </div>
                                    </div> --> <!-- changed UI to fix design and image privew by anil on 11-02-2025 -->

                                        <div class="col-lg-6 col-xl-6 col-xxl-4 d-flex justify-content-between">
                                            <div class="form-group form-box">
                                                <label for="photo" class="form-label">Upload Passport Size
                                                    Photo</label>
                                                <input type="file" name="convcoapplicantphoto[0][photo]"
                                                    class="form-control" accept=".jpg, .png, .jpeg" data-name="photo"
                                                    id="convcoapplicantphoto[0][photo]" data-should-validate="">
                                                <span class="error-message text-danger"></span>
                                            </div>
                                            <div class="preview_img">
                                                <input type="hidden" data-name="preview_img"
                                                    class="preview_img_hidden" name="coapplicant[0][preview_img]">
                                                <?php if(isset($coapplicant) && $coapplicant->image_path): ?>
                                                    <img class="preview" alt="Photo Preview"
                                                        src="<?php echo e(asset('storage/' . $coapplicant->image_path)); ?>"
                                                        style="display: block;" />
                                                <?php else: ?>
                                                    <img class="preview" alt="Photo Preview" />
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- Repeater Remove Btn -->
                                <div class="repeater-remove-btn">
                                    <button type="button" class="btn btn-danger remove-btn px-4"
                                        data-toggle="tooltip" data-placement="bottom"
                                        title="Click here to delete this co-applicant."
                                        onclick="removeRepeater($(this).parents('.items'),1)">
                                        <i class="fadeIn animated bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
        <!-- row end -->



        <div class="row g-2 mt-2" id="convLeaseDeed">
            <!-- convLeaseDeed id added by anil on 24-02-2025 for Regn. No. because of mutation and conversion Regn. No. have same id  -->
            <div class="col-lg-12">
                <div class="part-title mb-2">
                    <h5 id="freeleasetitle">Details of Lease Deed</h5>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convNameAsOnLease" class="form-label">Executed in Favour of<span
                            class="text-danger">*</span></label>
                    <input type="text" name="convNameAsOnLease" class="form-control alpha-only"
                        id="convNameAsOnLease" placeholder="Executed in Favour of"
                        value="<?php echo e(isset($application) ? $application->name_as_per_lease_conv_deed : ''); ?>" >
                    <div id="convNameAsOnLeaseError" class="text-danger text-left"></div>
                </div>
            </div>
            <!-- <div class="col-lg-4">
                <div class="form-group">
                    <label for="fathername" class="form-label">Father's name</label>
                    <input type="text" name="fathername" class="form-control alpha-only"
                        id="fathername" placeholder="Father's Name">
                </div>
            </div> -->
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="convExecutedOnAsOnLease" class="form-label">Executed on<span
                            class="text-danger">*</span></label>
                    <input type="date" name="convExecutedOnAsOnLease" class="form-control"
                        id="convExecutedOnAsOnLease" placeholder="Executed on"
                        value="<?php echo e(isset($application) ? $application->executed_on : ''); ?>" >
                    <!-- add readonly by anil on 16-02-2025 -->
                    <div id="convExecutedOnAsOnLeaseError" class="text-danger text-left"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="regno" class="form-label">Registration Number<span
                            class="text-danger">*</span></label>
                    <i class="bi bi-info-circle-fill text-primary qmark" data-toggle="tooltip" data-placement="top"
                        title="Registration No. as per registration details."
                        data-bs-custom-class="tooltip-info">
                        <span class="qmark">&#8505;</span>
                    </i>
                    <!-- ui chagnes by anil on 04-03-2025 for set maxlength -->
                    <input type="text" name="convRegnoAsOnLease" maxlength="30" class="form-control numericOnly"
                        id="regno" placeholder="Registration Number"
                        value="<?php echo e(isset($application) ? $application->reg_no : ''); ?>">
                    <div id="regnoError" class="text-danger text-left"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="bookno" class="form-label">Book Number<span class="text-danger">*</span></label>
                    <i class="bi bi-info-circle-fill text-primary qmark" data-toggle="tooltip" data-placement="top"
                        title="Book No. as per registration details."
                        data-bs-custom-class="tooltip-info">
                        <span class="qmark">&#8505;</span>
                    </i>
                    <!-- ui chagnes by anil on 04-03-2025 for set maxlength -->
                    <input type="text" name="convBooknoAsOnLease" maxlength="10" class="form-control numericOnly"
                        id="convbookno" placeholder="Book Number"
                        value="<?php echo e(isset($application) ? $application->book_no : ''); ?>">
                    <div id="convbooknoError" class="text-danger text-left"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="volumeno" class="form-label">Volume Number<span class="text-danger">*</span></label>
                    <i class="bi bi-info-circle-fill text-primary qmark" data-toggle="tooltip" data-placement="top"
                        title="Volume No. as per registration details."
                        data-bs-custom-class="tooltip-info">
                        <span class="qmark">&#8505;</span>
                    </i>
                    <!-- ui chagnes by anil on 04-03-2025 for set maxlength -->
                    <input type="text" name="convVolumenoAsOnLease" maxlength="10"
                        class="form-control numericOnly" id="convvolumeno" placeholder="Volume Number"
                        value="<?php echo e(isset($application) ? $application->volume_no : ''); ?>">
                    <div id="convvolumenoError" class="text-danger text-left"></div>
                </div>
            </div>
            <?php
                $page_no_from = $page_no_to = '';
                if (isset($application) && !is_null($application->page_no)) {
                    [$page_no_from, $page_no_to] = explode('-', $application->page_no);
                }
            ?>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="pageno" class="form-label">Page Number<span class="text-danger">*</span></label>
                    <i class="bi bi-info-circle-fill text-primary qmark" data-toggle="tooltip" data-placement="top"
                        title=" Page Nos. as per registration details."
                        data-bs-custom-class="tooltip-info">
                        <span class="qmark">&#8505;</span>
                    </i>
                    <div class="row merge-inputs">
                        <!-- ui chagnes in class by anil on 11-02-2025 as same relation input merge with error -->
                        <div class="col-lg-6">
                            <input type="text" name="convPagenoFrom" maxlength="4"
                                class="form-control numericOnly" id="convPagenoFrom" placeholder="From"
                                value="<?php echo e($page_no_from); ?>">
                            <div id="convPagenoFromError" class="text-danger text-left"></div>
                        </div>
                        <div class="col-lg-6">
                            <input type="text" name="convPagenoTo" maxlength="4"
                                class="form-control numericOnly" id="convPagenoTo" placeholder="To"
                                value="<?php echo e($page_no_to); ?>">
                            <div id="convPagenoToError" class="text-danger text-left"></div>
                        </div>
                    </div> <!-- ui chagnes by anil on 11-02-2025 for relation input merge with error -->
                    
                    <!-- <input type="text" name="convPagenoAsOnLease" class="form-control numericOnly" id="pageno" placeholder="Page No." value="<?php echo e(isset($application) ? $application->page_no_as_per_lease_conv_deed : ''); ?>"> -->
                    <div id="pagenoError" class="text-danger text-left"></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="regdate" class="form-label">Registration Date<span
                            class="text-danger">*</span></label>
                    <input type="date" name="convRegdateAsOnLease" class="form-control" id="convregdate"
                        value="<?php echo e(isset($application) ? $application->reg_date : ''); ?>">
                    <!-- changed ID in input and error div by anil on 18-02-2025 -->
                    <div id="convregdateError" class="text-danger text-left"></div>
                </div>
            </div>
        </div>
        <!-- row end -->
        <div class="container-fluid"> <!-- changed and added by developer -->
            <div class="row row-mb-2">
                <div class="col-lg-12 items">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center">
                            <h6 class="mr-5 mb-0">Whether the application is on the basis of a court order?
                            </h6>
                            <div class="form-check mr-5">
                                <input class="form-check-input" name="courtorderConversion" type="radio"
                                    value="1" id="YesCourtOrderConversion"
                                    <?php echo e(isset($application) && $application->is_court_order ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="YesCourtOrderConversion">
                                    <h6 class="mb-0">Yes</h6>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" name="courtorderConversion" type="radio"
                                    value="0" id="NoCourtOrderConversion"
                                    <?php echo e(isset($application) && $application->is_court_order ? '' : 'checked'); ?>>
                                <label class="form-check-label" for="NoCourtOrderConversion">
                                    <h6 class="mb-0">No</h6>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- added class mt-2 for careate spacing between title and label by anil on 17-04-2025 -->
                    <div class="col-lg-12 mt-2" id="yescourtorderConversionDiv"
                        <?php echo e(isset($application) && $application->is_court_order ? '' : 'style=display:none;'); ?>>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="casenosubmut" class="form-label">Case Number<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="convCaseNo"
                                        class="form-control alphaNum-hiphenForwardSlash" id="convCaseNo"
                                        placeholder="Case Number"
                                        value="<?php echo e(isset($application) ? $application->case_no : ''); ?>">
                                    <div id="convCaseNoError" class="text-danger text-left"></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="convCaseDetail" class="form-label">Details</label>
                                    <textarea name="convCaseDetail" id="convCaseDetail" class="form-control"><?php echo e(isset($application) ? $application->case_detail : ''); ?></textarea>
                                    <div id="convCaseDetailError" class="text-danger text-left"></div>
                                </div>
                            </div>
                        </div>
                        <?php
                            $courtOrder = null;
                            if (isset($application) && $application->tempDocument) {
                                $courtOrder = $application->tempDocument
                                    ->where('document_type', 'convCourtOrderFile')
                                    ->first();
                            }
                        ?>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group form-box">
                                    <label for="convCourtOrderFile" class="quesLabel form-label"
                                        data-toggle="tooltip" data-placement="top"
                                        title="Certified copies of any court order / decree">Court Order / Decree In PDF
                                        <span><i class='bx bx-info-circle'></i></span> <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="convCourtOrderFile" class="form-control"
                                        accept="application/pdf" id="convCourtOrderFile"
                                        data-should-validate = "<?php echo e(isset($courtOrder) && $courtOrder->file_path != ''); ?>">
                                    <?php if($courtOrder): ?>
                                        <a href="<?php echo e(asset('storage/' . $courtOrder->file_path ?? '')); ?>"
                                            target="_blank" class="fs-6">View Saved Document</a>
                                    <?php endif; ?>
                                    <div id="convCourtOrderFileError" class="text-danger text-left">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="convCourtOrderDate" class="form-label">Date of Document<span
                                            class="text-danger">*</span></label>
                                    <?php
                                        if ($courtOrder) {
                                            $courtOrderDate = $courtOrder->tempDocumentKeys
                                                ->where('key', 'convCourtOrderDate')
                                                ->first();
                                        }
                                    ?>
                                    <input type="date" name="convCourtOrderDate" class="form-control"
                                        id="convCourtOrderDate"
                                        value="<?php echo e(empty($courtOrderDate) ? '' : $courtOrderDate->value); ?>">
                                    <div id="convCourtOrderDateError" class="text-danger text-left"></div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="courtorderattestedbyConversion" class="form-label">Issuing
                                        Authority<span class="text-danger">*</span></label>
                                    <?php
                                        if ($courtOrder) {
                                            $courtOrderIssuingAuthority = $courtOrder->tempDocumentKeys
                                                ->where('key', 'courtorderattestedbyConversion')
                                                ->first();
                                        }
                                    ?>
                                    <input type="text" name="courtorderattestedbyConversion" class="form-control"
                                        id="courtorderattestedbyConversion" placeholder="Issuing Authority"
                                        value="<?php echo e(empty($courtOrderIssuingAuthority) ? '' : $courtOrderIssuingAuthority->value); ?>">
                                    <div id="courtorderattestedbyConversionError" class="text-danger text-left"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row end -->

            <div class="row row-mb-2">
                <div class="col-lg-12 items">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="d-flex align-items-center">
                                <h6 class="mr-5 mb-0">Is property mortgaged?</h6>
                                <div class="form-check mr-5">
                                    <input class="form-check-input" name="propertymortgagedConversion"
                                        id="YesMortgagedConversion" type="radio" value="1"
                                        <?php echo e(isset($application) && $application->is_mortgaged == 1 ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="YesMortgagedConversion">
                                        <h6 class="mb-0">Yes</h6>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="propertymortgagedConversion" type="radio"
                                        value="0" id="NoMortgagedConversion"
                                        <?php echo e(isset($application) && $application->is_mortgaged == 1 ? '' : 'checked'); ?>>
                                    <label class="form-check-label" for="NoMortgagedConversion">
                                        <h6 class="mb-0">No</h6>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- added class mt-2 for careate spacing between title and label by anil on 17-04-2025 -->
                        <div class="col-lg-12 mt-2" id="yesRemarksDivConversion"
                            <?php echo e(isset($application) && $application->is_mortgaged == 1 ? '' : 'style=display:none;'); ?>>
                            <?php
                                $morgageNOC = null;
                                if (isset($application) && $application->tempDocument) {
                                    $morgageNOC = $application->tempDocument
                                        ->where('document_type', 'mortgageNoCFile')
                                        ->first();
                                    $NOCAttestationDateConversion = !empty($morgageNOC)
                                        ? $morgageNOC->tempDocumentKeys
                                            ->where('key', 'NOCAttestationDateConversion')
                                            ->first()
                                        : null;
                                    $NOCIssuedByConversion = !empty($morgageNOC)
                                        ? $morgageNOC->tempDocumentKeys->where('key', 'NOCIssuedByConversion')->first()
                                        : null;
                                }
                            ?>
                            <div class="row">
                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="NOCAttestationDateConversion" class="form-label">Date of
                                            NOC<span class="text-danger">*</span></label>
                                        <input type="date" name="NOCAttestationDateConversion"
                                            class="form-control" id="NOCAttestationDateConversion"
                                            value="<?php echo e(!empty($NOCAttestationDateConversion) ? $NOCAttestationDateConversion->value : ''); ?>">
                                        <div id="NOCAttestationDateConversionError" class="text-danger text-left">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="NOCIssuedByConversion" class="form-label">Issuing Authority (Bank/ Financial institution)<span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="NOCIssuedByConversion" class="form-control"
                                            id="NOCIssuedByConversion" placeholder="Issuing Authority"
                                            value="<?php echo e(!empty($NOCIssuedByConversion) ? $NOCIssuedByConversion->value : ''); ?>">
                                        <div id="NOCIssuedByConversionError" class="text-danger text-left"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>










    </div>
</div>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/conversion/include/step-1.blade.php ENDPATH**/ ?>