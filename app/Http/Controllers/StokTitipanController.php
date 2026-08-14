<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\StokTitipan;
use App\Models\PembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SimpanPinjamSupplier;

class StokTitipanController extends Controller
{
    private function toIntMoney($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        $cleaned = preg_replace('/[^\d\-]/', '', (string) $value);
        return $cleaned === '' || $cleaned === '-' ? 0 : (int) $cleaned;
    }

    private function syncPembelianTotals(int $pembelianId): void
    {
        $pembelian = Pembelian::find($pembelianId);
        if (!$pembelian) {
            return;
        }

        $totalNominalPembelian = (int) PembelianDetail::where('pembelian_id', $pembelianId)->sum('harga_netto');
        $totalNominalTerbayar = (int) SimpanPinjamSupplier::where('pembelian_id', $pembelianId)->sum('nominal');
        $kekurangan = max($totalNominalPembelian - $totalNominalTerbayar, 0);
        $statusPembayaran = $kekurangan <= 0 ? 'Lunas' : 'Belum Lunas';

        $pembelian->update([
            'total_nominal_pembelian' => $totalNominalPembelian,
            'total_nominal_terbayar' => $totalNominalTerbayar,
            'kekurangan' => $kekurangan,
            'status_pembayaran' => $statusPembayaran,
        ]);
    }

    public function index()
    {
        return view('stok-titipan.index');
    }

    // columns: [
    //     { data: 'produk.nama_produk', name: 'produk.nama_produk' },
    //     { data: 'supplier.nama', name: 'supplier.nama' },
    //     { data: 'tipe_stok', name: 'tipe_stok' },
    //     { data: 'jumlah', name: 'jumlah' },
    //     { data: 'keterangan', name: 'keterangan' },
    //     { data: 'tanggal', name: 'tanggal' },

    // ],
    public function indexTable()
    {
        if (request()->ajax()) {
            // Pastikan relasi 'produk' dan 'supplier' sudah terdefinisi di model StokTitipan
            $data = StokTitipan::with(['produk', 'supplier'])->select('stok_titipans.*'); // Sesuaikan dengan nama tabel Anda

            return datatables()->of($data)
                // Fix Fitur Pencarian untuk kolom Relasi Produk
                ->filterColumn('produk.nama_produk', function ($query, $keyword) {
                    $query->whereHas('produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })
                // Fix Fitur Pencarian untuk kolom Relasi Supplier
                ->filterColumn('supplier.nama', function ($query, $keyword) {
                    $query->whereHas('supplier', function ($q) use ($keyword) {
                        $q->where('nama', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d-m-Y'); // Format tanggal sesuai kebutuhan
                })

                ->addColumn('action', function ($row) {
                    if (strtolower($row->tipe_stok) === 'masuk') {
                        $btn = '<a href="' . route('stoktitipans.jual', ['stoktitipan' => $row->id]) . '" class="text-indigo-600 dark:text-indigo-400 hover:underline">Jual</a>';
                    } else {
                        $btn = ''; // Tidak ada tombol jika tipe_stok bukan "masuk"
                    }
                    return $btn;
                    
                })
               
                ->rawColumns(['action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function jual(StokTitipan $stoktitipan)
    {
        $supplier = $stoktitipan->pembelian->supplier;

        return view('stok-titipan.jualcreate', compact('stoktitipan', 'supplier'));
    }

    public function jualStore(Request $request)
    {
        $store_data = [
            'nopol' => $request->input('nopol'),
            'supplier_id' => $request->input('supplier_id'),
            'stok_titipan_id' => $request->input('stok_titipan_id'),
            'created_by' => auth()->id(),
        ];


        $validate = Validator::make($store_data, [
            'nopol' => ['required', 'string', 'max:255'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'stok_titipan_id' => ['required', 'integer', 'exists:stok_titipans,id'],

            'created_by' => ['required', 'integer']
        ]);


        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }

        $detail = StokTitipan::findOrFail($request->input('stok_titipan_id'));

        $pembelian = Pembelian::create($store_data);
        $produk = $detail->produk;

        return to_route('stoktitipans.jualnow', ['pembelian' => $pembelian->id, 'detail' => $detail->id])->with('success', 'Data berhasil disimpan. Silakan lanjutkan ke halaman berikutnya.');
    }

    public function jualnow(Pembelian $pembelian, StokTitipan $detail)
    {
        $produk = $detail->produk;

        // dd($pembelian, $detail, $produk);

        return view('stok-titipan.jualnow', compact('pembelian', 'detail', 'produk'));
    }

    public function jualNowStore(Request $request)
    {
        // dd($request->all());
        $harga = $request->harga ?? [];
        $harga_basis = $request->harga_basis ?? [];
        $harga_basis_pembelian = $request->harga_basis_pembelian ?? [];
        $harga_netto = $request->harga_netto ?? [];


        //karena ini create pastikan semua data dihapus dulu
        PembelianDetail::where('pembelian_id', $request->pembelian_id)->delete();

        foreach ($request->produk_id as $index => $produk_id) {
            if ($produk_id) {

                //if tipe_transaksi_pembelian = "titip" maka semua harga di set ke 0, karena titip tidak ada harga
                if ($request->tipe_pembelian[$index] === 'titip') {
                    $harga[$index] = 0;
                    $harga_basis[$index] = 0;
                    $harga_basis_pembelian[$index] = 0;
                    $harga_netto[$index] = 0;
                }
                    

                $pembelianDetail = PembelianDetail::create([
                    'pembelian_id' => $request->pembelian_id,
                    'produk_id'    => $produk_id,
                    'tipe_transaksi_pembelian'         => $request->tipe_pembelian[$index],
                    'netto'        => $request->netto[$index],
                    //TODO:
                    'satuan'       => 'kg', //sementara hardcode
                    'rendeman'     => $request->rendeman[$index] ?? null,
                    'bobot'     => $this->toIntMoney($request->bobot[$index] ?? 0),
                    'harga'     => $this->toIntMoney($harga[$index] ?? 0),
                    'harga_basis'     => $this->toIntMoney($harga_basis[$index] ?? 0),
                    'harga_basis_pembelian'     => $this->toIntMoney($harga_basis_pembelian[$index] ?? ($harga_basis[$index] ?? 0)),
                    'harga_netto'     => $this->toIntMoney($harga_netto[$index] ?? 0),
                ]);

                
            }
        }
        $this->syncPembelianTotals((int) $request->pembelian_id);

        

        //update stok titipan yang tadinya titip sekarang jual. maka tambah stok titipan keluar dengan jumlah yang sama dengan pembelian detail yang baru dibuat. maka stok titipan keluar akan berkurang
        $supplierId = Pembelian::find($request->pembelian_id)->supplier_id;
        StokTitipan::create([
            'produk_id' => $request->produk_id[0], //ambil produk pertama
            'supplier_id' => $supplierId,
            'pembelian_id' => $request->pembelian_id,
            'satuan' => 'kg', //sementara hardcode
            'tipe_stok' => 'keluar',
            'jumlah' => $request->netto[0], //ambil netto pertama
            'keterangan' => 'Stok Titipan ke Jual',
            'created_by' => auth()->id(),
        ]);


        return to_route('pembelians.createlanjut', $request->pembelian_id);
    }


}
