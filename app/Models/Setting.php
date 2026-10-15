<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    //tanpa timestamp

    public $timestamps = false;

    protected $fillable = ['key', 'value', 'description'];
}
