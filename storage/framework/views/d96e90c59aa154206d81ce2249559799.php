<div class="mt-3">
    <div class="container-fluid">
        
        <div class="row g-3 mb-2 mt-3">
            <div class="col-lg-12">
                <div class="part-title mb-2">
                    <h5>Land Use Change Details</h5>
                </div>
            </div>
            <!-- from -->
            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label for="lucpropertytype" class="form-label">Present Property Type</label>
                    <select name="lucpropertytype" id="lucpropertytype" class="form-select" disabled>

                    </select>
                    <div id="lucpropertytypeError" class="text-danger text-left"></div>
                </div>
            </div>
            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label for="lucpropertysubtype" class="form-label">Present Property Sub Type</label>
                    <select name="lucpropertysubtype" id="lucpropertysubtype" class="form-select" disabled>

                    </select>
                    <div id="lucpropertychangetouseError" class="text-danger text-left"></div>
                </div>
            </div>
            <!-- // from -->
            <!-- to -->
            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label for="lucpropertytypeto" class="form-label">Change to Property Type<span
                            class="text-danger">*</span></label>
                    <select name="lucpropertytypeto" id="lucpropertytypeto" class="form-select">

                    </select>
                    <div id="lucpropertytypetoError" class="text-danger text-left"></div>
                </div>
            </div>
            <div class="col-lg-3 col-12">
                <div class="form-group">
                    <label for="lucpropertysubtypeto" class="form-label">Change to Property Sub Type<span
                            class="text-danger">*</span> </label>
                    <select name="lucpropertysubtypeto" id="lucpropertysubtypeto" class="form-select">

                    </select>
                    <div id="lucpropertysubtypetoError" class="text-danger text-left"></div>
                </div>
            </div>
            <!--//to-->
        </div>
        <div class="row g-3 mb-2 mt-3">
            
            <div class="col-lg-3 builtUpAreaInputs" >
                <label>Total built up area</label>
                <input type="number" class="form-control" id="luc_TBUA" name="luc_TBUA">
                <div class="error" id="luc_TBUA_error"></div>
            </div>
            <div class="col-lg-3 builtUpAreaInputs" >
                <label>Area to be used as commercial</label>
                <input type="number" class="form-control" id="luc_BUAC" name="luc_BUAC">
                <div class="error" id="luc_BUAC_error"></div>
            </div>
            
        </div>
        
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/application/luc/include/step-1.blade.php ENDPATH**/ ?>