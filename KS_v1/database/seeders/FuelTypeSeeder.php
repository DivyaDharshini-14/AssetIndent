<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FuelType;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $fuelTypes = [
        ['name' => 'Petrol'],
        ['name' => 'Diesel'],
      ];

      foreach ($fuelTypes as $fuelType) {
        FuelType::updateOrCreate(
          ['name' => $fuelType['name']],
          $fuelType
        );
      }
    }
}
