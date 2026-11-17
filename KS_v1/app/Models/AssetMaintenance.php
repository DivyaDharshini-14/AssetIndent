<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AssetMaintenance extends Model
{
    use HasFactory;

    protected $table = 'asset_maintenance';

    protected $fillable = [
        'asset_id',
        'maintenance_code',
        'source_type',
        'maintenance_date',
        'maintenance_notes',
        'maintenance_cost',
        'vendor_id',
        'priority',
        'status',
        'start_date',
        'end_date',
        'remarks',
        'reported_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'maintenance_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($maintenance) {
            if (!$maintenance->maintenance_code) {
                $lastId = self::max('id') + 1;
                $maintenance->maintenance_code = 'MTN-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
            }

            // When maintenance created → set asset to under_maintenance
            if ($maintenance->asset_id) {
                Asset::where('id', $maintenance->asset_id)
                    ->update(['operational_status' => 'under_maintenance']);
            }
        });


        static::updating(function ($maintenance) {

            // If status changed
            if ($maintenance->isDirty('status')) {

                if (in_array($maintenance->status, ['completed', 'cancelled'])) {

                    Asset::where('id', $maintenance->asset_id)
                        ->update(['operational_status' => 'working']);
                }
            }

         });

        


    }


     /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsCompleted()
    {
        $this->update([
            'status'   => 'completed',
            'end_date' => now(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedCostAttribute()
    {
        return $this->maintenance_cost
            ? number_format($this->maintenance_cost, 2)
            : '0.00';
    }
}
