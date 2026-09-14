<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionRequest extends Model
{
    use HasFactory;
    protected $table = 'inspection_requests';
    protected $fillable = [
        'inspection_id',
        'property_id',
        'application_no',
        'application_type',
        'inspection_reason',
        'cname',
        'pname',       
        'cremarks',
        'communication_address',
        'communication_address_new',
        'user_id',
        'status',
        'reopen',
        'schedule_by',
        'schedule',
        'schedule_date',
        'schedule_fromtime',
        'refused_by',
        'reason',
        'reason_re_can',
        'schedule_totime',
        'created_by',
        'bnotice',
        'bremarks',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'schedule_time' => 'datetime:H:i',
    ];
    
			   public function propertyMaster()
			{
			    return $this->belongsTo(PropertyMaster::class, 'property_id', 'old_propert_id'
			    );
			}
			public function splittedProperty()
			{
			    return $this->belongsTo( SplitedPropertyDetail::class, 'property_id', 'old_property_id'
			    );
			}

			public function getPropertyAttribute()
			{
			    return $this->propertyMaster ?? $this->splittedProperty?->propertyMaster;
			}			
//    public function property()
//		{
//		    return $this->belongsTo(
//		        PropertyMaster::class,
//		        'property_id',
//		        'old_propert_id'
//		    );
//		}
		public function inspectionReport()
			{
			    return $this->hasOne(InspectionReport::class, 'inspection_id', 'id');
			}
}