<div class="mt-3">
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-lg-12">
                <h6 class="mt-3 mb-0" id="LUCHideTitle">Terms & Conditions</h6>
                <ul class="consent-agree">
                   
                    <li>
                        Processing fee {{ getApplicationCharge(getServiceType('LUC')) }} /- Rs. is non-refundable.
                    </li>
                </ul>
                <div class="form-check form-group">
                    <input class="form-check-input" type="checkbox" name="lucagreeconsent" id="lucagreeconsent"
                        {{ isset($application) && $application->applicant_consent == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="lucagreeconsent">I agree, all the
                        information provided by me is accurate to the best of my knowledge. I
                        take full responsibility for any issues or failures that may arise from
                        its use.</label>

                    <div id="lucagreeconsentError" class="text-danger text-left"></div>
                </div>

            </div>
        </div>
    </div>
</div>
