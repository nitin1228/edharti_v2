<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReportUnauthorisedDetail extends Model
{
    protected $table = 'inspection_report_unauthorised_details';

    protected $fillable = [

        'inspection_report_id',

        'inspected_type',

        'floor_breach',
        'floor_breach_use',

        'location_details',
        'location_details_brief', 
        'length',
        'lunit',

        'breadth',
        'bunit',

        'area',
        'aunit',

        'wpl',
        'bpl',

        'objectionable',
        'reason'
    ];

    public function inspectionReport()
    {
        return $this->belongsTo(InspectionReport::class);
    }
}
