<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

     // Define the relationship to Colony
     public function colony()
     {
         return $this->belongsTo(OldColony::class, 'colony_id');
     }
 
     // Define the relationship to observations
     public function observations()
     {
         return $this->hasMany(SurveyObservation::class, 'survey_id', 'survey_id');
     }
}
