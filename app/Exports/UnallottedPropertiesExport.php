<?php

namespace App\Exports;

use App\Models\UnallottedPropertyDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnallottedPropertiesExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithEvents
{
    protected $localityRecord;

    private int $serialNumber = 0;

    public function __construct($localityRecord = null)
    {
        $this->localityRecord = $localityRecord;
    }

    public function query()
    {
        $query = UnallottedPropertyDetail::query()
            ->leftJoin(
                'property_masters',
                'unallotted_property_details.property_master_id',
                '=',
                'property_masters.id'
            )
            ->leftJoin(
                'items',
                'property_masters.land_type',
                '=',
                'items.id'
            )
            ->leftJoin(
                'old_colonies',
                'property_masters.new_colony_name',
                '=',
                'old_colonies.id'
            )
            ->leftJoin(
                'departments',
                'unallotted_property_details.transferred_to',
                '=',
                'departments.id'
            )
            ->leftJoin(
                'survey_details',
                'survey_details.property_id',
                '=',
                'unallotted_property_details.old_property_id'
            )
            ->select(
                'unallotted_property_details.old_property_id',
                'unallotted_property_details.plot_area_in_sqm',
                'unallotted_property_details.is_litigation',
                'unallotted_property_details.is_encrached',
                'unallotted_property_details.is_vaccant',
                'unallotted_property_details.is_transferred',
                'unallotted_property_details.is_property_document_exist',
                'unallotted_property_details.date_of_transfer',
                'unallotted_property_details.purpose',

                'property_masters.unique_propert_id',
                'property_masters.plot_or_property_no',
                'property_masters.block_no',

                'items.item_name as landType',
                'old_colonies.name as colonyName',
                'departments.name as departmentName',

                'survey_details.latitude',
                'survey_details.longitude'
            );

        if (!empty($this->localityRecord)) {
            $query->where(
                'property_masters.new_colony_name',
                $this->localityRecord
            );
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'S.No.',
            'Property ID',
            'Old Property ID',
            'Plot/Property No.',
            'Block No.',
            'Land Type',
            'Colony Name',
            'Property Documents Exist?',
            'Area (Sqm.)',
            'Vacant?',
            'Transferred?',
            'Department',
            'Transfer Date',
            'Purpose',
            'Encroachment?',
            'Litigation?',
            'Latitude',
            'Longitude',
            'Google Map',
        ];
    }

    public function map($property): array
    {
        $this->serialNumber++;

        $mapUrl = '';

        if (!empty($property->latitude) && !empty($property->longitude)) {
            $mapUrl = 'https://www.google.com/maps/search/?api=1&query='
                . $property->latitude
                . ','
                . $property->longitude;
        }

        return [
            $this->serialNumber,
            $property->unique_propert_id,
            $property->old_property_id,
            $property->plot_or_property_no,
            $property->block_no,
            $property->landType,
            $property->colonyName,
            $property->is_property_document_exist ? 'Yes' : 'No',
            $property->plot_area_in_sqm,
            $property->is_vaccant ? 'Yes' : 'No',
            $property->is_transferred ? 'Yes' : 'No',
            $property->departmentName ?? '',
            !empty($property->date_of_transfer)
                ? \Carbon\Carbon::parse($property->date_of_transfer)->format('d/m/Y')
                : '',
            $property->purpose ?? '',
            $property->is_encrached ? 'Yes' : 'No',
            $property->is_litigation ? 'Yes' : 'No',
            $property->latitude ?? '',
            $property->longitude ?? '',
            $mapUrl ? 'View on Google Maps' : 'Location Not Available',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                 * Google Map is column S
                 * Row 1 = heading
                 * Data starts from row 2
                 */

                $highestRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {

                    $latitude = $sheet->getCell('Q' . $row)->getValue();
                    $longitude = $sheet->getCell('R' . $row)->getValue();

                    if (!empty($latitude) && !empty($longitude)) {

                        $url = 'https://www.google.com/maps/search/?api=1&query='
                            . $latitude
                            . ','
                            . $longitude;

                        $sheet->getCell('S' . $row)
                            ->getHyperlink()
                            ->setUrl($url);

                        $sheet->getStyle('S' . $row)
                            ->getFont()
                            ->setUnderline(true);
                    }
                }

                // Bold heading row
                $sheet->getStyle('A1:S1')
                    ->getFont()
                    ->setBold(true);

                // Auto-size columns
                foreach (range('A', 'S') as $column) {
                    $sheet->getColumnDimension($column)
                        ->setAutoSize(true);
                }
            },
        ];
    }
}