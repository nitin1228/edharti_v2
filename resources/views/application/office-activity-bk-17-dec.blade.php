<div class="part-title mt-2">
    <h5>Office Activity</h5>
</div>
<div class="part-details">

    @if ($showRevertButton)
        {{-- Forward Application To Other Department --}}



        {{-- Revert comming application --}}
        {{-- <div class="container-fluid pb-3">
                    <div class="row"> --}}
        {{-- <div class="col-lg-12 col-12">
                            <div class="row mb-3">
                                <div class="col-lg-4">
                                    {{-- Button to trigger modal --
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#revertModal">Revert</button>
                                </div>
                            </div>
                        </div> --}}
        {{-- Modal Popup --}}
        <div class="modal fade" id="revertModal" tabindex="-1" aria-labelledby="revertModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="revertAppForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="revertModalLabel">Revert Application</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="revertRemark" class="form-label">Enter Revert
                                    Remarks</label>
                                <textarea name="revertRemark" id="revertRemark" class="form-control" placeholder="Remarks" rows="3" required></textarea>
                                <div id="revertRemarkError" class="text-danger" style="display: none;">
                                    Please enter remarks.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="revertAppBtn" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- </div>
                </div> --}}
    @endif



    @if ($application->Signed_letter)
        <a href="{{ asset('storage/' . $application->Signed_letter) }}" target="_blank">View Signed
            Letter</a>
    @else
        <div class="container-fluid pb-3">
            <div class="row">
                <div class="col-lg-9">
                    <div class="mis-view-group-btn">
                        @php
                            $serviceCode = getServiceCodeById($serviceType) ?? '';
                            $modalId = $details->id ?? '';
                            $applicant_no = $details->application_no ?? '';
                            $masterId = $details->property_master_id ?? '';
                            $uniquePropertyId = $details->new_property_id ?? '';
                            $oldPropertyId = $details->old_property_id ?? '';
                            $sectionCode = $details->sectionCode ?? '';
                            $additionalData = [
                                $serviceCode,
                                $modalId,
                                $applicant_no,
                                $masterId,
                                $uniquePropertyId,
                                $oldPropertyId,
                                $sectionCode,
                            ];
                            $additionalDataJson = json_encode($additionalData);
                        @endphp
                        <div class="btn-group">
                            <a href="javascript:void(0);" id="PropertyIDSearchBtn" class="btn btn-grey pdf-btn"
                                data-bs-toggle="modal" data-bs-target="#viewScannedFiles">View Scanned Files <i
                                    class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="{{ route('viewDetails', ['property' => $propertyMasterId]) }}?params={{ urlencode($additionalDataJson) }}"
                                target="_blank">
                                <button type="button" id="PropertyIDSearchBtn" class="btn btn-primary ml-2">Go to
                                    Property Details</button>
                            </a>
                        </div>


                    </div>
                    <!-- <div class="row pt-3">
                                                                                        <div class="checkbox-options">
                                                                                            <div class="form-check form-check-success">
                                                                                                <label class="form-check-label" for="isUnderReview">
                                                                                                    Send To Deputy L&amp;DO For Review
                                                                                                </label>
                                                                                                <input class="form-check-inputs" type="checkbox" value="review" id="isUnderReview">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div> -->
                    <!-- <div class="row py-3">
                                                                                        <div class="col-lg-12 mt-4">
                                                                                            <div class="checkbox-options">
                                                                                                <div class="form-check form-check-success">
                                                                                                    <label class="form-check-label" for="isMISCorrect">
                                                            
                                                                                                        MIS Checked &amp; Found Correct
                                                                                                    </label>
                                                                                                    <input class="form-check-input required-for-approve" name="is_mis_checked"
                                                                                                        type="checkbox" value="1" id="isMISCorrect">
                                                            
                                                                                                </div>
                                                                                            </div>
                                                            
                                                                                            <div class="checkbox-options">
                                                                                                <div class="form-check form-check-success">
                                                                                                    <label class="form-check-label" for="isScanningCorrect">
                                                            
                                                                                                        Scanned File Checked &amp; Found Correct
                                                                                                    </label>
                                                                                                    <input class="form-check-input required-for-approve" name="is_scan_file_checked"
                                                                                                        type="checkbox" value="1" id="isScanningCorrect">
                                                            
                                                                                                </div>
                                                                                            </div>
                                                            
                                                                                            <div class="checkbox-options">
                                                                                                <div class="form-check form-check-success">
                                                                                                    <label class="form-check-label" for="isDocumentCorrect">
                                                            
                                                                                                        Uploaded Documents Checked &amp; Found Correct
                                                                                                    </label>
                                                                                                    <input class="form-check-input required-for-approve"
                                                                                                        name="is_uploaded_doc_checked" type="checkbox" value="1"
                                                                                                        id="isDocumentCorrect">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                    </div> -->

                    <div class="row py-3">
                        <div class="col-lg-12 mt-4">
                            <div class="checkbox-options">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label" for="isMISCorrect">

                                        MIS Checked
                                        {{-- &amp; Found Correct --}}
                                    </label>
                                    <input class="form-check-input required-for-approve"
                                        @if ($checkList && $checkList->is_mis_checked == 1) checked disabled @endif name="is_mis_checked"
                                        type="checkbox" value="1" id="isMISCorrect">
                                    <div class="text-danger required-error-message" id="misCheckedError">This
                                        field is required.
                                    </div>
                                </div>
                            </div>

                            <div class="checkbox-options">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label" for="isScanningCorrect">

                                        Scanned File Checked
                                        {{-- &amp; Found Correct --}}
                                    </label>
                                    <input class="form-check-input required-for-approve" name="is_scan_file_checked"
                                        type="checkbox" value="1" id="isScanningCorrect"
                                        @if ($checkList && $checkList->is_scan_file_checked == 1) checked disabled @endif>
                                    <div class="text-danger required-error-message">This field is required.
                                    </div>
                                </div>
                            </div>

                            <div class="checkbox-options">
                                <div class="form-check form-check-success">
                                    <label class="form-check-label" for="isDocumentCorrect">

                                        Uploaded Documents Checked
                                        {{-- &amp; Found Correct --}}
                                    </label>
                                    <input class="form-check-input required-for-approve"
                                        @if ($checkList && $checkList->is_uploaded_doc_checked == 1) checked disabled @endif
                                        name="is_uploaded_doc_checked" type="checkbox" value="1"
                                        id="isDocumentCorrect">
                                    <div class="text-danger required-error-message" id="isDocumentCorrectError">
                                        This field is required.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mt-4">
                    <div class="payment-due">
                        <div class="pending-amount-group">
                            <h4 class="pending-title">Pending Dues</h4>
                            <p class="pending-amount">₹ {{ $pendingAmount }}</p>
                        </div>
                        <div class="view-details">
                            <a href="#">View Details</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
    <div class="row border-top-1">
        @if (!empty($latestMovement->remarks))
            <div class="col-lg-12">
                <div class="remark-container">
                    <h4 class="remark-title">Remark</h4>
                    <p class="remark-content"> {{ $latestMovement->remarks }}<span class="author-name">-
                            {{ !empty($latestMovement->assigned_by) ? getUserNamebyId($latestMovement->assigned_by) : '' }}
                            <!--(JE)--></span>, <span
                            class="author-time">{{ date('h:i a - d/m/Y', strtotime($latestMovement->created_at)) }}</span>
                    </p>

                </div>
                @if ($showRevertButton)
                    <div class="revert-btn">
                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#revertModal">Revert <i
                                class="fa-solid fa-reply-all"></i></a>
                    </div>
                @endif
            </div>
        @endif

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
                                                @if ($departmentRoles)
                                                    @foreach ($departmentRoles as $departmentRole)
                                                        <option value="{{ $departmentRole }}">
                                                            {{ $departmentRole }}
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
                                            <textarea name="forwardRemark" id="forwardRemark" class="form-control" placeholder="Remarks" rows="3"
                                                required></textarea>
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

        @if ($roles === 'deputy-lndo')
            @if ($application->Signed_letter)
                <div class="col-lg-4 mt-4">
                    <a href="{{ asset('storage/' . $application->Signed_letter) }}" target="_blank">View
                        Signed
                        Letter</a>
                </div>
            @else
                @if ($application->letter)
                    <div class="col-lg-4 mt-4">
                        <form action="{{ route('uploadSignedLetter') }}" method="POST"
                            enctype="multipart/form-data" id="signedLetterForm">
                            @csrf
                            <input type="hidden" value="{{ $application->application_no }}"
                                name="application_no" />
                            <div class="upload-signed-form">
                                <div class="upload-signed-head">
                                    <h4 class="upload-signed-title">Upload Signed Letter</h4>
                                    <div class="view-generated">
                                        <a target="_blank" href="{{ asset('storage/' . $application->letter) }}">View
                                            Generated Letter</a>
                                    </div>
                                </div>
                                <div class="file-upload-wrapper">
                                    <label class="file-upload-box mb-0">
                                        <!-- <input type="file" class="file-upload-input"> -->
                                        <input type="file" name="signedLetter" class="file-upload-input"
                                            accept=".pdf" id="signedLetter">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                            <h5 class="mb-2">Choose a file or drag & drop it here</h5>
                                            <p class="text-muted mb-0">JPEG, PNG, JPG, and PDF formats, up to
                                                5MB</p>
                                            <span class="browse-file mb-0">Browse File</span>
                                        </div>
                                    </label>
                                    <div class="file-list">
                                        <!-- Files will be listed here -->
                                    </div>
                                </div>
                                <div class="signed-btn">
                                    <!-- <button type="button" class="btn btn-primary upload-signed-submit">Final Submit</button> -->
                                    <button type="button" id="uploadButton" onclick="handleLetterUpload()"
                                        class="btn btn-primary upload-signed-submit">Final Submit</button>

                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        @endif


        @if ($roles === 'CDV')
            @if ($applicationAppointmentLink)
                <div class="col-lg-12 mt-4">
                    <div class="proof-reading-details">
                        <h4 class="proof-reading-details">Proof Reading Details</h4>
                        <div class="proof-reading-content">
                            <p class="appointment-link"><span>Appointment Link:</span>
                                {{ $applicationAppointmentLink['link'] }}
                            </p>
                            @if (isset($applicationAppointmentLink['schedule_date']))
                                <p class="appointment-schedule-date"><span>Schedule Date:</span>
                                    {{ $applicationAppointmentLink['schedule_date'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($applicationAppointmentLink['schedule_date']))
                @if ($currentDateFormatted == $applicationAppointmentLink['schedule_date'])
                    <div class="col-lg-8 mt-4">
                        <a href="{{ route('startProofReading', ['id' => $details->id]) }}?type={{ request()->query('type') }}"
                            target="_blank">
                            <button type="button" id="PropertyIDSearchBtn" class="btn btn-primary ml-2">Start Proof
                                Reading</button>
                        </a>
                    </div>
                @endif
            @endif
        @endif
        @if ($roles === 'section-officer')
            @if ($applicationAppointmentLink)
                <div class="col-lg-12 mt-4">
                    <div class="proof-reading-details">
                        <h4 class="proof-reading-details">Proof Reading Details</h4>
                        <div class="proof-reading-content">
                            <p class="appointment-link"><span>Appointment Link:</span>
                                {{ $applicationAppointmentLink['link'] }}</p>
                            @if (isset($applicationAppointmentLink['schedule_date']))
                                <p class="appointment-schedule-date"><span>Schedule Date:</span>
                                    {{ $applicationAppointmentLink['schedule_date'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                @if (isset($applicationAppointmentLink['schedule_date']))
                    @if ($currentDateFormatted == $applicationAppointmentLink['schedule_date'])
                        <div class="col-lg-8 mt-4">
                            <a href="{{ route('startProofReading', ['id' => $details->id]) }}?type={{ request()->query('type') }}"
                                target="_blank">
                                <button type="button" id="PropertyIDSearchBtn" class="btn btn-primary ml-2">Start Proof
                                    Reading</button>
                            </a>
                        </div>
                    @endif
                @endif
            @endif
        @endif
    </div>
    <!-- <div class="row">
        <div class="d-flex justify-content-end gap-4 col-lg-12">
            <button type="button" class="btn btn-primary" id="approveBtn">Approve</button>
            <button type="button" id="rejectButton" class="btn btn-danger">Reject</button>
        </div>
    </div> -->

    @if (!$application->Signed_letter)
        <div class="row">
            <div class="d-flex justify-content-end gap-4 col-lg-12" id="action-btn-container">
                @if ($showActionButtons)

                    @if ($roles === 'section-officer')
                        {{-- Check if appointment link is not send --}}
                        @if (!$applicationAppointmentLink)
                        <button type="button" class="btn btn-primary"
                            onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend</button>
                        <button type="button"
                            onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)"
                            class="btn btn-warning">Object</button>
                        @endif
                    @endif
                    @if ($roles === 'CDV')
                        @if ($showAppointmentLinkButton)
                            <button type="button" id="sendProofReadingLink" class="btn btn-primary"
                                onclick="handleApplicationAction('PROOFREADINGLINK','{{ $details->application_no }}',this)">Send
                                Proof Reading Link</button>
                            <button type="button"
                                onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)"
                                class="btn btn-warning" id="objectButton">Object</button>
                        @endif

                    @endif

                    @if ($roles === 'deputy-lndo')
                        @if ($showAppointmentLinkButton)
                            <button type="button" id="sendProofReadingLink" class="btn btn-primary"
                                onclick="handleApplicationAction('PROOFREADINGLINK','{{ $details->application_no }}',this)">Send
                                Proof Reading Link</button>
                        @endif
                        @if ($showCreateLetterButtons)
                            <button type="button" class="btn btn-success"
                                onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">CREATE
                                LETTER</button>
                        @endif
                        {{-- @if ($latestAppAction['latest_action'] == 'RECOMMENDED') --}}
                            {{-- <button type="button" class="btn btn-primary" onclick="handleApplicationAction('APPROVE','{{ $details->application_no}}',this)">Approve</button>--}}
                        {{-- @endif --}}
                        @if ($showApproveButton)
                            <button type="button" class="btn btn-primary"
                                onclick="handleApplicationAction('APPROVE','{{ $details->application_no }}',this)">Approve</button>
                        @elseif ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT')
                            <button type="button" class="btn btn-primary"
                                onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend</button>
                            <button type="button" class="btn btn-warning"
                                onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)">Object</button>
                        @endif
                        @if ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT')
                            <button type="button" class="btn btn-danger"
                                onclick="handleApplicationAction('REJECT_APP','{{ $details->application_no }}',this)">Reject</button>
                        @endif
                        {{-- @if ($latestAppAction['latest_action'] == 'RECOMMENDED')
                                <button type="button" class="btn btn-info"
                                    onclick="handleApplicationAction('FOR_TO_DEP','{{ $details->application_no }}',this)">Forward To Department</button>
            @endif --}}

                    @endif
                    @if ($roles === 'lndo')
                        @if ($latestAppAction['latest_action'] == 'RECOMMENDED')
                            <button type="button" class="btn btn-primary"
                                onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommended</button>
                        @endif
                        @if ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT')
                            <button type="button" class="btn btn-warning"
                                onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)">Object</button>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    @endif
    <!-- </form> -->

</div>
