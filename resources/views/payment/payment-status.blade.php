@extends('layouts.public.app')

@section('title', 'eDharti 2.0 Payment Status')

@section('content')
<style>
    .submitButtonDiv {
        margin-top: auto;
        margin-bottom: 1em;
    }

    #additionalInputDiv {
        display: flex;
        flex-direction: row;
    }

    #additionalInputDiv>* {
        flex: 1;
        /* Makes all children grow equally to fill the available space */
    }

    .or {
        margin-top: 2.5em !important;
    }

    .or h5 {
        text-align: center;
    }
</style>
<div class="login-8">
   <div class="container">
    <div class="row login-box mb-2">
        <div class="col-lg-12 form-section">
            <div class="form-inner">

                <div class="form-inner-head">
                    <h3>eDharti 2.0 Payment Status</h3>
                </div>
                <form action="" method="post" autocomplete="off" class="text-left" id="searchForm">
                    <h6 class="mb-3">Search By :</h6>
                    <div class="mb-3">
                        <div class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="searchType" value="payment">
                            <label class="form-check-label">Payment ID</label>
                        </div>

                        <div class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="searchType" value="transaction">
                            <label class="form-check-label">Transaction No</label>
                        </div>

                        <div class="form-check form-check-inline me-3">
                            <input class="form-check-input" type="radio" name="searchType" value="property">
                            <label class="form-check-label">Property ID</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="searchType" value="payee">
                            <label class="form-check-label">Payee Details</label>
                        </div>
                    </div>

                    <!-- Input Fields -->
                    <div id="paymentBox" class="d-none mb-2">
                        <input type="text" class="form-control" placeholder="Enter Payment ID" name="payment_id">
                         <small class="text-danger error-msg"></small> 
                        <br> 
                      <span>Example ID:  <span class="text-success">  PACXXXXXXXXXXXXXX, GRXXXXXXXXXXXXX</span></span>
                    </div>

                    <div id="transactionBox" class="d-none mb-2">
                        <input type="text" class="form-control" placeholder="Enter Transaction No" name="transaction_no">
                         <small class="text-danger error-msg"></small> 
                        <br> 
                        <span>Example No.:  <span class="text-success">  06XXXXXXXXXXX </span></span>
                          
                    </div>

                    <div id="propertyBox" class="d-none mb-2">
                        <input type="text" class="form-control" placeholder="Enter Property ID" name="property_id"> 
                           <small class="text-danger error-msg"></small> 
                        <br>                                        
                        <span>Example ID:  <span class="text-success">  XXXXX</span></span>     
                    </div>
                    <div id="payeeBox" class="d-none mb-2">
                    <div class="row">
                    <div class="col-md-6">
                     	<input type="email" class="form-control  mb-2" placeholder="Enter Email" name="email">  <small class="text-danger error-msg"></small></div>
                     	<div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Enter Mobile No" name="mobile">  <small class="text-danger error-msg"></small></div>                       
                    </div></div>

                </form>
   <button type="button" class="btn btn-primary mt-3 d-none" id="submitButton1" >Search </button>
            </div>
           
        </div>
         <div id="result" class="mt-2"></div>
    </div>
</div>
</div>

@include('include.alerts.ajax-alert')
@endsection

@section('footerScript')
<style>
	.login-8 .form-section .form-check-input {     
    border: 1px solid #bbb;
}
</style>
<script src="{{asset('assets/js/demandPayment.js')}}"></script>
<script src="{{asset('assets/js/addressDropdown.js')}}"></script>
<script>
			$.ajaxSetup({
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
				$(document).ready(function () {
			    $('input[name="searchType"]').on('change', function () {
			        $('#paymentBox, #transactionBox, #propertyBox, #payeeBox')
			            .addClass('d-none');
			        $('#' + $(this).val() + 'Box')
			            .removeClass('d-none');
			        $('.error-msg').text('');
			         $('input[type="text"],input[type="email"]').val("");
			           $("#result").html("");
			        $('input').removeClass('is-invalid');
			        $("#submitButton1").removeClass('d-none');
			    });
			    $("#submitButton1").on('click', function (e) {
			        e.preventDefault();
			        $('.error-msg').text('');
			        $('input').removeClass('is-invalid');
			        let selected = $('input[name="searchType"]:checked').val();
			        if (!selected) {
			            alert("Please select a search type");
			            return;
			        }
			        let isValid = true;
			        let box = $('#' + selected + 'Box');
			        if (selected === 'payee') {
			            let emailInput = box.find('input[name="email"]');
			            let mobileInput = box.find('input[name="mobile"]');
			            let email = emailInput.val().trim();
			            let mobile = mobileInput.val().trim();
			            if (email === '' && mobile === '') {
			                isValid = false;
			                emailInput.addClass('is-invalid');
			                mobileInput.addClass('is-invalid');
			                emailInput.next('.error-msg')
			                    .text('Email or Mobile is required');
			                mobileInput.next('.error-msg')
			                    .text('Email or Mobile is required');
			            }
			        } else {
			            box.find('input').each(function () {
			                if ($(this).val().trim() === '') {
			                    isValid = false;
			                    $(this).addClass('is-invalid');
			                    $(this).next('.error-msg')
			                        .text('This field is required');
			                }
			            });
			        }
			        if (!isValid) return;
			        let formData = $("#searchForm").serialize();
			        $.ajax({
			            url: "{{ route('userPaymentStatusCheck') }}",
			            type: "GET",
			            data: formData,
			            success: function (response) {
			                $("#result").html(response);
			            },
			            error: function (xhr) {
			                console.log(xhr.responseText);
			                alert("Something went wrong");
			            }
			        });
			    });
			});			
</script>
<script>  

//    $('#submitButton1').click(function() {
//        var paymentType = $('#payemntType').val();
//        var additionalInput = $('#additionalInput');
//        var inputName = additionalInput.attr('name');
//        var inputValue = additionalInput.val();
//        $.ajax({
//            type: "GET",
//            url: "{{route('getPaymentDetails')}}",
//            data: {
//                paymentType: paymentType,
//                inputName: inputName,
//                inputValue: inputValue
//            },
//            success: function(response) {
//                console.log(!response.status);
//                if (!response.status) {
//                    showError(response.details);
//                } else {
//                    $('#paymentDetails').html(response.html);
//                    $('#paymentDetailsRow').removeClass('d-none');
//                }
//            }
//        });
//    });
</script>

@endsection
