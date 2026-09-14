@extends('layouts.app')

@section('title', 'Applications Summary Deatils')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Application</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>

                <li class="breadcrumb-item">Applications</li>
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <!-- <li class="breadcrumb-item active" aria-current="page">All Applications</li> -->
            </ol>
        </nav>
    </div>
</div>
<hr>
<div class="container-fluid general-widget g-0">
    

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <!-- <h5 class="card-title">All Applications</h5> -->
                    <div class="table-responsive mt-2">
                        <table  class="display nowrap" id="tab-all-applications">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Property Id</th>
                                    <th>Application No.</th>
                                    <th>Application type</th>
                                    <th>Submit Date</th>
                                    <th>Status<br><select id="status-filter" class="form-select-sm"><option value="">All</option></select></th>
                                    <th>Disposal Date</th>
                                    <th>Days for Disposal</th>                                    
                                </tr>
                      <!--          <tr id="filter-row">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>-->
                            </thead>
                            <tbody>
                                @forelse ($applications as $app)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$app->applicationData?->old_property_id??''}}</td>
                                    <td>{{$app->application_no}}</td>
                                    <td>{{getServiceNameById($app->service_type)}}</td>
                                    <td>{{date('d-m-Y',strtotime($app->created_at))}}</td>
                                    <td>{{getServiceNameById($app->status)}}</td>
                                    <td>{{!is_null($app->disposed_at) ? date('d-m-Y', strtotime($app->disposed_at)):'N/A'}}</td>
                                    <td>{{!is_null($app->disposed_at) ? round((strtotime($app->disposed_at) - strtotime($app->created_at)) / (60 * 60 * 24)).' days':'N/A'}}</td>
                                    <!-- <td></td> -->
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8">
                                        <h3>No Data to Display</h3>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScript')
<script>
//var commonExportOptions = {
//		    columns: function (idx, data, node) {
//		        return idx !== 1; // Exclude first column (index 0)
//		    }
//		};
//var commonExportOptions = {
//    columns: ':visible',
//    format: {
//        header: function (data, columnIdx) {
//            // Only export actual header row (skip filter dropdowns)
//            var headerRow = $('#tab-all-applications thead tr:eq(0) th');
//            return $(headerRow[columnIdx]).text();
//        }
//    }
//};

$(document).ready(function () {
    var table = $('#tab-all-applications').DataTable({
        responsive: false,
        searching: true,
        paging: false,
        info: false,
        dom: 'Bfrtip',
                 buttons: [
    {
        extend: 'excelHtml5',
         text: 'EXCEL',
        exportOptions: {
            columns: ':visible',
            format: {
                header: function (data, columnIdx) {
                    // Sirf plain text return karo — dropdown HTML hata do
                    return $('#tab-all-applications thead tr th').eq(columnIdx).contents().filter(function () {
                        return this.nodeType === 3; // Text nodes only (ignore select)
                    }).text().trim();
                }
            }
        }
    },
    {
        extend: 'csvHtml5',
         text: 'CSV',
        exportOptions: {
            columns: ':visible',
            format: {
                header: function (data, columnIdx) {
                    return $('#tab-all-applications thead tr th').eq(columnIdx).contents().filter(function () {
                        return this.nodeType === 3;
                    }).text().trim();
                }
            }
        }
    },
    {
        extend: 'pdfHtml5',
         text: 'PDF',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
            columns: ':visible',
            format: {
                header: function (data, columnIdx) {
                    return $('#tab-all-applications thead tr th').eq(columnIdx).contents().filter(function () {
                        return this.nodeType === 3;
                    }).text().trim();
                }
            }
        },
        customize: function (doc) {
            doc.defaultStyle.fontSize = 8;
            doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
            doc.content[1].table.body.forEach(function (row) {
                row.forEach(function (cell) {
                    cell.margin = [2, 2, 2, 2];
                });
            });
        }
    }
]
,
        columnDefs: [
            { orderable: false, targets: 5 }, 
    { orderable: true, targets: '_all' } 
        ]
    });
//    var statusColumn = table.column(5);
//    var select = $('#status-filter');
//    statusColumn.data().unique().sort().each(function (d) {
//        if (d) {
//            select.append('<option value="' + d + '">' + d + '</option>');
//        }
//    });
 var statusMap = {
    "APP_NEW": "New",
    "APP_PEN": "Pending",
    "APP_IP": "In Progress",
    "APP_OBJ": "Objected",
    "APP_APR": "Approved",
    "APP_REJ": "Rejected",
    "APP_CAN": "Cancelled",
    "APP_HOLD": "Hold"
};

var select = $('#status-filter');
var urlParams = new URLSearchParams(window.location.search);
var urlStatusParam = urlParams.get('status'); 
var urlStatusList = urlStatusParam ? urlStatusParam.split(',') : [];
var uniqueStatuses = [...new Set(urlStatusList)];
select.empty();
select.append('<option value="">All</option>');
uniqueStatuses.forEach(function(status) {
    var label = statusMap[status] || status;
    select.append('<option value="' + label + '">' + label + '</option>');
});
var statusColumn = table.column(5);
select.on('change', function () {
    var val = $.fn.dataTable.util.escapeRegex($(this).val());
    statusColumn.search(val ? '^' + val + '$' : '', true, false).draw();
});
});

