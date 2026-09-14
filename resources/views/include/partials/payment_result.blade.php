@if($data->count() > 0)
<div class="form-inner" style="max-width:100%; text-align:left">    
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr> <th>#</th>
                <th>Property Id</th>
                <th>Payment Type</th>
                <th>Payee Details</th>
                <th>Payment ID</th>
                <th>Transaction</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Mode</th>
                <th>Date/Time</th>
                <th>Receipt</th> 
            </tr>
            @foreach($data as $row)
            <tr><td>{{$row->id}} </td>
                <td>{{ $row->unique_propert_id }}<br>({{ $row->splited_old_property_id ?: $row->old_propert_id }}) </td>
                <td>{{getServiceNameById($row->type) =='Application'? 'Application Processing Fee' : getServiceNameById($row->type)}}</td>
                <td> {{ $row->first_name }}<br> {{ $row->mobile }}<br> {{ $row->email }} </td>
                <td>{{ $row->unique_payment_id ?? 'N/A' }}</td>
                <td>{{ $row->transaction_id ?? 'N/A' }}</td>
                <td>{{ $row->amount }}</td>
                <td>{{ $row->status == 1 ? 'Payment Initiated' : getServiceNameById($row->status) }}</td>
                 <td>{{ getServiceNameById($row->payment_mode) }}</td>
                <td> {{ !empty($row->updated_at)  ? \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i:s')  : 'N/A' }}</td>
                <td>  @if($row->status == "1546")<a href="{{route('downloadPaymentReceiptPdf',$row->unique_payment_id)}}">  
                                   <button class="btn btn-primary" type="button">Download Receipt</button></a>
                                   @else 
                                   N/A
                                   @endif
             </td>
            </tr>
            @endforeach
        </table>
    </div>

</div>
@else
	
<div class="form-inner" style="max-width:100%">
    <div class="alert alert-danger text-light">No Record Found</div>
</div>
@endif