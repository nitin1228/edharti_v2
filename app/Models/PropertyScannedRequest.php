<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyScannedRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'property_scanned_requests';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected static function booted()
    {
        // When soft-deleting, set is_active = 0
        static::deleting(function ($model) {
            // Only on soft delete, not forceDelete()
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                $model->is_active = 0;
                $model->saveQuietly(); // avoid event loops
            }
        });

        // When restoring, set is_active = 1
        static::restoring(function ($model) {
            $model->is_active = 1;
            $model->saveQuietly();
        });
    }

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

    public function colony()
    {
        return $this->belongsTo(OldColony::class, 'colony_id');
    }
}
