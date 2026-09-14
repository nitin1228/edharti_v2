@extends('layouts.app')

@section('title', 'Record Room Details')

@section('content')

    <style>
        div.dt-buttons {
            float: none !important;
            width: 19%;
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
         <div class="breadcrumb-title pe-3">Record Room</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Total Record List</li>
                    </ol>
                </nav>
            </div>
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>
    <!--end breadcrumb-->
    <hr>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-end pb-4">
                <!-- State Dropdown -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="mb-3">
                        <label for="state" class="form-label">Locality </label>
                        <select name="localityRecord" id="locality_record" class="form-select">
                            <option value="">Select</option>
                            @foreach ($colonyList as $colony)
                                <option value="{{ $colony->id }}">{{ $colony->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status Dropdown -->
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Block</label>
                       <select name="blockRecord" id="block_record"
                                    class="form-select alphaNum-hiphenForwardSlash">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>


                <div class="col-lg-2 col-md-4 col-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Plot</label>
                         <select name="plotRecord" id="plot_record"
                            class="form-select plotNoAlpaMix">
                            <option value="">Select</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons aligned to right -->
                <div class="col-lg-4 col-md-6 col-12 d-flex justify-content-between align-items-end gap-2 pb-3">
                    <div>
                        <label class="d-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="resetValues">Reset</button>
                    </div>
                    <div>
                        <label class="d-block">&nbsp;</label>
                        <a href="{{ route('recordRoom.create') }}">
                            <button type="button" class="btn btn-primary">+ Add New File</button>
                        </a>
                    </div>
                </div>
            </div>


            <table id="example" class="display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Record ID</th>
                        <th>Colony Code</th>
                        <th>Block</th>
                        <th>Plot No.</th>
                        <th>File Location</th>
                        <th>Section</th>
                        <th>Current Section</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="tooltip"></div>

    @include('include.loader')
    @include('include.alerts.ajax-alert')
    @include('include.alerts.delete-confirmation')

@endsection


@section('footerScript')


    <script type="text/javascript">

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
                    url: "{{ route('recordRoom.files') }}",
                    data: function(d) {
                        d.locality_record = $('#locality_record').val();
                        d.block_record = $('#block_record').val();
                        d.plot_record = $('#plot_record').val();
                        
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
                        data: 'section_code',
                        name: 'section_code'
                    },
                    {
                        data: 'transaction_section_code',
                        name: 'transaction_section_code'
                    }
                ]
            });
        });


        $(document).ready(function() {
            let deleteId;

            // Trigger the modal and set the record ID
            $(document).on('click', '.delete-btn', function() {
                deleteId = $(this).data('id'); // Get the record ID from the button
                $('#ModalDelete').modal('show'); // Show the modal
            });


            $(document).on('change', '#locality_record', function() {
               $('#example').DataTable().ajax.reload();
            });
            $(document).on('change', '#block_record', function() {
               $('#example').DataTable().ajax.reload();
            });
            $(document).on('change', '#plot_record', function() {
               $('#example').DataTable().ajax.reload();
            });
        });

            $('#resetValues').click(function() {
                $('#locality_record').val('');
                $('#block_record').val('');
                $('#plot_record').val('');
                $('#example').DataTable().ajax.reload();
            });
    </script>
@endsection
