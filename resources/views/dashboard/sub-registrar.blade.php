@extends('layouts.app')

@section('title', 'NOC Issued')

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


        .alertDot {
            width: 9px;
            height: 9px;
            background-color: #007bff;
            border-radius: 50%;
            box-shadow: 0 0 10px #007bff, 0 0 20px #007bff, 0 0 30px #007bff;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 10px #007bff, 0 0 20px #007bff, 0 0 30px #007bff;
            }

            50% {
                transform: scale(1.2);
                box-shadow: 0 0 15px #007bff, 0 0 30px #007bff, 0 0 45px #007bff;
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 10px #007bff, 0 0 20px #007bff, 0 0 30px #007bff;
            }
        }
    </style>
    <!--breadcrumb-->
    <div class="mb-3">
        <div class="breadcrumb-title pe-3">Noc Issued</div>
        {{-- <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.applications') }}">Applications</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Noc</li>
                </ol>
            </nav>
        </div> --}}
        <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
    </div>

    <hr>
    <div class="card">
        <div class="card-body">

            {{-- <div class="d-flex justify-content-end">
                <ul class="d-flex gap-3">
                    <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                        <i class="lni lni-spellcheck fs-5" style="color:#6610f2"></i>
                        <span class="text-secondary">Mis Is Checked</span>
                    </li>
                    <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">|</li>
                    <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                        <i class="fadeIn animated bx bx-file-find fs-5" style="color:#20c997"></i>
                        <span class="text-secondary">Scanned Files Checked</span>
                    </li>
                    <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">|</li>
                    <li class="list-group-item d-flex gap-2 align-items-center flex-wrap">
                        <i class="lni lni-cloud-upload fs-5" style="color:#fd7e14"></i>
                        <span class="text-secondary">Uploaded Documents Checked</span>
                    </li>
                </ul>
            </div> --}}

            <div class="row g-3 align-items-end flex-nowrap overflow-auto" style="flex-wrap: nowrap;">
                <div class="col-lg-2 col-12">
                    <label for="locality" class="form-label">Colony Name (Present)</label>
                    <select name="locality" id="locality" class="form-select">
                        <option value="">Select</option>
                        @foreach ($colonyList as $colony)
                            <option value="{{ $colony->id }}">{{ $colony->name }}</option>
                        @endforeach
                    </select>
                    @error('locality')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="text-danger" id="localityError"></div>
                </div>

                <div class="col-lg-2 col-12">
                    <label for="block" class="form-label">Block</label>
                    <select name="block" id="block" class="form-select">
                        <option value="">Select</option>
                    </select>
                    @error('block')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="text-danger" id="blockError"></div>
                </div>

                <div class="col-lg-2 col-12">
                    <label for="plot" class="form-label">Plot No./Flat No.</label>
                    <select name="plot" id="plot" class="form-select">
                        <option value="">Select</option>
                    </select>
                    @error('plot')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="text-danger" id="plotError"></div>
                </div>

                <div class="col-lg-1 col-12 text-center">
                    <label class="form-label d-block">&nbsp;</label>
                    <div style="font-size: 24px;">OR</div>
                </div>

                <div class="col-lg-2 col-12">
                    <label for="searchPropertyId" class="form-label">Search By Property Id</label>
                    <input type="text" id="searchPropertyId" name="searchPropertyId"
                        class="form-control numericOnly five-digit" placeholder="Enter Property ID">
                    <div class="text-danger" id="searchPropertyIdError"></div>
                </div>

                <div class="col-lg-3 col-12">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="button" id="searchNocApplication" name="searchNocApplication"
                            class="btn btn-primary w-50">
                            Search
                        </button>
                        <button type="button" id="resetNocFilter" class="btn btn-secondary w-50">
                            Reset
                        </button>
                    </div>
                </div>

            </div>
            <br>

            <table id="example" class="display nowrap applicant_list_table" style="width:100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Application No.</th>
                        <th>Property ID</th>
                        <th>Locality</th>
                        <th>Block</th>
                        <th>Plot No.</th>
                        <th>Known As</th>
                        <th>Section</th>
                        <th>Dispatch Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
    @include('include.alerts.ajax-alert')
@endsection


@section('footerScript')

    <script type="text/javascript">
        //get all blocks of selected locality
        $('#locality').on('change', function() {
            var locality = this.value;
            $("#block").html('');
            $.ajax({
                url: "{{ route('localityBlocks') }}",
                type: "POST",
                data: {
                    locality: locality,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#block').html('<option value="">Select</option>');
                    $.each(result, function(key, value) {
                        $("#block").append('<option value="' + value.block_no + '">' + value
                            .block_no + '</option>');
                    });
                }
            });
        });
        //get all plots of selected block
        $('#block').on('change', function() {
            var locality = $('#locality').val();
            var block = this.value;
            $("#plot").html('');
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
                    $('#plot').html('<option value="">Select</option>');
                    $.each(result, function(key, value) {
                        $("#plot").append('<option value="' + value + '">' +
                            value + '</option>');
                    });
                }
            });
        });
        //get known as of selected plot
        $('#plot').on('change', function() {
            var locality = $('#locality').val();
            var block = $('#block').val();
            var plot = this.value;
            $.ajax({
                url: "{{ route('getPropertyDetails') }}",
                type: "POST",
                data: {
                    locality: locality,
                    block: block,
                    plot: plot,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result);
                    // Remove the previously appended content, if it exists
                    const previousContent = document.getElementById('appendedContent');
                    if (previousContent) {
                        previousContent.remove();
                    }
                    // Append new content inside a wrapper div
                    document.getElementById('propertyDetailsDiv').insertAdjacentHTML('beforeend',
                        `<div id="appendedContent" style="margin-bottom:19px;">${result.data}</div>`
                    );
                }
            });
        });

        $(document).ready(function() {
            var table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                // responsive: false,
                ajax: {
                    url: "{{ route('get.noc.applications.disposed') }}",
                    data: function(d) {
                        d.locality = $('#locality').val();
                        d.block = $('#block').val();
                        d.plot = $('#plot').val();
                        d.searchPropertyId = $('#searchPropertyId').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'application_no',
                        name: 'application_no'
                    },
                    {
                        data: 'old_property_id',
                        name: 'old_property_id'
                    },
                    {
                        data: 'new_colony_name',
                        name: 'new_colony_name'
                    },
                    {
                        data: 'block_no',
                        name: 'block_no'
                    },
                    {
                        data: 'plot_or_property_no',
                        name: 'plot_or_property_no'
                    },
                    {
                        data: 'presently_known_as',
                        name: 'presently_known_as'
                    },
                    {
                        data: 'section',
                        name: 'section'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                scrollX: true, // Enable horizontal scrolling
            });
            // Reload on "Search" button click
            $('#searchNocApplication').click(function() {
                table.ajax.reload();
            });

            $('#resetNocFilter').click(function() {
                $('#locality').val('');
                $('#block').html('<option value="">Select</option>');
                $('#plot').html('<option value="">Select</option>');
                $('#searchPropertyId').val('');
                $('#example').DataTable().ajax.reload();
            });

        });
    </script>
@endsection
