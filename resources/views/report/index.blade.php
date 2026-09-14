@extends('layouts.app')
@section('title', 'Property Summarize Report')
@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/rgr.css') }}" />
    <style>
        .subhead-input {
            margin: 10px 0 !important;
            padding: 10px 0 !important;
            border-radius: 10px;
        }

        #detail-container>tr>td:not(:nth-child(2)) {
            width: 15%;
        }
    </style>
       <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Miscellaneous</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Property Summarize Report</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--breadcrumb-->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 mb-2">
                    @include('include.parts.property-selector_summarize_report')
                </div>
                 <div class="col col-lg-2 pt-1 mb-2">
                <button type="button" class="btn btn-primary px-4 mt-4" id="submitButton">Search<i
                        class="bx bx-right-arrow-alt ms-2"></i></button>
            </div>
            </div>
           
            <div  id="fullpropertyDetailsDiv">  
            </div>
        </div>
    </div>

    <!-- Transfer Property Modal -->
    <div class="modal fade" id="propertyTransferConfirmModal" tabindex="-1"
        aria-labelledby="propertyTransferConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertyTransferConfirmModalLabel">Property Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to transfer this property to selected section?
                    <div class="form-group">
                        <label for="regno" class="form-label"></label>
                        <textarea rows="4" class="form-control" name="additionalRemark" id="remarks" placeholder="Remarks"
                            data-listener-added_5916a0ef="true" spellcheck="false"></textarea>
                        <div id="transferError" class="text-danger text-left"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="propertyTransferConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- added by Swati on 07012026 -->
    <input type="hidden" id="selectedOldPropertyId" value="">
    <input type="hidden" id="isSplittedSearch" value="0">
    <input type="hidden" id="masterOldPropertyId" value="">
    <input type="hidden" id="searchedId" value="">

    @include('include.loader')
    @include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
    <script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script>
        $("#submitButton").click(function() {
        	let propId = $("#oldPropertyId").val();
			if (!isNaN(propId) && propId.length == 5) {
			    $.ajax({
			        type: "POST",
			        url: "{{ route('propertyAlldetail') }}",
			        data: {
			            _token: "{{ csrf_token() }}",
			            property_id: propId
			        },
			        success: function(response) {
			            if (response.status === "success") {
			            	console.log(response.html);
			                $("#fullpropertyDetailsDiv").html(response.html);
			            } else {
			                $("#fullpropertyDetailsDiv").html("Try Again");
			            }
			        }
			    });
			} else {
			    alert("Invalid Property ID");
			}       
			        });

    </script>

@endsection
