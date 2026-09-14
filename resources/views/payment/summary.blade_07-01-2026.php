@extends('layouts.app')

@section('title', 'Payment Summary')

@section('content')

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Payment</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Payment</li>
                <li class="breadcrumb-item active" aria-current="page">Summary</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>

<hr>
{{-- <div class="card w-33">
  <div class="card-body">
    <h5 class="card-title">Total Payment</h5>
    <h6 class="card-subtitle mb-2 text-muted">Total payment by suucessfull transactions</h6>
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
    <a href="#" class="card-link">Card link</a>
    <a href="#" class="card-link">Another link</a>
  </div>
</div> --}}
<div class="row">
<style>.card {
    margin-bottom: 0.5rem;
}</style>
    <div class="col-lg-12">
        <div class="card border-primary">
           <!-- <div class="card-header">Filter by Date</div>-->
            <div class="card-body">
<div  class="row mb-3">
                <form class="row mb-3">
                        
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Start Date:</label>
                                <input type="text" class="form-control datepicker" id="startDate" autocomplete="off" name="start_date" value="{{$request->input('start_date')}}">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">End Date:</label>
                                <input type="text" class="form-control datepicker" id="endDate" autocomplete="off" name="end_date" value="{{$request->input('end_date')}}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex justify-content-start gap-4 col-lg-12">
                            <button type="submit" class="btn btn-primary" id="btn-apply-filter">Apply</button>
                             <button type="button" class="btn btn-warning" id="btn-reset-filter">Reset</button>
                        </div>
                    </div>
                </form></div>
                <div class="row">
 <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-primary b-r-4 card-body" style="padding-bottom: 0.5%;">
                                    <a href="javascript:;"  class="app-query-link" data-status="allpayment">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fas fa-receipt"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Total Payments</span>
                                                <h4 class="mb-0 counter" id="totalApplications"  class="app-query-link" data-status="allpayment">₹  {{customNumFormat($total_amount)}}</h4>
                                                 <p class="card-text">{{$total_transactinos}} Transactions</p>
                                                <i class="fa-solid fa-solid fas fa-receipt"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-assigned b-r-4 card-body" style="padding-bottom: 0.5%;">
                                    <a href="javascript:;"  class="app-query-link" data-status="successpayment">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Total Successful Payments</span>
                                                <h4 class="mb-0 counter" id="newOrPendingApplications" >₹  {{customNumFormat($statuswise['PAY_SUCCESS']['amount'])}}</h4><p class="card-text">{{$statuswise['PAY_SUCCESS']['count']}} Transactions</p>
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                         <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-dark-orange b-r-4 card-body" style="padding-bottom: 0.5%;">
                                    <a href="javascript:;"  class="app-query-link" data-status="pendingpayment">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Total Pending Payments</span>
                                                <h4 class="mb-0 counter" id="totalinProgressApplicationCount"  > ₹  {{customNumFormat($statuswise['PAY_PENDING']['amount'])}}</h4>
                                                <p class="card-text">{{$statuswise['PAY_PENDING']['count']}} Transactions</p>
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-sm-6 col-xl-3 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-dark-orange  b-r-4 card-body">
                                    <a href="javascript:;"  class="app-query-link" data-status="inprogress">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Total Pending Payment</span>
                                                <h4 class="mb-0 counter" id="totalinProgressApplicationCount"  >{{customNumFormat($statuswise['PAY_PENDING']['amount'])}}</h4>
                                                <i class="fa-solid fa-solid fa-thumbs-up"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>-->
    
</div>
            </div>
            
        </div>
        
    </div>
    
