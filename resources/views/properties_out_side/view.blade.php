@extends('layouts.app')
@section('title', 'Outside Delhi Property Details')
@section('content')
    <style>
        .pagination .active a {
            color: #ffffff !important;
        }
    </style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Properties</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Properties</li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                    <li class="breadcrumb-item active" aria-current="page">Properties Outside Delhi</li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="part-title">
                <h5>Property Details</h5>
            </div>
            <div class="part-details">
                <div class="container-fluid">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><b>State:</b> {{ $property->state->name ?? 'N/A' }}</td>
                                <td><b>City:</b> {{ $property->city->name ?? 'N/A' }}</td>
                                {{-- <td><b>Old Property ID:</b> {{ $property->old_property_id ?? 'N/A' }}</td>
                                <td><b>File No.:</b> {{ $property->file_no ?? 'N/A' }}</td> --}}
                            </tr>
                            <tr>
                                <td colspan="2"><b>Address:</b> {{ $property->address }}</td>
                            </tr>
                            <tr>
                                <td><b>Area:</b> {{ $property->area }} Sq. Mt.</td>
                                <td><b>Present Custodian:</b> {{ $property->presentCustodian->item_name }}</td>
                            </tr>
                            <tr>
                                <td><b>Date Of Custody:</b> {{ $property->custody_date }}</td>
                                <td><b>Present Status:</b> {{ $property->presentStatus->item_name }}</td>
                            </tr>
                            <tr>
                                <td><b>Land Use:</b> {{ $property->landUse->item_name ?? 'N/A' }}</td>
                                <td><b>Present Status Details:</b> {{ $property->present_status_details }}</td>
                            </tr>
                            <tr>
                                <td><b>Is there a court case?:</b> {{ $property->court_case === 1 ? 'Yes' : 'No' }}</td>
                                <td><b>Details of Court Case:</b> {{ $property->court_case_details }}</td>
                            </tr>
                            <tr>
                                <td><b>Is it being used by any department?:</b>
                                    {{ $property->user_by_any_department === 1 ? 'Yes' : 'No' }}</td>
                                <td><b>Name of Department:</b> {{ $property->department }}</td>
                            </tr>
                            {{-- <tr>
                                <td><b>Property Status:</b> {{ $property->propertyStatus->item_name ?? 'N/A' }}</td>
                                <td><b>Land Type:</b> {{ $property->landType->item_name ?? 'N/A' }}</td>
                                <td><b>Area:</b> {{ $property->area }} Sq. Mt.</td>
                                <td><b>Received From:</b> {{ $property->received_from }}</td>

                            </tr>
                            <tr>
                                <td>
                                    <b>Custody Date:</b>
                                    {{ $property->custody_date ? \Carbon\Carbon::parse($property->custody_date)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td><b>Land Use:</b> {{ $property->landUse->item_name ?? 'N/A' }}</td>
                                <td>
                                    <b>Used by Department:</b>
                                    {{ $property->user_by_any_department ? 'Yes' : 'No' }}
                                    @if ($property->user_by_any_department && $property->department)
                                        <br><small class="text-muted">Department Name: {{ $property->department }}</small>
                                    @endif
                                </td>
                                <td><b>Encroached:</b> {{ $property->encroached ? 'Yes' : 'No' }}</td>
                            </tr> --}}
                            <tr>
                                <td colspan="4"><b>Remarks:</b> {{ $property->remarks ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endsection
    @section('footerScript')
    @endsection
