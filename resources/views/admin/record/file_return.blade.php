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
            background: url(assets/images/water-mark-emblem.png) center center no-repeat;
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
            background: url(assets/images/water-mark.png) 0 0 repeat;
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
            font-size: 14px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .content-wrap {
            margin-right: 30px;
            margin-left: 30px;
        }
        .content-wrap p{
            font-size: 14px;
        }
        .name-sign{
            font-size: 25px;
            text-align: right;
        }
        .qr-img{
            text-align: center;
        }
        .hidden-table,
        .hidden-table th,
        .hidden-table td{
            border:0;
            font-size:14px;
            vertical-align: top;
        }
        .hidden-table th p,
        .hidden-table td p{
            margin: 0;
        }
    </style>

<body>
    <!-- Login 8 section start -->
    <div class="content-wrap">

        <!-- Watermark -->
        <!-- <div class="watermark">Land and Development Office</div> -->

        <!-- Emblem Image -->
        <div class="emblem-div">
            <img src="assets/images/emblem.png" width="40" alt="Emblem" class="emblem">
        </div>

        <!-- Main Title -->
        <h1 class="title-main">Land And Development Office</h1>
        <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
        <h2 class="title-sub">Government of India</h2>

        <!-- Membership Details Title (Includes Club Type Now) -->
        <div class="part-title">
             File Return to Record Section
        </div>

        <table class="hidden-table">
            <tr>
                <td><p>Section Name: <span>Record Section</span></p></td>
                <td style="text-align: right;"><p>Report Date: <span>{{$dateOfRequest}}</span></p></td>
            </tr>
        </table>

         <p>The file laying in this section Block Number: {{$block}}, Plot Number: {{$plot}}, File Location: {{$file_location}}, Colony
            Name: {{$colony}} . No action is pending. Record Section may kindly received back the said
            file detail is as under:
        </p>

        <div class="letter-footer" style="text-align: right; margin-top: 30px;width:100%">
            <div class="ldo-sign" style="text-align:right;">
                <div style="display:flex; justify-content: end; ">
                    Signature<br/><br/>
                    Name: <span>{{$requestedUserName}}</span><br/>
                    Designation: <span>{{$designation}}</span><br/>
                    Section Name: <span>{{$requestSection}}</span><br/>
                </div>
            </div>
        </div>
        <div class="part-title">
            RECORD SECTION
        </div>
        <p>The Detail of Property file No. Block Number: {{$block}}, Plot Number: {{$plot}}, Colony Name: {{$colony}} is as under:</p>
        <table style="margin-bottom: 100px;">
            <tr>
                <th style="width:33%">Vol. No. </th>
                <th style="width:33%">Noting Page</th>
                <th style="width:33%">Correspondence Page</th>
            </tr>
             <tr>
                <td style="width:33%">I</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">II</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">III</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">IV</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">V</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">VI</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">VII</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">VIII</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">IX</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
            <tr>
                <td style="width:33%">X</td>
                <td style="width:33%">&nbsp;</td>
                <td style="width:33%">&nbsp;</td>
            </tr>
        </table>

        <table style="margin-bottom: 50px;">
            <tr>
                <td style="width:50%">
                    <p>Handed Over: <span>{{$requestSection}}</span></p>
                    <p>Signature (with Date) ______________</p>
                    <p>Full Name : ______________</p>
                    <p>Designation : _______________</p>
                </td>
                <td style="width:50%">
                    <p>Taken Over in Record Section: <span></span></p>
                    <p>Signature (with Date) ______________</p>
                    <p>Full Name : ______________</p>
                    <p>Designation : _______________</p>
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
</body>

</html>
