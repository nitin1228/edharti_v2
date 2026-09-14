<div class="table-responsive mt-2 mb-3">
@php
function toRoman($number) {
    $map = [
        'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
    ];
    $returnValue = '';
    foreach ($map as $roman => $int) {
        while ($number >= $int) {
            $returnValue .= $roman;
            $number -= $int;
        }
    }
    return $returnValue;
}
@endphp

@if($filtertype == "sectionWise")
<h5 >Section wise Demand Summary </h5>
    <table class="table table-bordered mb-5">
        <thead>
            <tr class="table-success">
                <th>S. No. </th>
                <th>Demand ID</th>
                <th>Demand Date</th>           
                <th>Property ID</th>
               <th>File Number</th>
                <th>Known As</th>
                <th>Financial Year</th>
                <th>Demand Amount</th>
                <th>Paid Amount</th>
                <th>Outstanding Amount</th>
                <th>Status</th>
                <!--<th>Action</th>-->
            </tr>
        </thead>
        <tbody>
            @forelse($queryResult as $demand)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td> <a href="{{route('ViewDemand',$demand->id)}}">{{ $demand->unique_id }}</a> <a href="{{route('demand.demand_letter_pdf', $demand->id) }}" target="_blank"><i class="lni lni-cloud-download text-danger" style="font-size: 25px; vertical-align: middle;"></i></a></td>
                    <td>{{ date('d-m-Y',strtotime($demand->created_at)) }}</td>
                    <td>{{ $demand->unique_propert_id }}<br><small>({{ $demand->old_property_id }})</small></td>
                    <td>{{ $demand->section_code }}</td>
                    <td>{{ $demand->property_known_as }}</td>
                    <td>{{ $demand->current_fy }}</td>
                    <td>₹ {{ customNumFormat(round($demand->net_total, 2)) }}<br>
                    	  <a href="javascript:void(0);" 
       class="text-primary viewBreakup" 
       data-id="{{ $demand->id }}" 
       data-toggle="tooltip" 
       title="View Breakup">
        (View Breakup)
    </a>
                    </td>                    
                    <td>₹ {{ customNumFormat(round($demand->paid_amount, 2)) ?? 0 }}</td>
                    <td>₹ {{ customNumFormat(round($demand->balance_amount, 2)) ?? 0 }}</td>
                    <td>{{ getServiceNameById($demand->status) }}</td>
                    <!--<td>
                        
                        <a href="{{route('ViewDemand',$demand->id)}}" class="btn btn-sm btn-flat btn-primary">View</a>
                       
                    </td>-->
                </tr>
            @empty
                <tr>
                    <td colspan="12" align="center">Sorry, no records found.</td>
                </tr>
            @endforelse
        </tbody>      

<tfoot>
    <tr class="table-secondary">
        <th colspan="7" class="text-end">Total:</th>
        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ {{ customNumFormat(round(collect($queryResult)->sum('net_total'), 2)) }}<br>           
           <!-- {{ collect($queryResult)->sum('net_total') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('net_total'), 2)) 
                : 'Zero Rupees Only' 
            }}-->
        </th>
        <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ {{ customNumFormat(round(collect($queryResult)->sum('paid_amount'), 2)) }}<br>
          <!--  {{ collect($queryResult)->sum('paid_amount') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('paid_amount'), 2)) 
                : 'Zero Rupees Only' 
            }}-->
        </th>
       <th class="text-wrap" style="max-width: 200px; white-space: normal;">
            ₹ {{ customNumFormat(round(collect($queryResult)->sum('balance_amount'), 2)) }}<br>            
           <!-- {{ collect($queryResult)->sum('balance_amount') > 0 
                ? convertToIndianCurrencyWords(round(collect($queryResult)->sum('balance_amount'), 2)) 
                : 'Zero Rupees Only' 
            }}-->
        </th>
        <th colspan="2"></th>
    </tr>
