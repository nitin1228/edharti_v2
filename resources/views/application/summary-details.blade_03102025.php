@extends('layouts.app')

@section('title', 'Applications Summary Deatils')

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
@endsection