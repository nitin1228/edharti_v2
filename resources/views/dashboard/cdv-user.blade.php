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
            <div class="col-md-3"> @if(session()->has('original_user_id'))
				<a href="{{ route('restore.user') }}" class="btn btn-warning">Switch back</a>
				@endif</div>
            <div class="col-lg-6">
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
        <div class="container-fluid general-widget g-0">
            <div class="row">
                <div class="col-12">
                    <div class="card-header rounded-0">
                        <h5 class="mt-3">
                            <a href="{{ route('admin.applicationsAssignedToUser') }}"
                                style="color: inherit">Total Applications:
                                <span id="totalAppCount">{{ $totalAppCount }}</span></a>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-3 custom_card_column lavender">
                @foreach ($statusList as $i => $status)
                @if ($status->item_name != 'New')
                <div class="col mb-3">
                    <a href="{{ route('admin.applicationsAssignedToUser', ['status' => Crypt::encrypt("$status->item_code")]) }}">
                        <div class="card radius-10 border-start border-0" >
                            <div class="card-body">
                                <div class="d-flex align-items-center dashboard-cards">
                                    <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/pageless-Total.svg')}}" alt="properties">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center w-calc-56">
                                        <h4 class="my-1 text-dark view-list" >{{ $status->item_name }}</h4>
                                        <p class="mb-0">
                                            <span id="total-{{ $status->item_code }}">{{ isset($statusWiseCounts[$status->item_code]) ? $statusWiseCounts[$status->item_code] : 0 }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                @endforeach
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card-header rounded-0">
                        <h5 class="mt-3">
                            <a href="#"
                                style="color: inherit">Applications
                                <span id=""> </span></a>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-3 custom_card_column peach">
                <div class="col mb-3">
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#appMutation">
                        <div class="card-body">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/app-icon-mutation.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Substitution / Mutation</h4>
                                    <p class="mb-0">
                                        <span id="mutation-total">{{ isset($mutataionData['total']) ? $mutataionData['total'] : 0 }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card radius-10 border-start border-0 dashboard-tabs" data-target="#appConversion">
                        <div class="card-body">
                            <div class="d-flex align-items-center dashboard-cards">
                                <div class="widgets-icons-2 rounded-circle text-white mr-icons-margin"><img src="{{asset('assets/images/app-icon-conversion.svg')}}" alt="properties">
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-calc-56">
                                    <h4 class="my-1 text-dark view-list" >Conversion</h4>
                                    <p class="mb-0">
                                        <span id="conversion-total">{{ isset($conversionData['total']) ? $conversionData['total'] : 0 }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card-content-tabs">
                        <div class="card card-content-section" id="appMutation">
                            <div class="card-body">
                                <ul>
                                    @foreach ($statusList as $i => $status)
                                    <li class="d-flex align-items-center justify-content-between">
                                        <div class="title-text">{{ $status->item_name }}</div>
                                        <div class="title-count">
                                            <span id="mutation-{{ $status->item_code }}">{{ isset($mutataionData[$status->item_code]) ? $mutataionData[$status->item_code] : 0 }}</span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="card card-content-section" id="appConversion">
                            <div class="card-body">
                                <ul>
                                    @foreach ($statusList as $i => $status)
                                    <li class="d-flex align-items-center justify-content-between">
                                        <div class="title-text">{{ $status->item_name }}</div>
                                        <div class="title-count">
                                            <span id="conversion-{{ $status->item_code }}">{{ isset($conversionData[$status->item_code]) ? $conversionData[$status->item_code] : 0 }}</span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="container-fluid dashboardcards">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="col-lg-12 col-12" style="margin-bottom: 0px;">
                        <div class="card offorangecard totalApp" style="margin-bottom: 0px;">
                            <div class="card-body">
                                <div class="dashboard-card-view">
                                    <h4><a href="{{ route('admin.applicationsAssignedToUser') }}" style="color: inherit">Total Applications:
                                            <span id="totalAppCount">{{ $totalAppCount }}</span></a></h4>
                                    <div class="container-fluid">
                                        <div class="row separate-col-border">
                                            @foreach ($statusList as $i => $status)
                                            @if($status->item_name != 'New' )
                                                <div class="custom-col-col col-4 col-lg-2">
                                                    <a href="{{ route('admin.applicationsAssignedToUser', ['status' => Crypt::encrypt("]) }}"><span
                                                            class="dashboard-label">{{ $status->item_name }}:</span> <span
                                                            id="total-{{ $status->item_code }}">{{ isset($statusWiseCounts[$status->item_code]) ? $statusWiseCounts[$status->item_code] : 0 }}</span></a>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    @foreach ($statusList as $status)
                                    <div class="custom-col-col col-4 col-lg-2">
                                        <span class="status_name">{{$status->item_name}}</span> <span
                                            class="status_value">{{isset($statusWiseCounts[$status->item_code]) ?
                                            $statusWiseCounts[$status->item_code] : 0}}</span>
                                    </div>
                                    @endforeach
                                </div> 
                        </div>
                    </div>
                    </div>
                </div> 
                
                <div class="col-lg-6 col-12">
                    <div class="card greycard submutCard">
                        <div class="card-body">
                            <h4>Substitution / Mutation: <span id="mutation-total">{{ isset($mutataionData['total']) ? $mutataionData['total'] : 0 }}</span> </h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                        <div class="table-item">
                                    <span>
                                        <a href="#">{{ $status->item_name }}:</a>
                                    </span>
                                    <div class="value"><span id="mutation-{{ $status->item_code }}">{{ isset($mutataionData[$status->item_code]) ? $mutataionData[$status->item_code] : 0 }}</span></div>
                                    
                                        </div>
                                @endforeach
                                    </div>
                        </div>
                    </div>
                </div>
           
                <div class="col-lg-6 col-12">
                    <div class="card bluecard conversioncard">
                        <div class="card-body">
                            <h4>Conversion: <span id="conversion-total">{{ isset($conversionData['total']) ? $conversionData['total'] : 0 }}</span></h4>
                            <div class="styled-table">
                                @foreach ($statusList as $i => $status)
                                <div class="table-item">
                                    <span>
                                        <a href="#">{{ $status->item_name }}:</a>
                                    </span>
                                    <div class="value">
                                        <span id="conversion-{{ $status->item_code }}">{{ isset($conversionData[$status->item_code]) ? $conversionData[$status->item_code] : 0 }}</span>
                                    </div>
                                </div>
                                @endforeach
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>--}}
        @include('include.alerts.ajax-alert')
    @endsection

    @section('footerScript')
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

            });

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
                            let mutationKeys = Object.keys(response.mutataionData);
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
                            })

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
