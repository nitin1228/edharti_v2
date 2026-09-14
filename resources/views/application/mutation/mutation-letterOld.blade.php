<!DOCTYPE html>
<html>

<head>
    <title>Mutation Letter PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* font-family: sans-serif !important;e */
            margin: 0;
            padding: 0;
            position: relative;
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

        body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url(assets/images/water-mark-emblem.png) center center no-repeat;
            background-size: 300px;
            opacity: 0.3;
        }

        /* body::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url(assets/images/water-mark.jpg) 0 0 repeat;
            background-size:300px;
            transform: rotate(-30deg);
            opacity: 0.2;
        } */

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

        .content-wrap {
            margin-right: 30px;
            margin-left: 30px;
        }
        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }

         ol {
            padding-left: 20px;
        }
        ol li{
            font-size: 12px;
            line-height: 20px;
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
            padding: 5px 5px;
        }

        .hidden-table th p,
        .hidden-table td p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="watermark"></div>
    <!-- Emblem -->
    <div class="emblem-div">
        <img src="assets/images/emblem.jpg" width="40" alt="Emblem" class="emblem">
    </div>

    <!-- Title -->
    <!-- <h1 class="title-main">Land And Development Office</h1>
    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
    <h2 class="title-sub">Government of India</h2> -->
    <h1 class="title-main">Government of India</h1>
    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
    <h1 class="title-main">Land And Development Office</h1>


    <!-- <p style="text-align: center; font-size: 14px; color:#116d6e; font-weight: bold; margin-top: 30px;">
        Payment Receipt
    </p> -->

    <!-- Applicant Details -->
    <div class="part-title">MUTATION LETTER</div>
    <table class="hidden-table">
        <tbody>
            <tr>
                <th colspan="2" style="width: 50%;">Property Details</th>
                <th colspan="2" style="width: 50%;">Lease / Ownership Details</th>
            </tr>
        </tbody>
        <tr>
            <td>Property ID: ______________________________________________________</td>
            <td style="width: 10%;"></td>
            <td>Lease Execution Date / Conveyance<br/>Deed Execution Date _______________________________________________</td>
            <td style="width: 10%;"></td>
        </tr>
        <tr>
            <td>Block No.: _______________________________________________________</td>
            <td></td>  
            <td>Registered on: ____________________________________________________</td>
            <td></td>
        </tr>
        <tr>
            <td>Plot No.: _________________________________________________________</td> 
            <td></td>
            <td>In favour of _______________________________________________________</td>
            <td></td>
        </tr>
        <tr>
            <td>FlatNo./ FloorNo./<br/>ShopNo. _________________________________________________________</td>
            <td></td>
            <td valign="baseline" style="padding-top: 20px;"><strong>Registration Details:</strong></td>
            <td></td>
        </tr>
        <tr>
            <td>Known As: ________________________________________________________</td>
            <td></td>
            <td>Date: ____________________________________________________________</td>
            <td></td>
        </tr>
        <tr>
            <td>Area (insqm): _____________________________________________________</td>
            <td>&nbsp;</td>
            <td>Reg. No.: ________________________________________________________</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Status:(FH/LH) ____________________________________________________</td>
            <td>&nbsp;</td>
            <td>Book No.: ________________________________________________________</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Vol. No.: _________________________________________________________</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Page No.: ___________to__________</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <!-- Property Details -->
    <div class="part-title">EXISTING LESSEES / OWNERS (AS PER RECORDS) - BEFORE APPLICATION</div>
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
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>2.</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>3.</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="part-title">UPDATED LESSEES / OWNERS (AS PER APPLICATION)</div>
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
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>2.</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>3.</strong></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <div class="part-title">APPLICATION DETAILS</div>
    <table>
        <tbody>
            <tr>
                <td style="width: 25%;"><strong>Application No.:</strong></td>
                <td style="width: 25%;"></td>
                <td style="width: 25%;"><strong>Date of Application:</strong></td>
                <td style="width: 25%;"></td>
            </tr>
            <tr>
                <td><strong>Applicant Name</strong></td>
                <td></td>
                <td><strong>Document Name</strong></td>
                <td></td>
            </tr>
            <tr>
                <td>Applicant Name 1</td>
                <td></td>
                <td>Document Name 1</td>
                <td></td>
            </tr>
            <tr>
                <td>Applicant Name 2</td>
                <td></td>
                <td>Document Name 2</td>
                <td></td>
            </tr>
            <tr>
                <td>Applicant Name 3</td>
                <td></td>
                <td>Document Name 3</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <h4>Note:</h4>
    <ol>
        <li>The above mutation has been carried out on the basis of the application and the documents provided by the applicants.</li>
        <li>Any discrepancies, concealment of facts, misrepresentation etc. will lead to cancellation of the mutation.</li>
        <li>Claims and Objections against this mutation shall be filed in the relevant courts. The decision in the litigation matter shall be submitted to Land & Development Office for execution.</li>
        <li>The updated lessees/owners shall be bound by the Lease/Conveyance Deed.</li>
    </ol>


</body>

</html>
