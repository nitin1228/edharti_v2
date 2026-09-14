@extends('layouts.app')

@section('title', 'Outstanding Dues List')

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
                <li class="breadcrumb-item active" aria-current="page">Outstanding Dues</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>

<hr>
<div class="card">
    <div class="card-body">
         <!-- added table responsive div mobile view by anil on 26-11-2025 -->
        <div class="table-responsive">
            <table id="example" class="table table-striped display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Property ID</th>
                        <th>Property Status</th>
                        <th>Demand Id</th>
                        <th>Demand Amount</th>
                        <th>Paid Amount</th>
                        <th>Outstanding Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        $firstItem = $data->firstItem();
                    @endphp
                    @forelse($data as $i=>$row)
                    <tr>
                        <td>{{ $i + $firstItem}}</td>
                        <td>{{$row->property_id}}</td>
                        <td>{{$row->propertyStatus}}</td>
                        <td>{{$row->demand_id}}</td>
                        <td> &#8377; {{customNumFormat($row->amount)}}</td>
                        <td> &#8377; {{customNumFormat($row->paid_amount)}}</td>
                        <td> &#8377; {{customNumFormat($row->outstanding)}}</td>
                        <th><buton class="btn btn-primary" id="full-demand-details" onclick="viewFullDemandDetails('{{$row->demand_id}}')">View Details</button></th>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8"> No Data to Display</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex mt-2 justify-content-between">
            <span>Showing {{ $data->firstItem() }}–{{ $data->lastItem() }} of {{ $data->total() }} records</span>
          {{ $data->onEachSide(3)->links() }}
        </div>
        
    </div>
</div>
@include('include.alerts.ajax-alert')
@include('include.parts.old-demand-details-modal')

@endsection


@section('footerScript')
<script>
    function viewFullDemandDetails( demandId){
        let baseUrl = '{{ route("oldDemandBreakUp", ["oldDemandId" => "__ID__"]) }}';
        let url = baseUrl.replace('__ID__', demandId);
        $('#oldDemandSubheadsModal .modal-body').load(url);
        $('#oldDemandSubheadsModal').modal("show");
        $('#oldDemandSubheadsModal #demandId').text(demandId);
    }
</script>
@endsection