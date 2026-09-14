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
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-3 custom_card_column lavender">
            <div class="col">
                <a href="{{ route('regiserUserListings') }}" style="color: inherit; text-decoration: none;">
                    <div class="card radius-10 border-start border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin">
                                    <img src="{{asset('assets/images/pageless-Total.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Registration{{ $registrationCount === 1 ? '' : 's' }}</h4>
                                    <p class="mb-0">{{ $registrationCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
   
    </div>

    @include('include.alerts.ajax-alert')
@endsection

@section('footerScript')

@endsection
