<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySectionTransfer extends Model
{
    use HasFactory;

    protected $table = 'property_section_transfer';

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [];
}
