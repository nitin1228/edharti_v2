<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarriedDemandDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function newDemand()
    {
        return $this->belongsTo(Demand::class, 'new_demand_id');
    }

    public function oldDemand()
    {
        return $this->belongsTo(Demand::class, 'old_demand_id');
    }
}
