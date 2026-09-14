<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresentCustodian extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function propertyOutsides()
    {
        return $this->hasMany(PropertyOutside::class, 'present_custodian');
    }
}
