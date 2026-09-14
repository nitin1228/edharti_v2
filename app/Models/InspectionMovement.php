<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionMovement extends Model
{
    use HasFactory;

    protected $table = 'inspection_movements';
    protected $fillable = [
        'assigned_by',
        'assigned_by_role',
        'assigned_to',
        'assigned_to_role',
        'status',
        'action',
        'is_forwarded',
        'inspection_id',
        'remarks',
    ];
}