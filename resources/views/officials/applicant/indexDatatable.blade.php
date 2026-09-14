@extends('layouts.app')

@section('title', 'Applicant Additional Properties Listing')

@section('content')

    <style>
        div.dt-buttons {
            float: none !important;
            /* width: 19%; */
            width: 33%;
            /* chagned by anil on 28-08-2025 to fix in resposive */
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
            div.dt-buttons {
                width:100%;
            }
            
            div.dt-buttons.btn-group {
                flex-direction: column;
                align-items: flex-start;
            }

            div.dt-buttons.btn-group .btn {
                width: 100%;
                text-align: left;
            }
        }

        .popover {
            max-width: 400px;
            z-index: 9999;
        }

        .popover-body {
            padding: 10px;
        }

        .popover-body a.document-link {
            display: block;
            color: #0d6efd;
            text-decoration: none;
            padding: 5px;
            transition: all 0.2s;
        }

        .popover-body a.document-link:hover {
            background-color: #f8f9fa;
            text-decoration: underline;
        }

        .popover-body div {
            margin-bottom: 5px;
        }

        .document-trigger,
        .remark-trigger {
            cursor: pointer;
            display: inline-block;
        }

        .document-trigger:hover,
        .remark-trigger:hover {
            opacity: 0.8;
        }

        /* Ensure links in popover are clickable */
        .popover-body a {
            pointer-events: auto;
            cursor: pointer;
        }

        /* Custom scrollbar for popover */
        .popover-body::-webkit-scrollbar {
            width: 6px;
        }

        .popover-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .popover-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .popover-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">REGISTRATION</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Additional Properties</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant Number</th>
                        <th>Name</th>
                        <th>Land Type</th>
                        <th>Property Details</th>
                        <th>Is Flat</th>
                        <th>Document</th>
                        <th><select class="form-control form-select form-select-sm" name="status" id="status"
                                style="font-weight: bold;">
                                <option value="">Status</option>
                                @foreach ($items as $item)
                                    <option class="text-capitalize" value="{{ $item->id }}" @if ($getStatusId == $item->id)
                                        @selected(true)
                                    @endif>{{ $item->item_name }}
                                    </option>
                                @endforeach
                            </select></th>
                        <th>Remark</th>
                        <th>Created On</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
    <div id="tooltip"></div>
@endsection


@section('footerScript')

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                ajax: {
                    url: "{{ route('get.applicant.property.listings') }}",
                    data: function(d) {
                        d.status = $('#status').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'applicant_number',
                        name: 'applicant_number'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'land_type_name',
                        name: 'land_type_name'
                    },
                    {
                        data: 'property_details',
                        name: 'property_details'
                    },
                    {
                        data: 'isFlat',
                        name: 'isFlat'
                    },
                    {
                        data: 'documents',
                        name: 'documents',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let documentLinks = '';
                            let hasDocuments = false;
                            let docCount = 0;

                            $.each(data, function(key, doc) {
                                if (doc) {
                                    hasDocuments = true;
                                    docCount++;
                                    let docParts = doc.split('/');
                                    let docName = docParts[docParts.length - 1];
                                    let docUrl = "{{ asset('storage/') }}/" + doc;
                                    let nameParts = docName.split('_');
                                    let prefix = nameParts[0];
                                    let displayName = '';

                                    switch (prefix) {
                                        case 'saledeed':
                                            displayName = 'Sale Deed';
                                            break;
                                        case 'BuilderAgreement':
                                            displayName = 'Builder Buyer Agreement';
                                            break;
                                        case 'leaseDeed':
                                            displayName = 'Lease Deed';
                                            break;
                                        case 'subsMutLetter':
                                            displayName = 'Substitution/Mutation Letter';
                                            break;
                                        case 'other':
                                            displayName = 'Other Document';
                                            break;
                                        case 'ownerLessee':
                                            displayName = 'Owner/Lessee Document';
                                            break;
                                        default:
                                            displayName = 'Unknown Document';
                                    }

                                    documentLinks += `<div style="padding: 5px 0; border-bottom: 1px solid #eee;">
                                    <a href='${docUrl}' target='_blank' class='document-link' style='color: #0d6efd; text-decoration: none;'>
                                        ${displayName}
                                    </a>
                                </div>`;
                                }
                            });

                            if (!hasDocuments) {
                                return '<span class="text-muted">No documents</span>';
                            }

                            // Create a unique ID for this popover
                            let popoverId = 'popover_' + row.id + '_' + Math.random().toString(36)
                                .substr(2, 9);

                            return `<a href="javascript:void(0);" class="text-danger document-trigger" data-popover-id="${popoverId}" style="cursor: pointer;">
                                    <i class="bx bxs-file-pdf fs-4"></i>
                                </a>
                                <div id="${popoverId}" class="popover-content-wrapper" style="display: none;">
                                    <div style="min-width: 250px; max-width: 350px; max-height: 300px; overflow-y: auto;">
                                        ${documentLinks}
                                    </div>
                                </div>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'remark',
                        name: 'remark',
                        render: function(data, type, row) {
                            if (!data.remark) {
                                return '<span>NA</span>';
                            }

                            let escapedRemark = $('<div>').text(data.remark || '').html();
                            let assignedByName = data.assigned_by_name ?
                                $('<span>').text(' (' + data.assigned_by_name + ')')
                                .css({
                                    'font-size': '13px',
                                    'color': '#7e7e7ea1',
                                    'font-weight': '700'
                                }).html() : '';

                            let escapedData = escapedRemark + assignedByName;
                            let shortRemark = escapedData.length > 30 ? escapedData.substring(0,
                                30) + '...' : escapedData;

                            let remarkId = 'remark_' + row.id + '_' + Math.random().toString(36)
                                .substr(2, 9);

                            return `<div class="text-wrap remark-trigger" data-remark-id="${remarkId}" style="cursor: pointer; border-bottom: 1px dashed #999;">${shortRemark}</div>
                                <div id="${remarkId}" class="popover-content-wrapper" style="display: none;">
                                    <div style="max-width: 300px; word-wrap: break-word;">
                                        ${escapedData}
                                    </div>
                                </div>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">',
                buttons: [
                    'csv', 'excel', {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':not(:nth-child(7))',
                            format: {
                                header: function(data, columnIdx) {
                                    if (columnIdx === 5) {
                                        return 'Status';
                                    }
                                    return data;
                                }
                            }
                        }
                    }
                ],
                scrollX: true,
                createdRow: function(row, data, dataIndex) {
                    $('td', row).eq(6).addClass('view-hover-data show-toggle-data');
                },
                drawCallback: function(settings) {
                    // Initialize document popovers
                    $('.document-trigger').each(function() {
                        let trigger = $(this);
                        let popoverId = trigger.data('popover-id');
                        let content = $('#' + popoverId).html();

                        if (content && content.trim()) {
                            // Destroy any existing popover
                            if (trigger.data('bs.popover')) {
                                trigger.popover('dispose');
                            }

                            trigger.popover({
                                html: true,
                                content: content,
                                placement: 'top',
                                trigger: 'manual',
                                container: 'body',
                                title: 'Documents',
                                sanitize: false,
                                template: '<div class="popover" role="tooltip" style="z-index: 9999;"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
                            });

                            // Handle hover events
                            let timeout;

                            trigger.hover(function() {
                                clearTimeout(timeout);
                                trigger.popover('show');
                            }, function() {
                                let $popover = $('.popover');
                                if ($popover.length) {
                                    timeout = setTimeout(function() {
                                        if (!$popover.is(':hover')) {
                                            trigger.popover('hide');
                                        }
                                    }, 100);
                                } else {
                                    trigger.popover('hide');
                                }
                            });

                            // Keep popover open when hovering over it
                            $(document).on('mouseenter', '.popover', function() {
                                clearTimeout(timeout);
                            }).on('mouseleave', '.popover', function() {
                                trigger.popover('hide');
                            });
                        }
                    });

                    // Initialize remark popovers
                    $('.remark-trigger').each(function() {
                        let trigger = $(this);
                        let remarkId = trigger.data('remark-id');
                        let content = $('#' + remarkId).html();

                        if (content && content.trim()) {
                            // Destroy any existing popover
                            if (trigger.data('bs.popover')) {
                                trigger.popover('dispose');
                            }

                            trigger.popover({
                                html: true,
                                content: content,
                                placement: 'top',
                                trigger: 'manual',
                                container: 'body',
                                title: 'Remark',
                                sanitize: false,
                                template: '<div class="popover" role="tooltip" style="z-index: 9999;"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
                            });

                            // Handle hover events
                            let timeout;

                            trigger.hover(function() {
                                clearTimeout(timeout);
                                trigger.popover('show');
                            }, function() {
                                let $popover = $('.popover');
                                if ($popover.length) {
                                    timeout = setTimeout(function() {
                                        if (!$popover.is(':hover')) {
                                            trigger.popover('hide');
                                        }
                                    }, 100);
                                } else {
                                    trigger.popover('hide');
                                }
                            });

                            // Keep popover open when hovering over it
                            $(document).on('mouseenter', '.popover', function() {
                                clearTimeout(timeout);
                            }).on('mouseleave', '.popover', function() {
                                trigger.popover('hide');
                            });
                        }
                    });
                }
            });

            // Trigger table reload on status filter change
            $('#status').change(function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
