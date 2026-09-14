<!DOCTYPE html>
<html>

<head>
    <title>Payment Receipt PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* font-family: sans-serif !important; */
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
            background: url("{{ public_path('assets/images/water-mark-emblem.png') }}") center center no-repeat;
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
            opacity: 0.1;
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
    </style>
</head>

<body>

    <div class="watermark"></div>
    <!-- Emblem -->
    <div class="emblem-div">
        <img src="{{ public_path('assets/images/emblem.jpg') }}" width="60" alt="Emblem" class="emblem">
    </div>

    <!-- Title -->
    <!-- <h1 class="title-main">Land And Development Office</h1>
    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
    <h2 class="title-sub">Government of India</h2> -->
    <h1 class="title-main">Government of India</h1>
    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
    <h1 class="title-main">Land And Development Office</h1>


    <p style="text-align: center; font-size: 14px; color:#116d6e; font-weight: bold; margin-top: 30px;">
        Payment Receipt
    </p>

    <!-- Applicant Details -->
    <div class="part-title">Payer Details</div>
    <table>
        <tr>
            <td><b>Name:</b></td>
            <td>{{ ($payment->payerDetails->first_name ?? '') . ' ' . ($payment->payerDetails->last_name ?? '') ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><b>Email:</b></td>  
            <td>{{ $payment->payerDetails->email ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><b>Mobile:</b></td> 
            <td>{{ $payment->payerDetails->mobile ?: 'N/A' }}</td>
        </tr>
    </table>

    <!-- Property Details -->
    <div class="part-title">Property Details</div>
    
    <table>
        <tr>
            <td><b>Property ID:</b></td>
            <td>{{ $payment->property->old_propert_id ?? 'N/A' }}</td>
            <td><b>Splited Property ID:</b></td>
            <td>{{ $payment->splited_old_property_id ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><b>Colony:</b></td>
            <td>{{ $payment->property->newColony->name ?? 'N/A' }}</td>
            <td><b>Land Type:</b></td>
            <td>{{ $payment->property->land_type_name ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><b>Property Type:</b></td>
            <td>{{ $payment->property->property_type_name ?: 'N/A' }}</td>
            <td><b>Property Sub-Type:</b></td>
            <td>{{ $payment->property->property_subtype_name ?: 'N/A' }}</td>
        </tr>
        <tr>
            <td><b>Block No.:</b></td>
            <td>{{ $payment->property->block_no ?: 'N/A' }}</td>
            <td><b>Plot No./Property No.:</b></td>
            <td>{{ $payment->property->plot_or_property_no ?: 'N/A' }}</td>
        </tr>
    </table>

    <!-- Transaction Details -->
    <div class="part-title">Transaction Details</div>
    @foreach (($payments ?? [$payment]) as $txn)
        <table>
            <tr>
                <td><b>Payment ID:</b></td>
                <td>{{ $txn->unique_payment_id ?: 'N/A' }}</td>
                <td><b>Demand ID:</b></td>
                <td>{{ $txn->demand->unique_id ?? 'N/A' }}</td>
            </tr>
            <tr>
            {{--  <td><b>Payment Purpose:</b></td>
                <td>{{ $txn->paymentTypeItem->item_name ?? 'N/A' }}</td>  --}}
                <td><b>Payment Purpose:</b></td>
                <td>
                    @if(($txn->paymentTypeItem->item_name ?? '') === 'Application' && $txn->application)
                        {{ $txn->application->serviceTypeItem->item_name ?? 'Application' }}
                    @else
                        {{ $txn->paymentTypeItem->item_name ?? 'N/A' }}
                    @endif
                </td>
                <td><b>Paid Amount:</b></td>
                <td>₹ {{ $txn->amount ? number_format($txn->amount, 2) : 'N/A' }}</td>
            </tr>
            <tr>
                <td><b>Payment Mode:</b></td>
                <td>{{ $txn->paymentModeItem->item_name ?? 'N/A' }}</td>
                <td><b>Reference No.:</b></td>
                <td>{{ $txn->transaction_id ?: 'N/A' }}</td>
                
            </tr>
            <tr>
                <td><b>Transaction Date:</b></td>
                <td>{{ $txn->created_at ?\Carbon\Carbon::parse($txn->created_at)->timezone('Asia/Kolkata')->format('d-m-Y h:i A') : 'N/A' }}</td>
                <td><b>Payment Received at L&DO on:</b></td>
                <td>{{ $txn->updated_at ? \Carbon\Carbon::parse($txn->updated_at)->timezone('Asia/Kolkata')->format('d-m-Y h:i A') : 'N/A' }}</td>
            </tr>
        </table>

            @php
                $amt = number_format((float)$txn->amount, 2, '.', '');
                [$r, $p] = array_pad(explode('.', $amt), 2, 0);
                $words = convertNumberToWords((int)$r);
                if ((int)$p > 0) {
                    $words .= ' and ' . convertNumberToWords((int)$p) . ' paise';
                }
                $amtWords = 'Rupees ' . ucfirst($words) . ' only';

                $purpose = ($txn->paymentTypeItem->item_name ?? '') === 'Application' && $txn->application
                ? ($txn->application->serviceTypeItem->item_name ?? 'Application')
                : ($txn->paymentTypeItem->item_name ?? 'N/A');

            @endphp

        @if(($txn->paymentTypeItem->item_name ?? '') === 'Demand')

            <p style="text-align: center; font-size: 10px; font-weight: bold; margin-top: 40px;">
                *** Received a sum of ₹ {{ number_format($txn->amount, 2) }}
            ({{ $amtWords }}) against the demand reference
            {{ $txn->demand->unique_id ?? 'N/A' }}. 
            We thank you for making the payment through the L&amp;DO ePayment system. *** </p> <br>

        @else

            <p style="text-align: center; font-size: 10px; font-weight: bold; margin-top: 40px;">
                *** Received a sum of ₹ {{ number_format($txn->amount, 2) }}
            ({{ $amtWords }}) for {{ $purpose }}. 
            We thank you for making the payment through the L&amp;DO ePayment system. *** </p> <br>

        @endif
    @endforeach


    <p style="text-align: right; font-size: 9px; margin-top: 40px;">
        Generated on: {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d-m-Y h:i A') }} IST
    </p>



</body>

</html>
