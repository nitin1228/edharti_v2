@extends('layouts.app')

@section('title', 'Colony-Wise Filter Report')

@section('content')

    <style>
        .page-item.active a {
            color: #fff !important;
        }

        .dropdown-item.active span.text {
            color: #fff !important;
        }

        .summary {
            display: flex;
            justify-content: space-between;
        }

        /**REPORT LIST  */
        table {
            width: 100%;
            table-layout: fixed;
            /* border-collapse: collapse; */
            border-color: none !important;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 0px !important;
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            overflow: hidden;
        }

        td:nth-child(odd) {
            background-color: #f1f1f166;
            vertical-align: middle;
        }

        td:nth-child(even) {
            background-color: #f1f1f166;
            vertical-align: middle;
        }

        .landtypeFreeH {
            background-color: #116d6e17;
            color: #116d6e !important;
            padding: 2px 6px;
            margin: 2px;
        }

        .landtypeRehab {
            background-color: #d7ae1a21;
            color: #9b7c0f !important;
            padding: 2px 6px;
            margin: 2px;
        }

        .landuseResid {
            background-color: #321e1e17;
            color: #321e1e !important;
            padding: 2px 6px;
            margin: 2px;
        }

        .statusRGRNo {
            background-color: rgb(223, 83, 97, 0.129);
            color: #df5361 !important;
            padding: 2px 6px;
        }

        .view_more_link {
            font-weight: 500 !important;
        }

        .show-demand-modal {
            font-weight: 500 !important;
        }

        /* .card {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        border: 0px !important;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    } */

        .parent_table_container {
            border-bottom: 1px solid #dcdcdc;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .export-btn {
            font-size: 14px;
        }


        button.export-btn svg {
            width: 35px;
            height: 35px;
        }

        button.filter-btn {
            border: 0px;
            background: transparent;
            position: relative;
            font-size: 20px
        }
    </style>
    <!-- use version 0.20.3 -->
    <script lang="javascript" src="{{ asset('assets/js/xlsx.full.min.js') }}"></script>


    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Colony-Wise Report</div>
        <style>
            .breadcrumb-item.active span {
                font-weight: 500;
            }
        </style>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item " aria-current="">
                        <span>Reports</span>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <span>Colony-Wise Filter Report</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card group_filters_container sticky-div">
                <div class="card-body">
                    <form action="#">
                        <div class="group-row-filters">
                            <!-- added class colony-form by anil on 06-10-2025 -->
                            <div class="d-flex align-items-start flex-wrap colony-form">
                                <!-- added class flex-wrap by anil on 04-09-2025 -->
                                <div class="d-flex align-items-start mb-2">
                                    <!-- added class div by anil on 04-09-2025 -->
                                    <div class="select-menu form-select mx-2" style="display: none;">
                                        <div class="select-btn">
                                            <span class="sBtn-text">More Filters</span>
                                            <i class="bx bx-chevron-down"></i>
                                        </div>
                                    </div>
                                    {{-- <div class="input-relative mx-2">
                                        <i class="lni lni-search-alt"></i>
                                        <input class="form-control" type="text" placeholder="Search by Property Id"
                                            id="propertyIdSearch">
                                        <button type="button" class="input-reset-icon" data-filter="propertyId"
                                            data-targets="#propertyIdSearch"><i class="lni lni-cross-circle"></i></button>
                                    </div> --}}
                                    <div class="input-relative mx-2">
                                        <select name="localityRecord" id="locality_record" class="form-select">
                                            <option value="">Select Colony</option>
                                            @foreach ($colonyList as $colony)
                                                <option value="{{ $colony->id }}">{{ $colony->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-relative mx-2">
                                        <select name="blockRecord" id="block_record"
                                            class="form-select alphaNum-hiphenForwardSlash">
                                            <option value="">Select Block</option>
                                        </select>
                                    </div>
                                    <div class="input-relative mx-2">
                                        <select name="plotRecord" id="plot_record" class="form-select plotNoAlpaMix">
                                            <option value="">Select Plot</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="btn-group-filter ms-auto"> <!-- added calss ms-auto by anil on 04-09-2025 -->
                                    <button type="button" class="btn btn-secondary px-5 filter-btn"
                                        onclick="resetColonyWiseFilters()">Reset</button>
                                    <button type="button" class="btn btn-primary px-5 filter-btn"
                                        onclick="handleColonyWiseFilterChange()">Apply</button>
                                    <!-- 🔽 Added Export Buttons -->
                                    <!-- <div class="input-relative ml-2">
                                        <div class="buttons d-flex">
                                            <button type="button" class="filter-btn export-btn mx-1">
                                            </button>
                                            <button type="button" class="filter-btn download mx-1" data-format="excel"
                                                data-toggle="tooltip" title="Download Excel">
                                            </button>
                                            <button type="button" class="filter-btn download mx-1" data-format="pdf"
                                                data-toggle="tooltip" title="Download PDF">
                                            </button>
                                        </div>
                                    </div> -->
                                </div>                                
                            </div>
                            <!-- aded this div and moved both section inside this by anil on 17-09-2025 -->
                            <div class="report-action">
                                <div class="input-relative ml-2">
                                    <div class="buttons d-flex">
                                        <button type="button" class="filter-btn export-btn mx-1">
                                            <!-- <i class="fa fa-envelope" aria-hidden="true"></i> -->
                                            <svg height="32px" width="32px" version="1.1" id="Layer_1"
                                                xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                                viewBox="0 0 511.999 511.999" xml:space="preserve" fill="#000000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path style="fill:#116d6e;"
                                                        d="M477.832,408.28H142.075c-7.691,0-13.929-6.236-13.929-13.929c0-7.693,6.237-13.929,13.929-13.929 h335.757c3.481,0,6.311-3.385,6.311-7.545V139.121c0-4.16-2.83-7.545-6.311-7.545H162.315c-3.481,0-6.311,3.385-6.311,7.545v167.826 c0,7.693-6.237,13.929-13.929,13.929c-7.691,0-13.929-6.236-13.929-13.929V139.121c0-19.521,15.328-35.402,34.168-35.402H477.83 c18.84,0,34.168,15.881,34.168,35.402v233.755C512,392.398,496.672,408.28,477.832,408.28z">
                                                    </path>
                                                    <path style="fill:#CFF09E;"
                                                        d="M142.075,139.121L142.075,139.121c0,6.854,3.085,13.298,8.303,17.341l169.697,131.493 l169.697-131.493c5.218-4.043,8.303-10.485,8.303-17.341l0,0c0-11.86-9.062-21.474-20.24-21.474h-315.52 C151.137,117.648,142.075,127.262,142.075,139.121z">
                                                    </path>
                                                    <g>
                                                        <path style="fill:#116d6e;"
                                                            d="M320.073,301.884c-3.01,0-6.02-0.974-8.531-2.918L141.845,167.473 c-8.577-6.647-13.699-17.245-13.699-28.35c0-19.521,15.328-35.402,34.168-35.402H477.83c18.84,0,34.168,15.881,34.168,35.402 c0,11.105-5.122,21.704-13.7,28.35L328.604,298.966C326.093,300.912,323.083,301.884,320.073,301.884z M162.315,131.576 c-3.479,0-6.311,3.385-6.311,7.545c0,2.555,1.085,4.921,2.904,6.331l161.165,124.883l161.164-124.883 c1.819-1.41,2.905-3.776,2.905-6.331c0-4.16-2.832-7.545-6.311-7.545H162.315L162.315,131.576z">
                                                        </path>
                                                        <path style="fill:#116d6e;"
                                                            d="M140.681,195.17H13.929C6.237,195.17,0,188.934,0,181.242s6.237-13.929,13.929-13.929h126.752 c7.691,0,13.929,6.236,13.929,13.929S148.372,195.17,140.681,195.17z">
                                                        </path>
                                                        <path style="fill:#116d6e;"
                                                            d="M140.681,258.545H55.719c-7.691,0-13.929-6.236-13.929-13.929s6.237-13.929,13.929-13.929h84.962 c7.691,0,13.929,6.236,13.929,13.929S148.372,258.545,140.681,258.545z">
                                                        </path>
                                                        <path style="fill:#116d6e;"
                                                            d="M140.681,321.921H97.509c-7.691,0-13.929-6.236-13.929-13.929s6.237-13.929,13.929-13.929h43.172 c7.691,0,13.929,6.236,13.929,13.929S148.372,321.921,140.681,321.921z">
                                                        </path>
                                                    </g>
                                                </g>
                                            </svg>
                                        </button>
                                        <button type="button" class="filter-btn download mx-1" data-format="excel"
                                            data-toggle="tooltip" title="Download Excel">
                                            <svg width="32px" height="32px" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M28.781,4.405H18.651V2.018L2,4.588V27.115l16.651,2.868V26.445H28.781A1.162,1.162,0,0,0,30,25.349V5.5A1.162,1.162,0,0,0,28.781,4.405Zm.16,21.126H18.617L18.6,23.642h2.487v-2.2H18.581l-.012-1.3h2.518v-2.2H18.55l-.012-1.3h2.549v-2.2H18.53v-1.3h2.557v-2.2H18.53v-1.3h2.557v-2.2H18.53v-2H28.941Z"
                                                    style="fill:#20744a;fill-rule:evenodd" />
                                                <rect x="22.487" y="7.439" width="4.323" height="2.2" style="fill:#20744a" />
                                                <rect x="22.487" y="10.94" width="4.323" height="2.2" style="fill:#20744a" />
                                                <rect x="22.487" y="14.441" width="4.323" height="2.2" style="fill:#20744a" />
                                                <rect x="22.487" y="17.942" width="4.323" height="2.2" style="fill:#20744a" />
                                                <rect x="22.487" y="21.443" width="4.323" height="2.2" style="fill:#20744a" />
                                                <polygon
                                                    points="6.347 10.673 8.493 10.55 9.842 14.259 11.436 10.397 13.582 10.274 10.976 15.54 13.582 20.819 11.313 20.666 9.781 16.642 8.248 20.513 6.163 20.329 8.585 15.666 6.347 10.673"
                                                    style="fill:#ffffff;fill-rule:evenodd" />
                                            </svg>
                                        </button>
                                        <button type="button" class="filter-btn download mx-1" data-format="pdf"
                                            data-toggle="tooltip" title="Download PDF">
                                            <!-- <i class="fa fa-file-pdf" aria-hidden="true"></i> -->
                                            <svg version="1.1" id="_x35_" xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve"
                                                width="25px" height="25px" fill="#000000">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                <g id="SVGRepo_iconCarrier">
                                                    <g>
                                                        <polygon style="fill:#116d6e;"
                                                            points="475.435,117.825 475.435,512 47.791,512 47.791,0.002 357.613,0.002 412.491,54.881 ">
                                                        </polygon>
                                                        <rect x="36.565" y="34.295" style="fill:#F2F2F2;" width="205.097"
                                                            height="91.768">
                                                        </rect>
                                                        <g>
                                                            <g>
                                                                <path style="fill:#116d6e;"
                                                                    d="M110.132,64.379c-0.905-2.186-2.111-4.146-3.769-5.804c-1.658-1.658-3.694-3.015-6.031-3.92 c-2.412-0.98-5.126-1.432-8.141-1.432H69.651v58.195h11.383V89.481h11.157c3.015,0,5.729-0.452,8.141-1.432 c2.337-0.905,4.372-2.261,6.031-3.92c1.659-1.658,2.865-3.543,3.769-5.804c0.829-2.186,1.282-4.523,1.282-6.935 C111.413,68.902,110.961,66.565,110.132,64.379z M97.844,77.118c-1.508,1.432-3.618,2.186-6.181,2.186H81.034V63.323h10.629 c2.563,0,4.674,0.754,6.181,2.261c1.432,1.432,2.186,3.392,2.186,5.804C100.031,73.726,99.277,75.686,97.844,77.118z">
                                                                </path>
                                                                <path style="fill:#116d6e;"
                                                                    d="M164.558,75.761c-0.075-2.035-0.151-3.844-0.377-5.503c-0.226-1.659-0.603-3.166-1.131-4.598 c-0.528-1.357-1.206-2.714-2.111-3.92c-2.035-2.94-4.523-5.126-7.312-6.483c-2.865-1.357-6.257-2.035-10.252-2.035h-20.956 v58.195h20.956c3.995,0,7.387-0.678,10.252-2.035c2.789-1.357,5.277-3.543,7.312-6.483c0.905-1.206,1.583-2.563,2.111-3.92 c0.528-1.432,0.905-2.94,1.131-4.598c0.226-1.658,0.301-3.468,0.377-5.503c0.075-1.96,0.075-4.146,0.075-6.558 C164.633,79.908,164.633,77.721,164.558,75.761z M153.175,88.2c0,1.734-0.151,3.091-0.302,4.297 c-0.151,1.131-0.377,2.186-0.678,2.94c-0.301,0.829-0.754,1.583-1.281,2.261c-1.885,2.412-4.749,3.543-8.518,3.543h-8.669V63.323 h8.669c3.769,0,6.634,1.206,8.518,3.618c0.528,0.678,0.98,1.357,1.281,2.186s0.528,1.809,0.678,3.015 c0.151,1.131,0.302,2.563,0.302,4.221c0.075,1.659,0.075,3.694,0.075,5.955C153.251,84.581,153.251,86.541,153.175,88.2z">
                                                                </path>
                                                                <path style="fill:#116d6e;"
                                                                    d="M213.18,63.323V53.222h-38.37v58.195h11.383V87.823h22.992V77.646h-22.992V63.323H213.18z">
                                                                </path>
                                                            </g>
                                                            <g>
                                                                <path style="fill:#116d6e;"
                                                                    d="M110.132,64.379c-0.905-2.186-2.111-4.146-3.769-5.804c-1.658-1.658-3.694-3.015-6.031-3.92 c-2.412-0.98-5.126-1.432-8.141-1.432H69.651v58.195h11.383V89.481h11.157c3.015,0,5.729-0.452,8.141-1.432 c2.337-0.905,4.372-2.261,6.031-3.92c1.659-1.658,2.865-3.543,3.769-5.804c0.829-2.186,1.282-4.523,1.282-6.935 C111.413,68.902,110.961,66.565,110.132,64.379z M97.844,77.118c-1.508,1.432-3.618,2.186-6.181,2.186H81.034V63.323h10.629 c2.563,0,4.674,0.754,6.181,2.261c1.432,1.432,2.186,3.392,2.186,5.804C100.031,73.726,99.277,75.686,97.844,77.118z">
                                                                </path>
                                                            </g>
                                                        </g>
                                                        <polygon style="opacity:0.08;fill:#040000;"
                                                            points="475.435,117.825 475.435,512 47.791,512 47.791,419.581 247.705,219.667 259.54,207.832 266.098,201.273 277.029,190.343 289.995,177.377 412.491,54.881 ">
                                                        </polygon>
                                                        <polygon style="fill:#063434;"
                                                            points="475.435,117.836 357.599,117.836 357.599,0 ">
                                                        </polygon>
                                                        <g>
                                                            <path style="fill:#F2F2F2;"
                                                                d="M414.376,370.658c-2.488-4.372-5.88-8.518-10.101-12.287c-3.467-3.166-7.538-6.106-12.137-8.82 c-18.544-10.93-45.003-16.207-80.961-16.207h-3.618c-1.96-1.809-3.995-3.618-6.106-5.503 c-13.644-12.287-24.499-25.63-32.942-40.48c16.584-36.561,24.499-69.126,23.519-96.867c-0.151-4.674-0.829-9.046-2.035-13.117 c-1.809-6.558-4.824-12.363-9.046-17.112c-0.075-0.075-0.075-0.075-0.151-0.151c-6.709-7.538-16.056-11.835-25.555-11.835 c-9.574,0-18.393,4.146-24.801,11.76c-6.332,7.538-9.724,17.866-9.875,30.002c-0.226,18.544,1.281,36.108,4.448,52.315 c0.301,1.282,0.528,2.563,0.829,3.844c3.166,14.7,7.84,28.645,13.87,41.611c-7.086,14.398-14.247,26.836-19.223,35.279 c-3.769,6.408-7.915,13.117-12.212,19.826c-19.373,3.468-35.807,7.689-50.129,12.966c-19.373,7.011-34.902,16.056-46.059,26.836 c-7.237,6.935-12.137,14.323-14.549,22.012c-2.563,7.915-2.412,15.83,0.452,22.916c2.638,6.558,7.387,12.061,13.72,15.83 c1.508,0.905,3.091,1.658,4.749,2.337c4.825,1.96,10.101,3.015,15.604,3.015c12.74,0,25.856-5.503,36.937-15.378 c20.655-18.469,41.988-48.169,54.577-66.94c10.327-1.583,21.559-2.94,34.224-4.297c14.926-1.508,28.118-2.412,40.104-2.865 c3.694,3.317,7.237,6.483,10.629,9.498c18.846,16.81,33.168,28.947,46.134,37.465c0,0.075,0.075,0.075,0.151,0.075 c5.126,3.392,10.026,6.181,14.926,8.443c5.503,2.563,11.081,3.92,16.81,3.92c7.237,0,14.021-2.186,19.675-6.181 c5.729-4.146,9.875-10.101,11.76-16.81C420.18,387.694,418.899,378.724,414.376,370.658z M247.705,219.667 c-1.055-9.348-1.508-19.072-1.357-29.324c0.151-9.724,3.694-16.283,8.895-16.283c3.92,0,8.066,3.543,9.95,10.327 c0.528,2.035,0.905,4.372,0.98,7.01c0.151,3.166,0.075,6.483-0.075,9.875c-0.452,9.574-2.111,19.75-4.975,30.681 c-1.734,7.011-3.995,14.323-6.784,21.936C251.173,243.186,248.911,231.803,247.705,219.667z M121.967,418.073 c-1.282-3.166,0.151-9.272,7.991-16.81c11.986-11.458,30.756-20.504,56.914-27.364c-4.975,6.784-9.875,12.966-14.624,18.619 c-7.237,8.744-14.172,16.132-20.429,21.71c-5.352,4.824-11.232,7.84-16.81,8.594c-0.98,0.151-1.96,0.226-2.94,0.226 C127.168,423.049,123.173,421.089,121.967,418.073z M242.428,337.942l0.528-0.829l-0.829,0.151 c0.151-0.377,0.377-0.754,0.603-1.055c3.166-5.352,7.161-12.212,11.458-20.127l0.377,0.829l0.98-2.035 c3.166,4.523,6.634,8.971,10.252,13.267c1.734,2.035,3.543,3.995,5.352,5.955l-1.206,0.075l1.055,0.98 c-3.091,0.226-6.332,0.528-9.574,0.829c-2.035,0.226-4.146,0.377-6.257,0.603C250.796,337.037,246.499,337.49,242.428,337.942z M369.297,384.98c-8.971-5.729-18.996-13.795-31.359-24.575c17.564,1.809,31.359,5.654,41.159,11.383 c4.297,2.488,7.538,5.051,9.724,7.538c3.618,3.844,4.9,7.312,4.221,9.649c-0.603,2.337-3.241,3.92-6.483,3.92 c-1.885,0-3.844-0.452-5.88-1.432c-3.468-1.658-7.086-3.694-10.93-6.181C369.598,385.282,369.448,385.131,369.297,384.98z">
                                                            </path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </form>                    
                </div>
            </div>

        </div>
    </div>
    <!--end row-->
    {{-- @include('include.loader') --}}
    @include('include.alerts.ajax-alert')
    <div class="row align-data-height results sticky-next d-none">
        <div class="col-xl-12 col-12">
            <div class="card list-data-height">
                <div class="card-body">
                    <div class="summary">

                        <div class="resultInfo">

                        </div>

                        <nav aria-label="...">
                            <ul class="pagination" style="justify-content: center;">
                                <li class="page-item disabled" id="page-previous"><a class="page-link"
                                        href="javascript:;" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item" id="page-next"><a class="page-link" href="javascript:;">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="property-data"></div>
                    <div class="summary">
                        <div class="resultInfo">
                        </div>
                        <nav aria-label="...">
                            <ul class="pagination" style="justify-content: center;">
                                <li class="page-item disabled" id="page-previous"><a class="page-link"
                                        href="javascript:;" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item" id="page-next"><a class="page-link" href="javascript:;">Next</a>
                                </li>
                            </ul>
                        </nav>
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
                    <a href="{{ route('map') }}" class="btn btn-primary" target="_blank">View All Properties</a>
                </div>
            </div>
        </div>
    </div>
    <!-- added by swati mishra for displaying dues in filter report by Swati Mishra 11-04-2025 -->
    @include('report.due-demand')

@endsection

@section('footerScript')
    <script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/knobs.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/map.js') }}"></script> --}}
    <script src="{{ asset('assets/js/property-type-subtype-dropdown.js') }}"></script>

    <!-- jsPDF (LEGACY BUILD - required) -->
    <!-- jsPDF UMD -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- AutoTable plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <script>
        //get all blocks of selected locality
        $('#locality_record').on('change', function() {
            var locality = this.value;
            $("#block_record").html('');
            $.ajax({
                url: "{{ route('localityBlocks') }}",
                type: "POST",
                data: {
                    locality: locality,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#block_record').html('<option value="">Select</option>');
                    $.each(result, function(key, value) {
                        $("#block_record").append('<option value="' + value.block_no + '">' +
                            value
                            .block_no + '</option>');
                    });
                    //#Start :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                    $("#landUse_org, #landUseSubtype_org").html('<option value="">Select</option>');
                    $.ajax({
                        url: "{{ route('landTypes') }}",
                        type: "POST",
                        data: {
                            locality: locality,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            console.log(result);
                            // Populate Property Types
                            if (result.propertyTypes?.length) {
                                $("#landUse_org").append(
                                    result.propertyTypes.map(type =>
                                        `<option value="${type.id}">${type.item_name}</option>`
                                    ).join('')
                                );
                            }
                            // Populate Property Sub Types
                            // if (result.propertySubtypes?.length) {
                            //     $("#landUseSubtype_org").append(
                            //         result.propertySubtypes.map(subtype => `<option value="${subtype.id}">${subtype.item_name}</option>`).join('')
                            //     );
                            // }
                        }
                    });
                    //#End :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                }
            });
        });


        //get all plots of selected block
        $('#block_record').on('change', function() {
            var locality = $('#locality_record').val();
            var block = this.value;
            $("#plot_record").html('');
            $.ajax({
                url: "{{ route('blockPlots') }}",
                type: "POST",
                data: {
                    locality: locality,
                    block: block,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    // console.log(result);
                    $('#plot_record').html('<option value="">Select Plot</option>');
                    $.each(result, function(key, value) {

                        $("#plot_record").append('<option value="' + value + '">' + value +
                            '</option>');
                    });
                }
            });
        });

        let filters = {};
        let pageNumber = 1;
        let totalPages;
        let wsData = [];
        let downloadFormat = null;
        const optionMenu = document.querySelector(".select-menu"),
            selectBtn = optionMenu.querySelector(".select-btn"),
            options = optionMenu.querySelectorAll(".option"),
            sBtn_text = optionMenu.querySelector(".sBtn-text");
        // start comment by anil for chagne the open the option hover to click on 23-05-2025
        selectBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // Prevent click from bubbling to document
            optionMenu.classList.toggle("active")
        });

        // Close menu when clicking outside
        document.addEventListener("click", function(e) {
            if (!optionMenu.contains(e.target)) {
                optionMenu.classList.remove("active");
            }
        });
        // end comment by anil for chagne the open the option hover to click on 23-05-2025
        options.forEach((option) => {
            option.addEventListener("click", () => {
                let selectedOption = option.querySelector(".option-text").innerText;
                sBtn_text.innerText = selectedOption;

                optionMenu.classList.remove("active");
            });
        });
        $(document).ready(function() {
            $(".range-filter-btn").click(function() {
                $(".range-controller").fadeToggle();;
            });
            $(".range-filter-btn2").click(function() {
                $(".range-controller2").fadeToggle();
            });
            getColonyWiseFilterData();
            $('.preloader').remove();
        });


        /** for property type subtype dropdowns added By Nitin */
        $(document).ready(() => {

            $('.multipleSelect').change(function() {
                var valueArr = $(this).val();
                if (valueArr.indexOf('') > -1) {
                    $(this).val([]);
                }
            })


        });

        function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                const context = this;
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(context, args);
                }, delay);
            };
        }

        $('#name_filter').keyup(debounce(function() {
            var name = $(this).val().trim();
            if (name.length > 0) {
                filters.name = name;
            } else {
                delete filters.name;
            }

        }, 400));

        $('#contact_filter').keyup(function() {
            var number = $(this).val().trim();
            if (!isNaN(number)) {
                filters.contact = number;
            } else {
                delete filters.contact
            }

        });

        //property Id Search
        $('#propertyIdSearch').blur(function() {
            var val = $(this).val().trim();
            if (val.length > 2 && !isNaN(val)) {
                filters.propertyId = val;
            } else {
                if (filters.propertyId)
                    delete filters.propertyId
            }
        })

        $('#locality_record').change(function() {
            var val = $(this).val();
            if (val !== '') {
                filters.localityRecord = val;
            } else {
                if (filters.localityRecord)
                    delete filters.localityRecord;
            }
        });
        $('#block_record').change(function() {
            var val = $(this).val();
            if (val !== '') {
                filters.blockRecord = val;
            } else {
                if (filters.blockRecord)
                    delete filters.blockRecord;
            }
        });
        $('#plot_record').change(function() {
            var val = $(this).val();
            if (val !== '') {
                filters.plotRecord = val;
            } else {
                if (filters.plotRecord)
                    delete filters.plotRecord;
            }
        });

        //property Address Search

        $('#propertyAddressSearch').blur(function() {
            var val = $(this).val().trim();
            if (val.length > 2) {
                filters.propertyAddress = val;
            } else {
                if (filters.propertyAddress)
                    delete filters.propertyAddress
            }

        })

        $('.input-reset-icon').click(function() {
            var filterName = $(this).data('filter');
            var targetElement = $($(this).data('targets'));
            if (filters[filterName]) {
                if (targetElement.length == 1 && targetElement.prop("tagName") && (targetElement.prop("tagName")
                        .toLowerCase() == 'select')) {
                    if (Array.isArray(filters[filterName])) {
                        targetElement.selectpicker('deselectAll');
                    } else {
                        targetElement.val('')
                        targetElement.selectpicker('render');
                    }
                } else {
                    targetElement.val('');
                }
                if (filterName ==
                    'property_type'
                ) { //if filter is property type then also remove property sub type filter and clear dropdown
                    $('#prop-sub-type').selectpicker('deselectAll');
                    $('#prop-sub-type') /** remove options from property sub type */
                        .find('option')
                        .remove()
                        .end();
                    delete filters.property_sub_type
                }
                delete filters[filterName];

            }
        })



        const handleColonyWiseFilterChange = () => {
            pageNumber = 1;
            filters.page = pageNumber;
            getColonyWiseFilterData();
        }


        //click on pagination page
        $(document).on('click', '.page-item', function() {
            let id = $(this).attr('id');
            if (id == 'page-previous') {
                if (pageNumber > 1) {
                    pageNumber--;
                    handlePageChnage();
                }
            }
            if (id == 'page-next') {
                if (pageNumber < +totalPages) {
                    pageNumber++;
                    handlePageChnage();
                }
            }
            if (id == 'page-number') {
                pageNumber = parseInt($(this).data('page-number'));
                handlePageChnage();
            }
        })
        const handlePageChnage = () => {
            filters.page = pageNumber;
            getColonyWiseFilterData();
        }

        const propertyDetailsRoute = "{{ route('viewPropertyDetails', ['property' => '__ID__']) }}";
        const streetviewRoute = "{{ route('streetview', ['id' => '__ID__']) }}";

        const getColonyWiseFilterData = () => {
            const spinnerOverlay = document.getElementById('spinnerOverlay');
            $('.results').addClass('d-none');
            if (spinnerOverlay) {
                spinnerOverlay.style.display = 'flex';
            }
            // if ($('.loader_container').hasClass('d-none'))
            // $('.loader_container').removeClass('d-none');

            $.ajax({
                type: 'post',
                url: '{{ route('getColonyWisePropertyResults') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    filters: filters
                },
                success: (results) => {
                    // $('.loader_container').addClass('d-none');
                    if ($('.results').hasClass('d-none'))
                        $('.results').removeClass('d-none');
                    if (spinnerOverlay) {
                        spinnerOverlay.style.display = 'none';
                    }



                    $('.property-data').html('');
                    let updatedListHTML = '';
                    let rowNum = 50 * (pageNumber - 1) + 1;
                    if (results.counter > 0) {

                        results.rows.forEach((row, index) => {
                            const mapMarkerHtml = `<span class="location_icon" data-bs-toggle="modal" data-bs-target="#viewMapModal" onclick="locate(${row.old_propert_id})">
                            <i class="lni lni-map-marker text-danger" data-toggle="tooltip" title="View Mapview"></i>
                        </span>`;
                            const dynamicViewMoreRoute = propertyDetailsRoute.replace('__ID__', row
                                .id);
                            const viewMoreHtml =
                                `<a href="${dynamicViewMoreRoute}" class="text-primary view_more_link">View More</a>`;
                            const dynamicStreetviewRoute = streetviewRoute.replace('__ID__', row
                                .old_propert_id);
                            const streetViewHtml = `<a href="${dynamicStreetviewRoute}" target="_blank" data-toggle="tooltip" title="View Streetview">
                            <span class="location_icon">
                                <img src="{{ asset('assets/images/street-view.svg') }}" class="map-marker-icon" />
                            </span>
                        </a>`;
                            const duesHtml =
                                `<a href="javascript:;" class="text-danger show-demand-modal" data-property-id="${row.old_propert_id}">Dues</a>`;
                            /** append row in result list */
                            updatedListHTML += `<div class="parent_table_container"><table class="table report-item">
                               <tr>
                                    <td colspan="4" class="address_data">(${rowNum}) Presently known as: <span class="highlight_value address_address">${row.address ?? 'N A'}</span></td>
                               </tr>
                                <tr>     
                                    <td>Property ID: <span class="highlight_value">${row.unique_propert_id}( ${row.old_propert_id} )</span></td>
                                    <td>Land Size: <span class="highlight_value">${row.area_in_sqm} Sq. Mtr.</span></td>
                                    <td>Land Value: <span class="highlight_value">₹${row.land_value ??'-'}</span></td>
                                    <td>Present Lessee: <span class="highlight_value lessee_address">${row.current_lessee_name ? row.current_lessee_name: 'N/A'}</span></td>
                                </tr>
                                <tr>
                                    <td>Lease Tenure: <span class="highlight_value">${row.lease_tenure ? row.lease_tenure +' years' : 'N/A'}</span></td>
                                    <td>RGR: <span class="highlight_value">${row.gr}</span></td>
                                    <td><span class="highlight_value landtypeRehab">${row.land_type}</span> <span class="highlight_value landtypeFreeH mx-2">${row.status}</span> <span class="highlight_value landuseResid">${row.land_use}</span></td>
                                    <td><span class="d-flex align-items-center border-l-w-0 map_view_location"> ${mapMarkerHtml} ${streetViewHtml}  ${viewMoreHtml} 
                                    &nbsp;|&nbsp; 
                                    ${duesHtml} <span></td>

                                    </tr>
                        </table></div>`;
                            rowNum++;
                        })
                    } else {
                        updatedListHTML += `<div>
					<h3>No matching record found</h3>
					</div>`;
                    }
                    $('.property-data').html(updatedListHTML);

                    /**update pagination links */
                    if (results.counter <= 50) {
                        $('.pagination').hide();
                        if (results.counter == 0) {
                            $('.summary').hide();
                        } else {
                            $('.summary').show();
                            $('.resultInfo').html(
                                `showing 1 - ${results.counter} of ${results.counter} records`)
                        }

                    } else {
                        $('.summary').show();
                        totalPages = parseInt(results.counter / 50 + (results.counter % 50 > 0 ? 1 : 0));


                        let paginationStartPage = Math.max(1, pageNumber - 3)
                        let paginationEndPage = Math.min(totalPages, pageNumber + 3);
                        let paginationHTML = `<li class="page-item ${pageNumber == 1 ? 'disabled':''}" id="page-previous"><a class="page-link" href="javascript:;" tabindex="-1" aria-disabled="true">Previous</a>
						</li>`;
                        for (let page = paginationStartPage; page <= paginationEndPage; page++) {
                            paginationHTML += `<li class="page-item ${ page == pageNumber ? 'active' : ''}" aria-current="page" id="page-number" data-page-number="${page}"><a class="page-link" href="javascript:;">${page}</a>
							</li>`
                        }

                        paginationHTML += `<li class="page-item ${pageNumber == totalPages ? 'disabled':''}" id="page-next"><a class="page-link" href="javascript:;">Next</a>
						</li>`;
                        $('.pagination').html(paginationHTML).show();
                        $('.resultInfo').show();
                        $('.resultInfo').html(
                            `showing ${50*(pageNumber -1) + 1} - ${50*(pageNumber -1) + results.rows.length} of ${results.counter} records`
                        )
                    }

                },
                error: response => {
                    $('.loader_container').addClass('d-none');
                    if ($('.results').hasClass('d-none'))
                        $('.results').removeClass('d-none');
                    if (response.responseJSON && response.responseJSON.message) {
                        showError(response.responseJSON.message)
                    }
                }
            });
        }

        function resetColonyWiseFilters() {
            document.getElementById("locality_record").selectedIndex = 0;
            document.getElementById("block_record").selectedIndex = 0;
            document.getElementById("plot_record").selectedIndex = 0;
            filters.localityRecord = '';
            filters.plotRecord = '';
            filters.blockRecord = '';
            handleColonyWiseFilterChange();
        }
        $('.export-btn').click(function() {
            let exportFormat = $(this).data('export-format');
            $('.results').addClass('d-none');
            if ($('.loader_container').hasClass('d-none'))
                $('.loader_container').removeClass('d-none');
            $.ajax({
                type: "post",
                url: '{{ route('colonyWiseReportExport') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    format: exportFormat,
                    filters: filters
                },
                /* xhrFields: {
                    responseType: 'blob' // Important
                }, */
                success: response => {
                    $('.loader_container').addClass('d-none');
                    if ($('.results').hasClass('d-none'))
                        $('.results').removeClass('d-none');
                    if (response.status) {
                        showSuccess(response.status)
                    }

                    $('.loader_container').addClass('d-none');
                    if ($('.results').hasClass('d-none'))
                        $('.results').removeClass('d-none');
                },
                error: response => {
                    $('.loader_container').addClass('d-none');
                    if ($('.results').hasClass('d-none'))
                        $('.results').removeClass('d-none');
                    if (response.responseJSON && response.responseJSON.message) {
                        showError(response.responseJSON.message)
                    }
                }
            })
        });



        $('.download').click(function() {
            downloadFormat = $(this).data('format')
            $('#spinnerOverlay').show();
            let currentPage = 1;
            wsData = []; // Reset
            fetchPage(currentPage);
        });

        function fetchPage(page) {
            let perpage = 1000;
            const updatedFilters = {
                ...filters,
                page: page,
                limit: perpage
            };

            $.ajax({
                type: "post",
                url: '{{ route('colonyWiseReportExport') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    filters: updatedFilters
                },
                success: response => {
                    if (response.rows && response.counter) {
                        let totalPages = Math.ceil(response.counter / perpage);

                        response.rows.forEach(row => appendToexcel(row));

                        if (page < totalPages) {
                            fetchPage(page + 1); // Recursive call to next page
                        } else {
                            switch (downloadFormat) {
                                case 'excel':
                                    downloadExel();
                                    break;
                                case 'pdf':
                                    downloadPdf();
                                    break;
                                default:
                                    showError('Download format not identified');
                                    console.log(downloadFormat);
                                    $('#spinnerOverlay').hide();
                                    break;
                            }
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
                    'PID - Old',
                    'PID - New',
                    'File no',
                    'PID - Sub-ID (Child)',
                    'Address (Known As)',
                    'Land - Nazul/ Rehab',
                    'Land Use Type',
                    'Land Use Sub-type',
                    'Land Status',
                    'Colony',
                    'Area',
                    'Unit',
                    'Area (In Sqm.)',
                    'Land Value',
                    'Present/Last GR',
                    'Re Entered',
                    'Re-Entered Date',
                    'Outstanding',
                    'Lease Deed Type',
                    'Lease Tenure(Years)',
                    'Date of Execution',
                    /* 'Land Rate - Circle',
                    'Land Rate - L&DO', */
                    'Original Lessee/Owner Name',
                    'Present Lessee/Owner Name',
                    'Contact Details',
                    'Remarks'
                ]);
            }

            wsData.push([
                item.old_propert_id,
                item.unique_propert_id,
                item.file_no,
                item.child_id,
                item.address,
                item.land_use,
                item.land_type,
                item.land_sub_type,
                item.status,
                item.colony,
                item.input_area,
                item.unit,
                item.area_in_sqm,
                item.land_value,
                item.gr_in_re_rs,
                item.is_re_rented == 1 ? 'Yes' : 'NO',
                item.re_rented_date,
                item.total_dues,
                item.lease_type,
                item.lease_tenure,
                item.doe,
                /* item.circle_land_rate,
                item.lndo_land_rate, */
                item.original_lessee_name,
                item.current_lessee_name,
                item.phone_no,
                item.remarks,
            ]);
        }

        function downloadExel() {
            if (wsData.length > 1) {
                let ws = XLSX.utils.aoa_to_sheet(wsData);
                let wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Report");
                XLSX.writeFile(wb, `report.xlsx`);
                $('#spinnerOverlay').hide();
            }
        }

        function downloadPdf() {

            // Register autotable manually, since it's not auto-attached

            if (wsData.length > 1) {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'landscape'
                });

                headers = wsData.shift();
                // Add table
                doc.autoTable({
                    head: [
                        ['#', ...headers]
                    ],
                    body: wsData.map((row, i) => [(i + 1), ...row]),
                    startY: 20,
                    styles: {
                        fontSize: 8
                    }
                });
                const finalY = doc.lastAutoTable.finalY || 20;

                // Add text after table
                doc.setFontSize(14);
                doc.text(`Showing ${wsData.length} records.`, 14, finalY + 10); // Adjust Y as needed

                // Save the PDF
                doc.save('Report.pdf');
                $('#spinnerOverlay').hide();
            } else {
                showError('Data not found');
            }
        }

        // Onscroll Sticky Filter
        // script.js
        // $(document).ready(function() {
        //     var stickyDiv = $('.sticky-div');
        //     var stickyOffset = stickyDiv.offset().top;

        //     $(window).scroll(function() {
        //         if ($(window).scrollTop() >= stickyOffset) {
        //             stickyDiv.addClass('sticky');
        //         } else {
        //             stickyDiv.removeClass('sticky');
        //         }
        //     });
        // });
        $(document).ready(function() {
            var stickyDiv = $('.sticky-div');
            var stickyNext = $('.sticky-next');
            var stickyOffset = stickyDiv.offset().top;

            $(window).scroll(function() {
                if ($(window).scrollTop() >= stickyOffset) {
                    stickyDiv.addClass('sticky');
                    stickyNext.addClass('sticky-space');
                } else {
                    stickyDiv.removeClass('sticky');
                    stickyNext.removeClass('sticky-space');
                }
            });
        });

        //added by swati mishra for displaying dues in filter report by Swati Mishra 11-04-2025
        $(document).on('click', '.show-demand-modal', function() {
            const propId = $(this).data('property-id');
            const modalBody = $('#demandModalBody');
            modalBody.html('<p>Loading...</p>'); // reset

            $.ajax({
                url: '{{ route('getDemandDetails') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    property_id: propId
                },
                success: (response) => {
                    if (response.status) {
                        const d = response.data;
                        const html = `
                    <table class="table table-bordered table-striped">
                        <tr><th>Demand ID</th><td>${d.demand_id}</td></tr>
                        <tr><th>Demand Date</th><td>${d.demand_date}</td></tr>
                        <tr><th>Amount</th><td>₹${d.amount}</td></tr>
                        <tr><th>Paid Amount</th><td>₹${d.paid}</td></tr>
                        <tr><th>Outstanding Dues</th><td>₹${d.outstanding}</td></tr>
                    </table>
                `;
                        modalBody.html(html);
                    } else {
                        modalBody.html('<p>No demand records found.</p>');
                    }

                    $('#demandModal').modal('show');
                },
                error: () => {
                    modalBody.html('<p class="text-danger">Error fetching demand details.</p>');
                    $('#demandModal').modal('show');
                }
            });
        });
    </script>
@endsection
