<?php

namespace App\Http\Controllers;

use App\Exports\Laporan\BiayaExport;
use App\Exports\Laporan\RugiLabaExport;
use App\Exports\LaporanExport;
use App\Models\CashbonKaryawan;
use App\Models\CashbonPihak3;
use App\Models\CashbonPihak3Pembayaran;
use App\Models\CashbonSupplier;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Karyawan;
use App\Models\Pengeluaran;
use App\Models\Pengiriman;
use App\Models\PenjualanDetail;
use App\Models\Stok;
use App\Models\StokTitipan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Supplier;
use App\Models\TitipSupplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


/* ======================================================================
     |  HELPER
     * ====================================================================== */

class LaporanExportController extends Controller
{

    /** Filter tanggal (startdate & enddate) */
    private function applyDateFilter(mixed $query, Request $request, string $column = 'created_at')
    {
        if ($request->filled(['startdate', 'enddate'])) {
            $start = Carbon::parse($request->startdate)->startOfDay();
            $end   = Carbon::parse($request->enddate)->endOfDay();

            $query->whereBetween($column, [$start, $end]);
        }

        return $query;
    }

    private function formatTanggal(mixed $value): string
    {
        return $value ? Carbon::parse($value)->translatedFormat('d M Y') : '-';
    }

    private function downloadLaporan(string $nama, array $headings, mixed $rows)
    {
        return Excel::download(
            new LaporanExport(collect($rows)->values(), $headings),
            $nama . '-' . date('Y-m-d') . '.xlsx'
        );
    }

