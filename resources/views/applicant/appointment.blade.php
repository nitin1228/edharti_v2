@extends('layouts.app')

@section('title', 'Book Proof Reading Appointment')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
<style>
    .flatpickr-disabled:not(.nextMonthDay,.taken-by-other){
        background: #6c757d40 !important;
        color: #0000004f !important;
    }
    .flatpickr-day:not(.nextMonthDay){
        background: #1c8b36; 
        color: white;
    }
    .holiday:not(.nextMonthDay){
        background: #dc3545ba !important;
        color:white !important;
    }
    .flatpickr-day.selected{
        background: #fb0 !important;
        color:white !important;
    }

    /* Legend ----*/

    .flatpickr-legend{
        display: flex;
        flex-wrap: wrap;
    }
     .legend-label{
        padding: 5px
    }
    .legend-item{
        width: 20px;
        height: 20px;
        margin-right: 8px;
    }
    .selected{
        background: #fb0 !important;
    }
    .available-date{
        background: #1c8b36;
    }

    /*css copied from public/style.ccs*/

    /* Appointment Date - Passed date */

    span.taken-by-other::before {
        position: absolute;
        content: '';
        top: 50%;
        left: 50%;
        width: 1px;
        height: 80%;
        background: black;
        transform: translate(-50%, -50%) rotate(45deg);
    }

    span.taken-by-other::after {
        position: absolute;
        content: '';
        top: 50%;
        left: 50%;
        width: 1px;
        height: 80%;
        background: black;
        transform: translate(-50%, -50%) rotate(-45deg);
    }

    span.taken-by-other {
        position: relative;
    }

    #spinnerOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        /* Ensure it covers other content */
    }

    /* commented and adeed by anil for replace the new loader on 24-07-2025  */
    /* .spinner {
        border: 8px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top: 8px solid #ffffff;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    } */
    .loader {
        width: 48px;
        height: 48px;
        border:6px solid #FFF;
        border-radius: 50%;
        position: relative;
        transform:rotate(45deg);
        box-sizing: border-box;
    }
    .loader::before {
        content: "";
        position: absolute;
        box-sizing: border-box;
        inset:-7px;
        border-radius: 50%;
        border:8px solid #116d6e;
        animation: prixClipFix 2s infinite linear;
    }

    @keyframes prixClipFix {
        0%   {clip-path:polygon(50% 50%,0 0,0 0,0 0,0 0,0 0)}
        25%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 0,100% 0,100% 0)}
        50%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,100% 100%,100% 100%)}
        75%  {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 100%)}
        100% {clip-path:polygon(50% 50%,0 0,100% 0,100% 100%,0 100%,0 0)}
    }
    /* commented and adeed by anil for replace the new loader on 13-08-2025  */
</style>
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Appointment</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Proof Reading</li>
                </ol>
            </nav>
        </div>
    </div>
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="row d-flex align-items-center justify-content-between">
                <div class="col-md-6">
                    <form>
                        <input type="hidden" name="appointmentId" id="appointmentId" value="{{$appointmentData->id}}">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="appointmentDate">Appointment Date</label>
                                <input type="text" name="appointmentDate" id="appointmentDate" class="form-control">
                            </div>
							<div class="col-lg-4 col-12" id="app_timeSlotDiv" style="display: none;">
								<div class="form-group form-box">
									<label for="app_meeting_time" class="quesLabel">Select a Time Slot<span class="text-danger">*</span></label>
									<select name="meetingTime" id="app_meeting_time" class="form-select">
										<option value="">Select</option>
									</select>
									<div id="app_meetingTimeError" class="text-danger text-left"></div>
								</div>
							</div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-success" id="appointmentButton" style="margin-top:20px" onclick="bookAppointment()">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 d-flex align-item-center justify-content-end">
                    <div>
                    <label class="note text-danger text-sm"><strong>Note<span class="text-danger">*</span>:</strong> Only two appointments are allowed on this link</label>
                </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Appointment details for this application -->
    <!-- <div class="col-lg-12 col-12 p-2 p-md-0">
                <div class="col-lg-12 col-12" style="margin-bottom: 0px; height:100%">
                    <div class="card purplecard public_service" style="margin-bottom: 0px;">
                        <h4 class="pubser-title">Appointment Details</h4>
                        <div class="card-body">
                            <div class="dashboard-card-view">
                                <div class="grievance-card-item row">
                                    <div class="col-12 col-md-5 d-flex justify-content-between align-items-center">
                                        <div class="services-count">
                                            <h4 class="services_count_text"><span id="grievencesCount">{{$appointmentData->application_no}}</span><br><span class="" style="
                                            font-size: 11px;
                                            color: #acacac;
                                            ">(Application No.)</span></h4>
                                        </div>
                                        <div class="services-label">
                                            <img src="/assets/images/Schedule.svg" alt="Appointments">
                                            <h4>{{ date('d M Y', strtotime($appointmentData->schedule_date)) }}<br><span class="" style="
                                                font-size: 11px;
                                                color: #acacac;
                                            ">(Scheduled Date)</span></h4>
                                        </div>
                                    </div>
                                    <div class="col-1 border-end border-2">

                                    </div>
                                    <div class="col-12 col-md-6 pt-4 pt-md-0">
                                        <ul class="appointmentInstr">
                                            <li>Please bring all documents that were uploaded to the eDharti portal during your application submission for verification.</li>
                                            <li>Ensure that you arrive on time for your scheduled appointment.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->






