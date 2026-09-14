@if(isset($downloading))
<div class="part-title mt-2">
    <h5>Details of Documents</h5>
</div>
<div class="part-details">
    <div class="container-fluid pb-3">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-bordered theme-table" style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <thead>
                            <tr style="background-color: #f8f9fa; border: 1px solid #000;">
                                <th width="2%" style="border: 1px solid #000; padding: 8px; text-align: center;">S.No</th>
                                <th style="border: 1px solid #000; padding: 8px;">Document Name</th>
                                @if ($roles != 'applicant')
                                    <th style="border: 1px solid #000; padding: 8px; text-align: center;">Action by SO</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Required Documents Section -->
                            <tr>
                                @php
                                    $colspan = $roles != 'applicant' ? 3 : 2;
                                @endphp
                                <td colspan="{{ $colspan }}" style="border: 1px solid #000; padding: 10px; background-color: #e9ecef; font-weight: bold;">
                                    <h4 style="margin: 0; font-size: 16px;">Required Documents</h4>
                                </td>
                            </tr>
                            
                            @php
                                $stepTwoDocs = config('applicationDocumentType.NOC.Required');
                                $counter = 0;
                            @endphp
                            
                            @foreach ($stepTwoDocs as $document)
                                @php
                                    $uploadedDocuments = [];
                                    if (!empty($details->documentFinal)) {
                                        $uploadedDocuments = $details->documentFinal
                                            ->where('document_type', $document['id'])
                                            ->all();
                                    }
                                @endphp
                                
                                @foreach ($uploadedDocuments as $ud)
                                    <tr style="border: 1px solid #000;">
                                        <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;">{{ ++$counter }}.</td>
                                        <td style="border: 1px solid #000; padding: 8px; vertical-align: top;">
                                            <div style="margin-bottom: 5px; font-weight: bold;">
                                                {{ $ud['title'] }}
                                            </div>
                                            
                                            @if ($ud->documentKeys->count() > 0)
                                                <div style="margin-top: 8px; font-size: 12px;">
                                                    @foreach ($ud->documentKeys as $data)
                                                        @if (isset($document['inputs'][$data->key]['label']))
                                                            @php
                                                                $value = $data->value;
                                                                $isDate = strtotime($value) !== false;
                                                                if($isDate){
                                                                    try{
                                                                        $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                    } catch (\Exception $e){
                                                                        $value = $data->value;
                                                                    }
                                                                }
                                                            @endphp
                                                            <div style="margin-bottom: 2px;">
                                                                <strong>{{ $document['inputs'][$data->key]['label'] }}:</strong> {{ $value }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        @if ($roles != 'applicant')
                                            <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;">
                                                @if ($checkList && $checkList->is_uploaded_doc_checked == 1)
                                                    <span style="color: green; font-weight: bold;">✓ Checked</span>
                                                @else
                                                    <span style="color: #6c757d;">Not Checked</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach

                            <!-- Additional Documents By Applicant Section -->
                            <tr>
                                <td colspan="{{ $colspan }}" style="border: 1px solid #000; padding: 10px; background-color: #e9ecef; font-weight: bold;">
                                    <h4 style="margin: 0; font-size: 16px;">Additional Documents By Applicant</h4>
                                </td>
                            </tr>
                            
                            @php
                                $applicantAdditionalDocuments = [];
                                $counter = 0;
                                if (!empty($details->documentFinal)) {
                                    $applicantAdditionalDocuments = $details->documentFinal
                                        ->where('document_type', 'AdditionalDocument')
                                        ->whereNotNull('file_path')
                                        ->all();
                                }
                                $uplodedDocCount = count($applicantAdditionalDocuments);
                            @endphp
                            
                            @if ($uplodedDocCount > 0)
                                @foreach ($applicantAdditionalDocuments as $ud)
                                    <tr style="border: 1px solid #000;">
                                        <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;">{{ ++$counter }}.</td>
                                        <td style="border: 1px solid #000; padding: 8px; vertical-align: top;">
                                            <div style="margin-bottom: 5px; font-weight: bold;">
                                                {{ $ud['title'] }}
                                            </div>
                                            
                                            @if ($ud->documentKeys->count() > 0)
                                                <div style="margin-top: 8px; font-size: 12px;">
                                                    @foreach ($ud->documentKeys as $data)
                                                        @if (isset($document['inputs'][$data->key]['label']))
                                                            @php
                                                                $value = $data->value;
                                                                $isDate = strtotime($value) !== false;
                                                                if($isDate){
                                                                    try{
                                                                        $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                    } catch (\Exception $e){
                                                                        $value = $data->value;
                                                                    }
                                                                }
                                                            @endphp
                                                            <div style="margin-bottom: 2px;">
                                                                <strong>{{ $document['inputs'][$data->key]['label'] }}:</strong> {{ $value }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        @if ($roles != 'applicant')
                                            <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: top;">
                                                @if ($checkList && $checkList->is_uploaded_doc_checked == 1)
                                                    <span style="color: green; font-weight: bold;">✓ Checked</span>
                                                @else
                                                    <span style="color: #6c757d;">Not Checked</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="{{ $colspan }}" style="border: 1px solid #000; padding: 15px; text-align: center;">
                                        <p style="margin: 0; color: #6c757d;">No Documents Available</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else

<div class="part-title mt-2">
    <h5>Details of Documents</h5>
</div>
<div class="part-details">
    <div class="container-fluid pb-3">
        <div class="row">
            <div class="col-lg-12">

                <div class="table-responsive">
                    <table class="table table-bordered theme-table">
                        <thead>
                            <tr>
                                <th width="2%">S.No</th>
                                <th>Document Name</th>
                                @if ($roles != 'applicant')
                                    <th>Action by SO</th>
                                @endif

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="document-type-row">
                                    <h4 class="doc-type-title">Required Documents</h4>
                                </td>
                            </tr>
                            @php
                                $stepTwoDocs = config('applicationDocumentType.NOC.Required');
                                $counter = 0;
                            @endphp
                            @foreach ($stepTwoDocs as $document)
                                @php
                                    $uploadedDocuments = [];
                                    if (!empty($details->documentFinal)) {
                                        $uploadedDocuments = $details->documentFinal
                                            ->where('document_type', $document['id'])
                                            ->all();
                                    }
                                    $uplodedDocCount = count($uploadedDocuments);
                                @endphp
                                @foreach ($uploadedDocuments as $ud)
                                    <tr id="{{ $ud['id'] }}">
                                        <td>{{ ++$counter }}.</td>
                                        <td>
                                            <span class="doc-name">
                                                {{ $ud['title'] }} <a
                                                    href="{{ asset('storage/' . $ud['file_path'] ?? '') }}"
                                                    target="_blank" class="text-danger"><i
                                                        class="fa-solid fa-file-pdf ml-2"></i></a>
                                            </span>



                                            @if ($ud->documentKeys->count() > 0)
                                                <div class="required-info">
                                                    <ul class="required-info-list">
                                                        @foreach ($ud->documentKeys as $data)
                                                            @if (isset($document['inputs'][$data->key]['label']))

                                                                @php
                                                                    $value = $data->value;
                                                                    $isDate = strtotime($value) !== false;
                                                                    if($isDate){
                                                                        try{
                                                                            $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                        } catch (\Exception $e){
                                                                            $value = $data->value;
                                                                        }
                                                                    }
                                                                @endphp
                                                                <li>{{ $document['inputs'][$data->key]['label'] }}:
                                                                    {{ $data->value }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>

                                        @if ($roles != 'applicant')
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input required-for-approve property-document-approval-chk"
                                                        type="checkbox" name="checkedAction" id="checkedAction"
                                                        @if ($checkList && $checkList->is_uploaded_doc_checked == 1) checked disabled @endif
                                                        @if ($roles != 'section-officer' &&
                                                                 $roles != 'assistant-section-officer' &&
                                                                 auth()->user()->can('can.approve.application.mis.scanned.document')) disabled @endif>
                                                    <label class="form-check-label" for="checkedAction">Checked</label>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach

                            <tr>
                                <td colspan="3" class="document-type-row">
                                    <h4 class="doc-type-title">Additional Documents By Applicant</h4>
                                </td>
                            </tr>
                            @php
                                $applicantAdditionalDocuments = [];
                                $counter = 0;
                                if (!empty($details->documentFinal)) {
                                    $applicantAdditionalDocuments = $details->documentFinal
                                        ->where('document_type', 'AdditionalDocument')
                                        ->whereNotNull('file_path')
                                        ->all();
                                }
                                $uplodedDocCount = count($applicantAdditionalDocuments);
                            @endphp
                            @if ($uplodedDocCount > 0)
                                @foreach ($applicantAdditionalDocuments as $ud)
                                    <tr id="{{ $ud['id'] }}">
                                        <td>{{ ++$counter }}.</td>


                                        <td>
                                            <span class="doc-name">{{ $ud['title'] }} <a
                                                    href="{{ asset('storage/' . $ud['file_path'] ?? '') }}"
                                                    target="_blank" class="text-danger"><i
                                                        class="fa-solid fa-file-pdf ml-2"></i></a></span>
                                            @if ($ud->documentKeys->count() > 0)
                                                <div class="required-doc">
                                                    <ul class="required-list">
                                                        @foreach ($ud->documentKeys as $data)
                                                            @if (isset($document['inputs'][$data->key]['label']))

                                                                @php
                                                                    $value = $data->value;
                                                                    $isDate = strtotime($value) !== false;
                                                                    if($isDate){
                                                                        try{
                                                                            $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                                                                        } catch (\Exception $e){
                                                                            $value = $data->value;
                                                                        }
                                                                    }
                                                                @endphp
                                                                <li>{{ $document['inputs'][$data->key]['label'] }}:
                                                                    {{ $data->value }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>

                                        @if ($roles != 'applicant')
                                            <td>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input required-for-approve property-document-approval-chk"
                                                        type="checkbox" name="checkedAction" id="checkedAction"
                                                        @if ($checkList && $checkList->is_uploaded_doc_checked == 1) checked disabled @endif
                                                        @if ($roles != 'section-officer' &&
                                                                 $roles != 'assistant-section-officer' &&
                                                                 auth()->user()->can('can.approve.application.mis.scanned.document')) disabled @endif>
                                                    <label class="form-check-label" for="checkedAction">Checked</label>
                                                </div>
                                            </td>
                                        @endif


                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="">
                                        <p class="text-center">No Documents Available</p>
                                    </td>
                                </tr>
                            @endif

                            {{-- <tr>
                                <td colspan="5" class="document-type-row">
                                    <h4 class="doc-type-title">Additional Documents By SO</h4>
                                </td>
                            </tr>
                            @php
                                $soAdditionalDocuments = [];
                                if (!empty($details->documentFinal)) {
                                    $soAdditionalDocuments = $details->documentFinal
                                        ->where('document_type', 'AdditionalDocument')
                                        ->whereNull('file_path')
                                        ->whereNotNull('office_file_path')
                                        ->all();
                                }
                                $uplodedDocCount = count($soAdditionalDocuments);
                                $count = 1;
                            @endphp
                            @if ($uplodedDocCount > 0)
                                @foreach ($soAdditionalDocuments as $index => $ud)
                                    <tr>
                                        <td>{{ $count }}</td>
                                        <td colspan="4" style="text-align: left;">
                                            <span class="doc-name">{{ $ud['title'] }} <a
                                                    href="{{ asset('storage/' . $ud['office_file_path'] ?? '') }}"
                                                    target="_blank" class="text-danger"><i
                                                        class="fa-solid fa-file-pdf ml-2"></i></a></span>
                                            @if ($ud->documentKeys->count() > 0)
                                                <div class="required-doc">
                                                    <ul class="required-list">
                                                        @foreach ($ud->documentKeys as $data)
                                                            <li>{{ $document['inputs'][$data->key]['label'] }}:
                                                                {{ $data->value }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </td>

                                    </tr>
                                    @php
                                        $count = $count + 1;
                                    @endphp
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="">
                                        <p class="text-center">No Documents Available.</p>
                                    </td>
                                </tr>
                            @endif --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endif
