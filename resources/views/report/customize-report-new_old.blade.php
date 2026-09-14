@extends('layouts.app')

@section('title', 'Customized Report')

@section('content')
    <style>
        label.form-check-label {
            margin: auto -0.5rem;
            padding-left: 10px;
        }

        div.dt-buttons {
            float: none !important;
            width: 19%;
        }

        div.dt-buttons.btn-group {
            margin-bottom: 20px;
        }

        div.dt-buttons.btn-group .btn {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* Ensure responsiveness on smaller screens */
        @media (max-width: 768px) {
            div.dt-buttons.btn-group {
                flex-direction: column;
                align-items: flex-start;
            }

            div.dt-buttons.btn-group .btn {
                width: 100%;
                text-align: left;
            }
        }

        .unit-green {
            color: green;
        }

        .unit-red {
            color: red;
        }

        .wrap-column {
            white-space: normal !important;
            word-wrap: break-word;
        }
    </style>

    <div id="flash-message" style="display: none;" class="alert alert-danger text-white">No data available to export.
    </div>
    {{-- <link rel="stylesheet" href="{{asset('assets/css/rgr.css')}}"> --}}
    <!--breadcrumb-->
 <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Customized Report</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Customized Report</li>

            </ol>
        </nav>
    </div>
</div>
    <!--breadcrumb-->
    <!--end breadcrumb-->
    <hr>
    <div class="card">
        <div class="card-header">
            <h5>Customized Report</h5>
        </div>
        <div class="card-body">

            <form id="filter-form" method="get" action="{{ route('customizeReport') }}">
                <input type="hidden" name="export" id="export" value="1">
                <input type="hidden" name="export_format" id="export_format" value="" />
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="report_type">Type of Report</label>
                        <select name="report_type" id="report_type" class="form-select">
                            <option value="">Select</option>
                            @foreach ($reportTypes as $key => $reportType)
                                <option value="{{ $key }}" {{ old('report_type') == $key ? 'selected' : '' }}>
                                    {{ $reportType }}
                                </option>
                            @endforeach
                        </select>
                        <div id="report_typeError" class="text-danger"></div>
                        @if ($errors->has('report_type'))
                            <div class="text-danger">{{ $errors->first('report_type') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3" id="sectionDiv" style="display: none;">
                        <label for="section">Sections</label>
                        <select name="section" id="section" class="form-select">
                            <option value="">Select</option>
                            @foreach ($sections as $key => $section)
                                <option value="{{ $section->id }}">{{ $section->section_code }}
                                </option>
                            @endforeach
                        </select>
                        <div id="sectionError" class="text-danger"></div>
                        @if ($errors->has('section'))
                            <div class="text-danger">{{ $errors->first('section') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="button" class="btn btn-primary w-100" id="apply-btn">Apply</button>
                        <button type="submit" class="btn btn-info w-100" id="export-btn">Export CSV</button>
                        <button type="button" class="btn btn-secondary w-100 export-type" data-format="pdf"
                            id="export-pdf-btn">Export
                            PDF</button>
                    </div>
                </div>

            </form>


            <div style="margin-top: 2%;">
                <table id="reportTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <!-- Table headers will be populated dynamically -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Table data will be populated dynamically -->
                    </tbody>
                </table>
            </div>

        </div>

    </div>
    <div id="spinnerOverlay" style="display:none;">
        <img src="{{ asset('assets/images/chatbot_icongif.gif') }}">
    </div>
@endsection

@section('footerScript')
    <script src="{{ asset('assets/js/jspdf.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/jspdf.plugin.autotable.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Define column configurations for each report type
            var reportColumns = {
                'SWPC': [{
                        data: 'section_name',
                        name: 'section_name',
                        title: 'SECTION NAME'
                    },
                    {
                        data: 'number_of_properties',
                        name: 'number_of_properties',
                        title: 'NUMBER OF PROPERTIES'
                    }
                    // Add more columns as needed for this report type
                ],
                'CLHFHUA': [{
                        data: 'colony_name',
                        name: 'colony_name',
                        title: 'COLONY NAME'
                    },
                    {
                        data: 'lease_hold_count',
                        name: 'lease_hold_count',
                        title: 'LEASE HOLD'
                    },
                    {
                        data: 'free_hold_count',
                        name: 'free_hold_count',
                        title: 'FREE HOLD'
                    },
                    {
                        data: 'unallotted_count',
                        name: 'unallotted_count',
                        title: 'UNALLOTED'
                    },
                    {
                        data: 'total_count',
                        name: 'total_count',
                        title: 'TOTAL'
                    }
                    // Add more columns as needed for this report type
                ],
                'TWPC': [{
                        data: 'property_type',
                        name: 'property_type',
                        title: 'PROPERTY TYPE'
                    },
                    {
                        data: 'number_of_properties',
                        name: 'number_of_properties',
                        title: 'NUMBER OF PROPERTIES'
                    }
                    // Add more columns as needed for this report type
                ],
                'PIAS': [{
                        data: 'unique_propert_id',
                        name: 'unique_propert_id',
                        title: 'UNIQUE PROPERTY ID'
                    },
                    {
                        data: 'property_id',
                        name: 'property_id',
                        title: 'PROPERTY ID'
                    },
                    {
                        data: 'land_type',
                        name: 'land_type',
                        title: 'LAND TYPE'
                    },
                    {
                        data: 'property_status',
                        name: 'property_status',
                        title: 'PROPERTY STATUS'
                    },
                    {
                        data: 'property_type',
                        name: 'property_type',
                        title: 'PROPERTY TYPE'
                    },
                    {
                        data: 'property_sub_type',
                        name: 'property_sub_type',
                        title: 'PROPERTY SUB TYPE'
                    },
                    {
                        data: 'area_in_sqm',
                        name: 'area_in_sqm',
                        title: 'AREA IN SQM.'
                    },
                    {
                        data: 'date_of_execution',
                        name: 'date_of_execution',
                        title: 'DATE OF EXECUTION'
                    },
                    {
                        data: 'current_lesse_name',
                        name: 'current_lesse_name',
                        title: 'CURRENT LESSE NAME'
                    },
                    /* {
                        data: 'is_joint_property',
                        name: 'is_joint_property',
                        title: 'IS JOINT PROPERTY'
                    } */
                ]
            };

            $('#apply-btn').on('click', function() {
                document.querySelectorAll('.text-danger').forEach(function(el) {
                    el.innerText = '';
                });
                var reportType = $('#report_type').val();
                var section = $('#section').val(); // Capture section selection if needed
                var reportTypeError = $("#report_typeError");
                var sectionError = $("#sectionError");

                // Reset previous error messages
                reportTypeError.hide();
                sectionError.hide();

                if (!reportType) {
                    reportTypeError.text("Report type is required").show();
                    return false;
                }

                if (reportType === 'PIAS' && !section) {
                    sectionError.text("Section is required").show();
                    return false;
                }

                if (reportType) {
                    // Destroy existing DataTable instance if it exists
                    if ($.fn.DataTable.isDataTable('#reportTable')) {
                        $('#reportTable').DataTable().destroy();
                        $('#reportTable').empty(); // Clear the table header and body
                    }

                    // Get columns for the selected report type
                    var columns = reportColumns[reportType];

                    if (columns) {
                        // Create table header dynamically
                        var thead = '<thead><tr>';
                        columns.forEach(function(column) {
                            thead += '<th>' + column.title + '</th>';
                        });
                        thead += '</tr></thead>';
                        $('#reportTable').html(thead);

                        // Initialize DataTable with dynamic columns
                        $('#reportTable').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '{{ route('get.customized.reports.data') }}',
                                data: {
                                    report_type: reportType,
                                    section: section // Include section if needed
                                }
                            },
                            columns: columns,
                            /*  dom: '<"top"Blf>rt<"bottom"ip><"clear">',
                            buttons: [{
                                    extend: 'csvHtml5',
                                    text: 'CSV',
                                    className: 'btn btn-outline-primary',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                },
                                {
                                    extend: 'pdfHtml5',
                                    orientation: 'landscape',
                                    pageSize: 'A3',
                                    customize: function(doc) {
                                        // Example: Adjust the font size
                                        doc.defaultStyle.fontSize = 8;
                                        // Example: Add a header to each page
                                        doc['header'] = (function() {
                                            return {
                                                text: 'Custom Header',
                                                alignment: 'center',
                                                fontSize: 12,
                                                bold: true
                                            }
                                        });
                                    }
                                },
                            ] */
                        });
                    } else {
                        alert('No column configuration found for the selected report type.');
                    }
                } else {
                    alert('Please select a report type.');
                }
            });
        });

        $(document).ready(function() {
            function updateLayout() {
                var selectedValue = $("#report_type").val();
                var classOne = selectedValue === 'PIAS' ? 'col-lg-6' : 'col-lg-8';
                var classSecond = 'col-lg-2';
                $("#type_of_report").attr("class", classOne);
                $("#sectionDiv").attr("class", classSecond).toggle(selectedValue === 'PIAS');
            }

            $("#report_type").change(updateLayout);
            updateLayout(); // Initialize on page load
        });

        document.getElementById('export-pdf-btn').addEventListener('click', async function() {
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            const reportType = document.getElementById('report_type').value;
            const section = document.getElementById('section')?.value || '';
            const export_format = $(this).data('format');

            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape'
            });

            try {
                // Fetch data from the server
                const url =
                    `{{ route('get.customized.reports.data') }}?report_type=${reportType}&section=${section}&export_format=${export_format}`;
                const response = await fetch(url);
                const result = await response.json();

                if (!result || !result.data || result.data.length === 0) {
                    const flashDiv = document.getElementById('flash-message');
                    if (flashDiv) {
                        flashDiv.textContent = 'No data available to export.';
                        flashDiv.style.display = 'block';
                        // Optional: auto-hide after 5 seconds
                        setTimeout(() => {
                            flashDiv.style.display = 'none';
                        }, 3000);
                    }
                    spinnerOverlay.style.display = 'none';
                    return;
                }

                const data = result.data;

                // Define headers, rows, and title
                let headers = [];
                let rows = [];
                let reportTitle = '';
                let reportHeading = '';

                switch (reportType) {
                    case 'SWPC':
                        reportTitle = 'Section_Wise_Property_Count';
                        reportHeading = 'Section Wise Property Count';
                        headers = ['SECTION NAME', 'NUMBER OF PROPERTIES'];
                        rows = data.map(item => [item.section_name, item.number_of_properties]);
                        break;
                    case 'CLHFHUA':
                        reportTitle = 'Colony_Wise_Lease_Hold_Free_Hold_And_Unalloted_Property_Count';
                        reportHeading = 'Colony Wise Lease Hold, Free Hold And Unalloted Property Count';
                        headers = ['COLONY NAME', 'LEASE HOLD', 'FREE HOLD', 'UNALLOTTED', 'TOTAL'];
                        rows = data.map(item => [
                            item.colony_name,
                            item.lease_hold_count,
                            item.free_hold_count,
                            item.unallotted_count,
                            item.total_count
                        ]);
                        break;
                    case 'TWPC':
                        reportTitle = 'Property_Type_Wise_Count';
                        reportHeading = 'Property Type Wise Count';
                        headers = ['PROPERTY TYPE', 'NUMBER OF PROPERTIES'];
                        rows = data.map(item => [item.property_type, item.number_of_properties]);
                        break;
                    case 'PIAS':
                        reportTitle = `Property_In_A_Section`;
                        const selectedSectionText = $('#section option:selected').text().trim().replace(/\s+/g,
                            '_');
                        reportHeading = `Properties in a section (${selectedSectionText})`;
                        headers = [
                            'UNIQUE PROPERTY ID',
                            'PROPERTY ID',
                            'LAND TYPE',
                            'PROPERTY STATUS',
                            'PROPERTY TYPE',
                            'PROPERTY SUB TYPE',
                            'AREA IN SQM',
                            'DATE OF EXECUTION',
                            'CURRENT LESSEE NAME',
                            'IS JOINT PROPERTY'
                        ];
                        rows = data.map(item => [
                            item.unique_propert_id || '',
                            item.property_id || '',
                            item.land_type || '',
                            item.property_status || '',
                            item.property_type || '',
                            item.property_sub_type || '',
                            item.area_in_sqm ? parseFloat(item.area_in_sqm).toFixed(2) : '0.00',
                            item.date_of_execution || '',
                            item.current_lesse_name || '',
                            item.is_joint_property === 1 ? 'Yes' : 'No'
                        ]);
                        break;
                    default:
                        const flashDiv = document.getElementById('flash-message');
                        if (flashDiv) {
                            flashDiv.textContent = 'Invalid report type selected.';
                            flashDiv.style.display = 'block';
                            // Optional: auto-hide after 5 seconds
                            setTimeout(() => {
                                flashDiv.style.display = 'none';
                            }, 3000);
                        }
                        spinnerOverlay.style.display = 'none';
                        return;
                }

                // Add title
                doc.setFontSize(14);
                doc.text(`Report : ${reportHeading}`, 14, 15);

                // Add table
                doc.autoTable({
                    head: [headers],
                    body: rows,
                    startY: 20,
                    styles: {
                        fontSize: 8
                    }
                });

                // Save the PDF
                doc.save(`${reportTitle}_Report.pdf`);
                spinnerOverlay.style.display = 'none';
            } catch (error) {
                const flashDiv = document.getElementById('flash-message');
                if (flashDiv) {
                    flashDiv.textContent = 'An error occurred while generating the PDF.';
                    flashDiv.style.display = 'block';
                    // Optional: auto-hide after 5 seconds
                    setTimeout(() => {
                        flashDiv.style.display = 'none';
                    }, 3000);
                }
                spinnerOverlay.style.display = 'none';
                return;
            }
        });
    </script>
@endsection
