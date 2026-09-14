<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Registration Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table th {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa !important;
        }

        .total-row td {
            font-weight: bold !important;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Registration Summary</h1>
        <p>Date Range: {{ $dateRangeArray['start'] }} to {{ $dateRangeArray['end'] }}</p>
        <p>Filter: {{ $filterLabel }}</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Section Name</th>
                <th>Total Pending</th>
                <th>Total Activated</th>
                <th>Total Rejected ({{ $filterLabel }})</th>
                <th>Total Approved ({{ $filterLabel }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($registrationData as $row)
                <tr>
                    <td>{{ $row['s_no'] }}</td>
                    <td>{{ $row['section_name'] }}</td>
                    <td>{{ $row['total_pending'] }}</td>
                    <td>{{ $row['total_activated'] }}</td>
                    <td>{{ $row['last_week_rejected'] }}</td>
                    <td>{{ $row['last_week_activated'] }}</td>
                </tr>
            @endforeach
            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="2" style="text-align: center;">Total</td>
                <td>{{ $registrationTotals['total_pending'] }}</td>
                <td>{{ $registrationTotals['total_activated'] }}</td>
                <td>{{ $registrationTotals['last_week_rejected'] }}</td>
                <td>{{ $registrationTotals['last_week_activated'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This report is automatically generated from the system.</p>
        <p>All rights reserved.</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>
</body>

</html>
