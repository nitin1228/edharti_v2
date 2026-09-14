<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['subhead_name', 'subhead_keys','property_known_as'];
    public function demand(): BelongsTo
    {
        return $this->belongsTo(Demand::class, 'demand_id', 'id');
    }
    public function getPropertyKnownAsAttribute()
    {
        if (is_null($this->splited_property_detail_id)) {
            $propertyMaster = PropertyMaster::find($this->property_master_id);
            if ($propertyMaster) {
                return $propertyMaster->propertyLeaseDetail->presently_known_as ?? null;
            } else {
                return null;
            }
        } else {
            $spd = SplitedPropertyDetail::find($this->splited_property_detail_id);
            if ($spd) {
                return $spd->presently_known_as;
            } else {
                return null;
            }
        }
    } 
    public function getSubheadNameAttribute()
    {
        return getServiceNameById($this->subhead_id);
    }
    public function getSubheadCodeAttribute()
    {
        return getServiceCodeById($this->subhead_id);
    }
    public function getSubheadKeysAttribute()
    {
        $headKeys = DemandHeadKey::where('head_id', $this->id)->pluck('value', 'key')->toArray();
        return $headKeys;
    }
}
