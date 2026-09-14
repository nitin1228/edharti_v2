<div class="row">
    <div class="col-lg-12">
      <div class="part-title">
        <h5>Old Demand Details</h5>
      </div>
      <div class="part-details">
        <div class="container-fluid">
            @forelse ($oldDemands as $demKey=>$oldDemand)
            <input type="hidden" name="oldDemandId[{{$demKey}}]" value="{{$oldDemand->demand_id}}">
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Demand Id</th>
                                <td>{{$oldDemand->demand_id}}</td>
                                <th>Demand Amount</th>
                                <td>₹ {{customNumFormat($oldDemand->amount)}}</td>
                                <th>Paid Amount</th>
                                <td>₹ {{customNumFormat($oldDemand->paid_amount)}}</td>
                                <th>Outstanding Amount</th>
                                <td>₹ {{customNumFormat($oldDemand->outstanding)}}</td>
                                <th><buton class="btn btn-primary" id="full-demand-details" onclick="viewFullDemandDetails('{{$oldDemand->demand_id}}')">View Details</button></th>
                            </tr>
                        </table>
                        <br>
                        <h5>Breakup of Previous Demand</h5>
                        <table class="table table-bordered table-striped">
                           
                            <tr>
                                <th width="180" class="hideSection">Include in New Demand</th>
                                <th>Details</th>
                                <th>Demand Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding Amount</th>
                            </tr>
                            @forelse ($oldDemand->subheadwiseBreakup as $key=>$item)
                                    @php
                                        $outstandingAmount = $item['demand_amount'] - $item['paid_amount'];
                                        $shouldCheck = $item['checked'] || $outstandingAmount < 0;
                                    @endphp
                                
                            <input type="hidden" name="oldDemandSubheadkey[{{$demKey}}][{{$key}}]" value="{{$outstandingAmount}}">
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" name="check[{{$demKey}}][{{$key}}]" class="check-include-in-demand form-check-input" data-demand-id="{{$oldDemand->demand_id}}" data-subhead-key="{{$key}}" data-subhead-amount='{{$outstandingAmount}}' {{(isset($openInReadOnlyMode) ||$outstandingAmount < 0 )? 'data-readonly':''}} {{ $shouldCheck ? 'checked' : '' }} onchange="calculateTotalAmount()">
                                        </div>
                                    </td>
                                    <td>{{$key}}</td>
                                    <td>₹ {{customNumFormat($item['demand_amount'])}}</td>
                                    <td>₹ {{customNumFormat($item['paid_amount'])}}</td>
                                    <td>₹ {{customNumFormat($outstandingAmount)}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nothing to display here.</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            @empty
                <div class="row">
                    <div class="col-lg-12">
                        <h5>No old demand found.</h5>
                    </div>
                </div>
            @endforelse
        </div>
      </div>
    </div>
</div>

@include('include.parts.old-demand-details-modal')

<script>
    function viewFullDemandDetails( demandId){
        let baseUrl = '{{ route("oldDemandBreakUp", ["oldDemandId" => "__ID__"]) }}';
        let url = baseUrl.replace('__ID__', demandId);
        $('#oldDemandSubheadsModal .modal-body').load(url);
        $('#oldDemandSubheadsModal').modal("show");
        $('#oldDemandSubheadsModal #demandId').html(demandId);
    }
</script>