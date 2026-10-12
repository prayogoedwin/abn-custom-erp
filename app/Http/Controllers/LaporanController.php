<?php

namespace App\Http\Controllers;

use App\Exports\Laporan\PembelianExport;
use App\Models\CashbonKaryawan;
use App\Models\CashbonPihak3;
use App\Models\CashbonPihak3Pembayaran;
use App\Models\CashbonSupplier;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Karyawan;
use App\Models\KategoriPengeluaran;
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
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// 'active' => ['laporansuppliers*', 'laporancustomers*', 'laporanpembelians*', 'laporanpengirimans*', 'laporanpenjualans*', 'laporanstoks*', 'laporantitipanbarangs*', 'laporanbonsuppliers*', 'laporanritans*', 'laporantitipankecustomers*', 'laporantransaksikas*', 'laporantransaksibanks*', 'laporankasbonkaryawans*', 'laporantransaksipihakketigas*', 'laporanbiayas*', 'laporanrugilabas*'],
// 'permission' => ['view-laporan'],
class LaporanController extends Controller
{

    public function index()
    {

        return view('laporan.comingsoon');
    }

    public function laporansuppliers(Request $request)
    {
        if (request()->ajax()) {
            $suppliers = Supplier::whereNull('deleted_at');

            return DataTables::of($suppliers)
                ->editColumn('created_at', function ($supplier) {
                    return $supplier->created_at->translatedFormat('d M Y');
                })
                ->make(true);
        }

        return view('laporan.suppliers');
    }