</tfoot>

    </table>
   @elseif($filtertype == "dyLDoWise")
    <div class="mb-4">
        <h5 >Dy. L&DO Section wise Demand Summary 
            @if(isset($selectedDyUser)) 
               <small> (Mr. {{ $selectedDyUser->name }}) </small>
            @endif
        </h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>S. No.</th>
                    <th>Section Name</th>
                   <!-- <th>Section Code</th>-->
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                     <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dySectionWiseSummary as $index => $section)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $section['section_name'] }}</td>
                       <!-- <td>{{ $section['section_code'] }}</td>-->
                        <td>{{ $section['total_demands'] }}</td>
                        <td>₹ {{ customNumFormat(round($section['total_amount'], 2)) }}</td>
                        <td>₹ {{ customNumFormat(round($section['total_paid'], 2)) }}</td>
                        <td>₹ {{ customNumFormat(round($section['total_balance'], 2)) }}</td>
                        <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="{{ $section['section_code'] }}" data-type="{{$demandType}}" data-from="{{$filterDateFrom}}" data-to="{{$filterDateTo}}" target="_blank">View More</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" align="center">No section-wise data found for selected Dy. L&DO.</td>
                    </tr>
                @endforelse
            </tbody>
               <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th>{{ collect($dySectionWiseSummary)->sum('total_demands') }}<br>
    	<!--{{ ucfirst(convertNumberToWords(collect($dySectionWiseSummary)->sum('total_demands'))) }}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($dySectionWiseSummary)->sum('total_amount'), 2)) }}<br>
   <!-- {{ collect($dySectionWiseSummary)->sum('total_amount') > 0 ? convertToIndianCurrencyWords(round(collect($dySectionWiseSummary)->sum('total_amount'), 2)) : 'Zero Rupees Only'}}  -->  	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($dySectionWiseSummary)->sum('total_paid'), 2)) }}<br>
    <!--	{{ collect($dySectionWiseSummary)->sum('total_paid') > 0 ? convertToIndianCurrencyWords(round(collect($dySectionWiseSummary)->sum('total_paid'), 2)) : 'Zero Rupees Only'}}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($dySectionWiseSummary)->sum('total_balance'), 2)) }}<br>
   <!-- {{ collect($dySectionWiseSummary)->sum('total_balance') > 0 ? convertToIndianCurrencyWords(round(collect($dySectionWiseSummary)->sum('total_balance'), 2)) : 'Zero Rupees Only'}}   --> 	
    </th>
    <th></th>
</tr>
</tfoot>
        </table>
    </div>
    
@elseif($filtertype == "yearWise")
    <div class="mb-4">
        <h5 >Demand Summary</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                <td>S.No.</td>
                    <th>Designation</th>
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr> 
                	<td>1.</td>
                	<td>Land and Development Office</td>
                    <td>{{ $totalSummary->total_demands ?? 0 }}</td>
                    <td>₹ {{ customNumFormat(round($totalSummary->total_amount ?? 0, 2)) }}</td>
                    <td>₹ {{ customNumFormat(round($totalSummary->total_paid ?? 0, 2)) }}</td>
                    <td>₹ {{ customNumFormat(round($totalSummary->total_balance ?? 0, 2)) }}</td>
                    <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="{{ trim($totalSummary->section_codes) }}" data-type="{{$demandType}}" data-from="{{$filterDateFrom}}" data-to="{{$filterDateTo}}" target="_blank">View More</a></td>
                    
                </tr>
            </tbody>
             <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th>{{ $totalSummary->total_demands ?? 0 }}<br>
    	<!--{{ ucfirst(convertNumberToWords($totalSummary->total_demands ?? 0)) }}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round($totalSummary->total_amount ?? 0, 2)) }}<br>
  <!--  {{ round($totalSummary->total_amount ?? 0, 2) > 0 ? convertToIndianCurrencyWords(round($totalSummary->total_amount ?? 0, 2)) : 'Zero Rupees Only'}}-->
    	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round($totalSummary->total_paid ?? 0, 2)) }}<br>
    	<!--{{ round($totalSummary->total_paid ?? 0, 2) > 0 ? convertToIndianCurrencyWords(round($totalSummary->total_paid ?? 0, 2)) : 'Zero Rupees Only'}}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round($totalSummary->total_balance ?? 0, 2)) }}<br>
   <!-- {{ round($totalSummary->total_balance ?? 0, 2) > 0 ? convertToIndianCurrencyWords(round($totalSummary->total_balance ?? 0, 2)) : 'Zero Rupees Only'}}-->
    	
    </th>
    <th></th>
