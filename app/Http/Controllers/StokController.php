<?php

namespace App\Http\Controllers;
use App\Models\Produk;
use App\Exports\StokExport;
use App\Models\Stok;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    private function getPagedata()
    {
        $pagedata = [
            'title' => 'Stok',
            'tablename' => 'stoks',
            'tableaction' => true,
            'canCreate' => false,
            'columns' => [
                ['name' => 'nama_produk', 'value' => 'nama_produk', 'title' => 'Produk', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'tipe_stok', 'value' => 'tipe_stok', 'title' => 'Tipe Stok', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'Satuan', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'stok', 'value' => 'stok', 'title' => 'Stok', 'type' => 'number', 'inform' => true, 'intable' => true],
            ],
        ];

        return $pagedata;
    }

    public function index()
    {
        $produks = Produk::all();

        $pagedata = $this->getPagedata();

        return view('stoks.index', $pagedata, compact('produks'));
    }

    public function indexTable(Request $request)
    {
        if ($request->ajax()) {
            $stoks = Stok::with(['produk', 'pembelianDetail.pembelian.supplier', 'penjualanDetail.penjualan.customer', 'pengirimanDetail.pengiriman.customer'])
                ->orderBy('stoks.id', 'desc');

            // Apply filters based on request parameters
            if ($request->has('filterproduk') && !empty($request->filterproduk)) {
                $stoks->where('produk_id', $request->filterproduk);
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
                ->addColumn('relasi', function ($stok) {
                    //referensi ke supplier jika beli. customer jika jual dan kirim
                    if ($stok->pembelian_detail_id) {
                        $pembelian = $stok->pembelianDetail->pembelian;
                        return $pembelian->supplier->nama;
                    } elseif ($stok->penjualan_detail_id) {
                        $penjualan = $stok->penjualanDetail->penjualan;
                        return $penjualan->customer->nama;
                    } elseif ($stok->pengiriman_detail_id) {
                        $pengiriman = $stok->pengirimanDetail->pengiriman;
                        return $pengiriman->customer->nama;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('jumlah', function ($stok) {
                    return $stok->stok;
                })
                ->addColumn('harga', function ($stok) {
                    if ($stok->pembelian_detail_id) {
                        return $stok->pembelianDetail->harga_netto;
                    } elseif ($stok->penjualan_detail_id) {
                        return $stok->penjualanDetail->sub_total;
                    } elseif ($stok->pengiriman_detail_id) {
                        return '-'; // Assuming pengiriman doesn't have a price associated
                    } else {
                        return '-';
                    }
                })
                ->addColumn('tanggal', function ($row) {
                    return $row->created_at->format('d-m-Y'); // Format tanggal sesuai kebutuhan
                })
                ->addColumn('actions', function ($stok) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-produks')) {
                        $actions .= '<a href="' . route('stoks.show', $stok) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">Detail</a>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return response()->json(['message' => 'Invalid request'], 400);
    }

    public function export()
    {
        return Excel::download(new StokExport, 'stoks-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        Stok::create($validated);

        return to_route('stoks.index')->with('status', 'Stok created successfully.');
    }

    public function show(Stok $stok): View
    {
        $pagedata = $this->getPagedata();

        $data = $stok;
        $data->nama_produk = $stok->produk->nama_produk;

        // dd($data);


        return view('dynamiccrud.show', compact('data'), $pagedata);
    }

    public function edit(Stok $stok): View
    {
        $pagedata = $this->getPagedata();

        $data = $stok;

        return view('dynamiccrud.edit', compact('data'), $pagedata);
    }

    public function update(Request $request, Stok $stok): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
        ]);

        $stok->update($validated);

        return to_route('stoks.index')->with('status', 'Stok updated successfully.');
    }

    public function destroy(Stok $stok): RedirectResponse
    {
        $stok->delete();

        return to_route('stoks.index')->with('status', 'Stok deleted successfully.');
    }
}
