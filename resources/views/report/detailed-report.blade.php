@extends('layouts.app')

@section('title', 'Detailed Report')

@section('content')
<style>
    .filter-btn {
        border: none;
        background: none;
    }
</style>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
   <div class="breadcrumb-title pe-3">Reports</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                 <li class="breadcrumb-item active" aria-current="page">Reports</li>
                <li class="breadcrumb-item active" aria-current="page">Detailed Report</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>
<!--breadcrumb-->
<!--end breadcrumb-->
{{-- @dd($filters) --}}
<hr>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <form id="filter-form" method="get" action="{{route('detailedReport')}}">
                    <input type="hidden" name="export" value="0">
                    <div class="group-row-filters">
                        <div class="d-flex align-items-start w-btn-full flex-wrap" style="margin-left:-0.5rem">
                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker" aria-label="Land" aria-placeholder="Land" data-live-search="true" title="Land" id="land-type" name="landType">
                                    <option value="">All</option>
                                    @foreach ($landTypes[0]->items as $landType)
                                    <option value="{{$landType->id}}" {{(isset($filters['landType'] ) && $landType->id==$filters['landType'] ) ? 'selected':''}}>{{ $landType->item_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-filter="land_type" data-targets="#land-type"><i class="lni lni-cross-circle"></i></button>
                            </div>

                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker propType multipleSelect" multiple aria-label="Land Use Type" data-live-search="true" title="Land Use Type" id="property-Type" name="property_type[]">
                                    <option value="">All</option>
                                    @foreach ($propertyTypes[0]->items as $propertyType)
                                    <option value="{{$propertyType->id}}" {{(isset($filters['property_type'] ) && in_array($propertyType->id,$filters['property_type'] )) ? 'selected':''}}>{{ $propertyType->item_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-filter="property_type" data-targets='#property-Type'><i class="lni lni-cross-circle"></i></button>
                            </div>
                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker propSubType multipleSelect" multiple aria-label="Land Use Sub-Type" data-live-search="true" title="Land Use Sub-Type" id="prop-sub-type" name="property_sub_type[]">
                                    <option value="">All</option>
                                    @foreach($propertySubtypes as $st)
                                    <option value="{{$st->id}}" {{(isset($filters['property_sub_type'] ) && in_array($st->id,$filters['property_sub_type'] )) ? 'selected':''}}>{{ $st->item_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-filter="property_sub_type" data-targets='#prop-sub-type'><i class="lni lni-cross-circle"></i></button>
                            </div>

                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker multipleSelect" multiple aria-label="Land Status" data-live-search="true" title="Land Status" id="land-status" name="property_status[]">
                                    <option value="">All</option>
                                    @foreach ($propertyStatus[0]->items as $status)
                                    <option value="{{$status->id}}" {{(isset($filters['property_status'] ) && in_array($status->id,$filters['property_status'] )) ? 'selected':''}}>{{ $status->item_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-filter="land_status" data-targets='#land-status'><i class="lni lni-cross-circle"></i></button>
                            </div>
                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker colony" multiple aria-label="Search by Colony" data-live-search="true" title="Colony" id="colony_filter" name="colony[]">
                                    <option value="">All</option>
                                    @foreach ($colonyList as $colony)
                                    <option value="{{$colony->id}}" {{(isset($filters['colony'] ) && in_array($colony->id,$filters['colony'] )) ? 'selected':''}}>{{ $colony->name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-filter="colony" data-targets="#colony_filter"><i class="lni lni-cross-circle"></i></button>
                            </div>
                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker" multiple aria-label="Search by Lease Deed" data-live-search="true" title="Lease Deed" id="leaseDeed_filter" name="leaseDeed[]">
                                    <option value="">All</option>
                                    @foreach ($leaseTypes[0]->items as $leaseType)
                                    <option value="{{$leaseType->id}}" {{(isset($filters['leaseDeed'] ) && in_array($leaseType->id, $filters['leaseDeed'] )) ? 'selected':''}}>{{ $leaseType->item_name }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-targets="#leaseDeed_filter"><i class="lni lni-cross-circle"></i></button>
                            </div>
                            <div class="relative-input mb-3 ms-2">
                                <select class="selectpicker" multiple aria-label="Search by section" data-live-search="true" title="Section" id="section_filter" name="section_id[]">
                                    <option value="">All</option>
                                    @foreach ($sections as $section)
                                    <option value="{{$section->id}}" {{(isset($filters['section_id'] ) && in_array($section->id, $filters['section_id'] )) ? 'selected':''}}><?= $section->name ?></option>
                                    @endforeach
                                </select>
                                <button type="button" class="input-reset-icon" data-targets="#section_filter"><i class="lni lni-cross-circle"></i></button>
                            </div>

                        </div>
                        <div class="d-flex justify-content-end w-btn-full">
                            <div class="btn-group-filter">
                                <button type="button" class="btn btn-secondary px-5 filter-btn" onclick="resetFilters()">Reset</button>
                                <button type="submit" class="btn btn-primary px-5 filter-btn">Apply</button>
                                <!-- <button type="button" class="btn btn-info px-5 filter-btn" id="export-btn">Email</button> -->
                                <!-- <button type="button" class="btn btn-info px-5 filter-btn" id="download-btn">Download</button> -->
                                <button type="button" class="filter-btn mx-1" id="export-btn">
                                    <!-- <i class="fa fa-envelope" aria-hidden="true"></i> -->
                                    <svg height="32px" width="32px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 511.999 511.999" xml:space="preserve" fill="#000000">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <path style="fill:#116d6e;" d="M477.832,408.28H142.075c-7.691,0-13.929-6.236-13.929-13.929c0-7.693,6.237-13.929,13.929-13.929 h335.757c3.481,0,6.311-3.385,6.311-7.545V139.121c0-4.16-2.83-7.545-6.311-7.545H162.315c-3.481,0-6.311,3.385-6.311,7.545v167.826 c0,7.693-6.237,13.929-13.929,13.929c-7.691,0-13.929-6.236-13.929-13.929V139.121c0-19.521,15.328-35.402,34.168-35.402H477.83 c18.84,0,34.168,15.881,34.168,35.402v233.755C512,392.398,496.672,408.28,477.832,408.28z"></path>
                                            <path style="fill:#CFF09E;" d="M142.075,139.121L142.075,139.121c0,6.854,3.085,13.298,8.303,17.341l169.697,131.493 l169.697-131.493c5.218-4.043,8.303-10.485,8.303-17.341l0,0c0-11.86-9.062-21.474-20.24-21.474h-315.52 C151.137,117.648,142.075,127.262,142.075,139.121z"></path>
                                            <g>
                                                <path style="fill:#116d6e;" d="M320.073,301.884c-3.01,0-6.02-0.974-8.531-2.918L141.845,167.473 c-8.577-6.647-13.699-17.245-13.699-28.35c0-19.521,15.328-35.402,34.168-35.402H477.83c18.84,0,34.168,15.881,34.168,35.402 c0,11.105-5.122,21.704-13.7,28.35L328.604,298.966C326.093,300.912,323.083,301.884,320.073,301.884z M162.315,131.576 c-3.479,0-6.311,3.385-6.311,7.545c0,2.555,1.085,4.921,2.904,6.331l161.165,124.883l161.164-124.883 c1.819-1.41,2.905-3.776,2.905-6.331c0-4.16-2.832-7.545-6.311-7.545H162.315L162.315,131.576z"></path>
                                                <path style="fill:#116d6e;" d="M140.681,195.17H13.929C6.237,195.17,0,188.934,0,181.242s6.237-13.929,13.929-13.929h126.752 c7.691,0,13.929,6.236,13.929,13.929S148.372,195.17,140.681,195.17z"></path>
                                                <path style="fill:#116d6e;" d="M140.681,258.545H55.719c-7.691,0-13.929-6.236-13.929-13.929s6.237-13.929,13.929-13.929h84.962 c7.691,0,13.929,6.236,13.929,13.929S148.372,258.545,140.681,258.545z"></path>
                                                <path style="fill:#116d6e;" d="M140.681,321.921H97.509c-7.691,0-13.929-6.236-13.929-13.929s6.237-13.929,13.929-13.929h43.172 c7.691,0,13.929,6.236,13.929,13.929S148.372,321.921,140.681,321.921z"></path>
                                            </g>
                                        </g>
                                    </svg>
                                </button>
                                <button type="button" class="filter-btn mx-1" id="download-btn">
                                    <svg width="25px" height="25px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M28.781,4.405H18.651V2.018L2,4.588V27.115l16.651,2.868V26.445H28.781A1.162,1.162,0,0,0,30,25.349V5.5A1.162,1.162,0,0,0,28.781,4.405Zm.16,21.126H18.617L18.6,23.642h2.487v-2.2H18.581l-.012-1.3h2.518v-2.2H18.55l-.012-1.3h2.549v-2.2H18.53v-1.3h2.557v-2.2H18.53v-1.3h2.557v-2.2H18.53v-2H28.941Z" style="fill:#20744a;fill-rule:evenodd" />
                                        <rect x="22.487" y="7.439" width="4.323" height="2.2" style="fill:#20744a" />
                                        <rect x="22.487" y="10.94" width="4.323" height="2.2" style="fill:#20744a" />
                                        <rect x="22.487" y="14.441" width="4.323" height="2.2" style="fill:#20744a" />
                                        <rect x="22.487" y="17.942" width="4.323" height="2.2" style="fill:#20744a" />
                                        <rect x="22.487" y="21.443" width="4.323" height="2.2" style="fill:#20744a" />
                                        <polygon points="6.347 10.673 8.493 10.55 9.842 14.259 11.436 10.397 13.582 10.274 10.976 15.54 13.582 20.819 11.313 20.666 9.781 16.642 8.248 20.513 6.163 20.329 8.585 15.666 6.347 10.673" style="fill:#ffffff;fill-rule:evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                </form>

                <div class="table-responsive mt-2">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>S. No.</th>
                                <th>Property Id</th>
                                <th>Known as</th>
                                <!-- <th>File Number</th> -->
                                <!-- <th>File Number</th> -->
                                <th>Land Type</th>
                                <th>Status</th>
                                <th>Property Type</th>
                                <th>Property SubType</th>
                                <!-- <th>Section</th> -->
                                <!-- <th>Premium (₹)</th>
                                <th>Ground Rent (₹)</th> -->
                                <th>Area(Sqm)</th>
                                <th>Date Of Execution</th>
                                <th>Current Lessee Name</th>
                                <th>Transfer Date</th>
                                <th>Joint Property</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $startNum = $properties->currentPage() ? ($properties->currentPage()-1)* $properties->perPage():0;
                            @endphp
                            @forelse($properties as $prop)
                            <tr>
                                <td>{{$loop->iteration + $startNum}}</td>
                                <td>{{$prop->unique_propert_id}} <br> {{' ('.$prop->old_propert_id.')'}}</td>
                                <td>{{$prop->address}}</td>
                                {{-- <td>$prop->file_no</td> --}}
                                <td>{{$prop->landType}}</td>
                                <td>{{$prop->propertyStatus}}</td>
                                <td>{{$prop-> latestPropertyType ?? $prop->propertyType}}</td>
                                <td>{{$prop->latestPropertySubType ?? $prop->propertySubtype}}</td>
                                {{--<td>$prop->section</td>--}}
                                <td>{{round($prop->area_in_sqm,2)}}</td>
                                {{--<td>$prop->premium.'.'.$prop->premium_in_paisa</td>--}}
                                {{--<td>$prop->ground_rent</td>--}}
                                <td>{{date('d-m-Y', strtotime($prop->doe))}}</td>
                                <td>{{(!is_null($prop->current_lesse_name) && $prop->current_lesse_name !="") ? $prop->current_lesse_name :  'NA'}}</td>
                                <td>{{!is_null($prop->transferDate) ? date('d-m-Y', strtotime($prop->transferDate)) : ''}}</td>
                                <td>{{$prop->child_prop_id ?? 'No'}}</td>
                                <td>
                                    <span class="location_icon" data-bs-toggle="modal" data-bs-target="#viewMapModal" onclick="locate('{{$prop->old_propert_id}}')"><i class="lni lni-map-marker text-danger" data-toggle="tooltip" title="View Mapview"></i></span><a href="/streetview/{{$prop->old_propert_id}}" target="_blank" data-toggle="tooltip" title="View Streetview"><span class="location_icon"> <img src="{{ asset('assets/images/street-view.svg') }}" class="map-marker-icon" /> </span></a>
                                    <a href="{{route('viewPropertyDetails',$prop->id)}}" class="btn btn-primary" target="_blank">View More</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13">No Data to Display</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="row mt-2">
                    @php
                    $total = $properties->total();
                    @endphp
                    <div class="col-lg-6">Total {{$total}} {{$total != 1 ? 'proeperties': 'proeperty'}} found</div>
                    <div class="col-lg-6">

                        <div style="float: right;">{{$properties->appends(request()->input())->links()}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<div class="modal fade" id="viewMapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Property ID: <span class="propId">32525</span></h5>
                <button class="btn btn-danger d-none" id="notFoundAlert">Property not found</button>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('modals.map')
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{route('map')}}" class="btn btn-primary" target="_blank">View All Properties</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footerScript')
<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('assets/js/property-type-subtype-dropdown.js') }}"></script>
<script src="{{ asset('assets/js/map.js') }}"></script>
<!-- use version 0.20.3 -->
<script lang="javascript" src="{{asset('assets/js/xlsx.full.min.js')}}"></script>
<script>
    const wsData = [];
    $('#export-btn').click(function() {
        $('input[name="export"]').val(1);
        $('#filter-form').submit();
        setTimeout(function() {
            $('input[name="export"]').val(0);
        }, 500)
    })
    $('#download-btn').click(function() {
        $('#spinnerOverlay').show();
        const formArray = $(document.querySelector('#filter-form')).serializeArray();
        const data = {};
        console.log(formArray)
        formArray.forEach(({
            name,
            value
        }) => {
            const cleanName = name.endsWith('[]') ? name.slice(0, -2) : name;

            if (data[cleanName]) {
                if (Array.isArray(data[cleanName])) {
                    data[cleanName].push(value);
                } else {
                    data[cleanName] = [...data[cleanName], value];
                }
            } else {
                data[cleanName] = name.endsWith('[]') ? [value] : value;
            }
        });
        fetchPage(1, data);
    });

    function fetchPage(page, data) {
        let perpage = 1000;
        // const formData = new FormData(document.querySelector('#filter-form'));
        data['page'] = page;
        data['perpage'] = perpage;
        $.ajax({
            type: "get",
            url: '{{route("detailedReportExport")}}',
            data: data,
            success: response => {
                if (response.rows && response.counter) {
                    let totalPages = Math.ceil(response.counter / perpage);

                    response.rows.forEach(row => appendToexcel(row));

                    if (page < totalPages) {
                        fetchPage(page + 1, data);
                    } else {
                        downloadExel();
                    }
                }
            },
            error: response => {
                $('#spinnerOverlay').hide();

                if (response.responseJSON && response.responseJSON.message) {
                    showError(response.responseJSON.message)
                }
            }
        });
    }

    function appendToexcel(item) {
        if (wsData.length == 0) {
            wsData.push([

                'Property Id',
                'Old Property Id',
                'Child Property Id',
                'File Number',
                'Old File Number',
                'Land Type',
                'Property Status',
                'Property Type',
                'Property SubType',
                'Is Land Use Changed',
                'Latest Property Type',
                'Latest Property SubType',
                'Original Property Type',
                'Original Property SubType',
                'Section',
                'Address',
                'Premium (₹)',
                'Ground Rent (₹)',
                'Area',
                'Area in Sqm',
                'Colony',
                'Block',
                'Plot',
                'Presently Known As',
                'Lease Type',
                'Date Of Allotment',
                'Date Of Execution',
                'Date Of Expiration',
                'Date of Transfer',
                'Start Date Of GR',
                'RGR Duration',
                'First RGR Due On',
                'Last Inspection Date',
                'Last Demand Letter Date',
                'Last Demand Id',
                'Last Demand Amount',
                'Last Amount Received',
                'Last Amount Received Date',
                'Total Dues',
                'Latest Lessee Name',
                'Lessee Address',
                'Lessee Phone',
                'Lessee Email',
                'Entry By',
                'Entry At'
            ]);
        }

        wsData.push(
            [item.unique_propert_id ?? '',
                item.old_propert_id ?? '',
                item.child_prop_id ?? '',
                item.unique_file_no ?? '',
                item.file_no ?? '',
                item.landType ?? '',
                item.propertyStatus ?? '',
                item.propertyType ?? '',
                item.propertySubtype ?? '',
                item.is_land_use_changed,
                item.presentPropertyType,
                item.presentPropertySubtype,
                item.originalPropertyType,
                item.originalPropertySubType,
                item.section ?? '',
                (item.block ?? 'NA') + '/' + (item.plot_no ?? 'NA') + '/' + (item.colony ?? ''),
                item.premium + '.' + item.premium_in_paisa ?? '',
                item.ground_rent ?? '',
                item.area ?? '',
                item.area_in_sqm ?? '',
                item.colony ?? '', item.block ?? '',
                item.plot_no ?? '',
                item.presently_known_as ?? '',
                item.leaseDeed ?? '',
                item.date_of_allotment ? displayDateTime(item.date_of_allotment) : '',
                item.date_of_execution ? displayDateTime(item.date_of_execution) : '',
                item.date_of_expiration ? displayDateTime(item.date_of_expiration) : '',
                item.transferDate ? displayDateTime(item.transferDate) : '',
                item.start_date_of_gr ? displayDateTime(item.start_date_of_gr) : '',
                item.rgr_duration ?? '',
                item.first_rgr_due_on ? displayDateTime(item.first_rgr_due_on) : '',
                item.last_inspection_ir_date ? displayDateTime(item.last_inspection_ir_date) : '',
                item.last_demand_letter_date ? displayDateTime(item.last_demand_letter_date) : '',
                item.last_demand_id ?? '',
                item.last_demand_amount ?? '',
                item.last_amount_received ?? '',
                item.last_amount_received_date ? displayDateTime(item.last_amount_received_date) : '',
                item.total_dues ?? '',
                item.current_lesse_name ?? '',
                item.lessee_address ?? '',
                item.lessee_phone ?? '',
                item.lessee_email ?? '',
                item.created_by ?? '',
                displayDateTime(item.created_at)

            ]);
    }

    function downloadExel() {
        if (wsData.length > 1) {
            let ws = XLSX.utils.aoa_to_sheet(wsData);
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Report");
            XLSX.writeFile(wb, `detailed-report.xlsx`);
            $('#spinnerOverlay').hide();
        }
    }

    function displayDateTime(dateString) { //conversts date('Y-m-d H:i:s') to d-m-Y
        const dateArr = dateString.split(' ');
        let date = dateArr[0];
        let time = dateArr[1];
        return `${(date.split('-').reverse().join('-'))}${time ? ' '+ time : ''}`;
    }

    $('.input-reset-icon').click(function() {
        var targetElement = $($(this).data('targets'));

        if (targetElement.attr('name').indexOf('[') > -1) {
            targetElement.selectpicker('deselectAll').selectpicker('render');
        } else {
            targetElement.val('')
            targetElement.selectpicker('render');
        }

        if (targetElement == 'property_type') { //if filter is property type then also remove property sub type filter and clear dropdown
            $('#prop-sub-type').selectpicker('deselectAll');
            $('#prop-sub-type') /** remove options from property sub type */
                .find('option')
                .remove()
                .end();
        }

    })

    function resetFilters() {
        /* debugger;
        $('.input-reset-icon').each(function() {
            $(this).click();
        })
        $('button[type="submit"]').click(); */
        window.location.href = "{{ route('detailedReport') }}";
    }

    $(document).ready(function() {
        var table = $('#example').DataTable({
            responsive: false,
            searching: false,
            paging: false,
            info: false
        });
    });
</script>
@endsection