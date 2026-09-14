<!DOCTYPE html>
<html>

<head>
    <title>Membership Application PDF</title>
    <style>
        body {
            /* font-family: 'DejaVu Sans', sans-serif; */
            font-family: sans-serif !important;
            margin: 0;
            padding: 0;
            position:relative;
            /* background-image:url("assets/images/water-mark.png"); */
        }
        body::before{
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url(assets/images/water-mark-emblem.png) center center no-repeat;
            opacity: 0.1;
        }
        /* body::after{
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url(assets/images/water-mark.png) 0 0 repeat;
            transform: rotate(-30deg);
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
            font-size: 16px;
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
            margin-top: 30px;
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
            font-size: 10px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        /* .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        } */

        /* for signature of admin by swati on 29052025*/
        .signature-box {
            margin-top: 50px;
            text-align: right;
        }

        .signature-inner {
            display: inline-block;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
        }

        .signature-label {
            font-size: 10px;
            margin-top: 5px;
        }

        .declaration {
            margin-top: 30px;
            font-size: 10px;
            text-align: left;
        }

        .content-wrap {
            margin-right: 30px;
            /* Adjust as needed */
            margin-left: 30px;
        }
        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }
    </style>
</head>

<body>

    <!-- Watermark -->
    <!-- <div class="watermark">Land and Development Office</div> -->
     <div class="watermark"></div>

    <!-- Emblem Image -->
    <div class="emblem-div">
        <img src="assets/images/emblem.png" width="60" alt="Emblem" class="emblem">
    </div>

    <!-- Main Title -->
    <h1 class="title-main">Land And Development Office</h1>
    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
    <h2 class="title-sub">Government of India</h2>

    <!-- Membership Details Title (Includes Club Type Now) -->
    <div class="part-title">
        Club Membership Application Details - {{ $membership->club_type }}
    </div>

    <!-- Membership Details Table -->
    <table>
        <!-- <tr><td><b>Date of Application:</b></td><td>{{ \Carbon\Carbon::parse($membership->date_of_application)->format('d-m-Y') }}</td></tr> -->
        <tr>
            <td><b>Application Number:</b></td>
            <td>{{ $membership->unique_id }}</td>
        </tr>
        <tr>
            <td><b>Name:</b></td>
            <td>{{ $membership->name }}</td>
        </tr>
        <tr>
            <td><b>Category:</b></td>
            <td>{{ $membership->category }}</td>
        </tr>
        <tr>
            <td><b>Designation:</b></td>
            <td>{{ $membership->designation }}</td>
        </tr>
        <tr>
            <td><b>Equivalent to Designation:</b></td>
            <td>{{ $membership->designation_equivalent_to }}</td>
        </tr>
        <tr>
            <td><b>Email:</b></td>
            <td>{{ $membership->email }}</td>
        </tr>
        <tr>
            <td><b>Mobile:</b></td>
            <td>{{ $membership->mobile }}</td>
        </tr>
        <tr>
            <td><b>Department:</b></td>
            <td>{{ $membership->department }}</td>
        </tr>
        <tr>
            <td><b>Name of Service:</b></td>
            <td>{{ $membership->name_of_service }}</td>
        </tr>
        <tr>
            <td><b>Allotment Year:</b></td>
            <td>{{ $membership->year_of_allotment }}</td>
        </tr>
        <tr>
            <td><b>Do you hold a central staffing position?:</b></td>
            <td>{{ $membership->is_central_deputated ? 'Yes' : 'No' }}</td>
        </tr>
        <tr>
            <td><b>Date of Joining on Central Deputation:</b></td>
            <td>{{ \Carbon\Carbon::parse($membership->date_of_joining_central_deputation)->format('d-m-Y') ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td><b>Expected Date of Tenure Completion:</b></td>
            <td>{{ \Carbon\Carbon::parse($membership->expected_date_of_tenure_completion)->format('d-m-Y') ?? 'N/A' }}
            </td>
        </tr>
        <tr>
            <td><b>Date of Superannuation:</b></td>
            <td>{{ \Carbon\Carbon::parse($membership->date_of_superannuation)->format('d-m-Y') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><b>Office Address:</b></td>
            <td>{{ $membership->office_address }}</td>
        </tr>
        <tr>
            <td><b>Telephone:</b></td>
            <td>{{ $membership->telephone_no }}</td>
        </tr>
        <tr>
            <td><b>Pay Level:</b></td>
            <td>{{ $membership->pay_scale }}</td>
        </tr>
        <tr>
            <td><b>Present/Previous Membership of other clubs:</b></td>
            <td>{{ $membership->present_previous_membership_of_other_clubs }}</td>
        </tr>
        <!-- <tr><td><b>Other Relevant Information:</b></td><td>{{ $membership->other_relevant_information }}</td></tr> -->

        {{-- IHC Membership Details --}}
        <!-- @if ($membership->club_type === 'IHC' && $membership->ihcDetails)
<tr><td><b>Date/ Relevant Details of Individual Membership In Ihc:</b></td><td>{{ $membership->ihcDetails->individual_membership_date_and_remark ?? 'N/A' }}</td></tr>
            <tr>
                <td><b>Tenure Period of Delhi Golf Club:</b></td>
                <td>
                    {{ \Carbon\Carbon::parse($membership->ihcDetails->dgc_tenure_start_date)->format('d-m-Y') ?? 'N/A' }}
                    -
                    {{ \Carbon\Carbon::parse($membership->ihcDetails->dgc_tenure_end_date)->format('d-m-Y') ?? 'N/A' }}
                </td>
            </tr>
@endif -->

        {{-- DGC Membership Details --}}
        <!-- @if ($membership->club_type === 'DGC' && $membership->dgcDetails)
<tr><td><b>Post under Central Staffing Scheme:</b></td><td>{{ $membership->dgcDetails->is_post_under_central_staffing_scheme }}</td></tr>
            <tr><td><b>DGC Regular Membership Date/Remark:</b></td><td>{{ $membership->dgcDetails->regular_membership_date_and_remark ?? 'N/A' }}</td></tr>
            <tr>
                <td><b>Tenure period of Delhi Golf Club:</b></td>
                <td>
                    {{ \Carbon\Carbon::parse($membership->dgcDetails->dgc_tenure_start_date)->format('d-m-Y') ?? 'N/A' }}
                    -
                    {{ \Carbon\Carbon::parse($membership->dgcDetails->dgc_tenure_end_date)->format('d-m-Y') ?? 'N/A' }}
                </td>
            </tr>
            <tr><td><b>Current Handicap in Golf, (certification if any)</b></td><td>{{ $membership->dgcDetails->handicap_certification ?? 'N/A' }}</td></tr>
            <tr><td><b>Date of Nomination for IHC:</b></td><td>{{ \Carbon\Carbon::parse($membership->dgcDetails->ihc_nomination_date)->format('d-m-Y') ?? 'N/A' }}</td></tr>
@endif -->
    </table>
    <!-- Declaration -->
    <div class="declaration">
        <div><strong>Declaration:</strong></div>
        <div>I hereby declare that the information provided above is true, correct, and complete to the best of my
            knowledge and belief. The details have been duly verified and are submitted for official processing.</div>
    </div>

    <!-- for signature of admin by swati on 29052025 -->
    <!-- Undersignee Section -->
    <div class="signature-box">
        <div class="signature-inner">
            <div class="signature-line"></div>
            <div class="signature-label">Signature of Admin Incharge</div>
        </div>
    </div>

</body>

</html>
