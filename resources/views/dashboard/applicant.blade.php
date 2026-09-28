@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <style>
        .subtypes {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
        }

        .typeName {
            text-align: center;
        }

        .custom-col {
            flex: 1;
            margin: 0 5px;
        }

        .custom-col:first-child {
            margin-left: 0;
        }

        .custom-col:last-child {
            margin-right: 0;
        }

        h6 {
            font-size: 11px !important;
        }

        .status_name {
            color: #101010;
            font-size: 16px;
            font-weight: 500;
        }

        .status_name:after {
            content: ':';
            display: inline
        }

        .status_value {
            color: #101010;
            font-size: 16px;
            font-weight: 500;
        }

        /* added by swati on 16-07-2025 for status of application  */
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
    <div class="container-fluid">
        <div class="row justify-content-between mb-3">
            <div class="col-lg-6">
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">Dashboard</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i
                                            class="bx bx-home-alt"></i></a>
                                </li>
                                <li class="breadcrumb-item">Dashboards</li>
                                {{-- <li class="breadcrumb-item active" aria-current="page">My Dashboard</li> --}}
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid dashboardcards">
            <div class="row row-flex mb-5">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="welcome-card">                                
                                {{-- <div class="welcome-text" style="display:none;">
                                    <h2>Welcome to eDharti 2.0 <img src="{{ asset('assets/frontend/assets/img/hand-waving.png') }}" alt="Welcome"></h2>
                                    <p>Manage your property applications, track requests and stay update with <br> real-time status - all in one place</p>
                                </div> --}}
                                <div class="welcome-text2" style="">
                                    <h2>Welcome, {{ auth()->user()->name}}</h2>
                                    <p>Manage your properties, track applications and view demands all in one place.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-flex mb-3">
                {{-- properties --}}
                <div class="col-md-4 mb-3">
                    <a href="{{ route('applicant.properties') }}" style="color: inherit; text-decoration: none;">
                        <div class="card bg-light-green dash-cards">
                            <div class="card-body">
                                <!-- <h4>My Propert{{ $userProperties->count() == 1 ? 'y' : 'ies' }}</h4> -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0" style="font-size:22px;">My
                                        Propert{{ $userProperties->count() == 1 ? 'y' : 'ies' }}</h4>
                                    <!-- <span class="badge text-light">{{ $userProperties->count() }}</span> -->
                                </div>
                                <div class="dash-widgets">
                                    <div class="widget-media-body">
                                        <div class="widget-count">{{ $userProperties->count() }}</div>
                                        <!-- <div class="property-list">
                                                                                                                                                                                <div class="row">
                                                                                                                                                                                    @foreach ($userProperties as $up)
                                            <div class="col-sm-12 col-xxl-6">
                                                                                                                                                                                                                                <p style="font-size:16px;">{{ $up->known_as }}</p>
                                                                                                                                                                                                                            </div>
                                            @endforeach
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>                                                                 -->
                                    </div>
                                    <div class="dash-icons">
                                        <i class="fa-solid fa-building-user"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>
                {{-- Applications --}}
                <div class="col-md-4 mb-3">
                    <a href="{{ route('applications.all.details') }}" style="color: inherit; text-decoration: none;">
                        <div class="card bg-primary dash-cards">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0"style="font-size:22px;">
                                        Application{{ $userApplications->count() === 1 ? '' : 's' }}</h4>
                                    <!-- <a href="{{ route('applications.history.details') }}">
                                                                                                                                                                            <span class="badge badge text-light">{{ $userApplications->count() }}</span>
                                                                                                                                                                        </a> -->
                                </div>

                                <div class="dash-widgets">
                                    <div class="widget-media-body">
                                        <div class="widget-count">{{ $userApplications->count() }}</div>
                                        <!-- <div class="property-list">
                                                                                                                                                                                <div class="row">
                                                                                                                                                                                   
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>                                                                 -->
                                    </div>
                                    <div class="dash-icons">
                                        <i class="fa-solid fa-house-user"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Demands -->
                <div class="col-md-4 mb-3">
                    <!-- by Swati on 16-07-2025 for adding hyperlink to redirect to demand page -->
                    <a href="{{ route('applicant.pendingDemands') }}" style="color: inherit; text-decoration: none;">
                        <div class="card bg-dark-orange dash-cards">
                            <div class="card-body">
                                <!-- <h4>Pending Demands ({{ $demandCount }})</h4> -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h4 class="mb-0"style="font-size:22px;">Pending Demands</h4>
                                    <!-- <span class="badge badge text-light">{{ $demandCount }}</span> -->
                                </div>

                                <div class="dash-widgets">
                                    <div class="widget-media-body">
                                        <div class="widget-count">{{ $demandCount }}</div>
                                        <!-- <div class="property-list">
                                                                                                                                                                                <div class="row">
                                                                                                                                                                                    <div class="col-sm-12 col-md-12 col-xl-6">
                                                                                                                                                                                            <p>{{ $demandCount }}</p>
                                                                                                                                                                                             <p style="font-size:16px;">Total: ₹{{ number_format($demandTotal) }}</p>

                                                                                                                                                                                    </div>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>                                                                 -->
                                    </div>
                                    <div class="dash-icons">
                                        <i class="fa-solid fa-hourglass-half"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="card-header">
                                                                                                                                                                    <h4>Pending Demands</h4>
                                                                                                                                                                </div>
                                                                                                                                                                <div class="card-body">
                                                                                                                                                                    <div class="dashboard-card-view">
                                                                                                                                                                        <h4>
                                                                                                                                                                            <a href="{{ route('applicant.pendingDemands') }}" style="color: inherit">
                                                                                                                                                                                <span id="totalAppCount">{{ $demandCount }}</span>
                                                                                                                                                                            </a>
                                                                                                                                                                        </h4>
                                                                                                                                                                    </div>
                                                                                                                                                                </div> -->
                        </div>
                    </a>
                </div>
                {{-- Appointments --}}

                {{-- user Appointments --}}

            </div>





            @forelse($userProperties as $property)

    {{-- ====================================================== --}}
    {{-- PROPERTY CARD --}}
    {{-- ====================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        {{-- Property Header --}}
        <div class="card-header bg-white border-bottom py-3">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <span class="fw-bold">
                        <i class="bi bi-house-door text-primary me-1"></i>
                        Property ID: {{ $property->old_property_id ?? '-' }}
                    </span>
                </div>

                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                    Ground Rent
                </span>

            </div>

        </div>


        <div class="card-body p-3">

            {{-- ====================================================== --}}
            {{-- GROUND RENT DETAILS AS PER LEASE --}}
            {{-- ====================================================== --}}

            <div class="mb-3">

                <h6 class="fw-bold mb-3">
                    <i class="bi bi-file-earmark-text text-primary me-1"></i>
                    Ground Rent Details as per Lease
                </h6>


                <div class="row g-2">

                    {{-- Ground Rent --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="bg-light rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Ground Rent
                            </small>

                            <span class="fw-bold text-primary">

                                @if($property->leaseDetails?->gr_in_re_rs !== null)

                                    ₹{{ number_format(
                                        $property->leaseDetails->gr_in_re_rs,
                                        0
                                    ) }}.{{ str_pad(
                                        $property->leaseDetails->gr_in_re_paise ?? 0,
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    ) }}

                                @else

                                    -

                                @endif

                            </span>

                        </div>

                    </div>


                    {{-- Ground Rent Start Date --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="bg-light rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                Ground Rent Start Date
                            </small>

                            <span class="fw-semibold">

                                {{ $property->leaseDetails?->start_date_of_gr
                                    ? \Carbon\Carbon::parse(
                                        $property->leaseDetails->start_date_of_gr
                                    )->format('d-m-Y')
                                    : '-'
                                }}

                            </span>

                        </div>

                    </div>


                    {{-- First RGR Due On --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="bg-light rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                First RGR Due On
                            </small>

                            <span class="fw-semibold">

                                {{ $property->leaseDetails?->first_rgr_due_on
                                    ? \Carbon\Carbon::parse(
                                        $property->leaseDetails->first_rgr_due_on
                                    )->format('d-m-Y')
                                    : '-'
                                }}

                            </span>

                        </div>

                    </div>


                    {{-- RGR Duration --}}
                    <div class="col-lg-3 col-md-6">

                        <div class="bg-light rounded p-3 h-100">

                            <small class="text-muted d-block mb-1">
                                RGR Duration
                            </small>

                            <span class="fw-semibold">

                                {{ $property->leaseDetails?->rgr_duration
                                    ? $property->leaseDetails->rgr_duration . ' Years'
                                    : '-'
                                }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <hr class="my-3">


            {{-- ====================================================== --}}
            {{-- GROUND RENT DEMAND DETAILS --}}
            {{-- ====================================================== --}}

           {{-- ====================================================== --}}
{{-- GROUND RENT DEMAND DETAILS --}}
{{-- ====================================================== --}}

<div>

    <h6 class="fw-bold mb-2">
        <i class="bi bi-receipt text-primary me-1"></i>
        Ground Rent Demand Details
    </h6>


    @php
        $hasGroundRent = false;
    @endphp


    {{-- ====================================================== --}}
    {{-- NEW DEMANDS --}}
    {{-- ====================================================== --}}

    @if($property->newDemands->isNotEmpty())

        @php
            $hasGroundRent = true;
        @endphp


        @foreach($property->newDemands->groupBy('id') as $demandId => $demands)

            @php
                $mainDemand = $demands->first();
            @endphp


            {{-- Single Demand --}}
            <div class="border rounded mb-2 overflow-hidden">


                {{-- Demand Header --}}
                <div class="bg-light border-bottom px-3 py-2">

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                        <div class="d-flex align-items-center gap-2">

                            <i class="bi bi-receipt text-primary"></i>

                            <small class="text-muted">
                                Demand ID:
                            </small>

                            <span class="fw-bold text-primary">
                                {{ $mainDemand->unique_id ?? '-' }}
                            </span>

                        </div>


                        <small class="text-muted">

                            {{ $demands->count() }}

                            {{ $demands->count() == 1
                                ? 'Item'
                                : 'Items'
                            }}

                        </small>

                    </div>

                </div>


                {{-- Demand Items --}}
@foreach($demands as $demand)

    @php
        $totalAmount   = (float) ($demand->head_net_total ?? 0);
        $paidAmount    = (float) ($demand->head_paid_amount ?? 0);
        $balanceAmount = (float) ($demand->head_balance_amount ?? 0);

        if ($paidAmount > 0 && abs($balanceAmount) < 0.01) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'unpaid';
        }
    @endphp


    <div class="border rounded-2 mb-2 bg-white">

        <div class="px-3 py-2">

            <div class="row align-items-center g-2">


                {{-- ================================================= --}}
                {{-- DEMAND TYPE + DESCRIPTION --}}
                {{-- ================================================= --}}
                <div class="col-lg-4 col-md-12">

                    <div class="d-flex align-items-center gap-2">

                       


                        <div class="overflow-hidden">

                            {{-- Title --}}
                            <div class="d-flex align-items-center gap-1">

                                <span
                                    class="fw-semibold text-dark"
                                    style="font-size: 13px;"
                                >
                                    {{ $demand->manual_title ?? '-' }}
                                </span>

                            </div>


                            {{-- Description --}}
                            @if(!empty($demand->manual_description))

                                <div
                                    class="text-muted text-truncate"
                                    style="
                                        font-size: 11px;
                                        max-width: 330px;
                                    "
                                    title="{{ $demand->manual_description }}"
                                >
                                    {{ $demand->manual_description }}
                                </div>

                            @endif

                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- PERIOD --}}
                {{-- ================================================= --}}
                <div class="col-lg-2 col-md-4">

                    <div class="border-start ps-2">

                        <div
                            class="text-muted"
                            style="font-size: 10px;"
                        >
                            Period
                        </div>

                        <div
                            class="fw-semibold text-nowrap"
                            style="font-size: 11px;"
                        >

                            {{ $demand->manual_date_from
                                ? \Carbon\Carbon::parse(
                                    $demand->manual_date_from
                                )->format('d-m-Y')
                                : '-'
                            }}

                            <span class="text-muted mx-1">→</span>

                            {{ $demand->manual_date_to
                                ? \Carbon\Carbon::parse(
                                    $demand->manual_date_to
                                )->format('d-m-Y')
                                : '-'
                            }}

                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- AMOUNT --}}
                {{-- ================================================= --}}
                <div class="col-lg-1 col-md-2">

                    <div class="border-start ps-2">

                        <div
                            class="text-muted"
                            style="font-size: 10px;"
                        >
                            Amount
                        </div>

                        <div
                            class="fw-bold text-primary text-nowrap"
                            style="font-size: 12px;"
                        >
                            ₹{{ number_format(
                                (float) ($demand->manual_amount ?? 0),
                                2
                            ) }}
                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- PAID --}}
                {{-- ================================================= --}}
                <div class="col-lg-1 col-md-2">

                    <div class="border-start ps-2">

                        <div
                            class="text-muted"
                            style="font-size: 10px;"
                        >
                            Paid
                        </div>

                        <div
                            class="fw-semibold text-success text-nowrap"
                            style="font-size: 12px;"
                        >
                            ₹{{ number_format($paidAmount, 2) }}
                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- BALANCE --}}
                {{-- ================================================= --}}
                <div class="col-lg-2 col-md-2">

                    <div class="border-start ps-2">

                        <div
                            class="text-muted"
                            style="font-size: 10px;"
                        >
                            Balance
                        </div>

                        <div
                            class="fw-semibold text-nowrap
                            {{ $balanceAmount > 0
                                ? 'text-danger'
                                : 'text-success'
                            }}"
                            style="font-size: 12px;"
                        >
                            ₹{{ number_format($balanceAmount, 2) }}
                        </div>

                    </div>

                </div>



                {{-- ================================================= --}}
                {{-- STATUS --}}
                {{-- ================================================= --}}
                <div class="col-lg-2 col-md-2 text-lg-end">

                    @if($paymentStatus === 'paid')
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Paid
                        </span>

                    @elseif($paymentStatus === 'partial')

                        <span class="badge bg-warning-subtle text-warning">
                            <i class="bi bi-clock-fill me-1"></i>
                            Partial
                        </span>

                    @else

                        <span
                            class="badge bg-danger-subtle text-danger">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>
                            Unpaid
                        </span>

                    @endif

                </div>


            </div>

        </div>

    </div>

