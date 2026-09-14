@extends('layouts.app')
@section('title', 'Scanned Property Files')

@section('content')

<style>
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
                    <th>Section</th>
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
        var table = $('#scannedFilesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('scanning.data') }}",
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    searchable: false
                },
                { data: 'old_property_id', name: 'old_property_id' },
                { data: 'plot_or_flat', name: 'plot_or_flat' },
                { data: 'colony_name', name: 'colony_name' },               
                { data: 'known_as', name: 'known_as' },
                { data: 'file_no', name: 'file_no' },
                { data: 'status', name: 'status' },
                { data: 'section', name: 'section' },
                { data: 'total_files', name: 'total_files'},
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        let html = data || '';
                        @if(auth()->user()->getRoleNames()->first() === 'super-admin')
                            html += ` <button class="btn btn-sm btn-danger delete-files-btn" data-old-property-id="${row.old_property_id}">
                                        Delete
                                    </button>`;
                        @endif
                        return html;
                    }
                }

                
            ],
            order: [[9, 'desc']],
            dom: '<"top"Blf>rt<"bottom"ip><"clear">',
            buttons: ['csv', 'excel'],
            scrollX: true
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
