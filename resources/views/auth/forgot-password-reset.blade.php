@extends('layouts.public.app')
@section('title', 'Login')

@section('content')

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">


    <div class="login-8">

        <div class="container">
            <div class="row login-box">
                {{-- <div class="col-lg-12">
                    <div class="fixed_login_container">
                        <div class="title">
                            <div class="bottom-container">
                                Welcome to eDharti<sup>2.0</sup>
                            </div>
                            <div class="top-container">
                                Welcome to eDharti<sup>2.0</sup>
                            </div>
                        </div>

                    </div>
                </div> --}}
                <div class="col-lg-3">
                    {{-- <figure class="swing">
                        <div class="wall-swing">
                            <p>Important Notice</p>
                            <div class="marquee">
                                <ul>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> e-Dharti Geo-Portal 2.0</a>
                                    </li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> e-Dharti</a></li>
                                    <li><a href="/appointment-detail"><i class="fas fa-chevron-right"></i> Office Visit
                                            Appointment</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Club Membership</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Order dt 19-3-2024 reg Public
                                            meetings in LDO English version</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice reg. Public
                                            Hearing in Land and Development Office</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice in News Paper -
                                            instructions</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice in News Paper -
                                            instructions</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice in News Paper -
                                            instructions</a></li>
                                    <li><a href="#"><i class="fas fa-chevron-right"></i> Public Notice in News Paper -
                                            instructions</a></li>
                                </ul>
                            </div>
                        </div>
                    </figure> --}}
                </div>
                <div class="col-lg-6 mx-auto form-section">
                    <div class="form-inner">
                        <div class="container mt-4">

                            <form id="forgotPasswordResetForm" autocomplete="off">
                                {{-- <form method="POST" action="{{ route('forgotPassword.store') }}"> --}}
                                @csrf
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                @if (session('failure'))
                                    <div class="alert alert-danger">{{ session('failure') }}</div>
                                @endif
                                <input type="hidden" name="userId" value="{{ $userId }}">

                                <!-- New Password -->
                                <div class="mb-3">
                                    <input type="password" id="new_password" name="new_password" class="form-control"
                                        minlength="8" title="Password must be at least 8 characters long"
                                        placeholder="Enter New Password" required>
                                    @if ($errors->has('new_password'))
                                        <div class="text-danger mt-2">{{ $errors->first('new_password') }}</div>
                                    @endif
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                        class="form-control" placeholder="Confirm New Password" required>
                                    @if ($errors->has('new_password_confirmation'))
                                        <div class="text-danger mt-2">{{ $errors->first('new_password_confirmation') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3" id="responseMessage" style="margin-top: 10px; text-align: left;"></div>
                                <div class="text-end">
                                    {{-- <button type="submit" class="btn btn-primary">Reset Password</button> --}}
                                    <button type="submit" class="btn btn-primary px-4">Reset Password</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    {{-- <div class="gallery">
                        <div class="block-33 display--inline-top">
                            <div class="gutter relative">
                                <div class="gallery-h">
                                    <div class="gallery-image relative">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('{{ asset('assets/frontend/assets/img/slider/Golf-Course-Club.jpg') }}')">
                                            </div>
                                            <h5>Golf Course Club</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('{{ asset('assets/frontend/assets/img/slider/habitat-center.jpg') }}')">
                                            </div>
                                            <h5>Habitat Center</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('{{ asset('assets/frontend/assets/img/slider/india-gate.jpg') }}')">
                                            </div>
                                            <h5>India Gate</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('{{ asset('assets/frontend/assets/img/slider/Parliament-house.jpg') }}')">
                                            </div>
                                            <h5>Parliament House</h5>
                                        </div>
                                    </div>
                                    <div class="gallery-image">
                                        <div class="gallery-image__img relative">
                                            <div class="fill-dimensions cover-img"
                                                style="background-image:url('{{ asset('assets/frontend/assets/img/slider/rasthtrapati-bhawan.jpg') }}')">
                                            </div>
                                            <h5>Rasthtrapati Bhawan</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="ocean">
            <div class="wave"></div>
            <div class="wave"></div>
        </div>
    </div>
    @include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
    <script>
        $(document).ready(function() {
            $('#forgotPasswordResetForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous messages
                $('#responseMessage').html('');

                // Client-side validation
                let newPassword = $('#new_password').val().trim();
                let confirmPassword = $('#new_password_confirmation').val().trim();

                if (!newPassword || !confirmPassword) {
                    $('#responseMessage').html(
                        '<span style="color:red;">New Password & Confirm Password Field Required.</span>'
                    );
                    return;
                }

                if (newPassword.length < 8) {
                    $('#responseMessage').html(
                        '<span style="color:red;">New password must be at least 8 characters.</span>');
                    return;
                }

                /* const alphanumericRegex = /^(?=.*[a-zA-Z])(?=.*\d)/;
                if (!alphanumericRegex.test(newPassword)) {
                    $('#responseMessage').html(
                        '<span style="color:red;">Password must be alphanumeric (letters and numbers).</span>'
                    );
                    return;
                } */

                if (newPassword !== confirmPassword) {
                    $('#responseMessage').html(
                        '<span style="color:red;">Password & Confirmation password does not match.</span>'
                    );
                    return;
                }

                // Send AJAX request if validation passes
                $.ajax({
                    url: "{{ route('forgotPassword.store') }}",
                    type: "POST",
                    data: $(this).serialize(),

                     success: function(response) {
                        if (response.status) {
                            alert(response.message);
                            // showSuccess(response.message);
                             setTimeout(() => {
                                window.location.href = "/edharti/login";
                            }, 1000);
                            
                        } else {
                            alert(response.message)
                            // showError(response.message)
                        }
                    },
                    error: function(response) {
                        alert(response.message)
                    }
                });
            });
        });
    </script>
@endsection