@if($appointmentData->schedule_date)
            <div class="col-lg-12 col-12 p-2 p-md-0 mt-3">
                <div class="col-lg-12 col-12" style="margin-bottom: 0px; height:100%">
                    <div class="card purplecard public_service" style="margin-bottom: 0px;">
                        <div class="card-body">
                        <h5 class="mb-1">APPOINTMENT DETAILS</h5>
                            <div class="dashboard-card-view">
                                <div class="grievance-card-item">
                                    <div class="">
                                        <div class="">
                                            <p class="services_count_text"><b>Application No.:</b> <span id="grievencesCount">{{$appointmentData->application_no}}</span></p>
                                        </div>
                                        <div class="">
                                          <p class="services_count_text"><b>Scheduled Date:</b> <span id="grievencesCount">{{ date('d-m-Y', strtotime($appointmentData->schedule_date)) }}</span></p>
                                         <p class="services_count_text"><b>Scheduled Time:</b> <span id="grievencesCount">{{ $appointmentData->schedule_timeslot }}</span></p>
                                        </div>
                                    </div>
                                    <div class="">
                                        <h6>Note:</h6>
                                        <ul class="appointmentInstr">
                                            <li>Please bring all documents that were uploaded to the eDharti portal during your application submission for verification.</li>
                                            <li>Ensure that you arrive on time for your scheduled appointment.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endif
    <!-- <div class="card">
                <div class="card-body">
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Application No</th>
                                <th scope="col">Schedule Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> -->

<!-- commented and adeed by anil for replace the new loader on 07-08-2025  -->
<!-- <div id="spinnerOverlay" style="display:none;">
    <div class="spinner"></div>
    <img src="{{ asset('assets/images/chatbot_icongif.gif') }}">
</div> -->
<div id="spinnerOverlay" style="display:none;">
        <span class="loader"></span>
        <h1 style="color: white;font-size: 20px; margin-top:10px;">Loading... Please wait</h1>
    </div>
<!-- commented and adeed by anil for replace the new loader on 07-08-2025  -->

    @include('include.alerts.ajax-alert')
@endsection

