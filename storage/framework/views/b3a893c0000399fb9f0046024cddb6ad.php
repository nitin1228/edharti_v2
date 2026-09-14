<div class="mt-3">
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-lg-12">
                <h6 class="mt-3 mb-0" id="LUCHideTitle">Terms & Conditions</h6>
                <ul class="consent-agree">
                   
                    <li> Processing fee of Re. <?php echo e(getApplicationCharge(getServiceType('LUC'))); ?> /-  is non-refundable.</li>
                    <li>The permission will be subject to approval of concerned municipal authority or any other government agency</li>
                    <li>The application will be liable to be rejected if any of the provided information is found to be false</li>
                </ul>
                <div class="form-check form-group">
                    <input class="form-check-input" type="checkbox" name="lucagreeconsent"
                        id="lucagreeconsent" <?php echo e(isset($application) && $application->applicant_consent ==1 ? 'checked':''); ?>>
                    <label class="form-check-label" for="lucagreeconsent">I agree</label>

                        <div id="lucagreeconsentError" class="text-danger text-left"></div>
                </div>

            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/luc/include/step-3.blade.php ENDPATH**/ ?>