</div>
<div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title"><!--Application Fee Payments Summary by Application Type-->Application-Wise Charges <!--Payment Summary--></h5>
                    <div class="table-responsive mt-2 mb-3">                      
					<table class="table table-bordered">
					    <thead>
					        <tr>
					            <th>Application <!--Payment--> Type</th>
					            <th>Total Payments</th>
					            <th>Successful Payments</th>
					            <th>Pending Payments</th>
					            <!-- <th>Sta</th> -->
					        </tr>
					    </thead>
					    <tbody>
					       @foreach ($applicationwiseBreakup as $index=>$item)            
					            <tr>
					                <td id="submitted_service" class="submitted-data"> {{ $index }} </td>
					                <td  class="submitted-data">
					                    <a href="javascript:;" class="app-query-link" data-status="allpayment" data-service="{{$index=='LUC'?'landUseChange':$index}}" >
					                       &#8377; {{customNumFormat($item['total']['amount'])}} <br> <small>({{customNumFormat($item['total']['count'])}} Transactions)</small>
					                    </a>
					                </td>
					                <td  class="submitted-data">
					                    <a href="javascript:;" class="app-query-link" data-status="successpayment" data-service="{{$index=='LUC'?'landUseChange':$index}}"  >
					                     &#8377; {{customNumFormat($item['success']['amount'])}} <br><small>({{customNumFormat($item['success']['count'])}} Transactions)</small>
					                    </a>
					                </td>
					                <td  class="submitted-data">
					                    <a href="javascript:;" class="app-query-link" data-status="pendingpayment" data-service="{{$index=='LUC'?'landUseChange':$index}}">
					                      &#8377;  {{customNumFormat($item['pending']['amount'])}}  <br><small>({{customNumFormat($item['pending']['count'])}} Transactions)</small>
					                    </a>
					                </td>					                
					            </tr>
					        @endforeach       
					    </tbody> 
					</table>
                    </div> 
                </div>
            </div>
        </div>
    </div>
  <div class="row">   
    {{-- @foreach ($applicationwiseBreakup as $index=>$item) --}}
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                <a href="{{ route('demandSummaryDetails') }}" class="app-query-link1" data-status="allpayment">
                    <div class="card-header">Total Demand Raised</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($demandData['total'])}}</h5>
                        {{-- <p class="card-text">{{$item['count']}} transactions</p> --}}
                    </div>
                     </a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                 <a href="{{ route('demandSummaryDetails') }}" class="app-query-link1" data-status="allpayment">
                    <div class="card-header">Total Payment Received for Demands</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($demandData['paid'])}}</h5>
                        {{-- <p class="card-text">{{$item['count']}} transactions</p> --}}
                    </div>
                    </a>
                </div>
            </div>
             <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                 <a href="{{ route('demandSummaryDetails') }}" class="app-query-link1" data-status="allpayment">
                    <div class="card-header">Total Pending Payments for Demands</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($demandData['pending'])}}</h5>
                        {{-- <p class="card-text">{{$item['count']}} transactions</p> --}}
                    </div>
                    </a>
                </div>
            </div>
        {{-- @endforeach --}}
	</div> 
    {{--
@if($typewiseCount->count() > 0)
    <div class="row">
        @foreach ($typewiseCount as $index=>$item)
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                    <div class="card-header">Payment for {{getServiceNameById($index)}}</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($typewiseAmount[$index])}}</h5>
                        <p class="card-text">{{$statuswise['PAY_SUCCESS']['count']}} transactions</p>
                    </div>
                </div>
            </div>
        @endforeach    
    </div>
@endif ---}}
<style>.widget-media .widget-media-body {
    align-self: center !important;
    padding-left: 20px;
    flex: 1;
    color: #ffffff;
    display: block !important; </style>
@include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
<script>
	 let statusKeyArray = {
	    	'allpayment': 'PAY_PENDING,PAY_SUCCESS',
	        'successpayment': 'PAY_SUCCESS',
	        'pendingpayment': 'PAY_PENDING'
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
	//==================================================================================
     $(document).on("click",".app-query-link",function(e) {
	 	e.preventDefault();
        let selectedService = $(this).data('service');  
        let selectedStatus = $(this).data('status');  
        let startDate = $("#startDate").val();
        let endDate = $("#endDate").val();
        let request = {};       
        if (selectedService !== undefined) {
            request.service = selectedService;
        }
        if (selectedStatus !== undefined) {
            request.status = statusKeyArray[selectedStatus];;
        }
        if (startDate !== undefined) {
            request.start = startDate;
        }
        if (endDate !== undefined) {
            request.end = endDate;
        }
        let queryString = new URLSearchParams(request).toString();    
    	let encoded = btoa(queryString);
    	let url = "{{ route('paymentSummaryDetails') }}" + "?data=" + encoded;
    	window.open(url, "_blank");
		// let url = "{{ route('paymentSummaryDetails') }}" + '?' + new URLSearchParams(request).toString();
		///////   window.open(url, "_blank");
    })    
	    $("#btn-reset-filter").click(function(){
	    	  window.location.href = "{{ route('paymentSummary') }}";
	    })
</script>
@endsection