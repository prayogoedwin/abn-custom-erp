<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LaporanLabaRugiController extends Controller
{
    public function exportPdf(Request $request)
    {
        // dd($request->all());
        $mulai = Carbon::now()->startOfYear();
        $akhir = Carbon::now()->endOfYear();

        if ($request->filled(['startdate', 'enddate'])) {
            $mulai = Carbon::parse($request->startdate)->startOfDay();
            $akhir = Carbon::parse($request->enddate)->endOfDay();
        }


        $penjualan = DB::table('penjualan_details as pd')
            ->join(
                'penjualans as p',
                'p.id',
                '=',
                'pd.penjualan_id'
            )
            ->whereBetween('p.created_at', [
                $mulai,
                $akhir,
            ])
            ->whereNull('p.deleted_at')
            ->whereNull('pd.deleted_at')
            ->selectRaw('
                COALESCE(SUM(pd.sub_total), 0) as penjualan,
                COALESCE(SUM(pd.ppn), 0) as ppn,
                COALESCE(SUM(pd.pph), 0) as pph,
                COALESCE(SUM(pd.nominal_akhir), 0)
                    as nominal_akhir
            ')
            ->first();

        // dd($penjualan);

        $totalPenjualan = (float) $penjualan->penjualan;
        $totalPpn = (float) $penjualan->ppn;
        $totalPph = (float) $penjualan->pph;
        $nominalAkhir = (float) $penjualan->nominal_akhir;


        $pembelianPerBarang = PembelianDetail::query()
            ->join('pembelians', 'pembelians.id', '=', 'pembelian_details.pembelian_id')
            ->join('produks', 'produks.id', '=', 'pembelian_details.produk_id')
            ->whereBetween('pembelians.created_at', [
                $mulai->toDateString(),
                $akhir->toDateString(),
            ])
            ->where('pembelian_details.tipe_transaksi_pembelian', 'jual')
            ->whereNull('pembelian_details.deleted_at')
            ->whereNull('produks.deleted_at')
            ->select(
                'produks.id as produk_id',
                'produks.nama_produk as nama_barang',
                DB::raw('SUM(pembelian_details.harga_netto) as total')
            )
            ->groupBy('produks.id', 'produks.nama_produk')
            ->orderBy('produks.nama_produk')
            ->get();


        $pengeluaranPerKategori = DB::table(
            'pengeluarans as pg'
        )
            ->leftJoin(
                'kategori_pengeluarans as kp',
                'kp.id',
                '=',
                'pg.kategori_pengeluaran_id'
            )
            ->whereBetween('pg.tanggal', [
                $mulai->toDateString(),
                $akhir->toDateString(),
            ])
            ->whereNull('pg.deleted_at')
            ->whereNull('kp.deleted_at')
            ->selectRaw("
                COALESCE(
                    kp.nama_kategori,
                    'Kategori tidak tersedia'
                ) as kategori,
                COALESCE(SUM(pg.nominal), 0) as total
            ")
            ->groupBy('kp.id', 'kp.nama_kategori')
            ->orderBy('kp.nama_kategori')
            ->get();

        $totalPengeluaran = (float) $pengeluaranPerKategori->sum('total') + $pembelianPerBarang->sum('total');

        // dd($totalPengeluaran);


        $hpp = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->join('produks as pr', 'pr.id', '=', 'pd.produk_id')
            ->whereBetween('p.created_at', [$mulai, $akhir])
            ->whereNull('p.deleted_at')
            ->whereNull('pd.deleted_at')
            ->whereNull('pr.deleted_at')
            ->selectRaw('
                COALESCE(
                    SUM(pd.netto * pr.harga_basis_pembelian),
                    0
                ) as hpp
            ')
            ->value('hpp');

        // Pengeluaran operasional, tidak termasuk pembelian persediaan
        $totalPengeluaranOperasional =
            (float) $pengeluaranPerKategori->sum('total');

        // HPP barang yang benar-benar terjual
        // $hpp dihitung menggunakan query sebelumnya

        $labaKotor = $totalPenjualan - $hpp;

        $labaBersih = $labaKotor - $totalPengeluaranOperasional;

        /*
         * 4. DATA UNTUK PDF
         */
        $data = [
            'tanggalMulai' => $mulai,
            'tanggalAkhir' => $akhir,

            'penjualan' => $totalPenjualan,
            'ppn' => $totalPpn,
            'pph' => $totalPph,
            'nominalAkhir' => $nominalAkhir,
            'pembelianPerBarang' => $pembelianPerBarang,

            'pengeluaranPerKategori' => $pengeluaranPerKategori,
            'totalPengeluaran' => $totalPengeluaran,

            'hpp' => $hpp,
            'totalPengeluaranOperasional' => $totalPengeluaranOperasional,
            'labaKotor' => $labaKotor,
            'labaBersih' => $labaBersih,
        ];

        // dd($data);

        $pdf = Pdf::loadView(
            'exports.laba-rugi-pdf',
            $data
        )->setPaper('a4', 'portrait');



        return $pdf->download('laporan-laba-rugi.pdf');
    }
}
