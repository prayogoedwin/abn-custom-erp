<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\Stok;
use App\Models\StokTitipan;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Supplier;

// 'active' => ['laporansuppliers*', 'laporancustomers*', 'laporanpembelians*', 'laporanpengirimans*', 'laporanpenjualans*', 'laporanstoks*', 'laporantitipanbarangs*', 'laporanbonsuppliers*', 'laporanritans*', 'laporantitipankecustomers*', 'laporantransaksikas*', 'laporantransaksibanks*', 'laporankasbonkaryawans*', 'laporantransaksipihakketigas*', 'laporanbiayas*', 'laporanrugilabas*'],
// 'permission' => ['view-laporan'],
class LaporanController extends Controller
{
    private function toIntMoney(mixed $value): int
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

    public function index()
    {

        return view('laporan.comingsoon');
    }

    public function laporansuppliers()
    {
        return view('laporan.comingsoon');
    }

    public function laporancustomers()
    {
        return view('laporan.comingsoon');
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
    public function laporanpengirimans()
    {
        return view('laporan.comingsoon');
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
    public function laporanbonsuppliers()
    {
        return view('laporan.comingsoon');
    }
    public function laporanritans()
    {
        return view('laporan.comingsoon');
    }
    public function laporantitipankecustomers()
    {
        return view('laporan.comingsoon');
    }
    public function laporantransaksikas()
    {
        return view('laporan.comingsoon');
    }
    public function laporantransaksibanks()
    {
        return view('laporan.comingsoon');
    }
    public function laporankasbonkaryawans()
    {
        return view('laporan.comingsoon');
    }
    public function laporantransaksipihakketigas()
    {
        return view('laporan.comingsoon');
    }
    public function laporanbiayas()
    {
        return view('laporan.comingsoon');
    }
    public function laporanrugilabas()
    {
        return view('laporan.comingsoon');
    }
}
