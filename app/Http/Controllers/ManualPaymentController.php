<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Country;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Models\OldColony;
use App\Models\Payment;
use App\Models\PaymentDetail;
use App\Models\PropertyLeaseDetail;
use App\Models\PropertyMaster;
use App\Models\SplitedPropertyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ManualPaymentController extends Controller
{
    public function manualPaymentInputForm()
    {
        $data['paymentTypeDemand'] = getItemsOnlyForDemand(17011);
        $data['paymentTypeProperty'] = getItemsOnlyForProperty(17011);
        return view('payment.manual-input-form', $data);
    }

    public function getManualPaymentDetails(Request $request)
    {
        try {
            $request->validate([
                'payment_type' => 'required|in:propertyId,demandId',
                'search_value' => 'required|string'
            ]);

            $paymentType = $request->payment_type;
            $searchValue = $request->search_value;

            if ($paymentType === 'propertyId') {
                return $this->searchByPropertyId($searchValue);
            } else {
                return $this->searchByDemandId($searchValue);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function searchByPropertyId($propertyId)
    {
        // First, try to find the property in property_masters
        $propertyMaster = PropertyMaster::where('old_propert_id', $propertyId)
            ->where('is_active', 1)
            ->first();

        if ($propertyMaster) {
            // Check if it's a joint property
            if ($propertyMaster->is_joint_property == '1' || $propertyMaster->is_joint_property === true) {
                // Search in splited_property_details
                return $this->searchInSplitedProperty($propertyId, $propertyMaster);
            } else {
                // Search in property_masters
                return $this->getPropertyMasterDetails($propertyMaster);
            }
        }

        // If not found in property_masters, try splited_property_details
        $splitedProperty = SplitedPropertyDetail::where('old_property_id', $propertyId)
            ->where('is_active', 1)
            ->first();

        if ($splitedProperty) {
            return $this->getSplitedPropertyDetails($splitedProperty);
        }

        return response()->json([
            'success' => false,
            'message' => 'Property not found with ID: ' . $propertyId
        ], 404);
    }

    private function searchByDemandId($demandId)
    {
        // Search in demands table
        $demand = Demand::where('unique_id', $demandId)
            ->whereIn('status', [
                getServiceType('DEM_PENDING'),
                getServiceType('DEM_PART_PAID'),
                getServiceType('DEM_PAID')
            ])
            ->first();

        if (!$demand) {
            return response()->json([
                'success' => false,
                'message' => 'Data not available for given demand id.'
            ], 404);
        }

        // Get countries and states for the form
        $countries = Country::all();
        $states = DB::table('states')->where('country_id', 101)->get();

        $propertyData = [];
        if (!empty($demand->old_property_id)) {
            $response = $this->searchByPropertyId($demand->old_property_id);

            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $content = json_decode($response->getContent(), true);
                if ($content && $content['success'] === true) {
                    $propertyData = $content['data'];
                    if (!empty($propertyData['locality'])) {
                        if (is_numeric($propertyData['locality'])) {
                            $propertyData['locality'] = OldColony::where('id', $propertyData['locality'])->value('name') ?? $propertyData['locality'];
                        }
                    }
                }
            }
        }

        // Convert demand to array
        $demandArray = $demand->toArray();

        // Also load demand details relationship if needed
        if ($demand->relationLoaded('demandDetails') || $demand->demandDetails) {
            $demandArray['demand_details'] = $demand->demandDetails->toArray();
        }

        // Generate HTML for demand details
        $html = view('include.parts.manual-demand-details', [
            'demand' => $demand,
            'property' => $propertyData,
            'countries' => $countries,
            'states' => $states,
            'demandDetails' => $demandArray // Now this is an array
        ])->render();

        return response()->json([
            'success' => true,
            'type' => 'demand',
            'html' => $html,
            'demand_id' => $demand->id,
            'data' => $propertyData
        ]);
    }

    private function searchInSplitedProperty($propertyId, $propertyMaster)
    {
        $splitedProperty = SplitedPropertyDetail::where('old_property_id', $propertyId)
            ->where('property_master_id', $propertyMaster->id)
            ->where('is_active', 1)
            ->first();

        if ($splitedProperty) {
            return $this->getSplitedPropertyDetails($splitedProperty, $propertyMaster);
        }

        // If not found by old_property_id, try by property_master_id
        $splitedProperty = SplitedPropertyDetail::where('property_master_id', $propertyMaster->id)
            ->where('is_active', 1)
            ->first();

        if ($splitedProperty) {
            return $this->getSplitedPropertyDetails($splitedProperty, $propertyMaster);
        }

        return response()->json([
            'success' => false,
            'message' => 'Joint property details not found'
        ], 404);
    }

    private function searchInSplitedPropertyByMaster($propertyMaster)
    {
        $splitedProperty = SplitedPropertyDetail::where('property_master_id', $propertyMaster->id)
            ->where('is_active', 1)
            ->first();

        if ($splitedProperty) {
            return $this->getSplitedPropertyDetails($splitedProperty, $propertyMaster);
        }

        return response()->json([
            'success' => false,
            'message' => 'Joint property details not found'
        ], 404);
    }

    private function getPropertyMasterDetails($propertyMaster)
    {
        // Get locality from old_colonies
        $locality = null;
        if ($propertyMaster->new_colony_name) {
            $colony = OldColony::where('name', $propertyMaster->new_colony_name)->first();
            $locality = $colony ? $colony->name : $propertyMaster->new_colony_name;
        }

        // Get known as from property_lease_details if available
        $knownAs = null;
        $leaseDetail = PropertyLeaseDetail::where('property_master_id', $propertyMaster->id)
            ->where('is_active', 1)
            ->first();

        if ($leaseDetail) {
            $knownAs = $leaseDetail->presently_known_as;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'old_property_id' => $propertyMaster->old_propert_id,
                'locality' => $locality,
                'block' => $propertyMaster->block_no,
                'plot' => $propertyMaster->plot_or_property_no,
                'known_as' => $knownAs,
                'unique_property_id' => $propertyMaster->unique_propert_id,
                'is_joint_property' => $propertyMaster->is_joint_property
            ]
        ]);
    }

    private function getSplitedPropertyDetails($splitedProperty, $propertyMaster = null)
    {
        if (!$propertyMaster) {
            $propertyMaster = PropertyMaster::find($splitedProperty->property_master_id);
        }

        if (!$propertyMaster) {
            return response()->json([
                'success' => false,
                'message' => 'Property master not found'
            ], 404);
        }

        // Get locality from old_colonies
        $locality = null;
        if ($propertyMaster->new_colony_name) {
            $colony = OldColony::where('name', $propertyMaster->new_colony_name)->first();
            $locality = $colony ? $colony->name : $propertyMaster->new_colony_name;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'old_property_id' => $splitedProperty->old_property_id,
                'locality' => $locality,
                'block' => $propertyMaster->block_no,
                'plot' => $splitedProperty->plot_flat_no,
                'known_as' => $splitedProperty->presently_known_as,
                'unique_property_id' => $splitedProperty->child_prop_id,
                'is_joint_property' => $propertyMaster->is_joint_property
            ]
        ]);
    }

    public function submitManaulPaymentDetails(Request $request)
    {
        try {
            // Determine payment type if not set
            if (!$request->has('paymentType')) {
                if ($request->has('demand_id')) {
                    $request->merge(['paymentType' => 'demandId']);
                } else {
                    $request->merge(['paymentType' => 'propertyId']);
                }
            }

            // Validate based on payment type
            if ($request->paymentType === 'demandId') {
                $validator = Validator::make($request->all(), [
                    'demand_id' => 'required|exists:demands,id',
                    'subhead_id' => 'required|array',
                    'paid_amount' => 'required|array',
                    'old_property_id' => 'nullable|string',
                    'is_joint_property' => 'nullable|in:0,1'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return $this->processDemandPayment($request);
            } else {
                // Property payment validation
                $validator = Validator::make($request->all(), [
                    'old_property_id' => 'required|string',
                    'payment_purpose' => 'required|string',
                    'transaction_date' => 'required|date',
                    'financial_year' => 'required|string',
                    'transaction_number' => 'required|string',
                    'amount' => 'required|numeric|min:0.01',
                    'is_joint_property' => 'nullable|in:0,1'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return $this->processPropertyPayment($request);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit payment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processDemandPayment($request)
    {
        $modelName = $modelId = $applicationNo = '';

        // Get demand details
        if (!empty($request->demand_id)) {
            $demandId = $request->demand_id;
            $demand = Demand::with('demandDetails')->find($demandId);
            if (empty($demand)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Given demand not found'
                ], 404);
            }

            // If demand payment done for application
            if (!empty($demand->app_no)) {
                $getApplicationDetails = Application::where('application_no', $demand->app_no)->first();
                if ($getApplicationDetails) {
                    $modelName = $getApplicationDetails->model_name ?? '';
                    $modelId = $getApplicationDetails->model_id ?? '';
                    $applicationNo = $demand->app_no;
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Demand not given'
            ], 400);
        }

        // Get property details
        $propertyMasterId = $demand->property_master_id;
        if (is_null($demand->splited_property_detail_id)) {
            $master_old_property_id = $demand->old_property_id;
            $splited_old_property_id = null;
            $splited_property_detail_id = null;
        } else {
            $masterProperty = PropertyMaster::find($propertyMasterId);
            $master_old_property_id = $masterProperty->old_propert_id ?? $demand->old_property_id;
            $splited_old_property_id = $demand->old_property_id;
            $splited_property_detail_id = $demand->splited_property_detail_id;
        }

        // Calculate paid amount - sum of all individual paid amounts
        $paidAmount = 0;
        if (isset($request->paid_amount) && is_array($request->paid_amount)) {
            foreach ($request->paid_amount as $amount) {
                // Convert to float and add to total
                $paidAmount += floatval($amount);
            }
        }

        // Round to 2 decimal places to avoid floating point issues
        $paidAmount = round($paidAmount, 2);

        // Validate that amount is greater than 0
        if ($paidAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount must be greater than 0'
            ], 400);
        }

        // Generate unique payment ID
        $uniquePaymentId = 'MAN' . date('YmdHis') . rand(100, 999);

        // Create payment record with exact amount
        $payment = Payment::create([
            'property_master_id' => $propertyMasterId,
            'type' => getServiceType('PAY_DEMAND'),
            'application_no' => $applicationNo ?? '',
            'model' => $modelName ?? '',
            'model_id' => $modelId ?? '',
            'demand_id' => $demandId,
            'payment_mode' => getServiceType('PAY_MANUAL'),
            'unique_payment_id' => $uniquePaymentId,
            'splited_property_detail_id' => $splited_property_detail_id ?? null,
            'master_old_property_id' => $master_old_property_id,
            'splited_old_property_id' => $splited_old_property_id,
            'amount' => $paidAmount, // Exact total amount from all inputs
            'status' => getServiceType('PAY_SUCCESS'),
            'transaction_id' => $request->transaction_number,
            'transaction_date' => $request->transaction_date,
            'created_by' => auth()->id() ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($payment) {
            // Save payment subheads and update demand details
            foreach ($request->subhead_id as $i => $subhead) {
                if (isset($request->paid_amount[$i]) && $request->paid_amount[$i] != "" && $request->paid_amount[$i] > 0) {
                    // Get the exact amount for this subhead
                    $paidAmountForSubhead = floatval($request->paid_amount[$i]);

                    // Create payment detail
                    PaymentDetail::create([
                        'payment_id' => $payment->id,
                        'demand_id' => $demandId,
                        'subhead_id' => $subhead,
                        'paid_amount' => $paidAmountForSubhead,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Update demand_details table for this subhead
                    $demandDetail = $demand->demandDetails()->where('id', $subhead)->first();
                    if ($demandDetail) {
                        $currentPaid = floatval($demandDetail->paid_amount ?? 0);
                        $newPaid = $currentPaid + $paidAmountForSubhead;
                        $newBalance = floatval($demandDetail->balance_amount) - $paidAmountForSubhead;

                        $demandDetail->update([
                            'paid_amount' => round($newPaid, 2),
                            'balance_amount' => round(max(0, $newBalance), 2)
                        ]);
                    }
                }
            }

            // Update demand status
            $this->updateDemandStatus($demandId);

            return response()->json([
                'success' => true,
                'message' => 'Demand payment submitted successfully',
                'data' => $payment,
                'payment_id' => $payment->id,
                'unique_payment_id' => $uniquePaymentId
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to create payment'
        ], 500);
    }

    private function updateDemandStatus($demandId)
    {
        $demand = Demand::with('demandDetails')->find($demandId);
        if (!$demand) {
            return;
        }

        // Recalculate totals from demand details
        $totalBalance = 0;
        $totalPaid = 0;

        foreach ($demand->demandDetails as $detail) {
            $totalBalance += floatval($detail->balance_amount);
            $totalPaid += floatval($detail->paid_amount ?? 0);
        }

        // Round to 2 decimal places
        $totalBalance = round($totalBalance, 2);
        $totalPaid = round($totalPaid, 2);

        // Update demand totals
        $demand->balance_amount = $totalBalance;
        $demand->paid_amount = $totalPaid;

        if ($totalBalance <= 0) {
            // All paid - update demand status to PAID
            $demand->status = getServiceType('DEM_PAID');
        } else if ($totalPaid > 0) {
            // Partially paid
            $demand->status = getServiceType('DEM_PART_PAID');
        } else {
            // No payment made
            $demand->status = getServiceType('DEM_PENDING');
        }

        $demand->save();
    }

    public function processPropertyPayment($request)
    {
        try {
            // Initialize variables
            $payment = null;
            $propertyMasterId = null;
            $splitedPropertyDetailId = null;
            $masterOldPropertyId = null;
            $splitedOldPropertyId = null;

            // Get the exact amount from request
            $amount = floatval($request->amount);

            // Round to 2 decimal places
            $amount = round($amount, 2);

            // Validate amount
            if ($amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount must be greater than 0'
                ], 400);
            }

            // Process only if payment type is propertyId
            if ($request->paymentType === 'propertyId') {
                $isJointProperty = $request->is_joint_property ?? 0;
                $oldPropertyId = $request->old_property_id;

                if ($isJointProperty == 1) {
                    // Handle joint property (splitted)
                    $splitPropertyDetails = SplitedPropertyDetail::where('old_property_id', $oldPropertyId)->first();

                    if (!$splitPropertyDetails) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Splitted property details not found'
                        ], 404);
                    }

                    $propertyMaster = PropertyMaster::find($splitPropertyDetails->property_master_id);

                    if (!$propertyMaster) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Property master not found'
                        ], 404);
                    }

                    // Set values for splitted property
                    $propertyMasterId = $splitPropertyDetails->property_master_id;
                    $splitedPropertyDetailId = $splitPropertyDetails->id;
                    $masterOldPropertyId = $propertyMaster->old_propert_id;
                    $splitedOldPropertyId = $splitPropertyDetails->old_property_id;
                } else {
                    // Handle non-joint property
                    $propertyMaster = PropertyMaster::where('old_propert_id', $oldPropertyId)->first();

                    if (!$propertyMaster) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Property not found'
                        ], 404);
                    }

                    // Set values for non-splitted property
                    $propertyMasterId = $propertyMaster->id;
                    $splitedPropertyDetailId = null;
                    $masterOldPropertyId = $propertyMaster->old_propert_id;
                    $splitedOldPropertyId = null;
                }

                $uniquePaymentId = 'MAN' . date('YmdHis') . rand(100, 999);

                // Create payment record with exact amount
                $payment = Payment::create([
                    'type' => getServiceType($request->payment_purpose),
                    'property_master_id' => $propertyMasterId,
                    'splited_property_detail_id' => $splitedPropertyDetailId,
                    'master_old_property_id' => $masterOldPropertyId,
                    'splited_old_property_id' => $splitedOldPropertyId,
                    'amount' => $amount, // Exact amount from form input
                    'payment_mode' => getServiceType('PAY_MANUAL'),
                    'unique_payment_id' => $uniquePaymentId,
                    'transaction_id' => $request->transaction_number,
                    'transaction_date' => $request->transaction_date,
                    'status' => getServiceType('PAY_SUCCESS'),
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment submitted successfully',
                'data' => $payment,
                'amount' => $amount // Return the exact amount for verification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
