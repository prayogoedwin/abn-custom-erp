<?php
namespace App\Http\Controllers;

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

        // dd($mulai, $akhir);

        /*
         * 1. DATA PENJUALAN
         *
         * Asumsi awal:
         * - sub_total adalah nilai penjualan sebelum pajak.
         * - Tanggal laporan berdasarkan penjualans.created_at.
         *
         * Pastikan kedua asumsi tersebut sesuai dengan
         * kebijakan pencatatan transaksi Anda.
         */
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

        /*
         * 2. DATA PENGELUARAN PER KATEGORI
         *
         */
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

        $totalPengeluaran = (float)
            $pengeluaranPerKategori->sum('total');

        // dd($totalPengeluaran);

        /*
         * 3. HPP BELUM DAPAT DIHITUNG DARI SKEMA SAAT INI
         *
         * Pembelian periode berjalan tidak sama dengan HPP.
         * Dibutuhkan data persediaan awal, persediaan akhir,
         * dan penyesuaian persediaan yang relevan.
         */
        $hpp = null;
        $labaSetelahPengeluaranSebelumHpp = 
            $totalPenjualan - $totalPengeluaran;

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

            'pengeluaranPerKategori' =>
                $pengeluaranPerKategori,
            'totalPengeluaran' => $totalPengeluaran,

            'hpp' => $hpp,
            'labaSetelahPengeluaranSebelumHpp' =>
                $labaSetelahPengeluaranSebelumHpp,

            'labaBersih' => null,
        ];

        // dd($data);

        $pdf = Pdf::loadView(
            'exports.laba-rugi-pdf',
            $data
        )->setPaper('a4', 'portrait');

        

        return $pdf->download('laporan-laba-rugi.pdf');
    }
}
