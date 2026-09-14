@extends('layouts.app')

@section('title', 'Revenue  Deatils')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Payment</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>

               <!-- <li class="breadcrumb-item">Demand</li>-->
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <li class="breadcrumb-item active" aria-current="page">All Payment</li>
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
                    <h5 class="card-title">{{!empty($subheadname)? "Total Amount for ".$subheadname :"All Payments"}}</h5>
                    <div class="table-responsive mt-2">
                    @if($query_type == "processingfee")
                          <table class="table table-bordered" id="tab-all-applications">
					        <thead>
					            <tr class="table-success">
					                <th>S. No.</th>
					                 <th>Property Id</th>
					                <th>Application No.</th>
					                <th>Application type</th> 
					                <th>Transaction No.</th>                
					                <th>Amount</th>             
					                <th>Payment Mode</th>              
					                 <th>Payment Date</th>
					                <th>Status</th>
					                
					            </tr>
					        </thead>
       						<tbody>
                                @forelse ($applications as $app)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$app->unique_propert_id ?? 'N/A'}} <br><small>({{$app->old_propert_id ?? 'N/A'}}) </small></td>
                                    <td>{{$app->application_no ??'N/A'}}</td>
                                    <td>{{getServiceNameById($app->service_type) ?? 'N/A'}}</td>
                                   	<td> {{$app->transaction_id ?? 'N/A'}}</td>
                                    <td>  &#8377; {{customNumFormat($app->amount)}}</td>
                                    <td> {{getServiceNameById($app->payment_mode) ?? 'N/A'}}</td>
                                    <td>{{date('d-m-Y',strtotime($app->created_at))}}</td>
                                    <td>{{getServiceNameById($app->status)}}</td>                                                                     
                                    <!-- <td></td> -->
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" align="center">
                                        <h5>No Data to Display</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
							    <tr class="table-secondary">
							        <th colspan="5" class="text-end">Total:</th>
							        <th class="text-wrap" style="max-width: 150px; white-space: normal;">
							            ₹ {{ customNumFormat(round(collect($applications)->sum('amount'), 2)) }}<br>           
							           {{-- {{ collect($applications)->sum('amount') > 0 
							                ? convertToIndianCurrencyWords(round(collect($applications)->sum('amount'), 2)) 
							                : 'Zero Rupees Only' 
							            }} --}}
							        </th> 
							        <th colspan="3"></th>
							    </tr>
							</tfoot>
							    </table>
				    @else 
						<table class="table table-bordered mb-5" id="tab-all-applications">
				        <thead>
				            <tr class="table-success">
				                <th>S. No.</th>
				                <th>Demand ID</th>
				                <th>Demand Date</th>           
				                <th>Property ID</th>
				                <th>Section</th>
				                <th>Known As</th>
				                <th>Financial Year</th>
				                <th>Total Amount</th>
				                <th>Paid Amount</th>
				                <th>Outstanding Amount</th>
				                <th>Status</th>
				            </tr>
				        </thead>
				        <tbody>
				            @forelse($applications as $demand)
				                <tr>
				                    <td>{{ $loop->iteration }}</td>
				                    <td>                    
				                    <a href="{{route('ViewDemand',$demand->id)}}"  data-toggle="tooltip" 
									   data-placement="top" 
									   title="View Details">{{$demand->unique_id}}</a>
				                     <a href="{{ route('demand.demand_letter_pdf', $demand->id) }}" 
									   target="_blank"
									   data-toggle="tooltip" 
									   data-placement="top" 
									   title="Download Demand Letter">
									    <i class="lni lni-cloud-download text-danger" 
									       style="font-size: 25px; vertical-align: middle;">
									    </i>
									</a>
				                    </td>
				                    <td>{{ date('d-m-Y',strtotime($demand->created_at)) }}</td>
				                    <td>{{ $demand->unique_propert_id }}<br><small>({{ $demand->old_property_id }})</small></td>
				                    <td>{{ $demand->section_code }}</td>
				                    <td>{{ $demand->property_known_as }}</td>
				                    <td>{{ $demand->current_fy }}</td>
				                    <td>₹ {{ customNumFormat(round($demand->net_total, 2)) }}</td>                    
				                    <td>₹ {{ customNumFormat(round($demand->paid_amount, 2)) ?? 0 }}</td>	
				                      <td>₹ {{ customNumFormat(round($demand->balance_amount, 2)) ?? 0 }}</td>				                   
				                    <td>{{ $demand->balance_amount == 0 ?"Paid" : getServiceNameById($demand->status) }}</td>                   
				                </tr>
				            @empty
				                <tr>
				                    <td colspan="12" align="center">Sorry, No records found.</td>
				                </tr>
				            @endforelse
				        </tbody>      

				<tfoot>
				    <tr class="table-secondary">
				        <th colspan="7" class="text-end">Total:</th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				            ₹ {{ customNumFormat(round(collect($applications)->sum('net_total'), 2)) }}<br>           
				          
				        </th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				            ₹ {{ customNumFormat(round(collect($applications)->sum('paid_amount'), 2)) }}<br>
				          
				        </th>
				       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				         
				        </th>
				        <th colspan="2"></th>
				    </tr>
				</tfoot>
				    </table>
				    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScript')
	<script>
			$(document).ready(function () {
			    var table = $('#tab-all-applications').DataTable({
			        responsive: false,
			        searching: true,
			        paging: false,
			        search : false,
			        info: false,
			        dom: 'Bfrtip',
			                 buttons: [
			    {
			        extend: 'excelHtml5',
			         text: 'EXCEL',
			        exportOptions: {
			            columns: ':not(:last-child)'  ,
			            footer: true         
			        }
			    },
			    {
			        extend: 'csvHtml5',
			         text: 'CSV',
			        exportOptions: {
			           columns: ':not(:last-child)',
			           footer: true
			                    }
			    },
			    {
			        extend: 'pdfHtml5',
			         text: 'PDF',
			        orientation: 'landscape',
			        pageSize: 'A4',
			        exportOptions: {
			           columns: ':not(:last-child)',
			           footer: true           
			        },

			    }
			],
			        columnDefs: [
			            { orderable: false, targets: 5 }, 
			    		{ orderable: true, targets: '_all'} 
			        ]
			    });
				});
	</script>
@endsection