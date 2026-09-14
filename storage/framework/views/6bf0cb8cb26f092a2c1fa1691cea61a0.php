<?php echo $__env->make('include.loader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<form id="colonyRgrForm" method="post" action="<?php echo e(route('reviseGroundRentForColony')); ?>">
    <?php echo csrf_field(); ?>
    <div class="bs-stepper-content">
        <div id="" role="" class="" aria-labelledby="">
            <h5 class="mb-1">Select a colony</h5>

            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <label for="colonyName" class="form-label">Colony Name (Present)</label>
                    <select class="form-control selectpicker" data-live-search="true" name="colony_id" id="colony_id" aria-label="Colony Name (Present)">
                        <option value="">Select</option>
                        <?php $__currentLoopData = $colonies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $colony): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($colony->id); ?>"><?php echo e($colony->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['colony_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="errorMsg"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div id="colonyIdError" class="text-danger"></div>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="colonyName" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(date('Y-01-01')); ?>">
                    <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="errorMsg"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div id="startDateError" class="text-danger"></div>
                </div>
                <div class="col-12 col-lg-4">
                    <label for="colonyName" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(date('Y-12-31')); ?>">
                    <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="errorMsg"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div id="endDateError" class="text-danger"></div>
                </div>
            </div><!---end row-->

            <div class="col col-lg-2 pt-1"><button type="button" class="btn btn-primary px-4 mt-4" id="submitButton1">Search<i class='bx bx-right-arrow-alt ms-2'></i></button></div>

        </div>
    </div>


    <div class="d-none" id="detail-card">
        <h5 class="mb-4 pt-3 text-decoration-underline">COLONY RGR DETAILS</h5>
        <div class="pb-3">

            <table class="table table-bordered">
                <tbody id="detail-container">
                </tbody>
            </table>
        </div>
        <div class="col-12 col-lg-4 mb-3">
            <label> Calculate using</label>
            <div class="d-flex flex-row" style="justify-content: space-between;">
                <div><input type="radio" name="calculation_rate" value="L"> L&DO rate</div>
                <div><input type="radio" name="calculation_rate" value="C"> Circle rate</div>
            </div>

            <?php $__errorArgs = ['calculation_rate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="errorMsg"><?php echo e($calculation_rate); ?></span>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div id="calculation_rateError" class="text-danger"></div>
        </div>
        <div class="pb-3">
            <button type="button" class="btn btn-primary btn-form-submit" data-action="show">Revise Ground Rent</button>
        </div>
        <div class="pb-3">

            <table class="table table-bordered" id="property-list">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Property Id</th>
                        <th>Property Type</th>
                        <th>Presently known as</th>
                        <th>Area(sqm)</th>
                        <th>Land value(L&DO rate)</th>
                        <th>Land value(circle rate)</th>
                        <th>RGR/year(L&DO rate)</th>
                        <th>RGR/year(circle rate)</th>
                        <th>RGR Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-primary btn-form-submit" data-action="show">Revise Ground Rent</button>
    </div>
</form>

<script>
    var selectedColonyId;

    $('#submitButton1').click(() => {
        let colony_id = $('#colony_id').val();
        let start_date = $('#start_date').val();
        if (colony_id.trim() != "") {
            getColonyRgrDetail(colony_id, start_date);
        }
    })



    function getColonyRgrDetail(selectedColonyId, startDate) {
        if (startDate && selectedColonyId) {
            $.ajax({
                type: "post",
                url: "<?php echo e(route('colonyRGRDetails')); ?>",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    selectedColonyId: selectedColonyId,
                    startDate: startDate,
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success') {
                        if (response.data.properties && response.data.properties.length > 0) {
                            displayRgrDetails(response.data);
                        } else {
                            showError("No lease hold property found for this colony")
                        }
                    } else {
                        showError(response.message)
                    }
                },
                error: response => {
                    if (response.responseJSON && response.responseJSON.message) {
                        showError(response.responseJSON.message)
                    }
                }
            })
        } else {
            alert('required values not provided');
        }
    }

    function displayRgrDetails(data) {
        detailHTML =
            $('#detail-container').html(`
            <tr>
                <th>Total Lease Hold Properties</th>
                <th>Total Area</th>
                <th>RGR Done for Properties</th>
                <th>RGR Done for Area</th>
            </tr>
            <tr>
                <td>${data.leaseHoldCount}</td>
                <td>${data.leaseHoldArea.toFixed(2)} sqm.</td>
                <td>${data.rgrDoneCount}</td>
                <td>${data.rgrDoneArea.toFixed(2)} sqm.</td>
            </tr>
            `);
        var landValueCircle = landValueLndo = rgrCircle = rgrLndo = 'not available';
        if (data.properties && data.properties.length > 0) {
            var listHTML = "";
            var counter = 1;
            data.properties.forEach(prop => {
                let propType;
                if (prop.property_type == '47') {
                    propType = 'residential';
                }
                if (prop.property_type == '48') {
                    propType = 'commercial';
                }
                plotArea = parseFloat(prop.plot_area);
                //caculate land value and RGR
                if (data.circleRates != null && data.circleRates[propType] != null) {
                    landRateCircle = parseFloat(data.circleRates[propType]['land_rate']);
                    landValueCircle = landRateCircle * plotArea;
                    rgrFactor = data.rgrFactor.circle[propType];
                    if (rgrFactor > 0) {
                        rgrCircle = ((landValueCircle * rgrFactor) / 100).toFixed(2);
                    }
                    landValueCircle = landValueCircle.toFixed(2);
                }
                if (data.lndoRates != null && data.lndoRates[propType] != null) {
                    landRateLndo = parseFloat(data.lndoRates[propType]['land_rate']);
                    landValueLndo = landRateLndo * plotArea;
                    rgrFactor = data.rgrFactor.lndo[propType];
                    if (rgrFactor > 0) {
                        rgrLndo = ((landValueLndo * rgrFactor) / 100).toFixed(2);
                    }
                    landValueLndo = landValueLndo.toFixed(2);
                }
                // is rgr done for property
                //let rgrDone = prop.id in data.done && (!prop.splited_id || data.done[prop.id].includes(prop.splited_id));
                let rgrDone = false; //rgr not done by default
                let rgrId = null;
                let pdf = null;
                if (prop.id in data.done) {
                    if (!prop.splited_id) {
                        rgrId = data.done[prop.id].id;
                        pdf = data.done[prop.id].pdf
                        rgrDone = true;
                    } else {
                        if (prop.splited_id in data.done[prop.id]) {
                            rgrId = data.done[prop.id][prop.splited_id].id;
                            pdf = data.done[prop.id][prop.splited_id].pdf;
                            rgrDone = true;
                        }
                    }
                }
                listHTML += `<tr>
                    <td>${counter}</td>
                    <td>${prop.property_id}</td>
                    <td>${propType.charAt(0).toUpperCase() + propType.substring(1).toLowerCase()}</td>
                    <td>${prop.presently_known_as}</td>
                    <td>${plotArea.toFixed(2)}</td>
                    <td>${landValueLndo}</td>
                    <td>${landValueCircle}</td>
                    <td>${rgrLndo}</td>
                    <td>${rgrCircle}</td>
                    <td id="${'status-'+prop.id+ ((prop.splited_id != null) ? '-'+prop.splited_id:'')}">${pdf ? '<a class="btn btn-pdf" href="/'+pdf+'" target="_blank"><i class=\'bx bxs-file-pdf\'></i> View PDF</a>' : rgrDone ? '<a class="btn btn-default btn-draft" onclick="viewDraft('+rgrId +')"><i class="bx bx-check"></i>View Draft</a>':'<i class="bx bx-x bx-md" style="color:red"></i>'}</td>
                </tr>`;
                counter++;
            });
        }
        $('#property-list tbody').html(listHTML);
        if (!data.lndoRates.residential && !data.lndoRates.commercial) {
            $('input[name="calculation_rate"][value="L"]').prop('disabled', true);
        } else {
            $('input[name="calculation_rate"][value="L"]').prop('disabled', false);
        }
        if (!data.circleRates.residential && !data.circleRates.commercial) {
            $('input[name="calculation_rate"][value="C"]').prop('disabled', true);
        } else {
            $('input[name="calculation_rate"][value="C"]').prop('disabled', false);
        }
        $('#detail-card').removeClass('d-none');
    }

    $(document).on('click', '.btn-form-submit', () => {
        $('#colonyRgrForm').addClass('d-none');
        if ($('.loader_container').hasClass('d-none'))
            $('.loader_container').removeClass('d-none');

        $.ajax({
            type: $('#colonyRgrForm').attr('method'),
            url: $('#colonyRgrForm').attr('action'),
            data: $('#colonyRgrForm').serializeArray(),
            success: (response) => {
                $('.loader_container').addClass('d-none');
                if ($('#colonyRgrForm').hasClass('d-none'))
                    $('#colonyRgrForm').removeClass('d-none');
                if (response.status == 'error') {
                    showError(response.message)
                }
                if (response.status == 'success') {

                    showSuccess(response.message)
                }
                if (response.data && response.data.length > 0) {
                    response.data.forEach(item => {
                        $(document).find('#status-' + item.target).html(`<a class="btn btn-default btn-draft"onclick="viewDraft(${item.id})"><i class="bx bx-check"></i>View Draft</a>`)
                    })
                }
            },
            error: response => {
                $('.loader_container').addClass('d-none');
                if ($('#colonyRgrForm').hasClass('d-none'))
                    $('#colonyRgrForm').removeClass('d-none');
                if (response.responseJSON && response.responseJSON.message) {
                    showError(response.responseJSON.message)
                }
            }
        })
    })
</script><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/rgr/colony-input.blade.php ENDPATH**/ ?>