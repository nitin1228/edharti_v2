@extends('layouts.app')

@section('title', 'Applications Summary')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Applications</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>

                <li class="breadcrumb-item">Application</li>
                <!-- <li class="breadcrumb-item active" aria-current="page">History</li> -->
                <li class="breadcrumb-item active" aria-current="page">All Applications</li>
            </ol>
        </nav>
    </div>
</div>
<hr>
<div class="container-fluid general-widget g-0">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="colonyName" class="form-label">Filter Data</label>
                            <select class="form-select" name="section_id" id="section_id" aria-label="Section Id" class="filter-input">
                                <option value="">Select Section</option>
                                @forelse ( $sections as $section)
                                <option value="{{$section->id}}">{{$section->name}}</option>
                                @empty

                                @endforelse
                                {{-- <option value="2">Property Section-III</option> --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="startDate" class="form-label">Start Date:</label>
                                    <input type="text" class="form-control" id="startDate" class="datepicker" autocomplete="off" class="filter-input">
                                </div>
                                <div class="col-md-6">
                                    <label for="endDate" class="form-label">End Date:</label>
                                    <input type="text" class="form-control" id="endDate" class="datepicker" autocomplete="off" class="filter-input">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex justify-content-start gap-4 col-lg-12">
                                <button type="button" class="btn btn-primary" id="btn-apply-filter">Apply</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-header rounded-0 text-center">
                            <h5 class="mt-2">
                                <a href="javascript:;" class="app-query-link">Total Applications:
                                    <span id="app-total-count">{{$totalApplications}}</span>
                                </a>
                            </h5>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-primary b-r-4 card-body">
                                    <a href="javascript:;" class="app-query-link" data-status="received">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-user-plus"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Applications Received</span>
                                                <h4 class="mb-0 counter" id="app-received-count">{{$applicationsReceived}}</h4>
                                                <i class="fa-solid fa-solid fa-user-plus"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-light-green b-r-4 card-body">
                                    <a href="javascript:;" class="app-query-link" data-status="pending">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Pending</span>
                                                <h4 class="mb-0 counter" id="app-pending-count">{{$newOrPendingApplications}}</h4>
                                                <i class="fa-solid fa-solid fa-bars-progress"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-deer b-r-4 card-body">
                                    <a href="javascript:;" class="app-query-link" data-status="disposed">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Disposed</span>
                                                <h4 class="mb-0 counter" id="app-disposed-count">{{$totalDisposedApplicationCount}}</h4>
                                                <i class="fa-solid fa-solid fa-trash-arrow-up"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-deer b-r-4 card-body">
                                    <a href="javascript:;" class="app-query-link" data-status="approved">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-thumbs-up"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Approved</span>
                                                <h4 class="mb-0 counter" id="app-approved-count">{{$approvedApplicationCount}}</h4>
                                                <i class="fa-solid fa-solid fa-thumbs-up"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-deer b-r-4 card-body">
                                    <a href="javascript:;" class="app-query-link" data-status="rejected">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-thumbs-down"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Rejected</span>
                                                <h4 class="mb-0 counter" id="app-rejected-count">{{$rejectedApplicationCount}}</h4>
                                                <i class="fa-solid fa-solid fa-thumbs-down"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-4 col-lg-6">
                            <div class="card o-hidden border-0">
                                <div class="bg-deer b-r-4 card-body">
                                    <a href="javascript:;" class="app-query-link" data-status="cancelled">
                                        <div class="widget-media">
                                            <div class="align-self-center text-center widget-media-icon">
                                                <i class="fa-solid fa-solid fa-close"></i>
                                            </div>
                                            <div class="widget-media-body">
                                                <span class="m-0">Cancelled</span>
                                                <h4 class="mb-0 counter" id="app-cancelled-count">{{$canceledApplicationCount}}</h4>
                                                <i class="fa-solid fa-solid fa-close"></i>
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

    </div>

    @php
    $services = [
    'CONVERSION'=> 'Conversion',
    'LUC'=>'Land Use Change (LUC)',
    'SUB_MUT'=>'Substitution/Mutation',
    'NOC'=>'NOC',
    'DOA'=>'Deed of Apartment'
    ];
    @endphp

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title">Submitted Applications</h5>
                    <div class="table-responsive mt-2 mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Service Type</th>
                                    <th>Applications Submitted</th>
                                    <th>Applicatoins Received</th>
                                    <th>Applications Disposed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $key=>$service)
                                <tr>
                                    <td id="submitted_service_{{$key}}" class="submitted-data">{{$service}}</td>
                                    <td id="submitted_total_{{$key}}" class="submitted-data">
                                        <a href="javascript:;" class="app-query-link" data-service="{{$key}}">
                                        {{isset($serviceTypeWiseApplicationStatus[$service]) ? $serviceTypeWiseApplicationStatus[$service]['total']:0}}
                                        </a>
                                    </td>
                                    <td id="submitted_received_{{$key}}" class="submitted-data">
                                        <a href="javascript:;" class="app-query-link" data-status="received" data-service="{{$key}}">
                                            {{isset($serviceTypeWiseApplicationStatus[$service]) ? $serviceTypeWiseApplicationStatus[$service]['total'] - (
                                                ($serviceTypeWiseApplicationStatus[$service]['New'] ?? 0) + 
                                                ($serviceTypeWiseApplicationStatus[$service]['Pending'] ?? 0)
                                            ):0}}
                                        </a>
                                    </td>
                                    <td id="submitted_disposed_{{$key}}" class="submitted-data">
                                        <a href="javascript:;" class="app-query-link" data-status="disposed" data-service="{{$key}}">
                                            {{isset($serviceTypeWiseApplicationStatus[$service]) ?
                                                ($serviceTypeWiseApplicationStatus[$service]['Rejected'] ?? 0) + 
                                                ($serviceTypeWiseApplicationStatus[$service]['Approved'] ?? 0):0
                                            }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="card-title">Disposed Applications</h5>
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Service Type</th>
                                    <th>Disposed</th>
                                    <th>Approved</th>
                                    <th>Rejected</th>
                                    <th>Cancelled</th>
                                </tr>
                            </thead>
                            @php
                            $disposedStatusArray = ['Approved', 'Rejected', 'Cancelled'];
                            @endphp
                            <tbody>
                                @foreach ($services as $key=>$service)
                                <tr>
                                    <td id="disposed_service_{{$key}}" class="disposed-data">{{$service}}</td>
                                    <td id="disposed_total_{{$key}}" class="disposed-data">
                                        <a href="javascript:;" class="app-query-link" data-status="disposed" data-service="{{$key}}">
                                            {{isset($serviceTypeWiseDisposeStatus[$service]) ? $serviceTypeWiseDisposeStatus[$service]['total']:0}}
                                        </a>
                                    </td>
                                    @foreach ($disposedStatusArray as $item)
                                    <a href="javascript:;" class="app-query-link" data-status="{{strtolower($item)}}" data-service="{{$key}}">
                                        <td id="disposed_{{$key}}_{{strtolower($item)}}" class="disposed-data">{{(isset($serviceTypeWiseDisposeStatus[$service]) && isset($serviceTypeWiseDisposeStatus[$service][$item])) ? $serviceTypeWiseDisposeStatus[$service][$item] : 0}}</td>
                                    </a>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card widget-card">
                <div class="card-body">
                    <h5 class="card-title">All Applications</h5>
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered" id="tab-all-applications">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Property Id</th>
                                    <th>Application No</th>
                                    <th>Application type</th>
                                    <th>Submit Date</th>
                                    <th>Status</th>
                                    <th>Dispose Date</th>
                                    <th>Days for disposal</th>
                                    <!-- <th>Application Movememnt</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applications as $app)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$app->applicationData?->old_property_id??''}}</td>
                                    <td>{{$app->application_no}}</td>
                                    <td>{{getServiceNameById($app->service_type)}}</td>
                                    <td>{{date('d-m-Y',strtotime($app->created_at))}}</td>
                                    <td>{{getServiceNameById($app->status)}}</td>
                                    <td>{{!is_null($app->disposed_at) ? date('d-m-Y', strtotime($app->disposed_at)):'N/A'}}</td>
                                    <td>{{!is_null($app->disposed_at) ? round((strtotime($app->disposed_at) - strtotime($app->created_at)) / (60 * 60 * 24)).' days':'N/A'}}</td>
                                    <!-- <td></td> -->
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8">
                                        <h3>No Data to Display</h3>
                                    </td>
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