    public function laporancustomers(Request $request)
    {
        if (request()->ajax()) {
            $customers = Customer::whereNull('deleted_at');

            return DataTables::of($customers)
                ->editColumn('created_at', function ($customer) {
                    return $customer->created_at->translatedFormat('d M Y');
                })
                ->make(true);
        }

        return view('laporan.customers');
    }
    public function laporanpembelians(Request $request)
    {
        if ($request->ajax()) {
            // dd('masuk ajax');




            $pembelians = Pembelian::with('supplier', 'details.produk')->whereNull('deleted_at');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $pembelians->whereBetween('created_at', [$start, $end]);
            }

            //filter supplier
            if ($request->filled('supplier')) {
                $supplier = $request->supplier;
                $pembelians->where('supplier_id', $supplier);
            }

            //filter barang
            if ($request->filled('barang')) {
                $barang = $request->barang;
                $pembelians->whereHas('details.produk', function ($q) use ($barang) {
                    $q->where('id', $barang);
                });
            }



            return DataTables::of($pembelians)
                ->editColumn('kekurangan', function ($pembelian) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($pembelian->kekurangan, 0, ',', '.');
                })
                ->addColumn('supplier', function ($pembelian) {
                    return $pembelian->supplier?->nama ?? '-';
                })
                ->editColumn('created_at', function ($pembelian) {
                    return $pembelian->created_at->translatedFormat('d M Y');
                })
                ->addColumn('detail', function ($pembelian) {
                    $produkNames = $pembelian->details->map(function ($detail) {
                        return   '[' . $detail->tipe_transaksi_pembelian . '] ' . ($detail->produk?->nama_produk ?? '-');
                    })->toArray();
                    $content = implode('<br>', $produkNames);
                    return '<div style="max-height: 100px; overflow-y: auto; white-space: nowrap;">' . $content . '</div>';
                })
                ->filterColumn('detail', function ($query, $keyword) {
                    $query->whereHas('details.produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })




                ->rawColumns(['detail'])
                ->make(true);
        }


        $suppliers = Supplier::all();
        $barangs = Produk::all();
        return view('laporan.pembelians', compact('suppliers', 'barangs'));
    }

    public function laporanpembeliansExport(Request $request)
    {
        $query = Pembelian::with('supplier', 'details.produk')->whereNull('deleted_at');

        //filter tanggal
        if ($request->filled(['startdate', 'enddate'])) {
            $start = Carbon::parse($request->startdate)->startOfDay();
            $end   = Carbon::parse($request->enddate)->endOfDay();

            $query->whereBetween('created_at', [$start, $end]);
        }

        //filter supplier
        if ($request->filled('supplier')) {
            $supplier = $request->supplier;
            $query->where('supplier_id', $supplier);
        }

        //filter barang
        if ($request->filled('barang')) {
            $barang = $request->barang;
            $query->whereHas('details.produk', function ($q) use ($barang) {
                $q->where('id', $barang);
            });
        }

        $data = $query->get();

        $data->transform(function ($pembelian) {
            $pembelian->produk_list = $pembelian->details->map(function ($detail) {
                return '[' . $detail->tipe_transaksi_pembelian . '] ' . ($detail->produk?->nama_produk ?? '-');
            })->implode(', ');
            return $pembelian;
        });

        $data->transform(function ($pembelian) {
            $pembelian->kekurangan = number_format($pembelian->kekurangan, 0, ',', '.');
            return $pembelian;
        });

        $data->transform(function ($pembelian) {
            $pembelian->created_at = $pembelian->created_at->translatedFormat('d M Y');
            return $pembelian;
        });

        $data->transform(function ($pembelian) {
            $pembelian->supplier = $pembelian->supplier?->nama ?? '-';
            return $pembelian;
        });
        // dd($data);

        return Excel::download(new PembelianExport($data), 'pembelians-' . date('Y-m-d') . '.xlsx');
    }

    public function laporanpenjualans(Request $request)
    {
        if ($request->ajax()) {
            // dd('masuk ajax');

            $penjualans = Penjualan::with('customer', 'details.produk')->whereNull('deleted_at');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();
                $penjualans->whereBetween('created_at', [$start, $end]);
            }
            //filter supplier
            if ($request->filled('customer')) {
                $customer = $request->customer;
                $penjualans->where('customer_id', $customer);
            }

            //filter barang
            if ($request->filled('barang')) {
                $barang = $request->barang;
                $penjualans->whereHas('details.produk', function ($q) use ($barang) {
                    $q->where('id', $barang);
                });
            }



            return DataTables::of($penjualans)
                ->editColumn('kekurangan', function ($penjualan) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($penjualan->kekurangan, 0, ',', '.');
                })
                ->addColumn('customer', function ($penjualan) {
                    return $penjualan->customer?->nama ?? '-';
                })
                ->editColumn('created_at', function ($penjualan) {
                    return $penjualan->tanggal();
                })
                ->addColumn('detail', function ($penjualan) {
                    $produkNames = $penjualan->details->map(function ($detail) {
                        return   '[' . $detail->tipe . '] ' . ($detail->produk?->nama_produk ?? '-');
                    })->toArray();
                    $content = implode('<br>', $produkNames);
                    return '<div style="max-height: 100px; overflow-y: auto; white-space: nowrap;">' . $content . '</div>';
                })
                ->filterColumn('detail', function ($query, $keyword) {
                    $query->whereHas('details.produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })




                ->rawColumns(['detail'])
                ->make(true);
        }


        $customers = Customer::all();
        $barangs = Produk::all();
        return view('laporan.penjualans', compact('customers', 'barangs'));
    }


    public function laporanstoks(Request $request)
    {
        if ($request->ajax()) {



            $stoks = Stok::with('produk', 'pembelianDetail.pembelian.supplier', 'penjualanDetail.penjualan.customer', 'pengirimanDetail.pengiriman.customer');


            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $stoks->whereBetween('stoks.created_at', [$start, $end]);
            }

            //filter sumber
            if ($request->filled('sumber')) {

                if ($request->sumber == 1) {
                    $stoks->whereHas('pembelianDetail.pembelian.supplier', function ($q) {
                        $q->whereNotNull('id');
                    });
                } elseif ($request->sumber == 2) {
                    $stoks->whereHas('penjualanDetail.penjualan.customer', function ($q) {
                        $q->whereNotNull('id');
                    });
                } elseif ($request->sumber == 3) {
                    $stoks->whereHas('pengirimanDetail.pengiriman.customer', function ($q) {
                        $q->whereNotNull('id');
                    });
                }
            }
            //filter barang
            if ($request->filled('barang')) {
                $barang = $request->barang;
                $stoks->whereHas('produk', function ($q) use ($barang) {
                    $q->where('id', $barang);
                });
            }
            return DataTables::of($stoks)
                ->filterColumn('produk.nama_produk', function ($query, $keyword) {
                    $query->whereHas('produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('jenis_stok', function ($stok) {
                    if ($stok->pembelian_detail_id) {
                        return $stok->pembelianDetail->tipe_transaksi_pembelian;
                    } elseif ($stok->penjualan_detail_id) {
                        return $stok->penjualanDetail->tipe;
                    } else {
                        return '-';
                    }
                })

                ->addColumn('sumber', function ($stok) {
                    if ($stok->pembelian_detail_id) {
                        return 'Pembelian';
                    } elseif ($stok->penjualan_detail_id) {
                        return 'Penjualan';
                    } elseif ($stok->pengiriman_detail_id) {
                        return 'Pengiriman';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('relasi', function ($stok) {
                    //referensi ke supplier jika beli. customer jika jual dan kirim
                    if ($stok->pembelian_detail_id) {
                        $pembelian = $stok->pembelianDetail->pembelian;
                        return $pembelian->supplier->nama;
                    } elseif ($stok->penjualan_detail_id) {
                        $penjualan = $stok->penjualanDetail->penjualan;
                        return $penjualan->customer?->nama ?? '-';
                    } elseif ($stok->pengiriman_detail_id) {
                        $pengiriman = $stok->pengirimanDetail->pengiriman;
                        return $pengiriman->customer?->nama ?? '-';
                    } else {
                        return '-';
                    }
                })
                ->addColumn('jumlah', function ($stok) {
                    //rata kanan
                    return '<div style="text-align: right;">' . $stok->stok . '</div>';
                })
                ->addColumn('harga', function ($stok) {
                    $harga = '-';
                    if ($stok->pembelian_detail_id) {
                        $harga = $stok->pembelianDetail->harga_netto;
                    } elseif ($stok->penjualan_detail_id) {
                        $harga = $stok->penjualanDetail->sub_total;
                    } elseif ($stok->pengiriman_detail_id) {
                        $harga = '-'; // Assuming pengiriman doesn't have a price associated
                    } else {
                        $harga = '-';
                    }
                    //format harga
                    if (is_numeric($harga)) {
                        $harga = number_format($harga, 0, ',', '.');
                    }
                    //rata kanan
                    return '<div style="text-align: right;">' . $harga . '</div>';
                })
                ->editColumn('created_at', function ($stok) {
                    return $stok->tanggal();
                })
                ->rawColumns(['harga', 'jumlah'])
                ->make(true);
        }



        $sumbers = collect([
            ['id' => 1, 'nama' => 'Pembelian'],
            ['id' => 2, 'nama' => 'Penjualan'],
            ['id' => 3, 'nama' => 'Pengiriman'],
        ])->map(fn($sumber) => (object) $sumber);
        $barangs = Produk::all();

        return view('laporan.stoks', compact('sumbers', 'barangs'));
    }
    public function laporantitipanbarangs(Request $request)
    {
        if ($request->ajax()) {

            $titipanbarangs = StokTitipan::with('produk', 'supplier');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $titipanbarangs->whereBetween('stok_titipans.created_at', [$start, $end]);
            }

            //filter supplier
            if ($request->filled('supplier')) {
                $supplier = $request->supplier;
                $titipanbarangs->whereHas('supplier', function ($q) use ($supplier) {
                    $q->where('id', $supplier);
                });
            }


            //filter barang
            if ($request->filled('barang')) {
                $barang = $request->barang;
                $titipanbarangs->whereHas('produk', function ($q) use ($barang) {
                    $q->where('id', $barang);
                });
            }

            return DataTables::of($titipanbarangs)
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

                ->editColumn('jumlah', function ($row) {
                    //rata kanan
                    return '<div style="text-align: right;">' . number_format($row->jumlah, 0, ',', '.') . '</div>';
                })


                ->editColumn('created_at', function ($row) {
                    return $row->tanggal(); // Format tanggal sesuai kebutuhan
                })
                ->rawColumns(['jumlah']) // Enable raw HTML for the 'jumlah' column
                ->make(true);
        }


        $supplier = Supplier::all();
        $barangs = Produk::all();
        return view('laporan.titipanbarangs', compact('supplier', 'barangs'));
    }
    public function laporanbonsuppliers(Request $request)
    {
        if (request()->ajax()) {
            $bonSuppliers = CashbonSupplier::with('supplier')->whereNull('deleted_at');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $bonSuppliers->whereBetween('cashbon_suppliers.created_at', [$start, $end]);
            }

            //filter supplier
            if ($request->filled('supplier')) {
                $supplier = $request->supplier;
                $bonSuppliers->whereHas('supplier', function ($q) use ($supplier) {
                    $q->where('id', $supplier);
                });
            }

            return DataTables::of($bonSuppliers)
                ->editColumn('nominal_cashbon', function ($cashbonsupplier) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    // rata kanan
                    return '<div style="text-align: right;">' . number_format($cashbonsupplier->nominal_cashbon, 0, ',', '.') . '</div>';
                })

                ->editColumn('created_at', function ($cashbonsupplier) {
                    return $cashbonsupplier->created_at->translatedFormat('d M Y');
                })
                ->rawColumns(['nominal_cashbon'])
                ->make(true);
        }

        $suppliers = Supplier::where('deleted_at', null)->get();

        return view('laporan.bonsuppliers', compact('suppliers'));
    }
    public function laporanritans(Request $request)
    {
        if (request()->ajax()) {
            $ritans = Pengiriman::whereNull('deleted_at')->with('customer');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $ritans->whereBetween('pengirimans.created_at', [$start, $end]);
            }

            //filter nopol
            if ($request->filled('nopol')) {
                $nopol = $request->nopol;
                $ritans->where('nopol', $nopol);
            }

            //filter barang
            if ($request->filled('barang')) {
                $barang = $request->barang;
                $ritans->whereHas('details.produk', function ($q) use ($barang) {
                    $q->where('id', $barang);
                });
            }

            return DataTables::of($ritans)
                ->addColumn('detail', function ($ritan) {
                    $produkNames = $ritan->details->map(function ($detail) {
                        return ($detail->produk?->nama_produk ?? '-');
                    })->toArray();
                    $content = implode('<br>', $produkNames);
                    return '<div style="max-height: 100px; overflow-y: auto; white-space: nowrap;">' . $content . '</div>';
                })
                ->filterColumn('detail', function ($query, $keyword) {
                    $query->whereHas('details.produk', function ($q) use ($keyword) {
                        $q->where('nama_produk', 'like', "%{$keyword}%");
                    });
                })

                ->editColumn('created_at', function ($ritan) {
                    return $ritan->created_at->translatedFormat('d M Y');
                })
                ->rawColumns(['detail'])
                ->make(true);
        }

        $nopols = Pengiriman::where('deleted_at', null)->pluck('nopol')->unique();
        $barangs = Produk::where('deleted_at', null)->get();
        return view('laporan.ritans', compact('nopols', 'barangs'));
    }
    public function laporantitipankecustomers(Request $request)
    {
        if ($request->ajax()) {
            $query = PenjualanDetail::with([
                'penjualan.customer',
                'produk'
            ])
                ->where('tipe', 'Titip')
                ->whereHas('penjualan', function ($q) {
                    $q->whereNull('deleted_at');
                });

            // Filter tanggal berdasarkan tanggal penjualan
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $query->whereHas('penjualan', function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                });
            }

            //filter supplier
            if ($request->filled('customer')) {
                $customer = $request->customer;
                $query->whereHas('penjualan', function ($q) use ($customer) {
                    $q->where('customer_id', $customer);
                });
            }

            return datatables()->of($query)
                ->editColumn('created_at', function ($row) {
                    return $row->penjualan->created_at
                        ->translatedFormat('d M Y');
                })
                ->addColumn('customer', function ($row) {
                    return $row->penjualan->customer->nama ?? '-';
                })
                ->addColumn('barangs', function ($row) {
                    return $row->produk?->nama_produk ?? '-';
                })
                ->addColumn('rendeman', function ($row) {
                    // allign right
                    return $row->rendeman !== null
                        ? '<div style="text-align: right;">' . $row->rendeman . '%</div>'
                        : '<div style="text-align: right;">-</div>';
                })
                ->addColumn('netto', function ($row) {
                    return '<div style="text-align: right;">' . ($row->netto ?? '-') . ' '
                        . ($row->produk?->satuan ?? '') . '</div>';
                })
                ->rawColumns(['rendeman', 'netto'])
                ->make(true);
        }

