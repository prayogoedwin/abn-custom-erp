<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\KategoriProduk;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use App\Models\Permission;
use App\Models\Produk;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengirimanSuratJalanTest extends TestCase
{
    use RefreshDatabase;

    public function test_surat_jalan_mengikuti_format_lama(): void
    {
        [$user, $pengiriman] = $this->buatPengiriman();

        $html = view('exports.pengiriman-surat_jalan', [
            'pengiriman' => $pengiriman->load('customer', 'detail.produk'),
        ])->render();

        $this->assertStringContainsString('CV ASIWA BUMI NIAGA', $html);
        $this->assertStringContainsString('Jl. Kol. Wahab Uzir No.930', $html);
        $this->assertStringContainsString('Kepada Yth.', $html);
        $this->assertStringContainsString('PT. OLAM INDONESIA', $html);
        $this->assertStringContainsString('South Quarter Tower A', $html);
        $this->assertStringContainsString('BANYAKNYA', $html);
        $this->assertStringContainsString('KETERANGAN', $html);
        $this->assertStringContainsString('KOPI ASALAN', $html);
        $this->assertStringContainsString('90 KARUNG', $html);
        $this->assertStringContainsString('9.126,00 KG', $html);
        $this->assertStringContainsString('BRUTO', $html);
        $this->assertStringContainsString('TARA', $html);
        $this->assertStringContainsString('27,00) KG', $html);
        $this->assertStringContainsString('NETTO', $html);
        $this->assertStringContainsString('9.099,00 KG', $html);
        $this->assertStringContainsString('TANDA TERIMA', $html);
        $this->assertStringContainsString('PENGIRIM', $html);
        $this->assertStringNotContainsString('Total Item', $html);
        $this->assertStringNotContainsString('Dibuat Oleh', $html);

        $this->actingAs($user)
            ->get(route('pengirimans.suratjalan', $pengiriman))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    /**
     * @return array{0: User, 1: Pengiriman}
     */
    private function buatPengiriman(): array
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'show-pengirimans']);
        $role->permissions()->attach($permission->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user->assignRole($role);

        $customer = Customer::create([
            'nama' => 'PT. OLAM INDONESIA',
            'alamat' => "South Quarter Tower A, Lt 21 Unit A,G & H JL. R.A Kartini\nCilandak\nJakarta Selatan",
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

        $pengiriman = Pengiriman::create([
            'customer_id' => $customer->id,
            'nopol' => 'BG 8574 FO',
        ]);

        PengirimanDetail::create([
            'pengiriman_id' => $pengiriman->id,
            'produk_id' => $produk->id,
            'nama_barang' => 'Kopi Asalan',
            'jumlah_per_karung' => 101.4,
            'jumlah_karung' => 90,
            'bruto' => 9126,
            'tara' => 27,
            'netto' => 9099,
        ]);

        return [$user, $pengiriman->fresh()];
    }
}
