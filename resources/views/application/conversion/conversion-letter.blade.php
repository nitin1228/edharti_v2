<!DOCTYPE html>
<html lang="en">

    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- External CSS libraries -->
        <!-- Favicon icon -->
        <link rel="shortcut icon" href="{{ asset('assets/frontend/assets/img/favicon.ico') }}" type="image/x-icon" />

        <!-- Custom Stylesheet -->
        <!-- Custom Stylesheet -->
    </head>
    <style>
        @page {
            margin: 15px 0 0;
        }

        @page :first {
            margin-top: 5px;
            /* No top margin on first page */
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url("{{ public_path('assets/images/water-mark-emblem.png') }}") center center no-repeat;
            opacity: 0.1;
        }
        .watermark{
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
        p{
            font-size:12px;
            margin-bottom:10px;
            text-align: justify;
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
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .title-sub {
            color: navy;
            font-size: 10px;
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
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .content-wrap {
            margin-right: 30px;
            margin-left: 30px;
        }
        .name-sign{
            font-size: 25px;
            text-align: right;
        }
        .qr-img{
            text-align: center;
        }
        ol{
            padding-left: 16px;
        }
        .hidden-table {
            margin-bottom: 0;
        }

        .hidden-table,
        .hidden-table th,
        .hidden-table td {
            border: 0;
            font-size: 12px;
            vertical-align: top;
            margin: 0 0 10px;
            padding: 5px 0;
        }

        .hidden-table th p,
        .hidden-table td p {
            margin: 0;
        }

        .ldo-sign {
            font-size: 12px;
        }
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background-color: #d9d9d9;
            border-top: 1px solid #727272;
            border-bottom: 1px solid #727272;
        }

        .pdf-footer table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .pdf-footer table,
        .pdf-footer table th,
        .pdf-footer table td {
            font-size: 11px;
            border: 0;
            color: #242424;
            padding: 2px 5px 5px;
        }

        /* Optional: Page break example */
        .page-break {
            page-break-before: always;
        }
        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }
    </style>

<body>
    <div class="watermark"></div>
    <!-- Login 8 section start -->
    <div class="content-wrap">

        <!-- Watermark -->
        <!-- <div class="watermark">Land and Development Office</div> -->

        <!-- Emblem Image -->
        <table class="hidden-table">
            <tr>
                <td style="width: 100px;"></td>
                <td>
                    <div class="emblem-div">
                        <img src="{{ public_path('assets/images/emblem.png') }}" width="40" alt="Emblem" class="emblem">
                    </div>

                    <!-- Main Title -->
                    <!-- <h1 class="title-main">Land And Development Office</h1>
                    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
                    <h2 class="title-sub">Government of India</h2> -->
                    <h1 class="title-main">Government of India</h1>
                    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
                    <h1 class="title-main">Land And Development Office</h1>
                </td>
                <td style="text-align: right;width: 100px;"><img src="qrcode/{{$filename}}" width="100" alt="Emblem" class="emblem"></td>
            </tr>
        </table>

        <!-- Membership Details Title (Includes Club Type Now) -->
        <div class="part-title">
           {{$showUploadSignedLetter == 1 ? 'CONVEYANCE DEED': 'DRAFT CONVEYANCE DEED'}} 
        </div>

        <!-- Membership Details Table -->
        <table style="margin-bottom: 20px;">
            <tr>
                <td style="width:50%"><b>No. L&DO/ <span>{{$sectionName}}</span></b></td>
                <td style="width:50%"><b>Date: <span>{{$date}}</span></b></td>
            </tr>
            <tr>
                <td style="width:50%"><b>Property ID: <span>{{$propertyId}} ({{$newPropertyId}}) </span></b></td>
                <td style="width:50%"><b>Application ID: <span>{{$applicationNo}}</span></b></td>
            </tr>
        </table>
        <!-- Declaration -->

        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>1.</p></td>
                <td><p>This Conveyance Deed made on <span>{{date('d-m-Y',strtotime(($leaseDetails->doe)))}}</span> between the President of India hereinafter called “the Vendor” (which expression shall, unless excluded by or repugnant to the context, be deemed to include these successors in office and assigns) of the one part and Sh./Smt.</p></td>
            </tr>
        </table>
        <table style="margin-bottom: 20px;">
            <tr>
                <th>Name of Applicant<br/>(Mr./Ms./Mrs.)</th>
                <th>Father/ Husband Name <br/></th>
                <th>Share</th>
                <th>Aadhaar/Passport No.</th>
                <th>Share in property <br/>(1/n th)</th>
            </tr>
            <tr>
                <td>{{$applicantDetail->name}}</td>
                <td>{{$applicantDetail->second_name}}</td>
                <td>-</td>
                <td>{{decryptString($applicantDetail->aadhar_number)}}</td>
                <td>-</td>
            </tr>
            @forelse ($coapplicants as $coapplicant)
                <td>{{$coapplicant->co_applicant_name}}</td>
                <td>{{$coapplicant->co_applicant_father_name}}</td>
                <td>-</td>
                <td>{{decryptString($coapplicant->co_applicant_aadhar)}}</td>
                <td>-</td>
            @empty
                
            @endforelse
        </table>

        <p>Particulars of Property {{$plotNo}}/{{$blockNo}}/{{$colonyName}}, Delhi/New Delhi whereinafter called the Purchaser(s) (which expression shall, unless excluded by or repugnant to the context be deemed to include, his/her/their heirs, administrators, representative and permitted assigns) of the other part.</p>

        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>2.</p></td>
                <td><p>Whereas by an Indenture of Lease deed dated {{date('d-m-Y',strtotime(($leaseDetails->doe)))}} made between the Vendor described therein as Lessor of the one pert and </p></td>
            </tr>
        </table>
        <table style="margin-bottom: 20px;">
            <tr>
                <th>Name of Original Lessee<br/>(Mr./Ms./Mrs.)</th>
                <th>Father/ Husband Name</th>
                <th>Address</th>
            </tr>
            @foreach($originalLessseeDetail as $row)
            <tr>
                <td>{{$row->lessee_name}}</td>
                <td>-</td>
                <td>-</td>
            </tr>
            @endforeach
        </table>

        <p>Of Lessee of the other part and registered on the date given below in the Office of the Sub-Registrar Delhi at:</p>

        <table style="margin-bottom: 20px;">
            <tr>
                <th>Registration Number</th>
                <th>Book Number</th>
                <th>Volume Number</th>
                <th>Page From </th>
                <th>Page To </th>
                <th>Registration Date</th>
            </tr>
            <tr>
                <td>{{$applicationData->reg_no}}</td>
                <td>{{$applicationData->book_no}}</td>
                <td>{{$applicationData->volume_no}}</td>
                <td>{{substr($applicationData->page_no,0,strpos($applicationData->page_no,'-'))}}</td>
                <td>{{substr($applicationData->page_no,strpos($applicationData->page_no,'-') +1)}}</td>
                <td>{{date('d-m-Y', strtotime($applicantDetail->reg_date))}}</td>
            </tr>
        </table>
        <p>(hereinafter referred to as the “said Lessee”) by way of the lease for a period of 99 years subject to the terms & conditions mentioned in the said Lease Deed.</p>

        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>3.</p></td>
                <td><p>AND WHEREAS by mutation/substitution Letter Details and in the names of </p></td>
            </tr>
        </table>

        <table style="margin-bottom: 20px;">
            <tr>
                <th>Letter Number</th>
                <th>Letter Date </th>
                <th>Name of Applicant <br/>(Mr./Ms./Mrs.)</th>
                <th>Father/Husband  Name <br/></th>
                <th>Share <br/>(1/n th)</th>
                <th>Remarks</th>
            </tr>
            {{-- <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr> --}}
             <tr>
                <td>-</td>
                <td>{{date('d-m-Y')}}</td>
                <td>{{$applicantDetail->name}}</td>
                <td>{{$applicantDetail->second_name}}</td>
                <td>-</td>
                {{-- <td>{{decryptString($applicantDetail->aadhar_number)}}</td> --}}
                <td>-</td>
            </tr>
            @forelse ($coapplicants as $coapplicant)
                <td>-</td>
                <td>{{date('d-m-Y')}}</td>
                <td>{{$coapplicant->co_applicant_name}}</td>
                <td>{{$coapplicant->co_applicant_father_name}}</td>
                <td>-</td>
                {{-- <td>{{decryptString($coapplicant->co_applicant_aadhar)}}</td> --}}
                <td>-</td>
            @empty
                
            @endforelse
        </table>

        <p>Purchase(s) was/were lastly mutated and he/she/they has/have been recorded as the Present Lessee(s) under the said Lease Deed with all rights and liabilities of the Lease under the said Lease Deed.</p>

        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>4.</p></td>
                <td>
                    <p>AND WHEREAS no persons has objected to the mutation/substitution of the names of the Lease above
                        made by the Lessor or has in any other manner claimed to be the successor in interests of the
                        original Lessee or of any other person claiming through the Original Lessee.</p>
                </td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>5.</p></td>
                <td><p>AND WHEREAS the vendor herein by a Public Notice published in prominent newspapers of Delhi dated 15-4-92 hereinafter referred to as Public Notice, has announced his decision interalia, to grant freehold rights in respect of the lease properties in Delhi on certain terms & conditions therein.</p></td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>6.</p></td>
                <td><p>AND WHEREAS the Purchaser herein in response to the Public Notice dated 15-4-92 referred to above has acting through his attorney appointed under Power of Attorney dated………. Applied to the Vendor for grant of freehold rights in respect of the said demised premises by purchasing the rights and 
                    interests of the Vendor in the said demised premises and the vendor has agreed to sell all his residuary & reversionary rights and interests in the said demised premises subject to the terms and conditions appearing hereinafter.</p></td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>7.</p></td>
                <td><p>NOW IN THE PREMISES HEREIN BEFORE THIS INDENTURE witnesses that in consideration of the sum of (Conversion amount) {{$conversionCharges}}/- (Rupees {{$conversionChargesInWords}}) was paid before the execution thereof (the receipt whereof the Vendor hereby admits and acknowledges) and subject to the limitations, covenants and 
                    condition mentioned hereinafter the Vendor doth hereby grants coneys, sells, transfer, assigns, releases and assures unto the Purchaser(s) all the residuary reversionary rights, title and interests of the lessor under the said Lease Deed in the demised property more fully described in the said Lease Deed 
                    as well as in the schedule hereunder together with all remainders, rents issues and profits thereof hereinafter referred to as the said property TO HAVE AND TO HOLD the same unto the Purchaser absolutely and forever, subject always to the exception that the Vendor reserves unto himself all mines, minerals 
                    coals, gold washings, earth oils and quarries of whatever nature lying in or under the said property together with full right and power at all times for the , Vendor, its agents and workmen, to do all acts and things which be necessary or expedient for the purpose of searching for working obtaining removing 
                    and enjoying the same without providing or leaving any vertical support for the surface of make reasonable compensation to the Purchaser(s) for all damages directly occasioned by the exercise of the rights herby reserved or any of them for damage done unto him thereby subject to the payment of property tax or 
                    other imposition payable or which may become lawfully payable in respect of said property and to all public rights or easement affection the same.</p></td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>8.</p></td>
                <td><p>It is further declared that as a result of these presents and subject to the conditions and covenants stated her in above, the Purchaser(s) from the date mentioned here above will become owner of the said property and the Vendor doth hereby releases the Purchasers from all future liability in respect of the 
                    rent reserved by the covenants and conditions contained in the said Lease Deed required to be observed by the Purchaser(s) as a Lessee of the said demised property.</p></td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>9.</p></td>
                <td><p>PROVIDED ALWAYS and it is hereby agreed by the purchaser(s) that if it comes to light at any later date that Purchaser(s) as Lessee(s) under the said Lease Deed was/were liable to pay any amount to the lessor under the said Lease Deed but payment of which could not be made before or at the time of execution of these presents then for such amount the Vendor will have the first charge over the said property.</p></td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>10.</p></td>
                <td><p>Provided further that this Conveyance Deed shall be revoked without any notice if it comes to light any later stage that the Vendor (including heirs, administrators, representatives and permitted assigns) have encroached upon any Government land/Public land or if the property is put to any use other than what is stipulated in the lease terms/master plan norms.</p></td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:15px;"><p>11.</p></td>
                <td><p>The Stamp Duty and registration charges, if any upon this instrument shall be borne by the Purchaser(s).</p></td>
            </tr>
        </table>


         <div class="part-title">
            SCHEDULE
        </div>
        <p>Particulars of the Property No. ___________________________________________________________, Delhi/New Delhi.</p>
        <p>Bounded on the North by:	______	</p>
        <p>Bounded on the East by:	______</p>
        <p>Bounded on the South by:	______</p>
        <p>Bounded on the West by:	______</p>
        <br/><br/>
        <p>IN WITNESS WHERE OF the purchaser(s) whose name mentioned below</p>
        <table style="margin-bottom: 20px;">
            <tr>
                <th>Signature of the Applicant</th>
                <th>Name of Applicant <br/>(Mr./Ms./Mrs.)</th>
                <th>Father/Husband Name </th>
                <th>Address</th>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        </table>
        <p style="text-align: center;">AND</p>
        <!-- <div class="declaration">
            <div><strong>Declaration:</strong></div>
            <div>I hereby declare that the information provided above is true, correct, and complete to the best of my
                knowledge and belief. The details have been duly verified and are submitted for official processing.
            </div>
        </div> -->

        <div class="letter-footer" style=" margin-top: 50px;display:flex:justify-content:end;width:100%;">
            <div class="ldo-sign" style="text-align:right;display: flex;">
                <div class="">
                    (Signature of the Officer)<br/>
                    ({{ $deputyUserName }})<br />
                    (Deputy Land & Development Officer)<br />
                    Ministry of Housing and Urban Affairs<br/>
                    Govt. of India, New Delhi<br/>
                    For and on behalf of President of India<br/>
                </div>
            </div>
        </div>
        <p>IN THE PRESENCE OF </p>
        <br/>
        <table class="hidden-table">
            <tr>
                <td><p>Witness No. 1</p></td>
                <td style="width: 75%;"></td>
                <td><p>Witness No. 2</p></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <table class="hidden-table">
            <tr>
                <td style="width:20%">
                    <p>Shri/Smt. (Name)<br />Address-<br />Aadhaar/PAN-</p>
                </td>
                <td style="width: 40%;"></td>
                <td style="width:40%;">
                    <p>Shri/Smt. (Name)<br />Address-<br />Aadhaar/PAN-</p>
                </td>
            </tr>
        </table>
        <!-- for signature of admin by swati on 29052025 -->
        <!-- Undersignee Section -->
        <!-- <div class="signature-box">
            <div class="signature-inner">
                <div class="signature-line"></div>
                <div class="signature-label">Signature of Admin Incharge</div>
            </div>
        </div> -->

    </div>
    <div class="pdf-footer">
        <table width="100%">
            <tr>
                <td style="text-align: left;">Property ID: <span>{{$propertyId}}</span></td>
                <td style="text-align: center;">Officer User ID LDO: <span>89879</span></td>
                <td style="text-align: right;">Application ID: <span>{{$applicationNo}}</span></td>
            </tr>
        </table>
    </div>
</body>

</html>
