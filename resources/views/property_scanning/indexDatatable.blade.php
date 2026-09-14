@extends('layouts.app')
@section('title', 'Scanned Property Files')

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
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Property Scanning</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Scanned Property Files</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @can('add.scanning.files')
            <div class="d-flex justify-content-end py-3">
                <a href="{{ route('property.scanning.create') }}">
                    <button type="button" class="btn btn-primary py-2">+ Upload Scanned File</button>
                </a>
            </div>
        @endcan


        <table id="scannedFilesTable" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Property ID</th>
                    <th>Block/Plot/Flat</th>
                    <th>Colony</th>
                    <th>Known As</th>
                    <th>File No</th>
                    <th>Status</th>
                    <th>
                        <select id="sectionFilter" class="form-select form-select-sm">
                            <option value="">Section</option> {{-- default: all assigned sections --}}
                            @foreach($sections as $sec)
                                <option value="{{ $sec->section_code }}">{{ $sec->section_code }}</option>
                            @endforeach
                        </select>
                    </th>

                    <th>Total Uploaded Files</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Delete All Files by Property ID -->
<div class="modal fade" id="deleteFilesModal" data-bs-backdrop="static" tabindex="-1" role="dialog"
     aria-labelledby="deleteFilesModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content text-center">
      <div class="modal-header border-0 h-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img src="{{ asset('assets/images/warning.svg') }}" alt="confirm" class="warning_icon mb-3" style="width: 60px;">
        <h4 class="modal-title mb-2" id="deleteFilesModalLabel">Are you sure?</h4>
        <p>This will permanently delete all scanned files for <strong id="delete-old-property-id-label"></strong>.</p>
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-secondary btn-width" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger btn-width confirm-delete-files">Yes</button>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
    $(document).ready(function () {

         // 🔹 Preselect Section from URL (?section=PS1, etc.). Empty => default "Section" (all assigned)
        var sectionParam = (new URLSearchParams(window.location.search)).get('section') || '';
        if (sectionParam) {
        $('#sectionFilter').val(sectionParam);
        }

        var table = $('#scannedFilesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('scanning.data') }}",
            data: function (d) {
            d.section_code = $('#sectionFilter').val() || '';
            d.start_date = (new URLSearchParams(window.location.search)).get('start_date') || '';
            d.end_date   = (new URLSearchParams(window.location.search)).get('end_date') || '';
            }
        },
        columns: [
            { // 0: S.No.
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            },
            orderable: true,   // ✅ allow
            searchable: false
            },
            { data: 'old_property_id', name: 'old_property_id', orderable: false },   // 1
            { data: 'plot_or_flat',    name: 'plot_or_flat',    orderable: true  },   // 2 ✅ allow
            { data: 'colony_name',     name: 'colony_name',     orderable: true  },   // 3 ✅ allow
            { data: 'known_as',        name: 'known_as',        orderable: false },   // 4
            { data: 'file_no',         name: 'file_no',         orderable: false },   // 5
            { data: 'status',          name: 'status',          orderable: true  },   // 6 ✅ allow
            { data: 'section',         name: 'section',         orderable: false },   // 7
            { data: 'total_files',     name: 'total_files',     orderable: false },   // 8
            {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            render: function(data, type, row, meta) {
                let html = data || '';
                @if(auth()->user()->getRoleNames()->first() === 'super-admin')
                html += ` <button class="btn btn-sm btn-danger delete-files-btn" data-old-property-id="${row.old_property_id}">Delete</button>`;
                @endif
                return html;
            }
            } // 9
        ],
        order: [[0, 'asc']], // ✅ default sort by S.No.
        dom: '<"top"Blf>rt<"bottom"ip><"clear">',
        buttons: ['csv', 'excel'],
        lengthMenu: [
            [10,25,50,100,5000,25000],
            [10,25,50,100,5000,25000],
        ],
        scrollX: true
        });

        // ✅ When the Section dropdown changes, reload the table
        // Make sure you have a <select id="sectionFilter"> in the "Section" header (or anywhere on the page).
        $(document).on('change', '#sectionFilter', function () {
        $('#scannedFilesTable').DataTable().ajax.reload();
        });


        let selectedOldPropertyId = null;

    // open modal
    $(document).on('click', '.delete-files-btn', function () {
        selectedOldPropertyId = $(this).data('old-property-id');
        $('#delete-old-property-id-label').text(selectedOldPropertyId);
        $('#deleteFilesModal').modal('show');
    });

    // confirm delete
    $('.confirm-delete-files').on('click', function () {
        if (!selectedOldPropertyId) return;

        const $btn = $(this);
        $btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: "{{ route('scanning.deleteByProperty') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                old_property_id: selectedOldPropertyId
            },
            success: function (response) {
                $('#deleteFilesModal').modal('hide');
                selectedOldPropertyId = null;
                $btn.prop('disabled', false).text('Yes');

                if (response.status === 'success') {
                    // If you have a toast helper like showSuccess():
                    if (typeof showSuccess === 'function') {
                        showSuccess(response.message);
                    }
                    $('#scannedFilesTable').DataTable().ajax.reload(null, false);
                } else {
                    if (typeof showError === 'function') {
                        showError(response.message || 'Something went wrong.');
                    }
                }
            },
            error: function (xhr) {
                $('#deleteFilesModal').modal('hide');
                selectedOldPropertyId = null;
                $btn.prop('disabled', false).text('Yes');

                const errorMsg = xhr.responseJSON?.message || 'Something went wrong.';
                if (typeof showError === 'function') {
                    showError(errorMsg);
                }
            }
        });
    });
    });
</script>

@endsection
