<!-- <style>
    .mt-center{
        margin-top: 2.5rem;
    }
</style> -->
<div class="col-12">
    <div class="row flex-wrap g-3"> 
        <div class="col col-lg-3">
            <label for="oldPropertyId" class="form-label">Search By Property Id</label>
            <input type="text" name="oldPropertyId" id="oldPropertyId" class="form-control" placeholder="Enter property id">
        </div>
    </div><!---end row-->
</div>

<!-- <script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script> -->
<script>
    var selectedColonyId;
    var leaseHoldOnly = <?= isset($leaseHoldOnly) ? 1 : 0 ?>;
    $('#colony_id').change(function() {
        selectedColonyId = $(this).val();
        var targetSelect = $('#block')
        targetSelect.html('<option>Select</option>');
        targetSelect.selectpicker('refresh');
        var reponseUrl = "{{ route('blocksInColony', ['colonyId' => '__ID__', 'leaseHoldOnly' => '__LEASE__']) }}";
            reponseUrl = reponseUrl.replace('__ID__', selectedColonyId).replace('__LEASE__', leaseHoldOnly ? 1 : 0);        
            if (selectedColonyId != "") {
            $.ajax({ // call for subtypes for selected property types
                url: reponseUrl,
                type: "get",
                success: function(res) {
                    if (leaseHoldOnly && res.length == 0) {
                        showError('No lease hold property found in the selected colony');
                    }
                    $.each(res, function(key, value) {

                        var newOption = $('<option>', {
                            value: value.block_no ?? "null",
                            text: value.block_no ?? 'Not Applicable'
                        });
                        targetSelect.append(newOption);
                    });
                    targetSelect.selectpicker('refresh');
                }
            });
        }
    })
    $('#block').change(function() {
        var selectedBlock = $(this).val();
        var targetSelect = $('#plot')
        targetSelect.html('<option value="">Select</option>')
        if (selectedColonyId != "") {
        var reponseUrl = "{{ route('propertiesInBlock', ['colonyId' => '__COLONY__', 'blockId' => '__BLOCK__', 'leaseHoldOnly' => '__LEASE__']) }}";
            reponseUrl = reponseUrl
                .replace('__COLONY__', selectedColonyId)
                .replace('__BLOCK__', selectedBlock)
                .replace('__LEASE__', leaseHoldOnly ? 1 : 0);            $.ajax({
                url: reponseUrl,
                type: "get",
                success: function(res) {

                    $.each(res, function(key, value) {
                        var newOption = $('<option>', {
                            value: (value.is_joint_property !== undefined) ? value.old_propert_id : value.property_master_id + '_' + value.id, // if not splited then old property id else parentPropertyId_splitedPropertyId
                            text: value.plot_or_property_no ?? value.plot_flat_no,
                            'data-known-as':value.presently_known_as
                        });
                        targetSelect.append(newOption);
                    });
                    targetSelect.selectpicker('refresh');

                }
            });
        }
    });
    $('#plot').change(function(){
        var knownAs = $(this).find(':selected').attr('data-known-as');
        $('#known-as').html(knownAs);
        $('#knownAsDiv').show();
    });
    $('#colony_id, #block').change(function(){
        $('#knownAsDiv').hide();
    })
</script>
