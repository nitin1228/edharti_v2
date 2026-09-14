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
</style>
<div class="container-fluid">
    <div class="row justify-content-between mb-3">
        <div class="col-lg-6">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Dashboard</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid dashboardcards">
        <div class="row mb-3">
            {{-- properties --}}
            <div class="col-md-4 mb-3">
                <div class="card bg-light-green dash-cards">
                    <div class="card-body">
                        <h4>My Propert{{$userProperties->count() == 1 ? 'y':'ies'}}</h4>                                
                        <div class="dash-widgets">                            
                            <div class="widget-media-body">                                
                                <div class="property-list">
                                    <div class="row">
                                        @foreach($userProperties as $up)
                                        <div class="col-sm-12 col-md-12 col-xl-6">
                                            <p>{{$up->known_as}}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>                                                                
                            </div>
                            <div class="dash-icons">
                                <i class="fa-solid fa-building-user"></i>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
            {{-- Applications --}}
            <div class="col-md-4 mb-3">
                <div class="card bg-primary dash-cards">
                    <div class="card-body">
                        <h4>Application{{$userApplications->count()== 1 ? '':'s'}}</h4>
                        <div class="dash-widgets">                            
                            <div class="widget-media-body">                                
                                <div class="property-list">
                                    <div class="row">
                                        @foreach($userApplications as $ua)
                                        <div class="col-sm-12 col-md-12 col-xl-6">
                                            <p>{{$ua->application_no.'('.getServiceNameById($ua->service_type).')'}}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>                                                                
                            </div>
                            <div class="dash-icons">
                                <i class="fa-solid fa-house-user"></i>
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
        <!-- Demands -->
            <div class="col-md-4 mb-3">
                <div class="card bg-dark-orange dash-cards">
                    <div class="card-body">
                        <h4>Pending Demands</h4>
                        <div class="dash-widgets">                            
                            <div class="widget-media-body">                                                                
                                <div class="property-list">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-xl-6">
                                            <p>{{$demandCount}}</p>
                                        </div>
                                    </div>
                                </div>                                                                
                            </div>
                            <div class="dash-icons">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        {{-- Appointments --}}

        {{-- user Appointments --}}
        
        </div>

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
                                            <td>{{$uapt->application_no}}</td>
                                            <td>{{date('d-m-Y',strtotime($uapt->valid_till))}}</td>
                                            <td><?= !is_null($uapt->schedule_date) ? date('d-m-Y',strtotime($uapt->schedule_date))  : '<a href="'.$uapt->link.'" target="_blank">Click to schedule</a>'?></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" style="text-align: center"> No appointeemnts</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @foreach($userAppointments as $uapt)
                                    <div class="col">
                                        <h5>{{$uapt->application_no.'('.getServiceNameById($ua->service_type).')'}}</h5>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('include.alerts.ajax-alert')
    @endsection

    @section('footerScript')

    @endsection
