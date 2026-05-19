<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        

        // Helper Blade untuk menyaring hak akses menu
        Blade::if('hasMenuPermission', function ($permission) {
            if (empty($permission)) return true;

            $user = auth()->user();
            if (!$user) return false;

            // Jika permission dikirim berupa array, user harus punya minimal salah satu
            if (is_array($permission)) {
                return collect($permission)->contains(fn($p) => $user->hasPermission($p));
            }

            return $user->hasPermission($permission);
        });
    }
}
