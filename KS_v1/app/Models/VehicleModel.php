<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
  use HasFactory;

  protected $fillable = [
    'name', 'segment_id', 'transmission_id', 'fuel_type_id', 'dealer_location_id', 'company_id', 'status'
  ];

  protected $casts = [
    'status' => 'string',
  ];

  public function segment()
  {
    return $this->belongsTo(Segment::class);
  }

  public function transmission()
  {
      return $this->belongsTo(Transmission::class);
  }

  public function fuelType()
  {
    return $this->belongsTo(FuelType::class);
  }

  public function dealerLocation()
  {
    return $this->belongsTo(DealerLocation::class);
  }

  public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function scopeActive($query)
  {
    return $query->where('status', 'active');
  }

  public function scopeInactive($query)
  {
    return $query->where('status', 'inactive');
  }
}
