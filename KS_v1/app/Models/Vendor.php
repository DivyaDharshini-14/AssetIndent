<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'gst_number',
        'address',
        'city',
        'state',
        'country',
        'status',
        'brands',
        'notes',
        'created_by',
        'updated_by',
    ];

     protected $casts = [
        'brands' => 'array', // JSON column cast to array
    ];

    // Add appended attribute
    protected $appends = ['brands_names'];

    public function getBrandIdsAttribute(): array
    {
        $brands = $this->getRawOriginal('brands');

        if (is_string($brands)) {
            $brands = json_decode($brands, true);

            if (is_string($brands)) {
                $brands = json_decode($brands, true);
            }
        }

        if (! is_array($brands)) {
            return [];
        }

        return array_values(array_filter(
            $brands,
            static fn ($brandId) => is_string($brandId) || is_int($brandId)
        ));
    }

    // This accessor will return the brand names as a string
    public function getBrandsNamesAttribute()
    {
        $brandIds = $this->brand_ids;

        if ($brandIds === []) {
            return '';
        }

        return Brand::query()
            ->whereIn('id', $brandIds)
            ->pluck('brand_name')
            ->implode(', ');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

      public function scopeActive($query)
      {
            return $query->where('status', 'active');
      }

    public function quotations()
    {
        return $this->hasMany(VendorQuotation::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function rfqs()
    {
        return $this->belongsToMany(Rfq::class, 'rfq_vendors')
            ->withPivot(['status', 'invited_at'])
            ->withTimestamps();
    }
}
