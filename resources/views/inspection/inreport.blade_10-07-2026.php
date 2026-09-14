<!DOCTYPE html>
<html lang="en">

    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- External CSS libraries -->
        <!-- Favicon icon -->
        <link rel="shortcut icon" href="{{ asset('assets/frontend/assets/img/favicon.ico') }}" type="image/x-icon" />

        <!-- Custom Stylesheet -->
        <!-- Custom Stylesheet -->
    </head>
    <style>
        body {
            /* font-family: 'DejaVu Sans', sans-serif; */
            font-family: sans-serif !important;
            margin: 0;
            padding: 0;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url("{{ public_path('assets/images/water-mark-emblem.png') }}") center center no-repeat;
            opacity: 0.1;
        }

        /* body::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url(assets/images/water-mark.png) 0 0 repeat;
            transform: rotate(-30deg);
            opacity: 0.1;
        } */
        .watermark{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("{{ public_path('assets/images/water-mark.png') }}") repeat;
            transform: rotate(-30deg);
            opacity: 0.1;
            z-index: -99;
        }

        .emblem-div {
            width: 100%;
            text-align: center;
        }

        .emblem {
            display: inline-block;
            margin: auto;
        }

        .title-main {
            color: navy;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .title-sub {
            color: navy;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .part-title {
            background-color: #1fa1a2;
            color: white;
            font-size: 14px;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .content-wrap {
            margin-right: 30px;
            margin-left: 30px;
        }
        .name-sign{
            font-size: 25px;
            text-align: right;
        }
          .content-wrap p{
            font-size: 14px;
        }
        .qr-img{
            text-align: center;
        }
        .bold{
            font-weight: bold;
        }
         .hidden-table,
        .hidden-table th,
        .hidden-table td{
            border:0;
            font-size:12px;
            vertical-align: top;
            margin:0 0 10px;
        }
        .hidden-table th p,
        .hidden-table td p{
            margin: 0;
        }
        p{
            text-align: justify;
        }
        img {
            image-rendering: optimizeQuality;
            -dompdf-image-resolution: 72dpi;
        }
         .info-table{
            width:100%;
            border-collapse: collapse;
            margin-bottom:30px;
        }
        table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

th, td {
    border: 1px solid #bbb;
    padding: 3px;
    font-size: 10px;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

    </style>

<body>
    <div class="watermark"></div>
    <!-- Login 8 section start -->
    <div class="content-wrap">
    <div style="text-align: center"> <div class="emblem-div">
                        <img src="{{ public_path('assets/images/emblem.jpg') }}" width="40" alt="Emblem" class="emblem">
                    </div>
                    <h1 class="title-main">Government of India</h1>
                    <h1 class="title-main">Ministry of Housing and Urban Affairs</h1>                    
                    <h1 class="title-main">Land And Development Office</h1></div>
        
        <div class="part-title">
        	@if($type != 'breachnoticep')	
         {{$inspectionReportBind->mark_final == 0 ? "Draft": "Final"}} Inspection Report  
         @else
          Breach Notice
          @endif
         </div>

        <!-- Membership Details Table -->
       <table class="info-table">
				    <tr>
				        <td><span class="bold">Property ID:</span> {{ $inspection->property_id }} </td>
				        <td><span class="bold">Report Date:</span> {{ \Carbon\Carbon::parse($inspectionReportBind->created_at)->format('d-M-Y h:i:s A') }}</td>
				    </tr>
				    <tr>
				        <td><span class="bold">Inspection ID:</span> {{ $inspection->inspection_id }} </td>
				        <td> <span class="bold">Application ID:</span>   {{ $inspection->application_no }}</td>
				    </tr>
				    <tr>
				        <td> <span class="bold">Property Known As:</span>  {{ $inspection->property_known_as }} </td>
				        <td><span class="bold">Address:</span> {{ $propertyData['address'] ?? '***' }} </td>
				    </tr>
				</table>
				@if($type == 'breachnoticep')
				@php
			    $comm = json_decode($inspection->communication_address ?? '{}');
			    $address = $comm->breach_address ?? $comm->second_address ?? null;
			@endphp
		<div class="content" style="font-size:14px; line-height:22px;">
		    <p> To,
		        <br><br>
		         {{ $address->bname ?? $address->pname ?? 'N/A' }} <br>
		         {{ $inspection->property_known_as ?? '' }} <br>
		         {{ $address->bhouse ?? $address->phouse ?? 'N/A' }} <br>
			    {{ $address->blocality ?? $address->plocality ?? 'N/A' }} <br>
			    {{ $address->bcity ?? $address->pcity ?? 'N/A' }} <br>
			    {{ $address->bpincode ?? $address->ppincode ?? 'N/A' }}
		    </p>
		    <p>
		        <strong>Subject:Notice served on the lessee to remedy the breaches before exercising re-entry powers for unauthorised construction / misuse of  Property Id : {{$inspection->property_id}}  Known as {{ $inspection->property_known_as }}</strong>
		    </p>
		    <p>Sir/Madam,</p>
		    <p>I am to refer to your Letter No. ___________________ dated __________________ and to say that the Inspection of the above premises on  {{ !empty($inspection->schedule_date) ? \Carbon\Carbon::parse($inspection->schedule_date)->format('d-M-Y') : 'N/A' }} the following breaches were noticed at site by the Inspection Officer : </p>
		    </div>
				@endif
				@if($type != 'breachnoticep')	
      <div class="part-title">  Property Details</div>
					<table>
					    <tr>					    
					    <td><b>Block Number</b></td>
					        <td>{{ $propertyData['block_no'] ?? '***' }}</td>
					       <td><b>Colony</b></td>
					        <td>{{ $propertyData['colony'] ?? '***' }}</td>					        
					         <td><b>Land Type</b></td>
					        <td colspan="3">{{ $propertyData['landTypeName'] ?? '***' }}</td>
					    </tr>
					    <tr>					        
					        <td><b>Land Size</b></td>
					        <td>{{ isset($propertyData['landSize']) ? round($propertyData['landSize'], 2) . ' Sq. Mtr.' : '***' }}</td>
					        <td><b>Property Type</b></td>
					        <td>{{  $propertyData['proprtyTypeName'] ?? '***' }}</td>
					         <td><b>Property Sub Type</b></td>
					        <td>{{  $propertyData['proprtySubtypeName'] ?? '***' }}</td>
					        <td><b>Property Status</b></td>
					        <td>{{  $propertyData['statusName'] ?? '***' }}</td>
					    </tr>
					   
					</table>
				
    <div class="part-title">  Lessee Details </div>
					<table>
					    <thead>
					        <tr> <th width="10%">S.No.</th>
					         <th>Lessee Name</th>
					         <th>Transfer Date</th> 
					          <th> Process Name</th>
					        </tr>
					    </thead>
					   
					    <tbody> 
					        @foreach($propertyData['trasferDetails'] as $key => $lessee)
					        <tr>
					            <td>{{ $key + 1 }}</td>
					             <td>{{ $lessee->lesse_name }}</td>
					            <td>{{ $lessee->transferDate }}</td>
					             <td>{{ $lessee->process_of_transfer }}</td>
					        </tr>
					        @endforeach
					    </tbody>
					</table>					
       <div class="part-title"> Inspection Details</div>
						<table>
						    <tr>
						        <td><b>Name Of The Inspecting Officer</b></td>
						        <td>{{ $inspectionReportBind->inspection_officer }}</td>
						    </tr>
						    <tr>
						        <td><b>Name Of The Accompanied Senior Officer</b></td>
						        <td>{{ $inspectionReportBind->senior_officer ?? '***' }}</td>
						    </tr>
						    <tr>
						        <td><b>Inspection Date</b></td>
						        <td>{{ \Carbon\Carbon::parse($inspectionReportBind->inspection_date)->format('d-M-Y') }}</td>
						    </tr>
						    <tr>
						        <td><b>Book Number</b></td>
						        <td>{{ $inspectionReportBind->book_number }}</td>
						    </tr>
						    <tr>
						        <td><b>Page Number</b></td>
						        <td>{{ $inspectionReportBind->page_number }}</td>
						    </tr>
						    <tr>
						        <td><b>Construction Tallies With Sanction Plan</b></td>
						        <td>{{ $inspectionReportBind->construction_tallies == 1 ? 'Yes' : 'No' }}</td>
						    </tr>
						    <tr>
						        <td><b>Reference Number Of Sanctioned Plan</b></td>
						        <td>{{ $inspectionReportBind->sanction_plan_ref_no }}</td>
						    </tr>
						    <tr>
						        <td><b>Date Of Sanctioned Plan</b></td>
						        <td>{{ $inspectionReportBind->sanction_plan_date }}</td>
						    </tr>
						    <tr>
						        <td><b>Status Of Property</b></td>
						        <td>{{ getServiceNameById($inspectionReportBind->property_status) }}</td>
						    </tr>
						    <tr>
						        <td><b>No. Of Floors Built</b></td>
						        <td>{{ $inspectionReportBind->total_floors }}</td>
						    </tr>
						</table>
						@endif
						<div class="part-title"> Breach Details</div>

									<table>
									    <thead>
									        <tr>
									            <th>#</th>
									            <th>Inspected Type</th>
									              <th>Breach Type</th>
									            <th>Breach On</th>
									            <th>Floor</th>
									            <th>Breach Use</th>
									            <th>Location</th>
									            <th  width="30%">Details</th>
									            <th>Length</th>
									            <th>Breadth</th>
									            <th>Area</th>
									            <th>Objectionable / Reason</th>
									        </tr>
									    </thead>

									    <tbody>
									        @forelse($inspectionReportBind->breaches as $row)
									        <tr>
									            <td>{{ $loop->iteration }}</td>
									            <td>{{ getServiceNameById($row->inspected_type) }}</td>
									            <td>{{ getServiceNameById($row->breach_type) }}</td>
									            <td>{{ getServiceNameById($row->breach_on) }}</td>
									            <td>{{ getServiceNameById($row->breach_floor) }}</td>
									            <td>{{ getServiceNameById($row->breach_use) }}</td>
									            <td>{{ $row->location_details }}</td>
									             <td>{{ $row->location_details_brief }}</td>
									            <td>{{ $row->length}} {{ getServiceNameById($row->lunit)}}</td>
									            <td>{{ $row->breadth }} {{ getServiceNameById($row->bunit)}}</td>
									            <td>{{ $row->area }} {{ getServiceNameById($row->aunit)}}</td>
									            <td>{{ $row->objectionable ? 'YES' : 'NO' }} / {{$row->reason ?? 'N/A'}}</td>
									        </tr>
									        @empty
									        <tr>
									            <td colspan="10" align="center">No Record Found</td>
									        </tr>
									        @endforelse
									    </tbody>
									</table>
									<div class="part-title">  Unauthorised Construction Details </div>
									<table>
									    <thead>
									        <tr>
									            <th>#</th>
									            <th>Inspected Type</th>
									            <th>Floor</th>
									            <th>Breach Use</th>
									            <th>Location</th>
									            <th  width="30%">Details</th>
									            <th>Length</th>
									            <th>Breadth</th>
									            <th>Area</th>
									            <th>WPL /BPL</th>									           
									            <th>Objectionable / Reason</th>
									        </tr>
									    </thead>
								    <tbody>  
								        @forelse($inspectionReportBind->unauthorisedDetails as $row)
								        <tr>
								            <td>{{ $loop->iteration }}</td>
								            <td>{{ getServiceNameById($row->inspected_type) }}</td>
								            <td>{{ getServiceNameById($row->floor_breach) }}</td>
								            <td>{{ getServiceNameById($row->floor_breach_use) }}</td>
								            <td>{{ $row->location_details }}</td>
								             <td>{{ $row->location_details_brief }}</td>
								            <td>{{ $row->length}} {{ getServiceNameById($row->lunit)}}</td>
									        <td>{{ $row->breadth }} {{ getServiceNameById($row->bunit)}}</td>
									        <td>{{ $row->area }} {{ getServiceNameById($row->aunit)}}</td>
								            <td>{{ $row->wpl }} / {{ $row->bpl }}</td>								          
								           <td>{{ $row->objectionable ? 'YES' : 'NO' }} / {{$row->reason ?? 'N/A'}}</td>
								        </tr>
								        @empty
								        <tr>
								            <td colspan="9" align="center">No Record Found</td>
								        </tr>
								        @endforelse
								    </tbody>
								</table>
								@if($type != 'breachnoticep')	
								  <div class="part-title"> Other Details</div>
						<table>
						    <tr>
						        <td><b>The area of plot shown in Lease Deed</b></td>
						        <td>{{ $inspectionReportBind->lease_deed_plot_area ??'N/A' }}</td>
						    </tr>
						    <tr>
						        <td><b>Does the plot area in the plan tallies with the area shown in Lease Deed</b></td>
						        <td>{{ $inspectionReportBind->plot_area_matches == 1 ? 'YES' : 'NO' }}</td>
						    </tr>
						    <tr>
						        <td><b>If No, The area of plot shown in plan</b></td>
						        <td>{{ $inspectionReportBind->plan_plot_area ?? 'N/A' }}</td>
						    </tr>
						    <tr>
						        <td><b>Construction Area</b></td>
						        <td>{{ $inspectionReportBind->construction_area ?? 'N/A' }}</td>
						    </tr>
						    <tr>
						        <td><b>Whether any tree(s) stand in site (above 18" girth)</b></td>
						        <td>{{ $inspectionReportBind->tree_exists == 1 ? 'YES' : 'NO'  }}</td>
						    </tr>
						    <tr>
						        <td><b>If yes, indicate number of tree(s)</b></td>
						        <td>{{ $inspectionReportBind->tree_count  ?? 'N/A' }}</td>
						    </tr>
						    
						</table>
								<div class="part-title">  Remarks</div>
							<table>
							    <tr>
							        <td>{{ $inspectionReportBind->remarks ?? '***' }}</td>
							    </tr>
							</table>
                       
							<div class="letter-footer" style="margin-top:100px;">
    <table style="width:100%; border:none;">
        <tr>
            <td style="width:50%; border:none; text-align:left;">
                ({{ $inspectionReportBind->inspection_officer }})
                <br>
                JUNIOR ENGINEER
            </td>

            <td style="width:50%; border:none; text-align:right;">
                ({{ $inspectionReportBind->signing_authority }})
                <br>
                A.E.-I / A.E.-II / B.O.
            </td>
        </tr>
    </table>
</div>
  @endif
  @if($type == 'breachnoticep')
  <p> 1. This breaches are in contravention of clause / clauses  of the Lease Deed / Agreement for lease / Allotment letter / Terms of allotment. </p>
  <p>2. Your are, therefore, hereby required to remedy the breaches within 30 days from the date of reciept of this notice failing which action to re-enter the  premises under clause of the lease deed will be taken against you without any further notice in the matter. </p>
  <p>3. You are also liable to pay the damage / misuse charges (which will be intimated to you in due course) for having committed the breaches of the terms of the lease deed shown in Para-1 above for the period of their existence. </p>
  <p>4. In case you have any point to clarify in connection with the above notice, you may kindly meet the undersigned, by prior appointment, within one week from the date of receipt of this notice.<br>
It may, however, be clearly understood that your inability or failure to avail yourself of this opportunity for a personal hearing/discussion shall not be accepted as a ground for withholding or delaying further action in the matter under the terms and conditions of the lease deed.
</p>
        <br><br>
        			<div class="letter-footer" style="margin-top:100px;">
    <table style="width:100%; border:none;">
        <tr>
            <td style="width:50%; border:none; text-align:left;">
               Copy To:-
                <br>
               Accounts Section
            </td>

            <td style="width:50%; border:none; text-align:right;">
                ({{$address->bofficer }})
                <br>
                <br>
                <br>
                 <br>
                <br>
              DEPUTY L&DO
            </td>
        </tr>
    </table>
</div>
@endif
       <!--  <div class="signature-box">
            <div class="signature-inner">
                <div class="signature-line"></div>
                <div class="signature-label">Branch Officer</div>
            </div>
        </div> -->
    </div>
</body>
</html>
