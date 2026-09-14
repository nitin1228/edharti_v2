@if($application->Signed_letter =="" )
    <div class="d-flex justify-content-end">
        @switch($details->service_type->item_code)
        @case('CONVERSION')
            <button type="button" class="btn btn-success" onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Generate Finalized Deed</button>
            @break
        @case('NOC')
            <button type="button" class="btn btn-success" onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Generate Finalized NOC</button>
            @break
        @case('SUB_MUT')
            <button type="button" class="btn btn-success" onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Generate Finalized Mutation Letter</button>
            @break
        @default
            <button type="button" class="btn btn-success" onclick="handleApplicationAction('LETTER_GEN','{{ $details->application_no }}',this)">Generate Finalized Letter</button>
        @endswitch
    </div>
@endif
<br><br>
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