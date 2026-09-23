<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
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
            if ($request->filled(['startdate', 'enddate'])) {
                $start = Carbon::parse($request->startdate)->startOfDay();
                $end   = Carbon::parse($request->enddate)->endOfDay();
            } else {
                $start = Carbon::now()->startOfDay();
                $end   = Carbon::now()->endOfDay();
            }
            $pembelians = Pembelian::with('supplier')->whereNull('deleted_at')
                ->whereBetween('created_at', [$start, $end]);

            return DataTables::of($pembelians)
                ->editColumn('kekurangan', function ($pembelian) {
                    // Formats to: Rp 1.500.000 (0 decimals)
                    return number_format($pembelian->kekurangan, 0, ',', '.');
                })
                ->addColumn('supplier', function ($pembelian) {
                    return $pembelian->supplier?->nama ?? '-';
                })
                ->addColumn('tanggal', function ($pembelian) {
                    return $pembelian->tanggal();
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

        $startdate = $request->startdate ?? Carbon::now()->toDateString();
        $enddate = $request->enddate ?? Carbon::now()->toDateString();
        $suppliers = Supplier::all();
        return view('laporan.pembelians', compact('startdate', 'enddate', 'suppliers'));
    }
    public function laporanpengirimans()
    {
        return view('laporan.comingsoon');
    }
    public function laporanpenjualans()
    {
        return view('laporan.comingsoon');
    }
    public function laporanstoks()
    {
        return view('laporan.comingsoon');
    }
    public function laporantitipanbarangs()
    {
        return view('laporan.comingsoon');
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
