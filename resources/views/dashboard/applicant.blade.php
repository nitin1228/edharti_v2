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
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">Dashboard</li>
                            <!-- <li class="breadcrumb-item active" aria-current="page">My Dashboard</li> -->
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid dashboardcards">
        <div class="row row-flex mb-3">
            {{-- properties --}}
            <div class="col-md-4 mb-3">
                <a href="{{ route('applicant.properties') }}" style="color: inherit; text-decoration: none;">
                    <div class="card bg-light-green dash-cards">
                        <div class="card-body">
                            <!-- <h4>My Propert{{$userProperties->count() == 1 ? 'y':'ies'}}</h4> -->
                             <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0" style="font-size:22px;">My Propert{{ $userProperties->count() == 1 ? 'y' : 'ies' }}</h4>
                                <!-- <span class="badge text-light">{{ $userProperties->count() }}</span> -->
                            </div>
                            <div class="dash-widgets">                            
                                <div class="widget-media-body">
                                    <div class="widget-count">{{ $userProperties->count() }}</div>
                                    <!-- <div class="property-list">
                                        <div class="row">
                                            @foreach($userProperties as $up)
                                            <div class="col-sm-12 col-xxl-6">
                                                <p style="font-size:16px;">{{$up->known_as}}</p>
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
                                <h4 class="mb-0"style="font-size:22px;">Application{{ $userApplications->count() === 1 ? '' : 's' }}</h4>
                               
                            </div>

                            <div class="dash-widgets">                            
                                <div class="widget-media-body">
                                    <div class="widget-count">{{ $userApplications->count() }}</div>                                
                                    
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
                                                    <p>{{$demandCount}}</p>
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
                                    <a href="{{ route('applicant.pendingDemands')}}" style="color: inherit">
                                        <span id="totalAppCount">{{$demandCount}}</span>
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

        @if($userAppointments->count() > 0)
        <div class="row justify-content-between mb-3">
            <div class="col">
                <div class="card darkbluecard">
                    <div class="card-body">
                        <div class="dashboard-card-view">
                            <h4>Appointment{{$userAppointments->count() == 1 ? '':'s'}}</h4>
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
                                            <td> <h5>{{$uapt->application_no.'('.getApplicationTypeByApplicationNo($uapt->application_no).')'}}</h5></td>
                                            <td>{{date('d-m-Y',strtotime($uapt->valid_till))}}</td>
                                            <td>{!! !is_null($uapt->schedule_date) ? date('d-m-Y',strtotime($uapt->schedule_date))  : '<a href="'.$uapt->link.'" target="_blank">Click to schedule</a>'!!}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" style="text-align: center"> No appointment</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                          
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
