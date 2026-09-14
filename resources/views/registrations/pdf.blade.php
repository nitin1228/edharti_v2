<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Section Wise Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 10px;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px;
        }

        .header p {
            margin: 3px 0;
            color: #666;
            font-size: 9px;
        }

        .section-title {
            background-color: #f5f5f5;
            padding: 5px;
            margin: 10px 0 8px 0;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 8px;
        }

        table th {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        table td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        .app-header {
            background-color: #e8f4f4 !important;
        }

        table {
            table-layout: fixed;
        }

        .col-sno {
            width: 4%;
        }

        .col-section {
            width: 12%;
        }

        .col-number {
            width: 7%;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa !important;
        }

        .total-row td {
            font-weight: bold !important;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Section Wise Report</h1>
        <p>Generated on: {{ date('d-m-Y H:i:s') }}</p>
        <p>Date Range: {{ $dateRange['start'] }} to {{ $dateRange['end'] }}</p>
        <p>Filter: {{ $filterLabel ?? 'Last Week' }}</p>
    </div>

    <!-- Registration Summary -->
    <div class="section-title">Registration Summary</div>
    <table>
        <thead>
            <tr>
                <th class="col-sno">S.No</th>
                <th class="col-section">Section Name</th>
                <th class="col-number">Total Pending</th>
                <th class="col-number">Total Activated</th>
                <th class="col-number">Total Rejected ({{ $filterLabel ?? 'Last Week' }})</th>
                <th class="col-number">Total Approved ({{ $filterLabel ?? 'Last Week' }})</th>
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

    <!-- Applications Section Wise Report -->
    <div class="section-title">Section Wise Report - Applications</div>
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: middle; width: 4%;">S.No</th>
                <th rowspan="2" style="vertical-align: middle; width: 10%;">Section Name</th>
                <th colspan="6" style="background-color: #e8f4f4; text-align: center; width: 42%;">NOC APPLICATION
                </th>
                <th colspan="6" style="background-color: #e8f4f4; text-align: center; width: 42%;">MUTATION
                    APPLICATION</th>
            </tr>
            <tr>
                <!-- NOC Sub-headers -->
                <th style="font-size: 7px; width: 7%;">Total Submitted</th>
                <th style="font-size: 7px; width: 7%;">Total Disposed</th>
                <th style="font-size: 7px; width: 7%;">Total Objected</th>
                <th style="font-size: 7px; width: 7%;">Total Pending</th>
                <th style="font-size: 7px; width: 7%;">Total Pending (Obj + Pending)</th>
                <th style="font-size: 7px; width: 7%;">Disposed ({{ $filterLabel ?? 'Last Week' }})</th>
                <!-- Mutation Sub-headers -->
                <th style="font-size: 7px; width: 7%;">Total Submitted</th>
                <th style="font-size: 7px; width: 7%;">Total Disposed</th>
                <th style="font-size: 7px; width: 7%;">Total Objected</th>
                <th style="font-size: 7px; width: 7%;">Total Pending</th>
                <th style="font-size: 7px; width: 7%;">Total Pending (Obj + Pending)</th>
                <th style="font-size: 7px; width: 7%;">Disposed ({{ $filterLabel ?? 'Last Week' }})</th>
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
</body>

</html>
