<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProdukExport;
use App\Models\HistoryHargaBasis;
use App\Models\Stok;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        // dd($request->headers->all());
        if ($request->ajax()) {
            // dd('masuk ajax');
            $produks = Produk::join('kategori_produks', 'produks.kategori_produk_id', '=', 'kategori_produks.id')
                ->select(
                    'produks.*',
                    'kategori_produks.nama as kategori'
                )
                ->get();
            // dd($produks);

            return DataTables::of($produks)
                // ->filterColumn('name', function ($query, $keyword) {
                //     $query->where('produks.nama_produk', 'like', "%{$keyword}%");
                // })
                // ->filterColumn('kategori', function ($query, $keyword) {
                //     $query->where('kategori_produks.nama', 'like', "%{$keyword}%");
                // })
                


                ->addColumn('actions', function ($produk) {
                    $actions = '';

                    if (auth()->user()->hasPermission('show-produks')) {
                        $actions .= '<a href="' . route('produks.show', $produk) . '" class="text-green-600 dark:text-green-400 hover:underline mr-3">View</a>';
                    }

                    if (auth()->user()->hasPermission('edit-produks')) {
                        $actions .= '<a href="' . route('produks.edit', $produk) . '" class="text-blue-600 dark:text-blue-400 hover:underline mr-3">Edit</a>';
                    }

                    if (auth()->user()->hasPermission('delete-produks')) {
                        $actions .= '<form action="' . route('produks.destroy', $produk) . '" method="POST" class="inline" onsubmit="return confirm(\'Are you sure?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('produks.index');
    }

    public function export()
    {
        return Excel::download(new ProdukExport, 'produks-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $kategori = KategoriProduk::orderBy('name')->get();

        return view('produks.create', compact('kategori'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori_produk_id' => ['required', 'exists:kategori_produks,id'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga_basis_pembelian' => ['required', 'numeric'],
            'stok_akhir' => ['required', 'integer'],
            'isactive' => ['boolean'],

            'created_by' => ['integer', 'exists:users,id'],
        ]);

        $produk = Produk::create($validated);
        // // log stok change
        // Stok::create([
        //     'produk_id' => $produk->id,
        //     'tipe_stok' => 'masuk',
        //     'satuan' => $validated['satuan'],
        //     'stok' => $validated['stok_akhir'],
        // ]);

        // // log historyharga
        // HistoryHargaBasis::create([
        //     'produk_id' => $produk->id,
        //     'satuan' => $validated['satuan'],
        //     'harga_basis' => $validated['harga_basis_pembelian'],
        //     'tanggal' => now(),
        // ]);

        return to_route('produks.index')->with('status', 'Produk created successfully.');
    }

    public function show(Produk $produk): View
    {
        $produk->kategori_nama = KategoriProduk::find($produk->kategori_produk_id)->nama;
        
        

        return view('produks.show', compact('produk'));
    }

    public function edit(Produk $produk): View
    {
        $kategoris = KategoriProduk::get();
        
        $produk->kategori_nama = KategoriProduk::find($produk->kategori_produk_id)->nama;

        return view('produks.edit', compact('produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        // dd($request->all());

        $current_user_id = auth()->id();

        // dd("current user id: " . $current_user_id);
        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategoris_id' => ['required'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga_basis_pembelian' => ['required', 'numeric'],
            'stok_akhir' => ['required', 'integer'],
            'isactive' => ['boolean'],

        ]);
        

        // dd("validated data: " . json_encode($validated));
        

        $data = [
            'nama_produk' => $validated['nama_produk'],
            'kategori_produk_id' => $validated['kategoris_id'],
            'satuan' => $validated['satuan'],
            'harga_basis_pembelian' => $validated['harga_basis_pembelian'],
            'stok_akhir' => $validated['stok_akhir'],
        ];

        // dd($data);

        $produk->update($data);

        
        // dd("produk updated: " . json_encode($produk));



        return to_route('produks.index')->with('status', 'Produk updated successfully.');
    }

    //soft delete
    public function destroy(Produk $produk): RedirectResponse
    {
        $produk->update(['isactive' => false, 'deleted_by' => auth()->id(), 'deleted_at' => now()]);
        

        return to_route('produks.index')->with('status', 'Produk deleted successfully.');
    }
}
