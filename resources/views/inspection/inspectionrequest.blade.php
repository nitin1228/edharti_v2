@extends('layouts.app')

@section('title', 'Property Inspection Request')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/rgr.css') }}" />
    <style>.form-label{font-size: 14px;
    font-weight: inherit;}</style>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Inspection </div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Inspection </li>
                <li class="breadcrumb-item active" aria-current="page">Request</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>
<div class="row">
<style>.card {
    margin-bottom: 0.5rem;
}</style>
    <div class="col-lg-12">
        <div class="card border-primary">
        @php
    $isEdit = isset($inspection);
    @endphp
           <!-- <div class="card-header">Filter by Date</div>-->
            <div class="card-body">
				<div  class="row mb-3">
                <form class="row mb-3">                        
                    <div class="col-md-8">
   					 <div class="row">
      				  <div class="col-md-6">
                   <label class="form-label">  Property Id  </label>  
        <input type="text"  name="oldPropertyId" id="oldPropertyId" class="form-control" placeholder="Enter property id" value="{{ $inspection->property_id ?? '' }}"  {{ $isEdit ? 'readonly' : '' }}>
      <input type="hidden"  id="inspectionId" value="{{ $inspection->id ?? '' }}">
        </div>
        @if(!$isEdit)
        <div class="col-md-4">
            <label class="form-label"> &nbsp;</label>
            <div class="justify-content-start gap-2 col-lg-12">
                <button type="button"  class="btn btn-primary px-4 mt-0"   id="searchButton">Search  <i class="bx bx-right-arrow-alt ms-2"></i>  </button>
                <button type="button"   class="btn btn-warning"id="btn-reset-filter"> Reset</button>
            </div>
        </div>
        @else 
         <div class="col-md-6">
         <label class="form-label">  Inspection Id  </label> 
      <input type="text" class="form-control"value="{{ $inspection->inspection_id ?? '' }}" readonly>
      </div>
        @endif
    </div>
  </div>
                    
                </form></div>
                <div class=" mb-2">      
            <div class="d-none" id="detail-card">               
                <div class="">
                    <div class=""> <!-- this div add by anil on 21-01-2025-->
                        <table class="table table-bordered table-striped">
                            <thead>
                                <th colspan="5">Property Basic Details</th>
                            </thead>
                            <tbody id="detail-container">
                            </tbody>
                        </table>
                    </div>
                </div>
        
                <div id="errorDiv" style="color: red; display: none;"></div> <!-- Error container -->
                 @if(!isset($inspectionreport))                
                 @if(isset($type) && $type == 'breachnotice')
                 <fieldset class="custom-fieldset">
							@php
			    $comm = json_decode($inspection->communication_address ?? '{}');
			    $isBreach = !empty($comm->breach_address);
			    if ($isBreach) {
			        $address = $comm->breach_address;
			    } elseif (!empty($comm->second_address)) {
			        $address = $comm->second_address;
			    } else {
			        $address = (object)[];
			    }
			@endphp
				    <legend>Communication Address</legend>
				    <div class="row mb-2 align-items-center">				  
				    <div class="col-md-2"> <b>Name</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"><input type="text" class="form-control " value="{{ $isBreach ? ($address->bname ?? '') : ($address->pname ?? '') }}" id="bname"> </div>
				    </div>
				    <div class="row mb-2 align-items-center">
			        <div class="col-md-2">  <b>Email Address</b> </div>
			        <div class="col-md-3">  <input type="email"  class="form-control" id="binspection_email" placeholder="Enter email address"  value="{{ $isBreach ? ($address->binspection_email ?? '') : ($address->inspection_email ?? '') }}"></div>
			        <div class="col-md-2 text-end">  <b>Phone Number</b>   </div>
			        <div class="col-md-3"> <input type="text"   class="form-control" id="binspection_phone"  placeholder="Enter phone number" value="{{ $isBreach ? ($address->binspection_phone ?? '') : ($address->inspection_phone ?? '') }}" >
			        </div>
			    </div>
				<div class="row mb-2 align-items-center">				  
				     <div class="col-md-2 "> <b>House No/Street name</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"> <input type="text" class="form-control " value="{{ $isBreach ? ($address->bhouse ?? '') : ($address->phouse ?? '') }}" id="bhouse"></div>
                     <div class="col-md-2 text-end"><b> Locality</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{ $isBreach ? ($address->blocality ?? '') : ($address->plocality ?? '') }}" id="blocality"> </div>				
				</div>
				<div class="row mb-2 align-items-center">				   
				      <div class="col-md-2"> <b>City</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{ $isBreach ? ($address->bcity ?? '') : ($address->pcity ?? '') }}" id="bcity">  </div>
				     <div class="col-md-2  text-end"> <b>Pincode: </b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"> <input type="text" class="form-control "  value="{{ $isBreach ? ($address->bpincode ?? '') : ($address->ppincode ?? '') }}" id="bpincode">  </div>
				</div>
				<div class="row mb-2 align-items-center">				   
				      <div class="col-md-2"> <b>Breach Clause</b>  </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{ $address->bclouse ??''}}" id="bclouse">  </div>
				     <div class="col-md-2  text-end"> <b>Re-enter Clause: </b> </div>
				    <div class="col-md-3"> <input type="text" class="form-control "  value="{{ $address->breenter ??''}}" id="breenter">  </div>
				</div>
				<div class="row mb-2 align-items-center">				   
				      <div class="col-md-2"> <b>Branch Officer:</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{ $address->bofficer ??  $assignedToUser->name}}" id="bofficer" readonly>  </div>
				     <div class="col-md-2  text-end"> <b>Contact Telephone: </b> </div>
				    <div class="col-md-3"> <input type="text" class="form-control "  value="{{ $address->btelephone ??''}}" id="btelephone">  </div>
				</div>
				<div class="row mb-1 align-items-center">				   
				    <div class="row mt-2"> <div class="col-md-2">  <b>Remarks: </b> <span class="text-danger">*</span></div>
				    <div class="col-md-9"> <textarea class="form-control" rows="4" id="bremarks">{{ $inspection->bremarks ??''}}</textarea> </div>
				</div>				
				</div>
				
				</fieldset>  
                 @else                                  
				<fieldset class="custom-fieldset">
							@php
						    $comm = json_decode($inspection->communication_address ?? '{}');						   
						@endphp
				    <legend>Communication Address</legend>
				    <div class="row mb-2 align-items-center">
				   <div class="col-md-2"> <b>Reason For</b> <span class="text-danger">*</span> </div>
				  <div class="col-md-3"> 
				  <select name="resasonfor" id="resasonfor" class="form-select">				  				 
				  	<option value="">--Select---</option>
				   @foreach(getItemsByGroupId(9015) as $reasonfor)
       		<option value="{{ $reasonfor->id }}" {{ isset($inspection) &&  $inspection->inspection_reason == $reasonfor->id ? 'selected' : '' }}>{{ $reasonfor->item_name }}
       		</option>
				  	@endforeach
				  </select> 
				  </div>
				    <div class="col-md-2 text-end"> <b>Name</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"><input type="text" class="form-control "  value="{{$comm->main_address->cname ?? '' }}" id="cname"> </div>
				    </div>
				<div class="row mb-2 align-items-center">				  
				     <div class="col-md-2 "> <b>House No/Street name</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"> <input type="text" class="form-control " value="{{$comm->main_address->chouse ?? '' }}" id="chouse"></div>
                     <div class="col-md-2 text-end"><b> Locality</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{$comm->main_address->clocality  ?? '' }}" id="clocality"> </div>
				
				</div>

				<div class="row mb-2 align-items-center">
				   
				      <div class="col-md-2"> <b>City</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{$comm->main_address->ccity  ?? '' }}" id="ccity">  </div>
				     <div class="col-md-2  text-end"> <b>Pincode: </b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"> <input type="text" class="form-control "  value="{{$comm->main_address->cpincode  ?? '' }}" id="cpincode">  </div>
				</div>
				<div class="row mb-1 align-items-center">				   
				    <div class="row mt-2"> <div class="col-md-2">  <b>Remarks: </b> <span class="text-danger">*</span></div>
				    <div class="col-md-9"> <textarea class="form-control" rows="4" id="cremarks">{{ $inspection->cremarks ?? '' }}</textarea> </div>
				</div>
				
				</div>
				
				</fieldset>  
				 @if(Auth::user()->sections()->first()->section_code === 'TECH')
				<fieldset class="custom-fieldset mt-3">
				    <legend> Inspection Details </legend>
				    <div class="row mb-2 align-items-center">
				        <div class="col-md-2"> <b>Inspecting Officer</b> </div>
				        <div class="col-md-3">
				            <input type="text" class="form-control" id="inspection_officer" value="{{ Auth::user()->name ?? '' }}" readonly>
				        </div>
				        <div class="col-md-2 text-end"> <b>Inspection Date</b> <span class="text-danger">*</span></div>
				        <div class="col-md-3 ">
				          <input type="date"  class="form-control"  id="inspection_date"
				           value="{{ !empty($inspection->schedule_date) ? \Carbon\Carbon::parse($inspection->schedule_date)->format('Y-m-d') : '' }}">
				           
				        </div>
				    </div>				  
				    <div class="row mb-2 align-items-center">
				        <div class="col-md-2"> <b>Inspection Time From</b> <span class="text-danger">*</span></div>
				        <div class="col-md-3"><input type="time" class="form-control" id="inspection_time_from" value="{{$inspection->schedule_fromtime ??''}}"> </div>
				         <div class="col-md-2 text-end"> <b>Inspection Time To</b> <span class="text-danger">*</span></div>
				        <div class="col-md-3"> <input type="time"  class="form-control" id="inspection_time_to" value="{{$inspection->schedule_totime ??''}}"> </div>
				    </div>
				    @if(isset($reschedule) && $reschedule === 'reschedule')
				  <div class="row mb-2 align-items-center">				  
				     <div class="col-md-2 "> <b>Refused By</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-9"><textarea class="form-control" rows="4" id="refused_by">{{ $inspection->refused_by ?? '' }}</textarea> </div>                    			
				</div>  <div class="row mb-2 align-items-center">		
				 <div class="col-md-2 "><b> Reason/Remarks</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-9">  <textarea class="form-control" rows="4" id="reason">{{ $inspection->reason ?? '' }}</textarea>  </div>	</div>
                  @endif				  
			      <div class="row mb-2 align-items-center">	
			       <div class="col-md-5 ">	
			        <div class="form-check d-inline-block">
            <input class="form-check-input" type="checkbox"  id="copyAddress"><label class="form-check-label"  for="copyAddress"> Copy Communication Address<small style="color:red;font-size:12px;"> (Check this box if both addresses are the same.)</small></label>
        			</div>		
        			</div>  
				    <div class="col-md-2 text-end"> <b>Name</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"><input type="text" class="form-control "  value="{{$comm->second_address->pname ??''}}" id="pname"> </div>
				    </div>
				<div class="row mb-2 align-items-center">				  
				     <div class="col-md-2 "> <b>House No/Street name</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"> <input type="text" class="form-control " value="{{$comm->second_address->phouse ??''}}" id="phouse"></div>
                     <div class="col-md-2 text-end"><b> Locality</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{$comm->second_address->plocality ?? ''}}" id="plocality"> </div>				
				</div>

				<div class="row mb-2 align-items-center">				   
				      <div class="col-md-2"> <b>City</b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3">  <input type="text" class="form-control " value="{{$comm->second_address->pcity ?? ''}}" id="pcity">  </div>
				     <div class="col-md-2  text-end"> <b>Pincode: </b> <span class="text-danger">*</span> </div>
				    <div class="col-md-3"> <input type="text" class="form-control "  value="{{$comm->second_address->ppincode ?? ''}}" id="ppincode">  </div>
				</div>
			      <div class="row mb-2 align-items-center">
			        <div class="col-md-2">  <b>Email Address</b> </div>
			        <div class="col-md-3">  <input type="email"  class="form-control" id="inspection_email" placeholder="Enter email address"  value="{{$comm->second_address->inspection_email ?? ''}}" ></div>
			        <div class="col-md-2 text-end">  <b>Phone Number</b>   </div>
			        <div class="col-md-3"> <input type="text"   class="form-control" id="inspection_phone"  placeholder="Enter phone number"  value="{{$comm->second_address->inspection_phone ?? ''}}" >
			        </div>
			    </div>
				</fieldset>	
				@endif	
				@endif
				<div class="col d-flex mt-3">				
				 @if(Auth::user()->sections()->first()->section_code === 'TECH')
				    @if($inspection->schedule == 0)
						<button type="button" class="btn btn-primary px-4 mt-0 mx-auto" id="submitButton"> Schedule Inspection</button>
						@else
					<!--	<button type="button" class="btn btn-primary px-4 mt-0 mx-auto" id="submitButton"> Noting Inspection</button>
						<button type="button" class="btn btn-primary px-4 mt-0 mx-auto" id="submitButton"> Inspection Notice</button>-->
						<button type="button" class="btn btn-primary px-4 mt-0 mx-auto" id="submitButton"> Reschedule Inspection</button>
						@endif
					@else
					@if(isset($type) && $type == 'breachnotice')
						<button type="button" class="btn btn-primary px-4 mt-0 mx-auto" id="submitButton">Save</button>
						@else
					<button type="button" class="btn btn-primary px-4 mt-0 mx-auto" id="submitButton">  {{ isset($inspection)  ? 'Update' : 'Save' }}</button>
					@endif
					@endif
				</div>	
				@else 
			
    <div class="">
        <div class="">  
            <table class="table table-bordered">
                <tr>
                    <td>Name of the Inspecting Officer</td>
                    <td>  <input type="text"  class="form-control" id="inspection_officer" value="{{ Auth::user()->name ?? '' }}" readonly> </td>
                      <td>Name of the accompanied Senior Officer</td>                      
                    <td> <select class="form-select"   id="senior_officer">
                          <option value="">Select</option>
    @foreach($signingAuthorities as $user)
        <option value="{{ $user->id }}" {{ (isset($inspectionReportBind) && $inspectionReportBind->senior_officer == $user->id) ? 'selected' : '' }}>{{ $user->name }}
        </option>
    @endforeach
                        </select>
                    </td>
                </tr>             
            </table>
            <div class="part-title"><h5>Inspection Details</h5></div>           
            <table class="table table-bordered">
                <tr>
                    <th>Date</th>
                    <th>Book Number</th>
                    <th>Page Number</th>
                </tr>
                <tr>
                    <td> <input type="text" id="inspection_date_all" class="form-control" value="{{ !empty($inspection->schedule_date)  ? \Carbon\Carbon::parse($inspection->schedule_date)->format('d-m-Y')  : 'N/A'}}" readonly> </td>
                    <td> <input type="text"  class="form-control numeric-only" id="book_number" value="{{ $inspectionReportBind->book_number ?? '' }}"></td>
                    <td><input type="text" class="form-control numeric-only" id="page_number" value="{{ $inspectionReportBind->page_number ?? '' }}"></td>
                </tr>
            </table>
            <table class="table table-bordered">
                <tr>  <td>  <label class="form-label">Whether the Construction tallies with the Sanction Plan</label>
                	<select class="form-select" id="construction_tallies"> 
	                        <option value="" >Select</option>
	                       <option value="1" {{ isset($inspectionReportBind) && $inspectionReportBind->construction_tallies === 1 ? 'selected' : '' }}>Yes</option> 
	                       <option value="0" {{ isset($inspectionReportBind) && $inspectionReportBind->construction_tallies === 0 ? 'selected' : '' }}>No</option>
                        </select>
                </td>
                    <td colspan="2"> 
                    <label class="form-label">Reference number of Sanctioned Plan</label>
                    <input type="text" class="form-control" id="sanction_plan_ref_no" value="{{ $inspectionReportBind->sanction_plan_ref_no ?? '' }}">
                    </td>
                </tr>               
                <tr>
                    <td>  <label class="form-label">Date of Sanctioned Plan</label>
                     <input type="date" class="form-control" id="sanction_plan_date" value="{{ !empty($inspectionReportBind->sanction_plan_date)? \Carbon\Carbon::parse($inspectionReportBind->sanction_plan_date)->format('Y-m-d') : '' }}"></td>
                    <td> <label class="form-label">Status of the property</label> 
                    <select class="form-select" id="property_status">
                           <option value="">Select Plot</option>
                   @foreach(getItemsByGroupId(9001) as  $type)
		            <option value="{{$type->id}}" {{ isset($inspectionReportBind) && $inspectionReportBind->property_status == $type->id ? 'selected' : '' }}>{{$type->item_name}}</option>
		                   @endforeach
                        </select></td>
                         <td><label class="form-label">If Built up Plot, Number of floors built</label>
                          <input type="text" class="form-control" id="total_floors" value="{{ $inspectionReportBind->total_floors ?? '' }}"></td>
                </tr> 
            </table>
            <div class="part-title"><h5>Breach Details</h5></div>
			<table class="table table-bordered" id="breachTable">
    		<thead>
		        <tr>
		         <th width="2%">S.No.</th>
		            <th width="18%">Inspected Type</th>
		            <th width="18%">Breach Type/ Breach on/ Floor/ Breach use</th>
		            <th width="24%">Location / Details</th>
		            <th width="25%">Length/Breadth/Area</th>
		            <th width="10%">Objectionable / Reason</th>
		            <th width="5%">Action</th>
		        </tr>
    		</thead>
	    	 <tbody id="breachTableBody">	    	 
					@if(isset($inspectionReportBind) && $inspectionReportBind->breaches->count() > 0)
					    @foreach($inspectionReportBind->breaches as $breach)
					    <tr class="breachRow" data-changed="0">
					    <td class="srNo" style="font-weight: bold;">1</td>
					        <td>
					        <input type="hidden"  class="row_id  breach_id"  value="{{$breach->id ?? ''}}">
					            <select class="form-select" name="inspected_type[]">
					                <option value="">Select Inspected Type</option>
					                @foreach(getItemsByGroupId(9010) as $type)
					                    <option value="{{$type->id}}"  {{ $breach->inspected_type == $type->id ? 'selected' : '' }}> {{$type->item_name}}
					                    </option>
					                @endforeach
					            </select>
					        </td>
					        <td>
					            <select class="form-select mb-2" name="breach_type[]">
					                <option value="">Select Breach Type</option>
					                @foreach(getItemsByGroupId(9004) as $type)
					                    <option value="{{$type->id}}" {{ $breach->breach_type == $type->id ? 'selected' : '' }}> {{$type->item_name}} </option>
					                @endforeach
					            </select>
					            <select class="form-select mb-2" name="breach_on[]">
					                <option value="">Select Breach on</option>
					                @foreach(getItemsByGroupId(9005) as $type)
					                    <option value="{{$type->id}}" {{ $breach->breach_on == $type->id ? 'selected' : '' }}>  {{$type->item_name}} </option>
					                @endforeach
					            </select>
					            <select class="form-select mb-2" name="breach_floor[]">
					                <option value="">Select Floor</option>
					                @foreach(getItemsByGroupId(9002) as $type)
					                    <option value="{{$type->id}}" {{ $breach->breach_floor == $type->id ? 'selected' : '' }}> {{$type->item_name}} </option>
					                @endforeach
					            </select>
					            <select class="form-select  mb-2" name="breach_use[]">
					                <option value="">Select Breach use</option>
					                @foreach(getItemsByGroupId(1052) as $type)
					                    <option value="{{$type->id}}" {{ $breach->breach_use == $type->id ? 'selected' : '' }}>  {{$type->item_name}} </option>
					                @endforeach
					            </select>
					        </td>
					        <td>
					            <textarea class="form-control" rows="2" name="location_details[]">{{ $breach->location_details }}</textarea>
					              <textarea class="form-control mt-2" rows="2" name="location_details_brief[]">{{ $breach->location_details_brief }}</textarea>
					        </td>
					        <td>
					            <div class="row g-1">
					                <div class="col-md-6">
					                    <input type="text"   class="form-control numeric-decimal" name="length[]" value="{{ $breach->length }}">
					                </div>
					                <div class="col-md-6">
	                    <select class="form-select mb-2" name="lunit[]">
	                    <option value="">Select Unit</option>
	                   @foreach(getItemsByGroupId(1008) as  $type)
			                   <option value="{{$type->id}}" {{ $breach->lunit == $type->id ? 'selected' : '' }}>{{$type->item_name}}</option>
			                   @endforeach
	                </select>                       
	                    </div>

					                <div class="col-md-6">
					                    <input type="text" class="form-control numeric-decimal" name="breadth[]"value="{{ $breach->breadth }}">
					                </div>
					                <div class="col-md-6">
	                    <select class="form-select mb-2" name="bunit[]">
	                    <option value="">Select Unit</option>
	                   @foreach(getItemsByGroupId(1008) as  $type)
			                   <option value="{{$type->id}}" {{ $breach->bunit == $type->id ? 'selected' : '' }}>{{$type->item_name}}</option>
			                   @endforeach
	                </select>                       
	                    </div>
					                <div class="col-md-6">
					                    <input type="text"  class="form-control numeric-decimal"  name="area[]" value="{{ $breach->area }}">
					                </div>
					                <div class="col-md-6">
	                    <select class="form-select mb-2" name="aunit[]">
	                    <option value="">Select Unit</option>
	                   @foreach(getItemsByGroupId(1008) as  $type)
			                   <option value="{{$type->id}}" {{ $breach->aunit == $type->id ? 'selected' : '' }}>{{$type->item_name}}</option>
			                   @endforeach
	                </select>                       
	                    </div>
					            </div>
					        </td>
					        <td>

					            <select class="form-select" name="objectionable[]">
					                <option value="" {{ $breach->objectionable === null ? 'selected' : '' }}>Select</option>
					                <option value="1" {{ $breach->objectionable === 1 ? 'selected' : '' }}>   Yes </option>
					                <option value="0" {{ $breach->objectionable === 0 ? 'selected' : '' }}> No </option>
					            </select>
					             <textarea class="form-control mt-2" rows="2" name="reason[]">{{ $breach->reason }}</textarea>
					        </td>
					        <td class="text-center">
					            <button type="button"  class="btn btn-danger btn-sm removeRow"> <i class="fa fa-trash"></i>  </button><br><br>
					                        <button type="button"
        class="btn {{ !empty($breach->id) ? 'btn-warning' : 'btn-success' }} btn-sm saveRow">
    {{ !empty($breach->id) ? 'Update' : 'Save' }}
