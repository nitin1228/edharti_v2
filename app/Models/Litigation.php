<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Litigation extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'parties' => 'array',
        'counsels' => 'array',
    ];

    public function hearings()
    {
        return $this->hasMany(CaseHearing::class);
    }

}
