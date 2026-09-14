<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class UserRegistration extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    //Added by Lalit on 02/08/2024 Define the relationship to OldColony
    public function oldColony()
    {
        return $this->belongsTo(OldColony::class, 'locality');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'status', 'id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id')->where('has_property', 1);
    }

    public function statusItem()
    {
        return $this->belongsTo(Item::class, 'status');
    }

}
