<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transmission;

class TransmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $transmissions = [
        ['name' => 'Manual'],
        ['name' => 'Automatic'],
      ];

      foreach ($transmissions as $transmission) {
        Transmission::updateOrCreate(
          ['name' => $transmission['name']],
          $transmission
        );
      }
    }
}
