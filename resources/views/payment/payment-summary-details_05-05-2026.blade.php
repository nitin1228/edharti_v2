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
            <h5 class="card-title">{{!empty($applications[0]['subhead_id'])? "Total Amount for ".getServiceNameById($applications[0]['subhead_id']) :"All Payments"}} </h5>
            <div class="table-responsive mt-2">
           
                    @if($query_type == "processingfee")
                          <table class="table table-bordered datatable-export" id="tab-all-applications">
					        <thead>
					            <tr class="table-success">
					                <th>S. No.</th>
					                 <th>Property Id</th>
					                <th>Application No.</th>
					                <th>Application type</th>
					                 <th>Payment Id</th>  
					                <th>Transaction No.</th>                
					                <th>Amount</th>             
					                <th>Payment Mode</th>              
					                 <th>Payment Date</th>
					                  <th>Payee Details</th>
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
                                    <td> {{$app->unique_payment_id ?? 'N/A'}}</td>
                                   	<td> {{$app->transaction_id ?? 'N/A'}}</td>
                                    <td>  &#8377; {{customNumFormat($app->amount)}}</td>
                                    <td> {{getServiceNameById($app->payment_mode) ?? 'N/A'}}</td>
                                    <td>{{date('d-m-Y',strtotime($app->updated_at))}}</td>
                                     <td>{{$app->first_name}}<br>
                                     	{{$app->email}}<br>
                                     	{{$app->mobile}}
                                     </td>
                                    <td>{{getServiceNameById($app->status)}}</td>                                                                     
                                    <!-- <td></td> -->
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" align="center">
                                        <h5>No Data to Display</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
							    <tr class="table-secondary">
							        <th colspan="6" class="text-end">Total:</th>
							        <th class="text-wrap" style="max-width: 150px; white-space: normal;">
							            ₹ {{ customNumFormat(round(collect($applications)->sum('amount'), 2)) }}<br>           
							           {{-- {{ collect($applications)->sum('amount') > 0 
							                ? convertToIndianCurrencyWords(round(collect($applications)->sum('amount'), 2)) 
							                : 'Zero Rupees Only' 
							            }} --}}
							        </th> 
							        <th colspan="4"></th>
							    </tr>
							</tfoot>
							    </table>
							    
						@elseif($query_type == "summaryall")						
							<table class="table table-bordered datatable-export" id="tab-summary-all">
						    <thead>
						        <tr class="table-success">
						            <th>S. No.</th>
						            <th>Type</th>
						            <th>Property ID</th>						            
						            <th>Reference No.</th>
						             <th>Section</th>
						             <th>Known as</th>
						            <th>Payment ID</th>
						            <th>Transaction No.</th>
						            <th>Amount</th>
						            <th>Payment Date</th>
						            <th>Payee Details</th>
						            <th>Status</th>
						        </tr>
						    </thead>

    <tbody>
        @php
            $i = 1;
        @endphp

        @forelse($applications as $row)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{getServiceNameById($row->type) == 'Application' ? "Application Processing Fee" : (getServiceNameById($row->type) =='Demand' ?  getServiceNameById($row->type) : getServiceNameById($row->type)) }} </td>
                <td> {{ $row->unique_propert_id ?? 'N/A' }}<br>
                    <small>({{ $row->old_propert_id ?? 'N/A' }})</small>
                </td>               
                <td> {{ $row->application_no  ?? $row->demand_unique_id  ?? 'N/A' }} </td>
                 <td>{{ $row->section_code}}</td>
                <td><div class="break-text">{{ $row->property_known_as }}</div></td>
                <td>{{ $row->unique_payment_id ?? 'N/A' }}</td>
                <td>{{ $row->transaction_id ?? 'N/A' }}</td>
                <td>               
                    ₹ {{ customNumFormat( $row->demand_detail_amount  != 0 ? $row->demand_detail_amount :
                        $row->amount 
                        ?? $row->paid_amount 
                        ?? 0
                    ) }}
                </td>
                <td>
                    {{ isset($row->updated_at) 
                        ? date('d-m-Y', strtotime($row->updated_at)) 
                        : 'N/A' }}
                </td>
                <td>
                    {{ $row->first_name ?? 'N/A' }}<br>
                    {{ $row->email ?? '' }}<br>
                    {{ $row->mobile ?? '' }}
                </td>
                <td>
                    @if(isset($row->balance_amount))
                        {{ $row->balance_amount == 0 
                            ? 'Success' 
                            : getServiceNameById($row->status) }}
                    @else
                        {{ getServiceNameById($row->status) }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center">
                    No Records Found
                </td>
            </tr>
        @endforelse
    </tbody>

   <tfoot>
    <tr class="table-secondary">
       <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
     <th></th>
        <th  class="text-end">Total Paid Amount:</th>
        <th>
            ₹ {{ customNumFormat(
                collect($applications)->sum(function ($r) {

                    // Case 1: demand exists → sum demand details ONLY
                    if (!empty($r->demand_id)) {
                        return (float) ($r->demand_detail_amount ?? 0);
                    }
                    // Case 2: no demand → sum payment amount
                    return (float) ($r->amount ?? $r->paid_amount ?? 0);
                })
            ) }}
        </th>
       <th></th>
          <th ></th>
            <th></th>
		
    </tr>
</tfoot>

</table>
				    @else 
						<table class="table table-bordered mb-5 datatable-export" id="tab-all-applications">
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
				                  <th>Payee Details</th>
				                   <th>Payment Id</th>  
					                <th>Transaction No.</th>  
				                <th>Status</th>
				            </tr>
				        </thead>
				        <tbody>				       
				            @forelse($applications as $demand)
				                <tr>
				                    <td>{{ $loop->iteration }}</td>
				                    <td>   
				                    @if( $demand->unique_id != "")                 
				                    <a href="{{route('ViewDemand',$demand->demaid)}}"  data-toggle="tooltip" 
									   data-placement="top" 
									   title="View Details">{{$demand->unique_id}}</a>
				                     <a href="{{ asset('storage/public/' . $demand->demand_path) }}" 
									   target="_blank"
									   data-toggle="tooltip" 
									   data-placement="top" 
									   title="Download Demand Letter">
									    <i class="lni lni-cloud-download text-danger" 
									       style="font-size: 25px; vertical-align: middle;">
									    </i>
									</a>
									@else 
									N/A
									@endif
									
				                    </td>
				                    <td>{{ date('d-m-Y',strtotime($demand->updated_at)) }}</td>
				                    <td>{{ $demand->unique_propert_id }}<br><small>({{ $demand->old_propert_id }})</small></td>
				                    <td>{{ $demand->section_code ?? "N/A" }}</td>
				                    <td><div class="break-text">{{ $demand->property_known_as }}</div></td>
				                    <td>{{ $demand->current_fy ?? "N/A" }}</td>
				                    <td>₹ {{ customNumFormat(round($demand->net_total, 2)) ?? 0 }}</td>                    
				                    <td>₹ {{ customNumFormat(round($demand->paid_amount, 2)) ?? 0 }}</td>	
				                    <td>₹ {{ customNumFormat(round($demand->balance_amount, 2)) ?? 0 }}</td>	
				                      <td>{{$demand->first_name ?? "N/A"}}<br>
                                     	{{$demand->email}}<br>
                                     	{{$demand->mobile}}
                                     </td> 	
                                     <td> {{$demand->unique_payment_id ?? 'N/A'}}</td>
                                   	<td> {{$demand->transaction_id ?? 'N/A'}}</td>		                   
				                    <td>{{ $demand->balance_amount == 0 ?"Success" : getServiceNameById($demand->status) }}</td>                   
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
				            ₹ {{ customNumFormat(round(collect($applications)->sum('net_total'), 2)) }}          
				          
				        </th>
				        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				            ₹ {{ customNumFormat(round(collect($applications)->sum('paid_amount'), 2)) }}
				          
				        </th>
				       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
				          ₹ {{ customNumFormat(round(collect($applications)->sum('balance_amount'), 2)) }}
				        </th>
				        <th colspan="4"></th>
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
<style>.break-text {
    max-width: 450px;   
    white-space: normal;
    word-break: normal;    
    overflow-wrap: break-word;
}
</style>
@endsection

@section('footerScript')
	<script>
$(document).ready(function () {

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

                footer: true, // ✅ IMPORTANT

                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data) {
                            return data.replace(/₹|,/g, '').trim(); // clean currency
                        },
                        footer: function (data) {
                            return data.replace(/₹|,/g, '').trim(); // clean footer
                        }
                    }
                }
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',

                footer: true,

                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                pageSize: 'A3',
                orientation: 'landscape',

                footer: true, // ✅ IMPORTANT

                exportOptions: {
                    columns: ':visible'
                },

                // 🔥 MAIN FIX FOR PDF FOOTER
