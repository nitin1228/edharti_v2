<div class="mt-3">
    <div class="container-fluid">
        <div class="row g-2">
            <div class="col-lg-12">

                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                @php
                    $stepTwoDocs = config('applicationDocumentType.DOA.documents');
                @endphp
                @foreach ($stepTwoDocs as $document)
                    @php
                        $uploadeddocsWithDocType = isset($stepSecondFinalDocuments)
                            ? collect($stepSecondFinalDocuments)->where('document_type', $document['id'])->all()
                            : [];
                    @endphp
                    @forelse ($uploadeddocsWithDocType as $i => $uploadeddocsWithDoc)
                        <div class="row row-mb-2">
                            <div class="col-lg-1 icons-flex"></div>
                            <div class="col-lg-11 selected-docs-field">
                                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <label for="{{ $document['id'] }}"
                                                class="quesLabel">{{ $document['label'] }}
                                                @if ($document['id'] != 'OtherDocument')
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="file" name="{{ $document['id'] }}" class="form-control {{ $document['id'] == 'OtherDocument' ? 'otherDocuemntByApplicant' : '' }}"
                                                accept="application/pdf" id="{{ $document['id'] }}"
                                                onchange="handleFileUpload(this.files[0], '{{ $document['label'] }}', '{{ $document['id'] }}', 'deed_of_apartment', 'DOA')"
                                                data-name="{{ $document['id'] }}"
                                                data-should-validate="{{ isset($uploadeddocsWithDoc->file_path) ? '1' : '' }}">
                                            <input type="hidden" value="{{ $uploadeddocsWithDoc->id }}"
                                                name="{{ $uploadeddocsWithDoc->id }}"
                                                data-name="{{ $document['id'] }}_oldId" data-repeaterId="id">
                                            <div id="{{ $document['id'] }}Error" class="text-danger text-left"></div>
                                            <a href="{{ asset('storage/' . $uploadeddocsWithDoc->file_path ?? '') }}"
                                                data-document-type="{{ $document['id'] }}" target="_blank"
                                                class="fs-6">View saved document</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="row row-mb-2">
                            <div class="col-lg-1 icons-flex"></div>
                            <div class="col-lg-11 selected-docs-field">
                                <div class="files-sorting-abs"><i class='bx bxs-file'></i></div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group form-box">
                                            <label for="{{ $document['id'] }}"
                                                class="quesLabel">{{ $document['label'] }}
                                                @if ($document['id'] != 'OtherDocument')
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <input type="file" name="{{ $document['id'] }}" class="form-control {{ $document['id'] == 'OtherDocument' ? 'otherDocuemntByApplicant' : '' }}" 
                                                accept="application/pdf" id="{{ $document['id'] }}"
                                                onchange="handleFileUpload(this.files[0], '{{ $document['label'] }}', '{{ $document['id'] }}', 'deed_of_apartment', 'DOA')"
                                                data-name="{{ $document['id'] }}">
                                            <div id="{{ $document['id'] }}Error" class="text-danger text-left"></div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                @endforeach
            </div>
            <div class="row row-mb-2">
                <div class="col-lg-12">
                    <h6 class="mt-3 mb-0" id="LUCHideTitle">Terms & Conditions</h6>
                    <ul class="consent-agree">

                        <li>
                            Processing fee {{ getApplicationCharge(getServiceType('DOA')) }} /- Rs. is non-refundable.
                        </li>
                    </ul>
                    <div class="form-check form-group">
                        @if (isset($application))
                            <input class="form-check-input" name="agreeConsent" type="checkbox" id="agreeDOAConsent">
                        @else
                            <input class="form-check-input" name="agreeConsent" type="checkbox" id="agreeDOAConsent">
                        @endif

                        <label class="form-check-label" for="doaagreeconsent">I agree, all the
                            information provided by me is accurate to the best of my knowledge. I
                            take full responsibility for any issues or failures that may arise from
                            its use.</label>

                        <div id="agreeDOAConsentError" class="text-danger text-left"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
