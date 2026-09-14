<head>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
        }

        /* body::before {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -99;
            background: url({{ public_path('assets/images/water-mark-emblem.png') }}) center center no-repeat;
            opacity: 0.1;
        }

        body::after {
            content: "";
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            z-index: -9;
            background: url({{ public_path('assets/images/water-mark.png') }}) 0 0 repeat;
            transform: rotate(-30deg);
            opacity: 0.1;
        }

        .emblem-div {
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }

        .emblem {
            display: inline-block;
            margin: auto;
        }

        .title-main {
            color: navy;
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        }

        .title-sub {
            color: navy;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin: 0;
        } */
        .card-body{
                margin: 1% 2%;
        }
        .pdfTitle{
            text-align:center;
            font-size:20px;
        }
        .part-title {
            background-color: #1fa1a2;
            color: white;
            font-size: 18px;
            line-height:24px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
            text-align: center;
            height:35px;
            vertical-align:center;
        }

        .part-title h5{
            margin-bottom:0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 12px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }
        table span.doc-name{
            display: inline !important;
        }
    </style>
</head>

    <div class="card">
        <div class="card-body">
            <div>
                <div>
                    <h4 class="pdfTitle">{{$applicationType == 'Noc'?'NOC':$applicationType}} Application</h4>
                </div>
                <div class="parent_table_container pb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Application No.:</strong> <span
                                        class="highlight_value">{{ $details->application_no ?? '' }}</span></td>
                                <td><strong>Application Type:</strong> <span class="highlight_value">
                                        <span
                                            class="ml-2 badge rounded-pill text-info bg-light-info p-2 text-uppercase px-3">
                                            {{$applicationType == 'Noc'?'NOC':$applicationType}}
                                        </span>
                                    </span></td>
                                <td><strong>Application Current Status:</strong> <span class="highlight_value">
                                        <!-- spelling correction by anil on 04-03-2025 -->
                                        @switch(getStatusDetailsById( $details->status ?? '' )->item_code)
                                            @case('APP_REJ')
                                                <span class=" statusRejected">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                            @break

                                            @case('APP_NEW')
                                                <span class=" statusNew">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                            @break

                                            @case('APP_IP')
                                                <span class=" statusSecondary">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                            @break

                                            @case('APP_OBJ')
                                                <span class=" statusObject">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                            @break

                                            @case('APP_APR')
                                                <span class=" landtypeFreeH">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                            @break

                                            @case('APP_HOLD')
                                                <span class="statusHold">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                            @break

                                            @default
                                                <span class="">
                                                    {{ getStatusDetailsById($details->status ?? '')->item_name }}
                                                </span>
                                        @endswitch
                                    </span></td>
                                
                            </tr>
                            <tr>
                                <td><strong>Status of Applicant:</strong> <span
                                        class="highlight_value">{{ getServiceNameById($details->status_of_applicant ?? ($details->applicant_status ?? '')) }}</span>
                                </td>
                                <td>
                                    <strong>Received Date:</strong>
                                    <span class="highlight_value">
                                        {{ !empty($application->created_at) ? \Carbon\Carbon::parse($application->created_at)->format('d-m-Y') : '' }}
                                    </span>
                                </td>
                                <td>
                                    <strong>Disposed Date:</strong>
                                    <span class="highlight_value">
                                        {{ !empty($application->disposed_at) ? \Carbon\Carbon::parse($application->disposed_at)->format('d-m-Y') : '' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- added for flat details -->
            @if ($details->flatDetails && $applicationType != 'Deed Of Apartment')
                <div class="part-title">
                    <h5>Flat Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <!-- added responsive div by anil on 19-11-2025 -->
                                <div class="table-responsive">
                                    <table class="table table-bordered property-table-info">
                                        <tbody>
                                            <tr>
                                                <th>Flat Id :</th>
                                                <td>{{ $details->flatDetails->unique_flat_id ?? '' }}</td>
                                                <th>File Number :</th>
                                                <td>{{ $details->flatDetails->unique_file_no ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Floor :</th>
                                                <td>{{ $details->flatDetails->floor ?? '' }}</td>
                                                <th>Flat Number :</th>
                                                <td>{{ $details->flatDetails->flat_number ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Flat Status :</th>
                                                <td>{{ optional($details->flatDetails)->property_flat_status == 951 ? 'Lease Hold' : 'Free Hold' }}
                                                </td>
                                                <th>Flat Area in Sqm :</th>
                                                <td>{{ $details->flatDetails->area_in_sqm ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Purchase Date :</th>
                                                <td>{{ !empty($details->flatDetails->purchase_date) ? \Carbon\Carbon::parse($details->flatDetails->purchase_date)->format('d-m-Y') : '' }}
                                                </td>
                                                <th>Original Buyer :</th>
                                                <td>{{ $details->flatDetails->original_buyer_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Present Occupant :</th>
                                                <td>{{ $details->flatDetails->present_occupant_name ?? '' }}</td>
                                                <th>Builder / Developer Name :</th>
                                                <td>{{ $details->flatDetails->builder_developer_name ?? '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="part-title">
                <h5>Property Details</h5>
            </div>
            <div class="part-details">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Old Property ID:</th>
                                        <td>{{ $details->old_property_id ?? '' }}</td>
                                        <th>New Property ID:</th>
                                        <td>{{ $details->new_property_id ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Property Status:</th>
                                        <td colspan="3">{{ $propertyCommonDetails['status'] ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lease Type:</th>
                                        <td>{{ $propertyCommonDetails['leaseType'] ?? '' }}</td>
                                        <th>Lease Execution Date:</th>
                                        <td id="leaseExecutionDate">{{ \Carbon\Carbon::parse($details->executed_on)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Property Type:</th>
                                        <td>{{ $propertyCommonDetails['propertyType'] ?? '' }}</td>
                                        <th>Property Sub Type:</th>
                                        <td>{{ $propertyCommonDetails['propertySubType'] ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Presently Known As:</th>
                                        <td>{{ $propertyCommonDetails['presentlyKnownAs'] ?? '' }}</td>
                                        <th>Original Lessee:</th>
                                        <td>{{ $propertyCommonDetails['inFavourOf'] ?? '' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="part-title">
                <h5>Name & Details of Registered Applicant</h5>
            </div>
            <div class="part-details">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12 col-12">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Applicant Number:</th>
                                        <td>{{ $user->applicantUserDetails->applicant_number ?? '' }}</td>
                                        <th>User Type:</th>
                                        <td class="text-uppercase">{{ $user->applicantUserDetails->user_sub_type ?? '' }}
                                        </td>   
                                        <td rowspan="5" class="text-center">
                                            @if($user->applicantUserDetails && $user->applicantUserDetails->profile_photo)
                                                @php
                                                    $imagePath = public_path('storage/' . $user->applicantUserDetails->profile_photo);
                                                    if (file_exists($imagePath)) {
                                                        $imageData = base64_encode(file_get_contents($imagePath));
                                                        $imageSrc = 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $imageData;
                                                    } else {
                                                        $imageSrc = public_path('images/default-avatar.png');
                                                    }
                                                @endphp
                                                <img style="width: 120px;" src="{{ $imageSrc }}">
                                            @else
                                                <img style="width: 120px;" src="{{ public_path('images/default-avatar.png') }}">
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Name:</th>
                                        <td>
                                            {{ $user->name ?? '' }}
                                        </td>
                                        <th>Email:</th>
                                        <td>
                                            @php
                                                if ($user->email) {
                                                    $useremail = $user->email;
                                                    $position = strpos($useremail, '@');
                                                    $email = $useremail
                                                        /* substr($useremail, 0, 2) .
                                                        str_repeat('*', $position - 2) .
                                                        substr($useremail, $position) */;
                                                } else {
                                                    $email = '';
                                                }
                                            @endphp
                                            {{ $email }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gender:</th>
                                        <td>{{ $user->applicantUserDetails->gender ?? '' }}</td>
                                        <th>{{ $user->applicantUserDetails->so_do_spouse }}:</th>
                                        <td> {{ $user->applicantUserDetails->second_name }}</td>
                                    </tr>
                                    <tr>
                                        @php
                                            $pan = $user->applicantUserDetails->pan_card;
                                            $pan =   decryptString($pan) ?? $pan;
                                        @endphp
                                       
                                        <th>PAN:</th>
                                        <td>{{ $pan
                                            /* ? str_repeat('*', 5) . substr($user->applicantUserDetails->pan_card, -5)
                                            : ''  */}}
                                        </td>
                                        <th>Aadhaar:</th>
                                        @php
                                            $applicantAadhaar = $user->applicantUserDetails->aadhar_card;
                                            $applicantAadhaar =  preg_match('/[a-zA-Z]/', $applicantAadhaar) ? decryptString($applicantAadhaar) : $applicantAadhaar;
                                        @endphp
                                        <td> {{ $applicantAadhaar
                                            /* ? substr($applicantAadhaar, 0, 4) .
                                                str_repeat('*', 4) .
                                                substr($applicantAadhaar, -4)
                                            : '' */ }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Mobile:</th>
                                        <td>{{ $user->mobile_no /* ? substr($user->mobile_no, 0, 3) . str_repeat('*', 4) . substr($user->mobile_no, -3) : '' */ }}
                                        </td>
                                        <th>Address:</th>
                                        <td class="w-50">{{ $user->applicantUserDetails->address ?? '' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if (isset($coapplicants) && count($coapplicants) > 0)
                <div class="part-title">
                    <h5>Name & Details of Other Co-Applicants</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            @foreach ($coapplicants as $key => $coapplicant)
                                <div class="col-lg-12 col-12 items" style="position: relative;">
                                    <div
                                        style="
                                position: absolute;
                                right: 10px;
                                padding: 2px 8px;
                                background: #126a6ba8;
                                border-radius: 50%;
                                color: #ffffff;
                            ">
                                        {{ $key + 1 }}
                                    </div>
                                    <div class="parent_table_container">
                                        <table class="table table-bordered" style="margin: 5px 0px;">
                                            <tbody>
                                                <tr>
                                                    <td>Name: <span
                                                            class="highlight_value">{{ $coapplicant->co_applicant_name }}</span>
                                                    </td>
                                                    <td>Gender/DOB: <span
                                                            class="highlight_value">{{ $coapplicant->co_applicant_gender }}/
                                                            {{ \Carbon\Carbon::parse($coapplicant->co_applicant_age)->format('d-m-Y') }}
                                                        </span></td>
                                                    <td>{{ $coapplicant->prefix }}: <span
                                                            class="highlight_value">{{ $coapplicant->co_applicant_father_name }}</span>
                                                    </td>
                                                    <td rowspan="2" class="text-center">
                                                        @if($coapplicant->image_path && $coapplicant->image_path)
                                                            @php
                                                                $imagePath = public_path('storage/' . $coapplicant->image_path);
                                                                if (file_exists($imagePath)) {
                                                                    $imageData = base64_encode(file_get_contents($imagePath));
                                                                    $imageSrc = 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $imageData;
                                                                } else {
                                                                    $imageSrc = public_path('images/default-avatar.png');
                                                                }
                                                            @endphp
                                                            <img style="width: 120px;" src="{{ $imageSrc }}">
                                                        @else
                                                            <img style="width: 120px;" src="{{ public_path('images/default-avatar.png') }}">
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Aadhaar: 
                                                        @php
                                                                $aadhaar = $coapplicant->co_applicant_aadhar;
                                                                $aadhaar =  preg_match('/[a-zA-Z]/', $aadhaar) ? decryptString($aadhaar) : $aadhaar;
                                                            @endphp
                                                            <span
                                                            class="highlight_value">{{ /* strlen($aadhaar) > 8 ? substr($aadhaar, 0, 4). str_repeat('*',strlen($aadhaar) - 8).substr($aadhaar,-4): */ $aadhaar }}
                                                            
                                                        </span><span><a target="_blank"
                                                                href="{{ asset('storage/' . $coapplicant->aadhaar_file_path ?? '') }}">
                                                                (View)
                                                            </a></span>
                                                    </td>
                                                    <td>PAN: <span
                                                            class="highlight_value text-uppercase">{{ $coapplicant->co_applicant_pan }}</span><span><a
                                                                target="_blank"
                                                                href="{{ asset('storage/' . $coapplicant->pan_file_path ?? '') }}">
                                                                (View)</a></span>
                                                    </td>
                                                    <td>Mobile Number: <span
                                                            class="highlight_value">{{ $coapplicant->co_applicant_mobile }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Check if application type is not Deed Of Apartment then only show Details of Lease Deed - Lalit Tiwari (13/02/2025) -->
            @if (!empty($applicationType) && $applicationType != 'Deed Of Apartment')
                <!-- For mutation application -->
                @if (isset($details->name_as_per_lease_conv_deed))
                    <div class="part-title">
                        @if ($details->property_status == 952)
                            <h5>Details of Conveyance Deed</h5>
                        @else
                            <h5>Details of Lease Deed</h5>
                        @endif
                    </div>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Executed In Favour of:</th>
                                                <td>{{ $details->name_as_per_lease_conv_deed ?? '' }}</td>
                                                <th>Executed On:</th>
                                                <td>{{ \Carbon\Carbon::parse($details->executed_on)->format('d-m-Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Regn. No.:</th>
                                                <td>{{ $details->reg_no_as_per_lease_conv_deed ?? '' }}</td>
                                                <th>Book No.:</th>
                                                <td>{{ $details->book_no_as_per_lease_conv_deed ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Volume No.:</th>
                                                <td>{{ $details->volume_no_as_per_lease_conv_deed ?? '' }}</td>
                                                <th>Page No.:</th>
                                                <td>{{ $details->page_no_as_per_lease_conv_deed ?? $details->page_no_as_per_deed }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Regn. Date:</th>
                                                <td id="regnDate" colspan="3">{{ \Carbon\Carbon::parse($details->reg_date_as_per_lease_conv_deed)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <!-- For conversion application -->
                @if (isset($details->applicant_name))
                    <div class="part-title">
                        @if ($details->status == 952)
                            <h5>Details of Conveyance Deed</h5>
                        @else
                            <h5>Details of Lease Deed</h5>
                        @endif
                    </div>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Executed In Favour of:</th>
                                                <td>{{ $details->applicant_name ?? '' }}</td>
                                                <th>Executed On:</th>
                                                <td>{{ \Carbon\Carbon::parse($details->executed_on)->format('d-m-Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Regn. No.:</th>
                                                <td>{{ $details->reg_no ?? '' }}</td>
                                                <th>Book No.:</th>
                                                <td>{{ $details->book_no ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Volume No.:</th>
                                                <td>{{ $details->volume_no ?? '' }}</td>
                                                <th>Page No.:</th>
                                                <td>{{ $details->page_no ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Regn. Date:</th>
                                                <td id="leaseDeedRegnDateNoc" colspan="3">{{ \Carbon\Carbon::parse($details->reg_date)->format('d-m-Y') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <!-- For noc application -->
                @if (isset($details->name_as_per_noc_conv_deed))
                    <div class="part-title">
                        @if ($details->property_status == 952)
                            <h5>Details of Conveyance Deed</h5>
                        @else
                            <h5>Details of Lease Deed</h5>
                        @endif
                    </div>
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th>Executed In Favour of:</th>
                                                <td>{{ $details->name_as_per_noc_conv_deed ?? '' }}</td>
                                                <th>Executed On:</th>
                                                <td id="executedOnNoc">
                                                    {{ \Carbon\Carbon::parse($details->executed_on_as_per_noc_conv_deed)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Regn. No.:</th>
                                                <td>{{ $details->reg_no_as_per_noc_conv_deed ?? '' }}</td>
                                                <th>Book No.:</th>
                                                <td>{{ $details->book_no_as_per_noc_conv_deed ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Volume No.:</th>
                                                <td>{{ $details->volume_no_as_per_noc_conv_deed ?? '' }}</td>
                                                <th>Page No.:</th>
                                                <td>{{ $details->page_no_as_per_noc_conv_deed ?? '' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Regn. Date:</th>
                                                <td id="regnDateNoc" colspan="3">{{ \Carbon\Carbon::parse($details->reg_date_as_per_noc_conv_deed)->format('d-m-Y') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif


            @if (isset($details->property_stands_mortgaged))
                @if ($details->property_stands_mortgaged == 1 || $details->is_basis_of_court_order == 1)
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                @if ($details->property_stands_mortgaged == 1)
                                                    <th>Mortgaged Remark:</th>
                                                    <td>{{ $details->mortgaged_remark ?? '' }}</td>
                                                @endif
                                                @if ($details->is_basis_of_court_order == 1)
                                                    <th>Court Case No.:</th>
                                                    <td>{{ $details->court_case_no ?? '' }}</td>
                                                    <th>Court Case Details:</th>
                                                    <td>{{ $details->court_case_details ?? '' }}</td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif


            <!-- for conversion application -->
             <!-- commented this code by anil not in use sugested by sourav on 24-04-2025 -->
            <!-- @if (isset($details->is_mortgaged))
                @if ($details->is_mortgaged == 1 || $details->is_Lease_deed_lost == 1)
                    <div class="part-details">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <table class="table table-bordered">
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif -->

            <!-- for LUC application -->
            @if (isset($details->property_type_change_to))
                <div class="part-title">
                    <h5>Application Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td colspan="{{ $details->mixed_use == 1 ? 6 : 2 }}"> Application to change
                                                use
                                                of property from
                                                <b>{{ $propertyCommonDetails['propertyType'] }}
                                                    ({{ $propertyCommonDetails['propertySubType'] }})</b> to
                                                <b>{{ getServiceNameById($details->property_type_change_to) ?? '' }}
                                                    ({{ getServiceNameById($details->property_subtype_change_to) ?? '' }})</b>
                                            </td>
                                            {{-- <th>Property Type:</th>
                                            <td></td>
                                            <th>Property Sub Type:</th>
                                            <td>/td> --}}
                                        </tr>
                                        <tr>
                                            <th>Mixed Use</th>
                                            <td>{{ $details->mixed_use == 1 ? 'Yes' : 'No' }}</td>
                                            @if ($details->mixed_use == 1)
                                                <th>Total built up area</th>
                                                <td>{{ $details->total_built_up_area . ' Sq. Mtr.' }}</td>
                                                <th>Area saght as commercial</th>
                                                <td>{{ $details->commercial_area . ' Sq. Mtr.' }}</td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($applicationType == 'Deed Of Apartment')
                <!-- for DOA pplication -->

                <div class="part-title">
                    <h5>Flat Details</h5>
                </div>
                <div class="part-details">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Name:</th>
                                            <td>{{ $details->applicant_name ?? '' }}</td>
                                            <th>Communication Address:</th>
                                            <td>{{ $details->applicant_address ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Building Name:</th>
                                            <td>{{ $details->building_name ?? '' }}</td>
                                            <th>Locality:</th>
                                            <td>{{ $details->locality ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Block:</th>
                                            <td>{{ $details->block ?? '' }}</td>
                                            <th>Plot No.:</th>
                                            <td>{{ $details->plot ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Presently Known As:</th>
                                            <td>{{ $details->known_as ?? '' }}</td>
                                            <th>Flat No.:</th>
                                            <td>{{ $details->flat_number ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>is Flat not listed:</th>
                                            <td>{{ $details->flat_id ? 'True' : 'False' }}</td>
                                            <th>Name of Builder / Developer:</th>
                                            <td>{{ $details->builder_developer_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name Of Original Buyer:</th>
                                            <td>{{ $details->original_buyer_name ?? '' }}</td>
                                            <th>Name Of Present Occupant:</th>
                                            <td>{{ $details->present_occupant_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Purchased From:</th>
                                            <td>{{ $details->purchased_from ?? '' }}</td>
                                            <th>Date of Purchase:</th>
                                            <td>{{ !empty($details->purchased_date) ? \Carbon\Carbon::parse($details->purchased_date)->format('d-m-Y') : '' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Flat Area In (Sq. Mtr.) :</th>
                                            <td>{{ $details->flat_area ?? '' }}</td>
                                            <th>Plot Area In (Sq. Mtr.) :</th>
                                            <td>{{ $details->plot_area ?? '' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @php
                $currentDate = new \DateTime();
                $currentDateFormatted = $currentDate->format('Y-m-d');
            @endphp

            <!-- For Property Document Details Section- SOURAV CHAUHAN (12/Dec/2024)-->
            @include('application.admin.application_document.index')
        </div>
    </div>
