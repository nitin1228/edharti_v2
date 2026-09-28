<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorAreaRatio extends Model
{
    use HasFactory;

    protected $fillable = [
        'effective_from',
        'effective_to',
        'area_from',
        'area_to',
        'area_unit',
        'far',
        'ground_coverage',
    ];

    protected $casts = [
        'effective_from'   => 'date',
        'effective_to'     => 'date',
        'area_from'        => 'decimal:2',
        'area_to'          => 'decimal:2',
        'far'              => 'decimal:2',
        'ground_coverage'  => 'decimal:2',
    ];
}
