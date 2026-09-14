
@if ($applicationAppointmentLink && $applicationAppointmentLink->is_active == 1)
                <div class="col-lg-12 mt-4">
                    <div class="proof-reading-details">
                        <h4 class="proof-reading-details">Proof Reading Details</h4>
                        <div class="proof-reading-content">
                            <p class="appointment-link"><span>Appointment Link:</span>
                                {{ $applicationAppointmentLink['link'] }}
                            </p>
                            @if (isset($applicationAppointmentLink['valid_till']))
                                <p class="appointment-schedule-date"><span>Valid Till:</span>
                                    <span id="proofReadValidDate">{{ $applicationAppointmentLink['valid_till'] }}</span>
                                </p>
                            @endif
                            @if (isset($applicationAppointmentLink['schedule_date']))
                                <p class="appointment-schedule-date mt-2"><span>Schedule Date:</span>
                                <span id="proofReadScheduleDate">{{ $applicationAppointmentLink['schedule_date'] }}</span>
                                </p>
                            @endif
                            @if (isset($applicationAppointmentLink['schedule_timeslot']))
							<p class="appointment-schedule-date mt-2"><span>Schedule Time:</span>
								<span id="proofReadScheduleDate">{{ $applicationAppointmentLink['schedule_timeslot'] }}</span>
							</p>
							@endif
                        </div>
                    </div>
                </div>
            @endif