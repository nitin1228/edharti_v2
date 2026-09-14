@extends('layouts.app')

@section('title', 'Payment  Report')

@section('content')

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Revenue </div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Payment </li>
                <li class="breadcrumb-item active" aria-current="page">Report</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>     
<hr>

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
             
            </div>
            
        </div>
        
    </div>
    
</div>
<div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title"><!--Application Fee Payments Summary by Application Type-->Payment Report <!--Payment Summary--></h5>
               				    <div class="table-responsive mt-2 mb-3">

    @php
        $grandTotal = collect($summary)->sum('amount');
        $grandTran = collect($summary)->sum('property_count')
    @endphp

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Items</th>
                <th>Total Amount</th>
                <th>Total  Transactions</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($summary as $item)
                <tr>
                    <td class="submitted-data">
                        {{ $item['name'] == 'Application' ? "Application Processing Fee" : $item['name']}}
                    </td>

                    <td class="submitted-data">
                        <a href="javascript:;"
                           class="app-query-link"
                           data-status="summary_report"
                           data-service="{{ $item['code'] }}">
                            ₹ {{ customNumFormat(round($item['amount'] ?? 0, 2)) }}
                        </a>
                    </td>
                     <td>
                    {{ $item['property_count'] ?? 0 }} 
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">
                        No data found
                    </td>
                </tr>
            @endforelse
        </tbody>

        <tfoot>
            <tr class="table-secondary">
                <th class="text-end">Total:</th>
                <th>
                <a href="javascript:;"
                           class="app-query-link"
                           data-status="summaryallreport"
                           data-service="all">     ₹ {{ customNumFormat(round($grandTotal, 2)) }}</a>
                </th>
                <th>   <a href="javascript:;"
                           class="app-query-link"
                           data-status="summaryallreport"
                           data-service="all">      {{$grandTran}}</a></th>
            </tr>
        </tfoot>
    </table>
</div>

                </div>
            </div>
        </div>
    </div>
 
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
//    kindly include the following details for each candidate:

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
            //request.status = statusKeyArray[selectedStatus];
            request.status = selectedStatus;
        }
        if (startDate !== undefined) {
            request.start = startDate;
        }
        if (endDate !== undefined) {
            request.end = endDate;
        }        
        let queryString = new URLSearchParams(request).toString(); 
       // console.log(queryString);
        //alert(queryString);
        //return false;   
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