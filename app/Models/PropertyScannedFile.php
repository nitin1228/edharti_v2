<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyScannedFile extends Model
{
    use HasFactory;

    protected $table = 'property_scanned_files';

    protected $guarded = [];
    public function propertyMaster()
{
    return $this->belongsTo(PropertyMaster::class, 'property_master_id');
}

public function flat()
{
    return $this->belongsTo(Flat::class, 'flat_id');
}

public function splitProperty()
{
    return $this->belongsTo(SplitedPropertyDetail::class, 'splited_property_detail_id');
}
}
