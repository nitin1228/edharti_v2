@extends('layouts.app')
@section('title', 'Demand Summary')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Demand</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
<!--
                <li class="breadcrumb-item">Application</li>-->
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <li class="breadcrumb-item active" aria-current="page">Demand Summary</li>
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
    <div class="col-xl-7 mb-2 col-lg-12">
        <label class="form-label">Filter:</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input filter-type" type="radio" name="filterType" id="yearWise" value="yearWise" checked>
            <label class="form-check-label" for="yearWise">Date wise</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input filter-type" type="radio" name="filterType" id="dyLDoWise" value="dyLDoWise">
            <label class="form-check-label" for="dyLDoWise">Dy L&DO wise</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input filter-type" type="radio" name="filterType" id="sectionWise" value="sectionWise">
            <label class="form-check-label" for="sectionWise">Section wise</label>
        </div>
    </div>

    <div class="col-xl-5 mb-2 col-lg-12"  style="visibility: hidden;">
        <label class="form-label">Demand Type:</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input demand-type" type="radio" name="demandType" id="latestDemand" value="0" checked>
            <label class="form-check-label" for="latestDemand">Latest Demand</label>
        </div>
       <!-- <div class="form-check form-check-inline">
            <input class="form-check-input demand-type" type="radio" name="demandType" id="allDemand" value="1">
            <label class="form-check-label" for="allDemand">All Demand</label>
        </div>-->
    </div>
</div>

<div class="row mb-3" id="filterDropdowns">    
    <!--<div class="col-md-4 filter-option" id="yearWiseSection">
        <label for="section_id" class="form-label">Select Section</label>
        <select class="form-select" name="section_id" id="section_id">
            <option value="">Select Section</option>
            @forelse ($sections as $section)
                <option value="{{$section->id}}">{{$section->name}}</option>
            @empty
            @endforelse
        </select>
    </div>-->
    <div class="col-md-4 filter-option d-none" id="dyLDoWiseSection">
        <label for="dyLDo_id" class="form-label">Select Dy L&DO</label>
        <select class="form-select" name="dyLDo_id" id="dyLDo_id">       
            <option value="">---- Select Dy L&DO ----</option>
            <!-- <option value="0">------ All Dy L&Do -------</option>-->
             @forelse($deputlndousers as $deputlndouser)
            <option value="{{$deputlndouser->id}}">Mr. {{$deputlndouser->name}}</option>
             @empty
            @endforelse
        </select>
    </div>
    <div class="col-md-4 filter-option d-none" id="sectionWiseSection">
        <label for="section_wise_id" class="form-label">Select Section</label>
        <select class="form-select" name="section_wise_id" id="section_wise_id">
            <option value="">---- Select Section ----</option>
             <!--<option value="0">------ All Sections -------</option>-->
            @forelse ($sections as $section)
                <option value="{{$section->id}}">{{$section->name}}</option>
            @empty
            @endforelse
        </select>
    </div>
      <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <label for="startDate" class="form-label">Start Date:</label>
                <input type="text" class="form-control datepicker" id="startDate" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label for="endDate" class="form-label">End Date:</label>
                <input type="text" class="form-control datepicker" id="endDate" autocomplete="off">
            </div>
        </div>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-primary" id="btn-apply-filter">Apply</button>
    </div>
</div>
<div class="row mb-3">
<div class="loader" style="display: none; "></div>
<div id="filter-content"></div>
</div>

                </div>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="breakupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
    <h5 class="modal-title">Demand Headwise Breakup</h5>
    <button type="button" class="close ms-auto btn btn-danger" data-bs-dismiss="modal">×</button>
</div>
      <div class="modal-body" id="breakupBody">
        Loading...
      </div>

    </div>
  </div>
</div>
@endsection

