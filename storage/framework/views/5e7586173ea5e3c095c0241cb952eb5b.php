<!-- updated by Swati on 22062026 for old demands -->
<div class="modal fade" id="reminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Send Old Demand Reminder
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>
            </div>

            <form id="sendReminderForm">

                <?php echo csrf_field(); ?>

                <div class="modal-body">

                    <input type="hidden"
                           name="demand_id"
                           id="reminder_demand_id">

                    <div class="row mb-3">

                        <div class="col-md-6">
                            <label class="fw-bold">Reminder Emails Sent</label>
                            <!-- anil changeg class for old demand moadal font size 22-06-2026 -->
                            <div id="email_sent_count" class="fs-5 text-dark">
                                0
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Latest Reminder Sent On</label>
                            <div id="last_reminder_sent_at" class="fs-5 text-dark">
                                N/A
                            </div>
                        </div>

                    </div>

                    <div class="alert alert-warning">
                        You may edit the recipient details before sending the reminder email.
                        Updated details will be saved for future reminders.
                    </div>

                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Lessee Name
                            </label>

                            <input type="text"
                                   name="name"
                                   id="reminder_name"
                                   class="form-control"
                                   placeholder="Enter Lessee Name">
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Email Address
                            </label>

                            <input type="email"
                                   name="email"
                                   id="reminder_email"
                                   class="form-control"
                                   placeholder="Enter Email Address"
                                   required>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label class="form-label">
                                Address
                            </label>

                            <textarea name="address"
                                      id="reminder_address"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Enter Address"></textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="finalSendReminderBtn">

                        <i class="bx bx-envelope"></i>
                        Send Reminder Email

                    </button>

                </div>

            </form>

        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\edhartiMain\resources\views/include/parts/demand-reminder.blade.php ENDPATH**/ ?>