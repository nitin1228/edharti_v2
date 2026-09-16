@extends('layouts.app')

@section('title', 'Lease Hold Demand Report')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
   <div class="breadcrumb-title pe-3">Reports</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                 <li class="breadcrumb-item active" aria-current="page">Reports</li>
                <li class="breadcrumb-item active" aria-current="page">Lease Hold Demand Report</li>
            </ol>
        </nav>
    </div>
</div>
<!--breadcrumb-->
<!--end breadcrumb-->
{{-- @dd($filters) --}}
<hr>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                

                <div class="table-responsive mt-2">
                    <table id="reportTable" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Property Id</th>
                                <th>Known as</th>
                                <th>Section</th>
                                <th>Area(Sqm)</th>
                                
                                <th>Outstanding Amount</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @forelse($properties as $prop)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$prop->old_property_id}}</td>
                                <td>{{$prop->known_as}}</td>
                                <td>{{$prop->section}}</td>
                               
                                <td>{{round($prop->area_in_sqm,2)}}</td>
                                
                                <td> &#8377; {{customNumFormat($prop->outstanding)}}</td>
                                <td>
                                  
                                    <button class="btn btn-success" onclick="loadDemandDetails({{$prop->old_property_id}})" 
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewDemandModal">View Details</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">No Data to Display</td>
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

<div class="modal fade" id="viewDemandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                Demand details of &nbsp;<span id="propName"></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="new-demand-details"  style="display: none">
                    <div class="col-md-12">
                        <h3>Demand Details</h3>
                        <table class="table table-bordered table-striped" id="new-demand-table">
                            <thead>
                                <th>S. No.</th>
                                <th>Demand Id</th>
                                <th>Demand Date</th>
                                <th>Demand Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row" id="old-demand-details" style="display: none">
                    <div class="col-md-12">
                        <h3>Edharti 1.0 Demands</h3>
                        <table class="table table-bordered table-striped" id="old-demand-table">
                            <thead>
                                <th>S. No.</th>
                                <th>Demand Id</th>
                                <th>Demand Date</th>
                                <th>Demand Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>


@endsection

@section('footerScript')
<script>
    $(document).ready(function() {
        var table = $('#reportTable').DataTable({
            responsive: false,
            searching: true,
            paging: true,
            info: true
        });
    });

    const loadDemandDetails = propertyId=>{
        var responseUrl = "{{ route('getExistingPropertyDemand', ['oldPropertyId' => '__ID__']) }}";
        responseUrl = responseUrl.replace('__ID__', propertyId);

        $.ajax({
            type:'get',
            url: responseUrl,
            success: response=>{
               // console.log(response);
               if(response.data){
                let data = response.data;
                $('#propName').html(data.propertyContactDetails.address);
                 $('#old-demand-table tbody').empty();
                if(data.previousDemands){
                    $('#old-demand-details').show();
                    data.previousDemands.forEach((item,index) => {
                        let dataRow = `<tr>
                            <td>${index+1}</td>
                            <td>${item.demand_id}</td>
                            <td>${item.demand_date.split('-').reverse().join('-')}</td>
                            <td>&#8377; ${customNumFormat(item.amount)}</td>
                            <td>&#8377; ${customNumFormat(item.paid_amount)}</td>
                            <td>&#8377; ${customNumFormat(item.outstanding)}</td>
                            </tr>`;
                        $('#old-demand-table tbody').append(dataRow);
                    });
                }
                else{
                    $('#old-demand-details').hide();
                }

                if(data.demand){
                    $('#new-demand-details').show();
                    $('#new-demand-table tbody').empty()
                    // data.previousDemands.forEach((item,index) => {
                        let demand = data.demand
                        // alert(JSON.stringify(demand))
                        let d = new Date(demand.approved_at);
                        let dataRow = `<tr>
                            <td>${1}</td>
                            <td>${demand.unique_id}</td>
                            <td>${formatDateToDDMMYYYY(demand.approved_at)}</td>
                            <td>&#8377; ${customNumFormat(demand.net_total)}</td>
                            <td>&#8377; ${customNumFormat(demand.paid_amount)}</td>
                            <td>&#8377; ${customNumFormat(demand.balance_amount)}</td>
                            </tr>`;
                        $('#new-demand-table tbody').append(dataRow);
                    // });
                }
                else{
                    $('#new-demand-details').hide();
                }
               }
            }
        })
    }
</script>
@endsection