		@php
		$currentUrl = url()->current();
		$isPublic = strpos($currentUrl, 'public-dashboard') !== false;
		$layout = $isPublic ? 'layouts.public.app' : 'layouts.app';
		@endphp


		@extends($layout)

		@section('title', 'Dashboard')
		@section('content')
		@if ($isPublic || (Auth::check() && Auth::user()->hasPermissionTo('master.dashboard')))

		@if ($isPublic)
		<link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
		<link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
		<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-select.min.css') }}">
		@endif
		<?php
		$labels = [];
		$barData = [];
		$seriesLabels = [];
		foreach ($barChartData as $key => $row) {
		$labels[] = $row->land;
		$data = [];
		$series = [];
		$counter = 0;
		foreach ($row as $index => $val) {
		    if ($counter > 0) {
		        $data[] = $val;
		        if (count($seriesLabels) == 0) {
		            $series[] = "'" . $index . "'"; 
		        }
		    }
		    $counter++;
		}
		if (count($seriesLabels) == 0 && count($series) > 0) {
		    $seriesLabels = $series;
		}
		$barData[] = $data;
		}
		//print_r($seriesLabels);
		//Get the number of colonies --Amita [15-01-2025]
		$number_of_colonies = getMisDoneColoniesCount();
		//print_r($number_of_colonies);
		?>
		<style>
		.colony-dropdown {
		    width: 48.5% !important;
		}

		.dropdown.bootstrap-select {
		    min-width: 100% !important;
		}

		.btn.dropdown-toggle {
		    min-width: 100% !important;
		}

		a.active .text {
		    color: #fff !important;
		}

		.view-list {
		    cursor: pointer;
		}
		</style>
		<style>
	.mycard .left .number {
		font-size: clamp(1.5rem, 2.1vw, 1.7rem);
		line-height: clamp(1.2rem, 2.5vw, 2rem);
		font-weight: 400;
		display: block;
		color: #fff;
	}

	.mycard .left .title {
		font-size: clamp(0.6rem, 1.8vw, 0.9rem);
		color: #fff;
		line-height: clamp(1.3rem, 2.5vw, 1.4rem);
		margin-bottom: 6px;
		font-weight: 400;
	}

	.mycard .left {
		position: inherit;
		z-index: 1;
	}
	.mycard .right{
		z-index: 1
	}
	.mycard .right .icon {
		font-size: clamp(28px, 6vw, 45px);
		color: #fff;
		position: inherit;
		z-index: 9;
	}
	.mycard {
		border-radius: 3px;
		box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3) !important;
		padding: clamp(0.8rem, 2vw, 1rem) clamp(1rem, 2vw, 1.4rem) clamp(0.7rem, 3vw, 1.0rem);
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-pack: justify;
		-ms-flex-pack: justify;
		justify-content: space-between;
		position: relative;
		overflow: hidden;
		-webkit-transition: all 0.3s ease-in;
		-o-transition: all 0.3s ease-in;
		transition: all 0.3s ease-in;
		margin-bottom: clamp(0.8rem, 1.8vw, 1.2rem); min-height:115px;
	}

	.mycard.bg1 {
		background-image: -webkit-gradient(linear, left top, right top, from(#f85108), to(#f4ad3c));
		background-image: -webkit-linear-gradient(left, #f85108, #f4ad3c);
		background-image: -o-linear-gradient(left, #f85108, #f4ad3c);
		background-image: linear-gradient(to right, #f85108, #f4ad3c);
	}
	.mycard.bg1::after {
		background: #f85108;
	}
	.mycard::after {
		position: absolute;
		content: " ";
		width: 258px;
		height: 577px;
		top: -16px;
		right: -50px;
		-webkit-transform: rotate(28deg);
		-ms-transform: rotate(28deg);
		transform: rotate(28deg);
	}
	.mycard.bg2 {
		background-image: -webkit-gradient(linear, left top, right top, from(#047edf), to(#0bb9fa));
		background-image: -webkit-linear-gradient(left, #047edf, #0bb9fa);
		background-image: -o-linear-gradient(left, #047edf, #0bb9fa);
		background-image: linear-gradient(to right, #047edf, #0bb9fa);
	}
	.mycard.bg2::after {
		background: #047edf;
	}
	.mycard.bg3 {
		background-image: -webkit-gradient(linear, left top, right top, from(#0fa49b), to(#03dbce));
		background-image: -webkit-linear-gradient(left, #0fa49b, #03dbce);
		background-image: -o-linear-gradient(left, #0fa49b, #03dbce);
		background-image: linear-gradient(to right, #0fa49b, #03dbce);
	}
	.mycard.bg3::after {
		background: #0fa49b;
	}
	.mycard.bg4 {
		background-image: -webkit-gradient(linear, left top, right top, from(#5a49e9), to(#7a6cf0));
		background-image: -webkit-linear-gradient(left, #5a49e9, #7a6cf0);
		background-image: -o-linear-gradient(left, #5a49e9, #7a6cf0);
		background-image: linear-gradient(to right, #5a49e9, #7a6cf0);
	}
	.mycard.bg4::after {
		background: #352d7b;
	}
	.mycard.bg5 {
		background-image: -webkit-gradient(linear, left top, right top, from(#cf0633), to(#f96079));
		background-image: -webkit-linear-gradient(left, #cf0633, #f96079);
		background-image: -o-linear-gradient(left, #cf0633, #f96079);
		background-image: linear-gradient(to right, #cf0633, #f96079);
	}
	.mycard.bg5::after {
		background: #cf0633;
	}
	.mycard.bg6 {
		background-image: -webkit-gradient(linear, left top, right top, from(#129021), to(#1ed41e));
		background-image: -webkit-linear-gradient(left, #129021, #1ed41e);
		background-image: -o-linear-gradient(left, #129021, #1ed41e);
		background-image: linear-gradient(to right, #129021, #1ed41e);
	}
	.mycard.bg6::after {
		background: #129021;
	}
</style> 
		<!--breadcrumb-->
		<div class="container-fluid">
		<div class="row justify-content-between mb-3">
		    <div class="col-lg-6">
		        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
		            <div class="breadcrumb-title pe-3">Dashboard</div>
		            <div class="ps-3">
		                <nav aria-label="breadcrumb">
		                    <ol class="breadcrumb mb-0 p-0">
		                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i
		                                    class="bx bx-home-alt"></i></a>
		                        </li>
		                        <li class="breadcrumb-item">Dashboards</li>
		                        @if (Auth::user()->hasRole('super-admin'))
		                            <li class="breadcrumb-item active" aria-current="page">My Dashboard</li>
		                        @else
		                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
		                        @endif
		                    </ol>
		                </nav>
		            </div>
		        </div>
		    </div>
		    <div class="col-lg-6">
		        <div class="colony-dropdown ms-auto">
		            <select id="colony-filter" class="selectpicker" data-live-search="true">
		                <option value="">Filter by Colony ({{ $number_of_colonies }})</option>
		                @foreach ($colonies as $colony)
		                    <option value="{{ $colony->id }}">{{ $colony->name }}</option>
		                @endforeach
		            </select>
		        </div>
		    </div>


		</div>
		</div>

		<div class="container-fluid">
		<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 custom_card_container">
		    <div class="col">
				<div class="mycard bg1">
					<div class="left">
						<h5 class="title">Total No. of Properties in Delhi </h5>
						<span class="number view-list" id="tile_total_count"
						data-original="{{ customNumFormat($totalCount) }}">
						{{ customNumFormat($totalCount) }}</span>							
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-home-alt'></i> 
						</div>
					</div>
				</div>					
		    
		  
		    </div>
		    <div class="col">
				<div class="mycard bg2">
					<div class="left">
						<h5 class="title">Total Area (Sqm) </h5>
						<span class="number view-list" id="tile_total_area_formatted"
						data-original="{{ customNumFormat(round($totalArea)) }}">
						{{ customNumFormat(round($totalArea)) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-area'></i>
						</div>
					</div>
				</div>                   
		    </div>
		    <div class="col hoverable" title="₹{{ customNumFormat($totalLdoValue) }}" id="tile_total_land_value_ldo"
		        data-original="₹{{ customNumFormat($totalLdoValue) }}">
				<div class="mycard bg3">
					<div class="left">
						<h5 class="title">Total Land Value (L&DO)</h5>
						<span class="number view-list" id="tile_total_land_value_ldo_formatted"
						data-original="₹{{ customNumFormat(round($totalLdoValue / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($totalLdoValue / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div> 
		    </div>
		    <div class="col hoverable" title="₹{{ customNumFormat($totalCircleValue) }}"
		        id="tile_total_land_value_circle" data-original="₹{{ customNumFormat($totalCircleValue) }}">
		        
				<div class="mycard bg4">
					<div class="left">
						<h5 class="title">Total Land Value (Circle rate)</h5>
						<span class="number view-list" id="tile_total_land_value_circle_formatted"
						data-original="₹{{ customNumFormat(round($totalCircleValue / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($totalCircleValue / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>                   
		    </div>
		</div>
		<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 custom_card_container">
			<div class="col">
				<div class="mycard bg6">
					<div class="left">
						<h5 class="title">Total No. of LH Properties in Delhi</h5>
						<span class="number view-list"id="tile_lease_hold_count"
						data-original="{{ customNumFormat($statusCount['lease_hold']) }}"
						data-filter-name="property_status" data-filter-value="951">
						{{ customNumFormat($statusCount['lease_hold']) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-home-alt'></i>
						</div>
					</div>
				</div>				
			</div>
			<div class="col">
				<div class="mycard bg5">
					<div class="left">
						<h5 class="title">Total Area of LH Properties (Sqm)</h5>
						<span class="number view-list" id="tile_lease_hold_area"
						data-original="{{ customNumFormat(round($statusArea['lease_hold'])) }}">
						{{ customNumFormat(round($statusArea['lease_hold'])) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-area'></i>
						</div>
					</div>
				</div>	
			</div>
			<div class="col hoverable" title="₹{{ customNumFormat($statusLdoValue['lease_hold']) }}"
		        id="tile_lease_hold_land_value_ldo"
		        data-original="₹{{ customNumFormat($statusLdoValue['lease_hold']) }}">
				<div class="mycard bg1">
					<div class="left">
						<h5 class="title">Total LH Land Value (L&DO)</h5>
						<span class="number view-list" id="tile_lease_hold_land_value_ldo_formatted"
						data-original="₹{{ customNumFormat(round($statusLdoValue['lease_hold'] / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($statusLdoValue['lease_hold'] / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>					
			</div>
			<div class="col hoverable" title="₹{{ customNumFormat($statusCircleValue['lease_hold']) }}"
		        id="tile_lease_hold_land_value_circle"
		        data-original="₹{{ customNumFormat($statusCircleValue['lease_hold']) }}">
				<div class="mycard bg2">
					<div class="left">
						<h5 class="title">Total LH Land Value (Circle rate)</h5>
						<span class="number view-list"  id="tile_lease_hold_land_value_circle_formatted"
						data-original="₹{{ customNumFormat(round($statusCircleValue['lease_hold'] / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($statusCircleValue['lease_hold'] / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 custom_card_container">
			<div class="col">
				<div class="mycard bg4">
					<div class="left">
						<h5 class="title">Total LH Land Value (Circle rate)</h5>
						<span class="number view-list"  id="tile_free_hold_count"
						data-original="{{ customNumFormat($statusCount['free_hold']) }}"
						data-filter-name="property_status" data-filter-value="952">
						{{ customNumFormat($statusCount['free_hold']) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-home'></i>
						</div>
					</div>
				</div>				
			</div>
			<div class="col">
				<div class="mycard bg3">
					<div class="left">
						<h5 class="title">Total Area of FH Properties (Sqm)</h5>
						<span class="number view-list"  id="tile_free_hold_area"
						data-original="{{ customNumFormat(round($statusArea['free_hold'])) }}">
						{{ customNumFormat(round($statusArea['free_hold'])) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-area'></i>
						</div>
					</div>
				</div>				
			</div>
			<div class="col hoverable" title="₹{{ customNumFormat($statusLdoValue['free_hold']) }}"
		        id="tile_free_hold_land_value_ldo"
		        data-original="₹{{ customNumFormat($statusLdoValue['free_hold']) }}">
				<div class="mycard bg6">
					<div class="left">
						<h5 class="title">Total FH Land Value (L&DO)</h5>
						<span class="number view-list"  id="tile_free_hold_land_value_ldo_formatted"
						data-original="₹{{ customNumFormat(round($statusLdoValue['free_hold'] / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($statusLdoValue['free_hold'] / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>					
			</div>
			<div class="col hoverable" title="₹{{ customNumFormat($statusCircleValue['free_hold']) }}"
		        id="tile_free_hold_land_value_circle"
		        data-original="₹{{ customNumFormat($statusCircleValue['free_hold']) }}">
				<div class="mycard bg5">
					<div class="left">
						<h5 class="title">Total FH Land Value (Circle rate)</h5>
						<span class="number view-list"  id="tile_free_hold_land_value_circle_formatted"
						data-original="₹{{ customNumFormat(round($statusCircleValue['free_hold'] / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($statusCircleValue['free_hold'] / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- unalloted properties -->

		<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 custom_card_container">
			<div class="col">
				<div class="mycard bg2">
					<div class="left">
						<h5 class="title">Total No. of Unallotted Properties</h5>
						<span class="number view-list"  id="tile_unallotted_count"
						data-original="{{ customNumFormat($statusCount['unallotted']) }}"
						data-filter-name="property_status" data-filter-value="unalloted">
						{{ customNumFormat($statusCount['unallotted']) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-home'></i>
						</div>
					</div>
				</div>
				
			</div>
			<div class="col">
				<div class="mycard bg1">
					<div class="left">
						<h5 class="title">Total Area of U.A Properties (Sqm)</h5>
						<span class="number view-list"  id="tile_unallotted_area"
						data-original=" {{ customNumFormat(round($statusArea['unallotted'])) }}">
						{{ customNumFormat(round($statusArea['unallotted'])) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-area'></i>
						</div>
					</div>
				</div>
				
			</div>
			<div class="col hoverable" title="₹{{ customNumFormat($statusLdoValue['unallotted']) }}"
		        id="tile_unallotted_land_value_ldo"
		        data-original="₹{{ customNumFormat($statusLdoValue['unallotted']) }}">
				<div class="mycard bg4">
					<div class="left">
						<h5 class="title">Total Unallotted Land Value (L&DO)</h5>
						<span class="number view-list" id="tile_unallotted_land_value_ldo_formatted"
						data-original="₹{{ customNumFormat(round($statusLdoValue['unallotted'] / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($statusLdoValue['unallotted'] / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>
			
			</div>
			<div class="col hoverable" title="₹{{ customNumFormat($statusCircleValue['unallotted']) }}"
		        id="tile_unallotted_land_value_circle"
		        data-original="₹{{ customNumFormat($statusCircleValue['unallotted']) }}">
				<div class="mycard bg6">
					<div class="left">
						<h5 class="title">Total U.A Land Value (Circle rate)</h5>
						<span class="number view-list" id="tile_unallotted_land_value_circle_formatted"
						data-original="₹{{ customNumFormat(round($statusCircleValue['unallotted'] / 10000000)) }} Cr.">
						₹{{ customNumFormat(round($statusCircleValue['unallotted'] / 10000000)) }} Cr.</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-rupee'></i>
						</div>
					</div>
				</div>
			
			</div>
		</div>
		<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 custom_card_container  outside-tiles">
			<div class="col">
				<div class="mycard bg3">
					<div class="left">
						<h5 class="title">Total No. of Properties Outside Delhi</h5>
						<span class="number view-list"  id="outside_count"
						data-filter-value="vaccant">{{ customNumFormat($outsideProperty['count']) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-home'></i>
						</div>
					</div>
				</div>
				
			</div>
			<div class="col">
				<div class="mycard bg5">
					<div class="left">
						<h5 class="title">Total Area of P.O.S Delhi(Sqm)</h5>
						<span class="number view-list" id="outside_area">
						{{ customNumFormat(round($outsideProperty['total_area'])) }}</span>
					</div>
					<div class="right d-flex align-self-center">
						<div class="icon">
							<i class='bx bx-area'></i>
						</div>
					</div>
				</div>				
			</div>			
		</div>

		<!-- Flats-->


		<!--end row-->

		</div>


		<div id="no-filter-applied">
		<div class="row">
			<div class="col-12 col-lg-6 ">
				<div class="card radius-10">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Land Value Comparison L&DO and Circle Rate (in ₹ Cr)</h6>
							</div>
						</div>
						<div class="chart-container-2 mt-4" >
							<canvas id="landValueChart"></canvas>
						</div>

					</div>
				</div>
			</div>
			<div class="col-12 col-lg-6 ">
				<div class="card radius-10">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Number of Properties & Area (in Sqm) by Property Type</h6>
							</div>


						</div>
						<div class="chart-container-2 mt-4" >
							<canvas id="propertyCountChart"></canvas>
						</div>

					</div>
				</div>
			</div>
			
			
		</div>

		<div class="row"> 
			<div class="col-12 col-lg-6 ">
				<div class="card radius-10">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Lease/Free Hold Count vs Total Area by Property Range</h6>
							</div>


						</div>
						<div class="chart-container-2 mt-4" style="height: 365px;">
							<canvas id="propertyAreaCombinedChart"></canvas>
						</div>

					</div>
				</div>
			</div>                 
			<div class="col-12 col-lg-6 ">
				<div class="card radius-10">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Total Area of Properties by Area Range</h6>
							</div>


						</div>
						<div class="chart-container-2 mt-4" style="height: 365px;">
							<canvas id="areaChart"></canvas>
						</div>

					</div>
				</div>
			</div> 
			<div class="col-12 col-lg-5 ">
				<div class="card radius-10 ">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
				<h6 class="mb-0">Number of properties categorized by their land value</h6>
							</div>

						</div>

						<div class="chart-container-1" >
							<canvas id="landvalueChartnew" ></canvas>
						</div>
					</div>
				</div>
			</div>              
			<!--<div class="col-12 col-lg-5 ">
				<div class="card radius-10">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Number of Lease Hold vs Free Hold Properties by Area (sqm)</h6>
							</div>
						</div>
						<div class="chart-container-2 mt-4" style="height: 365px;">
							<canvas id="propertyChart"></canvas>
						</div>

					</div>
				</div>
			</div>-->
			<div class="col-12 col-lg-7">
				<div class="card radius-10">
					<div class="card-body">
						<!-- added responsive div for table responsiveness by anil on 30-05-2025 -->
						<div class="table-responsive">
							<table class="table table-striped text-center">
								<thead>
									<tr>
										<th>Area (Sqm)</th>
										<th>No. of Properties</th>
										<th>No. of Lease Hold Properties</th>
										<th>No. of Free Hold Properties</th>
										<th>Area of Properties</th>
									</tr>
								</thead>
								@php
								/*$labels = $propertyAreaDetails['labels'];*/
								$totalCount = array_sum(array_column($propertyAreaDetails, 'count'));
								$totalAreaInSqm = array_sum(array_column($propertyAreaDetails, 'area'));
								$totalAreaInAcre = $totalAreaInSqm * 0.0002471053815;
								@endphp
								<tbody>
									@foreach ($propertyAreaDetails as $key => $row)
									<tr>
										<td>{{ $key }}</td>
										<td>{{ $row['count'] . ' (' . round(($row['count'] / $totalCount) * 100, 2) . '%)' }}
										</td>
										<td>{{ $row['leaseHold'] . ' (' . round(($row['leaseHold'] / $totalCount) * 100, 2) . '%)' }}
										</td>
										<td>{{ $row['freeHold'] . ' (' . round(($row['freeHold'] / $totalCount) * 100, 2) . '%)' }}
										</td>
										<td>{{ $row['area'] . ' (' . round(($row['area'] / $totalAreaInSqm) * 100, 2) . '%)' }}
										</td>
									</tr>
									@endforeach
									<tr>
										<th>Grand Total</th>
										<th colspan="1">{{ $totalCount }}</th>
										<th colspan="3">{{ round($totalAreaInSqm, 3) }}
											({{ round($totalAreaInAcre) }} Acres)</th>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end row-->

		<div class="row height-row-align">           
		<div class="col-12 col-lg-7">
			<div class="card radius-10">
				<div class="card-body no-padding">
					<div class="d-flex tabs-progress-container">
						<div class="nav-tabs-left-aside">
							<ul class="nav nav-tabs nav-primary" role="tablist"
		                            style="display: block !important;">

								@if (count($tabHeader) > 0)
								@foreach ($tabHeader as $i => $th)
								<li class="nav-item" role="presentation">
									<a class="nav-link {{ $i == 0 ? 'active' : '' }}"
		                                            id="original_tab_total_{{ $i }}" data-bs-toggle="tab"
		                                            href="#" role="tab" aria-selected="true"
		                                            onclick="getTabData('{{ $th->id }}')">
										<div class="text-center">
											<div class="tab-title">{{ $th->property_type_name }}</div>
											<span class="tab-total-no">{{ $th->counter }}</span>
										</div>
									</a>
								</li>
								@endforeach
								@endif
							</ul>
						</div>
						<div class="nav-tabs-right-aside">
							<div class="tab-content py-3">
								<div class="tab-pane fade show active" id="" role="tabpanel">
									<ul class="progress-report">
										<!-- new code -->
										<?php
										$max = 0;
										foreach ($tab1Details as $row) {
										$max = $row->counter > $max ? $row->counter : $max;
										}
										?>

										@foreach ($tab1Details as $detail)
										<li>
											<?php
											$width = $max > 0 ? ($detail->counter / $max) * 100 : 0;
											?>
											<div
		                                                class="d-flex justify-content-between align-items-center mb-2">
												<span
												class="progress-title">{{ $detail->PropSubType }}</span>
												<span class="progress-result">{{ $detail->counter }}</span>
											</div>
											<div class="progress mb-4" style="height:7px;">
												<div class="progress-bar" role="progressbar"
		                                                    style="width: <?= $width ?>%"
		                                                    aria-valuenow="{{ $detail->counter }}" aria-valuemin="0"
		                                                    aria-valuemax="100"></div>
											</div>
										</li>
										@endforeach

									</ul>

									<!--- new code -->
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-5">
			<div class="card radius-10">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
							<h6 class="mb-0">No. of Properties Land Type Wise</h6>
						</div>							
					</div>

					<div class="chart-container-1" style="height: 420px;">
						<canvas id="chartProperties1"></canvas>
					</div>
				</div>
			</div>
		</div>
		</div> 
		<!-- end row -->


			
		<div class="row">
			<div class="col-12 col-lg-6">
				<div class="card radius-10">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Payment Summary </h6>
							</div>
							<div class="ms-auto">
								<select id="yearFilterp" class="form-select">
									<option value="2025">2025</option>
									<option value="2024">2024</option>
									<option value="2023">2023</option>
									<option value="2022">2022</option>
								</select>
							</div>
						</div>
						<div class="table-responsive mb-3">
							<table class="table align-middle m-0">
								<thead>
									<tr>
										<th class="text-center" style="background-color:#116d6e17;">											
												<h6 class="pt-2">Total Payment Received</h6>
												<h5 class="pb-1">
													&#8377;
													<span id="summaryAmount"></span> Rs.
												</h5>
											
										</th>
									</tr>
								</thead>
							</table>
						</div>						
						<div class="chart-container-1" style="height: 420px;">
							<canvas id="myChart"></canvas>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-12 col-lg-6">
				<div class="card radius-10">
					<div class="card-body">
						<h6 class="">Outstanding Dues</h6>
						@php
						$leaseHoldDue = customNumFormat(round($demandData['leaseHold']->total_balance_amount / 10000000, 2)) ;
						$freeHoldDue =  customNumFormat(round($demandData['freeHold']->total_balance_amount / 10000000, 2));
						$leaseHoldProperties = customNumFormat($demandData['leaseHold']->property_count)  ?? 0;
						$freeHoldProperties = customNumFormat($demandData['freeHold']->property_count) ?? 0;
						@endphp
						<div class="table-demand">
							<div class="table-responsive">
								<table class="table align-middle m-0">
									<thead>
										<tr>
											<th class="text-center" style="background-color:#116d6e17;">
												<a href="{{ route('outStandingDuesList') }}" target="_blank">
													<h6 class="pt-2">Total Outstanding Dues </h6>
													<h5 class="pb-1">&#8377;
														{{ customNumFormat(round($demandData['total']->total_balance_amount / 10000000, 2)) }}
														Cr.
														({{ customNumFormat($demandData['total']->property_count) }}
														Properties)</h5>
												</a>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<table class="table align-middle table-striped m-0">
													<thead>
														<tr>
															<th>Property Status</th>
															<th>Outstanding Dues</th>
															<th>No. of Properties</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>Lease Hold</td>
															<td>&#8377;
																{{ $leaseHoldDue }}
																Cr. </td>
															<td>{{$leaseHoldProperties }}
															</td>
														</tr>
														<tr>
															<td>Free Hold</td>
															<td>&#8377;
																{{ $freeHoldDue }}
																Cr. </td>
															<td>{{ $freeHoldProperties }}
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
								<div class="chart-container-2">
								<canvas id="propertyDueChart"></canvas></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--<div class="col-12 col-lg-6 ">
				<div class="card radius-10 ">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div>
								<h6 class="mb-0">Number of properties categorized by their land value</h6>
							</div>

						</div>

						<div class="chart-container-1" >
							<canvas id="landvalueChart"></canvas>
						</div>
					</div>
				</div>
			</div>-->
		</div>
		<!-- end row -->
		 <div class="row">          

		<div class="col-12 col-lg-12 ">
			<div class="card radius-10">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div>
						<h6 class="mb-0">Applications Summary Grouped by applications type</h6>
						</div>
						<div class="ms-auto">
						<select id="yearFilter" class="form-select">
							<option value="2025">2025</option>
							<option value="2024">2024</option>
							<option value="2023">2023</option>
							<option value="2022">2022</option>
						</select>
						</div>
					</div>
					<div class="chart-container-2 mt-4" style="height: 365px;">
						<canvas id="monthlyAppChart"></canvas>
					</div>
				</div>
			</div>
			
		</div>
		</div> 
		</div>
		<div class="container-fluid">
		@if ($applicationData)
		    <div class="col-lg-12 col-12" style="margin-bottom: 25px;">
		        <div class="card offorangecard totalApp" style="margin-bottom: 0px;">
		            <div class="card-body">
		                <div class="dashboard-card-view">
		                    <h4><a href="{{ route('admin.applications') }}" style="color: inherit">Total
		                            Applications:
		                            <span id="totalAppCount">{{ $applicationData['totalAppCount'] }}</span></a></h4>
		                    <div class="container-fluid">
		                        <div class="row separate-col-border">
		                            @foreach ($statusList as $i => $status)
		                                <div class="custom-col-col col-md-6 col-lg-2">
		                                    @if ($status->item_name == 'Disposed')
		                                        <a href="{{ route('applications.disposed') }}">
		                                            <span class="dashboard-label">{{ $status->item_name }}:</span>
		                                            <span
		                                                id="total-{{ $status->item_code }}">{{ isset($applicationData['statusWiseCounts'][$status->item_code]) ? $applicationData['statusWiseCounts'][$status->item_code] : 0 }}</span></a>
		                                    @else
		                                        <a
		                                            href="{{ route('admin.applications', ['status' => Crypt::encrypt(" $status->item_code")]) }}">
		                                            <span class="dashboard-label">{{ $status->item_name }}:</span>
		                                            <span
		                                                id="total-{{ $status->item_code }}">{{ isset($applicationData['statusWiseCounts'][$status->item_code]) ? $applicationData['statusWiseCounts'][$status->item_code] : 0 }}</span></a>
		                                    @endif
		                                </div>
		                            @endforeach
		                        </div>
		                    </div>
		                </div>
		                {{-- <div class="row mt-4">
		    @foreach ($statusList as $status)
		    <div class="custom-col-col col-4 col-lg-2">
		        <span class="status_name">{{$status->item_name}}</span> <span
		            class="status_value">{{isset($statusWiseCounts[$status->item_code]) ?
		            $statusWiseCounts[$status->item_code] : 0}}</span>
		    </div>
		    @endforeach
		</div> --}}
		            </div>
		        </div>
		    </div>
		@endif
		</div>


		<div id="filter-applied" class="d-none">
		@include('include.parts.dashboard-filtered')
		</div>


		@endif
		@endsection
		<!-- end row -->

		@section('footerScript')
		{{-- @haspermission('view dashboard') --}}

		@if ($isPublic || (Auth::check() && Auth::user()->hasPermissionTo('master.dashboard')))
		<!--<script src="{{ asset('assets/js/Chart.min.js') }}"></script>-->
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"> </script>
		<script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script> <!-- added By Nitin to for colony filter -->
		<!--<script src="{{ asset('assets/js/chartjs-plugin-datalabels.min.js') }}"></script>-->
		<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"> </script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"> </script>		
		<script src="{{ asset('assets/js/index.js') }}"></script>        
		<script>
		// ================= Application chart month wise and application type ==================
		let monthlyAppChartpay; 
		function loadChartpay(year)
		{
			fetch(getBaseURL() + `/monthly-payments?year=${year}`)
			.then(response => response.json())
			.then(data => {
				console.log(data.total_applications);
				//alert(data.total_applications);
				$("#summaryAmount").text(data.total_applications);
				
				//document.getElementById('summaryAmount').innerText = data.total_applications;
				if (monthlyAppChartpay) {
					monthlyAppChartpay.destroy();
				}
				console.log("Labels length:", data.labels.length);
				data.datasets.forEach(ds => {
					console.log(`${ds.label} data length:`, ds.data.length);
				});
				const ctx = document.getElementById('myChart').getContext('2d'); // canvas id must match
				monthlyAppChartpay = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: data.labels,
						datasets: data.datasets
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { position: 'top' },
							title: { display: true, text: `Monthly Payment Summary for ${year}` },
							tooltip: { mode: 'index', intersect: false }
						},
						scales: {
							x: { stacked: false, title: { display: true, text: 'Month' } },
							y: { beginAtZero: true, title: { display: true, text: 'Amount' }, ticks: { precision: 0 } }
						}
					}
				});
			})
			.catch(error => console.error(error));
		}
		loadChartpay(document.getElementById('yearFilterp').value);
		// Update chart when year changes
		document.getElementById('yearFilterp').addEventListener('change', function() {
			loadChartpay(this.value);
		});

		const labelsnew = ['LH Properties', 'FH Properties', 'Unallotted'];

		// Chart 1: Land Value Comparison
		const landValueChart = new Chart(document.getElementById('landValueChart'), {
		type: 'bar',
		data: {
		labels: labelsnew,
		datasets: [
		{
		label: 'L&DO Value (₹ Cr)',
		data: [
		{{ round($statusLdoValue["lease_hold"] / 10000000) }}, 
		{{round($statusLdoValue['free_hold'] / 10000000)   }}, 
		{{round($statusLdoValue['unallotted'] / 10000000)  }}],
		backgroundColor: '#047edf'
		},
		{
		label: 'Circle Rate Value (₹ Cr)',
		data: [
		{{round($statusCircleValue["lease_hold"] / 10000000) }}, 
		{{round($statusCircleValue["free_hold"] / 10000000)  }}, 
		{{round($statusCircleValue["unallotted"] / 10000000)  }}],
		backgroundColor: '#cf0633'
		}
		]
		},
		options: {
		responsive: true,
		plugins: {
		legend: { position: 'top' },
		title: { display: true, text: 'Land Value Comparison (L&DO vs Circle Rate)' }
		}
		}
		});

		// Chart 2: Number of Properties
		const combinedChart = new Chart(document.getElementById('propertyCountChart'), {
		type: 'bar',
		data: {
			labels: ['LH Properties', 'FH Properties', 'Unallotted'],
			datasets: [
				{
					label: 'Number of Properties',
					data: [{{$statusCount['lease_hold']}}, {{$statusCount['free_hold']}}, {{$statusCount['unallotted']}}],
					backgroundColor: '#0fa49b',
					yAxisID: 'y'
				},
				{
					label: 'Area (in Sqm)',
					data: [{{round($statusArea['lease_hold'])}}, {{round($statusArea['free_hold'])}}, {{round($statusArea['unallotted'])}}],
					backgroundColor: '#5a49e9',
					yAxisID: 'y1'
				}
			]
		},
		options: {
			responsive: true,
			plugins: {
				title: {
					display: true,
					text: 'Number of Properties & Area (in Sqm) by Property Type'
				},
				legend: {
					position: 'top'
				}
			},
			scales: {
				y: {
					type: 'linear',
					position: 'left',
					title: {
						display: true,
						text: 'Number of Properties'
					},
					beginAtZero: true
				},
				y1: {
					type: 'linear',
					position: 'right',
					title: {
						display: true,
						text: 'Area (in Sqm)'
					},
					grid: {
						drawOnChartArea: false
					},
					beginAtZero: true
				}
			}
		}
		});

		let monthlyAppChart;
		function loadChart(year)
		{
		fetch(getBaseURL() +`/chart/monthly-applications?year=${year}`)
		.then(response => response.json())
		.then(data => {
			if (monthlyAppChart) {
				monthlyAppChart.destroy(); // destroy old chart
			}

			const ctx = document.getElementById('monthlyAppChart').getContext('2d');
			monthlyAppChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: data.labels,
					datasets: data.datasets
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { position: 'top' },
						title: {
							display: true,
							text: `Monthly Application Summary for ${year}`
						},
						tooltip: {
							mode: 'index',
							intersect: false
						}
					},
					scales: {
						x: {
							stacked: false,
							title: {
								display: true,
								text: 'Month'
							}
						},
						y: {
							beginAtZero: true,
							title: {
								display: true,
								text: 'Number of Applications'
							},
							ticks: { precision: 0 }
						}
					}
				}
			});
		});
		}

		// Load current year by default
		const currentYear = new Date().getFullYear();
		document.getElementById('yearFilter').value = currentYear;
		loadChart(currentYear);
		document.getElementById('yearFilter').addEventListener('change', function () {
		loadChart(this.value);
		});

		// ============ Outstanding dues vs number of property by property type ======================
		const ctx14 = document.getElementById('propertyDueChart').getContext('2d');
		const propertyChartfull = new Chart(ctx14, {
		type: 'bar',
		data: {
			labels: ['Lease Hold', 'Free Hold'],
			datasets: [
				{
					label: 'Outstanding Dues (in Cr)',
					data: [{{ $leaseHoldDue }}, {{ $freeHoldDue }}],
					backgroundColor: '#FFA500',
					yAxisID: 'y'
				},
				{
					label: 'No. of Properties',
					data: [{{ $leaseHoldProperties }}, {{ $freeHoldProperties }}],
					backgroundColor: '#008080',
					yAxisID: 'y1'
				}
			]
		},
		options: {
			responsive: true,
			plugins: {
				title: {
					display: true,
					text: 'Outstanding Dues vs Number of Properties by Property Status'
				}
			},
			scales: {
				y: {
					type: 'linear',
					position: 'left',
					title: { display: true, text: 'Outstanding Dues (in Cr)' }
				},
				y1: {
					type: 'linear',
					position: 'right',
					grid: { drawOnChartArea: false },
					title: { display: true, text: 'Number of Properties' }
				}
			}
		}
		});

		// ============ Lease Hold / Free Hold + Area Combined Chart ======================
		const labels = @json($chartData['labels']);
		const ctxf = document.getElementById('propertyAreaCombinedChart').getContext('2d');
		new Chart(ctxf, {
		type: 'bar',
		data: {
			labels: labels,
			datasets: [
				{
					label: 'Lease Hold',
					data: @json($chartData['leaseHold']),
					backgroundColor: '#243ca7',
					stack: 'count'
				},
				{
					label: 'Free Hold',
					data: @json($chartData['freeHold']),
					backgroundColor: '#dc7f27',
					stack: 'count'
				},
				{
					label: 'Total Area (sqm)',
					type: 'line',
					data: @json($chartData['area']),
					backgroundColor: '#59a14f',
					borderColor: '#59a14f',
					yAxisID: 'y1',
					fill: false,
					tension: 0.3,
					pointStyle: 'circle',
					pointRadius: 5,
					pointHoverRadius: 7
				}
			]
		},
		options: {
			responsive: true,
			interaction: { mode: 'index', intersect: false },
			plugins: {
				title: {
					display: true,
					text: 'Lease/Free Hold Count vs Total Area by Property Range'
				},
				tooltip: { mode: 'index', intersect: false },
				legend: { position: 'top' }
			},
			scales: {
				x: {
					title: { display: true, text: 'Area Range (sqm)' }
				},
				y: {
					type: 'linear',
					position: 'left',
					stacked: true,
					title: { display: true, text: 'Number of Properties' }
				},
				y1: {
					type: 'linear',
					position: 'right',
					stacked: false,
					title: { display: true, text: 'Total Area (sqm)' },
					grid: { drawOnChartArea: false }
				}
			}
		}
		});

		// ============ Lease Hold vs Free Hold Count ======================
		//		const ctx = document.getElementById('propertyChart').getContext('2d');
		//		new Chart(ctx, {
		//			type: 'bar',
		//			data: {
		//				labels: labels,
		//				datasets: [
		//					{ label: 'Lease Hold', data: @json($chartData['leaseHold']), backgroundColor: '#4d00e8' },
		//					{ label: 'Free Hold', data: @json($chartData['freeHold']), backgroundColor: '#00a200' }
		//				]
		//			},
		//			options: {
		//				responsive: true,
		//				plugins: {
		//					title: { display: true, text: 'Number of Lease Hold vs Free Hold Properties by Area' },
		//					tooltip: { mode: 'index', intersect: false },
		//					legend: { position: 'top' }
		//				},
		//				scales: {
		//					x: {
		//						stacked: true,
		//						title: { display: true, text: 'Area Range (sqm)' }
		//					},
		//					y: {
		//						stacked: true,
		//						title: { display: true, text: 'Number of Properties' }
		//					}
		//				}
		//			}
		//		});

		// ============ Total Area Chart ======================
		const ctx1 = document.getElementById('areaChart').getContext('2d');
		new Chart(ctx1, {
		type: 'bar',
		data: {
			labels: labels,
			datasets: [
				{ label: 'Total Area (sqm)', data: @json($chartData['area']), backgroundColor: '#ff5500' }
			]
		},
		options: {
			responsive: true,
			plugins: {
				title: { display: true, text: 'Total Area of Properties by Area Range' },
				tooltip: { mode: 'index', intersect: false },
				legend: { display: false }
			},
			scales: {
				x: { title: { display: true, text: 'Area Range (sqm)' } },
				y: { title: { display: true, text: 'Total Area (sqm)' } }
			}
		}
		});

		// ============ Doughnut Chart (Nazul vs Rehabilitation) ======================
		const ctx2 = document.getElementById("chartProperties1").getContext('2d');
		const gradientNazul = ctx2.createLinearGradient(0, 0, 0, 300);
		gradientNazul.addColorStop(0, 'red');
		const gradientRehab = ctx2.createLinearGradient(0, 0, 0, 300);
		gradientRehab.addColorStop(0, '#5fbf00');

		new Chart(ctx2, {
		type: 'doughnut',
		data: {
			labels: ['Nazul', 'Rehabilitation'],
			datasets: [{
					label: 'Number of Properties',
					data: [
						{{ $landTypeCount['Nazul'] ?? 0 }},
						{{ $landTypeCount['Rehabilitation'] ?? 0 }}
					],
					backgroundColor: [gradientNazul, gradientRehab],
					hoverBackgroundColor: [gradientNazul, gradientRehab],
					borderWidth: 0
				}]
		},
		options: {
			maintainAspectRatio: false,
			responsive: true,
			cutout: '60%',
			plugins: {
				//datalabels: datalabelOptions, // only works if plugin included
				legend: { position: 'bottom', labels: { boxWidth: 8 } },
				tooltip: { enabled: true }
			}
		}
		});

		// ============ Land Value Chart ======================
		const ctx15 = document.getElementById('landvalueChartnew').getContext('2d');
		new Chart(ctx15, {
		type: 'bar',
		data: {
			labels: <?= json_encode($landValueChartData['labels']) ?>,
			datasets: [{
				label: 'Number of properties categorized by their land value',
					data: <?= json_encode($landValueChartData['data']) ?>,
					backgroundColor: '#7c00ba'
				}]
		},
		options: {
			responsive: true,
			plugins: {
				legend: { display: true },
				tooltip: { enabled: true }
			},
			scales: { y: { beginAtZero: true } }
		}
		});

		// ============ Download Chart (PNG/PDF) ======================
		async function downloadChart(chartId, type)
		{
		const canvas = document.getElementById(chartId);
		const imgData = canvas.toDataURL('image/png');

		if (type === 'png') {
			const a = document.createElement('a');
			a.href = imgData;
			a.download = `${chartId}.png`;
			a.click();
		} else if (type === 'pdf') {
			const { jsPDF } = window.jspdf;
			const pdf = new jsPDF();
			pdf.addImage(imgData, 'PNG', 10, 10, 180, 100);
			pdf.save(`${chartId}.pdf`);
		}
		}

		// Auto-add top-left icons
		window.addEventListener('DOMContentLoaded', () => {
		const wrappers = document.querySelectorAll('.chart-container-1,.chart-container-2');
		wrappers.forEach(wrapper => {
			const canvas = wrapper.querySelector('canvas');
			const chartId = canvas.id;
			const iconContainer = document.createElement('div');
			iconContainer.className = 'download-icons';
			iconContainer.innerHTML = `
		    <button onclick="downloadChart('${chartId}', 'png')" title="Download PNG">📥</button>
		    <button onclick="downloadChart('${chartId}', 'pdf')" title="Download PDF">📄</button>
		`;
			wrapper.appendChild(iconContainer);
		});
		});

		// ============ Colony filter + table update ======================
		let colony_id = null;
		const datalabelOptions = {
		color: '#444',
		anchor: 'center',
		align: 'top',
		font: { size: 14 }
		};

		// Animate numbers
		document.addEventListener("DOMContentLoaded", function () 																																								{
			const animationTargets = document.querySelectorAll(".text-dark, .view-list");
			animationTargets.forEach(el => {
				let isMoney = el.innerHTML.indexOf('₹') >= 0;
				let isCrore = el.innerHTML.indexOf('Cr.') >= 0;
				const finalValue = parseInt(el.innerHTML.replace(/[₹,]| Cr\./g, '').trim());
				const startValue = finalValue * 0.8;
				const duration = 4000;
				const startTime = performance.now();

				function animate(time)
				{
					const elapsed = time - startTime;
					const progress = Math.min(elapsed / duration, 1);
					const currentValue = Math.round(startValue + (finalValue - startValue) * progress);
					el.textContent = (isMoney ? '₹ ' : '') + customNumFormat(currentValue) + (isCrore ? ' Cr.' : '');
					if (progress < 1)
						requestAnimationFrame(animate);
				}
				requestAnimationFrame(animate);
			});
		});

		const getTabData = (tabId) => {
        var ajaxUrl = "{{ route('propertyTypeDetails', [':typeId', ':colonyId']) }}"
    .replace(':typeId', tabId)
    .replace(':colonyId', colony_id ? colony_id : '');
        $.ajax({
            type: 'get',
            url: ajaxUrl,
            success: response => {
                let updatedHTML = '';
                //get max counter 
                let max = 0;
                response.map(row => {
                    max = row.counter > max ? row.counter : max;
                });
                //get progressbar html for each row
                response.map(row => {
                    let width = (max > 0) ? (row.counter / max) * 100 : 0;
                    updatedHTML += `<li>
						<div class = "d-flex justify-content-between align-items-center mb-2">
							<span class = "progress-title">${row.PropSubType}</span>
							<span class="progress-result">${row.counter}</span>
						</div>
						<div class = "progress mb-4" style = "height:7px;">
						<div class="progress-bar" role="progressbar" style="width: ${width}%" aria-valuenow="${row.counter}" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</li>`

                })
                $('ul.progress-report').html(updatedHTML);
            },
            error: errorResponse => {
                console.log(errorResponse);
            }
        })
    }

    /** on selecting  a colony froom colony dropdown*/

    $('#colony-filter').change(() => {
        colony_id = $('#colony-filter').val();
        if (colony_id != "") {
            $('#colony-filter').find('option:first').text('Remove Colony Filter');
            $('#colony-filter').selectpicker('refresh');
            $.ajax({
                type: 'POST',
                url: "{{route('dashbordColonyFilter')}}",
                data: {
                    _token: '{{csrf_token()}}',
                    colony_id: colony_id
                },
                success: (response) => {
		       $('.outside-tiles').addClass('d-none');
             if (Object.keys(response).length > 0) {
                        $('#no-filter-applied').addClass('d-none'); // hide main dashboaed
                        $('#filter-applied').removeClass('d-none'); // show filtered table and tabs

                        for (const key in response) {
                            // update the tiles
                            if (response.hasOwnProperty(key)) {
                                const value = response[key];
                                let targetElement = $('#tile_' + key);
                                if (targetElement.length) {
                                    if (targetElement.hasClass('hoverable')) {
                                        targetElement.attr('title', value);
                                    } else {
                                        targetElement.html(value);
                                    }
                                }

                            }
                        }
                    }

                    //update property types

                    if (response.property_types && Object.keys(response.property_types).length > 0) {
                       // $('.tab-total-no').html(0);
                        let ptypes = response.property_types
                        for (const ind in ptypes) {
                            // debugger;
                            if (ptypes.hasOwnProperty(ind)) {
                                const value = ptypes[ind];
                                let targetElement = $('#' + ind + '_count');
                                if (targetElement.length) {
                                    targetElement.html(value);
                                }

                                //update the tab data
                                let tabElement = $('#tab_total_' + ind);
                                if (tabElement.length) {
                                    tabElement.html(value);
                                }
                            }
                        }
                    }

                    //update table
                    if (response.areaRangeData.length > 0) {
                        const tbody = $('#area-wise-details tbody');
                        $('#area-wise-details tbody').empty();
                        response.areaRangeData.forEach(item => {
                            tbody.append(`<tr>
								<td>${item.label}</td>
								<td>${item.count} (${item.percent_count}%)</td>
								<td>${item.leaseHoldCount} (${item.percent_leaseHold}%)</td>
								<td>${item.freeHoldCount} (${item.percent_freeHold}%)</td>
								<td>${item.area.toFixed(2)} (${item.percent_area}%)</td>
							</tr>`);
                        });
                        let totalAreaInSqm = response.total_area;
                        let totalAreaInAcre = Math.round(totalAreaInSqm * 0.0002471053815);
                        tbody.append(`<tr>
								<th>Grand Total</th>
								<th>${response.total_count}</th>
								<th colspan="3">${totalAreaInSqm.toFixed(3)} (${totalAreaInAcre} Acres)</th>
							</tr>`);
                    }

                    //click the first tab

                    var elemFound = $('[id^=tab_total_]');
                    if (elemFound.length > 0) {
                        elemFound[0].closest('a').click();
                    }


                },
                error: (err) => {
                    console.log(err);
                }
            })
        } else {
            //reset the filter
            $('.text-dark').each((i, elm) => { // reset text content
                if ($(elm).attr('data-original') !== undefined) {
                    $(elm).html($(elm).attr('data-original'));
                }
            })
            $('.hoverable').each((i, elm) => { // reset text content
                if ($(elm).attr('data-original') !== undefined) {
                    $(elm).attr('title', $(elm).attr('data-original'));
                }
            })

             var elemFound = $('[id^=original_tab_total_]');
            if (elemFound.length > 0) {
                elemFound[0].click();
            }


            $('#no-filter-applied').removeClass('d-none'); // show main dashboaed
            $('#filter-applied').addClass('d-none'); // hide filtered table and tabs

            //Chnage the dropdown first option

            $('#colony-filter').find('option:first').text('Filter by Colony ({{$number_of_colonies}})');
            $('#colony-filter').selectpicker('refresh');

            // reset colony id
            colony_id = null;
$('.outside-tiles').removeClass('d-none');
        }
    })


    $('.view-list').click(function() {
        let linkEnabled = '<?= $isPublic == true ? 0 : 1 ?>';
        if (linkEnabled == '1') {
            let filterName = $(this).data('filterName');
            let filterValue = $(this).data('filterValue');
            let filterData = (filterName && filterValue) ? {
                [`${filterName}[]`]: filterValue
            } : {};

            if (colony_id) {
                filterData = {
                    ...filterData,
                    "colony[]": colony_id
                }
            }

            // Create a query string from the filterData
            let queryString = $.param(filterData);

            // Construct the URL with the query string
            let url = '';
            if (filterValue == 'unalloted') {
                url = `{{ route("unallotedReport") }}`;
            }else if(filterValue == 'vaccant')
            {
                url = `{{ route("vacant.land.list") }}`;
            } else {
                url = `{{ route("detailedReport") }}?${queryString}`;
            }

            // Open the URL in a new tab
            window.open(url, '_blank');
        }

    });
		</script>

		{{-- @endhaspermission --}}
		@endif

		<style>.nav-tabs-right-aside{
		overflow-x: hidden;
		height: 475px;
		overflow-y: scroll;
		}.download-icons {
		position: absolute;
		top: 2px;
		right: 10px;
		z-index: 10;
		}

		.download-icons button {
		background: rgba(255, 255, 255, 0.9);
		border: 1px solid #ccc;
		border-radius: 4px;
		padding: 4px 6px;
		margin-right: 5px;
		font-size: 14px;
		cursor: pointer;
		}

		.download-icons button:hover {
		background: #e0e0e0;
		}</style>
		@endsection
