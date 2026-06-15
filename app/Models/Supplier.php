<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{

    // Schema::create('suppliers', function (Blueprint $table) {
    //         $table->id();
    //         $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
    //         $table->string('nama');
    //         $table->string('kontak')->nullable();
    //         $table->string('alamat')->nullable();
    //         $table->timestamps();
    //         $table->string('deleted_at')->nullable();
    //         $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
    //         $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
    //     });

    protected $fillable = [
        'user_id',
        'nama',
        'kontak',
        'alamat',
        'isactive',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashbons(): HasMany
    {
        return $this->hasMany(CashbonSupplier::class);
    }

    public function totalCashbon()
    {
        return $this->cashbons->sum('nominal_cashbon');
    }
}
