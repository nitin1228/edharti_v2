@extends('layouts.app')

@section('title', 'Demand Listing')

@section('content')

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Demand</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Demand</li>
                <li class="breadcrumb-item active" aria-current="page">Created Demands</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>

<hr>
<div class="card">
    <div class="card-body">
        {{-- <div class="d-flex justify-content-end">
            <ul class="d-flex gap-3">
                <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                    <div class="alertDot"></div>
                    <span class="text-secondary">Have To Take Action</span>
                </li>
            </ul>
        </div> --}}
        <!-- added div table-responsive by anil on 21-11-2025 -->
        <div class="table-responsive">
            <table id="example" class="table table-striped display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Unique Demand Id</th>
                        <th>Property ID</th>
                        <th>Known As</th>
                        <th> Financial Year</th>
                        <th>Net Total</th>
                        <th>Approved Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($demands as $demand)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$demand->unique_id}}</td>
                        <td>{{$demand->old_property_id}} 
                            @if($demand->flat)
                            <br>
                            <span style="font-size: 12px; color: #6c757d;">(Flat No. - {{$demand->flat->flat_number}})</span>
                            @endif  
                        </td>
                        <td>{{$demand->property_known_as}}</td>
                        <td>{{$demand->current_fy}}</td>
                        <td>₹&nbsp;{{customNumFormat(round($demand->net_total,2))}}</td>
                         <td> {{ !empty($demand->approved_at)  ? \Carbon\Carbon::parse($demand->approved_at)->format('d-m-Y')  : 'N/A' }}</td>

                        <td>{{getServiceNameById($demand->status)}}</td>
                        <td>
                            <a href="{{route('ViewDemand',$demand->id)}}">
                                <button class="btn btn-info">View</button>
                            </a>
                            @unlessrole('internal-audit-cell')
                                @if(getServiceCodeById($demand->status) == "DEM_DRAFT" || getServiceCodeById($demand->status) == 'DEM_WD')
                                <a href="{{route('EditDemand',$demand->id)}}">
                                    <button class="btn btn-warning">Edit</button>
                                </a>
                                @endif
                                @if(getServiceCodeById($demand->status) == "DEM_PENDING")
                                <a href="{{route('withdrawDemand',$demand->id)}}" onclick="withdrawConfirmModal('Are you sure to withdraw this demand?', this); return false;">
                                    <button class="btn btn-danger">Withdraw</button>
                                </a>
                                
                                {{-- <a href="javascript;">
                                    <button class="btn btn-success">Send Mail</button>
                                </a> --}}
                                @endif
                            @endunlessrole
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8"> No Data to Display</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('include.alerts.ajax-alert')
@include('include.alerts.delete-confirmation')
@endsection


@section('footerScript')
<script>
let confirmationCallback = null;

    function withdrawConfirmModal(customMessage, element) {
        document.getElementById('customConfirmationMessage').textContent = customMessage;
        confirmationCallback = function() {
            window.location.href = element.getAttribute('href');
        };
        $('#ModalDelete').modal('show');
    }

    $('#confirmDelete').click(() => {
        // If the callback is defined, call it
        if (confirmationCallback) {
            confirmationCallback();
            $('#ModalDelete').modal('hide'); // Close the modal after confirming
        }
    });
    </script>
    <script>
$(document).ready(function() {
    $('#example').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                text: 'Export CSV',
                className: 'btn btn-info',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});
</script>
@endsection
