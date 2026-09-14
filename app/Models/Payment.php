<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = [ 'property_known_as'];
    //Relations added by Swati Mishra for integrating payment payment details in reciept on 14-07-2025
    public function PaymentDetails(): HasMany
    {
        return $this->hasMany(PaymentDetail::class);
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

    public function paymentModeItem()
    {
        return $this->belongsTo(Item::class, 'payment_mode');
    }
    public function paymentTypeItem()
    {
        return $this->belongsTo(Item::class, 'type');
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class, 'demand_id');
    }

    public function payerDetails()
    {
        return $this->hasOne(PayerDetail::class, 'payment_id');
    }
    public function property()
    {
        return $this->belongsTo(PropertyMaster::class, 'property_master_id');
    }
public function application()
    {
        return $this->hasOne(Application::class, 'application_no', 'application_no');
    }

}