        $customers = Customer::whereNull('deleted_at')->get();
        return view('laporan.titipankecustomers', compact('customers'));
    }

    public function laporantransaksitunai(Request $request)
    {
        if ($request->ajax()) {

            //ambil data Tunai dari table pengeluaran dan pembelian
            $pengeluaran = Pengeluaran::leftJoin(
                'kategori_pengeluarans',
                'pengeluarans.kategori_pengeluaran_id',
                '=',
                'kategori_pengeluarans.id'
            )
                ->where('pengeluarans.metode_pembayaran', 'Tunai')
                ->select([
                    'pengeluarans.id',
                    'pengeluarans.nama_pengeluaran as transaksi',
                    'pengeluarans.nominal',
                    'pengeluarans.created_at',
                    'kategori_pengeluarans.nama_kategori as kategori',
                ])
                ->selectRaw("'pengeluaran' as sumber");


            $pembelian = Pembelian::whereNotNull('ambil_tunai')
                ->where('ambil_tunai', '>', 0)
                ->select([
                    'id',
                    'no_transaksi as transaksi',
                    'ambil_tunai as nominal',
                    'created_at',
                ])
                ->selectRaw("' ' as kategori")
                ->selectRaw("'pembelian' as sumber");
                

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $pembelian->whereBetween('created_at', [$start, $end]);
                $pengeluaran->whereBetween('created_at', [$start, $end]);
            }

            //filter sumber
            if ($request->filled('sumber')) {
                $sumber = $request->sumber;
                $pengeluaran->whereRaw("'pengeluaran' = ?", [$sumber]);
                $pembelian->whereRaw("'pembelian' = ?", [$sumber]);
            }

            //filter kategori
            if ($request->filled('kategori')) {
                $kategori = $request->kategori;
                $pengeluaran->where('pengeluarans.kategori_pengeluaran_id', $kategori);

                // kosongkan pembelian
                $pembelian = $pembelian->whereRaw('1 = 0');
            }

            $query = $pengeluaran->unionAll($pembelian);

            // dd($query->get());

            $totalNominal = $query->sum('nominal');



            return DataTables::of($query)
                ->editColumn('nominal', function ($row) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    // rata kanan
                    return '<div style="text-align: right;">' . number_format($row->nominal, 0, ',', '.') . '</div>';
                })

                ->filterColumn('transaksi', function ($query, $keyword) {
                    $query->where('transaksi', 'like', "%{$keyword}%");
                })

                ->filterColumn('sumber', function ($query, $keyword) {
                    $query->where('sumber', 'like', "%{$keyword}%");
                })


                ->editColumn('created_at', function ($row) {
                    return $row->created_at->translatedFormat('d M Y');
                })
                ->with([
                    'total_nominal' => $totalNominal,
                ])
                ->rawColumns(['nominal'])
                ->make(true);
        }

        $kategoris = KategoriPengeluaran::where('deleted_at', null)->get();
        $sumbers = collect([
            (object)['id' => 'pembelian', 'nama' => 'Pembelian'],
            (object)['id' => 'pengeluaran', 'nama' => 'Pengeluaran'],
        ])->values();

        // dd($kategoris, $sumbers);

        return view('laporan.transaksitunai', compact('kategoris', 'sumbers'));
    }
    public function laporantransaksibanks(Request $request)
    {
        if ($request->ajax()) {

            //ambil data Tunai dari table pengeluaran dan pembelian
            $pengeluaran = Pengeluaran::leftJoin(
                'kategori_pengeluarans',
                'pengeluarans.kategori_pengeluaran_id',
                '=',
                'kategori_pengeluarans.id'
            )
                ->where('pengeluarans.metode_pembayaran', 'Transfer')
                ->select([
                    'pengeluarans.id',
                    'pengeluarans.nama_pengeluaran as transaksi',
                    'pengeluarans.nominal',
                    'pengeluarans.created_at',
                    'kategori_pengeluarans.nama_kategori as kategori',
                ])
                ->selectRaw("'pengeluaran' as sumber");


            $pembelian = Pembelian::whereNotNull('ambil_transfer')
                ->where('ambil_transfer', '>', 0)
                ->select([
                    'id',
                    'no_transaksi as transaksi',
                    'ambil_transfer as nominal',
                    'created_at',
                ])
                ->selectRaw("' ' as kategori")
                ->selectRaw("'pembelian' as sumber");
                

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $pembelian->whereBetween('created_at', [$start, $end]);
                $pengeluaran->whereBetween('created_at', [$start, $end]);
            }

            //filter sumber
            if ($request->filled('sumber')) {
                $sumber = $request->sumber;
                $pengeluaran->whereRaw("'pengeluaran' = ?", [$sumber]);
                $pembelian->whereRaw("'pembelian' = ?", [$sumber]);
            }

            //filter kategori
            if ($request->filled('kategori')) {
                $kategori = $request->kategori;
                $pengeluaran->where('pengeluarans.kategori_pengeluaran_id', $kategori);

                // kosongkan pembelian
                $pembelian = $pembelian->whereRaw('1 = 0');
            }

            $query = $pengeluaran->unionAll($pembelian);

            // dd($query->get());

            $totalNominal = $query->sum('nominal');



            return DataTables::of($query)
                ->editColumn('nominal', function ($row) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    // rata kanan
                    return '<div style="text-align: right;">' . number_format($row->nominal, 0, ',', '.') . '</div>';
                })

                ->filterColumn('transaksi', function ($query, $keyword) {
                    $query->where('transaksi', 'like', "%{$keyword}%");
                })

                ->filterColumn('sumber', function ($query, $keyword) {
                    $query->where('sumber', 'like', "%{$keyword}%");
                })


                ->editColumn('created_at', function ($row) {
                    return $row->created_at->translatedFormat('d M Y');
                })
                ->with([
                    'total_nominal' => $totalNominal,
                ])
                ->rawColumns(['nominal'])
                ->make(true);
        }

        $kategoris = KategoriPengeluaran::where('deleted_at', null)->get();
        $sumbers = collect([
            (object)['id' => 'pembelian', 'nama' => 'Pembelian'],
            (object)['id' => 'pengeluaran', 'nama' => 'Pengeluaran'],
        ])->values();

        // dd($kategoris, $sumbers);

        return view('laporan.transaksibanks', compact('kategoris', 'sumbers'));
    }
    public function laporankasbonkaryawans(Request $request)
    {
        if ($request->ajax()) {
            // dd('masuk ajax');

            $kasbonKaryawans = CashbonKaryawan::with('karyawan')->whereNull('deleted_at');

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $kasbonKaryawans->whereBetween('cashbon_karyawans.created_at', [$start, $end]);
            }

            //filter karyawan
            if ($request->filled('karyawan')) {
                $karyawan = $request->karyawan;
                $kasbonKaryawans->whereHas('karyawan', function ($q) use ($karyawan) {
                    $q->where('id', $karyawan);
                });
            }

            return DataTables::of($kasbonKaryawans)
                ->editColumn('nominal_cashbon', function ($cashbonkaryawan) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    // rata kanan
                    return '<div style="text-align: right;">' . number_format($cashbonkaryawan->nominal_cashbon, 0, ',', '.') . '</div>';
                })

                ->editColumn('created_at', function ($cashbonkaryawan) {
                    return $cashbonkaryawan->created_at->translatedFormat('d M Y');
                })
                ->rawColumns(['nominal_cashbon'])
                ->make(true);
        }

        $karyawans = Karyawan::where('deleted_at', null)->get();
        return view('laporan.kasbonkaryawans', compact('karyawans'));
    }
    public function laporantransaksipihakketigas(Request $request)
    {
        if ($request->ajax()) {

            $cashbon = CashbonPihak3::with('pihak3')
                ->select([
                    'id',
                    'pihak3_id',
                    'nominal_cashbon as nominal',
                    'keterangan',
                    'created_at',
                ])
                ->selectRaw("'cashbon' as sumber");

            $pembayaran = CashbonPihak3Pembayaran::with('pihak3')
                ->select([
                    'id',
                    'pihak3_id',

                    'nominal_bayar as nominal',
                    'keterangan',
                    'created_at',
                ])
                ->selectRaw("'pembayaran' as sumber");


            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $cashbon->whereBetween('created_at', [$start, $end]);
                $pembayaran->whereBetween('created_at', [$start, $end]);
            }

            $query = $cashbon->unionAll($pembayaran);

            return DataTables::of($query)
                ->editColumn('nominal', function ($row) {
                    return '<div style="text-align: right;">' . number_format($row->nominal, 0, ',', '.') . '</div>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->translatedFormat('d M Y');
                })
                ->rawColumns(['nominal'])
                ->make(true);
        }

        return view('laporan.transaksipihakketigas');
    }
    public function laporanbiayas(Request $request)
    {
        if ($request->ajax()) {
            //ambil data Tunai dari table pengeluaran dan pembelian
            $pengeluaran = Pengeluaran::leftJoin(
                'kategori_pengeluarans',
                'pengeluarans.kategori_pengeluaran_id',
                '=',
                'kategori_pengeluarans.id'
            )
                
                ->select([
                    'pengeluarans.id',
                    'pengeluarans.nama_pengeluaran as transaksi',
                    'pengeluarans.nominal',
                    'pengeluarans.created_at',
                    'kategori_pengeluarans.nama_kategori as kategori',
                ])
                ->selectRaw("'pengeluaran' as sumber");


            $pembelian = Pembelian::whereNotNull('total_nominal_terbayar')
                ->where('total_nominal_terbayar', '>', 0)
                ->select([
                    'id',
                    'no_transaksi as transaksi',
                    'total_nominal_terbayar as nominal',
                    'created_at',
                ])
                ->selectRaw("' ' as kategori")
                ->selectRaw("'pembelian' as sumber");

            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $pembelian->whereBetween('created_at', [$start, $end]);
                $pengeluaran->whereBetween('created_at', [$start, $end]);
            }

            //filter sumber
            if ($request->filled('sumber')) {
                $sumber = $request->sumber;
                $pengeluaran->whereRaw("'pengeluaran' = ?", [$sumber]);
                $pembelian->whereRaw("'pembelian' = ?", [$sumber]);
            }

            //filter kategori
            if ($request->filled('kategori')) {
                $kategori = $request->kategori;
                $pengeluaran->where('pengeluarans.kategori_pengeluaran_id', $kategori);

                // kosongkan pembelian
                $pembelian = $pembelian->whereRaw('1 = 0');
            }

            $totalNominalFromPengeluaran = $pengeluaran->sum('nominal');
            $totalNominalFromPembelian = $pembelian->sum('total_nominal_terbayar');

            $query = $pengeluaran->unionAll($pembelian);


            $totalNominal = $totalNominalFromPengeluaran + $totalNominalFromPembelian;


            return DataTables::of($query)
                ->editColumn('nominal', function ($row) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    // rata kanan
                    return '<div style="text-align: right;">' . number_format($row->nominal, 0, ',', '.') . '</div>';
                })

                ->editColumn('created_at', function ($row) {
                    return $row->created_at->translatedFormat('d M Y');
                })
                ->rawColumns(['nominal'])
                ->with([
                    'total_nominal' => $totalNominal,
                    'total_nominal_pengeluaran' => $totalNominalFromPengeluaran,
                    'total_nominal_pembelian' => $totalNominalFromPembelian,
                ])

                ->make(true);
        }

        $kategoris = KategoriPengeluaran::where('deleted_at', null)->get();
        $sumbers = collect([
            (object)['id' => 'pembelian', 'nama' => 'Pembelian'],
            (object)['id' => 'pengeluaran', 'nama' => 'Pengeluaran'],
        ])->values();

        return view('laporan.biayas', compact('kategoris', 'sumbers'));
    }
    public function laporanrugilabas(Request $request)
    {


        if ($request->ajax()) {
            //ambil data Tunai dari table pengeluaran dan pembelian
            $pengeluaran = Pengeluaran::select([
                'id',
                'nama_pengeluaran as transaksi',
                'nominal',
                'created_at',
            ])
                ->selectRaw("'pengeluaran' as sumber");


            $pembelian = Pembelian::whereNotNull('total_nominal_terbayar')
                ->where('total_nominal_terbayar', '>', 0)
                ->select([
                    'id',
                    'no_transaksi as transaksi',
                    'total_nominal_terbayar as nominal',
                    'created_at',
                ])

                ->selectRaw("'pembelian' as sumber");

            $penjualan = Penjualan::query()
                ->select([
                    'penjualans.id as id',
                    'penjualans.no_transaksi_penjualan as transaksi',
                ])
                ->selectSub(
                    PenjualanDetail::query()
                        ->selectRaw('COALESCE(SUM(nominal_akhir), 0)')
                        ->whereColumn(
                            'penjualan_details.penjualan_id',
                            'penjualans.id'
                        ),
                    'nominal'
                )
                ->selectRaw('penjualans.created_at as created_at')
                ->selectRaw("'penjualan' as sumber");



            //filter tanggal
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();

                $pembelian->whereBetween('created_at', [$start, $end]);
                $pengeluaran->whereBetween('created_at', [$start, $end]);
                $penjualan->whereBetween('created_at', [$start, $end]);
            }

            $query = $pengeluaran->unionAll($pembelian)->unionAll($penjualan);

            // Bungkus UNION sebagai subquery




            $totals = DB::query()
                ->fromSub($query, 'laporan')
                ->selectRaw('sumber, SUM(nominal) as total')
                ->groupBy('sumber')
                ->pluck('total', 'sumber');

            $query = DB::query()
                ->fromSub($query, 'laporan');

            $totalPenjualan   = (float) ($totals['penjualan'] ?? 0);
            $totalPembelian   = (float) ($totals['pembelian'] ?? 0);
            $totalPengeluaran = (float) ($totals['pengeluaran'] ?? 0);

            $totalNominal = $totalPenjualan - $totalPembelian - $totalPengeluaran;


            return DataTables::of($query)
                ->filterColumn('transaksi', function ($query, $keyword) {
                    $query->where('transaksi', 'like', "%{$keyword}%");
                })
                ->filterColumn('sumber', function ($query, $keyword) {
                    $query->where('sumber', 'like', "%{$keyword}%");
                })
                ->editColumn('nominal', function ($row) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    // rata kanan
                    return '<div style="text-align: right;">' . number_format((float) $row->nominal, 0, ',', '.') . '</div>';
                })

                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('d M Y');
                })
                ->rawColumns(['nominal'])
                ->with([
                    'total_nominal' => $totalNominal,
                    'total_nominal_pengeluaran' => $totalPengeluaran + $totalPembelian,
                    'total_nominal_pemasukan' => $totalPenjualan,
                ])

                ->make(true);
        }
        return view('laporan.rugilabas');
    }
}
