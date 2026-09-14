@extends('layouts.app')

@section('title', 'Registration Summary Details')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Registration</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item">Registrations</li>
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
                        <div class="table-responsive mt-2">
                            <table class="display nowrap" id="tab-registration-details">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Applicant Number</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Section</th>
                                        <th>Land Type</th>
                                        <th>Block/Plot/Locality</th>
                                        <th>Registration Type</th>
                                        <th>Status</th>
                                        <th>Submit Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($registrations as $reg)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $reg->applicant_number }}</td>
                                            <td>{{ $reg->name }}</td>
                                            <td>{{ $reg->email }}</td>
                                            <td>{{ $reg->mobile_no ?? 'N/A' }}</td>
                                            <td>{{ $reg->section_code ?? 'N/A' }}</td>
                                            <td>{{ $reg->land_type_name ?? 'N/A' }}</td>
                                            <td>
                                                @if ($reg->is_property_flat == 1)
                                                    Flat No: {{ $reg->flat_number ?? ($reg->flat_no ?? 'N/A') }}
                                                @else
                                                    {{ $reg->block ?? '' }}/{{ $reg->plot ?? '' }}/{{ $reg->colony_name ?? '' }}
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($reg->user_type) }}</td>
                                            <td>
                                                @php
                                                    $statusClasses = [
                                                        'RS_REJ' => 'badge bg-danger',
                                                        'RS_NEW' => 'badge bg-primary',
                                                        'RS_REW' => 'badge bg-warning',
                                                        'RS_UREW' => 'badge bg-secondary',
                                                        'RS_PEN' => 'badge bg-info',
                                                        'RS_APP' => 'badge bg-success',
                                                    ];
                                                    $class = $statusClasses[$reg->status_code] ?? 'badge bg-secondary';
                                                @endphp
                                                <span
                                                    class="{{ $class }}">{{ ucwords($reg->status_name ?? 'N/A') }}</span>
                                            </td>
                                            <td>{{ date('d-m-Y', strtotime($reg->created_at)) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11">
                                                <h3 class="text-center">No Data to Display</h3>
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
        $(document).ready(function() {
            var table = $('#tab-registration-details').DataTable({
                responsive: false,
                searching: true,
                paging: false,
                info: false,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excelHtml5',
                        text: 'EXCEL',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 8;
                            doc.content[1].table.widths = Array(doc.content[1].table.body[0]
                                .length + 1).join('*').split('');
                            doc.content[1].table.body.forEach(function(row) {
                                row.forEach(function(cell) {
                                    cell.margin = [2, 2, 2, 2];
                                });
                            });
                        }
                    }
                ],
                columnDefs: [{
                    orderable: true,
                    targets: '_all'
                }]
            });
        });
    </script>
@endsection
