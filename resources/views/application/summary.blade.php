@extends('layouts.app')

@section('title', 'Applications Summary')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Application</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
<!--
                <li class="breadcrumb-item">Application</li>-->
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <li class="breadcrumb-item active" aria-current="page">Applications Summary</li>
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
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="colonyName" class="form-label">Filter Data</label>
                            <select class="form-select" name="section_id" id="section_id" aria-label="Section Id" class="filter-input">
                                <option value="">Select Section</option>
                                @forelse ( $sections as $section)
                                <option value="{{$section->id}}">{{$section->name}}</option>
                                @empty

                                @endforelse
                                {{-- <option value="2">Property Section-III</option> --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="startDate" class="form-label">Start Date:</label>
                                    <input type="text" class="form-control" id="startDate" class="datepicker" autocomplete="off" class="filter-input">
                                </div>
                                <div class="col-md-6">
                                    <label for="endDate" class="form-label">End Date:</label>
                                    <input type="text" class="form-control" id="endDate" class="datepicker" autocomplete="off" class="filter-input">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex justify-content-start gap-4 col-lg-12">
                                <button type="button" class="btn btn-primary" id="btn-apply-filter">Apply</button>
                                 <button type="button" class="btn btn-warning" id="btn-reset-filter">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-header rounded-0 text-center">
                            <!--<h5 class="mt-2">
                                <a href="javascript:;" class="app-query-link">Total Applications:
                                    <span id="app-total-count">{{$totalApplications}}</span>
                                </a>
                            </h5>-->
                        </div>
                        <div class="col-sm-6 col-xl-3 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-primary b-r-4 card-body">
                                    <a href="javascript:;"  class="app-query-link" data-status="allapplications">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-user-plus"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Total Applications</span>
                                                <h4 class="mb-0 counter" id="totalApplications"  class="app-query-link" data-status="allapplications">{{$totalApplications}}</h4>
                                                <i class="fa-solid fa-solid fa-user-plus"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-light-green b-r-4 card-body">
                                    <a href="javascript:;"  class="app-query-link" data-status="newpending">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">New Applications</span>
                                                <h4 class="mb-0 counter" id="newOrPendingApplications" >{{$newOrPendingApplications}}</h4>
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                         <div class="col-sm-6 col-xl-3 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-dark-orange  b-r-4 card-body">
                                    <a href="javascript:;"  class="app-query-link" data-status="inprogress">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">In Progress Applications</span>
                                                <h4 class="mb-0 counter" id="totalinProgressApplicationCount"  >{{$totalinProgressApplicationCount}}</h4>
                                                <i class="fa-solid fa-solid fa-thumbs-up"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-assigned b-r-4 card-body">
                                   <a href="javascript:;"  class="app-query-link" data-status="disposed">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Disposed Applications</span>
                                                <h4 class="mb-0 counter" id="totalDisposedApplicationCount">{{$totalDisposedApplicationCount}}</h4>
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                        </div>
                                   </a>
                                </div>
                            </div>
                        </div>
                       
                     
                    </div>
                </div>
            </div>
        </div>

    </div>

    @php
    $services = [
    'CONVERSION'=> 'Conversion',
   // 'LUC'=>'Land Use Change (LUC)',
    'SUB_MUT'=>'Mutation',
    'NOC'=>'NOC',
   'DOA'=>'Deed of Apartment',
   // 'PRPCERT'=>'Property Certificate',
    //'SEL_PERM'=>'Sale Permission'    
    ];
    @endphp
<style> .nav.flex-column:not(.nav-sidebar)>li {
    border-bottom: 1px solid rgba(0, 0, 0, .125);
    margin: 0;
}.float-right {
    float: right !important;
}
.nav.flex-column li a{display: block;
    padding: .5rem 0.2rem;}
    .card {
    margin-bottom: 0.5rem;
</style>

<div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title">Application Summary by Service Type</h5>
                    <div class="table-responsive mt-2 mb-3">
                      @php
    $totalApplications = 0;
    $totalNew = 0;
    $totalInProgress = 0;
    $totalDisposed = 0;
@endphp

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Service Type</th>
            <th>Total Applications</th>
            <th>New Applications</th>
            <th>In Progress Applications</th>
            <th>Disposed Applications</th>
        </tr>
    </thead>
   <tbody>
        @foreach ($services as $key => $service)
            @php
                $total = isset($serviceTypeWiseApplicationStatus[$service]) ? $serviceTypeWiseApplicationStatus[$service]['total'] : 0;
                $new = 
                    (isset($serviceTypeWiseApplicationStatus[$service]['New']) ? $serviceTypeWiseApplicationStatus[$service]['New'] : 0) +
                    (isset($serviceTypeWiseApplicationStatus[$service]['Pending']) ? $serviceTypeWiseApplicationStatus[$service]['Pending'] : 0);
                $inProgress = isset($serviceTypeWiseApplicationStatus[$service]) ?
                    ($serviceTypeWiseApplicationStatus[$service]['In Progress'] ?? 0) +
                    ($serviceTypeWiseApplicationStatus[$service]['Objected'] ?? 0) +
                    ($serviceTypeWiseApplicationStatus[$service]['Hold'] ?? 0) : 0;
                $disposed = isset($serviceTypeWiseDisposeStatus[$service]) ?
                    (($serviceTypeWiseDisposeStatus[$service]['Rejected'] ?? 0) +
                    ($serviceTypeWiseDisposeStatus[$service]['Approved'] ?? 0) +
                    ($serviceTypeWiseDisposeStatus[$service]['Cancelled'] ?? 0)) : 0;

                // Add to totals
                $totalApplications += $total;
                $totalNew += $new;
                $totalInProgress += $inProgress;
                $totalDisposed += $disposed;
            @endphp
            <tr>
                <td id="submitted_service_{{$key}}" class="submitted-data">{{ $service }}</td>
                <td  class="submitted-data">
                    <a href="javascript:;" class="app-query-link" data-status="allapplications" data-service="{{ $key }}" id="submitted_total_{{$key}}">
                        {{ $total }}
                    </a>
                </td>
                <td  class="submitted-data">
                    <a href="javascript:;" class="app-query-link" data-status="newpending" data-service="{{ $key }}" id="submitted_newpending_{{$key}}" >
                        {{ $new }}
                    </a>
                </td>
                <td  class="submitted-data">
                    <a href="javascript:;" class="app-query-link" data-status="inprogress" data-service="{{ $key }}" id="submitted_inprogress_{{$key}}">
                        {{ $inProgress }}
                    </a>
                </td>
                <td  class="submitted-data">
                    <a href="javascript:;" class="app-query-link" data-status="disposed" data-service="{{ $key }}" id="submitted_disposed_{{$key}}">
                        {{ $disposed }}
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>

   <tfoot>
    <tr>
        <th>Total</th>
        <th id="footer_total_applications">{{ $totalApplications }}</th>
        <th id="footer_total_new">{{ $totalNew }}</th>
        <th id="footer_total_inprogress">{{ $totalInProgress }}</th>
        <th id="footer_total_disposed">{{ $totalDisposed }}</th>
    </tr>
</tfoot>

</table>

                    </div>

                    <!--<h5 class="card-title">Disposed Applications</h5>
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Service Type</th>
                                    <th>Disposed</th>
                                    <th>Approved</th>
                                    <th>Rejected</th>
                                    <th>Cancelled</th>
                                </tr>
                            </thead>
                            @php
                            $disposedStatusArray = ['Approved', 'Rejected', 'Cancelled'];
                            @endphp
                            <tbody>
                                @foreach ($services as $key=>$service)
                                <tr>
                                    <td id="disposed_service_{{$key}}" class="disposed-data">{{$service}}</td>
                                    <td id="disposed_total_{{$key}}" class="disposed-data">
                                        <a href="javascript:;" class="app-query-link" data-status="disposed" data-service="{{$key}}">
                                            {{isset($serviceTypeWiseDisposeStatus[$service]) ? $serviceTypeWiseDisposeStatus[$service]['total']:0}}
                                        </a>
                                    </td>
                                    @foreach ($disposedStatusArray as $item)
                                    <a href="javascript:;" class="app-query-link" data-status="{{strtolower($item)}}" data-service="{{$key}}">
                                        <td id="disposed_{{$key}}_{{strtolower($item)}}" class="disposed-data">{{(isset($serviceTypeWiseDisposeStatus[$service]) && isset($serviceTypeWiseDisposeStatus[$service][$item])) ? $serviceTypeWiseDisposeStatus[$service][$item] : 0}}</td>
                                    </a>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>-->
                </div>
            </div>
        </div>
    </div>
   <!-- <div class="row mb-0">
     @foreach ($services as $key=>$service)   

     <div class="col-sm-6 col-xl-4 col-lg-4 mb-4">
     <div class="card widget-card">
                <div class="card-body">
                <h5 class="card-title">{{$service}} Applications</h5>
                <ul class="nav flex-column">
                  <li class="submitted-data" id="submitted_total_{{$key}}" >
                    <a href="javascript:;" class="app-query-link" data-service="{{$key}}" class="">
                      Received Applications<span class="float-right badge bg-primary">                                        
                                        {{isset($serviceTypeWiseApplicationStatus[$service]) ? $serviceTypeWiseApplicationStatus[$service]['total']:0}}  
                                        </span>
                    </a>
                  </li>
                  <li id="submitted_received_{{$key}}" class="submitted-data">                   
                    <a href="javascript:;" class="app-query-link" data-status="received" data-service="{{$key}}">
                      New/Pending Applications<span class="float-right badge bg-primary">{{
                                              (
    isset($serviceTypeWiseApplicationStatus[$service]['New']) 
        ? $serviceTypeWiseApplicationStatus[$service]['New'] 
        : 0
) + (
    isset($serviceTypeWiseApplicationStatus[$service]['Pending']) 
        ? $serviceTypeWiseApplicationStatus[$service]['Pending'] 
        : 0
)

                                            }}</span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="#" class="">
                      In Progress Applications<span class="float-right badge bg-warning">{{$serviceTypeWiseApplicationStatus[$service]['In Progress'] ?? 0}}</span>
                    </a>
                  </li>
                  <li class="">
                    <a href="#" class="">
                     Approved Applications<span class="float-right badge bg-success">{{$serviceTypeWiseApplicationStatus[$service]['Approved'] ?? 0}}</span>
                    </a>
                  </li>
                  <li class="">
                    <a href="#" class="">
                      Reject Applications <span class="float-right badge bg-danger">{{$serviceTypeWiseApplicationStatus[$service]['Rejected'] ?? 0}}</span>
                    </a>
                  </li>
                </ul>              
                </div>
                </div>
     </div>
     @endforeach
     <div class="clearfix"></div>
    </div>-->
    
    
    

   
</div>

@endsection

@section('footerScript')
<script>
    let statusKeyArray = {
    	'allapplications': 'APP_NEW,APP_PEN,APP_IP,APP_OBJ,APP_APR,APP_REJ,APP_CAN,APP_HOLD',
        //'received': 'APP_IP, APP_OBJ, APP_APR, APP_REJ, APP_CAN, APP_HOLD', //all applications other than new, pending, withdrawn
        'newpending': 'APP_NEW,APP_PEN',
        'disposed': 'APP_APR,APP_REJ,APP_CAN',
        'inprogress' : 'APP_IP,APP_OBJ,APP_HOLD',
        'approved': 'APP_APR',
        'rejected': 'APP_REJ',
        'cancelled': 'APP_CAN'
    }
    $(function() {
        var dateFormat = "dd-mm-yy";
        $("#startDate").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            // Set min date for endDate when startDate is selected
            $("#endDate").datepicker("option", "minDate", selectedDate);
        }
        });

        $("#endDate").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            // Set max date for startDate when endDate is selected
            $("#startDate").datepicker("option", "maxDate", selectedDate);
        }
        });
    });

    $('#btn-apply-filter').click(function() {
        let sectionId = $('#section_id').val();
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let request = {};
        if (sectionId !== '') {
            request.section_id = sectionId;
        }
        if (startDate !== '') {
            request.date_from = startDate;
        }
        if (endDate !== '') {
            request.date_to = endDate;
        }
        //console.log(request);
        let services = @json($services);
        $.ajax({
            type: "GET",
            data: request,
            success: function(response) {
            	//console.log(response);
				               if (response.success) {
				               	let totalApplications = 0;
							    let totalNew = 0;
							    let totalInProgress = 0;
							    let totalDisposed = 0;
				    $('#totalApplications').html(response.data.totalApplications ?? 0);
				    $('#newOrPendingApplications').html(response.data.newOrPendingApplications ?? 0);
				    $('#totalinProgressApplicationCount').html(response.data.totalinProgressApplicationCount ?? 0);
				    $('#totalDisposedApplicationCount').html(response.data.totalDisposedApplicationCount ?? 0);
				    $('#app-approved-count').html(response.data.totalApplications ?? 0);
				    $('#app-rejected-count').html(response.data.totalApplications ?? 0);
				    $('#app-cancelled-count').html(response.data.totalApplications ?? 0);
				    //$('.submitted-data, .disposed-data').html(0);
				   Object.entries(services).forEach(([key, label]) => {
					    let appData = response.data.serviceTypeWiseApplicationStatus[label] ?? {};
					    let disposeData = response.data.serviceTypeWiseDisposeStatus[label] ?? {};
					    let total = appData.total ?? 0;

					    let newOrPending =
					        (appData.New ?? 0) +
					        (appData.Pending ?? 0);

					    let inProgress =
					        (appData['In Progress'] ?? 0) +
					        (appData.Objected ?? 0) +
					        (appData.Hold ?? 0);

					    // IMPORTANT
					    let disposed =
					        (disposeData.Approved ?? 0) +
					        (disposeData.Rejected ?? 0) +
					        (disposeData.Cancelled ?? 0);

					    $('#submitted_total_' + key).text(total);
					    $('#submitted_newpending_' + key).text(newOrPending);
					    $('#submitted_inprogress_' + key).text(inProgress);
					    $('#submitted_disposed_' + key).text(disposed);

					    totalApplications += total;
					    totalNew += newOrPending;
					    totalInProgress += inProgress;
					    totalDisposed += disposed;
					});
				    $('#footer_total_applications').text(totalApplications);
				    $('#footer_total_new').text(totalNew);
				    $('#footer_total_inprogress').text(totalInProgress);
				    $('#footer_total_disposed').text(totalDisposed);
				}

				            }
        })
    })
    $('.app-query-link').click(function() {
    	//alert('');
        let selectedStatus = $(this).data('status');
        //alert(selectedStatus);return false;
        let selectedService = $(this).data('service');
        let sectionId = $('#section_id').val();
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let request = {};
        if (sectionId !== undefined && sectionId !== '') {
            request.section_id = sectionId;
        }
        if (startDate !== undefined && startDate !== '') {
            request.date_from = startDate;
        }
        if (endDate !== undefined && endDate !== '') {
            request.date_to = endDate;
        }
        if (selectedStatus !== undefined) {
            request.status = statusKeyArray[selectedStatus];
        }
        
        if (selectedService !== undefined) {
            request.service = selectedService;
        }
        let url = "{{ route('applicationSummaryDetails') }}" + '?' + new URLSearchParams(request).toString();
        window.open(url, "_blank");
    })
    $("#btn-reset-filter").click(function(){
	    	  window.location.href = "{{ route('applicationSummary') }}";
	    })
</script>
@endsection