@section('footerScript')
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip();
  $(document).on('click', '.viewBreakup', function(){
    let demand_id = $(this).data('id');
    $('#breakupModal').modal('show');
    $('#breakupBody').html("Loading...");
    $.ajax({
        url: "{{ route('get.breakup') }}",
        type: "GET",
        data: { demand_id: demand_id },
        success: function(response){
            $('#breakupBody').html(response);
        }
    });
});

})
$(document).ready(function() {
    // Handle radio button change
	    $('input[name="filterType"]').change(function() {
	    var selectedValue = $(this).val();
	    $('.filter-option').addClass('d-none');
	    $('#' + selectedValue + 'Section').removeClass('d-none');
	    $('#section_id').val('');
	    $('#dyLDo_id').val('');
	    $('#section_wise_id').val(''); 
	    $('#startDate, #endDate').val('');            // input empty
	    $('#startDate, #endDate').datepicker('setDate', null); // clear selection
	    $('#startDate, #endDate').datepicker('option', 'minDate', null); // clear minDate restriction
	    $('#startDate, #endDate').datepicker('option', 'maxDate', null); // clear maxDate restriction
	    $('#startDate, #endDate').datepicker('refresh'); // refresh calendar UI to current month
	});
    $('#yearWise').trigger('change');
     $('#btn-apply-filter').click(function () {
    var selectedFilter = $('input[name="filterType"]:checked').val();
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();
    var selectedId;

    if (selectedFilter === 'yearWise') {
        selectedId = $('#section_id').val();
    } else if (selectedFilter === 'dyLDoWise') {
        selectedId = $('#dyLDo_id').val();
    } else if (selectedFilter === 'sectionWise') {
        selectedId = $('#section_wise_id').val();
    }
    $.ajax({
        url: "{{ route('demandSummary') }}",
        type: "GET",
        data: {
            filterType: selectedFilter,
            selectedId: selectedId,
            startDate: startDate,
            demandType : $('input[name="demandType"]:checked').val(),
            endDate: endDate
        },
         beforeSend: function () {
                $('.loader').show(); 
            },
        success: function (response) {        	
            $('#filter-content').html(response.html);
        },
        error: function () {
            alert('Something went wrong!');
        },
         complete: function () {
                $('.loader').hide();
            }
    });
});

});
</script>
<script>	
		$(function() {
		    var dateFormat = "dd-mm-yy";

		    $("#startDate").datepicker({
		        dateFormat: dateFormat,
		        changeMonth: true,
		        changeYear: true,
		        onClose: function(selectedDate) {
		            if (selectedDate) {
		                $("#endDate").datepicker("option", "minDate", selectedDate);
		            } else {
		                $("#endDate").datepicker("option", "minDate", null); // Clear restriction
		            }
		        }
		    });

		    $("#endDate").datepicker({
		        dateFormat: dateFormat,
		        changeMonth: true,
		        changeYear: true,
		        onClose: function(selectedDate) {
		            if (selectedDate) {
		                $("#startDate").datepicker("option", "maxDate", selectedDate);
		            } else {
		                $("#startDate").datepicker("option", "maxDate", null); // Clear restriction
		            }
		        }
		    });
		});  
	 $(document).on("click",".app-query-link",function(e) {
	 	e.preventDefault();
    	//alert('hi');    	
        let selectedService = $(this).data('service');  
        let selectedType = $(this).data('type');  
        let selectedFrom = $(this).data('from');
        let selectedTo = $(this).data('to');             
        let request = {};       
        if (selectedService !== undefined) {
            request.service = selectedService;
        }
        if (selectedType !== undefined) {
            request.type = selectedType;
        }
        if (selectedFrom !== undefined) {
            request.from = selectedFrom;
        }
        if (selectedTo !== undefined) {
            request.to = selectedTo;
        }
        let queryString = new URLSearchParams(request).toString();    
    	let encoded = btoa(queryString);
    	let url = "{{ route('demandSummaryDetails') }}" + "?data=" + encoded;
    	window.open(url, "_blank");
    })
</script>
@endsection