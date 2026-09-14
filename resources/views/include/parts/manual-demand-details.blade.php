@if ($property && is_array($property))
    <div class="card shadow-sm">
        <div class="card-header bg-success">
            <h5 class="mb-0 text-white">
                <i class="fas fa-building me-2 text-white"></i>Property Details
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <!-- Property ID -->
                <div class="col-md-2">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">PROPERTY ID</label>
                        <span class="fw-semibold text-primary">{{ $property['old_property_id'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <!-- Unique Property ID -->
                <div class="col-md-2">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">UNIQUE ID</label>
                        <span class="fw-semibold text-primary">{{ $property['unique_property_id'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Locality -->
                <div class="col-md-2">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">LOCALITY</label>
                        <span class="fw-semibold">{{ $property['locality'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Block -->
                <div class="col-md-2">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">BLOCK</label>
                        <span class="fw-semibold">{{ $property['block'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Plot -->
                <div class="col-md-2">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">PLOT</label>
                        <span class="fw-semibold">{{ $property['plot'] ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Known As -->
                <div class="col-md-2">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">KNOWN AS</label>
                        <span class="fw-semibold">{{ $property['known_as'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@if ($demandDetails && is_array($demandDetails))
    <div class="card shadow-sm">
        <div class="card-header bg-success">
            <h5 class="mb-0 text-white">
                <i class="fas fa-building me-2 text-white"></i>Demand Details
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <!-- Demand ID -->
                <div class="col-md-3">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">DEMAND ID</label>
                        <span class="fw-semibold text-primary">{{ $demandDetails['unique_id'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <!-- Amount -->
                <div class="col-md-3">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">AMOUNT</label>
                        <span
                            class="fw-semibold text-primary">{{ number_format($demandDetails['total'] ?? 0, 2) }}</span>
                    </div>
                </div>

                <!-- Balance Amount -->
                <div class="col-md-3">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">BALANCE</label>
                        <span class="fw-semibold">{{ number_format($demandDetails['balance_amount'] ?? 0, 2) }}</span>
                    </div>
                </div>

                <!-- Financial Year -->
                <div class="col-md-3">
                    <div class="detail-item p-2">
                        <label class="fw-bold text-muted d-block mb-1" style="font-size: 0.8rem;">FINANCIAL YEAR</label>
                        <span class="fw-semibold">{{ $demandDetails['current_fy'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<form method="post" id="manualDemandPaymentForm">
    @csrf
    <div class="col-lg-12">
        <h5 class="mt-2 mb-2">Fill payment details</h5>
    </div>
    <input type="hidden" name="demand_id" value="{{ $demand->id }}">
    <input type="hidden" name="old_property_id" value="{{ $demand->old_property_id }}">
    <input type="hidden" name="is_joint_property" value="{{ $demand->splited_property_detail_id ? 1 : 0 }}">

    <!-- New Row: Transaction Date & Transaction Number -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-group">
                <label for="demandTransactionDate" class="form-label fw-bold">Transaction Date <span
                        class="text-danger">*</span></label>
                <input type="date" id="demandTransactionDate" name="transaction_date" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="demandTransactionNumber" class="form-label fw-bold">Transaction / Reference Number <span
                        class="text-danger">*</span></label>
                <input type="text" id="demandTransactionNumber" name="transaction_number" class="form-control"
                    placeholder="Enter Transaction Number" required>
                <div id="demandTransactionNumberError" style="display: none;" class="text-danger">Please enter
                    Transaction / Reference Number</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table class="table table-bordered table-striped mt-2">
                <tr>
                    <th>S.No</th>
                    <th>Particulars</th>
                    <th>Net Total</th>
                    <th>Paid Amount</th>
                    <th>Balance</th>
                    <th>Fill Payment Amount</th>
                </tr>
                @foreach ($demand->demandDetails as $i => $detail)
                    <input type="hidden" name="subhead_id[{{ $i }}]" value="{{ $detail->id }}">
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->subhead_name }}</td>
                        <td>₹{{ customNumFormat($detail->net_total) }}</td>
                        <td>₹{{ customNumFormat($detail->paid_amount ?? 0) }}</td>
                        @php
                            $balance = $detail->balance_amount;
                        @endphp
                        <td>₹{{ customNumFormat($balance) }}</td>
                        <td>
                            <input type="number" name="paid_amount[{{ $i }}]"
                                class="form-control amountToPay" min="0" max="{{ $balance }}"
                                data-max="{{ $balance }}" {{ $balance == 0 ? 'disabled' : '' }}>
                            <small class="text-muted">Max: ₹{{ customNumFormat($balance) }}</small>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5">Total amount to pay</th>
                    <th id="totalAmountToPay">₹ 0</th>
                </tr>
            </table>
            <!-- After the table, before the submit button -->
            <div id="paymentAmountError" class="alert alert-danger" style="display: none; margin-top: 10px;">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="paymentAmountErrorMessage">Please enter at least one payment amount</span>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-12 text-end">
            <button type="button" id="btnSubmitDemandPayment" class="btn btn-success">
                <i class="fas fa-save me-1"></i>Submit Payment
            </button>
        </div>
    </div>
</form>
