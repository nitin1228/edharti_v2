<div class="row border-top-1">
    <!-- For Showing Latest Remark and Showing Revert Button START ********************************************************* -->
    @if (!empty($latestMovement->remarks))
        <div class="col-lg-12">
            <div class="remark-container">
                <h4 class="remark-title">Remark</h4>
                <p class="remark-content"> {{ $latestMovement->remarks }}<span class="author-name">-
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

    {{--  display uploaded letter  --}}

    @if ($application->letter && !$application->Signed_letter && $roles !== 'deputy-lndo')
        <div class="col-lg-4 mt-4 text-end">
            <a href="{{ asset('storage/' . $application->letter) }}" target="_blank">View Generated Letter</a>
        </div>
    @endif


    <!-- For Deputy L&do Role  START************************************************************************************ -->
    @if ($roles === 'deputy-lndo')
        @if ($application->Signed_letter)
            <div class="col-lg-4 mt-4 text-end">
                <a href="{{ asset('storage/' . $application->Signed_letter) }}" target="_blank">View Signed Letter</a>
            </div>
        @else
            @if ($showApproveButton && $application->letter)
                <div class="col-lg-4 mt-4">
                    
                    <form action="{{ route('uploadSignedLetter') }}" method="POST" enctype="multipart/form-data"
                        id="signedLetterForm">
                        @csrf
                        <input type="hidden" value="{{ $application->application_no }}" name="application_no" />
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
                                    <input type="file" name="signedLetter" class="file-upload-input" accept=".pdf"
                                        id="signedLetter">
                                    <div class="upload-content">
                                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        <h5 class="mb-2">Choose a file or drag & drop it here</h5>
                                        <p class="text-muted mb-0">Only .pdf files up to 5MB</p>
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
                </div>
            @endif
        @endif
    @endif
    <!-- For Deputy L&do Role END************************************************************************************ -->

    <!-- For Section Officer Role START************************************************************************************ -->
    @if ($roles === 'section-officer')
        @if ($showCreateLetterButtons)
            @if ($application->letter)
                <div class="col-lg-8 mt-4">
                    <button type="button" class="btn btn-success"
                        onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Regenerate Draft Letter</button>
                </div>
            @else
                {{-- @if(customNumFormat($pendingAmount) > 0)
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


</div>
<!-- All Action Buttons Started START *********************************************************************************** -->
@if (!$application->Signed_letter)
    <div class="row">
        <div class="d-flex justify-content-end gap-4 col-lg-12" id="action-btn-container">
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


                @if ($roles === 'deputy-lndo' && !$showApproveButton)
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
                    @if (
                        $latestAppAction &&
                            ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT'))
                        <button type="button" class="btn btn-primary"
                            onclick="handleApplicationAction('RECOMMENDED','{{ $details->application_no }}',this)">Recommend</button>
                        <button type="button" class="btn btn-warning"
                            onclick="handleApplicationAction('OBJECT','{{ $details->application_no }}',this)">Object</button>
                    @endif
                    @if (
                        $latestAppAction &&
                            ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT'))
                        <button type="button" class="btn btn-danger"
                            onclick="handleApplicationAction('REJECT_APP','{{ $details->application_no }}',this)">Reject</button>
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
        @else
            @if ($roles === 'deputy-lndo' && $showApproveButton)
                <div class="row">
                    <div class="d-flex justify-content-end gap-4 col-lg-12" id="action-btn-container">
                        <button type="button" class="btn btn-primary"
                            onclick="handleApplicationAction('APPROVE','{{ $details->application_no }}',this)">Approve</button>

                        @if (
                            $latestAppAction &&
                                ($latestAppAction['latest_action'] == 'RECOMMENDED' || $latestAppAction['latest_action'] == 'OBJECT'))
                            <button type="button" class="btn btn-danger"
                                onclick="handleApplicationAction('REJECT_APP','{{ $details->application_no }}',this)">Reject</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif
