@extends('layouts.app')

@section('title', 'Litigation Management')

@section('content')

<style>
    div.dt-buttons {
        float: none !important;
        width: 33%;
    }

    div.dt-buttons.btn-group {
        margin-bottom: 20px;
    }

    div.dt-buttons.btn-group .btn {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 4px;
    }

    .ellipsis-wrapper {
        max-width: 240px;
        display: inline-flex;
        align-items: center;
    }

    .ellipsis-text {
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .view-more-link {
        margin-left: 6px;
        cursor: pointer;
        color: #0d6efd;
        font-size: 12px;
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        div.dt-buttons {
            width: 100%;
        }
    }
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Litigation Management</div>

    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-alt"></i>
                    </a>
                </li>
                <li class="breadcrumb-item active">Court Cases List</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('litigation.create') }}"
                class="btn btn-primary">
                Add Court Case
            </a>
        </div>

    <div class="card mb-3 border" style="background:#f8f9fa;">

        <div class="card-body">

            <div class="d-flex align-items-center mb-3">
                <h5 class="mb-0">Filters</h5>
            </div>

            <div class="row g-3 align-items-end">

                <div class="col-md-3 col-xl-2">
                    <label class="form-label">Property ID</label>
                    <input type="text"
                        id="propertyIdFilter"
                        class="form-control"
                        maxlength="5">
                </div>

                <div class="col-md-3 col-xl-2">
                    <label class="form-label">Case Type</label>
                    <select id="caseTypeSelect" class="form-select">
                        <option value="All">All</option>

                        @foreach($caseTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-xl-2">
                    <label class="form-label">Section</label>
                    <select id="sectionSelect" class="form-select">
                        <option value="All">All</option>

                        @foreach($sections as $section)
                            <option value="{{ $section->id }}">
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-xl-2">
                    <label class="form-label">Next Hearing Date From</label>
                    <input type="date"
                        id="dateFilter"
                        class="form-control">
                </div>

                <div class="col-md-3 col-xl-2">
                    <label class="form-label">Next Hearing Date To</label>
                    <input type="date"
                        id="dateEndFilter"
                        class="form-control">
                </div>

                <div class="col-md-3 col-xl-2 d-flex gap-2">

                    <button type="button"
                        id="filterBtn"
                        class="btn btn-primary w-100">
                        Search
                    </button>

                    <button type="button"
                        id="resetBtn"
                        class="btn btn-secondary w-100">
                        Reset
                    </button>

                </div>

            </div>

        </div>

    </div>

        <table id="litigationTable" class="display litigation-table" style="width:100%">
            <thead>
                <tr>
                    <th>S.No.</th>
                    <th>Unique Case ID</th>
                    <th>Case Details</th>
                    <th>Property Details</th>
                    <th>Court Details</th>
                    <th>Section Details</th>
                    <th>Next Date of Hearing</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>

    </div>
</div>

<div class="modal fade" id="commonTextModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body"
                id="commonTextModalBody"
                style="word-break: break-word; white-space: pre-wrap;">
            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScript')

<script>
    function renderSubtextEllipsis(text, type, limit, modalTitle) {
        if (!text) return '';

        if (type === 'display' && text.length > limit) {
            let shortText = text.substring(0, limit);

            return `
                <span class="ellipsis-wrapper">
                    <span class="ellipsis-text">${shortText}...</span>
                    <span class="view-more-link view-full-text"
                        data-text="${encodeURIComponent(text)}"
                        data-title="${modalTitle}">
                        view more
                    </span>
                </span>
            `;
        }

        return text;
    }

    $(document).on('click', '.view-full-text', function () {
        let fullText = decodeURIComponent($(this).data('text'));
        let title = $(this).data('title') || 'Details';

        $('#commonTextModal .modal-title').text(title);
        $('#commonTextModalBody').text(fullText);

        let modal = new bootstrap.Modal(document.getElementById('commonTextModal'));
        modal.show();
    });

    $(document).ready(function () {
        let table = $('#litigationTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[1, 'desc']],
            ajax: {
                url: "{{ route('get.litigations') }}",
                data: function (d) {
                    d.property_id = $('#propertyIdFilter').val();
                    d.date = $('#dateFilter').val();
                    d.dateEnd = $('#dateEndFilter').val();
                    d.case_type = $('#caseTypeSelect').val();
                    d.section = $('#sectionSelect').val();
                }
            },
            columns: [
                {
                    data: null,
                    name: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    searchable: false,
                    orderable: false
                },
                { data: 'unique_case_id', name: 'unique_case_id' },
                { data: 'case_details', name: 'case_number' },
                { data: 'property_details', name: 'property_known_as' },
                { data: 'court_details', name: 'judicial_authority' },
                { data: 'section_details', name: 'section_counsel' },
                { data: 'latest_ndoh', name: 'latest_ndoh', orderable: false },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            dom: '<"top"Blf>rt<"bottom"ip><"clear">',
            buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',

                            exportOptions: {
                                columns: ':not(:last-child)',

                                modifier: {
                                    search: 'applied',
                                    order: 'applied',
                                    page: 'all'
                                },

                                format: {
                                    body: function (data, row, column, node) {
                                        return node ? node.innerText.trim() : data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'Export CSV',

                            exportOptions: {
                                columns: ':not(:last-child)',

                                modifier: {
                                    search: 'applied',
                                    order: 'applied',
                                    page: 'all'
                                },

                                format: {
                                    body: function (data, row, column, node) {
                                        return node ? node.innerText.trim() : data;
                                    }
                                }
                            }
                        }
                    ],
            scrollX: true
        });

        $('#filterBtn').on('click', function () {
            table.ajax.reload();
        });

        $('#caseTypeSelect').on('change', function () {
            table.ajax.reload();
        });

        $('#sectionSelect').on('change', function () {
            table.ajax.reload();
        });

        $('#resetBtn').on('click', function () {
            $('#propertyIdFilter').val('');
            $('#caseTypeSelect').val('All');
            $('#sectionSelect').val('All');
            $('#dateFilter').val('');
            $('#dateEndFilter').val('');

            table.ajax.reload();
        });
    });
</script>

@endsection