@section('footerScript')

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
$(document).ready(function () {
		let calendarData = @json($calendarData);		
		const meetingTimeElement = document.getElementById("app_meeting_time");
		const timeSlotDiv = document.getElementById("app_timeSlotDiv");
		const today = new Date();
		today.setHours(0, 0, 0, 0);
		const minDate = new Date(calendarData.minDate);
		minDate.setHours(0, 0, 0, 0);
		$("#appointmentDate").flatpickr({
			...calendarData,
			altFormat: "d-m-Y",altInput: true,
  			allowInput: true, 			
			onDayCreate: function (dObj, dStr, fp, dayElem) {
				const holidays = calendarData.holidays;
				const bookedDates = calendarData.bookedDates;
				//alert(bookedDates);
				const dateObj = new Date(dayElem.dateObj);
				const dayOfWeek = dateObj.getDay();
				const year = dateObj.getFullYear();
				const month = String(dateObj.getMonth() + 1).padStart(2, "0");
				const day = String(dateObj.getDate()).padStart(2, "0");
				const localDate = `${year}-${month}-${day}`;
				console.log(localDate);
				if (holidays.includes(localDate)) {
					dayElem.classList.add("holiday");
					//console.log(dayElem);
				}
				if (bookedDates.includes(localDate)) {
					dayElem.classList.add("taken-by-other");
				}
				const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
				if (dateObj >= minDate && dateObj < today && !isWeekend) {
					dayElem.classList.add("passed-date", "flatpickr-disabled");
					dayElem.removeAttribute("tabindex");
				}
				
			},

			onReady: function (selectedDates, dateStr, instance) {				
				const legend = `
                <div class="flatpickr-legend">
                    <div class="d-flex legend-label"><span class="legend-item selected"></span> <label>Present appointment</label></div>
                    <div class="d-flex legend-label"><span class="legend-item available-date"></span> <label>Available</label></div>
                    <div class="d-flex legend-label"><span class="legend-item holiday"></span> <label>Holiday</label></div>
                    <div class="d-flex legend-label"><span class="legend-item taken-by-other"></span><label>All Slots booked</label></div>
                    <div class="d-flex legend-label"><span class="legend-item flatpickr-disabled"></span><label>Disabled</label></div>
					<div class="d-flex legend-label"><span class="legend-item passed-date"></span><label>Passed Date</label></div>
                </div>
            `;
				const calendarContainer = instance.calendarContainer;
				$(calendarContainer).append(legend);
			},

			onChange: function (selectedDates, dateStr, instance) {
				
				if (dateStr) {
					fetchAvailableTimeSlots(dateStr);
				}
			}
		});

		// new  functions added  in 16-09-2025
		function fetchAvailableTimeSlots(date)
		{
			fetch(getBaseURL() + `/applications/get-available-time-slots?date=${date}`)
			.then(response => response.json())
			.then(data => {
				meetingTimeElement.innerHTML = '<option value="">Select a time slot</option>';
				if (data.length > 0) {
					data.forEach(timeSlot => {
						let option = document.createElement("option");
						option.value = timeSlot;
						option.textContent = timeSlot;
						meetingTimeElement.appendChild(option);
					});
					timeSlotDiv.style.display = "block";
				} else {
					timeSlotDiv.style.display = "none";
				}
			})
			.catch(error => console.error("Error fetching time slots:", error));
		}
	});
    function bookAppointment(){

        const appontmentButon = $("#appointmentButton")
        appontmentButon.html('submitting...')
        appontmentButon.prop('disabled', true);
        const spinnerOverlay = document.getElementById('spinnerOverlay');
        if(spinnerOverlay){
            spinnerOverlay.style.display = 'flex';
        }

        var dateString = $('#appointmentDate').val();
        var appointmentId = $('#appointmentId').val();
        var appointmentDate = dateString;
		var appointmentTime = $('#app_meeting_time').val(); //New line add
        $.ajax({
            type: "POST",
            url: "{{route('applicant.bookAppointment')}}",
            data:{
                _token  :   "{{csrf_token()}}",
                appointmentId   :   appointmentId,
                appointmentDate :   appointmentDate,
				appointmentTime :   appointmentTime //New line add
            },
            success:function(response){
                if(response.status){
                    showSuccess(response.message, window.location.href);
                }
                else{
                    spinnerOverlay.style.display = 'none';
                    appontmentButon.prop('disabled', false);
                    appontmentButon.html('Save')
                    showError(response.message);
                }
            },
            error:function(response){
                spinnerOverlay.style.display = 'none';
                appontmentButon.prop('disabled', false);
                appontmentButon.html('Save')
                showError(response.resposnseJson.error)
            }
        })
    }

    $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                // responsive: true,
                ajax: {
                    url: "{{ route('applicant.getappointments') }}",
                    type: "GET",
                },
                columns: [{
                        data: null,
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // Auto-increment ID based on row index
                        },
                        orderable: false, // Disable ordering on this column
                        searchable: false // Disable searching on this column
                    },
                    {
                        data: 'application_no',
                        name: 'application_no'
                    },
                    {
                        data: 'schedule_date',
                        name: 'schedule_date'
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
                buttons: ['csv', 'excel', 'pdf'],
                scrollX: true,
            });

            // Filter button click event
            $('#filter').click(function() {
                table.draw(); // Redraw the DataTable to apply the date filters
            });
        });
</script>
@endsection