@endforeach


            </div>

        @endforeach


    {{-- ====================================================== --}}
    {{-- OLD DEMANDS --}}
    {{-- ====================================================== --}}

    @elseif($property->oldDemands->isNotEmpty())


        @foreach($property->oldDemands as $demand)

            @if($demand->oldDemandSubheads->count() > 0)

                @php
                    $hasGroundRent = true;
                @endphp


                {{-- Single Demand --}}
                <div class="border rounded mb-2 overflow-hidden">


                    {{-- Demand Header --}}
                    <div class="bg-light border-bottom px-3 py-2">

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

                            <div class="d-flex align-items-center gap-2">

                                <i class="bi bi-receipt text-primary"></i>

                                <small class="text-muted">
                                    Demand ID:
                                </small>

                                <span class="fw-bold text-primary">

                                    {{ $demand->demand_id ?? '-' }}

                                </span>

                            </div>


                            <small class="text-muted">

                                {{ $demand->oldDemandSubheads->count() }}

                                {{ $demand->oldDemandSubheads->count() == 1
                                    ? 'Item'
                                    : 'Items'
                                }}

                            </small>

                        </div>

                    </div>


                    {{-- Demand Subheads --}}
                    @foreach($demand->oldDemandSubheads as $subhead)

                        @php

                            $paymentStatus = strtoupper(
                                trim($subhead->PaymentStatus ?? '')
                            );

                        @endphp


                        <div class="px-3 py-2 border-bottom">

                            <div class="row align-items-center g-2">


                                {{-- Demand Type --}}
                                <div class="col-lg-3 col-md-6">

                                    <small
                                        class="text-muted d-block"
                                        style="font-size:11px;"
                                    >
                                        Demand Type
                                    </small>

                                    <span class="fw-semibold small">

                                        {{ $subhead->Subhead ?? '-' }}

                                    </span>

                                </div>


                                {{-- Period --}}
                                <div class="col-lg-3 col-md-6">

                                    <small
                                        class="text-muted d-block"
                                        style="font-size:11px;"
                                    >
                                        Period
                                    </small>

                                    <span class="small fw-semibold">

                                        {{ $subhead->DateFrom
                                            ? \Carbon\Carbon::parse(
                                                $subhead->DateFrom
                                            )->format('d-m-Y')
                                            : '-'
                                        }}

                                        <span class="text-muted mx-1">
                                            to
                                        </span>

                                        {{ $subhead->DateTo
                                            ? \Carbon\Carbon::parse(
                                                $subhead->DateTo
                                            )->format('d-m-Y')
                                            : '-'
                                        }}

                                    </span>

                                </div>


                                {{-- Rate --}}
                                <div class="col-lg-2 col-md-4">

                                    <small
                                        class="text-muted d-block"
                                        style="font-size:11px;"
                                    >
                                        Rate
                                    </small>

                                    <span class="small fw-semibold">

                                        ₹{{ number_format(
                                            (float) ($subhead->Rate ?? 0),
                                            2
                                        ) }}

                                    </span>

                                </div>


                                {{-- Amount --}}
                                <div class="col-lg-2 col-md-4">

                                    <small
                                        class="text-muted d-block"
                                        style="font-size:11px;"
                                    >
                                        Amount
                                    </small>

                                    <span class="fw-bold text-primary small">

                                        ₹{{ number_format(
                                            (float) ($subhead->Amount ?? 0),
                                            2
                                        ) }}

                                    </span>

                                </div>


                                {{-- Status --}}
                                <div class="col-lg-2 col-md-4 text-lg-end">

                                    <small
                                        class="text-muted d-block mb-1"
                                        style="font-size:11px;"
                                    >
                                        Status
                                    </small>


                                    @if($paymentStatus === 'Y')

                                        <span class="badge bg-success-subtle text-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Paid

                                        </span>

                                    @else

                                        <span class="badge bg-danger-subtle text-danger">

                                            <i class="bi bi-exclamation-circle me-1"></i>

                                            Unpaid

                                        </span>

                                    @endif

                                </div>


                            </div>

                        </div>

                    @endforeach


                </div>

            @endif

        @endforeach

    @endif



    {{-- ====================================================== --}}
    {{-- NO DEMAND --}}
    {{-- ====================================================== --}}

    @if(!$hasGroundRent)

        <div class="bg-light rounded p-2 text-center">

            <small class="text-muted">

                <i class="bi bi-info-circle me-1"></i>

                No previous Ground Rent demand details available.

            </small>

        </div>

    @endif


