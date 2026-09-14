@extends('layouts.app')
@section('title', 'Create Record Room Entry')
@section('content')
    <link href="{{ asset('assets/plugins/bs-stepper/css/bs-stepper.css') }}" rel="stylesheet" />
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Record Room</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Record Room</li>
                    <li class="breadcrumb-item active" aria-current="page">File Request</li>
                </ol>
            </nav>
        </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>
    <div class="card">
        <div class="card-body">
            <div class="bs-stepper gap-4 vertical">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
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
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="block_record" class="quesLabel">Block No. / Sector<span
                                        class="text-danger">*</span></label>
                                <select name="blockRecord" id="block_record"
                                    class="form-select alphaNum-hiphenForwardSlash">
                                    <option value="">Select</option>
                                </select>
                                <div id="block_recordError" class="text-danger text-left"></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="plot_record" class="quesLabel">Plot<span
                                        class="text-danger">*</span></label>
                                <select name="plotRecord" id="plot_record"
                                    class="form-select plotNoAlpaMix">
                                    <option value="">Select</option>
                                </select>
                                <div id="plot_recordError" class="text-danger text-left"></div>
                            </div>
                        </div>
                         <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end gap-4 pb-3">
                            <div>
                                <label class="d-block">&nbsp;</label>
                                <button type="button" class="btn btn-primary" id="search">Search</button>
                            </div>
                            <div>
                                <label class="d-block">&nbsp;</label>
                                <button type="button" class="btn btn-primary" id="search">Reset</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="mb-4 text-uppercase">Already Requested Files</h5>
            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Record ID</th>
                        <th>Colony Code</th>
                        <th>Block</th>
                        <th>Plot No.</th>
                        <th>File Location</th>
                        <th>Request Section</th>
                        <th>Date of Request</th>
                        <th>Current Section</th>
                        <th>Request Remark</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!--end stepper three-->
    @include('include.alerts.ajax-alert')
@endsection
@section('footerScript')
   <script>
     //get all blocks of selected locality
        $('#locality_record').on('change', function() {
            var locality = this.value;
            $("#block_record").html('');
            $.ajax({
                url: "{{ route('localityBlocks') }}",
                type: "POST",
                data: {
                    locality: locality,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#block_record').html('<option value="">Select</option>');
                    $.each(result, function(key, value) {
                        $("#block_record").append('<option value="' + value.block_no + '">' + value
                            .block_no + '</option>');
                    });
                    //#Start :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                    $("#landUse_org, #landUseSubtype_org").html('<option value="">Select</option>');
                    $.ajax({
                        url: "{{ route('landTypes') }}",
                        type: "POST",
                        data: {
                            locality: locality,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(result) {
                            console.log(result);
                            // Populate Property Types
                            if (result.propertyTypes?.length) {
                                $("#landUse_org").append(
                                    result.propertyTypes.map(type =>
                                        `<option value="${type.id}">${type.item_name}</option>`
                                    ).join('')
                                );
                            }
                            // Populate Property Sub Types
                            // if (result.propertySubtypes?.length) {
                            //     $("#landUseSubtype_org").append(
                            //         result.propertySubtypes.map(subtype => `<option value="${subtype.id}">${subtype.item_name}</option>`).join('')
                            //     );
                            // }
                        }
                    });
                    //#End :- Populate Property Type & Property Sub Type on locality dropdown change - Lalit Tiwari (17/Jan/2025)
                }
            });
        });


         //get all plots of selected block
        $('#block_record').on('change', function() {
            var locality = $('#locality_record').val();
            var block = this.value;
            $("#plot_record").html('');
            $.ajax({
                url: "{{ route('blockPlots') }}",
                type: "POST",
                data: {
                    locality: locality,
                    block: block,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    // console.log(result);
                    $('#plot_record').html('<option value="">Select Plot</option>');
                    $.each(result, function(key, value) {

                        $("#plot_record").append('<option value="' + value + '">' + value +
                            '</option>');
                    });
                }
            });
        });




        $(document).ready(function() {
            $('#example').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('recordRoom.getFilesRequestData') }}",
                    data: function(d) {
                        // d.state = $('#state').val();
                        // d.status = $('#status').val();
                        
                        // d.searchPropertyId = $('#searchPropertyId').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'record_id',
                        name: 'record_id',
                    },
                    {
                        data: 'colony_code',
                        name: 'colony_code'
                    },
                    {
                        data: 'block',
                        name: 'block'
                    },
                    {
                        data: 'plot',
                        name: 'plot'
                    },
                    {
                        data: 'file_location',
                        name: 'file_location'
                    },
                    {
                        data: 'request_section',
                        name: 'request_section'
                    },
                    {
                        data: 'date_of_request',
                        name: 'date_of_request'
                    },
                    {
                        data: 'current_section',
                        name: 'current_section'
                    },
                    {
                        data: 'request_remark',
                        name: 'request_remark'
                    }
                ]
            });
        });

   </script>
@endsection
