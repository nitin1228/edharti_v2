<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;
    protected $guarded = [];

    //User have many sections
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function propertySectionMappings(): HasMany
    {
        return $this->hasMany(PropertySectionMapping::class);
    }

    public function publicgrievances()
    {
        return $this->hasMany(AdminPublicGrievance::class, 'section_ids', 'section_code');
    }

    public function userRegistrations()
    {
        return $this->hasMany(UserRegistration::class, 'section_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'section_id');
    }

    public function scopeWithProperty($query)
    {
        return $query->where('has_property', 1);
    }

}
