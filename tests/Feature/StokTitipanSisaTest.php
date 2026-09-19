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

class StokTitipanSisaTest extends TestCase
{
    use RefreshDatabase;

    public function test_sisa_titipan_masuk_dikurangi_keluar(): void
    {
        [$supplier, $produk] = $this->buatProdukSupplier();

        StokTitipan::create([
            'produk_id' => $produk->id,
            'supplier_id' => $supplier->id,
            'tipe_stok' => 'masuk',
            'satuan' => 'kg',
            'jumlah' => 1000,
        ]);
        StokTitipan::create([
            'produk_id' => $produk->id,
            'supplier_id' => $supplier->id,
            'tipe_stok' => 'keluar',
            'satuan' => 'kg',
            'jumlah' => 1000,
        ]);

        $this->assertSame(0.0, StokTitipan::sisaUntuk($supplier->id, $produk->id));
        $this->assertSame(0.0, StokTitipan::petaSisa()[$supplier->id.'-'.$produk->id]);
    }

    public function test_tidak_bisa_jual_melebihi_sisa_titipan(): void
    {
        [$user, $pembelian, $produk, $stokTitipan] = $this->buatTitipanMasuk(100);

        StokTitipan::create([
            'produk_id' => $produk->id,
            'supplier_id' => $stokTitipan->supplier_id,
            'tipe_stok' => 'keluar',
            'satuan' => 'kg',
            'jumlah' => 100,
        ]);

        $this->actingAs($user)
            ->get(route('stoktitipans.jual', $stokTitipan))
            ->assertRedirect(route('stoktitipans.index'))
            ->assertSessionHasErrors('stok_titipan');

        $this->actingAs($user)
            ->get(route('stoktitipans.jualnow', ['pembelian' => $pembelian, 'detail' => $stokTitipan]))
            ->assertRedirect(route('stoktitipans.index'))
            ->assertSessionHasErrors('stok_titipan');

        $this->actingAs($user)
            ->post(route('stoktitipans.jualNowStore'), [
                'pembelian_id' => $pembelian->id,
                'detail_id' => $stokTitipan->id,
                'produk_id' => [$produk->id],
                'tipe_pembelian' => ['jual'],
                'netto' => [100],
                'rendeman' => [90],
                'harga' => [56250],
                'harga_basis_pembelian' => [62500],
                'harga_netto' => [5625000],
            ])
            ->assertSessionHasErrors('netto');

        $this->assertSame(1, StokTitipan::where('tipe_stok', 'keluar')->count());
        $this->assertSame(0.0, StokTitipan::sisaUntuk((int) $stokTitipan->supplier_id, (int) $produk->id));
    }

    public function test_jual_ulang_tidak_menggandakan_stok_keluar(): void
    {
        [$user, $pembelian, $produk, $stokTitipan] = $this->buatTitipanMasuk(100);

        $payload = [
            'pembelian_id' => $pembelian->id,
            'detail_id' => $stokTitipan->id,
            'produk_id' => [$produk->id],
            'tipe_pembelian' => ['jual'],
            'netto' => [40],
            'rendeman' => [90],
            'bobot' => [0],
            'harga' => [56250],
            'harga_basis_pembelian' => [62500],
            'harga_netto' => [2250000],
            'keterangan' => 'TITIPAN',
        ];

        $this->actingAs($user)
            ->post(route('stoktitipans.jualNowStore'), $payload)
            ->assertRedirect(route('pembelians.createlanjut', $pembelian));

        $payload['netto'] = [60];
        $payload['harga_netto'] = [3375000];

        $this->actingAs($user)
            ->post(route('stoktitipans.jualNowStore'), $payload)
            ->assertRedirect(route('pembelians.createlanjut', $pembelian));

        $this->assertSame(1, StokTitipan::where('tipe_stok', 'keluar')->count());
        $this->assertSame(60.0, (float) StokTitipan::where('tipe_stok', 'keluar')->value('jumlah'));
        $this->assertSame(40.0, StokTitipan::sisaUntuk((int) $stokTitipan->supplier_id, (int) $produk->id));
    }

    /**
     * @return array{0: Supplier, 1: Produk}
     */
    private function buatProdukSupplier(): array
    {
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

        return [$supplier, $produk];
    }

    /**
     * @return array{0: User, 1: Pembelian, 2: Produk, 3: StokTitipan}
     */
    private function buatTitipanMasuk(float $jumlah): array
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'create-stok-titipans']);
        $role->permissions()->attach($permission->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user->assignRole($role);

        [$supplier, $produk] = $this->buatProdukSupplier();
        $pembelian = Pembelian::create([
            'supplier_id' => $supplier->id,
            'nopol' => 'BG 1 AA',
        ]);
        $stokTitipan = StokTitipan::create([
            'produk_id' => $produk->id,
            'supplier_id' => $supplier->id,
            'pembelian_id' => $pembelian->id,
            'tipe_stok' => 'masuk',
            'satuan' => 'kg',
            'jumlah' => $jumlah,
        ]);

        return [$user, $pembelian, $produk, $stokTitipan];
    }
}
