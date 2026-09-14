@extends('layouts.app')

@section('title', 'Property Inspection List')

@section('content')
   
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Inspection </div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Inspection </li>
                <li class="breadcrumb-item active" aria-current="page">List</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>
<div class="">
<style>.card {
    margin-bottom: 0.5rem;
}</style>
<div class="card">
	<div class="card-body"> 
        <div  class="row mb-3">
                <form class="row mb-3">
                        
                    <div class="col-md-8">
                        <div class="row">
                         <div class="col-md-4">
                                <label for="startDate" class="form-label">Inspection Id:</label>
                                <input type="text" class="form-control " id="inspectionid" autocomplete="off" name="inspectionid" value="{{$request->input('inspectionid')}}">
                            </div>
                            <div class="col-md-3">
                                <label for="startDate" class="form-label">Start Date:</label>
                                <input type="text" class="form-control datepicker" id="startDate" autocomplete="off" name="start_date" value="{{$request->input('start_date')}}">
                            </div>
                            <div class="col-md-3">
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
	 @if(Auth::user()->sections()->first()->section_code !== 'TECH')
     @can('create.inspection') 
	<div class="d-flex justify-content-between py-3">
                <h6 class="mb-0 text-uppercase tabular-record_font align-self-end"></h6>
                <a href="{{ route('inspectionRequest') }}"><button class="btn btn-primary">+ Inspection Request</button></a>
            </div>
            @endcan
            @endif
          <div class="table-responsive">
            <table id="example" class="table table-striped table-bordered datatable-export" style="width:100%">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Inspection ID</th>
            <th>Property ID</th>
             <th>Property Known as</th>
             <th>Section</th>
            <th>Application No</th>
            <th>Application Type</th>
             <th>Name & Address</th>  
             <th>Schedule Date time</th>
            <th>Status</th>
              <th>Reason/Remarks</th>
            <th>Created Date</th>
            <th>Action</th>
        </tr>
    </thead>   
    <tbody>
        @forelse($inspectionRequests as $inspection)
       @php
		$comm = json_decode($inspection->communication_address ?? '{}');
       $mainAddress = $comm->main_address ?? null;
		@endphp
        <tr>
            <td>{{ $loop->iteration }} </td>
            <td>{{ $inspection->inspection_id }}</td>
            <td>{{ $inspection->property_id }}  </td>
             <td>{{$inspection->property_known_as}}  </td>
             <td>{{$inspection->property?->section_code}}  </td>              
            <td>{{ $inspection->application_no ?? 'N/A' }} </td>
            <td>{{ getServiceNameById($inspection->application_type) ?? 'N/A' }}</td>
           <td> {{$comm->main_address->cname ?? 'N/A'}}<br> 
            {{ $comm->main_address->chouse ?? 'N/A' }}
            <br>{{ $comm->main_address->clocality ?? 'N/A' }}
            <br>{{ $comm->main_address->ccity ?? 'N/A' }}
            <br>{{ $comm->main_address->cpincode ?? 'N/A' }}</td>
            <td><i class="bx bx-calendar"></i> {{!empty($inspection->schedule_fromtime)  ? \Carbon\Carbon::parse($inspection->schedule_date)->format('d-m-Y'):'N/A'}}<br>
                                    <i class="bx bx-time-five" style="color: blue;"></i>
                                    <span style="font-size: 0.875em; color: blue;"> {{ !empty($inspection->schedule_fromtime)  ? \Carbon\Carbon::parse($inspection->schedule_fromtime)->format('h:i A') : 'N/A' }}- {{ !empty($inspection->schedule_totime) ? \Carbon\Carbon::parse($inspection->schedule_totime)->format('h:i A') : 'N/A' }}</span></td>
            <td> @if($inspection->status == 0) <span class="badge bg-warning"> Pending </span>
                @elseif($inspection->status == 1)<span class="badge bg-success"> Completed </span>
                @elseif($inspection->status == 2)<span class="badge bg-secondary"> Cancelled </span>
                @else
                    <span class="badge bg-secondary"> N/A </span>
                @endif
                @if($inspection->reopen == 1 && $inspection->status == 0) <span class="badge bg-danger"> Reopen </span>
                @endif
                
            </td>
		          <td>
		    <strong>Remarks :</strong><br>
		   <span  data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $inspection->cremarks }}" >
             {{ \Illuminate\Support\Str::limit($inspection->cremarks, 20, '...') }}
          </span>

		    @if($inspection->reopen == 1)
		        <hr style="margin:5px 0;">
		        <strong> Reason:</strong><br>
		       <span   class="text-danger" data-bs-toggle="tooltip"  data-bs-placement="top" title="{{ $inspection->reason_re_can }}"  >
               {{ \Illuminate\Support\Str::limit($inspection->reason_re_can, 20, '...') }}
               </span>
		    @endif
		</td>
            <td>{{ \Carbon\Carbon::parse($inspection->created_at)->format('d-m-Y') }}</td>
              <td> <!--<a href=""><button class="btn btn-info btn-sm"> View </button> </a>-->
          @if(Auth::user()->sections()->first()->section_code === 'TECH')
             @if($inspection->schedule == 0)
              <a  href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'schedule'])}}" ><button class="btn btn-warning btn-sm"> Schedule Inspections </button> </a>
              @else 
                 @if(\Carbon\Carbon::parse($inspection->schedule_date)->format('Y-m-d') <= now()->format('Y-m-d'))
                  @if($inspection->inspectionReport)
                  @if($inspection->inspectionReport->mark_final != 1)
            <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'inspectionreportedit']) }}"> <button class="btn btn-success btn-sm">Edit Inspection Report</button></a> 
             <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'finalreport']) }}"> <button class="btn btn-success btn-sm"><i class="fas fa-file-pdf"></i>Draft Report</button> </a>
            @else           
    <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'finalreport']) }}"> <button class="btn btn-success btn-sm"><i class="fas fa-file-pdf"></i>Final Report</button> </a> 
            @endif 
                  @else
                   <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'inspectionreport']) }}"><button class="btn btn-success btn-sm">Inspection Report </button> </a>
                  @endif
       		
                @endif
			    <a  href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'innoting'])}}" ><button class="btn btn-info btn-sm"> <i class="fas fa-file-pdf"></i>  Noting </button> </a>
			     <a  href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'innotice'])}}" ><button class="btn btn-secondary btn-sm"> <i class="fas fa-file-pdf"></i>  Notice</button> </a>
			      @if(!$inspection->inspectionReport)
               <a  href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'reschedule'])}}" ><button class="btn btn-warning btn-sm"> Reschedule Inspections </button> </a>
                @endif
              @endif
             @else
             @if($inspection->status == 0 && $inspection->schedule == 0 )
            @can('create.inspection')         
            <a  href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'cancel'])}}"  onclick="confirmInspectionAction(event, this,'cancel')"><button class="btn btn-danger btn-sm"> Cancel Request </button> </a>
            <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'edit'])}}"><button class="btn btn-warning btn-sm"> Edit Request </button> </a> 
               @endcan           
            @endif
            @if($inspection->status == 1)
             @if($inspection->bnotice == 1)
            <!--  <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'breachnotingp'])}}" ><button class="btn btn-info btn-sm"> <i class="fas fa-file-pdf"></i>View Breach Noting </button> </a>-->
               <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'breachnoticep'])}}" ><button class="btn btn-success btn-sm"> <i class="fas fa-file-pdf"></i> View Breach Notice </button> </a>
             @endif
               @can('create.inspection') 
              <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'breachnotice'])}}" ><button class="btn btn-warning btn-sm"> Breach Notice </button> </a>
              @endcan
              @if($inspection->bnotice == 0)
               @can('create.inspection') 
            <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'reopen'])}}" onclick="confirmInspectionAction(event, this,'reopen')"><button class="btn btn-warning btn-sm"> Reopen Request </button> </a>
            @endcan
              @endif
            @endif
             @if(optional($inspection->inspectionReport)->mark_final == 1)
    <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'finalreport']) }}"> <button class="btn btn-info btn-sm"><i class="fas fa-file-pdf"></i> Report</button> </a>
               @endif
            <a href="{{ route('inspection.action',['id' => $inspection->id, 'type' => 'noting'])}}"><button class="btn btn-success btn-sm"> <i class="fas fa-file-pdf"></i> View Noting</button> </a> 
            
            @endif
            </td>           
        </tr>
        @empty
        <tr>
            <td colspan="12"  class="text-center">  No Data to Display </td> </tr>
        @endforelse
    </tbody>
