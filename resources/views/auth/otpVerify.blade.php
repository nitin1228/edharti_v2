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
                            @if (session('sucess'))
                                <div class="alert alert-success">
                                    {{ session('sucess') }}
                                </div>
                            @endif
                            @if (session('failure'))
                                <div class="alert alert-danger">
                                    {{ session('failure') }}
                                </div>
                            @endif
                            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-start"
                                style="color: #116d6e !important;">
                                An OTP is sent to your {{ isset($email) ? 'email' : 'phone number' }}. Please enter the OTP.
                            </div>
                            <form method="POST" action="{{ route('verification.verify') }}">
                                @csrf
                                <input type="hidden" name="{{ isset($email) ? 'email' : 'phone' }}"
                                    value="{{ isset($email) ? $email : $phone }}">

                                <div class="mb-3">
                                    {{-- <label for="otp" class="form-label">OTP</label> --}}
                                    <input type="text" id="otp" name="otp" placeholder="Enter OTP"
                                        class="form-control" required autocomplete="off">
                                    @if ($errors->has('otp'))
                                        <div class="text-danger mt-2">{{ $errors->first('otp') }}</div>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Verify</button>
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




@endsection

@section('footerScript')
    <script></script>
@endsection
