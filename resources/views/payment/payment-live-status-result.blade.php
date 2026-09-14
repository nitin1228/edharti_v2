@extends('layouts.app')

@section('content')

<div class="container">

<div class="card">

<div class="card-header">

<h4>Payment Details</h4>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>
    <th>Property ID</th>
    <td>{{ $payment->old_property_id }}</td>
</tr>

<tr>
    <th>Demand ID</th>
    <td>{{ $payment->demand_id }}</td>
</tr>

<tr>
    <th>Payment ID</th>
    <td>{{ $payment->unique_payment_id }}</td>
</tr>

<tr>
    <th>Transaction Number</th>
    <td>{{ $payment->transaction_id }}</td>
</tr>

<tr>
    <th>Status</th>
    <td>{{ $payment->status }}</td>
</tr>

</table>

@if($liveStatus)

<hr>

<h5>Live BharatKosh Status</h5>

<table class="table table-bordered">

<tr>

<th>Order ID</th>

<td>{{ $liveStatus['orderId'] }}</td>

</tr>

<tr>

<th>Status</th>

<td>{{ $liveStatus['status'] }}</td>

</tr>

<tr>

<th>Transaction ID</th>

<td>{{ $liveStatus['transactionId'] }}</td>

</tr>

</table>

@endif

</div>

</div>

</div>

@endsection