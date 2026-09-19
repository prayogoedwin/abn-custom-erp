<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanHargaJadiTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_penjualan_harga_jadi_bisa_diedit(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'create-penjualans']);
        $role->permissions()->attach($permission->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('penjualans.create'))
            ->assertOk()
            ->assertSee('Harga Jadi')
            ->assertSee('name="harga_jadi[]"', false)
            ->assertSee('Bisa diedit. Kosongkan lalu Hitung untuk isi dari rendeman.')
            ->assertSee('hargaJadiInput.value === \'\' || Number.isNaN(hargaJadi)', false);
    }
}
