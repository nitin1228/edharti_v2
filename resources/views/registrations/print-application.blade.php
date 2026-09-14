<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Section Wise Report - Applications</title>
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
            font-size: 9px;
        }

        table th {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }

        .section-title {
            background-color: #e8f4f4;
            padding: 6px;
            margin: 10px 0;
            font-weight: bold;
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
        <h1>Section Wise Report - Applications</h1>
        <p>Date Range: {{ $dateRangeArray['start'] }} to {{ $dateRangeArray['end'] }}</p>
        <p>Filter: {{ $filterLabel }}</p>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle;">S.No</th>
                <th rowspan="2" style="vertical-align: middle;">Section Name</th>
                <th colspan="6" style="background-color: #e8f4f4;">NOC APPLICATION</th>
                <th colspan="6" style="background-color: #e8f4f4;">MUTATION APPLICATION</th>
            </tr>
            <tr>
                <!-- NOC Sub-headers -->
                <th>Total Submitted</th>
                <th>Total Disposed</th>
                <th>Total Objected</th>
                <th>Total Pending</th>
                <th>Total Pending (Obj + Pending)</th>
                <th>Disposed ({{ $filterLabel }})</th>
                <!-- Mutation Sub-headers -->
                <th>Total Submitted</th>
                <th>Total Disposed</th>
                <th>Total Objected</th>
                <th>Total Pending</th>
                <th>Total Pending (Obj + Pending)</th>
                <th>Disposed ({{ $filterLabel }})</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($applicationData as $row)
                <tr>
                    <td>{{ $row['s_no'] }}</td>
                    <td>{{ $row['section_name'] }}</td>
                    <!-- NOC Data -->
                    <td>{{ $row['noc']['total_submitted'] }}</td>
                    <td>{{ $row['noc']['total_disposed'] }}</td>
                    <td>{{ $row['noc']['total_objected'] }}</td>
                    <td>{{ $row['noc']['total_pending'] }}</td>
                    <td>{{ $row['noc']['total_pending_with_objected'] }}</td>
                    <td>{{ $row['noc']['disposed_last_week'] }}</td>
                    <!-- Mutation Data -->
                    <td>{{ $row['mutation']['total_submitted'] }}</td>
                    <td>{{ $row['mutation']['total_disposed'] }}</td>
                    <td>{{ $row['mutation']['total_objected'] }}</td>
                    <td>{{ $row['mutation']['total_pending'] }}</td>
                    <td>{{ $row['mutation']['total_pending_with_objected'] }}</td>
                    <td>{{ $row['mutation']['disposed_last_week'] }}</td>
                </tr>
            @endforeach
            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="2" style="text-align: center;">Total</td>
                <!-- NOC Totals -->
                <td>{{ $applicationTotals['noc_total_submitted'] }}</td>
                <td>{{ $applicationTotals['noc_total_disposed'] }}</td>
                <td>{{ $applicationTotals['noc_total_objected'] }}</td>
                <td>{{ $applicationTotals['noc_total_pending'] }}</td>
                <td>{{ $applicationTotals['noc_total_pending_with_objected'] }}</td>
                <td>{{ $applicationTotals['noc_disposed_last_week'] }}</td>
                <!-- Mutation Totals -->
                <td>{{ $applicationTotals['mut_total_submitted'] }}</td>
                <td>{{ $applicationTotals['mut_total_disposed'] }}</td>
                <td>{{ $applicationTotals['mut_total_objected'] }}</td>
                <td>{{ $applicationTotals['mut_total_pending'] }}</td>
                <td>{{ $applicationTotals['mut_total_pending_with_objected'] }}</td>
                <td>{{ $applicationTotals['mut_disposed_last_week'] }}</td>
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
