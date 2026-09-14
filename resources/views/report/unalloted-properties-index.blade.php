@extends('layouts.app')

@section('title', 'Unalloted Property Listings')

@section('content')

    <style>
        div.dt-buttons {
            float: none !important;
            /* width: 19%; */
            width: 33%;
            /* chagned by anil on 28-08-2025 to fix in resposive */
        }

        div.dt-buttons.btn-group {
            margin-bottom: 20px;
        }

        div.dt-buttons.btn-group .btn {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }

        /* Ensure responsiveness on smaller screens */
        @media (max-width: 768px) {
            div.dt-buttons {
                width:100%;
            }
            
            div.dt-buttons.btn-group {
                flex-direction: column;
                align-items: flex-start;
            }

            div.dt-buttons.btn-group .btn {
                width: 100%;
                text-align: left;
            }
        }
  /* Added by Swati Mishra for adding scroller to the survey modal on 23052025 */

        #surveyModal .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        .expandable-img {
            cursor: zoom-in;
            transition: all 0.3s ease-in-out;
        }

        .fullscreen-img {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100vw !important;
            height: 100vh !important;
            object-fit: contain;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 1055; /* above modal */
            cursor: zoom-out;
            margin: 0 !important;
            padding: 0 !important;
        }


        /* Ensure responsiveness on smaller screens */
        @media (max-width: 768px) {
            div.dt-buttons.btn-group {
                flex-direction: column;
                align-items: flex-start;
            }

            div.dt-buttons.btn-group .btn {
                width: 100%;
                text-align: left;
            }
        }
    </style>
    <!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Reports</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Report of Unallotted Properties</li>
            </ol>
        </nav>
    </div>
    <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>
