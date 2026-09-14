<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;
    protected $table = 'revenues';

    protected $fillable = [
        'financial_year',
        'month',
        'revenue_amount_without_ntrp',
        'total_revenue_amount_pfms'
    ];

    protected $casts = [
        'revenue_amount_without_ntrp' => 'decimal:2',
        'total_revenue_amount_pfms' => 'decimal:2'
    ];
}