</table>               
        </div>  
        </div>
        </div>      
    </div> 
@include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $("#btn-reset-filter").click(function(){     	
	     window.location.href = "{{ route('inspectionlist') }}";
	    })

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

    $(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
		function confirmInspectionAction(event, element, type)
				{
				    event.preventDefault();

				    let title = '';
				    let text = '';
				    let buttonText = '';

				    if (type == 'cancel') {
				        title = 'Are you sure?';
				        text = 'You want to cancel this inspection request.';
				        buttonText = 'Yes, Cancel It';
				    }

				    if (type == 'reopen') {
				        title = 'Reopen Request?';
				        text = 'You want to reopen this inspection request.';
				        buttonText = 'Yes, Reopen It';
				    }

				    Swal.fire({
				        title: title,
				        text: text,
				        icon: 'warning',
				        input: 'textarea',
				        inputLabel: 'Reason / Remarks',
				        inputPlaceholder: 'Enter reason here...',
				        inputAttributes: {
				            maxlength: 500
				        },
				        showCancelButton: true,
				        confirmButtonColor: '#d33',
				        cancelButtonColor: '#3085d6',
				        confirmButtonText: buttonText,

				        preConfirm: (reason) => {
				            if (!reason || reason.trim().length < 10) {
				                Swal.showValidationMessage(
				                    'Reason is required (minimum 10 characters)'
				                );
				                return false;
				            }
				            return reason;
				        }

				    }).then((result) => {

				        if (result.isConfirmed) {

				            let reason = encodeURIComponent(result.value);

				            window.location.href =
				                element.href + '&reason=' + reason;
				        }
				    });
				}
				
		 var table = $('.datatable-export').DataTable({
        responsive: false,
        searching: true,
        paging: false,
        search: false,
        info: false,
        dom: 'Bfrtip',

        buttons: [
				    {
				        extend: 'excelHtml5',
				        text: 'EXCEL',
				        footer: true,

				        exportOptions: {
				            columns: ':not(:last-child)',

				            format: {
				                body: function (data) {
				                    return data.replace(/₹|,/g, '').trim();
				                },
				                footer: function (data) {
				                    return data.replace(/₹|,/g, '').trim();
				                }
				            }
				        }
				    },
				    {
				        extend: 'csvHtml5',
				        text: 'CSV',
				        footer: true,

				        exportOptions: {
				            columns: ':not(:last-child)'
				        }
				    },
				    {
				        extend: 'pdfHtml5',
				        text: 'PDF',
				        pageSize: 'A4',
				        orientation: 'landscape',
				        footer: true,

				        exportOptions: {
				            columns: ':not(:last-child)'
				        }
				    }
				],

        columnDefs: [
            { orderable: false, targets: 5 },
            { orderable: true, targets: '_all' }
        ]
    });
	</script>
@endsection
	 