//	$(document).ready(function () {
//        var table = $('#tab-all-applications').DataTable({
//            responsive: false,
//            searching: true,
//            paging: false,
//            info: false,
//            dom: 'Bfrtip', // Buttons ke liye yeh zaroori hai
//
//            buttons: [
//                {
//                    extend: 'excelHtml5',
//                    exportOptions: commonExportOptions
//                },
//                {
//                    extend: 'csvHtml5',
//                    exportOptions: commonExportOptions
//                },
//                {
//                    extend: 'pdfHtml5',
//                    orientation: 'landscape',
//                    pageSize: 'A4',
//                    exportOptions: commonExportOptions,
//                    customize: function (doc) {
//                        doc.defaultStyle.fontSize = 8;
//                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
//                        doc.content[1].table.body.forEach(function (row) {
//                            row.forEach(function (cell) {
//                                cell.margin = [2, 2, 2, 2];
//                            });
//                        });
//                    }
//                }
//            ],
//
//            columnDefs: [
//                { orderable: true, targets: '_all' } // Allow sorting on all columns
//            ],
//initComplete: function () {
//                // Only target column index 5 (Status)
//                var column = this.api().column(5);
////                var statuses = [
////                    "New",
////                    "Pending",
////                    "In Progress",
////                    "Objected",
////                    "Approved",
////                    "Rejected",
////                    "Cancelled",
////                    "Hold"
////                ];
//                var select = $('<select class="form-select"><option value="">All</option></select>')
//                    .appendTo($('#filter-row th').eq(5).empty())
//                    .on('change', function () {
//                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
//                        column
//                            .search(val ? '^' + val + '$' : '', true, false)
//                            .draw();
//                    });
//                column.data().unique().sort().each(function (d, j) {
//                    d = $('<div>').html(d).text(); // Clean HTML if needed
//                    if (d && !select.find("option[value='" + d + "']").length) {
//                        select.append('<option value="' + d + '">' + d + '</option>');
//                    }
//                });
//// statuses.forEach(function (status) {
////                    select.append('<option value="' + status + '">' + status + '</option>');
////                });
//            }
////            initComplete: function () {
////                this.api().columns().every(function () {
////                    var column = this;
////
////                    // Skip first column if it's just serial number
////                    if (column.index() === 0) return;
////
////                    var select = $('<select><option value="">All</option></select>')
////                        .appendTo($('#filter-row th').eq(column.index()).empty())
////                        .on('change', function () {
////                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
////                            column
////                                .search(val ? '^' + val + '$' : '', true, false)
////                                .draw();
////                        });
////
////                    column.data().unique().sort().each(function (d, j) {
////                        // Clean HTML tags if present
////                        d = $('<div>').html(d).text();
////
////                        if (d && !select.find("option[value='" + d + "']").length) {
////                            select.append('<option value="' + d + '">' + d + '</option>');
////                        }
////                    });
////                });
////            }
//        });
//    });
</script>
@endsection