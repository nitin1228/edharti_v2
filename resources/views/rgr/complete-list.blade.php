@extends('layouts.app')

@section('title', 'Revised Properties')

@section('content')
<link rel="stylesheet" href="{{asset('assets/css/rgr.css')}}">

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">RGR</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">RGR</li>
                    <li class="breadcrumb-item active" aria-current="page">Revised Properties</li>
                </ol>
            </nav>
        </div>
    </div>
<!--end breadcrumb-->

<hr>


<div class="card">
    <div class="card-body">
        <div class="row">
            @include('include.parts.rgr-list',['data'=>$data,'highlighted'=>$highlighted])
        </div>
    </div>
</div>

@endsection