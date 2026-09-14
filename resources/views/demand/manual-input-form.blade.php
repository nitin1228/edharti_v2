@extends('layouts.app') @section('title', 'Create Demand') @section('content')
<link rel="stylesheet" href="{{ asset('assets/css/rgr.css') }}" />
<style>
  .subhead-input {
    margin: 10px 0 !important;
    padding: 10px 0 !important;
    border-radius: 10px;
  }

  #detail-container>tr>td:not(:nth-child(2)) {
    width: 15%;
  }
</style>
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
  <div class="breadcrumb-title pe-3">Demand</div>
  <div class="ps-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 p-0">
        <li class="breadcrumb-item">
          <a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
        </li>
        <li class="breadcrumb-item" aria-current="page">Demand</li>
        <li class="breadcrumb-item active" aria-current="page">
          @if (Route::is('createDemandView'))
          Create demand
          @elseif (Route::is('EditDemand'))
          Edit demand
          @endif
        </li>
      </ol>
    </nav>
  </div>
  <!-- <div class="ms-auto"><a href="#" class="btn btn-primary">Button</a></div> -->
</div>
<!--end breadcrumb-->
<hr />
@php
$propertyAllreadySelected = isset($demand) || isset($applicationData);
$creatingNewDemand = !isset($demand);
$propertSelectorPath = $propertyAllreadySelected ? null: 'include.parts.property-selector';
@endphp
<div class="card">
  <div class="card-body">
    @if($propertyAllreadySelected)
    <div class="row">
      <div class="col-lg-12">
        <div class="part-title">
          <h5>Property
            @if(isset($demand) || isset($applicationData)) and @endif
            @isset($demand) Demand @endisset @isset($applicationData) Application @endisset Details</h5>
        </div>
        <div class="part-details">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12 col-12">
                <table class="table table-bordered property-table-info">
                  <tbody>
                    <th>Old Property ID:</th>
                    <td>{{ $demand->splited_property_detail->old_property_id ?? $demand->property_master->old_propert_id ?? $applicationData->old_property_id }}</td>

                    <th>New Property ID:</th>
                    <td>{{ $demand->splited_property_detail->child_property_id ?? $demand->property_master->unique_propert_id ?? $applicationData->new_property_id }}</td>

                    <tr>
                      <th>Property Status:</th>
                      <td colspan="3">{{ getServiceNameById($demand->splited_property_detail->property_status ?? $demand->property_master->status ?? $applicationData->property_status) }}</td>
                    </tr>

                    <tr>
                      <th>Property Type:</th>
                      <td>{{ getServiceNameById($demand->property_master->property_type ?? $applicationData->propertyMaster->property_type) }}</td>

                      <th>Presently Known As:</th>
                      <td>{{ $demand->property_known_as ?? $applicationData->propertyMaster->plot_or_property_no.'/'. $applicationData->propertyMaster->block_no.'/'. $applicationData->propertyMaster->newColony->name}}</td>
                    </tr>

                    <tr>
                      <th>Lesse's Name:</th>
                      <td colspan="3">{{ $demand->current_lessee ?? $applicationData->name_as_per_lease_conv_deed }}</td>
                    </tr>
                    @isset($demand)
                    <tr>
                      <th>Demand Id:</th>
                      <td>{{ $demand->unique_id ?? 'N/A' }}</td>

                      <th>Amount:</th>
                      <td>₹ {{ customNumFormat($demand->net_total ?? 0) }}</td>
                    </tr>

                    <tr>
                      <th>Balance:</th>
                      <td>₹ {{ customNumFormat($demand->balance_amount ?? 0) }}</td>

                      <th>Financial Year:</th>
                      <td>{{ $demand->current_fy ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    @if(isset($applicationData))
                    <tr>
                      <th>Application No.</th>
                      <td>{{$applicationData->application_no}}</td>
                      <th>Application Type</th>
                      <td>{{$applicationData->service_type->item_name}}</td>
                    </tr>
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        {{-- <table class="table table-bordered table-striped">
          <tr>
            <td colspan="4">
              <h5>Property Details</h5>
            </td>
          </tr>
          <tr>
            <th>Known As</th>
            <td>{{$demand->property_known_as}}</td>
        <th>Lesse&apos;s Name</th>
        <td>{{$demand->current_lessee}}</td>
        </tr>
        </table>
        <br>
        <table class="table table-bordered table-striped">
          <tr>
            <td colspan="8">
              <h5>Demand details</h5>
            </td>
          </tr>
          <tr>
            <th>Demand Id</th>
            <td>{{$demand->unique_id}}</td>
            <th>Amount</th>
            <td>₹ {{customNumFormat($demand->net_total)}}</td>
            <th>Balance</th>
            <td>₹ {{customNumFormat($demand->balance_amount)}}</td>
            <th>Financial Year</th>
            <td>{{$demand->current_fy}}</td>
          </tr>
        </table>
        <br>
        <!-- <table class="table table-bordered">
            <tr>
                <th>Property</th>
                <th>Unique Demand Id</th>
                <th>Financial Year</th>
                <th>Net Total</th>
                <th>Balance</th>
            </tr>
            <tr>
                <th>{{$demand->property_known_as}}</th>
                <th>{{$demand->unique_id}}</th>
                <th>{{$demand->current_fy}}</th>
                <th>₹{{customNumFormat($demand->net_total)}}</th>
                <th>₹{{customNumFormat($demand->balance_amount)}}</th>
            </tr>
        </table> -->
        <br> --}}
        @endif
        <div class="row">
          <div class="col-lg-12 mb-2  {{$propertyAllreadySelected ? 'd-none':''}}">
            @if($propertSelectorPath)
            @include($propertSelectorPath)
            @endif
          </div>
        </div>
        <div class="col col-lg-2 pt-1 mb-2 {{$propertyAllreadySelected ? 'd-none':''}}">
          <button type="button" class="btn btn-primary px-4 mt-4" id="submitButton">Search<i class="bx bx-right-arrow-alt ms-2"></i></button>
        </div>

        <div class="d-none" id="detail-card">
          {{-- <h5 class="mb-4 pt-3 text-decoration-underline">BASIC DETAILS</h5> --}}
          <div class="pb-3">

            <div class=""> <!-- this div add by anil on 21-01-2025-->
              <table class="table table-bordered table-striped">
                <thead>
                  <th colspan="5">BASIC DETAILS</th>
                </thead>
                <tbody id="detail-container">
                </tbody>
              </table>
            </div>
          </div>
          <button type="button" class="btn btn-primary mb-2" id="btn-demand" data-action="show">Continue</button>

        </div>
        <div class="{{ $propertyAllreadySelected ? '':'d-none' }}" id="input-form-container">
          <form id="demand-input-form" method="post" action="">
            <div id="formOldDemandDetails"></div>
            <input type="hidden" id="selectedOldPropertyId" name="oldPropertyId" value="{{$demand->old_property_id ?? $applicationData->old_property_id ?? ''}}" />
            <input type="hidden" name="id" value="{{isset($demand) ? $demand->id : ''}}" />
            <input type="hidden" name="application_no" value="{{$demand->app_no ?? $applicationData->application_no ?? ''}}" />
            @csrf
            <div id="subhead-container">
              @foreach (isset($demand) ? $demand->demandDetails : [null] as $key => $detail)
              <div class="row mt-2 subhead-input">
                <input type="hidden" name="detail_id[]" value="{{ $detail->id ?? '' }}">

                <div class="col-lg-4">
                  <div class="form-group" style="margin-right: 0px; margin-bottom: 5px;">
                    <label for="subhead_input">Subhead<span class="text-danger">*</span></label>
                    <select name="subhead[]" class="form-select" required>
                      <option value="">Select</option>
                      @foreach ($demandSubheads as $subhead)
                      @php
                      $additional_data = !is_null($subhead->additional_data) ? json_decode($subhead->additional_data) : null;
                      $setDurationRequired = isset($additional_data->duration);
                      $setRemarksRequired = isset($additional_data->remarks);
                      @endphp
                      <option value="{{ $subhead->id }}"
                        {{ ($subhead->id == ($detail->subhead_id ?? '')) ? 'selected' : '' }}
                        {{ $setDurationRequired ? 'attrDuration="true"' : '' }}
                        {{ $setRemarksRequired ? 'attrRemarks="true"' : '' }}>
                        {{ $subhead->item_name }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-lg-4">
                  <div class="form-group" style="margin-bottom: 5px;">
                    <label for="amount">Amount<span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="amount[]" min="0" step="0.01" required
                      value="{{ $detail->total ?? '' }}">
                  </div>
                </div>

                <div class="col-lg-4">
                  <div class="mix-field">
                    <div>
                      <label for="fromduration">From</label>
                      <input type="date" class="form-control" name="duration_from[]"
                        value="{{ $detail->duration_from ?? '' }}">
                    </div>
                    <span>-</span>
                    <div>
                      <label for="toduration">To</label>
                      <input type="date" class="form-control" name="duration_to[]"
                        value="{{ $detail->duration_to ?? '' }}">
                    </div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="form-group" style="margin-bottom: 5px;">
                    <label for="remark">Remark</label>
                    <div class="mix-field">
                      <div>
                        <textarea name="remark[]" class="form-control" rows="3">{{ $detail->remarks ?? '' }}</textarea>
                      </div>
                      @if(!isset($openInReadOnlyMode))
                      <button type="button" class="btn btn-danger btn-remove-subhead ml-2"><i class="bx bx-trash"></i></button>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <div class="row">
              <div class="col-lg-12">
                @if(!isset($openInReadOnlyMode))
                <button type="button" id="btn-add-more" class="btn btn-primary"><i class='bx bx-plus'></i> Add More</button>
                @endif
              </div>
            </div>
            <div class="row mt-2 mb-2">
              <div class="col-lg-12">
                <div class="bill-raise">
                  <h6 class="demand-total">Demand Total Amount:</h6>
                  <h6 class="demand-amount">₹<span id="demandTotalAmount">{{isset($demand) ? customNumFormat($demand->net_total) : 0}}</span></h6>
                </div>
              </div>
            </div>
            <div class="row mb-2">

              <div class="col-lg-4">
                @if(!isset($openInReadOnlyMode))
                <button type="button" class="btn btn-success float-right" id="btn-submit">Submit</button>
                @endif
              </div>
              <div class="col-lg-8 d-flex justify-content-end">
                @if(isset($canApprove) && $canApprove)
                <a href="{{$demand->status == getServiceType('DEM_PENDING') ? '': route('ApproveDemand',$demand->id)}}"><button type="button" class="btn btn-success mr-2" {{$demand->status == getServiceType('DEM_PENDING') ? 'disabled': ''}}>{{$demand->status == getServiceType('DEM_PENDING') ? 'Approved': 'Approve'}}</button></a>
                @endif
                @if(isset($canEdit) && $canEdit)
                <a href="{{route('EditDemand',$demand->id)}}"><button type="button" class="btn btn-warning">Edit</button></a>
                @endif
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    @include('include.alerts.ajax-alert')
    <div class="modal fade" id="confirmNewDemandModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="submitModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"> <!-- modal-dialog-centered class added by anil on 21-01-2025 -->
        <div class="modal-content text-center">
          <div class="modal-header border-0 h-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            {{-- <img
              src="{{ asset('assets/images/update.svg') }}"
            alt="success"
            class="success_icon" /> --}}
            <h5 class="modal-title mb-2" id="ModalSuccessLabel">Are you sure?</h5>
            <p id="confirmationMessage">
              For the selected property, unpaid demand found. Do you want to
              continue?
            </p>
            <div class="row mt-2">
              <div class="col-lg-12" id="oldDemandDetails"></div>
            </div>
          </div>
          <div class="modal-footer border-0 justify-content-center">
            <button type="button" class="btn btn-secondary btn-width" data-bs-dismiss="modal" id="confirmation-no">No</button>
            <button type="button" name="status" value="submit" class="btn btn-primary btn-width" id="confirmation-yes">Yes</button> <!-- change the button color yellow to theme green by anil on 21-01-2025 -->
          </div>
        </div>
      </div>
    </div>
    @endsection
    @section('footerScript')
    <script src="{{ asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script>
      let propertyId;
      let propertyTypes;
      let propertyDetails;
      let demandDetailsHtml;
      let prevPendingAmount = 0;
      let redirectToEdit = false;
      let oldDemandId = [];
      let editDemandId = null;


      $(document).ready(function() {
        let openInReadOnlyMode = <?= isset($openInReadOnlyMode) ? 'true' : 'false' ?>;
        let creatingNewDemand = <?= $creatingNewDemand ? 'true' : 'false' ?>;
        if (openInReadOnlyMode) {
          $('#demand-input-form').find('input, textarea').attr('readonly', true);
          $('#demand-input-form').find('select').attr('disabled', true);
        }
        if (creatingNewDemand) {
          getSelctedPropertyOldDemand();
        }
        toggleRemoveButton();
      });

      $("#submitButton").click(function() {
        propertyId = !isNaN($("#oldPropertyId").val()) && $("#oldPropertyId").val().length == 5 ?
          $("#oldPropertyId").val() :
          $("#property").length > 0 && $("#property").val() != "" ?
          $("#property").val() :
          $("#plot").length > 0 && $("#plot").val() != "" ? $("#plot").val() : "";
        getPropertyBasicDetail(propertyId);
      });

      /** get detail of property when property is selected */
      function getPropertyBasicDetail(propId) {
        $.ajax({
          type: "post",
          url: "{{route('propertyCommonBasicdetail')}}",
          data: {
            _token: "{{csrf_token()}}",
            property_id: propId,
          },
          success: function(response) {
            if (response.status == "success") displayPropertyDetails(response.data);
            else {
              showError(response.message);
            }
          },
        });
      }
      /** display details of property */
      function displayPropertyDetails(data) {
        $("#detail-container").empty();
        if (Array.isArray(data)) {
          $("#detail-container").html(`<tr>
                      <td colspan="5"><h6>Given property has ${data.length} propert${
              data.length > 1 ? "ies" : "y"
            }</h6></td>
                  </tr>`);
          data.forEach(function(row, i) {
            appendPropertyDetail(row, true, i + 1);
          });
          $("#detail-container").append(`<tr>
                <td colspan="5"><h5>Pease enter property id of splited property to continue</h5></td>
            </tr>`);
          $("#btn-rgr").prop("disabled", true);
        } else {
          appendPropertyDetail(data);
          $("#property_id").val(data.id);
          $("#splited").val(data.is_joint_property === undefined ? 1 : 0);
        }
        $("#selectedOldPropertyId").val(
          data.old_property_id ?? data.old_propert_id
        );
        $("#detail-card").removeClass("d-none");
      }

      function appendPropertyDetail(row, isMultiple = false, rowNum = null) {
        if (isMultiple && rowNum) {
          $("#detail-container").append(`<tr>
                <td>${rowNum}</td><td colspan="4"></td>
            </tr>`);
        }
        // removed <td><b>Land Value : </b> &nbsp;-</td>
        let transferHTML = "";
        if (row.trasferDetails && row.trasferDetails.length > 0) {
          transferHTML = `<div class= "transfer-details" style="display: inline; position:absolute">
            <span class="qmark">&#8505;</span>
            <ul class="transfer-list container">
                <li class="transfer-list-item row row-lg-4">
                    <div class="transfer-list-cell col">#</div>
                    <div class="transfer-list-cell col">Transfer Date</div>
                    <div class="transfer-list-cell col">Process </div>
                    <div class="transfer-list-cell col">Lessee Name</div>
                    </li>
            `;
          row.trasferDetails.forEach((data, i) => {
            transferHTML += `<li class="transfer-list-item row row-lg-4">
                    <div class="transfer-list-cell col">${i + 1}</div>
                    <div class="transfer-list-cell col">${data.transferDate ? data.transferDate.split('-').reverse().join('-'):'N/A'}</div>
                    <div class="transfer-list-cell col">${data.process_of_transfer}</div>
                    <div class="transfer-list-cell col">${data.lesse_name}</div>
                    </li>`;
          });
          transferHTML +
            `</ul>
            </div>`;
        }

        landValue = row.plot_value ?? row.property_lease_detail.plot_value ?? 'N/A';
        isPropertyTypeCommercial = row.property_type == 48;
        let detailHTML = `
        <div class="part-title">
                        <h5>Property Basic Details</h5>
                      </div>
        <div class="part-details">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-lg-12 col-12">
                    <table class="table table-bordered property-table-info">
                      <tbody>
                        <tr>
                          <th>Old Property ID:</th>
                          <td colspan="">${row.old_propert_id}</td>
                          <th>New Property ID:</th>
                          <td colspan="">${row.unique_propert_id}</td>
                          <th>Colony: </th>
                          <td>${row.colony}</td>
                        </tr>
                        <tr>
                          <th>Land Size:</th>
                          <td colspan="2">${ customNumFormat(Math.round(row.landSize * 100) / 100)}Sq. Mtr. <small>(${ Number(sqmToSqyard(row.landSize)).toFixed(2) } Sq. Yard )</small></td>
                          <th>Land Value:</th>
                          <td colspan="2">₹${customNumFormat(Math.round(landValue*100)/100)}</td>
                        </tr>

                        <tr>
                          <th>Property Type:</th>
                          <td>${row.proprtyTypeName}</td>
                          <th>Property Sub-type:</th>
                          <td>${row.proprtySubtypeName}</td>
                          <th>Land Type:</th>
                          <td>${row.landTypeName}</td>
                        </tr>
                        <tr>
                          <th>Present Lessee:</th>
                          <td colspan="2">${row.lesseName ? row.lesseName.replaceAll(',', ', ') : "N/A"} ${transferHTML}</td>
                            <th>Property Status:</th>
                            <td colspan="2">${row.statusName}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>`
        let knownAs = row.presently_known_as ?? row.property_lease_detail.presently_known_as ?? ''
        if (knownAs != "") {
          $('#known-as').html(knownAs);
          $('#knownAsDiv').show();
        }
        $("#detail-container").append(detailHTML);
      }


      /** function checks and return unpaid demand for property */
      $("#btn-demand").click(getSelctedPropertyOldDemand);

      // $(document).ready(getSelctedPropertyoldDemand);

      function getSelctedPropertyOldDemand() {
        oldDemandId = [];
        var selectedOldPropertyId = $("#selectedOldPropertyId").val();
        if (selectedOldPropertyId && selectedOldPropertyId != "") {
          $.ajax({
            type: "get",
            url: "{{url('/demand/getExistingPropertyDemand')}}" + "/" + selectedOldPropertyId,
            success: function(response) {
              if (response.status) {
                if (response.data && (response.data.demand || (response.data.dues && response.data.dues > 0))) {
                  if (response.data.demand) {
                    var oldDemand = response.data.demand;
                    var confirmationMessage = oldDemand.status_code == 'DEM_DRAFT' ? "There is already a demand with status DRAFT against the selected property. If you continue then new data will be added to the previously saved demand." : "There is already a unpaid demand against this property. Do you want to create a new demand? All unpaid subheads will be carrieed forward to new demand."
                    $('#confirmationMessage').html(confirmationMessage);
                    prevPendingAmount += oldDemand.balance_amount;
                    redirectToEdit = (oldDemand.status_code && oldDemand.status_code == 'DEM_DRAFT')
                    editDemandId = oldDemand.id;
                    $("#demandTotalAmount").text(customNumFormat(prevPendingAmount));
                    var demandDetails = response.data.demandDetails;
                    demandDetailsHtml = `<div class="row mt-2"><div class="col-lg-12">
                            <h5>Previous Demand</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Unique Demand Id</th>
                                    <th>Financial Year</th>
                                    <th>Net Total</th>
                                    <th>Balance</th>
                                </tr>
                                <tr>
                                    <th>${oldDemand.unique_id}</th>
                                    <th>${oldDemand.current_fy}</th>
                                    <th>₹ ${customNumFormat(oldDemand.net_total)}</th>
                                    <th>₹ ${customNumFormat(oldDemand.balance_amount)}</th>
                                </tr>
                            </table>`;

                    var pendingSubheads = demandDetails.filter(
                      (row) => parseFloat(row.balance_amount) > 0
                    );
                    if (pendingSubheads && pendingSubheads.length > 0) {
                      demandDetailsHtml += ` <br>
                                <table class="table table-bordered mt-2">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Subhead Name</th>
                                        <th>Duration</th>
                                        <th>Amount</th>
                                        <th>Balance</th>
                                    </tr>`;
                      pendingSubheads.forEach((row, index) => {
                        demandDetailsHtml += `<tr>
                                                            <td>${index + 1}</td>
                                                            <td>${row.subhead_name}</td>
                                                            <td>${row.duration_from ?? ''} - ${row.duration_to ?? ''}</td>
                                                            <td>₹ ${customNumFormat(row.net_total)}</td>
                                                            <td>₹ ${customNumFormat(row.balance_amount)}</td>
                                                        </tr>`;
                      });
                      demandDetailsHtml += `</table> </div></div>`;
                    }
                  } else if (response.data.dues && response.data.dues > 0) {

                    // prevPendingAmount += response.data.dues; //not required after last update on 03Mar2025
                    demandDetailsHtml = `<div class="row mt-2"><div class="col-lg-12">
                            <h5>Previous Dues</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Property Id</th>
                                    <th>Demand Id</th>
                                    <th>Demand Date</th>
                                    <th>Demand amount</th>
                                    <th>Paid amount</th>
                                    <th>Outstanding</th>
                                    <th></th>
                                </tr>`;
                    response.data.previousDemands.forEach(demand => {
                      oldDemandId.push(demand.demand_id)
                      let demandDate = demand.demand_date.substring(0, demand.demand_date.indexOf("T")).split('-').reverse().join('-')
                      demandDetailsHtml += `<tr>
                                    <th>${demand.property_id}</th>
                                    <th>${demand.demand_id}</th>
                                    <th>${demandDate}</th>
                                    <th>₹ ${customNumFormat(demand.amount)}</th>
                                    <th>₹ ${customNumFormat(demand.paid_amount)}</th>
                                    <th>₹ ${customNumFormat(demand.outstanding)}</th>
                                </tr>`;
                    });

                    demandDetailsHtml += `</table></div></div>`;
                  }

                  $("#oldDemandDetails").html(demandDetailsHtml);
                  $("#confirmNewDemandModal").modal("show");

                } else {
                  $("#input-form-container").removeClass("d-none");
                }
              } else {
                showError(response.details);
              }
            }
          });
        }
      }

      //when confirm yes
      $("#confirmation-yes").click(function() {
        if (redirectToEdit) {
          let redirectMessage = `<h6>Redirecting to edit page</h6>`;
          $('#oldDemandDetails').after(redirectMessage);
          setTimeout(() => {
            window.location.href = "{{url('/demand/edit')}}" + '/' + editDemandId;
          }, 1000);
        } else if (oldDemandId.length > 0) {
          let ids = oldDemandId.join(',');
          $("#formOldDemandDetails").load("{{url('/demand/old-demand-data')}}" + '/' + ids /* , preloadScripts() */ );
          $("#confirmNewDemandModal").modal("hide");
          $("#input-form-container").removeClass("d-none");
        } else {
          $("#confirmNewDemandModal").modal("hide");
          $("#input-form-container").removeClass("d-none");
          $("#formOldDemandDetails").html(demandDetailsHtml);
        }
      });
      //when confirm No
      $("#confirmation-no").click(function() {
        $("#confirmNewDemandModal").modal("hide");
        $("#input-form-container").addClass("d-none");
        $("#formOldDemandDetails").html('');
      });

      /** add new subhead */

      $("#btn-add-more").click(function() {
        var subheadInput = $(".subhead-input").first().clone();
        subheadInput.find('.text-danger').remove();
        subheadInput.find("input, select, textarea").each(function() {
          $(this).val("");
          if ($(this).attr('name') == "amount[]") {
            $(this).siblings('label').append('<span class="text-danger">*</span>');
          }
        });
        $("#subhead-container").append(subheadInput);
        toggleRemoveButton();
      });

      /** remove subhead */
      $(document).on("click", ".btn-remove-subhead", function() {
        if ($(".subhead-input").length > 1) {
          var subheadRow = $(this).closest(".subhead-input");
          subheadRow.remove();
          calculateTotalAmount();
        }
        toggleRemoveButton()
      });

      /** Function to enable/disable remove buttons */
      function toggleRemoveButton() {
        if ($(".subhead-input").length === 1) {
          $(".btn-remove-subhead").prop("disabled", true);
        } else {
          $(".btn-remove-subhead").prop("disabled", false);
        }
      }

      /** calculate total demand amount */

      $("body").on("change", 'input[name="amount[]"]', function() {
        // Use event delegation to handle dynamic elements
        calculateTotalAmount();
      });

      function calculateTotalAmount() {
        var allAmountInputs = document.querySelectorAll('input[name="amount[]"]');
        var sum = prevPendingAmount;
        allAmountInputs.forEach((amountInput) => {
          var value = parseFloat(amountInput.value) || 0;
          sum += value;
        });
        $("#demandTotalAmount").text(customNumFormat(sum));
      }

      /* Submit the form */

      $("#btn-submit").click(function() {
        var formData = $("#demand-input-form").serialize();
        var isValid = true;
        var errorMessage = '';
        var today = new Date().toISOString().split("T")[0];
        $('#demand-input-form span.text-danger').not('label span.text-danger').remove();
        $("#demand-input-form").find("[required]").each(function() {
          if ($(this).val() === "" || $(this).val() === null) {
            isValid = false;
            var fieldName = $(this).attr('name');

            var validateLabel = fieldName.substring(0, fieldName.indexOf('[') != -1 ? fieldName.indexOf('[') : fieldName.length).split('_').map(word => word.charAt(0).toUpperCase() + word.substring(1)).join(' ');
            errorMessage = validateLabel + " is required.\n";
            $(this).parent().append('<span class="text-danger">' + errorMessage + '</span>');
          }
        });

        $("input[name^='duration_from']").each(function() {
          var fromField = $(this);
          var toField = fromField.closest('.subhead-input').find("input[name='" + fromField.attr("name").replace("from", "to") + "']");
          var durationFrom = fromField.val();
          var durationTo = toField.val();
          if (durationFrom) {
            if (durationFrom > today) {
              isValid = false;
              fromField.parent().append('<span class="text-danger">From date cannot be a future date.</span>');
            }
          }

          if (durationFrom && durationTo) {
            if (durationFrom > durationTo) {
              isValid = false;
              toField.parent().append('<span class="text-danger">To date should be greater than From date.</span>');
            }
          }
        });

        // If validation fails
        if (!isValid) {
          return false; // Prevent form submission
        }
        $.ajax({
          type: "post",
          url: "{{route('storeDemand')}}",
          data: formData,
          success: function(response) {
            if (response.status) {
              showSuccess(response.message, "{{route('demandList')}}");
            } else {
              showError(response.details);
            }
          },
        });
      });

      /** on changing select dropdown check duration or remarks is required for selected subhead */

      $(document).on('change', 'select[name="subhead[]"]', function() {
        let subheadContainer = $(this).closest('.subhead-input');
        let selectedOption = $(this).find('option:selected');
        if (selectedOption.attr('attrDuration')) {
          subheadContainer.find('input[name="duration_from[]"]').attr('required', true);
          subheadContainer.find('input[name="duration_from[]"]').siblings('label').html('From <span class="text-danger">*</span>');
          subheadContainer.find('input[name="duration_to[]"]').attr('required', true);
          subheadContainer.find('input[name="duration_to[]"]').siblings('label').html('To <span class="text-danger">*</span>');
        } else {
          subheadContainer.find('input[name="duration_from[]"]').removeAttr('required');
          subheadContainer.find('input[name="duration_from[]"]').siblings('label').html('From');
          subheadContainer.find('input[name="duration_to[]"]').removeAttr('required');
          subheadContainer.find('input[name="duration_to[]"]').siblings('label').html('To');
        }
        if (selectedOption.attr('attrRemarks')) {
          subheadContainer.find('textarea[name="remark[]"]').attr('required', true);

          // Update the label text to include the asterisk for required fields
          subheadContainer.find('textarea[name="remark[]"]').closest('.form-group').find('label').html('Remark <span class="text-danger">*</span>');
        } else {
          subheadContainer.find('textarea[name="remark[]"]').removeAttr('required');
          subheadContainer.find('textarea[name="remark[]"]').closest('.form-group').find('label').html('Remark');
        }
      })

      // function preloadScripts() {
      /** include previous demand subheds in new demand */
      $(document).on('change', '.check-include-in-demand', function() {
        // console.log($(this).data('demandId'), $(this).data('subheadKey'), $(this).data('subheadAmount'))
        let subheadAmount = $(this).data('subheadAmount')
        if ($(this).is(':checked')) {
          prevPendingAmount += subheadAmount;
        } else {
          prevPendingAmount -= subheadAmount;
        }
        calculateTotalAmount();
      })
      // }
    </script>
    @endsection