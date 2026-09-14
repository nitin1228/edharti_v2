<!DOCTYPE html>
<html>

<head>
    <title>Membership Application PDF</title>
    <style>
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
            background: url({{ public_path('assets/images/water-mark-emblem.png') }}) center center no-repeat;
            opacity: 0.1;
        }

        body::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url({{ public_path('assets/images/water-mark.png') }}) 0 0 repeat;
            transform: rotate(-30deg);
            opacity: 0.1;
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
            margin-left: 30px;
        }
    </style>
</head>

<body>
    <div class="content-wrap">

        <!-- Emblem Image -->
        <div class="emblem-div">
            <img src="{{ public_path('assets/images/emblem.png') }}" width="40" alt="Emblem" class="emblem">
        </div>

        <!-- Main Title -->
        <h1 class="title-main">Land And Development Office</h1>
        <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
        <h2 class="title-sub">Government of India</h2>

        <!-- Section Title -->
        <div class="part-title">Club Membership Application Details</div>

        <!-- Membership Details Table -->
        <table>
            <!-- Keep your table rows as is -->
            <tr><td><b>Date Of Application:</b></td><td></td></tr>
            <tr><td><b>Club Name for Membership Application:</b></td><td></td></tr>
            <tr><td><b>Name:</b></td><td></td></tr>
            <tr><td><b>Category:</b></td><td></td></tr>
            <tr><td><b>Designation:</b></td><td></td></tr>
            <tr><td><b>Equivalent to Designation:</b></td><td></td></tr>
            <tr><td><b>Email:</b></td><td></td></tr>
            <tr><td><b>Mobile:</b></td><td></td></tr>
            <tr><td><b>Department:</b></td><td></td></tr>
            <tr><td><b>Name of Service:</b></td><td></td></tr>
            <tr><td><b>Allotment Year:</b></td><td></td></tr>
            <tr><td><b>Do you hold a central staffing position?:</b></td><td></td></tr>
            <tr><td><b>Date of Joining on Central Deputation:</b></td><td></td></tr>
            <tr><td><b>Expected Date of Tenure Completion:</b></td><td></td></tr>
            <tr><td><b>Date of Superannuation:</b></td><td></td></tr>
            <tr><td><b>Office Address:</b></td><td></td></tr>
            <tr><td><b>Telephone:</b></td><td></td></tr>
            <tr><td><b>Pay Level:</b></td><td></td></tr>
            <tr><td><b>Present/Previous Membership of other clubs:</b></td><td></td></tr>
        </table>

        <!-- Declaration -->
        <div class="declaration">
            <div><strong>Declaration:</strong></div>
            <div>
                I hereby declare that the information provided above is true, correct, and complete to the best of my knowledge and belief.
                The details have been duly verified and are submitted for official processing.
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-box">
            <div class="signature-inner">
                <div class="signature-line"></div>
                <div class="signature-label">Signature of Admin Incharge</div>
            </div>
        </div>

    </div>
</body>

</html>
