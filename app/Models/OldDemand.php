<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OldDemand extends Model
{
    use HasFactory;
    protected $guarded = [];
     protected $appends = ['status_code', 'property_known_as', 'current_lessee', 'property_master', 'splited_property_detail'];
    public function demandDetails(): HasMany
    {
        return $this->hasMany(DemandDetail::class);
    }

    public function oldDemandSubheads(): HasMany
    {
        return $this->hasMany(OldDemandSubhead::class, 'DemandID', 'demand_id');
    }

    public function getStatusCodeAttribute()
    {
        return getServiceCodeById($this->status);
    }

	   public function getPropertyKnownAsAttribute()
	{
	    if (!empty($this->property_master_id)) {

	        if (is_null($this->splited_property_detail_id)) {
	            $propertyMaster = PropertyMaster::find($this->property_master_id);

	            return $propertyMaster?->propertyLeaseDetail?->presently_known_as;
	        }

	        $spd = SplitedPropertyDetail::find($this->splited_property_detail_id);

	        return $spd?->presently_known_as;
	    }

	    // Old Demand Mapping
	    $propertyMaster = PropertyMaster::where('old_propert_id', $this->property_id)->first();

	    if ($propertyMaster) {
	        return $propertyMaster->propertyLeaseDetail->presently_known_as ?? null;
	    }

	    $spd = SplitedPropertyDetail::where('old_property_id', $this->property_id)->first();

	    return $spd?->presently_known_as;
	}

		public function getCurrentLesseeAttribute()
		{
		    if (!empty($this->property_master_id)) {

		        if (is_null($this->splited_property_detail_id)) {
		            $cld = CurrentLesseeDetail::where('property_master_id', $this->property_master_id)
		                ->whereNull('splited_property_detail_id')
		                ->first();
		        } else {
		            $cld = CurrentLesseeDetail::where('property_master_id', $this->property_master_id)
		                ->where('splited_property_detail_id', $this->splited_property_detail_id)
		                ->first();
		        }

		        return $cld->lessees_name ?? null;
		    }

		    // Old Demand
		    $propertyMaster = PropertyMaster::where('old_propert_id', $this->property_id)->first();

		    if (!$propertyMaster) {
		        return null;
		    }

		    $spd = SplitedPropertyDetail::where('old_property_id', $this->property_id)->first();

		    $query = CurrentLesseeDetail::where('property_master_id', $propertyMaster->id);

		    if ($spd) {
		        $query->where('splited_property_detail_id', $spd->id);
		    } else {
		        $query->whereNull('splited_property_detail_id');
		    }

		    return optional($query->first())->lessees_name;
		}
		public function getPropertyMasterAttribute()
		{
		    if (!empty($this->property_master_id)) {
		        return PropertyMaster::find($this->property_master_id);
		    }

		    return PropertyMaster::where('old_propert_id', $this->property_id)->first();
		}
		public function getSplitedPropertyDetailAttribute()
		{
		    if (!empty($this->splited_property_detail_id)) {
		        return SplitedPropertyDetail::find($this->splited_property_detail_id);
		    }

		    return SplitedPropertyDetail::where('old_property_id', $this->property_id)->first();
		}

}