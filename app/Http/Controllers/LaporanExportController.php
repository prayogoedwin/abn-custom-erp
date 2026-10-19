<?php

namespace App\Http\Controllers;

use App\Exports\BonKaryawanExport;
use App\Exports\BonPihak3Export;
use App\Exports\BonSupplierExport;
use App\Exports\Laporan\BiayaExport;
use App\Exports\Laporan\RugiLabaExport;
use App\Exports\LaporanExport;
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

    private function downloadLaporan(string $nama, array $headings, mixed $rows, array $headerInfo = [], array $excludeTotal = [])
    {
        return Excel::download(
            new LaporanExport(collect($rows)->values(), $headings, $headerInfo, $excludeTotal),
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
     |  PEMBELIAN
     * ====================================================================== */

    public function laporanpembeliansExport(Request $request)
    {
        $query = Pembelian::with(['supplier', 'details.produk'])
            ->whereNull('deleted_at');

        // Filter tanggal
        if ($request->filled(['startdate', 'enddate'])) {
            $start = Carbon::parse($request->startdate)->startOfDay();
            $end = Carbon::parse($request->enddate)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        // Filter supplier
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        // Filter barang
        if ($request->filled('barang')) {
            $barang = $request->barang;

            $query->whereHas('details.produk', function ($q) use ($barang) {
                $q->where('id', $barang);
            });
        }

        $rows = $query->latest()->get()->map(function ($pembelian) {
            $detail = $pembelian->details->map(function ($d) {
                return '[' . ($d->tipe_transaksi_pembelian ?? '-') . '] '
                    . ($d->produk?->nama_produk ?? '-')
                    . ' (' . ($d->netto ?? 0)
                    . ' ' . ($d->produk?->satuan ?? '-') . ')';
            })->implode("\n");

            // Total netto (angka murni) + satuan
            $totalNetto = (float) $pembelian->details->sum(fn($d) => (float) ($d->netto ?? 0));
            $satuan = $pembelian->details
                ->map(fn($d) => $d->produk?->satuan)
                ->filter()
                ->unique()
                ->implode(', ');

            return [
                'no_transaksi' => $pembelian->no_transaksi,
                'supplier'     => $pembelian->supplier?->nama ?? '-',
                'created_at'   => $pembelian->created_at
                    ? $pembelian->created_at->translatedFormat('d M Y')
                    : '-',
                'detail'       => $detail,
                'total_netto'  => $totalNetto,
                'satuan'       => $satuan !== '' ? $satuan : '-',
                'total_nominal_pembelian' => (float) $pembelian->total_nominal_pembelian,
                'total_nominal_terbayar'  => (float) $pembelian->total_nominal_terbayar,
                'kekurangan'              => (float) $pembelian->kekurangan,
                'status_pembayaran'       => $pembelian->status_pembayaran,
            ];
        });

        return $this->downloadLaporan(
            'laporan-pembelian',
            [
                'No Transaksi',
                'Supplier',
                'Tanggal',
                'Detail',
                'Total Netto',
                'Satuan',
                'Total Pembelian',
                'Total Terbayar',
                'Kekurangan',
                'Status Pembayaran',
            ],
            $rows
        );
    }

    /* ======================================================================
     |  PENJUALAN
     * ====================================================================== */
    public function laporanpenjualansExport(Request $request)
    {
        $query = Penjualan::with(['customer', 'details.produk'])
            ->whereNull('deleted_at');

        // Filter tanggal
        if ($request->filled(['startdate', 'enddate'])) {
            $start = Carbon::parse($request->startdate)->startOfDay();
            $end = Carbon::parse($request->enddate)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        // Filter customer
        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        // Filter barang
        if ($request->filled('barang')) {
            $barang = $request->barang;

            $query->whereHas('details.produk', function ($q) use ($barang) {
                $q->where('id', $barang);
            });
        }

        $rows = $query->latest()->get()->map(function ($penjualan) {
            $detail = $penjualan->details->map(function ($d) {
                return '[' . ($d->tipe ?? '-') . '] '
                    . ($d->produk?->nama_produk ?? '-')
                    . ' (' . ($d->netto ?? 0)
                    . ' ' . ($d->produk?->satuan ?? '-') . ')';
            })->implode("\n");

            // Total netto (angka murni) + satuan
            $totalNetto = (float) $penjualan->details->sum(fn($d) => (float) ($d->netto ?? 0));
            $satuan = $penjualan->details
                ->map(fn($d) => $d->produk?->satuan)
                ->filter()
                ->unique()
                ->implode(', ');

            return [
                'no_transaksi_penjualan' => $penjualan->no_transaksi_penjualan,
                'customer'   => $penjualan->customer?->nama ?? '-',
                'created_at' => $penjualan->tanggal(),
                'detail'     => $detail,
                'total_netto' => $totalNetto,
                'satuan'     => $satuan !== '' ? $satuan : '-',
                'jumlah'     => (float) $penjualan->invoiceJumlah(),
                'ppn'        => (float) $penjualan->invoicePpn(),
                'pph'        => (float) $penjualan->invoicePph22(),
                'total'      => (float) $penjualan->invoiceTotalDibayar(),
            ];
        });

        return $this->downloadLaporan(
            'laporan-penjualan',
            [
                'No Transaksi',
                'Customer',
                'Tanggal',
                'Detail',
                'Total Netto',
                'Satuan',
                'Jumlah',
                'PPN',
                'PPh 22',
                'Total Dibayar',
            ],
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
        // dd($request->all());
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'startdate' => 'nullable|date|required_with:enddate',
            'enddate' => 'nullable|date|after_or_equal:startdate|required_with:startdate',
        ]);
        // dd($request->all());

        return Excel::download(
            new BonSupplierExport(
                $request->supplier_id,
                $request->startdate,
                $request->enddate
            ),
            'laporan-bon-suppliers.xlsx'
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

    public function laporanselisihnettoExport(Request $request)
    {
        $query = PenjualanDetail::with('penjualan.customer', 'produk', 'pengiriman_detail.pengiriman')->whereNull('deleted_at');

        $this->applyDateFilter($query, $request, 'penjualan_details.created_at');

        //filter customer
        if ($request->filled('customer')) {
            $customer = $request->customer;
            $query->whereHas('penjualan.customer', fn($q) => $q->where('id', $customer));
        }

        //filter barang
        if ($request->filled('barang')) {
            $barang = $request->barang;
            $query->whereHas('produk', fn($q) => $q->where('id', $barang));
        }

        $rows = $query->latest()->get()->map(function ($row) {
            $netto            = $row->netto !== null ? (float) $row->netto : null;
            $nettoPengiriman  = $row->netto_pengiriman !== null ? (float) $row->netto_pengiriman : null;

            return [
                $row->penjualan->no_transaksi_penjualan,
                $row->penjualan->customer?->nama ?? '-',
                $row->pengiriman_detail->pengiriman->nopol,
                $row->produk?->nama_produk ?? '-',
                $row->produk?->satuan ?? '-',
                $row->rendeman !== null ? $row->rendeman . '%' : '-',
                $nettoPengiriman ?? '-',
                $netto ?? '-',
                ($nettoPengiriman !== null && $netto !== null) ? ($nettoPengiriman - $netto) : '-',
                $this->formatTanggal($row->created_at),
            ];
        });

        return $this->downloadLaporan(
            'selisih-netto',
            ['No Transaksi', 'Customer', 'Nopol', 'Barang', 'Satuan', 'Rendeman', 'Netto Pengiriman', 'Netto', 'Selisih', 'Tanggal'],
            $rows
        );
    }

    /* ======================================================================
     |  TITIPAN KE CUSTOMER
     * ====================================================================== */
    public function laporantitipankecustomersExport(Request $request)
    {
        $query = PenjualanDetail::with('penjualan.customer', 'produk')->where('tipe', 'Titip')->whereHas('penjualan', fn($q) => $q->whereNull('deleted_at'));

        $this->applyDateFilter($query, $request, 'penjualan_details.created_at');
        //filter customer
        if ($request->filled('customer')) {
            $customer = $request->customer;
            $query->whereHas('penjualan.customer', fn($q) => $q->where('id', $customer));
        }

        $rows = $query->latest()->get()->map(fn($row) => [
            $row->penjualan->no_transaksi_penjualan,
            $row->penjualan->customer?->nama ?? '-',
            $row->produk?->nama_produk ?? '-',
            $row->produk?->satuan ?? '-',
            $row->rendeman !== null ? $row->rendeman . '%' : '-',
            $row->netto !== null ? (float) $row->netto : '-',
            $this->formatTanggal($row->created_at),
        ]);

        return $this->downloadLaporan(
            'titipan-ke-customers',
            ['No Transaksi', 'Customer', 'Barang', 'Satuan', 'Rendeman', 'Netto', 'Tanggal'],
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

        $totalNominal = 0;
        foreach ($rows as $row) {
            $totalNominal += $row[1];
        }

        return $this->downloadLaporan(
            'transaksi-tunai',
            ['Transaksi', 'Nominal', 'Sumber', 'Tanggal'],
            $rows,
            [
                'Total Nominal' => number_format($totalNominal, 0, ',', '.'),
            ]
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

        $totalNominal = 0;
        foreach ($rows as $row) {
            $totalNominal += $row[1];
        }

        // dd($totalNominal);

        return $this->downloadLaporan(
            'transaksi-bank',
            ['Transaksi', 'Nominal', 'Sumber', 'Tanggal'],
            $rows,
            [
                'Total Nominal' => number_format($totalNominal, 0, ',', '.'),
            ]
        );
    }

    /* ======================================================================
     |  KASBON KARYAWAN
     * ====================================================================== */
    public function laporankasbonkaryawansExport(Request $request)
    {
        $request->validate([
            'karyawan' => 'required|exists:karyawans,id',
            'startdate' => 'nullable|date|required_with:enddate',
            'enddate' => 'nullable|date|after_or_equal:startdate|required_with:startdate',
        ]);

        return Excel::download(
            new BonKaryawanExport(
                $request->karyawan,
                $request->startdate,
                $request->enddate
            ),
            'laporan-bon-karyawans.xlsx'
        );
    }

    /* ======================================================================
     |  TRANSAKSI PIHAK KETIGA
     * ====================================================================== */
    public function laporantransaksipihakketigasExport(Request $request)
    {
        $request->validate([
            'pihak3' => 'required|exists:pihak3s,id',
            'startdate' => 'nullable|date|required_with:enddate',
            'enddate' => 'nullable|date|after_or_equal:startdate|required_with:startdate',
        ]);

        return Excel::download(
            new BonPihak3Export(
                $request->pihak3,
                $request->startdate,
                $request->enddate
            ),
            'laporan-bon-pihak3.xlsx'
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

        $totalPemasukan = (float) $rows
            ->where('sumber', 'penjualan')
            ->sum('nominal');

        $totalPengeluaran = (float) $rows
            ->where('sumber', 'pembelian')
            ->sum('nominal')
            + (float) $rows
                ->where('sumber', 'pengeluaran')
                ->sum('nominal');

        [$start, $end] = $this->periodeLabel($request);

        return Excel::download(
            new RugiLabaExport($rows, $start, $end, $totalPemasukan, $totalPengeluaran),
            'rugi-laba-' . date('Y-m-d') . '.xlsx'
        );
    }
}
