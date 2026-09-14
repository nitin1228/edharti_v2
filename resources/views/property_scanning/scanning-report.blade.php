@extends('layouts.app')
@section('title', 'Scanned Property Files')

@section('content')

@php
    // Card config (label + css + icon)
    $cards = [
        'PS1'  => ['title' => 'Property Section 1 (PS1)', 'class' => 'bg-primary'],
        'PS2'  => ['title' => 'Property Section 2 (PS2)', 'class' => 'bg-reddis'],
        'PS3'  => ['title' => 'Property Section 3 (PS3)', 'class' => 'bg-light-green'],
        'LS1'  => ['title' => 'Lease Section 1 (LS1)',    'class' => 'bg-secondary'],
        'LS2A' => ['title' => 'Lease Section 2A (LS2A)',  'class' => 'bg-yellow'],
        'LS2B' => ['title' => 'Lease Section 2B (LS2B)',  'class' => 'bg-dark-orange'],
        'LS3'  => ['title' => 'Lease Section 3 (LS3)',    'class' => 'bg-deer'],
        'LS5'  => ['title' => 'Lease Section 5 (LS5)',    'class' => 'bg-assigned'],
        'LS4'  => ['title' => 'Lease Section 4 (LS4)',    'class' => 'bg-lightishblue'],
        'RPC'  => ['title' => 'R P Cell',    'class' => 'bg-assigned'],
    ];
@endphp


<style>
    div.dt-buttons {
        float: none !important;
        /* width: 19%; */
        width: 33%;
        /* chagned by anil on 28-08-2025 to fix in resposive */
    }

    div.dt-buttons.btn-group {
        margin-bottom: 20px;
    }

    div.dt-buttons.btn-group .btn {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        div.dt-buttons {
            width:100%;
        }
        
        div.dt-buttons.btn-group {
            flex-direction: column;
            align-items: flex-start;
        }

        div.dt-buttons.btn-group .btn {
            width: 100%;
            text-align: left;
        }
    }
</style>

<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Property Scanning</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Scanning Report</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form class="row mb-3">                        
                    <div class="col-md-8">
                        <div class="row">                        
                            <div class="col-md-3">
                                <label for="startDate" class="form-label">Start Date:</label>
                                <input type="text" class="form-control datepicker" id="startDate" autocomplete="off" name="start_date" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="endDate" class="form-label">End Date:</label>
                                <input type="text" class="form-control datepicker" id="endDate" autocomplete="off" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex justify-content-start gap-4 col-lg-12">
                            <button type="submit" class="btn btn-primary" id="btn-apply-filter">Apply</button>
                             <button type="button" class="btn btn-warning" id="btn-reset-filter">Reset</button>
                        </div>
                    </div>
                </form>
        <div class="col-lg-12 order-lg-1 mb-4">
            <div class="widget-card">
                <div class="card-header rounded-0 text-center">
                    <h5 class="mt-3">
                        <a href="{{ route('scanning.index') }}">Total Scanned Files:
                            <span id="scan-totalCount">{{ number_format($totalCount ?? 0) }}</span>
                        </a>
                    </h5>
                </div>
                <!-- <div class="card-body"> -->
                    <div class="row">

                    @foreach($sectionCounts as $key => $count)
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="{{$cards[$key]['class']}} b-r-4 card-body">
                                      <a href="{{ route('scanning.index', ['section' => $key, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">{{$cards[$key]['title']}}</span>
                                                <h4 class="mb-0 counter"><span id="scan-ps1">{{ $count ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        {{--
                        @if(in_array('PS1', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-primary b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'PS1']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Property Section 1 (PS1)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ps1">{{ $sectionCounts['PS1'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('PS2', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-reddis b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'PS2']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon"><i class="fa-solid fa-house"></i></div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Property Section 2 (PS2)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ps2">{{ $sectionCounts['PS2'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('PS3', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-light-green b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'PS3']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Property Section 3 (PS3)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ps3">{{ $sectionCounts['PS3'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('LS1', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-secondary b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'LS1']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Lease Section 1 (LS1)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls1">{{ $sectionCounts['LS1'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('LS2A', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-yellow b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'LS2A']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Lease Section 2A (LS2A)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls2a">{{ $sectionCounts['LS2A'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('LS2B', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-dark-orange b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'LS2B']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Lease Section 2B (LS2B)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls2b">{{ $sectionCounts['LS2B'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('LS3', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-deer b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'LS3']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Lease Section 3 (LS3)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls3">{{ $sectionCounts['LS3'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('LS4', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                            <div class="card o-hidden border-0 h-100 w-100">
                                <div class="bg-lightishblue b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'LS4']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Lease Section 4 (LS4)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls5">{{ $sectionCounts['LS4'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('LS5', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-assigned b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'LS5']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Lease Section 5 (LS5)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls5">{{ $sectionCounts['LS5'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(in_array('RPC', $allowedCodes))
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-assigned b-r-4 card-body">
                                    <a href="{{ route('scanning.index', ['section' => 'RPC']) }}">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-house"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">RP Cell (RPC)</span>
                                                <h4 class="mb-0 counter"><span id="scan-ls5">{{ $sectionCounts['RPC'] ?? 0 }}</span></h4>
                                                <i class="fa-solid fa-copy"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        --}}
                    </div>

                <!-- </div> -->
            </div>
        </div>

    </div>
</div>

@endsection
@section('footerScript')
<script>
	 $(function() {
        var dateFormat = "dd-mm-yy";
        $("#startDate").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            // Set min date for endDate when startDate is selected
            $("#endDate").datepicker("option", "minDate", selectedDate);
        }
        });
        $("#endDate").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        changeYear: true,
        onClose: function (selectedDate) {
            // Set max date for startDate when endDate is selected
        $("#startDate").datepicker("option", "maxDate", selectedDate);
        }
        });
    });
    $('#btn-reset-filter').click(function () {
    window.location = "{{ route('scanning.report') }}";
});
</script>
@endsection
