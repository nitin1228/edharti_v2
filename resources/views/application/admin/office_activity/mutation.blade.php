<style>
    .document-content{
        background: #106f701c;
        font-size: 17px;
        padding: 10px 12px;
        color: #321e1e;
        font-weight: 600;
    }
</style>
<div class="row border-top-1">
        <!-- For Showing Latest Remark and Showing Revert Button START ********************************************************* -->
        @if (!empty($latestMovement->remarks))
            <div class="col-lg-12">
                <div class="remark-container">
                    <h4 class="remark-title">Remark</h4>
                    
                    <p class="remark-content"> {{ $latestMovement['remarks'] }}<span class="author-name">-
                            {{ !empty($latestMovement->assigned_by) ? getUserNamebyId($latestMovement->assigned_by) : '' }}
                            <!--(JE)--></span>, <span
                            class="author-time">{{ date('d-m-Y h:i a', strtotime($latestMovement->created_at)) }}</span>
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
        <!-- For Showing Latest Remark and Showing Revert Button END ********************************************************* -->

        <!-- For Forwarding the application START ******************************************************************** -->
        @include('application.admin.office_activity.forward_application')
        <!-- For Forwarding the application END ******************************************************************** -->



          <!-- For Deputy L&do Role  START************************************************************************************ -->
       
            @if ($application->Signed_letter)
                <div class="col-lg-4 mt-4 text-end">
                    <a href="{{ asset('storage/' . $application->Signed_letter) }}" target="_blank">View
                        Signed
                        Letter</a>
                </div>
            @else
                @if ($application->letter)
                    <div class="col-lg-4 mt-4">
                        <div class="view-generated text-end">
                            <a target="_blank" href="{{ asset('storage/' . $application->letter).'?t='.time() }}">View
                                Generated Letter</a>
                        </div>
                        @if ($roles === 'deputy-lndo')
                            @if($showUploadSignedLetter)
                                @include('application/admin/office_activity/upload-signed-letter')
                                {{--
                                <form action="{{ route('uploadSignedLetter') }}" method="POST"
                                    enctype="multipart/form-data" id="signedLetterForm">
                                    @csrf
                                    <input type="hidden" value="{{ $application->application_no }}"
                                        name="application_no" />
                                    <div class="upload-signed-form">
                                        <div class="upload-signed-head">
                                            <h4 class="upload-signed-title">Upload Signed Letter</h4>
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
                                            <div id="signedLetterError" class="text-danger" style="display: none;">Please upload a signed letter.</div>
                                            <div class="file-list">
                                                <!-- Files will be listed here -->
                                            </div>
                                        </div>
                                        <div class="signed-btn">
                                            <!-- <button type="button" class="btn btn-primary upload-signed-submit">Final Submit</button> -->
                                            <button type="button" id="uploadButton" onclick="handleLetterUpload()"
                                                class="btn btn-primary upload-signed-submit">Submit</button>

                                        </div>
                                    </div>
                                </form>
                                --}}
                            @endif
                        @endif
                    </div>
                @endif
            @endif
        <!-- For Deputy L&do Role END************************************************************************************ -->


        <!-- For Section Officer Role START************************************************************************************ -->
        @if ($roles === 'section-officer' || $roles === 'CDV')
            @if ($showCreateLetterButtons)
             <div class="container" id="documentSection">
                <p class="document-content">Mention Supporting Documents for Mutation</p>
                <!-- Row 1 -->
                <div class="row g-4 document-row" data-row="1">
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Name of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="text" name="documents[0][name]" class="form-control alpha-only document-name"
                            placeholder="Document Name" value="{{ $applicationDocumentDetails[0]['name'] ?? '' }}">
                        <!-- Add data-error attribute -->
                        <div class="invalid-feedback document-error" data-error="name"></div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Date of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="date" name="documents[0][date]" class="form-control document-date"
                            max="{{ date('Y-m-d') }}" value="{{ $applicationDocumentDetails[0]['date'] ?? '' }}">
                        <!-- Add data-error attribute -->
                        <div class="invalid-feedback document-error" data-error="date"></div>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="row g-4 document-row" data-row="2">
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Name of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="text" name="documents[1][name]" class="form-control alpha-only document-name"
                            placeholder="Document Name" value="{{ $applicationDocumentDetails[1]['name'] ?? '' }}">
                        <div class="invalid-feedback document-error" data-error="name"></div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Date of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="date" name="documents[1][date]" class="form-control document-date"
                            max="{{ date('Y-m-d') }}" value="{{ $applicationDocumentDetails[1]['date'] ?? '' }}">
                        <div class="invalid-feedback document-error" data-error="date"></div>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="row g-4 document-row" data-row="3">
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Name of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="text" name="documents[2][name]" class="form-control alpha-only document-name"
                            placeholder="Document Name" value="{{ $applicationDocumentDetails[2]['name'] ?? '' }}">
                        <div class="invalid-feedback document-error" data-error="name"></div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Date of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="date" name="documents[2][date]" class="form-control document-date"
                            max="{{ date('Y-m-d') }}" value="{{ $applicationDocumentDetails[2]['date'] ?? '' }}">
                        <div class="invalid-feedback document-error" data-error="date"></div>
                    </div>
                </div>

                <!-- Row 4 -->
                <div class="row g-4 document-row" data-row="4">
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Name of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="text" name="documents[3][name]" class="form-control alpha-only document-name"
                            placeholder="Document Name" value="{{ $applicationDocumentDetails[3]['name'] ?? '' }}">
                        <div class="invalid-feedback document-error" data-error="name"></div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold">Date of Document<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <input type="date" name="documents[3][date]" class="form-control document-date"
                            max="{{ date('Y-m-d') }}" value="{{ $applicationDocumentDetails[3]['date'] ?? '' }}">
                        <div class="invalid-feedback document-error" data-error="date"></div>
                    </div>
                </div>
            </div>
                @if ($application->letter)
                    <div class="col-lg-8 mt-4">
                        <button type="button" class="btn btn-success" onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Regenerate Draft Letter</button>
                    </div>
                @else
                {{-- @if($pendingAmount > 0)
                    <div class="invoice overflow-auto" style="min-height: 0;">
                        <div style="min-width: 600px">
                            <main style="padding-bottom: 0;">
                                <div class="notices">
                                    <div>IMPORTANT NOTE:</div>
                                    <div class="notice text-danger">There is oustanding dues on this property, so application can not be processed further. Get it cleared first.</div>
                                </div>
                            </main>
                        </div>
                    </div>
                @else --}}
                    <div class="col-lg-8 mt-4">
                        <button type="button" class="btn btn-success" onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Generate Draft Letter</button>
                    </div>
                {{-- @endif --}}
                @endif
            @endif
        @endif
        <!-- For Section Officer Role END************************************************************************************ -->


        <!-- For CDV Role ************************************************************************************ -->
        @if ($roles === 'CDV')
        @include('application.admin.office_activity.proof_reading_section')
        {{--
            @if ($applicationAppointmentLink && $applicationAppointmentLink->is_active == 1)
                <div class="col-lg-12 mt-4">
                    <div class="proof-reading-details">
                        <h4 class="proof-reading-details">Proof Reading Details</h4>
                        <div class="proof-reading-content">
                            <p class="appointment-link"><span>Appointment Link:</span>
                                {{ $applicationAppointmentLink['link'] }}
                            </p>
                            @if (isset($applicationAppointmentLink['valid_till']))
                                <p class="appointment-schedule-date"><span>Valid Till:</span>
                                    {{ $applicationAppointmentLink['valid_till'] }}
                                </p>
                            @endif
                            @if (isset($applicationAppointmentLink['schedule_date']))
                                <p class="appointment-schedule-date mt-2"><span>Schedule Date:</span>
                                    {{ $applicationAppointmentLink['schedule_date'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
--}}
            @if($application->is_warning_sent == 1)
                <div class="invoice overflow-auto" style="min-height: 0;">
                    <div style="min-width: 600px">
                        <main style="padding-bottom: 0;">
                            <div class="notices">
                                <div>NOTICE:</div>
                                <div class="notice">Warning mail sent on: <b id="warningMailSentDate">{{$application->warning_sent_on}}</b></div>
                            </div>
                        </main>
                    </div>
                </div>
            @endif

            @if (isset($applicationAppointmentLink['schedule_date']) && $showActionButtons)
                
               {{-- @if ($currentDate >= $scheduleDate && $currentDate <= $endDate && $applicationAppointmentLink['is_active'] != 0)--}}
                @if ($currentDate >= $scheduleDate) {{-- added after discussion with dhirah sir - SOURAV Chauhan (7 May 2026) --}}
                    <div class="col-lg-8 mt-4">
                            <button type="button"  data-bs-toggle="modal" data-bs-target="#startProofReadingModal" id="PropertyIDSearchBtn" class="btn btn-primary ml-2">Start Proof
                                Reading</button>
                    </div>
                @endif
            @endif
        @endif
        <!-- For CDV Role END ************************************************************************************ -->


        
    </div>
  
    <!-- All Action Buttons Started START *********************************************************************************** -->
    
        <div class="row">
            <div class="d-flex justify-content-end gap-4 col-lg-12" id="action-btn-container">
        @if (
            ($roles === 'section-officer' || $roles === 'deputy-lndo') &&
                $showActionButtons &&
                empty($application->letter) &&
                !isset($latestAppAction))
            <button type="button" onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)"
                class="btn btn-warning">Object</button>
        @endif
        
                @if ($showActionButtons)
                    @if ($roles === 'section-officer')
                        @if ($application->letter)
                            <button type="button" class="btn btn-primary"
                                onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend</button>
                            <button type="button"
                                onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)"
                                class="btn btn-warning">Object</button>
                        @endif
                    @endif
                    <!-- @if ($roles === 'CDV')
                        @if ($showAppointmentLinkButton)
                            <button type="button" id="sendProofReadingLink" class="btn btn-primary"
                                    onclick="handleApplicationAction('PROOFREADINGLINK', '{{ $details->application_no }}', this)">
                                Send Proof Reading Link
                            </button>
                        @endif

                        @if (!$applicationAppointmentLink || ($applicationAppointmentLink && $applicationAppointmentLink->is_active == 0))
                            <button type="button"
                                    onclick="handleApplicationAction('OBJECT', '{{ $details->application_no }}', this)"
                                    class="btn btn-warning" id="objectButton">
                                Object
                            </button>
                        @endif
                        @if($shoWarningMailButton)
                            <button type="button" class="btn btn-primary" onclick="handleApplicationAction('SENT_WARNING_MAIL','{{ $details->application_no }}',this)">Send Warning Mail</button>
                        @endif
                    @endif -->
                    <!-- Hold the application - SOURAV CHAUHAN (17/Dec/2024) START**************************************************-->
                    <!-- @if ($roles === 'CDV')
                        @if($isAppointmentAttended)
                            @if(getStatusDetailsById( $details->status ?? '' )->item_code != 'HOLD')
                                <button type="button" onclick="handleApplicationAction('HOLD','{{ $details->application_no }}',this)" class="btn btn-warning" id="objectButton">Hold</button>
                                @if($showRecommandForAppoval)
                                    <button type="button" class="btn btn-primary" onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend For Approval</button>
                                @endif
                                <button type="button" onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)" class="btn btn-warning" id="objectButton">Object</button>
                            @endif
                        @endif
                    @endif -->
                    @if ($roles === 'CDV')
                        @if ($showAppointmentLinkButton)
                            <button type="button" id="sendProofReadingLink" class="btn btn-primary"
                                    onclick="handleApplicationAction('PROOFREADINGLINK', '{{ $details->application_no }}', this)">
                                Send Proof Reading Link
                            </button>
                        @endif

                        
                        @if($shoWarningMailButton)
                            <button type="button" class="btn btn-primary" onclick="handleApplicationAction('SENT_WARNING_MAIL','{{ $details->application_no }}',this)">Send Warning Mail</button>
                        @endif
                    
                        <!-- Hold the application - SOURAV CHAUHAN (17/Dec/2024) START**************************************************-->
                    
                        @if($isAppointmentAttended)
                            @if(getStatusDetailsById( $details->status ?? '' )->item_code != 'HOLD')
                                <button type="button" onclick="handleApplicationAction('HOLD','{{ $details->application_no }}',this)" class="btn btn-warning" id="objectButton">Hold</button>
                                @if($showRecommandForAppoval)
                                    <button type="button" class="btn btn-primary" onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend For Approval</button>
                                @endif
                                <button type="button" onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)" class="btn btn-warning" id="objectButton">Object</button>
                            @endif
                        @else
                            @if (!$applicationAppointmentLink || ($applicationAppointmentLink && $applicationAppointmentLink->is_active == 0))
                                <button type="button"
                                        onclick="handleApplicationAction('OBJECT', '{{ $details->application_no }}', this)"
                                        class="btn btn-warning" id="objectButton">
                                    Object
                                </button>
                            @endif
                        @endif
                    @endif
                    <!-- Hold the application - SOURAV CHAUHAN (17/Dec/2024) END**************************************************-->


                    @if ($roles === 'deputy-lndo')
                        @if($applicationRecommendeByCdv && !$isSignedLetterAvailable)
                        @else
                            @if($showApproveButton)
                                <button type="button" class="btn btn-primary" onclick="handleApplicationAction('APPROVE','{{ $details->application_no}}',this)">Approve</button>
                            @elseif (isset($latestAppAction) && ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT'))
                                <button type="button" class="btn btn-primary" onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend</button>
                                <button type="button" class="btn btn-warning" onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)">Object</button>
                            @endif
                            @if (isset($latestAppAction) && ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT'))
                                <button type="button" class="btn btn-danger"
                                    onclick="handleApplicationAction('REJECT_APP','{{ $details->application_no }}',this)">Reject</button>
                            @endif
                        @endif
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

       

        <script>
        let applicantIndex = 0;
        const existingApplicants = @json($extraApplicants);

        document.getElementById('addApplicant')?.addEventListener('click', function () {

            addApplicant();
        });

        if (existingApplicants.length > 0) {
            existingApplicants.forEach(function(applicant) {
                addApplicant(applicant);
            });
        }


        function addApplicant(applicant = {}) {

            const html = `
            <div class="mb-3 applicant-item" style="border:1px solid #ccc;padding:15px;position:relative;">

                <div class="mb-2" style="position:absolute;right:-10px;top:-10px;">
                    @if($roles == 'section-officer')
                        <button type="button" class="btn btn-danger btn-sm removeApplicant">
                            Remove
                        </button>
                    @endif
                </div>

                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label>Name</label>
                        <input type="text"
                            name="applicants[${applicantIndex}][name]"
                            value="${applicant.name ?? ''}"
                            class="form-control applicant-name">
                        <div class="invalid-feedback">Name is required.</div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>S/o D/o Spouse/o</label>
                        <input type="text"
                            name="applicants[${applicantIndex}][relation]"
                            value="${applicant.relation ?? ''}"
                            class="form-control applicant-relation">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Age</label>
                        <input type="number"
                            name="applicants[${applicantIndex}][age]"
                            value="${applicant.age ?? ''}"
                            class="form-control applicant-age">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Gender</label>
                        <select
                            name="applicants[${applicantIndex}][gender]"
                            class="form-control applicant-gender">

                            <option value="">Select</option>
                            <option value="Male" ${applicant.gender === 'Male' ? 'selected' : ''}>Male</option>
                            <option value="Female" ${applicant.gender === 'Female' ? 'selected' : ''}>Female</option>
                            <option value="Other" ${applicant.gender === 'Other' ? 'selected' : ''}>Other</option>

                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Share (%)</label>
                        <input type="text"
                            name="applicants[${applicantIndex}][share]"
                            value="${applicant.share ?? ''}"
                            class="form-control applicant-share">
                             <div class="invalid-feedback">Share is required.</div>
                    </div>

                </div>

            </div>`;

            document.getElementById('applicantContainer')
                .insertAdjacentHTML('beforeend', html);

            applicantIndex++;
        }


        document.getElementById('applicantContainer').addEventListener('click', function(e) {
            if (e.target.classList.contains('removeApplicant')) {
                e.target.closest('.applicant-item').remove();
            }
        });
    </script>
 