<!--breadcrumb-->
<!--end breadcrumb-->


    <div class="card mx-3 mb-5">
        <div class="card-body ">
          <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="locality_record" class="quesLabel">Locality<span
                                        class="text-danger">*</span></label>
                                <select name="localityRecord" id="locality_record" class="form-select">
                                    <option value="">Select</option>
                                    @foreach ($colonyList as $colony)
                                        <option value="{{ $colony->id }}">{{ $colony->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="locality_recordError" class="text-danger text-left"></div>
                            </div>
                        </div>
            <div class="d-flex justify-content-between py-3">
                <h6 class="mb-0 text-uppercase tabular-record_font align-self-end"></h6>
            </div>
			<div class="table-responsive mt-2">
				<table id="example" class="display nowrap" style="width:100%">
					<thead>
						<tr>
							<th>S.No.</th>
							<th>Property Id</th>
							<th>Land Type</th>
							<th>Colony Name</th>
							<th>Do property documents<br>exist?</th>
							<th>Area (In Sqm.)</th>
							<th>Vacant?</th>
							<th>Is it under the custodianship <br>of any other department?</th>
							<th>Encroachment?</th>
							<th>Litigation?</th>
                                                     <!-- added by Swati Mishra on 23052025 to integrate Survey Details-->
                                                       <th>Survey Details</th> 
						</tr>
					</thead>
				</table>
			</div>
        </div>
    </div>
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="colonyNameModal" tabindex="-1" aria-labelledby="colonyNameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="colonyNameModalLabel">Full Colony Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="fullColonyName"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Survey Modal added by Swati Mishra on 2305205 for adding Survey Details-->
    <div class="modal fade" id="surveyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Survey Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="surveyDetailsBody">
                    <p>Loading...</p>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footerScript')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCas7Ce7ycj4zRlD3fx53GvhreTVS-g6TI" defer></script>

    <script type="text/javascript">
       $(document).ready(function() {
        /*$('#example').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
           scrollX: true, // added by Swati Mishra on 23052025 to make table scrollable
            ajax: {
                    url: "{{ route('getUnallotedProperties') }}",
                    data: function(d) {
                        d.locality_record = $('#locality_record').val();
                    }
                },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'unique_propert_id', name: 'unique_propert_id' },
                { data: 'landType', name: 'landType' },
                {
                    data: 'colonyName',
                    name: 'colonyName',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const truncated = data.length > 20 ? data.substring(0, 20) + '...' : data;
                            return `<a href="#" class="view-colony" data-full="${data}" title="Click to see full colony Name">${truncated}</a>`;
                        }
                        return data;
                    }
                },
                { data: 'is_property_document_exist', name: 'is_property_document_exist' },
                { data: 'plot_area_in_sqm', name: 'plot_area_in_sqm' },
                { data: 'is_vaccant', name: 'is_vaccant' },
                { data: 'is_transferred', name: 'is_transferred' },
                { data: 'is_encrached', name: 'is_encrached' },
                { data: 'is_litigation', name: 'is_litigation' },
  // added by Swati Mishra on 23052025 for displaying survey details of unalloted properties
                    {
                        data: null,
                        name: 'survey_details',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-primary show-survey" data-property-id="${row.old_property_id_raw}" title="View Survey Details">Survey Details</button>`;
                        }
                    }
            ],
            dom: '<"top"Blf>rt<"bottom"ip><"clear">', // Custom DOM for button and pagination positioning
            buttons: [
                'csv', 'excel', 'pdf'
            ],
          
        });*/
         $('#example').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                ajax: {
                    url: "{{ route('getUnallotedProperties') }}",
                    data: function(d) {
                        d.locality_record = $('#locality_record').val();
                    }
                },
                createdRow: function(row, data, dataIndex) {
                    // Calculate S.No. based on current page
                    var pageInfo = $('#example').DataTable().page.info();
                    var pageNumber = pageInfo.page;
                    var pageLength = pageInfo.length;
                    var serialNumber = (pageNumber * pageLength) + dataIndex + 1;

                    // Update the first cell with the calculated S.No.
                    $('td:eq(0)', row).html(serialNumber);
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row, meta) {
                            // Server-side already provides correct ID, but this ensures client-side too
                            if (type === 'display') {
                                var pageInfo = $('#example').DataTable().page.info();
                                return (pageInfo.page * pageInfo.length) + meta.row + 1;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'unique_propert_id',
                        name: 'unique_propert_id'
                    },
                    {
                        data: 'landType',
                        name: 'landType'
                    },
                    {
                        data: 'colonyName',
                        name: 'colonyName',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                const truncated = data.length > 20 ? data.substring(0, 20) + '...' :
                                    data;
                                return `<a href="#" class="view-colony" data-full="${data}" title="Click to see full colony Name">${truncated}</a>`;
                            }
                            return data;
                        }
                    },
                    {
                        data: 'is_property_document_exist',
                        name: 'is_property_document_exist'
                    },
                    {
                        data: 'plot_area_in_sqm',
                        name: 'plot_area_in_sqm'
                    },
                    {
                        data: 'is_vaccant',
                        name: 'is_vaccant'
                    },
                    {
                        data: 'is_transferred',
                        name: 'is_transferred'
                    },
                    {
                        data: 'is_encrached',
                        name: 'is_encrached'
                    },
                    {
                        data: 'is_litigation',
                        name: 'is_litigation'
                    },
                    {
                        data: null,
                        name: 'survey_details',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-primary show-survey" data-property-id="${row.old_property_id_raw}" title="View Survey Details">Survey Details</button>`;
                        }
                    }
                ],
                dom: '<"top"Blf>rt<"bottom"ip><"clear">',
                buttons: [
                    'csv', 'excel', 'pdf'
                ],
            });
        $(document).on('change', '#locality_record', function() {
               $('#example').DataTable().ajax.reload();
            });
        // Event delegation to handle clicks on dynamically generated links
        $('#example').on('click', '.view-colony', function(e) {
            e.preventDefault();
            const fullColonyName = $(this).data('full');
            $('#fullColonyName').text(fullColonyName);
            $('#colonyNameModal').modal('show');
        });
      // added by Swati Mishra on 23052025 to show survey modal for unalloted
            $('#example').on('click', '.show-survey', function() {
                const propertyId = $(this).data('property-id');
                const url = {!! json_encode(route('getSurveyDetails', '__PROPERTY_ID__')) !!}.replace('__PROPERTY_ID__', propertyId);
                console.log("Survey button clicked for Property ID:", propertyId);
                $('#surveyDetailsBody').html('<p>Loading...</p>');
                $('#surveyModal').modal('show');

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $('#surveyDetailsBody').html(response.html);
                    },
                    error: function() {
                        $('#surveyDetailsBody').html('<p class="text-danger">Error fetching survey details.</p>');
                    }
                });
            });

        });
        document.addEventListener('click', function(e) {
            const target = e.target;

            if (target.classList.contains('expandable-img')) {
                target.classList.toggle('fullscreen-img');
            }
        });
    </script>
@endsection
