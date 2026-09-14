<!DOCTYPE html>
<html>
<head>
    <title>Demand PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }
        .emblem-div {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }
        .emblem {
            display: inline-block;
            margin: auto;
        }
        .title-main {
            color: navy;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }
        .title-sub {
            color: navy;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }
        .part-title {
            background-color: #1fa1a2;
            color: white;
            font-size: 16px;
            padding: 5px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 50px;
            color: rgba(0, 0, 0, 0.1);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <!-- Optional watermark -->
    <div class="watermark">Land and Development Office</div>

    <!-- Emblem Header -->
    <div class="emblem-div">
        <img src="{{ public_path('assets/images/emblem.png') }}" width="60" alt="Emblem" class="emblem">
    </div>
    <h1 class="title-main">Land And Development Office</h1>
    <h2 class="title-sub">Ministry of Housing and Urban Affairs</h2>
    <h2 class="title-sub">Government of India</h2>

    <!-- Demand Overview Section -->
    <div class="part-title">
        DEMAND SUMMARY
    </div>

    <table>
        <tbody>
            <tr>
                <td><strong>Address:</strong> {{ $address }}</td>
                <td><strong>Demand ID:</strong> {{ $demand->unique_id }}</td>
            </tr>
            <tr>
                <td><strong>Amount:</strong> ₹{{ number_format($demand->net_total, 2) }}</td>
                <td><strong>Date:</strong> {{ now()->setTimezone('Asia/Kolkata')->format('d-m-Y H:i:s') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Demand Details Table -->
    <div class="part-title">
        DEMAND DETAILS BREAKUP
    </div>

    <table>
        <thead>
            <tr>
                <th>Subhead Name</th>
                <th>Net Total</th>
                <th>Paid Amount</th>
                <th>Balance Amount</th>
                <th>Financial Year</th>
                <th>Formula</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($demandDetails as $detail)
                <tr>
                    <td>{{ $items[$detail->subhead_id] ?? 'N/A' }}</td>
                    <td>{{ number_format($detail->net_total, 2) ?? 'N/A' }}</td>
                    <td>{{ number_format($detail->paid_amount, 2) ?? 'N/A' }}</td>
                    <td>{{ number_format($detail->balance_amount, 2) ?? 'N/A' }}</td>
                    <td>{{ $detail->fy ?? 'N/A' }}</td>
                    <td>{{ $formulas[$detail->formula_id] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