</div>

        </div>

    </div>


@empty


    {{-- ====================================================== --}}
    {{-- NO PROPERTY FOUND --}}
    {{-- ====================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <i class="bi bi-house-x fs-1 text-muted"></i>

            <h6 class="mt-3 mb-1">
                No Property Found
            </h6>

            <p class="text-muted mb-0">

                No property is currently associated with your account.

            </p>

        </div>

    </div>


@endforelse







            {{-- What you can do --}}
            <section class="ed-panel ed-actions">
                <div class="ed-section-heading">
                    <h3>What you can do</h3>
                    <p>Access services and manage your applications easily.</p>
                </div>

                <div class="ed-action-grid">
                    {{-- Replace # with your actual New Application route when available. --}}
                    <a href="{{ route('new.application') }}" class="ed-action-item">
                        <div class="ed-action-icon"><i class="fa-regular fa-file-lines"></i></div>
                        <div class="ed-action-copy">
                            <strong>Apply for Services</strong>
                            <span>Submit new application<br>for L&amp;DO services.</span>
                        </div>
                    </a>

                    <a href="{{ route('applications.all.details') }}" class="ed-action-item">
                        <div class="ed-action-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <div class="ed-action-copy">
                            <strong>Track Applications</strong>
                            <span>Check status of your<br>submitted applications.</span>
                        </div>
                    </a>

                    <a href="{{ route('applicant.properties') }}" class="ed-action-item">
                        <div class="ed-action-icon"><i class="fa-solid fa-house"></i></div>
                        <div class="ed-action-copy">
                            <strong>View Properties</strong>
                            <span>View and manage your<br>property details.</span>
                        </div>
                    </a>

                    <a href="{{ route('applicant.pendingDemands') }}" class="ed-action-item">
                        <div class="ed-action-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                        <div class="ed-action-copy">
                            <strong>View Demands</strong>
                            <span>View and pay your<br>pending demands.</span>
                        </div>
                    </a>
                </div>
            </section>

            {{-- How it works --}}
            <section class="ed-panel ed-process">
                <div class="ed-section-heading">
                    <h3>How it works</h3>
                    <p>Simple steps to submit your application</p>
                </div>

                <div class="ed-process-grid">
                    <div class="ed-process-step">
                        <div class="ed-process-icon"><i class="fa-solid fa-file-pen"></i></div>
                        <div class="ed-process-copy">
                            <span class="ed-step-no">Step 1</span>
                            <strong>Fill Application Form</strong>
                            <p>Fill in the required details and upload supporting documents.</p>
                        </div>
                    </div>

                    <div class="ed-process-arrow" aria-hidden="true">→</div>

                    <div class="ed-process-step">
                        <div class="ed-process-icon"><i class="fa-regular fa-credit-card"></i></div>
                        <div class="ed-process-copy">
                            <span class="ed-step-no">Step 2</span>
                            <strong>Pay Application Charges</strong>
                            <p>Pay the required application charges securely through online or offline mode.</p>
                        </div>
                    </div>

                    <div class="ed-process-arrow" aria-hidden="true">→</div>

                    <div class="ed-process-step">
                        <div class="ed-process-icon"><i class="fa-solid fa-print"></i></div>
                        <div class="ed-process-copy">
                            <span class="ed-step-no">Step 3</span>
                            <strong>Submit &amp; Print</strong>
                            <p>Submit your application and download/print the application form.</p>
                        </div>
                    </div>
                </div>
            </section>

            @if ($userAppointments->count() > 0)
                <div class="row justify-content-between mb-3">
                    <div class="col">
                        <div class="card darkbluecard">
                            <div class="card-body">
                                <div class="dashboard-card-view">
                                    <h4>Appointment{{ $userAppointments->count() == 1 ? '' : 's' }}</h4>
                                    <div class="row p-2">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Application</th>
                                                    <th>Valid till</th>
                                                    <th>Appointment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($userAppointments as $uapt)
                                                    <tr>
                                                        <td>{{ $uapt->application_no }}</td>
                                                        <td>{{ date('d-m-Y', strtotime($uapt->application_no)) }}</td>
                                                        <td>{{ !is_null($uapt->application_no) ? date('d-m-Y', strtotime($uapt->application_no)) : '<a href="' . $upat->link . '" target="_blank">Click to schedule</a>' }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" style="text-align: center"> No appointment</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        @foreach ($userAppointments as $uapt)
                                            <div class="col">
                                                <h5>{{ $uapt->application_no . '(' . getServiceNameById($uapt->service_type) . ')' }}
                                                </h5>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @include('include.alerts.ajax-alert')
    @endsection

    @section('footerScript')

    @endsection
