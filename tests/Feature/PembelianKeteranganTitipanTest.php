<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Pembelian;
use App\Models\Permission;
use App\Models\Produk;
use App\Models\Role;
use App\Models\StokTitipan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianKeteranganTitipanTest extends TestCase
{
    use RefreshDatabase;

    public function test_jual_stok_titipan_mengisi_keterangan_titipan_dan_tetap_bisa_diubah(): void
    {
        [$user, $pembelian, $produk] = $this->buatPembelianTitipan();

        $this->actingAs($user)
            ->post(route('stoktitipans.jualNowStore'), [
                'pembelian_id' => $pembelian->id,
                'produk_id' => [$produk->id],
                'tipe_pembelian' => ['jual'],
                'netto' => [100],
                'rendeman' => [90],
                'bobot' => [0],
                'harga' => [56250],
                'harga_basis_pembelian' => [62500],
                'harga_netto' => [5625000],
            ])
            ->assertRedirect(route('pembelians.createlanjut', $pembelian));

        $this->assertSame('TITIPAN', $pembelian->fresh()->keterangan);

        $this->actingAs($user)
            ->get(route('pembelians.createlanjut', $pembelian))
            ->assertOk()
            ->assertSee('value="TITIPAN"', false);

        $this->actingAs($user)
            ->post(route('pembelians.storelanjut', $pembelian), [
                'potong_bon' => 0,
                'titip' => 0,
                'ambil_tunai' => 0,
                'ambil_transfer' => 0,
                'status' => 'Belum Lunas',
                'keterangan' => 'catatan admin',
                'action' => 'save',
            ])
            ->assertRedirect(route('pembelians.index'));

        $this->assertSame('catatan admin', $pembelian->fresh()->keterangan);
    }

    /**
     * @return array{0: User, 1: Pembelian, 2: Produk}
     */
    private function buatPembelianTitipan(): array
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        foreach (['create-pembelians', 'create-stok-titipans'] as $permissionName) {
            $permission = Permission::create(['name' => $permissionName]);
            $role->permissions()->attach($permission->id, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $user->assignRole($role);

        $supplier = Supplier::create([
            'nama' => 'Amin Tualang',
            'isactive' => true,
        ]);
        $kategori = KategoriProduk::create(['nama' => 'Komoditas']);
        $produk = Produk::create([
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Kopi',
            'satuan' => 'kg',
            'harga_basis_pembelian' => 62500,
            'harga_basis_penjualan' => 70000,
            'stok_akhir' => 100,
            'isactive' => true,
        ]);
        $pembelian = Pembelian::create([
            'supplier_id' => $supplier->id,
            'nopol' => 'BG 1 AA',
        ]);
        StokTitipan::create([
            'produk_id' => $produk->id,
            'supplier_id' => $supplier->id,
            'pembelian_id' => $pembelian->id,
            'tipe_stok' => 'masuk',
            'satuan' => 'kg',
            'jumlah' => 100,
            'keterangan' => 'Pembelian Titip',
        ]);

        return [$user, $pembelian, $produk];
    }
}
