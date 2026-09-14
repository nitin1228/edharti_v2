@extends('layouts.app')

@section('title', 'Applicant Profile')

@section('content')
    <style>
        .pagination .active a {
            color: #ffffff !important;
        }

        .required-error-message {
            display: none;
        }

        .required-error-message {
            margin-left: -1.5em;
            margin-top: 3px;
        }

        .form-check-inputs[type=checkbox] {
            border-radius: .25em;
        }

        .form-check .form-check-inputs {
            float: left;
            margin-left: -1.5em;
        }

        .form-check-inputs {
            width: 1.5em;
            height: 1.5em;
            margin-top: 0;
        }
    </style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">ACCOUNT DETAILS</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    <hr>
    <div class="container-fluid">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                @if (isset($user->applicantDetails->profile_photo))
                                <!-- comented by anil for rounded circle to square on 09-10-2025 -->
                                    <!-- <img src="{{ asset('storage/' . $user->applicantDetails->profile_photo) }}"
                                        alt="Admin" class="rounded-circle p-1 bg-primary" width="155"> -->
                                    <img src="{{ asset('storage/' . $user->applicantDetails->profile_photo) }}"
                                        alt="Admin" class="p-1 bg-primary" width="155">
                                @else
                                <!-- comented by anil for rounded circle to square on 09-10-2025 -->
                                    <!-- <img src="{{ asset('assets/images/avatars/avatar-1.png') }}" alt="Admin"
                                        class="rounded-circle p-1 bg-primary" width="155"> -->
                                    <img src="{{ asset('assets/images/avatars/avatar-1.png') }}" alt="Admin"
                                        class="p-1 bg-primary" width="155">
                                @endif
                                <div class="mt-3">
                                    <h4>{{ $user->name ?? '' }}</h4>
                                    <p class="text-secondary mb-1">{{ $user->email ?? '' }}</p>
                                    <p class="text-muted font-size-sm">{{ $user->mobile_no ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            
                            <form action="{{ route('applicant.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Mobile No:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" name="mobile_no" class="form-control"
                                            value="{{ old('mobile_no', $user->mobile_no) }}" required>
                                    </div>
                                </div>

                                {{-- Only for Individual --}}
                                @if(optional($user->applicantDetails)->user_sub_type === 'individual')
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Communication Address:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <textarea name="address" class="form-control" rows="2" required>{{ old('address', $user->applicantDetails->address ?? '') }}</textarea>
                                    </div>
                                </div>
                                @endif

                                 <!-- PROFILE PHOTO -->
                                <!-- <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Update Profile Photo:</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="file" name="profile_photo" id="profile_photo"
                                            class="form-control" accept="image/*" onchange="previewFile(this)">
                                        <small class="text-muted">Accepted formats: JPG, PNG. Max size: 2MB.</small>
                                    </div>
                                </div> -->

                                <div class="row">
                                    <div class="col-sm-12 text-end">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection
@section('footerScript')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
@endsection
