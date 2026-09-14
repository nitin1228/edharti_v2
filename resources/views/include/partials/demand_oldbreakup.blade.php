@if($details->first())
<table class="table table-bordered">
	<tr>
	<th><h6>Demand Id : {{$details->first()->DemandID }} </h6>
	</th>
	<th><h6> Property Id : {{$newPropertyId}} <small>({{$oldPropertyId}})</small></h6> </th></tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Demand Id</th>             
             <th>Computer Code</th>
             <th>Demand Date</th>
             <th>Demand Subhead</th>
            <th>Demand Amount</th>
            <th>Offline Payment</th>
            <th>Online  Payment</th>
        </tr>
    </thead>
		@php
		    $totalDemand = $details->sum('demand_amount');
		    $totalOffline = $details->sum('paid_amount');
		    $totalOnline = $details->first()->total_payment ?? 0;
		@endphp
    <tbody>       
        @foreach($details as $key => $d)
            <tr>
                <td>{{ $key+1 }} </td>
                <td>{{$d->DemandID}}</td>
                 <td>{{$d->ComputerCode}}</td>
                <td> {{ !empty($d->demand_date)  ? \Carbon\Carbon::parse($d->demand_date)->format('d-m-Y') : 'N/A' }}</td>               
                <td>{{$d->Subhead}}</td>
			    <td>{{customNumFormat(round($d->demand_amount, 2))}}</td>
				<td>{{customNumFormat(round($d->paid_amount, 2))}}</td>
				<td>0</td>               
            </tr>
        @endforeach
        @if($details->count() > 0)
		<tr>
		    <td>{{ $details->count() + 1 }}</td>
		    <td>{{ $details->first()->DemandID }}</td>
		    <td>{{ $details->first()->ComputerCode }}</td>
		     <td> {{ !empty($d->demand_date)  ? \Carbon\Carbon::parse($d->demand_date)->format('d-m-Y') : 'N/A' }}</td>
		    <td>-</td>
		    <td>0</td>
		    <td>0</td>
		    <td>{{customNumFormat(round($details->first()->total_payment, 2))}}</td>
		</tr>
        @endif 
        <tr style="font-weight:bold; background:#f2f2f2;">
    <td colspan="5" class="text-end">Total </td>
   <td>₹ {{ customNumFormat(round($totalDemand,2)) }}</td>
<td>₹ {{ customNumFormat(round($totalOffline,2)) }}</td>
<td>₹ {{ customNumFormat(round($totalOnline,2)) }}</td>
</tr>
    </tbody>
</table>
@else 
<div class="alert alert-info text-center">
    No demand details were found in the records.
</div>
@endif