</button>
					        </td>
					    </tr>
					    @endforeach
					@else
					   <tr class="breachRow" data-changed="0">
					   <td class="srNo" style="font-weight: bold;">1</td>
	            <td>
	            <input type="hidden"  class="row_id breach_id" value="{{$breach->id ?? ''}}">
	                <select class="form-select" name="inspected_type[]">
	                    <option value="">Select Inspected Type</option>
	                   @foreach(getItemsByGroupId(9010) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>
	            </td>
	            <td>
	                <select class="form-select mb-2" name="breach_type[]">
	                    <option value="">Select Breach Type</option>
	                    @foreach(getItemsByGroupId(9004) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>
	                 <select class="form-select mb-2" name="breach_on[]">
	                    <option value="">Select Breach on</option>
	                   @foreach(getItemsByGroupId(9005) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>
	                 <select class="form-select mb-2" name="breach_floor[]">
			                    <option value="">Select Floor</option>
			                   @foreach(getItemsByGroupId(9002) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
			                </select>
	                  <select class="form-select mb-2" name="breach_use[]">
	                    <option value="">Select Breach use</option>
	                   @foreach(getItemsByGroupId(1052) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>
	            </td>
	            <td><textarea class="form-control" rows="2" name="location_details[]"></textarea>
	             <textarea class="form-control mt-2" rows="2" name="location_details_brief[]"></textarea> </td>
	            <td>
	                <div class="row g-1">
	                    <div class="col-md-6">
	                        <input type="text" class="form-control numeric-decimal" placeholder="L" name="length[]">
	                    </div>

	                    <div class="col-md-6">
	                    <select class="form-select mb-2" name="lunit[]">
	                    <option value="">Select Unit</option>
	                   @foreach(getItemsByGroupId(1008) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>                       
	                    </div>
	                    <div class="col-md-6">
	                     <input type="text" class="form-control numeric-decimal" placeholder="B" name="breadth[]">                       
	                    </div>
	                     <div class="col-md-6">
	                    <select class="form-select mb-2" name="bunit[]">
	                    <option value="">Select Unit</option>
	                   @foreach(getItemsByGroupId(1008) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>                       
	                    </div>
	                    
	                     <div class="col-md-6">                   
	                        <input type="text" class="form-control numeric-decimal" placeholder="Area" name="area[]">
	                    </div>
	                    <div class="col-md-6">
	                    <select class="form-select mb-2" name="aunit[]">
	                    <option value="">Select Unit</option>
	                   @foreach(getItemsByGroupId(1008) as  $type)
			                   <option value="{{$type->id}}">{{$type->item_name}}</option>
			                   @endforeach
	                </select>                       
	                    </div>
	                </div>
	            </td>
	            <td>
	                <select class="form-select" name="objectionable[]">
	                    <option value="">Select</option>
	                      <option value="1">Yes</option> 
		                       <option value="0">No</option>
	                </select>
	                 <textarea class="form-control mt-2" rows="2" name="reason[]"></textarea>
	            </td>
	            <td class="text-center">
	                <button type="button" class="btn btn-danger btn-sm removeRow"> <i class="fa fa-trash"></i>  </button><br><br>
	                            <button type="button"
        class="btn {{ !empty($breach->id) ? 'btn-warning' : 'btn-success' }} btn-sm saveRow">
    {{ !empty($breach->id) ? 'Update' : 'Save' }}
</button>
	            </td>
	        </tr>
					@endif

					</tbody>

 </table>

	<div class="text-end">
	    <button type="button" class="btn btn-primary btn-sm" id="addMoreRow">  + Add </button>
	    <br><br>
	</div>

           <div class="part-title"> <h5>Unauthorised Construction Details</h5></div>

		<table class="table table-bordered" id="unauthorisedTable">
		    <thead>
		        <tr>
		          <th width="2%">S.No.</th>
		            <th width="18%">Inspected Type</th>
		            <th width="18%">Floor/Breach Use</th>
		            <th width="24%">Location / Details</th>
		            <th width="25%">Length/Breadth/Area/WPL/BPL</th>
		            <th width="10%">Objectionable / Reason</th>
		            <th width="5%">Action</th>
		        </tr>
		    </thead>
		    <tbody id="unauthorisedTableBody">
             @if(isset($inspectionReportBind) && $inspectionReportBind->unauthorisedDetails->count() > 0)
              @foreach($inspectionReportBind->unauthorisedDetails as $unauthorised)
             <tr class="unauthorisedRow"  data-changed="0">
             <td class="srNo" style="font-weight: bold;">1</td>
             <td>
             <input type="hidden"    class="row_id unauthorised_id"    value="{{$unauthorised->id ?? ''}}">
            <select class="form-select" name="unauthorised_inspected_type[]">
                <option value="">Select Inspected Type</option>
                @foreach(getItemsByGroupId(9010) as $type)
                    <option value="{{$type->id}}"  {{ $unauthorised->inspected_type == $type->id ? 'selected' : '' }}> {{$type->item_name}} </option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-select mb-2" name="floor_breach[]">
                <option value="">Select Floor</option>
                @foreach(getItemsByGroupId(9002) as $type)
                    <option value="{{$type->id}}"  {{ $unauthorised->floor_breach == $type->id ? 'selected' : '' }}>  {{$type->item_name}}  </option>
                @endforeach
            </select>
            <select class="form-select mb-2" name="floor_breach_use[]">
                <option value="">Select Breach use</option>
                @foreach(getItemsByGroupId(1052) as $type)
                    <option value="{{$type->id}}"  {{ $unauthorised->floor_breach_use == $type->id ? 'selected' : '' }}>{{$type->item_name}}  </option>
                @endforeach
            </select>
        </td>
        <td>
            <textarea class="form-control"  rows="2"  name="unauthorised_location_details[]">{{ $unauthorised->location_details }}</textarea>
             <textarea class="form-control mt-2" rows="2" name="unauthorised_location_details_brief[]">{{ $unauthorised->location_details_brief }}</textarea>
        </td>
        <td>
            <div class="row g-1">
                <div class="col-md-6">
                    <input type="text"  class="form-control numeric-decimal"  placeholder="L"  name="unauthorised_length[]"  value="{{ $unauthorised->length }}">
                </div>
                <div class="col-md-6">
                    <select class="form-select mb-2" name="lunit[]">
                        <option value="">Select Unit</option>
                        @foreach(getItemsByGroupId(1008) as $type)
                            <option value="{{$type->id}}"    {{ $unauthorised->lunit == $type->id ? 'selected' : '' }}> {{$type->item_name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control numeric-decimal"  placeholder="B"    name="unauthorised_breadth[]" value="{{ $unauthorised->breadth }}">
                </div>

                <div class="col-md-6">
                    <select class="form-select mb-2" name="bunit[]">
                        <option value="">Select Unit</option>
                        @foreach(getItemsByGroupId(1008) as $type)
                            <option value="{{$type->id}}"   {{ $unauthorised->bunit == $type->id ? 'selected' : '' }}>  {{$type->item_name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text"  class="form-control numeric-decimal"   placeholder="Area" name="unauthorised_area[]"   value="{{ $unauthorised->area }}">
                </div>

                <div class="col-md-6">
                    <select class="form-select mb-2" name="aunit[]">
                        <option value="">Select Unit</option>
                        @foreach(getItemsByGroupId(1008) as $type)
                            <option value="{{$type->id}}"   {{ $unauthorised->aunit == $type->id ? 'selected' : '' }}> {{$type->item_name}}  </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    WPL: <input type="text"   class="form-control numeric-decimal" name="wpl[]"   value="{{ $unauthorised->wpl }}">
                </div>
                <div class="col-md-6">
                    BPL :    <input type="text" class="form-control numeric-decimal"  name="bpl[]"  value="{{ $unauthorised->bpl }}">
                </div>
            </div>
        </td>
        <td>
            <select class="form-select" name="objectionable[]">
                 <option value="" {{ $unauthorised->objectionable === null ? 'selected' : '' }}>Select</option>
                <option value="1"  {{ $unauthorised->objectionable === 1 ? 'selected' : '' }}>   Yes  </option>
                <option value="0" {{ $unauthorised->objectionable === 0 ? 'selected' : '' }}>  No </option>
            </select>
             <textarea class="form-control mt-2" rows="2" name="reason[]"> {{ $unauthorised->reason }}</textarea>
        </td>

        <td class="text-center">
            <button type="button"   class="btn btn-danger btn-sm removeUnauthorisedRow">    <i class="fa fa-trash"></i> </button><br><br>
            <button type="button"
        class="btn {{ !empty($unauthorised->id) ? 'btn-warning' : 'btn-success' }} btn-sm saveRow">
    {{ !empty($unauthorised->id) ? 'Update' : 'Save' }}
</button>          
        </td>
    </tr>
    @endforeach
     @else
     <tr class="unauthorisedRow" data-changed="0">
     <td class="srNo" style="font-weight: bold;">1</td>
		            <td>
		            <input type="hidden"  class="row_id unauthorised_id" value="{{$unauthorised->id ?? ''}}">
		                <select class="form-select" name="unauthorised_inspected_type[]">
		                    <option value="">Select Inspected Type</option>
		                   @foreach(getItemsByGroupId(9010) as  $type)
		                   <option value="{{$type->id}}">{{$type->item_name}}</option>
		                   @endforeach
		                </select>
		            </td>
		            <td>
		                <select class="form-select mb-2" name="floor_breach[]">
		                    <option value="">Select Floor</option>
		                   @foreach(getItemsByGroupId(9002) as  $type)
		                   <option value="{{$type->id}}">{{$type->item_name}}</option>
		                   @endforeach
		                </select>
		                <select class="form-select mb-2" name="floor_breach_use[]">
		                   <option value="">Select Breach use</option>
                   @foreach(getItemsByGroupId(1052) as  $type)
		                   <option value="{{$type->id}}">{{$type->item_name}}</option>
		                   @endforeach
		                </select>
		            </td>
		            <td>
		                <textarea class="form-control" rows="2" name="unauthorised_location_details[]"></textarea>
		                 <textarea class="form-control mt-2" rows="2" name="unauthorised_location_details_brief[]"></textarea>		                
		            </td>
		            <td>
		            <div class="row g-1">
                    <div class="col-md-6">
                        <input type="text" class="form-control numeric-decimal" placeholder="L" name="unauthorised_length[]">
                    </div>

                    <div class="col-md-6">
                    <select class="form-select mb-2" name="lunit[]">
                    <option value="">Select Unit</option>
                   @foreach(getItemsByGroupId(1008) as  $type)
		                   <option value="{{$type->id}}">{{$type->item_name}}</option>
		                   @endforeach
                </select>                       
                    </div>
                    <div class="col-md-6">
                     <input type="text" class="form-control numeric-decimal" placeholder="B" name="unauthorised_breadth[]">                       
                    </div>
                     <div class="col-md-6">
                    <select class="form-select mb-2" name="bunit[]">
                    <option value="">Select Unit</option>
                   @foreach(getItemsByGroupId(1008) as  $type)
		                   <option value="{{$type->id}}">{{$type->item_name}}</option>
		                   @endforeach
                </select>                       
                    </div>
                    
                     <div class="col-md-6">                   
                        <input type="text" class="form-control numeric-decimal" placeholder="Area" name="unauthorised_area[]">
                    </div>
                    <div class="col-md-6">
                    <select class="form-select mb-2" name="aunit[]">
                    <option value="">Select Unit</option>
                   @foreach(getItemsByGroupId(1008) as  $type)
		                   <option value="{{$type->id}}">{{$type->item_name}}</option>
		                   @endforeach
                </select>                       
                    </div>
                     <div class="col-md-6">                   
                      WPL:   <input type="text" class="form-control numeric-decimal" placeholder="" name="wpl[]">
                    </div>
                     <div class="col-md-6">                   
                      BPL :   <input type="text" class="form-control numeric-decimal" placeholder="" name="bpl[]">
                    </div>
                </div>		             
		            </td>
		            <td>
		                <select class="form-select" name="objectionable[]">
		                    <option value="">Select</option>
		                       <option value="1">Yes</option> 
	                       <option value="0">No</option>
		                </select>
		                  <textarea class="form-control mt-2" rows="2" name="reason[]"></textarea>
		            </td>
		            <td class="text-center">
		             <button type="button" class="btn btn-danger btn-sm removeUnauthorisedRow"> <i class="fa fa-trash"></i>  </button><br><br>
		              <button type="button"
        class="btn {{ !empty($unauthorised->id) ? 'btn-warning' : 'btn-success' }} btn-sm saveRow">
    {{ !empty($unauthorised->id) ? 'Update' : 'Save' }}
</button>   
		            </td>
		        </tr>
		@endif
 </tbody>
		</table>
		<div class="text-end">
		    <button type="button" class="btn btn-primary btn-sm" id="addUnauthorisedRow">  + Add    </button>
		    <br><br>
		</div>
            <table class="table table-bordered mt-4">
                <tr>
                    <td>
                        <label class="form-label">The area of plot shown in Lease Deed</label>
                         <input type="text"  class="form-control" id="lease_deed_plot_area"  value="{{ $inspectionReportBind->lease_deed_plot_area ?? '' }}">
                    </td>
                    <td >
                       <label class="form-label">Does the plot area in the plan tallies with the area shown in Lease Deed</label>
                        <select class="form-select" id="plot_area_matches">
                            <option value="">Select</option>
                             <option value="1" {{ isset($inspectionReportBind) && $inspectionReportBind->plot_area_matches === 1 ? 'selected' : '' }}> Yes </option>
                <option value="0" {{ isset($inspectionReportBind) && $inspectionReportBind->plot_area_matches === 0 ? 'selected' : '' }}> No  </option>
                        </select>
                    </td>
                    <td>
                       <label class="form-label">If No, The area of plot shown in plan</label>
                          <input type="text" class="form-control" id="plan_plot_area"   value="{{ $inspectionReportBind->plan_plot_area ?? '' }}">
                    </td>
                </tr>
              
                <tr> 
                    <td>
                      <label class="form-label">Construction Area</label>
                       <input type="text"  class="form-control" id="construction_area"  value="{{ $inspectionReportBind->construction_area ?? '' }}">
                    </td>
                     <td>
                      <label class="form-label"> Whether any tree(s) stand in site (above 18" girth)</label>
                        <select class="form-select" id="tree_exists">
                            <option value="">Select</option>
                               <option value="1"{{ isset($inspectionReportBind) && $inspectionReportBind->tree_exists === 1 ? 'selected' : '' }}>Yes  </option>
                <option value="0" {{ isset($inspectionReportBind) && $inspectionReportBind->tree_exists === 0 ? 'selected' : '' }}>  No </option>
                        </select>
                    </td>
                     <td>
                      <label class="form-label">If yes, indicate number of tree(s)</label>
                       <input type="text"  class="form-control" id="tree_count"  value="{{ $inspectionReportBind->tree_count ?? '' }}">
                    </td>
                </tr>
               
            </table>
           
            <table class="table table-bordered">              
                <tr>
                    <td>Remarks</td>
                    <td> <textarea class="form-control" rows="3" id="remarks">{{ $inspectionReportBind->remarks ?? '' }}</textarea> </td>
                </tr>
                <tr>                 
                    <td>Signing Authority</td>
                    <td>
                       <select class="form-select" id="signing_authority" name="signing_authority">
    <option value="">Select</option>
    @foreach($signingAuthorities as $user)
        <option value="{{ $user->id }}" {{ (isset($inspectionReportBind) && $inspectionReportBind->signing_authority == $user->id) ? 'selected' : '' }}>{{ $user->name }}
        </option>
    @endforeach
</select>
                    </td>
                </tr>
            </table>
          
           <div class="text-center mt-4">
			    <div class="form-check d-inline-block me-4">
			     <input class="form-check-input"   type="checkbox" id="mark_final">
			        <label class="form-check-label  text-danger" for="mark_final">  Mark as Final Report </label>
			    </div>
			    @if(isset($inspectionReportBind->inspection_id))
			        <button type="button"   class="btn btn-primary px-4"  id="submitButton"> Update Inspection Report  </button>
			    @else
			        <button type="button" class="btn btn-primary px-4"   id="submitButton"> Save Inspection Report  </button>
			    @endif
			</div>
        </div>
    </div>
				@endif	
            </div>
        </div>
    </div>
            </div>            
        </div>        
    </div>
  <input type="hidden" id="selectedOldPropertyId" value="">
    <input type="hidden" id="isSplittedSearch" value="0">
    <input type="hidden" id="masterOldPropertyId" value="">
    <input type="hidden" id="searchedId" value="">
    <input type="hidden" id="withOutLeaseHold" value="{{ $withOutLeaseHold ?? 0 }}">
@include('include.alerts.ajax-alert')
<style>  .custom-fieldset{
            border:1px solid #bdbdbd;
            padding:20px;
            background:#fff;
            margin-bottom:20px;
            position:relative;
        }

        .custom-fieldset legend {
    float: none;
    width: auto;
    padding: 0 14px;
    margin-left: -4px;
    margin-bottom: 0;
    font-size: 16px;
    font-weight: 400;
    color: #fff;
    background: teal;
    border-radius: 4px;
}</style>
@endsection
@section('footerScript')
<script>
$(document).ready(function(){

    updateRowNumbers("#breachTableBody",".breachRow");

    updateRowNumbers("#unauthorisedTableBody",".unauthorisedRow");

});
	function updateRowNumbers(tableBody, rowClass)
	{
	    $(tableBody).find(rowClass).each(function(index){

	        $(this).find(".srNo").text(index + 1);

	    });
	}
$(document).on(
'change input',
'.unauthorisedRow input,.unauthorisedRow select,.unauthorisedRow textarea',
function(){
    $(this).closest("tr").attr("data-changed","1");
});
$(document).on(
'change input',
'.breachRow input,.breachRow select,.breachRow textarea',
function(){
    $(this).closest("tr").attr("data-changed","1");
});
		$("#mark_final").change(function () {
		    if ($(this).is(":checked")) {
		        Swal.fire({
		            icon: 'warning',
		            title: 'Finalize Inspection Report?',
		            html: `
		                <b>Important:</b><br><br>
		                Once this report is marked as Final and submitted:
		                <ul style="text-align:left;">
		                    <li>The report cannot be edited again.</li>
		                    <li>The inspection process will be completed.</li>
		                    <li>The file will be sent back to the concerned section.</li>
		                </ul>
		                Do you want to continue?
		            `,
		            showCancelButton: true,
		            confirmButtonText: 'Yes, Mark Final',
		            cancelButtonText: 'Cancel'
		        }).then((result) => {
		            if (!result.isConfirmed) {
		                $("#mark_final").prop("checked", false);
		            }
		        });
		    }
		});
	$(document).ready(function () {
	    // Common Function
	    $(document).on("click", ".saveRow", function () {
    let row = $(this).closest("tr");
    let isValid = true;

    if (row.hasClass("breachRow")) {
        isValid = validateValueUnitPairsRow(row);
        isValid = isValid && validateObjectionableReasonRow(row);
        if (!isValid) return;
        saveBreachRow(row);
    }

    if (row.hasClass("unauthorisedRow")) {
        isValid = validateValueUnitPairsRow(row);
        isValid = isValid && validateObjectionableReasonRow(row);
        isValid = isValid && validateWplBplAreaRow(row);
        if (!isValid) return;
        saveUnauthorisedRow(row);
    }
});
function saveUnauthorisedRow(row)
						     {

						    $.ajax({
						        url: "{{ route('inspection.saveUnauthorisedRow') }}",
						        type: "POST",
						        data: {
						            _token: "{{ csrf_token() }}",
						            inspectionId: $("#inspectionId").val(),
						            id: row.find(".unauthorised_id").val(),
						            inspection_officer: $("#inspection_officer").val(),
						            senior_officer: $("#senior_officer").val(),
						            inspection_date: $("#inspection_date_all").val(),
						            book_number: $("#book_number").val(),
						            page_number: $("#page_number").val(),
						            construction_tallies: $("#construction_tallies").val(),
						            sanction_plan_ref_no: $("#sanction_plan_ref_no").val(),
						            sanction_plan_date: $("#sanction_plan_date").val(),
						            property_status: $("#property_status").val(),
						            total_floors: $("#total_floors").val(),
						            lease_deed_plot_area: $("#lease_deed_plot_area").val(),
						            plot_area_matches: $("#plot_area_matches").val(),
						            plan_plot_area: $("#plan_plot_area").val(),
						            construction_area: $("#construction_area").val(),
						            tree_exists: $("#tree_exists").val(),
						            tree_count: $("#tree_count").val(),
						            remarks: $("#remarks").val(),
						            signing_authority: $("#signing_authority").val(),
						            // Row
						            inspected_type: row.find('[name="unauthorised_inspected_type[]"]').val(),
						            floor_breach: row.find('[name="floor_breach[]"]').val(),
						            floor_breach_use: row.find('[name="floor_breach_use[]"]').val(),
						            location_details: row.find('[name="unauthorised_location_details[]"]').val(),
						            location_details_brief: row.find('[name="unauthorised_location_details_brief[]"]').val(),
						            length: row.find('[name="unauthorised_length[]"]').val(),
						            lunit: row.find('[name="lunit[]"]').val(),
						            breadth: row.find('[name="unauthorised_breadth[]"]').val(),
						            bunit: row.find('[name="bunit[]"]').val(),
						            area: row.find('[name="unauthorised_area[]"]').val(),
						            aunit: row.find('[name="aunit[]"]').val(),
						            wpl: row.find('[name="wpl[]"]').val(),
						            bpl: row.find('[name="bpl[]"]').val(),
						            objectionable: row.find('[name="objectionable[]"]').val(),
						            reason: row.find('[name="reason[]"]').val()
						        },
						        success:function(res){
						            row.find(".unauthorised_id").val(res.id);
						            row.find(".saveRow")
						                .removeClass("btn-success")
						                .addClass("btn-primary")
						                .text("Update");
						            Swal.fire({
						                icon:'success',
						                title:'Success',
						                text:res.message,
						                timer:1500,
						                showConfirmButton:false
						            });
						        },
						        error:function(xhr){

						            Swal.fire(
						                'Error',
						                xhr.responseJSON.message,
						                'error'
						            );
						        }
						    });
						}
					function saveBreachRow(row)
					{

					    $.ajax({
					        url: "{{ route('inspection.saveBreachRow') }}",
					        type: "POST",
					        data: {
					            _token: "{{ csrf_token() }}",
					            inspectionId: $("#inspectionId").val(),
					            id: row.find(".breach_id").val(),
					            // Header
					            inspection_officer: $("#inspection_officer").val(),
					            senior_officer: $("#senior_officer").val(),
					            inspection_date: $("#inspection_date_all").val(),
					            book_number: $("#book_number").val(),
					            page_number: $("#page_number").val(),
					            construction_tallies: $("#construction_tallies").val(),
					            sanction_plan_ref_no: $("#sanction_plan_ref_no").val(),
					            sanction_plan_date: $("#sanction_plan_date").val(),
					            property_status: $("#property_status").val(),
					            total_floors: $("#total_floors").val(),
					            lease_deed_plot_area: $("#lease_deed_plot_area").val(),
					            plot_area_matches: $("#plot_area_matches").val(),
					            plan_plot_area: $("#plan_plot_area").val(),
					            construction_area: $("#construction_area").val(),
					            tree_exists: $("#tree_exists").val(),
					            tree_count: $("#tree_count").val(),
					            remarks: $("#remarks").val(),
					            signing_authority: $("#signing_authority").val(),
					            // Row
					            inspected_type: row.find('[name="inspected_type[]"]').val(),
					            breach_type: row.find('[name="breach_type[]"]').val(),
					            breach_on: row.find('[name="breach_on[]"]').val(),
					            breach_floor: row.find('[name="breach_floor[]"]').val(),
					            breach_use: row.find('[name="breach_use[]"]').val(),
					            location_details: row.find('[name="location_details[]"]').val(),
					            location_details_brief: row.find('[name="location_details_brief[]"]').val(),
					            length: row.find('[name="length[]"]').val(),
					            lunit: row.find('[name="lunit[]"]').val(),
					            breadth: row.find('[name="breadth[]"]').val(),
					            bunit: row.find('[name="bunit[]"]').val(),
					            area: row.find('[name="area[]"]').val(),
					            aunit: row.find('[name="aunit[]"]').val(),
					            objectionable: row.find('[name="objectionable[]"]').val(),
					            reason: row.find('[name="reason[]"]').val()
					        },

					        success:function(res){
					            row.find(".breach_id").val(res.id);
					            row.find(".saveRow")
					                .removeClass("btn-success")
					                .addClass("btn-primary")
					                .text("Update");
					            Swal.fire({
					                icon:'success',
					                title:'Success',
					                text:res.message,
					                timer:1500,
					                showConfirmButton:false
					            });
					        },

					        error:function(xhr){
					            Swal.fire(
					                'Error',
					                xhr.responseJSON.message,
					                'error'
					            );
					        }
					    });
					}	

	    function setupDynamicTable(
	        addButton,
	        rowClass,
	        tableBody,
	        removeButtonClass
	    ) {

	        // Add Row
	        $(document).on("click", addButton, function () {
	            let cloneRow = $(rowClass + ":first").clone();
					            cloneRow.find("input").val('');
					            cloneRow.find("textarea").val('');
					            cloneRow.find("select").prop('selectedIndex', 0);					            
   								 cloneRow.find(".row_id").val("");
   								 cloneRow.find(".saveRow").removeClass("btn-primary").addClass("btn-success").text("Save");
					            $(tableBody).append(cloneRow);
					            updateRowNumbers(tableBody, rowClass);
					        });
	
										
	        // Remove Row
	        $(document).on("click", removeButtonClass, function () {
						    let row = $(this).closest("tr");
						    let rowId = row.find(".row_id").val(); // hidden id
						    if ($(tableBody + " " + rowClass).length <= 1) {
						        alert("At least one row is required.");
						        return;
						    }
						    if (rowId != "") {
						        let type = row.hasClass("breachRow") ? "breach" : "unauthorised";
						        $.ajax({
						            url: "{{ route('inspection.delete.row') }}",
						            type: "POST",
						            data: {
						                _token: $('meta[name="csrf-token"]').attr('content'),
						                id: rowId,
						                type: type
						            },
						            success: function(res){
						                if(res.status){
						                    row.remove();
						                    updateRowNumbers(tableBody);
						                }else{
						                    alert("Delete failed");
						                }
						            }
						        });

						    } else {
						        row.remove();
						        updateRowNumbers(tableBody);
						    }
						});

	    }
		setupDynamicTable(
			    "#addMoreRow",
			    ".breachRow",
			    "#breachTableBody",
			    ".removeRow"
			   
			);

			setupDynamicTable(
			    "#addUnauthorisedRow",
			    ".unauthorisedRow",
			    "#unauthorisedTableBody",
			    ".removeUnauthorisedRow"
			   
			);

	});


</script>
 <script>
 
				 $(document).on('input', '.numeric-decimal', function () {
				    this.value = this.value.replace(/[^0-9.]/g, '');
				    // Sirf ek decimal allow kare
				    const parts = this.value.split('.');
				    if (parts.length > 2) {
				        this.value = parts[0] + '.' + parts.slice(1).join('');
				    }
				});
				$(document).on('input', '.numeric-only', function () {
					    this.value = this.value.replace(/\D/g, '');
					});
					$("#copyAddress").change(function () {
			    if ($(this).is(":checked")) {
			        $("#pname").val($("#cname").val());
			        $("#phouse").val($("#chouse").val());
			        $("#plocality").val($("#clocality").val());
			        $("#pcity").val($("#ccity").val());
			        $("#ppincode").val($("#cpincode").val());
			    } else {
			        $("#pname").val('');
			        $("#phouse").val('');
			        $("#plocality").val('');
			        $("#pcity").val('');
			        $("#ppincode").val('');		    
			        }
			});

		$(document).ready(function () {
		    let inspectionId = $("#inspectionId").val();
		    if (inspectionId != '') {
		        let propertyId = $("#oldPropertyId").val();
		        getPropertyBasicDetail(propertyId,withOutLeaseHold);
		    }
		});
       $("#searchButton").click(function () {
		    let propertyId = $("#oldPropertyId").val().trim();
		    if (!/^\d{5}$/.test(propertyId)) 
		    {
       			showError("Please enter valid 5 digit Property ID");
        		return false;
   			 }   
		    getPropertyBasicDetail(propertyId, withOutLeaseHold);
		    getInspectionStatus(propertyId);
		}); 
		       
         function getPropertyBasicDetail(propId, withOutLeaseHold) {               
               $.ajax({
                type: "post",
                url: "{{ route('propertyCommonBasicdetail') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    property_id: propId,
                    skipAccessCheck: {{ $skipAccessCheck }},
                    withOutLeaseHold: 0,
                },
                success: function(response) { 
                    if (response.status == "success") 
                    displayPropertyDetails(response.data, response.meta);//added by Swati 07092026
                    else {
                        showError(response.message);
                    }
                },
            });    
        }
        function displayPropertyDetails(data, meta = null) {
            $("#detail-container").empty();
            if (!meta || !meta.is_splitted_search) {
                $("#splitSearchMsg").addClass("d-none").html("");
                $("#isSplittedSearch").val("0");
                $("#masterOldPropertyId").val("");
                $("#searchedId").val("");
            }
            if (meta) {
                $("#searchedId").val(meta.searched_id ?? "");
                $("#isSplittedSearch").val(meta.is_splitted_search ? "1" : "0");
                $("#masterOldPropertyId").val(meta.master_old_property_id ?? "");
                if (meta.is_splitted_search) {
                    $("#splitSearchMsg")
                        .removeClass("d-none")
                        .html(`The searched Property ID <b>${meta.searched_id}</b> is <b>splitted</b>.
                            The main property will be transferred upon selection (<b>${meta.master_old_property_id}</b>).`);
                }
            }
            if (Array.isArray(data)) {
                $("#detail-container").html(`<tr>
                    <td colspan="5"><h6>Given property has ${data.length} propert${data.length > 1 ? "ies" : "y"}</h6></td>
                </tr>`);
                data.forEach(function(row, i) {
                    appendPropertyDetail(row,meta, true, i + 1);
                });
                $("#detail-container").append(`<tr>
                    <td colspan="5"><h5>Pease enter property id of splited property to continue</h5></td>
                </tr>`);
                $("#btn-rgr").prop("disabled", true);                
                $("#selectedOldPropertyId").val("");               
                $("#current_section").val("");                
            } else {
                appendPropertyDetail(data,meta);
                $("#property_id").val(data.id);
                $("#splited").val(data.is_joint_property === undefined ? 1 : 0);
                $("#selectedOldPropertyId").val(data.old_property_id ?? data.old_propert_id);
                let currentSectionText = "";
                if (meta && meta.master_section_code) {
                    currentSectionText = (meta.master_section_name ?? "") + " (" + meta.master_section_code + ")";
                }
                $("#current_section").val(currentSectionText);
            }

            $("#detail-card").removeClass("d-none");
        }
        function appendPropertyDetail(data,meta, isMultiple = false, rowNum = null) {        	
            if (isMultiple && rowNum) {
                $("#detail-container").append(`<tr>
                <td>${rowNum}</td><td colspan="4"></td>
            </tr>`);
            }
            // removed <td><b>Land Value : </b> &nbsp;-</td>
            let transferHTML = "";
            if (data.trasferDetails && data.trasferDetails.length > 0) {
                transferHTML = `<input type="hidden" name="property_master_id" id="property_master_id" value="${data.id}"><div class= "transfer-details" style="display: inline; position:relative">
            <span class="qmark">&#8505;
            <ul class="transfer-list container">
                <li class="transfer-list-item row row-lg-4">
                    <div class="transfer-list-cell col">#</div>
                    <div class="transfer-list-cell col">Transfer Date</div>
                    <div class="transfer-list-cell col">Process </div>
                    <div class="transfer-list-cell col">Lessee Name</div>
                    </li>
            `;
                data.trasferDetails.forEach((row, i) => {
                    transferHTML += `<li class="transfer-list-item row row-lg-4">
                    <div class="transfer-list-cell col">${i + 1}</div>
                    <div class="transfer-list-cell col">${row.transferDate}</div>
                    <div class="transfer-list-cell col">${row.process_of_transfer}</div>
                    <div class="transfer-list-cell col">${row.lesse_name}</div>
                    </li>`;
                });
                transferHTML += `</ul></span></div>`;//added by Swati 0n 07012026

            }

            $("#detail-container").append(`
          <tr>
            <td><b>Property ID : </b> &nbsp;${data.unique_propert_id} (${data.old_propert_id})</td>
            <td><b>Land Type : </b> &nbsp;${data.landTypeName}</td>
            <td><b>Land Use Type : </b> &nbsp;${data.proprtyTypeName}</td>
            <td><b>Land Use Subtype : </b> &nbsp;${data.proprtySubtypeName}</td>
            <td><b>Land Size : </b> &nbsp;${ Math.round(data.landSize * 100) / 100} Sq. Mtr.</td>
          </tr>
          <tr>
            <td><b>Block: </b> &nbsp;${data.block_no}</td>
            <td><b>Colony : </b> &nbsp;${data.colony}</td>
            <td><b>Property Status : </b> &nbsp;${data.statusName}</td>
            <td colspan="2"><b>Section  : </b> &nbsp;${data.section_code} &nbsp;(${meta.master_section_name}) </td>            
          </tr>
          <tr>
              <td><b>Status of RGR : </b> &nbsp;<span class="rgrStatus">${data.rgr == 1 ? "Yes" : "No"}</span></td>
              <td><b>Lessee/Owner Name : </b> &nbsp;${data.lesseName ? data.lesseName.replaceAll(',', ', ') : "N/A"} ${ data.trasferDetails && data.trasferDetails.length > 0 ? transferHTML : ""}</td>
              <td><b>Lease Type : </b> &nbsp;${data.leaseTypeName ? data.leaseTypeName : "N/A"}</td>
              <td><b>Owner&apos;s E-mail : </b> &nbsp;${data.email ? data.email : "N/A"}</td>
              <td><b>Owner&apos;s Phone Number: </b> &nbsp;${data.phone_no ? data.phone_no : "N/A"}</td>
          </tr>
          <tr>
            <td><b>Date of Allotment : </b> &nbsp;${data.leaseDate? data.leaseDate.split("-").reverse().join("-"):"N/A"}</td>
            <td><b>Lease Tenure : </b> &nbsp;${data.leaseTenure? data.leaseTenure + " years": "N/A"}</td>
            <td colspan="4"><b>Address : </b> &nbsp;${data.address ?? "N A"} </td>
          </tr>
        `);
        }       
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		function getInspectionStatus(propertyId)
		{
			//alert(propertyId);
	    $.ajax({
	        type: "GET",
	        url: '{{ route("getInspectionStatus") }}',
	        data: {
	            property_id: propertyId
	        },
		success: function (response) {
		    if (
		        response.status == true &&
		        response.type == 'already_created'
		    ) {
		        Swal.fire({
		            icon: 'warning',
		            title: 'Already Exists',
		            text: response.message
		        });
		    }
		    // Reopen or New
		    else if (
		        response.status == true &&
		        response.type == 'reopen_or_new'
		    ) {
		        Swal.fire({
		            icon: 'question',
		            title: 'Inspection Exists',
		            text: response.message,
		            showDenyButton: true,
		            confirmButtonText: 'Reopen',
		            denyButtonText: 'Create New'

		        }).then((result) => {
		            // Reopen old inspection
		            if (result.isConfirmed) {
		                window.location.href =
		                    'inspection/action/' +
		                    response.inspection_id+'?type=reopen';
		            }
		            // Create new inspection
		            else if (result.isDenied) {
		                console.log('Create New Inspection');
		                // call save function here
		                // saveInspection();
		            }

		        });
		    }

		}
	    });

	}
	$.ajaxSetup({
	    headers: {
	        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    }
		});	
		$(".form-control, .form-select, textarea").each(function () {
		    $(this).after('<small class="text-danger error"></small>');
		});
		$(".form-control, .form-select, textarea").on("input change", function () {
			$(this).removeClass('is-invalid');
        $(this).next(".error").html("");
     });
    function validateObjectionableReasonRow(row)
		{

		    let isValid=true;

		    let objectionable=row.find('[name="objectionable[]"]').val();
		    let reason=row.find('[name="reason[]"]');
		    reason.removeClass("is-invalid");
		    reason.next(".error").html("");
		    if(objectionable=="0" && reason.val().trim()=="")
		    {
		        reason.addClass("is-invalid");
		        reason.next(".error").html("Reason required");
		        isValid=false;
		    }
		    return isValid;

		}
		function validateWplBplAreaRow(row)
			{
			    let isValid=true;
			    let wplVal=row.find('[name="wpl[]"]').val().trim();
			    let bplVal=row.find('[name="bpl[]"]').val().trim();
			    let areaVal=row.find('[name="unauthorised_area[]"]').val().trim();

			    let wpl=parseFloat(wplVal)||0;
			    let bpl=parseFloat(bplVal)||0;
			    let area=parseFloat(areaVal)||0;

			    let areaInput=row.find('[name="unauthorised_area[]"]');

			    areaInput.removeClass("is-invalid");
			    areaInput.next(".error").html("");

			    if(wplVal!=="" || bplVal!=="")
			    {
			        let total=wpl+bpl;

			        if(areaVal=="")
			        {
			            areaInput.addClass("is-invalid");
			            areaInput.next(".error").html("Area is required.");
			            isValid=false;
			        }
			        else if(area!=total)
			        {
			            areaInput.addClass("is-invalid");
			            areaInput.next(".error").html("Area should be equal to WPL + BPL ("+total+")");
			            isValid=false;
			        }
			    }

			    return isValid;

			}
    function validateValueUnitPairsRow(row)
{
    let isValid = true;

    const pairs = [
        {
            valueField: row.find('[name$="length[]"]'),
            unitField: row.find('[name="lunit[]"]')
        },
        {
            valueField: row.find('[name$="breadth[]"]'),
            unitField: row.find('[name="bunit[]"]')
        },
        {
            valueField: row.find('[name$="area[]"]'),
            unitField: row.find('[name="aunit[]"]')
        }
    ];

    pairs.forEach(function(pair){

        let valueField = pair.valueField;
        let unitField  = pair.unitField;

        let value = valueField.val().trim();
        let unit  = unitField.val().trim();

        valueField.removeClass('is-invalid');
        unitField.removeClass('is-invalid');

        valueField.next('.error').html('');
        unitField.next('.error').html('');

        if (value !== '' && unit === '') {
            unitField.addClass('is-invalid');
            unitField.next('.error').html('Unit is required');
            isValid = false;
        }

        if (value === '' && unit !== '') {
            valueField.addClass('is-invalid');
            valueField.next('.error').html('Value is required');
            isValid = false;
        }

    });

    return isValid;
}
	function checkRequired(id, msg) {
		   //if ($("#" + id).val().trim() == "") {
		   	  if ($("#" + id).length && $("#" + id).val().trim() == "") {
		    	 $("#" + id).addClass('is-invalid');
		        $("#" + id).next(".error").html(msg);
		        return false;
		    }
		    return true;
		}
		$("#cpincode,#ppincode").on("input", function () {
		    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
		});
		$("#inspection_phone").on("input", function () {
     this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
	});
	$("#submitButton").click(function () {
		  $(".error").html("");
			let isValid = true;
			
			if($("#resasonfor").length)
			{/////////// Ist Step
				isValid &= checkRequired("resasonfor", "Reason is required");
				isValid &= checkRequired("cname", "Name is required");
				isValid &= checkRequired("chouse", "House No is required");
				isValid &= checkRequired("clocality", "Locality is required");
				isValid &= checkRequired("ccity", "City is required");			
				isValid &= checkRequired("cremarks", "Remarks is required");
				if ($("#cremarks").val().trim() == "") {
				    $("#cremarks").next(".error").html("Remarks is required");
				    isValid = false;
				}
				else if ($("#cremarks").val().trim().length < 50) {
				    $("#cremarks").next(".error").html("Remarks must be at least 50 characters");
				    isValid = false;
				}
				let pincode = $("#cpincode").val().trim();
				if (pincode == "") {
				    $("#cpincode").next(".error").html("Pincode is required");
				    $("#cpincode").addClass('is-invalid');
				    isValid = false;
				}
				else if (pincode.length != 6) {
				    $("#cpincode").next(".error").html("Pincode must be 6 digits");
				     $("#cpincode").addClass('is-invalid');
				    isValid = false;
				}			
			}
			if($("#inspection_date").length) 
			  { //Step 2nd 
			 
					if ($("#inspection_date").val() == "") {
					    $("#inspection_date").next(".error").html("Inspection Date is required");
					    $("#inspection_date").addClass('is-invalid');
					    isValid = false;
					    
					} 
				/*	else //if (!$("#inspection_date").prop("readonly")) 
					{

					    let selectedDate = new Date($("#inspection_date").val());
					    let today = new Date();
					    today.setHours(0, 0, 0, 0);

					    if (selectedDate <= today) {
					        $("#inspection_date").next(".error").html("Date must be a future date");
					        $("#inspection_date").addClass('is-invalid');
					        isValid = false;
					    }
					 
					}  */
					if ($("#inspection_time_from").val() == "") {
					    $("#inspection_time_from").next(".error").html("From Time is required");
					     $("#inspection_time_from").addClass('is-invalid');
					    isValid = false;
					}
					if ($("#inspection_time_to").val() == "") {
					    $("#inspection_time_to").next(".error").html("To Time is required");
					     $("#inspection_time_to").addClass('is-invalid');
					    isValid = false;
					}
					let fromTime = $("#inspection_time_from").val();
					let toTime   = $("#inspection_time_to").val();
					if (fromTime != "" && toTime != "") {
					    if (fromTime >= toTime) {
					        $("#inspection_time_to").next(".error").html(
					            "To Time must be greater than From Time"
					        );
					        $("#inspection_time_to").addClass('is-invalid');
					        isValid = false;
					    }
					}
						isValid &= checkRequired("pname", "Name  is required");
						isValid &= checkRequired("phouse", "House No. is required");
						isValid &= checkRequired("plocality", "Locality  is required");
						isValid &= checkRequired("pcity", "City is required");
					let ppincode = ($("#ppincode").val() || "").trim();
					if (ppincode == "") {
					    $("#ppincode").next(".error").html("Pincode is required");
					    $("#ppincode").addClass('is-invalid');
					    isValid = false;
					}
					else if (ppincode.length != 6) {
					    $("#ppincode").next(".error").html("Pincode must be 6 digits");
					     $("#ppincode").addClass('is-invalid');
					    isValid = false;
					}

					let email = ($("#inspection_email").val() || "").trim();
					if (email != "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
					    $("#inspection_email").next(".error").html("Invalid email address");
					     $("#inspection_email").addClass('is-invalid');
					    isValid = false;
					}
					let phone = ($("#inspection_phone").val() || "").trim();					
					if (phone != "" && !/^[0-9]{10}$/.test(phone)) {
					    $("#inspection_phone").next(".error").html("Phone number must be 10 digits");
					     $("#inspection_phone").addClass('is-invalid');
					    isValid = false;
					}
		      }
			if ($("#refused_by").length)
			{ // step -3
					if ($("#refused_by").val().trim() == "") {
				    $("#refused_by").next(".error").html("Refused By is required");
				    $("#refused_by").addClass('is-invalid');
				    isValid = false;
				}
				else if ($("#refused_by").val().trim().length < 10) {
				
				    $("#refused_by").next(".error").html("Minimum 10 characters required");
				    $("#refused_by").addClass('is-invalid');
				    isValid = false;
				}

				if ($("#reason").val().trim() == "") {
				    $("#reason").next(".error").html("Reason/Remarks is required");
				    $("#reason").addClass('is-invalid');
				    isValid = false;
				}
				else if ($("#reason").val().trim().length < 20) {
				    $("#reason").next(".error").html("Minimum 20 characters required");
				    $("#reason").addClass('is-invalid');
				    isValid = false;
				}
			}	
			if ($("#inspection_date_all").length)
			{ //step -4
						
				let totalFloors = ($("#total_floors").val() || "").trim();
				if ($("#property_status").val() == "322") {
				    if (totalFloors === "") {
				        $("#total_floors").next(".error").html("Number of floors is required");
				        $("#total_floors").addClass("is-invalid");
				        isValid = false;
				    }
				}
				
				let tree_count = ($("#tree_count").val() || "").trim();
				if ($("#tree_exists").val() == "1") {
				    if (tree_count === "") {
				        $("#tree_count").next(".error").html("Number of tree required");
				        $("#tree_count").addClass("is-invalid");
				        isValid = false;
				    }
				}
				let plan_plot_area = ($("#plan_plot_area").val() || "").trim();
				if ($("#plot_area_matches").val() == "0") {
				    if (plan_plot_area === "") {
				        $("#plan_plot_area").next(".error").html("Plan area is required");
				        $("#plan_plot_area").addClass("is-invalid");
				        isValid = false;
				    }
				}
				
				
			}
			
			if ($('#bclouse').length) 
			{// step -5
		    // let bclouse = ($("#bclouse").val() || "").trim();
		    // let breenter = ($("#breenter").val() || "").trim();
		    // if (bclouse == "") {
		    //     $("#bclouse").next(".error").html("Breach Clause is required");
		    //     $("#bclouse").addClass('is-invalid');
		    //     isValid = false;
		    // }
		    // if (breenter == "") {
		    //     $("#breenter").next(".error").html("Re-enter Clause is required");
		    //     $("#breenter").addClass('is-invalid');
		    //     isValid = false;
		    // }
		    // else if (bclouse !== breenter) {
		    //     $("#breenter").next(".error").html("Both clauses must match");
		    //     $("#breenter").addClass('is-invalid');
		    //     isValid = false;
		    // }
		    if ($("#bremarks").val().trim() == "") {
				    $("#bremarks").next(".error").html("Remarks is required");
				    isValid = false;
				}
				else if ($("#bremarks").val().trim().length < 20) {
				    $("#bremarks").next(".error").html("Remarks must be at least 20 characters");
				    isValid = false;
				}
		}
//			console.log("Step1", $("#resasonfor").length);
//			console.log("Step2", $("#inspection_date").length);
//			console.log("Step3", $("#refused_by").length);
		if (!isValid) return;
		let saveUrl = "";
		@if((isset($schedule) && $schedule === 'schedule') || (isset($reschedule) && $reschedule === 'reschedule'))
		saveUrl = "{{ route('save.inspection.request') }}";
		let formData = { 
			   inspectionId : $("#inspectionId").val(),
		       schedule_date      : $("#inspection_date").val(),
		       schedule_fromtime  : $("#inspection_time_from").val(),
		       schedule_totime    : $("#inspection_time_to").val(),		      
		       refused_by         : $("#refused_by").val(),
		       reason             : $("#reason").val(),
			   communication_address :{
	        	    second_address : {			     
			            inspection_officer   : $("#inspection_officer").val(),
			            pname              : $("#pname").val(),
			            phouse               : $("#phouse").val(),
			            plocality            : $("#plocality").val(),
			            pcity                : $("#pcity").val(),
			            ppincode             : $("#ppincode").val(),
			            inspection_email     : $("#inspection_email").val(),
			            inspection_phone     : $("#inspection_phone").val(),
				 }     			     
			    }
			};
			@elseif(isset($inspectionreport) && $inspectionreport === 'inspectionreport')
			     saveUrl = "{{ route('save.inspection.report') }}";	
			   
		    let formData = {
		        inspectionId : $("#inspectionId").val(),
		        inspection_officer : $("#inspection_officer").val(),
		        senior_officer     : $("#senior_officer").val(),
		        inspection_date : $("#inspection_date_all").val(),
		        book_number     : $("#book_number").val(),
		        page_number     : $("#page_number").val(),
		        construction_tallies : $("#construction_tallies").val(),
		        sanction_plan_ref_no : $("#sanction_plan_ref_no").val(),
		        sanction_plan_date   : $("#sanction_plan_date").val(),
		        property_status : $("#property_status").val(),
		        total_floors    : $("#total_floors").val(),
		        lease_deed_plot_area : $("#lease_deed_plot_area").val(),
		        plot_area_matches    : $("#plot_area_matches").val(),
		        plan_plot_area       : $("#plan_plot_area").val(),
		        construction_area : $("#construction_area").val(),
		        tree_exists : $("#tree_exists").val(),
		        tree_count  : $("#tree_count").val(),
		        remarks : $("#remarks").val(),
		        signing_authority : $("#signing_authority").val(),
		        mark_final : $("#mark_final").is(":checked") ? 1 : 0,		       
		    };
			@elseif(isset($type) && $type === 'breachnotice')
			 saveUrl = "{{ route('save.inspection.request') }}";    
	   		let formData = {
	       bremarks   : $("#bremarks").val(),
	       inspectionId : $("#inspectionId").val(),
	       communication_address : {
			    breach_address : {
			        bname : $("#bname").val(),
			        bhouse : $("#bhouse").val(),
			        blocality : $("#blocality").val(),
			        bcity : $("#bcity").val(),
			        bpincode : $("#bpincode").val(),
			        binspection_email : $("#binspection_email").val(),
			        binspection_phone : $("#binspection_phone").val(),
			        bofficer : $("#bofficer").val(),
			        btelephone : $("#btelephone").val(),			       
			        bclouse : $("#bclouse").val(),
					breenter : $("#breenter").val()
			    }
			}
	    };
	    @else	 
	    saveUrl = "{{ route('save.inspection.request') }}";    
	    let formData = {	    	
	        resasonfor : $("#resasonfor").val(),	       
	        cremarks   : $("#cremarks").val(),
	        inspectionId :  $("#inspectionId").val(),	        
	        selectedOldPropertyId:  $("#selectedOldPropertyId").val(),
	        communication_address :{
	        	          	 main_address: {
	        	                    cname      : $("#cname").val(),
							        chouse     : $("#chouse").val(),
							        clocality  : $("#clocality").val(),
							        ccity      : $("#ccity").val(),
							        cpincode   : $("#cpincode").val(),
	                               }	
			}
	    };
	    @endif	   
	    
	    $.ajax({
	        url: saveUrl,
	        type: "POST",
	        data: formData,
	        beforeSend: function () {
	            $("#submitButton")
	                .html('Saving...')
	                .prop('disabled', true);
	        },
	        success: function (response) {
	            if (response.status == true) {
	               Swal.fire({
					    icon: 'success',
					    title: 'Success',
					    text: response.message,
					    timer: 2000,
					    showConfirmButton: false
					})
					.then(() => {
                    window.location.href =
                        "{{ route('inspectionlist') }}";
                });
	                $("#submitButton")
	                    .html('Save')
	                    .prop('disabled', false);
	                $("input").val('');
	                $("textarea").val('');
	                $("select").val('');
	            }
	        },
	       error: function (xhr) {
			    $("#submitButton")
			        .html('Save')
			        .prop('disabled', false);
			    if (xhr.status === 422) {
			        let errors = xhr.responseJSON.errors;
			        let errorString = '';
			        $.each(errors, function (key, value) {
			            errorString += value[0] + '<br>';
			        });
			        Swal.fire({
			            icon: 'error',
			            title: 'Validation Error',
			            html: errorString,
			        });
			    } else {
			        Swal.fire({
			            icon: 'error',
			            title: 'Oops...',
			            text: 'Something went wrong!',
			        });
			    }
			}
	    });
	});
		    $("#btn-reset-filter").click(function(){
		    	  window.location.href = "{{ route('paymentSummary') }}";
		    })
	</script>
@endsection