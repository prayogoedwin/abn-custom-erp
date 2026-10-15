<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['key' => 'TIK', 'value' => 'temp_tik', 'description' => 'TIK yang digunakan pada xml Coretax'],
            ['key' => 'IdTKU', 'value' => 'temp_idtku', 'description' => 'IdTKU yang digunakan pada xml Coretax'],
        ];
        foreach ($settings as $setting) {
            DB::table('settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            DB::table('settings')->whereIn('key', ['TIK', 'IdTKU'])->delete();
        });
    }
};
