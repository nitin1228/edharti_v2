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
        .sections-wise-property-main-div{
            height:335px !important;
            overflow-y:scroll !important;
        }

    </style>
    <div class="container-fluid">
         <!-- @if(Auth::user()->hasAnyRole('section-officer'))      
 <form action="{{ route('switch.user') }}" method="POST" id="switchUserForm">
	@csrf
	<div class="row mb-2">
	<div class="col-md-3"></div>
	<div class="col-md-2">
		<select  class="form-select" name="section" required>
			<option  value="">--Select section name--</option>
			@foreach ($sections as $section)
			<option value="{{$section->id}}">{{$section->name}}</option>
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

            @if(Auth::user()->hasAnyRole('section-officer'))
                <div class="col-lg-6 mb-3">
                    <form action="{{ route('switch.user') }}" method="POST" id="switchUserForm">
                        @csrf
                        <div class="switch-userwrap">
                            <div class="switch-select">
                                <select  class="form-select" name="section" required>
                                    <option  value="">--Select Section name--</option>
                                    @foreach ($sections as $section)
                                    <option value="{{$section->id}}">{{$section->name}}</option>
                                    @endforeach
                                </select>                            
                            </div>
                            <button type="submit" class="btn btn-warning">Switch to CDV User</button>
                        </div>
                    </form>
                </div>
                @endif 

            <div class="col-lg-6 ms-auto">
                <div class="colony-dropdown ms-auto">
                    <div>
                        <select id="select-filter" class="form-select">
                            <option value=""> Filter by section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- added by anil for new UI on 04-06-2025 -->
        <div class="container-fluid general-widget g-0">
            <div class="row">
                <div class="col-lg-8 order-lg-1 mb-4">
                    <div class="card widget-card">
                        <!-- <div class="card-header rounded-0 text-center">
                            <h5 class="mt-3">
                                <a href="{{ route('regiserUserListings') }}">Total Registrations:
                                    <span id="reg-totalCount">{{ $registrations['totalCount'] }}</span>
                                </a>
                            </h5>
                        </div> -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings') }}" class="btn btn-light-orange btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-file-pen"></i>
                                                    </span>
                                                    <span class="3d-text">Total Registrations</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="reg-totalCount">{{ $registrations['totalCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_NEW')]) }}" class="btn btn-light-skyblue btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-user-plus"></i>
                                                    </span>
                                                    <span class="3d-text">New</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="reg-newCount">{{ $registrations['newCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_PEN')]) }}" class="btn btn-bubble-sky btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-bars-progress"></i>
                                                    </span>
                                                    <span class="3d-text">In Progress</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="reg-penCount">{{ $registrations['penCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_APP')]) }}" class="btn btn-light-green btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-thumbs-up"></i>
                                                    </span>
                                                    <span class="3d-text">Approved</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="reg-appCount">{{ $registrations['appCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REJ')]) }}" class="btn btn-light-grey btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </span>
                                                    <span class="3d-text">Rejected</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="reg-rejCount">{{ $registrations['rejCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_UREW')]) }}" class="btn btn-light-yellow btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-book-open-reader"></i>
                                                    </span>
                                                    <span class="3d-text">Under Review</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total">{{ $registrations['urewCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div> --}}
                                {{-- <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REW')]) }}" class="btn btn-light-brown btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-clipboard-check"></i>
                                                    </span>
                                                    <span class="3d-text">Reviewed</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total">{{ $registrations['rewCount'] }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                        {{--<div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                    <div class="card o-hidden border-0 h-100 w-100">
                                        <div class="bg-primary b-r-4 card-body">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_NEW')]) }}">
                                                <div class="widget-media">
                                                    <div class="align-self-center text-center widget-media-icon">
                                                        <i class="fa-solid fa-user-plus"></i>
                                                    </div>
                                                    <div class="widget-media-body">
                                                        <span class="m-0">New</span>
                                                        <h4 class="mb-0 counter"><span id="reg-newCount">
                                                                {{ $registrations['newCount'] }}</span></h4>
                                                        <i class="fa-solid fa-user-plus"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                    <div class="card o-hidden border-0 h-100 w-100">
                                        <div class="bg-reddis b-r-4 card-body">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_PEN')]) }}">
                                                <div class="widget-media">
                                                    <div class="align-self-center text-center widget-media-icon"><i
                                                            class="fa-solid fa-hourglass-half"></i></div>
                                                    <div class="widget-media-body"><span class="m-0">In Progress</span>
                                                        <h4 class="mb-0 counter">
                                                            <span id="reg-penCount"> {{ $registrations['penCount'] }}
                                                            </span>
                                                        </h4>
                                                        <i class="fa-solid fa-hourglass-half"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                    <div class="card o-hidden border-0 h-100 w-100">
                                        <div class="bg-light-green b-r-4 card-body">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_APP')]) }}">
                                                <div class="widget-media">
                                                    <div class="align-self-center text-center widget-media-icon">
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                    <div class="widget-media-body">
                                                        <span class="m-0">Approved</span>
                                                        <h4 class="mb-0 counter">
                                                            <span id="reg-appCount">{{ $registrations['appCount'] }}</span>
                                                        </h4>
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                    <div class="card o-hidden border-0 h-100 w-100">
                                        <div class="bg-secondary b-r-4 card-body">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REJ')]) }}">
                                                <div class="widget-media">
                                                    <div class="align-self-center text-center widget-media-icon">
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                    <div class="widget-media-body">
                                                        <span class="m-0">Rejected</span>
                                                        <h4 class="mb-0 counter">
                                                            <span id="reg-rejCount">{{ $registrations['rejCount'] }}</span>
                                                        </h4>
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                    <div class="card o-hidden border-0 h-100 w-100">
                                        <div class="bg-yellow b-r-4 card-body">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_UREW')]) }}">
                                                <div class="widget-media">
                                                    <div class="align-self-center text-center widget-media-icon">
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                    <div class="widget-media-body">
                                                        <span class="m-0">Under Review</span>
                                                        <h4 class="mb-0 counter">
                                                            <span
                                                                id="reg-rejCount">{{ $registrations['urewCount'] }}</span>
                                                        </h4>
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div> 
                                </div>
                                 <div class="col-sm-6 col-xl-4 col-lg-6">
                                    <div class="card o-hidden border-0">
                                        <div class="bg-dark-orange b-r-4 card-body">
                                            <a
                                                href="{{ route('regiserUserListings', ['status' => Crypt::encrypt('RS_REW')]) }}">
                                                <div class="widget-media">
                                                    <div class="align-self-center text-center widget-media-icon">
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                    <div class="widget-media-body">
                                                        <span class="m-0">Reviewed</span>
                                                        <h4 class="mb-0 counter">
                                                            <span
                                                                id="reg-rejCount">{{ $registrations['rewCount'] }}</span>
                                                        </h4>
                                                        <i class="fa-solid fa-trash-arrow-up"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div> --}}
                    </div>
                </div>
                <div class="col-lg-8 order-lg-3 mb-4">
                    <div class="card widget-card">
                        {{--<div class="card-header rounded-0 text-center">
                            <h5 class="mt-3">
                                <a href="{{ route('admin.applications') }}">Total Applications:
                                    <span id="totalAppCount">{{ $totalAppCount }}</span>
                                </a>
                            </h2>
                        </div> --}}

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('admin.applications') }}" class="btn btn-light-orange btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-file-pen"></i>
                                                    </span>
                                                    <span class="3d-text">Total Applications</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="totalAppCount">{{ $totalAppCount }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                @foreach ($statusList as $i => $status)
                                    @php
                                        $additionalData = !is_null($status->additional_data)
                                            ? json_decode($status->additional_data)
                                            : null;
                                        $color = !is_null($additionalData) ? $additionalData->color : '';
                                        $icon = !is_null($additionalData) ? $additionalData->icon : '';
                                    @endphp
                                    @continue(in_array($status->item_code, ['APP_PEN', 'APP_OBJ', 'APP_HOLD', 'APP_CAN']))
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ $status->item_name == 'Disposed' ? route('applications.disposed') : route('admin.applications', ['status' => Crypt::encrypt(" $status->item_code")]) }}" class="btn {{$color}} btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid {{ $icon }}"></i>
                                                    </span>
                                                    <span class="3d-text">{{ $status->item_name }}</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="total-{{ $status->item_code }}">{{ isset($statusWiseCounts[$status->item_code]) ? $statusWiseCounts[$status->item_code] : 0 }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('admin.myapplications')}}" class="btn btn-pistachio btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-tasks"></i>
                                                    </span>
                                                    <span class="3d-text">Assigned to Me</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="total-assigned">{{ $assignedToUserCount ?? 0 }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="buttons-wrap3d">
                                        <a href="{{ route('admin.forwardedApplications')}}" class="btn btn-soft-blush btn-lg btn3d">
                                            <div class="button3d-media d-flex justify-content-between">
                                                <div class="button3d-left">
                                                    <span class="button3d-icon">
                                                        <i class="fa-solid fa-forward"></i>
                                                    </span>
                                                    <span class="3d-text">Forwarded Applications</span>
                                                </div>
                                                <div class="button3d-count">
                                                    <span class="count3d-total" id="total-assigned">{{ $forwardedApplicationCount ?? 0 }}</span>
                                                </div>
                                            </div>                                            
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--<div class="card-body">
                            <div class="row">
                                @foreach ($statusList as $i => $status)
                                    @php
                                        $additionalData = !is_null($status->additional_data)
                                            ? json_decode($status->additional_data)
                                            : null;
                                        $color = !is_null($additionalData) ? $additionalData->color : '';
                                        $icon = !is_null($additionalData) ? $additionalData->icon : '';
                                    @endphp
                                    @continue(in_array($status->item_code, ['APP_PEN','APP_OBJ','APP_HOLD', 'APP_CAN']))
                                    <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                    <div class="card o-hidden border-0 h-100 w-100">
                                            <div class="{{ $color }} b-r-4 card-body">
                                                <a
                                                    href="{{ $status->item_name == 'Disposed' ? route('applications.disposed') : route('admin.applications', ['status' => Crypt::encrypt(" $status->item_code")]) }}">
                                                    <div class="widget-media">
                                                        <div class="align-self-center text-center widget-media-icon">
                                                            <i class="fa-solid {{ $icon }}"></i>
                                                        </div>
                                                        <div class="widget-media-body">
                                                            <span class="m-0">{{ $status->item_name }}</span>
                                                            <h4 class="mb-0 counter" id="total-{{ $status->item_code }}">
                                                                {{ isset($statusWiseCounts[$status->item_code]) ? $statusWiseCounts[$status->item_code] : 0 }}
                                                            </h4>
                                                            <i class="fa-solid {{ $icon }}"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                 @if(!Auth::user()->hasAnyRole(['Joint Secretary','minister']))
                                    <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                        <div class="card o-hidden border-0 h-100 w-100">
                                            <div class="bg-assigned b-r-4 card-body">
                                                <a
                                                    href="{{ route('admin.myapplications')}}">
                                                    <div class="widget-media">
                                                        <div class="align-self-center text-center widget-media-icon">
                                                            <i class="fa-solid fa-tasks"></i>
                                                        </div>
                                                        <div class="widget-media-body">
                                                            <span class="m-0">Assigned to Me</span>
                                                            <h4 class="mb-0 counter" id="total-assigned">
                                                                {{ $assignedToUserCount ?? 0 }}
                                                            </h4>
                                                            <i class="fa-solid fa-tasks"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>        

                                    <div class="col-sm-6 col-xl-4 col-lg-6 d-flex mb-2">
                                        <div class="card o-hidden border-0 h-100 w-100">
                                            <div class="bg-info b-r-4 card-body">
                                                <a
                                                    href="{{ route('admin.forwardedApplications')}}">
                                                    <div class="widget-media">
                                                        <div class="align-self-center text-center widget-media-icon">
                                                            <i class="fa-solid fa-forward"></i>
                                                        </div>
                                                        <div class="widget-media-body">
                                                            <span class="m-0">Forwarded Applications</span>
                                                            <h4 class="mb-0 counter" id="total-assigned">
                                                                {{ $forwardedApplicationCount ?? 0 }}
                                                            </h4>
                                                            <i class="fa-solid fa-forward"></i>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>  
                               @endif                                                                                                                                                                                                         
                            </div>
                        </div>--}}

                    </div>
                </div>
                <div class="col-lg-4 col-12 order-lg-2">
                    <div class="card public_service service-seclist sections-wise-property-main-div">
                        {{-- <h4 class="pubser-title">Section{{ $sections->count() == 1 ? '' : 's' }}</h4> --}}
                        <!-- <h4 class="pubser-title">Properties in Section</h4> -->
                        <div class="card-header text-center">
                            <h5 class="mt-3">Properties in Section</h5>
                        </div>
                        <div class="card-body">
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
                <div class="col-lg-4 col-12 order-lg-4 mb-4">
                    <div class="card public_service service-seclist">
                        <div class="card-header text-center">
                            <h5 class="mt-3">
                                <a href="{{ route('applicantNewProperties') }}">
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
                    <div class="col-lg-6 col-12">
                        <div class="card radius-10">
                            <div class="d-flex tabs-progress-container">
                                <div class="nav-tabs-left-aside-dashboard">
                                    <ul class="nav nav-tabs nav-primary" role="tablist"
                                        style="display: block !important;">
                                        @foreach ($applicationData as $key => $application)
                                             @if($key != 'nocDataByDemand')
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
                                           @if($key != 'noc' && $key != 'nocDataByDemand')
                                             <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                id="v-pills-{{ $key }}" role="tabpanel"
                                                aria-labelledby="v-pills-{{ $key }}-tab">
                                                <div class="col-12">
                                                    <ul class="progress-report">
                                                        @foreach ($statusList as $i => $status)
                                                            @php
                                                                $statusCount = $application[$status->item_code] ?? 0;
                                                                $total = $application['total'] ?? 0;
                                                                $progress =
                                                                    $total > 0
                                                                        ? round(($statusCount / $total) * 100)
                                                                        : 0;
                                                                if ($status->item_name == 'Disposed') {
                                                                    $applicationRoute = route('applications.disposed', [
                                                                        'status' => Crypt::encrypt($status->item_code),
                                                                        'applicationType' => $key,
                                                                    ]);
                                                                    $applicationTypeRoute = route(
                                                                        'applications.disposed',
                                                                        [
                                                                            'status' => '',
                                                                            'applicationType' => $key,
                                                                        ],
                                                                    );
                                                                } else {
                                                                    $applicationRoute = route('admin.applications', [
                                                                        'status' => Crypt::encrypt($status->item_code),
                                                                        'applicationType' => $key,
                                                                    ]);
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
                                                @if($key == 'nocDataByDemand')
                                                @php
                                                    $key = 'noc';
                                                @endphp
                                            <div class="tab-pane fade" id="v-pills-noc" role="tabpanel" aria-labelledby="v-pills-noc-tab">
                                                <div class="col-12">
                                                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">With Demand -  <b>{{$application['with_demand_count']}}</b></button>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Without Demand -  <b>{{$application['without_demand_count']}}</b></button>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content" id="pills-tabContent">
                                                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                                                <ul class="progress-report">
                                                                    @foreach ($statusList as $i => $status)
                                                                        @php
                                                                            $statusCount = $application['with_demand_status_wise'][$status->item_code] ?? 0;
                                                                            $total = $application['with_demand_status_wise']['total'] ?? 0;
                                                                            $progress =
                                                                                $total > 0
                                                                                    ? round(($statusCount / $total) * 100)
                                                                                    : 0;
                                                                            if ($status->item_name == 'Disposed') {
                                                                                $applicationRoute = route('applications.disposed', [
                                                                                    'status' => Crypt::encrypt($status->item_code),
                                                                                    'applicationType' => $key,
                                                                                    'demandType' => 'with_demand',
                                                                                ]);
                                                                                $applicationTypeRoute = route(
                                                                                    'applications.disposed',
                                                                                    [
                                                                                        'status' => '',
                                                                                        'applicationType' => $key,
                                                                                        'demandType' => 'with_demand',
                                                                                    ],
                                                                                );
                                                                            } else {
                                                                                $applicationRoute = route('admin.applications', [
                                                                                    'status' => Crypt::encrypt($status->item_code),
                                                                                    'applicationType' => $key,
                                                                                    'demandType' => 'with_demand',
                                                                                ]);
                                                                                $applicationTypeRoute = route(
                                                                                    'admin.applications',
                                                                                    [
                                                                                        'status' => '',
                                                                                        'applicationType' => $key,
                                                                                        'demandType' => 'with_demand',
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
                                                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                                                <ul class="progress-report">
                                                                    @foreach ($statusList as $i => $status)
                                                                        @php
                                                                            $statusCount = $application['without_demand_status_wise'][$status->item_code] ?? 0;
                                                                            $total = $application['without_demand_status_wise']['total'] ?? 0;
                                                                            $progress =
                                                                                $total > 0
                                                                                    ? round(($statusCount / $total) * 100)
                                                                                    : 0;
                                                                            if ($status->item_name == 'Disposed') {
                                                                                $applicationRoute = route('applications.disposed', [
                                                                                    'status' => Crypt::encrypt($status->item_code),
                                                                                    'applicationType' => $key,
                                                                                    'demandType' => 'without_demand',
                                                                                ]);
                                                                                $applicationTypeRoute = route(
                                                                                    'applications.disposed',
                                                                                    [
                                                                                        'status' => '',
                                                                                        'applicationType' => $key,
                                                                                         'demandType' => 'without_demand',
                                                                                    ],
                                                                                );
                                                                            } else {
                                                                                $applicationRoute = route('admin.applications', [
                                                                                    'status' => Crypt::encrypt($status->item_code),
                                                                                    'applicationType' => $key,
                                                                                     'demandType' => 'without_demand',
                                                                                ]);
                                                                                $applicationTypeRoute = route(
                                                                                    'admin.applications',
                                                                                    [
                                                                                        'status' => '',
                                                                                        'applicationType' => $key,
                                                                                         'demandType' => 'without_demand',
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
                                                    </div>
                                                </div>
                                           
                                            @endif
                                            @endif
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
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
                 @haspermission('view.new.added.properties')
                    <div class="col-lg-6 col-12">
                        <div class="card addedproperties">
                            <div class="card-header text-center">
                                <h4 class="mt-3">
                                    <a href="{{ route('applicantNewProperties') }}">
                                        Additional Properties:
                                        <span id="new-prop-totalCount">{{ $newProperty['totalCount'] }}</span>
                                    </a>
                                </h4>
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


                                        {{-- <div class="item-cards-col">
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
                                        </div> --}}


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

                                        {{-- <div class="item-cards-col">
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
                                        </div> --}}


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @endhaspermission
            </div>
        </div>
        @include('include.alerts.ajax-alert')
    @endsection

    @section('footerScript')
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"> </script>
<script>
	document.getElementById('switchUserForm').addEventListener('submit', function (e) {
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
                            if(response.applicationData.mutation)
                            {
                                let mutationKeys = Object.keys(response.applicationData.mutation);
                                mutationKeys.forEach(mk => {
                                    $('#mutation-' + mk).html(response.applicationData.mutation[mk]);
                                })
                            }
                            if(response.applicationData.luc)
                            {
                                let lucKeys = Object.keys(response.applicationData.luc);
                                lucKeys.forEach(lk => {
                                    $('#luc-' + lk).html(response.applicationData.luc[lk]);
                                });
                            }
                            if(response.applicationData.conversion)
                            {
                                let conversionKeys = Object.keys(response.applicationData.conversion);
                                conversionKeys.forEach(ck => {
                                    $('#conversion-' + ck).html(response.applicationData.conversion[ck]);
                                })
                            }
                            if(response.applicationData.doa)
                            {
                                let doaKeys = Object.keys(response.applicationData.doa);
                                doaKeys.forEach(ck => {
                                    $('#doa-' + ck).html(response.applicationData.doa[ck]);
                                })
                            }
                            if(response.applicationData.mutation)
                            {
                                let nocKeys = Object.keys(response.applicationData.noc);
                                nocKeys.forEach(ck => {
                                    $('#noc-' + ck).html(response.applicationData.noc[ck]);
                                })
                            }

                            //registration
                            let registrationKeys = Object.keys(response.registrationData);
                            registrationKeys.forEach(rk => {
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
        </script>
    @endsection
