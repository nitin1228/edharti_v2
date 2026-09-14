@extends('layouts.app')

@section('title', 'File Movement')

@section('content')
<style>
    div.dt-buttons {
        float: none !important;
        /* width: 19%; */
        width: 33%; /* chagned by anil on 28-08-2025 to fix in resposive */
    }

    div.dt-buttons.btn-group {
        margin-bottom: 20px;
    }

    div.dt-buttons.btn-group .btn {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 4px;
    }

    /* Ensure responsiveness on smaller screens */
    @media (max-width: 768px) {
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
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Application</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Applications List</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>

<hr>

<div class="card">
    <div class="card-body">
        <div class="application-movement">
            <!-- dd($fileMovement) -->
            <!-- Status -->
            <div class="movement-status-container">
                <div class="movement-status-item__status">
                    <div class="movement-status-item status--new">
                        <span class="status-circle"></span>
                        <h4 class="status-title">New</h4>
                    </div>
                    <!-- <div class="movement-status-item status--pending">
                        <span class="status-circle"></span>
                        <h4 class="status-title">Pending</h4>
                    </div> -->
                    <div class="movement-status-item status--recommend">
                        <span class="status-circle"></span>
                        <h4 class="status-title">Inprogress</h4>
                    </div>
                    <div class="movement-status-item status--object">
                        <span class="status-circle"></span>
                        <h4 class="status-title">Object</h4>
                    </div>
                    <!--<div class="movement-status-item status--underproof">
                        <span class="status-circle"></span>
                        <h4 class="status-title">Under Proof Reading</h4>
                    </div>-->
                    <div class="movement-status-item status--reject">
                        <span class="status-circle"></span>
                        <h4 class="status-title">Reject</h4>
                    </div>
                    <div class="movement-status-item status--approve">
                        <span class="status-circle"></span>
                        <h4 class="status-title">Approve</h4>
                    </div>
                    <!-- <div class="movement-status-item status--file-movement">
                        <span class="status-circle"></span>
                        <h4 class="status-title">File Movement</h4>
                    </div> -->
                </div>
            </div>

            <!-- End -->
            <div class="container-grid">
                @if(count($data->fileMovement) > 0)
            @php
                $lastMovement = $data->fileMovement[count($data->fileMovement)-1];
            @endphp
            <di class="movment-asign">
                <h6 class="mb-0 text-success">Currently assigned to: &nbsp; {{$lastMovement['assigned_to']}} <small>({{$lastMovement['assigned_to_role']}})</small> </h6>
            </div>
            {{-- <div class="row mb-2 mt-2">
                <h6>Currently assigned to: &nbsp; {{$lastMovement['assigned_to']}} <small>({{$lastMovement['assigned_to_role']}})</small> </h6>
            </div> --}}
            @endif
                <ol class="application-movement">
                    @if(count($data->fileMovement) > 0)
                    @foreach($data->fileMovement as $movement)
                    @if($movement['status'] != "In Progress")

                    <li class="{{ $movement['status'] =='New Application'? 'status__new':''}}  @if(!is_null($movement['action']))action-type--{{strtolower($movement['action'])}} @endif">
                        <div class="grid-items-status">
                            <div class="movement-status">
                                <span class="status-pin"><i class="fas fa-map-pin fa-fw"></i></span>
                                <h4 class="application-no">{{$data->application_no}}</h4>
                                @if (!is_null($movement['action']))
                                    @if (strtoupper($movement['status']) == 'OBJECTED' && $movement['assigned_by_role'] == 'Applicant')
                                        <h6 class="action-type">RE-SUBMIT</h6>
                                    @else
                                        <h6 class="action-type">{{ strtoupper($movement['status']) }}</h6>
                                    @endif
                                @else
                                    @if ($movement['assigned_by_role'] == 'Applicant')
                                        <h6 class="action-type">SUBMIT</h6>
                                    @endif
                                @endif
                                @if(!is_null($movement['remark'])) <p class="remarks"><span style="font-weight: 600;">Remarks:</span>
                                    {{$movement['remark'] }}
                                </p>@endif
                                <h5 class="officer-name">by: {{$movement['assigned_by']}}</h5>
                                <span class="application-date-time">{{$movement['created_at']}}</span>
                            </div>
                        </div>
                    </li>
                    @else
                    <li class="status__action action-type--{{strtolower($movement['action'])}}">
                        <div class="recommend-action">
                            <h6 class="action-type">{{strtoupper(str_replace('-',' ',$movement['status']))}}</h6>
                            @if(!is_null($movement['remark'])) <p class="remarks"><span style="font-weight: 600;">Remarks:</span>
                                {{$movement['remark'] }}
                            </p>@endif
                            <h5 class="officer-name">by: {{$movement['assigned_by']}} ({{$movement['assigned_by_role']}})
                                <h5 class="officer-name">to: {{$movement['assigned_to']}} ({{$movement['assigned_to_role']}})
                                </h5>
                                <span class="application-date-time">{{$movement['created_at']}}</span>
                        </div>
                    </li>
                    @endif
                    @endforeach
                    @endif
                </ol>
            </div>
            {{-- @dd($data->fileMovement) --}}
            
        </div>
    </div>
</div>

@endsection
@section('footerScript')
<script>
    const listItems = document.querySelectorAll(".application-movement > li");

    let currentRowOffsetTop = null;
    let previousRowStartIndex = 0;

    conosole.log(listItems.length)
    listItems.forEach((item, index) => {
        if ((currentRowOffsetTop === null || item.offsetTop > currentRowOffsetTop)) {
            currentRowOffsetTop = item.offsetTop;

            if (index - previousRowStartIndex > 1 && index < listItems.length - 1) {
                listItems[index - 1].classList.add("last-item-row");
            }

            previousRowStartIndex = index;
        }
    });

    /* if (listItems.length - previousRowStartIndex > 1) {
        // listItems[listItems.length - 1].classList.add("last-item-row"); //not requred at lat item nitin -  17-02-2025
    } */
</script>
@endsection