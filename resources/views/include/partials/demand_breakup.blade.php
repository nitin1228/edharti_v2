<table class="table table-bordered">
	<tr>
	<th><h6>Demand Id : {{$demandUniqueId}} </h6>
	</th>
	<th><h6> Property Id : {{$newPropertyId}} <small>({{$oldPropertyId}})</small></h6> </th></tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Demand Sub Head</th>
            <th>Demand Amount</th>
            <th>Paid Amount</th>
            <th>Balance Amount</th>
        </tr>
    </thead>

    <tbody>
        @php
            $totalDemand = 0;
            $totalPaid = 0;
            $totalBalance = 0;
        @endphp

        @foreach($details as $key => $d)
            @php
                $totalDemand += $d->net_total;
                $totalPaid += $d->paid_amount;
                $totalBalance += $d->balance_amount;
            @endphp

            <tr>
                <td>{{ $key+1 }}</td>
                <td>{!! $d->subhead_name."<br>".($d->subhead_code == "DEM_MANUAL" ? '('. ($d->subhead_keys['manual_title'] ??'No description').')' : '') !!}</td>
                <td>₹ {{ number_format($d->net_total, 2) }}</td>
                <td>₹ {{ number_format($d->paid_amount, 2) }}</td>
                <td>₹ {{ number_format($d->balance_amount, 2) }}</td>
            </tr>
        @endforeach

        <tr style="font-weight: bold; background: #f8f8f8;">
            <td colspan="2" style="text-align:right;">Total:</td>
            <td>₹ {{ number_format($totalDemand, 2) }}</td>
            <td>₹ {{ number_format($totalPaid, 2) }}</td>
            <td>₹ {{ number_format($totalBalance, 2) }}</td>
        </tr>

    </tbody>
</table>
