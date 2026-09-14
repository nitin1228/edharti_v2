<!-- //updated by Swati on 22062026 for old demands -->
@extends('layouts.app')


@section('title', 'Old Demand Listing')

@section('content')
<!-- anil addded a css for old demand table css on 22-06-2026 -->
<style>
    .RGR_table_design tbody tr td {
    background-color: #ffffff;
}
</style>
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Demand</div>

    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-alt"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">Demand</li>
                <li class="breadcrumb-item active">Old Demands</li>
            </ol>
        </nav>
    </div>
</div>
<hr>
<div class="card">
    <div class="card-body">
        <!-- anil addded a ui design for old demand cards scection into a table on 22-06-2026 -->
        <div class="col"> 
            <div class="table-responsive">
                <table class="table table-bordered table-info table-top RGR_table_design" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Pending</th>
                            <th>Email Sent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $totalDemands }}</td>
                            <td>{{ $paidDemands }}</td>
                            <td>{{ $pendingDemands }}</td>
                            <td>{{ $emailSentDemands }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table id="example" class="table table-striped display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Property Details</th>
                        <th>Demand ID</th>
                        <th>Section Code</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Outstanding</th>
                        <th>Demand Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($oldDemands as $oldDemand)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $oldDemand->property_id }}</td>
                            <td>{{ $oldDemand->demand_id }}</td>
                            <td>{{ $oldDemand->section ?? 'N/A' }}</td>
                            <td>
                                ₹&nbsp;{{ customNumFormat(round($oldDemand->amount, 2)) }}
                            </td>

                            <td>
                                ₹&nbsp;{{ customNumFormat(round($oldDemand->paid_amount, 2)) }}
                            </td>

                            <td>
                                ₹&nbsp;{{ customNumFormat(round($oldDemand->outstanding, 2)) }}
                            </td>

                            <td>
                                {{ !empty($oldDemand->demand_date) ? \Carbon\Carbon::parse($oldDemand->demand_date)->format('d-m-Y') : 'N/A' }}
                            </td>

                            <td>
                                @if($oldDemand->outstanding > 0)
                                    <span class="badge bg-warning text-dark">
                                        Pending
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        Paid
                                    </span>
                                @endif
                            </td>

                            <td>
                                <button
                                    type="button"
                                    class="btn btn-info breakupBtn"
                                    data-id="{{ $oldDemand->demand_id }}">
                                    Demand Breakup
                                </button>
                                @if($oldDemand->outstanding > 0)
                                    <button
                                        type="button"
                                        class="btn btn-success sendOldDemandEmailBtn"
                                        data-id="{{ $oldDemand->demand_id }}">
                                        Send Email
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No Data to Display</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="breakupModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Old Demand Breakup</h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body" id="breakupModalBody">
                <div class="text-center">Loading...</div>
            </div>

        </div>
    </div>
</div>

@include('include.parts.demand-reminder')

@endsection

@section('footerScript')

<script>
$(document).ready(function() {
    $('#example').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                text: 'Export CSV',
                className: 'btn btn-info',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ]
    });
});

$(document).on('click', '.breakupBtn', function () {

    let demandId = $(this).data('id');

    $('#breakupModal .modal-title').html('Old Demand Breakup');
    $('#breakupModalBody').html('<div class="text-center">Loading...</div>');
    $('#breakupModal').modal('show');

    $.ajax({
        url: '{{ route("oldDemandData", ":id") }}'.replace(':id', demandId),
        type: 'GET',

        success: function (response) {
            $('#breakupModalBody').html(response);

            // Hide create-demand-only fields only on this index page modal
            $('#breakupModalBody .hideSection').hide();
            $('#breakupModalBody td:has(.check-include-in-demand)').hide();

            // Prevent nested modal overlap only here
            $('#breakupModalBody #full-demand-details')
                .removeAttr('onclick')
                .off('click')
                .on('click', function () {
                    let demandId = $(this).closest('tr').find('td:first').text().trim();

                    let baseUrl = '{{ route("oldDemandBreakUp", ["oldDemandId" => "__ID__"]) }}';
                    let url = baseUrl.replace('__ID__', demandId);

                    $('#breakupModal .modal-title').html('Subhead-wise breakup of demand id: ' + demandId);
                    $('#breakupModalBody').html('<div class="text-center">Loading...</div>');
                    $('#breakupModalBody').load(url);
                });
        },

        error: function () {
            $('#breakupModalBody').html(`
                <div class="alert alert-danger">
                    Failed to load demand breakup.
                </div>
            `);
        }
    });
});

$(document).on('click', '.sendOldDemandEmailBtn', function () {

    let demandId = $(this).data('id');

    $('#reminder_demand_id').val(demandId);
    $('#email_sent_count').html('Loading...');
    $('#last_reminder_sent_at').html('Loading...');

    $('#reminder_name').val('');
    $('#reminder_email').val('');
    $('#reminder_address').val('');

    $('#reminderModal').modal('show');

    $.ajax({
        url: '{{ route("oldDemandReminderDetails", ":id") }}'
                .replace(':id', demandId),

        type: 'GET',

        success: function(response) {

            if (response.status) {

                $('#email_sent_count')
                    .html(response.email_sent_count);

                $('#last_reminder_sent_at')
                    .html(response.last_reminder_sent_at);

                $('#reminder_name')
                    .val(response.name);

                $('#reminder_email')
                    .val(response.email);

                $('#reminder_address')
                    .val(response.address);

            } else {

                alert(response.message);
                $('#reminderModal').modal('hide');
            }
        },

        error: function() {

            alert('Unable to fetch reminder details.'); 
            $('#reminderModal').modal('hide');
        }
    });
});

$(document).on('submit', '#sendReminderForm', function(e) {

    e.preventDefault();

    let button = $('#finalSendReminderBtn');

    button.prop('disabled', true)
          .html('Sending...');

    $.ajax({

        url: '{{ route("sendOldDemandReminder") }}',

        type: 'POST',

        data: $(this).serialize(),

        success: function(response) {

            button.prop('disabled', false)
                  .html('Send Reminder Email');

            alert(response.message);

            if (response.status) {

                $('#reminderModal').modal('hide');

                // refresh counts and table data
                location.reload();
            }
        },

        error: function(xhr) {

            button.prop('disabled', false)
                  .html('Send Reminder Email');

            if (xhr.responseJSON?.message) {
                alert(xhr.responseJSON.message);
            } else {
                alert('Something went wrong.');
            }
        }
    });
});
</script>

@endsection