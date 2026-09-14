<div class="table-responsive mt-2 mb-3">
@if(($data['applications'] ?? collect())->isNotEmpty())
    @foreach($data['applications'] as $app)

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <b>Application No:</b> {{ $app->application_no }}
                &nbsp; | &nbsp;
                <b>Application Type:</b> {{ getServiceNameById($app->service_type) ?? '-' }}
                  &nbsp; | &nbsp;
                <b>Application Submitted: </b> {{ !empty($app->created_at)  ? \Carbon\Carbon::parse($app->created_at)->format('d-m-Y')  : 'N/A' }}
                  &nbsp; | &nbsp;
                 <b>Application Disposed : </b> {{ !empty($app->disposed_at)  ? \Carbon\Carbon::parse($app->disposed_at)->format('d-m-Y')  : 'N/A' }}
            </div>

            <div class="card-body p-0">
                @if(!empty($app->movements) && $app->movements->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Application Status</th>                                   
                                    <th>Assigned By</th>
                                    <th>Assigned To</th>
                                    <th>Action</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($app->movements as $move)
                                    <tr>
                                        <td>{{ getServiceNameById($move->status ?? '') ?? '-' }}</td>
                                        
                                        <td>{{getUserRoleName($move->assigned_by) ?? '-' }}<br>{{ getUserNameById($move->assigned_by) ?? '-' }}</td>
                                        <td>{{ getUserRoleName($move->assigned_to) ?? '-' }}<br>{{ getUserNameById($move->assigned_to) ?? '-' }}</td>
                                        <td>{{ $move->action ?? '-' }}</td>
                                        <td>{{ $move->remarks ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="p-3 text-muted">No Movement Found</div>
                @endif

            </div>
        </div>

    @endforeach

@else
    <div class="alert alert-secondary">No Applications Found</div>
@endif
 @foreach($data['demands'] as $demand)

        <div class="card mb-4 shadow-sm">          
            <div class="card-body">    
                 
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-secondary">
                            <tr>
                                <th>Demand Unique ID</th>
                                <th>Total</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $demand->unique_id ?? "N/A" }}</td>
                                <td>{{ $demand->total  ?? "0"}}</td>
                                <td>{{ $demand->paid_amount ?? "0"}}</td>
                                <td>{{ $demand->balance_amount ?? "0" }}</td>
                                <td>                                  
                                        {{ ucfirst(getServiceNameById($demand->status)) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>              
                @if($demand->payments->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Unique Payment ID</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($demand->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ getServiceNameById($payment->type) ?? "N/A" }}</td>
                                        <td>{{ $payment->amount ?? "N/A" }}</td>
                                        <td>{{ $payment->unique_payment_id ?? "N/A"}}</td>
                                        <td>{{ $payment->transaction_id ?? "N/A"}}</td>
                                        <td> {{ ucfirst(getServiceNameById($payment->status)) }}
                                        
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="alert alert-warning">
                        No payments found for this demand.
                    </div>
                @endif

            </div>
        </div>

    @endforeach
<table class="table table-bordered table-striped">
                            <thead>
                                <tr><th colspan="6">Master Property details</th>
                            </tr></thead>
                       <tbody id="detail-container">
						<tr>
						    <td> <b>Property ID :</b> {{ $data['master']['unique_propert_id'] ?? '' }} ({{ $data['master']['old_propert_id'] ?? 'N/A' }}) </td>
						    <td><b>Splited Property:</b>  {{$data['master']['is_joint_property'] == 1 ? "Yes":"No"}}</td>
						     <td><b>Section Code :</b> {{$data['master']['section_code'] ?? 'N/A'}}</td>
						       <td><b>Scanned Files:</b> {{$data['master']['scanned_files_count'] ?? 'N/A'}} </td>
						     </tr>
						     <tr>
						      <td><b>Land Type:</b>  {{ getServiceNameById($data['master']['land_type']) ?? "N/A"}}<br>
						      	<b>Property Status :</b> {{ getServiceNameById($data['master']['status']) ?? 'N/A' }}<br>
						      	<b>Property Type :</b> {{ getServiceNameById($data['master']['property_type']) ?? 'N/A' }}<br>
						      	<b>Property Sub Type :</b> {{ getServiceNameById($data['master']['property_sub_type']) ?? 'N/A' }}
						      </td>	
						      <td><b>Plot No.:</b>  {{$data['master']['plot_or_property_no'] ?? "N/A"}}<br>
						     	<b>Block :</b> {{ $data['master']['block_no'] ?? 'N/A' }} <br>
						     		<b>Known as:</b>  {{$data['master']['property_known_as'] ?? "N/A"}}
						     	</td>
						     <td><b>Old Colony Name :</b> {{ $data['master']['name'] ?? 'N/A' }}<br>
						     <b>New Colony Name :</b> {{ $data['master']['new_name'] ?? 'N/A' }}<br>
						     	<b>File No. :</b> {{$data['master']['file_no'] ?? 'N/A'}}<br> 
						      	   <b>File No. :</b> {{$data['master']['unique_file_no'] ?? 'N/A'}}</td>						    
						     <td><b>Remark:</b> {{$data['master']['additional_remark'] ?? 'N/A'}} </td>					    
						     
						    </tr>
						     <tr>						    
						      <td>
							    <b>Lessee/Owner Name</b><br>

							   @if(!empty($data['master']['lessees_name']))
    @foreach(array_chunk(explode(',', $data['master']['lessees_name']), 2) as $pair)
        {{ implode(', ', array_map('trim', $pair)) }}<br>
    @endforeach
@else
    <span>-</span>
@endif
							</td>
						      <td><b>Area:</b> {{$data['master']['area']}} {{getServiceNameById($data['master']['unit'])}}<br><b>Area:</b> {{number_format($data['master']['area_in_sqm'],2)}} Sq. Mtr.</td>
						      <td><b>Property Contact Details:</b><br>
						     <b>Address:</b> {{$data['master']['address'] ?? "N/A"}}<br>
						     <b>Phone:</b> {{$data['master']['phone_no'] ?? "N/A"}}<br>
						      <b>Email:</b> {{$data['master']['email'] ?? "N/A"}}<br>
						      </td>
						      <td><b>Lease Type :</b> {{getServiceNameById($data['master']->propertyLeaseDetail->type_of_lease) ?? 'N/A'}}<br>
						      	<b>Date of Allotment:</b> {{ !empty($data['master']->propertyLeaseDetail->doa)  ? \Carbon\Carbon::parse($data['master']->propertyLeaseDetail->doa)->format('d-m-Y')  : 'N/A' }}<br>
						      	<b>Date of Execution :</b> {{ !empty($data['master']->propertyLeaseDetail->doe)  ? \Carbon\Carbon::parse($data['master']->propertyLeaseDetail->doe)->format('d-m-Y')  : 'N/A' }}<br>
						      	<b>Date of Expiration :</b> {{ !empty($data['master']->propertyLeaseDetail->date_of_expiration)  ? \Carbon\Carbon::parse($data['master']->propertyLeaseDetail->date_of_expiration)->format('d-m-Y')  : 'N/A' }}
						      </td>
						    </tr>
						</tbody>
                        </table>
                       <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Lessee Name</th>
            <th>Process of Transfer</th>
            <th>Transfer Date</th>
        </tr>
    </thead>
    <tbody>

  @forelse($data['master']->transfer ?? [] as $index => $transfer)
 	@if(is_null($transfer->splited_property_detail_id))  
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $transfer->lessee_name ?? 'N/A' }}</td>
            <td>{{ $transfer->process_of_transfer ?? 'N/A'}}</td>
          <td>{{ !empty($transfer->transferDate)  ? \Carbon\Carbon::parse($transfer->transferDate)->format('d-m-Y')  : 'N/A' }}</td>
        </tr>
        @endif
    @empty
        <tr>
            <td colspan="4" class="text-center">No records found</td>
        </tr>
    @endforelse

    </tbody>
</table>

@if(!empty($data['children']) && $data['children']->isNotEmpty())
    <div class="row">
        @foreach($data['children'] as $child)
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Child Property: {{ $child->child_prop_id }} ({{$child->old_property_id}})</strong>
                    </div>

                    <div class="card-body p-2">

                        <table class="table table-bordered table-sm">
                            <tr>
                                <th>Plot / Flat No</th>
                                <td>{{ $child->plot_flat_no ?? 'N/A' }}</td>
                            </tr>                           
                            <tr>
                                <th>Area</th>
                                <td>{{ $child->current_area ?? 'N/A' }}  {{getServiceNameById($child->unit)}}<br> {{ $child->area_in_sqm ?? 'N/A' }} Sq. Mtr.</td>
                            </tr>
                            <tr>
                                <th>Property Status</th>
                                <td> {{getServiceNameById($child->property_status)}}</td>
                            </tr>
                            <tr>
                                <th>Presently Known As</th>
                                <td>{{ $child->presently_known_as ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                            	<th>
							    Lessee/Owner Name</th>
								<td>
							   @php
    $lesseeName = $child->currentLesseeName?->lessees_name;
@endphp

@if($lesseeName)
    @foreach(array_chunk(explode(',', $lesseeName), 2) as $pair)
        {{ implode(', ', array_map('trim', $pair)) }}<br>
    @endforeach
@else
    <span>-</span>
@endif
							</td>							
							</tr>
							  <tr>
                           <th>Scanned Files </th>
							<td>{{$child->scanned_files_count ?? 0}}</td>
							</tr>
                             <tr>
                                <th>Property Contact Details</th>
                                  <td>
                              <b>Address:</b> {{$child->propertyContactDetail->address ?? "N/A"}}<br>
						     <b>Phone:</b> {{$child->propertyContactDetail->phone_no ?? "N/A"}}<br>
						      <b>Email:</b> {{$child->propertyContactDetail->email ?? "N/A"}}<br></td>
						      </tr>
                        </table>
						 <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Lessee Name</th>
                <th>Process</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>

        @forelse($child->propertyTransferredLesseeDetails as $index => $transfer)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transfer->lessee_name ?? 'N/A' }}</td>
                <td>{{ $transfer->process_of_transfer ?? 'N/A' }}</td>
                <td>
                    {{ $transfer->transferDate 
                        ? \Carbon\Carbon::parse($transfer->transferDate)->format('d-m-Y') 
                        : 'N/A' }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No records found</td>
            </tr>
        @endforelse

        </tbody>
    </table>
						
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No child properties found.</p>
@endif

@if(isset($data['has_flats']) && $data['has_flats'])
<table class="table table-bordered table-striped">
    <thead>
 	<tr>
 	<th>Property Id</th>
 	<th>Flat Id</th>
 	<th>File No.</th>
 	<th>Known As</th>
 	<th>Property Status</th>
 	<th>Area</th>
 	<th>Purchase Date</th>
 	<th>Original Buyer Name</th>
 	<th>Present Occupant Name</th> 	
 	</tr>
 	</thead>
 	<tbody>
    @foreach($data['flats'] as $flat)
        <tr>
        <td>{{$flat->unique_property_id}}<br>({{$flat->old_property_id}})</td>              
        <td>{{ $flat->unique_flat_id }}</td>
        <td>{{ $flat->unique_file_no }}</td>
        <td>{{$flat->known_as}}</td>
        <td>{{getServiceNameById($flat->property_flat_status)}}</td>
        <td>{{ $flat->area }} {{getServiceNameById($flat->unit)}}<br>{{number_format($flat->area_in_sqm,2)}} Sq. Mtr.</td>
        <td>{{ $flat->purchase_date ? \Carbon\Carbon::parse($flat->purchase_date)->format('d-m-Y') : 'N/A' }}</td>
        <td>{{trim($flat->original_buyer_name) ?: 'N/A' }}</td>
        <td>{{ trim($flat->present_occupant_name) ?: 'N/A' }}</td>      
        </tr>
    @endforeach
    </tbody>
@else
    <tr>
        <td colspan="3">No Flats Available</td>
    </tr>
@endif
</table>

</div>

