<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dinamis_variables', function (Blueprint $table) {
            $table->id();
            $table->string('nama_variable');
            $table->string('jenis');
            $table->float('variable_value');
            $table->text('keterangan');
            $table->boolean('isactive')->nullable()->default(true);
            $table->timestamps();
            $table->string('deleted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dinamis_variables');
    }
};
