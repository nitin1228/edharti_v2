<!-- Delete Request Confirmation Modal -->
<div class="modal fade" id="deleteRequestModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-center">
      <div class="modal-header border-0 h-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img src="{{ asset('assets/images/warning.svg') }}" alt="confirm" class="warning_icon mb-3" style="width: 60px;">
        <h4 class="modal-title mb-2" id="deleteRequestModalLabel">Are you sure?</h4>
        <p>This will remove the request from the list. You can restore it later if needed.</p>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-secondary btn-width" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger btn-width confirm-delete-request">Yes</button>
        </div>
      </div>
    </div>
  </div>
</div>
