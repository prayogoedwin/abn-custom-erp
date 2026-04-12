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
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    private function getPagedata()
    {
        $kategoris = KategoriProduk::get();

        $pagedata = [
            'title' => 'Produk',
            'tablename' => 'produks',
            
            'columns' => [
                ['name' => 'nama_produk','value' => 'nama_produk',  'title' => 'Nama Produk', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'kategori_produk_id', 'value' => 'kategori', 'title' => 'Kategori', 'type' => 'select', 'inform' => true, 'intable' => true, 'options' => [
                    // Ambil data kategori dari database
                    ['value' => '', 'label' => 'Pilih Kategori'],
                    ...$kategoris->map(function ($kategori) {
                        return ['value' => $kategori->id, 'label' => $kategori->nama];
                    })->toArray(),
                    
                ]],
                ['name' => 'satuan', 'value' => 'satuan', 'title' => 'Satuan', 'type' => 'text', 'inform' => true, 'intable' => true],
                ['name' => 'harga_basis_pembelian', 'value' => 'harga_basis_pembelian', 'title' => 'Harga Basis Pembelian', 'type' => 'number', 'inform' => true, 'intable' => true],
                ['name' => 'stok_akhir', 'value' => 'stok_akhir', 'title' => 'Stok Akhir', 'type' => 'number', 'inform' => true, 'intable' => true],
            ],
        ];

        return $pagedata;
    }

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
                ->where('produks.isactive', true)
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

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.index', $pagedata);
    }

    



    public function export()
    {
        return Excel::download(new ProdukExport, 'produks-' . date('Y-m-d') . '.xlsx');
    }

    public function create(): View
    {
        $kategoris = KategoriProduk::get();

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.create', compact('kategoris'), $pagedata);
    }

    public function store(Request $request): RedirectResponse
    {
        $store_data = [
            'nama_produk' => $request->input('nama_produk'),
            'kategori_produk_id' => $request->input('kategori_produk_id'),
            'satuan' => $request->input('satuan'),
            'harga_basis_pembelian' => $request->input('harga_basis_pembelian'),
            'stok_akhir' => $request->input('stok_akhir'),

            'created_by' => auth()->id(),
        ];

        
        $validate = Validator::make($store_data, [
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori_produk_id' => ['required'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga_basis_pembelian' => ['required', 'numeric'],
            'stok_akhir' => ['required', 'integer'],
            'created_by' => ['required', 'integer']
        ]);

        
        if ($validate->fails()) {
            return back()->withErrors($validate)->withInput();
        }
        
        

        $produk = Produk::create($store_data);
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

        return to_route('produks.index')->with('status', 'Produk updated successfully.');
    }

    public function show(Produk $produk): View
    {
        $produk->kategori_nama = KategoriProduk::find($produk->kategori_produk_id)->nama;

        $data = $produk;

        $kategori = KategoriProduk::get();

        $pagedata = $this->getPagedata();

       //TO DO: asdfasdfwe

        

        return view('produks.show', compact('produk'), $pagedata);
    }

    public function edit(Produk $produk): View
    {
        $kategoris = KategoriProduk::get();

        $produk->kategori_nama = KategoriProduk::find($produk->kategori_produk_id)->nama;
        $data = $produk;

        $kategori = KategoriProduk::get();

        $pagedata = $this->getPagedata();

        return view('dynamiccrud.edit', compact('data', 'kategoris'), $pagedata);
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        // dd($request->all());


        // dd("current user id: " . $current_user_id);
        $store_data = [
            'nama_produk' => $request->input('nama_produk'),
            'kategori_produk_id' => $request->input('kategori_produk_id'),
            'satuan' => $request->input('satuan'),
            'harga_basis_pembelian' => $request->input('harga_basis_pembelian'),
            'stok_akhir' => $request->input('stok_akhir'),

            'updated_by' => auth()->id(),
        ];

        
        $validate = Validator::make($store_data, [
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori_produk_id' => ['required'],
            'satuan' => ['required', 'string', 'max:50'],
            'harga_basis_pembelian' => ['required', 'numeric'],
            'stok_akhir' => ['required', 'integer'],
            'created_by' => ['required', 'integer']
        ]);


        // dd("validated data: " . json_encode($validate));


        

        // dd($data);

        $produk->update($store_data);


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
