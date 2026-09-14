<?php

namespace App\Exports;

use App\Models\PropertyScannedRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ScanningRequestsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $user;
    protected $request;

    public function __construct($user, $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    /**
     * Fetch ALL rows for Excel
     */
    public function collection(): Collection
    {
        $userRole = $this->user->getRoleNames()->first();
        $sendToScanItemId = DB::table('items')
            ->where('item_code', 'SEND_TO_SCAN')
            ->value('id');

        $query = PropertyScannedRequest::query()
            ->join('property_masters', 'property_masters.id', '=', 'property_scanned_requests.property_master_id')
            ->leftJoin('items as status_items', 'status_items.id', '=', 'property_scanned_requests.status')
            ->leftJoin('applications', 'applications.id', '=', 'property_scanned_requests.application_id')
            ->leftJoin('items as reason_items', 'reason_items.id', '=', 'applications.service_type')
            ->leftJoin('record_room_files', 'record_room_files.id', '=', 'property_scanned_requests.record_id')
            ->leftJoin('old_colonies', 'old_colonies.id', '=', 'property_scanned_requests.colony_id')
            ->select([
                DB::raw("DATE_FORMAT(property_scanned_requests.created_at, '%d-%m-%Y') as request_date"),
                'property_scanned_requests.old_property_id',
                'old_colonies.name as colony_name',
                'property_masters.file_no',
                'record_room_files.file_location as record_file_location',
                'reason_items.item_name as reason',
                'status_items.item_name as request_status',
                'property_masters.section_code as section',
            ]);

        // 🔒 Role filter (same as listing)
        if ($userRole === 'scan-admin' && $sendToScanItemId) {
            $query->where('property_scanned_requests.status', $sendToScanItemId);
        }

        // 🔍 Global search (same behavior as DataTable)
        $search = $this->request->input('search.value')
               ?? $this->request->input('search');

        if (is_array($search)) {
            $search = $search['value'] ?? '';
        }

        $search = trim((string) $search);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('property_scanned_requests.old_property_id', 'like', "%{$search}%")
                  ->orWhere('property_masters.file_no', 'like', "%{$search}%")
                  ->orWhere('old_colonies.name', 'like', "%{$search}%")
                  ->orWhere('record_room_files.file_location', 'like', "%{$search}%")
                  ->orWhere('reason_items.item_name', 'like', "%{$search}%")
                  ->orWhere('status_items.item_name', 'like', "%{$search}%")
                  ->orWhere('property_masters.section_code', 'like', "%{$search}%");
            });
        }

        // Default ordering: Request Date DESC
        return $query
            ->orderBy('property_scanned_requests.created_at', 'desc')
            ->get();
    }

    /**
     * Excel column headers
     */
    public function headings(): array
    {
        return [
            'Request Date',
            'Property ID',
            'Colony',
            'File No',
            'Record File Location',
            'Reason',
            'Request Status',
            'Section',
        ];
    }
}
