@extends('layouts.app') {{-- Change layout if required --}}

@section('content')

<div class="container">

    <div class="card">

        <div class="card-header">
            <h4>Payment Status Search</h4>
        </div>

        <div class="card-body">

            @if(session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('payment.livestatus.result') }}" method="POST">

                @csrf

                <div class="mb-3">

                    <label>Search By</label>

                    <select class="form-control" id="search_by" name="search_by" required>

                        <option value="">Select</option>

                        <option value="old_property_id">
                            Property ID
                        </option>

                        <option value="unique_payment_id">
                            Payment ID
                        </option>

                        <option value="transaction_id">
                            Transaction Number
                        </option>

                        <option value="demand_id">
                            Demand ID
                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label id="searchLabel">
                        Enter Value
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="search_value"
                        required>

                </div>

                <button class="btn btn-primary">

                    Search

                </button>

            </form>

        </div>

    </div>



    @if(isset($payment) && $payment)

    <div class="card mt-4">

        <div class="card-header">
            <h4>Payment Details</h4>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>Property ID</th>
                    <td>{{ $payment->splited_old_property_id ? $payment->splited_old_property_id : $payment->master_old_property_id}}</td>
                </tr>

                <tr>
                    <th>Demand ID</th>
                    <td>{{ $payment->demand ? $payment->demand->unique_id : 'N/A' }}</td>
                </tr>

                <tr>
                    <th>Payment ID</th>
                    <td>{{ $payment->unique_payment_id }}</td>
                </tr>

                 <tr>
                    <th>Amount</th>
                    <td>{{ $payment->amount }}</td>
                <tr>

                <tr>
                    <th>Transaction Number</th>
                    <td>{{ $payment->transaction_id }}</td>
                <tr>
                    <th>Database Status</th>
                    <td>
                        @if($payment->status == 1546)
                            <div class="badge bg-success">{{ getServiceNameById($payment->status) }}</div>
                        @elseif($payment->status == 1545)
                            <div class="badge bg-danger">{{ getServiceNameById($payment->status) }}</div>
                        @else
                            <div class="badge bg-warning">{{ getServiceNameById($payment->status) }}</div>
                        @endif
                    </td>
                        
                </tr>

            </table>

        </div>

    </div>

    @endif




    @if(isset($liveStatus) && $liveStatus)
<div class="card mt-3">

    <div class="card-header">
        <h4>Live BharatKosh Status</h4>
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th>Order ID</th>
                <td>{{ $liveStatus['orderId'] }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    @if( $liveStatus['status'] == 'Success')
                        <div class="badge bg-success">{{ $liveStatus['status'] }}</div>
                    @elseif( $liveStatus['status'] == 'FAIL')
                        <div class="badge bg-danger">{{ $liveStatus['status'] }}</div>
                    @else
                        <div class="badge bg-warning">{{ $liveStatus['status'] }}</div>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Transaction ID</th>
                <td>{{ $liveStatus['transactionId'] }}</td>
            </tr>

            <tr>
                <th>Payment Method</th>
                <td>{{ $liveStatus['method'] }}</td>
            </tr>

            <tr>
                <th>Amount</th>
                <td>{{ $liveStatus['amount'] }}</td>
            </tr>
            <tr>
                <th>Transaction Date/ Time</th>
                <td>{{ $liveStatus['transactionDate'] }}</td>
            </tr>
            <tr>
                <th>Message</th>
                <td>{{ $liveStatus['message'] }}</td>
            </tr>

        </table>

    </div>

</div>

@endif

</div>

<script>

document.getElementById('search_by').addEventListener('change', function(){

    let labels = {

        old_property_id : 'Enter Property ID',

        unique_payment_id : 'Enter Payment ID',

        transaction_id : 'Enter Transaction Number',

        demand_id : 'Enter Demand ID'

    };

    document.getElementById('searchLabel').innerHTML = labels[this.value];

});

</script>

@endsection