</tr>
</tfoot>

        </table>
    </div>
   
    <div class="mb-4">
        <h5 >Dy. L&DO wise Demand Summary</h5>
        <table class="table table-bordered mb-5">
            <thead >
                <tr class="table-success">
                    <th>S. No.</th>
                    <th>Designation</th>
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @php //print_r($dyLdoWiseSummary); @endphp
                @forelse($dyLdoWiseSummary as $index => $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{!! $item['designation'] !!} Officer {{-- {{ toRoman($loop->iteration) }} ---}}<br><small>( Mr. {{ ucfirst($item['user'])}} )</small> </td>
                        <td>{{ $item['total_demands'] }}</td>
                        <td>₹ {{ customNumFormat(round($item['total_amount'], 2)) }}</td>
                        <td>₹ {{ customNumFormat(round($item['total_paid'], 2)) }}</td>
                        <td>₹ {{ customNumFormat(round($item['total_balance'], 2)) }}</td>
                       <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="{{$item['dyassingsection']}}"  data-type="{{$demandType}}" data-from="{{$filterDateFrom}}" data-to="{{$filterDateTo}}" target="_blank">View More</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center">No data available.</td>
                    </tr>
                @endforelse
            </tbody>
    <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th>{{ collect($dyLdoWiseSummary)->sum('total_demands') }}<br>
    	<!--{{ ucfirst(convertNumberToWords(collect($dyLdoWiseSummary)->sum('total_demands'))) }}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($dyLdoWiseSummary)->sum('total_amount'), 2)) }}<br>
    <!-- {{ collect($dyLdoWiseSummary)->sum('total_amount') > 0 ? convertToIndianCurrencyWords(round(collect($dyLdoWiseSummary)->sum('total_amount'), 2)) : 'Zero Rupees Only'}} -->    	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($dyLdoWiseSummary)->sum('total_paid'), 2)) }}<br>
    	<!--{{ collect($dyLdoWiseSummary)->sum('total_paid') > 0 ? convertToIndianCurrencyWords(round(collect($dyLdoWiseSummary)->sum('total_paid'), 2)) : 'Zero Rupees Only'}}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($dyLdoWiseSummary)->sum('total_balance'), 2)) }}<br>
   <!-- {{ collect($dyLdoWiseSummary)->sum('total_balance') > 0 ? convertToIndianCurrencyWords(round(collect($dyLdoWiseSummary)->sum('total_balance'), 2)) : 'Zero Rupees Only'}} -->   	
    </th>
    <th></th>
</tr>
</tfoot>
        </table>
    </div>
    <div>
        <h5 >Section wise Demand Summary</h5>
        <table class="table table-bordered mb-5">
            <thead>
                <tr class="table-success">
                    <th>S. No.</th>
                    <th>Section Name</th>
                   <!-- <th>Section Code</th>-->
                    <th>Total Demands</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Outstanding Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @php
$sortedSections = collect($sectionWiseSummary)->sortBy('section_name');
@endphp
                @forelse($sortedSections as $index => $section)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $section['section_name'] }}</td>
                       <!-- <td>{{ $section['section_code'] }}</td>-->
                        <td>{{ $section['total_demands'] }}</td>
                        <td>₹ {{ customNumFormat(round($section['total_amount'], 2)) }}</td>
                        <td>₹ {{ customNumFormat(round($section['total_paid'], 2)) }}</td>
                        <td>₹ {{ customNumFormat(round($section['total_balance'], 2)) }}</td>
                        <td><a href="javascript:;" class="app-query-link btn btn-sm btn-flat btn-primary" data-service="{{$section['section_code']}}" data-type="{{$demandType}}" data-from="{{$filterDateFrom}}" data-to="{{$filterDateTo}}" >View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center">No section-wise data found.</td>
                    </tr>
                @endforelse
            </tbody>
              <tfoot>
<tr class="table-secondary">
    <th colspan="2" class="text-end">Total:</th>
    <th>{{ collect($sectionWiseSummary)->sum('total_demands') }}<br>
    	<!--{{ucfirst(convertNumberToWords(collect($sectionWiseSummary)->sum('total_demands')))}}-->
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($sectionWiseSummary)->sum('total_amount'), 2)) }}<br>
   <!-- {{ collect($sectionWiseSummary)->sum('total_amount') > 0 ? convertToIndianCurrencyWords(round(collect($sectionWiseSummary)->sum('total_amount'), 2)) : 'Zero Rupees Only'}}  -->  	
    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($sectionWiseSummary)->sum('total_paid'), 2)) }}<br>
    	<!--{{ collect($sectionWiseSummary)->sum('total_paid') > 0 ? convertToIndianCurrencyWords(round(collect($sectionWiseSummary)->sum('total_paid'), 2)) : 'Zero Rupees Only'}}-->

    </th>
    <th class="text-wrap" style="max-width: 200px; white-space: normal;">₹ {{ customNumFormat(round(collect($sectionWiseSummary)->sum('total_balance'), 2)) }}<br>
   <!-- {{ collect($sectionWiseSummary)->sum('total_balance') > 0 ? convertToIndianCurrencyWords(round(collect($sectionWiseSummary)->sum('total_balance'), 2)) : 'Zero Rupees Only'}}-->
    	
    </th>
    <th></th>
</tr>
</tfoot>
        </table>
    </div>
@endif
</div>