    /** Untuk laporan gabungan (UNION): transaksi, nominal, sumber, created_at */
    private function rowsFromUnion(mixed $unionQuery): Collection
    {
        return DB::query()
            ->fromSub($unionQuery, 'laporan')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($row) => [
                $row->transaksi,
                (float) $row->nominal,
                ucfirst($row->sumber),
                $this->formatTanggal($row->created_at),
            ]);
    }

    private function periodeLabel(Request $request): array
    {
        if ($request->filled(['startdate', 'enddate'])) {
            return [
                Carbon::parse($request->startdate)->translatedFormat('d M Y'),
                Carbon::parse($request->enddate)->translatedFormat('d M Y'),
            ];
        }

        return ['Semua', 'Semua'];
    }

    /** Hasil UNION -> collection array asosiatif [transaksi, nominal, sumber, tanggal] */
    private function unionRowsAssoc($unionQuery): Collection
    {
        return DB::query()
            ->fromSub($unionQuery, 'laporan')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($row) => [
                'transaksi' => $row->transaksi,
                'nominal'   => (float) $row->nominal,
                'sumber'    => ucfirst($row->sumber), // Pengeluaran | Pembelian | Penjualan
                'tanggal'   => $this->formatTanggal($row->created_at),
            ]);
    }


    /* ======================================================================
     |  SUPPLIER
     * ====================================================================== */
    public function laporansuppliersExport(Request $request)
    {
        $query = Supplier::whereNull('deleted_at');
        $this->applyDateFilter($query, $request);

        $rows = $query->latest()->get()->map(fn($s) => [
            $s->nama,
            $s->kontak,
            $s->alamat,
            $this->formatTanggal($s->created_at),
        ]);

        return $this->downloadLaporan('suppliers', ['Nama', 'Kontak', 'Alamat', 'Tanggal'], $rows);
    }

    /* ======================================================================
     |  CUSTOMER
     * ====================================================================== */
    public function laporancustomersExport(Request $request)
    {
        $query = Customer::whereNull('deleted_at');
        $this->applyDateFilter($query, $request);

        $rows = $query->latest()->get()->map(fn($c) => [
            $c->nama,
            $c->kontak,
            $c->alamat,
            $this->formatTanggal($c->created_at),
        ]);

        return $this->downloadLaporan('customers', ['Nama', 'Kontak', 'Alamat', 'Tanggal'], $rows);
    }

    /* ======================================================================
     |  PENJUALAN
     * ====================================================================== */
    public function laporanpenjualansExport(Request $request)
    {
        $query = Penjualan::with('customer', 'details.produk')->whereNull('deleted_at');

        $this->applyDateFilter($query, $request);

        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        if ($request->filled('barang')) {
            $barang = $request->barang;
            $query->whereHas('details.produk', fn($q) => $q->where('id', $barang));
        }

        $rows = $query->latest()->get()->map(fn($p) => [
            $p->no_transaksi_penjualan,
            $p->customer?->nama ?? '-',
            $p->tanggal(),
            $p->details->map(
                fn($d) => '[' . $d->tipe . '] ' . ($d->produk?->nama_produk ?? '-')
            )->implode(', '),
        ]);

        return $this->downloadLaporan(
            'penjualans',
            ['No Transaksi', 'Customer', 'Tanggal', 'Detail'],
            $rows
        );
    }

    /* ======================================================================
     |  STOK
     * ====================================================================== */
    public function laporanstoksExport(Request $request)
    {
        $query = Stok::with(
            'produk',
            'pembelianDetail.pembelian.supplier',
            'penjualanDetail.penjualan.customer',
            'pengirimanDetail.pengiriman.customer'
        );

        $this->applyDateFilter($query, $request, 'stoks.created_at');

        if ($request->filled('sumber')) {
            if ($request->sumber == 1) {
                $query->whereHas('pembelianDetail.pembelian.supplier', fn($q) => $q->whereNotNull('id'));
            } elseif ($request->sumber == 2) {
                $query->whereHas('penjualanDetail.penjualan.customer', fn($q) => $q->whereNotNull('id'));
            } elseif ($request->sumber == 3) {
                $query->whereHas('pengirimanDetail.pengiriman.customer', fn($q) => $q->whereNotNull('id'));
            }
        }

        if ($request->filled('barang')) {
            $barang = $request->barang;
            $query->whereHas('produk', fn($q) => $q->where('id', $barang));
        }

        $rows = $query->orderByDesc('stoks.created_at')->get()->map(function ($stok) {
            $sumber = '-';
            $jenis  = '-';
            $harga  = '-';
            $relasi = '-';

            if ($stok->pembelian_detail_id) {
                $detail = $stok->pembelianDetail;
                $sumber = 'Pembelian';
                $jenis  = $detail?->tipe_transaksi_pembelian ?? '-';
                $harga  = $detail?->harga_netto ?? '-';
                $relasi = $detail?->pembelian?->supplier?->nama ?? '-';
            } elseif ($stok->penjualan_detail_id) {
                $detail = $stok->penjualanDetail;
                $sumber = 'Penjualan';
                $jenis  = $detail?->tipe ?? '-';
                $harga  = $detail?->sub_total ?? '-';
                $relasi = $detail?->penjualan?->customer?->nama ?? '-';
            } elseif ($stok->pengiriman_detail_id) {
                $sumber = 'Pengiriman';
                $relasi = $stok->pengirimanDetail?->pengiriman?->customer?->nama ?? '-';
            }

            return [
                $stok->produk?->nama_produk ?? '-',
                $sumber,
                $stok->tipe_stok,
                $jenis,
                $stok->stok,
                is_numeric($harga) ? (float) $harga : $harga,
                $relasi,
                $stok->tanggal(),
            ];
        });

        return $this->downloadLaporan(
            'stoks',
            ['Produk', 'Sumber', 'Tipe Stok', 'Jenis Stok', 'Jumlah', 'Harga', 'Relasi', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  TITIPAN BARANG
     * ====================================================================== */
    public function laporantitipanbarangsExport(Request $request)
    {
        $query = StokTitipan::with('produk', 'supplier');

        $this->applyDateFilter($query, $request, 'stok_titipans.created_at');

        if ($request->filled('supplier')) {
            $supplier = $request->supplier;
            $query->whereHas('supplier', fn($q) => $q->where('id', $supplier));
        }

        if ($request->filled('barang')) {
            $barang = $request->barang;
            $query->whereHas('produk', fn($q) => $q->where('id', $barang));
        }

        $rows = $query->orderByDesc('stok_titipans.created_at')->get()->map(fn($row) => [
            $row->produk?->nama_produk ?? '-',
            $row->supplier?->nama ?? '-',
            $row->tipe_stok,
            $row->jumlah,
            $row->keterangan,
            $row->tanggal(),
        ]);

        return $this->downloadLaporan(
            'titipan-barangs',
            ['Produk', 'Supplier', 'Tipe Stok', 'Jumlah', 'Keterangan', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  BON SUPPLIER
     * ====================================================================== */
    public function laporanbonsuppliersExport(Request $request)
    {
        $query = CashbonSupplier::with('supplier')->whereNull('deleted_at');

        $this->applyDateFilter($query, $request, 'cashbon_suppliers.created_at');

        if ($request->filled('supplier')) {
            $supplier = $request->supplier;
            $query->whereHas('supplier', fn($q) => $q->where('id', $supplier));
        }

        $rows = $query->orderByDesc('cashbon_suppliers.created_at')->get()->map(fn($row) => [
            $row->supplier?->nama ?? '-',
            (float) $row->nominal_cashbon,
            $row->keterangan,
            $this->formatTanggal($row->created_at),
        ]);

        return $this->downloadLaporan(
            'bon-suppliers',
            ['Supplier', 'Nominal Cashbon', 'Keterangan', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  RITAN (PENGIRIMAN)
     * ====================================================================== */
    public function laporanritansExport(Request $request)
    {
        $query = Pengiriman::with('customer', 'details.produk')->whereNull('deleted_at');

        $this->applyDateFilter($query, $request, 'pengirimans.created_at');

        // CATATAN: di versi ajax filter-nya memakai relasi 'supplier', padahal
        // Pengiriman berelasi ke customer. Di sini memakai 'customer'.
        // Sesuaikan nama parameter dengan yang dikirim oleh view ritans.
        if ($request->filled('customer')) {
            $customer = $request->customer;
            $query->whereHas('customer', fn($q) => $q->where('id', $customer));
        }

        if ($request->filled('barang')) {
            $barang = $request->barang;
            $query->whereHas('details.produk', fn($q) => $q->where('id', $barang));
        }

        $rows = $query->orderByDesc('pengirimans.created_at')->get()->map(fn($ritan) => [
            $ritan->no_transaksi,
            $ritan->customer?->nama ?? '-',
            $ritan->nopol,
            $ritan->details->map(fn($d) => $d->produk?->nama_produk ?? '-')->implode(', '),
            $this->formatTanggal($ritan->created_at),
        ]);

        return $this->downloadLaporan(
            'ritans',
            ['No Transaksi', 'Customer', 'Nopol', 'Detail', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  TITIPAN KE CUSTOMER
     * ====================================================================== */
    public function laporantitipankecustomersExport(Request $request)
    {
        $query = TitipSupplier::with('supplier');

        $this->applyDateFilter($query, $request);

        $rows = $query->latest()->get()->map(fn($row) => [
            $row->supplier?->nama ?? '-',
            (float) $row->nominal_titip,
            $row->keterangan,
            $this->formatTanggal($row->created_at),
        ]);

        return $this->downloadLaporan(
            'titipan-ke-customers',
            ['Supplier', 'Nominal Titip', 'Keterangan', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  TRANSAKSI TUNAI
     * ====================================================================== */
    public function laporantransaksitunaiExport(Request $request)
    {
        $pengeluaran = Pengeluaran::where('metode_pembayaran', 'Tunai')
            ->select(['id', 'nama_pengeluaran as transaksi', 'nominal', 'created_at'])
            ->selectRaw("'pengeluaran' as sumber");

        $pembelian = Pembelian::whereNotNull('ambil_tunai')
            ->where('ambil_tunai', '>', 0)
            ->select(['id', 'no_transaksi as transaksi', 'ambil_tunai as nominal', 'created_at'])
            ->selectRaw("'pembelian' as sumber");

        $this->applyDateFilter($pengeluaran, $request);
        $this->applyDateFilter($pembelian, $request);

        $rows = $this->rowsFromUnion($pengeluaran->unionAll($pembelian));

        return $this->downloadLaporan(
            'transaksi-tunai',
            ['Transaksi', 'Nominal', 'Sumber', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  TRANSAKSI BANK
     * ====================================================================== */
    public function laporantransaksibanksExport(Request $request)
    {
        $pengeluaran = Pengeluaran::where('metode_pembayaran', 'Transfer')
            ->select(['id', 'nama_pengeluaran as transaksi', 'nominal', 'created_at'])
            ->selectRaw("'pengeluaran' as sumber");

        $pembelian = Pembelian::whereNotNull('ambil_transfer')
            ->where('ambil_transfer', '>', 0)
            ->select(['id', 'no_transaksi as transaksi', 'ambil_transfer as nominal', 'created_at'])
            ->selectRaw("'pembelian' as sumber");

        $this->applyDateFilter($pengeluaran, $request);
        $this->applyDateFilter($pembelian, $request);

        $rows = $this->rowsFromUnion($pengeluaran->unionAll($pembelian));

        return $this->downloadLaporan(
            'transaksi-bank',
            ['Transaksi', 'Nominal', 'Sumber', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  KASBON KARYAWAN
     * ====================================================================== */
    public function laporankasbonkaryawansExport(Request $request)
    {
        $query = CashbonKaryawan::with('karyawan')->whereNull('deleted_at');

        $this->applyDateFilter($query, $request, 'cashbon_karyawans.created_at');

        if ($request->filled('karyawan')) {
            $karyawan = $request->karyawan;
            $query->whereHas('karyawan', fn($q) => $q->where('id', $karyawan));
        }

        $rows = $query->orderByDesc('cashbon_karyawans.created_at')->get()->map(fn($row) => [
            $row->karyawan?->nama ?? '-',
            (float) $row->nominal_cashbon,
            $row->keterangan,
            $this->formatTanggal($row->created_at),
        ]);

        return $this->downloadLaporan(
            'kasbon-karyawans',
            ['Karyawan', 'Nominal Cashbon', 'Keterangan', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  TRANSAKSI PIHAK KETIGA
     * ====================================================================== */
    public function laporantransaksipihakketigasExport(Request $request)
    {
        $cashbon = CashbonPihak3::with('pihak3')
            ->select(['id', 'pihak3_id', 'nominal_cashbon as nominal', 'keterangan', 'created_at'])
            ->selectRaw("'cashbon' as sumber");

        $pembayaran = CashbonPihak3Pembayaran::select(
            ['id', 'pihak3_id', 'nominal_bayar as nominal', 'keterangan', 'created_at']
        )->selectRaw("'pembayaran' as sumber");

        $this->applyDateFilter($cashbon, $request);
        $this->applyDateFilter($pembayaran, $request);

        // Hasil UNION di-hydrate sebagai model CashbonPihak3, relasi pihak3
        // di-eager-load berdasarkan pihak3_id dari kedua sumber.
        $rows = $cashbon->unionAll($pembayaran)
            ->get()
            ->sortByDesc('created_at')
            ->map(fn($row) => [
                $row->pihak3?->nama ?? '-',
                ucfirst($row->sumber),
                (float) $row->nominal,
                $this->formatTanggal($row->created_at),
            ]);

        return $this->downloadLaporan(
            'transaksi-pihak-ketiga',
            ['Pihak 3', 'Sumber', 'Nominal', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  BIAYA
     * ====================================================================== */
    public function laporanbiayasExport(Request $request)
    {
        $pengeluaran = Pengeluaran::select(['id', 'nama_pengeluaran as transaksi', 'nominal', 'created_at'])
            ->selectRaw("'pengeluaran' as sumber");

        $pembelian = Pembelian::whereNotNull('total_nominal_terbayar')
            ->where('total_nominal_terbayar', '>', 0)
            ->select(['id', 'no_transaksi as transaksi', 'total_nominal_terbayar as nominal', 'created_at'])
            ->selectRaw("'pembelian' as sumber");

        $this->applyDateFilter($pengeluaran, $request);
        $this->applyDateFilter($pembelian, $request);

        $rows = $this->unionRowsAssoc($pengeluaran->unionAll($pembelian));

        $totalPengeluaran = (float) $rows->where('sumber', 'Pengeluaran')->sum('nominal');
        $totalPembelian   = (float) $rows->where('sumber', 'Pembelian')->sum('nominal');

        [$start, $end] = $this->periodeLabel($request);

        return Excel::download(
            new BiayaExport($rows, $start, $end, $totalPengeluaran, $totalPembelian),
            'biayas-' . date('Y-m-d') . '.xlsx'
        );
    }


    /* ======================================================================
     |  RUGI LABA
     * ====================================================================== */
    public function laporanrugilabasExport(Request $request)
    {
        $pengeluaran = Pengeluaran::select(['id', 'nama_pengeluaran as transaksi', 'nominal', 'created_at'])
            ->selectRaw("'pengeluaran' as sumber");

        $pembelian = Pembelian::whereNotNull('total_nominal_terbayar')
            ->where('total_nominal_terbayar', '>', 0)
            ->select(['id', 'no_transaksi as transaksi', 'total_nominal_terbayar as nominal', 'created_at'])
            ->selectRaw("'pembelian' as sumber");

        $penjualan = Penjualan::query()
            ->select([
                'penjualans.id as id',
                'penjualans.no_transaksi_penjualan as transaksi',
            ])
            ->selectSub(
                PenjualanDetail::query()
                    ->selectRaw('COALESCE(SUM(nominal_akhir), 0)')
                    ->whereColumn('penjualan_details.penjualan_id', 'penjualans.id'),
                'nominal'
            )
            ->selectRaw('penjualans.created_at as created_at')
            ->selectRaw("'penjualan' as sumber");

        $this->applyDateFilter($pengeluaran, $request);
        $this->applyDateFilter($pembelian, $request);
        $this->applyDateFilter($penjualan, $request, 'penjualans.created_at');

        $rows = $this->unionRowsAssoc(
            $pengeluaran->unionAll($pembelian)->unionAll($penjualan)
        );

        $totalPemasukan   = (float) $rows->where('sumber', 'Penjualan')->sum('nominal');
        $totalPengeluaran = (float) $rows->where('sumber', 'Pembelian')->sum('nominal')
            + (float) $rows->where('sumber', 'Pengeluaran')->sum('nominal');

        [$start, $end] = $this->periodeLabel($request);

        return Excel::download(
            new RugiLabaExport($rows, $start, $end, $totalPemasukan, $totalPengeluaran),
            'rugi-laba-' . date('Y-m-d') . '.xlsx'
        );
    }
}
