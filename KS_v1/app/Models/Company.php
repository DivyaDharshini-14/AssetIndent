<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Company extends Model
{
  use HasFactory;

  protected $fillable = [
    'name', 'code', 'address_line_1', 'address_line_2', 'city', 'state', 'country', 'pincode', 'contact_person',
    'contact_number', 'email', 'status'
  ];

  protected $casts = [
    'status' => 'string',
  ];

  public function branches()
  {
    return $this->hasMany(Branch::class);
  }

  public function assets(){

    return $this->hasMany(Asset::class,'company_id','id');
  }

  public function assetAssignment(){

     return $this->hasMany(AssetAssignment::class,'to_company_id','id');
  }

  public function dealerLocations()
  {
    return $this->hasMany(DealerLocation::class);
  }

  public function vehicleModels()
  {
    return $this->hasMany(VehicleModel::class);
  }

  public function scopeActive($query)
  {
    return $query->where('status', 'active');
  }

  public function scopeVisibleToUser($query, $user)
  {
        // Super admins can see every company.
       if ($user->hasRole('superadmin')) {
        return $query;
        
       }

      $companyIds = $user->companies()->pluck('companies.id');

      return $query->where(function ($q) use ($companyIds) {
                // Asset belongs to user's branch
                $q->whereIn('id', $companyIds);
      });
    
  }

  public function users(){

    return $this->belongsToMany(User::class)->withTimestamps();
  }

  public function hasDependencies()
  {
      return $this->branches()->exists()
          || $this->assets()->exists()
          || $this->assetAssignment()->exists()
          || $this->users()->exists();
  }


}
