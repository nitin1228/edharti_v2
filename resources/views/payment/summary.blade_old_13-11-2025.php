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
    <div class="col-lg-12">
        <div class="card border-primary">
            <div class="card-header">Filter by Date</div>
            <div class="card-body">

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
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="card border-primary mb-3">
            <div class="card-header">Total Payment</div>
            <div class="card-body text-primary">
                <h5 class="card-title">&#8377; {{customNumFormat($total_amount)}}</h5>
                <p class="card-text">{{$total_transactinos}} transactions</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-secondary mb-3">
            <div class="card-header">Total Successfull Payment</div>
            <div class="card-body text-secondary">
                <h5 class="card-title">&#8377; {{customNumFormat($statuswise['PAY_SUCCESS']['amount'])}}</h5>
                <p class="card-text">{{$statuswise['PAY_SUCCESS']['count']}} transactions</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-warning mb-3">
            <div class="card-header">Total Pending Payment</div>
            <div class="card-body text-warning">
                <h5 class="card-title">&#8377; {{customNumFormat($statuswise['PAY_PENDING']['amount'])}}</h5>
                <p class="card-text">{{$statuswise['PAY_PENDING']['count']}} transactions</p>
            </div>
        </div>
    </div>
</div>
@if($typewiseCount->count() > 0)
    <div class="row">
        @foreach ($typewiseCount as $index=>$item)
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                    <div class="card-header">Payment for {{getServiceNameById($index)}}</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($typewiseAmount[$index])}}</h5>
                        {{-- <p class="card-text">{{$statuswise['PAY_SUCCESS']['count']}} transactions</p> --}}
                    </div>
                </div>
            </div>
        @endforeach    
    </div>
@endif
<hr>
<h5>Application-Wise Charges</h5>
<div class="row">
    @foreach ($applicationwiseBreakup as $index=>$item)
            <div class="col-lg-3">
                <div class="card border-secondary mb-3">
                    <div class="card-header">Payment for {{ucfirst($index)}} Applications</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($item['amount'])}}</h5>
                        <p class="card-text">{{$item['count']}} transactions</p>
                    </div>
                </div>
            </div>
        @endforeach   
</div>
<hr>
<h5>Demand Payment</h5>
<div class="row">
    {{-- @foreach ($applicationwiseBreakup as $index=>$item) --}}
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                    <div class="card-header">Total Demand Raised</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($demandData['total'])}}</h5>
                        {{-- <p class="card-text">{{$item['count']}} transactions</p> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-secondary mb-3">
                    <div class="card-header">Payment Received for Demands</div>
                    <div class="card-body text-secondary">
                        <h5 class="card-title">&#8377; {{customNumFormat($demandData['paid'])}}</h5>
                        {{-- <p class="card-text">{{$item['count']}} transactions</p> --}}
                    </div>
                </div>
            </div>
        {{-- @endforeach --}}
</div>
@include('include.alerts.ajax-alert')

@endsection


@section('footerScript')
<script>
    // $(document).ready(function() {
    //     var dateFormat = "dd-mm-yy";

    //     $("#startDate").datepicker({
    //     dateFormat: dateFormat,
    //     changeMonth: true,
    //     changeYear: true,
    //     onClose: function (selectedDate) {
    //         // Set min date for endDate when startDate is selected
    //         $("#endDate").datepicker("option", "minDate", selectedDate);
    //     }
    //     });

    //     $("#endDate").datepicker({
    //     dateFormat: dateFormat,
    //     changeMonth: true,
    //     changeYear: true,
    //     onClose: function (selectedDate) {
    //         // Set max date for startDate when endDate is selected
    //         $("#startDate").datepicker("option", "maxDate", selectedDate);
    //     }
    //     });
    // });
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
</script>
@endsection