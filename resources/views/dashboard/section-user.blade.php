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

        .section-row {
            display: flex;
            width: 100%;
            background: #02020200;
            justify-content: space-between;
            padding: 5px 15px;
            box-shadow: 5px 2px 8px -2px #00000066;
        }
    </style>
    <div class="container-fluid">
        <!-- @if (Auth::user()->hasAnyRole('section-officer'))
    <form action="{{ route('switch.user') }}" method="POST" id="switchUserForm">
     @csrf
     <div class="row mb-2">
      <div class="col-md-3"></div>
      <div class="col-md-2">
       <select  class="form-select" name="section" required>
        <option  value="">--Select Section name--</option>
        @foreach ($sections as $section)
    <option value="{{ $section->id }}">{{ $section->name }}</option>
    @endforeach
       </select>
      </div>
      <div class="col-md-2">
       <button type="submit" class="btn btn-warning">Switch to CDV User</button>
      </div>
      <div class="clearfix"></div>
     </div>
    </form>
    @endif  -->
   
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
                                <li class="breadcrumb-item active" aria-current="page">My Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            @if (Auth::user()->hasAnyRole('section-officer'))
                <div class="col-lg-6 mb-3">
                    <form action="{{ route('switch.user') }}" method="POST" id="switchUserForm">
                        @csrf
                        <div class="switch-userwrap">
                            <div class="switch-select">
                                <select class="form-select" name="section" required>
                                    <option value="">--Select Section name--</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning">Switch to CDV User</button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="col-lg-6 ms-auto">
                {{--<div class="colony-dropdown ms-auto">
                    <div>
                        <select id="select-filter" class="form-select">
                            <option value=""> Filter by section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>--}}
            </div>
        </div>
        <!-- added by anil for new UI on 04-06-2025 -->
        <div class="container-fluid general-widget g-0">
            <div class="row">
                <div class="col-12">
                    <div class="card-header rounded-0">
                        <h5 class="mt-3">
                            Registrations
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-5 custom_card_column lavender">
                <div class="col">
                    <div class="card radius-10 border-start border-0" >
                        <div class="card-body" onclick="handleRegistrationClick()">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pageless-Total.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Total</h4>
                                    <p class="mb-0" id="reg-totalCount">{{ $registrations['totalCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0">
                        <div class="card-body" onclick="handleRegistrationClick('{{Crypt::encrypt('RS_NEW')}}')">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/registration-icon-new.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >New</h4>
                                    <p class="mb-0 " id="reg-newCount">{{ $registrations['newCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0">
                        <div class="card-body" onclick="handleRegistrationClick('{{Crypt::encrypt('RS_APP')}}')">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/registration-icon-approved.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Approved</h4>
                                    <p class="mb-0 " id="reg-appCount">{{ $registrations['appCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0">
                        <div class="card-body" onclick="handleRegistrationClick('{{Crypt::encrypt('RS_REJ')}}')">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/registration-icon-rejected.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list">Rejected</h4>
                                    <p class="mb-0 " id="reg-rejCount">{{ $registrations['rejCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="col">
                    <div class="card radius-10 border-start border-0">
                        <div class="card-body" onclick="handleRegistrationClick('{{Crypt::encrypt('RS_PEN')}}')">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/registration-icon-progress.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Pending</h4>
                                    <p class="mb-0 " id="reg-penCount">{{ $registrations['penCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            <div class="row">
                <div class="col-12">
                    <div class="card-header rounded-0">
                        <h5 class="mt-3">
                            Applications
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-5 custom_card_column peach">
                <div class="col">
                    <div class="card radius-10 border-start border-0">
                        <a class="card-body"  target="_blank" href="{{ route('admin.applications') }}">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pageless-LH.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Total</h4>
                                    <p class="mb-0" id="totalAppCount">{{ $totalAppCount }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @if($applicationData)
                    @php
                       $images = [
                        'mutation'=> asset('assets/images/app-icon-mutation.svg'),
                        'conversion' => asset('assets/images/app-icon-conversion.svg'),
                        'noc' => asset('assets/images/app-icon-noc.svg'),
                        'doa' => asset('assets/images/app-icon-doa.svg'),
                        'default' => asset('assets/images/commercial.svg')
                       ]; 
                    @endphp
                @endif
                @foreach ($applicationData as $key=>$details)
                    @isset($details['application_type'])
                        <div class="col">
                            <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#app_{{$key}}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center dashboard-cards">
                                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{$images[$key]?? $images['default']}}" alt="properties">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center w-calc-56">
                                            <h4 class="my-1 text-dark view-list" >{{$details['application_type']}}</h4>
                                            <p class="mb-0 ">{{$details['total']}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endisset
                @endforeach
               
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-content-tabs">
                        @foreach ($applicationData as $key=>$details)
                        @isset($details['application_type'])
                                <div class="card card-content-section" id="app_{{$key}}">
                                    <div class="card-body">
                                     @if($key == 'noc')
                                       <div class="row">
                                       	<div class="col-md-6"> 
                                       	 <h5 class="mb-2" style="color:teal;">NOC with Demand -: ({!! $applicationData['nocDataByDemand']['with_demand_count'] ?? 0 !!})</h5>                                       
                                       	 <ul>
                                            @foreach ($applicationData['nocDataByDemand']['with_demand_status_wise'] as $index=>$count)
                                                @continue(in_array($index,['total', 'application_type', 'APP_DES']))
                                                                                              
                                                <li class="d-flex align-items-center justify-content-between" onclick="handleApplicationClick('{{Crypt::encrypt($index)}}', '{{$key}}','{{$index}}','with_demand')">
                                                    <div class="title-text">{{ getServiceNameByCode($index)}}</div>
                                                    <div class="title-count">{{$count}}</div>
                                                </li>
                                                 @endforeach
                                        </ul></div>
                                       	<div class="col-md-6">
                                       	 <h5 class="mb-2" style="color:teal;">NOC without Demand -: ({!! $applicationData['nocDataByDemand']['without_demand_count'] ?? 0 !!})</h5> <ul>
                                            @foreach ($applicationData['nocDataByDemand']['without_demand_status_wise'] as $index=>$count)
                                                @continue(in_array($index,['total', 'application_type', 'APP_DES']))
                                                                                              
                                                <li class="d-flex align-items-center justify-content-between" onclick="handleApplicationClick('{{Crypt::encrypt($index)}}', '{{$key}}','{{$index}}','without_demand')">
                                                    <div class="title-text">{{ getServiceNameByCode($index)}}</div>
                                                    <div class="title-count">{{$count}}</div>
                                                </li>
                                                 @endforeach
                                        </ul></div>
                                       	
                                       </div>
                                        @else
                                        <ul>
                                            @foreach ($details as $index=>$count)
                                                @continue(in_array($index,['total', 'application_type', 'APP_DES']))
                                                                                              
                                                <li class="d-flex align-items-center justify-content-between" onclick="handleApplicationClick('{{Crypt::encrypt($index)}}', '{{$key}}','{{$index}}')">
                                                    <div class="title-text">{{ getServiceNameByCode($index)}}</div>
                                                    <div class="title-count">{{$count}}</div>
                                                </li>
                                                 @endforeach
                                        </ul>
                                        @endif 
                                    </div>
                                </div>
                            @endisset

                            
                            <!-- @isset($details['application_type'])
                                <div class="card card-content-section" id="app_{{$key}}"> 
                                    <div class="card-body">
                                        <ul>
                                            @foreach ($details as $index=>$count)
                                                @continue(in_array($index,['total', 'application_type', 'APP_DES']))
                                                <li class="d-flex align-items-center justify-content-between" onclick="handleApplicationClick('{{Crypt::encrypt($index)}}', '{{$key}}','{{$index}}')">
                                                    <div class="title-text">{{ getServiceNameByCode($index)}}</div>
                                                    <div class="title-count">{{$count}}</div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endisset -->
                        @endforeach
                    </div>
                </div>
            </div>
            

            <div class="row">
                <div class="col-12">
                    <div class="card-header rounded-0">
                        <h5 class="mt-3">
                            Pending Applications (Age-wise)
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-5 custom_card_column bright-blue">
                <div class="col">
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#pendAppTotal">
                        <div class="card-body" onclick="getApplicationSummary()">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pageless-FH.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Total</h4>
                                    <p class="mb-0 "> {{$pendingApplicationNewSummary['totalPending']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @php
                $pendingApplicationNewSummary = $pendingApplicationNewSummary['ranges'];
                @endphp
                <div class="col">
                    <!--data-target="#pendAppLess30"--> 
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#pendAppTotal">
                        <div class="card-body" onclick="getApplicationSummary(0)">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pan-app-icon-minimum.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" > <i class='bx  bx-chevron-left'></i>30 Days </h4>
                                    <p class="mb-0 ">{{$pendingApplicationNewSummary['0_30_days']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    {{-- pendApp60 --}}
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#pendAppTotal">
                        <div class="card-body" onclick="getApplicationSummary(1)">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pan-app-icon-range.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >30-60 Days</h4>
                                    <p class="mb-0 ">{{$pendingApplicationNewSummary['31_60_days']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#pendAppTotal">
                        <div class="card-body" onclick="getApplicationSummary(2)">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pan-app-icon-range.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >60-90 Days</h4>
                                   <p class="mb-0 ">{{$pendingApplicationNewSummary['61_90_days']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#pendAppTotal">
                        <div class="card-body" onclick="getApplicationSummary(3)">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pan-app-icon-maximum.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list">90<i class='bx  bx-chevron-right'></i> Days</h4>
                                   <p class="mb-0 ">{{$pendingApplicationNewSummary['above_90']}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-content-tabs">
                        <div class="card card-content-section" id="pendAppTotal">
                            <div class="card-body">
                                <ul>
                                    
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @can('view.financial.reports')
            <div class="row">
                <div class="col-12">
                    <div class="card-header rounded-0 d-flex justify-content-between align-items-center">
                        <h5 class="mt-3">
                            Revenue Details
                        </h5>
                        <div class="header-title-note" id="revenueYear"></div>
                    </div>
                </div>
            </div>
                @php
                $grandTotal = collect($revenueDetails['summary'])->sum('amount');
                $revenueIcons = [
                'pageless.svg',
                'properties-icon-hand.svg',
                'revenue-icon-sublease.svg',
                'revenue-icon-conversion.svg',
                'properties-icon-hand.svg',
                'properties-icon-hand.svg',
                'properties-icon-hand.svg',
                'properties-icon-hand.svg',
                
                ]
                @endphp
                  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-2 custom_card_column mint-green">
                      <div class="col">
                            <div class="card radius-10 border-start border-0">
                                <div class="card-body">
                                    <a href="javascript:;"
                                    class="app-query-link"
                                    data-status="summary"
                                    data-service="all">
                                    <div class="d-flex align-items-center dashboard-cards">
                                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin">
                                          
                                            <img src="{{ asset('assets/images/' . $revenueIcons[0]) }}" alt="properties">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center w-calc-56">
                                            <div class="" style="width: 70%;">
                                                <h4 class="my-1 text-dark view-list" > Total</h4>
                                            </div>
                                            <p class="mb-0"> ₹ {{ customNumFormat(round(($grandTotal ?? 0) / 10000000, 2)) }}</p>
                                        </div>
                                    </div>
                                </a>
                                </div>
                            </div>
                        </div>
                    @forelse ($revenueDetails['summary'] as $key => $item)
                        <div class="col">
                            <div class="card radius-10 border-start border-0">
                                <div class="card-body">
                                    <a href="javascript:;"
                                    class="app-query-link"
                                    data-status="summary"
                                    data-service="{{ $item['code'] }}">
                                    <div class="d-flex align-items-center dashboard-cards">
                                        <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin">
                                          
                                            <img src="{{ asset('assets/images/' . $revenueIcons[$key]) }}" alt="properties">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center w-calc-56">
                                            <div class="" style="width: 70%;">
                                                <h4 class="my-1 text-dark view-list" > {{ $item['name'] == 'Application' ? "Application Processing Fee" : $item['name']}}</h4>
                                            </div>
                                            <p class="mb-0"> ₹ {{ customNumFormat(round(($item['amount'] ?? 0) / 10000000, 2)) }}</p>
                                        </div>
                                    </div>
                                </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </div>
@endcan
                <div class="row">
                    <div class="col-lg-4 col-12 mb-4">
                        <div class="card public_service service-seclist">
                            {{-- <h4 class="pubser-title">Section{{ $sections->count() == 1 ? '' : 's' }}</h4> --}}
                            <!-- <h4 class="pubser-title">Properties in Section</h4> -->
                            <div class="card-header text-center">
                                <h5 class="mt-3">Properties in Section</h5>
                            </div>
                            <div class="card-body" style="
    height: 260px;
    /* overflow: scroll; */
    overflow-x: hidden;   /* horizontal scroll */
    overflow-y: auto;
">
                                <div class="dashboard-card-view">
                                    @foreach ($sections->sortBy('name') as $section)
                                        <div class="grievance-card-item">
                                            <a href="{{ route('colonywiseSectionReport', [$section->id]) }}" target="_blank">
                                                <div class="public-services-content">
                                                    <div class="services-label">
                                                        <h4>{{ $section->name }}</h4>
                                                    </div>
                                                    <div class="services-count">
                                                        <h4 class="services_count_text">
                                                            <span>{{ $section->property_count }}</span>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-12 mb-4">
                        <div class="card public_service service-seclist">
                            <div class="card-header text-center">
                                <h5 class="mt-3">
                                    <a href="#">
                                        Public Services:
                                        <span id="publicServiceCount">{{ $grievencesCount + $appointmentCount }}</span>
                                    </a>
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="dashboard-card-view">
                                    <div class="grievance-card-item">
                                        <a href="{{ route('grievance.index') }}">
                                            <div class="public-services-content">
                                                <div class="services-label">
                                                    <img src="{{ asset('assets/images/WhyGrievances.svg') }}"
                                                        alt="Grievances" class="grievance-icon">
                                                    <h4>Grievances</h4>
                                                </div>
                                                <div class="services-count">
                                                    <h4 class="services_count_text"><span
                                                            id="appointmentCount">{{ $grievencesCount }}</span></h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="grievance-card-item">
                                        <a href="{{ route('appointments.index') }}">
                                            <div class="public-services-content">
                                                <div class="services-label">
                                                    <img src="{{ asset('assets/images/Schedule.svg') }}" alt="Appointments">
                                                    <h4>Appointments</h4>
                                                </div>
                                                <div class="services-count">
                                                    <h4 class="services_count_text"><span
                                                            id="grievencesCount">{{ $appointmentCount }}</span></h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @haspermission('view.new.added.properties')
                    <div class="col-lg-4 col-12">
                        <div class="card addedproperties">
                            <div class="card-header text-center">
                                <h5 class="mt-3">
                                    <a href="{{ route('applicantNewProperties') }}">
                                        Added Properties:
                                        <span id="new-prop-totalCount">{{ $newProperty['totalCount'] }}</span>
                                    </a>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="dashboard-card-view temp_design">
                                    <div class="added-properties-content">

                                        <div class="item-cards-col">
                                            <div class="added-status">
                                                <span class="status-added-color newStatus"></span>
                                                <a
                                                    href="{{ route('applicantNewProperties', ['status' => Crypt::encrypt('RS_NEW')]) }}">
                                                    <p class="cards-title-p">New</p>
                                                </a>
                                            </div>
                                            <h3 class="item-cards-count" id="new-prop-newCount">
                                                <span class="badge badge-new counter">{{ $newProperty['newCount'] }}</span>
                                            </h3>
                                        </div>

                                        <div class="item-cards-col">
                                            <div class="added-status">
                                                <span class="status-added-color pendingStatus"></span>
                                                <a
                                                    href="{{ route('applicantNewProperties', ['status' => Crypt::encrypt('RS_PEN')]) }}">

                                                    <p class="cards-title-p">Pending</p>
                                                </a>
                                            </div>
                                            <h3 class="item-cards-count" id="new-prop-penCount">
                                                <span class="badge badge-pending counter">
                                                    {{ $newProperty['penCount'] }}
                                                </span>
                                            </h3>
                                        </div>


                                        <div class="item-cards-col">
                                            <div class="added-status">
                                                <span class="status-added-color underreviewStatus"></span>
                                                <a
                                                    href="{{ route('applicantNewProperties', ['status' => Crypt::encrypt('RS_UREW')]) }}">
                                                    <p class="cards-title-p">Under Review</p>
                                                </a>
                                            </div>
                                            <h3 class="item-cards-count" id="new-prop-urewCount">
                                                <span
                                                    class="badge badge-underreview counter">{{ $newProperty['urewCount'] }}</span>
                                            </h3>
                                        </div>


                                        <div class="item-cards-col">
                                            <div class="added-status">
                                                <span class="status-added-color approvedStatus"></span>
                                                <a
                                                    href="{{ route('applicantNewProperties', ['status' => Crypt::encrypt('RS_APP')]) }}">
                                                    <p class="cards-title-p">Approved</p>
                                                </a>
                                            </div>
                                            <h3 class="item-cards-count" id="new-prop-appCount">
                                                <span class="badge badge-approved counter">
                                                    {{ $newProperty['appCount'] }}
                                                </span>
                                            </h3>
                                        </div>


                                        <div class="item-cards-col">
                                            <div class="added-status">
                                                <span class="status-added-color rejectedStatus"></span>
                                                <a
                                                    href="{{ route('applicantNewProperties', ['status' => Crypt::encrypt('RS_REJ')]) }}">
                                                    <p class="cards-title-p">Rejected</p>
                                                </a>
                                            </div>
                                            <h3 class="item-cards-count" id="new-prop-rejCount">
                                                <span class="badge badge-rejected counter">
                                                    {{ $newProperty['rejCount'] }}
                                                </span>
                                            </h3>
                                        </div>

                                        <div class="item-cards-col">
                                            <div class="added-status">
                                                <span class="status-added-color reviewedStatus"></span>
                                                <a
                                                    href="{{ route('applicantNewProperties', ['status' => Crypt::encrypt('RS_REW')]) }}">
                                                    <p class="cards-title-p">Reviewed</p>
                                                </a>
                                            </div>
                                            <h3 class="item-cards-count" id="new-prop-urewCount">
                                                <span class="badge badge-reviewed counter">
                                                    {{ $newProperty['rewCount'] }}
                                                </span>
                                            </h3>
                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endhaspermission
                </div>
                
            </div>
        </div>
        <!-- end added by anil for new UI on 04-06-2025 -->
        <!-- commeted by anil for new UI on 04-06-2025 -->
        <div class="container-fluid dashboardcards">
            <div class="row">
                <!-- commeted by anil for new UI on 04-06-2025 -->
                <!-- <div class="col-lg-8 col-12">
                                                                                                                                                                                                                                                            <div class="col-lg-12 col-12" style="margin-bottom: 0px;">
                                                                                                                                                                                                                                                                <div class="card skybluecard totalrgn">
                                                                                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                                                                                        <div class="dashboard-card-view" id="registrationData">
                                                                                                                                                                                                                                                                            <h4><a href="{{ route('regiserUserListings') }}" style="color: inherit">Total
                                                                                                                                                                                                                                                                                    Registrations:
                                                                                                                                                                                                                                                                                    <span id="reg-totalCount">{{ $registrations['totalCount'] }}</span></a></h4>
                                                                                                                                                                                                                                                                            <div class="container-fluid">
                                                                                                                                                                                                                                                                                <div class="row separate-col-border">
                                                                                                                                                                                                                                                                                    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                        <a
                                                                                                                                                                                                                                                                                            href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_NEW')]) }}"><span
                                                                                                                                                                                                                                                                                                class="dashboard-label">New:</span>
                                                                                                                                                                                                                                                                                            <span id="reg-newCount"> {{ $registrations['newCount'] }}</span>
                                                                                                                                                                                                                                                                                        </a>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                        <a
                                                                                                                                                                                                                                                                                            href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_PEN')]) }}"><span
                                                                                                                                                                                                                                                                                                class="dashboard-label">Pending:</span>
                                                                                                                                                                                                                                                                                            <span id="reg-penCount"> {{ $registrations['penCount'] }} </span>
                                                                                                                                                                                                                                                                                        </a>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                        <a
                                                                                                                                                                                                                                                                                            href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_UREW')]) }}"><span
                                                                                                                                                                                                                                                                                                class="dashboard-label">Under Review:</span>
                                                                                                                                                                                                                                                                                            <span id="reg-urewCount"> {{ $registrations['urewCount'] }}
                                                                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                                                                        </a>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                        <a
                                                                                                                                                                                                                                                                                            href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REW')]) }}"><span
                                                                                                                                                                                                                                                                                                class="dashboard-label">Reviewed:</span>
                                                                                                                                                                                                                                                                                            <span id="reg-urewCount"> {{ $registrations['rewCount'] }}
                                                                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                                                                        </a>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                        <a
                                                                                                                                                                                                                                                                                            href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_APP')]) }}"><span
                                                                                                                                                                                                                                                                                                class="dashboard-label">Approved:</span><span id="reg-appCount">
                                                                                                                                                                                                                                                                                                {{ $registrations['appCount'] }}</span></a>
                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                        <a
                                                                                                                                                                                                                                                                                            href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REJ')]) }}"><span
                                                                                                                                                                                                                                                                                                class="dashboard-label">Rejected:</span> <span
                                                                                                                                                                                                                                                                                                id="reg-rejCount">{{ $registrations['rejCount'] }}</span></a>
                                                                                                                                                                                                                                                                                    </div>


                                                                                                                                                                                                                                                                                    {{-- <div class="custom-col-col col-4 col-lg-2">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REW')]) }}"><span
                                                    class="dashboard-label">Review:</span> {{ $registrations['rewCount']
                                                }}</a>
                                        </div> --}}
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                            <div class="col-lg-12 col-12" style="margin-bottom: 0px;">
                                                                                                                                                                                                                                                                <div class="card offorangecard totalApp" style="margin-bottom: 0px;">
                                                                                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                                                                                        <div class="dashboard-card-view">
                                                                                                                                                                                                                                                                            <h4><a href="{{ route('admin.applications') }}" style="color: inherit">Total
                                                                                                                                                                                                                                                                                    Applications:
                                                                                                                                                                                                                                                                                    <span id="totalAppCount">{{ $totalAppCount }}</span></a></h4>
                                                                                                                                                                                                                                                                            <div class="container-fluid">
                                                                                                                                                                                                                                                                                <div class="row separate-col-border">
                                                                                                                                                                                                                                                                                    @foreach ($statusList as $i => $status)
    <div class="custom-col-col col-4 col-lg-2">
                                                                                                                                                                                                                                                                                            @if ($status->item_name == 'Disposed')
    <a href="{{ route('applications.disposed') }}">
                                                                                                                                                                                                                                                                                                    <span class="dashboard-label">{{ $status->item_name }}:</span>
                                                                                                                                                                                                                                                                                                    <span
                                                                                                                                                                                                                                                                                                        id="total-{{ $status->item_code }}">{{ isset($statusWiseCounts[$status->item_code]) ? $statusWiseCounts[$status->item_code] : 0 }}</span></a>
@else
    <a
                                                                                                                                                                                                                                                                                                    href="{{ route('admin.applications', ['status' => Crypt::encrypt(" $status->item_code")]) }}">
                                                                                                                                                                                                                                                                                                    <span class="dashboard-label">{{ $status->item_name }}:</span>
                                                                                                                                                                                                                                                                                                    <span
                                                                                                                                                                                                                                                                                                        id="total-{{ $status->item_code }}">{{ isset($statusWiseCounts[$status->item_code]) ? $statusWiseCounts[$status->item_code] : 0 }}</span></a>
    @endif
                                                                                                                                                                                                                                                                                        </div>
    @endforeach
                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                        {{-- <div class="row mt-4">
                                @foreach ($statusList as $status)
                                <div class="custom-col-col col-4 col-lg-2">
                                    <span class="status_name">{{$status->item_name}}</span> <span
                                        class="status_value">{{isset($statusWiseCounts[$status->item_code]) ?
                                        $statusWiseCounts[$status->item_code] : 0}}</span>
                                </div>
                                @endforeach
                            </div> --}}
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                        </div> -->
                <!-- commeted end by anil for new UI on 04-06-2025 -->
                {{-- <div class="col-lg-4 col-12">
                <div class="card redcard">
                    <div class="card-body">
                        <h4>Section</h4>
                        @foreach ($sections as $section)
                        <div class="section-row">
                            <span>{{$section->name}}</span>
                            <a href="{{route('colonywiseSectionReport',[$section->id])}}"
                                target="_blank">{{$section->property_count}}</a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div> --}}

                <!-- commeted by anil for new UI on 04-06-2025 -->
                <!-- <div class="col-lg-4 col-12">
                                                                                                                                                                                                                                                            <div class="col-lg-12 col-12" style="margin-bottom: 0px; height:100%">
                                                                                                                                                                                                                                                                <div class="card purplecard public_service" style="margin-bottom: 0px;">
                                                                                                                                                                                                                                                                    {{-- <h4 class="pubser-title">Section{{ $sections->count() == 1 ? '' : 's' }}</h4> --}}
                                                                                                                                                                                                                                                                    <h4 class="pubser-title">Properties in Section</h4>
                                                                                                                                                                                                                                                                    <div class="card-body">
                                                                                                                                                                                                                                                                        <div class="dashboard-card-view">
                                                                                                                                                                                                                                                                            @foreach ($sections as $section)
    <div class="grievance-card-item">
                                                                                                                                                                                                                                                                                    <a href="{{ route('colonywiseSectionReport', [$section->id]) }}"
                                                                                                                                                                                                                                                                                        target="_blank">
                                                                                                                                                                                                                                                                                        <div class="public-services-content">
                                                                                                                                                                                                                                                                                            <div class="services-label">
                                                                                                                                                                                                                                                                                                <h4>{{ $section->name }}</h4>
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                            <div class="services-count">
                                                                                                                                                                                                                                                                                                <h4 class="services_count_text"><span>{{ $section->property_count }}</span>
                                                                                                                                                                                                                                                                                                </h4>
                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                    </a>
                                                                                                                                                                                                                                                                                </div>
    @endforeach
                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                        </div> -->
                <!-- commeted end by anil for new UI on 04-06-2025 -->
                {{-- <div class="col-lg-4 col-12">
                    <div class="card greycard submutCard">
                        <div class="card-body">
                            <h4>Substitution / Mutation: <span
                                    id="mutation-total">{{ isset($applicationData['mutation']['total']) ? $applicationData['mutation']['total'] : 0 }}</span>
                            </h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                    <div class="table-item">
                                        <span>
                                            <a href="#">{{ $status->item_name }}:</a>
                                        </span>
                                        <div class="value"><span
                                                id="mutation-{{ $status->item_code }}">{{ isset($applicationData['mutation'][$status->item_code]) ? $applicationData['mutation'][$status->item_code] : 0 }}</span>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card darkbluecard landusechange">
                        <div class="card-body">
                            <h4>Land Use Change: <span
                                    id="luc-total">{{ isset($applicationData['luc']['total']) ? $applicationData['luc']['total'] : 0 }}</span>
                            </h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                    <div class="table-item">
                                        <span>
                                            <a href="#">{{ $status->item_name }}:</a>
                                        </span>
                                        <div class="value"><span
                                                id="luc-{{ $status->item_code }}">{{ isset($applicationData['luc'][$status->item_code]) ? $applicationData['luc'][$status->item_code] : 0 }}</span>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card bluecard conversioncard">
                        <div class="card-body">
                            <h4>Conversion: <span
                                    id="conversion-total">{{ isset($applicationData['conversion']['total']) ? $applicationData['conversion']['total'] : 0 }}</span>
                            </h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                    <div class="table-item">
                                        <span>
                                            <a href="#">{{ $status->item_name }}:</a>
                                        </span>
                                        <div class="value">
                                            <span
                                                id="conversion-{{ $status->item_code }}">{{ isset($applicationData['conversion'][$status->item_code]) ? $applicationData['conversion'][$status->item_code] : 0 }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card redcard">
                        <div class="card-body">
                            <h4>NOC: <span
                                    id="noc-total">{{ isset($applicationData['noc']['total']) ? $applicationData['noc']['total'] : 0 }}</span>
                            </h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                    <div class="table-item">
                                        <span>
                                            <a href="#">{{ $status->item_name }}:</a>
                                        </span>
                                        <div class="value"><span
                                                id="noc-{{ $status->item_code }}">{{ isset($applicationData['noc'][$status->item_code]) ? $applicationData['noc'][$status->item_code] : 0 }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="card skybluecard">
                        <div class="card-body">
                            <h4>DOA: <span
                                    id="doa-total">{{ isset($applicationData['doa']['total']) ? $applicationData['doa']['total'] : 0 }}</span>
                            </h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                    <div class="table-item">
                                        <span>
                                            <a href="#">{{ $status->item_name }}:</a>
                                        </span>
                                        <div class="value"><span
                                                id="doa-{{ $status->item_code }}">{{ isset($applicationData['doa'][$status->item_code]) ? $applicationData['doa'][$status->item_code] : 0 }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}
                @if ($applicationData)
            
                    {{-- <div class="col-lg-6 col-12">
                        <div class="card radius-10">
                            <div class="d-flex tabs-progress-container">
                                <div class="nav-tabs-left-aside-dashboard">
                                    <ul class="nav nav-tabs nav-primary" role="tablist"
                                        style="display: block !important;">
                                        @foreach ($applicationData as $key => $application)
                                            @if ($key != 'nocDataByDemand')
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" href="#"
                                                        id="v-pills-{{ $key }}-tab" data-bs-toggle="pill"
                                                        data-bs-target="#v-pills-{{ $key }}" type="button"
                                                        role="tab" aria-controls="v-pills-{{ $key }}"
                                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                        <div class="text-center">
                                                            <div class="tab-title">{{ $application['application_type'] }}
                                                            </div>
                                                            <span class="tab-total-no"
                                                                id="{{ $key }}-total">{{ $application['total'] ?? 0 }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="nav-tabs-right-aside-dashboard">
                                    <div class="tab-content py-3" id="v-pills-tabContent">
                                        @foreach ($applicationData as $key => $application)
                                            @if ($key != 'noc' && $key != 'nocDataByDemand')
                                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                    id="v-pills-{{ $key }}" role="tabpanel"
                                                    aria-labelledby="v-pills-{{ $key }}-tab">
                                                    <div class="col-12">
                                                        <ul class="progress-report">
                                                            @foreach ($statusList as $i => $status)
                                                                @php
                                                                    $statusCount =
                                                                        $application[$status->item_code] ?? 0;
                                                                    $total = $application['total'] ?? 0;
                                                                    $progress =
                                                                        $total > 0
                                                                            ? round(($statusCount / $total) * 100)
                                                                            : 0;
                                                                    if ($status->item_name == 'Disposed') {
                                                                        $applicationRoute = route(
                                                                            'applications.disposed',
                                                                            [
                                                                                'status' => Crypt::encrypt(
                                                                                    $status->item_code,
                                                                                ),
                                                                                'applicationType' => $key,
                                                                            ],
                                                                        );
                                                                        $applicationTypeRoute = route(
                                                                            'applications.disposed',
                                                                            [
                                                                                'status' => '',
                                                                                'applicationType' => $key,
                                                                            ],
                                                                        );
                                                                    } else {
                                                                        $applicationRoute = route(
                                                                            'admin.applications',
                                                                            [
                                                                                'status' => Crypt::encrypt(
                                                                                    $status->item_code,
                                                                                ),
                                                                                'applicationType' => $key,
                                                                            ],
                                                                        );
                                                                        $applicationTypeRoute = route(
                                                                            'admin.applications',
                                                                            [
                                                                                'status' => '',
                                                                                'applicationType' => $key,
                                                                            ],
                                                                        );
                                                                    }
                                                                @endphp
                                                                @if ($i === 0)
                                                                    <li class="d-flex align-items-end w-100 mb-2">
                                                                        <a href="{{ $applicationTypeRoute }}"
                                                                            class="btn btn-primary ms-auto btn-sm"
                                                                            style="float: right;">View
                                                                            All</a>
                                                                    </li>
                                                                @endif
                                                                <li>
                                                                    <a href="{{ $applicationRoute }}">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <span
                                                                                class="progress-title">{{ $status->item_name }}</span>
                                                                            <span class="progress-result"
                                                                                id="{{ $key }}-{{ $status->item_code }}">{{ $statusCount }}</span>
                                                                        </div>
                                                                        <div class="progress mb-4" style="height:7px;">
                                                                            <div class="progress-bar" role="progressbar"
                                                                                style="width: {{ $progress }}%"
                                                                                aria-valuenow="{{ $progress }}"
                                                                                aria-valuemin="0" aria-valuemax="100">
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    </div>
                                                </div>
                                            @else
                                                @if ($key == 'nocDataByDemand')
                                                    @php
                                                        $key = 'noc';
                                                    @endphp
                                                    <div class="tab-pane fade" id="v-pills-noc" role="tabpanel"
                                                        aria-labelledby="v-pills-noc-tab">
                                                        <div class="col-12">
                                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link active" id="pills-home-tab"
                                                                        data-bs-toggle="pill" data-bs-target="#pills-home"
                                                                        type="button" role="tab"
                                                                        aria-controls="pills-home"
                                                                        aria-selected="true">With Demand -
                                                                        <b>{{ $application['with_demand_count'] }}</b></button>
                                                                </li>
                                                                <li class="nav-item" role="presentation">
                                                                    <button class="nav-link" id="pills-profile-tab"
                                                                        data-bs-toggle="pill"
                                                                        data-bs-target="#pills-profile" type="button"
                                                                        role="tab" aria-controls="pills-profile"
                                                                        aria-selected="false">Without Demand -
                                                                        <b>{{ $application['without_demand_count'] }}</b></button>
                                                                </li>
                                                            </ul>
                                                            <div class="tab-content" id="pills-tabContent">
                                                                <div class="tab-pane fade show active" id="pills-home"
                                                                    role="tabpanel" aria-labelledby="pills-home-tab">
                                                                    <ul class="progress-report">
                                                                        @foreach ($statusList as $i => $status)
                                                                            @php
                                                                                $statusCount =
                                                                                    $application[
                                                                                        'with_demand_status_wise'
                                                                                    ][$status->item_code] ?? 0;
                                                                                $total =
                                                                                    $application[
                                                                                        'with_demand_status_wise'
                                                                                    ]['total'] ?? 0;
                                                                                $progress =
                                                                                    $total > 0
                                                                                        ? round(
                                                                                            ($statusCount / $total) *
                                                                                                100,
                                                                                        )
                                                                                        : 0;
                                                                                if ($status->item_name == 'Disposed') {
                                                                                    $applicationRoute = route(
                                                                                        'applications.disposed',
                                                                                        [
                                                                                            'status' => Crypt::encrypt(
                                                                                                $status->item_code,
                                                                                            ),
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'with_demand',
                                                                                        ],
                                                                                    );
                                                                                    $applicationTypeRoute = route(
                                                                                        'applications.disposed',
                                                                                        [
                                                                                            'status' => '',
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'with_demand',
                                                                                        ],
                                                                                    );
                                                                                } else {
                                                                                    $applicationRoute = route(
                                                                                        'admin.applications',
                                                                                        [
                                                                                            'status' => Crypt::encrypt(
                                                                                                $status->item_code,
                                                                                            ),
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'with_demand',
                                                                                        ],
                                                                                    );
                                                                                    $applicationTypeRoute = route(
                                                                                        'admin.applications',
                                                                                        [
                                                                                            'status' => '',
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'with_demand',
                                                                                        ],
                                                                                    );
                                                                                }
                                                                            @endphp
                                                                            @if ($i === 0)
                                                                                <li
                                                                                    class="d-flex align-items-end w-100 mb-2">
                                                                                    <a href="{{ $applicationTypeRoute }}"
                                                                                        class="btn btn-primary ms-auto btn-sm"
                                                                                        style="float: right;">View
                                                                                        All</a>
                                                                                </li>
                                                                            @endif
                                                                            <li>
                                                                                <a href="{{ $applicationRoute }}">
                                                                                    <div
                                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                                        <span
                                                                                            class="progress-title">{{ $status->item_name }}</span>
                                                                                        <span class="progress-result"
                                                                                            id="{{ $key }}-{{ $status->item_code }}">{{ $statusCount }}</span>
                                                                                    </div>
                                                                                    <div class="progress mb-4"
                                                                                        style="height:7px;">
                                                                                        <div class="progress-bar"
                                                                                            role="progressbar"
                                                                                            style="width: {{ $progress }}%"
                                                                                            aria-valuenow="{{ $progress }}"
                                                                                            aria-valuemin="0"
                                                                                            aria-valuemax="100">
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                                <div class="tab-pane fade" id="pills-profile"
                                                                    role="tabpanel" aria-labelledby="pills-profile-tab">
                                                                    <ul class="progress-report">
                                                                        @foreach ($statusList as $i => $status)
                                                                            @php
                                                                                $statusCount =
                                                                                    $application[
                                                                                        'without_demand_status_wise'
                                                                                    ][$status->item_code] ?? 0;
                                                                                $total =
                                                                                    $application[
                                                                                        'without_demand_status_wise'
                                                                                    ]['total'] ?? 0;
                                                                                $progress =
                                                                                    $total > 0
                                                                                        ? round(
                                                                                            ($statusCount / $total) *
                                                                                                100,
                                                                                        )
                                                                                        : 0;
                                                                                if ($status->item_name == 'Disposed') {
                                                                                    $applicationRoute = route(
                                                                                        'applications.disposed',
                                                                                        [
                                                                                            'status' => Crypt::encrypt(
                                                                                                $status->item_code,
                                                                                            ),
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'without_demand',
                                                                                        ],
                                                                                    );
                                                                                    $applicationTypeRoute = route(
                                                                                        'applications.disposed',
                                                                                        [
                                                                                            'status' => '',
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'without_demand',
                                                                                        ],
                                                                                    );
                                                                                } else {
                                                                                    $applicationRoute = route(
                                                                                        'admin.applications',
                                                                                        [
                                                                                            'status' => Crypt::encrypt(
                                                                                                $status->item_code,
                                                                                            ),
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'without_demand',
                                                                                        ],
                                                                                    );
                                                                                    $applicationTypeRoute = route(
                                                                                        'admin.applications',
                                                                                        [
                                                                                            'status' => '',
                                                                                            'applicationType' => $key,
                                                                                            'demandType' =>
                                                                                                'without_demand',
                                                                                        ],
                                                                                    );
                                                                                }
                                                                            @endphp
                                                                            @if ($i === 0)
                                                                                <li
                                                                                    class="d-flex align-items-end w-100 mb-2">
                                                                                    <a href="{{ $applicationTypeRoute }}"
                                                                                        class="btn btn-primary ms-auto btn-sm"
                                                                                        style="float: right;">View
                                                                                        All</a>
                                                                                </li>
                                                                            @endif
                                                                            <li>
                                                                                <a href="{{ $applicationRoute }}">
                                                                                    <div
                                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                                        <span
                                                                                            class="progress-title">{{ $status->item_name }}</span>
                                                                                        <span class="progress-result"
                                                                                            id="{{ $key }}-{{ $status->item_code }}">{{ $statusCount }}</span>
                                                                                    </div>
                                                                                    <div class="progress mb-4"
                                                                                        style="height:7px;">
                                                                                        <div class="progress-bar"
                                                                                            role="progressbar"
                                                                                            style="width: {{ $progress }}%"
                                                                                            aria-valuenow="{{ $progress }}"
                                                                                            aria-valuemin="0"
                                                                                            aria-valuemax="100">
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div> --}}
                @endif
                <!-- commeted by anil for new UI on 04-06-2025 -->
                <!-- <div class="col-lg-3 col-12">
                    <div class="col-lg-12 col-12" style="margin-bottom: 0px; height:100%">
                        <div class="card purplecard public_service" style="margin-bottom: 0px;">
                            <h4 class="pubser-title"><a href="{{ route('applicantNewProperties') }}"
                                    style="color: inherit">Public Services:
                                    <span id="publicServiceCount">{{ $grievencesCount + $appointmentCount }}</span></a>
                            </h4>
                            <div class="card-body">
                                <div class="dashboard-card-view">
                                    <div class="grievance-card-item">
                                        <a href="{{ route('grievance.index') }}">
                                            <div class="public-services-content">
                                                <div class="services-label">
                                                    <img src="{{ asset('assets/images/WhyGrievances.svg') }}"
                                                        alt="Grievances" class="grievance-icon">
                                                    <h4>Grievances</h4>
                                                </div>
                                                <div class="services-count">
                                                    <h4 class="services_count_text"><span
                                                            id="appointmentCount">{{ $grievencesCount }}</span></h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="grievance-card-item">
                                        <a href="{{ route('appointments.index') }}">
                                            <div class="public-services-content">
                                                <div class="services-label">
                                                    <img src="{{ asset('assets/images/Schedule.svg') }}"
                                                        alt="Appointments">
                                                    <h4>Appointments</h4>
                                                </div>
                                                <div class="services-count">
                                                    <h4 class="services_count_text"><span
                                                            id="grievencesCount">{{ $appointmentCount }}</span></h4>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- commeted end by anil for new UI on 04-06-2025 -->
               
            </div>
        </div>
        @include('include.alerts.ajax-alert')
    @endsection

    @section('footerScript')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>

             $(document).ready(function () {

                $('.dashboard-tabs').on('click', function () {

                    const target = $(this).attr('data-target');
                    const isActive = $(this).hasClass('active');

                    // stop any running animations
                    $('.card-content-section').stop(true, true);

                    // close everything
                    $('.dashboard-tabs').removeClass('active');
                    $('.card-content-section').slideUp(200);

                    // if clicked card was NOT active → open it
                    if (!isActive) {
                        $(this).addClass('active');
                        $(target).slideDown(250);
                    }
                    // else: toggle off (already closed)
                });

            })


            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth(); // Jan = 0, Apr = 3

            let startYear, endYear;

            // Indian Financial Year: April (3) to March (2)
            if (currentMonth >= 3) {
                startYear = currentYear;
                endYear = currentYear + 1;
            } else {
                startYear = currentYear - 1;
                endYear = currentYear;
            }

            document.getElementById("revenueYear").textContent =
                `Revenue Details FY ${startYear}-${endYear} (₹ in Cr)`;


            document.getElementById('switchUserForm').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to switch to act as a CDV user.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f0ad4e', // Bootstrap warning color
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, switch user',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // submit the form if confirmed
                    }
                });
            });
        </script>
        <script>
            $('#select-filter').change(function() {
                let selectedOption = $(this).val();
                if (selectedOption != "") {
                    getFilterDataforSelectedOption(selectedOption);
                    $('#select-filter option:first').text('Remove Filter').val('');
                } else {
                    let allValues = $('#select-filter option').map(function() {
                        if ($(this).val() != "")
                            return $(this).val();
                    }).get();
                    getFilterDataforSelectedOption(allValues);
                    $('#select-filter option:first').text('Filter by section').val('');
                }
            })

            function getFilterDataforSelectedOption(values) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('dashbordSectionFilter') }}",
                    data: {
                        filter: values,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#totalAppCount').html(response.totalAppCount);
                            let totalKeys = Object.keys(response.statusWiseCounts);
                            totalKeys.forEach(tk => {
                                $('#total-' + tk).html(response.statusWiseCounts[tk]);
                            })
                            // Comment given below code because we have changed the backend response for fetching data because we have to make dynamic tab listing for all applications - Lalit Tiwari (21/april/2025)
                            /* let mutationKeys = Object.keys(response.mutataionData);
                            mutationKeys.forEach(mk => {
                                $('#mutation-' + mk).html(response.mutataionData[mk]);
                            })
                            let lucKeys = Object.keys(response.lucData);
                            lucKeys.forEach(lk => {
                                $('#luc-' + lk).html(response.lucData[lk]);
                            });

                            let conversionKeys = Object.keys(response.conversionData);
                            conversionKeys.forEach(ck => {
                                $('#conversion-' + ck).html(response.conversionData[ck]);
                            }) */

                            // Add given below code to set application status count through filter by section - Lalit Tiwari (21/April/2025)
                            let mutationKeys = Object.keys(response.applicationData.mutation);
                            mutationKeys.forEach(mk => {
                                $('#mutation-' + mk).html(response.applicationData.mutation[mk]);
                            })
                            let lucKeys = Object.keys(response.applicationData.luc);
                            lucKeys.forEach(lk => {
                                $('#luc-' + lk).html(response.applicationData.luc[lk]);
                            });

                            let conversionKeys = Object.keys(response.applicationData.conversion);
                            conversionKeys.forEach(ck => {
                                $('#conversion-' + ck).html(response.applicationData.conversion[ck]);
                            })

                            let doaKeys = Object.keys(response.applicationData.doa);
                            doaKeys.forEach(ck => {
                                $('#doa-' + ck).html(response.applicationData.doa[ck]);
                            })

                            let nocKeys = Object.keys(response.applicationData.noc);
                            nocKeys.forEach(ck => {
                                $('#noc-' + ck).html(response.applicationData.noc[ck]);
                            })

                            //registration
                            let registrationKeys = Object.keys(response.registrationData);
                            registrationKeys.forEach(rk => {
                                if($('#reg-' + rk).length > 0)
                                    $('#reg-' + rk).html(response.registrationData[rk]);
                            })
                            //new properties
                            let newPropKeys = Object.keys(response.newPropertyData);
                            newPropKeys.forEach(npk => {
                                $('#new-prop-' + npk).html(response.newPropertyData[npk]);
                            })

                            //public services
                            $('#grievencesCount').html(response.grievencesCount);
                            $('#appointmentCount').html(response.appointmentCount);
                            $('#publicServiceCount').html(response.grievencesCount + response.appointmentCount);


                        } else {
                            showError(response.details);
                        }
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.message) {
                            showError(response.responseJSON.message)
                        }
                    }
                })
            }

            function handleRegistrationClick(status=null){
                url = status ? "{{ route('regiserUserListings', ['status' => '__STATUS__']) }}".replace('__STATUS__',status): "{{ route('regiserUserListings') }}",
                window.open(url, '_blank');   // open in new tab
            }
            function handleApplicationClick(status = null, applicationType = null,decyptedStatus = null,demandType= null) {
            
                let url;
                if(decyptedStatus == 'APP_APR' || decyptedStatus == 'APP_REJ'){
                     url = "{{ route('applications.disposed') }}";
                } else {
                     url = "{{ route('admin.applications') }}";
                }
                let params = [];

                if (status) {
                    params.push('status=' + encodeURIComponent(status));
                }

                if (applicationType) {
                    params.push('applicationType=' + encodeURIComponent(applicationType));
                }
                if (demandType) {
                    params.push('demandType=' + encodeURIComponent(demandType));
                }

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.open(url, '_blank');   // open in new tab
            }

    function getApplicationSummary(filter = null)
    {
        
        if(filter === ''){
            filter = null
        }
        let rows = '';
        $('#pendAppTotal .card-body ul').html('');
        let sectionIds = @json($sections->pluck('id')->toArray())

        let url = '{{route("dashboardApplicationSummary")}}' ;
        $.ajax({
            type:'get',
            url: url,
            data: {
                dateFilter: filter,
                pending : true,
                sectionIds : sectionIds
            },
            success: function(res){
                res.rows.forEach(r => {
                    let service = r.service_name.toLowerCase();
                    if(service.indexOf(' ') > -1)
                    {
                        service = r.service_code.toLowerCase();
                    }
                    rows += `
                    <li class="d-flex align-items-center justify-content-between" onclick="handleApplicationClick(${null}, '${service}')">
                                        <div class="title-text">${r.service_name}</div>
                                        <div class="title-count">${r.new + r.progress}</div>
                                    </li>
                    `;
                });
                /** 
                 * <tr>
                        <td>${r.service_name}</td>
                        <td><a target="_blank" href="/applicationSummaryDetails?status=APP_NEW,APP_PEN&service=${r.service_code}">${r.new}</a></td>
                        <td><a target="_blank" href="/applicationSummaryDetails?status=APP_IP,APP_OBJ,APP_HOLD&service=${r.service_code}">${r.progress}</a></td>
                        <td><a target="_blank" href="/applicationSummaryDetails?status=APP_APR,APP_REJ,APP_CAN&service=${r.service_code}">${r.disposed}</a></td>
                    </tr>
                */
                $('#pendAppTotal .card-body ul').html(rows);
                /* $('#summary-total-new').html(res.totals.new);
                $('#summary-total-progress').html(res.totals.progress);
                $('#summary-total-disposed').html(res.totals.disposed); */
            }
        })
    }





    $(document).on("click",".app-query-link",function(e) {
	 	e.preventDefault();
        let selectedService = $(this).data('service');  
        let selectedStatus = $(this).data('status');  
        let startDate = $("#startDate").val();
        let endDate = $("#endDate").val();
        let request = {};       
        if (selectedService !== undefined) {
            request.service = selectedService;
        }
        if (selectedStatus !== undefined) {
            //request.status = statusKeyArray[selectedStatus];
            request.status = selectedStatus;
        }
        if (startDate !== undefined) {
            request.start = startDate;
        }
        if (endDate !== undefined) {
            request.end = endDate;
        }        
        let queryString = new URLSearchParams(request).toString(); 
       // console.log(queryString);
        //alert(queryString);
        //return false;   
    	let encoded = btoa(queryString);
    	let url = "{{ route('paymentSummaryDetails') }}" + "?data=" + encoded;
    	window.open(url, "_blank");
		// let url = "{{ route('paymentSummaryDetails') }}" + '?' + new URLSearchParams(request).toString();
		///////   window.open(url, "_blank");
    })    
	    $("#btn-reset-filter").click(function(){
	    	  window.location.href = "{{ route('paymentSummary') }}";
	    })

            
        </script>
    @endsection
