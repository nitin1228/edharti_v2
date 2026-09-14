<div class="col-lg-8 mt-4">
    <div class="container-fluid pb-3 forward_department">
        @if ($showRevertButton || $showActionButtons)
            <div class="row">
                <div class="checkbox-options">
                    <div class="form-check">
                        <label class="form-check-label" for="forwardToDepartment">
                            Forward To
                        </label>
                        <input class="form-check-input" name="forwardToDepartment" type="checkbox"
                            id="forwardToDepartment">
                    </div>
                </div>
                <div class="col-lg-12 col-12 items" id="divForwardAppDept" style="display: none;">
                    <form id="forwardAppForm" method="POST">
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="forwardTo">Select</label>
                                    <select id="forwardTo" name="forwardTo" class="form-select" required>
                                        <option value="">Select</option>
                                        {{-- // Commented the code below to display the title instead of the name in the "Forward to Department Role" dropdown - Lalit (2 April 2025) --}}
                                        {{-- @if ($departmentRoles)
                                                    @foreach ($departmentRoles as $departmentRole)
                                                        <option value="{{ $departmentRole }}">
                                                            {{ $departmentRole }}
                                                        </option>
                                                    @endforeach
                                                @endif --}}
                                        {{-- Added the code below to display the title in the "Forward to Department Role" dropdown - Lalit (2 April 2025) --}}
                                        @if ($departmentRoles)
                                            @foreach ($departmentRoles as $value => $label)
                                                <option value="{{ $value }}">
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        @endif

                                    </select>
                                    <div id="forwardToError" class="text-danger" style="display: none;">
                                        Please select a role.</div>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="forwardRemark">Enter Remarks</label>
                                    <textarea name="forwardRemark" id="forwardRemark" class="form-control" placeholder="Remarks" rows="3" required></textarea>
                                    <div id="forwardRemarkError" class="text-danger" style="display: none;">
                                        Please
                                        enter remarks.</div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button type="submit" id="forwardAppBtn" class="btn btn-secondary">Send
                                    Remark <i class="fa-regular fa-paper-plane"></i></button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        @endif
    </div>
</div>
