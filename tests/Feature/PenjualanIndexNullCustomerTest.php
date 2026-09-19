<?php

namespace Tests\Feature;

use App\Models\Pengiriman;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanIndexNullCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_penjualan_tidak_error_jika_pengiriman_tanpa_customer(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        foreach (['view-penjualans', 'create-penjualans'] as $permissionName) {
            $permission = Permission::create(['name' => $permissionName]);
            $role->permissions()->attach($permission->id, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $user->assignRole($role);

        Pengiriman::create([
            'customer_id' => null,
            'nopol' => 'BG 1 AA',
        ]);

        $this->actingAs($user)
            ->get(route('penjualans.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('penjualans.create'))
            ->assertOk();
    }
}
