@extends('layouts.app')
@section('title', 'Edit Record Room Entry')
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
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('recordRoom.index')}}">Record Room</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Entry</li>
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
                <form action="{{route('recordRoom.edit')}}" method="POST" id="recordForm">
                    @csrf
                    <input type="hidden" name="record_id" value="{{$recordRoomFile->id}}">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="locality_record" class="quesLabel">Locality<span
                                        class="text-danger">*</span></label>
                               <input type="text" name="localityRecord" id="locality_record"
                                    class="form-control alphaNum-hiphenForwardSlash" value="{{$recordRoomFile->colony_code}}" placeholder="Locality" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="block_record" class="quesLabel">Block No. / Sector<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="blockRecord" id="block_record"
                                    class="form-control alphaNum-hiphenForwardSlash" value="{{$recordRoomFile->block}}" placeholder="Block No. / Sector" disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="plot_record" class="quesLabel">Plot<span
                                        class="text-danger">*</span></label>
                                <input type="text" name="plotRecord" id="plot_record"
                                    class="form-control plotNoAlpaMix" value="{{$recordRoomFile->plot}}" placeholder="Plot No." disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="filePlace" class="quesLabel">File Placed At<span
                                        class="text-danger">*</span></label>
                            <input type="text" name="filePlace" value="{{$recordRoomFile->file_location}}" id="filePlace"
                                    class="form-control alphaNum-hiphenForwardSlash" placeholder="File Placed At">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="form-group py-3">
                                <label for="filePlace" class="quesLabel">Section Name<span
                                        class="text-danger">*</span></label>
                           <input type="text" name="sectionName" value="{{$recordRoomFile->section_code}}" id="sectionName"
                                    class="form-control alphaNum-hiphenForwardSlash" placeholder="Section Name" disabled>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center">
                            <button type="submit" class="btn btn-primary" id="submitRecord">Update</button>
                        </div>
                    </div>
                </form>
            </div>
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

   </script>
@endsection
