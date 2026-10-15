<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'TIK', 'value' => 'temp_tik', 'description' => 'TIK yang digunakan pada xml Coretax'],
            ['key' => 'IdTKU', 'value' => 'temp_idtku', 'description' => 'IdTKU yang digunakan pada xml Coretax'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
