<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DinamisVariable extends Model
{
    // Schema::create('dinamis_variables', function (Blueprint $table) {
    //         $table->id();
    //         $table->string('nama_variable');
    //         $table->string('jenis');
    //         $table->string('variable_value');
    //         $table->text('keterangan');
    //         $table->timestamps();
    //     });

    protected $fillable = [
        'nama_variable',
        'jenis',
        'variable_value',
        'keterangan',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];
}
