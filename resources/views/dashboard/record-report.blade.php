@extends('layouts.app')
@section('title', 'Scanned Property Files')

@section('content')

@php
    // Get sections user is allowed to see
    [$limitToAssigned, $userSectionIds] = getUserAssignedSections();
    $allowedSections = getRequiredSections(); // returns a Collection<Section>

    if ($limitToAssigned) {
        $allowedSections = $allowedSections->whereIn('id', $userSectionIds);
    }

    // Normalize to clean, comparable codes like "PS2"
    $allowedCodes = $allowedSections->pluck('section_code')
        ->map(fn($c) => strtoupper(trim($c)))
        ->values()
        ->all();
@endphp

@php
    $section_counts = $section_counts ?? [];
    $total_records  = $total_records ?? 0;

    // Card config (label + css + icon)
    $cards = [
        'PS1'  => ['title' => 'Property Section 1 (PS1)', 'class' => 'bg-primary',     'icon' => 'fa-solid fa-house'],
        'PS2'  => ['title' => 'Property Section 2 (PS2)', 'class' => 'bg-reddis',      'icon' => 'fa-solid fa-house'],
        'PS3'  => ['title' => 'Property Section 3 (PS3)', 'class' => 'bg-light-green', 'icon' => 'fa-solid fa-house'],
        'LS1'  => ['title' => 'Lease Section 1 (LS1)',    'class' => 'bg-secondary',   'icon' => 'fa-solid fa-house'],
        'LS2A' => ['title' => 'Lease Section 2A (LS2A)',  'class' => 'bg-yellow',      'icon' => 'fa-solid fa-house'],
        'LS2B' => ['title' => 'Lease Section 2B (LS2B)',  'class' => 'bg-dark-orange', 'icon' => 'fa-solid fa-house'],
        'LS3'  => ['title' => 'Lease Section 3 (LS3)',    'class' => 'bg-deer',        'icon' => 'fa-solid fa-house'],
        'LS5'  => ['title' => 'Lease Section 5 (LS5)',    'class' => 'bg-assigned',    'icon' => 'fa-solid fa-house'],
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

<!-- <div class="container-fluid general-widget g-0">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card-header rounded-0 text-center">
                <a href="{{ route('recordRoom.index') }}" class="d-inline-block mx-auto">
                    <h5 class="mt-3">Total Record Files: <span id="record-totalCount">{{ number_format($total_records) }}</span></h5>
                </a>
            </div>
        </div>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-3 custom_card_column lavender">
        @foreach($cards as $code => $meta)
            @php
                $count = (int) ($section_counts[$code] ?? 0);
            @endphp

            {{-- show only if: user allowed + count > 0 --}}
            @if(in_array($code, $allowedCodes) && $count > 0)
        <div class="col">
            <div class="card radius-10 border-start border-0" >
                <div class="card-body">
                    <div class="d-flex align-items-center dashboard-cards">
                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin">
                            <img src="{{asset('assets/images/pageless-Total.svg')}}" alt="properties">
                        </div>
                        <div class="d-flex justify-content-between align-items-center w-calc-56">
                            <h4 class="my-1 text-dark view-list" >{{ $meta['title'] }}</h4>
                            <p class="mb-0" id="reg-totalCount"><span id="record-{{ strtolower($code) }}">{{ number_format($count) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div> -->

<div class="card">
    <div class="card-body">
        <div class="col-lg-12 order-lg-1 mb-4">
            <div class="widget-card">
                <div class="card-header rounded-0 text-center">
                    <h5 class="mt-3">
                        <a href="{{ route('recordRoom.index') }}">Total Record Files:
                            <span id="record-totalCount">{{ number_format($total_records) }}</span>
                        </a>
                    </h5>
                </div>
                <!-- <div class="card-body"> -->


                <div class="row">
                    @foreach($cards as $code => $meta)
                        @php
                            $count = (int) ($section_counts[$code] ?? 0);
                        @endphp

                         <!-- show only if: user allowed + count > 0  -->
                        @if(in_array($code, $allowedCodes) && $count > 0)
                            <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                <div class="card o-hidden border-0 h-100 w-100">
                                    <div class="{{ $meta['class'] }} b-r-4 card-body">
                                        <a href="{{ route('recordRoom.index', ['section' => $code]) }}">
                                            <div class="widget-media">
                                                <div class="align-self-center text-center widget-media-icon">
                                                    <i class="{{ $meta['icon'] }}"></i>
                                                </div>

                                                <div class="widget-media-body">
                                                    <span class="m-0">{{ $meta['title'] }}</span>

                                                    <h4 class="mb-0 counter">
                                                        <span id="record-{{ strtolower($code) }}">{{ number_format($count) }}</span>
                                                    </h4>

                                                    <i class="fa-solid fa-copy"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
