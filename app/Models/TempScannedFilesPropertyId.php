<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempScannedFilesPropertyId extends Model
{
    use HasFactory;
    protected $table = 'temp_scanned_files_property_id';
    // public $timestamps = false;
    protected $guarded = [];
}
