<!DOCTYPE html>
<html>

<head>
    <title>Mutation Letter PDF</title>



    
@if(auth()->check() && auth()->user()->hasRole('section-officer'))
        <style>
            body::after {
                content: "DRAFT";
                position: fixed;
                top: 30%;
                left: 50%;
                transform: translate(-50%, -30%) rotate(-15deg);
                font-size: 8rem;
                font-weight: 900;
                color: rgba(240, 114, 114, 0.19); /* Light gray and transparent */
                border: 5px solid rgba(240, 114, 114, 0.19);
                padding: 1rem 2rem;
                z-index: 2;
                white-space: nowrap;
                pointer-events: none; /* Let clicks pass through */
            }
            .content {
                position: relative;
                z-index: 1; /* Ensure content is above the watermark */
                padding: 2rem;
            }
        </style>
    @endif
    <style>
        @page {
            margin: 15px;
            background: transparent;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* font-family: sans-serif !important;e */
            margin: 0;
            padding: 0;
            position: relative;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("{{ public_path('assets/images/water-mark.png') }}") repeat;
            transform: rotate(-30deg);
            opacity: 0.1;
            z-index: -99;
        }

        body::before {
            content: "";
            position: fixed;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url(assets/images/water-mark-emblem.png) center center no-repeat;
            background-size: 300px;
            opacity: 0.2;
        }

      

        .emblem-div {
            width: 100%;
            text-align: center;
        }

        .emblem {
            display: inline-block;
            margin: auto;
        }

        .title-main {
            color: navy;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .title-sub {
            color: navy;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .part-title {
            background-color: #1fa1a2;
            color: white;
            font-size: 14px;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            /* margin-top: 10px; */
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .content-wrap {
            margin-right: 30px;
            margin-left: 30px;
        }

        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }

        .note-bott {
            margin-bottom: 10px;
        }

        ol {
            padding-left: 20px;
            margin-top: 0;
        }

        ol li {
            font-size: 10px;
            line-height: 20px;
        }

        .hidden-table {
            margin-bottom: 0;
        }

        .hidden-table th {
            border: 0;
            font-size: 12px;
            vertical-align: top;
            margin: 0 0 10px;
            padding: 5px 5px;
        }

        .hidden-table,
        .hidden-table td {
            border: 0;
            font-size: 10px;
            vertical-align: top;
            margin: 0 0 10px;
            padding: 5px 5px;
        }

        .hidden-table th p,
        .hidden-table td p {
            margin: 0;
        }

        .hidden-header {
            margin-bottom: 0;
        }

        .hidden-header,
        .hidden-header td {
            border: 0;
            font-size: 10px;
            margin: 0 0 10px;
            padding: 0;
        }

        .hidden-header th p,
        .hidden-header td p,
        .hidden-header td h1 {
            margin: 0;
        }

        .text-cap {
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="watermark"></div>
    <!-- Emblem -->
    <!-- <div class="emblem-div">
        <img src="assets/images/emblem.jpg" width="40" alt="Emblem" class="emblem">
    </div> -->

    <!-- Title -->
    <!-- <h1 class="title-main">Land And Development Office</h1>
    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
    <h2 class="title-sub">Government of India</h2> -->
    <!-- <h1 class="title-main">Government of India</h1>
    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>
    <h1 class="title-main">Land And Development Office</h1> -->
    <table class="hidden-header">
        <tr>
            <td style="width:30%">&nbsp;</td>
            <td style="text-align:center;width:40%">
                <div class="emblem-div">
                    <img src="assets/images/emblem.jpg" width="40" alt="Emblem" class="emblem">
                </div>
                <h1 class="title-main">Government of India</h1>
                <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>
                <h1 class="title-main">Land And Development Office</h1>
            </td>
            <!-- <td style="width:30%; text-align:center;vertical-align: middle;">
                <img src="assets/images/ldo_mohua_qr.png" width="100" alt="Emblem" class="emblem">
            </td> -->
            <td style="width:30%; text-align:center;vertical-align: middle;"><img src="qrcode/{{$filename}}" alt="QR Code" width="100" height="100"></td>
        </tr>
    </table>

    <!-- <p style="text-align: center; font-size: 14px; color:#116d6e; font-weight: bold; margin-top: 30px;">
        Payment Receipt
    </p> -->

    <!-- Applicant Details -->
    <div class="part-title">
         @if(auth()->check() && auth()->user()->hasRole('section-officer'))
                DRAFT MUTATION LETTER
            @else
                MUTATION LETTER
            @endif
        
    </div>
    <table class="hidden-table">
        <tbody>
            <tr>
                <th colspan="2" style="width: 50%;">Property Details</th>
                <th colspan="2" style="width: 50%;">Lease / Ownership Details</th>
            </tr>
        </tbody>
        <tr>
            <td>Property ID: <span>{{ $propertyId }}
                    ({{ $uniquePropertyId }})</span></td>
            <td style="width: 10%;">&nbsp;</td>
            <td>Lease Execution Date / Conveyance<br />Deed Execution Date:
                <span>{{ date('d-m-Y', strtotime($leaseDetails->doe)) }}</span>
            </td>
            <td style="width: 10%;">&nbsp;</td>
        </tr>
        <tr>
            <td>Block No.: <span>{{ $blockNo }}</span></td>
            <td>&nbsp;</td>
            <td>Registered on: <span></span></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Plot No.: <span>{{ $plotNo }}</span></td>
            <td>&nbsp;</td>
            <td>In favour of: <span>{{ $applicationData->name_as_per_lease_conv_deed }}</span></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>FlatNo./ FloorNo./<br />ShopNo. <span></span></td>
            <td>&nbsp;</td>
            <th valign="baseline" style="padding-top: 20px; background:none;"><strong>Registration Details:</strong>
            </th>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Known As: <span>{{$knownAs}}</span></td>
            <td>&nbsp;</td>
            <td>Date: <span>{{ date('d-m-Y', strtotime($applicationData->reg_date_as_per_lease_conv_deed)) }}</span>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Area (in sqm): <span>{{$plotArea}}</span></td>
            <td>&nbsp;</td>
            <td>Reg. No.: <span>{{ $applicationData->reg_no_as_per_lease_conv_deed }}</span>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Status:(FH/LH) <span>
                    {{$propertyStatus}}
                </span></td>
            <td>
                &nbsp;
            </td>
            <td>Book No.: <span>{{ $applicationData->book_no_as_per_lease_conv_deed }}</span>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Vol. No.: <span>{{ $applicationData->volume_no_as_per_lease_conv_deed }}</span>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Page No.: <span>{{ $applicationData->page_no_as_per_deed }}</span><span></span></td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <!-- Property Details -->
    <div class="part-title">LAST LESSEE(S) / OWNERS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">S. No.</th>
                <th>Name</th>
                <th>S/O, W/O, D/O</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($latestLesseeDetails as $index => $lessee)
                <tr>
                    <td> <strong>{{ $index + 1 }}.</strong></td>
                    <td>{{ $lessee->lessee_name ?? '' }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $lessee->lessee_age ?? '' }}</td>
                    <td>{{ $lessee->property_share ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; border: 1px solid #ddd; color: #777;">
                        No lessee records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="part-title">PRESENT LESSEE(S) / OWNERS</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">S. No.</th>
                <th>Name</th>
                <th>S/O, W/O, D/O</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Share</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1.</strong></td>
                <td>{{ $applicantDetail->name }}</td>
                <td>{{ $applicantDetail->prefix }} {{ $applicantDetail->second_name }}</td>
                <td>{{ $applicantDetail->gender }}</td>
                <td>{{ $applicantUserDetail->age }} years</td>
                <td>{{ $applicantShare }}</td>
            </tr>
            @foreach ($coapplicants as $index => $coapplicant)
                <tr>
                    <td><strong>{{ $index + 2 }}.</strong></td>
                    <td>{{ $coapplicant->co_applicant_name }}</td>
                    <td>{{ $coapplicant->prefix }} {{ $coapplicant->co_applicant_father_name }}</td>
                    <td>{{ $coapplicant->co_applicant_gender }}</td>
                    <td>
                        @php
                            // Calculate age from date of birth (co_applicant_age field)
                            $dob = \Carbon\Carbon::parse($coapplicant->co_applicant_age);
                            $age = $dob->age;
                        @endphp
                        {{ $age }} years
                    </td>
                    <td>
                        <!-- Share field is not present in your data -->
                        <!-- You might need to add this field to your coapplicants table or calculate it -->
                         {{ $coapplicant->share }}
                    </td>
                </tr>
            @endforeach

            @php
                $startIndex = count($coapplicants) + 2;
            @endphp

            @foreach ($latestApplicationExtraApplicant as $index => $extraApplicant)
                <tr>
                    <td><strong>{{ $startIndex + $index }}.</strong></td>
                    <td>{{ $extraApplicant->name }}</td>
                    <td>{{ $extraApplicant->relation }}</td>
                    <td>{{ $extraApplicant->gender }}</td>
                    <td>{{ $extraApplicant->age }}</td>
                    <td>{{ $extraApplicant->share }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- <div class="part-title">APPLICATION DETAILS</div> -->
    <div class="part-title text-cap">The mutation has been carried out in the name of the applicant on the basis of the
        following documents.</div>
    {{-- <table>
        <tbody>
            <tr>
                <td>Name of Docuemnt</td>
                <td>Docuemnt name comes here</td>
                <td>Date of Docuemnt</td>
                <td>Docuemnt date comes here</td>
            </tr>
            <tr>
                <td>Name of Docuemnt</td>
                <td></td>
                <td>Date of Docuemnt</td>
                <td></td>
            </tr>
            <tr>
                <td>Name of Docuemnt</td>
                <td></td>
                <td>Date of Docuemnt</td>
                <td></td>
            </tr>
            <tr>
                <td>Name of Docuemnt</td>
                <td></td>
                <td>Date of Docuemnt</td>
                <td></td>
            </tr>
        </tbody>
    </table> --}}

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th width="25%">Name of Document</th>
                <th width="25%">Document Name</th>
                <th width="25%">Date of Document</th>
                <th width="25%">Document Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applicationDocumentDetails as $document)
                <tr>
                    <td><strong>Name of Document</strong></td>
                    <td>{{ $document->document_name ?? 'N/A' }}</td>
                    <td><strong>Date of Document</strong></td>
                    <td>
                        @if ($document->document_date)
                            {{ \Carbon\Carbon::parse($document->document_date)->format('d-m-Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No documents found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="hidden-table">
        <tbody>
            <tr>
                <td style="text-align: right;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: right;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: right;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: right;">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: right;">……………………………………………………</td>
            </tr>
            <tr>
                <td style="text-align: right;">
                    <strong>
                        Dy. Land & Development Officer<br />

                    </strong>
                </td>
            </tr>
        </tbody>
    </table>

    <h4 class="note-bott">Note:</h4>
    <ol>
        <li>The above mutation has been carried out on the basis of the application and the documents provided by the
            applicants.</li>
        <li>Any discrepancies, concealment of facts, misrepresentation etc. will lead to cancellation of the mutation.
        </li>
        <li>Claims and Objections against this mutation shall be filed in the relevant courts. The decision in the
            litigation matter shall be submitted to Land & Development Office for execution.</li>
        <li>The updated lessees/owners shall be bound by the Lease/Conveyance Deed.</li>
        <li>Sub-divisions of the property will not be allowed at any stage / they will remain as co-lessee, JOINTLY.</li>
    </ol>


</body>

</html>
