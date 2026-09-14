<?php

namespace App\Exports;

use App\Models\RecordRoomFile;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class RecordRoomFilesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $user;
    protected $request;

    public function __construct($user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

public function headings(): array
{
    return [
        'S.No.',
        'Property ID',
        'Colony Name',
        'Block',
        'Plot',
        'File Location',
        'Section',
        'Current Section',
    ];
}

    public function collection(): Collection
    {
        $request = $this->request;

        $query = RecordRoomFile::query()
            ->leftJoin('old_colonies', 'old_colonies.code', '=', 'record_room_files.colony_code')
            ->select([
                'record_room_files.old_property_id',
                'old_colonies.name as colony_name',
                'record_room_files.block',
                'record_room_files.plot',
                'record_room_files.file_location',
                'record_room_files.section_code',
                'record_room_files.transaction_section_code',
            ]);

        // ✅ section restriction (same as your listing)
        $sections = getLoggedInUserSections();
        $sectionCodes = Section::whereIn('id', $sections)->pluck('section_code')->toArray();

        if (!empty($sectionCodes) && $sectionCodes[0] !== 'REC') {
            $query->whereIn('record_room_files.section_code', $sectionCodes);
        }

        // ✅ dropdown filters (same as listing)
        if ($request->filled('locality_record')) {
            $query->where('record_room_files.colony_id', $request->locality_record);
        }
        if ($request->filled('block_record')) {
            $query->where('record_room_files.block', $request->block_record);
        }
        if ($request->filled('plot_record')) {
            $query->where('record_room_files.plot', $request->plot_record);
        }
        if ($request->filled('section_code')) {
            $query->where('record_room_files.section_code', $request->section_code);
        }

        // ✅ global search (DataTables style: search[value])
        $search = $request->input('search.value');
        if ($search === null) $search = $request->input('search');
        if (is_array($search)) $search = $search['value'] ?? '';
        $search = trim((string) $search);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('record_room_files.old_property_id', 'like', "%{$search}%")
                  ->orWhere('old_colonies.name', 'like', "%{$search}%")
                  ->orWhere('record_room_files.block', 'like', "%{$search}%")
                  ->orWhere('record_room_files.plot', 'like', "%{$search}%")
                  ->orWhere('record_room_files.file_location', 'like', "%{$search}%")
                  ->orWhere('record_room_files.section_code', 'like', "%{$search}%")
                  ->orWhere('record_room_files.transaction_section_code', 'like', "%{$search}%");
            });
        }

        // ✅ ordering (match your record-list DataTable columns indexes)
        $orderColumnIndex = (int) $request->input('order.0.column', 1);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Your DataTable column order:
        // 0 DT_RowIndex, 1 record_id, 2 colony_name, 3 block, 4 plot, 5 file_location, 6 section_code, 7 transaction_section_code
        $orderMap = [
            0 => 'record_room_files.id',                     // S.No -> sort by ID
            1 => 'record_room_files.old_property_id',
            2 => 'old_colonies.name',
            3 => 'record_room_files.block',
            4 => 'record_room_files.plot',
            5 => 'record_room_files.file_location',
            6 => 'record_room_files.section_code',
            7 => 'record_room_files.transaction_section_code',
        ];

        $orderBy = $orderMap[$orderColumnIndex] ?? 'record_room_files.id';
        $query->orderBy($orderBy, $orderDir);

        // ✅ return as rows for Excel
        $rows = $query->get();

        $counter = 1;

        return $rows->map(function ($r) use (&$counter) {
            return [
                $counter++,                     // ✅ S.No.
                $r->old_property_id ?? '-',
                $r->colony_name ?? '-',
                $r->block ?? '-',
                $r->plot ?? '-',
                $r->file_location ?? '-',
                $r->section_code ?? '-',
                $r->transaction_section_code ?? '-',
            ];
        });
    }
}
