<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\KategoriProduk;
use App\Models\Pengiriman;
use App\Models\PengirimanDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Permission;
use App\Models\Produk;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_penjualan_mengikuti_format_resmi(): void
    {
        [$penjualan] = $this->buatInvoiceOlam();
        $penjualan->load('details.produk', 'pengiriman.customer', 'customer');

        $this->assertSame(561967560, $penjualan->invoiceJumlah());
        $this->assertSame(6181643, $penjualan->invoicePpn());
        $this->assertSame(1404919, $penjualan->invoicePph22());
        $this->assertSame(566744284, $penjualan->invoiceTotalDibayar());
        $this->assertSame(58520, $penjualan->details->first()->hargaUntukNota());

        $html = view('exports.penjualan-nota', [
            'penjualan' => $penjualan,
            'terbilang' => 'Lima Ratus Enam Puluh Enam Juta Tujuh Ratus Empat Puluh Empat Ribu Dua Ratus Delapan Puluh Empat Rupiah',
        ])->render();

        $this->assertStringContainsString('I N V O I C E', $html);
        $this->assertStringContainsString('CV. ASIWA BUMI NIAGA', $html);
        $this->assertStringContainsString('NPwP : 53.695.315.1-302.000', $html);
        $this->assertStringContainsString('Kepada Yth.', $html);
        $this->assertStringContainsString('PT OLAM INDONESIA', $html);
        $this->assertStringContainsString('South Quarter Tower A', $html);
        $this->assertStringContainsString('Nomor : J26-00210', $html);
        $this->assertStringContainsString('No. Srt Jalan', $html);
        $this->assertStringContainsString('K26-00386', $html);
        $this->assertStringContainsString('KOPI', $html);
        $this->assertStringContainsString('9.603,00 KG', $html);
        $this->assertStringContainsString('Rp. 58.520,000', $html);
        $this->assertStringContainsString('Rp. 561.967.560', $html);
        $this->assertStringContainsString('1.1%', $html);
        $this->assertStringContainsString('0,25%', $html);
        $this->assertStringContainsString('Rp. 6.181.643', $html);
        $this->assertStringContainsString('Rp. 1.404.919', $html);
        $this->assertStringContainsString('TOTAL YANG DIBAYARKAN', $html);
        $this->assertStringContainsString('Rp. 566.744.284', $html);
        $this->assertStringContainsString('BANK CENTRAL ASIA', $html);
        $this->assertStringContainsString('2570799269', $html);
        $this->assertStringContainsString('IWAN SAPUTRA', $html);
        $this->assertStringContainsString('Direktur', $html);
        $this->assertStringNotContainsString('Titipan Barang', $html);
        $this->assertStringNotContainsString('Grand Total', $html);
    }

    public function test_invoice_penjualan_langsung_terunduh_pdf(): void
    {
        [$penjualan, $user] = $this->buatInvoiceOlam();

        $this->actingAs($user)
            ->get(route('penjualans.cetaknota', $penjualan))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('Invoice-Penjualan-J26-00210.pdf');
    }

    /**
     * @return array{0: Penjualan, 1: User}
     */
    private function buatInvoiceOlam(): array
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'show-penjualans']);
        $role->permissions()->attach($permission->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user->assignRole($role);

        $customer = Customer::create([
            'nama' => 'PT OLAM INDONESIA',
            'alamat' => "South Quarter Tower A, Lt 21 Unit A,G & H JL. R.A Kartini\nCilandak\nJakarta Selatan",
            'isactive' => true,
        ]);
        $kategori = KategoriProduk::create(['nama' => 'Komoditas']);
        $produk = Produk::create([
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Kopi',
            'satuan' => 'kg',
            'harga_basis_pembelian' => 50000,
            'harga_basis_penjualan' => 58520,
            'stok_akhir' => 10000,
            'isactive' => true,
        ]);
        $pengiriman = Pengiriman::create([
            'customer_id' => $customer->id,
            'nopol' => 'BG 123 AA',
            'no_transaksi' => 'K26-00386',
        ]);
        $pengiriman->forceFill(['no_transaksi' => 'K26-00386'])->save();

        $pengirimanDetail = PengirimanDetail::create([
            'pengiriman_id' => $pengiriman->id,
            'produk_id' => $produk->id,
            'nama_barang' => 'Kopi',
            'jumlah_per_karung' => 60,
            'jumlah_karung' => 160,
            'bruto' => 9700,
            'tara' => 97,
            'netto' => 9603,
        ]);

        $penjualan = Penjualan::create([
            'no_transaksi_penjualan' => 'J26-00210',
            'pengiriman_id' => $pengiriman->id,
            'customer_id' => $customer->id,
            'created_at' => '2026-09-11 10:00:00',
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'pengiriman_detail_id' => $pengirimanDetail->id,
            'produk_id' => $produk->id,
            'tipe' => 'Jual',
            'netto_pengiriman' => 9603,
            'netto' => 9603,
            'selisih' => 0,
            'basis_harga' => 58520,
            'sub_total' => 561967560,
            'pph' => 0,
            'ppn' => 0,
            'nominal_akhir' => 561967560,
        ]);

        return [$penjualan->fresh(), $user];
    }
}
