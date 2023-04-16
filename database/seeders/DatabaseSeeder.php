<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'TP-Link TL-WR74', 'mask' => 'XXAAAAAXAA'],
            ['name' => 'D-Link DIR-300', 'mask' => 'NXXAAXZXaa'],
            ['name' => 'D-Link DIR-300 E', 'mask' => 'NAAAAXZXXX']
        ];

        foreach ($data as $item) {
            EquipmentType::create($item);
        }
    }
}
