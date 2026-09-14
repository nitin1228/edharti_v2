<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyOutside extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function propertyStatus()
    {
        return $this->belongsTo(Item::class, 'status');
    }

    public function landType()
    {
        return $this->belongsTo(Item::class, 'land_type');
    }

    public function landUse()
    {
        return $this->belongsTo(Item::class, 'land_use');
    }
    // Relationship to PresentCustodian
    public function presentCustodian()
    {
        return $this->belongsTo(PresentCustodian::class, 'present_custodian');
    }

    // Relationship to PresentStatus (assuming the status is stored in `items` table)
    public function presentStatus()
    {
        return $this->belongsTo(Item::class, 'present_status');
    }
}
