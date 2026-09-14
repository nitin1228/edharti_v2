<!-- Return to Record Confirmation Modal -->
<div class="modal fade" id="returnToRecordModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="returnToRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header border-0 h-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="{{ asset('assets/images/approve.svg') }}" alt="confirm" class="warning_icon mb-3" style="width: 60px;">
                <h4 class="modal-title mb-2" id="returnToRecordModalLabel">Are you sure?</h4>
                <p>Do you want to return this request to record?</p>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary btn-width" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-warning btn-width confirm-return-to-record">Yes</button>
                </div>
            </div>
        </div>
    </div>
</div>
