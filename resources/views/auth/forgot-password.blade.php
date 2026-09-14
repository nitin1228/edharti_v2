@extends('layouts.public.app')
@section('title', 'Forgot Password')

@section('content')

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">


    <div class="login-8 forgot-passwrap">

        <div class="container">
            <div class="row login-box">
                
                <div class="col-lg-3">
                    
                </div>
                <div class="col-lg-6 mx-auto form-section">
                    <div class="form-inner" style="color: #116d6e;">
                        <h3>Forgot password</h3>
                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 text-start">
                            Please enter your email address or phone number, and we will send you an OTP to reset your password.
                        </div>
                        <form method="POST" action="{{ route('password.otp') }}" autocomplete="off">
                            @csrf

                            <div class="mb-4">
                                
                                <input id="email" type="email" class="form-control" name="email"
                                    placeholder="Enter Registered Email" autofocus>
                                @if ($errors->has('email'))
                                    <div class="text-danger mt-2 text-start">{{ $errors->first('email') }}</div>
                                @endif
                            </div>
                            <h6 class="text-divider" id="dividerLogin"><span>OR</span></h6>

                            <div class="mb-4">
                                <input id="phone" type="text" class="form-control" name="phone"
                                    placeholder="Enter Registered Mobile Number" pattern="[0-9]{8,12}" autofocus>
                                @if ($errors->has('phone'))
                                    <div class="text-danger mt-2 text-start">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">Get OTP</button>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="col-lg-3">
                    
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
