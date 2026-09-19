<?php

namespace Tests\Feature;

use App\Models\CashbonSupplier;
use App\Models\CashbonSupplierPembayaran;
use App\Models\KategoriProduk;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Permission;
use App\Models\Produk;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianRendemanCashbonTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_createnow_menerima_rendeman_pecahan(): void
    {
        [$user, $pembelian] = $this->buatPembelianDenganCashbon();

        $this->actingAs($user)
            ->get(route('pembeliandetails.createnow', $pembelian))
            ->assertOk()
            ->assertSee('name="rendeman[]"', false)
            ->assertSee('step="0.01"', false);
    }

    public function test_halaman_lanjut_punya_input_pengurangan_cashbon(): void
    {
        [$user, $pembelian] = $this->buatPembelianDenganCashbon();

        $this->actingAs($user)
            ->get(route('pembelians.createlanjut', $pembelian))
            ->assertOk()
            ->assertSee('Pengurangan Cashbon')
            ->assertSee('Harga Jual')
            ->assertSee('Rp 87.086')
            ->assertSee('name="potong_bon"', false)
            ->assertSee('value="10010000"', false)
            ->assertSee('Rp 77.075.963')
            ->assertSee("addEventListener('blur', hitungOtomatis)", false)
            ->assertDontSee("addEventListener('input', hitungOtomatis)", false);
    }

    public function test_admin_bisa_memotong_cashbon_secara_manual(): void
    {
        [$user, $pembelian] = $this->buatPembelianDenganCashbon(10_010_000, 87_085_963);

        $this->actingAs($user)
            ->post(route('pembelians.storelanjut', $pembelian), [
                'potong_bon' => 10_010_000,
                'titip' => 0,
                'ambil_tunai' => 77_075_963,
                'ambil_transfer' => 0,
                'status' => 'Lunas',
                'keterangan' => 'Potong cashbon',
                'action' => 'save',
            ])
            ->assertRedirect(route('pembelians.index'))
            ->assertSessionHasNoErrors();

        $pembelian->refresh();

        $this->assertSame(87_085_963, (int) $pembelian->total_nominal_terbayar);
        $this->assertSame(0, (int) $pembelian->kekurangan);
        $this->assertSame('Lunas', $pembelian->status_pembayaran);

        $this->assertDatabaseHas('cashbon_supplier_pembayarans', [
            'supplier_id' => $pembelian->supplier_id,
            'nominal_bayar' => 10_010_000,
            'keterangan' => 'Lewat Pembelian' . $pembelian->no_transaksi,
        ]);
    }

    public function test_pengurangan_cashbon_tidak_boleh_melebihi_sisa_cashbon(): void
    {
        [$user, $pembelian] = $this->buatPembelianDenganCashbon(1_000_000, 5_000_000);

        $this->actingAs($user)
            ->from(route('pembelians.createlanjut', $pembelian))
            ->post(route('pembelians.storelanjut', $pembelian), [
                'potong_bon' => 2_000_000,
                'titip' => 0,
                'ambil_tunai' => 5_000_000,
                'ambil_transfer' => 0,
                'status' => 'Lunas',
                'keterangan' => 'Potong terlalu besar',
                'action' => 'save',
            ])
            ->assertRedirect(route('pembelians.createlanjut', $pembelian))
            ->assertSessionHasErrors('potong_bon');
    }

    /**
     * @return array{0: User, 1: Pembelian}
     */
    private function buatPembelianDenganCashbon(int $cashbon = 10_010_000, int $tagihan = 87_085_963): array
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Admin']);
        foreach (['create-pembelians', 'create-pembeliandetails'] as $permissionName) {
            $permission = Permission::create(['name' => $permissionName]);
            $role->permissions()->attach($permission->id, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $user->assignRole($role);

        $supplier = Supplier::create([
            'nama' => 'Kapril Ulak Lebar',
            'isactive' => true,
        ]);

        CashbonSupplier::create([
            'supplier_id' => $supplier->id,
            'nominal_cashbon' => $cashbon,
            'tipe' => 'Cash',
            'keterangan' => 'Cashbon awal',
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
            'tipe_transaksi_pembelian' => 'Jual',
            'total_nominal_pembelian' => $tagihan,
        ]);

        PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => $produk->id,
            'tipe_transaksi_pembelian' => 'jual',
            'netto' => 1000,
            'satuan' => 'kg',
            'rendeman' => 89.25,
            'harga' => 55625,
            'harga_basis_pembelian' => 62500,
            'harga_netto' => $tagihan,
        ]);

        return [$user, $pembelian->fresh()];
    }
}
