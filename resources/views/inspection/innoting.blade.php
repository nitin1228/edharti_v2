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

        /* body::after {
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
         .info-table{
            width:100%;
            border-collapse: collapse;
            margin-bottom:30px;
        }
    </style>

<body>
    <div class="watermark"></div>
    <!-- Login 8 section start -->
    <div class="content-wrap">

        <table class="hidden-table">
            <tr>
                <td style="width: 100px;"></td>
                <td>
                    <div class="emblem-div">
                        <img src="{{ public_path('assets/images/emblem.jpg') }}" width="40" alt="Emblem" class="emblem">
                    </div>
                    <h1 class="title-main">Government of India</h1>
                    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
                    <h1 class="title-main">Land And Development Office</h1>
                </td>
                <td style="text-align: right;width: 100px;"></td> 
                <td></td>
            </tr>
        </table>
         <div class="part-title">
             Noting for  Inspection
        </div>

        <!-- Membership Details Table -->
        <table class="info-table">
        <tr>
            <td><span class="bold"> Property ID:</span> {{ $inspection->property_id }} </td>
            <td><span class="bold"> Report Date:</span>  {{ \Carbon\Carbon::parse($inspection->created_at)->format('d-M-Y h:i:s A') }} </td>
        </tr>
        <tr>
            <td><span class="bold">Inspection ID:</span> {{ $inspection->inspection_id }}</td>
            <td><span class="bold">Application ID: </span> {{ $inspection->application_no }}</td>
        </tr>
    </table>
        <div class="content">@php
			    $comm = json_decode($inspection->communication_address ?? '{}');
			    $address = $comm->second_address ?? null;
			@endphp
      As inspection is/was SCHEDULED on  <b>{{ !empty($inspection->schedule_date) ? \Carbon\Carbon::parse($inspection->schedule_date)->format('d-M-Y') : 'N/A' }}</b> between  <b>{{ !empty($inspection->schedule_fromtime) ? \Carbon\Carbon::parse($inspection->schedule_fromtime)->format('h:i A') : 'N/A' }} </b> and <b>{{ !empty($inspection->schedule_totime) ? \Carbon\Carbon::parse($inspection->schedule_totime)->format('h:i A') : 'N/A' }} </b> 
for the Property ID:  <b>{{ $inspection->property_id }}</b> known as <b>{{ $inspection->property_known_as }}</b> for which inspection official is Sh. {{ $address->inspection_officer ?? 'N/A' }}, 
Junior Engineer.<br><br>
An Inspection notice may be send to the occupant of the premises.  
    </div>
  
        <!-- <div class="declaration">
            <div><strong>Declaration:</strong></div>
            <div>I hereby declare that the information provided above is true, correct, and complete to the best of my
                knowledge and belief. The details have been duly verified and are submitted for official processing.
            </div>
        </div> -->

        <div class="letter-footer" style="margin-top: 200px;">
          <div class="signature-box">
            <div class="signature-inner">
                <div class="signature-line"></div>
                <div class="signature-label">(A.E-I/A.E-II/BO)</div>
            </div>
        </div> <br><br>
            <!-- <div class="ldo-sign" style="text-align: end;display: flex;justify-self: end;width: 200px;">
                <div class="name-sign">RAJEEV KUMAR DAS</div>
                <div class="">
                    Digitally signed by RAJEEV KUMAR DAS
                    <br/>
                    <span>Date: 2025.07.16</span><br/>
                    <span>16:48:11 +05'30'</span>
                </div>
            </div> -->
            <!--<p class="uppercase fs18 bold" style="margin: 0px; text-align:right">({{$inspection->section_user_name}})</p>-->
            <p style="margin: 0px;text-align:right" class="bold fs16">(JE/OVERSEER/SURVEY OFFICER)</p>
           <!-- <p style="margin: 0px;text-align:right" class="bold fs16">{{$inspection->section_code}}</p>    -->         
        </div>
        <!-- for signature of admin by swati on 29052025 -->
        <!-- Undersignee Section -->
        <br>
        <br>
       
        <br><br>
         <!--<div class="signature-box">
            <div class="signature-inner">
                <div class="signature-line"></div>
                <div class="signature-label">Branch Officer</div>
            </div>
        </div> -->

    </div>
</body>

</html>
