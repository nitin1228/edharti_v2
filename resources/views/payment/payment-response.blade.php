@php
    $extends = Auth::check() ? 'layouts.app' : 'layouts.public.app';
@endphp
@extends($extends)
@section('title', 'Payment Response')

@section('content')

<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
<div class="payment-wrap login-8">
     <div class="container">
          @if(isset($status) && $status == "SUCCESS")
               <div class="row justify-content-center">
                    <div class="col-sm-10 col-md-8 col-xl-5">
                         <div class="message-box success">
                              <h3 class="title">Payment Status</h3>
                              <i class="fa fa-check-circle" aria-hidden="true"></i>
                              <!-- <h2> Your payment was successful </h2> -->
                                   <h2> Payment Successful </h2>
                                   <br/>
                                   <!-- <p> Thank you for your payment. we will <br> be in contact with more details shortly </p> -->
                              
                              <hr>
                              <div class="message-footer mt-5">
                                   <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                        <a href="{{route('dashboard')}}"> <button class="btn btn-primary me-md-2" type="button">Go to dashboard</button></a>
                                   <a href="{{route('downloadPaymentReceiptPdf',$orderCode)}}">  
                                   <button class="btn btn-primary" type="button">Download Receipt</button></a>
                                   </div>
                              </div>
                         </div> 
                    </div>
               </div>
          @elseif(isset($status) && $status == "PENDING")
                         <div class="row justify-content-center">
                         <div class="col-sm-10 col-md-8 col-xl-5">
                              <div class="message-box success failed">
                                   <h3 class="title">Payment Status</h3>
                                   <i class="fa fa-times-circle" aria-hidden="true"></i>
                                   <!-- <h2> Your payment is Pending </h2> -->
                                   <h2> Payment Pending </h2>
                                   <br/>
                                   <!-- <h4> We are processing your payment. Please wait for confirmation.</h4>  -->
                                   <hr>
                                   <div class="message-footer mt-5">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                        <a href="{{route('dashboard')}}"> <button class="btn btn-primary me-md-2" type="button">Go to dashboard</button></a>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div> 
               @else
               <div class="row justify-content-center">
                    <div class="col-sm-10 col-md-8 col-xl-5">
                         <div class="message-box success failed">
                              <h3 class="title">Payment Status</h3>
                              <i class="fa fa-times-circle" aria-hidden="true"></i>
                              <!-- <h2> Your payment failed </h2> -->
                              <h2> Payment Failed </h2>
                              <br/>
                              <!-- <h4>Try after some time</h4>  -->
                              <hr>
                              <div class="message-footer mt-5">
                                   <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                   <a href="{{route('dashboard')}}"> <button class="btn btn-primary me-md-2" type="button">Go to dashboard</button></a>
                                   </div>
                              </div>
                         </div>
                    </div>
               </div>
               @endif

     </div>
</div>

@endsection