//                customize: function (doc) {
//
//                    var footer = [];
//
//                    $('#tab-all-applications tfoot th').each(function () {
//                        footer.push({
//                            text: $(this).text(),
//                            style: 'tableFooter'
//                        });
//                    });
//
//                    // add footer row in PDF table
//                    doc.content[1].table.body.push(footer);
//                }
            }
        ],

        columnDefs: [
            { orderable: false, targets: 5 },
            { orderable: true, targets: '_all' }
        ]
    });

});

    // $('.datatable-export').each(function () {

    //     $(this).DataTable({
    //         responsive: false,
    //         searching: true,
    //         paging: false,
    //         info: false,
    //         dom: 'Bfrtip',

    //         buttons: [
    //             {
    //                 extend: 'excelHtml5',
    //                 text: 'EXCEL',
    //                 exportOptions: {
    //                     columns: ':visible',
    //                     footer: true
    //                 }
    //             },
    //             {
    //                 extend: 'csvHtml5',
    //                 text: 'CSV',
    //                 exportOptions: {
    //                     columns: ':visible',
    //                     footer: true
    //                 }
    //             },
    //             {
    //                 extend: 'pdfHtml5',
    //                 text: 'PDF',
    //                 orientation: 'landscape',
    //                 pageSize: 'A4',
    //                 exportOptions: {
    //                     columns: ':visible',
    //                     footer: true
    //                 }
    //             }
    //         ],

    //         columnDefs: [
    //             { orderable: true, targets: '_all' }
    //         ]
    //     });
	// 	});
	// 			});
	</script>
@endsection