<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReportBreach extends Model
{
    protected $fillable = [

        'inspection_report_id',

        'inspected_type',

        'breach_type',
        'breach_on',
        'breach_floor',
        'breach_use',

        'location_details',
        'location_details_brief', 
        'length',
        'lunit',

        'breadth',
        'bunit',

        'area',
        'aunit',

        'objectionable',
        'reason'
    ];

    public function inspectionReport()
    {
        return $this->belongsTo(InspectionReport::class);
    }
}