@endsection

@section('footerScript')
<script>
    let statusKeyArray = {
        'received': 'APP_IP, APP_OBJ, APP_APR, APP_REJ, APP_CAN, APP_HOLD', //all applications other than new, pending, withdrawn
        'pending': 'APP_NEW, APP_PEN',
        'disposed': 'APP_APR, APP_REJ, APP_CAN',
        'approved': 'APP_APR',
        'rejected': 'APP_REJ',
        'cancelled': 'APP_CAN'
    }


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

    $('#btn-apply-filter').click(function() {
        let sectionId = $('#section_id').val();
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let request = {};
        if (sectionId !== '') {
            request.section_id = sectionId;
        }
        if (startDate !== '') {
            request.date_from = startDate;
        }
        if (endDate !== '') {
            request.date_to = endDate;
        }
        console.log(request);
        let services = @json($services);
        $.ajax({
            type: "GET",
            data: request,
            success: function(response) {
                if (response.success) {
                    let totalAPplications = response.data.totalApplications ?? 0;
                    let applicationsReceived = response.data.applicationsReceived ?? 0;
                    let pendingApplications = response.data.newOrPendingApplications ?? 0;
                    let disposedApplications = response.data.totalDisposedApplicationCount ?? 0;
                    let approvedApplications = response.data.approvedApplicationCount ?? 0;
                    let rejectedApplications = response.data.rejectedApplicationCount ?? 0;
                    let cancelledApplications = response.data.canceledApplicationCount ?? 0;
                    $('#app-total-count').html(totalAPplications);
                    $('#app-received-count').html(applicationsReceived);
                    $('#app-pending-count').html(pendingApplications);
                    $('#app-disposed-count').html(disposedApplications);
                    $('#app-approved-count').html(approvedApplications);
                    $('#app-rejected-count').html(rejectedApplications);
                    $('#app-cancelled-count').html(cancelledApplications);
                    $('.submitted-data, .disposed-data').html(0);
                    console.log(services)
                    Object.entries(services).forEach(([key, label]) => {
                        let submittedServiceNameTarget = $('#submitted_service_' + key);
                        let submittedServiceName = label;
                        submittedServiceNameTarget.html(submittedServiceName);
                        let submittedServiceTotalTarget = $('#submitted_total_' + key);
                        let serviceTypeWiseApplicationStatus = response.data.serviceTypeWiseApplicationStatus;
                        let submittedServiceTotalvalue = serviceTypeWiseApplicationStatus[label]?.total ?? 0;
                        submittedServiceTotalTarget.html(submittedServiceTotalvalue);
                        let submittedServiceReceivedTarget = $('#submitted_received_' + key);
                        let submittedServiceReceivedApplications = serviceTypeWiseApplicationStatus[label]?.total ?
                            serviceTypeWiseApplicationStatus[label].total -
                            (
                                (serviceTypeWiseApplicationStatus[label]?.New ?? 0) +
                                (serviceTypeWiseApplicationStatus[label]?.Pending ?? 0)
                            ) :
                            0;
                        submittedServiceReceivedTarget.html(submittedServiceReceivedApplications);
                        let submittedServiceDisposedTarget = $('#submitted_disposed_' + key);

                        let submittedServiceDisposedApplications = serviceTypeWiseApplicationStatus[label] ?
                            (serviceTypeWiseApplicationStatus[label]?.Rejected ?? 0) +
                            (serviceTypeWiseApplicationStatus[label]?.Approved ?? 0) :
                            0;
                        submittedServiceDisposedTarget.html(submittedServiceDisposedApplications);

                        let disposedServiceNameTarget = $('#disposed_service_' + key);
                        let disposedServiceName = label;
                        disposedServiceNameTarget.html(disposedServiceName);
                        let disposedServiceTotalTarget = $('#disposed_total_' + key);
                        let serviceTypeWiseDisposeStatus = response.data.serviceTypeWiseDisposeStatus;
                        let disposedServiceTotalvalue = serviceTypeWiseDisposeStatus[label]?.total ?? 0;
                        disposedServiceTotalTarget.html(disposedServiceTotalvalue);
                        let disposedServiceApprovedTarget = $('#disposed_' + key + '_approved');
                        let disposedServiceApprovededApplications = serviceTypeWiseDisposeStatus[label]?.Approved ?? 0;
                        disposedServiceApprovedTarget.html(disposedServiceApprovededApplications);
                        let disposedServiceRejectedTarget = $('#disposed_' + key + '_rejected');
                        let disposedServiceRejectedApplications = serviceTypeWiseDisposeStatus[label]?.Rejected ?? 0;
                        disposedServiceRejectedTarget.html(disposedServiceRejectedApplications);
                        let disposedServiceCancelledTarget = $('#disposed_' + key + '_cancelled');
                        let disposedServiceCancelledApplications = serviceTypeWiseDisposeStatus[label]?.Cancelled ?? 0;
                        disposedServiceCancelledTarget.html(disposedServiceCancelledApplications);
                    });

                    let tbody = $('#tab-all-applications tbody');
                    tbody.empty();
                    if (response.data.applications.length > 0) {
                        let rows = response.data.applications;
                        rows.forEach((app, index) => {
                            let createdAt = new Date(app.created_at);
                            let createdDate = formatDateToDDMMYYYY(createdAt);
                            let daysToDispose = 'N/A';
                            if (app.disposed_at) {
                                daysToDispose = (Math.floor((new Date(app.disposed_at) - (new Date(app.created_at))) / (1000 * 24 * 60 * 60))) + ' day' + (daysToDispose == 1 ? '' : 's');
                            }
                            tbody.append(`<tr>
                            <td>${index+1}</td>
                            <td>${app.oldProperty}</td>
                            <td>${response.data.serviceTypes[app.service_type]}</td>
                            <td>${app.application_no}</td>
                            <td>${createdDate}</td>
                            <td>${response.data.statusList[app.status]}</td>
                            <td>${app.disposed_at ? formatDateToDDMMYYYY(app.disposed_at): 'NA'}</td>
                            <td>${daysToDispose}</td>
                            </tr>`);
                        });
                    } else {
                        tbody.append(`<tr>
                                    <td colspan="8">
                                        <h3>No Data to Display</h3>
                                    </td>
                                </tr>`);
                    }
                }
            }
        })
    })

    $('.app-query-link').click(function() {
        let selectedStatus = $(this).data('status');
        let selectedService = $(this).data('service');
        let sectionId = $('#section_id').val();
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let request = {};
        if (sectionId !== undefined && sectionId !== '') {
            request.section_id = sectionId;
        }
        if (startDate !== undefined && startDate !== '') {
            request.date_from = startDate;
        }
        if (endDate !== undefined && endDate !== '') {
            request.date_to = endDate;
        }
        if (selectedStatus !== undefined) {
            request.status = statusKeyArray[selectedStatus];
        }
        if (selectedService !== undefined) {
            request.service = selectedService;
        }
        let url = "{{ route('applicationSummaryDetails') }}" + '?' + new URLSearchParams(request).toString();
        window.open(url, "_blank");
    })
</script>
@endsection