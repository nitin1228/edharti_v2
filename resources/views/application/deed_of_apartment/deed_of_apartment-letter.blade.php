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
        body {
            /* font-family: 'DejaVu Sans', sans-serif; */
            font-family: sans-serif !important;
            margin: 0;
            padding: 0;
            position: relative;
        }

        /* body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url("{{ public_path('assets/images/water-mark-emblem.png') }}") center center no-repeat;
            opacity: 0.1;
        } */
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

        body::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url("{{ public_path('assets/images/water-mark-emblem.png') }}") center center no-repeat;
            transform: rotate(-30deg);
            opacity: 0.3;
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
          .content-wrap p{
            font-size: 14px;
        }
        .qr-img{
            text-align: center;
        }
        .bold{
            font-weight: bold;
        }
        .hidden-table,
        .hidden-table th,
        .hidden-table td{
            border:0;
            font-size:12px;
            vertical-align: top;
            margin:0 0 10px;
        }
        .hidden-table th p,
        .hidden-table td p{
            margin: 0;
        }
        p{
            text-align: justify;
        }
        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }
    </style>

<body>
    <div class="watermark"></div>
    <div class="content-wrap">
        <table class="hidden-table">
            <tr>
                <td style="width: 100px;"></td>
                <td>
                    <div class="emblem-div">
                        <img src="{{ public_path('assets/images/emblem.png') }}" width="40" alt="Emblem" class="emblem">
                    </div>

                    <!-- Main Title -->
                     <h1 class="title-main">Government of India</h1>
                     <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
                     <h1 class="title-main">Land And Development Office</h1>
                </td>
                <!-- <td style="text-align: right;width: 100px;"><img src="assets/images/ldo_mohua_qr.png" width="100" alt="Emblem" class="emblem"></td> -->
                <td><img src="qrcode/{{$filename}}" alt="QR Code" width="100" height="100"></td>
            </tr>
        </table>
         
        <!-- Declaration -->
         <h4 style="text-align: center;">DEED OF APARTMENT FOR APARTMENTS CONSTRUCTED ON THE LAND LEASED BY GOVERNMENT OF INDIA</h4>
         <ol>
            <li>
                <p>This indenture is made at Delhi on {{ date('d-m-Y', strtotime($noticeData['applicantDetail']->updated_at)) }}, amongst <strong>({{$noticeData['builder_developer_name']??'N/A'}})</strong> herein after called the DEVELOPER / BUILDER known as the Party No. 1 & <strong> {{ ucfirst($noticeData['applicantDetail']->name) ?? 'N/A'}}</strong> hereinafter called as the flat BUYER / PURCHASER OF THE APARTMENT known as the Party No. 2 & the President of India through Land and Development Officer, Land and Development Office, Ministry of Housing & Urban Affairs who manages the affairs of land belonging to Government of India hereinafter called THE LESSOR known as the Party No. 3.</p>
            </li>
            <li>
                <p>WHEREAS the lessor had granted leasehold rights to <strong>{{$noticeData['orignnallease']->lessee_name}}</strong> through a lease deed executed on <strong>{{date('d-m-Y',strtotime($noticeData['orignnallease']->transferDate)) ?? 'N/A'}}</strong> for the land situated at <strong>(Block No. {{$noticeData['blockNo'] ?? "N/A"}}, Plot No. {{$noticeData['plotNo'] ?? "N/A"}} known as {{$noticeData['known_as'] ?? "N/A"}})</strong> for a land admeasuring <strong>{{$noticeData['plotArea']}} sqm. .</strong> </p>
            </li>
            <li>
                <p>Whereas Party No.1 constructed a building comprising flats, common area, etc. to be used for <strong>{{getServiceNameById($noticeData['applicantDetail']->land_use_type) ?? "N/A"}}</strong> purposes on the land described above which is known as <strong>{{$noticeData['doaDetail']->building_name}}</strong>, situated on the aforesaid address and sold / transferred the Property No. <strong>{{$noticeData['doaDetail']->flat_number}}</strong> having an area of <strong>{{$noticeData['doaDetail']->flat_area}} sqm.</strong>, which includes the proportionate common area attributed to the said flat, to <strong>{{$noticeData['doaDetail']->original_buyer_name}}</strong>. The <strong>{{$noticeData['doaDetail']->flat_number ?? "N/A"}}</strong> was transferred / sold to {{$noticeData['applicantDetail']->name}} by <strong>{{$noticeData['doaDetail']->test ?? "N/A"}}</strong> on <strong>{{ date('d-m-Y', strtotime($noticeData['doaDetail']->purchased_date))}}</strong>.</p>
            </li>
            <li>
                <p>Now, it is hereby agreed by and amongst the parties that -</p>
            </li>
         </ol>
         <ol type="I">
            <li><p>The buyer of the apartment shall have exclusive leasehold and possession rights of the apartment and the proportionate common area attributed to it, purchased by him by virtue of the builder-buyer agreement or any other valid transfer / assignment document and this deed being executed, today. </p></li>
            <li><p>For the purpose of stamp duty and registration fee to be imposed on registration of this deed under the relevant legislations, the value of the said flat / space is calculated as under:-</p></li>
            <ol type="a">
                <li><p>Area of the flat / space in sqm. : <strong> {{$noticeData['doaDetail']->flat_area}} sqm.</strong></p></li>
                <li><p>Rate per sqm. : <strong>{{$noticeData['rate']}} Inr</strong></p></li>
                <li><p>Value of the Flat / Space : <strong>Rs. {{$noticeData['rate']*$noticeData['doaDetail']->flat_area}}</strong></p></li>
                <li><p>Ground Rent to be paid for the Flat to the Lessor per annum : <strong> Rs. {{ ($noticeData['rate'] * $noticeData['doaDetail']->flat_area) * 0.01 }}</strong></p></li>
            </ol>
            <p><strong>The responsibility of payment of stamp duty, registration fee along with all other charges to be paid in the process of registration, shall be borne by the buyer / purchaser Party No. 2.</strong></p> 
            <li><p>The apartment will remain a leasehold property and the buyer will be bound by all the terms and conditions of the original lease if the same are not amended by this deed of apartment. </p></li>
            <li>
                <p>It is also hereby agreed between the parties that in case of conflict of any terms and conditions of this deed of apartment or the original lease, the said conflict shall be decided by the lessor or through an officer authorized for the purpose.</p>
            </li>
            <li>
                <p>This deed of apartment is being executed as per the Delhi Apartment Ownership Act, 1986, in compliance with the Hon’ble Delhi High Court’s judgement dated 28.5.2010 and subsequent modified order dated 13.7.2012 in the matter of Writ Petition (C) No.1959/2007 titled as OS Bajpai Vs. Administrator of Delhi and Others.</p>
            </li>
            <li>
                <p>This deed of apartment is being signed in triplicate. All the copies shall be presented to the collector of stamps for payment of stamp duty and to the sub-registrar concerned for registration of the deed within 4 (four) months of execution of this deed. An original copy of the registered deed of apartment shall be submitted to the lessor within 15 (fifteen) days of the registration of the deed, failing which the lessor may levy a penalty on the buyer / purchaser.</p>
            </li>
         </ol>
         <p>
            In witness whereof the parties herein mentioned below have executed these presents on <strong>{{date('d-m-Y')}}</strong>.
        </p>
        <table class="hidden-table">
            <tbody>
                <tr>
                    <td style="text-align: right;"><strong>Signed and delivered for and on behalf of </strong></td>                    
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>............................................</strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>(Name of Developer, if available. Write N.A. if not available)</strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;">Party No. 1 </td>
                </tr>
                <tr>
                    <td style="text-align: right;">Aadhaar No……..…………….</td>
                </tr>
                <tr>
                    <td style="text-align: right;">PAN No……………………….. </td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>Signed and delivered by</strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="text-align: right;">............................................</td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>(Name of Applicant)</strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>(Add : Through Name of the GPA / SPA Holder, if GPA/SPA)</strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>Party No. 2 </strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;">Aadhaar No……..…………….</td>
                </tr>
                <tr>
                    <td style="text-align: right;">PAN No……………………….. </td>
                </tr>
                <tr>
                    <td style="text-align: right;"><strong>Signed and delivered for and on behalf of Lessor</strong></td>
                </tr>
                <tr>
                    <td style="text-align: right;">…………………………………….</td>
                </tr>
                <tr>
                    <td style="text-align: right;">…………………………………….</td>
                </tr>
                <tr>
                    <td style="text-align: right;">…………………………………….</td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        <strong>
                             ({{ $noticeData['deputyUserName'] }})<br/>
                            Dy. Land & Development Officer<br/>
                            Party No. 3

                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="hidden-table">
            <tbody>
                <tr>
                    <td><strong>Witness 1 </strong></td>                    
                </tr>
                <tr>
                    <td><strong>Sign : ….....................................................</strong></td>
                </tr>
                <tr>
                    <td><strong>Name : ……………………………………….</strong></td>
                </tr>
                <tr>
                    <td><strong>Address: ……………………………………..</strong></td>
                </tr>
                <tr>
                    <td><strong>ID Name & No.: ……………………………..</strong></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><strong>Witness 2</strong></td>
                </tr>
                <tr>
                    <td><strong>Sign : ….....................................................</strong></td>
                </tr>
                <tr>
                    <td><strong>Name : ……………………………………….</strong></td>
                </tr>
                <tr>
                    <td><strong>Address: ……………………………………..</strong></td>
                </tr>
                <tr>
                    <td><strong>ID Name & No.: ……………………………..</strong></td>
                </tr>
            </tbody>
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
</body>

</html>