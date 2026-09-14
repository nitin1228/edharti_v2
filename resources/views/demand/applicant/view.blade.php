@extends('layouts.app')

@section('title', 'Demand view')

@section('content')

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Demand</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item" aria-current="page">Demand</li>
                <li class="breadcrumb-item active" aria-current="page">View</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>
<hr>
<div class="card">
    <div class="card-body">
        <div class="row mt-2">
            <div class="col-lg-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Property</th>
                        <th>Unique Demand Id</th>
                        <th>Financial Year</th>
                        <th>Net Total</th>
                        <th>Balance</th>
                    </tr>
                    <tr>
                        <th>{{$demand->property_known_as}}</th>
                        <th>{{$demand->unique_id}}</th>
                        <th>{{$demand->current_fy}}</th>
                        <th>₹{{customNumFormat($demand->net_total)}}</th>
                        <th>₹{{customNumFormat($demand->balance_amount)}}</th>
                    </tr>
                </table>
                <br>

                @php
                    $headKeysConfig = config('demandHeadKeys');
                    $activeDemandDetails = $demand->demandDetails->where('subhead_code','<>','PNL_CHG')->whereNull('carried_amount');
                    $carriedDemandDetails = $demand->demandDetails->where('subhead_code','<>','PNL_CHG')->whereNotNull('carried_amount');
                    $carriedDemand = $demand->carriedForwardDemand?->oldDemand;
                    $penalties = $demand->demandDetails->where('subhead_code','PNL_CHG');
                    //$carriedDemandUniqueId = $carriedDemand->carriedForwardDemand;
                    @endphp

                    @if($carriedDemandDetails->count() > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="mt-2 mb-2">Details of carried amount from previous demand id: {{$carriedDemand->unique_id}}</h6>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#forwardDetailModal">
                                View Details
                            </button>
                        </div>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Head Name</th>
                                    <th>Carried Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Balance Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($carriedDemandDetails as $item)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$item->subhead_name}}</td>
                                    <td>&#8377; {{customNumFormat(round($item->net_total,2))}}</td>
                                    <td>&#8377; {{customNumFormat(round($item->paid_amount,2))}}</td>
                                    <td>&#8377; {{customNumFormat(round($item->balance_amount,2))}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="modal fade" id="forwardDetailModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="forwardDetailModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content text-center">
                                    <div class="modal-header">
                                        <h5>Details of demand id: {{$carriedDemand->unique_id}}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @php
                                        $cdds = $carriedDemand->demandDetails;
                                        @endphp
                                        @if($cdds->count() > 0)
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Num</th>
                                                    <th>Head</th>
                                                    <th>Total Amount</th>
                                                    <th>Paid Amount</th>
                                                    <th>Balance Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($cdds as $item)
                                                @php
                                                    $itemName = getServiceNameById($item->subhead_id);
                                                    if (getServiceCodeById($item->subhead_id) == 'DEM_MANUAL') {
                                                        $itemName .= ' ('.$item->subhead_keys['manual_title'].')';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$itemName}}</td>
                                                    <td>&#8377; {{customNumFormat(round($item->net_total,2))}}</td>
                                                    <td>&#8377; {{customNumFormat(round($item->paid_amount,2))}}</td>
                                                    <td>&#8377; {{customNumFormat(round($item->balance_amount,2))}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif
                                    </div>
                                    <div class="modal-footer justify-content-end">
                                        <button type="button" class="btn btn-secondary btn-width" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    @if($activeDemandDetails->count() > 0)
                        <table class="table table-bordered">
                            @foreach($activeDemandDetails as $detail)
                            @php
                            $outerLoop = $loop;
                            @endphp

                            @if($detail->subhead_code != "PREV_DUE")
                            @php
                            $headCode = $detail->subhead_code;
                            $headInputs = $headKeysConfig[$headCode] ?? [];
                            $headChunks = array_chunk($headInputs, 2);
                            $headKeys = $detail->subhead_keys ?? [];
                            @endphp
                            {{-- Main summary row --}}
                            <tr style="background-color: #006c6d; color:#fff">
                                <th colspan="2">{{ $detail->subhead_name }}</th>
                                <th colspan="3">&#8377;{{ customNumFormat($detail->balance_amount) }}</th>
                            </tr>

                            @foreach($headChunks as $i => $chunk)
                            <tr>
                                @if($i === 0)
                                {{-- Only in the first chunk row --}}
                                <th rowspan="{{ count($headChunks) }}">{{ $outerLoop->iteration }}</th>
                                @endif
                               
                                @foreach($chunk as $input)
                                @php
                                $value = $headKeys[$input['key']] ?? '';
                                $type = $input['type'] ?? 'text';

                                if ($type == 'number') {
                                $value = customNumFormat(round(($value !="" ? $value:0), 2));
                                } elseif ($type == 'date') {
                                $value = !is_null($value) ? date('d-m-Y', strtotime($value)) : '-';
                                } elseif (in_array($type, ['checkbox', 'radio'])) {
                                $value = $value == 1 ? 'Yes' : 'No';
                                }

                                if ($input['label'] == "Amount") {
                                $value = '₹ ' . $value;
                                }
                                @endphp
                                <th>{{ $input['label'] }}</th>
                                <td>{{ $value }}</td> 
                                @endforeach

                                {{-- If only one item in the chunk, span the rest --}}
                                @if(count($chunk) == 1)
                                <td colspan="2"></td>
                                @endif
                            </tr>
                            @endforeach
                            @else
                            <tr style="background-color: #006c6d; color:#fff">
                                <th>{{ $loop->iteration }}</th>
                                <th>{{ $detail->subhead_name }}</th>
                                <th colspan="3">&#8377;{{ customNumFormat($detail->balance_amount) }}</th>
                            </tr>
                            @endif
                            @if(!$loop->last)
                            <tr>
                                <td colspan="5" style="border-left: none; border-right: none;"></td>
                            </tr>
                            @endif
                            @endforeach
                        </table>
                    @endif
                    @if($penalties->count() > 0)
                        <h6 class="mt-2 mb-2">Penalty on demand</h6>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penalties as $item)
                                <td>{{$loop->iteration}}</td>
                                <td>{{$item->remarks}}</td>
                                <td>{{customNumFormat(round($item->net_total,2))}}</td>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-12">
                    <a href="{{route('applicant.payForDemand',$demand->id)}}">
                        <button type="button" class="btn btn-primary">Proceed to pay</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@include('include.alerts.ajax-alert')
@endsection


@section('footerScript')
@endsection
