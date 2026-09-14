<?php

namespace App\Http\Controllers;

use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RevenueController extends Controller
{
    public function index()
    {
        return view('revenues.index');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'financial_year' => 'required|string',
                'month' => 'required|string|in:April,May,June,July,August,September,October,November,December,January,February,March',
                'revenue_amount_without_ntrp' => 'required|numeric|min:0',
                'total_revenue_amount_pfms' => 'required|numeric|min:0',
            ]);

            // Check if record already exists
            $exists = Revenue::where('financial_year', $request->financial_year)
                ->where('month', $request->month)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record already exists for this Financial Year and Month!'
                ], 422);
            }

            $revenue = Revenue::create([
                'financial_year' => $request->financial_year,
                'month' => $request->month,
                'revenue_amount_without_ntrp' => $request->revenue_amount_without_ntrp,
                'total_revenue_amount_pfms' => $request->total_revenue_amount_pfms,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Revenue record created successfully!',
                'data' => $revenue
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getData(Request $request)
    {
        // Get all revenue records for the table with actions
        $allRevenues = Revenue::select(
            'id',
            'financial_year',
            'month',
            'revenue_amount_without_ntrp',
            'total_revenue_amount_pfms'
        )
            ->orderBy('financial_year', 'desc')
            ->orderBy(DB::raw("FIELD(month, 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March')"))
            ->get();

        // Get summary data for the table
        $summaryData = Revenue::select(
            'financial_year',
            DB::raw('COUNT(month) as months_count'),
            DB::raw('SUM(revenue_amount_without_ntrp) as total_revenue_without_ntrp'),
            DB::raw('SUM(total_revenue_amount_pfms) as total_revenue_pfms')
        )
            ->groupBy('financial_year')
            ->orderBy('financial_year', 'desc')
            ->get();

        // Get monthly details for each financial year
        $monthlyDetails = Revenue::select('financial_year', 'month', 'revenue_amount_without_ntrp', 'total_revenue_amount_pfms')
            ->orderBy('financial_year', 'desc')
            ->orderBy(DB::raw("FIELD(month, 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March')"))
            ->get()
            ->groupBy('financial_year');

        return response()->json([
            'all_revenues' => $allRevenues,
            'summary' => $summaryData,
            'details' => $monthlyDetails
        ]);
    }

    public function edit($id)
    {
        try {
            $revenue = Revenue::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $revenue
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Revenue record not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'revenue_amount_without_ntrp' => 'required|numeric|min:0',
                'total_revenue_amount_pfms' => 'required|numeric|min:0',
            ]);

            $revenue = Revenue::findOrFail($id);

            $revenue->update([
                'revenue_amount_without_ntrp' => $request->revenue_amount_without_ntrp,
                'total_revenue_amount_pfms' => $request->total_revenue_amount_pfms,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Revenue record updated successfully!',
                'data' => $revenue
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $revenue = Revenue::findOrFail($id);
            $revenue->delete();

            return response()->json([
                'success' => true,
                'message' => 'Revenue record deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting revenue record: ' . $e->getMessage()
            ], 500);
        }
    }
}
