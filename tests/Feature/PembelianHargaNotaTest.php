<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianHargaNotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_harga_nota_mengikuti_jumlah_uang_bukan_rumus_rendeman(): void
    {
        $detail = new PembelianDetail([
            'tipe_transaksi_pembelian' => 'jual',
            'netto' => 321,
            'harga' => 89680,
            'harga_netto' => 28890000,
        ]);

        $this->assertSame(90000, $detail->hargaUntukNota());
    }

    public function test_simpan_detail_menyelaraskan_harga_dengan_subtotal(): void
    {
        $supplier = Supplier::create([
            'nama' => 'Kapril Ulak Lebar',
            'isactive' => true,
        ]);
        $kategori = KategoriProduk::create(['nama' => 'Komoditas']);
        $produk = Produk::create([
            'kategori_produk_id' => $kategori->id,
            'nama_produk' => 'Lada',
            'satuan' => 'kg',
            'harga_basis_pembelian' => 94000,
            'harga_basis_penjualan' => 94000,
            'stok_akhir' => 100,
            'isactive' => true,
        ]);
        $pembelian = Pembelian::create([
            'supplier_id' => $supplier->id,
            'nopol' => 'BG 555 FG',
        ]);

        $detail = PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => $produk->id,
            'tipe_transaksi_pembelian' => 'jual',
            'netto' => 321,
            'satuan' => 'kg',
            'rendeman' => -3,
            'bobot' => -1500,
            'harga' => 89680,
            'harga_basis_pembelian' => 94000,
            'harga_netto' => 28890000,
        ]);

        $this->assertSame(90000, (int) $detail->fresh()->harga);
        $this->assertSame(28890000, (int) $detail->fresh()->harga_netto);
    }

    public function test_nota_menampilkan_keterangan_jika_diisi(): void
    {
        $supplier = Supplier::create([
            'nama' => 'Kapril Ulak Lebar',
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
            'keterangan' => 'jual titipan',
        ]);
        PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => $produk->id,
            'tipe_transaksi_pembelian' => 'jual',
            'netto' => 250,
            'satuan' => 'kg',
            'rendeman' => 90,
            'harga' => 56250,
            'harga_basis_pembelian' => 62500,
            'harga_netto' => 14062500,
        ]);

        $pembelian->load('details.produk', 'supplier');

        $html = view('exports.pembelian-nota', [
            'pembelian' => $pembelian,
            'pembayarancashbon' => null,
            'cashbonsebelum' => 0,
            'terbilang' => 'Empat Belas Juta Enam Puluh Dua Ribu Lima Ratus Rupiah',
        ])->render();

        $this->assertStringContainsString('Keterangan :', $html);
        $this->assertStringContainsString('jual titipan', $html);

        $pembelianTanpaKeterangan = Pembelian::create([
            'supplier_id' => $supplier->id,
            'nopol' => 'BG 2 AA',
            'keterangan' => null,
        ]);
        $pembelianTanpaKeterangan->load('details.produk', 'supplier');

        $htmlKosong = view('exports.pembelian-nota', [
            'pembelian' => $pembelianTanpaKeterangan,
            'pembayarancashbon' => null,
            'cashbonsebelum' => 0,
            'terbilang' => 'Nol Rupiah',
        ])->render();

        $this->assertStringContainsString('Keterangan :', $htmlKosong);
        $this->assertStringNotContainsString('jual titipan', $htmlKosong);
    }
}
