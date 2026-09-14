@extends('layouts.app')

@section('title', 'Change Password')

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

        .form-group {
            width: 100%;
            position: relative;
            margin: 0px 0px 12px;
        }

        .verticle-horizontal-center {
            min-height: 79vh;
            display: grid;
            place-items: center;
        }
    </style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Change Password</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    <hr>
    <div class="container-fluid">
        <div class="main-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-12">
                                    {{-- <form method="POST" action="{{ route('password.store') }}"> --}}
                                    <form id="changePasswordForm">
                                        @csrf
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        @if (session('failure'))
                                            <div class="alert alert-danger">
                                                {{ session('failure') }}
                                            </div>
                                        @endif

                                        <div class="row g-2">
                                            <div class="col-lg-12 col-12">
                                                <div class="form-group form-box">
                                                    <label for="current_password" class="form-label">Old Password<span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" name="current_password" id="current_password"
                                                        class="form-control" placeholder="Current Password">
                                                    <div id="current_passwordError" class="text-danger text-left">
                                                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-12">
                                                <div class="form-group form-box">
                                                    <label for="new_password" class="form-label">New Password<span
                                                            class="text-danger">*</span></label>
                                                    <input type="password" name="new_password" id="new_password"
                                                        class="form-control" placeholder="New Password">
                                                    <div id="new_passwordError" class="text-danger text-left"><x-input-error
                                                            :messages="$errors->get('new_password')" class="mt-2" /></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-12">
                                                <div class="form-group form-box">
                                                    <label for="new_password_confirmation" class="form-label">Confirm
                                                        Password<span class="text-danger">*</span></label>
                                                    <input type="password" name="new_password_confirmation"
                                                        id="new_password_confirmation" class="form-control"
                                                        placeholder="Confirm Password">
                                                    <div id="new_password_confirmationError" class="text-danger text-left">
                                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                {{-- <button type="submit" class="btn btn-primary px-4">Change Password</button> --}}
                                                <button type="submit" class="btn btn-primary px-4">Change Password</button>
                                            </div>
                                        </div>

                                    </form>
                                    <div id="responseMessage" style="margin-top: 10px;"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
    <script>
        $(document).ready(function() {
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();

                // Clear previous messages
                $('#responseMessage').html('');

                // Client-side validation
                let oldPassword = $('#current_password').val().trim();
                let newPassword = $('#new_password').val().trim();
                let confirmPassword = $('#new_password_confirmation').val().trim();

                if (!oldPassword || !newPassword || !confirmPassword) {
                    $('#responseMessage').html('<span style="color:red;">All fields are required.</span>');
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
                    url: "{{ route('password.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            showSuccess(response.message)
                            $('#changePasswordForm')[0].reset();
                        } else {
                            showError(response.message)
                        }
                    },
                    error: function(response) {
                        showError(response.message)
                    }
                });
            });
        });
    </script>
@endsection
